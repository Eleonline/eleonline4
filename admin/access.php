<?php
// Rileva se siamo in HTTPS
$is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

// Setta i cookie della sessione in modo adattivo (HTTPS o HTTP)
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '', // usa il tuo dominio se in produzione
    'secure' => $is_https, // sicuro solo su HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// Timeout inattività: 24h
$timeout = 86400;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header('Location: ../login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

// Regenera ID sessione ogni 5 min
if (!isset($_SESSION['regen']) || $_SESSION['regen'] < time() - 300) {
    session_regenerate_id(true);
    $_SESSION['regen'] = time();
}

// Fingerprinting per sicurezza IP e User Agent
$fingerprint = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
if (!isset($_SESSION['fingerprint'])) {
    $_SESSION['fingerprint'] = $fingerprint;
} elseif ($_SESSION['fingerprint'] !== $fingerprint) {
    session_unset();
    session_destroy();
    header('Location: ../login.php?timeout=1');
    exit;
}

// Ruoli permessi (default se non impostato da pagina)
if (!isset($allowed_roles)) {
    $allowed_roles = ['superuser', 'admin', 'operatore'];
}

// Controllo autenticazione
if (!isset($_SESSION['username']) || !isset($_SESSION['ruolo'])) {
    header('Location: ../login.php');
    exit;
}

// Controllo autorizzazione
if (!in_array($_SESSION['ruolo'], $allowed_roles, true)) {
    header('HTTP/1.0 403 Forbidden');
    exit('Accesso negato: permessi insufficienti.');
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function checkCsrfToken($token) {
    if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        header('HTTP/1.0 403 Forbidden');
        exit('Token CSRF non valido.');
    }
}

// Header di protezione (solo HSTS disattivo in HTTP)
header('X-Frame-Options: DENY');
header("Content-Security-Policy: frame-ancestors 'none';");
if ($is_https) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>
