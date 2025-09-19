<?php
$row=dati_generali();
$numcirco=$row[3];
$arsedi=elenco_sedi(1);
global $idcirc,$circo;
if(isset($_GET['idcirc'])) $idcirc=intval($_GET['idcirc']); 
if($circo) $cirpar="&idcirc=$idcirc"; else $cirpar='';
if(isset($_GET['id_sede']) and intval($_GET['id_sede'])>0) 
	$id_sede=intval($_GET['id_sede']); 
else 
	$id_sede=$arsedi[0][0];
foreach ($arsedi as $key=>$val) $sediid[]=$val[0];
$sedecorrente=array_search($id_sede,$sediid);
$idsedecorrente=$sediid[$sedecorrente];
if($sedecorrente) $idsedeprecedente=$sediid[$sedecorrente-1]; else $idsedeprecedente='';
if((count($sediid)-1)>$sedecorrente) $idsedesuccessiva=$sediid[$sedecorrente+1]; else $idsedesuccessiva='';

$row2=dati_sede($id_sede);
if(!$idcirc) $idcirc=$row2[0]['id_circ'];
?>
<div class="table-responsive overflow-x">
	<table class="table table-striped table-bordered table-sm align-middle">
		<thead class="table-light">
			<tr class="title-content">
				<th class="primary-bg-c11" scope="col">Aventi Diritto</th>
				<th class="primary-bg-c11" scope="col">Maschi</th>
				<th class="primary-bg-c11" scope="col">Femmine</th>
				<?php if($row[3]>1){?>
					<th class="primary-bg-c11" scope="col">Circoscrizioni</th>
				<?php  }?>
				<th class="primary-bg-c11" scope="col">Sedi elettorali</th>
				<th class="primary-bg-c11" scope="col">Sezioni</th>
				<?php if (_GRUPPI!='Liste') { ?>
					<th class="primary-bg-c11" scope="col"><?php echo _GRUPPI; ?></th>
				<?php } ?>
				<?php if($genere!=0) { ?>
					<th class="primary-bg-c11" scope="col">Liste</th>
					<?php if($genere>2) { ?>
						<th class="primary-bg-c11" scope="col">Candidati</th>
					<?php }?>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<tr class="text-end">
				<td><?php echo $row[0];?></td>
				<td><?php echo $row[1];?></td>
				<td><?php echo $row[2];?></td>
				<?php if($row[3]>1){?>
					<td><?php echo $row[3];?></td>
				<?php }?>
				<td><?php echo $row[4];?></td>
				<td><?php echo $row[5];?></td>
				<?php if (_GRUPPI!='Liste') { ?>
					<td><?php echo $row[6];?></td>
				<?php }?>
				<?php if($genere!=0) { 
						if($genere==2) {?>
							<td><?php echo $row[6];?></td>
						<?php }else{ ?>
							<td><?php echo $row[7];?></td>
						<?php }?>
				<?php if($genere>2)
					echo "<td>".$row[8]."</td>";?>
				<?php }?>
			</tr>
		</tbody>
	</table>
</div>
<div class="d-flex justify-content-center">
	<div class="table-responsive  overflow-x">
		<table class="table table-striped table-bordered table-sm align-middle">
			<thead class="table-light mx-auto">
				<tr>
					<th class="primary-bg-c6" scope="col">Sezioni nella sede</th>
					<?php if($row[3]>1){?>
					<th class="primary-bg-c6" scope="col">Circoscrizione</th>
					<?php  }?>
					<th class="primary-bg-c6" scope="col">Sede elettorale</th>
					<th class="primary-bg-c6" scope="col">Telefono</th>
					<th class="primary-bg-c6" scope="col">Telefono</th>
					<th class="primary-bg-c6" scope="col">Mappa</th>
				</tr>
			</thead>
			<tbody>
			  	<tr>
					<td> 
						<?php  $rowsez=elenco_sezioni($id_sede); $i=0; foreach($rowsez as $valsez) {if($i++>0) echo ', '; echo $valsez[3]; } ?>
					</td>
					<?php if($row[3]>1){
						$elenco=elenco_circoscrizioni();
						foreach($elenco as $nome) 
							if($nome['id_circ']==$idcirc) { 
							$nomecirc=$nome['descrizione']; break;}?>
						<td><?php echo "$nomecirc"; ?></td>
					<?php  }?>	
					<td><?php echo $row2[0][3];?></td>
					<td><?php echo $row2[0][4];?></td>
					<td><?php echo $row2[0][5];?></td>
					<td>
						<?php if(strlen($row2[0]['latitudine'])>0 and strlen($row2[0]['longitudine'])>0) $indirizzo=$row2[0]['latitudine'].",".$row2[0]['longitudine'];
						else
						$indirizzo=$row2[0][3]." ".$sitename;?>
						<a href="https://maps.google.com/maps/search/<?php echo urlencode($indirizzo);?>" target="_blank"><span>MAPPA</span></a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<!-- Paginazione sedi -->
<div class="d-flex justify-content-center">
	<div class="table-responsive  overflow-x">
		<table class="table table-sm align-middle">
			<tbody>
			  	<tr>
					<td  class="<?php if(!$idsedeprecedente) { ?>page-item d-none disabled <?php } ?>pe-5">
						 <a class="page-link" href="modules.php?op=30&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>&id_sede=<?php echo $idsedeprecedente;?>">
						 <svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chevron-left"></use></svg>
						 Sede precedente</a>
					 </td>
					 <td  class="<?php if(!$idsedesuccessiva) { ?>page-item d-none disabled<?php } ?> ps-5">
						 <a class="page-link" href="modules.php?op=30&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>&id_sede=<?php echo $idsedesuccessiva;?>" tabindex="-1" >
						 Sede successiva
						 <svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chevron-right"></use></svg></A>
					 </td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<!-- fine Paginazione sedi -->
<div class="d-flex justify-content-center">
	<div class="table-responsive  overflow-x">
	<?php if(strlen($row2[0]['filemappa'])>0) { ?>
	<img src="modules.php?name=Elezioni&amp;file=foto&amp;id_sede=<?php echo $id_sede;?>" alt=\"mappa\" >
	<?php } ?>
		<table class="table table-striped table-bordered table-sm align-middle">
			<thead class="table-light mx-auto">
				 <tr>
					<th class="primary-bg-c6" scope="col">Sezione</th>
					<th class="primary-bg-c6" scope="col">Maschi</th>
					<th class="primary-bg-c6" scope="col">Femmine</th>
					<th class="primary-bg-c6" scope="col">Totali aventi Diritto</th>
				 </tr>
			</thead>
			<tbody>
			<?php
				$rowsez=elenco_sezioni($id_sede);
				$totm=0; $totf=0; $tott=0; $contasedi=0;
				foreach($rowsez as $val) {
					$contasedi++;
					$totm+=$val[4];
					$totf+=$val[5];
					$tott+=$val[4]+$val[5];	?>
					<tr class="text-end">
						<td><?php echo $val[3]; ?></td>	
						<td><?php echo $val[4]; ?></td>
						<td><?php echo $val[5]; ?></td>
						<td><?php echo $val[4]+$val[5]; ?></td>
					</tr>
				<?php } ?>	
			</tbody>
			<?php if ($contasedi>1) {?>
				<tfoot>
					<tr class="primary-bg-c4 white-color align-middle text-end">
						<th scope="row">Totale</th>
						<th scope="col"><?php echo $totm; ?></th>
						<th scope="col"><?php echo $totf; ?></th>
						<th scope="col"><?php echo $tott; ?></th>
					</tr>
				</tfoot>
			<?php }?>
		</table>
	</div>
</div>