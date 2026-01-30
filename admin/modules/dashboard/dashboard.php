<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
?>

<style>
/* Drag & drop */
.sortable-ghost { 
  opacity: .6; 
  border: 2px dashed #007bff; 
}
.card, .small-box { 
  cursor: move; 
}

/* Bottone toggle pannello - desktop */
#side-toggle-btn {
  position: fixed;
  top: 76px;       /* subito sotto la navbar */
  right: 35px;     /* più vicino al bordo destro su desktop */
  z-index: 1100;  
  padding: 0.25rem 0.5rem;
  font-size: 0.85rem;
  cursor: pointer;
}

/* Pannello laterale */
#side-panel {
  position: fixed;
  top: 110px;                     /* sotto la navbar desktop */
  right: -300px;                  /* nascosto inizialmente */
  width: 280px;
  height: calc(100% - 70px);      /* altezza pannello adattata */
  background: #f8f9fa;
  border-left: 1px solid #ccc;
  padding: 1rem;
  transition: right .3s;
  z-index: 1050;                  /* sotto il toggle ma sopra il contenuto */
  overflow-y: auto;
}
#side-panel.open { 
  right: 0; 
}

/* Sposta contenuto quando pannello aperto */
body.panel-open section.content { 
  margin-right: 280px !important; 
}

/* Full width e top forzati per le cards */
.force-full {
  flex: 0 0 100% !important;
  max-width: 100% !important;
}
.force-top {
  order: -1;
}

/* Pulsante toggle interno alle cards */
.toggle-layout-btn {
  font-size: .75rem;
  padding: 2px 6px;
}

