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


if (isset($_GET['colore'])) $colore=$_GET['colore']; else $colore='';
#if (isset($_GET['scrutinata'])) {$scrutinata=$_GET['scrutinata']==false ? false : true;}else $scrutinata=false;

global $prefix,$fileout,$aid,$id_cons;
	
	$salvato=1;
	$sql="update  ".$prefix."_config set tema_colore='$colore'";
try {
		$res = $dbi->prepare("$sql");
		$res->execute();
	}
catch(PDOException $e)
	{
		echo $sql . "<br>" . $e->getMessage();
		$salvato=0;
	}                  
	$colore=str_replace('tema-', '', $colore);
	if($salvato){
		$datal=date('Y-m-d');
		$orariol=date(' H:i:s');
		$riga=addslashes($sql);
		$sqlog="insert into ".$prefix."_ele_log values('$id_cons','','$aid','$datal','$orariol','','$riga','".$prefix."_config - nuovo colore tema: $colore')";
		$res = $dbi->prepare("$sqlog");
		$res->execute();
		echo "Salvata l'impostazione del colore $colore per il tema bootstrap";
	}else{
		echo "Errore, nessun cambiamento dell'impostazione del colore";
	}

?>
