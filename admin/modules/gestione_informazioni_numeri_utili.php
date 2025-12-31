<?php require_once '../includes/check_access.php'; 
$_SESSION['tipo_info']='numero';
?>
<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-phone-alt me-2"></i>Numeri Utili</h3>
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
            <input type="text" name="titolo" id="title" class="form-control" value="">
          </div>
          <div class="form-group">
            <label>Sede</label>
            <textarea name="preamble" id="preamble" class="form-control" rows="2"></textarea>
          </div>
			<div class="form-row row">
			  <div class="form-group col-md-6">
				<label>N. Telefono 1</label>
				<input type="text" name="telefono1" id="telefono1" class="form-control" maxlength="20" placeholder="Telefono 1">
			  </div>
			  <div class="form-group col-md-6">
				<label>N. Telefono 2</label>
				<input type="text" name="telefono2" id="telefono2" class="form-control" maxlength="20" placeholder="Telefono 2">
			  </div>
			</div>


          <button type="submit" class="btn btn-primary mt-2" id="btnSalvaInfo">Salva</button>
          <button type="reset" class="btn btn-secondary mt-2" onclick="resetFormInfo()">Annulla</button>
        </form>

        <hr>

        <h5>Elenco Numeri Utili</h5>
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

function aggiungiInfo(e) { 
    e.preventDefault(); 
    const tipo = document.getElementById('tipo').value;
    const mid = document.getElementById('mid').value;
    const title = document.getElementById('title').value;
    const preamble = document.getElementById('preamble').value;
    const telefono1 = document.getElementById('telefono1').value;
	const telefono2 = document.getElementById('telefono2').value;

    const formData = new FormData();
    formData.append('funzione', 'salvaInfo');
    formData.append('tipo', tipo);
    formData.append('mid', mid);
    formData.append('title', title);
    formData.append('preamble', preamble);
    formData.append('telefono1', telefono1);
	formData.append('telefono2', telefono2);
    formData.append('op', 'salva');

    fetch('../principale.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            document.getElementById('risultato').innerHTML = data;
            resetFormInfo();
            aggiornaNumero();
        });
}

function deleteInfo(index) {
    deleteMid = document.getElementById('mid'+index).innerText;
    const titolo = document.getElementById('title'+index).innerText; // leggo il titolo/ufficio
    document.getElementById('deleteTitle').textContent = titolo;      // lo mostro nel modal
    $('#confirmDeleteModal').modal('show'); // apro il modal
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
            document.getElementById("btnSalvaInfo").textContent = "Aggiungi";
            resetFormInfo();
            aggiornaNumero();
            $('#confirmDeleteModal').modal('hide');
            deleteMid = null;
        });
    }
});

function editInfo(index) {
    document.getElementById("mid").value = document.getElementById("mid"+index).innerText;
    document.getElementById("title").value = document.getElementById("title"+index).innerText;
    document.getElementById("preamble").value = document.getElementById("preamble"+index).innerText;
    document.getElementById("telefono1").value = document.getElementById("telefono1"+index).innerText;
	document.getElementById("telefono2").value = document.getElementById("telefono2"+index).innerText;
    document.getElementById("btnSalvaInfo").textContent = "Salva modifiche";
    document.getElementById("mid").focus();
}

function resetFormInfo() {
    const form = document.getElementById('infoForm');
    form.reset();
    document.getElementById('mid').value = '';
    document.getElementById('btnSalvaInfo').textContent = "Aggiungi";
}

function aggiornaNumero() {
    const maxNum = document.getElementById("maxNumero").innerText;
    document.getElementById('mid').value = maxNum;
}
</script>
