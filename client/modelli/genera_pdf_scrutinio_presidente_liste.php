<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// === CONFIG ===
$comune = "Capo d'Orlando";
$consultazione = "Elezioni Comunali";
$totali_sezioni = 6;
$sezione_scelta = $_POST['sezione'] ?? 'tutte';

// === CANDIDATI PRESIDENTE (fittizi)
$candidati = [
    "Giuseppe Verdi",
    "Anna Bianchi",
    "Marco Neri",
    "Lucia Rizzo",
    "Franco Russo",
    "Carla Costa"
];

// === LISTE
$liste = [
    'Lista Civica A',
    'Unione per Capo',
    'Insieme Si Cambia',
    'Progetto Comune',
    'Futuro Insieme',
	'Lista Civica A',
    'Unione per Capo',
    'Insieme Si Cambia',
    'Progetto Comune',
    'Futuro Insieme',
	'Lista Civica A',
    'Unione per Capo',
    'Insieme Si Cambia',
    'Progetto Comune',
    'Futuro Insieme',
	'Lista Civica A',
    'Unione per Capo',
    'Insieme Si Cambia',
    'Progetto Comune',
    'Futuro Insieme',
	'Insieme Si Cambia',
	'Progetto Comune',
    'Futuro Insieme',
    'Popolari Uniti'
];

// === Layout dinamico
$font_size = '8pt';
$row_height = '18px';
if (count($candidati) > 7 || count($liste) > 10) {
    $font_size = '7.5pt';
    $row_height = '16px';
}
if (count($liste) > 20) {
    $row_height = '1px';
    $font_size = '6pt';
}
if (count($liste) > 25) {
    $row_height = '10px';
    $font_size = '5pt';
}

// === Logo fittizio
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
    margin: 14px 22px;
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
    font-size: <?= $font_size ?>;
    margin-top: 5px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 5px;
}
th, td {
    border: 1px solid black;
    padding: 2px;
    text-align: center;
}
td.nome {
    text-align: left;
}
td.totali {
    text-align: left;
}
.dicitura {
    font-size: 7pt;
    margin-top: 6px;
}
.firma {
    text-align: right;
    margin-top: 10px;
    font-size: <?= $font_size ?>;
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
<h3>SCRUTINIO PRESIDENTI + LISTE</h3>
<div style="clear: both;"></div>

<div class="sezione-info"><strong>SEZIONE N. <?= $sezione ?></strong></div>

<!-- CANDIDATI PRESIDENTE -->
<h3>CANDIDATI PRESIDENTE</h3>
<table>
    <thead>
        <tr>
            <th style="width: 8%;">N.</th>
            <th class="nome">CANDIDATO</th>
            <th style="width: 18%;">VOTI</th>
            <th style="width: 22%;">VOTI SOLO CANDIDATO PRESIDENTE</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($candidati as $index => $nome): ?>
        <tr>
            <td style="height: <?= $row_height ?>;"><?= $index + 1 ?></td>
            <td class="nome"><?= htmlspecialchars($nome) ?></td>
            <td></td>
            <td></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td></td>
            <td><strong>TOTALE VOTI DI PREFERENZA</strong></td>
            <td class="totali"><strong>(A)</strong></td>
            <td class="totali"><strong>(B)</strong></td>
        </tr>
    </tbody>
</table>

<!-- LISTE -->
<h3>LISTE</h3>
<table>
    <thead>
        <tr>
            <th style="width:8%;">N.</th>
            <th class="nome">DENOMINAZIONE LISTA</th>
            <th style="width:18%;">VOTI</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($liste as $index => $nome): ?>
        <tr>
            <td style="height: <?= $row_height ?>;"><?= $index + 1 ?></td>
            <td class="nome"><?= htmlspecialchars($nome) ?></td>
            <td></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2"><strong>TOTALE VOTI VALIDI</strong></td>
            <td class="totali"><strong>(C)</strong></td>
        </tr>
    </tbody>
</table>

<!-- RIEPILOGO -->
<h3>RIEPILOGO</h3>
<table>
    <thead>
        <tr>
            <th>TOTALI VOTI DI PREFERENZA<br>(A)</th>
			<th>TOTALI VOTI DI PREFERENZA AL SOLO CANDIDATO<br>(B)</th>
			<th>TOTALI VOTI DI LISTA<br>(C)</th>
            <th>BIANCHE<br>(D)</th>
            <th>NULLE<br>(E)</th>
            <th>CONTESTATI<br>(F)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="height: 20px;"></td>
            <td></td>
			<td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th>VOTANTI</th>
            <th>TOTALI VOTI VALIDI DI PREFERENZA(*)<br>(A+D+E+F)</th>
			<th>TOTALI VOTI VALIDI DI LISTA(*)<br>(B+C+D+E+F)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="height: 20px;"></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<p class="dicitura">
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
$dompdf->stream("scrutinio_PRESIDENTE_liste.pdf", ["Attachment" => false]);
