<?php
require_once '../includes/check_access.php';
?>

<style>
.sortable-ghost { opacity: .6; border: 2px dashed #007bff; }
.card, .small-box { cursor: move; }
/* Smartphone */
@media (max-width: 767px) {
  .card, .small-box {
    cursor: default !important; /* disabilita move su smartphone */
  }

  #side-toggle-btn {
    top: calc(56px + 10px); /* altezza navbar + margine */
    right: 15px;
    z-index: 1000;
    padding: 0.25rem 0.5rem; /* bottone più compatto */
    font-size: 0.85rem;
  }

  /* Spazio extra per il content-wrapper, se serve */
  .content-wrapper {
    padding-top: 66px; /* stessa altezza della navbar su smartphone */
  }
}


#side-panel {
  position: fixed;
  top: 70px;
  right: -300px;
  width: 280px;
  height: calc(100% - 70px);
  background: #f8f9fa;
  border-left: 1px solid #ccc;
  padding: 1rem;
  transition: right .3s;
  z-index: 999;
  overflow-y: auto;
}
#side-panel.open { right: 0; }
#side-toggle-btn { position: fixed; top: 60px; right: 25px; z-index: 1000; }
body.panel-open section.content { margin-right: 300px !important; }
</style>

<section class="content-header">
  <h1>Dashboard Comune</h1>
</section>

<section class="content container-fluid mt-3">
<button id="side-toggle-btn" class="btn btn-outline-secondary btn-sm">
  <i class="fas fa-sliders-h"></i>
</button>

<?php
$ruolo = $_SESSION['ruolo'];
$cards = [
  'scheda-comune'          => ['file'=>'cards/scheda_comune.php','col'=>'col-md-6','roles'=>['superuser','admin','operatore'], 'defaultVisible'=>true],
  'scheda-ruolo'           => ['file'=>'cards/scheda_ruolo.php','col'=>'col-md-6','roles'=>['superuser','admin','operatore'], 'defaultVisible'=>true],
  'box-abitanti'           => ['file'=>'cards/box_abitanti.php','col'=>'col-lg-3 col-6','roles'=>['superuser','admin'], 'defaultVisible'=>true],
  'box-elettori'           => ['file'=>'cards/box_elettori.php','col'=>'col-lg-3 col-6','roles'=>['superuser','admin'], 'defaultVisible'=>true],
  'box-sezioni'            => ['file'=>'cards/box_sezioni.php','col'=>'col-lg-3 col-6','roles'=>['superuser','admin','operatore'], 'defaultVisible'=>true],
  'box-superficie'         => ['file'=>'cards/box_superficie.php','col'=>'col-lg-3 col-6','roles'=>['superuser'], 'defaultVisible'=>true],
  'box-demo'               => ['file'=>'cards/box_demo.php','col'=>'col-lg-3 col-6','roles'=>['superuser','admin'], 'defaultVisible'=>false],
  'scheda-demo'            => ['file'=>'cards/scheda_demo.php','col'=>'col-md-6','roles'=>['superuser','admin'], 'defaultVisible'=>false],
  'box-grafico'            => ['file'=>'cards/box_grafico.php','col'=>'col-md-6','roles'=>['superuser','admin','operatore'], 'defaultVisible'=>false],
  'box-affluenze-orario'   => ['file'=>'cards/box_affluenze_orario.php','col'=>'col-md-6','roles'=>['superuser','admin','operatore'], 'defaultVisible'=>true],
];
?>

<div class="row invisible" id="dashboard-cards">
<?php foreach ($cards as $id => $card): ?>
  <?php if (!in_array($ruolo,$card['roles'])) continue; ?>
  <div class="<?= $card['col'] ?> card-wrapper" data-id="<?= $id ?>">
    <?php include __DIR__ . '/' . $card['file']; ?>
  </div>
<?php endforeach; ?>
</div>

