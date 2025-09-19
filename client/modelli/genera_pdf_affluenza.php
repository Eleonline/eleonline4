<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// === CONFIGURAZIONE ===
#$comune = "Capo d'Orlando"; // o da DB
$row=dati_consultazione(0);
$tipo=$row[0]['descrizione'];
#$tipo = "Elezioni Europee 2024";
$data_ora = $_POST['data_ora'] ?? '';
$sezione_scelta = $_POST['sezione'] ?? '';
$totali_sezioni = (int)($_POST['totali_sezioni'] ?? 0);

if (!$data_ora || !$sezione_scelta || !$totali_sezioni) {
    die("Dati mancanti.");
}

[$data, $ora] = explode(' ', $data_ora);
[$gg, $mm, $aaaa] = explode('-', $data);
$data_italiana = "$gg/$mm/$aaaa";
$mesi_it = [
    '01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo', '04' => 'Aprile',
    '05' => 'Maggio', '06' => 'Giugno', '07' => 'Luglio', '08' => 'Agosto',
    '09' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
];
$data_formattata = ltrim($gg, '0') . ' ' . $mesi_it[$mm] . ' ' . $aaaa;



// Adatta la dimensione font in base al nome del comune
$lunghezza_comune = strlen($comune);
$font_size_comune = $lunghezza_comune > 25 ? '18pt' : '22pt';

// === LOGO da BLOB ===
$row=dati_comune();
$logo_blob = $row[0]['stemma']; #file_get_contents('logo.png'); // da DB: base64_encode($row['logo'])
$logo_base64 = 'data:image/png;base64,' . base64_encode($logo_blob);

// === Sezioni da stampare ===
$sezioni = ($sezione_scelta === 'tutte') ? range(1, $totali_sezioni) : [(int)$sezione_scelta];

// === Genera HTML ===
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12pt; margin: 40px; }
        h1, h2 { text-align: center; margin: 0; }
        h1.comune { font-size: <?= $font_size_comune ?>; }
        h2 { font-size: 16pt; }
        table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        td, th { border: 1px solid #000; padding: 8px; text-align: center; }
        .firma { margin-top: 80px; text-align: right; }
        .logo { width: 70px; float: left; margin-bottom: 10px; }
    </style>
</head>
<body>
<?php foreach ($sezioni as $i => $sezione): 
    $is_last = ($i === array_key_last($sezioni));
?>
    <div style="<?= $is_last ? '' : 'page-break-after: always;' ?>">
        <img class="logo" src="<?= $logo_base64 ?>">
        <h1 class="comune">COMUNE DI <?= strtoupper($comune) ?></h1>
        <h2><?= strtoupper($tipo) ?></h2>
        <div style="clear: both;"></div>
		<div style="margin-top: 180px; text-align: center;">
			<h3 style="font-size: 20pt; margin-bottom: 30px;">
				NUMERO VOTANTI ALLE ORE <?= $ora ?>
			</h3>
			<h2 style="font-size: 28pt; margin-bottom: 40px;">
				SEZIONE N. <?= $sezione ?>
			</h2>
		</div>
		<div style="margin-top: 100px; text-align: left;">	
			<p style="font-size: 11pt; margin: 0;">
				<strong>Alle ore <?= $ora ?> del giorno <?= $data_formattata ?> hanno votato in complesso:</strong>
			</p>
		</div>
        <table>
            <tr>
                <th>Maschi</th>
                <th>Femmine</th>
                <th>Totale</th>
            </tr>
            <tr>
                <td height="50px"></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <div class="firma">
            <p>Il Presidente del seggio</p>
            <p>______________________________</p>
        </div>
    </div>
<?php endforeach; ?>
</body>
</html>
<?php
$html = ob_get_clean();

// === GENERA PDF ===
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("moduli_affluenza.pdf", ["Attachment" => false]);
