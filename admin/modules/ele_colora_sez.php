<?php
function colora_sezione() {
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("Location: ../index.php");
    exit;
}
global $id_cons,$id_circ,$id_sez,$dbi,$prefix,$genere,$id_cons_gen;

$sql="SELECT t1.voto_c,circo FROM ".$prefix."_ele_tipo as t1 left join ".$prefix."_ele_consultazione as t2 on t1.tipo_cons=t2.tipo_cons where id_cons_gen='$id_cons_gen'";
$res = $dbi->prepare("$sql");
$res->execute();
list($votoc,$circo)=$res->fetch(PDO::FETCH_NUM);
if ($circo) $iscirco="and id_circ=$id_circ"; else $iscirco=''; 
$sql="select * from ".$prefix."_ele_controllo where id_cons='$id_cons' and id_sez='$id_sez'";
$resc = $dbi->prepare("$sql");
$resc->execute();
$perr=$resc->rowCount();
#list($saff,$stato)=$resc->fetch(PDO::FETCH_NUM);
$sezstat=0;
if($perr) {
	$sezstat=1;
	$sql="UPDATE ".$prefix."_ele_sezione set colore='#FF3300' where id_cons='$id_cons' and id_sez='$id_sez'"; #ROSSO
	$res = $dbi->prepare("$sql");
	$res->execute();	
}else{ #candidati
	$sql="SELECT t2.id_lista FROM ".$prefix."_ele_voti_candidato as t1 left join ".$prefix."_ele_candidato as t2 on t1.id_cand=t2.id_cand where t1.id_cons='$id_cons' and t1.id_sez='$id_sez' group by t2.id_lista";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$liste=$res->rowCount(); 
	list($listescru)=$res->fetch(PDO::FETCH_NUM);
	if($res->rowCount() and $listescru==0) {$listescru=1;$liste=0;} 
	$sql="SELECT count(id_lista) FROM ".$prefix."_ele_lista where id_cons='$id_cons' $iscirco";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($ltot)=$res->fetch(PDO::FETCH_NUM);
	if(($liste && $liste==$ltot)){
		$sezstat=2;
		$sql="UPDATE ".$prefix."_ele_sezione set colore='#99CC33' where id_cons='$id_cons' and id_sez='$id_sez'"; ;#VERDE
		$res = $dbi->prepare("$sql");
		$res->execute();	
	}elseif($liste){
		$sezstat=2;
		$sql="UPDATE ".$prefix."_ele_sezione set colore='#99ee33' where id_cons='$id_cons' and id_sez='$id_sez'"; ;#VERDE
		$res = $dbi->prepare("$sql");
		$res->execute();	

	}
	if(!$sezstat) { #liste
		if($genere==2)
			$sql="SELECT id_gruppo FROM ".$prefix."_ele_voti_gruppo where id_cons='$id_cons' and id_sez='$id_sez'";
		else
			$sql="SELECT id_lista FROM ".$prefix."_ele_voti_lista where id_cons='$id_cons' and id_sez='$id_sez'";
		$res = $dbi->prepare("$sql");
		$res->execute();
		if($res->rowCount()>0){
			$sezstat=3;
			if($genere>3 and !$votoc)
				$sql="UPDATE ".$prefix."_ele_sezione set colore='#48D1CC' where id_cons='$id_cons' and id_sez='$id_sez'"; #"MEDIUMTORQUOISE"
			else
				$sql="UPDATE ".$prefix."_ele_sezione set colore='#99CC33' where id_cons='$id_cons' and id_sez='$id_sez'"; #VERDE
			$res = $dbi->prepare("$sql");
			$res->execute();
		}elseif(!$sezstat){ #gruppi
			if($genere!=4){
				if($genere)
					$sql="SELECT id_gruppo FROM ".$prefix."_ele_voti_gruppo where id_sez='$id_sez'";
				else{
					$sql="SELECT id_gruppo FROM ".$prefix."_ele_gruppo where id_cons='$id_cons'";
					$res = $dbi->prepare("$sql");
					$res->execute();
					$righeref=$res->rowCount();
					$sql="SELECT id_gruppo FROM ".$prefix."_ele_voti_ref where id_sez='$id_sez'";
				}
				$res = $dbi->prepare("$sql");
				$res->execute();
				$righe=$res->rowCount();
			}else $righe=0;
			if($righe){ 
				$sezstat=4;
				if(($genere==0 and $righe==$righeref) or $genere==1)
					$sql="UPDATE ".$prefix."_ele_sezione set colore='#99CC33' where id_cons='$id_cons' and id_sez='$id_sez'"; #VERDE					
				else $sql="UPDATE ".$prefix."_ele_sezione set colore='#B0C4DE' where id_cons='$id_cons' and id_sez='$id_sez'"; #"LIGHTSTEELBLUE"
				$res = $dbi->prepare("$sql");
				$res->execute();
			}elseif(!$sezstat){ #voti
				$sql="SELECT validi+nulli+bianchi+contestati as voti FROM ".$prefix."_ele_sezione where id_cons='$id_cons' and id_sez='$id_sez'";
				$res = $dbi->prepare("$sql");
				$res->execute();
				list($voti)=$res->fetch(PDO::FETCH_NUM);
				if($voti) {
					$sezstat=5;
					$sql="UPDATE ".$prefix."_ele_sezione set colore='#F5DEB3' where id_cons='$id_cons' and id_sez='$id_sez'"; #"WHEAT"
					$res = $dbi->prepare("$sql");
					$res->execute(); 
				}elseif(!$sezstat) {
					$sql="SELECT count(0) FROM ".$prefix."_ele_voti_parziale where id_cons='$id_cons' and id_sez='$id_sez'";
					$res = $dbi->prepare("$sql");
					$res->execute();
					list($righe)=$res->fetch(PDO::FETCH_NUM);
					$num_ril=$righe % 4;
					
					if($num_ril==0) {$cursez="#DCDCDC";}#gainsboro
					elseif($num_ril==1) {$cursez="#ADD8E6";}#lightblue
					elseif($num_ril==2) {$cursez="#7FFFD4";} #aquamarine
					elseif($num_ril==3) {$cursez="#E0FFFF";}#lightcyan 
					$sql="UPDATE ".$prefix."_ele_sezione set colore='$cursez' where id_cons='$id_cons' and id_sez='$id_sez'";
					$res = $dbi->prepare("$sql");
					$res->execute(); 
							
						
					
				}
			}
		}
	}
}
}

?>