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
if (isset($param['indirizzo'])) $indirizzo=addslashes($param['indirizzo']); else $indirizzo='';
if (isset($param['id_circ'])) $id_circ=intval($param['id_circ']); else $id_circ='0';
if (isset($param['id_sede'])) $id_sede=addslashes($param['id_sede']); else $id_sede='';
if (isset($param['telefono'])) $telefono=addslashes($param['telefono']); else $telefono='';
if (isset($param['fax'])) $fax=addslashes($param['fax']); else $fax='';
if (isset($param['latitudine'])) $latitudine=addslashes($param['latitudine']); else $latitudine='';
if (isset($param['longitudine'])) $longitudine=addslashes($param['longitudine']); else $longitudine='';
if (isset($param['responsabile'])) $responsabile=addslashes($param['responsabile']); else $responsabile='';
if (isset($param['op'])) $op=addslashes($param['op']); else $op='';

global $prefix,$aid,$dbi,$id_cons_gen,$id_cons,$id_comune;
$salvato=0;
$query="select * from ".$prefix."_ele_sede where id_sede='$id_sede'";
$res = $dbi->prepare("$query");
$res->execute();
if($res->rowCount()) {
	if($op=='salva') {
		#update
		$sql="update ".$prefix."_ele_sede set indirizzo='$indirizzo',id_circ='$id_circ',responsabile='$responsabile',telefono1='$telefono',fax='$fax' where id_sede='$id_sede'";
		try {
			$compl = $dbi->prepare("$sql");
			$compl->execute();
		}
		catch(PDOException $e) {
			$salvato=1;
		}

	}elseif($op=='cancella'){	
		#delete
		$sql="select * from ".$prefix."_ele_sezione where id_sede='$id_sede'";
		$compl = $dbi->prepare("$sql");
		$compl->execute();
		if(!$compl->rowCount()){
			$sql="delete from ".$prefix."_ele_sede where id_sede='$id_sede'";
			$compl = $dbi->prepare("$sql");
			$compl->execute();
			if(!$compl->rowCount()) $salvato=1;
		}else
			$salvato=2;
	}
}else{
	#insert
		$sql="insert into ".$prefix."_ele_sede values( '$id_cons','','$id_circ','$indirizzo','$telefono','','$fax','$responsabile','','','$latitudine','$longitudine','','')";
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
}elseif($salvato==2){
	echo "<tr><th colspan=\"7\" style=\"text-align:center\">ATTENZIONE - Non è possibile eliminare una sede che contiene sezioni</th></tr>";
}else{	
	echo "<tr><td colspan=\"7\">Errore, impossibile salvare i dati - $sql</td></tr>";
}
include('modules/elenco_sedi.php');

?>
