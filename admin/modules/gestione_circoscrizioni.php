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
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title" id="titoloGestioneCircoscrizioni"><i class="fas fa-flag me-2"></i>Gestione Circoscrizioni</h3>
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

<script>

function aggiungiCircoscrizione(e) {
    e.preventDefault();

    const id_circ = document.getElementById("id_circ").value;
    const numero = document.getElementById("numero").value;
    const denominazione = document.getElementById("denominazione").value.trim();
    const btn = document.getElementById("btnAggiungi");

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
	})

};


  function deleteCircoscrizione(index) {
	const denominazione = document.getElementById ( "denominazione"+index ).innerText
	const numero = document.getElementById ( "numero"+index ).innerText
	const id_circ = document.getElementById ( "id_circ"+index ).innerText
    const formData = new FormData();
    formData.append('funzione', 'salvaCircoscrizione');
    formData.append('descrizione', denominazione);
    formData.append('numero', numero);
    formData.append('id_circ', id_circ);
    formData.append('op', 'cancella');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
        document.getElementById('risultato').innerHTML = data;
		document.getElementById ( "btnAggiungi" ).textContent = "Aggiungi";
		resetFormCircoscrizione();
		aggiornaNumero();
    })


  }
  
function annullaModifica() {
    // Reset del form
    const myForm = document.getElementById('circoscrizioneForm');
    myForm.reset();

    // Ripulisci l'id nascosto
    document.getElementById('id_circ').value = '';

    // Ripristina il testo del bottone principale
    document.getElementById('btnAggiungi').textContent = "Aggiungi";

    // Nascondi il bottone Annulla
    document.getElementById('btnAnnulla').classList.add('d-none');
}

// Aggiornamento funzione editCircoscrizione
function editCircoscrizione(index) {
    document.getElementById("denominazione").value = document.getElementById("denominazione"+index).innerText;
    document.getElementById("numero").value = document.getElementById("numero"+index).innerText;
    document.getElementById("id_circ").value = document.getElementById("id_circ"+index).innerText;
    document.getElementById("btnAggiungi").textContent = "Salva modifiche";

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

function aggiornaNumero() {

	const maxNum = document.getElementById("maxNumero").innerText;
    document.getElementById('numero').value = maxNum;
}
</script>
