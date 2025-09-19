<?php
$row=dati_consultazione(0);
$tipo=$row[0][4];
$affluenze=affluenze_referendum(0,0);
$sezionitotali=sezioni_totali();
$listareferendum=elenco_gruppi('gruppo');
$scrutinatetemp=scrutinate_referendum();
$iscrittitemp=totale_iscritti(0);
$iscritti=$iscrittitemp[0]['elettori'];
foreach($scrutinatetemp as $key=>$val){
	if(!isset($scrutinate[$val[1]])) $scrutinate[$val[1]]=0;
	$scrutinate[$val[1]]++; #echo "<br>TEST: ".$val[1]." - ".$scrutinate[$val[1]];
}
$votitemp=voti_tot_referendum();
foreach($votitemp as $key=>$val){
	if(!isset($si[$val[0]])) {
		$si[$val[0]]=0;
		$no[$val[0]]=0; 
		$validi[$val[0]]=0; 
		$nulli[$val[0]]=0; 
		$bianchi[$val[0]]=0; 
		$contestati[$val[0]]=0; 
	}
	$si[$val[0]]+=$val[2]; 
	$no[$val[0]]+=$val[3]; 
	$validi[$val[0]]+=$val[4];
	$nulli[$val[0]]+=$val[5]; 
	$bianchi[$val[0]]+=$val[6]; 
	$contestati[$val[0]]+=$val[7]; 
#	echo "<br>TEST: ".$val[0]." - ".$val['si']." - ".$val[2];
}
if(count($affluenze)>0) $affluenzaattivo=1; else $affluenzaattivo=0;
if(count($scrutinatetemp)>0) $spoglioattivo=1; else $spoglioattivo=0;
$i=0;
# colore dello sfondo del quesito
include_once('config_colori_quesiti.php');
$idQuesito = $val['id_quesito'] ?? 0;
$coloreSfondo = isset($coloriQuesiti[$idQuesito]) ? $coloriQuesiti[$idQuesito] : '#ffffff'; 
 ?>
<?php include 'electionday.php'; //Pagina Election Day?>
<table class="table table-bordered table-sm align-middle">
	<thead class="table-light">
		<tr>
			<th class="primary-bg-c1 text-center" scope="col">Quesito Referendario </th>
		</tr>
	</thead>
