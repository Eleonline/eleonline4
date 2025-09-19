<?php 
//controllo se c'e un dato delle affluenze e attivare
$tab=_TABGRUPPO;
$elet=totale_iscritti(0);
$iscritti=$elet[0][2];
if($tipocons==10 or $tipocons==11 or $tipocons==15 or $tipocons==16)
	 $scrutinate=scrutinate('lista');
else
	$scrutinate=scrutinate($tab);
if($scrutinate) $spoglioattivo=1; else $spoglioattivo=0;
$sezionitotali=sezioni_totali();

$rowaff=ultime_affluenze($id_cons);
if(count($rowaff)) $affluenzaattivo=1; else $affluenzaattivo=0;
$tot1=0;
$tot2=0;
$row=scrutinio_schede($tab);
$schede_scrutinate=$row[0][0];
include 'electionday.php'; //Pagina Election Day
if(!$schede_scrutinate) $schede_scrutinate=0;
// Blocco affluenza mettere spoglioattivo==0 dopo aver sistemato le affluenze $tab
	if (($affluenzaattivo==1) and ($spoglioattivo==0)){
		$tipoconstemp=$tipocons;
		if($tipocons==10 or $tipocons==19) $tipocons="'10' or tipo_cons='19'"; 
		if($tipocons==11 or $tipocons==18) $tipocons="'11' or tipo_cons='18'"; 
		$idrif=precedente_consultazione();
		$tipocons=$tipoconstemp;
		$precedentepresente=count($idrif);
		if($precedentepresente){
			$tiporif=tipo_consultazione($idrif[0][0]);
			$row=conscomune($idrif[0][0]);
			if(count($row)) {
				$precedentiaffluenze=ultime_affluenze($row[0][0]); 
				if(!count($precedentiaffluenze)) $precedentepresente=0;
				$precedentiiscritti=totale_iscritti($row[0][0]);
				$spazio=strpos($tiporif[0][1],' ');
				if($spazio===false) $desc=$tiporif[0][1]; else $desc=substr($tiporif[0][1],0,strpos($tiporif[0][1],' '));
				$precedentedescrizione=$desc." ".substr($tiporif[0][8],0,4);
			}else $precedentepresente=0;
		}
		$rowora=affluenze_totali(0);
		$oraridate=array();
		$i=1;
		foreach($rowora as $val) {
			if(!isset($oraridate[$val['data']])) $oraridate[$val['data']]=1; else $oraridate[$val['data']]++;
		}
		$numeroorari=count($rowora);
		$giorno=array('domenica','lunedì','martedì','mercoledì','giovedì','venerdì','sabato');
		
		#<!--Tabella affluenza -->
		#<!--mettere $spoglioattivo==0 dopo aver sistemato tutto e funzionante--> 
		if ( $affluenzaattivo==1 and  $spoglioattivo==0) {?>
			<div class="table-responsive">
				<table id="table-votanti" class="table votanti">
					<thead>
						<tr>
							<?php foreach($oraridate as $key=>$val) {?> 
								<th scope="col" class="text-center col" colspan="<?php echo $val ?>">
									<?php echo $giorno[date_format(date_create($key) ,'w')] ?>
								</th>
							<?php } ?>
							<?php if($precedentepresente) {?>
								<th scope="col" class="text-center col" rowspan="2">
									Prec.<br><?php echo $precedentedescrizione; ?>
								</th> 
							<?php } ?>
						</tr>
						<tr>
						<?php foreach($rowora as $val) { 
							$sscru=affluenze_sezione(0,$val['data'],$val['orario'],0);
							$sezscru=count($sscru); ?>
							<th scope="col" class="text-center col">
								% ore <?php echo substr($val['orario'],0,5); if($sezscru<$sezionitotali) echo "<br>Sezioni $sezscru su $sezionitotali"; ?>
							</th>
						<?php } ?>
						</tr>
					</thead>
					<tbody>
						<tr>
							<?php 
							#$row=elenco_tot_affluenze();
							foreach($rowora as $key=>$val) { ?> 
								<td class="text-center"> 
									<?php echo number_format($val['complessivi'],0,'','.') ?> <br> <?php echo number_format(100*$val['complessivi']/$iscritti,2)?>%
								</td>
							<?php } ?>
							<?php if($precedentepresente) { ?>
								<td class="text-center">
									<?php echo number_format($precedentiaffluenze[0][0],0,'','.');?> <br> <?php echo number_format(100*$precedentiaffluenze[0][0]/$precedentiiscritti[0][2],2); ?>%
								</td>
							<?php } ?>
						</tr>
					</tbody>
				</table>
			</div> 
		<?php }?>
		<!-- fine Tabella affluenza -->
	<?php }?>
