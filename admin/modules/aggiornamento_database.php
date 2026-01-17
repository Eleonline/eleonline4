<?php require_once '../includes/check_access.php'; ?>

<section class="content">
<div class="container-fluid mt-4">

<!-- CARD INIZIALE -->
<div id="cardIniziale" class="card card-warning shadow-sm">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fas fa-database me-2"></i>
      Aggiornamento Database
    </h3>
  </div>

  <div class="card-body">

    <div class="alert alert-info">
      <strong>Procedura di aggiornamento struttura database Eleonline 4</strong><br>
      Questa operazione modifica tabelle e campi.<br>
      <b>È obbligatorio eseguire un backup prima di continuare.</b>
    </div>

    <button id="btnAggiornaDB" class="btn btn-warning">
      <i class="fas fa-play me-2"></i>
      Avvia aggiornamento Database
    </button>

  </div>
</div>

<!-- CARD LOG -->
<div id="cardAggiornaDB" class="card card-info shadow-sm" style="display:none;">
  <div class="card-header">
    <h3 class="card-title" id="titoloAggiornaDB">
      <i class="fas fa-spinner fa-spin me-2"></i>
      Aggiornamento Database in corso...
    </h3>
  </div>

  <div class="card-body">
    <pre id="logAggiornaDB"
         style="white-space: pre-wrap;
                background:#f4f4f4;
                border:1px solid #ccc;
                padding:10px;
                height:350px;
                overflow:auto;
                font-family: monospace;"></pre>
  </div>
</div>

</div>
</section>

<script>
document.getElementById('btnAggiornaDB').addEventListener('click', async function () {

  document.getElementById('cardIniziale').style.display = 'none';
  document.getElementById('cardAggiornaDB').style.display = 'block';

  const logBox = document.getElementById('logAggiornaDB');
  const titolo = document.getElementById('titoloAggiornaDB');

  logBox.textContent = '';

  try {

    const response = await fetch('aggiornadb_output.php');

    if (!response.ok || !response.body) {
      throw new Error("Errore stream");
    }

    const reader = response.body
      .pipeThrough(new TextDecoderStream())
      .getReader();

    while (true) {
      const { value, done } = await reader.read();
      if (done) break;

      const lines = value.split('\n');

      lines.forEach(line => {
        if (line.trim() !== '') {
          const div = document.createElement('div');
          div.textContent = line;
          logBox.appendChild(div);
          logBox.scrollTop = logBox.scrollHeight;
        }
      });
    }

    titolo.innerHTML =
      '<i class="fas fa-check-circle text-success me-2"></i>Aggiornamento Database completato';

    logBox.appendChild(document.createElement('hr'));
    const ok = document.createElement('div');
    ok.style.color = 'green';
    ok.textContent = 'Operazione completata con successo.';
    logBox.appendChild(ok);

  } catch (e) {

    titolo.innerHTML =
      '<i class="fas fa-times-circle text-danger me-2"></i>Errore aggiornamento Database';

    const err = document.createElement('div');
    err.style.color = 'red';
    err.textContent = 'Errore durante l\'esecuzione.';
    logBox.appendChild(err);

  }

});
</script>
