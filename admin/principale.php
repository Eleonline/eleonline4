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
global $dbi,$prefix,$id_comune;
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
$permessi=ChiSei($id_cons_gen);
if($permessi<16) return("Errore: non hai i permessi");
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['funzione'])) {$funzione=$param['funzione'];} else die("Errore: funzione non definita");
require_once 'includes/query.php';
if($permessi>32)
	switch ($funzione) {
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
		case 'salvaConsultazione':
			include("modules/salva_consultazione.php");
			break;
		case 'salvaUtente':
			include("modules/salva_utente.php");
			break;
		case 'salvaPermesso':
			include("modules/salva_permesso.php");
			break;
		case 'importaCircoscrizioni':
			include("modules/importa_circoscrizioni.php");
			break;
		case 'salvaCircoscrizione':
			include("modules/salva_circoscrizione.php");
			break;
		case 'salvaSede':
			include("modules/salva_sede.php");
			break;
		case 'salvaSezione':
			include("modules/salva_sezione.php");
			break;
		case 'salvaInfo':
			include("modules/salva_info.php");
			break;
		case 'menuConsultazione':
			include("modules/elenco_cons_menu.php");
			break;
		case 'salvaGruppo': 
			include("modules/salva_gruppo.php");
			break;
		case 'salvaLista': 
			include("modules/salva_lista.php");
			break;
		case 'salvaCandidato': 
			include("modules/salva_candidato.php");
			break;
		case 'scaricaDati': 
			include("modules/scarica.php");
			break;
		case 'salvaReferendum': 
			include("modules/salva_referendum.php");
			break;
	}
	
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
	case 'immagine':
		include("modules/foto.php");
		break;
	case 101: 
		include("ws/funzioni/salvaModifiche.php");
	break;

	default :
		return("Errore");
}		

function ChiSei($idcg){
global $dbi, $msglogout, $id_cons_gen,$giorniaut,$id_cons,$id_comune;

$aid=$_SESSION['username'];
$prefix=$_SESSION['prefix'];
$ruolo=$_SESSION['ruolo'];
$idcom=$_SESSION['id_comune'];
$perms=0;
if($id_comune==$idcom){
	if ($ruolo=='superuser')
		return 256;
	elseif ($ruolo=='admin') 
		return 64;
	elseif($ruolo=='operatore') #{$msglogout=1; return 0;} # id_cons='$id_cons' and 
		return 32;
}
return 0;
		
/*	
	{
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
	}*/
}

?>