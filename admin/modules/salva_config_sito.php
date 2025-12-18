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
if (isset($_GET['mapsProvider'])) $mapsProvider=$_GET['mapsProvider']; else $mapsProvider='0';
if (isset($_GET['googleApiKey'])) $googleApiKey=$_GET['googleApiKey']; else $googleApiKey='';
if (isset($_GET['multicomune'])) $multicomune=$_GET['multicomune']; else $multicomune='0';
if (isset($_GET['defaultComune'])) $defaultComune=$_GET['defaultComune']; else $defaultComune='';
if (isset($_GET['op'])) $op=$_GET['op']; else $op='';

$id_cons=$_SESSION['id_cons'];
$salvato=1;

/* ===== UNICA PARTE MODIFICATA (QUI ERA L'ERRORE) ===== */
$sql = "update ".$prefix."_config 
        set siteistat = :siteistat,
            sitename = :sitename,
            siteurl = :siteurl,
            adminmail = :adminmail,
            multicomune = :multicomune,
            googlemaps = :googlemaps,
            gkey = :gkey";

try {
    $res = $dbi->prepare($sql);
    $res->execute([
        ':siteistat'   => $siteIstat,
        ':sitename'    => $siteName,
        ':siteurl'     => $siteUrl,
        ':adminmail'   => $emailAdmin,
        ':multicomune' => $multicomune,
        ':googlemaps'  => $mapsProvider,
        ':gkey'        => $googleApiKey
    ]);
}
catch(PDOException $e)
{
    echo $sql . "<br>" . $e->getMessage();
    $salvato=0;
}
/* ===== FINE PARTE MODIFICATA ===== */

if($salvato){
	echo "<br><button id=\"bottoneStato\" style=\"background-color:aquamarine;\" onfocusout=\"document.getElementById('bottoneStato').style.display='none'\" > Dati salvati correttamente</button>";
}else{
	echo "Errore di inserimento dati";
}

?>
