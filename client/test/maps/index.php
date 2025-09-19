<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8" />
<title>Test Mappa Popup</title>
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

<table border="1">
<tr>
  <td>
    <input type="text" class="indir" name="indir[]" value="Piazza San Marco, Venezia" autocomplete="off" />
    <button type="button" class="btnApriMappa">📍 Apri mappa</button>
    <input type="hidden" class="lat" name="lat[]" value="" />
    <input type="hidden" class="lng" name="lng[]" value="" />
  </td>
</tr>
</table>

<div id="mapPopup">
  <div id="map"></div>
  <button id="closeMap">✅ Usa questa posizione</button>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  const mapPopup = document.getElementById('mapPopup');
  const closeMapBtn = document.getElementById('closeMap');

  let map, marker;
  let currentInputs = null;

  document.querySelectorAll('.btnApriMappa').forEach(button => {
    button.addEventListener('click', () => {
      const td = button.parentElement;
      const indir = td.querySelector('.indir');
      const lat = td.querySelector('.lat');
      const lng = td.querySelector('.lng');

      if (!indir) {
        alert('Input indirizzo non trovato.');
        return;
      }

      const query = indir.value.trim();
      if (!query) {
        alert("Inserisci un indirizzo da cercare.");
        return;
      }

      currentInputs = {indir, lat, lng};

      fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(results => {
          if (results.length === 0) {
            alert('Nessun risultato trovato per "' + query + '". Puoi modificare manualmente.');
            return;
          }
          apriMappa(results[0]);
        })
        .catch(() => alert('Errore nella ricerca indirizzo'));
    });
  });

  function apriMappa(result) {
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
      marker.on('dragend', onMarkerDragEnd);
    }

    aggiornaCampi(lat, lon);
    aggiornaIndirizzo(lat, lon);
  }

  function onMarkerDragEnd(e) {
    const pos = e.target.getLatLng();
    aggiornaCampi(pos.lat, pos.lng);
    aggiornaIndirizzo(pos.lat, pos.lng);
  }

  function aggiornaCampi(lat, lon) {
    if (!currentInputs) return;
    currentInputs.lat.value = lat.toFixed(6);
    currentInputs.lng.value = lon.toFixed(6);
  }

  function aggiornaIndirizzo(lat, lon) {
    if (!currentInputs) return;
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
      .then(res => res.json())
      .then(data => {
        const via = data.address?.road || data.address?.pedestrian || data.address?.footway || "";
        const num = data.address?.house_number || "";
        const indirizzoCompleto = (via + " " + num).trim();

        currentInputs.indir.value = indirizzoCompleto || currentInputs.indir.value;
      })
      .catch(() => {});
  }

  closeMapBtn.addEventListener('click', () => {
    mapPopup.style.display = 'none';
  });
</script>

</body>
</html>
