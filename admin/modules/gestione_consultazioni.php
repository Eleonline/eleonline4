<?php
require_once '../includes/check_access.php';

?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-vote-yea"></i> Gestione Consultazioni</h2>

    <!-- FORM -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="form-title">Aggiungi Consultazione</h3>
      </div>
      <div class="card-body">
        <form id="consultazioneForm"  onsubmit="aggiungiConsultazione(event)">
          <input type="hidden" name="id_cons_gen" id="id_cons_gen">

          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="tipo">Tipo*</label>
              <select class="form-control" id="tipo" name="tipo" onchange="selezionaInput()" required>
                <option value="">Seleziona...</option>
                <option value="1">PROVINCIALI</option>
                <option value="2">REFERENDUM</option>
                <option value="3">COMUNALI</option>
                <option value="4">CIRCOSCRIZIONALI</option>
                <option value="5">BALLOTTAGGIO COMUNALI</option>
                <option value="6">CAMERA</option>
                <option value="7">SENATO</option>
                <option value="8">EUROPEE</option>
                <option value="9">REGIONALI</option>
                <option value="10">SENATO CON GRUPPI</option>
                <option value="11">CAMERA CON GRUPPI</option>
                <option value="12">PROVINCIALI CON COLLEGI</option>
                <option value="13">BALLOTTAGGIO PROVINCIALI</option>
                <option value="14">EUROPEE CON COLLEGI</option>
                <option value="15">CAMERA CON GRUPPI E COLLEGI</option>
                <option value="16">SENATO CON GRUPPI E COLLEGI</option>
                <option value="17">REGIONALI CON COLLEGI</option>
                <option value="18">CAMERA - Rosatellum 2.0</option>
                <option value="19">SENATO - Rosatellum 2.0</option>
              </select>
            </div>
            <div class="form-group col-md-5">
              <label for="denominazione">Denominazione*</label>
              <input type="text" class="form-control" id="denominazione" name="denominazione" required>
            </div>
            <div class="form-group col-md-2">
              <label for="data_inizio">Data Inizio*</label>
              <input type="date" class="form-control" id="data_inizio" name="data_inizio" required>
            </div>
            <div class="form-group col-md-2">
              <label for="data_fine">Data Fine*</label>
              <input type="date" class="form-control" id="data_fine" name="data_fine" required>
            </div>
          </div>

          <div class="form-group" id="divlink">
            <label for="link">Link DAIT Trasparenza</label>
            <input type="url" class="form-control" id="link" name="link">
		  </div>
          <div class="form-row">		  
            <div class="form-group col-md-1" id="divpreferenze">
				<label for="preferenze">Preferenze</label>
				<input type="input" class="form-control" id="preferenze" name="preferenze">
			</div>
			<?php $row=elenco_leggi(); ?>
			<div class="form-group col-md-2" id="divlegge">
				<label for="id_conf">Legge elettorale</label>
				<select class="form-control" id="id_conf" name="id_conf">
				<?php $i=0; foreach($row as $val) { ?>
					<option value="<?= $val['id_conf'] ?>" <?php if(!$i++) echo "selected"; ?>><?= $val['descrizione'] ?></option>
				<?php } ?>
				</select>				
			</div>
			<div class="form-group col-md-2" id="divstato">
				<label for="chiusa">Stato</label>
				<select class="form-control" id="chiusa" name="chiusa">
                <option value="0">Attiva</option>
                <option value="1">Chiusa</option>
                <option value="2">Nulla</option>
              </select>				
			</div>
			<div class="form-group col-md-2" id="divfascia">
				<label for="id_fascia">Abitanti</label>
				<select class="form-control" id="id_fascia" name="id_fascia">
                <option value="1">0 - 3.000</option>
                <option value="2">3.001 - 10.000</option>
                <option value="3">10.001 - 15.000</option>
                <option value="4">15.001 - 30.000</option>
                <option value="5">30.001 - 100.000</option>
                <option value="6">100.001 - 250.000</option>
                <option value="7">250.001 - 500.000</option>
                <option value="8">500.001 - 1.000.000</option>
                <option value="9">Oltre 1.000.000</option>
              </select>				
			</div>
		  </div>
          <div class="form-row">		  		  
			<div class="form-group col-md-2" id="divdisgiunto">
				<label for="disgiunto">Voto disgiunto</label>
				<select class="form-control" id="disgiunto" name="disgiunto">
				<option value="0">No</option>
                <option value="1">Si</option>
              </select>				
			</div>
			<div class="form-group col-md-2" id="divsologruppo">
				<label for="solo_gruppo">Ai soli gruppi</label>
				<select class="form-control" id="solo_gruppo" name="solo_gruppo">
				<option value="0">No</option>
                <option value="1">Si</option>
              </select>				
			</div>
			<div class="form-group col-md-2" id="divvismf">
				<label for="vismf">Affluenze per genere</label>
				<select class="form-control" id="vismf" name="vismf">
				<option value="0">No</option>
                <option value="1">Si</option>
              </select>				
			</div>
			<div class="form-group col-md-2" id="divproiezione">
				<label for="disgiunto">Proiezione consiglio</label>
				<select class="form-control" id="proiezione" name="proiezione">
				<option value="0">No</option>
                <option value="1">Si</option>
              </select>				
			</div>
          </div>

          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="preferita" name="preferita">
            <label class="form-check-label" for="preferita">Consultazione predefinita</label>
			
          </div>

          <button type="submit" class="btn btn-success" id="submitBtn">Aggiungi Consultazione</button>
          <button type="reset" class="btn btn-secondary" id="cancelEdit">Annulla</button>
        </form>
      </div>
    </div>

    <!-- LISTA -->
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Lista Consultazioni</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="consultazioniTable">
          <thead>
            <tr>
              <th style="width:30px;"></th> <!-- colonna stella senza titolo -->
              <th>Denominazione</th>
              <th>Data Inizio</th>
              <th>Data Fine</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody id="risultato"><?php include('elenco_consultazioni.php'); ?>	</tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script>
