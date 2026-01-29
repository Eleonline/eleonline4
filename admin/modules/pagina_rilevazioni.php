<?php
if(is_file('includes/check_access.php'))
    require_once 'includes/check_access.php';
else
    require_once '../includes/check_access.php';
global $id_cons_gen,$id_cons;
if(isset($_SESSION['sezione_attiva'])) $sezione_attiva=$_SESSION['sezione_attiva'];
if(isset($_SESSION['id_sez'])) $id_sez=$_SESSION['id_sez'];
if(isset($_SESSION['id_cons_gen'])) $id_cons_gen=$_SESSION['id_cons_gen'];
if(isset($_SESSION['id_cons'])) $id_cons=$_SESSION['id_cons'];
$num_sez=$_SESSION['num_sez'];
$row = dati_consultazione(0);
$dataInizio = strtotime($row[0]['data_inizio']);

// Definizione temporanea per evitare errori
$inizioNoGenere = time(); // <-- qui definisci la tua data reale
$nascondi = ($inizioNoGenere > $dataInizio) ? '' : 'style="display:none;"';

$sezione_attiva=$num_sez;
$orari = elenco_orari();
$affluenze = elenco_affluenze($id_sez);
//checkbox cancella
if(isset($_POST['chkDelete'])) {
    foreach($_POST['chkDelete'] as $key => $val) {
        // qui cancelli i dati della riga $key
    }
}
?>
    <!-- Totali Finali

    <h3>Totali Finali</h3> -->
	<form id="votiForm"  onsubmit="salva_affluenza(event)">
	<input type="hidden" id="contaok" value="<?= count($orari) ?>">
	<input type="hidden" id="id" value="">
	<input type="hidden" name="op" value="31">
	<input type="hidden" name="id_sezione" id="id_sezione" value="<?= $id_sez ?>">
   <table class="table table-bordered text-center">
      <thead class="bg-primary text-white">
        <tr>
          <th>Data</th>
          <th>Orario</th>
		<?php  if(!$nascondi) { ?>
          <th>Maschi</th>
          <th>Femmine</th>
		<?php } ?>
		<th>Voti Totali</th>
          <th></th>
		  <th></th>
        </tr>
      </thead>
      <tbody>
	<?php foreach($orari as $key=>$val): 
    $dis = ($key === count($affluenze) - 1) ? '' : 'disabled';
	$nasc = ($key === count($affluenze) - 1) ? '' : 'style="display:none;"';

	?>
	<tr>
		<td><input type="date" class="form-control text-end" id="data<?= $key ?>" value="<?= $val['data'] ?>" disabled></td>
		<td><input type="time" class="form-control text-end" id="orario<?= $key ?>" value="<?= $val['orario'] ?>" disabled></td>

		<?php if(!$nascondi): ?>
			<td><input type="number" class="form-control text-end" id="uomini<?= $key ?>" value="<?= isset($affluenze[$key]['voti_uomini']) ? $affluenze[$key]['voti_uomini'] : '' ?>"></td>
			<td><input type="number" class="form-control text-end" id="donne<?= $key ?>" value="<?= isset($affluenze[$key]['voti_donne']) ? $affluenze[$key]['voti_donne'] : '' ?>"></td>
		<?php endif; ?>

		<td><input type="number" class="form-control text-end" id="totale<?= $key ?>" onclick="attiva_ok(<?= $key ?>)" value="<?= isset($affluenze[$key]['voti_complessivi']) ? $affluenze[$key]['voti_complessivi'] : '' ?>"></td>
		<td>
  <span <?= $nasc ?>>
    <input type="checkbox" id="chkDelete<?= $key ?>" name="chkDelete[<?= $key ?>]"> Elimina
  </span>
</td>

		<td><button id="btnOkFinale<?= $key ?>" class="btn btn-success" onclick="test('<?= $key ?>')" <?= $dis ?>>Ok</button></td>
	</tr>
	<?php endforeach; ?>
      </tbody>
    </table>
	</form>

<script>
function test(id) {
	document.getElementById('id').value=id;
}

function attiva_ok(id) {
	const conta = parseInt(document.getElementById('contaok').value);
	for (i = 0; i < conta; i++) {
		if(document.getElementById('btnOkFinale'+i))
			document.getElementById('btnOkFinale'+i).disabled=true;
	} 
	document.getElementById('btnOkFinale'+id).disabled=false;
}
</script>