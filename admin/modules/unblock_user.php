<?php
session_start();
header('Content-Type: application/json');

if (!isset($_POST['username']) || empty($_POST['username'])) {
    echo json_encode(['success' => false, 'msg' => 'Username mancante']);
    exit;
}

$username = trim($_POST['username']);

// ===== FILE BLOCCO LOGIN =====
$blockFile = __DIR__ . '/../logs/login_block_' . md5($username) . '.json';
if (file_exists($blockFile)) unlink($blockFile);

// ===== AZZERAMENTO COUNTER =====
try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../config/variabili.php';

    $dsn = "mysql:host=$dbhost;dbname=$dbname;charset=utf8";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $dbi = new PDO($dsn, $dbuname, $dbpass, $opt);

    $sql = "UPDATE {$prefix}_authors SET counter = 0 WHERE BINARY aid = :aid LIMIT 1";
    $stmt = $dbi->prepare($sql);
    $stmt->execute([':aid' => $username]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
?>