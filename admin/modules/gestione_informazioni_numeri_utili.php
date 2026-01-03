<?php require_once '../includes/check_access.php'; 
$_SESSION['tipo_info']='numero';
?>
<section class="content">
  <div class="container-fluid">
  <h2><i class="fas fa-phone-alt "></i> Gestione Numeri Utili</h2>
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title">Aggiungi Numeri Utili</h3>
      </div>
      <div class="card-body">

       <form id="infoForm" onsubmit="aggiungiInfo(event)">
    <div class="form-group" style="display:none;">
        <label>Tipo</label>
        <input type="text" name="tipo" id="tipo" class="form-control" value="<?= $_SESSION['tipo_info'] ?>">
    </div>

    <div class="form-group" style="display:none;">
        <label>Posizione</label>
        <input type="text" name="mid" id="mid" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Titolo/Ufficio</label>
        <input type="text" name="title" id="title" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Sede</label>
        <textarea name="preamble" id="preamble" class="form-control" rows="2"></textarea>
    </div>

    <div class="form-group">
        <label>Numero di telefono</label>
        <input type="text" name="content" id="content" class="form-control" maxlength="20" placeholder="Numero di telefono">
    </div>

    <button type="submit" class="btn btn-primary mt-2" id="btnSalvaInfo">Salva</button>
    <button type="reset" class="btn btn-secondary mt-2" onclick="resetFormInfo()">Annulla</button>
</form>

        <hr>
		<div class="card-header bg-secondary text-white">
			<h3 class="card-title">Elenco Numeri Utili</h3>
		</div>
		<div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Titolo/Ufficio</th>
              <th>Sede</th>
              <th style="display:none;">Telefono 1</th>
			  <th style="display:none;">Telefono 2</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody id="risultato">
            <?php include('elenco_info.php'); ?>
          </tbody>
        </table>
		 </div>
      </div>
    </div>
  </div>
</section>

<!-- Modal conferma eliminazione -->
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
		  Sei sicuro di voler eliminare il numero utile: <strong id="deleteTitle"></strong>? Questa azione non può essere annullata.
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

<script>
let deleteMid = null;

// Funzione Aggiungi / Salva
function aggiungiInfo(e) { 
    e.preventDefault(); 
    const tipo = document.getElementById('tipo').value;
    const mid = document.getElementById('mid').value;
    const title = document.getElementById('title').value;
    const preamble = document.getElementById('preamble').value;
    const content = document.getElementById('content').value; // unico numero

    const formData = new FormData();
    formData.append('funzione', 'salvaInfo');
    formData.append('tipo', tipo);
    formData.append('mid', mid);
    formData.append('title', title);
    formData.append('preamble', preamble);
    formData.append('content', content);
    formData.append('op', 'salva');

    fetch('../principale.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            document.getElementById('risultato').innerHTML = data;
            resetFormInfo();
            aggiornaNumero();
        });
}

// Funzione Modifica
function editInfo(index) {
    document.getElementById("mid").value = document.getElementById("mid"+index).innerText;
    document.getElementById("title").value = document.getElementById("title"+index).innerText;
    document.getElementById("preamble").value = document.getElementById("preamble"+index).innerText;
    document.getElementById("content").value = document.getElementById("content"+index).innerText;
    document.getElementById("btnSalvaInfo").textContent = "Salva modifiche";
    document.getElementById("mid").focus();
}

// Funzione Elimina con modale
function deleteInfo(index) {
    deleteMid = document.getElementById('mid'+index).innerText;
    const titolo = document.getElementById('title'+index).innerText;
    document.getElementById('deleteTitle').textContent = titolo;
    $('#confirmDeleteModal').modal('show'); // apri modale
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if(deleteMid) {
        const formData = new FormData();
        formData.append('funzione', 'salvaInfo');
        formData.append('mid', deleteMid);
        formData.append('op', 'cancella');

        fetch('../principale.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            document.getElementById('risultato').innerHTML = data;
            resetFormInfo();
            aggiornaNumero();
            $('#confirmDeleteModal').modal('hide');
            deleteMid = null;
        });
    }
});

// Reset Form
function resetFormInfo() {
    document.getElementById('infoForm').reset();
    document.getElementById('mid').value = '';
    document.getElementById('btnSalvaInfo').textContent = "Aggiungi";
}

// Aggiorna numero progressivo (mid)
function aggiornaNumero() {
    const maxNum = document.getElementById("maxNumero").innerText;
    document.getElementById('mid').value = maxNum;
}

</script>
