<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

include ("jpgraph.php");
include ("jpgraph_pie.php");
include ("jpgraph_pie3d.php");

$data = array($a,$b,$c,$d);
$legend = array(_VALIDI,_NULLI,_BIANCHI,_CONTESTATI);
$graph = new PieGraph(330,200,"auto");
$graph->SetShadow();

$graph->title->Set("A simple 3D Pie plot");
$graph->title->SetFont(FF_FONT1,FS_BOLD);

$p1 = new PiePlot3D($data);
$p1->ExplodeSlice(1);
$p1->SetCenter(0.45);
$p1->SetLegends($legend);
$graph ->img->SetImgFormat( "png");
$graph->Add($p1);
$graph->Stroke();


?>


