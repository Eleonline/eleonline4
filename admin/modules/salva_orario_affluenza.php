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


if (isset($_GET['data'])) $data=$_GET['data']; else $data='';
if (isset($_GET['ora'])) $ora=$_GET['ora']; else $ora='';
if (isset($_GET['minuto'])) $minuto=$_GET['minuto']; else $minuto='';
if (isset($_GET['op'])) $op=$_GET['op']; else $op='';
#if (isset($_GET['scrutinata'])) {$scrutinata=$_GET['scrutinata']==false ? false : true;}else $scrutinata=false;
if(!$op)
	$orario="$ora:$minuto:00";
else
	$orario="$ora:00";
global $prefix,$fileout,$aid,$id_cons_gen;
$id_cons=$_SESSION['id_cons'];
$salvato=1;
if($op=='cancella'){
	list ($giorno,$mese,$anno)=explode('/',$data);
	$data="$anno/$mese/$giorno";
	$sql="delete from ".$prefix."_ele_rilaff where id_cons_gen='$id_cons_gen' and data='$data' and orario='$orario'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	if($res->rowCount()){
#		echo "Orario eliminato";
		include('modules/elenco_rilevazioni.php');
		return;
	}
}
$sql="select * from ".$prefix."_ele_rilaff where id_cons_gen='$id_cons_gen' and data='$data' and orario='$orario'";
$res = $dbi->prepare("$sql");
$res->execute();
if($res->rowCount()){
	echo "<tr><td colspan=\"3\">Questo orario è già stato inserito</td></tr>";
	include('modules/elenco_rilevazioni.php');
	return;
}
$sql="insert into ".$prefix."_ele_rilaff (id_cons_gen, orario,data) values('$id_cons_gen', '$orario', '$data')";

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
		$datal=date('Y-m-d');
		$orariol=date(' H:i:s');
		$riga=addslashes($sql);
		$sqlog="insert into ".$prefix."_ele_log values('$id_cons','0','$aid','$datal','$orariol','','$riga','".$prefix."_ele_rilaff')";
		$res = $dbi->prepare("$sqlog");
		$res->execute();
#		echo "Nuovo orario di rilevazione inserito";
	}else{
		echo "<tr><td colspan=\"3\">Errore, impossibile salvare i dati</td></tr>";
	}
	include('modules/elenco_rilevazioni.php');

?>
