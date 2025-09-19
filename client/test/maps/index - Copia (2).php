<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" />
  <title>Seleziona sede elettorale - ricerca e nome uniti</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <style>
    #mapPopup {
      display: none;
      position: fixed;
      top: 5%;
      left: 5%;
      width: 90%;
      height: 80%;
      background: white;
      border: 2px solid #ccc;
      z-index: 1000;
      padding: 10px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }
    #map {
      width: 100%;
      height: 90%;
    }
    #closeMap {
      margin-top: 5px;
    }
  </style>
</head>
<body>

<h3>Seleziona sede elettorale</h3>

<!-- Campo unico per ricerca + nome sede -->
<input type="text" id="nomeSede" placeholder="Cerca o inserisci nome sede..." style="width: 100%; padding: 8px; margin-bottom: 10px;" />

<form id="formSede">
  <label>Indirizzo (via, piazza, ecc.):</label><br />
  <input type="text" id="indirizzo" name="indirizzo" readonly style="width: 100%; margin-bottom: 10px;" />

  <label>Latitudine:</label><br />
  <input type="text" id="lat" name="lat" readonly style="margin-bottom: 10px;" />

  <label>Longitudine:</label><br />
  <input type="text" id="lng" name="lng" readonly style="margin-bottom: 10px;" />
</form>

<!-- Popup con mappa -->
<div id="mapPopup">
  <div id="map"></div>
  <button id="closeMap">✅ Usa questa posizione</button>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  let map, marker;
  let lastSearchQuery = "";

  const nomeSedeInput = document.getElementById('nomeSede');
  const indirizzoInput = document.getElementById('indirizzo');
  const latInput = document.getElementById('lat');
  const lngInput = document.getElementById('lng');
  const mapPopup = document.getElementById('mapPopup');
  const closeMapBtn = document.getElementById('closeMap');

  nomeSedeInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const query = this.value.trim();
      if (!query) return;

      lastSearchQuery = query;

      fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(results => {
          if (results.length === 0) {
            alert('Nessun risultato trovato per "' + query + '". Puoi modificare il nome sede manualmente.');
            return;
          }

          const r = results[0];
          apriPopupMappa(r);
        });
    }
  });

  function apriPopupMappa(result) {
    const lat = parseFloat(result.lat);
    const lon = parseFloat(result.lon);

    mapPopup.style.display = 'block';

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
      marker = L.marker([lat, lon], { draggable: true }).addTo(map);

      // Aggiorna dati quando sposti il marker
      marker.on('dragend', onMarkerDragEnd);
    }

    // Mostra popup col nome e indirizzo iniziale
    aggiornaPopup(result.display_name, lat, lon);

    // Aggiorna i campi con la posizione iniziale
    latInput.value = lat.toFixed(6);
    lngInput.value = lon.toFixed(6);

    // Prova a ottenere indirizzo e nome via reverse geocoding per il popup e indirizzo
    aggiornaIndirizzoPopup(lat, lon);
  }

  function onMarkerDragEnd(e) {
    const pos = e.target.getLatLng();
    latInput.value = pos.lat.toFixed(6);
    lngInput.value = pos.lng.toFixed(6);

    // Aggiorna indirizzo e nome tramite reverse geocoding
    aggiornaIndirizzoPopup(pos.lat, pos.lng);
  }

  function aggiornaIndirizzoPopup(lat, lon) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
      .then(res => res.json())
      .then(data => {
        let nome = data.name || lastSearchQuery || nomeSedeInput.value;
        let via = data.address.road || data.address.pedestrian || data.address.footway || data.address.cycleway || '';
        let numero = data.address.house_number || '';
        let indirizzoCompleto = (via + ' ' + numero).trim();

        indirizzoInput.value = indirizzoCompleto;

        // Aggiorna campo nomeSede con il nome trovato (ma puoi comunque modificare manualmente)
        nomeSedeInput.value = nome;

        // Aggiorna popup marker
        aggiornaPopup(nome + (indirizzoCompleto ? '<br>' + indirizzoCompleto : ''), lat, lon);
      });
  }

  function aggiornaPopup(contenuto, lat, lon) {
    marker.bindPopup(contenuto).openPopup();
  }

  closeMapBtn.addEventListener('click', function() {
    mapPopup.style.display = 'none';
  });
</script>

</body>
</html>
