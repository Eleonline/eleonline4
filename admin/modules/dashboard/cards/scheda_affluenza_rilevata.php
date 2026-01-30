<div class="small-box bg-info" id="box-affluenza">
  <div class="inner" style="position:relative;">

    <p style="margin-bottom:2px;">Affluenza (ultimo dato)</p>
    <small id="orario-affluenza" style="display:block;margin-bottom:4px;">Ore 19:00</small>

    <h3 id="affluenza-valore" style="margin:0;">0 %</h3>
  
  </div>
  <div class="icon">
    <i class="fas fa-poll"></i>
  </div>
</div>

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