</table>
<!--Blocco referendum -->
<?php
$c=0;
foreach($listareferendum as $key=>$val) {
	$affluenzatemp=ultime_affluenze_referendum($val['id_gruppo']);
	if(count($affluenzatemp))
	$affluenza=$affluenzatemp[0][0]; 
// prova dei colori mettere il vaolre del colora preso dal db
$c = $c= $c + 1 ;
$coloreSfondo = isset($coloriQuesiti[$c]); // colore di default
?>
	<div class="box_referendum">
		<div class="row box_testo_ref">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="dati_referendum_titolo_quesito" <?php if (!empty($coloreSfondo) && $coloreSfondo != '0') echo ' style="background-color: ' . $coloreSfondo . ';"'; ?>>
				
					<table class="align-middle" style="width: 100%">
						<tbody>
							<tr>
								<td>
									<?php $i = $i + 1; ?>
									<p>Quesito n. <?php echo $i; ?></p>
								</td>
								<td class="text-center">
									<?php if (presenza_immagine('gruppo', $val['id_gruppo'])) { ?>
										<div>Scheda</div>
										<?php
											$facext = strtoupper(substr(stripslashes($val['prognome']), -4));
											$presenza_facsimile = ($facext == '.PDF') ? 1 : 0;
											$facsimile_url = "modules.php?name=Elezioni&amp;file=foto&amp;id_gruppo=" . $val['id_gruppo'] . "&amp;pdfvis=1";
											$img_url = "modules.php?name=Elezioni&file=foto&id_gruppo=" . $val['id_gruppo'];
										?>
										<?php if ($presenza_facsimile) { ?>
											<a href="<?php echo $facsimile_url; ?>" target="_blank" class="text-decoration-none d-block">
												<img src="<?php echo $img_url; ?>" class="img-scheda" alt="scheda">
												<div>Fac-simile</div>
											</a>
										<?php } else { ?>
											<img src="<?php echo $img_url; ?>" class="img-scheda" alt="scheda">
										<?php } ?>
									<?php } ?>
								</td>
							</tr>
						</tbody>
					</table>
					<?php echo $val[2]; ?>
				</div>
			</div>
		</div>
	</div>

		<?php //$oplink="come"; $infolink="affluenze_sez"; include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>	
		<!--blocco affluenza-->
		<?php if ( $affluenzaattivo==1 and  $spoglioattivo==0) {?>	
			<div class="row box_elevot">
					<table class="dati_riepilogo">
						<tbody>
							<tr>
								<?php
								if(isset($scrutinate[$val['id_gruppo']])) {
									if ($scrutinate[$val['id_gruppo']] == $sezionitotali) { ?> 
										<th class="text-center box_elevot_title">Dati finali</th>
									<?php } else {?>
										<th class="text-center box_elevot_title">Sezioni scrutinate: <?php echo $scrutinate[$val['id_gruppo']]; ?> su <?php echo $sezionitotali; ?></th>
									<?php }
								}?>
							</tr>
						</tbody>
					</table>
				</div>
			<div class="row box_elevot">
				<?php
				foreach($affluenze as $aff) { 
					if($val['id_gruppo']!=$aff[0]) continue; 
				?>
				  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<table class="dati_riepilogo">
						<tbody>
							<tr>
								<th colspan="3" class="text-left box_elevot_title">Affluenza <?php echo date_format(date_create($aff[2]) ,'d/m/Y')." ORE ".substr($aff[3],0,5);?></th>
							</tr>
							<tr>
								<th id="hvotanti0" scope="row">Votanti</th>
								<td class="bd_r" headers="hvotanti0 hquesito0"><?php echo number_format($aff[1],0,'','.');?></td>
							</tr>
							<tr>
								<th id="hvotanti0" scope="row">%</th>
								<td class="bd_r" headers="hvotivalidi0 hquesito0"><?php echo number_format(100*$aff[1]/$iscritti,2); ?></td>
							</tr>
						</tbody>
					</table>
				  </div>	
				<?php } ?>
			</div>
		<?php } ?>			
		<!--fine blocco affluenza-->	
		<!--blocco schede totali votanti-->
		<?php if ($spoglioattivo==1) {?> 		
			<div class="container">
				<div class="row box_elevot">
					<table class="dati_riepilogo">
						<tbody>
							<tr>
								<?php
								if(isset($scrutinate[$val['id_gruppo']])) {
									if ($scrutinate[$val['id_gruppo']] == $sezionitotali) { ?> 
										<th class="text-center box_elevot_title">Dati finali</th>
									<?php } else {?>
										<th class="text-center box_elevot_title">Sezioni scrutinate: <?php echo $scrutinate[$val['id_gruppo']]; ?> su <?php echo $sezionitotali; ?></th>
									<?php }
								}?>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row box_elevot">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<?php if(isset($affluenza)){ ?>
						<table class="dati_riepilogo">
							<tbody>
								<tr>
									<th id="hquesito<?php echo $val['id_gruppo'];?>" colspan="2" class="text-left box_elevot_title">Affluenza <?php echo date_format(date_create($affluenzatemp[0][3]) ,'d/m/Y')." ORE ".substr($affluenzatemp[0][4],0,5); ?></th>
								</tr>
								<tr>
									<th id="hvotanti<?php echo $val['id_gruppo'];?>" scope="row">Votanti</th>
									<td class="bd_r" headers="hvotanti<?php echo $val['id_gruppo'];?>"><?php echo number_format($affluenza,0,'','.'); ?></td>
								</tr>
								<tr>
									<th id="hpercvotanti<?php echo $val['id_gruppo'];?>" scope="row">%</th>
									<td class="bd_r" headers="hpercvotanti<?php echo $val['id_gruppo'];?>"><?php echo number_format(100*$affluenza/$iscritti,2); ?> </td>
								</tr>
							</tbody>
						</table>
					<?php } unset($affluenza); ?>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 box_schede">
					<?php if(isset($validi[$val['id_gruppo']])) { ?>
						<table class="dati_riepilogo">
							<tbody>
								<tr>
									<th colspan="2" class="text-left box_elevot_title">Schede</th>
								</tr>
								<tr>
									<th id="hvotivalidi<?php echo $val['id_gruppo'];?>" scope="row">Valide</th>
									<td class="bd_r" headers="hvotivalidi<?php echo $val['id_gruppo'];?>"><?php echo number_format($validi[$val['id_gruppo']],0,'','.'); ?></td>
								</tr>
								<tr>
									<th id="hskbianche<?php echo $val['id_gruppo'];?>" scope="row">Schede bianche</th>
									<td class="bd_r" headers="hskbianche<?php echo $val['id_gruppo'];?>"><?php echo number_format($bianchi[$val['id_gruppo']],0,'','.'); ?></td>
								</tr>
								<tr>
									<th id="hsknonvalide<?php echo $val['id_gruppo'];?>" scope="row">Schede non valide (bianche incl.)</th>
									<td class="bd_r" headers="hsknonvalide<?php echo $val['id_gruppo'];?>"><?php echo number_format($bianchi[$val['id_gruppo']]+$nulli[$val['id_gruppo']]+$contestati[$val['id_gruppo']],0,'','.'); ?></td>
								</tr>
							</tbody>
						</table>
					<?php } ?>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<?php if(isset($si[$val['id_gruppo']])) { ?>
						<table class="dati">
							<tbody>
								<tr>
									<th id="hsi<?php echo $val['id_gruppo'];?>" scope="row" style="font-size:3rem; font-weight:bold; text-align:center; color:#5a6772;">SI</th>
									<th id="hno<?php echo $val['id_gruppo'];?>" scope="row" style="font-size:3rem; font-weight:bold; text-align:center; color:#5a6772;">NO</th>
								</tr>
								<tr>
									<td headers="hsi<?php echo $val['id_gruppo'];?>" class="text-center"><?php echo number_format($si[$val['id_gruppo']],0,'','.'); ?></td>
									<td headers="hno<?php echo $val['id_gruppo'];?>" class="text-center"><?php echo number_format($no[$val['id_gruppo']],0,'','.'); ?></td>
								</tr>
								<tr>
									<td headers="hsi<?php echo $val['id_gruppo'];?>" class="text-center"><?php echo number_format(100*$si[$val['id_gruppo']]/$validi[$val['id_gruppo']],2); ?>%</td>
									<td headers="hno<?php echo $val['id_gruppo'];?>" class="text-center"><?php echo number_format(100*$no[$val['id_gruppo']]/$validi[$val['id_gruppo']],2); ?>%</td>
								</tr>
							</tbody>
						</table>
					<?php } ?>
				</div>
			</div>
		<?php }?>
	<!--fine blocco schede totali votanti-->	
	</div>
<?php }?>	
<!-- Fine Blocco referendum -->