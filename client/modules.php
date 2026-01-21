<?php

/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

define('MODULE_FILE', true);

// Additional security (Union, CLike, XSS)
// We want to use the function stripos,
// but thats only available since PHP5.
// So we cloned the function...
if(!function_exists('stripos')) {
  function stripos_clone($haystack, $needle, $offset=0) {
    return strpos(strtoupper($haystack), strtoupper($needle), $offset);
  }
} else {
// But when this is PHP5, we use the original function	
  function stripos_clone($haystack, $needle, $offset=0) {
    return stripos($haystack, $needle, $offset=0);
  }
}

  if(isset($_SERVER['QUERY_STRING']) && (!stripos_clone($_SERVER['QUERY_STRING'], "ad_click") || !stripos_clone($_SERVER['QUERY_STRING'], "url"))) {
    $queryString = $_SERVER['QUERY_STRING'];
    if (stripos_clone($queryString,'%20union%20') OR stripos_clone($queryString,'..') OR stripos_clone($queryString,'+') OR  stripos_clone($queryString,'/*') OR stripos_clone($queryString,'*/union/*') OR stripos_clone($queryString,'c2nyaxb0') OR stripos_clone($queryString,'+union+') OR stripos_clone($queryString,'http://') OR (stripos_clone($queryString,'cmd=') AND !stripos_clone($queryString,'&cmd')) OR (stripos_clone($queryString,'exec') AND !stripos_clone($queryString,'execu')) OR stripos_clone($queryString,'concat')) {
      die('Operazione non consentita');
    }
  }

foreach ($_GET as $sec_key => $secvalue) {
    if ((preg_match("/<[^>]*script*\"?[^>]*>/i",$secvalue)) ||
	(preg_match("/<[^>]*object*\"?[^>]*>/i", $secvalue)) ||
	(preg_match("/<[^>]*iframe*\"?[^>]*>/i", $secvalue)) ||
	(preg_match("/<[^>]*applet*\"?[^>]*>/i", $secvalue)) ||
	(preg_match("/<[^>]*meta*\"?[^>]*>/i", $secvalue)) ||
	(preg_match("/<[^>]*style*\"?[^>]*>/i", $secvalue)) ||
	(preg_match("/<[^>]*form*\"?[^>]*>/i", $secvalue)) ||
	(preg_match("/<[^>]*img*\"?[^>]*>/i", $secvalue)) ||
	(preg_match("/<[^>]*onmouseover*\"?[^>]*>/i", $secvalue)) ||
	(preg_match("/<[^>]*body*\"?[^>]*>/i", $secvalue)) ||
	(preg_match("/\([^>]*\"?[^)]*\)/", $secvalue)) ||
	(preg_match("/\"/", $secvalue)) ||
	(preg_match("/inside_mod/i", $sec_key))) {
        die ("Operazione non consentita");
     }
  }

  foreach ($_POST as $secvalue) {
    if ((preg_match("/<[^>]*onmouseover*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]script*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]*body*\"?[^>]*>/i", $secvalue)) || (preg_match("/<[^>]style*\"?[^>]*>/i", $secvalue))) {
      die ($htmltags);
    }
  }
  
// Posting from other servers in not allowed
// Fix by Quake
// Bug found by PeNdEjO
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if (isset($_SERVER['HTTP_REFERER'])) {
    if (!stripos_clone($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
        die('Posting da un altro server non  consentito!');
    } else {
#    	die('<b>Attenzione:</b> il tuo browser non puo inviare gli header HTTP_REFERER al website.<br/>');
    }
  }
}

  
function jsexist(){ // controlla javascript by l.apolito 2008
global $op,$name;
if(!isset($_GET['js'])){
$querystring= @preg_replace('/'.$_SERVER['DOCUMENT_ROOT'].'/i','http://'.$_SERVER['HTTP_HOST'].'/',$_SERVER['SCRIPT_FILENAME']);
if (preg_match("/modules.php/i",$_SERVER['SCRIPT_NAME'])) $pagina="name=$name"; // reindirizza
if (preg_match("/admin.php/i",$_SERVER['SCRIPT_NAME'])) $pagina="op=$op"; // reindirizza
 echo "<noscript><meta http-equiv=\"refresh\" content=\"0; url=".$querystring."?js=b&amp;$pagina\"/></noscript>";
  }
$js=$_GET['js'];
return $js;
}
  
  