<div id="side-panel">
  <h5>Mostra / Nascondi schede</h5><hr>
  <?php foreach ($cards as $id => $card): ?>
    <?php if (!in_array($ruolo,$card['roles'])) continue; ?>
    <div>
      <input type="checkbox" data-target="<?= $id ?>">
      <?= ucfirst(str_replace('-', ' ', $id)) ?>
    </div>
  <?php endforeach; ?>
  <hr>
  <button type="button" id="reset-filters" class="btn btn-sm btn-warning">Ripristina tutto</button>
</div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
const dashboard = document.getElementById('dashboard-cards');
const sidePanel = document.getElementById('side-panel');
const toggleBtn = document.getElementById('side-toggle-btn');
const originalOrder = Array.from(dashboard.querySelectorAll('.card-wrapper')).map(el => el.dataset.id);
const cardDefaults = <?= json_encode($cards) ?>;

function saveState() {
  const state = { order: [], visibility: {} };
  dashboard.querySelectorAll('.card-wrapper').forEach(el => {
    state.order.push(el.dataset.id);
    state.visibility[el.dataset.id] = !el.classList.contains('d-none');
  });
  localStorage.setItem('dashboardState', JSON.stringify(state));

  /*
  // SQL FUTURO
  fetch('ajax/dashboard_save.php', {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify(state)
  });
  */
}

function loadState() {
  const saved = localStorage.getItem('dashboardState');
  if(saved){
    const state = JSON.parse(saved);
    state.order.forEach(id=>{
      const el = dashboard.querySelector(`[data-id="${id}"]`);
      if(el) dashboard.appendChild(el);
    });
    for(const id in state.visibility){
      const el = dashboard.querySelector(`[data-id="${id}"]`);
      if(el) el.classList.toggle('d-none', !state.visibility[id]);
      const cb = sidePanel.querySelector(`input[data-target="${id}"]`);
      if(cb) cb.checked = state.visibility[id];
    }
  } else {
    // Applica defaultVisible
    dashboard.querySelectorAll('.card-wrapper').forEach(card=>{
      const id = card.dataset.id;
      const visible = cardDefaults[id].defaultVisible;
      card.classList.toggle('d-none', !visible);
      const cb = sidePanel.querySelector(`input[data-target="${id}"]`);
      if(cb) cb.checked = visible;
    });
  }
  dashboard.classList.remove('invisible');

  // Pannello laterale sempre chiuso
  sidePanel.classList.remove('open');
  document.body.classList.remove('panel-open');
}

// Toggle pannello
toggleBtn.onclick = ()=>{
  const isOpen = sidePanel.classList.toggle('open');
  document.body.classList.toggle('panel-open', isOpen);
  saveState();
};

// Checkbox visibilità schede
document.querySelectorAll('#side-panel input[type=checkbox]').forEach(cb=>{
  cb.onchange=()=>{
    const el = dashboard.querySelector(`[data-id="${cb.dataset.target}"]`);
    if(el) el.classList.toggle('d-none', !cb.checked);
    saveState();
  };
});

// Drag & Drop -- disattivo SOLO su smartphone
if (window.innerWidth >= 768) {
  new Sortable(dashboard, {
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd: saveState
  });
}



// Ripristina tutto al default
document.getElementById('reset-filters').addEventListener('click', () => {
  dashboard.querySelectorAll('.card-wrapper').forEach(card=>{
    const id = card.dataset.id;
    const visible = cardDefaults[id].defaultVisible;
    card.classList.toggle('d-none', !visible);
    const cb = sidePanel.querySelector(`input[data-target="${id}"]`);
    if(cb) cb.checked = visible;
  });
  originalOrder.forEach(id=>{
    const el = dashboard.querySelector(`[data-id="${id}"]`);
    if(el) dashboard.appendChild(el);
  });
  saveState();
  alert('Dashboard ripristinata allo stato originale!');
});

window.addEventListener('DOMContentLoaded', loadState);
</script>
