<?php
require_once '../includes/check_access.php';

// Simulazione dati da DB:
$configs = [
  ['id'=>1, 'denominazione'=>'Config A', 'limite_maggioritario'=>'3000', 'sindaco_eletto'=>'1',
   'premio_magg_percent_mm'=>'5.0', 'sbarramento_mm'=>'3.0', 'premio_magg_mm'=>'1000', 'solo_oltre_mm'=>'2.5', 'sottosoglia_gruppo_mm'=>'1',
   'premio_magg_percent_mp'=>'6.0', 'sbarramento_mp'=>'4.0', 'premio_magg_mp'=>'1200', 'solo_oltre_mp'=>'3.5', 'sottosoglia_gruppo_mp'=>'0',
  ],
  ['id'=>2, 'denominazione'=>'Config B', 'limite_maggioritario'=>'10000', 'sindaco_eletto'=>'0',
   'premio_magg_percent_mm'=>'4.0', 'sbarramento_mm'=>'2.0', 'premio_magg_mm'=>'900', 'solo_oltre_mm'=>'1.5', 'sottosoglia_gruppo_mm'=>'0',
   'premio_magg_percent_mp'=>'5.0', 'sbarramento_mp'=>'3.0', 'premio_magg_mp'=>'1100', 'solo_oltre_mp'=>'2.0', 'sottosoglia_gruppo_mp'=>'1',
  ],
];

// Funzione per sicurezza output
function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES); }

// Array limiti usati nel select
$limiti = ['maggioritario', 3000, 10000, 15000, 30000, 100000, 250000, 500000, 1000000, 100000000];

// Campi sistema maggioritario
$campi_magg = [
  'premio_magg_percent_mm' => 'Premio maggioranza %',
  'sbarramento_mm' => 'Sbarramento %',
  'premio_magg_mm' => 'Premio di maggioranza',
  'solo_oltre_mm' => 'Solo oltre %',
  'sottosoglia_gruppo_mm' => 'Sottosoglia conteggiati?'
];

// Campi sistema proporzionale
$campi_prop = [
  'premio_magg_percent_mp' => 'Premio maggioranza %',
  'sbarramento_mp' => 'Sbarramento %',
  'premio_magg_mp' => 'Premio di maggioranza',
  'solo_oltre_mp' => 'Solo oltre %',
  'sottosoglia_gruppo_mp' => 'Sottosoglia conteggiati?'
];

