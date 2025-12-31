<?php
require_once '../includes/check_access.php';

$circos=elenco_circoscrizioni();
?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
       <h3 id="titoloGestioneSedi" class="card-title"><i class="fas fa-map-marker-alt me-2"></i>Gestione Sedi Elettorali</h3>

      </div>

      <div class="card-body">
        <form id="formSede" class="mb-3" onsubmit="aggiungiSede(event)">
          <input type="hidden" id="idSede" value=""> 
          <div class="row mb-2">
            <div class="col-md-4">
              <label>Circoscrizione</label>
              <select id="idCirc" class="form-control">
				<?php foreach($circos as $key=>$val) { ?>
                <option value="<?= $val['id_circ'] ?>"><?= $val['descrizione'] ?></option>
				<?php } ?>
              </select>
            </div>
            <div class="col-md-8 rigaMappa">
              <label>Indirizzo</label>
              <div class="input-group">
                <input type="text" id="indirizzo" name="indirizzo" class="form-control indir" required>
                <button type="button" class="btn btn-outline-secondary btnApriMappa btnApriMappaForm">
                  <i class="fas fa-map-pin me-2"></i>Apri mappa
                </button>
              </div>

              <input type="hidden" class="nome_comune" name="nome_comune" value="Capo d'Orlando" >
              <input type="hidden" class="lat" id="lat" name="lat" value="" >
              <input type="hidden" class="lng" id="lng" name="lng" value="" >
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <label>Telefono</label>
              <input type="text" id="telefono" class="form-control">
            </div>
            <div class="col-md-3">
              <label>Fax</label>
              <input type="text" id="fax" class="form-control">
            </div>
            <div class="col-md-4">
              <label>Responsabile</label>
              <input type="text" id="responsabile" class="form-control">
            </div>
          </div>
		  <div class="row mt-2">
		  <div class="col-md-2">
			<button type="submit" class="btn btn-primary w-100" id="btnSalvaSede">Aggiungi</button>
		  </div>
		  <div class="col-md-2">
			<button type="reset" class="btn btn-secondary w-100" id="btnResetSede" onclick="resetFormSede()">Annulla</button>
		  </div>
		</div>
        </form>
      </div>
<div class="card shadow-sm mb-3">
  <div class="card-header bg-secondary text-white">
    <h3 class="card-title">Lista Sedi</h3>
  </div>
  <div class="card-body table-responsive" style="max-height:450px; overflow-y:auto; border: 1px solid #dee2e6; border-radius: 0 0 0.25rem 0.25rem;">
    <table class="table table-striped mb-0" id="tabellaSedi">
      <thead>
        <tr>
          <th>Circoscrizione</th>
          <th>Indirizzo</th>
          <th>Mappa</th>
          <th>Telefono</th>
          <th>Fax</th>
          <th>Responsabile</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody id="risultato">
        <?php include('elenco_sedi.php'); ?>
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    <nav>
      <ul class="pagination justify-content-center mb-0" id="pagination"></ul>
    </nav>
  </div>
</div>

      <div class="card-footer text-muted">
        Puoi gestire qui tutte le sedi elettorali collegate alle circoscrizioni.
      </div>
    </div>
  </div>
</section>
<!-- Modal conferma eliminazione sede -->
<div class="modal fade" id="confirmDeleteSedeModal" tabindex="-1" aria-labelledby="confirmDeleteSedeLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteSedeLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Conferma eliminazione
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        Sei sicuro di voler eliminare la sede <strong id="deleteSedeIndirizzo"></strong>? Questa azione non può essere annullata.
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times me-1"></i>Annulla
        </button>
        <button type="button" class="btn btn-danger" id="confirmDeleteSedeBtn">
          <i class="fas fa-trash me-1"></i>Elimina
        </button>
      </div>

    </div>
  </div>
</div>

<script>
let deleteIdSede = null;
let deleteIndexSede = null;

function confermaEliminaSede(index) {
    deleteIndexSede = index;
    const indirizzo = document.getElementById('indirizzo'+index).innerText.trim();
    deleteIdSede = document.getElementById('idSede'+index).innerText;
    document.getElementById('deleteSedeIndirizzo').textContent = indirizzo;
    $('#confirmDeleteSedeModal').modal('show');
}

