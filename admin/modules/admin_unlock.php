<?php
require_once '../includes/check_access.php';

/* =========================
   CARTELLA LOG
   ========================= */
$logDir = __DIR__ . '/../logs/';

/* =========================
   SBLOCCO IP (AJAX)
   ========================= */
if (isset($_POST['unlock'])) {
    $file = basename($_POST['unlock']); // sicurezza
    $fullPath = $logDir . $file;

    if (is_file($fullPath) && strpos($file, 'ip_block_') === 0) {
        if (unlink($fullPath)) {
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Impossibile cancellare il file, controlla i permessi.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'File non trovato']);
    }
    exit;
}

/* =========================
   CARICAMENTO IP BLOCCATI
   ========================= */
$ipsBlocked = [];
foreach (glob($logDir . 'ip_block_*.json') as $file) {
    $data = json_decode(file_get_contents($file), true);
    if (!is_array($data) || !isset($data['attempts'])) continue;

    $ipsBlocked[] = [
        'file' => basename($file),
        'ip' => $data['ip'] ?? basename($file),
        'attempts' => (int)$data['attempts'],
        'last_attempt' => $data['last_attempt'] ?? 0
    ];
}
?>

<section class="content">
  <div class="container-fluid">
    <h2><i class="fas fa-network-wired"></i> IP Bloccati</h2>
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Elenco IP Bloccati</h3>
      </div>
      <div class="card-body table-responsive">
        <?php if (count($ipsBlocked) === 0): ?>
          <div class="alert alert-success mb-0">Nessun IP bloccato al momento.</div>
        <?php else: ?>
          <table class="table table-bordered table-hover mb-0" id="ipTable">
            <thead>
              <tr>
                <th>IP / Hash</th>
                <th>Tentativi</th>
                <th>Ultimo tentativo</th>
                <th>Azioni</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ipsBlocked as $info): ?>
              <tr data-file="<?= htmlspecialchars($info['file']) ?>">
                <td><?= htmlspecialchars($info['ip']) ?></td>
                <td><?= $info['attempts'] ?></td>
                <td><?= $info['last_attempt'] ? date('d/m/Y H:i:s', $info['last_attempt']) : '-' ?></td>
                <td>
                  <button class="btn btn-sm btn-success unlockBtn">
                    <i class="fas fa-unlock"></i> Sblocca
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<script>
$(document).ready(function() {
    $('.unlockBtn').click(function() {
        if(!confirm('Sbloccare questo IP?')) return;

        let row = $(this).closest('tr');
        let file = row.data('file');

        $.post('<?= basename(__FILE__) ?>', {unlock: file}, function(response) {
            try {
                let res = JSON.parse(response);
                if(res.status === 'ok') {
                    row.fadeOut(300, function(){ $(this).remove(); });
                } else {
                    alert('Errore: ' + (res.msg || 'unknown'));
                }
            } catch(e) {
                alert('Risposta non valida dal server');
            }
        });
    });
});
</script>
