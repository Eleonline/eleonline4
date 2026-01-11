<?php
// Imposta header HTTP corretto
header('HTTP/1.0 403 Forbidden');

// === DATI PER IL LOG ===
$data   = date('Y-m-d H:i:s');
$ip     = $_SERVER['REMOTE_ADDR'] ?? 'IP sconosciuto';
$uri    = $_SERVER['REQUEST_URI'] ?? 'URI sconosciuto';
$file   = $_SERVER['SCRIPT_FILENAME'] ?? 'File sconosciuto';
$utente = $_SESSION['username'] ?? 'Anonimo';

// Riga di log
$log = "[$data] ACCESSO NEGATO | IP: $ip | Utente: $utente | URI: $uri | File: $file" . PHP_EOL;

// Percorso file log
$logFile = __DIR__ . '/../logs/accessi_negati.log';

// Crea cartella logs se non esiste
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

// Scrittura log
file_put_contents($logFile, $log, FILE_APPEND);

// === PAGINA HTML ===
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Accesso negato</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- AdminLTE / Bootstrap -->
	<link rel="stylesheet" href="../assets/css/all.min.css" >
    <link rel="stylesheet" href="../css/adminlte.min.css" >
</head>
<body class="hold-transition login-page">

<div class="login-box">
    <div class="login-logo text-danger">
        <i class="fas fa-ban"></i>
        <b>Accesso</b> Negato
    </div>

    <div class="card">
        <div class="card-body login-card-body text-center">
            <p class="login-box-msg">
                Non hai i permessi per accedere a questa pagina.
            </p>

            <p class="text-muted small">
                L’evento è stato registrato.
            </p>
        </div>
    </div>
</div>

</body>
</html>
