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
if(isset($_SESSION['id_lista'])) $id_lista=$_SESSION['id_lista'];
if(isset($sezione_attiva)) $num_sez=$sezione_attiva;
echo "<script>const idSez = " . json_encode($id_sez) . ";</script>";
if(!isset($id_lista)){
	$num_lista=1;
	$row=dati_lista();
	$id_lista=$row[0]['id_lista'];
}
$candidati=array();
$row=elenco_candidati_lista($id_lista);
$numCandidati=count($row);
foreach($row as $key=>$val)
	$candidati[]=['id' => $val['id_cand'],'num' => $val['num_cand'],'nome' => $val['cognome'].' '. $val['nome']];
$row=voti_candidato_sezione($id_sez,$id_lista);
$tot_voti_candidato=0;
foreach($row as $key=>$val){
	$votiSezione[$id_sez][$val['num_cand']]= $val['voti'];
$tot_voti_candidato+=$val['voti'];}
$liste=elenco_liste();

?>
    <!-- Tabella Voti di Lista -->
    <div class="table-responsive">
		<div class="col-sm-4">
		<label  class="col-sm-3 col-form-label">Lista</label>
		  <select class="form-control form-control-sm" id="idLista" onchange="aggiorna_candidato(<?= $sezione_attiva ?>)">
			<option value="0"></option>
			<?php foreach($liste as $key=>$val) { ?>
			<option value="<?= $val['id_lista'] ?>" <?php if($val['id_lista']==$id_lista) echo "selected";?>><?= $val['descrizione'] ?></option>
			<?php } ?>
		  </select>
		</div>
		<form id="candidatiForm"  onsubmit="salva_voti_candidato(event)">
		<input type="number" id="numSezCandidato" value="<?= $sezione_attiva ?>" style="display:none">
		<input type="number" id="idSezCandidato" value="<?= $id_sez ?>" style="display:none">
		<input type="number" id="numCandidati" value="<?= $numCandidati ?>" style="display:none">
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
			<?php foreach ($candidati as $index => $candidato): ?>
			<tr>
			  <td><?php echo $candidato['num']; ?></td>
			  <td><?php echo htmlspecialchars($candidato['nome']); ?></td>
			  <td><input type="number" class="form-control form-control-sm voto-candidato" id="candidato<?php echo $candidato['num']; ?>" maxlength="5" value="<?php echo $votiSezione[$id_sez][$candidato['num']] ?? 0; ?>" name="<?php echo $candidato['id']; ?>"></td>
			</tr>
			<?php endforeach; ?>
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

