<?php
require_once '../includes/check_access.php';

// Tema attivo di default
$DEFAULT_THEME = "bootstrap-italia";

// Cartella dei temi
$themesDir = __DIR__ . '/../../client/temi/bootstrap/themes';
$files = glob("$themesDir/tema-*.css");

$themes = [];
$paletteColors = [];

// Aggiungi tema istituzionale Bootstrap Italia manualmente PRIMA degli altri temi
$bootstrapItaliaTheme = 'bootstrap-italia';
$themes[] = $bootstrapItaliaTheme;
$paletteColors[$bootstrapItaliaTheme] = [
    '#007FFF', '#0056B3', '#003D80', '#66A3FF', '#99C2FF', '#CCE5FF'
];

// Carica i temi dalla cartella
foreach ($files as $file) {
    $theme = basename($file, '.css');
    $themes[] = $theme;

    $content = file_get_contents($file);
    preg_match('/:root\s*{([^}]*)}/', $content, $matches);

    $colors = [];
    if (isset($matches[1])) {
        preg_match_all('/:\s*(#[a-fA-F0-9]{3,6})\s*;/', $matches[1], $colorMatches);
        $colors = array_slice($colorMatches[1], 0, 6);
    }

    while (count($colors) < 6) {
        $colors[] = '#ccc';
    }

    $paletteColors[$theme] = $colors;
}
?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-palette me-2"></i>Seleziona Tema Colore</h3>
      </div>
      <div class="card-body">
        <form id="themeForm" novalidate>
          <div class="d-flex flex-wrap justify-content-start">
            <?php foreach ($themes as $theme): ?>
              <label class="theme-card mb-3 <?= $theme === $DEFAULT_THEME ? 'selected' : '' ?>" data-theme="<?= htmlspecialchars($theme) ?>" style="cursor:pointer; user-select:none;">
                <input type="radio" name="theme"
                       value="<?= $theme === $bootstrapItaliaTheme ? '0' : htmlspecialchars($theme) ?>"
                       style="display:none" <?= $theme === $DEFAULT_THEME ? 'checked' : '' ?>>

                <div class="theme-name text-center mb-2" style="font-weight:600;">
                  <?= $theme === $bootstrapItaliaTheme ? 'Istituzionale (default)' : ucfirst(str_replace('tema-', '', $theme)) ?>
                </div>

                <div class="d-flex justify-content-center gap-1">
                  <?php foreach ($paletteColors[$theme] as $color): ?>
                    <span class="palette-color" style="background-color: <?= htmlspecialchars($color) ?>"></span>
                  <?php endforeach; ?>
                </div>
              </label>
            <?php endforeach; ?>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">Salva</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<style>
  .theme-card {
    border: 2px solid transparent;
    border-radius: 5px;
    padding: 12px 16px;
    width: 180px;
    box-shadow: 0 0 8px #ccc;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }
  .theme-card.selected {
    border-color: #007BFF;
    box-shadow: 0 0 12px #007BFF;
  }
  .palette-color {
    width: 30px;
    height: 30px;
    border-radius: 3px;
    display: inline-block;
  }
</style>

<script>
  // Selezione tema visiva
  document.querySelectorAll('.theme-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.theme-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      card.querySelector('input[type="radio"]').checked = true;
    });
  });

  document.getElementById('themeForm').addEventListener('submit', e => {
    e.preventDefault();
    const selectedTheme = document.querySelector('input[name="theme"]:checked').value;
    alert('Tema selezionato: ' + (selectedTheme === '0' ? 'Istituzionale (default)' : selectedTheme));
    // Qui puoi aggiungere chiamata ajax o submit vero al backend
  });
</script>
