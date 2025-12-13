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
if (isset($param['id_sez'])) $id_sez=intval($param['id_sez']); else $id_sez='0';
if (isset($param['id_sede'])) $id_sede=addslashes($param['id_sede']); else $id_sede='';
if (isset($param['maschi'])) $maschi=addslashes($param['maschi']); else $maschi='';
if (isset($param['femmine'])) $femmine=addslashes($param['femmine']); else $femmine='';
if (isset($param['numero'])) $numero=addslashes($param['numero']); else $numero='';
if (isset($param['op'])) $op=addslashes($param['op']); else $op='';

global $prefix,$aid,$dbi,$id_cons_gen,$id_cons,$id_comune;
$salvato=0;
$query="select * from ".$prefix."_ele_sezione where id_sez='$id_sez'";
$res = $dbi->prepare("$query");
$res->execute();
if($res->rowCount()) {
	if($op=='salva') {
		#update
		$sql="update ".$prefix."_ele_sezione set id_sede='$id_sede',maschi='$maschi',femmine='$femmine',num_sez='$numero' where id_sez='$id_sez'";
		try {
			$compl = $dbi->prepare("$sql");
			$compl->execute();
		}
		catch(PDOException $e) {
			$salvato=1;
		}

	}elseif($op=='cancella'){	
		#delete
		$sql="select * from ".$prefix."_ele_voti_parziale where id_sez='$id_sez'";
		$compl = $dbi->prepare("$sql");
		$compl->execute();
		if(!$compl->rowCount()){
			$sql="delete from ".$prefix."_ele_sezione where id_sez='$id_sez'";
			$compl = $dbi->prepare("$sql");
			$compl->execute();
			if(!$compl->rowCount()) $salvato=1;
		}else
			$salvato=2;
	}
}else{
	#insert
		$sql="insert into ".$prefix."_ele_sezione (id_cons,id_sede,num_sez,maschi,femmine) values( '$id_cons','$id_sede','$numero','$maschi','$femmine')";
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
	echo "<tr><th colspan=\"7\" style=\"text-align:center\">ATTENZIONE - Per questa sezione sono state inserite delle rilevazioni di voto. Non è possibile procedere con l'eliminazione</th></tr>";
}else{	
	echo "<tr><td colspan=\"7\">Errore, impossibile salvare i dati - $sql</td></tr>";
}
include('modules/elenco_sezioni.php');

?>
