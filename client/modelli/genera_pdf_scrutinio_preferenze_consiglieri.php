<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$comune = "Capo d'Orlando";
$tipo = "Elezioni Comunali";
$totali_sezioni = 6;
$sezione_scelta = $_POST['sezione'] ?? 'tutte';

// Dati fittizi
$liste = [];
for ($i = 1; $i <= 30; $i++) {
    $lista_nome = "Lista $i";
    $candidati = [];
    for ($j = 1; $j <= 6; $j++) {
        $candidati[] = "Candidato $i.$j";
    }
    $liste[] = ['nome' => $lista_nome, 'candidati' => $candidati];
}

$num_liste = count($liste);

if ($num_liste <= 13) {
    $font_size = '8pt';
    $inner_font_size = '7pt';
    $row_padding = '3px';
} elseif ($num_liste <= 21) {
    $font_size = '6pt';
    $inner_font_size = '5.5pt';
    $row_padding = '1px';
} elseif ($num_liste <= 24) {
    $font_size = '6pt';
    $inner_font_size = '4.5pt';
    $row_padding = '1px';
} else {
    $font_size = '5pt';
    $inner_font_size = '4pt';
    $row_padding = '0.5px';
}

$sezioni = ($sezione_scelta === 'tutte') ? range(1, $totali_sezioni) : [(int)$sezione_scelta];

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
    line-height: 1.1;
    margin: 6px 16px;
}
h1, h2, h3 {
    text-align: center;
    margin: 0;
}
.lista {
    width: 100%;
    border-collapse: collapse;
}
.lista td {
    width: 33%;
    vertical-align: top;
    padding: 1px;
}
table.preferenze {
    width: 100%;
    border-collapse: collapse;
    font-size: <?= $inner_font_size ?>;
}
table.preferenze th, table.preferenze td {
    border: 1px solid black;
    padding: <?= $row_padding ?>;
}
.firma {
    text-align: right;
    margin-top: 6px;
    font-size: <?= $font_size ?>;
}
</style>
</head>
<body>
<?php foreach ($sezioni as $i => $sezione):
$is_last = ($i === array_key_last($sezioni));
if (!$is_last) echo '<div style="page-break-after: always;">';
?>
<h1>COMUNE DI <?= strtoupper($comune) ?></h1>
<h2><?= strtoupper($tipo) ?></h2>
<h3>SCRUTINIO PREFERENZE</h3>
<p style="text-align:right; margin-bottom: 2px;"><strong>SEZIONE N. <?= $sezione ?></strong></p>

<table class="lista">
<tr>
<?php foreach ($liste as $index => $lista): ?>
<td>
    <table class="preferenze">
        <tr><th colspan="3"><?= $index + 1 ?> - <?= htmlspecialchars($lista['nome']) ?></th></tr>
        <tr>
            <th style="width: 6%;">N.</th>
            <th style="width: 64%;">Nome</th>
            <th style="width: 30%;">Voti</th>
        </tr>
        <?php foreach ($lista['candidati'] as $i => $nome): ?>
        <tr>
            <td style="width: 6%;"><?= $i + 1 ?></td>
            <td style="width: 64%;"><?= htmlspecialchars($nome) ?></td>
            <td style="width: 30%;"></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2"><strong>Totale</strong></td>
            <td></td>
        </tr>
    </table>
</td>
<?php if (($index + 1) % 3 === 0 && $index + 1 < count($liste)): ?>
</tr><tr>
<?php endif; ?>
<?php endforeach; ?>
</tr>
</table>

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
$dompdf->stream("scrutinio_preferenze.pdf", ["Attachment" => false]);
