<div class="col-12 col-md-12 col-xl-12 py-md-3 px-md-3 bd-content">
	<div class="container">
		<div class="row text-center">
			<h4 class="fw-semibold text-primary mobile-expanded mt-2">Assegnazione dei seggi in Consiglio</h4>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<?php
global $stampa,$pdf;
$row=dati_comune();
$desc_comune=$row[0][1];
$row=dati_consultazione(0);
$desc_cons=$row[0][1];
$stampa="";	$pdf=1;		
			$row=conscomune(0);
			$hondt=$row[0][7];
			include("modules/Elezioni/consiglieri.php");
			consiglio();
			$html=htmlspecialchars($stampa);
#			ob_start();
if(strlen($stampa)>0) {
	$test=phpversion();
$form_action = ($test >= 7.1) ? "modelli/genera_pdf_seggi.php" : "modelli/genera_pdf_seggiphp5.php";
?>
<form action="<?php echo $form_action; ?>" method="post" id="pdfForm" style="margin: 0; padding: 0; width: 100%;">
  <input type="hidden" name="stampa" value="<?php echo $html; ?>">
  <input type="hidden" name="comune" value="<?php echo $desc_comune; ?>">
  <input type="hidden" name="id_comune" value="<?php echo $id_comune; ?>">
  <input type="hidden" name="consultazione" value="<?php echo $desc_cons; ?>">

  <div class="d-flex justify-content-end align-items-center gap-2" style="padding: 0; margin: 0;">
    <div class="btn-group" role="group" aria-label="Pulsanti azione" style="margin: 0; padding: 0;">
      <!--<button type="button" class="btn-tab" aria-label="Stampa">
        <a href="<?php //echo $linkopendata; ?>" target="_blank" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
          <span>Stampa</span>
          <svg class="icon icon-sm icon-primary ms-auto">
            <use href="<?php //echo $curdir ?>/svg/sprites.svg#it-print"></use>
          </svg>
        </a>
      </button>-->
	  <button type="submit" class="btn-tab" aria-label="Stampa PDF">
        <span>PDF</span>
        <svg class="icon icon-sm icon-primary ms-auto">
          <use href="<?php echo $curdir ?>/svg/sprites.svg#it-file-pdf-ext"></use>
        </svg>
      </button>
      <button type="button" class="btn-tab" id="fullscreen-btn" aria-label="Fullscreen">
        <svg class="icon icon-sm icon-primary ms-auto" id="fullscreen-icon">
          <use href="<?php echo $curdir ?>/svg/sprites.svg#it-fullscreen"></use>
        </svg>
      </button>
    </div>
  </div>
</form>
<?php
}
#			$stampa=ob_get_clean();
echo "$stampa";
#if($pdf) include('modelli/genera_pdf_seggi.php');
			?>
		</div>
	</div>
</div>
<!-- Script per fullscreen -->
<script>
  const fullscreenBtn = document.getElementById('fullscreen-btn');
  const fullscreenIcon = document.getElementById('fullscreen-icon');

  fullscreenBtn.addEventListener('click', function() {
    // Se il documento è già in modalità fullscreen
    if (document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
      // Esci dalla modalità fullscreen
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.mozCancelFullScreen) { // Firefox
        document.mozCancelFullScreen();
      } else if (document.webkitExitFullscreen) { // Chrome, Safari, Opera
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) { // IE/Edge
        document.msExitFullscreen();
      }
      // Cambia l'icona per la modalità finestra (it-fullscreen)
      fullscreenIcon.querySelector('use').setAttribute('href', '<?php echo $curdir?>/svg/sprites.svg#it-fullscreen');
    } else {
      // Entra in modalità fullscreen
      if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen();
      } else if (document.documentElement.mozRequestFullScreen) { // Firefox
        document.documentElement.mozRequestFullScreen();
      } else if (document.documentElement.webkitRequestFullscreen) { // Chrome, Safari, Opera
        document.documentElement.webkitRequestFullscreen();
      } else if (document.documentElement.msRequestFullscreen) { // IE/Edge
        document.documentElement.msRequestFullscreen();
      }
      // Cambia l'icona per la modalità fullscreen (it-minimize)
      fullscreenIcon.querySelector('use').setAttribute('href', '<?php echo $curdir?>/svg/sprites.svg#it-minimize');
    }
  });
</script>