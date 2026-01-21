<?php
session_start();
define('APP_RUNNING', true);

/* ===== LOG LOGIN ===== */
function login_log($user, $esito, $id_comune) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $riga = date('Y-m-d H:i:s') . " | $user | $esito | comune:$id_comune | IP:$ip | UA:$ua\n";
    file_put_contents(__DIR__ . '/logs/login_attempts.log', $riga, FILE_APPEND);
}

/* ===== BLOCCO DOPO 4 TENTATIVI FALLITI ===== */
define('MAX_LOGIN_ATTEMPTS', 4);
define('LOGIN_BLOCK_TIME', 120); // 2 minuti

function login_block_file($user) {
    return __DIR__ . '/logs/login_block_' . md5($user) . '.json';
}

function is_login_blocked($user) {
    $file = login_block_file($user);
    if (!file_exists($file)) return false;

    $data = json_decode(file_get_contents($file), true);
    if (!$data) return false;

    if (($data['attempts'] ?? 0) >= MAX_LOGIN_ATTEMPTS) {
        $elapsed = time() - ($data['last_attempt'] ?? 0);
        if ($elapsed < LOGIN_BLOCK_TIME) {
            return LOGIN_BLOCK_TIME - $elapsed; // secondi rimanenti
        } else {
            unlink($file); // sblocco automatico
            return false;
        }
    }
    return false;
}

function register_login_fail($user) {
    $file = login_block_file($user);
    $data = ['attempts' => 0, 'last_attempt' => time()];
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
    }
    $data['attempts']++;
    $data['last_attempt'] = time();
    file_put_contents($file, json_encode($data));
}

function clear_login_fail($user) {
    $file = login_block_file($user);
    if (file_exists($file)) unlink($file);
}

/* ===== ANTI-BRUTEFORCE IP CON WHITELIST ===== */
define('MAX_IP_ATTEMPTS', 10);
define('IP_BLOCK_TIME', 86400); // 24 ore

$ipAllowedFile = __DIR__ . '/includes/ip_allowed.json';
$ipManualBlockFile = __DIR__ . '/includes/ip_block_MANUAL.json';

// Controlla se l'IP è nella whitelist
function is_ip_allowed($ip) {
    global $ipAllowedFile;
    if (!file_exists($ipAllowedFile)) return false;
    $data = json_decode(file_get_contents($ipAllowedFile), true);
    return in_array($ip, $data);
}

// Restituisce il path del file di blocco dell'IP
function ip_block_file($ip) {
    return __DIR__ . '/logs/ip_block_' . md5($ip) . '.json';
}

// Controlla se l'IP è bloccato (bypass whitelist)
function is_ip_blocked($ip) {
    if (is_ip_allowed($ip)) return false; // whitelist bypass

    $file = ip_block_file($ip);
    if (!file_exists($file)) return false;

    $data = json_decode(file_get_contents($file), true);
    if (!$data) return false;

    if (($data['attempts'] ?? 0) >= MAX_IP_ATTEMPTS) {
        $elapsed = time() - ($data['last_attempt'] ?? 0);
        if ($elapsed < IP_BLOCK_TIME) {
            return IP_BLOCK_TIME - $elapsed; // secondi rimanenti
        } else {
            unlink($file); // sblocco automatico
        }
    }
    return false;
}

// Registra un tentativo fallito (bypass whitelist)
function register_ip_fail($ip) {
    if (is_ip_allowed($ip)) return; // whitelist bypass

    $file = ip_block_file($ip);
    $data = ['attempts' => 0, 'last_attempt' => time()];
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
    }
    $data['attempts']++;
    $data['last_attempt'] = time();
    file_put_contents($file, json_encode($data));
}

// Sblocca manualmente l'IP
function clear_ip_fail($ip) {
    $file = ip_block_file($ip);
    if (file_exists($file)) unlink($file);
}

function is_ip_manual_blocked($ip) {
    global $ipManualBlockFile;

    if (!file_exists($ipManualBlockFile)) return false;

    $data = json_decode(file_get_contents($ipManualBlockFile), true);
    if (!is_array($data)) return false;

    foreach ($data as $row) {
        if (isset($row['ip']) && $row['ip'] === $ip) {
            return true;
        }
    }
    return false;
}

/* ===== CONFIG E DB ===== */
global $id_comune;
if (file_exists("config/config.php")) {
    $install = "0";
    require_once("config/config.php");
} else {
    $install = "1";
}

if (empty($dbname) || $install == "1") {
    die("<html><body><div style='text-align:center'><br><br>
    <img src='modules/Elezioni/images/logo.jpg'><br><br>
    <strong>Eleonline non installato.
    <a href='../install/index.php'>Installa</a></strong>
    </div></body></html>");
}

require_once('config/variabili.php');

$dsn = "mysql:host=$dbhost";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false
];

if ($prefix == '') db_err('stepBack', 'Prefisso tabelle non indicato.');

$dbi = new PDO($dsn, $dbuname, $dbpass, $opt);
$dbi->exec("USE $dbname");
$dbi->exec("SET NAMES 'utf8'");

