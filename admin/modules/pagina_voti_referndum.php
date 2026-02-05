<?php
// Config colori quesiti
if(is_file('../../client/temi/bootstrap/pagine/config_colori_quesiti.php'))
    $coloripath='../../client/temi/bootstrap/pagine/';
else
    $coloripath='../client/temi/bootstrap/pagine/';
require_once $coloripath.'config_colori_quesiti.php';

// Imposta l'id colore del quesito (puoi cambiarlo dinamicamente)
$id_colore = 1; 
$coloreQuesito = isset($coloriQuesiti[$id_colore]) ? $coloriQuesiti[$id_colore]['colore'] : 'transparent';
$immagineQuesito = isset($coloriQuesiti[$id_colore]) ? $coloriQuesiti[$id_colore]['immagine'] : 'imgscheda/default.jpg';
$nomeColore = isset($coloriQuesiti[$id_colore]) ? $coloriQuesiti[$id_colore]['nome'] : '';
?>

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

/* Spazi verticali ridotti */
section.content > .container-fluid > * {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

h3, h5 {
  margin-top: 0.75rem;
  margin-bottom: 0.75rem;
}

/* Rimuove margine extra da btn-group */
.mb-3 {
  margin-bottom: 0.5rem !important;
}

/* Riduce margine checkbox + bottone */
.d-flex.align-items-center.mt-2 {
  margin-top: 0.5rem !important;
  margin-bottom: 0.5rem !important;
}
input[type=number].text-end {
  text-align: right !important;
}
  #boxMessaggio {
    max-width: 500px;
    margin: 20px auto; /* centro orizzontale con margine sopra e sotto */
    display: none;
    text-align: center;
  }
  .btn-verde {
  border: 3px solid #28a745 !important;
  box-shadow: 0 0 5px #28a745;
}
.btn-rosso {
  border: 3px solid #dc3545 !important;
  box-shadow: 0 0 5px #dc3545;
}

</style>

<table class="table table-bordered smartable text-center" style="table-layout:fixed; width:100%;">

  <!-- TITOLO -->
  <thead class="bg-primary text-white">
    <tr>
      <th colspan="6">Quesito Referendario</th>
    </tr>

    <!-- TESTO QUESITO -->
    <tr class="bg-light">
  <th colspan="6" class="text-start fw-normal" style="padding:0; border-radius:0;">
    <div style="background-color: <?= $coloreQuesito ?>; color:#000; display:flex; justify-content:space-between; align-items:center; padding:0.5rem; border-radius:0.25rem;">
      <div>
        <strong>Quesito:</strong>
        Volete voi che sia abrogato l’art. 579 del codice penale recante
        “Omicidio del consenziente”?
      </div>
      <img src="<?= $coloripath . $immagineQuesito ?>" alt="<?= $nomeColore ?>" style="max-height:40px; margin-left:10px;">
    </div>
  </th>
</tr>


    <!-- INTESTAZIONI COLONNE -->
    <tr class="bg-light">
      <th style="width:16.66%">Votanti Sì</th>
      <th style="width:16.66%">Votanti No</th>
      <th style="width:16.66%">Voti Validi</th>
      <th style="width:16.66%">Schede Bianche</th>
      <th style="width:16.66%">Schede Contestate</th>
      <th style="width:16.66%">Schede Nulle</th>
    </tr>
  </thead>

  <tbody>
    <!-- RIGA UNICA DI INSERIMENTO -->
    <tr>
      <td><input type="number" id="voti_si" class="form-control form-control-sm text-end"></td>
      <td><input type="number" id="voti_no" class="form-control form-control-sm text-end"></td>
      <td><input type="number" id="voti_validi" class="form-control form-control-sm text-end"></td>
      <td><input type="number" id="bianche" class="form-control form-control-sm text-end"></td>
      <td><input type="number" id="contestate" class="form-control form-control-sm text-end"></td>
      <td><input type="number" id="nulle" class="form-control form-control-sm text-end"></td>
    </tr>

    <!-- TOTALI -->
   <!-- TOTALI -->
<tr class="table-primary fw-bold">
  <td>Tot. Voti non Validi</td>
  <td id="tot_non_validi">0</td>

  <td>Voti Totali</td>
  <td id="voti_totali">0</td>

  <td>Votanti</td>
  <td id="tot_votanti">0</td>
</tr>


    <!-- PULSANTE -->
    <tr>
      <td colspan="6" class="text-end">
        <button class="btn btn-success btn-sm">Ok</button>
      </td>
    </tr>
  </tbody>
</table>







    <!-- Checkbox + Bottone Elimina -->
    <div class="d-flex align-items-center">
      <div class="form-check me-2">
        <input class="form-check-input" type="checkbox" id="eliminaRiga">
        <label class="form-check-label" for="eliminaRiga">Elimina</label>
      </div>
      <button class="btn btn-danger btn-sm" id="btnConfermaElimina" style="display: none;">
        OK
      </button>
    </div>
<script>
function aggiornaTotali() {
  const validi = +voti_validi.value || 0;
  const b = +bianche.value || 0;
  const c = +contestate.value || 0;
  const n = +nulle.value || 0;

  const nonValidi = b + c + n;
  const totali = validi + nonValidi;

  tot_non_validi.innerText = nonValidi;
  voti_totali.innerText = totali;
  tot_votanti.innerText = totali;
}
</script>