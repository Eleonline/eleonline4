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


if (isset($_POST['id_sez'])) $id_sez=intval($_POST['id_sez']); else $id_sez='';
if (isset($_POST['validi'])) $validi=intval($_POST['validi']); else $validi='0';
if (isset($_POST['nulle'])) $nulle=intval($_POST['nulle']); else $nulle='0';
if (isset($_POST['bianche'])) $bianche=intval($_POST['bianche']); else $bianche='0';
if (isset($_POST['vcontestati'])) $contestati=intval($_POST['vcontestati']); else $contestati='0';
if (isset($_POST['vnulli'])) $votinulli=intval($_POST['vnulli']); else $votinulli='0';
if (isset($_POST['delete'])) $delete=intval($_POST['delete']); else $delete='';
if (isset($_POST['scrutinata'])) {$scrutinata=$_POST['scrutinata']==false ? false : true;}else $scrutinata=false;
if (isset($_POST['op']) and $op='aggiorna_voti') { include('pagina_voti_finali.php'); return; }
echo "TEST: $op :";
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
