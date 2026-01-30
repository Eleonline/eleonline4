<?php
// Demo PHP: array iniziale delle liste
// Sostituire con query al DB reale
$listes = [
    ['lista' => 'Lista A', 'voti' => 1245],
    ['lista' => 'Lista B', 'voti' => 980],
    ['lista' => 'Lista C', 'voti' => 760],
];

// Calcolo totale voti
$totaleVoti = array_sum(array_column($listes, 'voti'));
?>

<div class="card bg-light" id="box-liste">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-layer-group"></i> Liste</h3>
	<div class="card-tools d-flex align-items-center">
	   <span class="badge badge-info" id="badge-sezioni">
            Sezioni <?php echo $sezioni_scrutinate; ?> su <?php echo $totale_sezioni; ?>
        </span>
    </div>
  </div>
  <div class="card-body">
    <div id="liste-contenuto">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>Lista</th>
            <th>Voti</th>
            <th>%</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($listes as $l): ?>
          <tr>
            <td><?= htmlspecialchars($l['lista']) ?></td>
            <td><?= number_format($l['voti'], 0, ',', '.') ?></td>
            <td>
              <?php 
                $percent = $totaleVoti > 0 ? ($l['voti'] / $totaleVoti) * 100 : 0;
                echo number_format($percent, 1, '.', '') . '%'; // punto come separatore decimale
              ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th>Totale</th>
            <th><?= number_format($totaleVoti, 0, ',', '.') ?></th>
            <th>100%</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<!-- JS auto-refresh -->
<script>
function aggiornaListe() {
    fetch('dashboard/cards/dati_liste.php') // PHP che restituisce solo la tabella aggiornata
      .then(res => res.text())
      .then(html => {
          document.getElementById('liste-contenuto').innerHTML = html;
      })
      .catch(err => console.error("Errore aggiornamento liste:", err));
}

// Primo caricamento
aggiornaListe();

// Auto-refresh ogni 60 secondi
setInterval(aggiornaListe, 60000);
</script>
