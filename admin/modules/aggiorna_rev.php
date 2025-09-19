<?php
set_time_limit(0);
while (ob_get_level()) ob_end_clean();

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no'); // per nginx disabling buffer

function send_output($msg, $status = 'progress') {
    if ($status === 'ok') {
        echo "__OK__" . $msg . "\n";
    } elseif ($status === 'error') {
        echo "__ERROR__" . $msg . "\n";
    } elseif ($status === 'question') {
        echo "__QUESTION__" . $msg . "\n";
    } else {
        echo "__STEP__" . $msg . "\n";
    }
    @ob_flush();
    @flush();
}

// Leggi parametri POST con fallback a 0
$data_rev = isset($_POST['data_rev']) ? $_POST['data_rev'] : '';
$rev_locale = isset($_POST['rev_locale']) ? (int)$_POST['rev_locale'] : 0;
$rev_online = isset($_POST['rev_online']) ? (int)$_POST['rev_online'] : 0;
$rev_successivo = $rev_locale + 1;

$backup_sql_confermato = isset($_POST['backup_sql']) ? (int)$_POST['backup_sql'] : -1;

$tmpDir = dirname(__DIR__) . '/tmp/aggiornamento';
$zipFile = $tmpDir . '/file.zip';
$extractPath = $tmpDir . '/';

// Passi aggiornamento (solo per riferimento)
$steps = [
    "Connessione al server di aggiornamento...",
    "Verifica versione corrente...",
    "Download pacchetto aggiornamento (file.zip)...",
    "Estrazione file...",
    "Backup configurazioni in corso...",
    "Verifica backup database...",
    "Aggiornamento database...",
    "Pulizia file temporanei..."
];

// Step 1: Connessione reale al server di aggiornamento
send_output("Connessione al server di aggiornamento...");
$testUrl = "https://trac.eleonline.it/eleonline4/";

$ctx = stream_context_create(['http' => ['timeout' => 5]]);
$fp = @fopen($testUrl, 'r', false, $ctx);

if (!$fp) {
    send_output("Errore: impossibile connettersi al server di aggiornamento.", 'error');
    exit;
}
fclose($fp);
send_output("Connessione al server di aggiornamento...", 'ok');

// Step 3: Download pacchetto zip
send_output("Download pacchetto aggiornamento (file.zip)...");

if (!is_dir($tmpDir) && !mkdir($tmpDir, 0777, true)) {
    send_output("Errore: impossibile creare cartella temporanea: $tmpDir", 'error');
    exit;
}

$url = "https://trac.eleonline.it/eleonline4/changeset?format=zip&new={$rev_online}&new_path=%2Ftrunk&old={$rev_successivo}&old_path=%2Ftrunk";

send_output("Scaricamento da: $url");

$ctx = stream_context_create(['http' => ['timeout' => 30]]);
$fp = @fopen($url, 'r', false, $ctx);
if (!$fp) {
    send_output("Errore: impossibile aprire URL per download.", 'error');
    exit;
}

$out = @fopen($zipFile, 'w');
if (!$out) {
    send_output("Errore: impossibile aprire file ZIP per scrittura: $zipFile", 'error');
    fclose($fp);
    exit;
}

while (!feof($fp)) {
    $data = fread($fp, 8192);
    if ($data === false) {
        send_output("Errore: problema durante la lettura del flusso di download.", 'error');
        fclose($fp);
        fclose($out);
        exit;
    }
    if (fwrite($out, $data) === false) {
        send_output("Errore: problema durante la scrittura nel file ZIP.", 'error');
        fclose($fp);
        fclose($out);
        exit;
    }
}
fclose($fp);
fclose($out);

send_output("Download pacchetto aggiornamento (file.zip)...", 'ok');

// Step 4: Estrazione zip
send_output("Estrazione file...");

$zip = new ZipArchive;
$res = $zip->open($zipFile);
if ($res !== TRUE) {
    send_output("Errore: impossibile aprire file ZIP (codice errore: $res).", 'error');
    exit;
}

if (!is_dir($extractPath) && !mkdir($extractPath, 0777, true)) {
    send_output("Errore: impossibile creare cartella estrazione: $extractPath", 'error');
    $zip->close();
    exit;
}

