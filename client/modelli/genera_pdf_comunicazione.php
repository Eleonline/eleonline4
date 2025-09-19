<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// === CONFIG ===
$comune = "Capo d'Orlando";
$totali_sezioni = 6;
$tipo = $_POST['tipo'] ?? '';
$sezione_scelta = $_POST['sezione'] ?? 'tutte';
$data_input = $_POST['data'] ?? '';

// Tipo di consultazione impostato via codice (es. da DB)
$tipo_consultazione = "Referendum Popolare";

// ✅ Validazione
if (empty($tipo) || empty($data_input)) {
    die("Errore: tipo e data sono obbligatori.");
}

// ✅ Formatto la data in italiano
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_input)) {
    die("Formato data non valido.");
}
[$aaaa, $mm, $gg] = explode('-', $data_input);
$mesi_it = [
    '01'=>'Gennaio','02'=>'Febbraio','03'=>'Marzo','04'=>'Aprile',
    '05'=>'Maggio','06'=>'Giugno','07'=>'Luglio','08'=>'Agosto',
    '09'=>'Settembre','10'=>'Ottobre','11'=>'Novembre','12'=>'Dicembre'
];
$data_formattata = ltrim($gg, '0') . ' ' . $mesi_it[$mm] . ' ' . $aaaa;

// ✅ Logo da DB simulato
$logo_blob = file_get_contents('logo.png');
$logo_base64 = 'data:image/png;base64,' . base64_encode($logo_blob);

// ✅ Testi comunicazione
$testi = [
    '1' => "Il sottoscritto Presidente del seggio elettorale della Sezione n. __SEZIONE__ del Comune di __COMUNE__, comunica di aver provveduto alla costituzione dell’Ufficio elettorale di sezione nella giornata di __DATA__, alle ore __________.",
    '2' => "Il sottoscritto Presidente del seggio elettorale della Sezione n. __SEZIONE__ del Comune di __COMUNE__, comunica di aver provveduto alla ricostituzione dell’Ufficio elettorale di sezione nella giornata di __DATA__, alle ore __________."
];
if (!isset($testi[$tipo])) die("Tipo comunicazione non valido.");

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
            font-size: 12pt;
            margin: 30px 40px;
        }
        .logo {
            width: 60px;
            float: left;
        }
        h1, h2 {
            text-align: center;
            margin: 5px 0;
        }
        .contenuto {
            margin-top: 60px;
            line-height: 1.6;
        }
        .firma {
            margin-top: 80px;
            text-align: right;
        }
        .firma p {
            margin: 0;
        }
    </style>
</head>
<body>
<?php foreach ($sezioni as $i => $sezione):
    $is_last = ($i === array_key_last($sezioni));
    if (!$is_last) echo '<div style="page-break-after: always;">';

    $testo = $testi[$tipo];
    $testo = str_replace(['__SEZIONE__', '__COMUNE__', '__DATA__'], [$sezione, $comune, $data_formattata], $testo);
?>
    <img src="<?= $logo_base64 ?>" class="logo">
    <h1>COMUNE DI <?= strtoupper($comune) ?></h1>
    <h2><?= strtoupper($tipo_consultazione) ?></h2>
    <h2 style="margin-top: 10px;">
        <?= $tipo == '1'
            ? 'COSTITUZIONE DELL’UFFICIO ELETTORALE DI SEZIONE'
            : 'RICOSTITUZIONE DELL’UFFICIO ELETTORALE DI SEZIONE'
        ?>
    </h2>
    <div style="clear: both;"></div>

    <div class="contenuto">
        <p><?= nl2br(htmlspecialchars($testo)) ?></p>
    </div>

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
$dompdf->stream("comunicazione_presidente.pdf", ["Attachment" => false]);
