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
	$sezioni=elenco_sezioni(0);
	$totsez=count($sezioni);
	# Blocco opendata
	/*if($genere==2){
		$nosez=1;
		$linkopendata="modules.php?name=Elezioni&op=gruppo_sezione&minsez=1&offsetsez=$totsez&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
	}else{
		$nosez=0;
		$linkopendata="modules.php?name=Elezioni&op=lista_sezione&minsez=1&offsetsez=$totsez&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
		$linkopendatapdf="modules.php?name=Elezioni&op=lista_sezione&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
	}
	include 'opendata.php'; */
?>
<?php if($genere==2){
		$nosez=1;
		$linkopendata="modules.php?name=Elezioni&op=gruppo_sezione&minsez=1&offsetsez=$totsez&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
	}else{
		$nosez=0;
		$linkopendata="modules.php?name=Elezioni&op=lista_sezione&minsez=1&offsetsez=$totsez&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
		$linkopendatapdf="modules.php?name=Elezioni&op=lista_sezione&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&id_lista=";
	}
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
				<th style="width: 30%" scope="col">Sezione</th>
				<?php foreach ($sezioni as $key=>$val) { ?>
					<th class="text-center" scope="col"><?php echo $val['num_sez']; ?></th>
				<?php } 
				if(!isset($controllo)) $controllo=$val['num_sez']+1; ?>
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
			foreach ($sezioni as $k=>$i) $ar[$numlista][$i['num_sez']]='0';
		}
		if($genere==2)
			$row=voti_gruppo('gruppo');
		else
			$row=voti_gruppo($tab); 
		$righe=count($row);
		foreach($row as $key=>$val){
			$numgruppo=$val[2]; 
			$sezione=$val[1];
			$ar[$numgruppo][0]=$val[3];
			if(!isset($ar["$numgruppo"][$controllo]))
				foreach ($sezioni as $k=>$val2) {
					$ar[$numgruppo][$val2['num_sez']]="";
				}
			$ar["$numgruppo"][$val[1]]=$val[4];
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
							<?php if(is_integer($val2)) echo number_format($val2,0,'','.'); else echo "-"; ?>
						</td>
					<?php }?>
				</tr>	
			<?php } ?>
		</tbody>
	</table>
</div>