/*
---- PHP: codice per salva/modifica/elimina da mettere in salva_configurazione.php o nello stesso file (con if POST/GET) ----

// Esempio per salvare nuova configurazione (INSERT)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'] ?? null;
  $denominazione = $_POST['denominazione'] ?? '';
  $limite = $_POST['limite_maggioritario'] ?? '';
  $sindaco = $_POST['sindaco_eletto'] ?? '0';

  // Altri campi da prendere da $_POST...

  if ($id) {
    // UPDATE configurazione esistente con id = $id
    $sql = "UPDATE configurazioni SET 
      denominazione = ?, limite_maggioritario = ?, sindaco_eletto = ?,
      premio_magg_percent_mm = ?, sbarramento_mm = ?, premio_magg_mm = ?, solo_oltre_mm = ?, sottosoglia_gruppo_mm = ?,
      premio_magg_percent_mp = ?, sbarramento_mp = ?, premio_magg_mp = ?, solo_oltre_mp = ?, sottosoglia_gruppo_mp = ?
      WHERE id = ?";
    // prepare, bind e execute...
  } else {
    // INSERT nuova configurazione
    $sql = "INSERT INTO configurazioni (denominazione, limite_maggioritario, sindaco_eletto,
      premio_magg_percent_mm, sbarramento_mm, premio_magg_mm, solo_oltre_mm, sottosoglia_gruppo_mm,
      premio_magg_percent_mp, sbarramento_mp, premio_magg_mp, solo_oltre_mp, sottosoglia_gruppo_mp)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    // prepare, bind e execute...
  }

  // Dopo salvataggio, ridireziona o rispondi con JSON per aggiornare lista via JS
}

// Esempio per eliminare configurazione via GET (con id)
if (isset($_GET['elimina_id'])) {
  $id = (int)$_GET['elimina_id'];
  $sql = "DELETE FROM configurazioni WHERE id = ?";
  // prepare, bind e execute...
  // poi ridireziona o rispondi con JSON
}

*/
?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cogs me-2"></i>Gestione Configurazioni</h3>
      </div>
      <div class="card-body">
        <form method="post" action="" id="form-config" lang="it" class="mb-5">

          <input type="hidden" name="id" id="config_id" value="">

          <!-- DATI GENERALI -->
          <div class="mb-4">
            <div class="row mb-3 align-items-center">
              <label for="denominazione" class="col-md-4 col-form-label text-md-end">Denominazione</label>
              <div class="col-md-6">
                <input type="text" name="denominazione" id="denominazione" class="form-control" required>
              </div>
            </div>
            <div class="row mb-3 align-items-center">
              <label for="limite_maggioritario" class="col-md-4 col-form-label text-md-end">Limite sist. maggioritario</label>
              <div class="col-md-6">
                <select name="limite_maggioritario" id="limite_maggioritario" class="form-select" required>
                  <?php foreach ($limiti as $val): ?>
                    <option value="<?= h($val) ?>"><?= h($val) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="row mb-3 align-items-center">
              <label for="sindaco_eletto" class="col-md-4 col-form-label text-md-end">Il sindaco è consigliere?</label>
              <div class="col-md-6">
                <select name="sindaco_eletto" id="sindaco_eletto" class="form-select" required>
                  <option value="1">Sì</option>
                  <option value="0">No</option>
                </select>
              </div>
            </div>
          </div>

          <!-- SISTEMA MAGGIORITARIO -->
          <div class="mb-4 border rounded p-3 bg-light">
            <h5 class="text-center mb-3">Sistema Maggioritario</h5>
            <?php foreach ($campi_magg as $id => $label): ?>
              <div class="row mb-3 align-items-center">
                <label for="<?= h($id) ?>" class="col-md-4 col-form-label text-md-end"><?= h($label) ?></label>
                <div class="col-md-6">
                  <?php if (strpos($id, 'sottosoglia') !== false): ?>
                    <select name="<?= h($id) ?>" id="<?= h($id) ?>" class="form-select" required>
                      <option value="1">Sì</option>
                      <option value="0">No</option>
                    </select>
                  <?php else: ?>
                    <input type="number" step="0.01" inputmode="decimal" name="<?= h($id) ?>" id="<?= h($id) ?>" class="form-control" required>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- SISTEMA PROPORZIONALE -->
          <div class="mb-4 border rounded p-3 bg-light">
            <h5 class="text-center mb-3">Sistema Proporzionale</h5>
            <?php foreach ($campi_prop as $id => $label): ?>
              <div class="row mb-3 align-items-center">
                <label for="<?= h($id) ?>" class="col-md-4 col-form-label text-md-end"><?= h($label) ?></label>
                <div class="col-md-6">
                  <?php if (strpos($id, 'sottosoglia') !== false): ?>
                    <select name="<?= h($id) ?>" id="<?= h($id) ?>" class="form-select" required>
                      <option value="1">Sì</option>
                      <option value="0">No</option>
                    </select>
                  <?php else: ?>
                    <input type="number" step="0.01" inputmode="decimal" name="<?= h($id) ?>" id="<?= h($id) ?>" class="form-control" required>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="mb-3 text-center">
            <button type="submit" class="btn btn-success px-4" id="btn-submit">Salva Configurazione</button>
            <button type="button" class="btn btn-secondary ms-2 d-none" id="btn-annulla" onclick="resetForm()">Annulla</button>
          </div>

        </form>

<!-- LISTA CONFIGURAZIONI -->
<div class="card card-secondary mt-5">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-list me-2"></i>Configurazioni Salvate</h3>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover table-striped mb-0" id="tabella-configurazioni">
      <thead class="bg-light">
        <tr>
          <th style="width: 60px;">#</th>
          <th>Denominazione</th>
          <th style="width: 180px;" class="text-center">Azioni</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($configs as $c): ?>
          <tr data-id="<?= h($c['id']) ?>">
            <td><?= h($c['id']) ?></td>
            <td><?= h($c['denominazione']) ?></td>
            <td class="text-center">
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-warning btn-modifica" data-id="<?= $c['id'] ?>">
                  Modifica
                </button>
                <button type="button" class="btn btn-sm btn-danger btn-elimina" data-id="<?= $c['id'] ?>" data-nome="<?= h($c['denominazione']) ?>">
                  Elimina
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

      <div class="card-footer">
        <!-- Puoi aggiungere note o bottoni footer -->
      </div>
    </div>
  </div>
</section>

