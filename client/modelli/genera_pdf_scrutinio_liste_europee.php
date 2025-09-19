<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// === CONFIGURAZIONE ===
$comune = "Capo d'Orlando";
$tipo = "Elezioni Parlamento Europeo";
$totali_sezioni = 6;
$sezione_scelta = $_POST['sezione'] ?? 'tutte';

// Simulazione logo da DB
$logo_blob = file_get_contents('logo.png');
$logo_base64 = 'data:image/png;base64,' . base64_encode($logo_blob);

// Liste dinamiche (in futuro da DB)
$liste = [
    'ALLEANZA VERDI SINISTRA',
    'MOVIMENTO 5 STELLE',
    "FRATELLI D'ITALIA",
    'PARTITO DEMOCRATICO',
    'LEGA SALVINI PREMIER',
    "STATI UNITI D'EUROPA",
    'AZIONE - SIAMO EUROPEI',
    "LIBERTA'",
    "PACE TERRA DIGNITA'",
    "FORZA ITALIA - NOI MODERATI - PPE",
    'ALTERNATIVA POPOLARE',
    'DEMOCRAZIA CRISTIANA',
    'INSIEME PER L’EUROPA',
    'LISTA POPOLARE UNITARIA',
    'ITALIA SOVRANA E POPOLARE',
    'LISTA POPOLARE UNITARIA',
    'ITALIA SOVRANA E POPOLARE',
	'ITALIA SOVRANA E POPOLARE',
    'ITALIA SOVRANA E POPOLARE',
	'ITALIA SOVRANA E POPOLARE',
	'ITALIA SOVRANA E POPOLARE',
    'ITALIA SOVRANA E POPOLARE',
	'ITALIA SOVRANA E POPOLARE',
    'ITALIA SOVRANA E POPOLARE',
	'ITALIA SOVRANA E POPOLARE',
    'ITALIA SOVRANA E POPOLARE',
	'ITALIA SOVRANA E POPOLARE',
    'ITALIA SOVRANA E POPOLARE',
    'DEMOCRAZIA VERDE'
];

$num_liste = count($liste);
if ($num_liste <= 14) {
    $font_size = '11pt';
    $row_height = '26px';
} elseif ($num_liste <= 20) {
    $font_size = '10pt';
    $row_height = '20px';
} elseif ($num_liste <= 23) {
    $font_size = '9pt';
    $row_height = '18px';
} elseif ($num_liste <= 25) {
    $font_size = '8pt';
    $row_height = '16px';
} else {
    $font_size = '6.5pt';
    $row_height = '15px'; // minimo compatibile
}



// Sezioni da stampare
$sezioni = ($sezione_scelta === 'tutte') ? range(1, $totali_sezioni) : [(int)$sezione_scelta];

// === GENERA HTML ===
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
            margin: 18px 25px 20px 25px;
        }

        h1, h2, h3 {
            text-align: center;
            margin: 2px 0;
        }

        .logo {
            width: 50px;
            float: left;
            margin-bottom: 5px;
        }

        .sezione-box {
            text-align: right;
            margin-bottom: 4px;
            font-size: <?= $font_size ?>;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            border: 1px solid black;
            padding: 2px 4px;
            font-size: <?= $font_size ?>;
        }

        .denominazione {
            text-align: left;
        }

        .firma {
            margin-top: 20px;
            text-align: right;
            font-size: <?= $font_size ?>;
        }

        .note {
            font-size: 7.5pt;
            margin-top: 5px;
        }

        .pagina {
            page-break-after: always;
        }
    </style>
</head>
<body>
<?php foreach ($sezioni as $i => $sezione):
    $is_last = ($i === array_key_last($sezioni));
?>
   <?php if (!$is_last): ?>
    <div style="page-break-after: always;">
<?php endif; ?>
        <img src="<?= $logo_base64 ?>" class="logo">
        <h1>COMUNE DI <?= strtoupper($comune) ?></h1>
        <h2><?= strtoupper($tipo) ?></h2>
        <div style="clear: both;"></div>

        <div class="sezione-box"><strong>SEZ. <?= $sezione ?></strong></div>

        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">N. LISTA</th>
                    <th class="denominazione" style="width: 65%;">DENOMINAZIONE</th>
                    <th style="width: 25%;">VOTI VALIDI</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($liste as $index => $nome): ?>
                <tr>
                    <td style="height: <?= $row_height ?>;"><?= $index + 1 ?></td>
                    <td class="denominazione"><?= htmlspecialchars($nome) ?></td>
                    <td></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2"><strong>TOTALE VOTI VALIDI</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <h3 style="margin-bottom: 4px;">RIEPILOGO</h3>
        <table style="font-size: <?= $font_size ?>;">
            <thead>
                <tr>
                    <th>VOTANTI<br></th>
                    <th>VOTI VALIDI (*)<br>(A)</th>
                    <th>VOTI CONTESTATI E NON ASSEGNATI<br>(B)</th>
                    <th>SCHEDE BIANCHE<br>(C)</th>
                    <th>SCHEDE NULLE<br>(D)</th>
                    <th>TOTALE (**)<br>(A+B+C+D)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td height="30px"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <p class="note">
            (*) COMPRESI VOTI CONTESTATI E PROVVISORIAMENTE ASSEGNATI<br>
            (**) QUESTO TOTALE DEVE CORRISPONDERE AL NUMERO DEI VOTANTI
        </p>

        <div class="firma">
            <p>IL PRESIDENTE DEL SEGGIO</p>
            <p>______________________________</p>
        </div>
    <?php if (!$is_last): ?>
    </div>
<?php endif; ?>
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
$dompdf->stream("moduli_scrutinio_liste.pdf", ["Attachment" => false]);
