<?php 
if(is_file('../includes/check_access.php')) 
{
	require_once '../includes/check_access.php';
}else{
	require_once 'includes/check_access.php';
}
global $id_cons,$id_sez;
if (isset($param['num_sez'])) { $num_sez=intval($param['num_sez']);} else $num_sez=1;
if(isset($_SESSION['id_sez'])) $id_sez=$_SESSION['id_sez'];
$id_cons=$_SESSION['id_cons'];
$totale_sezioni=totale_sezioni();
$row=dati_sezione(0,$num_sez);
if(count($row)) {
	$id_sez=$row[0]['id_sez'];
	$_SESSION['id_sez']=$id_sez;
}
$row=elenco_sezioni();
if(count($row)){
	foreach($row as $key=>$val) {
		$colore[$val['num_sez']]=$val['colore'];
	}
	$sezioni_scrutinate = 0;
	foreach($colore as $c) {
		if(!empty($c)) $sezioni_scrutinate++;
	}
}else{
	$sezioni_scrutinate=0;
	$totale_sezioni=0;
}
?>
<div class="small-box bg-warning">
  <div class="inner">
	<p>Sezioni scrutinate</p>
    <h3>
      <?= $sezioni_scrutinate ?> <small style="font-size:18px">su</small> <?= $totale_sezioni ?>
    </h3>
  </div>
  <div class="icon">
    <i class="fas fa-vote-yea"></i>
  </div>
</div>