<!-- fine blocco affluenza-->
<!-- lista e preferneze-->
<!-- top dati scrutinate-->
<?php 
	$row=voti_totali();
	$validi=$row[0][0];
	if($scrutinate) {
		if($spoglioattivo)
			$row=scrutinio_tot_gruppo_finale($tab);
		else
			$row=scrutinio_tot_gruppo($tab);
		if($tab=='gruppo' and !$votolista)
			$rowlista=scrutinio_tot_lista_finale('lista');
		else
			$rowlista=voti_tot_lista();
		if($tab=='gruppo' and $votogruppo) { ;
			$row=elenco_gruppi($tab);
			foreach($rowlista as $val)
				if(!isset($valg[$val['num_gruppo']])) $valg[$val['num_gruppo']]=$val['votisum'];
				else $valg[$val['num_gruppo']]+=$val['votisum'];
			foreach($row as $k=>$val)
				$row[$k][3]=$valg[$val['num_gruppo']];
		}
		if(!count($rowlista))
			$rowlista=elenco_liste();
		$rowcand=array();
	###################
		if(!$votocandidato)
		{
			$iniziocand=scrutinate_inizio('candidati');
			if($iniziocand[0][0]>0) {
				$rowcand=scrutinio_tot_cand_finale(0);
			
			}else{
				$rowcand=elenco_candidati_liste(0);				
			}	
		}else{
			$rowcand=voti_tot_candidato(0);
			if(!count($rowcand)){
					$rowcand=elenco_candidati_liste(0);					
			}
		}
	}else{
		$row=elenco_gruppi($tab);
		$rowlista=elenco_liste();
		$rowcand=elenco_candidati_liste(0);
	}
	if (count($rowlista))
	{
		foreach($rowlista as $key=>$val)
		{
			$row2[$val[1]][]=$val;
			foreach($rowcand as $kcand=>$kval){
				if($kval[5]==$val['id_lista']) {$row3[$kval[1]][]=$kval;}
			}
		}
	}
?>
<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th>Voti per <?php echo _GRUPPO;?> e Preferenze </th>
				<?php if ($spoglioattivo==1) {?> 
					<?php if ($scrutinate == $sezionitotali) {?> 
						<th class="text-end">Dati finali</th>
					<?php } else {?>
						<th class="text-end">
							<span>Sezioni scrutinate: <?php echo $scrutinate; ?> su <?php echo $sezionitotali; ?> <br> 
							Schede scrutinate: <?php echo $schede_scrutinate; ?> su <?php if(isset($rowaff[0][0])) echo $rowaff[0][0]; else echo '0'; ?></span>
						</th>
					<?php }?>
				<?php }?>
			</tr>
		</thead>
	</table>
