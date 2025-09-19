<?php
/***************************
 * gestione_autorizzazioni.php
 ***************************/
require_once '../includes/check_access.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Parametro tipo consultazione (da definire o passare via GET/POST, per esempio)

$colonne_per_tipo = [
    'comunali' => [
        'consultazione' => true, 'comune' => true, 'preferenze' => true, 'stato' => true,
        'legge' => true, 'abitanti' => true, 'proiezione' => true, 'consiglio' => true,
        'affluenze' => true, 'solo_gruppi' => true, 'scheda_unica' => true,
    ],
    'regionali' => [
        'consultazione' => true, 'comune' => true, 'preferenze' => true, 'stato' => true,
        'legge' => false, 'abitanti' => false, 'proiezione' => false, 'consiglio' => false,
        'affluenze' => true, 'solo_gruppi' => true, 'scheda_unica' => true,
    ],
    'camera' => [
        'consultazione' => true, 'comune' => true, 'preferenze' => true, 'stato' => true,
        'legge' => false, 'abitanti' => false, 'proiezione' => false, 'consiglio' => false,
        'affluenze' => true, 'solo_gruppi' => true, 'scheda_unica' => true,
    ],
    'senato' => [
        'consultazione' => true, 'comune' => true, 'preferenze' => true, 'stato' => true,
        'legge' => false, 'abitanti' => false, 'proiezione' => false, 'consiglio' => false,
        'affluenze' => true, 'solo_gruppi' => true, 'scheda_unica' => true,
    ],
    'europee' => [
        'consultazione' => true, 'comune' => true, 'preferenze' => true, 'stato' => true,
        'legge' => false, 'abitanti' => false, 'proiezione' => false, 'consiglio' => false,
        'affluenze' => true, 'solo_gruppi' => false, 'scheda_unica' => false,
    ],
    'referendum' => [
        'consultazione' => true, 'comune' => true, 'preferenze' => true, 'stato' => true,
        'legge' => false, 'abitanti' => false, 'proiezione' => false, 'consiglio' => false,
        'affluenze' => true, 'solo_gruppi' => false, 'scheda_unica' => false,
    ],
];

$colonne_attive = $colonne_per_tipo[$tipo_consultazione] ?? [];

/* ---------- SEZIONE MYSQL (commentata) ----------
   // 1. Connessione
   $conn = new mysqli('host', 'user', 'pass', 'database');
   if ($conn->connect_error) {
     die('Connessione fallita: ' . $conn->connect_error);
   }

   // 2. Lettura autorizzazioni dal DB
   $sql = "SELECT * FROM autorizzazioni ORDER BY comune";
   $res = $conn->query($sql);
   $autorizza_autorizzazioni = [];
   if ($res && $res->num_rows) {
     while ($r = $res->fetch_assoc()) {
       $r['proiezione']   = (bool)$r['proiezione'];
       $r['affluenze']    = (bool)$r['affluenze'];
       $r['solo_gruppi']  = (bool)$r['solo_gruppi'];
       $r['scheda_unica'] = (bool)$r['scheda_unica'];
       $autorizza_autorizzazioni[]  = $r;
     }
   }
   $conn->close();
 --------------------------------------------------*/

/* ---------- DATI DI ESEMPIO / SESSIONE ---------- */
$autorizza_consultazione = 'Elezioni Comunali 2025';
if (!isset($_SESSION['autorizza_autorizzazioni']) || empty($_SESSION['autorizza_autorizzazioni'])) {
  $_SESSION['autorizza_autorizzazioni'] = [
    ['consultazione'=>$autorizza_consultazione,'comune'=>'003','preferenze'=>6,'stato'=>'Chiusa','legge'=>'proporzionale','abitanti'=>'10001-15000','proiezione'=>false,'affluenze'=>true,'solo_gruppi'=>false,'scheda_unica'=>true],
    ['consultazione'=>$autorizza_consultazione,'comune'=>'001','preferenze'=>10,'stato'=>'Attiva','legge'=>'maggioritario','abitanti'=>'3000-10000','proiezione'=>true,'affluenze'=>false,'solo_gruppi'=>true,'scheda_unica'=>false],
    ['consultazione'=>$autorizza_consultazione,'comune'=>'002','preferenze'=>5,'stato'=>'Chiusa','legge'=>'proporzionale','abitanti'=>'10001-15000','proiezione'=>false,'affluenze'=>true,'solo_gruppi'=>false,'scheda_unica'=>true],
	['consultazione'=>$autorizza_consultazione,'comune'=>'002','preferenze'=>5,'stato'=>'Chiusa','legge'=>'proporzionale','abitanti'=>'10001-15000','proiezione'=>false,'affluenze'=>true,'solo_gruppi'=>false,'scheda_unica'=>true],
  ];
}
$autorizza_autorizzazioni = $_SESSION['autorizza_autorizzazioni'];
/* ------------------------------------------------ */
?>
<section class="content">
  <div class="container-fluid mt-4">
    <!-- FORM ---------------------------------------------------------------->
