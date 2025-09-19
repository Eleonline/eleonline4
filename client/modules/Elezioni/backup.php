<?php
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
global $prefix,$dbi;
if (isset($param['id_cons_gen']))
	 $id_cons_gen2=intval($param['id_cons_gen']);
if (isset($param['id_comune']))
	 $id_comune2=intval($param['id_comune']);
$sql="select descrizione from ".$prefix."_ele_consultazione  where id_cons_gen='$id_cons_gen2'";
	$result = $dbi->prepare("$sql");
	$result->execute();

list($nomeFile) = $result->fetch(PDO::FETCH_NUM);
#$nomeFile="backup";
header( 'Content-Type: application/octet-stream; charset=utf-8' );
header( 'Content-Disposition: attachment; filename="'.$nomeFile.'"' );
#header( 'Content-Length:'.strlen( $content ) );
header( 'Content-Transfer-Encoding: binary' );
include("backup2.php");
exit(0);

?>
