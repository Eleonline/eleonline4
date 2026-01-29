<?php
// Demo PHP: array iniziale delle liste
// Sostituire con query al DB reale
$listes = [
    ['lista' => 'Lista A', 'voti' => 1245],
    ['lista' => 'Lista B', 'voti' => 980],
    ['lista' => 'Lista C', 'voti' => 760],
];
?>

<div class="card bg-light" id="box-liste">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-layer-group"></i> Liste</h3>
  </div>
  <div class="card-body">
    <div id="liste-contenuto">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>Lista</th>
            <th>Voti</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($listes as $l): ?>
          <tr>
            <td><?= htmlspecialchars($l['lista']) ?></td>
            <td><?= number_format($l['voti']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
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
