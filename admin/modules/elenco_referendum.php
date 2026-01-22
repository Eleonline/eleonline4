<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}
if(is_file('../../client/temi/bootstrap/pagine/config_colori_quesiti.php'))
	$coloripath='../../client/temi/bootstrap/pagine/';
else
	$coloripath='../client/temi/bootstrap/pagine/';
require_once $coloripath.'config_colori_quesiti.php';

$schedapath='../../client/temi/bootstrap/pagine/imgscheda/';
$pdfpath="../../client/documenti/$id_comune/$id_cons/programmi/";
$tipo_cons=$_SESSION['tipo_cons'];
$row = elenco_gruppi();
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_gruppo'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
$tabella_schede=[
1 => 'scheda_verde.jpg',
2 => 'scheda_arancione.jpg',
3 => 'scheda_grigio.jpg',
4 => 'scheda_viola.jpg',
5 => 'scheda_giallo.jpg'
];
?>
<!-- Riga nascosta per JSstyle="display:none;" -->
<tr id="riga<?= $maxNumero ?>">
    <td id="maxNumero" colspan="7" style="display:none;"><?= $maxNumero ?></td>
</tr>

<?php foreach ($row as $i => $q): 
$id_cons     = htmlspecialchars($q['id_cons']     ?? '', ENT_QUOTES, 'UTF-8');
$id_gruppo   = htmlspecialchars($q['id_gruppo']   ?? '', ENT_QUOTES, 'UTF-8');
$numero      = htmlspecialchars($q['num_gruppo']  ?? '', ENT_QUOTES, 'UTF-8');
$denominazione = htmlspecialchars($q['descrizione'] ?? '', ENT_QUOTES, 'UTF-8');
$simbolo     = htmlspecialchars($q['simbolo']     ?? '', ENT_QUOTES, 'UTF-8');
$prognome    = htmlspecialchars($q['prognome']    ?? '', ENT_QUOTES, 'UTF-8');

?>
              <tr id="riga<?= $i ?>">
                <td id="numero<?= $i ?>"><?= (int)$numero ?></td>
                <td id="denominazione<?= $i ?>"><?= $denominazione ?></td>
                <td style="background-color: <?= isset($q['id_colore']) && $q['id_colore'] ? $coloriQuesiti[$q['id_colore']]['colore'] : 'transparent' ?>">
                  <strong><?= isset($q['id_colore']) && $q['id_colore'] ? htmlspecialchars($coloriQuesiti[$q['id_colore']]['nome']) : '' ?></strong><br>
                </td>
                <td class="align-middle">
                  <?php if (!empty($q['prognome']) and is_file($pdfpath.$q['prognome'])): ?>
                    <a href="<?= $pdfpath.$q['prognome'] ?>" target="_blank" class="btn btn-sm btn-primary">Visualizza PDF</a>
                  <?php elseif(isset($tabella_schede[$q['id_colore']])): ?><img src="<?= $schedapath.$tabella_schede[$q['id_colore']]  ?>" style="max-height:60px;" alt="Immagine scheda">
                   
                  <?php endif; ?>
                </td>
                <td><div id="id_gruppo<?= $i ?>"  style="display:none;"><?= $q['id_gruppo'] ?></div><div id="id_colore<?= $i ?>"  style="display:none;"><?= $q['id_colore'] ?></div><div id="prognome<?= $i ?>"  style="display:none;"><?= $q['prognome'] ?></div>
				<button class="btn btn-sm btn-warning me-1" onclick="modificaQuesito(<?= $i ?>)" title="Modifica"><i class="fas fa-edit"></i></button>
				<button type="button" class="btn btn-sm btn-danger" onclick="deleteReferendum(<?= $i ?>)">Elimina</button>

                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($row)): ?>
              <tr><td colspan="5" class="text-muted">Nessun quesito presente.</td></tr>
            <?php endif; ?>


