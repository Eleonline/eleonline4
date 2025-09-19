<?php
require_once '../includes/check_access.php';

// --- DA ATTIVARE QUANDO HAI IL DB PRONTO ---

/*
$conn = new mysqli('host', 'username', 'password', 'database');
if ($conn->connect_error) {
  die("Connessione fallita: " . $conn->connect_error);
}

// Carica consultazioni da DB, ordinate da ultima a prima (data_inizio DESC)
$sql = "SELECT id, tipo, denominazione, data_inizio, data_fine, link, predefinita FROM consultazioni ORDER BY data_inizio DESC";
$result = $conn->query($sql);

$consultazioni = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $row['predefinita'] = (bool)$row['predefinita'];
    $consultazioni[] = $row;
  }
} else {
  $consultazioni = [];
}

$conn->close();
*/

// --- Dati di esempio in mancanza di DB (puoi rimuoverli quando usi DB) ---
$consultazioni = [
  ['id'=>1, 'tipo'=>'CAMERA', 'denominazione'=>'Elezioni Politiche 2022', 'data_inizio'=>'2022-09-25', 'data_fine'=>'2022-09-26', 'link'=>'https://dait.interno.gov.it/elezioni', 'predefinita'=>true],
  ['id'=>2, 'tipo'=>'COMUNALI', 'denominazione'=>'Comunali 2023 - Roma', 'data_inizio'=>'2023-06-15', 'data_fine'=>'2023-06-16', 'link'=>'https://dait.interno.gov.it/elezioni/trasparenza', 'predefinita'=>false],
];
?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-vote-yea"></i> Gestione Consultazioni</h2>

    <!-- FORM -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="form-title">Aggiungi Consultazione</h3>
      </div>
      <div class="card-body">
        <form id="consultazioneForm">
          <input type="hidden" name="consultazione_id" id="consultazione_id">

          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="tipo">Tipo*</label>
              <select class="form-control" id="tipo" name="tipo" required>
                <option value="">Seleziona...</option>
                <option value="PROVINCIALI">PROVINCIALI</option>
                <option value="REFERENDUM">REFERENDUM</option>
                <option value="COMUNALI">COMUNALI</option>
                <option value="CIRCOSCRIZIONALI">CIRCOSCRIZIONALI</option>
                <option value="BALLOTTAGGIO COMUNALI">BALLOTTAGGIO COMUNALI</option>
                <option value="CAMERA">CAMERA</option>
                <option value="SENATO">SENATO</option>
                <option value="EUROPEE">EUROPEE</option>
                <option value="REGIONALI">REGIONALI</option>
                <option value="SENATO CON GRUPPI">SENATO CON GRUPPI</option>
                <option value="CAMERA CON GRUPPI">CAMERA CON GRUPPI</option>
                <option value="PROVINCIALI CON COLLEGI">PROVINCIALI CON COLLEGI</option>
                <option value="BALLOTTAGGIO PROVINCIALI">BALLOTTAGGIO PROVINCIALI</option>
                <option value="EUROPEE CON COLLEGI">EUROPEE CON COLLEGI</option>
                <option value="CAMERA CON GRUPPI E COLLEGI">CAMERA CON GRUPPI E COLLEGI</option>
                <option value="SENATO CON GRUPPI E COLLEGI">SENATO CON GRUPPI E COLLEGI</option>
                <option value="REGIONALI CON COLLEGI">REGIONALI CON COLLEGI</option>
                <option value="CAMERA - Rosatellum 2.0">CAMERA - Rosatellum 2.0</option>
                <option value="SENATO - Rosatellum 2.0">SENATO - Rosatellum 2.0</option>
              </select>
            </div>
            <div class="form-group col-md-5">
              <label for="denominazione">Denominazione*</label>
              <input type="text" class="form-control" id="denominazione" name="denominazione" required>
            </div>
            <div class="form-group col-md-2">
              <label for="data_inizio">Data Inizio*</label>
              <input type="date" class="form-control" id="data_inizio" name="data_inizio" required>
            </div>
            <div class="form-group col-md-2">
              <label for="data_fine">Data Fine*</label>
              <input type="date" class="form-control" id="data_fine" name="data_fine" required>
            </div>
          </div>

          <div class="form-group">
            <label for="link">Link DAIT Trasparenza</label>
            <input type="url" class="form-control" id="link" name="link">
          </div>

          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="predefinita" name="predefinita">
            <label class="form-check-label" for="predefinita">Consultazione predefinita</label>
          </div>

          <button type="submit" class="btn btn-success">Aggiungi Consultazione</button>
          <button type="reset" class="btn btn-secondary" id="cancelEdit">Annulla</button>
        </form>
      </div>
    </div>

    <!-- LISTA -->
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Lista Consultazioni</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="consultazioniTable">
          <thead>
            <tr>
              <th style="width:30px;"></th> <!-- colonna stella senza titolo -->
              <th>Tipo</th>
              <th>Denominazione</th>
              <th>Data Inizio</th>
              <th>Data Fine</th>
              <th>Link DAIT</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody id="consultazioniRows"></tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script>
