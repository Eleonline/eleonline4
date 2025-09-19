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
}else{
	$scrutinatetemp=voti_sezione();
	$scrutinate=0;
	foreach($scrutinatetemp as $k=>$v) if($v['validi']+$v['nulli']+$v['bianchi']+$v['contestati']>0) $scrutinate++;
}
$sezionitotali=sezioni_totali();
$getref='';
if ($genere==0) {
	$numquesiti=count($quesiti);
	$getref="&num_ref=$num_gruppo&num_refs=$numquesiti";	
	if($numquesiti>1){
		if(isset($_GET['data'])) $data=$_GET['data'];
		if(isset($_GET['orario'])) $orario=$_GET['orario'];
		else{
			foreach($row as $campo=>$val) {}
			$data=$val['data'];	
			$orario=$val['orario'];
		}
		?>
		<!-- Blocco select quesito referndum-->	
		<div class="container pb-2">
			<label for="defaultSelect">Seleziona Quesito</label>
			<select id="defaultSelect" onchange="location = this.value;">
				<!-- option selected>Selezione Quesito</option -->
					<?php
					$desc='';
					foreach($quesiti as $key=>$val) { 
						if ($num_gruppo==$val[1]) {
							$id_gruppo=$val['id_gruppo']; $sel='selected';
						} else {
							$sel='';
						}?>
						<option <?php echo $sel; ?> value=" <?php echo "modules.php?op=12&id_comune=$id_comune&file=index&id_cons_gen=$id_cons_gen&data=$data&orario=$orario&num_gruppo=".$val[1];?>">Quesito <?php echo $val['num_gruppo'];?></option>
					<?php }?>
			</select>
		</div>
		<!-- fine Blocco select quesito referndum-->
		<?php echo $desc;
	} 
	if(!$id_gruppo) {
#		$scrutinate=$scrutinatetemp;
		foreach($voti[0] as $key=>$val) if($key=='id_gruppo') $id_gruppo=$val;
	}
}?>
<?php/*
if($genere==0) $valgruppo="&num_ref=$num_gruppo&num_refs=1"; else $valgruppo='';
$linkopendata="modules.php?op=come&info=votanti&csv=1&id_comune=$id_comune&id_cons_gen=$id_cons_gen$valgruppo";
$nosez=1;
include 'opendata.php'; */?>
<div class="container">
	<div class="row text-center">
		<h4 class="fw-semibold text-primary mobile-expanded mt-2">Dettaglio Voti espressi</h4>
	</div>
</div>
<?php 
if($genere==0) $valgruppo="&num_ref=$num_gruppo&num_refs=1"; else $valgruppo='';
$linkopendata="modules.php?op=come&info=votanti&csv=1&id_comune=$id_comune&id_cons_gen=$id_cons_gen$valgruppo";
$nosez=1;
include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>	
<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th>Votanti per sezione</th> 
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
				<th scope="col">Voti Uomini</th>
				<th scope="col">Voti Donne</th>
				<th scope="col">Voti Espressi</th>
				<th scope="col">Voti Validi</th>
				<th scope="col">Voti Nulli</th>
				<th scope="col">Voti Bianchi</th>
				<th scope="col">Voti Contestati</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$totuomini=0;$totdonne=0;$totespressi=0;$totvalidi=0;$totnulli=0;$totbianchi=0;$totcontestati=0;
			$row=elenco_sezioni(0);
			$sezioni= array();
			$iscrittif=array();
			$iscrittim=array();
			$totiscrittim=0;
			$totiscrittif=0;
			foreach($row as $sez) {$sezioni[$sez['num_sez']]=$sez['id_sez']; $iscrittif[$sez['num_sez']]=$sez['femmine'];$iscrittim[$sez['num_sez']]=$sez['maschi']; $totiscrittim+=$sez['maschi']; $totiscrittif+=$sez['femmine'];}
			if($genere==0)
				$voti=voti_referendum($id_gruppo);
			else
				$voti=voti_sezione();
			foreach($voti as $votitemp) $row[$votitemp['num_sez']]=$votitemp;
