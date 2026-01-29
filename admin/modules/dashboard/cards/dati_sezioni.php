<?php
header('Content-Type: application/json');

$row = elenco_sezioni();
$totale_sezioni = totale_sezioni();
$colore = [];
$sezioni_scrutinate = 0;

if(count($row)){
    foreach($row as $val) {
        $colore[$val['num_sez']] = $val['colore'];
        if(!empty($val['colore'])) $sezioni_scrutinate++;
    }
}

echo json_encode([
    'totale' => $totale_sezioni,
    'scrutinate' => $sezioni_scrutinate,
    'colori' => $colore
]);
?>
