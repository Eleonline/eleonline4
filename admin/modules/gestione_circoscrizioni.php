<?php
require_once '../includes/check_access.php';
$row = elenco_circoscrizioni();
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_circ'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
?>

<section class="content">
  <div class="container-fluid">
  <h2><i class="fas fa-flag"></i> Gestione Circoscrizioni</h2>
    <div class="card card-primary shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title" id="titoloGestioneCircoscrizioni">Aggiungi Circoscrizioni</h3>

    <!-- Bottone Importa -->
    <button type="button"
            class="btn btn-warning btn-sm text-dark"
            data-toggle="modal"
            data-target="#importCircModal"
            data-toggle="tooltip"
            title="Attenzione: l'importazione può modificare le circoscrizioni esistenti">
      <i class="fas fa-exclamation-triangle me-1"></i>
      Importa da altra consultazione
    </button>
</div>


      <div class="card-body table-responsive" style="max-height:400px; overflow-y:auto;">
        <form id="circoscrizioneForm" class="mb-3" onsubmit="aggiungiCircoscrizione(event)">
          <div class="row">
            <div class="col-md-2" style="display:none">
              <label for="id_circ"></label>
              <input type="number" class="form-control" id="id_circ">
            </div>
            <div class="col-md-2">
              <label for="numero">Numero</label>
              <input type="number" class="form-control" id="numero" min="1" value="<?= $maxNumero; ?>" required>
            </div>
            <div class="col-md-7">
              <label for="denominazione">Denominazione</label>
              <input type="text" class="form-control" id="denominazione" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-50 me-2" id="btnAggiungi">Aggiungi</button>
              <button type="button" class="btn btn-secondary w-50 d-none" id="btnAnnulla" onclick="annullaModifica()">Annulla</button>
            </div>
          </div>
        </form>
      </div>

      <div class="card shadow-sm mb-3">
        <div class="card-header bg-secondary text-white">
          <h3 class="card-title">Lista Circoscrizioni</h3>
        </div>
        <div class="card-body table-responsive" style="overflow-y:auto; border: 1px solid #dee2e6; border-radius: 0 0 0.25rem 0.25rem;">
          <table class="table table-striped mb-0" id="tabellaCircoscrizioni">
            <thead>
              <tr>
                <th style="width: 10%;">Numero</th>
                <th>Denominazione</th>
                <th style="width: 20%;">Azioni</th>
              </tr>
            </thead>
            <tbody id="risultato">
              <?php include("elenco_circoscrizioni.php"); ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer text-muted">
        Puoi gestire le circoscrizioni elettorali da qui.
      </div>
    </div>
  </div>
</section>

<!-- Modal conferma eliminazione circoscrizione -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Conferma eliminazione
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        Sei sicuro di voler eliminare la circoscrizione <strong id="deleteCircoscrizione"></strong>? Questa azione non può essere annullata.
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times me-1"></i>Annulla
        </button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
          <i class="fas fa-trash me-1"></i>Elimina
        </button>
      </div>

    </div>
  </div>
</div>
<!-- =======================
     MODAL IMPORTA CIRCOSCRIZIONI
     ======================= -->
<div class="modal fade" id="importCircModal" tabindex="-1" role="dialog" aria-labelledby="importCircLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="importCircLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Importa circoscrizioni da altra consultazione
        </h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <div class="alert alert-warning">
          <strong>ATTENZIONE</strong><br>
          L'importazione sovrascrive i dati già inseriti per circoscrizioni, sedi e sezioni.
        </div>

        <form id="formImportCirc">
          <div class="row">
            <div class="col-md-6">
              <label>Consultazione di origine</label>
              <select class="form-control" id="consultazioneOrigineCirc" required>
                <option value="">-- Seleziona consultazione --</option>
			  <?php $row=elenco_cons();
			  foreach($row as $key=>$val) { if($val['id_cons_gen']==$id_cons_gen) continue;?>
                <option value="<?= $val['id_cons_gen'] ?>"><?= $val['descrizione'] ?></option>
			  <?php } ?>
              </select>
            </div>
          </div>

          <hr>

          <!-- CHECKBOX SOVRASCRIVI -- >
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="sovrascriviCirc">
            <label class="form-check-label" for="sovrascriviCirc">
              Sovrascrivi circoscrizioni esistenti
            </label>
          </div -->

          <!-- DOPPIA CONFERMA -->
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="confermaImportCirc">
            <label class="form-check-label text-danger" for="confermaImportCirc">
              Confermo di aver compreso i rischi dell'importazione
            </label>
          </div>
        </form>

        <div id="importResultCirc" class="mt-3"></div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
        <button type="button"
                class="btn btn-warning text-dark"
                id="btnImportaCirc"
                onclick="importaCircoscrizioni()"
                disabled>
          <i class="fas fa-download me-1"></i>Importa
        </button>
      </div>

    </div>
  </div>
