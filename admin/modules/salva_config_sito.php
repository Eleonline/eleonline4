<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/************************************************************************/
/* Modulo per il salvataggio del colore del tema                        */
/* Amministrazione                                                      */
/************************************************************************/
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("Location: ../index.php");
    exit;
}

global $prefix,$fileout,$aid,$id_cons_gen,$id_comune;

if (isset($_GET['siteIstat'])) $siteIstat=$_GET['siteIstat']; else $siteIstat=$id_comune;
if (isset($_GET['siteName'])) $siteName=$_GET['siteName']; else $siteName='';
if (isset($_GET['siteUrl'])) $siteUrl=$_GET['siteUrl']; else $siteUrl='';
if (isset($_GET['emailAdmin'])) $emailAdmin=$_GET['emailAdmin']; else $emailAdmin='';
if (isset($_GET['mapsProvider'])) $mapsProvider=$_GET['mapsProvider']; else $mapsProvider='';
if (isset($_GET['googleApiKey'])) $googleApiKey=$_GET['googleApiKey']; else $googleApiKey='';
if (isset($_GET['multicomune'])) $multicomune=$_GET['multicomune']; else $multicomune='';
if (isset($_GET['defaultComune'])) $defaultComune=$_GET['defaultComune']; else $defaultComune='';
if (isset($_GET['op'])) $op=$_GET['op']; else $op='';
#if (isset($_GET['scrutinata'])) {$scrutinata=$_GET['scrutinata']==false ? false : true;}else $scrutinata=false;


$id_cons=$_SESSION['id_cons'];
$salvato=1;

$sql="update ".$prefix."_config set siteistat='$siteIstat',sitename='$siteName', siteurl='$siteUrl', adminmail='$emailAdmin', multicomune='$multicomune', googlemaps='$mapsProvider', gkey='$googleApiKey'";

try {
		$res = $dbi->prepare("$sql");
		$res->execute();
	}
catch(PDOException $e)
	{
		echo $sql . "<br>" . $e->getMessage();
		$salvato=0;
	}                  
if($salvato){
	echo "<br><button id=\"bottoneStato\" style=\"background-color:aquamarine;\" onfocusout=\"document.getElementById('bottoneStato').style.display='none'\" > Dati salvati correttamente $id_comune</button>";
}else{
	echo "Errore di inserimento dati";
}

?>
