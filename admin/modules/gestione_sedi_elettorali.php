<?php
require_once '../includes/check_access.php';
$row=configurazione();
$maps_provider = $row[0]['googlemaps']=='0' ? 'openstreetmap' : 'google'; // usare 'openstreetmap' oppure 'google'
$circos=elenco_circoscrizioni();

?>
<input type="hidden" id="consultazioneAttiva" value="3">
<section class="content">
  <div class="container-fluid">
  <h2><i class="fas fa-map-marker-alt"></i> Gestione Sedi Elettorali</h2>
  <div class="card card-primary shadow-sm">
      <!-- HEADER CARD CON TITOLO + BOTTONE IMPORTA -->
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 id="titoloGestioneSedi" class="card-title">Aggiungi Sedi Elettorali</h3>
      </div>
      <div class="card-body">
        <form id="formSede" class="mb-3" onsubmit="aggiungiSede(event)">
          <input type="hidden" id="idSede" value=""> 
          <div class="row mb-2">
            <div class="col-md-4">
              <label>Circoscrizione</label>
              <select id="idCirc" class="form-control">
				<?php foreach($circos as $key=>$val) { ?>
                <option value="<?= $val['id_circ'] ?>"><?= $val['descrizione'] ?></option>
				<?php } ?>
              </select>
            </div>
            <div class="col-md-8 rigaMappa">
              <label>Indirizzo</label>
              <div class="input-group">
                <input type="text" id="indirizzo" name="indirizzo" class="form-control indir" required>
                <button type="button" class="btn btn-outline-secondary btnApriMappa btnApriMappaForm">
                  <i class="fas fa-map-pin me-2"></i>Apri mappa
                </button>
              </div>
			  <!-- popolare di nome comune e lat e lng-->
				<?php $rowcomune=dati_comune(); ?>
              <input type="hidden" class="nome_comune" name="nome_comune" value="<?= $rowcomune[0]['descrizione'] ?>" >
              <input type="hidden" class="lat" id="lat" name="lat" value="" >
              <input type="hidden" class="lng" id="lng" name="lng" value="" >
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <label>Telefono</label>
              <input type="text" id="telefono" class="form-control">
            </div>
            <div class="col-md-3">
              <label>Fax</label>
              <input type="text" id="fax" class="form-control">
            </div>
            <div class="col-md-4">
              <label>Responsabile</label>
              <input type="text" id="responsabile" class="form-control">
            </div>
          </div>
		  <div class="row mt-2">
		  <div class="col-md-2">
			<button type="submit" class="btn btn-primary w-100" id="btnSalvaSede">Aggiungi</button>
		  </div>
		  <div class="col-md-2">
			<button type="reset" class="btn btn-secondary w-100" id="btnResetSede" onclick="resetFormSede()">Annulla</button>
		  </div>
		</div>
        </form>
      </div>
<div class="card shadow-sm mb-3">
  <div class="card-header bg-secondary text-white">
    <h3 class="card-title">Lista Sedi</h3>
  </div>
  <div class="card-body table-responsive">
    <table class="table table-striped mb-0" id="tabellaSedi">
      <thead>
        <tr>
          <th>Circoscrizione</th>
          <th>Indirizzo</th>
          <th>Mappa</th>
          <th>Telefono</th>
          <th>Fax</th>
          <th>Responsabile</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody id="risultato">
        <?php include('elenco_sedi.php'); ?>
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    <nav>
      <ul class="pagination justify-content-center mb-0" id="pagination"></ul>
    </nav>
  </div>
</div>

      <div class="card-footer text-muted">
        Puoi gestire qui tutte le sedi elettorali collegate alle circoscrizioni.
      </div>
    </div>
  </div>
</section>
<!-- Modal conferma eliminazione sede -->
<div class="modal fade" id="confirmDeleteSedeModal" tabindex="-1" aria-labelledby="confirmDeleteSedeLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteSedeLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Conferma eliminazione
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        Sei sicuro di voler eliminare la sede <strong id="deleteSedeIndirizzo"></strong>? Questa azione non può essere annullata.
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times me-1"></i>Annulla
        </button>
        <button type="button" class="btn btn-danger" id="confirmDeleteSedeBtn">
          <i class="fas fa-trash me-1"></i>Elimina
        </button>
      </div>

    </div>
  </div>