session_start();//MODIFICHE PER GESTIONE SESSIONI
if (!count($_SESSION)) $_SESSION['op']='gruppo';
// apre database
////////////////////////
if (file_exists("config.php")) { @require_once("config.php");$install="";}else{ $install="1";}


# verifica se effettuata la configurazione
if(empty($dbname) || $install=="1") {
    die("<html><body><div style=\"text-align:center\"><br /><br /><img src=\"modules/Elezioni/images/logo.jpg\" alt=\"Eleonline\" title=\"Eleonline\"><br /><br /><strong>Sembra che <a href='http://www.eleonline.it' title='Eleonline'>Eleonline</a> non sia stato ancora installato.<br /><br />Puoi procedere <a href='../install/index.php'>cliccando qui</a> per iniziare l'installazione</strong></div></body></html>");
}


$dbi = new PDO("mysql:host=$dbhost;charset=utf8", $dbuname, $dbpass, array(PDO::ATTR_EMULATE_PREPARES => false, 
                                                                                                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));	
$sql = "use $dbname";
$dbi->exec($sql);
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

# protezione csrf ottobre 2012 - by l.apolito
if (file_exists("inc/csrf-magic/csrf-magic.php")) {
#	include_once 'inc/csrf-magic/csrf-magic.php';
}

require_once('variabili.php');
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['id_comune'])) $id_comune=intval($param['id_comune']); #else $id_comune=$siteistat;
if (isset($param['id_cons_gen'])) $id_cons_gen=intval($param['id_cons_gen']);

# carica i parametri di default sulla tabella
$sql = $dbi->prepare("SELECT * FROM ".$prefix."_config");
$sql->execute();
while($riga = $sql->fetchAll(PDO::FETCH_ASSOC)){$row=$riga[0];
$sitename = stripslashes($row['sitename']);
$siteurl = $row['siteurl'];
$site_logo = $row['site_logo'];
$startdate = $row['startdate'];
$adminmail = $row['adminmail'];
$tema = $row['tema'];
$language = $row['language'];
$blocco = intval($row['blocco']);
$fileout = intval($row['fileout']);
$copyright = $row['copyright'];
$Versione = $row['versione'];
$patch = $row['patch'];
$siteistat = intval($row['siteistat']);
$multicomune = intval($row['multicomune']);
$flash = intval($row['flash']);
$displayerrors = $row['displayerrors'];
$gkey = $row['gkey'];
$googlemaps = intval($row['googlemaps']);
$editor = intval($row['editor']);
$tema_on = intval($row['tema_on']);
$ed_user = $row['ed_user'];
$tema_colore= $row['tema_colore'];
#tema mobile
}



# altre config
$sql = $dbi->prepare("SELECT * FROM ".$prefix."_ele_comune where id_comune='$siteistat' ");
$sql->execute();
$riga = $sql->fetchAll(PDO::FETCH_ASSOC);
$row=$riga[0];
$id_cons_pred = intval($row['id_cons']);
if($id_cons_pred=='0')$id_cons_pred='';
if(!isset($id_cons_gen)) $id_cons_gen=$id_cons_pred;
# carica il metodo d'hontd 
##$sql = $dbi->prepare("SELECT * FROM ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen' ");
##$sql->execute();

