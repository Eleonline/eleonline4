<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}

$row = elenco_sedi(); // elenco sedi
?>

<?php foreach ($row as $key => $val): ?>
    <tr id="riga<?= $key ?>">

        <td id="idSede<?= $key ?>" style="display:none;">
            <?= $val['id_sede'] ?>
        </td>

        <td id="idCirc<?= $key ?>" style="display:none;"><?= $val['id_circ'] ?></td>

        <td id="descrizione<?= $key ?>">
            <?= $val['descrizione'] ?>
        </td>

        <td id="indirizzo<?= $key ?>">
            <?= $val['indirizzo'] ?>
        </td>

        <td id="mappa<?= $key ?>">
            <?= $val['filemappa'] ?>
        </td>

        <td id="telefono<?= $key ?>">
            <?= $val['telefono1'] ?>
        </td>

        <td id="fax<?= $key ?>">
            <?= $val['fax'] ?>
        </td>

        <td id="responsabile<?= $key ?>">
            <?= $val['responsabile'] ?>
        </td>

        <td id="lat<?= $key ?>" style="display:none;">
            <?= $val['latitudine'] ?>
        </td>

        <td id="lng<?= $key ?>" style="display:none;">
            <?= $val['longitudine'] ?>
        </td>

        <td>
            <button type="button"
                    class="btn btn-sm btn-warning me-1"
                    onclick="editSede(<?= $key ?>); scrollToGestioneSedi();">
                Modifica
            </button>

            <button type="button"
                    class="btn btn-sm btn-danger"
                    onclick="deleteSede(<?= $key ?>)">
                Elimina
            </button>
        </td>

    </tr>
<?php endforeach; ?>

<script>
function scrollToGestioneSedi() {
    const target = document.getElementById('titoloGestioneSedi');
    if (target) {
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}
</script>
