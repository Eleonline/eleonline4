<?php 
$op = $_GET['op'] ?? '';
$opcirco=0;
$attivaopendata=1;
$linkgrafici='';
$linkrislutati='';
if(!isset($nosez)) $nosez=0;
$arval = array();
if ($op==51 or $op==52 or $op==53 or $op==54 or $op==41 or $op==42 or $op==43 or $op==44) {
	$attivaopendata=0;
} else {
$attivaopendata=1;
if(isset($linkopendatapdf))
	$linktmp=substr($linkopendatapdf,strrpos($linkopendata,'?')+1);
else
	$linktmp=substr($linkopendata,strrpos($linkopendata,'?')+1);
$parametri=explode('&',$linktmp);
foreach($parametri as $key=>$val) {
	$arval[$key]=explode('=',$val);
	if($arval[$key][0]=='op' and substr($arval[$key][1],-5)=='circo') $opcirco=1;
}
}
if ($op==41 or $op==11 or $op==51) {
	$linkrisultati='modules.php?op=11&id_comune=' . $id_comune.$cirpar.'&file=index&id_cons_gen='.$id_cons_gen;
	if($genere==0) {
	$linkgrafici='modules.php?op=51&id_comune=' .$id_comune.$cirpar.'&id_cons_gen=' .$id_cons_gen;
	} else {
$linkgrafici='modules.php?op=41&id_comune=' .$id_comune.$cirpar.'&id_cons_gen=' .$id_cons_gen;
	}
} 
if ($op==42 or $op==12 or $op==52) {
	$linkrisultati='modules.php?op=12&id_comune=' . $id_comune.$cirpar.'&file=index&id_cons_gen='.$id_cons_gen;
	if($genere==0) {
		$linkgrafici='modules.php?op=52&id_comune=' .$id_comune.$cirpar.'&id_cons_gen=' .$id_cons_gen;
	} else {
		$linkgrafici='modules.php?op=42&id_comune=' .$id_comune.$cirpar.'&id_cons_gen=' .$id_cons_gen;
	}
}
if ($op==43 or $op==13) {
	$linkrisultati='modules.php?op=13&id_comune=' . $id_comune.$cirpar.'&file=index&id_cons_gen='.$id_cons_gen;
	if($genere==0) {
		$linkgrafici='modules.php?op=43&id_comune=' .$id_comune.$cirpar.'&id_cons_gen=' .$id_cons_gen;
	} else {
		$linkgrafici='modules.php?op=43&id_comune=' .$id_comune.$cirpar.'&id_cons_gen=' .$id_cons_gen;
	}
}
if ($op==44 or $op==14) {
	$linkrisultati='modules.php?op=14&id_comune=' . $id_comune.$cirpar.'&file=index&id_cons_gen='.$id_cons_gen;
	$linkgrafici='modules.php?op=44&id_comune=' .$id_comune.$cirpar.'&id_cons_gen=' .$id_cons_gen;
}
if ($op==53 or $op==29) {
	$linkrisultati='modules.php?op=29&id_comune=' . $id_comune.$cirpar.'&file=index&id_cons_gen='.$id_cons_gen;
	$linkgrafici='modules.php?op=53&id_comune=' .$id_comune.$cirpar.'&id_cons_gen=' .$id_cons_gen;
}
?>
<!-- Pulsante per aprire il Modal -->
<div class="d-flex justify-content-end align-items-center">
  <div class="btn-group " role="group" aria-label="Pulsanti azione">
          <?php if (isset($linkrisultati)) {?>
	<button type="button" class="btn-tab ">
	  <a href="<?php echo $linkrisultati; ?>">
        <span>Risultati</span>
        <svg class="icon icon-sm icon-primary ms-auto"><use href="<?php echo $curdir?>/svg/sprites.svg#it-note"></use></svg>
      </a>
    </button>
	<?php }?>
	<?php if ($linkgrafici!='') {?>
    <button type="button" class="btn-tab">
	  <a href="<?php echo $linkgrafici; ?>">
        <span>Grafici</span>
        <svg class="icon icon-sm icon-primary ms-auto"><use href="<?php echo $curdir?>/svg/sprites.svg#it-chart-line"></use></svg>
      </a>
    </button>
	<?php }?>
	 <?php if ($attivaopendata == 1 ) {?>
    <button type="button" class="btn-tab">
      <a href="<?php echo $linkopendata;?>" target="_blank">
        <span>Stampa</span>
        <svg class="icon icon-sm icon-primary ms-auto"><use href="<?php echo $curdir?>/svg/sprites.svg#it-print"></use></svg>
      </a>
    </button>
	<button type="button" class="btn-tab">
      <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#pdfModal">
        <span>PDF</span>
        <svg class="icon icon-sm icon-primary ms-auto"><use href="<?php echo $curdir?>/svg/sprites.svg#it-file-pdf-ext"></use></svg>
      </a>
    </button>
    <button type="button" class="btn-tab">
      <a href="<?php echo $linkopendata;?>&xls=1" target="_blank">
        <span>Esporta in CSV</span>
        <svg class="icon icon-sm icon-primary ms-auto"><use href="<?php echo $curdir?>/svg/sprites.svg#it-file-csv"></use></svg>
      </a>
    </button>
	 <?php }?>
  <?php if ($op==11 or $op==12 or $op==29) { ?>
  <button type="button" class="btn-tab" id="toggle-percentuali">
  <span>Visualizza percentuali</span>
  <svg class="icon icon-sm icon-primary ms-auto"><use href="<?php echo $curdir?>/svg/spritespersonalizzate.svg#it-percentuale"></use></svg>
</button>
  <?php } ?>
  <button type="button" class="btn-tab d-flex align-items-center" id="fullscreen-btn" aria-label="fullscreen">
    <svg class="icon icon-sm icon-primary ms-auto" id="fullscreen-icon">
      <use href="<?php echo $curdir?>/svg/sprites.svg#it-fullscreen"></use>
    </svg>
  </button>
  </div>
