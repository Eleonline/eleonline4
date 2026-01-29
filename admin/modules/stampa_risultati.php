<?php
if (!defined('APP_RUNNING')) define('APP_RUNNING', true);
if(is_file('includes/check_access.php'))
    require_once 'includes/check_access.php';
else
    require_once '../includes/check_access.php';

require_once('../plugins/TCPDF/tcpdf.php'); // Libreria TCPDF

// ======================= DATI SIMULATI =======================
$logo_comune = 'logo_comune.png';
$nome_comune = 'COMUNE DI XXXXX';
$titolo_consultazione = 'CONSULTAZIONE XXXXX';

$candidati = [
    ['nome' => 'Rossi Mario','voti' => 3500,'solo' => 120,'liste' => [
        ['nome'=>'Lista A','voti'=>2200],
        ['nome'=>'Lista C','voti'=>500],
        ['nome'=>'Lista D','voti'=>300]
    ]],
    ['nome' => 'Bianchi Luca','voti' => 3400,'solo' => 95,'liste' => [['nome'=>'Lista B','voti'=>2100]]],
    ['nome'=>'Verdi Anna','voti'=>2700,'solo'=>50,'liste'=>[
        ['nome'=>'Lista E','voti'=>1500],
        ['nome'=>'Lista F','voti'=>700],
        ['nome'=>'Lista G','voti'=>450]
    ]],
    ['nome'=>'Neri Luca','voti'=>1800,'solo'=>20,'liste'=>[
        ['nome'=>'Lista H','voti'=>1200],
        ['nome'=>'Lista I','voti'=>500]
    ]],
    ['nome'=>'Russo Marco','voti'=>4000,'solo'=>150,'liste'=>[
        ['nome'=>'Lista J','voti'=>2000],
        ['nome'=>'Lista K','voti'=>1000],
        ['nome'=>'Lista L','voti'=>600],
        ['nome'=>'Lista M','voti'=>300],
        ['nome'=>'Lista N','voti'=>100]
    ]],
    ['nome'=>'Gialli Sara','voti'=>2200,'solo'=>70,'liste'=>[
        ['nome'=>'Lista O','voti'=>1200],
        ['nome'=>'Lista P','voti'=>800]
    ]]
];

$totali_candidati = ['voti'=>17600,'solo'=>505];
$totali_liste = ['validi'=>12750,'nulli'=>240,'contestati'=>150];

// ======================= CREAZIONE ARRAY LISTE =======================
$listes = [];
foreach($candidati as $c) {
    foreach($c['liste'] as $l) {
        $listes[$l['nome']][] = [
            'candidato' => $c['nome'],
            'voti' => $l['voti']
        ];
    }
}

