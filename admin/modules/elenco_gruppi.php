<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}

$row = elenco_gruppi();
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_gruppo'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
?>
<!-- Riga nascosta per JSstyle="display:none;" -->
<tr id="riga<?= $maxNumero ?>">
    <td id="maxNumero" colspan="7" style="display:none;"><?= $maxNumero ?></td>
</tr>

<?php foreach ($row as $key => $val): 
    $id_cons     = htmlspecialchars($val['id_cons']     ?? '', ENT_QUOTES, 'UTF-8');
$id_gruppo   = htmlspecialchars($val['id_gruppo']   ?? '', ENT_QUOTES, 'UTF-8');
$numero      = htmlspecialchars($val['num_gruppo']  ?? '', ENT_QUOTES, 'UTF-8');
$descrizione = htmlspecialchars($val['descrizione'] ?? '', ENT_QUOTES, 'UTF-8');
$simbolo     = htmlspecialchars($val['simbolo']     ?? '', ENT_QUOTES, 'UTF-8');
$prognome    = htmlspecialchars($val['prognome']    ?? '', ENT_QUOTES, 'UTF-8');
$cv          = htmlspecialchars($val['cv']          ?? '', ENT_QUOTES, 'UTF-8');
$cg          = htmlspecialchars($val['cg']          ?? '', ENT_QUOTES, 'UTF-8');

?>
<tr id="riga<?= $key ?>">
    <td id="numero<?= $key ?>"><?= $numero ?></td>
    <td id="denominazione<?= $key ?>"><?= $descrizione ?></td>
	<td id="simbolo<?= $key ?>"><?= $simbolo ?></td>
	<td id="prognome<?= $key ?>"><?= $prognome ?></td>
	<?php if ($tipo_cons === 3): ?>
	<td id="cv<?= $key ?>"><?= $cv ?></td>
	<td id="cg<?= $key ?>"><?= $cg ?></td>
	<?php endif; ?>
    <td> 
	<div id="id_cons<?= $key ?>" style="display:none;"><?= $id_cons ?></div>
	<div id="id_gruppo<?= $key ?>" style="display:none;"><?= $id_gruppo ?></div>
        <button class="btn btn-sm btn-warning me-1" onclick="editGruppo(<?= $key ?>); scrollToFormTitle();">Modifica</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="deleteGruppo(<?= $key ?>)">Elimina</button>
    </td>
</tr>
<?php endforeach; ?>


