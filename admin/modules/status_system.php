<?php require_once '../includes/check_access.php'; ?>
<?php require_once '../includes/versione.php'; ?>

<?php
// Funzioni e variabili come da te scritte (rimangono uguali)
function getOsInfo() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $info = shell_exec('systeminfo');
        if ($info) {
            if (preg_match('/OS Name:\s+(.+)/i', $info, $m)) {
                $osName = trim($m[1]);
            } else {
                $osName = 'Windows';
            }
            if (preg_match('/OS Version:\s+(.+)/i', $info, $m)) {
                $osVersion = trim($m[1]);
            } else {
                $osVersion = '';
            }
            return $osName . ' ' . $osVersion;
        }
        return 'Windows (info non disponibile)';
    } else {
        if (is_readable('/etc/os-release')) {
            $content = file_get_contents('/etc/os-release');
            if (preg_match('/PRETTY_NAME="(.+)"/', $content, $matches)) {
                return $matches[1];
            }
        }
        return php_uname('s') . ' ' . php_uname('r');
    }
}

function getUptime() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $output = shell_exec('wmic os get LastBootUpTime /value');
        if ($output && preg_match('/LastBootUpTime=([0-9]+)\.?\d*\+?\d*/', $output, $matches)) {
            $boot = $matches[1];
            $bootTime = DateTime::createFromFormat('YmdHis', substr($boot, 0, 14));
            if ($bootTime) {
                $uptimeSec = time() - $bootTime->getTimestamp();
                return formatUptime($uptimeSec);
            }
        }
        return 'Non disponibile';
    } else {
        if (is_readable('/proc/uptime')) {
            $uptimeRaw = file_get_contents('/proc/uptime');
            $seconds = (int)explode(' ', $uptimeRaw)[0];
            return formatUptime($seconds);
        }
        return 'Non disponibile';
    }
}

function getCpuLoad() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $cmd = 'powershell -Command "Get-CimInstance Win32_Processor | Select-Object -ExpandProperty LoadPercentage"';
        $output = shell_exec($cmd);
        if ($output !== null) {
            $cpuLoad = intval(trim($output));
            if ($cpuLoad >= 0 && $cpuLoad <= 100) {
                return $cpuLoad . '%';
            }
        }
        return 'Non disponibile';
    } else {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $cores = (int)shell_exec("nproc") ?: 1;
            $percent = ($load[0] / $cores) * 100;
            return round($percent, 1) . '%';
        }
        return 'Non disponibile';
    }
}

function formatUptime($seconds) {
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    return "{$days} giorni, {$hours} ore, {$minutes} min";
}

function getMemoryUsage() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $output = shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value');
        if ($output) {
            preg_match('/FreePhysicalMemory=(\d+)/', $output, $free);
            preg_match('/TotalVisibleMemorySize=(\d+)/', $output, $total);
            if ($free && $total) {
                $freeKB = (int)$free[1];
                $totalKB = (int)$total[1];
                $usedKB = $totalKB - $freeKB;
                return [
                    'total' => round($totalKB / 1024, 1),
                    'used' => round($usedKB / 1024, 1),
                    'percent' => round(($usedKB / $totalKB) * 100, 1)
                ];
            }
        }
        return null;
    } else {
        if (is_readable('/proc/meminfo')) {
            $data = file_get_contents('/proc/meminfo');
            preg_match_all('/^(\w+):\s+(\d+) kB$/m', $data, $matches, PREG_SET_ORDER);
            $memTotal = $memFree = $buffers = $cached = 0;
            foreach ($matches as $m) {
                switch ($m[1]) {
                    case 'MemTotal': $memTotal = (int)$m[2]; break;
                    case 'MemFree': $memFree = (int)$m[2]; break;
                    case 'Buffers': $buffers = (int)$m[2]; break;
                    case 'Cached': $cached = (int)$m[2]; break;
                }
            }
            $used = $memTotal - $memFree - $buffers - $cached;
            return [
                'total' => round($memTotal / 1024, 1),
                'used' => round($used / 1024, 1),
                'percent' => round(($used / $memTotal) * 100, 1)
            ];
        }
        return null;
    }
}

function getDiskUsage() {
    $total = disk_total_space("/");
    $free = disk_free_space("/");
    if ($total === false || $free === false) return null;
    $used = $total - $free;
    return [
        'total' => round($total / 1073741824, 2),
        'used' => round($used / 1073741824, 2),
        'percent' => round(($used / $total) * 100, 1)
    ];
}

$phpVersion = phpversion();
$apacheVersion = function_exists('apache_get_version') ? apache_get_version() : 'Non disponibile';

$osInfo = getOsInfo();
$uptime = getUptime();
$memory = getMemoryUsage();
$disk = getDiskUsage();
$cpuLoad = getCpuLoad();
?>

<section class="content">
  <div class="container-fluid">
    <div class="card shadow-sm">
      <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fas fa-server me-2"></i> Stato Sistema</h3>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-4"><i class="fas fa-server me-1"></i> Sistema Operativo</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($osInfo) ?></dd>

          <dt class="col-sm-4"><i class="fab fa-php me-1"></i> Versione PHP</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($phpVersion) ?></dd>

          <dt class="col-sm-4"><i class="fas fa-code-branch me-1"></i> Versione Apache</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($apacheVersion) ?></dd>

          <dt class="col-sm-4"><i class="fas fa-clock me-1"></i> Uptime Server</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($uptime) ?></dd>

          <dt class="col-sm-4"><i class="fas fa-memory me-1"></i> Memoria Usata</dt>
          <dd class="col-sm-8">
            <?php if ($memory): ?>
              <?= $memory['used'] ?> / <?= $memory['total'] ?> MB (<?= $memory['percent'] ?>%)
            <?php else: ?>
              Non disponibile
            <?php endif; ?>
          </dd>

          <dt class="col-sm-4"><i class="fas fa-hdd me-1"></i> Spazio Disco Usato</dt>
          <dd class="col-sm-8">
            <?php if ($disk): ?>
              <?= $disk['used'] ?> / <?= $disk['total'] ?> GB (<?= $disk['percent'] ?>%)
            <?php else: ?>
              Non disponibile
            <?php endif; ?>
          </dd>

          <dt class="col-sm-4"><i class="fas fa-microchip me-1"></i> Carico CPU (1 min)</dt>
          <dd class="col-sm-8"><?= htmlspecialchars($cpuLoad) ?></dd>

          <dt class="col-sm-4"><i class="fas fa-layer-group me-1"></i> Versione AdminLTE</dt>
          <dd class="col-sm-8">3.2</dd>

          <dt class="col-sm-4"><i class="fas fa-info-circle me-1"></i> Versione Eleonline</dt>
          <dd class="col-sm-8"><?= $version ?? 'Non definita' ?></dd>
        </dl>
      </div>
    </div>
  </div>
</section>