// ======================= GENERAZIONE PDF =======================
if(isset($_GET['action']) && $_GET['action']=='genera_pdf'){
    $pdf = new TCPDF('L','mm','A4',true,'UTF-8',false);
    $pdf->SetCreator('Eleonline');
    $pdf->SetTitle('Riepilogo voti Candidati');
    $pdf->SetMargins(10,20,10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE,15);
    $pdf->setPrintFooter(true);
    $pdf->setFooterFont(['helvetica','',8]);
    $pdf->AddPage();

    // ================= PDF RIEPILOGO CANDIDATI UNINOMINALI =================
    $html = '<div style="display:flex; align-items:center; justify-content:center; gap:15px; margin-bottom:10px;">
                <img src="'.$logo_comune.'" style="width:80px;">
                <div style="text-align:center;">
                    <h2 style="margin:0;">'.$nome_comune.'</h2>
                    <h3 style="margin:0;">'.$titolo_consultazione.'</h3>
                    <div>Data: '.date("d/m/Y").'</div>
                </div>
            </div>';
    $html .= '<h4 style="text-align:center; margin-bottom:10px;">Riepilogo voti Candidati Uninominali</h4>';
    $html .= '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse; font-size:12px; width:100%;">';
    $html .= '<tr style="background-color:#f0f0f0; font-weight:bold;">
                <td style="vertical-align:middle;">Candidato Uninominale</td>
                <td style="vertical-align:middle;">Voti</td>
                <td style="vertical-align:middle;">Al solo candidato</td>
                <td>Lista Collegata</td>
                <td>Voti alla lista</td>
              </tr>';

    foreach($candidati as $c){
        $num_liste = count($c['liste']);
        foreach($c['liste'] as $i=>$l){
            $style = '';
            if($i===0) $style .= 'border-top:3px solid #000; font-weight:bold;';
            if($i===$num_liste-1) $style .= 'border-bottom:3px solid #000;';
            $html .= '<tr style="'.$style.'">';
            if($i===0){
                $html .= '<td rowspan="'.$num_liste.'" style="vertical-align:middle;">'.$c['nome'].'</td>';
                $html .= '<td rowspan="'.$num_liste.'" style="vertical-align:middle;">'.$c['voti'].'</td>';
                $html .= '<td rowspan="'.$num_liste.'" style="vertical-align:middle;">'.$c['solo'].'</td>';
            }
            $html .= '<td>'.$l['nome'].'</td>';
            $html .= '<td>'.$l['voti'].'</td>';
            $html .= '</tr>';
        }
    }

    $html .= '<tr style="font-weight:bold;">
                <td rowspan="3" style="vertical-align:middle;">TOTALE</td>
                <td rowspan="3" style="vertical-align:middle;">'.$totali_candidati['voti'].'</td>
                <td rowspan="3" style="vertical-align:middle;">'.$totali_candidati['solo'].'</td>
                <td>Validi di lista</td>
                <td>'.$totali_liste['validi'].'</td>
              </tr>';
    $html .= '<tr>
                <td>Nulli di lista</td>
                <td>'.$totali_liste['nulli'].'</td>
              </tr>';
    $html .= '<tr>
                <td>Contestati di lista</td>
                <td>'.$totali_liste['contestati'].'</td>
              </tr>';
    $html .= '</table>';
    $pdf->writeHTML($html,true,false,true,false,'');

    // ================= PDF PER OGNI LISTA =================
    foreach($listes as $nome_lista => $candidati_lista){
        $pdf->AddPage();
        $html = '<div style="text-align:center; margin-bottom:10px;">
                    <h2>'.$nome_comune.'</h2>
                    <h3>'.$titolo_consultazione.'</h3>
                    <h4>Lista: '.$nome_lista.'</h4>
                 </div>';

        $html .= '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse; width:100%; font-size:12px;">
                    <tr style="background:#f0f0f0; font-weight:bold;">
                        <td>Candidato</td>
                        <td>Voti</td>
                    </tr>';
        foreach($candidati_lista as $cl){
            $html .= '<tr>
                        <td>'.$cl['candidato'].'</td>
                        <td>'.$cl['voti'].'</td>
                      </tr>';
        }
        $html .= '</table>';
        $pdf->writeHTML($html,true,false,true,false,'');
    }

    $pdf->Output('riepilogo_candidati.pdf','D');
    exit;
}
?>

