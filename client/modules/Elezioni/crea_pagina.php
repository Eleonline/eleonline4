<?php

/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

//testa_riga[contenuto in posizione 0][y]
//testa_colonna[x][contenuto in posizione 0]
//corpo[da 1 a x][da 1 a y]

if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}

global $name;

function crea_tabella($ar) {


global $prefix,$dbi,$pdf,$csv,$xls,$lang,$descr_cons,$prefix,$dbi,$id_comune,$descrizione,$siteistat,$min,$offset,$minsez,$offsetsez,$datipdf,$orienta,$formato;
$sql="SELECT descrizione,simbolo,stemma FROM ".$prefix."_ele_comuni where id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($descr_com,$simbolo,$stemma) = $res->fetch(PDO::FETCH_NUM);



$datipdf=stripslashes($datipdf);

$data=date("d-m-y G:i");

if ($xls==1) {

	$nomefile=strip_tags(str_replace(array('"', "'", ' ', ','), "_", $datipdf).".xls");
#	$nomefile=$_SESSION['nomefile'].".xls";
	header ("Content-Type: application/vnd.ms-excel");
	header ("Content-Disposition: inline; filename=$nomefile");

$datipdf=str_replace("<br/>","\n",$datipdf);

$datipdf=strip_tags($datipdf);
//			$cella=str_replace("</b>"," ",$cella);
//			$cella=str_replace("<br />"," ",$cella);
			

echo "$datipdf";

	$y=1;$i='';$e='';
		foreach ($ar as $riga) {
		$e++;
                        if($y) {
				echo "\n";
			}else{
				
				echo "\n";
				
			}

			
			foreach ($riga as $cella) {
			$cella=str_replace("<b>"," ",$cella);
			$cella=str_replace("</b>"," ",$cella);
			$cella=str_replace("<br>"," ",$cella);
			$cella=str_replace("<br />"," ",$cella);
			$cella=str_replace("<span class=\"red\"><i>"," - ",$cella);
			$cella=str_replace("<span class=\"red\" style=\"font-size:80%;\"><i>"," - ",$cella);
			$cella=str_replace("</span>%</i>","%",$cella);
			$cella=str_replace("</i></span>","",$cella);
			$cella=str_replace("_CIRCOS","Circoscrizione ",$cella);
			$cella=str_replace("_SEZIONI","Sezione ",$cella);
			$cella=str_replace("_TOT","Totale",$cella);
			$cella=str_replace("_COMPLESSIVO","Complessivo",$cella);
			 			
				echo "$cella \t";
					
				
			}
			if ($y) $y=0;
			
		}

	    echo"\n\n\nPowered by Eleonline http://www.eleonline.it \t \n";	
	    echo"by luciano apolito & roberto gigli - stampato: $data \t \n";
	    die();

}else{

  

	$bg='bgw';
	
	$tmpbg='bggray2'; 
	$tmpbg1='bgw';
	$tmpbg2='bggray';
	$tmpbg3='bggray2';
	
	$html ='';
	if ($pdf!="1" && $csv=="1")
	{
	
	$html .="<center><table><tr><td>
		<img src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_comune=".$id_comune."\" align=\"left\" alt=\"logo\" /> ";
	$html .= "</td><td>$datipdf</td></td></table>";
        }


	
	$html .= "<table class=\"table-docs\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" rules=\"all\">";
	



		$y=1;$i='';$e=0;
		foreach ($ar as $riga) {
		$e++;
			if($y) {
				$html .= "<tr class=\"bggray\">";
			}else{
				$bg= ($bg==$tmpbg) ? $tmpbg1:$tmpbg3;				  
				$html .= "<tr class=\"$bg\">";
				$i="class=\"td-130c\"";
			}
			foreach ($riga as $cella) {

			$cella=str_replace("_CIRCOS","Circoscrizione ",$cella);
			$cella=str_replace("_SEZIONI","Sezione ",$cella);
			$cella=str_replace("_TOT","Totale",$cella);
			$cella=str_replace("_COMPLESSIVO","",$cella);

			 if ($e==1){ 
				$t="<td style='text-align:left;'";$f="</td>";
			}else{ 
				$t="<td";$f="</td>";	
			}					
				$html .= "$t $i>$cella $f";
					$i='';
				
			}
			if ($y) $y=0;
			$html .= "</tr>";
		}
		$html .= "</table>";


		if ($pdf!="1" && $csv=="1"){
		      $html .="<br/><span class=\"copy\"><i>Stampato: $data</i></span>";
		      $html .="<br/><span class=\"copy\"><i>Eleonline by luciano apolito & roberto gigli - www.eleonline.it</i></span>";
		}
		$html .="</center><br />";

	    # inizio stampa a video o pdf


		if ($pdf!='1'){
		  echo $html;
	        }else{

		$style ="     
		<style type=\"text/css\">
		<!--
		.table-docs {
			font-size: 10px;
			padding: 1px;
			color: #000000;
			/* margin: 4px 4px 40px;*/
			border: solid  #666666;
			text-align:center;
		}
		.bggray 	{
			background: #d2d2d2; 
			FONT-SIZE: 13px; 
			FONT-FAMILY: Helvetica;
			border: 1px;
		}

		.bggray2 	{
			background: #EFEFEF; 
			FONT-SIZE: 13px; 
			FONT-FAMILY: Helvetica;
			border: 1px;
			}

		bggray3 	{
			background: #EFEFEF; 
			FONT-SIZE: 10px; 
			FONT-FAMILY:  Helvetica;
			text-align: left
			}

		.bgw	{
			background: #ffffff; 
			FONT-SIZE: 13px; 
			FONT-FAMILY: Helvetica;
			border: 1px;
	
		}
		.td-130 {
			float: right;
			margin: 0px 0 0 1px;
			width: 130px;	
			border: none;
			background-color: #d2d2d2;
			padding: 0px;
	
		}
		.td-130c {
			float: right;
			text-align:left;
			margin: 0px 0 0 1px;
			width: 130px;	
			border: none;
			padding: 0px;
		}		

		td {
		    border: .2px;
		}
		.red 	{
			BACKGROUND: none; 
			COLOR: #ff0000; 
			FONT-SIZE: 12px; 
			FONT-FAMILY:  Helvetica
		}
		.copy 	{
			background: #d2d2d2; 
			FONT-SIZE: 8px; 
			FONT-FAMILY: Helvetica;
			border: 1px;
		}
		.cen {
		margin: 10px auto 0 auto; 
		}
		-->
		</style>";


			# salva sull'hardisk lo stemma del comune
			$logo=verificasimbolo();
			$immagine= "<img src=\"modules/Elezioni/images/$logo\" alt=\"logo\" align=\"left\"/>";


		$style .="<table style=\"margin: auto;\" cellspace=0 border=0 cellpadding=0><tr><td border=0>$immagine</td> ";	
	
		$style .= "<td border=0>$datipdf</td></tr></table><br/><br/>";
		$style .= "<table style=\"margin: auto;\"><tr><td>$html</td></tr></table>";
		$style .= "<table style=\"margin: auto;\" cellspace=0 border=0 cellpadding=0><tr><td border=0>";

		$data=date("d-m-y G:i");
		$style .="<br/><span class=\"copy\"><i>Stampato il $data</i></span>";
		$style .="<br/><span class=\"copy\"><i>Eleonline by luciano apolito & roberto gigli - www.eleonline.it</i></span>";
		$style .="</td></tr></table>";
		$nomefile=strip_tags($datipdf).".pdf";
#		$nomefile=$_SESSION['nomefile'].".pdf";
		$nomefile=str_replace(" ", "_",$nomefile);
		// conversion HTML => PDF
		//$nomefile="elezioni_tabelle.pdf";
		//$orienta="P";
		$html2pdf = new Html2Pdf($orienta,$formato, 'it');
		$html2pdf->WriteHTML($style, isset($_GET['vuehtml']));
		$html2pdf->Output($nomefile);



	      }

	


   }
}





?>
