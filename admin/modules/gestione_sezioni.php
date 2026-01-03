<?php
require_once '../includes/check_access.php';

$inizioNoGenere = strtotime('2025/06/30');
$row = dati_consultazione(0);
$dataInizio = strtotime($row[0]['data_inizio']);
$sezioni = elenco_sezioni();
$row = elenco_sedi();
$maxNumero = count($sezioni) ? end($sezioni)['num_sez'] : 0;
$maxNumero++;
?>
<input type="hidden" id="consultazioneAttiva" value="1">

<section class="content">
  <div class="container-fluid">
    <h2><i class="fas fa-map-marker-alt"></i> Gestione Sezioni</h2>

    <!-- FORM -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
  <h3 class="card-title" id="form-title">Aggiungi Sezione</h3>

  <button type="button"
          class="btn btn-warning btn-sm text-dark"
          data-toggle="modal"
          data-target="#importSezioniModal">
    <i class="fas fa-exclamation-triangle me-1"></i>
    Importa sezioni
  </button>
</div>

      <div class="card-body">
        <form id="sezioneForm" onsubmit="aggiungiSezione(event)">
          <input type="hidden" id="idSezione" value="">
          <div class="form-row">
            <div class="form-group col-md-2">
              <label>Numero Sezione*</label>
              <input type="number" id="numero" class="form-control" required min="1" value="<?= $maxNumero ?>">
            </div>
            <div class="form-group col-md-4">
              <label>Indirizzo (Sede)*</label>
              <select id="idSede" class="form-control">
                <?php foreach($row as $val) { ?>
                  <option value="<?= $val['id_sede'] ?>"><?= $val['indirizzo'] ?></option>
                <?php } ?>
              </select>
            </div>
            <?php $nascondi = ($inizioNoGenere > $dataInizio) ? '' : 'style="display:none;"'; ?>
            <div class="form-group col-md-2">
              <label><?= ($inizioNoGenere > $dataInizio) ? "Maschi" : "Iscritti"; ?></label>
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
          </div>

          <!-- BOTTONI -->
          <div class="row mt-2">
            <div class="col-md-2">
              <button type="submit" class="btn btn-success w-100" id="btnSalvaSezione">Aggiungi sezione</button>
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-secondary w-100" onclick="resetFormSezione()">Annulla</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- LISTA SEZIONI -->
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
              <?php $span = ($inizioNoGenere>$dataInizio) ? 'colspan="3"' : 'colspan="5"'; ?>
              <?php if($inizioNoGenere>$dataInizio) { ?>
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
      </div>
    </div>
  </div>
</section>
<!-- Modal conferma eliminazione sezione -->
<div class="modal fade" id="confirmDeleteSezioneModal" tabindex="-1" aria-labelledby="confirmDeleteSezioneLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteSezioneLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Conferma eliminazione
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        Sei sicuro di voler eliminare la sezione <strong id="deleteSezioneNumero"></strong>? Questa azione non può essere annullata.
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times me-1"></i>Annulla
        </button>
        <button type="button" class="btn btn-danger" id="confirmDeleteSezioneBtn">
          <i class="fas fa-trash me-1"></i>Elimina
        </button>
      </div>

    </div>
  </div>
</div>
<!-- =======================
     MODAL IMPORTA SEZIONI
     ======================= -->
<div class="modal fade" id="importSezioniModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Importa sezioni da altra consultazione
        </h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <div class="alert alert-warning">
          <strong>ATTENZIONE</strong><br>
          L'importazione può sovrascrivere le sezioni già presenti.
        </div>

        <form id="formImportSezioni">

          <div class="form-group">
            <label>Consultazione di origine</label>
            <select class="form-control" id="consultazioneOrigineSez">
              <option value="">-- Seleziona consultazione --</option>
              <option value="1">Europee 2024</option>
              <option value="2">Regionali 2023</option>
            </select>
          </div>

          <hr>

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="sovrascriviSezioni">
            <label class="form-check-label">
              Sovrascrivi sezioni esistenti
            </label>
          </div>

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="importaTotale">
            <label class="form-check-label">
              Importa anche il totale iscritti
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="confermaImportSezioni">
            <label class="form-check-label text-danger">
              Confermo di aver compreso i rischi dell'importazione
            </label>
          </div>

        </form>

        <div id="importSezioniResult" class="mt-3"></div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Annulla
        </button>
        <button type="button"
                class="btn btn-warning text-dark"
                id="btnImportaSezioni"
                onclick="importaSezioni()"
                disabled>
          <i class="fas fa-download me-1"></i>
          Importa sezioni
        </button>
      </div>

    </div>
  </div>
</div>

<script>
document.getElementById('confermaImportSezioni').addEventListener('change', function () {
  document.getElementById('btnImportaSezioni').disabled = !this.checked;
});
</script>
<script>
function importaSezioni() {

  const origine = document.getElementById('consultazioneOrigineSez').value;
  const attiva = document.getElementById('consultazioneAttiva').value;
  const sovrascrivi = document.getElementById('sovrascriviSezioni').checked ? 1 : 0;
  const importaTotale = document.getElementById('importaTotale').checked ? 1 : 0;
  const conferma = document.getElementById('confermaImportSezioni').checked;

  if (!origine) {
    alert('Seleziona una consultazione di origine');
    return;
  }

  if (origine === attiva) {
    alert('Non puoi importare dalla stessa consultazione attiva');
    return;
  }

  if (!conferma) {
    alert('Devi confermare di aver compreso i rischi');
    return;
  }

  const formData = new FormData();
  formData.append('funzione', 'importaSezioni');
  formData.append('id_consultazione_origine', origine);
  formData.append('id_consultazione_dest', attiva);
  formData.append('sovrascrivi', sovrascrivi);
  formData.append('importa_totale', importaTotale); // NUOVO PARAMETRO

  document.getElementById('importSezioniResult').innerHTML =
    '<div class="alert alert-info">Importazione in corso...</div>';

  fetch('../principale.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    document.getElementById('importSezioniResult').innerHTML =
      '<div class="alert alert-success">Importazione completata</div>';
    document.getElementById('risultato').innerHTML = data;
    $('#importSezioniModal').modal('hide');
    aggiornaNumero();
  })
  .catch(() => {
    document.getElementById('importSezioniResult').innerHTML =
      '<div class="alert alert-danger">Errore durante l’importazione</div>';
  });
}

