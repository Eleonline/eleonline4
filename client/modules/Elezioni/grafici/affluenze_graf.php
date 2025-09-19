<?php

/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

include ("jpgraph.php");
include ("jpgraph_pie.php");
if(file_exists(TTF_DIR))
	define("CURFONT1",FF_DV_SANSSERIF);
else 
	define ("CURFONT1",FF_FONT1);


$e=$_GET['e'];$f=$_GET['f'];$e1=$_GET['e1'];$f1=$_GET['f1'];
$titolo=$_GET['titolo'];
if (isset($_GET['cop'])) $cop=$_GET['cop'];else $cop='';
if (isset($_GET['x'])) $dim_x=$_GET['x']; else $dim_x='300';
if (isset($_GET['y'])) $dim_y=$_GET['y']; else $dim_y='200';
$data = array($e,$f);
$legend = array("$e $e1","$f $f1");
$graph = new PieGraph($dim_x,$dim_y,"auto");
$graph->SetShadow();

$graph->title->Set($titolo);
$graph->title->SetFont(CURFONT1,FS_BOLD);
$graph ->legend->Pos( 0.02,0.85,"left" ,"center");
$graph->SetBackgroundImage("../images/logo.jpg",BGIMG_COPY);
// testo
$txt =new Text("$cop");
$txt->Pos( 2,180);
$txt->SetColor( "black");
$graph->AddText( $txt);



$p1 = new PiePlot($data);
$p1->value->SetFormat('');
$p1->value->Show();
$p1->SetLegends($legend);
$p1->SetCenter(0.5,0.5);

$graph->Add($p1);
$graph->Stroke();

?>


