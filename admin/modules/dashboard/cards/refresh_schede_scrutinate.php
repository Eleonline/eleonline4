<?php
// Dati aggiornati simulati
$schede_scrutinate = 6450; // qui puoi collegarti al DB per valore reale
$schede_totali = 13200;

// Se vuoi MySQL (commentato)
/*
$sql = "SELECT schede_scrutinate, schede_totali
        FROM risultati_sezioni
        ORDER BY aggiornamento DESC
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$schede_scrutinate = $row['schede_scrutinate'];
$schede_totali = $row['schede_totali'];
*/

echo "<h3>" . number_format($schede_scrutinate) . " su " . number_format($schede_totali) . "</h3>";
?>