$sth = $dbi->prepare("SELECT * FROM {$prefix}_config");
$sth->execute();
$config = $sth->fetch(PDO::FETCH_ASSOC);

$_SESSION['id_comune'] = $id_comune;
$multicomune = $config['multicomune'];
$_SESSION['multicomune'] = $multicomune;

if ($multicomune) {
    $sth = $dbi->prepare("SELECT * FROM {$prefix}_ele_comune");
    $sth->execute();
    $comuni = $sth->fetchAll(PDO::FETCH_ASSOC);
}

require_once 'includes/query.php';

/* ===== LOGIN ===== */
if (isset($_POST['username'], $_POST['password'])) {

    $aid = trim($_POST['username']);
    $pwd = $_POST['password'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

	// BLOCCO BLACKLIST MANUALE
	if (is_ip_manual_blocked($ip)) {
		$_SESSION['msglogout'] = 6;
		header("Location: login.php");
		exit;
	}

    // BLOCCO IP
    $ip_block = is_ip_blocked($ip);
    if ($ip_block !== false) {
        $_SESSION['msglogout'] = 5;
        $_SESSION['last_login_user'] = $aid;
        $_SESSION['block_seconds'] = $ip_block;
        header("Location: login.php");
        exit;
    }

    // BLOCCO LOGIN USER
    $block_time = is_login_blocked($aid);
    if ($block_time !== false) {
        $_SESSION['msglogout'] = 4;
        $_SESSION['last_login_user'] = $aid;
        $_SESSION['block_seconds'] = $block_time;
        header("Location: login.php");
        exit;
    }

    $id_comune = (isset($_POST['id_comune']) && intval($_POST['id_comune']) > 0)
        ? intval($_POST['id_comune'])
        : $config['siteistat'];

    $sth = $dbi->prepare("
        SELECT pwd, adminop, admincomune, adminsuper, counter, admlanguage
        FROM {$prefix}_authors
        WHERE BINARY aid = :aid
          AND (id_comune = :id_comune OR adminsuper = '1')
        LIMIT 1
    ");
    $sth->execute([
        ':aid' => $aid,
        ':id_comune' => $id_comune
    ]);

    $row = $sth->fetch(PDO::FETCH_ASSOC);

    // UTENTE NON TROVATO
    if (!$row) {
        register_login_fail($aid);
        register_ip_fail($ip);
        login_log($aid, 'UTENTE_NON_TROVATO', $id_comune);
        $_SESSION['msglogout'] = 2;
        $_SESSION['last_login_user'] = $aid;
        header("Location: login.php");
        exit;
    }

    // CONTROLLO PASSWORD
    $password_ok = false;
    $storedPwd   = $row['pwd'];

    if (!empty($storedPwd) && strlen($storedPwd) > 32) {
        if (password_verify($pwd, $storedPwd)) $password_ok = true;
    } elseif (!empty($storedPwd) && preg_match('/^[a-f0-9]{32}$/i', $storedPwd)) {
        if (md5($pwd) === $storedPwd) {
            $newHash = password_hash($pwd, PASSWORD_DEFAULT);
            $upd = $dbi->prepare("
                UPDATE {$prefix}_authors
                SET pwd = :pwd
                WHERE BINARY aid = :aid
                  AND (id_comune = :id_comune OR adminsuper = '1')
            ");
            $upd->execute([
                ':pwd' => $newHash,
                ':aid' => $aid,
                ':id_comune' => $id_comune
            ]);
            $password_ok = true;
        }
    }

    if (!$password_ok) {
        register_login_fail($aid);
        register_ip_fail($ip);
        $_SESSION['last_login_user'] = $aid;

        $upd = $dbi->prepare("
            UPDATE {$prefix}_authors
            SET counter = counter + 1
            WHERE BINARY aid = :aid
              AND id_comune = :id_comune
            LIMIT 1
        ");
        $upd->execute([
            ':aid' => $aid,
            ':id_comune' => $id_comune
        ]);
        login_log($aid, 'PASSWORD_ERRATA', $id_comune);
        $_SESSION['msglogout'] = 3;
        header("Location: login.php");
        exit;
    }

    // LOGIN OK
    clear_login_fail($aid);
    clear_ip_fail($ip);
    $upd = $dbi->prepare("
        UPDATE {$prefix}_authors
        SET counter = 0
        WHERE BINARY aid = :aid
          AND id_comune = :id_comune
    ");
    $upd->execute([
        ':aid' => $aid,
        ':id_comune' => $id_comune
    ]);

    if ($row['adminsuper']) $role = 'superuser';
    elseif ($row['admincomune']) $role = 'admin';
    elseif ($row['adminop']) $role = 'operatore';
    else $role = 'utente';

    session_regenerate_id(true);
    $_SESSION['username']  = $aid;
    $_SESSION['ruolo']     = $role;
    $_SESSION['id_comune'] = $id_comune;
    $_SESSION['prefix']    = $prefix;
    $_SESSION['lang']      = $row['admlanguage'];

    $def = default_cons();
    $_SESSION['id_cons_gen'] = $def[0];
    $_SESSION['tipo_cons']   = $def[3];

    login_log($aid, 'LOGIN_OK', $id_comune);
    unset($_SESSION['last_login_user']);
    header("Location: modules/modules.php");
    exit;
}

// ===== LOGIC COUNTDOWN E TENTATIVI =====
$block_seconds = 0;
$remaining_attempts = MAX_LOGIN_ATTEMPTS; // default
$user = $_SESSION['last_login_user'] ?? null;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

if ($user) {
    $file = login_block_file($user);
    $attempts = 0;
    if(file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        $attempts = $data['attempts'] ?? 0;
        if($attempts >= MAX_LOGIN_ATTEMPTS) {
            $elapsed = time() - ($data['last_attempt'] ?? 0);
            if($elapsed < LOGIN_BLOCK_TIME) {
                $block_seconds = LOGIN_BLOCK_TIME - $elapsed;
            } else {
                unlink($file);
                $attempts = 0;
            }
        }
    }
    $remaining_attempts = MAX_LOGIN_ATTEMPTS - $attempts;
    if($remaining_attempts < 0) $remaining_attempts = 0;
}

// IP countdown
$ip_block_seconds = 0;
$ip_attempts = 0;
if ($ip) {
    $file = ip_block_file($ip);
    if(file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        $ip_attempts = $data['attempts'] ?? 0;
        if($ip_attempts >= MAX_IP_ATTEMPTS) {
            $elapsed = time() - ($data['last_attempt'] ?? 0);
            if($elapsed < IP_BLOCK_TIME) {
                $ip_block_seconds = IP_BLOCK_TIME - $elapsed;
            } else {
                unlink($file);
                $ip_attempts = 0;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login Eleonline</title>
<link rel="stylesheet" href="assets/css/all.min.css" />
<link rel="stylesheet" href="css/adminlte.min.css" />
<link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body class="hold-transition login-page">
<div class="login-box">
<div class="login-logo"><a href="#"><b>Admin</b>Eleonline</a></div>
<div>
User: admin Psw: admin123 Ruolo:admin<br>
User: superuser Psw:superpass Ruolo:superuser<br>
<!--	User: operatore Psw:operapass Ruolo:	operatore<br> -->
<?php 
if($user && isset($_SESSION['msglogout'])): 
$msg = '';
switch($_SESSION['msglogout']) {
    case 1: $msg = "Utente non trovato"; break;
    case 2: $msg = "Utente non trovato / ID comune non valido"; break;
    case 3: $msg = "Password errata"; break;
    case 4: $msg = "Troppi tentativi errati. Riprova tra <span id='countdown'>--:--</span>"; break;
    case 5: $msg = "Troppi tentativi dall’indirizzo IP. Riprova tra <span id='countdown'>--:--</span>"; break;
    default: $msg = "Login fallito";
}
echo "<div style='color:red;margin-bottom:10px;'>Login fallito: $msg</div>";

// tentativi rimanenti user
echo "<div style='color:orange;margin-bottom:10px;'>
      Tentativi rimanenti: $remaining_attempts / ".MAX_LOGIN_ATTEMPTS."</div>";

// se bloccato user o IP, mostra countdown
$block = max($block_seconds, $ip_block_seconds);
if($block > 0) {
    echo "<script>
    let remaining = $block;
    function updateCountdown() {
        if(remaining <= 0) {
            document.getElementById('countdown').innerText = '0:00';
            return;
        }
        let minutes = Math.floor(remaining/60);
        let seconds = remaining%60;
        if(seconds<10) seconds='0'+seconds;
        document.getElementById('countdown').innerText = minutes + ':' + seconds;
        remaining--;
        setTimeout(updateCountdown, 1000);
    }
    updateCountdown();
    </script>";
}
unset($_SESSION['msglogout']);
?>
<?php endif; ?>
</div>

<div class="card">
<div class="card-body login-card-body">
<p class="login-box-msg">Effettua il login per iniziare la sessione</p>
<form action="" method="post" autocomplete="off">
<div class="input-group mb-3">
<input type="text" name="username" class="form-control" placeholder="Username" required autofocus />
<div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
</div>
<div class="input-group mb-3">
<input type="password" name="password" id="passwordInput" class="form-control" placeholder="Password" required />
<div class="input-group-append">
<div class="input-group-text" style="cursor:pointer;" onclick="togglePassword()">
<span id="togglePassword" class="fas fa-eye"></span>
</div>
</div>
</div>
<?php if($multicomune): ?>
<label for="comuneSelect">Seleziona il Comune</label>
<div class="input-group mb-3">
<select id="comuneSelect" name="id_comune">
<?php foreach($comuni as $val): ?>
<option value="<?php echo $val['id_comune']; ?>"><?php echo $val['descrizione']; ?></option>
<?php endforeach; ?>
</select>
</div>
<?php endif; ?>
<div class="row"><div class="col-12">
<button type="submit" class="btn btn-primary btn-block">Login</button>
</div></div>
</form>
</div>
</div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('togglePassword');
    if(input.type === 'password') {
        input.type='text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type='password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/adminlte.min.js"></script>
</body>
</html>
