<?php
require_once '../includes/check_access.php';

$param = strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;

$fase          = isset($param['fase']) ? intval($param['fase']) : 0;
$fase_prec     = max(0, $fase - 1);

$id_cons_gen2  = isset($param['id_cons_gen2']) ? intval($param['id_cons_gen2']) : 0;
$id_comune2    = isset($param['id_comune2']) ? intval($param['id_comune2']) : 0;
$indirizzoweb  = isset($param['indirizzoweb']) ? addslashes($param['indirizzoweb']) : 'https://www.eleonline.it/client/';
?>

<section class="content">
<div class="container-fluid">

  <!-- TITOLO -->
  <div class="mb-3">
    <h2 class="text-primary">
      <i class="fas fa-cloud-download-alt"></i>
      Scarica liste da altri comuni
    </h2>
    <small class="text-muted">
      Importazione guidata da altre installazioni Eleonline
    </small>
  </div>

  <!-- STEPPER -->
  <div class="row mb-3">
    <?php
    $steps = ['Server', 'Consultazione', 'Comune', 'Conferma'];
    $icons = ['fa-globe', 'fa-flag', 'fa-city', 'fa-check'];
    foreach ($steps as $i => $label) {
        $active = ($fase == $i) ? 'bg-info' : (($fase > $i) ? 'bg-primary text-white' : 'bg-light');
        echo "
        <div class='col-6 col-md-3 mb-2'>
          <div class='info-box $active'>
            <span class='info-box-icon'><i class='fas {$icons[$i]}'></i></span>
            <div class='info-box-content'>
              <span class='info-box-text'>$label</span>
              <span class='info-box-number'>".($i+1)."</span>
            </div>
          </div>
        </div>";
    }
    ?>
  </div>

  <!-- CARD -->
  <div class="card card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-download"></i> Procedura di importazione
      </h3>
    </div>

    <form id="wizardForm" action="modules.php" method="get">
      <div class="card-body">

        <!-- FASE 0 – SERVER -->
		
        <?php if ($fase == 0) { ?>
          <input type="hidden" name="op" value="20">
          <input type="hidden" name="fase" value="1">

          <div class="form-group">
            <label><i class="fas fa-globe"></i> URL del server remoto</label>
            <input type="text" name="indirizzoweb" class="form-control"
                   value="<?= htmlspecialchars($indirizzoweb) ?>" required>
          </div>
        <?php } ?>

        <!-- SERVER BLOCCATO (FASE 1 e 2) -->
        <?php if ($fase > 0 && $fase < 3) { ?>
          <div class="alert alert-info py-2">
            <i class="fas fa-globe"></i>
            <strong>Server:</strong> <?= htmlspecialchars($indirizzoweb) ?>
          </div>
        <?php } ?>

        <!-- FASE 1 – CONSULTAZIONE -->
        <?php if ($fase == 1) {
            $urlrem = "$indirizzoweb/file.php?fase=1&tipo_cons=$tipo_cons";
        ?>
          <input type="hidden" name="op" value="20">
          <input type="hidden" name="fase" value="2">
          <input type="hidden" name="indirizzoweb" value="<?= htmlspecialchars($indirizzoweb) ?>">

          <div class="form-group">
            <label><i class="fas fa-flag"></i> Consultazione</label>
            <div class="border rounded bg-light p-2 remote-load">
              <script src="<?= htmlspecialchars($urlrem) ?>"></script>
            </div>
          </div>
        <?php } ?>

        <!-- FASE 2 – COMUNE -->
        <?php if ($fase == 2) {
            $urlcomune = "$indirizzoweb/file.php?fase=2&id_cons_gen2=$id_cons_gen2";
        ?>
          <input type="hidden" name="op" value="20">
          <input type="hidden" name="fase" value="3">
          <input type="hidden" name="indirizzoweb" value="<?= htmlspecialchars($indirizzoweb) ?>">
          <input type="hidden" name="id_cons_gen2" value="<?= intval($id_cons_gen2) ?>">
          <input type="hidden" name="descr_cons" value="<?= $_GET[$_GET['id_cons_gen2']] ?>">

          <div class="alert alert-success py-2">
            <i class="fas fa-flag"></i> Consultazione selezionata: <?= $_GET[$_GET['id_cons_gen2']] ?> 
          </div>

          <div class="form-group">
            <label><i class="fas fa-city"></i> Comune</label>
            <div class="border rounded bg-light p-2 remote-load">
              <script src="<?= htmlspecialchars($urlcomune) ?>"></script>
            </div>
          </div>
		  <input type="hidden" name="descr_comune2" id="descr_comune2">
        <?php } ?>

       <!-- FASE 3 – CONFERMA -->

