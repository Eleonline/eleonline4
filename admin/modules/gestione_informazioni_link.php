<?php 
	require_once '../includes/check_access.php'; 
	$_SESSION['tipo_info'] = 'link';
?>
<section class="content">
  <div class="container-fluid">

    <h2><i class="fas fa-link"></i> Gestione Link Utili</h2>

    <!-- ========================= -->
    <!-- CARD FORM -->
    <!-- ========================= -->
    <div class="card card-primary shadow-sm mb-3">
      <div class="card-header">
        <h3 class="card-title" id="form-title">Aggiungi Link Utili</h3>
      </div>

      <div class="card-body">
        <form id="infoForm" onsubmit="aggiungiInfo(event)">

          <input type="hidden" name="tipo" id="tipo" value="<?= $_SESSION['tipo_info'] ?>">
          <input type="hidden" name="mid" id="mid" value="">

          <div class="form-group">
            <label>Titolo</label>
            <input type="text" name="title" id="title" class="form-control">
          </div>

          <div class="form-group">
            <label>URL</label>
            <input type="url" name="preamble" id="preamble" class="form-control">
          </div>

          <div class="form-group">
            <label>Descrizione</label>
            <textarea name="content" id="content" class="form-control" rows="3"></textarea>
          </div>

          <button type="submit" class="btn btn-success mt-2" id="btnSalvaInfo">Salva</button>
          <button type="reset" class="btn btn-secondary mt-2" onclick="resetFormInfo()">Annulla</button>

        </form>
      </div>
    </div>

    <!-- ========================= -->
    <!-- CARD LISTA -->
    <!-- ========================= -->
    <div class="card shadow-sm">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Elenco Link Utili</h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover mb-0">
          <thead>
            <tr>
              <th>Titolo</th>
              <th>URL</th>
              <th>Descrizione</th>
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
      
      <!-- Header con icona e colore rosso -->
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Conferma eliminazione
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <!-- Corpo del modal -->
		<div class="modal-body">
		  Sei sicuro di voler eliminare il link: <strong id="deleteTitle"></strong>? Questa azione non può essere annullata.
		</div>

      <!-- Footer con pulsanti chiari e icone -->
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
let editor;
let deleteMid = null; // variabile globale per il link da eliminare

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
			document.getElementById("form-title").textContent = "Aggiungi Link Utili";
        });
}

function deleteInfo(index) {
    deleteMid = document.getElementById('mid'+index).innerText;
    const titolo = document.getElementById('title'+index).innerText; // prendo il titolo del link
    document.getElementById('deleteTitle').textContent = titolo;      // lo mostro nel modal
    $('#confirmDeleteModal').modal('show'); // apro il modal
}


// Conferma cancellazione dal modal
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
			document.getElementById("form-title").textContent = "Aggiungi Link Utili";
            resetFormInfo();
            aggiornaNumero();
			document.getElementById("form-title").textContent = "Aggiungi Link Utili";
            $('#confirmDeleteModal').modal('hide'); // chiudo il modal
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
	document.getElementById("form-title").textContent = "Modifica Link Utili";
	document.getElementById("mid").focus();
}

function resetFormInfo() {
    const form = document.getElementById('infoForm');
    form.reset();
    document.getElementById('mid').value = '';
	editor.setData('');
    document.getElementById('btnSalvaInfo').textContent = "Aggiungi";
	document.getElementById("form-title").textContent = "Aggiungi Link Utili";
}

function aggiornaNumero() {
	const maxNum = document.getElementById("maxNumero").innerText;
    document.getElementById('mid').value = maxNum;
}

ClassicEditor
    .create(document.querySelector('#content'))
    .then(newEditor => {
        editor = newEditor;

        // Imposta altezza iniziale
        editor.ui.view.editable.element.style.height = '100px';
        editor.ui.view.editable.element.style.minHeight = '100px';

        // Mantieni altezza anche quando si clicca dentro
        editor.editing.view.change(writer => {
            writer.setStyle('height', '100px', editor.editing.view.document.getRoot());
        });
    })
    .catch(error => {
        console.error(error);
    });

</script>
