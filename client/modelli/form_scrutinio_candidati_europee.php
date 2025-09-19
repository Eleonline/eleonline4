<?php
$totali_sezioni = 6; // oppure da DB
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Stampa Scrutinio Preferenze Candidati</title>
</head>
<body>
    <h2>Genera modulo scrutinio preferenze per candidati</h2>
    <form action="genera_pdf_scrutinio_candidati_europee.php" method="post" target="_blank">
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
