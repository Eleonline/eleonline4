<?php
/*
require_once '../includes/check_access.php';
require_once '../includes/db.php'; // connessione PDO

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($_SESSION['username'])) {
    http_response_code(400);
    exit('Dati mancanti o utente non autenticato');
}

$username = $_SESSION['username'];

// Cancella layout precedente
$pdo->prepare("DELETE FROM dashboard_layout WHERE username = ?")
    ->execute([$username]);

$pos = 0;
foreach ($data['order'] as $cardId) {
    $visibile = isset($data['visibility'][$cardId]) && $data['visibility'][$cardId] ? 1 : 0;
    $stmt = $pdo->prepare("
        INSERT INTO dashboard_layout
        (username, card_id, posizione, visibile)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $username,
        $cardId,
        ++$pos,
        $visibile
    ]);
}

echo json_encode(['status' => 'ok']);
*/
