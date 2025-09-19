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
global $prefix,$id_parz,$id_sez,$dbi,$id_cons,$id_cons_gen;
if (isset($_GET['id_sez'])) $id_sez=intval($_GET['id_sez']); else $id_sez='0';
$salvato=0;
foreach($_GET as $key=>$val)
	if(substr($key,0,5)=='lista') { 
		$id_lista=substr($key,5);
#		echo "<br>TEST: idgen:$id_cons_gen - idcons:$id_cons - idsez:$id_sez - id_lista:$id_lista - valori_lista:$key---$val";  
		if($id_lista) {
			$sql="select num_lista from ".$prefix."_ele_lista where id_lista='$id_lista'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($num_lista)=$res->fetch(PDO::FETCH_NUM);
			$sql="select count(0) from ".$prefix."_ele_voti_lista where id_lista='$id_lista'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($inserita)=$res->fetch(PDO::FETCH_NUM);
			if($inserita){
				$sql="update ".$prefix."_ele_voti_lista set voti='$val' where id_lista='$id_lista' and id_sez='$id_sez'";
				$res = $dbi->prepare("$sql");
				$res->execute();
				if($res->rowCount()) $salvato=1;
			}else{
				$sql="insert ".$prefix."_ele_voti_lista values ('$id_cons','$id_lista','$id_sez','$num_lista','$val','0','0'";
				$res = $dbi->prepare("$sql");
				$res->execute();
				if($res->rowCount()) $salvato=1; 
			}
		}
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
	}
	include("ele_controlli.php");
	controllo_votil($id_cons,$id_sez,$id_lista);
	include("ele_colora_sez.php");

/*
$sql="SELECT chiusa FROM ".$prefix."_ele_cons_comune where id_cons='$id_cons' ";
$res = $dbi->prepare("$sql");
$res->execute();
list($chiusa)=$res->fetch(PDO::FETCH_NUM);

if($chiusa!=1){
	$sql="SELECT tipo_cons FROM ".$prefix."_ele_consultazione where id_cons_gen='$id_cons_gen'" ;
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($tipo_cons) = $res->fetch(PDO::FETCH_NUM);
	$sql="SELECT circo FROM ".$prefix."_ele_tipo where tipo_cons='$tipo_cons' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($circo)=$res->fetch(PDO::FETCH_NUM);
	if ($circo) 
	{
		$sql="select id_circo from ".$prefix."_ele_sede where id_sede in (select id_sede from ".$prefix."_ele_sezione where id_sez=$id_sez')";
		$sth = $dbi->prepare("$sql");
		$sth->execute();
		list($id_circ) = $sth->fetch(PDO::FETCH_NUM);
		$iscirco="and id_circ=$id_circ";
	} else 
		$iscirco='';
*/
/* 	if($id_lista){
		$sql="select num_cand,id_cand from ".$prefix."_ele_candidati where id_cons='$id_cons' and id_lista='$id_lista' ORDER BY num_cand  ";
		$result = $dbi->prepare("$sql");
		$result->execute();
		while(list($i,$y)=$result->fetch(PDO::FETCH_NUM)) {
			$vot="voti$i";$cand="id_cand$i";
			if (isset($_GET[$cand])) $idcand[$i]=intval($_GET[$cand]); else $idcand[$i]=$y;
			if (isset($_GET[$vot])) $voti[$i]=intval($_GET[$vot]); else $voti[$i]='0';
		#	if (isset($_GET[$solo])) $solog[$i]=intval($_GET[$solo]); else $solog[$i]='0';

		}
	}else{
		$sql="select num_lista from ".$prefix."_ele_lista where id_cons='$id_cons' $iscirco ORDER BY num_lista ";
		$result = $dbi->prepare("$sql");
		$result->execute();
		$sololiste=0;
		while(list($i)=$result->fetch(PDO::FETCH_NUM)) {
			$vot="voti$i";$vnp="vnpl$i";$slp="slpl$i";$idlist="id_lista$i";
			if (isset($_GET[$vnp])) $vnpl[$i]=intval($_GET[$vnp]); else $vnpl[$i]='0';
			if (isset($_GET[$vot])) $voti[$i]=intval($_GET[$vot]); else $voti[$i]='0';
			if (isset($_GET[$slp])) $slpl[$i]=intval($_GET[$slp]); else $slpl[$i]='0';
			if (isset($_GET[$idlist])) $idlista[$i]=intval($_GET[$idlist]); else $idlista[$i]='0';
			$sololiste+=$slpl[$i];
		}	
	}
	if (!isset($fileout)) $fileout='';
	#if(($voti_u+$voti_d) and !$voti_t) $voti_t=$voti_u+$voti_d;
	if ($fileout) while (!$fp = fopen($fileout,"a"));

	$salvato=0;
*/	##################
	# if($id_lista){ 

	#$andlis="and 