<?php if ($fase == 3) { ?>
    <input type="hidden" name="op" value="20">
    <input type="hidden" name="fase" value="4">
    <input type="hidden" name="indirizzoweb" value="<?= htmlspecialchars($indirizzoweb) ?>">
    <input type="hidden" name="id_cons_gen2" value="<?= intval($id_cons_gen2) ?>">
    <input type="hidden" name="id_comune2" value="<?= intval($id_comune2) ?>">

    <div class="alert alert-warning p-3 border border-warning rounded">
        <h5>
            <i class="fas fa-exclamation-triangle"></i>
            Conferma importazione
        </h5>

        <ul class="list-unstyled mt-3">
            <li>
                <i class="fas fa-globe text-primary"></i>
                <strong> Server:</strong> <?= htmlspecialchars($indirizzoweb) ?>
            </li>
            <li class="mt-2">
                <i class="fas fa-flag text-success"></i>
                <strong> Consultazione:</strong> <?= $_GET['descr_cons'] ?>
            </li>
            <li class="mt-2">
				<i class="fas fa-city text-info"></i>
				<strong> Comune:</strong>
				<?= htmlspecialchars($_GET['descr_comune2'] ?? '') ?>
			</li>

        </ul>

        <p class="text-danger mt-2 mb-0">
            L’operazione importerà le liste e candidati dal comune selezionato.
        </p>
    </div>
<?php } ?>


        <!-- FASE 4 – IMPORTAZIONE DATI -->
        <?php if ($fase == 4) { 
            $url_dati = rtrim($indirizzoweb, '/') 
                      . "/modules.php?op=backup&id_cons_gen=" . intval($id_cons_gen2) 
                      . "&id_comune=" . intval($id_comune2);

            $dati_remoti = @file_get_contents($url_dati);

            if ($dati_remoti === false) {
                echo '<div class="alert alert-danger">Errore: impossibile recuperare i dati dal server remoto.</div>';
            } else {
                echo '<div class="alert alert-info">Importazione delle liste e candidati in corso...</div>';
                include_once('importa.php');
                importa($dati_remoti);
                echo '<div class="alert alert-success mt-2">Importazione completata!</div>';
            }
        } ?>

      </div>

      <!-- FOOTER BOTTONI -->
<div class="card-footer">
    <div class="row">
        <div class="col-6">
            <?php if ($fase > 0) { ?>
                <a class="btn btn-secondary btn-block btn-lg mb-2"
				   href="modules.php?op=20">
					<i class="fas fa-times"></i> Annulla
				</a>
            <?php } ?>
        </div>

        <div class="col-6 text-right">
            <?php if ($fase < 3) { ?>
                <!-- Fasi 0,1,2 -->
                <button type="submit" class="btn btn-primary btn-block btn-lg submit-btn">
                    Prosegui <i class="fas fa-arrow-right"></i>
                </button>
            <?php } elseif ($fase == 3) { ?>
                <!-- Fase 3: Conferma -->
                <button type="submit" class="btn btn-danger btn-block btn-lg submit-btn">
                    <i class="fas fa-cloud-download-alt"></i> Avvia importazione
                    <i class="fas fa-spinner fa-spin d-none ms-2"></i>
                </button>
            <?php } ?>
        </div>
    </div>
</div>

    </form>
  </div>
</div>

<!-- JS UX -->
<script>
document.getElementById('wizardForm')?.addEventListener('submit', function (e) {
    const btn = this.querySelector('.submit-btn');
    btn.querySelector('.fa-spinner')?.classList.remove('d-none');
    btn.disabled = true;
});
document.querySelectorAll('.remote-load').forEach(box => {
    const selects = box.querySelectorAll('select');
    selects.forEach(s => s.classList.add('form-control'));
});
</script>
<script>
const selectComune = document.querySelector('select[name="id_comune2"]');
const descrComuneInput = document.getElementById('descr_comune2');

if (selectComune && descrComuneInput) {
    // Imposta subito il valore nascosto al caricamento della pagina
    descrComuneInput.value = selectComune.options[selectComune.selectedIndex].text;

    // Aggiorna se l'utente cambia selezione
    selectComune.addEventListener('change', function() {
        descrComuneInput.value = this.options[this.selectedIndex].text;
        console.log('COMUNE COPIATO:', descrComuneInput.value);
    });
}
</script>

</section>
