<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// === CONFIG ===
$comune = "Capo d'Orlando";
$tipo = "Referendum Popolare";
$totali_sezioni = 6;
$data_ora = $_POST['data_ora'] ?? '';
$sezione_scelta = $_POST['sezione'] ?? 'tutte';

if (empty($data_ora)) die("Data e ora non selezionati.");

// Estrai data e ora
[$data, $ora] = explode(' ', $data_ora);
[$gg, $mm, $aaaa] = explode('-', $data);
$mesi_it = [
    '01' => 'Gennaio', '02' => 'Febbraio', '03' => 'Marzo', '04' => 'Aprile',
    '05' => 'Maggio', '06' => 'Giugno', '07' => 'Luglio', '08' => 'Agosto',
    '09' => 'Settembre', '10' => 'Ottobre', '11' => 'Novembre', '12' => 'Dicembre'
];
$data_formattata = ltrim($gg, '0') . ' ' . $mesi_it[$mm] . ' ' . $aaaa;

// Simulazione logo da DB
$logo_blob = file_get_contents('logo.png');
$logo_base64 = 'data:image/png;base64,' . base64_encode($logo_blob);

// === QUESITI (max 10) ===
$quesiti = [
    'Abrogazione norma su giustizia',
    'Responsabilità dei magistrati',
    'Limitazione misure cautelari',
    'Separazione carriere magistrati',
    'Accesso ai documenti dei magistrati',
    'Riforma del CSM',
    'Modifica elettorale',
    'Trasparenza degli atti giudiziari',
    'Responsabilità disciplinare',
    'Tutela diritti dei cittadini'
];

$quesiti = array_slice($quesiti, 0, 10);
$num_quesiti = count($quesiti);

// === Adattamento dinamico per spazio ===
if ($num_quesiti <= 6) {
    $font_size = '9pt';
    $td_height = '25px';
    $firma_margin = '30px';
} elseif ($num_quesiti <= 8) {
    $font_size = '8pt';
    $td_height = '20px';
    $firma_margin = '20px';
} elseif ($num_quesiti <= 10) {
    $font_size = '7pt';
    $td_height = '16px';
    $firma_margin = '10px';
} else {
    $font_size = '6.5pt';
    $td_height = '15px';
    $firma_margin = '5px';
}

// Sezioni
$sezioni = ($sezione_scelta === 'tutte') ? range(1, $totali_sezioni) : [(int)$sezione_scelta];

// === HTML ===
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
            margin: 20px 25px;
        }
        h1, h2 {
            text-align: center;
            margin: 2px 0;
        }
        .logo {
            width: 50px;
            float: left;
        }
        .sezione-info {
            text-align: right;
            margin-bottom: 5px;
            font-size: <?= $font_size ?>;
        }
        .quesito {
            margin-top: 6px;
            font-weight: bold;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        th, td {
            border: 1px solid black;
            padding: 3px;
            text-align: center;
        }
        .firma {
            text-align: right;
            margin-top: <?= $firma_margin ?>;
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
    <h2><?= strtoupper($tipo) ?></h2>
    <div style="clear: both;"></div>

    <div style="margin-top: 5px; text-align: center;">
        <h3 style="font-size: 16pt; margin-bottom: 5px;">
            NUMERO VOTANTI ALLE ORE <?= $ora ?>
        </h3>
        <h2 style="font-size: 22pt; margin-bottom: 5px;">
            SEZIONE N. <?= $sezione ?>
        </h2>
    </div>

    <div style="margin-top: 20px; text-align: left;">
        <p style="font-size: 10pt; margin: 0;">
            <strong>Alle ore <?= $ora ?> del giorno <?= $data_formattata ?> hanno votato in complesso:</strong>
        </p>
    </div>

    <?php foreach ($quesiti as $idx => $q): ?>
        <div class="quesito">QUESITO <?= $idx + 1 ?>: <?= htmlspecialchars($q) ?></div>
        <table>
            <thead>
                <tr>
                    <th>MASCHI</th>
                    <th>FEMMINE</th>
                    <th>TOTALE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: <?= $td_height ?>;"></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    <?php endforeach; ?>

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
$dompdf->stream("affluenza_referendum.pdf", ["Attachment" => false]);
