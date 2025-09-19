<?php
include ("jpgraph.php");
include ("jpgraph_line.php");

$grp1=$_GET['grp1'];$grp2=$_GET['grp2'];$altro=$_GET['altro'];
$desc=$_GET['desc'];
$ar1=$_GET['ar1'];$ar2=$_GET['ar2'];$ar3=$_GET['ar3'];

$ar1=unserialize(stripslashes($ar1));
$ar2=unserialize(stripslashes($ar2));
$ar3=unserialize(stripslashes($ar3));


//$datay = array(0.2980,0.3039,0.3020,0.3027,0.3015);
//$data2 = array(0.2910,0.3039,0.3000,0.3010,0.3000);
$graph = new Graph(450,320,"auto");
$graph->img->SetMargin(40,40,40,40);	

$graph->img->SetAntiAliasing();
$graph->SetScale("textlin");
$graph->SetShadow();
$graph->title->Set("Example of 10% top/bottom grace");
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Add 10% grace to top and bottom of plot
$graph->yscale->SetGrace(20,20);
$graph->legend->Pos(0.05,0.5,"right","center");

//$graph->xaxis-> SetTickLabels($desc);


$p1 = new LinePlot($ar1);
$p1->mark->SetType(MARK_FILLEDCIRCLE);
$p1->mark->SetFillColor("red");
$p1->mark->SetWidth(4);
$p1->SetColor("blue");
$p1->SetCenter();
$p1->SetLegend($grp1);


$p2 = new LinePlot($ar2);
$p2->mark->SetType(MARK_FILLEDCIRCLE);
$p2->mark->SetFillColor("gray");
$p2->mark->SetWidth(4);
$p2->SetColor("black");
$p2->SetCenter();
$p2->SetLegend($grp2);

$p3 = new LinePlot($ar3);
$p3->mark->SetType(MARK_FILLEDCIRCLE);
$p3->mark->SetFillColor("green");
$p3->mark->SetWidth(4);
$p3->SetColor("red");
$p3->SetCenter();
$p3->SetLegend("Altro");


$graph->Add($p1);
$graph->Add($p2);
$graph->Add($p3);

$graph->Stroke();





?>


