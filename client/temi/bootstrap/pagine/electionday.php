<?php 
// Controllo se attivare l'Election Day

$row=dati_consultazione(0);
$giornorif= date_create($row[0][2]);
$oggi = date_create("now");
$interval = date_diff($giornorif, $oggi);
$trascorsi=$interval->format('%a');
#echo "<br>$giorniviselday : $trascorsi";
$attivaelctionday = 0;
$comunali=0;
if($giorniviselday>=$trascorsi) {
	$row=elenco_cons();
	foreach($row as $val){
		$giornorif2=date_create($val[2]);
		$interval = date_diff($giornorif2, $oggi);
		$trascorsi=$interval->format('%a');
		if($giorniviselday>=$trascorsi){ 
			$listacons[]=$val;
			if(!$comunali){
				$tmp=dati_consultazione($val[1]); 
				if(($tmp[0][4]==3 and $tipo_cons==5) or ($tmp[0][4]==5 and $tipo_cons==3))
					$comunali=1;
			}
		}else{
			$break;
		}
	}
	$numcons=count($listacons);
	if($numcons>1){ 
		if($numcons!=2)
			$comunali=0;
		$attivaelctionday =1;
	}
}
			
?>
<?php if ($attivaelctionday == 1) { ?>
	<div class="container">
		<div class="row text-center">
			<!-- Icona e testo allineati orizzontalmente e verticalmente -->
			<div class="d-flex justify-content-center align-items-center">
				<!-- Icona Election Day -->
				<svg class="icon icon-primary me-2" style="vertical-align: middle;">
					<use href="<?php echo $curdir?>/svg/sprites.svg#it-box"></use>
				</svg>
				<!-- Titolo "Election Day" -->
				<h4 class="fw-semibold text-primary mt-2 align-items-center"><?php if($comunali) echo "Turni Elettorali"; else echo "Election Day"; ?></h4>
			</div>
		</div>
	</div>
	<div class="w-100 lightgrey-bg-b2 text-white mt-2 mb-4"> <!-- Spazio sotto -->
		<div class="container">
			<div class="row py-4 divided">
				<?php 
				foreach ($listacons as $val) {
				?>
					<div class="col-12 col-md-4 text-primary fw-semibold primary-bg-a2">
						<!-- Icona e link per ogni consultazione -->
						<a class="pt-2 pt-md-0 d-flex align-items-center justify-content-center text-decoration-none" data-focus-mouse="false" href="modules.php?op=gruppo&id_comune=<?php echo $id_comune; ?>&id_cons_gen=<?php echo $val[1]; ?>">
							<!-- Icona accanto al testo -->
							<svg class="icon icon-sm icon-primary me-2" style="vertical-align: middle;">
								<use href="<?php echo $curdir?>/svg/sprites.svg#it-box"></use>
							</svg>
							<!-- Testo della consultazione -->
							<h6 class="align-self-center mb-0" style="line-height: 1.5;">Consultazione <?php echo $val[0]; ?></h6>
						</a>
					</div>
				<?php 
				} 
				?>
			</div>
		</div>
	</div>
<?php } ?>
