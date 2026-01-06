<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}
global $id_lista;
$row = elenco_liste();
if(!count($row)) $grp[0]=0;
foreach($row as $key=>$val) {
	$grp[$val['num_lista']]=$val['descrizione'];
}
$row = elenco_candidati($id_lista);
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_cand'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
?>
<!-- Riga nascosta per JSstyle="display:none;" -->
<tr id="riga<?= $maxNumero ?>">
    <td id="maxNumero" colspan="7" style="display:none;"><?= $maxNumero ?></td>
</tr>

<?php foreach ($row as $key => $val) {
$id_cons     = htmlspecialchars($val['id_cons']     ?? '', ENT_QUOTES, 'UTF-8');
$id_lista   = htmlspecialchars($val['id_lista']   ?? '', ENT_QUOTES, 'UTF-8');
$num_lista = htmlspecialchars($val['num_lista']   ?? '', ENT_QUOTES, 'UTF-8');
$desc_lista   = htmlspecialchars($grp[$val['num_lista']]   ?? '', ENT_QUOTES, 'UTF-8');
$id_cand   = htmlspecialchars($val['id_cand']   ?? '', ENT_QUOTES, 'UTF-8');
$numero      = htmlspecialchars($val['num_cand']  ?? '', ENT_QUOTES, 'UTF-8');
$cognome = htmlspecialchars($val['cognome'] ?? '', ENT_QUOTES, 'UTF-8');
$nome = htmlspecialchars($val['nome'] ?? '', ENT_QUOTES, 'UTF-8');
$cv     = htmlspecialchars($val['cv']     ?? '', ENT_QUOTES, 'UTF-8');
$cg     = htmlspecialchars($val['cg']     ?? '', ENT_QUOTES, 'UTF-8');

?>
<tr id="riga<?= $key ?>">
    <td id="numero<?= $key ?>"><?= $numero ?></td>
	<?php if(isset($grp[0]) and $grp[0]==0) { ?>
		<td  style="display:none;"
	<?php }else{ ?>
		<td
	<?php } ?>
		id="lista<?= $key ?>"><?= $grp[$val['num_lista']] ?></td>
    <td id="cognome<?= $key ?>"><?= $cognome ?></td><td id="nome<?= $key ?>"><?= $nome; ?></td>
    <td class="col-2"  style="text-align:center;"><div id="id_candidato<?= $key ?>" style="display:none;"><?= $id_cand ?></div><div id="id_cons<?= $key ?>" style="display:none;"><?= $id_cons ?></div><div id="id_lista<?= $key ?>" style="display:none;"><?= $id_lista ?></div><div id="num_lista<?= $key ?>" style="display:none;"><?= $num_lista ?></div>
        <button class="btn btn-sm btn-warning me-1" onclick="editCandidato(<?= $key ?>); scrollToGestioneCandidato();">Modifica</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="deleteCandidato(<?= $key ?>)">Elimina</button>
    </td>
</tr>
<?php } ?>


