<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
global $prefix,$dbi,$id_comune;
if(!$id_comune and isset($_SESSION['id_comune'])) $id_comune=$_SESSION['id_comune']; 
$filebk='';

$sql="select * from ".$prefix."_access " ;
$filebk=scarica_array($sql,$prefix."_access");

$sql="select * from ".$prefix."_authors " ;
$filebk.=scarica_array($sql,$prefix."_authors");

$sql="select * from ".$prefix."_config " ;
$filebk.=scarica_array($sql,$prefix."_config");

$sql="select * from ".$prefix."_ele_comune " ;
$filebk.=scarica_array($sql,$prefix."_ele_comune");

$sql="select * from ".$prefix."_ele_conf " ;
$filebk.=scarica_array($sql,$prefix."_ele_conf");

$sql="select * from ".$prefix."_ele_cons_comune " ;
$filebk.=scarica_array($sql,$prefix."_ele_cons_comune");

$sql="select * from ".$prefix."_ele_consultazione " ;
$filebk.=scarica_array($sql,$prefix."_ele_consultazione");

$sql="select * from ".$prefix."_ele_controllo " ;
$filebk.=scarica_array($sql,$prefix."_ele_controllo");

$sql="select * from ".$prefix."_ele_fascia " ;
$filebk.=scarica_array($sql,$prefix."_ele_fascia");

$sql="select * from ".$prefix."_ele_link " ;
$filebk.=scarica_array($sql,$prefix."_ele_link");

$sql="select * from ".$prefix."_ele_come " ;
$filebk.=scarica_array($sql,$prefix."_ele_come");

$sql="select * from ".$prefix."_ele_operatore " ;
$filebk.=scarica_array($sql,$prefix."_ele_operatore");

$sql="select * from ".$prefix."_ele_log " ;
$filebk.=scarica_array($sql,$prefix."_ele_log");

$sql="select * from ".$prefix."_ele_numero " ;
$filebk.=scarica_array($sql,$prefix."_ele_numero");

$sql="select * from ".$prefix."_ele_servizio " ;
$filebk.=scarica_array($sql,$prefix."_ele_servizio");

$sql="select * from ".$prefix."_ele_rilaff" ;
$filebk.=scarica_array($sql,$prefix."_ele_rilaff");

$sql="select * from ".$prefix."_ele_voti_parziale " ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_parziale");

$sql="select * from ".$prefix."_ele_circoscrizione " ;
$filebk.=scarica_array($sql,$prefix."_ele_circoscrizione");

$sql="select * from ".$prefix."_ele_sede " ;
$filebk.=scarica_array($sql,$prefix."_ele_sede");

$sql="select * from ".$prefix."_ele_sezione " ;
$filebk.=scarica_array($sql,$prefix."_ele_sezione");

$sql="select * from ".$prefix."_ele_gruppo " ;
$filebk.=scarica_array($sql,$prefix."_ele_gruppo");

$sql="select * from ".$prefix."_ele_lista " ;
$filebk.=scarica_array($sql,$prefix."_ele_lista");

$sql="select * from ".$prefix."_ele_candidato " ;
$filebk.=scarica_array($sql,$prefix."_ele_candidato");

$sql="select * from ".$prefix."_ele_tema " ;
$filebk.=scarica_array($sql,$prefix."_ele_tema");

$sql="select * from ".$prefix."_ele_tipo " ;
$filebk.=scarica_array($sql,$prefix."_ele_tipo");

$sql="select * from ".$prefix."_ele_voti_candidato " ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_candidato");

$sql="select * from ".$prefix."_ele_voti_gruppo " ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_gruppo");

$sql="select * from ".$prefix."_ele_voti_lista " ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_lista");

$sql="select * from ".$prefix."_ele_voti_parziale " ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_parziale");

$sql="select * from ".$prefix."_ele_voti_ref " ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_ref");

        $sql="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '".$prefix."_ubicazione'";
        $res = $dbi->prepare("$sql");
        $res->execute();
        if($res->rowCount()) {
			$sql="select * from ".$prefix."_ubicazione " ;
			$filebk.=scarica_array($sql,$prefix."_ubicazione");

			$sql="select * from ".$prefix."_ws_comunicazione " ;
			$filebk.=scarica_array($sql,$prefix."_ws_comunicazione");

			$sql="select * from ".$prefix."_ws_funzione " ;
			$filebk.=scarica_array($sql,$prefix."_ws_funzione");

			$sql="select * from ".$prefix."_ws_sezione " ;
			$filebk.=scarica_array($sql,$prefix."_ws_sezione");

			$sql="select * from ".$prefix."_ws_tipo " ;
			$filebk.=scarica_array($sql,$prefix."_ws_tipo");
		}
#salva la variabile su file zip
#$zip = new ZipArchive();
if(!is_dir(dirname(__DIR__)."documenti/backup"))
	mkdir(dirname(__DIR__)."documenti/backup", 0777, true);
$filename = dirname(__DIR__)."documenti/backup/eleonlineDb_".$id_comune."_.txt";
#if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
#    exit("Non è possibile aprire il file <$filename>\n");
	
#}
#$zip->addFromString("file_bak_$id_cons.txt", $filebk);
#$zip->close();
$file=fopen($filename, 'w');
fwrite($file, "$filebk");
fclose($file);
#if(is_file($filename)) echo "TEST: creato"; else echo "Niente file";
function scarica_array($sql,$tab){
	global $dbi;
		
	$res_comune = $dbi->prepare("$sql");
	try {
		$res_comune->execute();
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
	
	$fr= "[$tab]\n";
	while ($lista = $res_comune->fetch(PDO::FETCH_NUM)) {
		$x=0;
		foreach ($lista as $key=>$val) {$riga[$key]=base64_encode($val ?? '');
			if ($x++) $fr.= ":";
			
			$fr.= "'".$riga[$key]."'";
		}
		$fr.= "\n";
	}
	return($fr);
}
?>
