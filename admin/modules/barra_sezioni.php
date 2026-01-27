<?php 
if(is_file('../includes/check_access.php')) 
{
	require_once '../includes/check_access.php';
//	require_once '../includes/query.php';
}else{
	require_once 'includes/check_access.php';
//	require_once 'includes/query.php';
}
global $id_cons,$id_sez;
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
#if (isset($param['id_gruppo'])) {$id_gruppo=intval($param['id_gruppo']);}
#if (isset($param['id_cand'])) {$id_cand=intval($param['id_cand']);}
if (isset($param['num_sez'])) { $num_sez=intval($param['num_sez']);} else $num_sez=1;
if(isset($_SESSION['id_sez'])) $id_sez=$_SESSION['id_sez'];
#$_SESSION['id_cons']=$id_cons;
$ultimasez=0;
$id_cons=$_SESSION['id_cons'];
$totale_sezioni=totale_sezioni();
$row=dati_sezione(0,$num_sez);
$id_sez=$row[0]['id_sez'];
$_SESSION['id_sez']=$id_sez;

$sezione_attiva = $num_sez;
$_SESSION['sezione_attiva']=$sezione_attiva;
$row=elenco_sezioni();
//for($i=1;$i<=$totale_sezioni;$i++) $colore[$i]='';
foreach($row as $key=>$val) {
//	if($val['num_sez']==$sezione_attiva) 
//		$id_sez=$val['id_sez'];	
	$colore[$val['num_sez']]=$val['colore'];
}
$row=ultime_affluenze_sezione($id_sez);
# t3.voti_complessivi,t3.voti_uomini,t3.voti_donne
$votantiUltimaOra=array();
if(count($row))
	$votantiUltimaOra=[ 1 => ['uomini' => $row[0]['voti_uomini'], 'donne' => $row[0]['voti_donne'], 'totali' => $row[0]['voti_complessivi']]];
?>
<!-- Titolo principale -->
<h3 id="titoloSezione">Voti di Lista - Sezione n. <?php echo $sezione_attiva; ?></h3>
<!-- Navigazione Sezioni -->
<div class="mb-3">
   <div class="d-flex flex-wrap" id="sezioniBtn">
   <?php
   for ($i = 1; $i <= $totale_sezioni; $i++) {
       $classe = ($i == $sezione_attiva) ? 'btn-primary' : 'btn-outline-primary';
       echo '<button class="btn ' . $classe . ' sezione-btn" data-sezione="' . $i . '" onclick="selezionaSezione('.$i.')" 
             style="border: 3px solid '.$colore[$i].'; box-shadow: 0 0 5px '.$colore[$i].'; margin:2px;">' 
             . $i . '</button>';
   }
   ?>
   </div>
</div>

<style>
.sezione-btn {
    width: 30px;              /* larghezza fissa */
    height: 30px;             /* altezza fissa */
    display: flex;             /* attiva flexbox */
    justify-content: center;   /* centra orizzontalmente */
    align-items: center;       /* centra verticalmente */
    padding: 0;                /* rimuove padding extra */
    font-size: 0.9rem;         /* dimensione testo */
    text-align: center;        /* sicurezza */
}

/* adattamento responsive */
@media (max-width: 768px) {
    .sezione-btn {
        width: 25px;
        height: 25px;
        font-size: 0.75rem;
    }
}
</style>





  <div class="container-fluid">
    <!-- Statistiche Ultima Ora -->
	<?php if(count($votantiUltimaOra)) { ?>
    <h5 class="text-center">Votanti Ultima Ora</h5>
    <table class="table table-bordered text-center mx-auto" style="max-width: 400px; background-color: #f8f9fa; border-radius: 0.375rem;">
	  <tbody id="tabellaVotanti">
		<tr>
		  <td><strong>Votanti Uomini</strong><br><?php echo $votantiUltimaOra[1]['uomini']; ?></td>
		  <td><strong>Votanti Donne</strong><br><?php echo $votantiUltimaOra[1]['donne']; ?></td>
		  <td><strong>Totali</strong><br><?php echo $votantiUltimaOra[1]['totali']; ?></td>
		</tr>
	  </tbody>
	</table>
	<?php } ?>
<!-- Box per messaggio di errore/successo -->
<div id="boxMessaggio" class="card">
  <div id="boxBody" class="card-body">
    <strong id="titoloMsg"></strong> <span id="contenutoMsg"></span>
  </div>
</div>