</div>


<script>
// Abilita il bottone solo se conferma spuntata
document.getElementById('confermaImportCirc').addEventListener('change', function () {
  document.getElementById('btnImportaCirc').disabled = !this.checked;
});

function importaCircoscrizioni() {

  const consultazioneOrigine = document.getElementById('consultazioneOrigineCirc').value;
//  const sovrascrivi = document.getElementById('sovrascriviCirc').checked ? 1 : 0;
  const conferma = document.getElementById('confermaImportCirc').checked;
  const consultazioneAttiva = <?= $id_cons_gen ?>; 

  if (!consultazioneOrigine) {
    alert('Seleziona una consultazione di origine');
    return;
  }
// ⛔ BLOCCO: stessa consultazione
  if (consultazioneOrigine === consultazioneAttiva.toString()) {
    alert('Non puoi importare dalla stessa consultazione attiva.');
    return;
  }
// ⛔ BLOCCO: conferma non spuntata
  if (!conferma) {
    alert('Devi confermare di aver compreso i rischi.');
    return;
  }

  const formData = new FormData();
  formData.append('funzione', 'importaCircoscrizioni');
  formData.append('id_consultazione_origine', consultazioneOrigine);
  formData.append('id_consultazione_dest', consultazioneAttiva);
//  formData.append('sovrascrivi', sovrascrivi);

  document.getElementById('importResultCirc').innerHTML =
    '<div class="alert alert-info">Importazione in corso...</div>';

  fetch('../principale.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    document.getElementById('importResultCirc').innerHTML =
      '<div class="alert alert-success">Importazione completata</div>';
    document.getElementById('risultato').innerHTML = data; // Aggiorna tabella circoscrizioni
    $('#importCircModal').modal('hide');
  })
  .catch(() => {
    document.getElementById('importResultCirc').innerHTML =
      '<div class="alert alert-danger">Errore durante l’importazione</div>';
  });
}

function aggiungiCircoscrizione(e) {
    e.preventDefault();

    const id_circ = document.getElementById("id_circ").value;
    const numero = document.getElementById("numero").value;
    const denominazione = document.getElementById("denominazione").value.trim();
    const btn = document.getElementById("btnAggiungi");
    
	// ✅ Controllo duplicati
    if (numeroCircoscrizioneDuplicato(numero, id_circ)) {
		alert("ATTENZIONE: il numero di circoscrizione " + numero + " è già assegnato!");
        document.getElementById("numero").focus();
        return;
    }
	
    const formData = new FormData();
    formData.append('funzione', 'salvaCircoscrizione');
    formData.append('descrizione', denominazione);
    formData.append('numero', numero);
    formData.append('id_circ', id_circ);
    formData.append('op', 'salva');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
    document.getElementById('risultato').innerHTML = data;
    resetFormCircoscrizione();
    aggiornaNumero();

    document.getElementById('titoloGestioneCircoscrizioni').textContent = "Aggiungi Circoscrizioni";
})


};



function deleteCircoscrizione(index) {
    const denominazione = document.getElementById("denominazione"+index).innerText;
    const numero = document.getElementById("numero"+index).innerText;
    deleteIdCirc = document.getElementById("id_circ"+index).innerText;

    document.getElementById("deleteCircoscrizione").textContent = numero + " - " + denominazione;

    $('#confirmDeleteModal').modal('show'); // apri il modal
}

