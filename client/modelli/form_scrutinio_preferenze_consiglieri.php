<?php
$totali_sezioni = 6;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Stampa modulo di Scrutinio Preferenze (Consiglieri)</title>
</head>
<body>
    <h2>Genera moduli di Scrutinio Preferenze (Consiglieri)</h2>
    <form action="genera_pdf_scrutinio_preferenze_consiglieri.php" method="post" target="_blank">
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
