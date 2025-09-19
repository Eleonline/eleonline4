<?php
// Disabilita buffering di output PHP
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(true);

// Forza il browser o il terminale a ricevere subito qualcosa
echo str_repeat(' ', 2048);
flush();

// Funzione per stampare immediatamente
function send_output($msg, $type = 'info') {
    echo $msg . "\n";
    flush();
    if (ob_get_length()) ob_flush();
}

// Percorso allo script di aggiornamento
$aggiornaDbFile = __DIR__ . '/aggiornadb.php';

if (file_exists($aggiornaDbFile)) {
    send_output("Trovato script di aggiornamento database: aggiornadb.php. Esecuzione in corso...");
    include $aggiornaDbFile;
    send_output("Esecuzione aggiornadb.php completata.");
    send_output("Aggiornamento DB completato con successo.");
} else {
    send_output("Nessuno script di aggiornamento database trovato in admin/modules/aggiornadb.php. Saltando aggiornamento database...");
}
?>
