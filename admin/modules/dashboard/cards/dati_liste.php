<?php
// Esempio PHP per dati_liste.php: restituisce solo la tabella aggiornata
// Puoi sostituire con query al DB reale
$listes = [
    ['lista' => 'Lista A', 'voti' => rand(1200,1500)],
    ['lista' => 'Lista B', 'voti' => rand(900,1100)],
    ['lista' => 'Lista C', 'voti' => rand(700,900)],
];

// Calcolo totale voti
$totaleVoti = array_sum(array_column($listes, 'voti'));

// Generazione tabella HTML
echo '<table class="table table-striped table-sm">';
echo '<thead><tr><th>Lista</th><th>Voti</th><th>%</th></tr></thead>';
echo '<tbody>';
foreach($listes as $l){
    $percent = $totaleVoti > 0 ? ($l['voti'] / $totaleVoti) * 100 : 0;
    echo '<tr>';
    echo '<td>'.htmlspecialchars($l['lista']).'</td>';
    echo '<td>'.number_format($l['voti'], 0, ',', '.').'</td>';
    echo '<td>'.number_format($percent, 1, '.', '').'%</td>';
    echo '</tr>';
}
echo '</tbody>';
echo '<tfoot>';
echo '<tr><th>Totale</th><th>'.number_format($totaleVoti, 0, ',', '.').'</th><th>100%</th></tr>';
echo '</tfoot>';
echo '</table>';
?>
