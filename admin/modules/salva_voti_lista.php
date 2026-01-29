<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';

global $prefix,$id_parz,$id_sez,$dbi,$id_cons,$id_cons_gen;
if (isset($_POST['id_sez'])) $id_sez=intval($_POST['id_sez']); else $id_sez='0';
if (isset($_POST['op']) and $_POST['op']=='aggiornaLista') {include('pagina_voti_lista.php'); return;}
$salvato=0;
include("ele_controlli.php");
include("ele_colora_sez.php");

foreach($_POST as $key=>$val) 
	if(substr($key,0,6)=='lista-') {
		$id_lista=substr($key,6);
		if($id_lista) {
			$sql="select num_lista from ".$prefix."_ele_lista where id_lista='$id_lista'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($num_lista)=$res->fetch(PDO::FETCH_NUM);
			$sql="select count(0) from ".$prefix."_ele_voti_lista where id_lista='$id_lista' and id_sez=$id_sez";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($inserita)=$res->fetch(PDO::FETCH_NUM);
			if($inserita){
				$sql="update ".$prefix."_ele_voti_lista set voti='$val' where id_lista='$id_lista' and id_sez='$id_sez'";
				$res = $dbi->prepare("$sql");
				$res->execute();
				if($res->rowCount()) $salvato=1;
			}else{
				$sql="insert ".$prefix."_ele_voti_lista values ('$id_cons','$id_lista','$id_sez','$num_lista','$val','0','0')"; 
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
	controllo_votil($id_cons,$id_sez,0);
	colora_sezione();


	include('pagina_voti_lista.php');

?>
