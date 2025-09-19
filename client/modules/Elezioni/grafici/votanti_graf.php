<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

include ("jpgraph.php");
include ("jpgraph_pie.php");
require_once ('jpgraph_pie3d.php');
$e=$_GET['e'];$f=$_GET['f'];$e1=$_GET['e1'];$f1=$_GET['f1'];
$titolo=$_GET['titolo'];
if (isset($_GET['cop'])) $cop=$_GET['cop'];else $cop='';
if (isset($_GET['x'])) $dim_x=$_GET['x']; else $dim_x='450';
if (isset($_GET['y'])) $dim_y=$_GET['y']; else $dim_y='300';
if (isset($_GET['logo'])) $logo=$_GET['logo'];else $logo='';
$data = array($e,$f);
$legend = array("$e $e1","$f $f1");
$graph = new PieGraph($dim_x,$dim_y,"auto");
$graph->SetShadow();
$theme_class= new VividTheme;
$graph->SetTheme($theme_class);
$graph->title->Set($titolo);
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->legend->Pos( 0,0.41,"left" ,"center");
$graph->legend->SetColumns(1);
$graph->SetBackgroundImage("../images/$logo",BGIMG_COPY);

$graph->SetBackgroundGradient('red','yellow',GRAD_HOR,BGRAD_MARGIN);

$p1 = new PiePlot3D($data);
$p1->value->SetFormat('');
$p1->value->Show();
$p1->SetLegends($legend);
$p1->SetCenter(0.65,0.35);

$graph->Add($p1);
$p1->ExplodeSlice(1);
$graph->Stroke();

?>