<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <h3 class="card-title" id="form-title"><i class="fas fa-cogs me-2"></i>Autorizzazione Comune</h3>
  </div>
  <div class="card-body">
    <form id="formAutorizzazione">
      <input type="hidden" name="autorizza_idx" id="autorizza_idx"> <!-- indice array per modifica -->
      <div class="d-flex flex-wrap align-items-end gap-3">

        <!-- Campo testo readonly -->
<div class="form-group col-auto d-flex flex-column" style="min-width: 150px; max-width: 400px;">
  <label>Consultazione</label>
  <div class="form-control-plaintext" style="padding-left: 0; padding-right: 0;">
    <input type="text" readonly class="form-control-plaintext" id="autorizza_consultazione" name="autorizza_consultazione"
                     value="<?= htmlspecialchars($autorizza_consultazione) ?>">
  </div>
</div>



        <!-- Comune -->
        <?php if (!empty($colonne_attive['comune'])): ?>
          <div class="form-group col-auto" style="min-width: 150px;">
            <label>Comune*</label>
            <select class="form-control form-control-sm" id="autorizza_comune" name="autorizza_comune" required>
              <option value="">Seleziona</option>
              <option value="001">Comune 1</option>
              <option value="002">Comune 2</option>
              <option value="003">Comune 3</option>
            </select>
          </div>
        <?php endif; ?>

        <!-- Preferenze -->
        <?php if (!empty($colonne_attive['preferenze'])): ?>
        <div class="form-group col-auto" style="min-width: 70px; max-width: 80px;">
  <label>Pref.</label>
  <input type="number" step="1" class="form-control form-control-sm" id="autorizza_preferenze" name="autorizza_preferenze" >
</div>

        <?php endif; ?>

        <!-- Stato -->
        <?php if (!empty($colonne_attive['stato'])): ?>
          <div class="form-group col-auto" style="min-width: 120px;">
            <label>Stato</label>
            <select class="form-control form-control-sm" id="autorizza_stato" name="autorizza_stato">
              <option>Attiva</option>
              <option>Chiusa</option>
              <option>Nulla</option>
            </select>
          </div>
        <?php endif; ?>

        <!-- Legge -->
        <?php if (!empty($colonne_attive['legge'])): ?>
          <div class="form-group col-auto flex-grow-1" style="min-width: 180px;">
            <label>Legge</label>
            <select class="form-control form-control-sm" id="autorizza_legge" name="autorizza_legge">
              <option value="">Seleziona</option>
              <option value="maggioritario">Maggioritario</option>
              <option value="proporzionale">Proporzionale</option>
              <option value="altro">Altro</option>
            </select>
          </div>
        <?php endif; ?>

        <!-- Abitanti -->
        <?php if (!empty($colonne_attive['abitanti'])): ?>
          <div class="form-group col-auto" style="min-width: 150px;">
            <label>Abitanti</label>
            <select class="form-control form-control-sm" id="autorizza_abitanti" name="autorizza_abitanti">
              <option value="">Seleziona</option>
              <option value="3000-10000">3.000‑10.000</option>
              <option value="10001-15000">10.001‑15.000</option>
              <option value="15001-30000">15.001‑30.000</option>
              <option value="30001-50000">30.001‑50.000</option>
              <option value=">50000">&gt;50.000</option>
            </select>
          </div>
        <?php endif; ?>

        <!-- Checkbox -->
        <?php
          $checkboxes = ['proiezione', 'affluenze', 'solo_gruppi', 'scheda_unica'];
          $checkbox_labels = [
            'proiezione' => 'Proiezione',
            'affluenze' => 'Affluenze',
            'solo_gruppi' => 'Solo gruppi',
            'scheda_unica' => 'Scheda unica'
          ];
        ?>
        <?php foreach ($checkboxes as $cb): ?>
          <?php if (!empty($colonne_attive[$cb])): ?>
            <div class="form-group col-auto d-flex align-items-center" style="min-width: 120px;">
              <div class="form-check mt-4">
                <input type="checkbox" class="form-check-input" id="autorizza_<?= $cb ?>" name="autorizza_<?= $cb ?>">
                <label class="form-check-label" for="autorizza_<?= $cb ?>"><?= $checkbox_labels[$cb] ?></label>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>

      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-success" id="btn-save">Aggiungi</button>
        <button type="button" class="btn btn-secondary" id="btn-cancel">Annulla</button>
      </div>
    </form>
  </div>
