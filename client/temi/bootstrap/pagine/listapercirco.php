<?php
	global $id_cons,$genere;
	$tab='lista';
	if($genere==2)
		$scrutinate=scrutinate('gruppo');
	else
		$scrutinate=scrutinate($tab);
	$sezionitotali=sezioni_totali();
	$row=conscomune(0);
	$id_cons=$row[0][0];
	$sezioni=elenco_circoscrizioni();
	# Blocco opendata
	/*$nosez=1;
	if($genere==2)
		$linkopendata="modules.php?name=Elezioni&op=gruppo_circo&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
	else
		$linkopendata="modules.php?name=Elezioni&op=lista_circo&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
	include 'opendata.php'; */
?>
<?php
$nosez=1; 
if($genere==2)
		$linkopendata="modules.php?name=Elezioni&op=gruppo_circo&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
	else
		$linkopendata="modules.php?name=Elezioni&op=lista_circo&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>	
<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th><?php echo _LISTE." per sezione";?></th> 
				<?php if ($scrutinate == $sezionitotali) {?> 
					<th class="text-end">Dati finali</th>
				<?php } else {?>
					<th class="text-end"><span>Sezioni scrutinate: <?php echo $scrutinate; ?> su <?php echo $sezionitotali; ?></span></th>
				<?php }?>
			</tr>
		</thead>
	</table>
</div>	  
<div class="table-responsive overflow-x">
	<table class="table tablescroll table-striped table-bordered table-sm align-middle ">
		<thead class="primary-bg-c1 text-end">
			<tr>
				<th style="width: 30%" scope="col">Circoscrizione</th>
				<?php foreach ($sezioni as $key=>$val) { ?>
					<th class="text-center" scope="col"><?php echo $val['descrizione']; ?></th>
				<?php } 
				if(!isset($controllo)) $controllo=$val['num_circ']+1; ?>
				<th class="text-center" scope="col">Totale <br>Complessivo</th>
			</tr>
		</thead>
		<?php 
		$ar=array();
		$row=elenco_gruppi($tab);
		$numeroliste=count($row);
		foreach($row as $key=>$val){
			$numlista=$val[1]; 
			$ar[$numlista][0]=$val[2];
			foreach ($sezioni as $k=>$i) $ar[$numlista][$i['num_circ']]=0;
		}
		if($genere==2)
			$row=voti_gruppo_circo('gruppo');
		else
			$row=voti_gruppo_circo($tab); 
		$righe=count($row);
		foreach($row as $key=>$val){
			$numgruppo=$val[2]; 
			$sezione=$val[1];
			$ar[$numgruppo][0]=$val[3];
			if(!isset($ar["$numgruppo"][$controllo]))
				foreach ($sezioni as $k=>$val2) {
					$ar[$numgruppo][$val2['num_circ']]="0";
				}
			$ar["$numgruppo"][$val[1]]=$val[4]; #echo "<br>TEST:$numgruppo:".$val[1]." - ".$val[4];
			if(!isset($ar["$numgruppo"][$controllo])) $ar["$numgruppo"][$controllo]=0;
			$ar["$numgruppo"][$controllo]+=$val[4];
		}
		$totvoti=array_column($ar,$controllo);
		if(count($totvoti)==count($ar))
			array_multisort($totvoti, SORT_DESC,$ar);
		?>	
		<tbody>
			<?php 
			foreach($ar as $key=>$val) {?>
				<tr class="text-end">
					<?php 
					$i=0;
					foreach($val as $key2=>$val2) 
					if(!$i) {
						$i=1;?>
						<th class="text-wrap text-start" scope="row">
							<?php echo $val2 ?>
						</th>
					<?php }	else { ?>
						<td class="text-center">
							<?php if(is_integer($val2)) echo number_format($val2,0,'','.'); else echo "$val2"; ?>
						</td>
					<?php }?>
				</tr>	
			<?php } ?>
		</tbody>
	</table>
</div>