<!-- ======================= CARD HTML ======================= -->
<div class="card card-primary card-outline mb-3">
    <div class="card-header text-center" style="display:flex; align-items:center; justify-content:center; gap:15px;">
        <img src="<?php echo $logo_comune; ?>" class="logo" style="width:100px;">
        <div class="header-text" style="text-align:center;">
            <h2 style="margin:4px 0; font-size:24px;"><?php echo $nome_comune; ?></h2>
            <h3 style="margin:2px 0; font-size:20px;"><?php echo $titolo_consultazione; ?></h3>
            <div>Data: <?php echo date("d/m/Y"); ?></div>
        </div>
    </div>

    <div class="card-body">
        <h4 class="text-center mb-3">Riepilogo voti Candidati Uninominali</h4>

        <!-- Bottone PDF -->
        <div class="mb-2 text-right no-print">
            <form action="stampa_risultati.php" method="get" target="_blank" style="display:inline;">
                <input type="hidden" name="action" value="genera_pdf">
                <button class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i> Crea PDF
                </button>
            </form>
        </div>

        <!-- Tabella voti -->
        <div class="table-responsive">
            <table class="table table-bordered table-candidati">
                <tr class="titolo-tabella">
                    <td>Candidato Uninominale</td>
                    <td>Voti</td>
                    <td>Al solo candidato</td>
                    <td>Lista Collegata</td>
                    <td>Voti alla lista</td>
                </tr>
                <?php foreach($candidati as $c):
                    $num_liste = count($c['liste']);
                    foreach($c['liste'] as $i=>$l):
                        $classeBordo = '';
                        if($i===0) $classeBordo = 'lista-prima candidato-row-first';
                        if($i===$num_liste-1) $classeBordo .= ($classeBordo?' ':'').'lista-ultima';
                ?>
                <tr class="<?php echo $classeBordo; ?>">
                    <?php if($i===0): ?>
                        <td class="text-left" rowspan="<?php echo $num_liste; ?>" style="vertical-align:middle;">
                            <?php echo $c['nome']; ?>
                        </td>
                        <td rowspan="<?php echo $num_liste; ?>" style="vertical-align:middle;">
                            <?php echo $c['voti']; ?>
                        </td>
                        <td rowspan="<?php echo $num_liste; ?>" style="vertical-align:middle;">
                            <?php echo $c['solo']; ?>
                        </td>
                    <?php endif; ?>
                    <td class="text-left"><?php echo $l['nome']; ?></td>
                    <td><?php echo $l['voti']; ?></td>
                </tr>
                <?php endforeach; endforeach; ?>

                <!-- Totali -->
                <tr>
                    <td rowspan="3" style="vertical-align:middle;">TOTALE</td>
                    <td rowspan="3" style="vertical-align:middle;"><?php echo $totali_candidati['voti']; ?></td>
                    <td rowspan="3" style="vertical-align:middle;"><?php echo $totali_candidati['solo']; ?></td>
                    <td class="text-left">Validi di lista</td>
                    <td><?php echo $totali_liste['validi']; ?></td>
                </tr>
                <tr>
                    <td class="text-left">Nulli di lista</td>
                    <td><?php echo $totali_liste['nulli']; ?></td>
                </tr>
                <tr>
                    <td class="text-left">Contestati di lista</td>
                    <td><?php echo $totali_liste['contestati']; ?></td>
                </tr>
            </table>
        </div>

        <!-- ================= CARD PER OGNI LISTA ================= -->
        <div class="mt-4">
            <?php foreach($listes as $nome_lista => $candidati_lista): ?>
            <div class="card card-info mb-3">
                <div class="card-header">
                    Lista: <?php echo $nome_lista; ?>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr style="background:#f0f0f0; font-weight:bold;">
                            <td>Candidato</td>
                            <td>Voti</td>
                        </tr>
                        <?php foreach($candidati_lista as $cl): ?>
                        <tr>
                            <td><?php echo $cl['candidato']; ?></td>
                            <td><?php echo $cl['voti']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.table-candidati th, .table-candidati td {
    border:1px solid #000;
    padding:8px;
    text-align:center;
    font-size:14px;
}
.table-candidati .text-left { text-align:left; }
.table-candidati .lista-prima td { border-top:3px solid #000; }
.table-candidati .lista-ultima td { border-bottom:3px solid #000; }
.table-candidati .candidato-row-first td:first-child,
.table-candidati .candidato-row-first td:nth-child(2),
.table-candidati .candidato-row-first td:nth-child(3) {
    border-top:3px solid #000;
    border-bottom:3px solid #000;
    font-weight:bold;
}
.titolo-tabella { font-weight:bold; font-size:16px; background:#f0f0f0; }
.subheader { font-weight:bold; background:#e0e0e0; }
</style>