// Conferma cancellazione
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if(deleteIdCirc) {
        const formData = new FormData();
        formData.append('funzione', 'salvaCircoscrizione');
        formData.append('id_circ', deleteIdCirc);
        formData.append('op', 'cancella');

        fetch('../principale.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('risultato').innerHTML = data;
            $('#confirmDeleteModal').modal('hide');
            deleteIdCirc = null;
            resetFormCircoscrizione();
            aggiornaNumero();
        });
    }
});

  
function annullaModifica() {
    // Reset del form
    const myForm = document.getElementById('circoscrizioneForm');
    myForm.reset();

    // Ripulisci l'id nascosto
    document.getElementById('id_circ').value = '';

    // Ripristina il testo del bottone principale
    document.getElementById('btnAggiungi').textContent = "Aggiungi";
	document.getElementById('titoloGestioneCircoscrizioni').textContent = "Aggiungi Circoscrizioni";

    // Nascondi il bottone Annulla
    document.getElementById('btnAnnulla').classList.add('d-none');
}

// Aggiornamento funzione editCircoscrizione
function editCircoscrizione(index) {
    document.getElementById("denominazione").value = document.getElementById("denominazione"+index).innerText;
    document.getElementById("numero").value = document.getElementById("numero"+index).innerText;
    document.getElementById("id_circ").value = document.getElementById("id_circ"+index).innerText;
    document.getElementById("btnAggiungi").textContent = "Salva modifiche";
	document.getElementById("titoloGestioneCircoscrizioni").textContent = "Modifica Circoscrizione";


    // Mostra il bottone Annulla
    document.getElementById("btnAnnulla").classList.remove('d-none');
}

function scrollToGestioneCircoscrizioni() {
    const target = document.getElementById('titoloGestioneCircoscrizioni');
    if (target) {
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

function resetFormCircoscrizione() {
    const form = document.getElementById('circoscrizioneForm');
    form.reset();
    document.getElementById('id_circ').value = '';
    document.getElementById('btnAggiungi').textContent = "Aggiungi";
}

// function aggiornaNumero() {

	// const maxNum = document.getElementById("maxNumero").innerText;
    // document.getElementById('numero').value = maxNum;
// }
function aggiornaNumero() {
    const numeri = Array.from(document.querySelectorAll('#risultato tr td[id^="numero"]'))
        .map(td => parseInt(td.textContent))
        .filter(n => !isNaN(n));

    let numero = 1;
    while (numeri.includes(numero)) numero++;
    document.getElementById('numero').value = numero;
}

function numeroCircoscrizioneDuplicato(numeroInserito, idCircEditing) {
    const righe = document.querySelectorAll('#risultato tr');

    for (let riga of righe) {
        const tdNumero = riga.querySelector('td[id^="numero"]');
        const tdIdCirc = riga.querySelector('div[id^="id_circ"]');

        // se manca il numero → SALTA
        if (!tdNumero || !tdIdCirc) continue;

        const numeroTabella = tdNumero.textContent.trim();
        const idCircTabella = tdIdCirc.textContent.trim();

        // se sto modificando la stessa → ok
        if (idCircEditing && idCircEditing === idCircTabella) continue;

        if (numeroTabella === numeroInserito) {
            return true; // duplicato trovato
        }
    }

    return false; // numero libero
}

</script>
<?php
// if ($_POST['funzione'] === 'importaCircoscrizioni') {
    // $id_consultazione_orig = $_POST['id_consultazione_orig'];
    // $id_consultazione_dest = $_POST['id_consultazione_dest'];
    // $sovrascrivi = $_POST['sovrascrivi'];

    //1️⃣ Leggi tutte le circoscrizioni, sedi e sezioni dalla consultazione origine
    // $circos = getCircoscrizioniByConsultazione($id_consultazione_orig); // funzione da implementare

    //2️⃣ Se sovrascrivi = 1, cancella dati esistenti della consultazione dest
    // if ($sovrascrivi) {
        // cancellaCircoscrizioniConsultazione($id_consultazione_dest); // funzione da implementare
    // }

    //3️⃣ Inserisci circoscrizioni + sedi + sezioni nella consultazione attiva
    // foreach ($circos as $c) {
        // $id_new = inserisciCircoscrizione($id_consultazione_dest, $c); // funzione da implementare
        // foreach ($c['sedi'] as $sede) {
            // $id_sede = inserisciSede($id_new, $sede);
            // foreach ($sede['sezioni'] as $sezione) {
                // inserisciSezione($id_sede, $sezione);
            // }
        // }
    // }

    //4️⃣ Restituisci l’elenco aggiornato (puoi includere il file elenco_circoscrizioni.php)
    // include('elenco_circoscrizioni.php');
    // exit;
// }
?>