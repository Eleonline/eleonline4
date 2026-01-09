<?php
global $dbi,$prefix;
$id_comune=intval($_GET['id_comune']);
$id_cons_gen=intval($_GET['id_cons_gen']);
$sql="select id_cons from ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen' and id_comune='$id_comune'" ;
	$res = $dbi->prepare("$sql");
	$res->execute();

list($id_cons)=$res->fetch(PDO::FETCH_NUM);
#echo "id_cons=$id_cons<br/>";
$sql="select * from ".$prefix."_ele_cons_comune where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_cons_comune");

$sql="select * from ".$prefix."_ele_link where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_link");

$sql="select * from ".$prefix."_ele_come where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_come");

$sql="select * from ".$prefix."_ele_numero where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_numero");

$sql="select * from ".$prefix."_ele_servizio where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_servizio");

$sql="select * from ".$prefix."_ele_rilaff where id_cons='$id_cons_gen'" ;
scarica_array($sql,$prefix."_ele_rilaff");

$sql="select * from ".$prefix."_ele_voti_parziale where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_voti_parziale");

$sql="select * from ".$prefix."_ele_circoscrizione where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_circoscrizione");

$sql="select * from ".$prefix."_ele_sede where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_sede");

$sql="select * from ".$prefix."_ele_sezione where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_sezione");

$sql="select * from ".$prefix."_ele_gruppo where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_gruppo");

$sql="select * from ".$prefix."_ele_lista where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_lista");

$sql="select * from ".$prefix."_ele_candidato where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_candidato");

$sql="select * from ".$prefix."_ele_voti_candidato where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_voti_candidato");

$sql="select * from ".$prefix."_ele_voti_gruppo where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_voti_gruppo");

$sql="select * from ".$prefix."_ele_voti_lista where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_voti_lista");

$sql="select * from ".$prefix."_ele_voti_parziale where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_voti_parziale");

$sql="select * from ".$prefix."_ele_voti_ref where id_cons='$id_cons'" ;
scarica_array($sql,$prefix."_ele_voti_ref");


function scarica_array($sql,$tab){
	global $dbi;
	$res_comune = $dbi->prepare("$sql");
	$res_comune->execute();
	echo "[$tab]\n";
	while ($lista = $res_comune->fetch(PDO::FETCH_NUM)) {
		$x=0;
		foreach ($lista as $key=>$val) {$riga[$key]=base64_encode($val);
			if ($x++) echo ":";
			
			echo "'".$riga[$key]."'";
		}
		echo "\n";
	}
}
?>
