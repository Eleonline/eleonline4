<?php
require_once '../includes/check_access.php';

$gru = [
    'google_api_key' => ''
];

$MAP_PROVIDER = isset($gru['googlemaps']) && in_array($gru['googlemaps'], ['google', 'openstreetmap']) ? $gru['googlemaps'] : 'openstreetmap';
$GOOGLE_API_KEY = !empty($gru['google_api_key']) ? htmlspecialchars($gru['google_api_key']) : '';
$comuni_disponibili = ['Comune di Roma', 'Comune di Milano', 'Comune di Napoli'];
$DEFAULT_COMUNE = 'Comune di Roma';
?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title" id="form-title"><i class="fas fa-cogs me-2"></i>Setup Sito</h3>
      </div>
      <div class="card-body">
        <form>
          <!-- LOGO + ANTEPRIMA -->
          <div class="mb-3 d-flex align-items-center">
            <div id="previewImageDiv"
                 style="width: 100px; height: 60px; border: 1px solid #ccc; object-fit: contain; margin-right: 15px;">
              <img src="<?= htmlspecialchars($SITE_IMAGE) ?>" alt="Anteprima" id="previewImg" style="max-width: 100%; max-height: 100%;">
            </div>
            <div class="flex-grow-1">
              <label for="siteImage" class="form-label">Logo del sito</label>
              <input type="text" id="siteImage" name="site_image" class="form-control" value="<?= htmlspecialchars($SITE_IMAGE) ?>">
              <button type="button" class="btn btn-outline-secondary mt-2" id="uploadBtn">Carica</button>
              <input type="file" id="fileInput" accept="image/*" style="display:none;">
            </div>
          </div>

          <div class="mb-3">
            <label for="siteName" class="form-label">Nome del sito</label>
            <input type="text" id="siteName" name="site_name" class="form-control" value="<?= htmlspecialchars($SITE_NAME) ?>">
          </div>

          <div class="mb-3">
            <label for="siteUrl" class="form-label">URL del sito o IP</label>
            <input type="text" id="siteUrl" name="site_url" class="form-control" value="<?= htmlspecialchars($SITE_URL) ?>">
          </div>

          <div class="mb-3">
            <label for="emailAdmin" class="form-label">Mail dell'amministratore</label>
            <input type="email" id="emailAdmin" name="email_admin" class="form-control" value="<?= htmlspecialchars($EMAIL_ADMIN) ?>">
          </div>

          <!-- PROVIDER MAPPA -->
          <div class="mb-3">
            <label for="maps_provider" class="form-label">
              Provider Mappa
              <i class="fas fa-circle-info text-primary ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="Seleziona il provider delle mappe da usare per l'interfaccia della sede elettorale. OpenStreetMap è gratuito. Google Maps può richiedere una chiave API e comportare costi."></i>
            </label>
            <select class="form-select" name="googlemaps" id="maps_provider" onchange="toggleApiKeyField()">
              <option value="openstreetmap" <?= $MAP_PROVIDER === 'openstreetmap' ? 'selected' : '' ?>>OpenStreetMap</option>
              <option value="google" <?= $MAP_PROVIDER === 'google' ? 'selected' : '' ?>>Google Maps</option>
            </select>
          </div>

          <!-- API KEY -->
          <div class="mb-3" id="apikey_row" style="<?= $MAP_PROVIDER === 'google' ? '' : 'display:none;' ?>">
            <label for="googleApiKey" class="form-label">
              Google Maps API Key
              <i class="fas fa-circle-info text-primary ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                 title="Inserisci qui la tua Google Maps API Key. Puoi ottenerla dalla Google Cloud Console. Abilita le API Maps e imposta restrizioni di sicurezza."></i>
            </label>
            <input type="text" id="googleApiKey" name="google_api_key" class="form-control" value="<?= $GOOGLE_API_KEY ?>">
            <a href="https://console.cloud.google.com/apis/credentials" target="_blank" style="font-size: 0.9em;">
              [Vai alla Google Cloud Console]
            </a>
          </div>

          <!-- MULTICOMUNE -->
          <div class="mb-3">
            <label for="multicomune" class="form-label">Gestione multicomune?</label>
            <select class="form-select" name="multicomune" id="multicomune" onchange="toggleComuneDefault()">
              <option value="si" <?= $MULTICOMUNE === 'si' ? 'selected' : '' ?>>Si</option>
              <option value="no" <?= $MULTICOMUNE === 'no' ? 'selected' : '' ?>>No</option>
            </select>
          </div>

          <!-- COMUNE DI DEFAULT -->
          <div class="mb-3" id="defaultComuneRow" style="<?= $MULTICOMUNE === 'si' ? '' : 'display:none;' ?>">
            <label for="defaultComune" class="form-label">Comune visualizzato per default</label>
            <select class="form-select" name="default_comune" id="defaultComune">
              <?php foreach ($comuni_disponibili as $comune): ?>
                <option value="<?= htmlspecialchars($comune) ?>" <?= $DEFAULT_COMUNE === $comune ? 'selected' : '' ?>>
                  <?= htmlspecialchars($comune) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
		
		<!-- VERIFICA DATI OPERATORE PRESIDENTE -->
<div class="mb-3">
  <label for="verifica_operatore" class="form-label">
    Verifica dati operatore presidente
    <i class="fas fa-circle-info text-primary ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
       title="Se SÌ, tutti i dati caricati dall'operatore presidente vengono salvati come provvisori e poi confermati da un operatore. Se NO, i dati vengono salvati direttamente come definitivi."></i>
  </label>
  <select class="form-select" name="verifica_operatore" id="verifica_operatore">
    <option value="si" <?= (isset($VERIFICA_OPERATORE) && $VERIFICA_OPERATORE === 'si') ? 'selected' : '' ?>>Si</option>
    <option value="no" <?= (isset($VERIFICA_OPERATORE) && $VERIFICA_OPERATORE === 'no') ? 'selected' : '' ?>>No</option>
  </select>
</div>

		
          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Salva</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<script>
  // Tooltip Bootstrap 5 init
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    toggleApiKeyField();
    toggleComuneDefault();
  });

  // Anteprima immagine
  document.getElementById('uploadBtn').addEventListener('click', () => {
    document.getElementById('fileInput').click();
  });

  document.getElementById('fileInput').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('previewImg').src = e.target.result;
      document.getElementById('siteImage').value = file.name;
    }
    reader.readAsDataURL(file);
  });

  // Mostra/nasconde campo API Key
  function toggleApiKeyField() {
    const provider = document.getElementById('maps_provider').value;
    document.getElementById('apikey_row').style.display = (provider === 'google') ? '' : 'none';
  }

  // Mostra/nasconde campo Comune default
  function toggleComuneDefault() {
    const multicomune = document.getElementById('multicomune').value;
    document.getElementById('defaultComuneRow').style.display = (multicomune === 'si') ? '' : 'none';
  }
</script>
