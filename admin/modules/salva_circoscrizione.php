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

$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['descrizione'])) $descrizione=addslashes($param['descrizione']); else $descrizione='';
if (isset($param['id_circ'])) $id_circ=intval($param['id_circ']); else $id_circ='0';
if (isset($param['numero'])) $numero=addslashes($param['numero']); else $numero='';
if (isset($param['op'])) $op=addslashes($param['op']); else $op='';

global $prefix,$aid,$dbi,$id_cons_gen,$id_cons,$id_comune;
$salvato=0;
$query="select * from ".$prefix."_ele_circoscrizione where id_circ='$id_circ'";
$res = $dbi->prepare("$query");
$res->execute();
if($res->rowCount()) {
	if($op=='salva') {
		#update
		$sql="update ".$prefix."_ele_circoscrizione set descrizione='$descrizione',num_circ='$numero' where id_circ='$id_circ'";
		try {
			$compl = $dbi->prepare("$sql");
			$compl->execute();
		}
		catch(PDOException $e) {
			$salvato=1;
		}

	}elseif($op=='cancella'){	
		#delete
		$sql="select * from ".$prefix."_ele_sede where id_circ='$id_circ'";
		$compl = $dbi->prepare("$sql");
		$compl->execute();
		if(!$compl->rowCount()){
			$sql="delete from ".$prefix."_ele_circoscrizione where id_circ='$id_circ'";
			$compl = $dbi->prepare("$sql");
			$compl->execute();
			if(!$compl->rowCount()) $salvato=1;
		}else
			$salvato=1;
	}
}else{
	#insert
		$sql="insert into ".$prefix."_ele_circoscrizione values( '$id_cons','','$numero','$descrizione' )";
		$compl = $dbi->prepare("$sql");		
		$compl->execute(); 
		if(!$compl->rowCount()) $salvato=1;
}

if(!$salvato){
	$datal=date('Y-m-d');
	$orariol=date(' H:i:s');
	$riga=addslashes($sql);
	$sqlog="insert into ".$prefix."_ele_log values('$id_cons','0','$aid','$datal','$orariol','','$riga','".$prefix."_ele_comune')";
	$res = $dbi->prepare("$sqlog");
	$res->execute();
#		echo "Nuovo orario di rilevazione inserito";
}else{
	echo "<tr><td colspan=\"8\">Errore, impossibile salvare i dati - $sql</td></tr>";
}
include('modules/elenco_circoscrizioni.php');

?>
