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

        <h5>Elenco Numeri Utili</h5>
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
</section>
<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@39.0.0/build/ckeditor.js"></script>
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

 function deleteInfo(index) {
	const mid = document.getElementById('mid'+index).innerText;
    const formData = new FormData();
    formData.append('funzione', 'salvaInfo');
    formData.append('mid', mid);
	formData.append('op', 'cancella');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
        document.getElementById('risultato').innerHTML = data; // Mostra la risposta del server
		document.getElementById ( "btnSalvaInfo" ).textContent = "Aggiungi";
		resetFormInfo();
		aggiornaNumero();
    })


  }
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
	.create( document.querySelector( '#content' ))
	.then( newEditor => {
editor = newEditor;
	})
	.catch( error => {
		console.error( error );
	} );
</script>
