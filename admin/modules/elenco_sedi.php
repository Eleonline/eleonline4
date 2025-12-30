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

        <td>
        <div id="idSede<?= $key ?>" style="display:none;"><?= $val['id_sede'] ?></div>
        <div id="idCirc<?= $key ?>" style="display:none;"><?= $val['id_circ'] ?></div>
        <div id="lat<?= $key ?>" style="display:none;"><?= $val['latitudine'] ?></div>
        <div id="lng<?= $key ?>" style="display:none;"><?= $val['longitudine'] ?></div>
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
