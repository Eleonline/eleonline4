<div class="card bg-info">
  <div class="card-header">
    <h3 class="card-title text-white"><i class="fas fa-user-shield"></i> Accesso Utente</h3>
  </div>
  <div class="card-body text-white">
    <p><strong>Ruolo:</strong> <?= htmlspecialchars($_SESSION['ruolo']) ?></p>
      <p>ID comune = <?= htmlspecialchars($id_comune) ?></br>
      ID consultazione = <?= $_SESSION['id_cons'] ?></br>
	  ID Tipo di consultazione = <?= $_SESSION['tipo_cons'] ?></br></p>
	  Tipo di consultazione = <?= htmlspecialchars($tipo_consultazione) ?></br></p>
	  username = <?= $_SESSION['username']?></br></p>
	  <p>Benvenuto nel sistema Eleonline. Usa il menu a sinistra per accedere alle funzionalità.</p>
  </div>
</div>
