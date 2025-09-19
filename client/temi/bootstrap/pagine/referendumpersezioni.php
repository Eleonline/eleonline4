<?php
if($genere==0) {
	if(isset($_GET['num_gruppo'])) $num_gruppo=intval($_GET['num_gruppo']); else $num_gruppo=1;
	$sql="SELECT id_gruppo FROM ".$prefix."_ele_gruppo where id_cons='$id_cons' and num_gruppo='$num_gruppo'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($id_gruppo) = $sth->fetch(PDO::FETCH_NUM);
}
$row=elenco_orari();
if($genere==2 or $genere==4) $tab='lista'; else $tab='gruppo';
if($genere==0){
	$quesiti=elenco_gruppi($tab);
	$scrutinatetemp=voti_referendum($id_gruppo);
	$scrutinate=count($scrutinatetemp);
}else
	$scrutinate=scrutinate($tab);
$sezionitotali=sezioni_totali();
$getref='';
if ($genere==0) {
	$numquesiti=count($quesiti);
	$getref="&id_gruppo=$id_gruppo";	
	if($numquesiti>1){
		if(isset($_GET['data'])) $data=$_GET['data'];
		if(isset($_GET['orario'])) $orario=$_GET['orario'];
		else{
			foreach($row as $campo=>$val) {}
			$data=$val['data'];	
			$orario=$val['orario'];
		}?>
		<!-- Blocco select quesito referndum-->	
		<div class="container pb-2">
			<label for="defaultSelect">Seleziona Quesito</label>
			<select id="defaultSelect" onchange="location = this.value;">
				<!-- option selected>Selezione Quesito</option -->
				<?php
				$desc='';
				foreach($quesiti as $key=>$val) { 
				if ($num_gruppo==$val[1]) {$id_gruppo=$val['id_gruppo']; $sel='selected';} else {$sel='';}
				?>
					<option <?php echo $sel; ?> value=" <?php echo "modules.php?op=29&id_comune=$id_comune&file=index&id_cons_gen=$id_cons_gen&num_gruppo=".$val[1];?>">Quesito <?php echo $val['num_gruppo'];?></option>
				<?php }?>
			</select>
		</div>
		<!-- fine Blocco select quesito referndum-->
	<?php echo $desc;} 
	if(!$id_gruppo) {
#		$scrutinate=$scrutinatetemp;
		foreach($voti[0] as $key=>$val) if($key=='id_gruppo') $id_gruppo=$val;
	}
}?>
<?php 
/*$nosez=1;
$linkopendata="modules.php?op=gruppo_sezione&csv=1&id_comune=$id_comune&id_cons_gen=$id_cons_gen$getref";
include 'opendata.php'; */?>
<div class="container">
	<div class="row text-center">
		<h4 class="fw-semibold text-primary mobile-expanded mt-2">Referendum per Sezioni</h4>
	</div>
</div>
<?php 
$nosez=1;
$linkopendata="modules.php?op=gruppo_sezione&csv=1&id_comune=$id_comune&id_cons_gen=$id_cons_gen$getref";
include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>	
<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th>Referendum per sezione</th> 
				<?php if ($scrutinate == $sezionitotali) {?> 
					<th class="text-end">Dati finali</th>
				<?php } else {?>
					<th class="text-end"><span>Sezioni scrutinate: <?php echo $scrutinate; ?> su <?php echo $sezionitotali; ?></span>
					</th>
				<?php }?>
			</tr>
		</thead>
	</table>
