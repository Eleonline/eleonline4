<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';

global $prefix,$id_parz,$id_sez,$dbi,$id_cons,$id_cons_gen,$id_lista;
if (isset($_POST['id_sez'])) $id_sez=intval($_POST['id_sez']); else $id_sez='0';
if (isset($_POST['id_lista'])) $id_lista=intval($_POST['id_lista']);
if (isset($_POST['op']) and $_POST['op']=='aggiornaCandidato') { $_SESSION['id_lista']=$id_lista; include('pagina_voti_preferenza.php'); return;}
$salvato=0;
include("ele_controlli.php");
include("ele_colora_sez.php");

foreach($_POST as $key=>$val) 
	if(substr($key,0,5)=='cand-') {
		$id_cand=substr($key,5);
		if($id_cand) {
			$sql="select num_cand from ".$prefix."_ele_candidato where id_cand=:id_cand";
			$res = $dbi->prepare("$sql");
			try {
			$res->bindParam(':id_cand', $id_cand, PDO::PARAM_INT);
			$res->execute();
			} catch(PDOException $e) {
				echo $e->getMessage();
				$salvato=1;
			}
			list($num_cand)=$res->fetch(PDO::FETCH_NUM);
			$sql="select count(0) from ".$prefix."_ele_voti_candidato where id_cand=:id_cand and id_sez=:id_sez";
			$res = $dbi->prepare("$sql");
			try {
			$res->bindParam(':id_cand', $id_cand, PDO::PARAM_INT);
			$res->bindParam(':id_sez', $id_sez, PDO::PARAM_INT);
			$res->execute();
			} catch(PDOException $e) {
				echo $e->getMessage();
				$salvato=1;
			}
			list($inserita)=$res->fetch(PDO::FETCH_NUM);
			if($inserita){
				$sql="update ".$prefix."_ele_voti_candidato set voti=:val where id_cand=:id_cand and id_sez=:id_sez";
				$res = $dbi->prepare("$sql");
				try {
				$res->bindParam(':id_cand', $id_cand, PDO::PARAM_INT);
				$res->bindParam(':id_sez', $id_sez, PDO::PARAM_INT);
				$res->bindParam(':val', $val, PDO::PARAM_INT);
				$res->execute();
				} catch(PDOException $e) {
					echo $e->getMessage();
					$salvato=1;
				}
			}else{
				$sql="insert ".$prefix."_ele_voti_candidato (id_cons,id_cand,id_sez,num_lista,voti) values (:id_cons,:id_cand,:id_sez,:num_lista,:val)"; 
				$res = $dbi->prepare("$sql");
				try {
				$res->bindParam(':id_cons', $id_cons, PDO::PARAM_INT);
				$res->bindParam(':id_cand', $id_cand, PDO::PARAM_INT);
				$res->bindParam(':num_lista', $num_lista, PDO::PARAM_INT);
				$res->bindParam(':id_sez', $id_sez, PDO::PARAM_INT);
				$res->bindParam(':val', $val, PDO::PARAM_INT);
				$res->execute();
				} catch(PDOException $e) {
					echo $e->getMessage();
					$salvato=1;
				}
			}
		}
	}
	if($salvato){
		$datal=date('Y-m-d');
		$orariol=date(' H:i:s');
		$riga=addslashes($sql);
		$sql="delete from ".$prefix."_ele_log where `id_cons`=:id_cons and ((`ora` > :orariol and `data`=:datal) or `data` > :datal)"; 
		$res = $dbi->prepare("$sql");
		try {
		$res->bindParam(':id_cons', $id_cons, PDO::PARAM_INT);
		$res->bindParam(':orariol', $orariol, PDO::PARAM_STR);
		$res->bindParam(':datal', $datal, PDO::PARAM_STR);
		$res->execute();
		} catch(PDOException $e) {
			echo $e->getMessage();
			$salvato=1;
		}
		$sqlog="insert into ".$prefix."_ele_log values(:id_cons,:id_sez,:aid,:datal,:orariol,'',:riga,'".$prefix."_ele_voti_parziale')";
		$res = $dbi->prepare("$sqlog");
		try {
		$res->bindParam(':id_cons', $id_cons, PDO::PARAM_INT);
		$res->bindParam(':id_sez', $id_sez, PDO::PARAM_INT);
		$res->bindParam(':aid', $aid, PDO::PARAM_STR);
		$res->bindParam(':datal', $datal, PDO::PARAM_STR);
		$res->bindParam(':orariol', $orariol, PDO::PARAM_STR);
		$res->bindParam(':riga', $riga, PDO::PARAM_STR);
		$res->execute();
		} catch(PDOException $e) {
			echo $e->getMessage();
			$salvato=1;
		}
	}
	controllo_votil($id_cons,$id_sez,0);
	colora_sezione();


	include('pagina_voti_preferenza.php');

?>
