<?php
require_once '../includes/check_access.php';

$includerDir = realpath(__DIR__ . '/../includes') . '/';
$ipsAllowed = [];
$ipsBlocked = [];

// ---- FILE JSON ----
$allowedFile = $includerDir . 'ip_allowed.json';
$manualBlockedFile = $includerDir . 'ip_block_MANUAL.json';

// ---- CARICA WHITELIST ----
if(file_exists($allowedFile)) {
    $data = json_decode(@file_get_contents($allowedFile), true);
    if(is_array($data)) $ipsAllowed = $data;
}

// ---- CARICA BLACKLIST MANUALE ----
if(file_exists($manualBlockedFile)) {
    $data = json_decode(@file_get_contents($manualBlockedFile), true);
    if(is_array($data)) $ipsBlocked = $data;
}

// ---- CARICA BLACKLIST AUTOMATICA ----
foreach (glob($includerDir . 'ip_block_*.json') as $file) {
    if(basename($file) === 'ip_block_MANUAL.json') continue; // salta manuale
    $data = json_decode(@file_get_contents($file), true);
    if (!is_array($data) || !isset($data['attempts'])) continue;

    $ipsBlocked[] = [
        'file' => basename($file),
        'ip' => $data['ip'] ?? basename($file),
        'attempts' => (int)$data['attempts'],
        'last_attempt' => $data['last_attempt'] ?? 0
    ];
}

// ---- POST AJAX ----
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])){
    $resp = ['success'=>false,'msg'=>'Errore'];

    // aggiungi IP whitelist
    if($_POST['action']==='add_allowed' && !empty($_POST['ip'])){
        $ip = trim($_POST['ip']);
        if(!filter_var($ip,FILTER_VALIDATE_IP)) $resp['msg']='IP non valido';
        else {
            if(!in_array($ip,$ipsAllowed)){
                $ipsAllowed[]=$ip;
                if(file_put_contents($allowedFile,json_encode($ipsAllowed,JSON_PRETTY_PRINT))) $resp['success']=true;
                else $resp['msg']='Errore salvataggio JSON';
            } else $resp['msg']='IP già presente';
        }
    }

    // rimuovi IP whitelist
    elseif($_POST['action']==='remove_allowed' && !empty($_POST['ip'])){
        $ip = trim($_POST['ip']);
        if(($key=array_search($ip,$ipsAllowed))!==false){
            unset($ipsAllowed[$key]);
            $ipsAllowed = array_values($ipsAllowed);
            if(file_put_contents($allowedFile,json_encode($ipsAllowed,JSON_PRETTY_PRINT))) $resp['success']=true;
            else $resp['msg']='Errore salvataggio JSON';
        } else $resp['msg']='IP non trovato';
    }

    // aggiungi IP manuale blacklist
    elseif($_POST['action']==='add_blocked' && !empty($_POST['ip'])){
        $ip = trim($_POST['ip']);
        if(!filter_var($ip,FILTER_VALIDATE_IP)) $resp['msg']='IP non valido';
        else {
            $ipsBlockedManual = file_exists($manualBlockedFile) ? json_decode(file_get_contents($manualBlockedFile), true) : [];
            if(!is_array($ipsBlockedManual)) $ipsBlockedManual = [];
            if(!in_array($ip,array_column($ipsBlockedManual,'ip'))){
                $entry = ['file'=>'ip_block_MANUAL.json','ip'=>$ip,'attempts'=>0,'last_attempt'=>time()];
                $ipsBlockedManual[] = $entry;
                if(file_put_contents($manualBlockedFile,json_encode($ipsBlockedManual,JSON_PRETTY_PRINT))) $resp['success']=true;
                else $resp['msg']='Errore salvataggio JSON';
            } else $resp['msg']='IP già presente in blacklist';
        }
    }

    // rimuovi IP manuale blacklist
    elseif($_POST['action']==='remove_blocked' && !empty($_POST['ip'])){
        $ip = trim($_POST['ip']);
        $ipsBlockedManual = file_exists($manualBlockedFile) ? json_decode(file_get_contents($manualBlockedFile), true) : [];
        if(is_array($ipsBlockedManual)){
            $found=false;
            foreach($ipsBlockedManual as $k=>$v){
                if($v['ip']==$ip){
                    unset($ipsBlockedManual[$k]);
                    $found=true;
                }
            }
            $ipsBlockedManual = array_values($ipsBlockedManual);
            if($found && file_put_contents($manualBlockedFile,json_encode($ipsBlockedManual,JSON_PRETTY_PRINT))) $resp['success']=true;
            else $resp['msg']='IP non trovato';
        } else $resp['msg']='IP non trovato';
    }

    // sblocca automatic blacklist
    elseif($_POST['action']==='unblock' && !empty($_POST['file'])){
        $file = basename($_POST['file']);
        $fullPath = $includerDir . $file;
        if(file_exists($fullPath) && strpos($file,'ip_block_')===0){
            if(@unlink($fullPath)) $resp['success']=true;
            else $resp['msg']='Impossibile eliminare file';
        } else $resp['msg']='File non trovato';
    }

    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;
}
?>