</div>

<style>
  /* Per i campi testo readonly: adattare la larghezza */
  .form-control-plaintext {
    display: inline-block;
    width: auto !important;
    padding-left: 0;
    padding-right: 0;
  }
</style>



    <!-- TABELLA ELENCO ------------------------------------------------------>
    <div class="card shadow-sm mt-4">
      <div class="card-header bg-info text-white">
        <h3 class="card-title"><i class="fas fa-table me-2"></i>Elenco autorizzazioni</h3>
      </div>
      <div class="card-body table-responsive p-0" style="max-height: 400px;">
        <table class="table table-striped table-hover table-sm align-middle">
          <thead class="table-primary sticky-top">
            <tr>
              <?php if (!empty($colonne_attive['consultazione'])): ?><th>Consultazione</th><?php endif; ?>
              <?php if (!empty($colonne_attive['comune'])): ?><th>Comune</th><?php endif; ?>
              <?php if (!empty($colonne_attive['preferenze'])): ?><th>Pref.</th><?php endif; ?>
              <?php if (!empty($colonne_attive['stato'])): ?><th>Stato</th><?php endif; ?>
              <?php if (!empty($colonne_attive['legge'])): ?><th>Legge</th><?php endif; ?>
              <?php if (!empty($colonne_attive['abitanti'])): ?><th>Abitanti</th><?php endif; ?>
              <?php if (!empty($colonne_attive['proiezione'])): ?><th>Proiez.</th><?php endif; ?>
              <?php if (!empty($colonne_attive['affluenze'])): ?><th>Affl.</th><?php endif; ?>
              <?php if (!empty($colonne_attive['solo_gruppi'])): ?><th>Solo gruppi</th><?php endif; ?>
              <?php if (!empty($colonne_attive['scheda_unica'])): ?><th>Scheda unica</th><?php endif; ?>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody id="tbodyAutorizzazioni">
            <?php foreach ($autorizza_autorizzazioni as $idx => $row): ?>
              <tr data-idx="<?= $idx ?>">
                <?php if (!empty($colonne_attive['consultazione'])): ?><td><?= htmlspecialchars($row['consultazione']) ?></td><?php endif; ?>
                <?php if (!empty($colonne_attive['comune'])): ?><td><?= htmlspecialchars($row['comune']) ?></td><?php endif; ?>
                <?php if (!empty($colonne_attive['preferenze'])): ?><td><?= (int)$row['preferenze'] ?></td><?php endif; ?>
                <?php if (!empty($colonne_attive['stato'])): ?><td><?= htmlspecialchars($row['stato']) ?></td><?php endif; ?>
                <?php if (!empty($colonne_attive['legge'])): ?><td><?= htmlspecialchars($row['legge']) ?></td><?php endif; ?>
                <?php if (!empty($colonne_attive['abitanti'])): ?><td><?= htmlspecialchars($row['abitanti']) ?></td><?php endif; ?>
                <?php if (!empty($colonne_attive['proiezione'])): ?><td><input type="checkbox" disabled <?= !empty($row['proiezione']) ? 'checked' : '' ?>></td><?php endif; ?>
                <?php if (!empty($colonne_attive['affluenze'])): ?><td><input type="checkbox" disabled <?= !empty($row['affluenze']) ? 'checked' : '' ?>></td><?php endif; ?>
                <?php if (!empty($colonne_attive['solo_gruppi'])): ?><td><input type="checkbox" disabled <?= !empty($row['solo_gruppi']) ? 'checked' : '' ?>></td><?php endif; ?>
                <?php if (!empty($colonne_attive['scheda_unica'])): ?><td><input type="checkbox" disabled <?= !empty($row['scheda_unica']) ? 'checked' : '' ?>></td><?php endif; ?>
                <td>
                  <button class="btn btn-sm btn-primary btn-edit" title="Modifica"><i class="fas fa-edit"></i></button>
                  <button class="btn btn-sm btn-danger btn-delete" title="Elimina"><i class="fas fa-trash-alt"></i></button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formAutorizzazione');
    const tbody = document.getElementById('tbodyAutorizzazioni');
    const btnCancel = document.getElementById('btn-cancel');
    const formTitle = document.getElementById('form-title');

    // Funzione per leggere dati dal form
    function getFormData() {
      return {
        consultazione: document.getElementById('autorizza_consultazione').value.trim(),
        comune: document.getElementById('autorizza_comune')?.value || '',
        preferenze: parseInt(document.getElementById('autorizza_preferenze')?.value) || 0,
        stato: document.getElementById('autorizza_stato')?.value || '',
        legge: document.getElementById('autorizza_legge')?.value || '',
        abitanti: document.getElementById('autorizza_abitanti')?.value || '',
        proiezione: document.getElementById('autorizza_proiezione')?.checked || false,
        affluenze: document.getElementById('autorizza_affluenze')?.checked || false,
        solo_gruppi: document.getElementById('autorizza_solo_gruppi')?.checked || false,
        scheda_unica: document.getElementById('autorizza_scheda_unica')?.checked || false,
      };
    }

    // Funzione per pulire form
   function resetForm() {
  form.reset();
  document.getElementById('autorizza_idx').value = '';
  formTitle.textContent = 'Nuova autorizzazione';
  form.querySelector('button[type="submit"]').textContent = 'Aggiungi';
}


    // Render tabella (ricostruisce tbody da sessione JS)
    function renderTable(autorizza_autorizzazioni) {
      tbody.innerHTML = '';
      autorizza_autorizzazioni.forEach((row, idx) => {
        const tr = document.createElement('tr');
        tr.dataset.idx = idx;

        <?php if (!empty($colonne_attive['consultazione'])): ?>
        let tdCons = document.createElement('td');
        tdCons.textContent = row.consultazione;
        tr.appendChild(tdCons);
        <?php endif; ?>
        <?php if (!empty($colonne_attive['comune'])): ?>
        let tdComune = document.createElement('td');
        tdComune.textContent = row.comune;
        tr.appendChild(tdComune);
        <?php endif; ?>
        <?php if (!empty($colonne_attive['preferenze'])): ?>
        let tdPref = document.createElement('td');
        tdPref.textContent = row.preferenze;
        tr.appendChild(tdPref);
        <?php endif; ?>
        <?php if (!empty($colonne_attive['stato'])): ?>
        let tdStato = document.createElement('td');
        tdStato.textContent = row.stato;
        tr.appendChild(tdStato);
        <?php endif; ?>
        <?php if (!empty($colonne_attive['legge'])): ?>
        let tdLegge = document.createElement('td');
        tdLegge.textContent = row.legge;
        tr.appendChild(tdLegge);
        <?php endif; ?>
        <?php if (!empty($colonne_attive['abitanti'])): ?>
        let tdAbitanti = document.createElement('td');
        tdAbitanti.textContent = row.abitanti;
        tr.appendChild(tdAbitanti);
        <?php endif; ?>
        <?php if (!empty($colonne_attive['proiezione'])): ?>
        let tdProiezione = document.createElement('td');
        let chkProiezione = document.createElement('input');
        chkProiezione.type = 'checkbox';
        chkProiezione.disabled = true;
        if (row.proiezione) chkProiezione.checked = true;
        tdProiezione.appendChild(chkProiezione);
        tr.appendChild(tdProiezione);
        <?php endif; ?>
        <?php if (!empty($colonne_attive['affluenze'])): ?>
        let tdAffluenze = document.createElement('td');
        let chkAffluenze = document.createElement('input');
        chkAffluenze.type = 'checkbox';
        chkAffluenze.disabled = true;
        if (row.affluenze) chkAffluenze.checked = true;
        tdAffluenze.appendChild(chkAffluenze);
        tr.appendChild(tdAffluenze);
        <?php endif; ?>
        <?php if (!empty($colonne_attive['solo_gruppi'])): ?>
        let tdSoloGruppi = document.createElement('td');
        let chkSoloGruppi = document.createElement('input');
        chkSoloGruppi.type = 'checkbox';
        chkSoloGruppi.disabled = true;
        if (row.solo_gruppi) chkSoloGruppi.checked = true;
        tdSoloGruppi.appendChild(chkSoloGruppi);
        tr.appendChild(tdSoloGruppi);
        <?php endif; ?>
        <?php if (!empty($colonne_attive['scheda_unica'])): ?>
        let tdSchedaUnica = document.createElement('td');
        let chkSchedaUnica = document.createElement('input');
        chkSchedaUnica.type = 'checkbox';
        chkSchedaUnica.disabled = true;
        if (row.scheda_unica) chkSchedaUnica.checked = true;
        tdSchedaUnica.appendChild(chkSchedaUnica);
        tr.appendChild(tdSchedaUnica);
        <?php endif; ?>

        // Colonna azioni
        let tdAzioni = document.createElement('td');
        let btnEdit = document.createElement('button');
        btnEdit.className = 'btn btn-sm btn-primary btn-edit me-1';
        btnEdit.title = 'Modifica';
        btnEdit.innerHTML = '<i class="fas fa-edit"></i>Modifca';
        btnEdit.addEventListener('click', () => editRow(idx));
        tdAzioni.appendChild(btnEdit);

        let btnDelete = document.createElement('button');
        btnDelete.className = 'btn btn-sm btn-danger btn-delete';
        btnDelete.title = 'Elimina';
        btnDelete.innerHTML = '<i class="fas fa-trash-alt"></i>Elimina';
        btnDelete.addEventListener('click', () => deleteRow(idx));
        tdAzioni.appendChild(btnDelete);

        tr.appendChild(tdAzioni);

        tbody.appendChild(tr);
      });
    }

    // Carica i dati dalla sessione PHP nel JS
    let autorizza_autorizzazioni = <?= json_encode($autorizza_autorizzazioni) ?>;

    // Render iniziale
    renderTable(autorizza_autorizzazioni);

    // Modifica riga
  function editRow(idx) {
  const row = autorizza_autorizzazioni[idx];
  document.getElementById('autorizza_consultazione').value = row.consultazione;
  if (document.getElementById('autorizza_comune')) document.getElementById('autorizza_comune').value = row.comune;
  if (document.getElementById('autorizza_preferenze')) document.getElementById('autorizza_preferenze').value = row.preferenze;
  if (document.getElementById('autorizza_stato')) document.getElementById('autorizza_stato').value = row.stato;
  if (document.getElementById('autorizza_legge')) document.getElementById('autorizza_legge').value = row.legge;
  if (document.getElementById('autorizza_abitanti')) document.getElementById('autorizza_abitanti').value = row.abitanti;
  if (document.getElementById('autorizza_proiezione')) document.getElementById('autorizza_proiezione').checked = row.proiezione;
  if (document.getElementById('autorizza_affluenze')) document.getElementById('autorizza_affluenze').checked = row.affluenze;
  if (document.getElementById('autorizza_solo_gruppi')) document.getElementById('autorizza_solo_gruppi').checked = row.solo_gruppi;
  if (document.getElementById('autorizza_scheda_unica')) document.getElementById('autorizza_scheda_unica').checked = row.scheda_unica;
  document.getElementById('autorizza_idx').value = idx;

  // Cambia titolo form
  formTitle.textContent = 'Modifica autorizzazione';

  // Cambia testo pulsante submit da 'Salva' a 'Salva modifiche'
  form.querySelector('button[type="submit"]').textContent = 'Salva modifiche';

  // Scrolla al titolo (usa id o classe corretta del titolo del form)
  formTitle.scrollIntoView({ behavior: 'smooth' });
}


    // Elimina riga
    function deleteRow(idx) {
      if (confirm('Sei sicuro di voler eliminare questa autorizzazione?')) {
        autorizza_autorizzazioni.splice(idx, 1);
        renderTable(autorizza_autorizzazioni);
        resetForm();
      }
    }

    // Salva form
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const idx = document.getElementById('autorizza_idx').value;
      const data = getFormData();

      if (!data.consultazione) {
        alert('Consultazione è obbligatorio');
        return;
      }
      // Eventuale validazione altri campi...

      if (idx === '') {
        // Nuova riga
        autorizza_autorizzazioni.push(data);
      } else {
        // Modifica
        autorizza_autorizzazioni[idx] = data;
      }

      renderTable(autorizza_autorizzazioni);
      resetForm();
    });

    btnCancel.addEventListener('click', (e) => {
      e.preventDefault();
      resetForm();
    });
  });
</script>
