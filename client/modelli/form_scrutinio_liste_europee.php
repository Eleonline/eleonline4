<?php
$totali_sezioni = 6; // Da DB in futuro
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Stampa Modulo Scrutinio Liste</title>
</head>
<body>
    <h2>Genera modulo di scrutinio per le liste</h2>
    <form action="genera_pdf_scrutinio_liste_europee.php" method="post" target="_blank">
        <label>Sezione:</label>
        <select name="sezione" required>
            <option value="tutte">Tutte le sezioni</option>
            <?php for ($i = 1; $i <= $totali_sezioni; $i++): ?>
                <option value="<?= $i ?>">Sezione <?= $i ?></option>
            <?php endfor; ?>
        </select>
        <input type="hidden" name="totali_sezioni" value="<?= $totali_sezioni ?>">
        <br><br>
        <input type="submit" value="Genera PDF">
    </form>
</body>
</html>