#				for($i = 1; $i <= $sezionitotali; $i++) {
			foreach($sezioni as $keysez=>$valsez) {
#				if(!isset($sezioni[$i]) or !isset($row[$sezioni[$i]]) or $row[$sezioni[$i]]['validi']+$row[$sezioni[$i]]['nulli']+$row[$sezioni[$i]]['contestati']==0) continue;
				if($genere==0)
					$aff=ultime_affluenze_sezref($valsez,$id_gruppo);
				else
					$aff=ultime_affluenze_sezione($valsez);
				if(!count($aff)) {
					$aff[0]['voti_uomini']=0;
					$aff[0]['voti_donne']=0;
					$aff[0]['voti_complessivi']=0;
				}	
				$totuomini+=$aff[0]['voti_uomini'];
				$totdonne+=$aff[0]['voti_donne'];
				$totespressi+=$aff[0]['voti_complessivi'];
				$totvalidi+=$row[$keysez]['validi'];
				$totnulli+=$row[$keysez]['nulli'];
				$totbianchi+=$row[$keysez]['bianchi'];
				$totcontestati+=$row[$keysez]['contestati'];
				//$totelettori+=$val['iscritti'];
				
				//$elettori = $val['iscritti']; // totale elettori
                $uomini = $aff[0]['voti_uomini'];
				$donne = $aff[0]['voti_donne'];
				$validi = $row[$keysez]['validi'];			
				$nulli = $row[$keysez]['nulli']; 
				$bianchi = $row[$keysez]['bianchi'];
				$contestati = $row[$keysez]['contestati'];
				$complessivi = $aff[0]['voti_complessivi'];
				
				$percentualeuomini = ($uomini / $iscrittim[$keysez]) * 100;
				$percentualedonne = ($donne / $iscrittif[$keysez]) * 100;	
				$percentualenulli = ($nulli / $complessivi) * 100;
				$percentualebianchi = ($bianchi / $complessivi) * 100;
				$percentualevalidi = ($validi / $complessivi) * 100;				
				$percentualecontestati = ($contestati / $complessivi) * 100;
				$percentualeespressi = ($complessivi / ($iscrittim[$keysez]+$iscrittif[$keysez])) * 100;
				
				$percentualetotuomini = ($totuomini / $totiscrittim) * 100;
				$percentualetotdonne = ($totdonne / $totiscrittif) * 100;
				$percentualetotnulli = ($totnulli / $totespressi) * 100;
				$percentualetotbianchi = ($totbianchi / $totespressi) * 100;
				$percentualetotvalidi= ($totvalidi / $totespressi) * 100;
				$percentualetotcontestati = ($totcontestati / $totespressi) * 100;
				$percentualetot = ($totespressi / ($totiscrittim+$totiscrittif)) * 100;
				?>
				<tr>
					<th scope="row"><?php echo $keysez;?></th>
					<td>
						<?php echo $uomini; ?><br>
						<span class="percentuale"><?php echo number_format($percentualeuomini, 2) . " %";?></span>
					</td>

					<td><?php echo $donne;?><br>
						<span class="percentuale"><?php echo number_format($percentualedonne, 2) . " %";?></span>
					</td>
					<td><?php echo $aff[0]['voti_complessivi'];?><br>
						<span class="percentuale"><?php echo number_format($percentualeespressi, 2) . " %";?></span>
					</td>
					<td><?php echo $validi;?><br>
						<span class="percentuale"><?php echo number_format($percentualevalidi, 2) . " %";?></span>
					</td>
					<td><?php echo $nulli;?><br>
						<span class="percentuale"><?php echo number_format($percentualenulli, 2) . " %";?></span>
					</td>
					<td><?php echo $bianchi;?><br>
						<span class="percentuale"><?php echo number_format($percentualebianchi, 2) . " %";?></span>
					</td>
					<td><?php echo $contestati;?><br>
						<span class="percentuale"><?php echo number_format($percentualecontestati, 2) . " %";?></span>
					</td>
				</tr>
			<?php
			  }
			?>
		</tbody>
		<tfoot>
			<tr class="primary-bg-c4 white-color align-middle text-end">
				<th scope="row">Totale</th>
				<th><?php echo $totuomini ?><br>
						<span class="percentuale"><?php echo number_format($percentualetotuomini, 2) . " %";?></span>
				</th>
				<th><?php echo $totdonne ?><br>
						<span class="percentuale"><?php echo number_format($percentualetotdonne, 2) . " %";?></span>
				</th>
				<th><?php echo $totespressi ?><br>
						<span class="percentuale"><?php echo number_format($percentualetot, 2) . " %";?></span>
				</th>
				<th><?php echo $totvalidi ?><br>
						<span class="percentuale"><?php echo number_format($percentualetotvalidi, 2) . " %";?></span>
				</th>
				<th><?php echo $totnulli ?><br>
						<span class="percentuale"><?php echo number_format($percentualetotnulli, 2) . " %";?></span>
				</th>
				<th><?php echo $totbianchi ?><br>
						<span class="percentuale"><?php echo number_format($percentualetotbianchi, 2) . " %";?></span>
				</th>
				<th><?php echo $totcontestati ?><br>
						<span class="percentuale"><?php echo number_format($percentualetotcontestati, 2) . " %";?></span>
				</th>
			</tr>
		</tfoot>
	</table>
</div>