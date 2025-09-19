<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

include ("jpgraph.php");
include ("jpgraph_bar.php");
if(file_exists(TTF_DIR))
	define("CURFONT1",FF_DV_SANSSERIF);
else 
	define ("CURFONT1",FF_FONT1);

$dati1=$_GET['dati1'];$dati2=$_GET['dati2'];$i=$_GET['i'];$l=$_GET['l'];$w=$_GET['w'];
$titolo=$_GET['titolo'];$descr=$_GET['descr'];$cop=$_GET['cop'];
$logo=$_GET['logo'];
$datay=unserialize(stripslashes($dati1)); // percentuali
$datax=unserialize(stripslashes($dati2)); // candidati


// $i= numero candidati
// $larghezza label
if (!isset($l)) $l=180;
if (!isset($w)) $w=500;
if ($l=='') $l=180;
if ($w=='') $w=500;

// Size of graph
$width=$w;
$height=$i*30;

// Set the basic parameters of the graph
$graph = new Graph($width,$height,'auto');
$graph->SetScale("textlin");

$top = 50;
$bottom = 80;
$left = $l;
$right = 40;
$graph->Set90AndMargin($left,$right,$top,$bottom);
//$graph->SetBackgroundImage("../images/$logo",BGIMG_COPY);

$graph->SetShadow();
$graph->SetBackgroundGradient('yellow','white',GRAD_HOR,BGRAD_MARGIN);
//$graph->SetBackgroundImage("../images/$logo",BGIMG_COPY);
// Setup title
$graph->title->Set("$titolo");
$graph->title->SetFont(CURFONT1,FS_BOLD,10);
$graph->subtitle->Set("$descr");

// Setup X-axis
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetFont(CURFONT1,FS_BOLD,8);
$graph->xaxis->SetColor("black","darkred");
// Some extra margin looks nicer
$graph->xaxis->SetLabelMargin(5);

// Label align for X-axis
$graph->xaxis->SetLabelAlign('right','center');

// Add some grace to y-axis so the bars doesn't go
// all the way to the end of the plot area
$graph->yaxis->scale->SetGrace(20);

// Setup the Y-axis to be displayed in the bottom of the
// graph. We also finetune the exact layout of the title,
// ticks and labels to make them look nice.
$graph->yaxis->SetPos('max');

// First make the labels look right
$graph->yaxis->SetLabelAlign('center','top');
$graph->yaxis->SetLabelFormat('%d');
$graph->yaxis->SetLabelSide(SIDE_RIGHT);

// The fix the tick marks
$graph->yaxis->SetTickSide(SIDE_LEFT);

// Finally setup the title
$graph->yaxis->SetTitleSide(SIDE_RIGHT);
$graph->yaxis->SetTitleMargin(35);

// To align the title to the right use :
$graph->yaxis->SetTitle("$cop",'high');
$graph->yaxis->title->Align('right');
$graph->yaxis->SetColor("black","darkred");
// To center the title use :
//$graph->yaxis->SetTitle('Turnaround 2002','center');
//$graph->yaxis->title->Align('center');

$graph->yaxis->title->SetFont(CURFONT1,FS_BOLD,12);
$graph->yaxis->title->SetAngle(0);



$graph->yaxis->SetFont(CURFONT1,FS_NORMAL); #era FF_FONT2
// If you want the labels at an angle other than 0 or 90
// you need to use TTF fonts
//$graph->yaxis->SetLabelAngle(0);

/*
// testo
$txt =new Text("$cop");
$txt->Pos( 100,$i*29);
$txt->SetColor( "red");
$graph->AddText( $txt);
*/








// Now create a bar pot
$bplot = new BarPlot($datay);
$bplot->SetFillColor("red");
$bplot->SetShadow();

//You can change the width of the bars if you like
$bplot->SetWidth(0.1);

// We want to display the value of each bar at the top
$bplot->value->Show();
$bplot->value->SetFont(CURFONT1,FS_BOLD,14);
$bplot->value->SetAlign('left','center');
$bplot->value->SetColor("red","yellow");
$bplot->value->SetFormat("%.2f %%"); #  &#37;");

// Add the bar to the graph
$graph->Add($bplot);


$graph->Stroke();
?>
