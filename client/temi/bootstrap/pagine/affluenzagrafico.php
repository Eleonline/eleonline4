<?php 
$id_gruppo=0;
$row=elenco_orari();
$quesiti=elenco_gruppi('gruppo');
if($genere==0)
$id_gruppo=$quesiti[0]['id_gruppo'];


if ($genere==0){
	if(isset($_GET['data'])) $data=$_GET['data'];
	if(isset($_GET['orario'])) $orario=$_GET['orario'];
	else{
		foreach($row as $campo=>$val) {}
		$data=$val['data'];	
		$orario=$val['orario'];
	}
	if(count($quesiti)>1) {
	?>
<div class="container pb-2">
<label for="defaultSelect">Seleziona Quesito</label>
<select id="defaultSelect" onchange="location = this.value;">
	<!-- option selected>Selezione Quesito</option -->
	<?php
		$desc='';
		if(isset($_GET['num_gruppo'])) $num_gruppo=intval($_GET['num_gruppo']); else $num_gruppo=1;
		foreach($quesiti as $key=>$val) { if ($num_gruppo==$val[1]) {$id_gruppo=$val['id_gruppo']; $sel='selected';} else {$sel='';} ?>
			<option <?php echo $sel; ?> value=" <?php echo "modules.php?op=21&id_comune=$id_comune&file=index&id_cons_gen=$id_cons_gen&data=$data&orario=$orario&num_gruppo=".$val[1];?>">Quesito <?php echo $val['num_gruppo'];?></option>
	<?php }?>
</select>

</div>
<?php echo $desc;}}?>
<!-- fine Blocco select quesito referndum-->
<div class="table-responsive overflow-x">
 <?php
	$totsez=sezioni_totali();
	$row=totale_iscritti(0);
	$elettori=$row[0][2];
	$totuomini=$row[0][0];
	$totdonne=$row[0][1];
	$row=elenco_affluenze();
	$affora=array();
	$affgruppi=array();
	foreach($row as $val) if($val[5]==$id_gruppo or $genere!=0)
		if(!isset($affora[$val[3].$val[4]])) $affora[$val[3].$val[4]]=1;
		else $affora[$val[3].$val[4]]++;
	$row=elenco_tot_affluenze();
	foreach($row as $val) {
	if($val[5]==$id_gruppo or $genere!=0) {if($genere==0) $affgruppi[$val[5]][]=$val; else $affgruppi[0][]=$val;}}
	if(count($affgruppi))
	foreach($affgruppi[$id_gruppo] as $key=>$val) {
	?>
 <table class="table table-sm align-middle table-borderless">
	<thead class="table-light">
      <tr>
        <th class="primary-bg-c6" colspan="2">Votanti alle ore <?php echo substr($val[4],0,5)." del ".date_format(date_create($val[3]) ,'d/m/Y'); ?></th>
		<th class="primary-bg-c6 text-end" colspan="2">Sezione scrutinate <?php echo $affora[$val[3].$val[4]]." su ".$totsez; ?></th>
       </tr>
    </thead>
    <thead>
      <tr>
        <th colspan="2">Votanti</th>
		<th class="text-end" colspan="2"><?php echo number_format($val[0],0,'','.'); ?></th>
      </tr>
    </thead>
    <tbody>
		<td colspan="4">
			<div class="progress rounded-3" style="height: 25px;">
			
				<div class="progress-bar progress-bar-striped complementary-2-bg-a4" role="progressbar" style="width: <?php echo number_format(100*$val[0]/$elettori,2); ?>%" aria-valuenow="<?php echo number_format(100*$val[0]/$elettori,2); ?>%" aria-valuemin="0" aria-valuemax="100"><?php echo number_format(100*$val[0]/$elettori,2); ?>%</div>
			<span class="sr-percent padd-rg-2"><?php echo number_format(100*$val[0]/$elettori,2); ?>%</span>
			</div>
		</td>
    </tbody>
	<?php if(($val[1]+$val[2])>0) { ?>
	<thead>
      <tr>
        <th class="w-50" colspan="1">Maschi</th>
        <th class="text-end" colspan="1"><?php echo number_format($val[1],0,'','.'); ?></th>
        <th class="w-50" colspan="1" scope="col">Femmine</th>
		<th class="text-end" colspan="1"><?php echo number_format($val[2],0,'','.'); ?></th>
      </tr>
    </thead>
	<tbody>
		<td colspan="2">
			<div class="progress rounded-3" style="height: 25px;">
				<div class="progress-bar progress-bar-striped complementary-2-bg-a4" role="progressbar" style="width: <?php echo number_format(100*$val[1]/$totuomini,2); ?>%;" aria-valuenow="<?php echo number_format(100*$val[1]/$totuomini,2); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format(100*$val[1]/$totuomini,2); ?>%</div>
			</div>
		</td>
		<td colspan="2">
			<div class="progress rounded-3" style="height: 25px;">
				<div class="progress-bar progress-bar-striped complementary-2-bg-a4" role="progressbar" style="width: <?php echo number_format(100*$val[2]/$totdonne,2); ?>%;" aria-valuenow="<?php echo number_format(100*$val[2]/$totdonne,2); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format(100*$val[2]/$totdonne,2); ?></div>
			</div>
		</td>
    </tbody>
	<?php } ?>
  </table>
 <hr>
 <?php
	  }
	?>
  </div>
 