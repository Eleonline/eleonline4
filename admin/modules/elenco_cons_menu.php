<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}
global $id_cons_gen;
$row = elenco_cons(); // elenco consultazioni

 #         <label for="consultazione-mobile">Consultazione</label>
 #         <select class="form-control form-control-sm" name="id_cons_gen" id="consultazione-mobile">
?>
<?php foreach ($row as $key => $val):
	if($id_cons_gen==$val['id_cons_gen']) {
		$selezionata='selected';
		$rl=dati_lista(1);
		$id_lista=$rl[0]['id_lista'];
		$_SESSION['id_lista']=$id_lista;
	}else
		$selezionata='';
?>
	<option value="<?= $val['id_cons_gen'] ?>" <?= $selezionata ?>><?= $val['descrizione'] ?></option>

<?php endforeach; ?>
        
