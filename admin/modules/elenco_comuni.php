<?php
if (is_file('includes/check_access.php'))
    require_once 'includes/check_access.php';
else
    require_once '../includes/check_access.php';

if (!isset($predefinito)) {
    $row = configurazione();
    $predefinito = $row[0]['siteistat'];
}

$row = elenco_comuni();
$enti = [];

foreach ($row as $key => $val) {
    $enti[] = [
        'id'           => $key + 1,
        'denominazione'=> $val['descrizione'],
        'codice_istat' => $val['id_comune'],
        'capoluogo'    => $val['capoluogo'],
        'indirizzo'    => $val['indirizzo'],
        'abitanti'     => $val['fascia'],
        'fax'          => $val['fax'],
        'email'        => $val['email'],
        'cap'          => $val['cap'],
        'centralino'   => $val['centralino'],
        'stemma'       => $val['stemma'],
        'simbolo'      => $val['simbolo'],
        'predefinito'  => ($predefinito === $val['id_comune'])
    ];
}

$row = elenco_fasce(1);
$fasce = [];
$i = 1;

foreach ($row as $val) {
    $fasce[$val['id_fascia']] =
        number_format($i, 0, ',', '.') . " - " .
        number_format(($val['abitanti'] - 1), 0, ',', '.');
    $i = $val['abitanti'];
    if ($val['id_fascia'] == 8) break;
}

$fasce[8] = "Oltre 1.000.000";
?>

<?php if (!empty($enti)): ?>
    <?php foreach ($enti as $key => $val): ?>
        <?php if (!isset($fasce[$val['abitanti']])) continue; ?>

        <tr>
            <td>
                <input type="hidden" id="cap<?= $key ?>" value="<?= $val['cap'] ?>">
                <input type="hidden" id="email<?= $key ?>" value="<?= $val['email'] ?>">
                <input type="hidden" id="centralino<?= $key ?>" value="<?= $val['centralino'] ?>">
                <input type="hidden" id="fax<?= $key ?>" value="<?= $val['fax'] ?>">
            </td>

            <td>
                <img src="../principale.php?funzione=immagine&id_comune=<?= $val['codice_istat'] ?>&simbolo=<?= $val['simbolo'] ?>"
                     width="50"
                     alt="stemma">
            </td>

            <td id="denominazione<?= $key ?>">
                <?= htmlspecialchars($val['denominazione']) ?>
            </td>

            <td id="indirizzo<?= $key ?>">
                <?= htmlspecialchars($val['indirizzo']) ?>
            </td>

            <td>
                <input type="hidden" id="abitanti<?= $key ?>" value="<?= $val['abitanti'] ?>">
                <?= $fasce[$val['abitanti']] ?>
            </td>

            <td id="codiceIstat<?= $key ?>">
                <?= $val['codice_istat'] ?>
            </td>

            <td>
                <input type="hidden" id="capoluogo<?= $key ?>" value="<?= $val['capoluogo'] ? 1 : 0 ?>">
                <?= $val['capoluogo'] ? 'si' : 'no' ?>
            </td>

            <td>
                <button type="button"
                        class="btn btn-sm btn-warning me-1"
                        onclick="editEnte(<?= $key ?>); scrollToFormTitle();">
                    Modifica
                </button>

                <button type="button"
                        class="btn btn-sm btn-danger"
                        onclick="deleteEnte(<?= $key ?>)">
                    Elimina
                </button>
            </td>
        </tr>

    <?php endforeach; ?>
<?php endif; ?>

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
