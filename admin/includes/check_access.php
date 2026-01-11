<?php
// if (!defined('APP_RUNNING')) {
    // header('HTTP/1.0 403 Forbidden');
    // exit('Accesso negato');
// }
 ?>
<?php
if (!defined('APP_RUNNING')) {
    require_once __DIR__ . '/accesso_negato.php';
    exit;
}
?>