</div>
<?php 
include('mappa_popup.php');
?>
<script>

let deleteIdSede = null;
let deleteIndexSede = null;

function confermaEliminaSede(index) {
    deleteIndexSede = index;
    const indirizzo = document.getElementById('indirizzo'+index).innerText.trim();
    deleteIdSede = document.getElementById('idSede'+index).innerText;
    document.getElementById('deleteSedeIndirizzo').textContent = indirizzo;
    $('#confirmDeleteSedeModal').modal('show');
}

document.getElementById('confirmDeleteSedeBtn').addEventListener('click', () => {
    if(deleteIdSede !== null && deleteIndexSede !== null){
        deleteSede(deleteIndexSede); // usa la tua funzione esistente
        deleteIdSede = null;
        deleteIndexSede = null;
        $('#confirmDeleteSedeModal').modal('hide');
    }
});

// --- Funzione principale per aggiungere o modificare una sede ---
function aggiungiSede(e) {
    e.preventDefault();

    const id_circ = document.getElementById('idCirc').value;
    const id_sede = document.getElementById('idSede').value;
    const indirizzo = document.getElementById('indirizzo').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const fax = document.getElementById('fax').value.trim();
    const responsabile = document.getElementById('responsabile').value.trim();
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const nomeComuneInput = document.querySelector('.nome_comune');

    if (!indirizzo) {
        alert("L'Indirizzo è obbligatorio.");
        return;
    }

    // --- funzione interna per inviare form ---
    function inviaForm() {
        const formData = new FormData();
        formData.append('funzione', 'salvaSede');
        formData.append('id_circ', id_circ);
        formData.append('id_sede', id_sede);
        formData.append('indirizzo', indirizzo);
        formData.append('telefono', telefono);
        formData.append('fax', fax);
        formData.append('responsabile', responsabile);
        formData.append('latitudine', latInput.value);
        formData.append('longitudine', lngInput.value);
        formData.append('op', 'salva');

        fetch('../principale.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                risultato.innerHTML = data;

                // reset form
                const myForm = document.getElementById('formSede');
                myForm.reset();
                latInput.value = '';
                lngInput.value = '';
                document.getElementById("idSede").value = '';
                document.getElementById("btnSalvaSede").textContent = "Aggiungi";
                document.getElementById("titoloGestioneSedi").textContent = "Aggiungi Sedi Elettorali";
            })
            .catch(err => alert("Errore durante il salvataggio: " + err));
    }

    // --- Geocoding automatico se lat/lng vuoti ---
    if (!latInput.value || !lngInput.value) {
        const comune = nomeComuneInput ? nomeComuneInput.value.trim() : "";
        const fullQuery = comune ? `${indirizzo}, ${comune}` : indirizzo;

        fetch("https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" + encodeURIComponent(fullQuery), {
            headers: { 'User-Agent': 'Eleonline/1.0' } // importante per OSM
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.length) {
                alert("Indirizzo non trovato: " + fullQuery);
                return;
            }
            latInput.value = parseFloat(data[0].lat);
            lngInput.value = parseFloat(data[0].lon);

            // Invia form solo dopo aver popolato lat/lng
            inviaForm();
        })
        .catch(err => alert("Errore geocoding: " + err));
    } else {
        // lat/lng già presenti -> invia subito
        inviaForm();
    }
}


  function deleteSede(index) { 
	const id_circ = document.getElementById('idCirc'+index).innerText;
	const id_sede = document.getElementById('idSede'+index).innerText;
	const indirizzo = document.getElementById('indirizzo'+index).innerText.trim();
	const telefono = document.getElementById('telefono'+index).innerText;
	const fax = document.getElementById('fax'+index).innerText;
	const responsabile = document.getElementById('responsabile'+index).innerText.trim();
    const formData = new FormData();
    formData.append('funzione', 'salvaSede');
    formData.append('descrizione', indirizzo);
    formData.append('telefono', telefono);
    formData.append('id_circ', id_circ);
    formData.append('id_sede', id_sede);
	formData.append('fax', fax);
	formData.append('responsabile', responsabile);
	formData.append('op', 'cancella');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
        risultato.innerHTML = data; // Mostra la risposta del server
		document.getElementById ( "btnSalvaSede" ).textContent = "Aggiungi";
		document.getElementById("titoloGestioneSedi").textContent = "Aggiungi Sedi Elettorali";
    })


  }
  
 function resetFormSede() {
    const myForm = document.getElementById('formSede');
    myForm.reset();
    document.getElementById('idSede').value = '';
    document.getElementById('btnSalvaSede').textContent = "Aggiungi";
	document.getElementById("titoloGestioneSedi").textContent = "Aggiungi Sedi Elettorali";
}

   function editSede(index) { 
	document.getElementById ( "idCirc" ).value = document.getElementById ( "idCirc"+index ).innerText
	document.getElementById ( "idSede" ).value = document.getElementById ( "idSede"+index ).innerText
	document.getElementById ( "indirizzo" ).value = document.getElementById ( "indirizzo"+index ).innerText
	document.getElementById ( "telefono" ).value = document.getElementById ( "telefono"+index ).innerText
	document.getElementById ( "fax" ).value = document.getElementById ( "fax"+index ).innerText
	document.getElementById ( "lng" ).value = document.getElementById ( "lng"+index ).innerText
	document.getElementById ( "lat" ).value = document.getElementById ( "lat"+index ).innerText
	document.getElementById ( "responsabile" ).value = document.getElementById ( "responsabile"+index ).innerText
	document.getElementById ( "btnSalvaSede" ).textContent = "Salva modifiche"
	document.getElementById("titoloGestioneSedi").textContent = "Modifica Sede Elettorale";
  }

