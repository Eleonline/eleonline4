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

require_once('inc/hpdf5/autoload.php');
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

global $name;

function crea_tabella($ar) {
	global $prefix,$dbi,$pdf,$csv,$xls,$lang,$descr_cons,$prefix,$dbi,$id_comune,$descrizione,$siteistat,$min,$offset,$minsez,$offsetsez,$datipdf,$orienta,$formato;
	$sql="SELECT descrizione,simbolo,stemma FROM ".$prefix."_ele_comuni where id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($descr_com,$simbolo,$stemma) = $res->fetch(PDO::FETCH_NUM);
	$datipdf=stripslashes("$datipdf");
	$data=date("d-m-y G:i");
	if ($xls==1) {
		$nomefile=strip_tags(str_replace(array('"', "'", ' ', ','), "_", $datipdf).".xls");
		if(!$nomefile) $nomefile="$descr_cons.xls";
		header ("Content-Type: application/vnd.ms-excel");
		header ("Content-Disposition: inline; filename=$nomefile");
		$datipdf=str_replace("<br/>","\n",$datipdf);
		$datipdf=strip_tags($datipdf);
		echo "$datipdf";
		$y=1;$i='';$e=0;
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
			$html .="<center><table><tr><td  align=\"middle\">
			<img src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_comune=".$id_comune."\" alt=\"logo\" /> ";
#			$html .= "</td><td>$datipdf";
			$html .= "</td></tr></table>";
        }
		if($pdf==1 or $csv==1) $html .= "<p style=\"text-align:center;\">$datipdf</p>";
		$html .= "<table class=\"table-docs\">";
		$y=1;$e=0;
		foreach ($ar as $riga) {
			$e++;
			$i=1;
			if($y) {
				$html .= "<tr class=\"bggray\">";
			}else{
				$bg= ($bg==$tmpbg) ? $tmpbg1:$tmpbg3;				  
				$html .= "<tr class=\"$bg\">";
			}
			foreach ($riga as $cella) {
				if($cella) {
					$cella=str_replace("_CIRCOS","Circoscrizione ",$cella);
					$cella=str_replace("_SEZIONI","Sezione ",$cella);
					$cella=str_replace("_TOT","Totale",$cella);
					$cella=str_replace("_COMPLESSIVO","",$cella);
				}
				if ($e==1){ 
					$t="<td";$f="</td>";
				}elseif($i){ 
					$t="<td style='text-align:left;'";$f="</td>";	
				}else{ 
					$t="<td";$f="</td>";	
				}					
				$html .= "$t >$cella $f";
				$i=0;
			}
			if ($y) $y=0;
			$html .= "</tr>";
		}
		$html .= "</table>";
		if ($pdf!="1" && $csv=="1"){
			$html .="<br/><span class=\"copy\"><i>Stampato: $data</i></span>";
			$html .="<br/><span class=\"copy\"><i>Eleonline by luciano apolito & roberto gigli - www.eleonline.it</i></span>";
			$html .="</center><br />";
		}

	    # inizio stampa a video o pdf
		if ($pdf!='1'){
			echo $html;
	    }else{// inzio
			 require_once('inc/tcpdf/tcpdf.php'); // O il percorso corretto

        $logo = verificasimbolo();
        $data = date("d/m/Y H:i");
        $nomefile = str_replace(" ", "_", strip_tags($datipdf)) . ".pdf";

        $orienta = isset($_GET['orienta']) ? $_GET['orienta'] : 'P';   // default: verticale
$formato = isset($_GET['formato']) ? $_GET['formato'] : 'A4';  // default: A4

$pdf = new TCPDF($orienta, 'mm', $formato, true, 'UTF-8', false);

        $pdf->SetMargins(5, 10, 5);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Eleonline');
        $pdf->SetTitle($datipdf);

        $pdf->SetFont('helvetica', '', 8);

        // Blocco: prime due colonne fisse (es: N. e Candidato), il resto per sezioni
        $colonne_fisse = 2;
        $colonne_totali = count($ar[0]);
        $colonne_per_pagina = 13; // N. max sezioni per pagina
        $blocco_colonne = $colonne_totali - $colonne_fisse;
        $blocchi = ceil($blocco_colonne / $colonne_per_pagina);

        for ($b = 0; $b < $blocchi; $b++) {
            $pdf->AddPage();

            // intestazione
            if (file_exists("modules/Elezioni/images/$logo")) {
                $pdf->Image("modules/Elezioni/images/$logo", 10, 10, 20);
            }
            $pdf->SetFont('helvetica', 'B', 13);
            $pdf->Cell(0, 12, strip_tags($datipdf), 0, 1, 'C');
            $pdf->Ln(2);

            $html = '<style>
                table { border-collapse: collapse; width: 100%; font-size: 7.5pt; }
                th, td { border: 1px solid #000; padding: 3px; text-align: center; }
                thead { background-color: #e0e0e0; font-weight: bold; }
            </style>';

            $html .= '<table><thead><tr>';

            // intestazioni fisse
            for ($i = 0; $i < $colonne_fisse; $i++) {
                $html .= '<th>' . strip_tags($ar[0][$i]) . '</th>';
            }

            // intestazioni variabili per blocco
            $start = $colonne_fisse + $b * $colonne_per_pagina;
            $end = min($start + $colonne_per_pagina, $colonne_totali);
            for ($i = $start; $i < $end; $i++) {
                $html .= '<th>' . strip_tags($ar[0][$i]) . '</th>';
            }

            $html .= '</tr></thead><tbody>';

            // righe dati
            for ($r = 1; $r < count($ar); $r++) {
                $html .= '<tr>';
                for ($i = 0; $i < $colonne_fisse; $i++) {
                    $html .= '<td>' . strip_tags($ar[$r][$i]) . '</td>';
                }
                for ($i = $start; $i < $end; $i++) {
                    $html .= '<td>' . strip_tags($ar[$r][$i]) . '</td>';
                }
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            $html .= '<br><div style="text-align:center; font-size:8pt;">Stampato il ' . $data . '<br/>Eleonline - www.eleonline.it</div>';

            $pdf->writeHTML($html, true, false, true, false, '');
        }

        $pdf->Output($nomefile, 'I');
		}//fine
	}
}





?>
