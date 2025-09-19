<?php
require_once '../includes/check_access.php';

?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="modules.php" class="nav-link">Home</a>
    </li>
    </ul>

<?php
// Recupera i valori GET oppure imposta default
if($_SESSION['ruolo']=='superuser' and isset($_GET['id_comune']))
	$id_comune=$_GET['id_comune'];
else
	$id_comune = $_SESSION['id_comune']; // default 82025
if(isset($_GET['id_cons_gen']) 
	if($_SESSION['ruolo']=='superuser')
	{
		$id_cons_gen=$_GET['id_cons_gen'];
	}else{
		if(isset($_SESSION['id_cons_gen']) and $_GET['id_cons_gen']==$_SESSION['id_cons_gen'])
			$id_cons_gen=$_GET['id_cons_gen'];
		else 
			$id_cons_gen=verifica_cons($GET['id_cons_gen']);
		if(!$id_cons_gen) $id_cons_gen=default_cons();
		if(!$id_cons_gen) header("Location: ../logout.php");
	}
$_SESSION['id_cons_gen']=$id_cons_gen;
// Array esempio consultazioni, id => nome
$row=elenco_cons();
foreach($row as $key=>$val)
	$consultazioni[] = $val[0] => $val[1];

// Array esempio comuni, id => nome
$comuni = [
    '82025' => 'Roma',
    '82026' => 'Milano',
    '82027' => 'Napoli'
];
?>

 <!-- Scelta consultazione: dropdown solo su mobile -->
  <div class="ml-3 d-block d-sm-none dropdown">
    <a class="btn btn-outline-secondary btn-sm dropdown-toggle" href="#" role="button" id="dropdownConsultazione" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Scelta consultazione
    </a>
    <div class="dropdown-menu p-3" aria-labelledby="dropdownConsultazione" style="min-width: 250px;">
      <form method="GET" action="modules.php" id="form-consultazione-mobile">
        <div class="form-group mb-2">
          <label for="consultazione-mobile">Consultazione</label>
          <select class="form-control form-control-sm" name="id_cons_gen" id="consultazione-mobile">
            <?php foreach ($consultazioni as $id => $nome): ?>
              <option value="<?= $id ?>" <?= ($id === $id_cons_gen) ? 'selected' : '' ?>><?= $nome ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group mb-2">
          <label for="comune-mobile">Comune</label>
          <select class="form-control form-control-sm" name="id_comune" id="comune-mobile">
            <?php foreach ($comuni as $id => $nomeComune): ?>
              <option value="<?= $id ?>" <?= ($id === $id_comune) ? 'selected' : '' ?>><?= $nomeComune ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </form>
    </div>
  </div>

  <!-- Scelta consultazione: form inline solo su desktop -->
  <form class="form-inline ml-3 d-none d-sm-flex" method="GET" action="modules.php" id="form-consultazione">
    <span class="navbar-text mr-3 font-weight-bold">Scelta della Consultazione:</span>
    <select class="form-control form-control-sm mr-2" name="id_cons_gen" id="consultazione">
      <?php foreach ($consultazioni as $id => $nome): ?>
        <option value="<?= $id ?>" <?= ($id === $id_cons_gen) ? 'selected' : '' ?>><?= $nome ?></option>
      <?php endforeach; ?>
    </select>
    <select class="form-control form-control-sm mr-2" name="id_comune" id="comune">
      <?php foreach ($comuni as $id => $nomeComune): ?>
        <option value="<?= $id ?>" <?= ($id === $id_comune) ? 'selected' : '' ?>><?= $nomeComune ?></option>
      <?php endforeach; ?>
    </select>
  </form>
<script>
  // Submit automatico al cambio select
  document.getElementById('consultazione').addEventListener('change', () => {
    document.getElementById('form-consultazione').submit();
  });
  document.getElementById('comune').addEventListener('change', () => {
    document.getElementById('form-consultazione').submit();
  });
</script>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

    <!-- Fullscreen -->
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Fullscreen">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>

    <!-- Navbar notifiche -->
<li class="nav-item dropdown" id="notifiche-menu">
  <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
    <i class="fas fa-bell"></i>
    <span class="badge badge-warning navbar-badge" id="notifiche-count" style="display:none">0</span>
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notifiche-lista" style="max-height: 400px; overflow-y: auto;">
    <span class="dropdown-header">Notifiche</span>
    <div class="dropdown-divider"></div>
    <div id="notifiche-items">
      <!-- Qui arriveranno le notifiche via JS -->
      <span class="dropdown-item dropdown-footer">Nessuna notifica</span>
    </div>
  </div>
</li>


    <!-- Profilo -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#" title="Profilo">
        <i class="far fa-user"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a href="modules.php?op=100" class="dropdown-item">
          <i class="fas fa-user-cog mr-2"></i> Il mio profilo
        </a>
        <div class="dropdown-divider"></div>
        <a href="../logout.php" class="dropdown-item">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </div>
    </li>
  </ul>
</nav>
<script>
  // Submit automatico al cambio select desktop
  document.getElementById('consultazione').addEventListener('change', () => {
    document.getElementById('form-consultazione').submit();
  });
  document.getElementById('comune').addEventListener('change', () => {
    document.getElementById('form-consultazione').submit();
  });

  // Submit automatico al cambio select mobile (dropdown)
  document.getElementById('consultazione-mobile').addEventListener('change', () => {
    document.getElementById('form-consultazione-mobile').submit();
  });
  document.getElementById('comune-mobile').addEventListener('change', () => {
    document.getElementById('form-consultazione-mobile').submit();
  });

function aggiornaNotifiche() {
  fetch('../includes/notifiche.php')
    .then(response => response.json())
    .then(data => {
      const count = data.count || 0;
      const notifiche = data.notifiche || [];

      const countBadge = document.getElementById('notifiche-count');
      const lista = document.getElementById('notifiche-items');

      if (count > 0) {
        countBadge.style.display = 'inline-block';
        countBadge.textContent = count;
      } else {
        countBadge.style.display = 'none';
      }

      // Pulisci lista notifiche
      lista.innerHTML = '';

      if (notifiche.length === 0) {
        lista.innerHTML = '<span class="dropdown-item dropdown-footer">Nessuna notifica</span>';
      } else {
        // Crea ogni notifica
        notifiche.forEach(notif => {
          const item = document.createElement('a');
          item.className = 'dropdown-item';
          item.href = notif.link || '#';
          item.title = notif.tempo || '';

          // Icona + testo + tempo a destra
          item.innerHTML = `
            <i class="${notif.icona} mr-2"></i>
            ${notif.testo}
            <span class="float-right text-muted text-sm">${notif.tempo || ''}</span>
          `;
          lista.appendChild(item);
        });
      }
    })
    .catch(err => {
      console.error('Errore caricamento notifiche:', err);
    });
}

// Aggiorna subito e poi ogni 30 secondi
aggiornaNotifiche();
setInterval(aggiornaNotifiche, 30000);
</script>
<?php include '../includes/sidebar.php'; ?>

<!-- Content Wrapper -->
<div class="content-wrapper">