$param=strip_tags(strtolower($_SERVER['REQUEST_METHOD'])) == 'get' ? $_GET : $_POST;
////////////////////
#funzione di backup
if (isset($param['op']) and $param['op']=='backup')
{
$id_cons_bak=intval($param['id_cons_gen']);
if (isset($param['id_comune'])) $id_combak=intval($param['id_comune']); else $id_combak=$_SESSION['id_comune'];
$sql = $dbi->prepare("SELECT id_cons,id_conf FROM ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_bak' and id_comune='$id_combak'");
$sql->execute();
list($id_cons,$hondt) = $sql->fetch(PDO::FETCH_NUM);

// incluso in consiglieri.php, ma io carico le vecchie variabili per compatibilit'a all'indietro
if($hondt>=1){
# proiezione consiglio
      $res2 = $dbi->prepare("SELECT * FROM ".$prefix."_ele_conf where id_conf='$hondt'");
      $res2->execute();
      $row=$res2->fetch(PDO::FETCH_ASSOC); 
	  
      $descrizione_consiglio = $row['descrizione'];
      $LIMITE = intval($row['limite']);
      $CONSIN = intval($row['consin']);
      $INFPREMIO=intval($row['infpremio']);
      $SUPSBARRAMENTO=intval($row['supsbarramento']);
      $SUPMINPREMIO=intval($row['supminpremio']);
      $SUPPREMIO=intval($row['suppremio']);
      $LISTINFSBAR=intval($row['listinfsbar']);
      $LISTINFCONTA=intval($row['listinfconta']);
      $LISTSUPCONTA=intval($row['listsupconta']);
      $SUPMINPREMIO=intval($row['supminpremio']);
      $INFMINPREMIO=intval($row['infminpremio']);
}


include("modules/Elezioni/backup.php");
die();
}
///////////////////
// lingua x demo
if (isset($param['newl'])){
	$newl=$param['newl'];
	if (file_exists("modules/Elezioni/language/lang-$newl.php")){ $lang=$newl;$_SESSION['newl']="$lang";
	}
}

// seesioni per flash, blocco e linguaggio, tour

if (isset($param['block'])){
   $blocco=$param['block'];
   $_SESSION['newblock']="$blocco";
        }
if (isset($_SESSION['newblock'])) $blocco=$_SESSION['newblock']; 



// linguaggio
if (isset($_SESSION['newl'])) $lang=$_SESSION['newl']; 
//else $lang=$lang;
if (! isset($lang)) $lang=$language;
if (strlen($lang)!=2) $lang=$language;

// flash x demo
if (isset($param['flash'])){
        $flash=$param['flash'];
        $_SESSION['newflash']="$flash";
        }
if (isset($_SESSION['newflash'])) $flash=$_SESSION['newflash']; 

if (isset($param['tema'])){
        $tema=$param['tema'];
        $tema=htmlentities($tema); // evita xss
        if(preg_match("/%/i", $tema)) $tema="default";// evita xss
        $_SESSION['newtema']="$tema";
	
        }
if (isset($_SESSION['newtema'])) {
	$tema=$_SESSION['newtema']; 
	if (preg_match("/%/i",$_SESSION['newtema'])) $_SESSION['newtema']="default"; // xss
}

//ritorno disabilita tema cellulare dal file backtoapp e dal footer lo riattiva
if (isset($param['nocell'])){
$nocell=$param['nocell'];
$_SESSION['newcell']="$nocell";
}
if (isset($_SESSION['newcell'])) $nocell=$_SESSION['newcell']; 




$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
$PHP_SELF=$_SERVER['PHP_SELF'];
$file=(isset($param['file'])) ? htmlentities($param['file']):"index";
if($tema=='spectral' and $file!='foto') $file="index";
$name=(isset($param['name'])) ? htmlentities($param['name']):"Elezioni";
$op=(isset($param['op'])) ? htmlentities($param['op']):"gruppo";
$id_comune=(isset($param['id_comune'])) ? intval($param['id_comune']): $siteistat;
#$op=(isset($param['id_cons_gen'])) ? intval($param['id_cons_gen']):$id_cons_pred;

########
$id_cons_gen=(isset($param['id_cons_gen'])) ? intval($param['id_cons_gen']) : 0;
$csv=(isset($param['csv'])) ? intval($param['csv']):"0";
$sql = $dbi->prepare("SELECT id_cons FROM ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen' and id_comune='$id_comune'");
$sql->execute();
list($id_cons) = $sql->fetch(PDO::FETCH_NUM);
$percorso=$name;
if(!file_exists("temi/$tema/index.php")) {$tema='default'; $_SESSION['tema']=$tema;}
if($name=='modelli') 
	$percorso='../modelli';
else {
	if($tema=='bootstrap' and !$csv) {
		$percorso='../temi/bootstrap';
	}
	###########
}
$modpath = "modules/$percorso/$file.php"; 

if (file_exists($modpath)) { 
	include($modpath);
} else {
	die ("Sorry, such file doesn't exist...:$modpath");
}







?>
