<?php
// Demo PHP: dati in tempo reale (puoi sostituire con query DB)
$listes = [
    ['lista' => 'Lista A', 'voti' => rand(1200,1500)],
    ['lista' => 'Lista B', 'voti' => rand(900,1100)],
    ['lista' => 'Lista C', 'voti' => rand(700,900)],
];

// Genera la tabella
echo '<table class="table table-striped table-sm">';
echo '<thead><tr><th>Lista</th><th>Voti</th></tr></thead><tbody>';
foreach($listes as $l){
    echo '<tr>';
    echo '<td>'.htmlspecialchars($l['lista']).'</td>';
    echo '<td>'.number_format($l['voti']).'</td>';
    echo '</tr>';
}
echo '</tbody></table>';
