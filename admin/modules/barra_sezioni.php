<?php 
if(is_file('../includes/check_access.php')) 
{
	require_once '../includes/check_access.php';
	require_once '../includes/query.php';
}else{
	require_once 'includes/check_access.php';
	require_once 'includes/query.php';
}
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
#if (isset($param['id_gruppo'])) {$id_gruppo=intval($param['id_gruppo']);}
#if (isset($param['id_cand'])) {$id_cand=intval($param['id_cand']);}
if (isset($param['num_sez'])) {$num_sez=intval($param['num_sez']);} else $num_sez=1;
global $id_cons,$id_sez;
#$_SESSION['id_cons']=$id_cons;
$ultimasez=0;
$id_cons=$_SESSION['id_cons'];
$totale_sezioni=totale_sezioni();
$sezione_attiva = $num_sez;
$row=dati_sezione(0,0);
for($i=1;$i<=$totale_sezioni;$i++) $colore[$i]='';
foreach($row as $key=>$val) {
	if($val['num_sez']==$sezione_attiva) 
		$id_sez=$val['id_sez'];	
	$colore[$val['num_sez']]=$val['colore'];
}
$_SESSION['id_sez']=$id_sez;
$_SESSION['sezione_attiva']=$sezione_attiva;
$row=ultime_affluenze_sezione($id_sez);
# t3.voti_complessivi,t3.voti_uomini,t3.voti_donne
$votantiUltimaOra=[ 1 => ['uomini' => $row[0]['voti_uomini'], 'donne' => $row[0]['voti_donne'], 'totali' => $row[0]['voti_complessivi']]];
$row=elenco_liste();
foreach($row as $key=>$val)
	$liste[]=['id' => $val['id_lista'],'num' => $val['num_lista'],'nome' => $val['descrizione']];
$row=voti_lista_sezione($id_sez);
$tot_voti_lista=0;
foreach($row as $key=>$val){
	$votiSezione[$num_sez][$val['num_lista']]= $val['voti'];
$tot_voti_lista+=$val['voti'];}
?>
<section class="content" id="sezioneContent">
    <!-- Titolo principale -->
	<h3 id="titoloSezione">Voti di Lista - Sezione n. <?php echo $sezione_attiva; ?></h3>
    <!-- Navigazione Sezioni -->
    <div class="mb-3">
   <div class="btn-group" id="sezioniBtn">
<?php

for ($i = 1; $i <= $totale_sezioni; $i++) {
    $classe = ($i == $sezione_attiva) ? 'btn-primary' : 'btn-outline-primary';
    echo '<button style="border: 3px solid '.$colore[$i].' !important; box-shadow: 0 0 5px '.$colore[$i].';" class="btn ' . $classe . '" data-sezione="' . $i . '" onclick="selezionaSezione('.$i.')">' . $i . '</button>';
}
 
	$row=dati_sezione(0,$sezione_attiva);
	$votitotali = [
	$num_sez => ['validi' => $row[0]['validi'], 'nulle' => $row[0]['nulli'], 'bianche' => $row[0]['bianchi'], 'nulli' => $row[0]['voti_nulli'], 'contestati' => $row[0]['contestati']]];
	$totnonvalidi=$row[0]['nulli']+$row[0]['bianchi']+$row[0]['voti_nulli']+$row[0]['contestati'];
	$totvoti=$totnonvalidi+$row[0]['validi'];
echo "<script>const idSez = " . json_encode($id_sez) . ";</script>";	

?>
</div>


    </div>

  <div class="container-fluid">
    <!-- Statistiche Ultima Ora -->
    <h5 class="text-center">Votanti Ultima Ora</h5>
    <table class="table table-bordered text-center mx-auto" style="max-width: 400px; background-color: #f8f9fa; border-radius: 0.375rem;">
  <tbody id="tabellaVotanti">
    <tr>
      <td><strong>Votanti Uomini</strong><br><?php echo $votantiUltimaOra[1]['uomini']; ?></td>
      <td><strong>Votanti Donne</strong><br><?php echo $votantiUltimaOra[1]['donne']; ?></td>
      <td><strong>Totali</strong><br><?php echo $votantiUltimaOra[1]['totali']; ?></td>
    </tr>
  </tbody>
</table>

<!-- Box per messaggio di errore/successo -->
<div id="boxMessaggio" class="card">
  <div id="boxBody" class="card-body">
    <strong id="titoloMsg"></strong> <span id="contenutoMsg"></span>
  </div>
</div>

    <!-- Tabella Voti di Lista -->
    <div class="table-responsive">
		<form action='modules.php'>
		<input type="hidden" name="op" value="32">
		<table class="table table-bordered table-striped smartable">
		  <thead class="bg-primary text-white">
			<tr>
			  <th>#</th>
			  <th>Denominazione</th>
			  <th>Voti</th>
			</tr>
		  </thead>
		  <tbody id="listaVoti">
			<?php foreach ($liste as $index => $lista): ?>
			<tr>
			  <td><?php echo $lista['num']; ?></td>
			  <td><?php echo htmlspecialchars($lista['nome']); ?></td>
			  <td><input type="number" class="form-control form-control-sm voto-lista" id="lista<?php echo $lista['num']; ?>" maxlength="5" value="<?php echo $votiSezione[$sezione_attiva][$lista['num']] ?? 0; ?>" name="<?php echo $lista['id']; ?>"></td>
			</tr>
			<?php endforeach; ?>
			<tr class="table-primary font-weight-bold">
			  <td colspan="2">Totale Voti di lista</td>
			  <td id="totaleVotiLista" class="text-right"><?php echo $tot_voti_lista; ?></td>
			</tr>
			<tr> <td colspan="3" style="text-align: right;" ><button id="btnOkFinale" class="btn btn-success"  onclick="salva_voti_lista(<?php echo $lista['num'].','.$id_sez; ?>)">Ok</button></td></tr>

		  </tbody>
		</table>

    </div>

    <!-- Checkbox + Bottone Elimina -->
    <div class="d-flex align-items-center">
      <div class="form-check me-2">
        <input class="form-check-input" type="checkbox" id="eliminaRiga">
        <label class="form-check-label" for="eliminaRiga">Elimina</label>
      </div>
      <button class="btn btn-danger btn-sm" id="btnConfermaElimina" style="display: none;">
        OK
      </button>
    </div>

    <!-- Totali Finali

    <h3>Totali Finali</h3> -->
	<form action='modules.php'>
	<input type="hidden" name="op" value="32">
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
          <td><button id="btnOkFinale" class="btn btn-success"  onclick="salva_voti()">Ok</button></td>

        </tr>
      </tbody>
    </table>
	</form>
  </div>
</section>

