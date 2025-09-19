<?php
	if($genere==0) {
		if(isset($_GET['id_gruppo'])) $id_gruppo=$_GET['id_gruppo'];
		else {
			$row=elenco_gruppi('gruppo');
			$id_gruppo=$row[0]['id_gruppo'];
		}
		$row=ultime_affluenze_referendum($id_gruppo);
	} else
		$row=ultime_affluenze(0);
	$ultimeaffluenze=$row[0]['complessivi'];
	$row=elenco_tot_iscritti();
	$iscritti=$row[0][2];
	if($iscritti)
		$percentualevotanti=number_format($ultimeaffluenze/$iscritti,2);
	else
		$percentualevotanti=0;

?>
<!-- div class="table-responsive overflow-x">
  <table class="table table-bordered table-sm align-middle">
    <thead class="table-light">
      <tr>
        <th class="primary-bg-c1 text-center" scope="col">Dettaglio Voti espressi - Sezioni scrutinate 13 su 13 </th>
       </tr>
    </thead>
  </table>
<table class="table table-bordered table-sm text-end">
    <tbody>
         <?php
	for($i = 1; $i <= 13; $i++) {
	?>
      <tr>
        <td class="text-end">testo <?php echo $i;?></td>
        <td>testo <?php echo $i;?></td>
		<td>testo <?php echo $i;?></td>
      </tr>
	  <?php
	  }
	?>
      </tr>
    </tbody>
  </table>
  </div -->
  
<div class="table-responsive overflow-x">
  <table class="table table-bordered table-sm align-middle text-end">
    <thead class="table-light">
      <tr>
        <th class="primary-bg-c1 text-center" scope="col">Percentuale Votanti</th>
       </tr>
    </thead>
  </table>
<table class="table table-bordered table-sm" class="align-middle">
    <tbody>
      <tr>
        <td>
		<div>
      <p><strong>Attivo</strong></p>
      <div class="progress-donut-wrapper">
        <div class="progress-donut" data-bs-progress-donut data-bs-value="<?php echo $percentualevotanti; ?>" id="prog-donut-2" ></div>
        <span class="visually-hidden"></span>
      </div>
   </div></td>
      </tr>
	  </tr>
    </tbody>
  </table>
  </div>