</script>

<script>
function aggiungiSezione(e) {
    e.preventDefault();
	let controllo = controlloSomma();
	if ( controllo === 1 ) {
		alert("La somma tra iscritti maschi e femmine non corrisponde al totale");
		return
	}
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

    fetch('../principale.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            document.getElementById('risultato').innerHTML = data;
            resetFormSezione();
            aggiornaNumero();
        });
}

let deleteIndexSezione = null;

function confermaEliminaSezione(index) {
    deleteIndexSezione = index;
    const numero = document.getElementById('numero'+index).innerText;
    document.getElementById('deleteSezioneNumero').textContent = numero;
    $('#confirmDeleteSezioneModal').modal('show');
}

document.getElementById('confirmDeleteSezioneBtn').addEventListener('click', () => {
    if(deleteIndexSezione !== null){
        deleteSezione(deleteIndexSezione); // chiama la funzione esistente
        deleteIndexSezione = null;
        $('#confirmDeleteSezioneModal').modal('hide');
    }
});

 function deleteSezione(index) {
	const id_sezione = document.getElementById('idSezione'+index).innerText;
    const numero = document.getElementById('numero'+index).innerText;
    const formData = new FormData();
    formData.append('funzione', 'salvaSezione');
    formData.append('id_sez', id_sezione);
	formData.append('numero', numero);
	formData.append('op', 'cancella');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
        document.getElementById('risultato').innerHTML = data; // Mostra la risposta del server
		document.getElementById ( "btnSalvaSezione" ).textContent = "Aggiungi";
		aggiornaNumero();
    })


  }
function editSezione(index) {
    document.getElementById("idSezione").value = document.getElementById("idSezione"+index).innerText;
    document.getElementById("idSede").value = document.getElementById("idSede"+index).innerText;
    document.getElementById("maschi").value = document.getElementById("maschi"+index).innerText;
    document.getElementById("femmine").value = document.getElementById("femmine"+index).innerText;
    document.getElementById("totale").value = document.getElementById("totale"+index).innerText;
    document.getElementById("numero").value = document.getElementById("numero"+index).innerText;
    document.getElementById("btnSalvaSezione").textContent = "Salva modifiche";
	document.getElementById("numero").focus();
	
}

function resetFormSezione() {
    const form = document.getElementById('sezioneForm');
    form.reset();
    document.getElementById('idSezione').value = '';
    document.getElementById('btnSalvaSezione').textContent = "Aggiungi sezione";
}

function aggiornaNumero() {
//    const sezioni = document.querySelectorAll('#sezioniTable tbody tr');
//    const maxNum = sezioni.length ? Math.max(...Array.from(sezioni).map(r => parseInt(r.querySelector('.numero')?.innerText || 0))) : 0;
	const maxNum = document.getElementById("maxNumero").innerText;
    document.getElementById('numero').value = maxNum;
}

function controlloSomma() {
	let maschi = Number(document.getElementById("maschi").value);
	let femmine = Number(document.getElementById("femmine").value);
	let totale = Number(document.getElementById("totale").value);
	let somma = maschi + femmine;
	if ( somma=== totale || totale===0)
		return 0;
	else
		return 1;
}
</script>
