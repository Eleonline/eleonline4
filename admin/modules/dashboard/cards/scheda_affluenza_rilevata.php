<div class="small-box bg-info" id="box-affluenza">
  <div class="inner" style="position:relative;">

    <p style="margin-bottom:2px;">Affluenza (ultimo dato)</p>
    <small id="orario-affluenza" style="display:block;margin-bottom:4px;">Ore 19:00</small>

    <h3 id="affluenza-valore" style="margin:0;">0 %</h3>

    <!-- Badge LIVE lampeggiante -->
    <span id="badge-live" 
          style="background:red;color:white;font-size:0.7rem;padding:2px 6px;border-radius:4px;
                 position:absolute;top:10px;right:10px;">
      LIVE
    </span>

  </div>
  <div class="icon">
    <i class="fas fa-poll"></i>
  </div>
</div>

<!-- CSS lampeggio netto -->
<style>
  @keyframes blink {
    0%, 50%, 100% { visibility: visible; }
    25%, 75% { visibility: hidden; }
  }
  #badge-live {
    animation: blink 2s step-start infinite;
  }
</style>

<!-- JS auto-refresh via PHP senza contatore animato -->
<script>
const affluenzaEl = document.getElementById('affluenza-valore');
const orarioEl = document.getElementById('orario-affluenza');

function aggiornaAffluenza() {
  fetch('dashboard/cards/refresh_dati_affluenza.php') // PHP endpoint restituisce JSON {affluenza:62.45, orario:"19:00"}
    .then(res => res.json())
    .then(data => {
      affluenzaEl.textContent = data.affluenza.toFixed(2) + " %";
      orarioEl.textContent = "Ore " + data.orario;
    })
    .catch(err => console.error("Errore aggiornamento affluenza:", err));
}

// Primo aggiornamento
aggiornaAffluenza();

// Auto-refresh ogni 60 secondi
setInterval(aggiornaAffluenza, 60000);
</script>
