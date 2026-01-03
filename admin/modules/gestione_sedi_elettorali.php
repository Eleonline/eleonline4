<?php
require_once '../includes/check_access.php';
include('mappa_popup.php');
$circos=elenco_circoscrizioni();
?>
<input type="hidden" id="consultazioneAttiva" value="3">
<section class="content">
  <div class="container-fluid">
  <h2><i class="fas fa-map-marker-alt"></i> Gestione Sedi Elettorali</h2>
  <div class="card card-primary shadow-sm">
      <!-- HEADER CARD CON TITOLO + BOTTONE IMPORTA -->
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 id="titoloGestioneSedi" class="card-title">Aggiungi Sedi Elettorali</h3>

        <!-- Bottone Importa -->
        <button type="button"
        class="btn btn-warning btn-sm text-dark"
        data-toggle="modal"
        data-target="#importConsultaModal"
        data-toggle="tooltip"
        title="Attenzione: l'importazione può modificare le sedi esistenti">
  <i class="fas fa-exclamation-triangle me-1"></i>
  Importa da altra consultazione
</button>

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
			  <!-- popolare di nome comune e lat e lng-->	
              <input type="hidden" class="nome_comune" name="nome_comune" value="" >
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

<!-- =======================
     MODAL IMPORTA SEDI
     ======================= -->