/* Media query mobile/tablet */
@media (max-width: 767px) {
  .card, .small-box { cursor: default !important; }  /* disabilita drag & drop */

  #side-toggle-btn {
    top: calc(56px + 10px);   /* sotto navbar con piccolo margine */
    right: 15px;               /* distanza dal bordo dello schermo più confortevole */
  }

  .content-wrapper { 
    padding-top: 66px;       /* spazio extra sotto navbar su mobile */
  }
}
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
$statoSezioniDisponibile   = true; // oppure false/null se non c'è dato
$aggiornamentoDisponibile  = true; // oppure false/null se non c'è dato
$ultimaAffluenza           = true; // oppure false/null se non c'è dato
$sezioniScrutinate         = true; // oppure false/null se non c'è dato
$schedeScrutinate          = true; // oppure false/null se non c'è dato
$affluenzeOrario           = true; // oppure false/null se non c'è dato
$schedeCandidati           = true; // oppure false/null se non c'è dato
$listeDisponibili          = true; // oppure false/null se non c'è dato
$referendumDisponibile     = true; // oppure false/null se non c'è dato
$cards = [

  ...( !empty($statoSezioniDisponibile) ? [

    'Stato-Sezioni' => [
        'file'=>'cards/box_situazione_sezioni.php',
        'col'=>'col-md-3',
        'roles'=>['superuser','admin','operatore'],
        'defaultVisible'=>true
    ]

] : []),


  ...( !empty($aggiornamentoDisponibile) ? [

    'Scheda-Aggionamento' => [
        'file'=>'cards/scheda_dato_aggiornato.php',
        'col'=>'col-lg-3 col-6',
        'roles'=>['superuser','admin'],
        'defaultVisible'=>true
    ]

] : []),

  ...( !empty($ultimaAffluenza) ? [

    'Scheda-Ultima-Affluenza' => [
        'file'=>'cards/scheda_affluenza_rilevata.php',
        'col'=>'col-lg-3 col-6',
        'roles'=>['superuser','admin'],
        'defaultVisible'=>true
    ]

] : []),

  ...( !empty($sezioniScrutinate) ? [

    'Scheda-Sezioni-Scrutinate' => [
        'file'=>'cards/sezione_scrutinate.php',
        'col'=>'col-lg-3 col-6',
        'roles'=>['superuser','admin','operatore'],
        'defaultVisible'=>true
    ]

] : []),

  ...( !empty($schedeScrutinate) ? [

    'Scheda-Schede-Scrutintate' => [
        'file'=>'cards/scheda_scrutinate.php',
        'col'=>'col-lg-3 col-6',
        'roles'=>['superuser'],
        'defaultVisible'=>true
    ]

] : []),

  
  'Profilo-Utente' => [
    'file'=>'cards/scheda_ruolo.php',
    'col'=>'col-md-3',
    'roles'=>['superuser','admin','operatore'],
    'defaultVisible'=>true
  ],

  'Scheda-Informazione-Comune' => [
    'file'=>'cards/scheda_comune.php',
    'col'=>'col-md-3',
    'roles'=>['superuser','admin','operatore'],
    'defaultVisible'=>true
  ],

  ...( !empty($affluenzeOrario) ? [

    'Scheda-Affluenze' => [
        'file'=>'cards/box_affluenze_orario.php',
        'col'=>'col-md-3',
        'roles'=>['superuser','admin','operatore'],
        'defaultVisible'=>true
    ]

] : []),


  ...( in_array($tipo_consultazione, [
        'comunali',
        'ballottaggio comunali',
        'regionali',
        'camera',
        'senato'
    ]) && !empty($schedeCandidati) ? [

        'scheda-Candidati' => [
            'file'=>'cards/scheda_gruppi.php',
            'col'=>'col-md-3',
            'roles'=>['superuser','admin'],
            'defaultVisible'=>true
        ]

    ] : []),


 ...( in_array($tipo_consultazione, [
        'europee',
        'comunali',
        'ballottaggio comunali',
        'regionali',
        'camera',
        'senato'
    ]) && !empty($listeDisponibili) ? [

        'scheda-Liste' => [
            'file'=>'cards/scheda_liste.php',
            'col'=>'col-md-3',
            'roles'=>['superuser','admin'],
            'defaultVisible'=>true
        ]

    ] : []),

    ...( $tipo_consultazione === 'referendum' && !empty($referendumDisponibile) ? [

    'Scheda-Referendum' => [
        'file'=>'cards/scheda_referendum.php',
        'col'=>'col-md-3',
        'roles'=>['superuser','admin'],
        'defaultVisible'=>true
    ]

] : []),


  'box-demo' => [
    'file'=>'cards/box_demo.php',
    'col'=>'col-lg-3 col-6',
    'roles'=>['superuser','admin'],
    'defaultVisible'=>false
  ],
  'scheda-demo' => [
    'file'=>'cards/scheda_demo.php',
    'col'=>'col-md-6',
    'roles'=>['superuser','admin'],
    'defaultVisible'=>false
  ],

  'box-grafico' => [
    'file'=>'cards/box_grafico.php',
    'col'=>'col-md-6',
    'roles'=>['superuser','admin','operatore'],
    'defaultVisible'=>false
  ],

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
      <?= ucwords(str_replace(['-','_'], ' ', $id)) ?>
    </div>
  <?php endforeach; ?>
  <hr>
  <button type="button" id="reset-filters" class="btn btn-sm btn-warning">Ripristina tutto</button>
</div>
</section>


<script>
const dashboard = document.getElementById('dashboard-cards');
const sidePanel = document.getElementById('side-panel');
const toggleBtn = document.getElementById('side-toggle-btn');
const originalOrder = Array.from(dashboard.querySelectorAll('.card-wrapper')).map(el => el.dataset.id);
const cardDefaults = <?= json_encode($cards) ?>;

// Salva stato completo: ordine + visibilità + full Stato-Sezioni
function saveState() {
  const state = { order: [], visibility: {}, boxSezioniFull: 0 };

  dashboard.querySelectorAll('.card-wrapper').forEach(el => {
    state.order.push(el.dataset.id);
    state.visibility[el.dataset.id] = !el.classList.contains('d-none');
    if(el.dataset.id === 'Stato-Sezioni' && el.classList.contains('force-full')){
      state.boxSezioniFull = 1;
    }
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

// Toggle pannello laterale
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


// Drag & Drop
if (window.innerWidth >= 768) {
  new Sortable(dashboard, {
    animation: 150,
    ghostClass: 'sortable-ghost',

    onStart: (evt) => {
      const el = evt.item;
      if (el.dataset.id === 'Stato-Sezioni') {
        // togli force-top ma mantieni force-full visivamente tramite ghost
        el.classList.remove('force-top'); 
      }
    },

    onEnd: (evt) => {
  saveState(); // salva ordine e visibilità

  const wrapper = dashboard.querySelector('[data-id="Stato-Sezioni"]');
  const saved = JSON.parse(localStorage.getItem('dashboardState') || '{}');

  if (wrapper && saved.boxSezioniFull == 1) {
    // Rimani full width ma NON spostare in cima
    wrapper.classList.add('force-full'); 
    // Non aggiungere più force-top se vuoi lasciare libero lo spostamento
    wrapper.classList.remove('force-top'); 
  }
}
  });
}



// Toggle full Stato-Sezioni (NON sposta la card)
function toggleSezioniLayout() {
  const wrapper = dashboard.querySelector('[data-id="Stato-Sezioni"]');
  if(!wrapper) return;

  const btnIcon = document.querySelector('#box-sezioni-card .toggle-layout-btn i');
  const isFull = wrapper.classList.toggle('force-full');

  // Rimuovo forzatura top: la posizione rimane dove si trova
  wrapper.classList.remove('force-top');

  // Aggiorna icona
  if(btnIcon){
    btnIcon.classList.toggle('fa-expand', !isFull);
    btnIcon.classList.toggle('fa-compress', isFull);
  }

  saveState();
}

// Ripristino dashboard al caricamento rimane invariato
function loadState() {
  const saved = JSON.parse(localStorage.getItem('dashboardState') || '{}');

  // Ripristina ordine
  if(saved.order){
    saved.order.forEach(id=>{
      const el = dashboard.querySelector(`[data-id="${id}"]`);
      if(el) dashboard.appendChild(el);
    });
  }

  // Ripristina visibilità
  if(saved.visibility){
    for(const id in saved.visibility){
      const el = dashboard.querySelector(`[data-id="${id}"]`);
      if(el) el.classList.toggle('d-none', !saved.visibility[id]);
      const cb = sidePanel.querySelector(`input[data-target="${id}"]`);
      if(cb) cb.checked = saved.visibility[id];
    }
  } else {
    dashboard.querySelectorAll('.card-wrapper').forEach(card=>{
      const id = card.dataset.id;
      const visible = cardDefaults[id].defaultVisible;
      card.classList.toggle('d-none', !visible);
      const cb = sidePanel.querySelector(`input[data-target="${id}"]`);
      if(cb) cb.checked = visible;
    });
  }

  // Ripristina full + top solo al caricamento o reset
  // Ripristina full senza spostare
const wrapper = dashboard.querySelector('[data-id="Stato-Sezioni"]');
const btnIcon = document.querySelector('#box-sezioni-card .toggle-layout-btn i');
if(wrapper && saved.boxSezioniFull == 1){
    wrapper.classList.add('force-full'); // solo full width, non spostare
    wrapper.classList.remove('force-top'); // rimuovo forzatura top
    if(btnIcon){
        btnIcon.classList.remove('fa-expand');
        btnIcon.classList.add('fa-compress');
    }
} else if(wrapper){
    wrapper.classList.remove('force-full','force-top');
    if(btnIcon){
        btnIcon.classList.remove('fa-compress');
        btnIcon.classList.add('fa-expand');
    }
}


  dashboard.classList.remove('invisible');
  sidePanel.classList.remove('open');
  document.body.classList.remove('panel-open');
}

// Reset dashboard
document.getElementById('reset-filters').addEventListener('click', () => {
  dashboard.querySelectorAll('.card-wrapper').forEach(card=>{
    const id = card.dataset.id;
    const visible = cardDefaults[id].defaultVisible;
    card.classList.toggle('d-none', !visible);
    const cb = sidePanel.querySelector(`input[data-target="${id}"]`);
    if(cb) cb.checked = visible;

    if(id === 'Stato-Sezioni' && visible){
      card.classList.add('force-full','force-top');
      const btnIcon = document.querySelector('#box-sezioni-card .toggle-layout-btn i');
      if(btnIcon){
        btnIcon.classList.remove('fa-expand');
        btnIcon.classList.add('fa-compress');
      }
    } else if(id === 'Stato-Sezioni'){
      card.classList.remove('force-full','force-top');
      const btnIcon = document.querySelector('#box-sezioni-card .toggle-layout-btn i');
      if(btnIcon){
        btnIcon.classList.remove('fa-compress');
        btnIcon.classList.add('fa-expand');
      }
    }
  });

  originalOrder.forEach(id=>{
    const el = dashboard.querySelector(`[data-id="${id}"]`);
    if(el) dashboard.appendChild(el);
  });

  saveState();
  sidePanel.classList.remove('open');
  document.body.classList.remove('panel-open');
  alert('Dashboard ripristinata allo stato originale!');
});

// Inizializza
window.addEventListener('DOMContentLoaded', loadState);
</script>


