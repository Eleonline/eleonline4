<?php
require_once '../includes/check_access.php';

$inizioNoGenere=strtotime('2025/06/30');
$row=dati_consultazione(0);
$dataInizio=strtotime($row[0]['data_inizio']);
global $inizioNoGenere;
$currentUserRole = $_SESSION['ruolo'] ?? 'operatore';
$row=elenco_sedi();
$sezioni = elenco_sezioni();
if(count($sezioni)){
	$ultimo = end($sezioni);
	$maxNumero = $ultimo['num_sez'];
}else 
	$maxNumero = 0;
// qui puoi caricare le consultazioni dal DB o array
$consultazioni = elenco_cons();

?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-map-marker-alt"></i> Gestione Sezioni</h2>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="form-title">Aggiungi Sezione</h3>
      </div>
      <div class="card-body">
<form id="sezioneForm" onsubmit="aggiungiSezione(event)">
  <input type="hidden" id="idSezione" value="">
  <div class="form-row">
    <div col-md-2">
      <label>Numero Sezione*</label>
      <input type="number" id="numero" class="form-control" required min="1" value="<?php echo $maxNumero + 1; ?>">
    </div>
    <div class="form-group col-md-4">
      <label>Indirizzo (Sede)*</label>
      <select id="idSede" class="form-control">
	  <?php foreach($row as $key=>$val) { ?>
	  <option value="<?= $val['id_sede'] ?>"><?= $val['indirizzo'] ?></option>
	  <?php } ?>
	  </select>
    </div>
	<?php if($inizioNoGenere>$dataInizio) $nascondi=''; else $nascondi='style="display:none;"' ?>
    <div class="form-group col-md-2">
<?php if($inizioNoGenere>$dataInizio) { ?>
	<label>Maschi</label>
<?php }else{ ?>	
	<label>Iscritti</label>
<?php } ?>	
      <input type="number" id="maschi" class="form-control" min="0">
    </div>
    <div class="form-group col-md-2" <?= $nascondi ?>>
      <label>Femmine</label>
      <input type="number" id="femmine" class="form-control" min="0">
    </div>
    <div class="form-group col-md-2" <?= $nascondi ?>>
      <label>Totale iscritti</label>
      <input type="number" id="totale" class="form-control" min="0">
    </div>
    <div class="form-group col-md-2 d-flex align-items-end">
      <button type="submit" class="btn btn-success w-100" id="btnSalvaSezione">Aggiungi sezione</button>
    </div>
  </div>
</form>

      </div>
    </div>

    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Lista Sezioni</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="sezioniTable">
          <thead>
            <tr>
              <th>Numero</th>
              <th>Indirizzo</th>
			  <?php $span = 'colspan="5"'; if($inizioNoGenere>$dataInizio) { $span= 'colspan="3"'?>
              <th>Maschi</th>
              <th>Femmine</th>
			  <?php } ?>
              <th>Totale iscritti</th>
              <th <?= $span ?>>Azioni</th>
            </tr>
          </thead>
          <tbody id="risultato"> 
		  <?php include('elenco_sezioni.php'); ?>
		  </tbody>
        </table>
		<div id="paginationControls" class="mt-3"></div>
      </div>
    </div>
  </div>
</section>

<script>
function aggiungiSezione(e) {
    e.preventDefault();

  const id_sede = document.getElementById('idSede').value;
  const id_sez = document.getElementById('idSezione').value;
  const numero = document.getElementById('numero').value;
  const maschi = document.getElementById('maschi').value;
  const femmine = document.getElementById('femmine').value;
    const formData = new FormData();
    formData.append('funzione', 'salvaSezione');
    formData.append('numero', numero);
    formData.append('id_sez', id_sez);
    formData.append('id_sede', id_sede);
	formData.append('maschi', maschi);
    formData.append('femmine', femmine);
	formData.append('op', 'salva');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
        risultato.innerHTML = data; // Mostra la risposta del server
		const myForm = document.getElementById('sezioneForm');
		myForm.reset();
		document.getElementById ( "btnSalvaSezione" ).textContent = "Aggiungi sezione";
		document.getElementById('idSezione').value='';
		aggiornaNumero();
	})
};


  function deleteSezione(index) { 
  const id_sede = document.getElementById('idSede'+index).innerText;
  const id_sez = document.getElementById('idSezione'+index).innerText;
  const numero = document.getElementById('numero'+index).innerText;
  const maschi = document.getElementById('maschi'+index).innerText;
  const femmine = document.getElementById('femmine'+index).innerText;

    const formData = new FormData();
    formData.append('funzione', 'salvaSezione');
    formData.append('id_sede', id_sede);
    formData.append('id_sez', id_sez);
    formData.append('numero', numero);
	formData.append('maschi', maschi);
	formData.append('femmine', femmine);
	formData.append('op', 'cancella');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
        risultato.innerHTML = data; // Mostra la risposta del server
		document.getElementById ( "btnSalvaSezione" ).textContent = "Aggiungi sezione";
		aggiornaNumero();
    })


  }
  
   function editSezione(index) {
	 
	document.getElementById ( "idSezione" ).value = document.getElementById ( "idSezione"+index ).innerText
	document.getElementById ( "idSede" ).value = document.getElementById ( "idSede"+index ).innerText
	document.getElementById ( "maschi" ).value = document.getElementById ( "maschi"+index ).innerText
	document.getElementById ( "femmine" ).value = document.getElementById ( "femmine"+index ).innerText
	document.getElementById ( "numero" ).value = document.getElementById ( "numero"+index ).innerText
	document.getElementById ( "btnSalvaSezione" ).textContent = "Salva modifiche"
  }

	function aggiornaNumero() {
		var numsez = document.getElementById('maxNumero').innerText;
		document.getElementById('numero').value = numsez;
	}
</script>
