<?php 
$visibility=0;
$visibilityconsultazione=1;
global $idcirc;
if(isset($_GET['idcirc'])) $idcirc=intval($_GET['idcirc']);
$arcons=explode(",",$arcon);
$elencotipi=elenco_tipi();
if($circo) $cirpar="&idcirc=$idcirc"; else $cirpar='';
$piucirc=count(elenco_circoscrizioni());
$linktrasp='';
?>
<div class="it-header-navbar-wrapper sticky-top" id="header-nav-wrapper">
    <div class="container">
        <div class="row">
			<div class="col-12">
            <!--start nav-->
				<nav class="navbar navbar-expand-lg has-megamenu" aria-label="Navigazione principale">
					  <button class="custom-navbar-toggler" type="button" aria-controls="navC1" aria-expanded="false" aria-label="Mostra/Nascondi la navigazione" data-bs-toggle="navbarcollapsible" data-bs-target="#navC1">
						<svg class="icon">
						  <use href="<?php echo $curdir?>/svg/sprites.svg#it-burger"></use>
						</svg>
					  </button>
					<div class="navbar-collapsable" id="navC1" style="display: none;">
						<div class="overlay" style="display: none;"></div>
						<div class="close-div">
						  <button class="btn close-menu" type="button">
							<span class="visually-hidden">Nascondi la navigazione</span>
							<svg class="icon">
							  <use href="<?php echo $curdir?>/svg/sprites.svg#it-close-big"></use>
							</svg>
						  </button>
						</div>
						<div class="menu-wrapper">
							<ul class="navbar-nav">
									<li class="nav-item">
										<a class="nav-link" href="<?php echo $link_paginaprincipale;?>" aria-current="page"><span>Home</span></a>
									</li>
									<!-- link per il test 
									<li class="nav-item">
										<a class="nav-link" href="modules.php?op=100" aria-current="page"><span>TEST</span></a>
									</li>
									 fine link per il test -->
									<?php
									$row=elenco_comuni();
									$contacomuni=count($row);
									if ($contacomuni>1 and $multicomune==1) {?>
										<li class="nav-item dropdown">
											<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdownC1"><span>Comuni</span>
												<svg class="icon icon-xs">
													<use href="<?php echo $curdir?>/svg/sprites.svg#it-expand"></use>
												</svg>
											</a>
										  <div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdownC1">
											<div class="link-list-wrapper">
											  <ul class="link-list anyClass">
											  <?php
												foreach($row as $campo=>$val) {?>
													<li>
														<a class="dropdown-item list-item" href="modules.php?op=gruppo&name=Elezioni&id_comune=<?php echo $val[0].$cirpar;?>&file=index"><span><?php echo $val[1];?></span></a>
													</li>
												<?php
												}
												?>
											  </ul>
											</div>
										  </div>
										</li>
									<?php }?>
										<li class="nav-item dropdown">
											<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdown">
												<span>Consultazioni</span>
												<svg class="icon icon-xs">
													<use href="<?php echo $curdir; ?>/svg/sprites.svg#it-expand"></use>
												</svg>
											</a>
											<div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdown">
												<div class="link-list-wrapper">
													<ul class="link-list anyClass">
														<!-- Sottomenu per Anno -->
														<li>
															<a class="dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="collapse" data-bs-target="#submenuAnnoConsultazioni" aria-expanded="false" aria-controls="submenuAnnoConsultazioni">
																<span><b>Consultazioni per Anno</b></span>
																<svg class="icon icon-xs">
																	<use href="<?php echo $curdir; ?>/svg/sprites.svg#it-expand"></use>
																</svg>
															</a>
															<div class="collapse" id="submenuAnnoConsultazioni">
																<ul class="link-list">
																	<?php
																		$row = elenco_cons();
																		$preanno = 0;
																		foreach ($row as $campo => $val) {
																			if (!cons_pubblica($val[3])) continue;
																			$anno = date('Y', strtotime($val[2]));
																			if ($anno != $preanno) {
																				$preanno = $anno;
																	?>
																				<li><a class="dropdown-item list-item disabledmenu" href="#"><span><b><u><?php echo $anno ?></u></b></span></a></li>
																	<?php } ?>
																			<li>
																				<a class="dropdown-item list-item <?php if ((isset($_GET["id_cons_gen"])) && ($_GET["id_cons_gen"] == $val[1])) {$linktrasp=$val['link_trasparenza']; ?> active <?php } ?>" 
																					href="modules.php?op=gruppo&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $val[1]; ?>">
																					<span><?php echo $val[0]; ?></span>
																				</a>
																			</li>
																	<?php } ?>
																</ul>
															</div>
														</li>
														<!-- Sottomenu per Tipologia -->
														<li>
															<a class="dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="collapse" data-bs-target="#submenuTipologiaConsultazioni" aria-expanded="false" aria-controls="submenuTipologiaConsultazioni">
																<span><b>Consultazioni per Tipologia</b></span>
																<svg class="icon icon-xs">
																	<use href="<?php echo $curdir; ?>/svg/sprites.svg#it-expand"></use>
																</svg>
															</a>
															<div class="collapse" id="submenuTipologiaConsultazioni">
																	<ul class="link-list">
																	<?php
																		$elecons=elenco_cons_tipo();
																		foreach($elecons[0] as $val) {
																	?>
																		<li><a class="dropdown-item list-item <?php if ((isset($_GET["id_cons_gen"])) && ($_GET["id_cons_gen"] == $val[1])) {$linktrasp=$val['link_trasparenza']; ?> active <?php } ?> " href="modules.php?op=gruppo&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $val[1];?>"><span><?php echo $val[0];?></span></a></li>
																	<?php
																		}
																	?>
																	<?php
																		foreach($arcons as $key=>$val) {
																			$i=0;
																			foreach($elecons[1] as $val2){
																				if($val2[4]==11 or$val2[4]==15 or$val2[4]==18) $val2[4]=6;
																				if($val2[4]==10 or$val2[4]==16 or$val2[4]==19) $val2[4]=7;
																				if($val2[4]==14) $val2[4]=8;
																				if($val2[4]==12) $val2[4]=1;
																				if($val==$val2[4]) {
																					if($i++==0) {?>  
																					<li><a class="dropdown-item list-item disabledmenu" href="#"><span><b><u><?php echo $elencotipi[$val-1][1]?></u></b></span></a></li>
																					<?php } ?>
																					<li><a class="dropdown-item list-item 
																					<?php if ((isset($_GET["id_cons_gen"])) && ($_GET["id_cons_gen"] == $val2['id_cons_gen'])){$linktrasp=$val2['link_trasparenza']; ?>
																						active
																					<?php } ?>
																					 " href="modules.php?op=gruppo&id_comune=<?php echo $id_comune.$cirpar?>&file=index&id_cons_gen=<?php echo $val2['id_cons_gen']?>">
																					 <span><?php echo $val2['descrizione']?></span></a></li>
																			<?php 
																				}
																			}
																		}
																	?>
																</ul>
															</div>
														</li>
													</ul>
												</div>
											</div>
										</li>
										<script>
										document.addEventListener('DOMContentLoaded', function () {
											// Aggiungi un controllo per prevenire conflitti con altri collapse
											const consultazioniSubmenuTogglers = document.querySelectorAll('[data-bs-target="#submenuAnnoConsultazioni"], [data-bs-target="#submenuTipologiaConsultazioni"]');

											consultazioniSubmenuTogglers.forEach(toggler => {
												toggler.addEventListener('click', function (event) {
													// Evita che il menu principale si richiuda
													event.stopPropagation();

													// Chiudi tutti i sottomenù all'interno di Consultazioni tranne quello cliccato
													const currentSubmenu = document.querySelector(this.getAttribute('data-bs-target'));
													document.querySelectorAll('#submenuAnnoConsultazioni, #submenuTipologiaConsultazioni').forEach(submenu => {
														if (submenu !== currentSubmenu && submenu.classList.contains('show')) {
															submenu.classList.remove('show');
														}
													});

													// Chiudi il sottomenu se è già aperto (per prevenire il riaprirsi)
													if (currentSubmenu.classList.contains('show')) {
														currentSubmenu.classList.remove('show');
														event.preventDefault(); // Prevenire il comportamento predefinito
													}
												});
											});

											// Evita che il menu principale si chiuda quando si interagisce con il dropdown
											const consultazioniDropdownMenu = document.querySelector('#mainNavDropdown + .dropdown-menu');
											if (consultazioniDropdownMenu) {
												consultazioniDropdownMenu.addEventListener('click', function (event) {
													event.stopPropagation();
												});
											}
										});
										</script>
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdownC3"><span>Informazioni Generali</span><svg class="icon icon-xs"><use href="<?php echo $curdir?>/svg/sprites.svg#it-expand"></use></svg></a><div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdownC3">
									<div class="link-list-wrapper">
									  <ul class="link-list anyClass">
										<li><a class="dropdown-item list-item left-icon" href="modules.php?op=1&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-pencil"></use></svg><span> Come si vota</span></a></li>
										<li><a class="dropdown-item list-item left-icon" href="modules.php?op=2&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-telephone"></use></svg><span> Numeri Utili</span></a></li>
										<li><a class="dropdown-item list-item left-icon" href="modules.php?op=3&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-tool"></use></svg><span> Servizi</span></a></li>
										<li><a class="dropdown-item list-item left-icon" href="modules.php?op=4&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-link"></use></svg><span> Link utili</span></a></li>
										<li><a class="dropdown-item list-item left-icon" href="modules.php?op=5&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-card"></use></svg><span> Dati Generali</span></a></li>
										<li><a class="dropdown-item list-item left-icon" href="modules.php?op=50&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-info-circle"></use></svg><span> Informazioni sulla Privacy</span></a></li>
										<?php if($genere>1) { ?>
											<li><span class="divider"></span></li>
											<li><a class="dropdown-item list-item left-icon" href="modules.php?op=28&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-files"></use></svg><span> Elezioni Trasparenti</span></a></li>
										<?php } ?>
										</ul>
									</div>
								  </div>
								</li>
								<li class="nav-item dropdown">
								  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdownC4">
									<span>Affluenza e Risultati</span>
									<svg class="icon icon-xs">
									  <use href="<?php echo $curdir;?>/svg/sprites.svg#it-expand"></use>
									</svg>
								  </a>
								  <div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdownC4">
									<div class="link-list-wrapper">
									  <ul class="link-list anyClass">
										<?php if(count(affluenze_totali(0))) $stato=''; else $stato='disabled'; ?>
										<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=11&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-user"></use></svg><span> Affluenze</span></a></li>
										<?php if($genere==0) { ?>
											<?php $tmp=voti_tot_referendum();if($tmp and $tmp[0][4]>0) $stato=''; else $stato='disabled'; ?>
										<?php }else{ ?>
											<?php $tmp=voti_totali();if($tmp and array_sum($tmp[0])>0) $stato=''; else $stato='disabled'; 
										} ?>
										<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=12&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-files"></use></svg><span> Votanti</span></a></li>
										<li><span class="divider"></span></li>
										<?php if($genere==0) {?>
											<?php if(count(voti_tot_referendum())>0) $stato=''; else $stato='disabled'; ?>
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=29&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-user"></use></svg><span> Referendum per Sezioni</span></a></li>
										<?php }?>	
										<?php if(($genere==1 or $genere==3 or $genere==5) and !$votogruppo) {?>
											<?php if(count(voti_gruppo('gruppo'))>0) $stato=''; else $stato='disabled'; ?>
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=13&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-user"></use></svg><span> <?php echo _GRUPPO; ?> per Sezioni</span></a></li>
											<?php if($piucirc>1) { ?>
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=16&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-user"></use></svg><span> <?php echo _GRUPPO; ?> per Circoscrizioni</span></a></li>
											<?php }?>
										<?php }?>
										<?php if($genere>1) { 
												if($genere==2) ?>
													<?php if(count(voti_gruppo('gruppo'))>0) $stato=''; else $stato='disabled'; ?>
											<?php if(($genere>2) and !$votolista) {?>
												<?php if(count(voti_tot_lista())>0) $stato=''; else $stato='disabled'; ?>
											<?php }?>
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=14&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-user"></use></svg><span> Lista per Sezioni</span></a></li>
											<?php if($piucirc>1) { ?>
												<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=17&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-user"></use></svg><span> Lista per Circoscrizioni</span></a></li>
											<?php }?>
										<?php }?>
										<?php if(($genere==4 or $genere==5) and !$votocandidato) {?>
											<?php if(count(voti_tot_candidato(0))>0) $stato=''; else $stato='disabled'; ?>
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=15&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-user"></use></svg><span> Candidato Lista per Sezioni</span></a></li>
											<?php if($piucirc>1) { ?>
												<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=18&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-user"></use></svg><span> Candidato Lista per Circoscrizioni</span></a></li>
											<?php }?>
										<?php }?>
										<?php if($proiezione==1) { ?>
											<?php if($tipo_cons==3) { ?>
												<li><span class="divider"></span></li>
												<li><a class="dropdown-item list-item left-icon" href="modules.php?op=31&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-files"></use></svg><span> Proiezione Seggi</span></a></li>
											<?php } ?>
										<?php } ?>
										<?php if ($visibility==1) {?>
											<li><span class="divider"></span></li>
											<li><a class="dropdown-item list-item left-icon" href="modules.php?op=16&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-exchange-circle"></use></svg><span> Raffronti</span></a></li>
										<?php }?>
									  </ul>
									</div>
								  </div>
								</li>
								<li class="nav-item dropdown">
								  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdownC7">
									<span>Grafici</span>
									<svg class="icon icon-xs">
									  <use href="<?php echo $curdir;?>/svg/sprites.svg#it-expand"></use>
									</svg>
								  </a>
								  <div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdownC7">
									<div class="link-list-wrapper">
									  <ul class="link-list anyClass">
										<?php if($genere>0) { ?>
											<?php if(count(affluenze_totali(0))) $stato=''; else $stato='disabled'; ?>
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=41&id_comune=<?php echo $id_comune.$cirpar;?>&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span> Affluenze</span></a></li>
											
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=42&id_comune=<?php echo $id_comune.$cirpar;?>&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span> Votanti</span></a></li>
											<li><span class="divider"></span></li>
											<?php if(($genere==1 or $genere==3 or $genere==5) and !$votogruppo) {?>
												<?php if(count(voti_gruppo('gruppo'))>0) $stato=''; else $stato='disabled'; ?>
												<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=43&id_comune=<?php echo $id_comune.$cirpar;?>&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span> <?php echo _GRUPPO; ?></span></a></li>
											<?php }?>
											<?php if($genere>1) { 
													if($genere==2) ?>
														<?php if(count(voti_gruppo('gruppo'))>0) $stato=''; else $stato='disabled'; ?>
												<?php if(($genere>2) and !$votolista) {?>
													<?php if(count(voti_tot_lista())>0) $stato=''; else $stato='disabled'; ?>
												<?php }?>
												<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=44&id_comune=<?php echo $id_comune.$cirpar;?>&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span> Voti di Lista </span></a></li>
											<?php }?>
										<?php }else{ ?>
											<?php $tmp=affluenze_referendum(0,0); if($tmp and $tmp[0][1]>0) $stato=''; else $stato='disabled'; ?>
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=51&id_comune=<?php echo $id_comune.$cirpar;?>&id_cons_gen=<?php echo $id_cons_gen;?>">
											<svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span>Affluenze Referendum</span></a></li>
											<?php $tmp=voti_tot_referendum();if($tmp and ($tmp[0][4]+$tmp[0][5])>0) $stato=''; else $stato='disabled'; ?>
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=52&id_comune=<?php echo $id_comune.$cirpar;?>&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span> Votanti</span></a></li>
											<?php $tmp=voti_tot_referendum();if($tmp and ($tmp[0][2]+$tmp[0][3])>0) $stato=''; else $stato='disabled'; ?>
											<li><a class="dropdown-item <?php echo $stato; ?> list-item left-icon" href="modules.php?op=53&id_comune=<?php echo $id_comune.$cirpar;?>&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span> Voti</span></a></li>
										
										<?php }?>	
									  </ul>
									</div>
								  </div>
								</li>
								<?php if ($visibility==1) {?>
								<li class="nav-item dropdown">
									 <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdownC5">
										<span>Grafici</span>
										<svg class="icon icon-xs">
										  <use href="<?php echo $curdir?>/svg/sprites.svg#it-expand"></use>
										</svg>
									</a>
									<div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdownC5">
									<div class="link-list-wrapper">
									  <ul class="link-list anyClass">
										<li><a class="dropdown-item list-item left-icon" href="modules.php?op=21&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span>Affluenze</span></a></li>
										<li><a class="dropdown-item list-item left-icon" href="modules.php?op=22&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span>Voti</span></a></li>
										<li><span class="divider"></span></li>
										<?php if($genere==0) {?>
											<li><a class="dropdown-item list-item left-icon" href="modules.php?op=23&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span>Referendum</span></a></li>
										<?php }?>	
										<?php if(($genere==1 or $genere==3 or $genere==5) and !$votogruppo) {?>
											<li><a class="dropdown-item list-item left-icon" href="modules.php?op=23&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span><?php echo _GRUPPO; ?></span></a></li>
										<?php }?>
										<?php if(($genere>1) and !$votolista) {?>
											<li><a class="dropdown-item list-item left-icon" href="modules.php?op=24&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span>Lista</span></a></li>
										<?php }?>
										<?php if(($genere==4 or $genere==5) and !$votocandidato) {?>
											<li><a class="dropdown-item list-item left-icon" href="modules.php?op=25&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span>Candidato Lista</span></a></li>
										<?php }?>
										<li><a class="dropdown-item list-item left-icon" href="modules.php?op=26&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>"><svg class="icon icon-sm icon-primary left"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg><span>Link lista 3</span></a></li>
									  </ul>
									</div>
									</div>
								</li>
								<?php }?>
								<?php 
								// controllo se il menu del tema è attivo, var valorizzata in index.php
								// 0 = disattivato
								// 1 = Attivato
								if ($temaattivo == 1)
								{
									$tlist='';
									$path = dirname(__FILE__) ."/../../temi/";
									$handle=opendir($path);
									while ($file = readdir($handle)) {
										if ( (preg_match('/^([_0-9a-zA-Z]+)([_0-9a-zA-Z]{3})$/',$file)) ) {

										$tlist .= "$file ";
										}
									}
									closedir($handle);
									$tlist2 = explode(" ", $tlist);
									unset($tlist);
									sort($tlist2);
									for ($i=0; $i < sizeof($tlist2); $i++) 
									if(($tlist2[$i]!="") && ($tlist2[$i]!="language")) 
											$tlist[]=$tlist2[$i];
									?>
									<li class="nav-item dropdown">
									  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdownC6">
										<span>Temi</span>
										<svg class="icon icon-xs">
										  <use href="<?php echo $curdir?>/svg/sprites.svg#it-expand"></use>
										</svg>
									  </a>					  
									  <div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdownC6">
										<div class="link-list-wrapper">
										  <ul class="link-list anyClass">
										  <?php
											for($i = 0; $i < sizeof($tlist); $i++) {
												?>
											<li><a class="dropdown-item list-item" href="modules.php?op=gruppo&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?>&tema=<?php echo $tlist[$i];?>"><span><?php echo $tlist[$i];?></span></a></li>
											<?php
											}					  
											?>
										  </ul>
										</div>
									  </div>
									</li>
								<?php
								}
								?>
