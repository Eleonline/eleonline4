<?php
if (is_file('includes/check_access.php'))
    require_once 'includes/check_access.php';
else
    require_once '../includes/check_access.php';

/* Calcolo visibilità maschi/femmine */
$inizioNoGenere = strtotime('2025/06/30');
$row = dati_consultazione(0);
$dataInizio = strtotime($row[0]['data_inizio']);
$nascondi = ($inizioNoGenere > $dataInizio) ? '' : 'display:none;';

/* Elenco sezioni */
$row = elenco_sezioni();

/* Numero massimo sezione */
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_sez'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
?>

<!-- Riga nascosta per JS -->
<tr id="riga<?= $maxNumero ?>">
    <td colspan="8" id="maxNumero" style="display:none;"><?= $maxNumero ?></td>
</tr>

<!-- Ciclo sezioni -->
<?php foreach ($row as $key => $val): ?>
<tr id="riga<?= $key ?>">

    <td id="idSede<?= $key ?>" style="display:none;"><?= $val['id_sede'] ?></td>
    <td id="idSezione<?= $key ?>" style="display:none;"><?= $val['id_sez'] ?></td>
    <td id="numero<?= $key ?>"><?= $val['num_sez'] ?></td>
    <td id="indirizzo<?= $key ?>"><?= $val['indirizzo'] ?></td>
    <td id="maschi<?= $key ?>" style="text-align:right; <?= $nascondi ?>"><?= $val['maschi'] ?></td>
    <td id="femmine<?= $key ?>" style="text-align:right; <?= $nascondi ?>"><?= $val['femmine'] ?></td>
    <td id="totale<?= $key ?>" style="text-align:right;"><?= number_format($val['maschi'] + $val['femmine'],0,',','.') ?></td>
    <td>
        <button class="btn btn-sm btn-warning me-1" onclick="editSezione(<?= $key ?>); scrollToFormTitle();">Modifica</button>
        <button type="button" class="btn btn-danger btn-sm" onclick="deleteSezione(<?= $key ?>)">Elimina</button>
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
