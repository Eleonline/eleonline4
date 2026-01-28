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
$id_sez=$row[0]['id_sez'];
$_SESSION['id_sez']=$id_sez;
$row=elenco_sezioni();
foreach($row as $key=>$val) {
	$colore[$val['num_sez']]=$val['colore'];
}
?>

<!-- Box Card -->
<div class="card bg-light" id="box-sezioni-card">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-list"></i> Stato Sezioni</h3>
    <div class="card-tools">
      <button class="btn btn-tool toggle-layout-btn" onclick="toggleSezioniLayout()">
        <i class="fas fa-expand"></i>
      </button>
    </div>
  </div>

  <div class="card-body">
    <!-- Navigazione Sezioni -->
    <div class="mb-3">
       <div class="d-flex flex-wrap" id="sezioniBtn">
       <?php
       for ($i = 1; $i <= $totale_sezioni; $i++) {
           $classe = 'btn-outline-primary';
           $col = (!isset($colore[$i]) || empty($colore[$i])) ? '#007bff' : $colore[$i];
           echo '<button class="btn ' . $classe . ' sezione-btn" data-sezione="' . $i . '" 
                 style="border: 3px solid '.$col.'; box-shadow: 0 0 5px '.$col.'; margin:2px;">' 
                 . $i . '</button>';
       }
       ?>
       </div>
    </div>
  </div>
</div>
