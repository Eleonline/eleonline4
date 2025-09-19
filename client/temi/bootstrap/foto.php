<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die ("You can't access this file dirrectly...");
}


$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;

if (isset($param['id_lista'])) $id_lista=intval($param['id_lista']); else $id_lista='';
if (isset($param['id_gruppo'])) $id_gruppo=intval($param['id_gruppo']); else $id_gruppo='';
if (isset($param['id_sede'])) $id_sede=intval($param['id_sede']); else $id_sede='';
if (isset($param['id_comune'])) $id_comune=intval($param['id_comune']); else $id_comune='';
if (isset($param['prefix'])) $prefix=$param['prefix'];
if (isset($param['pdfvis'])) $pdf=$param['pdfvis'];

if ($id_lista){
	$sql = "select stemma from ".$prefix."_ele_lista where id_lista='$id_lista'";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($stemma) = $res->fetch(PDO::FETCH_NUM);
}elseif ($id_gruppo){
	$sql = "select programma,stemma from ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($programma,$stemma) = $res->fetch(PDO::FETCH_NUM);
	if(isset($pdf)) $stemma = $programma;
}elseif ($id_sede){
	$sql = "select mappa from ".$prefix."_ele_sede where id_sede='$id_sede'";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($stemma) = $res->fetch(PDO::FETCH_NUM);
}elseif ($id_comune){
	$sql = "select stemma from ".$prefix."_ele_comune where id_comune='$id_comune'";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($stemma) = $res->fetch(PDO::FETCH_NUM);
}else{
die();
}


// nessuno stemma immagine vuota
if ($stemma=="" && is_readable('modules/Elezioni/images/vuoto.png')){
	$stemma =  fread( fopen( 'modules/Elezioni/images/vuoto.png', 'r' ), filesize( 'modules/Elezioni/images/vuoto.png' ) );}
if(isset($pdf)) 
{
  if (strstr($_SERVER['HTTP_USER_AGENT'],"MSIE"))
    {
    	header('Cache-Control: public'); 
    	header("Content-Type: application/pdf"); 
	header("Content-Transfer-Encoding: binary");
        header("Content-Disposition: attachment; filename=Programma.pdf");
    }else{
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=Programma.pdf");
    } 
}

echo $stemma;
?>
