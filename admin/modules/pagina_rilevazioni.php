<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';

if(isset($_SESSION['sezione_attiva'])) $sezione_attiva=$_SESSION['sezione_attiva'];
if(isset($_SESSION['id_sez'])) $id_sez=$_SESSION['id_sez'];
$row = dati_consultazione(0);
$dataInizio = strtotime($row[0]['data_inizio']);
$num_sez=$sezione_attiva;
$orari = elenco_orari();
$affluenze = elenco_affluenze($id_sez);
$nascondi = ($inizioNoGenere > $dataInizio) ? '' : 'style="display:none;"';
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
        </tr>
      </thead>
      <tbody>
	  <?php foreach($orari as $key=>$val) { if(count($affluenze)==$key) $dis= ''; else $dis='disabled';?>
        <tr>
          <td><input type="date" class="form-control text-end" id="data<?= $key ?>" value="<?php echo $val['data']; ?>" disabled></td>
          <td><input type="time" class="form-control text-end" id="orario<?= $key ?>" value="<?php echo $val['orario']; ?>" disabled ></td>
 		<?php if(count($affluenze)>=$key) { if(!$nascondi) { ?>
         <td><input type="number" class="form-control text-end" id="uomini<?= $key ?>" value="<?php if(isset($affluenze[$key]['voti_uomini'])) echo $val['voti_uomini']; ?>" ></td>
          <td><input type="number" class="form-control text-end" id="donne<?= $key ?>" value="<?php if(isset($affluenze[$key]['voti_donne'])) echo $val['voti_donne']; ?>" ></td>
		<?php } if(isset($affluenze[$key]['voti_complessivi'])) $tot = $affluenze[$key]['voti_complessivi']; else $tot='';?>
          <td><input type="number" class="form-control text-end" id="totale<?= $key ?>" onclick="attiva_ok(<?= $key ?>)" value="<?= $tot ?>"></td>
          <td><button id="btnOkFinale<?= $key ?>" class="btn btn-success" onclick="test('<?= $key ?>')" <?= $dis ?>>Ok</button></td>
		<?php } ?>
        </tr>
		<?php } ?>
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