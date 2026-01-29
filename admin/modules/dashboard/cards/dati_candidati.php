<?php
// Esempio PHP: qui puoi leggere dal DB in tempo reale
$candidati = [
    ['nome' => 'Mario', 'cognome' => 'Rossi', 'lista' => 'Lista A', 'voti' => rand(1200, 1500)],
    ['nome' => 'Luigi', 'cognome' => 'Bianchi', 'lista' => 'Lista B', 'voti' => rand(900, 1100)],
    ['nome' => 'Anna', 'cognome' => 'Verdi', 'lista' => 'Lista C', 'voti' => rand(700, 900)],
];

echo '<table class="table table-striped table-sm">';
echo '<thead><tr><th>Candidato</th><th>Lista</th><th>Voti</th></tr></thead><tbody>';
foreach($candidati as $c){
    echo '<tr>';
    echo '<td>'.htmlspecialchars($c['nome'].' '.$c['cognome']).'</td>';
    echo '<td>'.htmlspecialchars($c['lista']).'</td>';
    echo '<td>'.number_format($c['voti']).'</td>';
    echo '</tr>';
}
echo '</tbody></table>';
