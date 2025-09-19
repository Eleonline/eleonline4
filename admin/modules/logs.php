<?php
require_once '../includes/check_access.php';

$logs = [
    'access' => [
        'label' => 'Log Apache Access',
        'paths' => [
            '/var/log/apache2/access.log',
            '/var/log/httpd/access_log',
            '/opt/local/apache2/logs/access_log',
            '/Applications/MAMP/logs/apache_access.log',
            'C:\\xampp\\apache\\logs\\access.log',
        ],
    ],
    'error' => [
        'label' => 'Log Apache Error',
        'paths' => [
            '/var/log/apache2/error.log',
            '/var/log/httpd/error_log',
            '/opt/local/apache2/logs/error_log',
            '/Applications/MAMP/logs/apache_error.log',
            'C:\\xampp\\apache\\logs\\error.log',
        ],
    ],
    'php' => [
        'label' => 'Log PHP Error',
        'paths' => [
            '/var/log/php_errors.log',
            '/opt/local/var/log/php/php_error.log',
            '/Applications/MAMP/logs/php_error.log',
            'C:\\xampp\\php\\logs\\php_error_log',
            ini_get('error_log'),
        ],
    ],
];

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'access';
$filter = trim(filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
$auto = filter_input(INPUT_GET, 'auto', FILTER_VALIDATE_BOOLEAN) ?? false;

if (!isset($logs[$type])) $type = 'access';

function findLogFile(array $paths) {
    foreach ($paths as $file) {
        if ($file && file_exists($file) && is_readable($file)) {
            return $file;
        }
    }
    return null;
}

$logfile = findLogFile($logs[$type]['paths']);

$logContent = '';

if ($logfile) {
    $lines = [];
    try {
        $file = new SplFileObject($logfile, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        $startLine = max(0, $lastLine - 200);
        for ($i = $startLine; $i <= $lastLine; $i++) {
            $file->seek($i);
            $lineRaw = substr($file->current(), 0, 2000);
            if ($filter === '' || stripos($lineRaw, $filter) !== false) {
                $lineSafe = htmlspecialchars($lineRaw);

                $levelClass = '';
                $lineLower = strtolower($lineRaw);
                if (strpos($lineLower, 'error') !== false) {
                    $levelClass = 'log-error';
                } elseif (strpos($lineLower, 'warn') !== false) {
                    $levelClass = 'log-warn';
                } elseif (strpos($lineLower, 'notice') !== false || strpos($lineLower, 'info') !== false) {
                    $levelClass = 'log-info';
                }

                if ($filter !== '') {
                    $lineSafe = preg_replace('/(' . preg_quote($filter, '/') . ')/i', '<mark>$1</mark>', $lineSafe);
                }

                $lines[] = '<div class="' . $levelClass . '">' . $lineSafe . '</div>';
            }
        }
        $logContent = implode("", $lines);
        if (trim($logContent) === '') {
            $logContent = "<div>Nessuna riga trovata.</div>";
        }
    } catch (Exception $e) {
        $logContent = "<div>Errore lettura file log: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} else {
    $logContent = "<div>Nessun file di log trovato o leggibile per {$logs[$type]['label']}.</div>";
}
?>

<style>
  .log-error {
    color: #a94442;
    background-color: #f2dede;
    padding: 2px 6px;
    border-radius: 3px;
    margin-bottom: 1px;
  }
  .log-warn {
    color: #8a6d3b;
    background-color: #fcf8e3;
    padding: 2px 6px;
    border-radius: 3px;
    margin-bottom: 1px;
  }
  .log-info {
    color: #31708f;
    background-color: #d9edf7;
    padding: 2px 6px;
    border-radius: 3px;
    margin-bottom: 1px;
  }
  mark {
    background-color: yellow;
    color: black;
  }
  #loadingIndicator {
    display: none;
    font-weight: 600;
    color: #31708f;
    margin-bottom: 0.5rem;
  }
</style>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-file-alt me-2"></i>Visualizzatore Log - <?= htmlspecialchars($logs[$type]['label']) ?>
        </h3>
      </div>
      <div class="card-body">

        <ul class="nav nav-tabs mb-3">
          <?php foreach ($logs as $key => $log): ?>
            <li class="nav-item">
              <a href="?op=2&type=<?= urlencode($key) ?>&filter=<?= urlencode($filter) ?>&auto=<?= $auto ? '1' : '0' ?>"
                 class="nav-link <?= $key === $type ? 'active' : '' ?>">
                <?= htmlspecialchars($log['label']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>

        <form method="get" id="logForm" class="mb-3 d-flex flex-wrap gap-3 align-items-center">
          <input type="hidden" name="op" value="2">
          <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

          <div class="form-group mb-0 flex-grow-1" style="min-width: 200px;">
            <input type="text" name="filter" id="filterInput" placeholder="Filtro (parola chiave)" value="<?= htmlspecialchars($filter) ?>" class="form-control form-control-sm">
          </div>

          <div class="form-check form-switch ms-3">
            <input class="form-check-input" type="checkbox" name="auto" id="autoToggle" value="1" <?= $auto ? 'checked' : '' ?>>
            <label class="form-check-label" for="autoToggle">Auto-aggiorna</label>
          </div>

          <button type="submit" class="btn btn-sm btn-secondary ms-3">Aggiorna</button>
        </form>

        <div id="loadingIndicator">Caricamento...</div>

        <p><strong>File aperto:</strong> <?= $logfile ? htmlspecialchars($logfile) : 'Nessun file trovato' ?></p>

        <div id="logOutput" style="white-space: pre-wrap; font-family: monospace; max-height: 600px; overflow-y: auto; background:#f8f9fa; border:1px solid #ddd; padding:10px;">
          <?= $logContent ?>
        </div>

      </div>
      <div class="card-footer">
        <!-- Se vuoi aggiungere qualcosa qui -->
      </div>
    </div>
  </div>
</section>

<script>
  const filterInput = document.getElementById('filterInput');
  const autoCheckbox = document.getElementById('autoToggle');
  const loadingIndicator = document.getElementById('loadingIndicator');
  const logOutput = document.getElementById('logOutput');

  filterInput.addEventListener('input', () => {
    clearTimeout(filterInput._delay);
    filterInput._delay = setTimeout(() => {
      document.getElementById('logForm').submit();
    }, 600);
  });

  let autoUpdateInterval = null;

  function fetchLog() {
    if (loadingIndicator) loadingIndicator.style.display = 'block';

    const params = new URLSearchParams(window.location.search);
    fetch(window.location.pathname + '?' + params.toString())
      .then(res => res.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContent = doc.querySelector('#logOutput');
        if (newContent) {
          logOutput.innerHTML = newContent.innerHTML;
          logOutput.scrollTop = logOutput.scrollHeight;
        }
        if (loadingIndicator) loadingIndicator.style.display = 'none';
      })
      .catch(() => {
        if (loadingIndicator) loadingIndicator.style.display = 'none';
      });
  }

  function startAutoUpdate() {
    if (!autoUpdateInterval) {
      autoUpdateInterval = setInterval(fetchLog, 30000);
    }
  }

  function stopAutoUpdate() {
    if (autoUpdateInterval) {
      clearInterval(autoUpdateInterval);
      autoUpdateInterval = null;
    }
  }

  autoCheckbox.addEventListener('change', () => {
    if (autoCheckbox.checked) {
      startAutoUpdate();
    } else {
      stopAutoUpdate();
    }
  });

  if (autoCheckbox.checked) {
    startAutoUpdate();
  }
</script>
