<?php
$totali_sezioni = 6;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Comunicazioni Presidente di Seggio</title>
</head>
<body>
    <h2>Genera modulo comunicazione</h2>
    <form action="genera_pdf_comunicazione.php" method="post" target="_blank">
        <label>Tipo comunicazione:</label><br>
        <select name="tipo" required>
            <option value="">-- Seleziona --</option>
            <option value="1">Costituzione Ufficio Elettorale</option>
            <option value="2">Ricostituzione Ufficio Sezionale</option>
        </select>
        <br><br>

        <label>Data della comunicazione:</label><br>
        <input type="date" name="data" required>
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
