<?php require_once '../includes/check_access.php'; ?>

<style>
  /* Drag & drop */
  .sortable-ghost {
    opacity: 0.6;
    border: 2px dashed #007bff;
  }

  .card, .small-box {
    cursor: move;
  }

  /* Pannello filtri laterale */
  #side-panel {
    position: fixed;
    top: 70px;
    right: -300px;
    width: 280px;
    height: calc(100% - 70px);
    background: #f8f9fa;
    border-left: 1px solid #ccc;
    padding: 1rem;
    transition: right 0.3s ease;
    z-index: 999;
    overflow-y: auto;
  }

  #side-panel.open {
    right: 0;
  }

  /* Bottone toggle */
  #side-toggle-btn {
    position: fixed;
    top: 60px;
    right: 25px;
    z-index: 1000;
  }

  /* Spazio contenuto quando pannello è aperto */
  body.panel-open section.content {
    margin-right: 300px !important;
  }

  body:not(.panel-open) section.content {
    margin-right: 0 !important;
  }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2 align-items-center">
      <div class="col-sm-6">
        <h1>Dashboard Comune</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-end">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard Comune</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content container-fluid mt-3">
  <button id="side-toggle-btn" class="btn btn-outline-secondary btn-sm" title="Mostra/Nascondi pannello filtri" aria-controls="side-panel" aria-expanded="false">
    <i class="fas fa-sliders-h"></i>
  </button>

  <div class="row" id="dashboard-cards">

    <!-- Scheda Comune -->
    <div class="col-md-6 card-wrapper" data-id="scheda-comune">
      <div class="card bg-light">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-city"></i> Informazioni Comune</h3>
        </div>
        <div class="card-body">
          <p><strong>Nome:</strong> <?= htmlspecialchars($comune['nome']) ?></p>
          <p><strong>Abitanti:</strong> <?= number_format($comune['abitanti'], 0, ',', '.') ?></p>
          <p><strong>Superficie:</strong> <?= number_format($comune['superficie_km2'], 1, ',', '.') ?> km²</p>
          <p><strong>Elettori:</strong> <?= number_format($comune['elettori'], 0, ',', '.') ?></p>
          <p><strong>Sezioni:</strong> <?= $comune['sezioni'] ?></p>
        </div>
      </div>
    </div>

  <!-- Scheda Ruolo -->
<div class="col-md-6 card-wrapper" data-id="scheda-ruolo">
  <div class="card bg-info">
    <div class="card-header">
      <h3 class="card-title text-white"><i class="fas fa-user-shield"></i> Accesso Utente</h3>
    </div>
    <div class="card-body text-white">
      <p><strong>Ruolo:</strong> <?= htmlspecialchars($_SESSION['ruolo']) ?></p>
      <p>ID comune = <?= htmlspecialchars($id_comune) ?></br>
      ID consultazione = <?= $_SESSION['id_cons'] ?></br>
	  ID Tipo di consultazione = <?= $_SESSION['tipo_cons'] ?></br></p>$aid
	  Tipo di consultazione = <?= htmlspecialchars($tipo_consultazione) ?></br></p>
	  username = <?= $_SESSION['username']?></br></p>
	  <p>Benvenuto nel sistema Eleonline. Usa il menu a sinistra per accedere alle funzionalità.</p>
    </div>
  </div>
</div>


    <!-- Box Abitanti -->
    <div class="col-lg-3 col-6 card-wrapper" data-id="box-abitanti">
      <div class="small-box bg-info">
        <div class="inner">
          <h3><?= number_format($comune['abitanti'], 0, ',', '.') ?></h3>
          <p>Abitanti</p>
        </div>
        <div class="icon">
          <i class="fas fa-city"></i>
        </div>
        <a href="#" class="small-box-footer">Dettagli <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <!-- Box Elettori -->
    <div class="col-lg-3 col-6 card-wrapper" data-id="box-elettori">
      <div class="small-box bg-success">
        <div class="inner">
          <h3><?= number_format($comune['elettori'], 0, ',', '.') ?></h3>
          <p>Elettori</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
        <a href="#" class="small-box-footer">Dettagli <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <!-- Box Sezioni -->
    <div class="col-lg-3 col-6 card-wrapper" data-id="box-sezioni">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3><?= number_format($comune['sezioni']) ?></h3>
          <p>Sezioni</p>
        </div>
        <div class="icon">
          <i class="fas fa-map-marker-alt"></i>
        </div>
        <a href="#" class="small-box-footer">Dettagli <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <!-- Box Superficie -->
    <div class="col-lg-3 col-6 card-wrapper" data-id="box-superficie">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3><?= $comune['superficie_km2'] ?> Km²</h3>
          <p>Superficie</p>
        </div>
        <div class="icon">
          <i class="fas fa-ruler-combined"></i>
        </div>
        <a href="#" class="small-box-footer">Dettagli <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>

  </div>
