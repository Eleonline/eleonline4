<?php
// Demo PHP: array iniziale dei candidati
// Sostituire con query al DB se disponibile
$candidati = [
    ['nome' => 'Mario', 'cognome' => 'Rossi', 'lista' => 'Lista A', 'voti' => 1245],
    ['nome' => 'Luigi', 'cognome' => 'Bianchi', 'lista' => 'Lista B', 'voti' => 980],
    ['nome' => 'Anna', 'cognome' => 'Verdi', 'lista' => 'Lista C', 'voti' => 760],
];

// Calcolo totale voti
$totaleVoti = array_sum(array_column($candidati, 'voti'));
?>

<div class="card bg-light" id="box-candidati">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-user-tie"></i> Candidati Sindaco/Presidente</h3>
  </div>
  <div class="card-body">
    <div id="candidati-contenuto">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>Candidato</th>
            <th>Lista</th>
            <th>Voti</th>
            <th>%</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($candidati as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['nome'] . ' ' . $c['cognome']) ?></td>
            <td><?= htmlspecialchars($c['lista']) ?></td>
            <td><?= number_format($c['voti'], 0, ',', '.') ?></td>
            <td>
              <?php 
                $percent = $totaleVoti > 0 ? ($c['voti'] / $totaleVoti) * 100 : 0; 
                echo number_format($percent, 1, '.', '') . '%'; // <-- qui il punto come separatore decimale
              ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="2">Totale</th>
            <th><?= number_format($totaleVoti, 0, ',', '.') ?></th>
            <th>100%</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<!-- JS auto-refresh per la scheda candidati -->
<script>
function aggiornaCandidati() {
    fetch('dashboard/cards/dati_candidati.php') // PHP che restituisce solo il contenuto HTML della tabella
      .then(res => res.text())
      .then(html => {
          document.getElementById('candidati-contenuto').innerHTML = html;
      })
      .catch(err => console.error("Errore aggiornamento candidati:", err));
}

// Primo caricamento
aggiornaCandidati();

// Auto-refresh ogni 60 secondi
setInterval(aggiornaCandidati, 60000);
</script>
