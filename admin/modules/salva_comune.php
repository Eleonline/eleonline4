<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/************************************************************************/
/* Modulo salva affluenze                                               */
/* Amministrazione                                                      */
/************************************************************************/
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';

if (isset($_GET['descrizione'])) $descrizione=addslashes($_GET['descrizione']); else $descrizione='';
if (isset($_GET['op'])) $op=addslashes($_GET['op']); else $op='';
if (isset($_GET['indirizzo'])) $indirizzo=addslashes($_GET['indirizzo']); else $indirizzo='';
if (isset($_GET['cap'])) $cap=addslashes($_GET['cap']); else $cap='';
if (isset($_GET['email'])) $email=addslashes($_GET['email']); else $email='';
if (isset($_GET['centralino'])) $centralino=addslashes($_GET['centralino']); else $centralino='';
if (isset($_GET['fax'])) $fax=addslashes($_GET['fax']); else $fax='';
if (isset($_GET['fascia'])) $fascia=intval($_GET['fascia']); else $fascia='0';
if (isset($_GET['id_comune'])) $id_comune=addslashes($_GET['id_comune']); else $id_comune='';
if (isset($_GET['capoluogo'])) $capoluogo=intval($_GET['capoluogo']); else $capoluogo='0';
#if (isset($_GET['predefinito'])) $predefinito=addslashes($_GET['predefinito']); else $predefinito='';
global $prefix,$id_parz,$tempo,$username,$aid,$dbi,$genere;
$stemma=''; $simbolo='';
$salvato=0;
$query="select * from ".$prefix."_ele_comune where id_comune='$id_comune'";
$res = $dbi->prepare("$query");
$res->execute();
if($res->rowCount()) {
	if($op=='salva') {
		#update
		$sql="update ".$prefix."_ele_comune set descrizione='$descrizione',indirizzo='$indirizzo',cap='$cap',email='$email',centralino='$centralino',fax='$fax',fascia='$fascia',capoluogo='$capoluogo' where  id_comune='$id_comune'";
		$compl = $dbi->prepare("$sql");
		$compl->execute(); 
		if($compl->rowCount()) $salvato=1;
	}elseif($op=='cancella'){
		#delete
		$sql="delete from ".$prefix."_ele_comune where  id_comune='$id_comune'";
		$compl = $dbi->prepare("$sql");
		$compl->execute(); 
		if($compl->rowCount()) $salvato=1;
	}
}else{
	#insert
		$sql="insert into ".$prefix."_ele_comune values( '$id_comune','$descrizione','$indirizzo','$centralino','$fax','$email','$fascia','$capoluogo','$simbolo','$stemma','0','$cap','')";
		$compl = $dbi->prepare("$sql");
		$compl->execute(); 
		if($compl->rowCount()) $salvato=1;
}

if($salvato){
	$datal=date('Y-m-d');
	$orariol=date(' H:i:s');
	$riga=addslashes($sql);
	$sqlog="insert into ".$prefix."_ele_log values('$id_cons','0','$aid','$datal','$orariol','','$riga','".$prefix."_ele_comune')";
	$res = $dbi->prepare("$sqlog");
	$res->execute();
#		echo "Nuovo orario di rilevazione inserito";
}else{
	echo "<tr><td colspan=\"8\">Errore, impossibile salvare i dati</td></tr>";
}
include('modules/elenco_comuni.php');

/*if($salvato){
	echo "<br><button id=\"bottoneStato\" style=\"background-color:aquamarine;\" onfocusout=\"document.getElementById('bottoneStato').style.display='none'\" > Operazione eseguita correttamente </button>";
}else{
	echo "Errore di inserimento dati";
}
*/
#$BASE=substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['REQUEST_URI'], "/")-16);

#Header("Location: admin.php?op=6&id_cons_gen=$id_cons_gen&id_circ=$id_circ&id_sede=$id_sede&id_sez=$id_sez&ops=1&do=spoglio");


?>