<div class="modal fade" id="importConsultaModal" tabindex="-1" role="dialog" aria-labelledby="importConsultaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="importConsultaLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Importa sedi da altra consultazione
        </h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <!-- AVVISO -->
        <div class="alert alert-warning">
          <strong>ATTENZIONE</strong><br>
          L'importazione può sovrascrivere le sedi già presenti.
        </div>

        <form id="formImportSedi">

          <div class="row">
            <div class="col-md-6">
              <label>Consultazione di origine</label>
              <select class="form-control" id="consultazioneOrigine" required>
                <option value="">-- Seleziona consultazione --</option>
                <option value="1">Europee 2024</option>
                <option value="2">Regionali 2023</option>
              </select>
            </div>

            <div class="col-md-6">
              <label>Circoscrizione</label>
              <select class="form-control" id="circImport">
                <option value="">Tutte</option>
                <?php foreach($circos as $val){ ?>
                  <option value="<?= $val['id_circ'] ?>"><?= $val['descrizione'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <hr>

          <!-- CHECKBOX SOVRASCRIVI -->
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="sovrascrivi">
            <label class="form-check-label" for="sovrascrivi">
              Sovrascrivi sedi esistenti
            </label>
          </div>

          <!-- DOPPIA CONFERMA -->
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="confermaImport">
            <label class="form-check-label text-danger" for="confermaImport">
              Confermo di aver compreso i rischi dell'importazione
            </label>
          </div>

        </form>

        <div id="importResult" class="mt-3"></div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Annulla
        </button>
        <button type="button"
                class="btn btn-warning text-dark"
                id="btnImportaSedi"
                onclick="importaSedi()"
                disabled>
          <i class="fas fa-download me-1"></i>Importa
        </button>
      </div>

    </div>
  </div>
</div>

<script>
document.getElementById('confermaImport').addEventListener('change', function () {
  document.getElementById('btnImportaSedi').disabled = !this.checked;
});
</script>

<script>
function importaSedi() {

  const consultazioneOrigine = document.getElementById('consultazioneOrigine').value;
  const consultazioneAttiva = document.getElementById('consultazioneAttiva').value;
  const circ = document.getElementById('circImport').value;
  const sovrascrivi = document.getElementById('sovrascrivi').checked ? 1 : 0;
  const conferma = document.getElementById('confermaImport').checked;

  if (!consultazioneOrigine) {
    alert('Seleziona una consultazione di origine');
    return;
  }

  // ⛔ BLOCCO: stessa consultazione
  if (consultazioneOrigine === consultazioneAttiva) {
    alert('Non puoi importare dalla stessa consultazione attiva.');
    return;
  }

  // ⛔ BLOCCO: conferma non spuntata
  if (!conferma) {
    alert('Devi confermare di aver compreso i rischi.');
    return;
  }

  const formData = new FormData();
  formData.append('funzione', 'importaSedi');
  formData.append('id_consultazione_origine', consultazioneOrigine);
  formData.append('id_consultazione_dest', consultazioneAttiva);
  formData.append('id_circ', circ);
  formData.append('sovrascrivi', sovrascrivi);

  document.getElementById('importResult').innerHTML =
    '<div class="alert alert-info">Importazione in corso...</div>';

  fetch('../principale.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    document.getElementById('importResult').innerHTML =
      '<div class="alert alert-success">Importazione completata</div>';
    risultato.innerHTML = data;
    $('#importConsultaModal').modal('hide');
  })
  .catch(() => {
    document.getElementById('importResult').innerHTML =
      '<div class="alert alert-danger">Errore durante l’importazione</div>';
  });
}
</script>



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
//function apriMappaSingola(index) {
  //const s = sedi[index];
  //alert(`Apri mappa per:\n${s.indirizzo}\n(Circoscrizione: ${s.circoscrizione})`);
  // Qui puoi integrare il tuo modulo mappa con lat/lng
//}

// --- Gestione pulsante apri mappa nel form ---
// Dummy alert (sostituire con apertura popup mappa reale)
// document.querySelector('.btnApriMappaForm').addEventListener('click', () => {
  // alert("Apri mappa per inserimento/modifica sede (da implementare)");
// });
// --- Gestione pulsante apri mappa nel form  ---
document.querySelector('.btnApriMappaForm').addEventListener('click', () => {
  const container = document.querySelector('.rigaMappa');
  const indir = container.querySelector('.indir');
  const lat = container.querySelector('.lat');
  const lng = container.querySelector('.lng');
  const comuneInput = container.querySelector('.nome_comune');

  const comune = comuneInput ? comuneInput.value.trim() : "";
  if (!indir) return alert('Input indirizzo non trovato.');
  const query = indir.value.trim();
  if (!query) return alert("Inserisci un indirizzo o nome da cercare.");

  const fullQuery = comune ? `${query}, ${comune}` : query;
  currentInputs = { indir, lat, lng };

  if (maps_provider === 'google') {
    // Google Maps Geocoding API
    const apiKey = "LA_TUA_API_KEY_DAL_DB_O_CONFIG"; // già impostata in PHP
    fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(fullQuery)}&key=${apiKey}`)
        .then(res => res.json())
        .then(results => {
            if (!results.results || results.results.length === 0) return alert('Nessun risultato trovato per "' + fullQuery + '".');
            const location = results.results[0].geometry.location;
            apriMappa({ lat: location.lat, lon: location.lng });
        })
        .catch(() => alert('Errore durante la ricerca dell\'indirizzo con Google Maps.'));
} else {
    // Nominatim OSM
    fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(fullQuery)}`)
        .then(res => res.json())
        .then(results => {
            if (results.length === 0) return alert('Nessun risultato trovato per "' + fullQuery + '".');
            apriMappa(results[0]);
        })
        .catch(() => alert('Errore durante la ricerca dell\'indirizzo con OSM.'));
}

});


</script>
<?php
// da utilizzare sul file
// if ($_POST['funzione'] === 'importaSedi') {
    // $id_consultazione = $_POST['id_consultazione'];
    // $id_circ = $_POST['id_circ'];
    // $sovrascrivi = $_POST['sovrascrivi'];

    // 1️⃣ leggi sedi dalla consultazione origine
    // 2️⃣ eventuale DELETE se sovrascrivi = 1
    // 3️⃣ INSERT nelle sedi della consultazione attiva
    // 4️⃣ include elenco_sedi.php
//}
?>
