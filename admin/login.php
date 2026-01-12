<?php
# ALTER TABLE `soraldo_authors` CHANGE `pwd` `pwd` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
session_start();
define('APP_RUNNING', true);

/* ===== LOG LOGIN (AGGIUNTA UNICA) ===== */
function login_log($user, $esito, $id_comune) {
	$ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
	$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
	$riga = date('Y-m-d H:i:s')." | $user | $esito | comune:$id_comune | IP:$ip | UA:$ua\n";
	file_put_contents(__DIR__.'/logs/login_attempts.log', $riga, FILE_APPEND);
}
/* ===== FINE LOG ===== */

# Inserimento accesso al db
global $id_comune;
if (file_exists("config/config.php")){ 
	$install="0"; @require_once("config/config.php"); 
}else{ 
	$install="1";
}

# verifica se effettuata la configurazione
if(empty($dbname) || $install=="1") {
    die("<html><body><div style=\"text-align:center\"><br /><br /><img src=\"modules/Elezioni/images/logo.jpg\" alt=\"Eleonline\" title=\"Eleonline\"><br /><br /><strong>Sembra che <a href='http://www.eleonline.it' title='Eleonline'>Eleonline</a> non sia stato ancora installato.<br /><br />Puoi procedere <a href='../install/index.php'>cliccando qui</a> per iniziare l'installazione</strong></div></body></html>");
}

require_once('config/variabili.php');

$dsn = "mysql:host=$dbhost";
$opt = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false);

if($prefix == '') {
	db_err ('stepBack','Non avete indicato il prefisso tabelle database.');
}

try {
	$dbi = new PDO($dsn, $dbuname, $dbpass, $opt);
} catch(PDOException $e) {
	echo "<br>" . $e->getMessage(); die();
}

$dbi->exec("use $dbname");
$dbi->exec("SET NAMES 'utf8'");

$sth = $dbi->prepare("select * from ".$prefix."_config");
$sth->execute();
$row = $sth->fetch(PDO::FETCH_ASSOC);

$_SESSION['id_comune']=$id_comune;
$multicomune=$row['multicomune'];
$_SESSION['multicomune']=$multicomune;

if($multicomune) {
	$sth = $dbi->prepare("select * from ".$prefix."_ele_comune");
	$sth->execute();
	$comuni = $sth->fetchAll(PDO::FETCH_ASSOC);	
}

require_once 'includes/query.php';

if (isset($_POST['username'])) {

	$aid=$_POST['username'];
	$pwd=$_POST['password'];

	if (isset($_POST['id_comune']) and intval($_POST['id_comune'])>0)
		$id_comune=intval($_POST['id_comune']);
	else
		$id_comune=$row['siteistat'];

	$sth = $dbi->prepare("select pwd,adminop,admincomune,adminsuper,counter,admlanguage from ".$prefix."_authors where binary aid='$aid' and (id_comune='$id_comune' or adminsuper='1')");
	$sth->execute();	

	$esiste=$sth->rowCount();
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	$bpwd=$row['pwd'];

	if(!$esiste) {
		login_log($aid, 'UTENTE_NON_TROVATO', $id_comune);   // LOG
		$_SESSION['msglogout']=2;
		header("Location: login.php");
		exit;
	}else{ 
		if (!password_verify($pwd,$row['pwd'])) {
			if(md5($pwd)!=$row['pwd']) {
				login_log($aid, 'PASSWORD_ERRATA', $id_comune); // LOG
				$msglogout=3;
				header("Location: logout.php");
			}else{
				if($row['admincomune'] or $row['adminsuper']) {
					$row2=configurazione();
					if($row2[0]['versione']<4) {
						require_once 'includes/aggiornadbTo4.php';
					}
				}
				$bpwd=password_hash($pwd,PASSWORD_DEFAULT);
				$sth = $dbi->prepare("update ".$prefix."_authors set pwd=:bpwd where binary aid=:aid and (id_comune=:id_comune or (adminsuper='1'))");
				$sth->execute([
					':id_comune' => $id_comune,
					':bpwd' => $bpwd,
					':aid' => $aid
				]);	
			}
		}

		$counter = $row['counter'] + 1;
		$sth = $dbi->prepare("update ".$prefix."_authors set counter=$counter where aid='$aid' and pwd='$bpwd' and id_comune='$id_comune'");
		$sth->execute();

		$_SESSION['id_comune']=$id_comune;
		$def=default_cons();
		$_SESSION['id_cons_gen']=$def[0];
		$_SESSION['tipo_cons']=$def[3];
	}

	if($row['adminsuper']) $role='superuser';
	elseif($row['admincomune']) $role='admin';
	elseif($row['adminop']) $role='operatore';

	login_log($aid, 'LOGIN_OK', $id_comune); // LOG

	$_SESSION['username'] = $aid;
	$_SESSION['pwd'] = $bpwd;
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
  <!-- Font Awesome (locale) -->
<link rel="stylesheet" href="assets/css/all.min.css" />
<!-- AdminLTE CSS (locale)--> 
<link rel="stylesheet" href="css/adminlte.min.css" />
<link rel="icon" href="favicon.ico" type="image/x-icon">
  <!-- AdminLTE CSS 
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" />-->
  <!-- Font Awesome 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />-->
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="#"><b>Admin</b>Eleonline</a>
    </div text=small;>
	<div>
<?php 
if(isset($_SESSION['msglogout'])) { 
    $msg = '';
    switch($_SESSION['msglogout']) {
        case 1:
            $msg = "Utente non trovato";
            break;
        case 2:
            $msg = "Utente non trovato"; // oppure "ID comune non valido"
            break;
        case 3:
            $msg = "Password errata";
            break;
        default:
            $msg = "Login fallito";
    }
    echo "Login fallito: ".$msg."<br>"; 
    unset($_SESSION['msglogout']); 
} 
?>
	
	User: admin Psw: admin123 Ruolo:admin<br>
	User: superuser Psw:superpass Ruolo:superuser<br>
<!--	User: operatore Psw:operapass Ruolo:	operatore<br> -->
	</div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Effettua il login per iniziare la sessione</p>

        <?php if (isset($error)): ?>
          <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

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
				<?php
					$desc='';
					foreach($comuni as $key=>$val) { $sel=''; ?>
					<option <?php echo $sel; ?> value="<?php echo $val['id_comune'];?>"> <?php echo $val['descrizione'];?></option>
				<?php }?>
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
      <!-- /.login-card-body -->
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
  <!-- AdminLTE and dependencies -->
  <script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/adminlte.min.js"></script>
  
 <!--  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script> -->
</body>
</html>
