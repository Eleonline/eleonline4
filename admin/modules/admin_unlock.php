<?php
require_once '../includes/check_access.php'; // serve solo per la pagina

$logDir = realpath(__DIR__ . '/../logs') . '/';
$ipsBlocked = [];

// Carica i file bloccati
foreach (glob($logDir . 'ip_block_*.json') as $file) {
    $data = json_decode(@file_get_contents($file), true);
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
        <?php if (empty($ipsBlocked)): ?>
          <div class="alert alert-success mb-0">
            Nessun IP bloccato al momento.
          </div>
        <?php else: ?>
          <table class="table table-bordered table-hover mb-0">
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
              <tr>
                <td><?= htmlspecialchars($info['ip']) ?></td>
                <td><?= $info['attempts'] ?></td>
                <td><?= $info['last_attempt'] ? date('d/m/Y H:i:s', $info['last_attempt']) : '-' ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-success"
                          onclick="unblockIp('<?= htmlspecialchars($info['file']) ?>', this)">
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
function unblockIp(file, btn) {

    if (!confirm('Sbloccare questo IP?')) return;

    $.ajax({
        url: 'unblock_ip.php',
        type: 'POST',
        data: { file: file },
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(res) {
            if(res.success) {
                $(btn).closest('tr').fadeOut(300,function(){ $(this).remove(); });
            } else {
                alert(res.msg || 'Errore durante lo sblocco');
            }
        },
        error: function(xhr, status, error){
            console.error('AJAX ERROR', xhr.status, xhr.responseText);
            alert('Errore AJAX: ' + xhr.status);
        }
    });
}
</script>
