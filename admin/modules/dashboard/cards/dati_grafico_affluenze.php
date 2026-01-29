<?php
header('Content-Type: application/json');

$affluenze = [];
$tipo_cons = 0; // oppure prendi dal contesto
$maxElettori = isset($comune['elettori']) ? $comune['elettori'] : 1000;

if ($tipo_cons == 2) {
    $row = affluenze_referendum(1, 0);
} else {
    $row = affluenze_totali(0);
}

if (!empty($row)) {
    foreach ($row as $val) {
        $elettoriPresenti = $val['complessivi'] ?? 0;

        // Calcolo percentuale
        $percentuale = $maxElettori > 0 ? ($elettoriPresenti / $maxElettori) * 100 : 0;

        // Formatta data in italiano gg/mm/yyyy HH:ii
        $dataItaliano = '';
        if (!empty($val['data'])) {
            $dt = DateTime::createFromFormat('Y-m-d H:i:s', $val['data'] . ' ' . ($val['orario'] ?? '00:00:00'));
            if ($dt) $dataItaliano = $dt->format('d/m/Y H:i');
        }

        $affluenze[] = [
            'data' => $dataItaliano ?: ($val['data'] . ' ' . ($val['orario'] ?? '')),
            'perc' => round($percentuale, 1),
            'val'  => $elettoriPresenti
        ];
    }
}

echo json_encode($affluenze, JSON_UNESCAPED_UNICODE);
?>
