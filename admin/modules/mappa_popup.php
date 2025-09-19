<?php
$maps_provider = 'openstreetmap'; // usare 'openstreetmap' oppure 'google'

if ($maps_provider === 'google') {
    $google_maps_api_key = "LA_TUA_API_KEY_DAL_DB_O_CONFIG";
} else {
    $google_maps_api_key = null;
}
// Se chiave Google mancante o provider diverso da google, forza openstreetmap
if ($maps_provider !== 'google' || empty($google_maps_api_key)) {
    $maps_provider = 'openstreetmap';
}
?>
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
    z-index: 10000;
    padding: 10px 10px 35px 10px;
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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<?php if ($maps_provider === 'google' && $google_maps_api_key): ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo htmlspecialchars($google_maps_api_key); ?>&callback=initMap" async defer></script>
<?php endif; ?>

<div id="mapPopup">
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <strong>Seleziona la posizione</strong>
    <button id="closeMapX" style="background: none; border: none; font-size: 20px; cursor: pointer;">❌</button>
  </div>
  <div id="map"></div>
  <button id="closeMap">✅ Usa questa posizione</button>
</div>

<script>
  const maps_provider = <?= json_encode($maps_provider) ?>;
  const mapPopup = document.getElementById('mapPopup');
  const closeMapBtn = document.getElementById('closeMap');
  const closeMapX = document.getElementById('closeMapX');
  const mapDiv = document.getElementById('map');

  let map, marker, popupMarker;
  let gmap, gmarker, ginfoWindow;
  let currentInputs = null;
  let currentLatLng = null;

  // Apri la mappa al click su ogni bottone con classe .btnApriMappa
  document.querySelectorAll('.btnApriMappa').forEach(button => {
    button.addEventListener('click', () => {
      // Trova il container più vicino con classe 'rigaMappa'
      const container = button.closest('.rigaMappa');
      const indir = container.querySelector('.indir');
      const lat = container.querySelector('.lat');
      const lng = container.querySelector('.lng');
      const comuneInput = container.querySelector('.nome_comune');

      const comune = comuneInput ? comuneInput.value.trim() : "";
      if (!indir) return alert('Input indirizzo non trovato.');
      const query = indir.value.trim();
      if (!query) return alert("Inserisci un indirizzo o nome da cercare.");

      const fullQuery = comune ? `${query}, ${comune}` : query;
      currentInputs = { indir, lat, lng };

      // Ricerca coordinate con Nominatim OSM
      fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(fullQuery)}`)
        .then(res => res.json())
        .then(results => {
          if (results.length === 0) return alert('Nessun risultato trovato per "' + fullQuery + '".');
          apriMappa(results[0]);
        })
        .catch(() => alert('Errore durante la ricerca dell\'indirizzo.'));
    });
  });

  function apriMappa(result) {
    const lat = parseFloat(result.lat);
    const lon = parseFloat(result.lon);
    currentLatLng = { lat, lon };
    mapPopup.style.display = 'block';

    if (maps_provider === 'google') {
      if (!gmap) {
        gmap = new google.maps.Map(mapDiv, {
          center: { lat, lng: lon },
          zoom: 16
        });
        gmarker = new google.maps.Marker({
          position: { lat, lng: lon },
          map: gmap,
          draggable: true
        });
        ginfoWindow = new google.maps.InfoWindow();

        gmarker.addListener('drag', () => {
          const pos = gmarker.getPosition();
          currentLatLng = { lat: pos.lat(), lon: pos.lng() };
        });

        gmarker.addListener('dragend', () => {
          const pos = gmarker.getPosition();
          currentLatLng = { lat: pos.lat(), lon: pos.lng() };
          aggiornaIndirizzo(pos.lat(), pos.lng()).then(info => {
            ginfoWindow.setContent(`<strong>${info.address}</strong><br>Lat: ${info.lat.toFixed(6)}, Lon: ${info.lon.toFixed(6)}`);
            ginfoWindow.open(gmap, gmarker);
          });
        });
      } else {
        gmap.setCenter({ lat, lng: lon });
        gmarker.setPosition({ lat, lng: lon });
      }

      aggiornaIndirizzo(lat, lon).then(info => {
        ginfoWindow.setContent(`<strong>${info.address}</strong><br>Lat: ${info.lat.toFixed(6)}, Lon: ${info.lon.toFixed(6)}`);
        ginfoWindow.open(gmap, gmarker);
      });

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
        marker = L.marker([lat, lon], { draggable: true }).addTo(map);
        marker.on('drag', e => {
          const pos = e.target.getLatLng();
          currentLatLng = { lat: pos.lat, lon: pos.lng };
        });
        marker.on('dragend', e => {
          const pos = e.target.getLatLng();
          currentLatLng = { lat: pos.lat, lon: pos.lng };
          aggiornaIndirizzo(pos.lat, pos.lng).then(info => {
            mostraPopup(info.address, info.lat, info.lon);
          });
        });
      }

      if (!popupMarker) {
        popupMarker = L.popup({ closeButton: false, offset: [0, -30] });
      }

      aggiornaIndirizzo(lat, lon).then(info => {
        mostraPopup(info.address, info.lat, info.lon);
      });
    }
  }

  function mostraPopup(address, lat, lon) {
    const content = address
      ? `<strong>${address}</strong><br>Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}`
      : `Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}`;
    if (maps_provider === 'openstreetmap') {
      popupMarker.setLatLng([lat, lon]).setContent(content).openOn(map);
    }
  }

  // Reverse geocoding con Nominatim
  function aggiornaIndirizzo(lat, lon) {
    return fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
      .then(res => res.json())
      .then(data => {
        const nome = data.name || "";
        const via = data.address?.road || data.address?.pedestrian || data.address?.footway || "";
        const num = data.address?.house_number || "";
        const indirizzoCompleto = [nome, via, num].filter(Boolean).join(" - ");
        return { address: indirizzoCompleto, lat, lon };
      })
      .catch(() => ({ address: null, lat, lon }));
  }

  // Usa posizione selezionata: aggiorna campi input e chiudi popup
  closeMapBtn.addEventListener('click', () => {
    if (currentInputs && currentLatLng) {
      currentInputs.lat.value = currentLatLng.lat.toFixed(6);
      currentInputs.lng.value = currentLatLng.lon.toFixed(6);
      aggiornaIndirizzo(currentLatLng.lat, currentLatLng.lon).then(info => {
        if (currentInputs.indir && info.address) {
          const cleanedAddress = info.address.replace(/"/g, '').trim();
          currentInputs.indir.value = cleanedAddress;
        }
      });
    }
    mapPopup.style.display = 'none';
  });

  // Chiudi popup mappa senza salvare
  closeMapX.addEventListener('click', () => {
    mapPopup.style.display = 'none';
  });
</script>
