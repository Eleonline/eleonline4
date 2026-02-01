<?php
// =======================================
// Bridge streaming per aggiornamento DB
// Compatibile Eleonline4 legacy
// Non modifica aggiornadbTo4.php
// =======================================

// Disabilita buffering PHP
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(true);

// =======================================
// Include config SENZA session_start (evita warning)
// =======================================

define('NO_SESSION', true);  // segnala a config.php di non avviare sessione
require_once __DIR__ . '/../config/config.php';

// =======================================
// Alias legacy richiesti da aggiornadbTo4.php
// =======================================
global $pdo, $prefix, $dbname;
$dbi = $pdo;

// Verifica connessione
if (!$dbi) {
    echo "ERRORE: Connessione DB NON inizializzata\n";
    exit;
}

// =======================================
// Inizio streaming log
// =======================================
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache');
header("Content-Encoding: none");


// Kick iniziale per flush immediato browser
echo str_repeat(' ', 2048);
flush();

// Funzione comoda per log live
function send_output($msg) {
    echo $msg . "\n";
    flush();
    if (ob_get_length()) ob_flush();
}

// =======================================
// Percorso script legacy
// =======================================
$aggiornaDbFile = __DIR__ . '/../includes/aggiornadbTo4.php';

send_output("=== AVVIO AGGIORNAMENTO DATABASE ===");

if (file_exists($aggiornaDbFile)) {

    send_output("Script trovato: aggiornadbTo4.php");
    send_output("Esecuzione in corso...\n");

    require_once $aggiornaDbFile;

    send_output("\n=== AGGIORNAMENTO DATABASE COMPLETATO ===");

} else {

    send_output("ERRORE: aggiornadbTo4.php NON trovato");
    send_output($aggiornaDbFile);

}
