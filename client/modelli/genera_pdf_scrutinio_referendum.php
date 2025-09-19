<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// === CONFIGURAZIONE ===
$comune = "Capo d'Orlando";
$consultazione = "Referendum Popolare";
$totali_sezioni = 6;
$sezione_scelta = $_POST['sezione'] ?? 'tutte';

// === QUESITI DINAMICI (max 10)
$quesiti = [
    'Quesito 1: Abrogazione norma su giustizia',
    'Quesito 2: Responsabilità dei magistrati',
    'Quesito 3: Limitazione misure cautelari',
    'Quesito 4: Separazione carriere magistrati',
    'Quesito 5: Accesso ai documenti dei magistrati',
    'Quesito 6: Modifica sistema disciplinare',
    'Quesito 7: Riforma del CSM',
    'Quesito 8: Rappresentanza territoriale',
    'Quesito 9: Voto all’estero',
    'Quesito 10: Riduzione numero parlamentari'
];
$quesiti = array_slice($quesiti, 0, 10);
$num_q = count($quesiti);

// === Adattamento layout dinamico
if ($num_q <= 6) {
    $font_size = '8pt';
    $row_height = '20px';
} elseif ($num_q <= 8) {
    $font_size = '7.5pt';
    $row_height = '18px';
} else {
    $font_size = '6.8pt';
    $row_height = '13px';
}


// === Logo da DB simulato
$logo_blob = file_get_contents('logo.png');
$logo_base64 = 'data:image/png;base64,' . base64_encode($logo_blob);

// === Sezioni da stampare
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
    font-size: 6.8pt;
    margin: 6px 16px 8px 16px;
}
h1, h2, h3 {
    text-align: center;
    margin: 0;
    font-size: 10pt;
}
.logo {
    width: 42px;
    float: left;
}
.sezione-info {
    text-align: right;
    font-size: 6.8pt;
    margin: 4px 0;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2px;
}
th, td {
    border: 1px solid black;
    padding: 1px 2px;
    text-align: center;
}
p {
    margin: 0;
}
.dicitura {
    font-size: 7pt;
    margin-top: 4px;
}
.nota {
    font-size: 6pt;
    margin-top: 3px;
}
.firma {
    text-align: right;
    margin-top: 8px;
    font-size: 6.8pt;
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
    <h3>SCRUTINIO REFERENDUM</h3>
    <div style="clear: both;"></div>

    <div class="sezione-info"><strong>SEZIONE N. <?= $sezione ?></strong></div>

    <?php foreach ($quesiti as $q): ?>
        <p><strong><?= htmlspecialchars($q) ?></strong></p>

        <table>
            <thead>
                <tr>
                    <th>VOTANTI</th>
                    <th>VALIDI</th>
                    <th>BIANCHE</th>
                    <th>NULLE</th>
                    <th>CONTESTATI</th>
                    <th>TOTALE (*)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: <?= $row_height ?>;"></td>
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
                    <th>VOTI “SÌ”</th>
                    <th>VOTI “NO”</th>
                    <th>TOTALE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: <?= $row_height ?>;"></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    <?php endforeach; ?>

    <p class="dicitura">
        SI ATTESTA CHE I DATI SOPRA RIPORTATI SONO CONFORMI A QUELLI RISULTANTI DAL VERBALE DELLE OPERAZIONI DELL’UFFICIO DI SEZIONE.
    </p>

    <p class="nota">
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

// === GENERA PDF ===
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("scrutinio_referendum.pdf", ["Attachment" => false]);
