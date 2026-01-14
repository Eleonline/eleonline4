<?php
require_once '../includes/check_access.php';

$currentUserRole = $_SESSION['ruolo'] ?? 'operatore';
$row=configurazione();
$predefinito=$row[0]['siteistat'];

?>

<section class="content">
  <div class="container-fluid">
    <h2><i class="fas fa-building"></i> Gestione Enti/Comuni</h2>

    <!-- FORM -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="form-title">Aggiungi Ente</h3>
      </div>
      <div class="card-body">
        <form id="enteForm" enctype="multipart/form-data"  onsubmit="aggiungiComune(event)">
          <input type="hidden" name="ente_id" id="ente_id">

          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="stemma">Stemma</label>
              <input type="file" class="form-control-file" id="stemma" name="stemma" accept="image/*">
              <img id="anteprimaStemma" src="" alt="Anteprima stemma" style="max-height: 80px; margin-top: 5px; display: none;">
            </div>
            <div class="form-group col-md-5">
              <label for="denominazione">Denominazione*</label>
              <input type="text" class="form-control" id="denominazione" name="denominazione" required>
            </div>
            <div class="form-group col-md-4">
              <label for="indirizzo">Indirizzo*</label>
              <input type="text" class="form-control" id="indirizzo" name="indirizzo" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-2">
              <label for="cap">CAP*</label>
              <input type="text" class="form-control" id="cap" name="cap" required>
            </div>
            <div class="form-group col-md-4">
              <label for="email">E-mail*</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group col-md-3">
              <label for="centralino">Centralino*</label>
              <input type="text" class="form-control" id="centralino" name="centralino" required>
            </div>
            <div class="form-group col-md-3">
              <label for="fax">Fax</label>
              <input type="text" class="form-control" id="fax" name="fax">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="codice_istat">Codice ISTAT*</label>
              <input type="text" class="form-control" id="codice_istat" name="codice_istat" required>
            </div>
            <div class="form-group col-md-4">
              <label for="abitanti">Abitanti*</label>
              <select class="form-control" id="abitanti" name="abitanti" required>
              <option value="">Seleziona...</option>
			  <?php
			  $row=elenco_fasce(1);
			  $i=1;
			  foreach($row as $key=>$val){
                echo "<option value=\"".$val['id_fascia']."\">".number_format($i,0,',','.')." - ".number_format(($val['abitanti']-1),0,',','.')."</option>";
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
                <option value="Sì">Sì</option>
                <option value="No">No</option>
              </select>
            </div>
            <!--div class="form-group col-md-3 d-flex align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="predefinito" name="predefinito">
                <label class="form-check-label" for="predefinito">Ente predefinito</label>
              </div>
            </div-->
          </div>
          <button type="submit" class="btn btn-success" id="submitBtn">Aggiungi ente</button>
          <button type="button"
        class="btn btn-secondary"
        onclick="resetFormEnte()">
    Annulla
</button>

        </form>
      </div>
    </div>
 
    <!-- LISTA -->
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Lista Enti/Comuni</h3>
      </div>
      <div class="card-body table-responsive" style="max-height:400px; overflow-y:auto;">
        <table class="table table-bordered table-hover" id="entiTable">
          <thead>
            <tr>
              <th style="width: 40px;"></th>
              <th style="width: 60px;">Stemma</th>
              <th>Denominazione</th>
              <th>Indirizzo</th>
              <th>Abitanti</th>
              <th>Codice ISTAT</th>
              <th>Capoluogo/Provincia</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody id="risultato">
		  <?php include('elenco_comuni.php'); ?>		  
		  </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script>

function aggiungiComune(e) {
    e.preventDefault();

    const fileInput = document.getElementById('stemma');
    const file = fileInput.files[0];
	const denominazione = document.getElementById ( "denominazione" ).value
	const indirizzo = document.getElementById ( "indirizzo" ).value
	const cap = document.getElementById ( "cap" ).value
	const email = document.getElementById ( "email" ).value
	const centralino = document.getElementById ( "centralino" ).value
	const fax = document.getElementById ( "fax" ).value
	const abitanti = document.getElementById ( "abitanti" ).value
	const codiceIstat = document.getElementById ( "codice_istat" ).value
	const capoluogo = document.getElementById ( "capoluogo" ).value

    // Crea un oggetto FormData e aggiungi il file
    const formData = new FormData();
	if (file) {
		formData.append('stemma', file);
	}
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

    // Invia la richiesta AJAX usando Fetch
    fetch('../principale.php', {
        method: 'POST',
        body: formData // FormData viene gestito automaticamente da Fetch per l'upload
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
    risultato.innerHTML = data;
    resetFormEnte(); 
})

    .catch(error => {
        console.error('Errore durante l\'upload:', error);
        risultato.innerHTML = 'Si è verificato un errore durante l\'upload.';
    });
};


  function deleteEnte(index) {
	var denominazione = document.getElementById ( "denominazione"+index ).innerText
	var indirizzo = document.getElementById ( "indirizzo"+index ).innerText
	var abitanti = document.getElementById ( "abitanti"+index ).innerText
	var codiceIstat = document.getElementById ( "codiceIstat"+index ).innerText
	var capoluogo = document.getElementById ( "capoluogo"+index ).innerText
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
				document.getElementById("risultato").innerHTML = this.responseText;
		}
    }
    xmlhttp.open("GET","../principale.php?funzione=salvaComune&descrizione="+denominazione+"&indirizzo="+indirizzo+"&fascia="+abitanti+"&id_comune="+codiceIstat+"&capoluogo="+capoluogo+"&op=cancella",true);
    xmlhttp.send();

//	document.getElementById("riga"+index).style.display = 'none'
  }
   function editEnte(index) {
	document.getElementById ( "denominazione" ).value = document.getElementById ( "denominazione"+index ).innerText
	document.getElementById ( "indirizzo" ).value = document.getElementById ( "indirizzo"+index ).innerText
	document.getElementById ( "cap" ).value = document.getElementById ( "cap"+index ).value
	document.getElementById ( "email" ).value = document.getElementById ( "email"+index ).value
	document.getElementById ( "centralino" ).value = document.getElementById ( "centralino"+index ).value
	document.getElementById ( "fax" ).value = document.getElementById ( "fax"+index ).value
	document.getElementById ( "abitanti" ).selectedIndex = document.getElementById ( "abitanti"+index ).value
	document.getElementById ( "codice_istat" ).value = document.getElementById ( "codiceIstat"+index ).innerText
	if( document.getElementById ( "capoluogo"+index ).value == 1 )
		document.getElementById ( "capoluogo" ).selectedIndex = 1
	else
		document.getElementById ( "capoluogo" ).selectedIndex = 2
	document.getElementById ( "submitBtn" ).textContent = "Salva modifiche"
	document.getElementById("form-title").textContent = "Modifica Ente";
//	document.getElementById("riga"+index).style.display = 'none' 
  }
function resetFormEnte() {
    const form = document.getElementById('enteForm');
    form.reset(); // reset campi base

    // stato ADD
    document.getElementById('ente_id').value = '';
    document.getElementById('submitBtn').textContent = 'Aggiungi ente';
    document.getElementById('form-title').textContent = 'Aggiungi Ente';

    // reset select
    document.getElementById('abitanti').selectedIndex = 0;
    document.getElementById('capoluogo').selectedIndex = 0;

    // reset immagine
    const img = document.getElementById('anteprimaStemma');
    if (img) {
        img.src = '';
        img.style.display = 'none';
    }
}


</script>
