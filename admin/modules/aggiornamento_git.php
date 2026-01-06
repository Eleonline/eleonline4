<?php
require_once '../includes/check_access.php';

define('ELEONLINE_VERSION', '1.0.0');

$repo   = 'Alexc-1/eleonline-update-test';
$branch = 'main';
$baseUrl = "https://raw.githubusercontent.com/$repo/$branch/";

// ROOT = cartella principale (admin → ../../)
$rootDir    = realpath(__DIR__ . '/../../');
$backupsDir = $rootDir . '/backups';
$updatesDir = $rootDir . '/updates';

/* =======================
   FUNZIONI
======================= */

function curl_get($url){
    $ch = curl_init($url);
    curl_setopt_array($ch,[
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_FOLLOWLOCATION=>true,
        CURLOPT_USERAGENT=>'Eleonline-Updater'
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function recurse_copy($src,$dst,$exclude=[],$diff=false,&$count=0){
    @mkdir($dst,0777,true);
    foreach(scandir($src) as $f){
        if($f=='.'||$f=='..') continue;
        if(in_array($f,$exclude)) continue;
        $s="$src/$f"; $d="$dst/$f";

        if(is_dir($s)){
            recurse_copy($s,$d,$exclude,$diff,$count);
        } else {
            if($diff && file_exists($d) && md5_file($s)===md5_file($d)) continue;
            copy($s,$d);
            $count++;
        }
    }
}

function step($msg,$type='info'){
    $icon = match($type){
        'ok'=>'✅','err'=>'❌','warn'=>'⚠️',default=>'➡️'
    };
    echo "<div>$icon $msg</div>";
    echo "<script>updateProgress();</script>";
    @ob_flush(); @flush();
    usleep(500000); // mezzo secondo per effetto visivo
}

/* =======================
   VERSIONE
======================= */

$remoteVersion = trim(curl_get($baseUrl.'VERSION'));
$updateAvailable = $remoteVersion && version_compare($remoteVersion, ELEONLINE_VERSION, '>');
$changelog = curl_get($baseUrl . 'changelog.txt');
$run      = isset($_GET['run']);
$rollback = isset($_GET['rollback']);
$diff     = isset($_GET['diff']);
$updateMessage = '';

/* =======================
   ROLLBACK
======================= */
if($rollback){
    echo "<section class='content'><div class='container-fluid mt-3'>";
    echo "<h2>Ripristino Backup</h2>";
    $backups = glob($backupsDir.'/backup_*');
    rsort($backups);
    if(empty($backups)){
        echo "<div class='alert alert-danger'>Nessun backup trovato!</div>";
    } else {
        $lastBackup = $backups[0];
        $count = 0;
        step("Ripristino dal backup: $lastBackup");
        recurse_copy($lastBackup,$rootDir,['backups','updates','.git'],$diff,$count);
        echo "<div class='alert alert-success'>✅ Ripristino completato: $count file</div>";
    }
    echo "</div></section>";
    exit;
}
?>

<section class="content">
<div class="container-fluid mt-3">

<h2><i class="fas fa-download mr-2"></i>Aggiornamento Eleonline</h2>

<?php if(!$run): ?>

<!-- ================= SCHERMATA NORMALE ================= -->
<div class="card">
  <div class="card-body">
    <p><b>Versione installata:</b> <?= ELEONLINE_VERSION ?></p>
    <p><b>Versione disponibile:</b> <?= $remoteVersion ?: 'Errore GitHub' ?></p>

    <?php if(!empty($changelog)): ?>
      <hr>
      <h5><i class="fas fa-list mr-1"></i> Changelog</h5>
      <pre style="background:#f4f6f9;padding:10px;border-radius:5px;"><?= htmlspecialchars($changelog) ?></pre>
    <?php endif; ?>

    <?php if($updateAvailable): ?>
      <div class="alert alert-warning">Aggiornamento disponibile</div>
      <a href="?op=7&run=1<?= $diff?'&diff=1':'' ?>" class="btn btn-primary">
        <i class="fas fa-play"></i> Avvia aggiornamento
      </a>
      <a href="?op=7&rollback=1" class="btn btn-danger">Ripristina Backup</a>
    <?php else: ?>
      <div class="alert alert-success">Sistema aggiornato</div>
    <?php endif; ?>
  </div>
</div>

<?php else: ?>

<!-- ================= MODALITÀ ESECUZIONE ================= -->
<div class="card">
  <div class="card-header bg-primary text-white">
    <h3 class="card-title"><i class="fas fa-sync-alt fa-spin"></i> Aggiornamento in corso…</h3>
  </div>
  <div class="card-body" style="font-family:monospace">
    <div class="progress mb-2">
      <div class="progress-bar" id="progress-bar" style="width:0%">0%</div>
    </div>
    <?php
    set_time_limit(0);
    ob_implicit_flush(true);
    ob_end_flush();

    $steps = [];

    $steps[] = function() use($backupsDir,$rootDir,$diff,&$backupFileCount){
        if(!is_dir($backupsDir)){ mkdir($backupsDir,0777,true); step("Creata cartella backups",'ok'); }
        $backupDir = $backupsDir.'/backup_'.date('Ymd_His');
        $backupFileCount = 0;
        recurse_copy($rootDir,$backupDir,['backups','updates','.git'],$diff,$backupFileCount);
        step("Backup completato: $backupFileCount file",'ok');
        return $backupDir;
    };

    $steps[] = function($backupDir=null) use($updatesDir,$baseUrl,$rootDir,&$updatedFileCount){
        if(!is_dir($updatesDir)){ mkdir($updatesDir,0777,true); step("Creata cartella updates",'ok'); }
        $zipUrl  = $baseUrl.'update.zip';
        $zipFile = $updatesDir.'/update.zip';

        step("Download aggiornamento...");
        $ch = curl_init($zipUrl);
        $fp = fopen($zipFile,'w');
        curl_setopt_array($ch,[
            CURLOPT_FILE=>$fp,
            CURLOPT_FOLLOWLOCATION=>true,
            CURLOPT_USERAGENT=>'Eleonline-Updater',
            CURLOPT_FAILONERROR=>true
        ]);
        $ok = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);
        fclose($fp);

        if(!$ok){ step("Errore download: $curlErr",'err'); return false; }
        step("Download completato",'ok');

        step("Estrazione file...");
        $zip = new ZipArchive;
        $updatedFileCount = 0;
        if($zip->open($zipFile)===TRUE){
            for($i=0;$i<$zip->numFiles;$i++){
                $filename = $zip->getNameIndex($i);
                if($zip->extractTo($rootDir, $filename)) $updatedFileCount++;
            }
            $zip->close();
            step("Aggiornamento applicato: $updatedFileCount file",'ok');
            return true;
        }else{
            step("ZIP non valido",'err');
            return false;
        }
    };

    $backupDir = null;
    $i=0;
    foreach($steps as $fn){
        $res = $i===0 ? $fn() : $fn($backupDir);
        if($res===false){ 
            echo "<div class='alert alert-danger'>‼️ Aggiornamento interrotto</div>";
            echo "<a href='?op=7&rollback=1' class='btn btn-danger'>Ripristina Backup</a>";
            break;
        }
        $i++;
        echo "<script>document.getElementById('progress-bar').style.width='".($i/count($steps)*100)."%';
              document.getElementById('progress-bar').innerText='".($i/count($steps)*100)."%';</script>";
    }

    echo "<hr><b>✅ Aggiornamento terminato</b>";
    ?>
  </div>
</div>

<?php endif; ?>

</div>

<script>
function updateProgress(){
    // placeholder per eventuali animazioni JS
}
</script>
