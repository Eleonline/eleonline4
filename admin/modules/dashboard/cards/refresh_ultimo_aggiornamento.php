<?php
// Dati aggiornati
$data = date('d/m/Y');
$ora  = date('H:i');

// Se vuoi MySQL:
// $sql = "SELECT DATE_FORMAT(data_aggiornamento,'%d/%m/%Y') AS data, DATE_FORMAT(data_aggiornamento,'%H:%i') AS ora
//         FROM log_aggiornamenti ORDER BY data_aggiornamento DESC LIMIT 1";
// $stmt = $pdo->prepare($sql); $stmt->execute(); $row = $stmt->fetch(PDO::FETCH_ASSOC);
// $data = $row['data']; $ora = $row['ora'];

echo "<h4>$data</h4>";
echo "<h5>Ore $ora</h5>";
?>