let consultazioni = <?php echo json_encode($consultazioni); ?>;

const form = document.getElementById('consultazioneForm');
const rows = document.getElementById('consultazioniRows');
const formTitle = document.getElementById('form-title');
const submitBtn = form.querySelector('button[type="submit"]');

// Ordina consultazioni da più recente a meno recente (data_inizio DESC)
function ordinaConsultazioni() {
  consultazioni.sort((a,b) => {
    // Confronto stringhe ISO date 'YYYY-MM-DD' direttamente valido
    if (a.data_inizio > b.data_inizio) return -1;
    if (a.data_inizio < b.data_inizio) return 1;
    return 0;
  });
}

// Render lista consultazioni in tabella
function renderConsultazioni() {
  ordinaConsultazioni();
  rows.innerHTML = '';
  consultazioni.forEach(c => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td style="text-align:center; cursor:pointer;" onclick="impostaPredefinita(${c.id})">
        ${c.predefinita ? '⭐' : ''}
      </td>
      <td>${c.tipo}</td>
      <td>${c.denominazione}</td>
      <td>${c.data_inizio}</td>
      <td>${c.data_fine}</td>
      <td>${c.link ? `<a href="${c.link}" target="_blank">Vai</a>` : ''}</td>
      <td>
        <button class="btn btn-sm btn-warning" onclick="editConsultazione(${c.id})">Modifica</button>
        <button class="btn btn-sm btn-danger" onclick="deleteConsultazione(${c.id})">Elimina</button>
      </td>
    `;
    rows.appendChild(tr);
  });
}

// Reset form in modalità aggiunta
function resetForm() {
  form.reset();
  document.getElementById('consultazione_id').value = '';
  formTitle.innerText = 'Aggiungi Consultazione';
  submitBtn.innerText = 'Aggiungi Consultazione';
}

// Funzione modifica consultazione
function editConsultazione(id) {
  const c = consultazioni.find(x => x.id === id);
  if (!c) return;
  document.getElementById('consultazione_id').value = c.id;
  document.getElementById('tipo').value = c.tipo;
  document.getElementById('denominazione').value = c.denominazione;
  document.getElementById('data_inizio').value = c.data_inizio;
  document.getElementById('data_fine').value = c.data_fine;
  document.getElementById('link').value = c.link;
  document.getElementById('predefinita').checked = c.predefinita;

  formTitle.innerText = 'Modifica Consultazione';
  submitBtn.innerText = 'Modifica Consultazione';
}

// Imposta consultazione predefinita (una sola alla volta)
function impostaPredefinita(id) {
  consultazioni.forEach(c => c.predefinita = (c.id === id));
  renderConsultazioni();
  const currentId = parseInt(document.getElementById('consultazione_id').value);
  if (currentId === id) {
    document.getElementById('predefinita').checked = true;
  }
}

// Cancella consultazione con conferma e controllo predefinita
function deleteConsultazione(id) {
  const c = consultazioni.find(x => x.id === id);
  if (!c) return;

  let msg = `Eliminare la consultazione "${c.denominazione}"?`;
  if (c.predefinita) {
    // Trova la nuova consultazione predefinita (più recente dopo eliminazione)
    const altre = consultazioni.filter(x => x.id !== id);
    ordinaConsultazioni(altre);
    const nuovaPredefinita = altre.length > 0 ? altre[0].denominazione : null;

    msg = `ATTENZIONE! Stai eliminando la consultazione predefinita "${c.denominazione}".\n` +
          `La consultazione predefinita sarà spostata automaticamente alla consultazione "${nuovaPredefinita}".\n\n` +
          `Procedere comunque?`;
  }
  if (!confirm(msg)) return;

  // Rimuove consultazione
  consultazioni = consultazioni.filter(x => x.id !== id);

  // Se eliminata consultazione predefinita, sposta flag all'ultima consultazione (più recente)
  if (c.predefinita && consultazioni.length > 0) {
    ordinaConsultazioni();
    consultazioni[0].predefinita = true;
  }

  resetForm();
  renderConsultazioni();
}

// Ordina consultazioni per data di inizio discendente (più recente prima)
function ordinaConsultazioni(lista = consultazioni) {
  lista.sort((a, b) => new Date(b.data_inizio) - new Date(a.data_inizio));
}


form.addEventListener('submit', function(e) {
  e.preventDefault();

  const id = document.getElementById('consultazione_id').value;
  const tipo = document.getElementById('tipo').value.trim();
  const denominazione = document.getElementById('denominazione').value.trim();
  const data_inizio = document.getElementById('data_inizio').value;
  const data_fine = document.getElementById('data_fine').value;
  const link = document.getElementById('link').value.trim();
  const predefinita = document.getElementById('predefinita').checked;

  if (!tipo || !denominazione || !data_inizio || !data_fine) {
    alert('Compilare tutti i campi obbligatori (*)');
    return;
  }

  if (id) {
    // Modifica
    const cIndex = consultazioni.findIndex(x => x.id == id);
    if (cIndex === -1) {
      alert('Consultazione non trovata');
      return;
    }
    consultazioni[cIndex] = { id: Number(id), tipo, denominazione, data_inizio, data_fine, link, predefinita };
  } else {
    // Aggiungi nuovo: genera id incrementale
    const maxId = consultazioni.reduce((max, c) => c.id > max ? c.id : max, 0);
    consultazioni.push({ id: maxId + 1, tipo, denominazione, data_inizio, data_fine, link, predefinita });
  }

  // Se predefinita selezionata, togli dalle altre
  if (predefinita) {
    const currentId = id ? Number(id) : consultazioni[consultazioni.length - 1].id;
    consultazioni.forEach(c => {
      if (c.id !== currentId) c.predefinita = false;
    });
  } else {
    // Se nessuna è predefinita (caso possibile dopo modifica), imposta la prima (più recente)
    if (!consultazioni.some(c => c.predefinita) && consultazioni.length > 0) {
      ordinaConsultazioni();
      consultazioni[0].predefinita = true;
    }
  }

  resetForm();
  renderConsultazioni();
});
const predefCheck = document.getElementById('predefinita');

predefCheck.addEventListener('change', function() {
  const isChecked = this.checked;
  const id = document.getElementById('consultazione_id').value;
  
  if (isChecked) {
    // Se sto selezionando come predefinita
    if (id) {
      alert('Stai impostando questa consultazione come predefinita. Verrà tolta la predefinita a un\'altra consultazione.');
    } else {
      alert('Stai aggiungendo una nuova consultazione predefinita. Verrà tolta la predefinita a un\'altra consultazione.');
    }
  } else {
    // Se sto togliendo la predefinita
    if (id) {
      alert('Stai togliendo la predefinita da questa consultazione. Il sistema imposterà un\'altra consultazione come predefinita automaticamente.');
    } else {
      alert('Hai tolto la spunta di consultazione predefinita.');
    }
  }
});
function impostaPredefinita(id) {
  const consultazione = consultazioni.find(c => c.id === id);
  if (!consultazione) return;

  if (consultazione.predefinita) {
    if (!confirm(`Stai rimuovendo la consultazione predefinita "${consultazione.denominazione}". Il sistema ne imposterà automaticamente un'altra.`)) {
      return;
    }
  } else {
    if (!confirm(`Stai impostando "${consultazione.denominazione}" come consultazione predefinita. Verrà tolta la predefinita a un'altra consultazione.`)) {
      return;
    }
  }

  consultazioni.forEach(c => c.predefinita = (c.id === id));
  renderConsultazioni();

  const currentId = parseInt(document.getElementById('consultazione_id').value);
  if (currentId === id) {
    document.getElementById('predefinita').checked = true;
  } else {
    document.getElementById('predefinita').checked = false;
  }
}

document.getElementById('cancelEdit').addEventListener('click', resetForm);

// Inizializza lista
renderConsultazioni();
</script>
