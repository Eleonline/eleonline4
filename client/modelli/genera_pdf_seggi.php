<?php
require_once('../inc/hpdf5/autoload.php');
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;


// === CONFIGURAZIONE ===
$comune = "Capo d'Orlando"; // o da DB
$tipo = "Elezioni Comunali";
#$data_ora = $_POST['data_ora'] ?? '';
$stampa=$_POST['stampa'] ?? '';
$comune=$_POST['comune'] ?? '';
$id_comune=$_POST['id_comune'] ?? '';
$consultazione=$_POST['consultazione'] ?? '';
// Adatta la dimensione font in base al nome del comune
$lunghezza_comune = strlen($comune);
$font_size_comune = $lunghezza_comune > 25 ? '18pt' : '22pt';

// === LOGO da BLOB ===
#$logo_blob = file_get_contents('modelli/logo.png'); // da DB: base64_encode($row['logo'])
#$logo_base64 = 'data:image/png;base64,' . base64_encode($logo_blob);

// === Sezioni da stampare ===
$sezioni = ""; #($sezione_scelta === 'tutte') ? range(1, $totali_sezioni) : [(int)$sezione_scelta];

// === Genera HTML ===
#ob_start();
$html1="<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; margin: 40px; }
        h1, h2 { text-align: center; margin: 0; }
        h1.comune { font-size: $font_size_comune; }
        h2 { font-size: 12pt; }
        table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        td, th { border: 1px solid #000; padding: 8px; text-align: center; }
        .firma { margin-top: 80px; text-align: right; }
        .logo { width: 70px; float: left; margin-bottom: 10px; }
    </style>
</head>
<body>";

$html2="</div></body>
</html>";

$sez1="        <h1 class=\"comune\">COMUNE DI ".strtoupper($comune)."</h1>
        <h2>".strtoupper($consultazione)."</h2>

	<div class=\"col-12 col-md-12 col-xl-12 py-md-3 px-md-3 bd-content\">
		<div class=\"container\">
			<div class=\"row\">
			<h4 class=\"fw-semibold text-primary mobile-expanded mt-2\">Assegnazione dei seggi in Consiglio</h4>
			<table><tr><td align=\"center\">PROIEZIONE DELLA COMPOSIZIONE DEL CONSIGLIO COMUNALE<br>LA FUNZIONE DI CALCOLO E' SPERIMENTALE<br>IL RISULTATO E' PURAMENTE INDICATIVO</td></tr></table>
			</div>
		</div>
	</div>
<div  style=\"display: inline-block; font-size:9; text-align:center;\">
";
$stampa=$html1.$sez1.$stampa.$html2;		
#$datipdf=$sez1;
// === GENERA PDF ===
$logo=$id_comune.'.png';
$formato='A4';
$orienta='L';
$pdf = new TCPDF($orienta, 'mm', $formato, true, 'UTF-8', false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Eleonline');
#        $pdf->SetTitle($datipdf);
        $pdf->SetFont('helvetica', '', 9);
		$pdf->AddPage();
		if (file_exists("../modules/Elezioni/images/$logo")) {
			$pdf->Image("../modules/Elezioni/images/$logo", 10, 10, 20);
		}
#		$pdf->SetFont('helvetica', 'B', 13);
#		$pdf->Cell(0, 12, strip_tags($datipdf), 0, 1, 'C');
#		$pdf->Ln(2);
        $pdf->writeHTML($stampa, true, false, true, false, '');
        $pdf->Output("proiezione_seggi.pdf", 'I');

?>