function aggiungiConsultazione(e) {
    e.preventDefault();

	const denominazione = document.getElementById ( "denominazione" ).value
	const dataInizio = document.getElementById ( "data_inizio" ).value
	const dataFine = document.getElementById ( "data_fine" ).value
	const linkDait = document.getElementById ( "link" ).value
	const tipo = document.getElementById ( "tipo" ).value
	const preferita = document.getElementById ( "preferita" ).value
	const id_cons_gen = document.getElementById ( "id_cons_gen" ).value
	const chiusa = document.getElementById ( "chiusa" ).value
	const id_conf = document.getElementById ( "id_conf" ).value
	const preferenze = document.getElementById ( "preferenze" ).value
	const id_fascia = document.getElementById ( "id_fascia" ).value
	const vismf = document.getElementById ( "vismf" ).value
	const solo_gruppo = document.getElementById ( "solo_gruppo" ).value
	const disgiunto = document.getElementById ( "disgiunto" ).value
	const proiezione = document.getElementById ( "proiezione" ).value

    // Crea un oggetto FormData e aggiungi il file
    const formData = new FormData();
    formData.append('funzione', 'salvaConsultazione');
    formData.append('id_cons_gen', id_cons_gen);
    formData.append('descrizione', denominazione);
    formData.append('data_inizio', dataInizio);
    formData.append('data_fine', dataFine);
    formData.append('link_dait', linkDait);
    formData.append('tipo', tipo);
    formData.append('preferita', preferita);
    formData.append('chiusa', chiusa);
    formData.append('id_conf', id_conf);
    formData.append('preferenze', preferenze);
    formData.append('id_fascia', id_fascia);
    formData.append('vismf', vismf);
    formData.append('solo_gruppo', solo_gruppo);
    formData.append('disgiunto', disgiunto);
    formData.append('proiezione', proiezione);
    formData.append('op', 'salva');

    // Invia la richiesta AJAX usando Fetch
    fetch('../principale.php', {
        method: 'POST',
        body: formData // FormData viene gestito automaticamente da Fetch per l'upload
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
		const myForm = document.getElementById('consultazioneForm');
        risultato.innerHTML = data; // Mostra la risposta del server
		myForm.reset();
		document.getElementById ( "submitBtn" ).textContent = "Aggiungi Consultazione"

    })
    .catch(error => {
        console.error('Errore durante l\'upload:', error);
        risultato.innerHTML = 'Si è verificato un errore durante l\'upload.';
    });
};


  function deleteConsultazione(index) {
	var id_cons_gen = document.getElementById ( "id_cons_gen"+index ).innerText
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
				document.getElementById("risultato").innerHTML = this.responseText;
		}
    }
    xmlhttp.open("GET","../principale.php?funzione=salvaConsultazione&id_cons_gen="+id_cons_gen+"&op=cancella",true);
    xmlhttp.send();

