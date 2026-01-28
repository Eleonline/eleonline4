<?php
require_once '../includes/check_access.php';
$id_cons_gen=$_SESSION['id_cons_gen'];
$row=dati_consultazione(0);
$dataInizio=$row[0]['data_inizio'];
$dataFine=$row[0]['data_fine'];
// Connessione al DB (commentata)
// $conn = new mysqli("localhost", "user", "password", "nome_database");
// if ($conn->connect_error) {
//   die("Connessione fallita: " . $conn->connect_error);
// }

// Caricamento dati dal DB (commentato)
// $affluenze = [];
// $result = $conn->query("SELECT data, ora, minuto FROM affluenze ORDER BY data DESC, ora DESC, minuto DESC");
// while ($row = $result->fetch_assoc()) {
//   $affluenze[] = $row;
// }
// $datiJson = json_encode($affluenze);
?>

<section class="content">
  <div class="container-fluid">

    <h2><i class="fas fa-clock"></i> Gestione Orari di Affluenza</h2>

    <!-- ========================= -->
    <!-- CARD FORM -->
    <!-- ========================= -->
    <div class="card card-primary shadow-sm mb-3">
      <div class="card-header">
        <h3 class="card-title">Aggiungi Orario di Affluenza</h3>
      </div>

      <div class="card-body">
        <form id="affluenzaForm" onsubmit="aggiungiAffluenza(event)">
          <div class="row align-items-end">

            <div class="col-12 col-md-3">
              <label>Data</label>
              <input type="date" id="data" class="form-control"
                     min="<?= $dataInizio ?>" max="<?= $dataFine ?>"
                     value="<?= $dataInizio ?>" required>
            </div>

            <div class="col-6 col-md-2">
              <label>Ora</label>
              <select id="ora" class="form-control" required>
                <?php for ($i = 0; $i < 24; $i++): ?>
                  <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>">
                    <?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="col-6 col-md-2">
              <label>Minuti</label>
              <select id="minuto" class="form-control" required>
                <?php foreach ([0, 15, 30, 45] as $m): ?>
                  <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>">
                    <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12 col-md-2">
              <button type="submit" class="btn btn-success w-100 mt-2 mt-md-0">
                Aggiungi
              </button>
            </div>

          </div>
        </form>
      </div>
    </div>

    <!-- ========================= -->
    <!-- CARD LISTA -->
    <!-- ========================= -->
    <div class="card shadow-sm">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Orari Inseriti</h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover mb-0">
          <thead>
            <tr>
              <th>Data</th>
              <th>Orario</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody id="risultato">
            <?php include('elenco_rilevazioni.php'); ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>

<!-- Modal conferma eliminazione affluenza -->
<div class="modal fade" id="confirmDeleteAffluenzaModal" tabindex="-1" aria-labelledby="confirmDeleteAffluenzaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteAffluenzaLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Conferma eliminazione
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        Sei sicuro di voler eliminare l'orario di affluenza <strong id="deleteAffluenzaInfo"></strong>? Questa azione non può essere annullata.
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times me-1"></i>Annulla
        </button>
        <button type="button" class="btn btn-danger" id="confirmDeleteAffluenzaBtn">
          <i class="fas fa-trash me-1"></i>Elimina
        </button>
      </div>

    </div>
  </div>
</div>

<script>

  function aggiungiAffluenza(e) {
    e.preventDefault();
    const ora = document.getElementById('ora').value;
    const minuto = document.getElementById('minuto').value;
    const data = document.getElementById('data').value;
    if (!data) {
      alert("Seleziona una data valida");
      return;
    }


    // Salvataggio nel DB (commentato)
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
				document.getElementById("risultato").innerHTML = this.responseText;
		}
    }
    xmlhttp.open("GET","../principale.php?funzione=salvaAffluenza&data="+data+"&ora="+ora+"&minuto="+minuto,true);
    xmlhttp.send();
    /*
    fetch(window.location.href, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'insert', data, ora, minuto })
    }).then(r => r.text()).then(console.log);
    */
  }
let deleteIndexAffluenza = null;

function confermaEliminaAffluenza(index) {
    deleteIndexAffluenza = index;
    const data = document.getElementById('data'+index).innerText;
    const orario = document.getElementById('orario'+index).innerText;
    document.getElementById('deleteAffluenzaInfo').textContent = data + " " + orario;
    $('#confirmDeleteAffluenzaModal').modal('show');
}

document.getElementById('confirmDeleteAffluenzaBtn').addEventListener('click', () => {
    if(deleteIndexAffluenza !== null){
        rimuoviAffluenza(deleteIndexAffluenza); // chiama la funzione esistente
        deleteIndexAffluenza = null;
        $('#confirmDeleteAffluenzaModal').modal('hide');
    }
});

  function rimuoviAffluenza(index) {
	var data = document.getElementById ( "data"+index ).innerText
	var orario = document.getElementById ( "orario"+index ).innerText
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
				document.getElementById("risultato").innerHTML = this.responseText;
		}
    }
    xmlhttp.open("GET","../principale.php?funzione=salvaAffluenza&data="+data+"&ora="+orario+"&minuto=&op=cancella",true);
    xmlhttp.send();

    // Eliminazione nel DB (commentato)
    /*
    fetch(window.location.href, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete', data: aff.data, ora: aff.ora, minuto: aff.minuto })
    }).then(r => r.text()).then(console.log);
    
*/
	document.getElementById("riga"+index).style.display = 'none'
  }
</script>
