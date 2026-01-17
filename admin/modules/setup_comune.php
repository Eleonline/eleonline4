<?php
require_once '../includes/check_access.php';

$row=configurazione();
$predefinito=$row[0]['siteistat'];
$row=dati_comune($predefinito);
?>

<section class="content">
  <div class="container-fluid">
    <h2 id="form-title"><i class="fas fa-building"></i> Gestione dati del Comune</h2>

    <!-- FORM -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="form-subtitle">Modifica dati Ente</h3>
      </div>
      <div class="card-body">
        <form id="enteForm" enctype="multipart/form-data"  onsubmit="aggiungiComune(event)">
          <input type="hidden" name="ente_id" id="ente_id">

          <div class="form-row">
            <div class="form-group col-md-3">
              <img id="anteprimaStemma" src="../../client/documenti/img/logo.jpg" alt="Anteprima stemma" style="max-height: 80px; margin-top: 5px;">
              <label for="stemma">Stemma</label>
              <input type="file" class="form-control-file" id="stemma" name="stemma" accept="image/*">
            </div>
            <div class="form-group col-md-5">
              <label for="denominazione">Denominazione*</label>
              <input type="text" class="form-control" id="denominazione" value="<?= $row[0]['descrizione'] ?>" name="denominazione" required>
            </div>
            <div class="form-group col-md-4">
              <label for="indirizzo">Indirizzo*</label>
              <input type="text" class="form-control" id="indirizzo" value="<?= $row[0]['indirizzo'] ?>" name="indirizzo" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-2">
              <label for="cap">CAP*</label>
              <input type="text" class="form-control" id="cap" value="<?= $row[0]['cap'] ?>" name="cap" required>
            </div>
            <div class="form-group col-md-4">
              <label for="email">E-mail*</label>
              <input type="email" class="form-control" id="email" value="<?= $row[0]['email'] ?>" name="email" required>
            </div>
            <div class="form-group col-md-3">
              <label for="centralino">Centralino*</label>
              <input type="text" class="form-control" id="centralino" value="<?= $row[0]['centralino'] ?>" name="centralino" required>
            </div>
            <div class="form-group col-md-3">
              <label for="fax">Fax</label>
              <input type="text" class="form-control" id="fax" value="<?= $row[0]['fax'] ?>" name="fax">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="codice_istat">Codice ISTAT*</label>
              <input type="text" class="form-control" id="codice_istat" value="<?= $row[0]['id_comune'] ?>" name="codice_istat" required>
            </div>
            <div class="form-group col-md-4">
              <label for="abitanti">Abitanti*</label>
              <select class="form-control" id="abitanti" name="abitanti" required>
              <option value="">Seleziona...</option>
			  <?php
			  $row2=elenco_fasce(1);
			  $i=1;
			  foreach($row2 as $key=>$val){ 
			  if($val['id_fascia']==$row[0]['fascia']) $sel='selected'; else $sel='';
                echo "<option value=\"".$val['id_fascia']."\" $sel>".number_format($i,0,',','.')." - ".number_format(($val['abitanti']-1),0,',','.')."</option>";
				$i=$val['abitanti'];
				if($val['id_fascia']==8) break;
			  }
			  ?>
                <option value="9">Oltre 1.000.000</option>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label for="capoluogo">Capoluogo/Provincia*</label>
              <select class="form-control" id="capoluogo" name="capoluogo" required>
                <option value="">Seleziona...</option>
                <option value="Sì" <?php if($row[0]['capoluogo']==1) echo 'selected'; ?>>Sì</option>
                <option value="No" <?php if($row[0]['capoluogo']==0) echo 'selected'; ?>>No</option>
              </select>
            </div>
            <!--div class="form-group col-md-3 d-flex align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="predefinito" name="predefinito">
                <label class="form-check-label" for="predefinito">Ente predefinito</label>
              </div>
            </div-->
          </div>
          <button type="submit" class="btn btn-success" id="submitBtn">Aggiorna dati</button>
          <button type="reset" class="btn btn-secondary" id="cancelEdit">Annulla</button>
        </form>
		
      </div>
    </div>

  </div>
</section>
<!-- Toast container in basso a destra -->
<div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 1060;"></div>

<script>
function mostraToast(messaggio, tipo='success') {
    const container = document.getElementById('toast-container');

    // Crea il toast
    const toast = document.createElement('div');
    toast.className = `toast bg-${tipo} text-white border-0 show`;
    toast.setAttribute('role','alert');
    toast.setAttribute('aria-live','assertive');
    toast.setAttribute('aria-atomic','true');
    toast.style.minWidth = '250px';
    toast.style.maxWidth = '90vw'; // responsive per smartphone
    toast.style.marginBottom = '10px';
    toast.style.padding = '10px';
    toast.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';

    // Contenuto con pulsante chiudi
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${messaggio}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Chiudi"></button>
        </div>
    `;

    container.appendChild(toast);

    // Rimuovi automaticamente dopo 4 secondi
    setTimeout(() => { toast.remove(); }, 4000);
}

function aggiungiComune(e) {
    e.preventDefault(); // blocca il submit normale

    const fileInput = document.getElementById('stemma');
    const file = fileInput.files[0];
    const denominazione = document.getElementById("denominazione").value;
    const indirizzo = document.getElementById("indirizzo").value;
    const cap = document.getElementById("cap").value;
    const email = document.getElementById("email").value;
    const centralino = document.getElementById("centralino").value;
    const fax = document.getElementById("fax").value;
    const abitanti = document.getElementById("abitanti").value;
    const codiceIstat = document.getElementById("codice_istat").value;
    const capoluogo = document.getElementById("capoluogo").value;

    const formData = new FormData();
    if(file) formData.append('stemma', file);
    formData.append('funzione', 'salvaComune');
    formData.append('descrizione', denominazione);
    formData.append('indirizzo', indirizzo);
    formData.append('cap', cap);
    formData.append('email', email);
    formData.append('centralino', centralino);
    formData.append('fax', fax);
    formData.append('fascia', abitanti);
    formData.append('id_comune', codiceIstat);
    formData.append('capoluogo', capoluogo);
    formData.append('op', 'salva');

    fetch('../principale.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if(!response.ok) throw new Error('Server risponde con status ' + response.status);
        return response.text();
    })
    .then(data => {
        mostraToast('Dati salvati correttamente!', 'success');
        console.log('Risposta server:', data);
    })
    .catch(error => {
        console.error('Errore fetch:', error);
        mostraToast('Si è verificato un errore durante l\'upload.', 'danger');
    });
}
</script>
