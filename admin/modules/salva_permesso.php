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
if (isset($param['utente'])) $utente=addslashes($param['utente']); else $utente='';
if (isset($param['sedi'])) $sedi=addslashes($param['sedi']); else $sedi='0';
if (isset($param['sezioni'])) $sezioni=addslashes($param['sezioni']); else $sezioni='0';
if (isset($param['op'])) $op=addslashes($param['op']); else $op='';
global $prefix,$aid,$dbi,$id_comune,$id_cons;
if(!$utente) return;

if(!$sedi) $sedi=0;
if(!$sezioni) $sezioni=0;
$salvato=0;
if($sezioni) $permessi=16;
elseif($sedi) $permessi=32;
else $permessi=64;
$query="select * from ".$prefix."_ele_operatore where aid='$utente' and id_cons='$id_cons'";
$res = $dbi->prepare("$query");
$res->execute();
if($res->rowCount()) {
	if($op=='salva') { 
			#update
			$sql="update ".$prefix."_ele_operatore set id_sede='$sedi',id_sez='$sezioni', permessi='$permessi' where aid='$utente' and id_cons='$id_cons'";
			$compl = $dbi->prepare("$sql");
			$compl->execute(); 
			if($compl->rowCount()) $salvato=1;
	}elseif($op=='cancella'){ 
		#delete
		$sql="delete from ".$prefix."_ele_operatore where  aid='$utente' and id_cons='$id_cons'";
		$compl = $dbi->prepare("$sql");
		$compl->execute(); 
		if($compl->rowCount()) $salvato=1;
	}
}else{
	#insert
		$sql="insert into ".$prefix."_ele_operatore (id_cons, id_sede, id_comune, permessi, aid, id_circ,id_sez) values( '$id_cons','$sedi','$id_comune','$permessi','$utente','0','$sezioni')";
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
	echo "<tr><td colspan=\"8\">Errore, impossibile salvare i dati - $sql</td></tr>";
}

include('modules/elenco_permessi.php');

?>
