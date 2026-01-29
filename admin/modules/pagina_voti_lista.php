<?php
if(is_file('includes/check_access.php'))
    require_once 'includes/check_access.php';
else
    require_once '../includes/check_access.php';
?>
<style>
/* Rimuove freccette nei campi number (Chrome, Safari) */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
/* Rimuove freccette nei campi number (Firefox) */
input[type="number"] {
  -moz-appearance: textfield;
}

/* Ottimizzazione larghezze tabelle */
.smartable th:nth-child(1),
.smartable td:nth-child(1) {
  width: 50px;
  text-align: center;
}
.smartable th:nth-child(3),
.smartable td:nth-child(3) {
  width: 100px;
  text-align: center;
}
.smartable td input {
  text-align: right;
  width: 100%;
}

/* Spazi verticali ridotti */
section.content > .container-fluid > * {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

h3, h5 {
  margin-top: 0.75rem;
  margin-bottom: 0.75rem;
}

/* Rimuove margine extra da btn-group */
.mb-3 {
  margin-bottom: 0.5rem !important;
}

/* Riduce margine checkbox + bottone */
.d-flex.align-items-center.mt-2 {
  margin-top: 0.5rem !important;
  margin-bottom: 0.5rem !important;
}
input[type=number].text-end {
  text-align: right !important;
}
  #boxMessaggio {
    max-width: 500px;
    margin: 20px auto; /* centro orizzontale con margine sopra e sotto */
    display: none;
    text-align: center;
  }
  .btn-verde {
  border: 3px solid #28a745 !important;
  box-shadow: 0 0 5px #28a745;
}
.btn-rosso {
  border: 3px solid #dc3545 !important;
  box-shadow: 0 0 5px #dc3545;
}

</style>

<?php
if(isset($_SESSION['sezione_attiva'])) $sezione_attiva=$_SESSION['sezione_attiva'];
if(isset($_SESSION['id_sez'])) $id_sez=$_SESSION['id_sez'];
if(isset($_SESSION['id_cons_gen'])) $id_cons_gen=$_SESSION['id_cons_gen'];
if(isset($_SESSION['id_cons'])) $id_cons=$_SESSION['id_cons'];
if(isset($sezione_attiva)) $num_sez=$sezione_attiva;
echo "<script>const idSez = " . json_encode($id_sez) . ";</script>";

$liste=array();
$row=elenco_liste();
$numliste=count($row);
foreach($row as $key=>$val)
	$liste[]=['id' => $val['id_lista'],'num' => $val['num_lista'],'nome' => $val['descrizione']];
$row=voti_lista_sezione($id_sez);
$tot_voti_lista=0;
foreach($row as $key=>$val){
	$votiSezione[$id_sez][$val['num_lista']]= $val['voti'];
$tot_voti_lista+=$val['voti'];}
?>
    <!-- Tabella Voti di Lista -->
    <div class="table-responsive">
		<form id="listeForm"  onsubmit="salva_voti_lista(event)">
		<input type="number" id="numSezLista" value="<?= $sezione_attiva ?>" style="display:none">
		<input type="number" id="idSezLista" value="<?= $id_sez ?>" style="display:none">
		<input type="number" id="numListe" value="<?= $numliste ?>" style="display:none">
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
			  <td><input type="number" class="form-control form-control-sm voto-lista" id="lista<?php echo $lista['num']; ?>" maxlength="5" value="<?php echo $votiSezione[$id_sez][$lista['num']] ?? 0; ?>" name="<?php echo $lista['id']; ?>"></td>
			</tr>
			<?php endforeach; ?>
			<tr class="table-primary font-weight-bold">
			  <td colspan="2">Totale Voti di lista</td>
			  <td id="totaleVotiLista" class="text-right"><?php echo $tot_voti_lista; ?></td>
			</tr>
			<tr> <td colspan="3" style="text-align: right;" ><button id="btnOkFinale" class="btn btn-success">Ok</button></td></tr>

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

