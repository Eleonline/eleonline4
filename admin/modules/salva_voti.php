<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/************************************************************************/
/* Modulo salva affluenze                                               */
/* Amministrazione                                                      */
/************************************************************************/
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("Location: ../index.php");
    exit;
}


if (isset($_GET['id_sez'])) $id_sez=intval($_GET['id_sez']); else $id_sez='';
if (isset($_GET['validi'])) $validi=intval($_GET['validi']); else $validi='0';
if (isset($_GET['nulle'])) $nulle=intval($_GET['nulle']); else $nulle='0';
if (isset($_GET['bianche'])) $bianche=intval($_GET['bianche']); else $bianche='0';
if (isset($_GET['vcontestati'])) $contestati=intval($_GET['vcontestati']); else $contestati='0';
if (isset($_GET['vnulli'])) $votinulli=intval($_GET['vnulli']); else $votinulli='0';
if (isset($_GET['delete'])) $delete=intval($_GET['delete']); else $delete='';
if (isset($_GET['scrutinata'])) {$scrutinata=$_GET['scrutinata']==false ? false : true;}else $scrutinata=false;

global $prefix,$id_parz,$genere,$fileout,$id_cons;
	
	$salvato=1;
	$sql="update  ".$prefix."_ele_sezione set validi='$validi', contestati='$contestati', nulli='$nulle',bianchi='$bianche', voti_nulli='$votinulli' where id_sez='$id_sez' "; #id_cons='$id_cons' and
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
		$sql="delete from ".$prefix."_ele_log where `id_cons`='$id_cons' and ((`ora` > '$orariol' and `data`='$datal') or `data` > '$datal')"; 
		$res = $dbi->prepare("$sql");
		$res->execute();
		$sqlog="insert into ".$prefix."_ele_log values('$id_cons','$id_sez','$aid','$datal','$orariol','','$riga','".$prefix."_ele_voti_parziale')";
		$res = $dbi->prepare("$sqlog");
		$res->execute();
		echo "salvato";
		include("ele_controlli.php");
		controllo_voti($id_cons,$id_sez);
		include("ele_colora_sez.php");	
	}else{
		echo "errore: $sql";
	}

?>
