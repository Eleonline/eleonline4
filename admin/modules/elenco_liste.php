<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}
$row = elenco_gruppi();
if(!count($row)) $grp[0]=0;
foreach($row as $key=>$val) {
	$grp[$val['num_gruppo']]=$val['descrizione'];
}
$row = elenco_liste();
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_lista'];
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
$num_gruppo = htmlspecialchars($val['num_gruppo']   ?? '', ENT_QUOTES, 'UTF-8');
$desc_gruppo   = htmlspecialchars($grp[$val['num_gruppo']]   ?? '', ENT_QUOTES, 'UTF-8');
$id_lista   = htmlspecialchars($val['id_lista']   ?? '', ENT_QUOTES, 'UTF-8');
$numero      = htmlspecialchars($val['num_lista']  ?? '', ENT_QUOTES, 'UTF-8');
$descrizione = htmlspecialchars($val['descrizione'] ?? '', ENT_QUOTES, 'UTF-8');
$simbolo     = htmlspecialchars($val['simbolo']     ?? '', ENT_QUOTES, 'UTF-8');

?>
<tr id="riga<?= $key ?>">
    <td id="numero<?= $key ?>"><?= $numero ?></td>
	<?php if(isset($grp[0]) and $grp[0]==0) { ?>
		<td  style="display:none;"
	<?php }else{ ?>
		<td
	<?php } ?>
		id="gruppo<?= $key ?>"><?= $grp[$val['num_gruppo']] ?></td>
    <td id="denominazione<?= $key ?>"><?= $val['descrizione'] ?></td>
	<td id="simbolo<?= $key ?>"><?= $simbolo ?></td>
    <td> <div id="id_lista<?= $key ?>" style="display:none;"><?= $id_lista ?></div><div id="id_cons<?= $key ?>" style="display:none;"><?= $id_cons ?></div><div id="id_gruppo<?= $key ?>" style="display:none;"><?= $id_gruppo ?></div><div id="num_gruppo<?= $key ?>" style="display:none;"><?= $num_gruppo ?></div>
        <button class="btn btn-sm btn-warning me-1" onclick="editLista(<?= $key ?>); scrollToGestioneLista();">Modifica</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="deleteLista(<?= $key ?>)">Elimina</button>
    </td>
</tr>
<?php endforeach; ?>