// --- Gestione pulsante apri mappa nel form  ---
document.querySelector('.btnApriMappaForm').addEventListener('click', () => {
    const container = document.querySelector('.rigaMappa');
    const indir = container.querySelector('.indir');
    const comuneInput = container.querySelector('.nome_comune');

    const comune = comuneInput ? comuneInput.value.trim() : "";
    if (!indir) return alert('Input indirizzo non trovato.');
    const query = indir.value.trim();
    if (!query) return alert("Inserisci un indirizzo o nome da cercare.");

    const fullQuery = comune ? `${query}, ${comune}` : query;

    if (maps_provider === 'google') {
        const apiKey = "LA_TUA_API_KEY_DAL_DB_O_CONFIG"; 
        fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(fullQuery)}&key=${apiKey}`)
            .then(res => res.json())
            .then(results => {
                if (!results.results || results.results.length === 0)
                    return alert('Nessun risultato trovato per "' + fullQuery + '".');
                const location = results.results[0].geometry.location;
                apriMappa({ lat: location.lat, lon: location.lng });
            })
            .catch(() => alert('Errore durante la ricerca dell\'indirizzo con Google Maps.'));
    } else {
        // Nominatim OSM
    fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(fullQuery)}`)
        .then(res => res.json())
        .then(results => {
            if (results.length === 0) return alert('Nessun risultato trovato per "' + fullQuery + '".');
            apriMappa(results[0]);
        })
        .catch(() => alert('Errore durante la ricerca dell\'indirizzo con OSM.'));
    }
});


function apriMappaSoloVisualizza(lat, lon, indirizzo) {
    const mapPopup = document.getElementById('mapPopup');
    const mapDiv = document.getElementById('map');

    mapPopup.style.display = 'block';

    if (maps_provider === 'google') {
        if (!gmap) {
            gmap = new google.maps.Map(mapDiv, {
                center: { lat: lat, lng: lon },
                zoom: 16
            });
            gmarker = new google.maps.Marker({
                position: { lat: lat, lng: lon },
                map: gmap,
                draggable: false
            });
        } else {
            gmap.setCenter({ lat: lat, lng: lon });
            gmarker.setPosition({ lat: lat, lng: lon });
        }

        ginfoWindow.setContent(`<strong>${indirizzo}</strong><br>Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}`);
        ginfoWindow.open(gmap, gmarker);

    } else {
        // OpenStreetMap + Leaflet
        if (!map) {
            map = L.map('map').setView([lat, lon], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
        } else {
            map.setView([lat, lon], 16);
        }

        if (marker) {
            marker.setLatLng([lat, lon]);
        } else {
            marker = L.marker([lat, lon], { draggable: false }).addTo(map);
        }

        if (!popupMarker) {
            popupMarker = L.popup({ closeButton: false, offset: [0, -30] });
        }

        popupMarker.setLatLng([lat, lon])
                   .setContent(`<strong>${indirizzo}</strong><br>Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}`)
                   .openOn(map);
    }
}



</script>