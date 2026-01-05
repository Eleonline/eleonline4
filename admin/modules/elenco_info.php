<?php
if (is_file('includes/check_access.php'))
    require_once 'includes/check_access.php';
else
    require_once '../includes/check_access.php';

$tab = $_SESSION['tipo_info'];
$row = elenco_info($tab);

/* Numero massimo mid */
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['mid'];
} else {
    $maxNumero = 0;
}
$maxNumero++;

// Determina se la terza colonna deve essere visibile
// Regole:
// "come" => nascosta
// "servizio" => nascosta
// "link" => visibile
// "numero" => visibile
$showCol3 = in_array($tab, ['link', 'numero']) ? '' : 'display:none;';
?>

<!-- Riga nascosta per JS -->
<tr id="riga<?= $maxNumero ?>">
    <td colspan="4" id="maxNumero" style="display:none;"><?= $maxNumero ?></td>
</tr>

<!-- Ciclo info -->
<?php foreach ($row as $key => $val): ?>
<tr id="riga<?= $key ?>">
    <td id="title<?= $key ?>"><?= $val['title'] ?></td>
    <td id="preamble<?= $key ?>"><?= $val['preamble'] ?></td>
    <td id="content<?= $key ?>" style="<?= $showCol3 ?>"><?= $val['content'] ?></td>
    <td>
        <div id="mid<?= $key ?>" style="display:none;"><?= $val['mid'] ?></div>
		<button type="button"
        class="btn btn-sm btn-warning me-1"
        onclick="editInfo(<?= $key ?>); scrollToFormTitle();">
    Modifica
</button>

        <button type="button" class="btn btn-danger btn-sm" onclick="deleteInfo(<?= $key ?>)">Elimina</button>
    </td>
</tr>
<?php endforeach; ?>
<script>
function scrollToFormTitle() {
    const target = document.getElementById('form-title');
    if (target) {
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

</script>