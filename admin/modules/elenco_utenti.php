<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}

global $currentUserRole;
$row = elenco_utenti(); // elenco utenti
?>

<?php foreach ($row as $key => $val): ?>
    <?php
        $key++; // mantiene l'indicizzazione originale
        $canDelete = (
            $currentUserRole != 'operatore' &&
            $val['adminsuper'] != 1 &&
            $val['admincomune'] != '1'
        );
    ?>
    <tr id="riga<?= $key ?>">

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
        </td>

    </tr>
<?php endforeach; ?>

<script>
function scrollToFormTitle() {
    const target = document.getElementById('form-title');
    if (target) {
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}
</script>
