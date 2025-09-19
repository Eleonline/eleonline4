<?php
$row=dati_generali();
$numcirco=$row[3]; 
global $idcirc,$circo;
if(isset($_GET['idcirc'])) $idcirc=intval($_GET['idcirc']); 
if($circo) $cirpar="&idcirc=$idcirc"; else $cirpar='';
?>
<main>
	<div class="container">
		<div class="row text-center">
			<h4 class="fw-semibold text-primary mobile-expanded mt-2">Dati Generali</h4>
		</div>
	</div>
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
		<div class="table-responsive overflow-x">
			<table class="table table-striped table-bordered table-sm align-middle">
				<thead class="table-light mx-auto">
					 <tr>
						<th class="primary-bg-c6" scope="col">Sezioni nella sede</th>
						<?php if($row[3]>1 and 0){?>
						<th class="primary-bg-c6" scope="col">Circoscrizione</th>
						<?php  }?>
						<th class="primary-bg-c6" scope="col">Indirizzo</th>
						<th class="primary-bg-c6" scope="col">Telefono</th>
						<th class="primary-bg-c6" scope="col">Telefono</th>
						<th class="primary-bg-c6" scope="col">Mappa</th>
						<th class="primary-bg-c6" scope="col"></th>
					 </tr>
				</thead>
				<tbody>
					<?php
					$row=elenco_sedi(1);
					$sezioni=elenco_sezioni(0);
			#		$righe=$result1->rowCount();$i=0;
					$i='';
					$y=0;
					foreach($row as $campo=>$val) {
						if($y!=$val[7] and $numcirco>1) { $y=$val[7];
							?>
							<thead class="mx-auto">
								<tr class="primary-bg-c1 border" scope="row">
									<th class="border-0" colspan="13">Circoscrizione n.<?php echo $val[7];?> : <?php echo $val[6];?></th>
								</tr>
							</thead>
						<?php } ?>
						<tr>
							<td scope="row">
							<?php 
							$i=0; 
							foreach ($sezioni as $numsez) 
								if($numsez[2]==$val['id_sede']) {
									if($i++>0) echo " - ";
								echo $numsez[3];
								}
							?>
							</td>
							<td><?php echo $val[1];?></td>
							<td><?php echo $val[2];?></td>
							<td><?php echo $val[3];?></td>
							<td>
								<?php
								if(strlen($val['latitudine'])>0 and strlen($val['longitudine'])>0) $query=$val['latitudine'].",".$val['longitudine'];
								else {
									// Usa indirizzo + nome sito
									$query = $val[1] . ' ' . $sitename;									
								}
								$url_mappa = "https://www.google.com/maps/search/" . urlencode($query);
								?>
								<a href="<?php echo $url_mappa; ?>" target="_blank"><span>MAPPA</span></a>
							</td>
							<td>
								<?php
								$id_sede=$val['id_sede'];
								?>
								<a href="modules.php?op=30&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>&id_sede=<?php echo $id_sede;?>">
									<svg class="icon"><use href="temi/bootstrap/svg/sprites.svg#it-note"></use></svg>
								</a>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</main>