<?php
if(isset($_SESSION['sezione_attiva'])) $sezione_attiva=$_SESSION['sezione_attiva'];
if(isset($_SESSION['id_sez'])) $id_sez=$_SESSION['id_sez'];
$num_sez=$sezione_attiva;
	$row=dati_sezione(0,$sezione_attiva);
	$votitotali = [
	$num_sez => ['validi' => $row[0]['validi'], 'nulle' => $row[0]['nulli'], 'bianche' => $row[0]['bianchi'], 'nulli' => $row[0]['voti_nulli'], 'contestati' => $row[0]['contestati']]];
	$totnonvalidi=$row[0]['nulli']+$row[0]['bianchi']+$row[0]['voti_nulli']+$row[0]['contestati'];
	$totvoti=$totnonvalidi+$row[0]['validi'];

?>
    <!-- Totali Finali

    <h3>Totali Finali</h3> -->
	<form id="votiForm"  onsubmit="salva_voti(event)">
	<input type="hidden" name="op" value="32">
	<input type="hidden" name="id_sezione" id="id_sezione" value="<?= $id_sez ?>">
   <div class="table-responsive">
   <table class="table table-bordered text-center">
      <thead class="bg-primary text-white">
        <tr>
          <th>Voti Validi</th>
          <th>Schede Nulle</th>
          <th>Schede Bianche</th>
          <th>Voti Nulli</th>
          <th>Voti Contestati</th>
          <th>Tot. Voti non Validi</th>
          <th>Voti Totali</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="number" class="form-control text-end" id="votiValidi" value="<?php echo $votitotali[$num_sez]['validi']; ?>" /></td>
          <td><input type="number" class="form-control text-end" id="schedeNulle" value="<?php echo $votitotali[$num_sez]['nulle']; ?>" /></td>
          <td><input type="number" class="form-control text-end" id="schedeBianche" value="<?php echo $votitotali[$num_sez]['bianche']; ?>" /></td>
          <td><input type="number" class="form-control text-end" id="votiNulli" value="<?php echo $votitotali[$num_sez]['nulli']; ?>" /></td>
          <td><input type="number" class="form-control text-end" id="votiContestati" value="<?php echo $votitotali[$num_sez]['contestati']; ?>" /></td>
          <td id="totNonValidi"><?php echo $totnonvalidi; ?></td>
          <td id="totaliVoti"><?php echo $totvoti; ?></td>
          <td><button id="btnOkFinale" class="btn btn-success" >Ok</button></td>

        </tr>
      </tbody>
    </table>
	</div>
	</form>
  </div>

