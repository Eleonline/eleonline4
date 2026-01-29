<div class="small-box bg-danger" id="box-schede">
  <div class="inner">
    <p>Schede scrutinate</p>
    <div id="dati-schede">
      <?php
      // Dati iniziali simulati
      $schede_scrutinate = 6450;
      $schede_totali = 13200;
      ?>
      <h3><?= number_format($schede_scrutinate) ?> su <?= number_format($schede_totali) ?></h3>
    </div>
  </div>

  <div class="icon">
    <i class="fas fa-poll-h"></i>
  </div>
</div>

<!-- JS auto-refresh solo per questo box -->
<script>
function aggiornaSchede() {
  fetch('dashboard/cards/refresh_schede_scrutinate.php') // PHP che restituisce solo il contenuto del box
    .then(res => res.text())
    .then(html => {
      document.getElementById('dati-schede').innerHTML = html;
    })
    .catch(err => console.error("Errore aggiornamento schede:", err));
}

// Auto-refresh ogni 60 secondi
setInterval(aggiornaSchede, 60000);
</script>
