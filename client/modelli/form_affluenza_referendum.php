<?php
// Simulazione dati disponibili
$data_ora_disponibili = [
    "09-06-2025 12:00",
    "09-06-2025 19:00",
    "09-06-2025 23:00"
];

$totali_sezioni = 6;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Affluenza Referendum</title>
</head>
<body>
    <h2>Stampa moduli affluenza – Referendum</h2>
    <form action="genera_pdf_affluenza_referendum.php" method="post" target="_blank">
        <label>Data e ora:</label><br>
        <select name="data_ora" required>
            <option value="">-- Seleziona --</option>
            <?php foreach ($data_ora_disponibili as $dt): ?>
                <option value="<?= $dt ?>"><?= $dt ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Sezione:</label><br>
        <select name="sezione" required>
            <option value="tutte">Tutte le sezioni</option>
            <?php for ($i = 1; $i <= $totali_sezioni; $i++): ?>
                <option value="<?= $i ?>">Sezione <?= $i ?></option>
            <?php endfor; ?>
        </select>
        <br><br>

        <input type="submit" value="Genera PDF">
    </form>
</body>
</html>
