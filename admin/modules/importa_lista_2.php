<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Assicurati che il modulo principale abbia definito APP_RUNNING
if (!defined('APP_RUNNING')) define('APP_RUNNING', true);

require_once '../includes/check_access.php';
?>

<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Importa Lista Elettorale</h3>
  </div>
  
  <div class="card-body">
    <!-- Box di avviso -->
    <div class="alert alert-warning" role="alert">
      <strong>Attenzione!</strong> I dati sostituiscono completamente quelli eventualmente già presenti nella consultazione corrente.<br>
    </div>

    <!-- Form upload -->
    <form name="importa" enctype="multipart/form-data" method="post" action="modules.php" onsubmit="return checkFile()">
      <input type="hidden" name="op" value="21">
      <input type="hidden" name="id_cons_gen" value="<?= $id_cons_gen ?>">
      <input type="hidden" name="id_comune" value="<?= $id_comune ?>">

      <div class="form-group">
        <label for="datafile">Scegli il file di dati da importare</label>
        <input type="file" name="datafile" id="datafile" class="form-control">
      </div>

      <div class="form-group mt-3">
       <button type="submit" name="add" value="<?= _OK ?>" class="btn btn-danger btn-block btn-lg submit-btn">
			<i class="fas fa-cloud-download-alt"></i> Avvia importazione
			<i class="fas fa-spinner fa-spin d-none ms-2" id="spinnerBtn"></i>
		</button>

      </div>

      <!-- Alert JS se non selezionato file -->
      <div id="fileAlert" class="alert alert-danger mt-2" style="display:none;">
        Devi selezionare un file da importare!
      </div>
    </form>
  </div>
</div>

<script>
function checkFile() {
    var fileInput = document.getElementById('datafile');
    var alertDiv = document.getElementById('fileAlert');
    var spinner = document.getElementById('spinnerBtn');

    if (fileInput.files.length === 0) {
        alertDiv.style.display = 'block';
        return false; // blocca submit
    } else {
        alertDiv.style.display = 'none';
        spinner.classList.remove('d-none');
        return true;
    }
}

document.getElementById('datafile').addEventListener('change', function() {
    var alertDiv = document.getElementById('fileAlert');
    if (this.files.length > 0) alertDiv.style.display = 'none';
});
</script>