//	document.getElementById("riga"+index).style.display = 'none'
  }
   function editConsultazione(index) { 
	document.getElementById ( "id_cons_gen" ).value = document.getElementById ( "id_cons_gen"+index ).innerText
	document.getElementById ( "denominazione" ).value = document.getElementById ( "descrizione"+index ).innerText
	document.getElementById ( "data_inizio" ).value = document.getElementById ( "data_inizio"+index ).innerText
	document.getElementById ( "data_fine" ).value = document.getElementById ( "data_fine"+index ).innerText
	document.getElementById ( "tipo" ).selectedIndex = document.getElementById ( "tipo_cons"+index ).innerText
	document.getElementById ( "link" ).value = document.getElementById ( "link_trasparenza"+index ).innerText
	document.getElementById ( "chiusa" ).selectedIndex = document.getElementById ( "chiusa"+index ).innerText
	document.getElementById ( "id_conf" ).selectedIndex = document.getElementById ( "id_conf"+index ).innerText - 1
	if(document.getElementById ( "preferita"+index ).innerText==1)
		document.getElementById ( "preferita" ).checked = true
	else
		document.getElementById ( "preferita" ).checked = false
	document.getElementById ( "preferenze" ).value = document.getElementById ( "preferenze"+index ).innerText
	document.getElementById ( "id_fascia" ).selectedIndex = document.getElementById ( "id_fascia"+index ).innerText -1
	document.getElementById ( "vismf" ).selectedIndex = document.getElementById ( "vismf"+index ).innerText
	document.getElementById ( "solo_gruppo" ).selectedIndex = document.getElementById ( "solo_gruppo"+index ).innerText
	document.getElementById ( "disgiunto" ).selectedIndex = document.getElementById ( "disgiunto"+index ).innerText
	document.getElementById ( "proiezione" ).selectedIndex = document.getElementById ( "proiezione"+index ).innerText

	document.getElementById ( "submitBtn" ).textContent = "Salva modifiche"
//	document.getElementById("riga"+index).style.display = 'none' 
	selezionaInput()
  }
  

function selezionaInput() {
	const tipo = document.getElementById ( "tipo" ).value
	document.getElementById ( "divproiezione" ).style.display = 'none';
	switch (tipo) {
		case "1":
		case "5":
		case "6":
		case "7":
		case "8":
		case "12":
		case "13":
		case "14":
		
			document.getElementById ( "divpreferenze" ).style.display = 'block';
			document.getElementById ( "divlink" ).style.display = 'block';
			document.getElementById ( "divsologruppo" ).style.display = 'none';
			document.getElementById ( "divdisgiunto" ).style.display = 'none';
			document.getElementById ( "divfascia" ).style.display = 'none';
			document.getElementById ( "divlegge" ).style.display = 'none';
			break
		case "2":
			document.getElementById ( "divpreferenze" ).style.display = 'none';
			document.getElementById ( "divlink" ).style.display = 'none';
			document.getElementById ( "divsologruppo" ).style.display = 'none';
			document.getElementById ( "divdisgiunto" ).style.display = 'none';
			document.getElementById ( "divfascia" ).style.display = 'none';
			document.getElementById ( "divlegge" ).style.display = 'none';
			break;
		case "3":
		case "4":
			document.getElementById ( "divproiezione" ).style.display = 'block';
		case "9":
		case "10":
		case "11":
		case "15":
		case "16":
		case "17":
		case "18":
		case "19":
			document.getElementById ( "divpreferenze" ).style.display = 'block';
			document.getElementById ( "divlink" ).style.display = 'block';
			document.getElementById ( "divsologruppo" ).style.display = 'block';
			document.getElementById ( "divdisgiunto" ).style.display = 'block';
			document.getElementById ( "divfascia" ).style.display = 'block';
			document.getElementById ( "divlegge" ).style.display = 'block';
			break
	}
}	
</script>
