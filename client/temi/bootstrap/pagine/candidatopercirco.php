<?php
	global $id_cons,$genere,$idcirc;
	$tab='gruppo';
	$scrutinate=scrutinate($tab);
	$sezionitotali=sezioni_totali();
	$row=conscomune(0);
	$id_cons=$row[0][0];
	$sezioni=elenco_circoscrizioni();
	# Blocco opendata
	/*$nosez=1;
	$linkopendata="modules.php?name=Elezioni&op=gruppo_circo&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&min=1&minsez=1&id_lista=";
	include 'opendata.php'; */
?>
<?php
$nosez=1;
$linkopendata="modules.php?name=Elezioni&op=gruppo_circo&id_cons_gen=$id_cons_gen&id_comune=$id_comune&csv=1&min=1&minsez=1&id_lista="; 
include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>		  
<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th><?php echo _GRUPPI." per sezione";?></th> 
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
	<table class="table tablescroll table-striped table-bordered table-sm align-middle ">
		<thead class="primary-bg-c1 text-end">
			<tr>
				<th style="width: 30%" scope="col">Circoscrizione</th>
				<?php foreach ($sezioni as $key=>$val) { ?>
				<th class="text-center" scope="col"><?php echo $val['descrizione']; ?></th>
				<?php } if(!isset($controllo)) $controllo=$val['num_circ']+1; ?>
				<th class="text-center" scope="col">Totale <br>Complessivo</th>
			</tr>
		</thead>
		<?php 
		$row=elenco_gruppi($tab);
		foreach($row as $key=>$val){
			$numgruppo=$val[1]; 
			$ar[$numgruppo][0]=$val[2];
			foreach ($sezioni as $k=>$i)$ar[$numgruppo][$i['num_circ']]=0;
		}
		$row=voti_gruppo_circo($tab); 
		$righe=count($row);
		foreach($row as $key=>$val){
			$numgruppo=$val[2]; 
			$sezione=$val[1];
			$ar[$numgruppo][0]=$val[3];
			if(!isset($ar[$numgruppo][$controllo]))
				foreach ($sezioni as $k=>$val2) {
					$ar[$numgruppo][$val2['num_circ']]="";
				}
			$ar[$numgruppo][$val[1]]=intval($val[4]); 
			if(!isset($ar[$numgruppo][$controllo])) $ar[$numgruppo][$controllo]=0;
			$ar[$numgruppo][$controllo]+=$val[4]; 
		}
		$totvoti=array_column($ar,$controllo);
		if(count($totvoti)==count($ar))
			array_multisort($totvoti, SORT_DESC,$ar);
		?>
		<tbody>
			<?php
			foreach($ar as $key=>$val) { ?>
				<tr style="width: 5%" class="text-start">
				<?php
				$i=0;
				foreach($val as $key2=>$val2) 
					if(!$i) {
						$i++; ?>
						<th class="text-wrap" scope="row"><?php echo $val2 ?></th>
					<?php } else { ?>
						<td style="width: 5%" class="text-center">
							<?php if(is_integer($val2)) echo number_format($val2,0,'','.'); else echo "-";?>
						</td>
					<?php } ?>
				</tr>	
			<?php }
			$ar["validi"][0]="Voti Validi";
			$ar["nulli"][0]="Voti Nulli";
			$ar["bianchi"][0]="Schede Bianche";
			$ar["contestati"][0]="Voti Contestati";
			foreach ($sezioni as $k=>$val2) {
				$ar["validi"][$val2['num_circ']]="0";
				$ar["nulli"][$val2['num_circ']]="0";
				$ar["bianchi"][$val2['num_circ']]="0";
				$ar["contestati"][$val2['num_circ']]="0";
			}
				$totv=0;
				$totn=0;
				$totb=0;
				$totc=0;
			foreach($sezioni as $key=>$val) {
				$ar["validi"][$val[2]]=$val[4];
				$ar["nulli"][$val[2]]=$val[5];
				$ar["bianchi"][$val[2]]=$val[6];
				$ar["contestati"][$val[2]]=$val[7];
				$totv+=$val[4];
				$totn+=$val[5];
				$totb+=$val[6];
				$totc+=$val[7];
			}
			?>
		</tbody>
		<tfoot>
			<tr class="primary-bg-c4 white-color align-middle text-start">
				<th scope="row">
					<?php echo $ar['validi'][0] ?>
				</th>
				<?php foreach($sezioni as $key=>$val) {?>
					<th class="text-center">
						<?php echo number_format($ar["validi"][$val['num_circ']],0,'','.') ?>
					</th>
				<?php } ?>
				<th class="text-center">
					<?php echo number_format($totv,0,'','.') ?>
				</th>
			</tr>	
			<tr class="primary-bg-c4 white-color align-middle text-start">
				<th scope="row"><?php echo $ar['nulli'][0] ?></th>
				<?php  foreach($sezioni as $key=>$val) {?>
					 <th class="text-center">
						<?php  echo number_format($ar["nulli"][$val['num_circ']],0,'','.') ?>
					 </th>
				<?php } ?>
				<th class="text-center"> 
					<?php echo number_format($totn,0,'','.') ?>
				</th>
			</tr>	
			<tr class="primary-bg-c4 white-color align-middle text-start">
				<th scope="row">
					<?php echo $ar['bianchi'][0] ?>
				</th>
				<?php foreach($sezioni as $key=>$val) { ?>
					<th class="text-center"> <?php echo number_format($ar["bianchi"][$val['num_circ']],0,'','.') ?></th>
				<?php } ?>
				<th class="text-center"> <?php echo  number_format($totb,0,'','.') ?></th>
			</tr>	
			<tr class="primary-bg-c4 white-color align-middle text-start">
				<th scope="row"> <?php  echo $ar['contestati'][0] ?></th>
				<?php foreach($sezioni as $key=>$val) {?>
					<th class="text-center"> <?php  echo number_format($ar["contestati"][$val['num_circ']],0,'','.') ?></th>
				<?php } ?>
				<th class="text-center"> <?php echo number_format($totc,0,'','.') ?></th>
			</tr>	
		</tfoot>
	</table>
</div>
