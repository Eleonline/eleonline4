<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Roberto Gigli & Luciano Apolito                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* Modulo visualizzazione immagini blob                                 */
/* Amministrazione                                                      */
/************************************************************************/

if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';

//session_start();
$aid=$_SESSION['username'];
if (!$aid) return;

$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;

if (isset($param['id_lista'])) $id_lista=intval($param['id_lista']); else $id_lista='';
if (isset($param['id_gruppo'])) $id_gruppo=intval($param['id_gruppo']); else $id_gruppo='';
if (isset($param['id_sede'])) $id_sede=intval($param['id_sede']); else $id_sede='';
if (isset($param['id_comune'])) $id_comune2=intval($param['id_comune']); else $id_comune2='';
#if (isset($param['prefix'])) $prefix=$param['prefix'];
global $prefix;


if ($id_lista){
	$sql = "select stemma from ".$prefix."_ele_lista where id_lista=".$id_lista;
	$res = $dbi->prepare("$sql");
	$res->execute(); 
	list($stemma) = $res->fetch(PDO::FETCH_NUM);
}elseif ($id_gruppo){
	$sql = "select stemma from ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
	$res = $dbi->prepare("$sql");
	$res->execute(); 
	list($stemma) = $res->fetch(PDO::FETCH_NUM);
}elseif ($id_sede){
	$sql = "select mappa from ".$prefix."_ele_sede where id_sede=".$id_sede;
	$res = $dbi->prepare("$sql");
	$res->execute(); 
	list($stemma) = $res->fetch(PDO::FETCH_NUM);
}elseif ($id_comune2){
	$sql = "select stemma from ".$prefix."_ele_comune where id_comune=".$id_comune2;
	$res = $dbi->prepare("$sql");
	$res->execute(); 
	list($stemma) = $res->fetch(PDO::FETCH_NUM);
}else{
return;
}


// nessuno stemma immagine vuota
if ($stemma==""){ 

$sql = "select stemma from ".$prefix."_ele_comune where id_comune='0'"; 
$res = $dbi->prepare("$sql");
$res->execute(); 
if($res->rowCount())
	{
	$dati = $res->fetch(PDO::FETCH_BOTH);
	$stemma = $dati['stemma'];
	}
}
echo $stemma;
?>
