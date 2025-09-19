<?php

include_once "jpgraph.php";
include "jpgraph_led.php";
$sez=$_GET['sez'];
$max=$_GET['max'];
// By default each "LED" circle has a radius of 3 pixels
$led = new DigitalLED74($aRadius=2,$aMargin=0.6);
$led->StrokeNumber($sez.' di '.$max,LEDC_YELLOW); 



?>
