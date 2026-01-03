<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}
global $id_cons_gen;
$row=dati_consultazione(0);
$cambia=count($row);
$row = elenco_cons(); // elenco consultazioni
if(!$cambia) {
	$id_cons_gen=$row[0]['id_cons_gen'];
	$_SESSION['id_cons_gen']=$id_cons_gen;
}
?>

<?php foreach ($row as $key => $val): ?>
    <?php
        $pref = ($val['preferita']) ? 1 : '';
    ?>
    <tr id="riga<?= $key ?>">


        <!-- PREFERITA -->
        <td class="text-center"> 
             <?php if ($val['preferita']): ?>
        <i class="fas fa-star text-warning"></i>
	<?php /*?>	
    <?php else: ?>
        <i class="far fa-star text-muted"></i>
    <?php */ ?>
	<?php endif; ?>
			        <!-- CAMPI NASCOSTI -->
        <div style="display:none;" >
		<div id="link_trasparenza<?= $key ?>">
            <?= $val['link_trasparenza'] ?>
		</div>
 
        <div id="id_cons_gen<?= $key ?>">
            <?= $val['id_cons_gen'] ?>
        </div>

        <div id="tipo_cons<?= $key ?>">
            <?= $val['tipo_cons'] ?> 
        </div>

        <div id="chiusa<?= $key ?>">
            <?= $val['chiusa'] ?>
        </div>

        <div id="id_conf<?= $key ?>">
            <?= $val['id_conf'] ?>
        </div>

        <div id="preferita<?= $key ?>">
            <?= $pref ?>
        </div>

        <div id="preferenze<?= $key ?>">
            <?= $val['preferenze'] ?>
        </div>

        <div id="id_fascia<?= $key ?>">
            <?= $val['id_fascia'] ?>
        </div>

        <div id="vismf<?= $key ?>">
            <?= $val['vismf'] ?>
        </div>

        <div id="solo_gruppo<?= $key ?>">
            <?= $val['solo_gruppo'] ?>
        </div>

        <div id="disgiunto<?= $key ?>">
            <?= $val['disgiunto'] ?>
        </div>

        <div id="proiezione<?= $key ?>">
            <?= $val['proiezione'] ?>
        </div>
		</div>
        </td>

        <!-- DATI VISIBILI -->
        <td id="descrizione<?= $key ?>">
            <?= $val['descrizione'] ?>
        </td>

        <td id="data_inizio<?= $key ?>">
            <?= $val['data_inizio'] ?>
        </td>

        <td id="data_fine<?= $key ?>">
            <?= $val['data_fine'] ?>
        </td>

        <!-- AZIONI -->
        <td>
            <button type="button"
                    class="btn btn-sm btn-warning me-1"
                    onclick="editConsultazione(<?= $key ?>); scrollToFormTitle();">
                Modifica
            </button>

            <button type="button"
                    class="btn btn-sm btn-danger"
                    onclick="deleteConsultazione(<?= $key ?>)">
                Elimina
            </button>
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