</div>
<!-- fine top dati scrutinate-->
<!-- inizio lista e preferneze-->	
<div class="accordion accordion-left-icon" id="accordionlistaprincipale">
	<div class="table-responsive overflow-x">
		<table class="accordion-item w-100">
			<thead class="table-light">
				<tr class="text-center ">
					<th class="primary-bg-c6" style="width: 5%" scope="col"></th>
					<?php if ($spoglioattivo==0) {?>
						<th class="primary-bg-c6 text-center" style="width: 5%" scope="col">#</th>
					 <?php }?> 
					<th class="primary-bg-c6" style="width: 10%" scope="col">Simbolo</th>
					<th class="primary-bg-c6" scope="col"><?php echo _GRUPPO; ?></th>
					<?php if ($spoglioattivo==1) { ?>
						<th class="primary-bg-c6" style="width: 5%" scope="col">Voti</th>
						<th class="primary-bg-c6" style="width: 5%" scope="col">%</th>
					<?php }?> 
					<?php if ($proiezioneattivo==1  and  $spoglioattivo==1) { ?>
							<th class="primary-bg-c6" style="width: 5%" scope="col"><!--Seggi--></th>
					<?php 
						$rowseggi=numseggilista();
						foreach($rowseggi as $rowval)
							$arseggi[$rowval['num_lista']]=$rowval['eletti'];
					}?> 
				</tr>
			</thead>
			<tbody class="align-middle">
				<?php
				if($genere==4 and isset($row3)) {
					$row2=$row3;
					unset($row3);
				} 
				?>
				<?php $i=0;$y=0; foreach($row as $campo=>$val) {?>
					<tr class="align-middle">
						<td>
							<?php 
							if(isset($row2[$val[1]]) and count($row2[$val[1]]) and $genere!=2) { ?>
								<div class=" " id="heading<?php echo $val[1];?>l"> 
									<button class="accordion-button border-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $val[1];?>l" aria-expanded="false" aria-controls="collapse<?php echo $val[1];?>l">
									</button>
								</div>
							<?php } ?>
						</td>
						<?php if ($spoglioattivo==0) {?>
							<td class="text-center"><?php echo $val[1];?></td>
						<?php }?> 
						<td>
							<div class="d-flex align-items-center justify-content-around flex-wrap flex-sm-nowrap my-1">
							<?php if(presenza_immagine($tab,$val["id_$tab"])) { ?>
								<div class="avatar simbolo">
									<img  src="modules.php?name=Elezioni&file=foto&id_<?php  echo $tab ?>=<?php  echo $val["id_$tab"] ?>" alt="<?php echo $val[2];?>">
								</div>
							<?php } ?>
							</div>
						</td>
						<td>
							<p class="text-start mx-2 align-middle d-inline"><?php echo $val[2]; $tot1+=$val[3];?>
								<?php if ($proiezioneattivo==1  and  $val[4]==2)
									{?>
									<span class="align-middle mx-2 text-primary">Eletto Sindaco</span>
								<?php }elseif ($proiezioneattivo==1  and  $val[4]==1)
									{?>
									<span class="align-middle mx-2 text-primary">Eletto Consigliere</span>
								<?php }?>
							</p>
						</td>
						<?php if ($spoglioattivo==1) {?>
							<td class="text-end">
								<p class="d-inline align-middle mx-1"><?php echo number_format($val[3],0,"",".");?></p>
							</td>
							<td class="text-end">
								<p class="d-inline align-middle mx-1"><?php if($validi) echo number_format(100*$val[3]/$validi,2); else echo '0';?>%</p>
							</td>
						<?php }?>
						<?php if ($proiezioneattivo==1  and  $spoglioattivo==1) {?>
							<td class="text-end">
								<!--p class="d-inline align-middle mx-1">num.</p-->
							</td>
						<?php }?> 
					</tr>
					<!-- test simbolo-->
					<tr> 
	<?php $col = 4; if ($spoglioattivo) { $col++; if ($proiezioneattivo) { $col++; }} ?>
	<td colspan="<?php echo $col; ?>" class="hiddenRow">
		<div id="collapse<?php echo $val[1]; ?>l" class="accordion-collapse collapse show" role="region" aria-label="heading<?php echo $val[1]; ?>l">
			<table class="table">
				<?php if (isset($row2[$val[1]]) && count($row2[$val[1]])) { ?>
					<thead class="table-light">
						<tr class="text-center">
							<?php if ($tab == 'gruppo') { ?>
								<th class="bg-white" style="width: 5%" scope="col"></th>
							<?php } ?>
							<?php if ($tab == 'gruppo') { ?>
								<th class="bg-white text-start" colspan="<?php echo count($row2[$val[1]]); ?>">
	<div class="fw-bold mb-2">Liste collegate:</div> <!-- Titolo aggiunto -->
	<div class="d-flex align-items-center flex-wrap gap-2">
		<?php foreach ($row2[$val[1]] as $val2) { ?>
			<?php if (presenza_immagine('lista', $val2['id_lista'])) { ?>
				<div class="avatar simbolo2">
					<img src="modules.php?name=Elezioni&file=foto&id_lista=<?php echo $val2['id_lista']; ?>" 
						 alt="<?php echo htmlspecialchars($val2[3], ENT_QUOTES, 'UTF-8'); ?>" 
						 class="img-simbolo">
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</th>

							<?php } ?>
						</tr>
					</thead>
				<?php } ?>
			</table>
		</div>
	</td>
</tr>


					<!-- fine test simboli --> 
					<tr> 
					<?php $col=4;$col2=6; if($spoglioattivo) {$col++; if($proiezioneattivo) {$col++; $col2++;}}?>
						<td colspan="<?php echo $col;?>" class="hiddenRow">
							<div id="collapse<?php echo $val[1];?>l" class="accordion-collapse collapse" role="region" aria-label="heading<?php echo $val[1];?>l">
								<table class="table">
									<?php if(isset($row2[$val[1]]) and count($row2[$val[1]])) { ?>
										<thead class="table-light">
											<tr class="text-center">
												<?php  if($tab=='gruppo') { ?>
													<th class="bg-white border-0" style="width: 5%" scope="col"></th>
													<th class="primary-bg-c6" style="width: 5%" scope="col"></th>
												<?php } else {?>
													<th class="bg-white border-0" style="width: 5%" scope="col"></th>
												<?php }?>
												<?php if ($spoglioattivo==0) {?>
													<th class="primary-bg-c6" style="width: 5%" scope="col">#</th>
												<?php }
												if($tab=='gruppo') {?>
													<th class="primary-bg-c6" style="width: 10%" scope="col">Simbolo</th>
												<?php }?>
												<th class="primary-bg-c6" scope="col">
												<?php  if($tab=='gruppo' or ($genere!=2 and $genere!=4)) echo "Liste"; else echo "Candidato"; ?></th>
												<?php if ($spoglioattivo==1 and $genere!=1) {?>
													<th class="primary-bg-c6 " style="width: 5%" scope="col">Voti</th>
													<th class="primary-bg-c6" style="width: 5%" scope="col">%</th>
												<?php }?>
												<?php if ($proiezioneattivo==1  and  $spoglioattivo==1) { ?>
													<th class="primary-bg-c6" style="width: 5%" scope="col">Seggi</th>
												<?php }?>
											</tr>
										</thead>
									<?php }?>
									<tbody class="align-middle">
										<?php 
										#elenco liste per gruppo
										#$row2=voti_tot_lista($val[1]); else $row2=voti_tot_candidato($val[1]);
										if(isset($row2[$val[1]]))
										foreach($row2[$val[1]] as $key2=>$val2) { 
										#for($coalizione = 1; $coalizione <= 3; $coalizione++) {
										?>
											<tr data-bs-toggle="collapse" class="accordion-toggle border-0 align-middle" data-bs-target="#lista1<?php echo $val[1];?><?php echo $val2[2];?>">
												<?php if($tab=='gruppo') {?>
													<td class="bg-white border-0"></td>
													<td class="bg-white border-0">
														<?php if($genere>2 and $genere!=4 and $tipo_cons!=10 and $tipo_cons!=11 or ($tipo_cons==18 or $tipo_cons==19)) { ?>
															<h2 class="" id="heading<?php echo $val[1].($i++).($y++);?>">&nbsp;
																<button class="accordion-button border-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#lista1<?php echo $val[1];?><?php echo $val2[2];?>" aria-expanded="false" aria-label="#lista1<?php echo $val[1];?><?php echo $val2[2];?>">
																</button>
															</h2>
														<?php } ?>
													</td>
												<?php } else {?>
													<td class="bg-white border-0"></td>
												<?php }?>
												<?php if ($spoglioattivo==0) {?>
													<td class="bg-white border-0 align-middle text-center"><?php echo $val2[2];?></td>
												<?php }?> 
												<?php if($tab=='gruppo') {?>
													<td class="bg-white border-0 ">
														<div class="d-flex align-items-center justify-content-around flex-wrap flex-sm-nowrap">
														<?php  if(presenza_immagine('lista',$val2['id_lista'])) {?>
															<div class="d-flex align-items-center justify-content-around flex-wrap flex-sm-nowrap">
																<div class="avatar simbolo"><img src="modules.php?name=Elezioni&file=foto&id_lista=<?php echo $val2['id_lista'];?>" alt="<?php echo $val2[3];?>">
																</div>
															</div>
														<?php
														} ?>
														</div>
													</td>
												<?php }?>
												<td class="bg-white border-0 align-middle">
													<p class="text-start mx-2 align-middle d-inline">
														<?php echo $val2[3];?>
													</p>
												</td>
												<?php if ($spoglioattivo==1 and $genere!=1) {?>									
													<td class="bg-white border-0 text-end"><p class="d-inline align-middle mx-1"><?php echo number_format($val2[4],0,"","."); $tot2+=$val2[4];?></p></td>
													<td class="bg-white border-0 text-end"><p class="d-inline align-middle mx-1"><?php if($validi) echo number_format(100*$val2[4]/$validi,2); else echo '0';?>%</p></td>
												<?php }?>
												<?php if ($proiezioneattivo==1  and  $spoglioattivo==1 ) {?>
													<td class="border-0 text-end"><p class="d-inline align-middle mx-1">
													<?php if(isset($arseggi[$val2['num_lista']]))echo $arseggi[$val2['num_lista']]; else echo "0"; ?>
													</p></td>
												<?php }?>
											</tr>
											<?php if($tab=='gruppo') { ?>
												<tr>
													<td colspan="<?php echo $col2;?>" class="hiddenRow border-0">
														<div class="accordian-body collapse" id="lista1<?php echo $val[1];?><?php echo $val2[2];?>">
															<table class="table table-striped">
																<thead>
																	<tr class="text-center">
																		<th class="bg-white border-0" style="width: 5%" scope="col"></th>
																		<th class="bg-white border-0" style="width: 5%" scope="col"></th>
																		<?php if ($spoglioattivo==0) {?>
																			<th class="primary-bg-c6" style="width: 5%" scope="col">#</th>
																		<?php }?>                                   
																		<th class="primary-bg-c6" scope="col">Candidato</th>
																		<?php if ($spoglioattivo==1 and $votocandidato==0) {?>
																			<th class="primary-bg-c6" style="width: 5%" scope="col">Voti</th>
																			<th class="primary-bg-c6" style="width: 5%" scope="col">%</th>
																		<?php }?>
																	</tr>
																	<?php 
																	if(isset($row3[$val2[2]]))
																	foreach($row3[$val2[2]] as $key3=>$val3) {?>
																		<tr class="align-middle">
																			<td class="bg-white border-0"></td>
																			<td class="bg-white border-0"></td>
																			<?php if ($spoglioattivo==0) {?>
																				<td class="border-0 align-middle text-center"><?php echo $val3[2];?></td>
																			<?php }?>                                      
																			<td class="border-0 ">
																				<p class="text-start mx-2 align-middle d-inline"><?php echo $val3[3];?>
																				<?php if ($proiezioneattivo==1  and  $val3[6]) {?>
																					<span class="align-middle mx-2 text-primary">Eletto Consigliere</span>
																				<?php }?>
																				</p>
																			</td>
																			<?php if ($spoglioattivo==1 and $votocandidato==0) {?>
																				<td class="border-0 text-end"><p class="d-inline align-middle mx-1"><?php echo number_format($val3[4],0,"",".");?></p></td>
																				<td class="border-0 text-end"><p class="d-inline align-middle mx-1"><?php if($validi) echo number_format(100*$val3[4]/$validi,2); else echo '0';?>%</p></td>
																			<?php }?>
																		</tr>
																	<?php }?>
																</thead>
															</table>
														</div>
													</td>
												</tr>
											<?php }
										}?>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				<?php }?>
				<!-- blocco totali-->
				<?php if($spoglioattivo==1){?>
					<tr class="border title-content mx-1">
						 <td><p class="d-inline align-middle mx-1">Totali</td>
						 <td colspan="2"><p class="d-inline align-middle mx-1"><?php echo _GRUPPI; ?></p></td>
						 <td class="text-end"><p class="d-inline align-middle mx-1"><?php echo $tot1; ?></p></td>
						 <td class="text-end"><p class="d-inline align-middle mx-1"><?php if(isset($rowaff[0][0]) and $rowaff[0][0]) echo number_format(100*$tot1/$rowaff[0][0],2); else echo '0';?>%</p></td>
						 <?php if ($proiezioneattivo==1  and  $spoglioattivo==1) {?>
							<td class="text-end"></td>
						 <?php }?>
					</tr>
					<?php if($genere==3 or $genere==5) { ?>
						<tr class="border title-content">
							 <td><p class="d-inline align-middle mx-1">Totali</p></td>
							 <td colspan="2"><p class="d-inline align-middle mx-1">Liste</p></td>
							 <td class="text-end"><p class="d-inline align-middle mx-1"><?php echo $tot2; ?></p></td>
							 <td class="text-end"><p class="d-inline align-middle mx-1"><?php if(isset($rowaff[0][0]) and $rowaff[0][0]>0) echo number_format(100*$tot2/$rowaff[0][0],2); else echo '0';?>%</p></td>
							 <?php if ($proiezioneattivo==1  and  $spoglioattivo==1) {?>
								<td class="text-end"></td>
							 <?php }?>					 
						</tr>
					<?php }
				}?>
				<!-- fine blocco totali-->
			</tbody>
		</table>
	</div>
</div>
<!-- fine lista e preferneze-->
<!-- totali votanti e schede-->
<?php if($spoglioattivo==1){?>

		
	<div class="container">
		<div class="row justify-content-center gx-2 gy-2 mb-3">
			<!-- Elettori -->
			<div class="col-auto me-2">
				<strong>Elettori:</strong> 
				<?php if(!isset($elet[0][2])) $elet[0][2]=0; echo number_format($elet[0][2], 0, ".", ".") ?>
			</div>
			<!-- Votanti -->
			<div class="col-auto me-2">
				<strong>Votanti:</strong> 
				<?php 
				if(!isset($rowaff[0][0])) $votanti=0; else $votanti = $rowaff[0][0] ; 
				echo number_format($votanti, 0, ".", ".");
				if(!isset($elet[0][2])) 
				$elettori = 0; else $elettori = $elet[0][2];
				$percentualeVotanti = ($elettori > 0) ? (100 * $votanti / $elettori) : 0;
				echo " (" . number_format($percentualeVotanti, 2) . "%)";
				?>
			</div>
			<!-- Schede -->
			<?php  $row=voti_totali();  if (($row[0][0] + $row[0][1] + $row[0][3]) > 0): ?>
				<?php 
				$schede = ['valide' => 0, 'nulle' => 1, 'bianche' => 2, 'contestate' => 3];
				foreach ($schede as $nome => $indice): 
				  $conteggio = intval($row[0][$indice]);
				  $percentuale = ($votanti > 0) ? (100 * $conteggio / $votanti) : 0;
				?>
				<div class="col-auto me-2">
					<strong>Schede <?= $nome ?>:</strong> 
					<?php echo number_format($conteggio, 0, ".", ".") ?> 
					(<?php echo number_format($percentuale, 2) ?>%)
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
		</div>
	</div>
<?php }?>
<!-- fine totali votanti e schede-->