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
$a=$_GET['a'];$b=$_GET['b'];$c=$_GET['c'];$d=$_GET['d'];
$a1=$_GET['a1'];$b1=$_GET['b1'];$c1=$_GET['c1'];$d1=$_GET['d1'];
$titolo=$_GET['titolo'];
$cop=$_GET['cop'];
$logo=$_GET['logo'];
$data = array($a,$b,$c,$d);
$legend = array("$a $a1","$b $b1", "$c $c1", "$d $d1");
$graph = new PieGraph(450,300,"auto");
$graph->SetShadow();
$theme_class= new VividTheme;
$graph->SetTheme($theme_class);
$graph->title->Set($titolo);
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->legend->Pos( 0.0,0.41,"left" ,"center");
$graph->legend->SetColumns(1);
$graph->SetBackgroundGradient('white','yellow',GRAD_HOR,BGRAD_MARGIN);


//$graph->SetBackgroundImagePos(1, 100);
$graph->SetBackgroundImage("../images/$logo",BGIMG_COPY);

// black-white
//$graph->AdjBackgroundImage(0.4,0.3,-1);

$p1 = new PiePlot3D($data);
$p1->value->SetFormat('');
$p1->value->Show();
$p1->SetLegends($legend);
$p1->SetCenter(0.65,0.35);
$graph->Add($p1);
$p1->ExplodeSlice(1);
$graph->Stroke();

?>


