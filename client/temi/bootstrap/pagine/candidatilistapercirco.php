<?php
	if(isset($_GET['num_lista'])) $num_lista=intval($_GET['num_lista']); else $num_lista=1;
	$elencoliste=elenco_liste();
	$id_lista=0;
	foreach($elencoliste as $key=>$val) if($val[2]==$num_lista) {$desclista=$val[3]; $id_lista=$val['id_lista'];break;}
	$sezioni=elenco_circoscrizioni();
	# Blocco opendata
	/*$nosez=1;
	$linkopendata="modules.php?name=Elezioni&op=candidato_circo&id_cons_gen=$id_cons_gen&id_comune=$id_comune$cirpar&csv=1&min=1&minsez=1&id_lista=$id_lista";
	include 'opendata.php'; */
?>
<div class="container pb-2">
  <label for="defaultSelect">Seleziona Lista</label>
	<select id="defaultSelect" onchange="location = this.value;">
	<?php
	foreach($elencoliste as $campo=>$val) {
		if($num_lista==$val[2]) $sel='selected'; else $sel='';?>
		<option <?php echo $sel; ?> value="modules.php?op=18&name=Elezioni&id_comune=<?php echo $id_comune.$cirpar;?>&id_cons_gen=<?php echo $id_cons_gen;?>&file=index&num_lista=<?php echo $val[2];?>"><?php echo $val[2].") ".$val[3];?></option>
	<?php } ?>
	</select>
</div>
<?php
	$tab='candidati';
	$row=dati_consultazione(0);
	$tipo=$row[0][4];
	$scrutinate=scrutinate($tab);
	$sezionitotali=sezioni_totali();
	$row=elenco_candidati($id_lista);
	$offsetmax=count($row);
?>
<?php 
$nosez=1;
	$linkopendata="modules.php?name=Elezioni&op=candidato_circo&id_cons_gen=$id_cons_gen&id_comune=$id_comune$cirpar&csv=1&min=1&minsez=1&id_lista=$id_lista&min=1&offset=$offsetmax";
	$linkopendatapdf="modules.php?name=Elezioni&op=candidato_circo&id_cons_gen=$id_cons_gen&id_comune=$id_comune$cirpar&csv=1&id_lista=$id_lista&min=1&offset=$offsetmax";
include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>	
<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th><?php echo _CANDIDATI." per circoscrizione";?></th> 
				<?php if ($scrutinate == $sezionitotali) {?> 
					<th class="text-end">Dati definitivi</th>
				<?php } else {?>
					<th class="text-end"><span>Sezioni scrutinate: <?php echo $scrutinate; ?> su <?php echo $sezionitotali; ?></span></th>
				<?php }?>
			</tr>
		</thead>
	</table>
</div>
<div class="table-responsive overflow-x">
	<table class="table tablescroll table-striped table-bordered table-sm align-middle text-end">
		<thead class="primary-bg-c1 align-middle">
			<tr>
				<th style="width: 30%" scope="col">Circoscrizione</th>
				<?php foreach ($sezioni as $key=>$val) { ?>
				<th class="text-center" scope="col"><?php echo $val['descrizione']; ?></th>
				<?php } if(!isset($controllo)) $controllo=$val['num_circ']+1; ?>
				<th class="text-center" scope="col">Totale <br>Complessivo</th>
			</tr>
		</thead>
		<?php 
		$ar=array();
		foreach($row as $key=>$val){
			$numcand=$val[2]; 
			$ar[$numcand][0]=$val[3];
			foreach ($sezioni as $k=>$i) $ar[$numcand][$i['num_circ']]=0;
		}
		$row=voti_candidati_circo($num_lista); 
		$righe=count($row);
		foreach($row as $key=>$val){
			$numcand=$val[2]; 
			$sezione=$val[1];
			$ar[$numcand][0]=$val[3];
			if(!isset($ar["$numcand"][$controllo]))
				foreach ($sezioni as $k=>$val2) 
					$ar["$numcand"][$val2['num_circ']]="";
			$ar["$numcand"][$val[1]]=intval($val[4]);
			if(!isset($ar["$numcand"][$controllo])) $ar["$numcand"][$controllo]=0;
			$ar["$numcand"][$controllo]+=$val[4]; 
		} 
		$totvoti=array_column($ar,$controllo);
		if(count($totvoti)==count($ar))
			array_multisort($totvoti, SORT_DESC,$ar); ?>	
		<tbody>
			<?php
			foreach($ar as $key=>$val) { ?>
				<tr class="text-start">
				<?php $i=0;
				foreach($val as $key2=>$val2) 
					if(!$i) {
						$i=1;?>
						<th class="text-wrap" scope="row"><?php echo $val2 ?></th>
					<?php }else{ ?>
						<td class="text-center">
							<?php if(is_integer($val2)) echo number_format($val2,0,'','.'); else echo "-"; ?>
						</td>
					<?php } ?>
				</tr>	
			<?php }?>
		</tbody>
	</table>
</div>