<script>
const configs = <?= json_encode($configs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

function riempiForm(id) {
  const conf = configs.find(c => c.id == id);
  if (!conf) return;

  document.getElementById('config_id').value = conf.id || '';
  document.getElementById('denominazione').value = conf.denominazione || '';
  document.getElementById('limite_maggioritario').value = conf.limite_maggioritario || '';
  document.getElementById('sindaco_eletto').value = conf.sindaco_eletto || '0';

  ['premio_magg_percent_mm','sbarramento_mm','premio_magg_mm','solo_oltre_mm','sottosoglia_gruppo_mm'].forEach(id => {
    document.getElementById(id).value = conf[id] ?? '';
  });

  ['premio_magg_percent_mp','sbarramento_mp','premio_magg_mp','solo_oltre_mp','sottosoglia_gruppo_mp'].forEach(id => {
    document.getElementById(id).value = conf[id] ?? '';
  });

  // Cambia testo e colore bottone e mostra Annulla
  const btn = document.getElementById('btn-submit');
  btn.textContent = 'Salva Modifiche';
  btn.classList.remove('btn-success');
  btn.classList.add('btn-warning');
  document.getElementById('btn-annulla').classList.remove('d-none');

  // Scroll fino al titolo della card
  document.querySelector('.card-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetForm() {
  document.getElementById('form-config').reset();
  document.getElementById('config_id').value = '';

  const btn = document.getElementById('btn-submit');
  btn.textContent = 'Salva Configurazione';
  btn.classList.remove('btn-warning');
  btn.classList.add('btn-success');
  document.getElementById('btn-annulla').classList.add('d-none');
}

// Elimina (chiede conferma e poi simula chiamata server)
function eliminaConfig(id, nome) {
  if (!confirm(`Sei sicuro di voler eliminare la configurazione "${nome}"?`)) return;

  // QUI: chiamata ajax o fetch per eliminare via GET o POST
  // per esempio: fetch(`?elimina_id=${id}`)...

  // Simulazione eliminazione locale:
  const idx = configs.findIndex(c => c.id == id);
  if (idx >= 0) configs.splice(idx,1);

  aggiornaLista();

  alert('Configurazione eliminata con successo.');
  resetForm();
}

// Aggiorna la tabella configurazioni dinamicamente
function aggiornaLista() {
  const tbody = document.querySelector('#tabella-configurazioni tbody');
  tbody.innerHTML = '';

  configs.forEach(c => {
    const tr = document.createElement('tr');
    tr.dataset.id = c.id;

    const tdId = document.createElement('td');
    tdId.textContent = c.id;
    tr.appendChild(tdId);

    const tdNome = document.createElement('td');
    tdNome.textContent = c.denominazione;
    tr.appendChild(tdNome);

    const tdAzioni = document.createElement('td');
    tdAzioni.classList.add('text-center');

    const divGroup = document.createElement('div');
    divGroup.classList.add('btn-group');

    const btnModifica = document.createElement('button');
    btnModifica.type = 'button';
    btnModifica.className = 'btn btn-sm btn-warning btn-modifica';
    btnModifica.dataset.id = c.id;
    btnModifica.textContent = 'Modifica';
    btnModifica.addEventListener('click', () => riempiForm(c.id));

    const btnElimina = document.createElement('button');
    btnElimina.type = 'button';
    btnElimina.className = 'btn btn-sm btn-danger btn-elimina';
    btnElimina.dataset.id = c.id;
    btnElimina.dataset.nome = c.denominazione;
    btnElimina.textContent = 'Elimina';
    btnElimina.addEventListener('click', () => eliminaConfig(c.id, c.denominazione));

    divGroup.appendChild(btnModifica);
    divGroup.appendChild(btnElimina);
    tdAzioni.appendChild(divGroup);

    tr.appendChild(tdAzioni);
    tbody.appendChild(tr);
  });
}

// Gestione submit form
document.getElementById('form-config').addEventListener('submit', function(e) {
  e.preventDefault();

  // Recupero dati dal form
  const formData = new FormData(this);
  const id = formData.get('id');
  const denominazione = formData.get('denominazione');

  // Qui chiamata ajax/fetch per salvare/modificare lato server con POST

  if (id) {
    // Modifica simulata in locale
    const idx = configs.findIndex(c => c.id == id);
    if (idx >= 0) {
      configs[idx] = Object.fromEntries(formData.entries());
      configs[idx].id = Number(id);
    }
    alert('Configurazione modificata con successo.');
  } else {
    // Simulazione nuovo id incrementale
    const newId = configs.length ? Math.max(...configs.map(c => c.id)) + 1 : 1;
    let newConfig = Object.fromEntries(formData.entries());
    newConfig.id = newId;
    configs.push(newConfig);
    alert('Configurazione salvata con successo.');
  }

  aggiornaLista();
  resetForm();
});

// Inizializza eventi su pulsanti esistenti (solo in caso di caricamento statico)
document.querySelectorAll('.btn-modifica').forEach(btn => {
  btn.addEventListener('click', () => riempiForm(btn.dataset.id));
});
document.querySelectorAll('.btn-elimina').forEach(btn => {
  btn.addEventListener('click', () => eliminaConfig(btn.dataset.id, btn.dataset.nome));
});
</script>
