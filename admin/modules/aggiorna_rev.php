<?php
// error_reporting(0);
// ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

set_time_limit(0);
while (ob_get_level()) ob_end_clean();

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache');
header("Content-Encoding: none");
header('X-Accel-Buffering: no'); // per nginx disabling buffer
global $dbi, $prefix;
echo str_repeat(" ", 8192);
flush();

function send_output($msg, $status = 'progress') {
    if ($status === 'ok') echo "__OK__" . $msg . "\n";
    elseif ($status === 'error') echo "__ERROR__" . $msg . "\n";
    elseif ($status === 'question') echo "__QUESTION__" . $msg . "\n";
    else echo "__STEP__" . $msg . "\n";

    if (ob_get_level()) ob_flush();
    flush();
}


set_exception_handler(function($e) {
    send_output("ERRORE CRITICO: " . $e->getMessage(), 'error');
    echo "__FINISH__AGGIORNAMENTO FALLITO\n";
    exit;
});

set_error_handler(function($errno, $errstr, $errfile, $errline) {

    // Ignora NOTICE e WARNING minori
    if ($errno === E_NOTICE || $errno === E_WARNING) {
        return true;
    }

    send_output("ERRORE PHP: $errstr in $errfile:$errline", 'error');
    echo "__FINISH__AGGIORNAMENTO FALLITO\n";
    exit;
});



// Leggi parametri POST con fallback a 0
$data_rev = isset($_POST['data_rev']) ? $_POST['data_rev'] : '';
$row=configurazione();
$rev_locale=$row[0]['patch'];
//$rev_locale = isset($_POST['rev_locale']) ? (int)$_POST['rev_locale'] : 0; # caricare da db
$ctx = stream_context_create(['http' => ['timeout' => 5]]);
$stream = @fopen('https://www.eleonline.it/rev/version4/risposta.php', 'r', false, $ctx);

if ($stream) {

	$rev= stream_get_contents($stream, 7);
	fclose($stream);							
	$rev_online=substr($rev,0,7);
	$_SESSION['remoterev']=$rev_online;         
}else{
    send_output("Errore: risposta versione non disponibile dal server.", 'error');
    echo "__FINISH__AGGIORNAMENTO FALLITO\n";
    exit;
}

$backup_sql_confermato = isset($_POST['backup_sql']) ? (int)$_POST['backup_sql'] : -1;

$tmpDir = dirname(__DIR__) . '/tmp/aggiornamento';
$zipFile = dirname(__DIR__) . '/tmp/file.zip';
$extractPath = dirname(__DIR__) . '/tmp/aggiornamento/';
$backupPath = dirname(__DIR__) . '/tmp/backup/';

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
//$testUrl = "https://trac.eleonline.it/eleonline4/";
$testUrl = "https://www.eleonline.it/rev/version4/";

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
if (!is_dir($backupPath) && !mkdir($backupPath, 0777, true)) {
    send_output("Errore: impossibile creare cartella temporanea: $backupPath", 'error');
    exit;
}

$url = "https://www.eleonline.it/rev/version4/scaricarev.php?new=$rev_online&old=$rev_locale";

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
    send_output("Errore: impossibile aprire il file ZIP (codice errore: $res).", 'error');
    exit;
}

if (!is_dir($extractPath) && !mkdir($extractPath, 0777, true)) {
    send_output("Errore: impossibile creare cartella estrazione: $extractPath", 'error');
    $zip->close();
    exit;
}

$zip->extractTo($extractPath);
$zip->close();	 

$admin =  dirname(__DIR__);
$client = dirname(__DIR__).'/../client';
$backup = $admin."/tmp/backup".$rev_locale;

recurse_copy($extractPath,$admin."/..",$backup);
send_output("Estrazione file...", 'ok');


if(file_exists("$extractPath/admin/modules/aggiornadb.php")) {
	$aggiornaDbFile = dirname(__DIR__).'/modules/aggiornadb.php';
}else $aggiornaDbFile = '';

