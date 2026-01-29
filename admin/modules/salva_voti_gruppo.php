<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';

global $prefix,$id_parz,$id_sez,$dbi,$id_cons,$id_cons_gen,$num_sez;
if (isset($_POST['id_sez'])) $id_sez=intval($_POST['id_sez']); else $id_sez='0';
if (isset($_POST['op']) and $_POST['op']=='aggiornaGruppo') {include('pagina_voti_gruppo.php'); return;}
$salvato=0;
include("ele_controlli.php");
include("ele_colora_sez.php"); 

foreach($_POST as $key=>$val) 
	if(substr($key,0,7)=='gruppo-') {
		$id_gruppo=substr($key,7);
		if($id_gruppo) { 
			$sql="select num_gruppo from ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($num_gruppo)=$res->fetch(PDO::FETCH_NUM);
			$sql="select count(0) from ".$prefix."_ele_voti_gruppo where id_gruppo='$id_gruppo' and id_sez=$id_sez";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($inserita)=$res->fetch(PDO::FETCH_NUM);
			if($inserita){
				$sql="update ".$prefix."_ele_voti_gruppo set voti='$val' where id_gruppo='$id_gruppo' and id_sez='$id_sez'";
				$res = $dbi->prepare("$sql");
				$res->execute();
				if($res->rowCount()) $salvato=1;
			}else{
				$sql="insert ".$prefix."_ele_voti_gruppo values ('$id_cons','$id_gruppo','$id_sez','$num_gruppo','$val','0','0')"; 
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
	controllo_votig($id_cons,$id_sez,0);
	colora_sezione();


	include('pagina_voti_gruppo.php');

?>
