<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}
$currentUserRole = $_SESSION['ruolo'] ?? '';
global $currentUserRole;
$row = elenco_utenti(); // elenco utenti

// Funzioni per blocco login
function login_block_file($user) {
    return __DIR__ . '/../logs/login_block_' . md5($user) . '.json';
}

function is_login_blocked($user) {
    $file = login_block_file($user);
    if (!file_exists($file)) return false;
    $data = json_decode(file_get_contents($file), true);
    if (!$data) return false;
    if (($data['attempts'] ?? 0) >= 4) {
        $elapsed = time() - ($data['last_attempt'] ?? 0);
        if ($elapsed < 300) return true;
        unlink($file);
    }
    return false;
}
?>

<?php foreach ($row as $key => $val): ?>
    <?php
        // Nascondi Superuser e Admin agli operatori
        if ($_SESSION['ruolo'] === 'operatore' && ($val['adminsuper'] || $val['admincomune'])) {
            continue; // salta la riga
        }

        $key++; // mantiene l'indicizzazione originale
        // Bottone elimina solo se l'utente non è superuser o admin
        $canDelete = ($val['adminsuper'] != 1 && $val['admincomune'] != 1);

        $blocked = is_login_blocked($val['aid']);
    ?>
    <tr id="riga<?= $key ?>">
        <td id="ruolo<?= $key ?>">
            <?php if ($val['adminsuper']): ?>
                Superuser
            <?php elseif ($val['admincomune']): ?>
                Amministratore
            <?php elseif ($val['adminop']): ?>
                Operatore
            <?php endif; ?>
        </td>
        <td id="username<?= $key ?>">
            <?= $val['aid'] ?>
        </td>

        <td style="display:none;" id="admin<?= $key ?>">
            <?= $val['admincomune'] ?>
        </td>

        <td style="display:none;" id="password<?= $key ?>">
            <?= $val['pwd'] ?>
        </td>

        <td id="email<?= $key ?>">
            <?= $val['email'] ?>
        </td>

        <td id="nominativo<?= $key ?>">
            <?= $val['name'] ?>
        </td>

        <td>
            <button type="button"
                    class="btn btn-sm btn-warning me-1"
                    onclick="editUser(<?= $key ?>); scrollToFormTitle();">
                Modifica
            </button>

            <?php if ($canDelete): ?>
                <button type="button"
                        class="btn btn-sm btn-danger"
                        onclick="deleteUser(<?= $key ?>)">
                    Elimina
                </button>
            <?php endif; ?>

            <?php if($blocked): ?>
                <button type="button"
                        class="btn btn-sm btn-warning"
                        onclick="unblockUser('<?= $val['aid'] ?>', <?= $key ?>)">
                    Sblocca
                </button>
            <?php endif; ?>
        </td>

    </tr>
<?php endforeach; ?>

<script>
function scrollToFormTitle() {
    const target = document.getElementById('form-title');
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Funzione AJAX per sbloccare utente
function unblockUser(username, key) {
    if(!confirm(`Sbloccare l'utente ${username}?`)) return;

    $.post('unblock_user.php', { username: username }, function(data) {
        if(data.success) {
            alert(`Utente ${username} sbloccato!`);
            // Rimuove il bottone Sblocca
            $(`#riga${key} button:contains("Sblocca")`).remove();
        } else {
            alert('Errore: impossibile sbloccare l\'utente.');
        }
    }, 'json');
}
</script>
