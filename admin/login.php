<?php
session_start();
define('APP_RUNNING', true);

// ======== MESSAGGI ERRORE =========
$msg_errori = [
    2 => 'Utente non trovato',
    3 => 'Password errata',
    4 => 'Troppi tentativi falliti. Attendi 5 minuti.'
];

// ======== INIZIALIZZA CONTATORI LOGIN =========
if (!isset($_SESSION['login_fail'])) $_SESSION['login_fail'] = 0;
if (!isset($_SESSION['login_block_until'])) $_SESSION['login_block_until'] = 0;

// ======== FUNZIONE LOG LOGIN =========
function log_login_attempt($username, $success, $id_comune = null) {
    $data   = date('Y-m-d H:i:s');
    $ip     = $_SERVER['REMOTE_ADDR'] ?? 'IP sconosciuto';
    $comune = $id_comune ?? 'N/A';
    $status = $success ? 'OK' : 'FAIL';
    $ua     = $_SERVER['HTTP_USER_AGENT'] ?? 'UA sconosciuto';
    $log = "[$data] LOGIN $status | IP: $ip | Utente: $username | Comune: $comune | UA: $ua" . PHP_EOL;

    $logFile = __DIR__ . '/logs/login_attempts.log';
    if (!is_dir(__DIR__ . '/logs')) mkdir(__DIR__ . '/logs', 0755, true);

    file_put_contents($logFile, $log, FILE_APPEND);
}
// ====================================

global $id_comune;
if (file_exists("config/config.php")){ 
	$install="0"; @require_once("config/config.php"); 
}else{ 
	$install="1";
}

if(empty($dbname) || $install=="1") {
    die("<html><body><div style=\"text-align:center\"><br><br><img src=\"modules/Elezioni/images/logo.jpg\" alt=\"Eleonline\" title=\"Eleonline\"><br><br><strong>Sembra che <a href='http://www.eleonline.it'>Eleonline</a> non sia stato ancora installato.<br><br>Puoi procedere <a href='../install/index.php'>cliccando qui</a> per iniziare l'installazione</strong></div></body></html>");
}

require_once('config/variabili.php');

$dsn = "mysql:host=$dbhost";
$opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false];

if($prefix == '') db_err('stepBack','Non avete indicato il prefisso tabelle database.');

try { $dbi = new PDO($dsn, $dbuname, $dbpass, $opt); } 
catch(PDOException $e) { echo "<br>" . $e->getMessage(); die(); }

$dbi->exec("use $dbname");
$dbi->exec("SET NAMES 'utf8'");

$sth = $dbi->prepare("SELECT * FROM ".$prefix."_config");
$sth->execute();
$row = $sth->fetch(PDO::FETCH_ASSOC);

$_SESSION['id_comune']=$id_comune;
$multicomune=$row['multicomune'];
$_SESSION['multicomune']=$multicomune;

if($multicomune) {
	$sth = $dbi->prepare("SELECT * FROM ".$prefix."_ele_comune");
	$sth->execute();
	$comuni = $sth->fetchAll(PDO::FETCH_ASSOC);	
}

require_once 'includes/query.php';

// ======== BLOCCO LOGIN DOPO 5 TENTATIVI =========
if ($_SESSION['login_block_until'] > time()) {
    $_SESSION['msglogout'] = 4; // Troppi tentativi
    header("Location: login.php");
    exit;
}

