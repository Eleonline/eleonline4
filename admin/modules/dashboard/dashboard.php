<?php
require_once '../includes/check_access.php';
?>

<style>
.sortable-ghost { opacity: .6; border: 2px dashed #007bff; }
.card, .small-box { cursor: move; }
#side-panel { position: fixed; top: 70px; right: -300px; width: 280px; height: calc(100% - 70px); background: #f8f9fa; border-left: 1px solid #ccc; padding: 1rem; transition: right .3s; z-index: 999; overflow-y: auto; }
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
  'scheda-comune'  => ['file'=>'cards/scheda_comune.php','col'=>'col-md-6','roles'=>['superuser','admin','operatore']],
  'scheda-ruolo'   => ['file'=>'cards/scheda_ruolo.php','col'=>'col-md-6','roles'=>['superuser','admin','operatore']],
  'box-abitanti'   => ['file'=>'cards/box_abitanti.php','col'=>'col-lg-3 col-6','roles'=>['superuser','admin']],
  'box-elettori'   => ['file'=>'cards/box_elettori.php','col'=>'col-lg-3 col-6','roles'=>['superuser','admin']],
  'box-sezioni'    => ['file'=>'cards/box_sezioni.php','col'=>'col-lg-3 col-6','roles'=>['superuser','admin','operatore']],
  'box-superficie' => ['file'=>'cards/box_superficie.php','col'=>'col-lg-3 col-6','roles'=>['superuser']],
  'box-demo'       => ['file'=>'cards/box_demo.php','col'=>'col-lg-3 col-6','roles'=>['superuser','admin']],
  'scheda-demo'    => ['file'=>'cards/scheda_demo.php','col'=>'col-md-6','roles'=>['superuser','admin']],
  'box-grafico'    => ['file'=>'cards/box_grafico.php','col'=>'col-md-6','roles'=>['superuser','admin','operatore']],
  'box-affluenze-orario'    => ['file'=>'cards/box_affluenze_orario.php','col'=>'col-md-6','roles'=>['superuser','admin','operatore']],
];

?>

<div class="row" id="dashboard-cards">
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
      <input type="checkbox" checked data-target="<?= $id ?>">
      <?= ucfirst(str_replace('-', ' ', $id)) ?>
    </div>
  <?php endforeach; ?>
</div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
const dashboard = document.getElementById('dashboard-cards');
const sidePanel = document.getElementById('side-panel');
const toggleBtn = document.getElementById('side-toggle-btn');

function saveState() {
  const state = { order: [], visibility: {}, sidePanelOpen: sidePanel.classList.contains('open') };
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
  if (!saved) return;
  const state = JSON.parse(saved);
  state.order.forEach(id=>{
    const el = dashboard.querySelector(`[data-id="${id}"]`);
    if(el) dashboard.appendChild(el);
  });
  for(const id in state.visibility){
    const el = dashboard.querySelector(`[data-id="${id}"]`);
    if(el) el.classList.toggle('d-none', !state.visibility[id]);
  }
  if(state.sidePanelOpen){
    sidePanel.classList.add('open');
    document.body.classList.add('panel-open');
  }
}

toggleBtn.onclick = ()=>{
  sidePanel.classList.toggle('open');
  document.body.classList.toggle('panel-open');
  saveState();
};

document.querySelectorAll('#side-panel input[type=checkbox]').forEach(cb=>{
  cb.onchange=()=>{
    const el = dashboard.querySelector(`[data-id="${cb.dataset.target}"]`);
    if(el) el.classList.toggle('d-none', !cb.checked);
    saveState();
  };
});

new Sortable(dashboard,{animation:150, ghostClass:'sortable-ghost', onEnd: saveState});
window.addEventListener('DOMContentLoaded', loadState);
</script>