send_output("Backup dei files da aggiornare...", 'ok');

// Step 6: Verifica backup database
send_output("Verifica backup database...");

if($aggiornaDb) {
//	if(false){
    try {
        include(dirname(__DIR__) ."/includes/backupDb.php");
    } catch (Throwable $e) {
        send_output("Backup DB fallito: ".$e->getMessage(), 'error');
		flush();
        echo "__FINISH__AGGIORNAMENTO FALLITO\n";
        exit;
    }
}

send_output("Aggiornamento database...");
// ✳️ Controllo ed esecuzione aggiornadb.php se presente
$aggiornaDbFile = dirname(__DIR__) . '/modules/aggiornadb.php';

if (file_exists($aggiornaDbFile)) {
    send_output("Trovato script di aggiornamento database: aggiornadb.php. Esecuzione in corso...");
    while (ob_get_level()) ob_end_flush(); // svuota eventuali buffer precedenti
ob_implicit_flush(true);
try {
    include $aggiornaDbFile;
	} catch (Throwable $e) {
		send_output("Aggiornamento DB fallito: ".$e->getMessage(), 'error');
		echo "__FINISH__AGGIORNAMENTO FALLITO TEST 1\n";
		exit;
	}
    send_output("Esecuzione aggiornadb.php completata.", 'ok');
} else {
    send_output("Nessuno script di aggiornamento database trovato in admin/modules/aggiornadb.php. Saltando aggiornamento database...", 'progress');
}

send_output("Aggiornamento database...", 'ok');
/*
// Step 8: Pulizia file temporanei
send_output("Archiviazione file temporanei...");
$newTmp = $tmpDir."Da_".$rev_locale."_a_".$rev_online;

if (!rename($tmpDir, $newTmp)) {
    send_output("Errore: impossibile rinominare cartella temporanea", 'error');
    echo "__FINISH__AGGIORNAMENTO FALLITO\n";
    exit;
}

send_output("Pulizia file temporanei...", 'ok');
*/

// Step 9: Aggiorna Versione
send_output("Aggiorna Versione...");  // Step 1: messaggio iniziale
$sql="update ".$prefix."_config set patch=:patch";
if (!$dbi) {
    send_output("Errore DB: connessione non inizializzata");
    echo "__FINISH__AGGIORNAMENTO FALLITO\n";
//    exit;
}
	$res = $dbi->prepare("$sql");
	try {
		$res->bindParam(':patch', $rev_online,  PDO::PARAM_STR);
		$res->execute();
	}
	catch(PDOException $e) {
 		echo $e->getMessage();
	}
send_output("Aggiorna Versione...", 'ok');
echo "__FINISH__Aggiornamento completato con successo.<br>";
echo "<br><br>";
exit;

function recurse_copy($src,$dst,$bck) {
	global $id_cons_gen;
    $dir = opendir($src);

    if(!file_exists($dst)) 
		if(mkdir($dst)==false) {
			send_output("Errore durante la creazione della directory", 'error');			exit;
		}
    if(!file_exists($bck)) 
		if(mkdir($bck,0777,true)==false) {
			send_output("Errore durante la creazione della directory", 'error');			exit;
		}
    while(false !== ( $file = readdir($dir)) ) {
#		send_output("Elaboro: $file");
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
			send_output("Apro: $src . '/' . $file");
                recurse_copy($src . '/' . $file,$dst . '/' . $file,$bck . '/' . $file);
            }
            else {
				if(file_exists($dst . '/' . $file))
					if(false===copy($dst . '/' . $file,$bck . '/' . $file)) {
					send_output("Errore durante la copia di $dst/$file");
					exit;
					}
               if(!copy($src . '/' . $file,$dst . '/' . $file)) {
					send_output("Errore durante la copia $src/$file");
					exit;
				}
            }
        }
    }
	send_output("Chiudo: $src");
    closedir($dir);
}

?>