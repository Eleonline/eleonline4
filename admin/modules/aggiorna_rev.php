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
$row=configurazione();
$rev_locale=$row[0]['patch'];
//$rev_locale = isset($_POST['rev_locale']) ? (int)$_POST['rev_locale'] : 0; # caricare da db
if ($stream = fopen('http://mail.eleonline.it/version4/risposta.php', 'r')) {
	$rev= stream_get_contents($stream, 7);
	fclose($stream);							
	$rev_online=substr($rev,0,7);
	$_SESSION['remoterev']=$rev_online;         
}else{
	$errmex=2;
	Header("Location: admin.php?op=aggiorna&id_cons_gen=$id_cons_gen&errmex=$errmex"); exit;
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
if (!is_dir($backupPath) && !mkdir($backupPath, 0777, true)) {
    send_output("Errore: impossibile creare cartella temporanea: $backupPath", 'error');
    exit;
}

$url = "http://mail.eleonline.it/version4/scaricarev.php?new=$rev_online&old=$rev_locale";

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
#####################
# controllo presenza aggiornadb
#$zip->locateName('entry1.txt') se non presente ritorna false
$aggiornaDb=$zip->locateName('modules/aggiornadb.php');

// ✳️ Nuovo blocco: controllo presenza aggiornadb.php
if($aggiornaDb)
	$aggiornaDbFile = 'modules/aggiornadb.php';
else
	$aggiornaDbFile='';
if (file_exists($aggiornaDbFile) && $backup_sql_confermato === -1) {
    send_output("È stato trovato uno script di aggiornamento del database. Vuoi procedere con il backup prima di eseguirlo? (Rispondere Sì o No)", 'question');
    exit; // attende risposta POST con backup_sql = 1 o 0
}
###################
$zipbak = new ZipArchive();
$filename = $backupPath . "/backup_$rev_locale.zip";

if ($zipbak->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$index=$zip->locateName('aggiorna_rev.php');
#die("TEST: $index : $aggiornaDb :");
#decommentare se funziona tutto
// for ($i = 0; $i < $zip->numFiles; $i++) {
	// if($i==$index) continue;
    // $entryName = $zip->getNameIndex($i); 
        // $relativePath = $entryName; #substr($entryName, strlen('trunk/'));
		// if(is_file("../$relativePath")) $zipbak->addFile("../$relativePath","admin/$relativePath");
		// if(is_file("../$relativePath")) $zipbak->addFile("../$relativePath","client/$relativePath");
				// send_output("Elaboro ../$relativePath", 'ok');

        // if (substr($relativePath, -1) === '/') {
            // @mkdir($extractPath . $relativePath, 0777, true);
			// send_output("Creata la dir $relativePath", 'ok');
			// if(is_dir("$relativePath") and !is_dir("../$relativePath")) {
				// mkdir("../$relativePath", 0777, true);
			// }
        // } else {
            // $dir = dirname($extractPath . $relativePath);
            // if (!is_dir($dir)) {
                // mkdir($dir, 0777, true);
            // }
            // $contents = $zip->getFromIndex($i);
            // if ($contents === false) {
                // send_output("Errore: impossibile leggere $entryName nello zip.", 'error');
                // $zip->close();
                // exit;
            // }
			// $artemp = explode( '/', $entryName );
			// array_pop( $artemp );
			// $newdir = implode( '/', $artemp );
			// if( !is_dir( $newdir ) )
				// mkdir( "../".$newdir, 0777, true );
			
// #            file_put_contents('../'.$extractPath . $relativePath, $contents);
            // file_put_contents("../$entryName", $contents);

// #			copy("$extractPath$entryName","../$entryName");
       // }
// #    }
// }

# demo sovrascrive tutti i file
for ($i = 0; $i < $zip->numFiles; $i++) {

    if($i == $index) continue;

    $entryName = $zip->getNameIndex($i);

    // SOLO LOG — NESSUNA SCRITTURA SU DISCO
    send_output("TEST MODE: file pronto -> $entryName", 'ok');

}


$zip->close();
$zipbak->close();
send_output("Estrazione file...", 'ok');

send_output("Backup dei files da aggiornare...", 'ok');

// Step 6: Verifica backup database
send_output("Verifica backup database...");
//if($aggiornaDb)
	include("includes/backupDb.php");
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
send_output("Archiviazione file temporanei...");
if(rename($tmpDir,$tmpDir."Da_".$rev_locale."_a_".$rev_online))
	send_output("Pulizia file temporanei...", 'ok');

// Step 9: Aggiorna Versione
send_output("Aggiorna Versione...");  // Step 1: messaggio iniziale
$sql="update ".$prefix."_config set patch=:patch";
	$res = $dbi->prepare("$sql");
	try {
		$res->bindParam(':patch', $rev_online,  PDO::PARAM_STR);
		$res->execute();
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
send_output("Aggiorna Versione...", 'ok');
send_output("Aggiornamento completato con successo.", 'ok');
echo "__FINISH__Aggiornamento completato con successo.\n";
exit;
?>