</div>

<!-- Modal PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="pdfForm" action="modules.php" method="get"  target="_blank"> <!-- endpoint -->
        <div class="modal-header">
          <h5 class="modal-title" id="pdfModalLabel">Opzioni PDF</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <?php if(!$opcirco) {
			if(!$nosez and sezioni_totali()>14) { ?>
              <div class="row">
                <fieldset>
                  <legend>Stampa Sezioni</legend>
                  <div class="mb-2">
                    <label for="sezmin" class="form-label">Da Sezione</label>
                    <input name="minsez" type="number" class="form-control" id="sezmin" value="1">
                  </div>
                  <div class="mb-2">
                    <label for="maxsez" class="form-label">A Sezione</label>
                    <input name="offsetsez" type="number" class="form-control" id="maxsez" value="14">
                  </div>
                </fieldset>
              </div>
            <?php } ?>
            <?php }elseif( count(elenco_circoscrizioni())>4) { ?>
              <div class="row">
                <fieldset>
                  <legend>Stampa Circoscrizioni</legend>
                  <div class="mb-2">
                    <label for="sezmin" class="form-label">Da Circoscrizione</label>
                    <input name="minsez" type="number" class="form-control" id="sezmin" value="1">
                  </div>
                  <div class="mb-2">
                    <label for="maxsez" class="form-label">A Circoscrizione</label>
                    <input name="offsetsez" type="number" class="form-control" id="maxsez" value="4">
                  </div>
                </fieldset>
              </div>
            <?php } ?>
            <div class="col">
              <fieldset>
                <legend>Formato documento</legend>
                <div class="form-check">
                  <input name="formato" type="radio" id="radio1" value="A4" checked class="form-check-input">
                  <label for="radio1" class="form-check-label">A4</label>
                </div>
                <div class="form-check">
                  <input name="formato" type="radio" id="radio2" value="A3" class="form-check-input">
                  <label for="radio2" class="form-check-label">A3</label>
                </div>
              </fieldset>
            </div>
            <div class="col">
              <fieldset>
                <legend>Orientamento</legend>
                <div class="form-check">
                  <input name="orienta" type="radio" id="radio3" value="P" checked class="form-check-input">
                  <label for="radio3" class="form-check-label">Verticale</label>
                </div>
                <div class="form-check">
                  <input name="orienta" type="radio" id="radio4" value="L" class="form-check-input">
                  <label for="radio4" class="form-check-label">Orizzontale</label>
                </div>
              </fieldset>
            </div>
          </div>

          <!-- Altri input hidden -->
          <?php foreach($arval as $val) echo "<input type=\"hidden\" name=\"".$val[0]."\" value=\"".$val[1]."\">"; ?>
          <input name="pdf" type="hidden" value="1">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="submit" class="btn btn-primary">Crea PDF</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script per chiudere il modal dopo submit -->
<script>
document.getElementById('pdfForm').addEventListener('submit', function () {
  const modal = bootstrap.Modal.getInstance(document.getElementById('pdfModal'));
  if (modal) {
    modal.hide();
  }
});
</script>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggleBtn = document.getElementById('toggle-percentuali');
  let percentualiVisibili = false; // parte da nascosto

  toggleBtn.querySelector('span').textContent = 'Mostra percentuali';

  toggleBtn.addEventListener('click', function () {
    percentualiVisibili = !percentualiVisibili;

    const percentuali = document.querySelectorAll('.percentuale');
    percentuali.forEach(p => {
      p.style.display = percentualiVisibili ? 'inline-block' : 'none';
      // o se sono elementi a blocco puoi usare 'block' o 'table-cell' etc.
    });

    toggleBtn.querySelector('span').textContent = percentualiVisibili
      ? 'Nascondi percentuali'
      : 'Mostra percentuali';
  });
});

</script>
