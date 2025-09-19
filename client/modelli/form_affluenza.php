<?php
$row=elenco_orari();
foreach($row as $key=>$val) $data_ora_disponibili[]=$val;
$row=elenco_sezioni(0);
$totali_sezioni = count($row); 
$comune='Guidonia';
?>

<?php


/*<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modulo affluenza</title>
</head>
<body> */
if(isset($param['data_ora'])) $data_ora=htmlentities($param['data_ora']);
if(isset($data_ora)) include('modelli/genera_pdf_affluenza.php');
else {
?>

    <h2>Stampa moduli di affluenza</h2>
    <form action="modules.php" method="post" target="_blank">
        <label>Data e ora:</label>
        <select name="data_ora" required>
            <option value="">-- Seleziona --</option>
            <?php foreach ($data_ora_disponibili as $dt): ?>
                <option value="<?php echo $dt[2]." - ".$dt[1]; ?>"><?php echo $dt[2]." - ".$dt[1]; ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Sezione:</label>
        <select name="sezione" required>
            <option value="tutte">Tutte le sezioni</option>
            <?php for ($i = 1; $i <= $totali_sezioni; $i++): ?>
                <option value="<?= $i ?>">Sezione <?= $i ?></option>
            <?php endfor; ?>
        </select>
        <input type="hidden" name="totali_sezioni" value="<?= $totali_sezioni ?>">
        <input type="hidden" name="name" value='modelli'>
        <input type="hidden" name="op" value='affluenza'>
        <input type="hidden" name="fase" value='2'>
        <input type="hidden" name="id_comune" value="<?= $id_comune ?>">
        <input type="hidden" name="id_cons_gen" value="<?= $id_cons_gen ?>">
        <br><br>

        <input type="submit" value="Genera PDF">
    </form>

<?php } ?>