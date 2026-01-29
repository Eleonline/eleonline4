<div class="small-box bg-success" id="box-aggiornamento">
  <div class="inner">
    <p style="margin-bottom:2px;">Ultimo aggiornamento effettuato</p>
    <div id="dati-aggiornamento">
      <?php
      // Dati iniziali
      $data = date('d/m/Y');
      $ora  = date('H:i');
      ?>
      <h4><?= $data ?></h4>
      <h5>Ore <?= $ora ?></h5>
    </div>
  </div>

  <div class="icon">
    <i class="fas fa-clock"></i>
  </div>
</div>

<!-- JS auto-refresh solo per questo box -->
<script>
function aggiornaUltimoAggiornamento() {
  fetch('dashboard/cards/refresh_ultimo_aggiornamento.php') // PHP che restituisce solo il contenuto del box
    .then(res => res.text())
    .then(html => {
      document.getElementById('dati-aggiornamento').innerHTML = html;
    })
    .catch(err => console.error("Errore aggiornamento:", err));
}

// Auto-refresh ogni 60 secondi
setInterval(aggiornaUltimoAggiornamento, 60000);
</script>

