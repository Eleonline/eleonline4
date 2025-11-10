<?php
#principale.php
#la funzione è il punto di ingresso per le chiamate ajax
#esegue i controlli di sicurezza e dei permessi di accesso
#quindi carica la funzione che è stata richiesta
#per scelta i parametri sono tutti numerici
#questa è la tabella delle corrispondenze:
#1 -> seggi_salva_gruppo
#2 -> seggi_salva_consiglieri
# ...

define('APP_RUNNING', true);
#die("Errore");
global $dbi,$prefix;
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
  // gestione sessione
if (!isset($_SESSION))
{
	session_start();
}else 
	session_regenerate_id();
$a = session_id();
if(empty($a)) 
	session_start();
require_once('config/variabili.php');
require_once('config/config.php');
$dsn = "mysql:host=$dbhost";
$opt = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false);
if($prefix == '') {
	db_err ('stepBack','Non avete indicato il prefisso tabelle database.');
}
try 
{
	$dbi = new PDO($dsn, $dbuname, $dbpass, $opt);
}
catch(PDOException $e)
{
	echo "<br>" . $e->getMessage();die();
}
$sql = "use $dbname";
try
{
	$dbi->exec($sql);
}
catch(PDOException $e)
{
	echo $sql . "<br>" . $e->getMessage();die();
}                                                                                       	
$sth = $dbi->prepare("SET SESSION character_set_connection = 'utf8' ");
$sth->execute();
$sth = $dbi->prepare("SET SESSION character_set_client = 'utf8' ");
$sth->execute();
$sth = $dbi->prepare("SET SESSION character_set_database = 'utf8' ");
$sth->execute();
$sth = $dbi->prepare("SET CHARACTER SET utf8");
$sth->execute();

$sth = $dbi->prepare("SET NAMES 'utf8'");
$sth->execute();
$sth = $dbi->prepare("select * from ".$prefix."_config");
$sth->execute();
//lettura sessione
$aid=$_SESSION['username'];
$prefix=$_SESSION['prefix'];
$id_comune=$_SESSION['id_comune'];
$id_cons_gen=$_SESSION['id_cons_gen']; #die("TEST QUI");
$id_cons=$_SESSION['id_cons'];
#if (isset($param['id_cons'])) {$id_cons=intval($param['id_cons']);} else die("Errore: consultazione non definita");
if (isset($param['funzione'])) {$funzione=$param['funzione'];} else die("Errore: funzione non definita");
$permessi=ChiSei($id_cons_gen);
if($permessi<16) return("Errore: non hai i permessi");
require_once 'includes/query.php';
switch ($funzione) {
	case 'salvaAffluenze':
		include("modules/salva_aff.php");
	break;
	case 'salvaVoti':
		include("modules/salva_voti.php");
	break;
	case 'salvaVotiLista':
		include("modules/salva_voti_lista.php");
	break;
	case 'salvaGruppi': 
		include("modules/seggi_salva_gruppi.php");
	break;
	case 'leggiBarraSezioni':
		include("modules/barra_sezioni.php");
		break;
	case 'salvaColoreTema':
		include("modules/salva_colore_tema.php");
		break;
	case 'salvaAffluenza':
		include("modules/salva_orario_affluenza.php");
		break;
	case 'salvaConfigSito':
		include("modules/salva_config_sito.php");
		break;
	case 'salvaComune':
		include("modules/salva_comune.php");
		break;
	case 101: 
		include("ws/funzioni/salvaModifiche.php");
	break;

	default :
		return("Errore");
}		

function ChiSei($idcg){
global $dbi, $msglogout, $id_cons_gen,$giorniaut;

$aid=$_SESSION['username'];
$prefix=$_SESSION['prefix'];
$pwd=$_SESSION['pwd'];
$id_comune=$_SESSION['id_comune'];
$perms=0;
$sql="select adminsuper, admincomune, adminop  from ".$prefix."_authors where aid='$aid' and pwd='$pwd' and (id_comune='$id_comune' or id_comune='0')";
$sth = $dbi->prepare("$sql");
$sth->execute();	
$row = $sth->fetch(PDO::FETCH_BOTH);	
if($row){
	$adminsuper=$row[0];
	$admincomune=$row[1];
	$oper=$row[2];
}else{
	$adminsuper=0;
	$admincomune=0;
	$oper=1;
}
	if ($adminsuper==1)
		return 256;
	elseif ($admincomune==1) 
		return 64;
	elseif($oper) {$msglogout=1; return 0;} # id_cons='$id_cons' and 
	else {
		$sql="select t1.id_cons, t1.id_cons_gen from ".$prefix."_ele_cons_comune as t1, ".$prefix."_ele_consultazione as t2 where t1.id_cons_gen=t2.id_cons_gen and t1.chiusa='0' and t1.id_comune='$id_comune' and date_add(t2.data_fine, interval $giorniaut day)>CURDATE()";
		$sth = $dbi->prepare("$sql");
		$sth->execute();	
		if(!$sth->rowCount()) { $msglogout=1; $perms=0; return $perms;}
		list($id_cons,$idcg) = $sth->fetch(PDO::FETCH_NUM);           
		if (!$id_cons_gen) $id_cons_gen=$idcg; 
		$sql="select permessi from ".$prefix."_ele_operatori where id_cons='$id_cons' and aid='$aid'";
		$sth = $dbi->prepare("$sql");
		$sth->execute();
		list($perms) = $sth->fetch(PDO::FETCH_NUM);
		return $perms;
	}
}

?>