/*		if($pwd3==1) {
			if($id_lista) $condiz="and id_cand in (select id_cand from ".$prefix."_ele_candidati where id_cons='$id_cons' and id_lista='$id_lista')"; else $condiz='';
			$sql="delete from ".$prefix."_ele_voti_candidati where id_sez='$id_sez' $condiz";
			$res = $dbi->prepare("$sql");
			$res->execute();
			$sql="delete from ".$prefix."_ele_controlli where tipo='candidato' and id_sez='$id_sez' ";
			$res = $dbi->prepare("$sql");
			$res->execute();
			if(!$id_lista){
				$sql="delete from ".$prefix."_ele_voti_lista where id_cons='$id_cons' and id_sez='$id_sez'";
				$res = $dbi->prepare("$sql");
				$res->execute();
				$sql="delete from ".$prefix."_ele_controlli where tipo='lista' and id_sez='$id_sez' ";
				$res = $dbi->prepare("$sql");
				$res->execute();
				$sql="update  ".$prefix."_ele_sezioni set validi_lista='0',contestati_lista='0',voti_nulli_lista='0',solo_gruppo='0',solo_lista='0' where id_cons='$id_cons' and id_sez='$id_sez'";
				$res = $dbi->prepare("$sql");
				$res->execute();
				if($res->rowCount()) $salvato=1; 
			}
			
		}elseif($id_lista){

			foreach($idcand as $idkey=>$idc){			
				$sql="select num_cand from ".$prefix."_ele_voti_candidati where id_sez='$id_sez' and id_cand='$idc'";
				$result = $dbi->prepare("$sql");
				$result->execute();
				if($result->rowCount()) 
					$sql="update ".$prefix."_ele_voti_candidati set voti='".$voti[$idkey]."' where id_sez='$id_sez' and id_cand='$idc'";
				else
					$sql="insert into ".$prefix."_ele_voti_candidati values('$id_cons','$idc','$id_sez','$idkey','".$voti[$idkey]."')";
				$result = $dbi->prepare("$sql");
				$result->execute();
				if($res->rowCount()) $salvato=1; 
			}
		}else{
				$sql="update  ".$prefix."_ele_sezioni set validi_lista='$validi',contestati_lista='$contestati',voti_nulli_lista='$votinulli',solo_gruppo='$sg' where id_cons='$id_cons' and id_sez='$id_sez'";
				$res = $dbi->prepare("$sql");
				$res->execute();
				if($res->rowCount()) $salvato=1; 
				$sql="select num_lista,id_lista from ".$prefix."_ele_voti_lista where id_cons='$id_cons' and id_sez='$id_sez'";
				$result = $dbi->prepare("$sql");
				$result->execute();
				if($result->rowCount()){
					$sql="select num_lista,id_lista from ".$prefix."_ele_lista where id_cons='$id_cons' $iscirco";
					$result = $dbi->prepare("$sql");
					$result->execute();
					while(list($i,$idl)=$result->fetch(PDO::FETCH_NUM)){
						$sql="update ".$prefix."_ele_voti_lista set num_lista='$i',voti='".$voti[$i]."',nulli_lista='".$vnpl[$i]."',solo_lista='".$slpl[$i]."' where num_lista='$i' and id_sez='$id_sez'";
						$res = $dbi->prepare("$sql");
						$res->execute();
						if($res->rowCount()) $salvato=1; 
					}
					
				}else{
					
					$sql="select num_lista,id_lista from ".$prefix."_ele_lista where id_cons='$id_cons' $iscirco";
					$result = $dbi->prepare("$sql");
					$result->execute();
					while(list($i,$idl)=$result->fetch(PDO::FETCH_NUM)){
						$sql="insert into  ".$prefix."_ele_voti_lista values('$id_cons','$idl','$id_sez','$i','".$voti[$i]."','".$vnpl[$i]."','".$slpl[$i]."')";
						$res = $dbi->prepare("$sql");
						$res->execute();
						if($res->rowCount()) $salvato=1; 
					}
				}
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
	}
	include("ele_controlli.php");
	controllo_votil($id_cons,$id_sez,$id_lista);
	include("ele_colora_sez.php");
	if ($fileout) fclose($fp);
}
$BASE=substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['REQUEST_URI'], "/")-16);
Header("Location: ".$BASE."admin.php?op=voti&id_cons_gen=$id_cons_gen&id_circ=$id_circ&id_sede=$id_sede&id_sez=$id_sez&do=spoglio&ops=$ops");
*/
#################################
?>
