<?php
// Sezioni disponibili (simulazione da DB)
$sezioni = range(1, 6);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Scrutinio Ballottaggio - Elezioni Comunali</title>
</head>
<body>
    <h2>Genera modulo di scrutinio per il ballottaggio</h2>
    <form action="genera_pdf_scrutinio_ballottaggio.php" method="post" target="_blank">
        <label>Sezione da stampare:</label><br>
        <select name="sezione" required>
            <option value="tutte">Tutte le sezioni</option>
            <?php foreach ($sezioni as $s): ?>
                <option value="<?= $s ?>">Sezione <?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <input type="submit" value="Genera PDF">
    </form>
</body>
</html>
