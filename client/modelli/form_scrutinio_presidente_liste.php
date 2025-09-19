<?php
$totali_sezioni = 6;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Stampa modulo di Scrutinio Candiati Presidente + Liste</title>
</head>
<body>
    <h2>Genera modulo di scrutinio Candiati Presidente + Liste</h2>
    <form action="genera_pdf_scrutinio_Presidente_liste.php" method="post" target="_blank">
        <label>Sezione da stampare:</label><br>
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