for ($i = 0; $i < $zip->numFiles; $i++) {
    $entryName = $zip->getNameIndex($i);

    if (strpos($entryName, 'trunk/') === 0) {
        $relativePath = substr($entryName, strlen('trunk/'));

        if (substr($relativePath, -1) === '/') {
            @mkdir($extractPath . $relativePath, 0777, true);
        } else {
            $dir = dirname($extractPath . $relativePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $contents = $zip->getFromIndex($i);
            if ($contents === false) {
                send_output("Errore: impossibile leggere $entryName nello zip.", 'error');
                $zip->close();
                exit;
            }
            file_put_contents($extractPath . $relativePath, $contents);
        }
    }
}
$zip->close();
send_output("Estrazione file...", 'ok');

// Step 5: Backup configurazioni
send_output("Backup configurazioni in corso...");

$origineVariabili = dirname(__DIR__) . '/tmp/aggiornamento/admin/config/variabili.php';
$backupDir = dirname(__DIR__) . '/backup/admin/config';
$nomeBackup = 'variabili_' . date('Ymd_His') . '.php';
$destinazioneVariabili = $backupDir . '/' . $nomeBackup;

if (!file_exists($origineVariabili)) {
    send_output("ERRORE: File origine non trovato: $origineVariabili", 'error');
    exit;
}

if (!is_dir($backupDir) && !mkdir($backupDir, 0777, true)) {
    send_output("ERRORE: Impossibile creare cartella backup: $backupDir", 'error');
    exit;
}

if (!copy($origineVariabili, $destinazioneVariabili)) {
    send_output("ERRORE: Impossibile copiare backup variabili.", 'error');
    exit;
}
send_output("Backup configurazioni in corso...", 'ok');

// ✳️ Nuovo blocco: controllo presenza aggiornadb.php
$aggiornaDbFile = $extractPath . 'admin/aggiornadb.php';

if (file_exists($aggiornaDbFile) && $backup_sql_confermato === -1) {
    send_output("È stato trovato uno script di aggiornamento del database. Vuoi procedere con il backup prima di eseguirlo? (Rispondere Sì o No)", 'question');
    exit; // attende risposta POST con backup_sql = 1 o 0
}

// Step 6: Verifica backup database
send_output("Verifica backup database...");

// Backup database: logica eventualmente da scommentare
/*
if ($backup_sql_confermato != 1) {
    send_output("ATTENZIONE: Backup database non confermato. Saltando aggiornamento database.", 'error');
    $salta_aggiornamento_db = true;
} else {
    send_output("Backup database confermato.", 'ok');
    $salta_aggiornamento_db = false;
}
*/

// Step 7: Aggiornamento database (simulazione)
/*
if (!isset($salta_aggiornamento_db) || $salta_aggiornamento_db === false) {
    send_output("Aggiornamento database...");
    // Qui inserire codice reale di aggiornamento DB
    sleep(3); // simulazione
    send_output("Aggiornamento database...", 'ok');
} else {
    send_output("Aggiornamento database saltato.", 'progress');
}
*/
send_output("Aggiornamento database...");
// ✳️ Controllo ed esecuzione aggiornadb.php se presente
$aggiornaDbFile = $extractPath . 'admin/modules/aggiornadb.php';

if (file_exists($aggiornaDbFile)) {
    send_output("Trovato script di aggiornamento database: aggiornadb.php. Esecuzione in corso...");
    while (ob_get_level()) ob_end_flush(); // svuota eventuali buffer precedenti
ob_implicit_flush(true);
	include $aggiornaDbFile;
    send_output("Esecuzione aggiornadb.php completata.", 'ok');
} else {
    send_output("Nessuno script di aggiornamento database trovato in admin/modules/aggiornadb.php. Saltando aggiornamento database...", 'progress');
}

send_output("Aggiornamento database...", 'ok');

// Step 8: Pulizia file temporanei
send_output("Pulizia file temporanei...");
sleep(1);
send_output("Pulizia file temporanei...", 'ok');

// Step 9: Aggiorna Versione
send_output("Aggiorna Versione...");  // Step 1: messaggio iniziale

$versionFile = __DIR__ . '/../includes/versione.php';
//send_output("Path file: $versionFile");  // Step 2: verifica percorso

if (!file_exists($versionFile)) {
    send_output("ERRORE: File non trovato!", 'error');
    exit;
}
//send_output("File trovato.");  // Step 3: il file esiste

$contenuto = file_get_contents($versionFile);
//send_output("Contenuto letto.");  // Step 4: lettura riuscita

// Verifica contenuto
if (strpos($contenuto, '$versione') === false) {
    send_output("ATTENZIONE: stringa \$versione non trovata nel file!", 'warning');
}
if (strpos($contenuto, '$datarel') === false) {
    send_output("ATTENZIONE: stringa \$datarel non trovata nel file!", 'warning');
}


// Esegui sostituzioni
$contenuto = preg_replace('/\$versione\s*=\s*".*?";/', '$versione = "3.0 rev ' . $rev_online . '";', $contenuto);
$contenuto = preg_replace('/\$datarel\s*=\s*".*?";/', '$datarel = "' . $data_rev . '";', $contenuto);

// Scrittura
if (file_put_contents($versionFile, $contenuto) === false) {
    send_output("ERRORE: Scrittura fallita!", 'error');
    exit;
}
send_output("Aggiorna Versione...", 'ok');
send_output("Aggiornamento completato con successo.", 'ok');
echo "__FINISH__Aggiornamento completato con successo.\n";
exit;
?>