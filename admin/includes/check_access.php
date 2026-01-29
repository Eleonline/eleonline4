<?php
// ================= PROTEZIONE CENTRALE ELEONLINE =================

// Avvia sessione se non attiva
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Blocco accesso diretto file PHP non bootstrap
if (!defined('APP_RUNNING')) {
    header("HTTP/1.0 403 Forbidden");
    require_once __DIR__ . '/accesso_negato.php';
    exit;
}

// Controllo autenticazione utente
if (
    empty($_SESSION['username']) ||
    empty($_SESSION['ruolo'])
) {
    header("HTTP/1.0 403 Forbidden");
    require_once __DIR__ . '/accesso_negato.php';
    exit;
}

// ===============================================================
?>
