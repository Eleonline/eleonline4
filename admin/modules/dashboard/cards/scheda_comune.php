<div class="card bg-light">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-city"></i> Informazioni Comune</h3>
  </div>
  <div class="card-body">
    <p><strong>Nome:</strong> <?= htmlspecialchars($comune['nome']) ?></p>
    <p><strong>Abitanti:</strong> <?= number_format($comune['abitanti']) ?></p>
    <p><strong>Elettori:</strong> <?= number_format($comune['elettori']) ?></p>
    <p><strong>Sezioni:</strong> <?= $comune['sezioni'] ?></p>
    <p><strong>Superficie:</strong> <?= number_format($comune['superficie_km2'],1) ?> km²</p>
  </div>
</div>
