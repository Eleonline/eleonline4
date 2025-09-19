<?php
include ("jpgraph.php");
include ("jpgraph_line.php");

$titvoti=$_GET['titvoti'];
if(isset($_GET['titperc']) $titperc=$_GET['titperc']; else $titperc='';
$altro=$_GET['altro'];
$desc=$_GET['desc'];
$logo=$_GET['logo'];
$grp1=$_GET['grp1'];$grp2=$_GET['grp2'];$grp3=$_GET['grp3'];

$ar1=$_GET['ar1'];$ar2=$_GET['ar2'];$ar3=$_GET['ar3'];

$ar1=unserialize(stripslashes($ar1));
$ar2=unserialize(stripslashes($ar2));
$ar3=unserialize(stripslashes($ar3));
$desc=unserialize(stripslashes($desc));



$graph = new Graph(640,320,"auto");
$graph->img->SetMargin(40,40,40,40);	

//$graph->img->SetAntiAliasing();
$graph->SetScale("textlin");
$graph->SetShadow();
$graph->title->Set($titvoti);
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Add 10% grace to top and bottom of plot
$graph->yscale->SetGrace(20,20);
$graph->xscale->SetGrace(20,20);
$graph->xaxis-> SetTickLabels($desc);
$graph->legend->Pos(0.03,0.2,"right","center");

$graph->SetBackgroundImage("../images/$logo",BGIMG_COPY);
//$graph->SetBackgroundImage("../images/logo.jpg",BGIMG_COPY);
//$graph->SetBackgroundCountryFlag('ital',BGIMG_COPY,20);

if($grp1){
$p1 = new LinePlot($ar1);
$p1->value-> Show();
$p1->value->SetFormat( "%0.0f");
$p1->mark->SetType(MARK_FILLEDCIRCLE);
$p1->mark->SetFillColor("red");
$p1->mark->SetWidth(4);
$p1->SetColor("blue");
$p1->SetCenter();
$p1->SetLegend($grp1);
}
if($grp2){
$p2 = new LinePlot($ar2);
$p2->value->SetFormat( "%0.0f");
$p2->value-> Show();
$p2->mark->SetType(MARK_FILLEDCIRCLE);
$p2->mark->SetFillColor("gray");
$p2->mark->SetWidth(4);
$p2->SetColor("black");
$p2->SetCenter();
$p2->SetLegend($grp2);
}
if($grp3){
$p3 = new LinePlot($ar3);
$p3->value->SetFormat( "%0.0f");
$p3->value-> Show();
$p3->mark->SetType(MARK_FILLEDCIRCLE);
$p3->mark->SetFillColor("green");
$p3->mark->SetWidth(4);
$p3->SetColor("red");
$p3->SetCenter();
$p3->SetLegend($altro);
}

if($grp1)$graph->Add($p1);

if($grp2)$graph->Add($p2);
if($grp3)$graph->Add($p3);

$graph->Stroke();





?>


