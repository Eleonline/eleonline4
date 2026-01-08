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

		<td id="mappa<?= $key ?>" class="text-center align-middle">
			<?php if (!empty($val['latitudine']) && !empty($val['longitudine'])): ?>
				<button type="button"
						class="btn btn-sm btn-info me-1"
						onclick="apriMappaSoloVisualizza(<?= $val['latitudine'] ?>, <?= $val['longitudine'] ?>, '<?= addslashes($val['indirizzo']) ?>')"
						title="Visualizza mappa">
					<i class="fas fa-map-marked-alt"></i>
				</button>
			<?php elseif (!empty($val['filemappa'])): ?>
				<?= $val['filemappa'] ?>
			<?php else: ?>
				<button type="button" class="btn btn-sm btn-secondary" disabled>
					Nessuna mappa
				</button>
			<?php endif; ?>
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
            <td>
        <button type="button"
            class="btn btn-sm btn-warning me-1"
            onclick="editSede(<?= $key ?>); scrollToGestioneSedi();">
        Modifica
    </button>

    <button type="button"
            class="btn btn-sm btn-danger"
            onclick="confermaEliminaSede(<?= $key ?>)">
        Elimina
    </button>
</td>


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
