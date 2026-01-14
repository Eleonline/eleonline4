<?php
require_once '../includes/check_access.php';
global $prefix,$dbi,$id_cons,$id_comune;
$filebk='';

$sql="select * from ".$prefix."_ele_cons_comune where id_cons='$id_cons'" ;
$filebk=scarica_array($sql,$prefix."_ele_cons_comune");

$sql="select * from ".$prefix."_ele_link where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_link");

$sql="select * from ".$prefix."_ele_come where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_come");

$sql="select * from ".$prefix."_ele_numero where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_numero");

$sql="select * from ".$prefix."_ele_servizio where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_servizio");

$sql="select * from ".$prefix."_ele_rilaff where id_cons_gen='$id_cons_gen'" ;
$filebk.=scarica_array($sql,$prefix."_ele_rilaff");

$sql="select * from ".$prefix."_ele_voti_parziale where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_parziale");

$sql="select * from ".$prefix."_ele_circoscrizione where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_circoscrizione");

$sql="select * from ".$prefix."_ele_sede where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_sede");

$sql="select * from ".$prefix."_ele_sezione where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_sezione");

$sql="select * from ".$prefix."_ele_gruppo where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_gruppo");

$sql="select * from ".$prefix."_ele_lista where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_lista");

$sql="select * from ".$prefix."_ele_candidato where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_candidato");

$sql="select * from ".$prefix."_ele_voti_candidato where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_candidato");

$sql="select * from ".$prefix."_ele_voti_gruppo where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_gruppo");

$sql="select * from ".$prefix."_ele_voti_lista where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_lista");

$sql="select * from ".$prefix."_ele_voti_parziale where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_parziale");

$sql="select * from ".$prefix."_ele_voti_ref where id_cons='$id_cons'" ;
$filebk.=scarica_array($sql,$prefix."_ele_voti_ref");
#salva la variabile su file zip
#$zip = new ZipArchive();
$filename = "../../client/documenti/backup/file_bak_$id_cons.txt";
#if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
#    exit("Non è possibile aprire il file <$filename>\n");
	
#}
#$zip->addFromString("file_bak_$id_cons.txt", $filebk);
#$zip->close();
$file=fopen($filename, 'w');
fwrite($file, "$filebk");
fclose($file);
if(is_file($filename))
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
		foreach ($lista as $key=>$val) {$riga[$key]=base64_encode($val);
			if ($x++) $fr.= ":";
			
			$fr.= "'".$riga[$key]."'";
		}
		$fr.= "\n";
	}
	return($fr);
}
?>
