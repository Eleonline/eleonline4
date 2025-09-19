<?php
$row=conscomune(0);
$idfascia=$row[0]['id_fascia'];
$idconf=$row[0]['id_conf'];
if($idconf>0 and $idfascia>0){
	$row=dati_fascia($idconf,$idfascia);
	$abitanti=$row[0][1];
}else{
	$row=dati_comune();
	if($row[0]['fascia']>3) $abitanti=30000; else $abitanti=10000;
}
/*visibilitatrasparenza
 0 - Visualizza solo Programma Elettorale (per le amministrativa con meno di 15000 abitanti)
 1 - Ente di competenza (tutti i tipi di consultazione escluso amministrative)
 2 - Amministrativa (Superiori a 15000 abitanti)
*/
if( $tipo_cons==3 or $tipo_cons==4) {
	if($abitanti<=15000 or $genere==0)
		$visibilitatrasparenza=0;
	else {
		$visibilitatrasparenza=2;
	}	
}else{
	$visibilitatrasparenza=1;
}
$docdir="documenti/$id_comune/$id_cons_gen/";
$docdirgruppi="documenti/$id_comune/$id_cons_gen/gruppi/"; ?>
<div class="table-responsive overflow-x">
	<table class="table table-bordered table-sm align-middle">
		<thead class="table-light">
			<tr>
				<th class="primary-bg-c1 text-center" scope="col">Elezioni trasparenti</th>
			</tr>
		</thead>
	</table>
	<blockquote class="blockquote blockquote-simple primary-bg-c1 text-start">
		<p class="m-3">
			<?php echo $leggeTrasparenza; ?>
		</p>
	</blockquote>
	<!---Visualizza i Comuni sotto 15000 abitanti solo Programma Elettorale-->
	<?php if($visibilitatrasparenza==0) { ?>
		<?php $rowg=elenco_gruppi_trasparenza();?>
			<?php foreach($rowg as $valg) { ?> 
				<table class="table mb-0">
					<thead class="table-light">
						<tr class="text-center ">
							<th class="primary-bg-c11" style="width: 10%" scope="col">
							
							</th>
							<th class="primary-bg-c11" scope="col"><?php echo _GRUPPO;?></th>
							<th class="primary-bg-c11" style="width: 10%" scope="col">Programma Elettorale</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="border-0" scope=row">
								<div class="d-flex align-items-center justify-content-around flex-wrap flex-sm-nowrap">
									<div class="avatar simbolo"><img src="modules.php?name=Elezioni&file=foto&id_gruppo=<?php echo $valg['id_gruppo'];?>" alt="<?php echo $valg[3];?>">
									</div>
								</div>
							</td>
							<td class="border-0"><?php echo $valg[2].") ".$valg[3] ?></td>
							<td class="align-middle border-0">
								<span class="d-block text-center">
									<?php if(strlen($valg[8])>0) { ?>
										<a href="modules.php?name=Elezioni&file=foto&pdfvis=1&id_gruppo=<?php echo $valg['id_gruppo'];?>" target="_blank" title="Programma" alt="Programma">
											<svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-file-pdf-ext"></use></svg>
										</a>
									<?php } else { ?>
										<span class="d-block text-center">
											<svg class="icon icon-secondary">
											<use href="temi/bootstrap/pagine/img/no_pdf.svg#it-no_pdf"></use>
											</svg>
										</span>
									<?php } ?>
								</span>
							</td>
						</tr>
					</tbody>
				</table>
		<?php }	?>
	<?php } ?>
	<!-- Visualizzazione Link estereni tutti i tipi di consultazione escluso amministrative-->
	<?php if (($visibilitatrasparenza==1) and (isset($linktrasp))) { ?>
		Link dell'ente di competenza: 
		<a href="<?php echo $linktrasp; ?>" target="_blank"><span> <?php echo $linktrasp; ?><br></span></a>
	<?php }	?>
	<!-- Fine Visualizzazione Link estereni tutti i tipi di consultazione escluso amministrative-->
	<!-- Visualizzazione scheda solo per le amministrative-->
	<?php if($visibilitatrasparenza==2) { ?>
		<div class="accordion accordion-left-icon border-0" id="accordionlistaprincipale">
			<?php $rowg=elenco_gruppi_trasparenza();?>
			<?php foreach($rowg as $valg) { ?> 
				<!-- Candidati a sindaco-->
				<?php $linksindaco=0;?>
				<table class="table mb-0">
					<thead class="table-light">
						<tr class="text-center text-white ">
							<th class="primary-bg-c11" style="width: 5%" scope="col">
							#
							</th>
							<th class="primary-bg-c11" style="width: 10%" scope="col">
							
							</th>
							<th class="primary-bg-c11" scope="col"><?php echo _GRUPPO;?></th>
							<?php if ($linksindaco==1) {?>
								<th class="primary-bg-c11" style="width: 10%" scope="col">Link</th>
							<?php } else { ?>
								<th class="primary-bg-c11" style="width: 10%" scope="col">Curriculum vitae</th>
								<th class="primary-bg-c11" style="width: 10%" scope="col">Certificato penale</th>
								<th class="primary-bg-c11" style="width: 10%" scope="col">Programma Elettorale</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="border-0 align-middle text-center"><?php echo $valg[2] ?></td>
							<td class="border-0 align-middle" scope=row">
								<div class="d-flex align-items-center justify-content-around flex-wrap flex-sm-nowrap">
									<div class="avatar simbolo"><img src="modules.php?name=Elezioni&file=foto&id_gruppo=<?php echo $valg['id_gruppo'];?>" alt="<?php echo $valg[3];?>">
									</div>
								</div>
							</td>
							<td class="border-0 align-middle"><?php echo $valg[3] ?></td>
							<?php if ($linksindaco==1) {?>
									<td class="align-middle border-0">
										<span class="d-block text-center">
											<a href="#" target="_blank" title="Link" alt="link">
												<svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-link"></use></svg>
											</a>
										</span>
									</td>
								<?php } else { ?>
							<td class="align-middle border-0">
								<span class="d-block text-center border-0">
									<?php if(is_file($docdirgruppi."cv/".$valg[10])) { ?>
										<a href="<?php echo $docdirgruppi."cv/".$valg[10]; ?>" target="_blank" title="Curriculum vitae" alt="Curriculum vitae">
										<svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-file-pdf-ext"></use></svg>
										</a>
									<?php }else{?>
											<span class="d-block text-center">
												<svg class="icon icon-secondary">
												<use href="temi/bootstrap/pagine/img/no_pdf.svg#it-no_pdf"></use>
												</svg>
											</span>
									<?php } ?>
								</span>
							</td>
							<td class="align-middle border-0">
								<span class="d-block text-center">
									<?php if(is_file($docdirgruppi."cg/".$valg[11])) { ?>
										<a href="<?php echo $docdirgruppi."cg/".$valg[11]; ?>" target="_blank" title="Certificato penale" alt="Certificato penale">
										<svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-file-pdf-ext"></use></svg>
									</a>
									<?php }else{?>
											<span class="d-block text-center">
												<svg class="icon icon-secondary">
												<use href="temi/bootstrap/pagine/img/no_pdf.svg#it-no_pdf"></use>
												</svg>
											</span>
									<?php } ?>
								</span>
							</td>
							<td class="align-middle border-0">
								<span class="d-block text-center">
									<?php if(strlen($valg[8])>0) { ?>
										<a href="modules.php?name=Elezioni&file=foto&pdfvis=1&id_gruppo=<?php echo $valg['id_gruppo'];?>" target="_blank" title="Programma" alt="Programma">
										<svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-file-pdf-ext"></use></svg>
									</a>
							<?php }else{?>
									<span class="d-block text-center">
										<svg class="icon icon-secondary">
										<use href="temi/bootstrap/pagine/img/no_pdf.svg#it-no_pdf"></use>
										</svg>
									</span>
							<?php } ?>
								</span>
							</td>
							<?php } ?>
						</tr>
					</tbody>
				</table>
				<!-- Fine Candidati al sindaco <?php if (isset($linklista) and $linklista==1) {?>
									<th class="primary-bg-c6" style="width: 10%" scope="col">Link</th>
								<?php } else { ?>								<?php } ?>
-->
				<!-- Liste collegate-->
					<table class="table mb-0">
						<thead class="table-light">
							<tr class="text-center">
								<th class="bg-white border-0" style="width: 5%" scope="col"></th>
								<th class="primary-bg-c6" style="width: 5%" scope="col">#</th>
								<th class="primary-bg-c6" style="width: 10%" scope="col">Simbolo</th>
								<th class="primary-bg-c6" scope="col">Partito/Movimento/Gruppo politico</th>
								
									<th class="primary-bg-c6" style="width: 5%" scope="col"></th>
							</tr>
						</thead>
						<tbody class="align-middle">				
				<?php $rowl=elenco_liste_gruppo($valg[1]); ?>
				<?php foreach($rowl as $vall) { 
					if(strlen($vall[5])>0) $linklista=1; else $linklista=0; 
				?>

							<tr class="border-0">
								<td class="bg-white border-0" scope=row"></td>
								<td class="bg-white border-0 align-middle text-center">
									<p class="mx-2 align-middle d-inline">
										<?php echo $vall[2]?>
									</p>
								</td>
								<td class="bg-white border-0 align-middle">
									<div class="d-flex align-items-center justify-content-around flex-wrap flex-sm-nowrap">
											<?php if(presenza_immagine('lista',$vall['id_lista'])) { ?>
										<div class="avatar simbolo">
											<img  src="modules.php?name=Elezioni&file=foto&id_lista=<?php echo $vall['id_lista'];?>" alt="simbolo">
										</div>
											<?php } ?>
									</div>
								</td>
								<td class="bg-white border-0 align-middle">
									<p class="text-start mx-2 align-middle d-inline">
										<?php echo $vall[3]; ?>
									</p>
								</td>
								<?php if ($linklista==1) {?>
									<td class="align-middle border-0">
										<span class="d-block text-center">
											<a href="<?php echo $vall[5]?>" target="_blank" title="Link" alt="link">
												<svg class="icon icon-primary"><use href="<?php echo $curdir;?>/svg/sprites.svg#it-link"></use></svg>
											</a>
										</span>
									</td>
								<?php } else { ?>
									<td class="align-middle border-0">
										<div class=" " id="heading<?php echo $vall[2];?>"> 
											<button class="accordion-button border-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $vall[2];?>" aria-expanded="false" aria-controls="collapse<?php echo $vall[2];?>">
											</button>
										</div>
									</td>
								<?php } ?>
							</tr>
							<tr><td colspan="5">
					<!-- fine Liste collegati-->
					<!-- Candidari Consiglieri-->
					<?php if ($linklista==0) {
						$rowc=elenco_candidati($vall['id_lista']);
						?>
						<div id="collapse<?php echo $vall[2];?>" class="accordion-collapse collapse" data-bs-parent="#accordionlistaprincipale" role="region" aria-label="heading<?php echo $vall[2];?>">
							<table class="table mb-0">
								<thead class="table-light">
									<tr class="text-center">
										<th class="bg-white border-0" style="width: 5%" scope="col"></th>
										<th class="bg-white border-0" style="width: 5%" scope="col"></th>
										<th class="primary-bg-c6" scope="col">Candidato</th>
										<th class="primary-bg-c6" style="width: 10%" scope="col">Curriculum vitae</th>
										<th class="primary-bg-c6" style="width: 10%" scope="col">Certificato penale</th>
									</tr>
								</thead>
								<?php foreach($rowc as $valc) { ?>
									<tbody>
										<tr>
											<td class="bg-white border-0" scope=row"></td>
											<td class="bg-white border-0"></td>
											<td scope="row"><?php echo $valc[3] ?></td>
											<td class="align-middle">
												<span class="d-block text-center">
													<?php if(strlen($valc[6])>0) { ?>
													<a href="<?php echo $docdir.'cv/'.$valc[6]; ?>" target="_blank" title="Curriculum vitae" alt="Curriculum vitae">
														<svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-file-pdf-ext"></use></svg>
													</a>
													<?php }else{ ?>
														<svg class="icon icon-secondary">
														<use href="temi/bootstrap/pagine/img/no_pdf.svg#it-no_pdf"></use>
														</svg>
													<?php } ?>
												</span>
											</td>
											<td class="align-middle">
												<span class="d-block text-center">
													<?php if(strlen($valc[7])>0) { ?>
													<a href="<?php echo $docdir.'cg/'.$valc[7]; ?>" target="_blank" title="Curriculum vitae" alt="Curriculum vitae">
														<svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-file-pdf-ext"></use></svg>
													</a>
													<?php }else{ ?>
														<svg class="icon icon-secondary">
														<use href="temi/bootstrap/pagine/img/no_pdf.svg#it-no_pdf"></use>
														</svg>
													<?php } ?>
												</span>
											</td>
										</tr>
									</tbody>
								<?php } ?>
							</table>
						</div>
						<!-- Fine Candidari Consiglieri-->
					<?php } ?>
				<?php } ?>
					</td></tr></tbody>
					</table>

			<?php } ?>
		</div>
	<?php }	?>
	<!-- Fine visualizzazione scheda solo per le amministrative-->
</div>