</div>  
<div class="table-responsive overflow-x">
	<table class="table table-striped table-bordered table-sm align-middle text-end">
		<thead class="primary-bg-c1">
			<tr>
				<th scope="col">Sezione</th>
				<th scope="col">Si</th>
				<th scope="col">No</th>
				<th scope="col">Voti Validi</th>
				<th scope="col">Voti Nulli</th>
				<th scope="col">Voti Bianchi</th>
				<th scope="col">Voti Contestati</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$totsi=0;$totno=0;$totuomini=0;$totdonne=0;$totespressi=0;$totvalidi=0;$totnulli=0;$totbianchi=0;$totcontestati=0;
			$seztemp=elenco_sezioni(0);
			foreach($seztemp as $sez) $sezioni[$sez['num_sez']]=$sez['id_sez'];
				if($genere==0)
					$voti=voti_referendum($id_gruppo);
				foreach($voti as $votitemp) $row[$votitemp['id_sez']]=$votitemp;
			for($i = 1; $i <= $sezionitotali; $i++) {
				if(!isset($row[$sezioni[$i]]) or $row[$sezioni[$i]]['validi']+$row[$sezioni[$i]]['nulli']+$row[$sezioni[$i]]['bianchi']+$row[$sezioni[$i]]['contestati']==0) continue;
				if($genere==0)
					$aff=ultime_affluenze_sezref($sezioni[$i],$id_gruppo);
				else
					$aff=ultime_affluenze_sezione($row[$sezioni[$i]]['id_sez']);
				$totsi+=$row[$sezioni[$i]]['si'];$totno+=$row[$sezioni[$i]]['no'];$totuomini+=$aff[0]['voti_uomini'];$totdonne+=$aff[0]['voti_donne'];$totespressi+=$aff[0]['voti_complessivi'];$totvalidi+=$row[$sezioni[$i]]['validi'];$totnulli+=$row[$sezioni[$i]]['nulli'];$totbianchi+=$row[$sezioni[$i]]['bianchi'];$totcontestati+=$row[$sezioni[$i]]['contestati'];	?>
				<tr>
					<th scope="row"><?php echo $i;?></th>
					<td><?php echo $row[$sezioni[$i]]['si'];?><br>
						<span class="percentuale"><?php echo 
						number_format(($row[$sezioni[$i]]['si']/$row[$sezioni[$i]]['validi'])*100, 2)." %";?></span></td>
					<td><?php echo $row[$sezioni[$i]]['no'];?><br>
						<span class="percentuale"><?php echo 
						number_format(($row[$sezioni[$i]]['no']/$row[$sezioni[$i]]['validi'])*100, 2)." %";?></span></td>
					<td><?php echo $row[$sezioni[$i]]['validi'];?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($row[$sezioni[$i]]['validi']/$aff[0]['voti_complessivi'])*100, 2)." %";?></span></td>
					<td><?php echo $row[$sezioni[$i]]['nulli'];?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($row[$sezioni[$i]]['nulli']/$aff[0]['voti_complessivi'])*100, 2)." %";?></span></td>
					<td><?php echo $row[$sezioni[$i]]['bianchi'];?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($row[$sezioni[$i]]['bianchi']/$aff[0]['voti_complessivi'])*100, 2)." %";?></span></td>
					<td><?php echo $row[$sezioni[$i]]['contestati'];?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($row[$sezioni[$i]]['contestati']/$aff[0]['voti_complessivi'])*100, 2)." %";?></span></td>
				</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr class="primary-bg-c4 white-color align-middle text-end">
				<td scope="row">Totale</td>
				<th><?php echo $totsi ?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($totsi/$totvalidi)*100, 2)." %";?></span></th>
				<th><?php echo $totno ?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($totno/$totvalidi)*100, 2)." %";?></span></th>
				<th><?php echo $totvalidi ?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($totvalidi/$totespressi)*100, 2)." %";?></span></th>
				<th><?php echo $totnulli ?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($totnulli/$totespressi)*100, 2)." %";?></span></th>
				<th><?php echo $totbianchi ?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($totbianchi/$totespressi)*100, 2)." %";?></span></th>
				<th><?php echo $totcontestati ?><br>
						<span class="percentuale"><?php echo //number_format($percentualeuomini, 2) .
						number_format(($totcontestati/$totespressi)*100, 2)." %";?></span></th>
			</tr>
		</tfoot>
	</table>
</div>