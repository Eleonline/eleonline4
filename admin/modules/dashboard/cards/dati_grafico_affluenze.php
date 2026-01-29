<?php
header('Content-Type: application/json');

$affluenze = [];
$tipo_cons = 0; // oppure prendi dal contesto

if ($tipo_cons == 2) {
    $row = affluenze_referendum(1, 0);
} else {
    $row = affluenze_totali(0);
}

if (!empty($row)) {
    foreach ($row as $val) {
        $affluenze[] = [
            'data' => $val['data'] . ' ' . $val['orario'],
            'val' => $val['complessivi'] ?? 0
        ];
    }
}

echo json_encode($affluenze);
?>
