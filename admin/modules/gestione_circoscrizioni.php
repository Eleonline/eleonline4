<?php
require_once '../includes/check_access.php';

// Dati fittizi PHP
$circoscrizioni = [
    ['numero' => '1', 'denominazione' => 'Centro città'],
    ['numero' => '2', 'denominazione' => 'Nord'],
    ['numero' => '3', 'denominazione' => 'Sud'],
    ['numero' => '4', 'denominazione' => 'Est'],
    ['numero' => '5', 'denominazione' => 'Ovest'],
];
?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-flag me-2"></i>Gestione Circoscrizioni</h3>
      </div>

      <div class="card-body table-responsive" style="max-height:400px; overflow-y:auto;">
        <form id="formCircoscrizione" class="mb-3" onsubmit="aggiungiCircoscrizione(event)">
          <div class="row">
            <div class="col-md-2" style="display:none">
              <label for="id_circ"></label>
              <input type="number" class="form-control" id="id_circ">
            </div>
            <div class="col-md-2">
              <label for="numero">Numero</label>
              <input type="number" class="form-control" id="numero" required>
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
        risultato.innerHTML = data; // Mostra la risposta del server
		const myForm = document.getElementById('formCircoscrizione');
		myForm.reset();
		document.getElementById ( "btnAggiungi" ).textContent = "Aggiungi";
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
        risultato.innerHTML = data; // Mostra la risposta del server
		document.getElementById ( "btnAggiungi" ).textContent = "Aggiungi";
    })


  }
  
   function editCircoscrizione(index) {
	document.getElementById ( "denominazione" ).value = document.getElementById ( "denominazione"+index ).innerText
	document.getElementById ( "numero" ).value = document.getElementById ( "numero"+index ).innerText
	document.getElementById ( "id_circ" ).value = document.getElementById ( "id_circ"+index ).innerText
	document.getElementById ( "btnAggiungi" ).textContent = "Salva modifiche"
//	document.getElementById("riga"+index).style.display = 'none' 
  }

</script>
