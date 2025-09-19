<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8" />
<title>Seleziona sede elettorale - solo compilazione campi</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
  #map { height: 400px; width: 100%; margin-bottom: 10px; }
  body { font-family: Arial, sans-serif; margin: 20px; }
</style>
</head>
<body>

<h3>Seleziona sede elettorale (scuola) con OpenStreetMap</h3>

<div>
  <input type="text" id="cercaSede" placeholder="Cerca scuola o indirizzo..." style="width: 100%; padding: 8px; margin-bottom: 10px;" />
</div>

<div id="map"></div>

<form id="formSede">
  <label>Nome sede / scuola:</label><br />
  <input type="text" id="nomeSede" name="nomeSede" readonly style="width: 100%; margin-bottom: 10px;" />

  <label>Indirizzo completo:</label><br />
  <input type="text" id="indirizzo" name="indirizzo" readonly style="width: 100%; margin-bottom: 10px;" />

  <label>Latitudine:</label><br />
  <input type="text" id="lat" name="lat" readonly style="margin-bottom: 10px;" />

  <label>Longitudine:</label><br />
  <input type="text" id="lng" name="lng" readonly style="margin-bottom: 10px;" />
</form>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  const map = L.map('map').setView([42.5, 12.5], 6);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  let marker = null;

  function aggiornaCampi(nome, indirizzo, lat, lng) {
    document.getElementById('nomeSede').value = nome || '';
    document.getElementById('indirizzo').value = indirizzo || '';
    document.getElementById('lat').value = lat ? lat.toFixed(6) : '';
    document.getElementById('lng').value = lng ? lng.toFixed(6) : '';
  }

  // Cerca con Nominatim (OpenStreetMap) la scuola o indirizzo
  document.getElementById('cercaSede').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const query = this.value.trim();
      if (!query) return;

      fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(results => {
          if (results.length === 0) {
            alert('Nessun risultato trovato');
            return;
          }

          // Prendo il primo risultato
          const r = results[0];
          const lat = parseFloat(r.lat);
          const lon = parseFloat(r.lon);
          const display_name = r.display_name;

          // Mostro marker sulla mappa
          if(marker) {
            marker.setLatLng([lat, lon]);
          } else {
            marker = L.marker([lat, lon], { draggable: true }).addTo(map);

            // Se sposto marker aggiorno campi indirizzo
            marker.on('dragend', function(e) {
              const pos = e.target.getLatLng();
              fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${pos.lat}&lon=${pos.lng}`)
                .then(res => res.json())
                .then(data => {
                  aggiornaCampi(data.name || '', data.display_name || '', pos.lat, pos.lng);
                })
                .catch(() => {
                  aggiornaCampi('', '', pos.lat, pos.lng);
                });
            });
          }

          map.setView([lat, lon], 16);

          // Provo a prendere nome (tipo scuola) dal "type" o "class" o dal display_name
          // Qui semplifico: metto prima parte display_name come nome sede
          const nomeSede = r.type === 'school' || r.class === 'amenity' ? r.display_name.split(',')[0] : '';

          aggiornaCampi(nomeSede, display_name, lat, lon);
        })
        .catch(() => {
          alert('Errore durante la ricerca');
        });
    }
  });
</script>

</body>
</html>