// ======== LOGIN =========
if (isset($_POST['username'])) {
	$aid=$_POST['username'];
	$pwd=$_POST['password'];

	if (strlen($aid)>25 ) die ("Nome utente troppo lungo: $aid");	
	if (strstr($aid," ")) die ("Gli spazi non sono ammessi nel nome utente: $aid");

	if (isset($_POST['id_comune']) and intval($_POST['id_comune'])>0)
		$id_comune=intval($_POST['id_comune']);
	else
		$id_comune=$row['siteistat'];

	$sth = $dbi->prepare("
		SELECT pwd,adminop,admincomune,adminsuper,counter,admlanguage 
		FROM ".$prefix."_authors 
		WHERE binary aid='$aid' 
		AND (id_comune='$id_comune' OR adminsuper='1')
	");
	$sth->execute();

	$esiste = $sth->rowCount();
	$row = $sth->fetch(PDO::FETCH_ASSOC);

	// ====== UTENTE NON TROVATO ======
	if(!$esiste) {
		log_login_attempt($aid, false, $id_comune);
		$_SESSION['login_fail']++;
		if ($_SESSION['login_fail'] >= 5) $_SESSION['login_block_until'] = time() + 300;
		$tentativi_rimasti = max(0, 5 - $_SESSION['login_fail']);
		$_SESSION['msglogout']=2;
		$_SESSION['tentativi_rimasti'] = $tentativi_rimasti;
		header("Location: login.php");
		exit;
	}

	// ====== PASSWORD ERRATA ======
	if (!password_verify($pwd, $row['pwd']) && md5($pwd)!=$row['pwd']) {
		log_login_attempt($aid, false, $id_comune);
		$_SESSION['login_fail']++;
		if ($_SESSION['login_fail'] >= 5) $_SESSION['login_block_until'] = time() + 300;
		$tentativi_rimasti = max(0, 5 - $_SESSION['login_fail']);
		$_SESSION['msglogout']=3;
		$_SESSION['tentativi_rimasti'] = $tentativi_rimasti;
		header("Location: login.php");
		exit;
	}

	// ====== LOGIN RIUSCITO ======
	$counter = $row['counter'] + 1;
	$dbi->exec("UPDATE ".$prefix."_authors SET counter=$counter WHERE aid='$aid'");

	if($row['adminsuper']) $role='superuser';
	elseif($row['admincomune']) $role='admin';
	elseif($row['adminop']) $role='operatore';

	log_login_attempt($aid, true, $id_comune);

	// RESET TENTATIVI
	$_SESSION['login_fail'] = 0;
	$_SESSION['login_block_until'] = 0;

	$_SESSION['username'] = $aid;
	$_SESSION['pwd'] = $row['pwd'];
	$_SESSION['ruolo'] = $role;
	$_SESSION['prefix'] = $prefix;

	header("Location: modules/modules.php");
	exit;
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
    <div class="login-logo">
      <a href="#"><b>Admin</b>Eleonline</a>
    </div>
	<div>
	<?php if(isset($_SESSION['msglogout'])) { 
    echo $msg_errori[$_SESSION['msglogout']] ?? 'Errore di login';
    if(isset($_SESSION['tentativi_rimasti']) && $_SESSION['msglogout'] != 4) {
        echo " | Tentativi rimasti: ".$_SESSION['tentativi_rimasti'];
    }
    unset($_SESSION['msglogout']);
    unset($_SESSION['tentativi_rimasti']);
} ?>
<br>
	User: admin Psw: admin123 Ruolo:admin<br>
	User: superuser Psw:superpass Ruolo:superuser<br>
	</div>
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Effettua il login per iniziare la sessione</p>
        <form action="" method="post" autocomplete="off">
          <div class="input-group mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
			<input type="password" name="password" id="passwordInput" class="form-control" placeholder="Password" required />
			<div class="input-group-append">
				<div class="input-group-text" style="cursor:pointer;" onclick="togglePassword()">
					<span id="togglePassword" class="fas fa-eye"></span>
				</div>
		    </div>
          </div>
		<?php if($multicomune) { ?>
		  <label for="comuneSelect">Seleziona il Comune</label>
		  <div class="input-group mb-3">
			<select id="comuneSelect" name="id_comune">
				<?php foreach($comuni as $val) { ?>
					<option value="<?php echo $val['id_comune'];?>"><?php echo $val['descrizione'];?></option>
				<?php } ?>
			</select>			
          </div>
		<?php } ?>  
          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('togglePassword');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
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