<!-- menu tema colori
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="mainNavDropdownThemes">
										<span>Colore Tema</span>
										<svg class="icon icon-xs">
										  <use href="<?php //echo $curdir ?>/svg/sprites.svg#it-expand"></use>
										</svg>
									</a>
									<div class="dropdown-menu" role="region" aria-labelledby="mainNavDropdownThemes">
										<div class="link-list-wrapper">
											  <ul class="link-list">
												<li>
												  <a class="dropdown-item list-item left-icon" href="#" data-theme="default">
													<svg class="icon icon-sm icon-primary left"><use href="<?php //echo $curdir ?>/svg/sprites.svg#it-palette"></use></svg>
													<span>Istituzionale (Default)</span>
												  </a>
												</li>
												<li>
												  <a class="dropdown-item list-item left-icon" href="#" data-theme="verde">
													<svg class="icon icon-sm icon-primary left"><use href="<?php //echo $curdir ?>/svg/sprites.svg#it-palette"></use></svg>
													<span>Verde</span>
												  </a>
												</li>
												<li>
												  <a class="dropdown-item list-item left-icon" href="#" data-theme="rosso">
													<svg class="icon icon-sm icon-primary left"><use href="<?php //echo $curdir ?>/svg/sprites.svg#it-palette"></use></svg>
													<span>Rosso</span>
												  </a>
												</li>
												<li>
												  <a class="dropdown-item list-item left-icon" href="#" data-theme="giallo">
													<svg class="icon icon-sm icon-primary left"><use href="<?php //echo $curdir ?>/svg/sprites.svg#it-palette"></use></svg>
													<span>Giallo</span>
												  </a>
												</li>
												<li>
												  <a class="dropdown-item list-item left-icon" href="#" data-theme="azzurro">
													<svg class="icon icon-sm icon-primary left"><use href="<?php //echo $curdir ?>/svg/sprites.svg#it-palette"></use></svg>
													<span>Azzurro</span>
												  </a>
												</li>
												<li>
												  <a class="dropdown-item list-item left-icon" href="#" data-theme="turchese">
													<svg class="icon icon-sm icon-primary left"><use href="<?php //echo $curdir ?>/svg/sprites.svg#it-palette"></use></svg>
													<span>Turchese</span>
												  </a>
												</li>
												<li>
												  <a class="dropdown-item list-item left-icon" href="#" data-theme="arancione">
													<svg class="icon icon-sm icon-primary left"><use href="<?php //echo $curdir ?>/svg/sprites.svg#it-palette"></use></svg>
													<span>Arancione</span>
												  </a>
												</li>
												<!--fine-->
											 </ul>
										</div>
									</div>
								</li>
							 </ul>
						</div>
					</div>
				</nav>
			</div>
        </div>
    </div>
</div>