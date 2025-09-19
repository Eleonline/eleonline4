<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// === CONFIGURAZIONE ===
$comune = "Capo d'Orlando";
$consultazione = "Elezioni Comunali – Ballottaggio";
$totali_sezioni = 6;
$sezione_scelta = $_POST['sezione'] ?? 'tutte';

// === CANDIDATI AL BALLOTTAGGIO
$candidati = [
    "Giuseppe Verdi",
    "Anna Bianchi"
];

// === Layout
$font_size = '8.5pt';
$row_height = '22px';

// === Logo da DB
$logo_blob = file_get_contents('logo.png');
$logo_base64 = 'data:image/png;base64,' . base64_encode($logo_blob);

// === Sezioni
$sezioni = ($sezione_scelta === 'tutte') ? range(1, $totali_sezioni) : [(int)$sezione_scelta];

// === HTML PDF ===
ob_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<style>
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: <?= $font_size ?>;
    margin: 16px 22px;
}
h1, h2, h3 {
    text-align: center;
    margin: 2px 0;
}
.logo {
    width: 45px;
    float: left;
}
.sezione-info {
    text-align: right;
    margin-top: 5px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}
th, td {
    border: 1px solid black;
    padding: 4px;
    text-align: center;
}
td.nome {
    text-align: left;
}
.firma {
    text-align: right;
    margin-top: 20px;
}
.note {
    font-size: 7pt;
}
</style>
</head>
<body>
<?php foreach ($sezioni as $i => $sezione):
    $is_last = ($i === array_key_last($sezioni));
    if (!$is_last) echo '<div style="page-break-after: always;">';
?>
<img src="<?= $logo_base64 ?>" class="logo">
<h1>COMUNE DI <?= strtoupper($comune) ?></h1>
<h2><?= strtoupper($consultazione) ?></h2>
<h3>SCRUTINIO BALLOTTAGGIO</h3>
<div style="clear: both;"></div>

<div class="sezione-info"><strong>SEZIONE N. <?= $sezione ?></strong></div>

<!-- CANDIDATI -->
<h3>CANDIDATI SINDACO</h3>
<table>
    <thead>
        <tr>
            <th style="width:8%;">N.</th>
            <th class="nome">CANDIDATO</th>
            <th style="width:22%;">VOTI</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($candidati as $index => $nome): ?>
        <tr>
            <td style="height: <?= $row_height ?>;"><?= $index + 1 ?></td>
            <td class="nome"><?= htmlspecialchars($nome) ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2"><strong>TOTALE VOTI VALIDI</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>

<!-- RIEPILOGO -->
<h3>RIEPILOGO</h3>
<table>
    <thead>
        <tr>
			<th>VOTANTI</th>
            <th>VOTI VALIDI</th>
            <th>BIANCHE</th>
            <th>NULLE</th>
            <th>CONTESTATI</th>
            <th>TOTALE VOTI ESPRESSI(*)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="height: 22px;"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>

<p class="note">
    (*) QUESTO TOTALE DEVE CORRISPONDERE AL NUMERO DEI VOTANTI
</p>

<div class="firma">
    <p>IL PRESIDENTE DEL SEGGIO</p>
    <p>______________________________</p>
</div>
<?php if (!$is_last) echo '</div>'; ?>
<?php endforeach; ?>
</body>
</html>
<?php
$html = ob_get_clean();
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("scrutinio_ballottaggio.pdf", ["Attachment" => false]);
