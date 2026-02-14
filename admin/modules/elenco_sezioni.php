<?php

if (is_file('includes/check_access.php'))
    require_once 'includes/check_access.php';
else
    require_once '../includes/check_access.php';

/* Calcolo visibilità maschi/femmine */
global $inizioNoGenere; //= strtotime('2025/06/30');
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
$arsez=array();
for($i=1;$i<$maxNumero;$i++) $arsez[$i]=$i;
$maxNumero++;
?>

<!-- Riga nascosta per JS -->
<tr id="riga<?= $maxNumero ?>">
    <td colspan="8" id="maxNumero" style="display:none;"><?= $maxNumero ?></td>
</tr>

<!-- Ciclo sezioni -->
<?php foreach ($row as $key => $val): $arsez[$key]=0;?>
<tr id="riga<?= $key ?>">

    <td id="idSede<?= $key ?>" style="display:none;"><?= $val['id_sede'] ?></td>
    <td id="idSezione<?= $key ?>" style="display:none;"><?= $val['id_sez'] ?></td>
    <td id="numero<?= $key ?>"><?= $val['num_sez'] ?></td>
    <td id="indirizzo<?= $key ?>"><?= $val['indirizzo'] ?></td>
    <td id="maschi<?= $key ?>" style="text-align:right; <?= $nascondi ?>"><?= $val['maschi'] ?></td>
    <td id="femmine<?= $key ?>" style="text-align:right; <?= $nascondi ?>"><?= $val['femmine'] ?></td>
    <td id="totale<?= $key ?>" style="text-align:right; white-space:nowrap;">

<?php
$totaleCalcolato = ($inizioNoGenere > $dataInizio)
    ? ($val['maschi'] + $val['femmine'])
    : $val['maschi'];
?>

<!-- Visualizzazione -->
<span id="totaleView<?= $key ?>">
    <?= $totaleCalcolato ?>
    <button class="btn btn-xs btn-link text-primary"
            onclick="editTotale(<?= $key ?>)">
        ✏️
    </button>
</span>

<!-- Modalità edit -->
<span id="totaleEdit<?= $key ?>" style="display:none;">

    <input type="number"
           id="totaleInput<?= $key ?>"
           value="<?= $totaleCalcolato ?>"
           class="form-control form-control-sm d-inline-block"
           style="width:80px; text-align:right;">

    <button class="btn btn-sm btn-success ms-1"
            onclick="salvaTotaleInline(<?= $key ?>)">
        OK
    </button>

    <button class="btn btn-sm btn-secondary ms-1"
            onclick="annullaTotale(<?= $key ?>)">
        ✖
    </button>

</span>

</td>


	<td>
        <button class="btn btn-sm btn-warning me-1" onclick="editSezione(<?= $key ?>);">Modifica</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="confermaEliminaSezione(<?= $key ?>)">
    Elimina
</button>

    </td>

</tr>
<?php endforeach;
$nonpres=0; 
foreach($arsez as $key=>$val) if($val!=0) $nonpres++;
?>
<tr><td colspan="8" style="display:none;" id="saltate"><?= $nonpres ?></td></tr>
