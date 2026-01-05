<?php require_once '../includes/check_access.php'; 
	$_SESSION['tipo_info']='servizio';
?>
<section class="content">
  <div class="container-fluid">
  <h2><i class="fas fa-concierge-bell"></i> Gestione Servizi</h2>
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title" id="form-title">Aggiungi Servizi</h3>
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
            <label>Titolo</label>
             <input type="text" name="titolo" id="title" class="form-control" value="">
          </div>
          <div class="form-group">
            <label>Descrizione</label>
            <textarea name="preamble" id="preamble" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Testo</label>
            <textarea name="content" id="content" class="form-control" rows="10"></textarea>
          </div>
          <button type="submit" class="btn btn-primary mt-2" id="btnSalvaInfo">Salva</button>
          <button type="reset" class="btn btn-secondary mt-2" onclick="resetFormInfo()">Annulla</button>
        </form>
        <hr>
       	<div class="card-header bg-secondary text-white">
			<h3 class="card-title">Elenco Servizi</h3>
		</div>
		<div class="table-responsive">
		  <table class="table table-bordered table-hover">
			<thead>
			  <tr>
				<th>Titolo</th>
				<th>Descrizione</th>
				<th style="display:none;">Testo</th>
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
        Sei sicuro di voler eliminare il servizio: <strong id="deleteTitle"></strong>? Questa azione non può essere annullata.
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
<!-- <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.0/build/ckeditor.js"></script> -->
<script src="../js/ckeditor.js"></script>
<script>
function aggiungiInfo(e) { 
    e.preventDefault(); 
    const tipo = document.getElementById('tipo').value;
    const mid = document.getElementById('mid').value;
    const title = document.getElementById('title').value;
    const preamble = document.getElementById('preamble').value;
	const content = editor.getData();

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

 let deleteMid = null;

function deleteInfo(index) {
    deleteMid = document.getElementById('mid'+index).innerText;
    const titolo = document.getElementById('title'+index).innerText; // titolo servizio
    document.getElementById('deleteTitle').textContent = titolo;      // mostra nel modal
    $('#confirmDeleteModal').modal('show'); // apri modal
}
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if(deleteMid) {
        const formData = new FormData();
        formData.append('funzione', 'salvaInfo');
        formData.append('mid', deleteMid);
        formData.append('op', 'cancella');

        fetch('../principale.php', {
            method: 'POST',
            body: formData
        })
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
    editor.setData(document.getElementById("content"+index).innerText);
    document.getElementById("btnSalvaInfo").textContent = "Salva modifiche";
	document.getElementById("mid").focus();
	
}

function resetFormInfo() {
    const form = document.getElementById('infoForm');
    form.reset();
    document.getElementById('mid').value = '';
	editor.setData('');
    document.getElementById('btnSalvaInfo').textContent = "Aggiungi";
}
function aggiornaNumero() {
	const maxNum = document.getElementById("maxNumero").innerText;
    document.getElementById('mid').value = maxNum;
}

ClassicEditor
    .create(document.querySelector('#preamble'))
    .then(newEditor => {
        preambleEditor = newEditor; // salvi l'istanza in una variabile se vuoi
       
        // Imposta altezza iniziale
        preambleEditor.ui.view.editable.element.style.height = '100px';
        preambleEditor.ui.view.editable.element.style.minHeight = '100px';

        // Mantieni altezza anche quando si clicca dentro
        preambleEditor.editing.view.change(writer => {
            writer.setStyle('height', '100px', preambleEditor.editing.view.document.getRoot());
        });
    })
    .catch(error => {
        console.error(error);
    });


ClassicEditor
    .create(document.querySelector('#content'))
    .then(newEditor => {
        editor = newEditor;

        // Imposta altezza iniziale
        editor.ui.view.editable.element.style.height = '400px';
        editor.ui.view.editable.element.style.minHeight = '400px';

        // Mantieni altezza anche quando si clicca dentro
        editor.editing.view.change(writer => {
            writer.setStyle('height', '400px', editor.editing.view.document.getRoot());
        });
    })
    .catch(error => {
        console.error(error);
    });

</script>
