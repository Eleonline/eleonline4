<?php
if (is_file('includes/check_access.php'))
    require_once 'includes/check_access.php';
else
    require_once '../includes/check_access.php';

$tab=$_SESSION['tipo_info'];
$row = elenco_info($tab);
#$nascondi = ($inizioNoGenere > $dataInizio) ? '' : 'display:none;';
/* Numero massimo mid */
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['mid'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
?>

<!-- Riga nascosta per JS -->
<tr id="riga<?= $maxNumero ?>">
    <td colspan="4" id="maxNumero" style="display:none;"><?= $maxNumero ?></td>
</tr>

<!-- Ciclo info -->
<?php foreach ($row as $key => $val): ?>
<tr id="riga<?= $key ?>">

    <td id="mid<?= $key ?>" style="display:none;"><?= $val['mid'] ?></td>
    <td id="title<?= $key ?>"><?= $val['title'] ?></td>
    <td id="preamble<?= $key ?>"><?= $val['preamble'] ?></td>
    <td id="content<?= $key ?>"><?= $val['content'] ?></td>
    <td>
        <button class="btn btn-sm btn-warning me-1" onclick="editInfo(<?= $key ?>);">Modifica</button>
        <button type="button" class="btn btn-danger btn-sm" onclick="deleteInfo(<?= $key ?>)">Elimina</button>
    </td>

</tr>
<?php endforeach; ?>