</section>

<!-- Pannello filtri laterale -->
<div id="side-panel" aria-label="Pannello filtri dashboard">
  <h5>Seleziona schede da visualizzare</h5>
  <hr>
  <form id="filters-form">
    <div><input type="checkbox" id="filter-scheda-comune" data-target="scheda-comune" checked><label for="filter-scheda-comune"> Informazioni Comune</label></div>
    <div><input type="checkbox" id="filter-scheda-ruolo" data-target="scheda-ruolo" checked><label for="filter-scheda-ruolo"> Accesso Utente</label></div>
    <div><input type="checkbox" id="filter-box-abitanti" data-target="box-abitanti" checked><label for="filter-box-abitanti"> Box Abitanti</label></div>
    <div><input type="checkbox" id="filter-box-elettori" data-target="box-elettori" checked><label for="filter-box-elettori"> Box Elettori</label></div>
    <div><input type="checkbox" id="filter-box-sezioni" data-target="box-sezioni" checked><label for="filter-box-sezioni"> Box Sezioni</label></div>
    <div><input type="checkbox" id="filter-box-superficie" data-target="box-superficie" checked><label for="filter-box-superficie"> Box Superficie</label></div>
    <hr>
    <button type="button" id="reset-filters" class="btn btn-sm btn-warning">Ripristina tutto</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
  const dashboard = document.getElementById('dashboard-cards');
  const sidePanel = document.getElementById('side-panel');
  const toggleBtn = document.getElementById('side-toggle-btn');
  const filtersForm = document.getElementById('filters-form');

  let saveTimeout;
  function saveStateDebounced() {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveState, 200);
  }

  function saveState() {
    const order = Array.from(dashboard.querySelectorAll('.card-wrapper')).map(el => el.dataset.id);
    const visibility = {};
    dashboard.querySelectorAll('.card-wrapper').forEach(el => {
      visibility[el.dataset.id] = !el.classList.contains('d-none');
    });
    const isOpen = sidePanel.classList.contains('open');
    localStorage.setItem('dashboardOrder', JSON.stringify(order));
    localStorage.setItem('dashboardVisibility', JSON.stringify(visibility));
    localStorage.setItem('sidePanelOpen', isOpen);
  }

  function loadState() {
    const order = JSON.parse(localStorage.getItem('dashboardOrder') || "[]");
    const visibility = JSON.parse(localStorage.getItem('dashboardVisibility') || "{}");
    const isOpen = localStorage.getItem('sidePanelOpen') === 'true';

    if (order.length) {
      order.forEach(id => {
        const el = dashboard.querySelector(`.card-wrapper[data-id="${id}"]`);
        if (el) dashboard.appendChild(el);
      });
    }

    for (const [id, visible] of Object.entries(visibility)) {
      const el = dashboard.querySelector(`.card-wrapper[data-id="${id}"]`);
      if (el) el.classList.toggle('d-none', !visible);
    }

    if (isOpen) {
      sidePanel.classList.add('open');
      document.body.classList.add('panel-open');
      toggleBtn.setAttribute('aria-expanded', true);
    }
  }

  toggleBtn.addEventListener('click', () => {
    const isOpen = sidePanel.classList.toggle('open');
    document.body.classList.toggle('panel-open', isOpen);
    toggleBtn.setAttribute('aria-expanded', isOpen);
    localStorage.setItem('sidePanelOpen', isOpen);
  });

  filtersForm.querySelectorAll('input[type=checkbox]').forEach(cb => {
    cb.addEventListener('change', e => {
      const id = e.target.dataset.target;
      const card = dashboard.querySelector(`.card-wrapper[data-id="${id}"]`);
      if (card) card.classList.toggle('d-none', !e.target.checked);
      saveStateDebounced();
    });
  });

  document.getElementById('reset-filters').addEventListener('click', () => {
    filtersForm.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true);
    dashboard.querySelectorAll('.card-wrapper').forEach(card => card.classList.remove('d-none'));
    saveStateDebounced();
    alert('Filtri ripristinati');
  });

  new Sortable(dashboard, {
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd: () => saveStateDebounced()
  });

  window.addEventListener('DOMContentLoaded', () => {
    loadState();
    filtersForm.querySelectorAll('input[type=checkbox]').forEach(cb => {
      const id = cb.dataset.target;
      const card = dashboard.querySelector(`.card-wrapper[data-id="${id}"]`);
      if (card) cb.checked = !card.classList.contains('d-none');
    });
  });
</script>
