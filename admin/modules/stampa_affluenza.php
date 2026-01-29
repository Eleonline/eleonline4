<?php
if (!defined('APP_RUNNING')) {
    define('APP_RUNNING', true);
}

if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
?>
<?php
require_once('../plugins/TCPDF/tcpdf.php'); // Libreria TCPDF

// ======================= DATI SIMULATI =======================
$comune = [
    'nome' => 'Comune di Test',
    'stemma' => 'stemma.png'
];

$consultazione = [
    'titolo' => 'Elezioni Comunali 2026',
    'date_ore' => [
        '29/01/2026' => ['12:00', '19:00', '23:00'],
        '30/01/2026' => ['12:00', '19:00']
    ]
];

$totali_percentuali = [
    '29/01/2026' => ['12:00'=>5,'19:00'=>15,'23:00'=>25],
    '30/01/2026' => ['12:00'=>6,'19:00'=>16]
];

$sezioni = [
    ['numero'=>1,'votanti'=>['29/01/2026'=>['12:00'=>10,'19:00'=>30,'23:00'=>50],'30/01/2026'=>['12:00'=>12,'19:00'=>32]]],
    ['numero'=>2,'votanti'=>['29/01/2026'=>['12:00'=>8,'19:00'=>28,'23:00'=>48],'30/01/2026'=>['12:00'=>10,'19:00'=>30]]],
    ['numero'=>3,'votanti'=>['29/01/2026'=>['12:00'=>12,'19:00'=>35,'23:00'=>55],'30/01/2026'=>['12:00'=>14,'19:00'=>36]]]
];

// Calcolo totali generali per ora
$totali_generali = [];
foreach($consultazione['date_ore'] as $data => $ore){
    foreach($ore as $ora){
        $tot = 0;
        foreach($sezioni as $s){
            $tot += $s['votanti'][$data][$ora];
        }
        $totali_generali[$data][$ora] = $tot;
    }
}

// ======================= GENERAZIONE PDF DIRETTA =======================
if(isset($_GET['action']) && $_GET['action']=='genera_pdf'){
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('Eleonline');
    $pdf->SetTitle('Affluenza '.$consultazione['titolo']);
    $pdf->SetMargins(10,20,10);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 15);

    // Footer con numeri di pagina
    $pdf->setPrintFooter(true);
    $pdf->setFooterFont(['helvetica','',8]);
    $pdf->setFooterData([0,64,0], [0,64,128]);

    $pdf->AddPage();

    // Header
    $html = '<h2 style="text-align:center">'.$comune['nome'].'</h2>';
    $html .= '<p style="text-align:center">'.$consultazione['titolo'].'</p>';

    // Tabella
    $html .= '<table border="1" cellpadding="4">';
    
    // Intestazione principale
    $html .= '<thead>';
    $html .= '<tr style="background-color:#ccc;"><th>Sezione</th>';
    foreach($consultazione['date_ore'] as $data => $ore){
        $html .= '<th colspan="'.count($ore).'">'.$data.'</th>';
    }
    $html .= '<th>Totale Sezione</th></tr>';

    // Ore
    $html .= '<tr><td></td>';
    foreach($consultazione['date_ore'] as $data => $ore){
        foreach($ore as $ora){
            $html .= '<td>'.$ora.'</td>';
        }
    }
    $html .= '<td></td></tr>';

    // Totali %
    $html .= '<tr style="background-color:#eee;"><td><b>Totale %</b></td>';
    foreach($consultazione['date_ore'] as $data => $ore){
        foreach($ore as $ora){
            $html .= '<td><b>'.$totali_percentuali[$data][$ora].'%</b></td>';
        }
    }
    $html .= '<td>-</td></tr>';
    $html .= '</thead>';

    // Corpo tabella
    $html .= '<tbody>';
    $row_color = false;
    foreach($sezioni as $s){
        $html .= '<tr'.($row_color?' style="background-color:#f9f9f9;"':'').'>';
        $html .= '<td>'.$s['numero'].'</td>';
        foreach($consultazione['date_ore'] as $data => $ore){
            foreach($ore as $ora){
                $html .= '<td>'.$s['votanti'][$data][$ora].'</td>';
            }
        }
        $tot = 0;
        foreach($s['votanti'] as $d=>$orev){
            foreach($orev as $v) $tot += $v;
        }
        $html .= '<td><b>'.$tot.'</b></td></tr>';
        $row_color = !$row_color;
    }
    $html .= '</tbody>';

    // Totali generali
    $html .= '<tfoot>';
    $html .= '<tr style="background-color:#ddd;"><td><b>Totale Generale</b></td>';
    foreach($consultazione['date_ore'] as $data => $ore){
        foreach($ore as $ora){
            $html .= '<td><b>'.$totali_generali[$data][$ora].'</b></td>';
        }
    }
    $html .= '<td>-</td></tr>';
    $html .= '</tfoot>';

    $html .= '</table>';

    // Scrive il PDF e scarica direttamente sul dispositivo
    $pdf->writeHTML($html,true,false,true,false,'');
    $pdf->Output('affluenza_'.$consultazione['titolo'].'.pdf','D'); // 'D' = Download diretto
    exit;
}
?>

<!-- ======================= BOTTONI E TABELLA ======================= -->
<div class="card card-primary card-outline mb-3">
    <div class="card-header text-center">
        <img src="<?php echo $comune['stemma']; ?>" style="width:60px; margin-bottom:5px;">
        <h4><?php echo $comune['nome']; ?></h4>
        <small><?php echo $consultazione['titolo']; ?></small>
    </div>
    <div class="card-body">
        <div class="mb-2 no-print text-right">
            <!-- Form PDF → download diretto -->
            <form action="stampa_affluenza.php" method="get" target="_blank" style="display:inline;">
                <input type="hidden" name="action" value="genera_pdf">
                <button class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i> Genera PDF
                </button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th rowspan="2">Sezione</th>
                        <?php foreach($consultazione['date_ore'] as $data => $ore): ?>
                            <th colspan="<?php echo count($ore); ?>"><?php echo $data; ?></th>
                        <?php endforeach; ?>
                        <th rowspan="2">Totale Sezione</th>
                    </tr>
                    <tr>
                        <?php foreach($consultazione['date_ore'] as $data => $ore): ?>
                            <?php foreach($ore as $ora): ?>
                                <th><?php echo $ora; ?></th>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tr>
                    <tr style="background-color:#f0f0f0;font-weight:bold;">
                        <td>Totale %</td>
                        <?php foreach($consultazione['date_ore'] as $data => $ore): ?>
                            <?php foreach($ore as $ora): ?>
                                <td><?php echo $totali_percentuali[$data][$ora]; ?>%</td>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <td>-</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($sezioni as $idx=>$s): ?>
                    <tr style="<?php echo $idx%2==0?'background-color:#f9f9f9;':''; ?>">
                        <td><?php echo $s['numero']; ?></td>
                        <?php foreach($consultazione['date_ore'] as $data => $ore): ?>
                            <?php foreach($ore as $ora): ?>
                                <td><?php echo $s['votanti'][$data][$ora]; ?></td>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <td>
                            <?php
                            $tot = 0;
                            foreach($s['votanti'] as $d=>$orev){
                                foreach($orev as $v) $tot += $v;
                            }
                            echo $tot;
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background-color:#ddd;font-weight:bold;">
                        <td>Totale Generale</td>
                        <?php foreach($consultazione['date_ore'] as $data => $ore): ?>
                            <?php foreach($ore as $ora): ?>
                                <td><?php echo $totali_generali[$data][$ora]; ?></td>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <td>-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