<section class="content">
<div class="container-fluid">

  <!-- WHITELIST -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white"><h3 class="card-title"><i class="fas fa-check-circle"></i> Whitelist (IP Accettati)</h3></div>
    <div class="card-body">
      <div class="input-group mb-2">
        <input type="text" id="newAllowedIp" class="form-control" placeholder="Inserisci IP">
        <div class="input-group-append">
          <button class="btn btn-success" onclick="addAllowedIp()"><i class="fas fa-plus"></i> Aggiungi</button>
        </div>
      </div>
      <table class="table table-bordered table-hover mb-0">
        <thead><tr><th>IP</th><th>Azioni</th></tr></thead>
        <tbody id="allowedTableBody">
          <?php foreach($ipsAllowed as $ip): ?>
          <tr>
            <td><?= htmlspecialchars($ip) ?></td>
            <td><button class="btn btn-sm btn-danger" onclick="removeAllowedIp('<?= htmlspecialchars($ip) ?>', this)"><i class="fas fa-trash-alt"></i> Rimuovi</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- BLACKLIST -->
  <div class="card shadow-sm">
    <div class="card-header bg-danger text-white"><h3 class="card-title"><i class="fas fa-network-wired"></i> Blacklist (IP Bloccati)</h3></div>
    <div class="card-body">
      <div class="input-group mb-2">
        <input type="text" id="newBlockedIp" class="form-control" placeholder="Aggiungi IP manualmente">
        <div class="input-group-append">
          <button class="btn btn-danger" onclick="addBlockedIp()"><i class="fas fa-plus"></i> Aggiungi</button>
        </div>
      </div>
      <table class="table table-bordered table-hover mb-0">
        <thead><tr><th>IP / File</th><th>Tentativi</th><th>Ultimo tentativo</th><th>Azioni</th></tr></thead>
        <tbody id="blockedTableBody">
          <?php foreach($ipsBlocked as $info): ?>
          <tr>
            <td><?= htmlspecialchars($info['ip']) ?></td>
            <td><?= $info['attempts'] ?? 0 ?></td>
            <td><?= $info['last_attempt'] ? date('d/m/Y H:i:s',$info['last_attempt']) : '-' ?></td>
            <td>
              <?php if($info['file']==='ip_block_MANUAL.json'): ?>
              <button class="btn btn-sm btn-danger" onclick="removeBlockedIp('<?= htmlspecialchars($info['ip']) ?>', this)"><i class="fas fa-trash-alt"></i> Rimuovi</button>
              <?php else: ?>
              <button class="btn btn-sm btn-success" onclick="unblockIp('<?= htmlspecialchars($info['file']) ?>', this)"><i class="fas fa-unlock"></i> Sblocca</button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</section>

<script>
function addAllowedIp(){
    let ip = $('#newAllowedIp').val().trim();
    if(!ip){ alert('Inserisci un IP'); return; }
    $.post('ip_management.php',{action:'add_allowed', ip:ip},function(res){
        if(res.success){
            $('#allowedTableBody').append(`<tr><td>${ip}</td><td><button class="btn btn-sm btn-danger" onclick="removeAllowedIp('${ip}', this)"><i class="fas fa-trash-alt"></i> Rimuovi</button></td></tr>`);
            $('#newAllowedIp').val('');
        } else alert(res.msg||'Errore');
    },'json');
}

function removeAllowedIp(ip, btn){
    if(!confirm('Rimuovere questo IP dalla whitelist?')) return;
    $.post('ip_management.php',{action:'remove_allowed', ip:ip},function(res){
        if(res.success) $(btn).closest('tr').fadeOut(300,function(){ $(this).remove(); });
        else alert(res.msg||'Errore');
    },'json');
}

function addBlockedIp(){
    let ip = $('#newBlockedIp').val().trim();
    if(!ip){ alert('Inserisci un IP'); return; }
    $.post('ip_management.php',{action:'add_blocked', ip:ip},function(res){
        if(res.success){
            $('#blockedTableBody').append(`<tr><td>${ip}</td><td>0</td><td>-</td><td><button class="btn btn-sm btn-danger" onclick="removeBlockedIp('${ip}', this)"><i class="fas fa-trash-alt"></i> Rimuovi</button></td></tr>`);
            $('#newBlockedIp').val('');
        } else alert(res.msg||'Errore');
    },'json');
}

function removeBlockedIp(ip, btn){
    if(!confirm('Rimuovere questo IP dalla blacklist manuale?')) return;
    $.post('ip_management.php',{action:'remove_blocked', ip:ip},function(res){
        if(res.success) $(btn).closest('tr').fadeOut(300,function(){ $(this).remove(); });
        else alert(res.msg||'Errore');
    },'json');
}

function unblockIp(file, btn){
    if(!confirm('Sbloccare questo IP automatico?')) return;
    $.post('ip_management.php',{action:'unblock', file:file},function(res){
        if(res.success) $(btn).closest('tr').fadeOut(300,function(){ $(this).remove(); });
        else alert(res.msg||'Errore');
    },'json');
}
</script>
