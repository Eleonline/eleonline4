<?php
header('Content-Type: application/json');

// Dati simulati
$affluenza = 62.45;
$orario = date('H:i');

// Se vuoi MySQL (commentato)
/*
$sql = "SELECT affluenza, DATE_FORMAT(orario,'%H:%i') AS orario
        FROM affluenza_sezioni
        ORDER BY aggiornamento DESC
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$affluenza = $row['affluenza'];
$orario = $row['orario'];
*/

echo json_encode([
  'affluenza' => $affluenza,
  'orario' => $orario
]);
?>
