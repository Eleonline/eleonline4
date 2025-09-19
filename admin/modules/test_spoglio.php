<style>
/* Rimuove freccette nei campi number (Chrome, Safari) */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
/* Rimuove freccette nei campi number (Firefox) */
input[type="number"] {
  -moz-appearance: textfield;
}

/* Ottimizzazione larghezze tabelle */
.smartable th:nth-child(1),
.smartable td:nth-child(1) {
  width: 50px;
  text-align: center;
}
.smartable th:nth-child(3),
.smartable td:nth-child(3) {
  width: 100px;
  text-align: center;
}
.smartable td input {
  text-align: right;
  width: 100%;
}
</style>

<section class="content">
  <div class="container-fluid">
    <section class="content-header">
      <h1>Sezione n. 7</h1>
    </section>

    <!-- Navigazione Sezioni -->
    <div class="mb-3">
      <div class="btn-group">
        <button class="btn btn-outline-primary">1</button>
        <button class="btn btn-outline-primary">2</button>
        <button class="btn btn-outline-primary">3</button>
        <button class="btn btn-primary">7</button>
        <button class="btn btn-outline-primary">8</button>
      </div>
    </div>

    <!-- Tabs Affluenze / Lista -->
    <ul class="nav nav-tabs" id="tabSpoglio">
      <li class="nav-item">
        <a class="nav-link" href="#">Affluenze</a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="#">Lista e Preferenza</a>
      </li>
    </ul>

    <!-- Statistiche Ultima Ora -->
    <h5 class="text-center my-4">Votanti Ultima Ora</h5>
    <table class="table table-bordered text-center mx-auto" style="max-width: 400px; background-color: #f8f9fa; border-radius: 0.375rem;">
      <tbody>
        <tr>
          <td><strong>Votanti Uomini</strong><br>208</td>
          <td><strong>Votanti Donne</strong><br>210</td>
          <td><strong>Totali</strong><br>418</td>
        </tr>
      </tbody>
    </table>

    <!-- Tabella Voti di Lista -->
    <h3 class="mt-5">Voti di Lista</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-striped smartable">
        <thead class="bg-primary text-white">
          <tr>
            <th>#</th>
            <th>Denominazione</th>
            <th>Voti</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>ALLEANZA VERDI E SINISTRA</td>
            <td><input type="number" class="form-control form-control-sm voto-lista" maxlength="5" value="18"></td>
          </tr>
          <tr>
            <td>2</td>
            <td>MOVIMENTO 5 STELLE</td>
            <td><input type="number" class="form-control form-control-sm voto-lista" maxlength="5" value="44"></td>
          </tr>
          <tr>
            <td>3</td>
            <td>FRATELLI D'ITALIA</td>
            <td><input type="number" class="form-control form-control-sm voto-lista" maxlength="5" value="56"></td>
          </tr>
          <tr class="table-primary font-weight-bold">
            <td colspan="2">Totale Voti di lista</td>
            <td id="totaleVotiLista">394</td>
          </tr>
        </tbody>
      </table>
    </div>

<div class="d-flex align-items-center mt-2">
  <div class="form-check me-2">
    <input class="form-check-input" type="checkbox" id="eliminaRiga">
    <label class="form-check-label" for="eliminaRiga">Elimina</label>
  </div>
  <button class="btn btn-danger btn-sm" id="btnConfermaElimina" style="display: none;">
    OK
  </button>
</div>



    <!-- Totali Finali -->
    <h3 class="mt-4">Totali Finali</h3>
    <table class="table table-bordered text-center">
      <thead class="table-light">
        <tr>
          <th>Voti Validi</th>
          <th>Schede Nulle</th>
          <th>Schede Bianche</th>
          <th>Voti Nulli</th>
          <th>Voti Contestati</th>
          <th>Tot. Voti non Validi</th>
          <th>Voti Totali</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input id="votiValidi" type="number" class="form-control text-end" value="394" /></td>
          <td><input id="schedeNulle" type="number" class="form-control text-end" value="18" /></td>
          <td><input id="schedeBianche" type="number" class="form-control text-end" value="6" /></td>
          <td><input id="votiNulli" type="number" class="form-control text-end" value="0" /></td>
          <td><input id="votiContestati" type="number" class="form-control text-end" value="0" /></td>
          <td id="totNonValidi">24</td>
          <td id="totVoti">418</td>
          <td><button class="btn btn-success">Ok</button></td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<script>
function aggiornaTotaleVotiLista() {
  let totale = 0;
  document.querySelectorAll('.voto-lista').forEach(input => {
    totale += parseInt(input.value || 0);
  });

  document.getElementById('totaleVotiLista').textContent = totale;

  const votiValidiInput = document.getElementById('votiValidi');
  if (votiValidiInput) votiValidiInput.value = totale;

  aggiornaTotaliFinali();
}

function aggiornaTotaliFinali() {
  const nulle = parseInt(document.getElementById('schedeNulle').value || 0);
  const bianche = parseInt(document.getElementById('schedeBianche').value || 0);
  const nulli = parseInt(document.getElementById('votiNulli').value || 0);
  const contestati = parseInt(document.getElementById('votiContestati').value || 0);
  const validi = parseInt(document.getElementById('votiValidi').value || 0);

  const nonValidi = nulle + bianche + nulli + contestati;
  const totali = validi + nonValidi;

  document.getElementById('totNonValidi').textContent = nonValidi;
  document.getElementById('totVoti').textContent = totali;
}

// Eventi per aggiornare automaticamente
document.querySelectorAll('.voto-lista').forEach(input => {
  input.addEventListener('input', aggiornaTotaleVotiLista);
});
['schedeNulle', 'schedeBianche', 'votiNulli', 'votiContestati', 'votiValidi'].forEach(id => {
  document.getElementById(id).addEventListener('input', aggiornaTotaliFinali);
});

// Pulsante Elimina (checkbox)
const checkboxElimina = document.getElementById('eliminaRiga');
const btnConfermaElimina = document.getElementById('btnConfermaElimina');

checkboxElimina.addEventListener('change', function () {
  btnConfermaElimina.style.display = this.checked ? 'inline-block' : 'none';
});

btnConfermaElimina.addEventListener('click', function () {
  if (checkboxElimina.checked) {
    document.querySelectorAll('.voto-lista').forEach(input => input.value = '');
    aggiornaTotaleVotiLista();
    checkboxElimina.checked = false;
    btnConfermaElimina.style.display = 'none';
  }
});
</script>
