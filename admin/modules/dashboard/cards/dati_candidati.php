<?php
// Esempio PHP: qui puoi leggere dal DB in tempo reale
$candidati = [
    ['nome' => 'Mario', 'cognome' => 'Rossi', 'lista' => 'Lista A', 'voti' => rand(1200, 1500)],
    ['nome' => 'Luigi', 'cognome' => 'Bianchi', 'lista' => 'Lista B', 'voti' => rand(900, 1100)],
    ['nome' => 'Anna', 'cognome' => 'Verdi', 'lista' => 'Lista C', 'voti' => rand(700, 900)],
];

// Calcolo totale voti
$totaleVoti = array_sum(array_column($candidati, 'voti'));

echo '<table class="table table-striped table-sm">';
echo '<thead><tr><th>Candidato</th><th>Lista</th><th>Voti</th><th>%</th></tr></thead><tbody>';

foreach($candidati as $c){
    $percent = $totaleVoti > 0 ? ($c['voti'] / $totaleVoti) * 100 : 0;

    echo '<tr>';
    echo '<td>'.htmlspecialchars($c['nome'].' '.$c['cognome']).'</td>';
    echo '<td>'.htmlspecialchars($c['lista']).'</td>';
    echo '<td>'.number_format($c['voti'], 0, ',', '.').'</td>';
    echo '<td>'.number_format($percent, 1, '.', '').'%</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '<tfoot>';
echo '<tr><th colspan="2">Totale</th><th>'.number_format($totaleVoti, 0, ',', '.').'</th><th>100%</th></tr>';
echo '</tfoot>';
echo '</table>';
?>

