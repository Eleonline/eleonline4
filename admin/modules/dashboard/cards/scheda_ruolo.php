<div class="card bg-info">
  <div class="card-header">
    <h3 class="card-title text-white"><i class="fas fa-user-shield"></i> Accesso Utente</h3>
  </div>
  <div class="card-body text-white">
    <p><strong>Ruolo:</strong> <?= htmlspecialchars($_SESSION['ruolo']) ?></p>
    <p><strong>Username:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
  </div>
</div>
