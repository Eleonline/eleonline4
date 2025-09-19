<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// === CONFIG ===
$comune = "Capo d'Orlando";
$tipo = "Elezioni Parlamento Europeo";
$totali_sezioni = 6;
$sezione_scelta = $_POST['sezione'] ?? 'tutte';

// Simulazione logo da DB
$logo_blob = file_get_contents('logo.png');
$logo_base64 = 'data:image/png;base64,' . base64_encode($logo_blob);

// === Array fittizio di liste con candidati ===
$liste = [];
for ($i = 1; $i <= 21; $i++) {
    $lista_nome = "LISTA $i";
    $candidati = [];
    for ($j = 1; $j <= 8; $j++) {
        $candidati[] = "Candidato $i.$j";
    }
    $liste[] = ['nome' => $lista_nome, 'candidati' => $candidati];
}

// Adattamento dinamico
$num_liste = count($liste);

if ($num_liste <= 9) {
    $font_size = '9pt';
    $inner_font = '8pt';
    $row_padding = '3px';
    $margin_page = '18px';
    $firma_size = '8pt';
} elseif ($num_liste <= 13) {
    $font_size = '8pt';
    $inner_font = '7pt';
    $row_padding = '2px';
    $margin_page = '14px';
    $firma_size = '7pt';
} elseif ($num_liste <= 15) {
    $font_size = '7pt';
    $inner_font = '6.5pt';
    $row_padding = '1.5px';
    $margin_page = '12px';
    $firma_size = '6.5pt';
} else {
    $font_size = '5pt';
    $inner_font = '4pt';
    $row_padding = '1px';
    $margin_page = '8px';
    $firma_size = '4pt';
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
        margin: <?= $margin_page ?>;
    }
    h1, h2 {
        text-align: center;
        margin: 1px 0;
    }
    .logo {
        width: 45px;
        float: left;
    }
    .sezione-box {
        text-align: right;
        font-size: <?= $font_size ?>;
        margin-bottom: 4px;
    }
    .firma {
        text-align: right;
        margin-top: 10px;
        font-size: <?= $font_size ?>;
    }
    .note {
        font-size: 6pt;
        margin-top: 6px;
    }
    table.lista {
        width: 100%;
        border-collapse: collapse;
    }
    td.lista-box {
        width: 33%;
        vertical-align: top;
        padding: 2px;
    }
    table.lista-interna {
        width: 100%;
        border: 1px solid black;
        border-collapse: collapse;
        font-size: <?= $inner_font ?>;
    }
    table.lista-interna th, table.lista-interna td {
        border: 1px solid black;
        padding: <?= $row_padding ?> 2px;
    }
</style>


</head>
<body>
<?php foreach ($sezioni as $i => $sezione):
    $is_last = ($i === array_key_last($sezioni));
?>
    <img src="<?= $logo_base64 ?>" class="logo">
    <h1>COMUNE DI <?= strtoupper($comune) ?></h1>
    <h2><?= strtoupper($tipo) ?></h2>
    <div style="clear: both;"></div>
    <div class="sezione-box"><strong>SEZIONE <?= $sezione ?></strong></div>

    <table class="lista">
        <tr>
        <?php foreach ($liste as $index => $lista): ?>
            <td class="lista-box">
                <table class="lista-interna">
                    <tr>
                        <td colspan="3" style="font-weight: bold;"><?= $index + 1 ?> <?= htmlspecialchars($lista['nome']) ?></td>
                    </tr>
                    <tr>
                        <th style="width: 15%;">N.</th>
                        <th>CANDIDATI</th>
                        <th style="width: 25%;">VOTI</th>
                    </tr>
                    <?php foreach ($lista['candidati'] as $i => $nome): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($nome) ?></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2"><strong>TOTALE VOTI</strong></td>
                        <td></td>
                    </tr>
                </table>
            </td>
            <?php if (($index + 1) % 3 === 0): ?>
        </tr><tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tr>
    </table>

<p style="font-size: <?= $inner_font ?>; margin: 6px 0 2px 0; line-height: 1.15;">
    SI ATTESTA CHE I DATI SOPRA RIPORTATI SONO CONFORMI A QUELLI RISULTANTI DAL VERBALE DELLE OPERAZIONI DELL’UFFICIO DI SEZIONE.
</p>
<p style="font-size: <?= $firma_size ?>; margin: 6px 0 2px 0; text-align: right; line-height: 1.1;">
    IL PRESIDENTE DEL SEGGIO
</p>
<div style="border-bottom: 1px solid black; width: 170px; margin: 0 0 0 auto; height: 10px;"></div>


<?php endforeach; ?>
</body>
</html>
<?php
$html = ob_get_clean();

// === DOMPDF ===
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("scrutinio_candidati.pdf", ["Attachment" => false]);
