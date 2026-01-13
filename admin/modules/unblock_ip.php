<?php
// Risposta JSON sempre
header('Content-Type: application/json');

$logDir = realpath(__DIR__.'/../logs') . '/';
$file = basename($_POST['file'] ?? '');
$fullPath = $logDir . $file;

// Sicurezza: solo file ip_block_*
if (strpos($file, 'ip_block_') !== 0) {
    echo json_encode(['success'=>false,'msg'=>'File non valido']);
    exit;
}
if (!is_file($fullPath)) {
    echo json_encode(['success'=>false,'msg'=>'File non trovato']);
    exit;
}
if (!is_writable($fullPath)) {
    echo json_encode(['success'=>false,'msg'=>'Permessi insufficienti']);
    exit;
}

// Cancella file
if (unlink($fullPath)) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'msg'=>'Errore durante la cancellazione']);
}
?>