document.getElementById('confirmDeleteSedeBtn').addEventListener('click', () => {
    if(deleteIdSede !== null && deleteIndexSede !== null){
        deleteSede(deleteIndexSede); // usa la tua funzione esistente
        deleteIdSede = null;
        deleteIndexSede = null;
        $('#confirmDeleteSedeModal').modal('hide');
    }
});
</script>

<script>
function aggiungiSede(e) {
    e.preventDefault();

  const id_circ = document.getElementById('idCirc').value;
  const id_sede = document.getElementById('idSede').value;
  const indirizzo = document.getElementById('indirizzo').value.trim();
  const telefono = document.getElementById('telefono').value.trim();
  const fax = document.getElementById('fax').value.trim();
  const lat = document.getElementById('lat').value.trim();
  const lng = document.getElementById('lng').value.trim();
  const responsabile = document.getElementById('responsabile').value.trim();

  if (!indirizzo) {
    alert("L'Indirizzo è obbligatorio.");
    return;
  }
    const btn = document.getElementById("btnAggiungi");

    const formData = new FormData();
    formData.append('funzione', 'salvaSede');
    formData.append('indirizzo', indirizzo);
    formData.append('telefono', telefono);
    formData.append('id_circ', id_circ);
    formData.append('id_sede', id_sede);
	formData.append('fax', fax);
    formData.append('responsabile', responsabile);
	formData.append('op', 'salva');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
        risultato.innerHTML = data; // Mostra la risposta del server
		const myForm = document.getElementById('formSede');
		myForm.reset();
		document.getElementById ( "btnSalvaSede" ).textContent = "Aggiungi";
		document.getElementById('idSede').value='';
    })

};


  function deleteSede(index) { 
	const id_circ = document.getElementById('idCirc'+index).innerText;
	const id_sede = document.getElementById('idSede'+index).innerText;
	const indirizzo = document.getElementById('indirizzo'+index).innerText.trim();
	const telefono = document.getElementById('telefono'+index).innerText;
	const fax = document.getElementById('fax'+index).innerText;
	const responsabile = document.getElementById('responsabile'+index).innerText.trim();
    const formData = new FormData();
    formData.append('funzione', 'salvaSede');
    formData.append('descrizione', indirizzo);
    formData.append('telefono', telefono);
    formData.append('id_circ', id_circ);
    formData.append('id_sede', id_sede);
	formData.append('fax', fax);
	formData.append('latitudine', lat);
	formData.append('longitudine', lng);
    formData.append('responsabile', responsabile);
	formData.append('op', 'cancella');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
        risultato.innerHTML = data; // Mostra la risposta del server
		document.getElementById ( "btnSalvaSede" ).textContent = "Aggiungi";
    })


  }
  
 function resetFormSede() {
    const myForm = document.getElementById('formSede');
    myForm.reset();
    document.getElementById('idSede').value = '';
    document.getElementById('btnSalvaSede').textContent = "Aggiungi";
}

   function editSede(index) { 
	document.getElementById ( "idCirc" ).value = document.getElementById ( "idCirc"+index ).innerText
	document.getElementById ( "idSede" ).value = document.getElementById ( "idSede"+index ).innerText
	document.getElementById ( "indirizzo" ).value = document.getElementById ( "indirizzo"+index ).innerText
	document.getElementById ( "telefono" ).value = document.getElementById ( "telefono"+index ).innerText
	document.getElementById ( "fax" ).value = document.getElementById ( "fax"+index ).innerText
	document.getElementById ( "lng" ).value = document.getElementById ( "lng"+index ).innerText
	document.getElementById ( "lat" ).value = document.getElementById ( "lat"+index ).innerText
	document.getElementById ( "responsabile" ).value = document.getElementById ( "responsabile"+index ).innerText
	document.getElementById ( "btnSalvaSede" ).textContent = "Salva modifiche"
  }


// Funzione per aprire popup mappa per la singola sede (dummy)
function apriMappaSingola(index) {
  const s = sedi[index];
  alert(`Apri mappa per:\n${s.indirizzo}\n(Circoscrizione: ${s.circoscrizione})`);
  // Qui puoi integrare il tuo modulo mappa con lat/lng
}

// --- Gestione pulsante apri mappa nel form ---
// Dummy alert (sostituire con apertura popup mappa reale)
document.querySelector('.btnApriMappaForm').addEventListener('click', () => {
  alert("Apri mappa per inserimento/modifica sede (da implementare)");
});
</script>
