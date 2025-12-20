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

if (isset($_POST['siteIstat'])) $siteIstat=$_POST['siteIstat']; else $siteIstat=$id_comune;
if (isset($_FILES['siteImage'])) $siteImage=$_FILES['siteImage']; else $siteImage='';
if (isset($_POST['siteImage'])) $imageName=$_POST['siteImage']; else $imageName='';
if (isset($_POST['siteName'])) $siteName=$_POST['siteName']; else $siteName='';
if (isset($_POST['siteUrl'])) $siteUrl=$_POST['siteUrl']; else $siteUrl='';
if (isset($_POST['emailAdmin'])) $emailAdmin=$_POST['emailAdmin']; else $emailAdmin='';
if (isset($_POST['mapsProvider'])) $mapsProvider=$_POST['mapsProvider']; else $mapsProvider='0';
if (isset($_POST['googleApiKey'])) $googleApiKey=$_POST['googleApiKey']; else $googleApiKey='';
if (isset($_POST['multicomune'])) $multicomune=$_POST['multicomune']; else $multicomune='0';
if (isset($_POST['defaultComune'])) $defaultComune=$_POST['defaultComune']; else $defaultComune='';
if (isset($_POST['op'])) $op=$_POST['op']; else $op='';

if(isset($siteImage['tmp_name'])) {
	move_uploaded_file($siteImage['tmp_name'],getcwd().'/documenti/'.$siteImage['name']);
	$img=$siteImage['tmp_name'];
	$nomeimg=$siteImage['name'];
}else $nomeimg=$imageName;
$id_cons=$_SESSION['id_cons'];
$salvato=1;
/* ===== UNICA PARTE MODIFICATA (QUI ERA L'ERRORE) ===== */
$sql = "update ".$prefix."_config 
        set siteistat = :siteistat,
            sitename = :sitename,
            siteurl = :siteurl,
			nome_testata = :nome_testata,
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
		':nome_testata' => $nomeimg,
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
	echo "<br><button id=\"bottoneStato\" style=\"background-color:aquamarine;\" onfocusout=\"document.getElementById('bottoneStato').style.display='none'\" > Dati salvati correttamente  </button>";
}else{
	echo "Errore di inserimento dati";
}

?>
