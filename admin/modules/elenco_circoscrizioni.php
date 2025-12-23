<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}

$row = elenco_circoscrizioni();
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_circ'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
?>
<!-- Riga nascosta per JSstyle="display:none;" -->
<tr id="riga<?= $maxNumero ?>">
    <td colspan="8" id="maxNumero" ><?= $maxNumero ?></td>
</tr>

<?php foreach ($row as $key => $val): 
    $id_cons = htmlspecialchars($val['id_cons'], ENT_QUOTES, 'UTF-8');
    $id_circ = htmlspecialchars($val['id_circ'], ENT_QUOTES, 'UTF-8');
    $numero = htmlspecialchars($val['num_circ'], ENT_QUOTES, 'UTF-8');
    $descrizione = htmlspecialchars($val['descrizione'], ENT_QUOTES, 'UTF-8');
?>
<tr id="riga<?= $key ?>">
    <td id="id_cons<?= $key ?>" style="display:none;"><?= $id_cons ?></td>
    <td id="id_circ<?= $key ?>" style="display:none;"><?= $id_circ ?></td>
    <td id="numero<?= $key ?>"><?= $numero ?></td>
    <td id="denominazione<?= $key ?>"><?= $descrizione ?></td>
    <td>
        <button class="btn btn-sm btn-warning me-1" onclick="editCircoscrizione(<?= $key ?>); scrollToGestioneCircoscrizioni();">Modifica</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="deleteCircoscrizione(<?= $key ?>)">Elimina</button>
    </td>
</tr>
<?php endforeach; ?>


