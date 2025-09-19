<?php

if (!defined('ADMIN_FILE')) {
    die ("You can't access this file directly...");
}
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['id_cons'])) {$idcons=intval($param['id_cons']);}else $idcons=0;
if (isset($param['idws'])) {$idws=intval($param['idws']);}else die("Errore");
if (isset($param['data'])) {$data=htmlentities($param['data']);}else die("Errore");
if (isset($param['descr'])) $descr=htmlentities($param['descr']); else $descr='';
if (isset($param['tipo'])) $tipo=intval($param['tipo']); else die("Errore");

if($tipo==1){ #die("delete from ".$prefix."_ele_consultazionews where id_cons='$idcons' and codicews='$idws' and data='$data'");
	if($idcons) {
		$sql="delete from ".$prefix."_ws_consultazione where id_cons='$idcons' and codicews='$idws' and data='$data'";
		$mex="0";
	} else {
	$sql="select id_locale from ".$prefix."_ws_tipo where id_ws='$idws'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	list($tipolocale) = $sth->fetch(PDO::FETCH_BOTH);
	$sql="select t1.id_cons from ".$prefix."_ele_cons_comune as t1 left join ".$prefix."_ele_consultazione as t2 on t1.id_cons_gen=t2.id_cons_gen where t2.tipo_cons=$tipolocale and t2.data_inizio='$data'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	list($idcons2) = $sth->fetch(PDO::FETCH_BOTH);
		
	$sql="insert into ".$prefix."_ws_consultazione values('$idcons2','$idws','$data','$descr')";
	$mex="$idcons2";
	}
	try { 
		$sth = $dbi->prepare("$sql");
		$sth->execute();
	}catch(PDOException $e)
	{
		die('Errore: ' .$e->getMessage());
	}
	echo "$mex";
}
/*
elseif($tipo==2) {
	$sql="select eletto from ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	list($eletto) = $sth->fetch(PDO::FETCH_BOTH);
	if($eletto==0) $eletto=1; else $eletto=0;
	$sql="update ".$prefix."_ele_gruppo set eletto='$eletto' where id_gruppo='$id_gruppo'";
	try {
		$sth = $dbi->prepare("$sql");
		$sth->execute();	
	}catch(PDOException $e){
		die('Errore: ' .$e->getMessage());
	}
}elseif($tipo==3) {
	if(!isset($id_cand)) die("Errore");
	$sql="select eletto from ".$prefix."_ele_candidati where id_cand='$id_cand'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	list($eletto) = $sth->fetch(PDO::FETCH_BOTH);
	if($eletto==0) $eletto=1; else $eletto=0;
	$sql="update ".$prefix."_ele_candidati set eletto='$eletto' where id_cand='$id_cand'";
	try {
		$sth = $dbi->prepare("$sql");
		$sth->execute();	
	}catch(PDOException $e){
		die('Errore: ' .$e->getMessage());
	}
} */

?>