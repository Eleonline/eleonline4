<?php
/*
require_once '../../includes/check_access.php';
require_once '../../includes/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $_SESSION['username'];

// Cancella layout precedente
$pdo->prepare("DELETE FROM dashboard_layout WHERE username=?")->execute([$username]);

$pos = 0;
foreach($data['order'] as $id){
  $pdo->prepare("
    INSERT INTO dashboard_layout
    (username, card_id, posizione, visibile, side_panel_open)
    VALUES (?, ?, ?, ?, ?)
  ")->execute([
    $username,
    $id,
    ++$pos,
    $data['visibility'][$id],
    $data['sidePanelOpen']
  ]);
}
*/
