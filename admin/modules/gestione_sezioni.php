<?php
require_once '../includes/check_access.php';

$currentUserRole = $_SESSION['ruolo'] ?? 'operatore';

$indirizzi = [
  'Via Roma',
  'Contrada Furriolo',
  'Contrada Piscittina',
  'Contrada San Martino',
  'Contrada Scafa',
  'Via Piave',
];

$sezioni = [
  ['id'=>1, 'numero'=>1, 'indirizzo'=>'Via Roma', 'maschi'=>430, 'femmine'=>540],
  ['id'=>2, 'numero'=>2, 'indirizzo'=>'Via Roma', 'maschi'=>487, 'femmine'=>517],
  ['id'=>3, 'numero'=>3, 'indirizzo'=>'Contrada Furriolo', 'maschi'=>545, 'femmine'=>624],
  ['id'=>4, 'numero'=>4, 'indirizzo'=>'Contrada Piscittina', 'maschi'=>289, 'femmine'=>306],
  ['id'=>5, 'numero'=>5, 'indirizzo'=>'Contrada San Martino', 'maschi'=>537, 'femmine'=>535],
  ['id'=>6, 'numero'=>6, 'indirizzo'=>'Contrada Scafa', 'maschi'=>377, 'femmine'=>405],
  ['id'=>7, 'numero'=>7, 'indirizzo'=>'Via Piave', 'maschi'=>470, 'femmine'=>565],
  ['id'=>8, 'numero'=>8, 'indirizzo'=>'Contrada Furriolo', 'maschi'=>545, 'femmine'=>624],
  ['id'=>9, 'numero'=>9, 'indirizzo'=>'Contrada Piscittina', 'maschi'=>289, 'femmine'=>306],
  ['id'=>10, 'numero'=>10, 'indirizzo'=>'Contrada San Martino', 'maschi'=>537, 'femmine'=>535],
  ['id'=>11, 'numero'=>11, 'indirizzo'=>'Contrada Scafa', 'maschi'=>377, 'femmine'=>405],
  ['id'=>12, 'numero'=>12, 'indirizzo'=>'Via Piave', 'maschi'=>470, 'femmine'=>565],
];

// qui puoi caricare le consultazioni dal DB o array
$consultazioni = [
  ['id'=>1, 'nome'=>'Consultazione 2025'],
  ['id'=>2, 'nome'=>'Consultazione 2024'],
  ['id'=>3, 'nome'=>'Consultazione 2023'],
];
/*
//// CONFIGURAZIONE CONNESSIONE
$host = "localhost";
$user = "tuo_utente";
$password = "tua_password";
$dbname = "nome_database";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

//// CARICAMENTO SEZIONI DAL DATABASE
$sezioni_db = [];
$sql = "SELECT * FROM sezioni ORDER BY numero ASC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $sezioni_db[] = $row;
}
echo "<script>sezioni = " . json_encode($sezioni_db) . ";</script>";

//// INSERIMENTO DI UNA NUOVA SEZIONE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero'], $_POST['indirizzo'], $_POST['maschi'], $_POST['femmine'])) {
    $stmt = $conn->prepare("INSERT INTO sezioni (numero, indirizzo, maschi, femmine) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $_POST['numero'], $_POST['indirizzo'], $_POST['maschi'], $_POST['femmine']);
    $stmt->execute();
    $stmt->close();
    // Redirect dopo inserimento (opzionale)
    // header("Location: gestione_sezione.php");
    // exit;
}

//// ELIMINAZIONE DI UNA SEZIONE (tramite GET o POST con ID)
if (isset($_GET['elimina_id'])) {
    $id = intval($_GET['elimina_id']);
    $conn->query("DELETE FROM sezioni WHERE id = $id");
    // Redirect dopo eliminazione
    // header("Location: gestione_sezione.php");
    // exit;
}

//// MODIFICA DI UNA SEZIONE (opzionale, se prevedi modifica diretta)
if (isset($_POST['modifica_id'])) {
    $stmt = $conn->prepare("UPDATE sezioni SET numero=?, indirizzo=?, maschi=?, femmine=? WHERE id=?");
    $stmt->bind_param("ssiii", $_POST['numero'], $_POST['indirizzo'], $_POST['maschi'], $_POST['femmine'], $_POST['modifica_id']);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
*/

// Trova ultimo numero sezione esistente
$maxNumero = 0;
foreach ($sezioni as $s) {
  if ($s['numero'] > $maxNumero) $maxNumero = $s['numero'];
}
?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-map-marker-alt"></i> Gestione Sezioni</h2>
<div class="card mb-4">
  <div class="card-header bg-info text-white">
    <h3 class="card-title"><i class="fas fa-users"></i> Popola Elettori da Consultazione</h3>
  </div>
  <div class="card-body">
    <div class="row align-items-center">
      <div class="col-md-8">
        <select id="selectConsultazione" class="form-control">
          <option value="">-- Seleziona Consultazione --</option>
          <?php foreach ($consultazioni as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <button id="popolaBtn" class="btn btn-success btn-block">
          <i class="fas fa-sync-alt"></i> Popola Elettori
        </button>
      </div>
    </div>
  </div>
</div>


    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="form-title">Aggiungi Sezione</h3>
      </div>
      <div class="card-body">
<form id="sezioneForm">
  <input type="hidden" id="sezione_id" value="">
  <div class="form-row">
    <div class="form-group col-md-2">
      <label>Numero Sezione*</label>
      <input type="number" id="numero" class="form-control" required min="1" value="<?php echo $maxNumero + 1; ?>">
    </div>
    <div class="form-group col-md-4">
      <label>Indirizzo (Sede)*</label>
      <select id="indirizzo" class="form-control" required></select>
    </div>
    <div class="form-group col-md-2">
      <label>Maschi</label>
      <input type="number" id="maschi" class="form-control" min="0">
    </div>
    <div class="form-group col-md-2">
      <label>Femmine</label>
      <input type="number" id="femmine" class="form-control" min="0">
    </div>
    <div class="form-group col-md-2 d-flex align-items-end">
      <button type="button" class="btn btn-success w-100" id="saveBtn">Aggiungi Sezione</button>
    </div>
  </div>
</form>

      </div>
    </div>

    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Lista Sezioni</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="sezioniTable">
          <thead>
            <tr>
              <th>Numero</th>
              <th>Indirizzo</th>
              <th>Maschi</th>
              <th>Femmine</th>
              <th>Totale</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody id="sezioniRows"></tbody>
        </table>
		<div id="paginationControls" class="mt-3"></div>
      </div>
    </div>
  </div>
</section>

<script>
// Passa la variabile PHP $consultazioni in JS
const consultazioni = <?php echo json_encode($consultazioni); ?>;
const indirizzi = <?php echo json_encode($indirizzi); ?>;
let sezioni = <?php echo json_encode($sezioni); ?>;
let paginaCorrente = 1;
const righePerPagina = 10;

const formTitle = document.getElementById('form-title');
const sezioneForm = document.getElementById('sezioneForm');
const sezioniRows = document.getElementById('sezioniRows');
const saveBtn = document.getElementById('saveBtn');

const inputId = document.getElementById('sezione_id');
const inputNumero = document.getElementById('numero');
const selectIndirizzo = document.getElementById('indirizzo');
const inputMaschi = document.getElementById('maschi');
const inputFemmine = document.getElementById('femmine');

function populateIndirizzi() {
  selectIndirizzo.innerHTML = '<option value="">Seleziona indirizzo</option>';
  indirizzi.forEach(indirizzo => {
    selectIndirizzo.innerHTML += `<option value="${indirizzo}">${indirizzo}</option>`;
  });
}

function renderSezioni() {
  sezioni.sort((a, b) => a.numero - b.numero);
  sezioniRows.innerHTML = '';

  const start = (paginaCorrente - 1) * righePerPagina;
  const end = start + righePerPagina;
  const sezioniPagina = sezioni.slice(start, end);

  sezioniPagina.forEach(sez => {
    const totale = (parseInt(sez.maschi) || 0) + (parseInt(sez.femmine) || 0);

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${sez.numero}</td>
      <td>
        <select class="form-select indirizzo-select" data-id="${sez.id}">
          ${indirizzi.map(ind => `<option value="${ind}" ${ind === sez.indirizzo ? 'selected' : ''}>${ind}</option>`).join('')}
        </select>
      </td>
      <td><input type="number" min="0" class="form-control maschi-input" data-id="${sez.id}" value="${sez.maschi}"></td>
      <td><input type="number" min="0" class="form-control femmine-input" data-id="${sez.id}" value="${sez.femmine}"></td>
      <td class="totale-cell">${totale}</td>
      <td>
        <button class="btn btn-sm btn-primary aggiorna-btn" data-id="${sez.id}">Aggiorna</button>
        <button class="btn btn-sm btn-danger" onclick="deleteSezione(${sez.id})">Elimina</button>
      </td>
    `;
    sezioniRows.appendChild(tr);
  });

  document.querySelectorAll('.aggiorna-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = parseInt(btn.getAttribute('data-id'));

      // Calcolo prima riga visibile
      const start = (paginaCorrente - 1) * righePerPagina;
      const end = start + righePerPagina;
      const sezioniPagina = sezioni.slice(start, end);

      // Se l'id è della prima riga visibile, la rimuovo dopo aggiornamento
      if (sezioniPagina.length > 0 && sezioniPagina[0].id === id) {
        aggiornaSezioneDaRiga(id);

        // Rimuovo dalla lista globale
        const globalIndex = sezioni.findIndex(s => s.id === id);
        if (globalIndex !== -1) {
          sezioni.splice(globalIndex, 1);
        }

        // Controllo se pagina corrente è valida
        const totalPages = Math.ceil(sezioni.length / righePerPagina);
        if (paginaCorrente > totalPages) {
          paginaCorrente = totalPages > 0 ? totalPages : 1;
        }

        renderSezioni();
      } else {
        // Aggiorna solo la riga senza rimuovere
        aggiornaSezioneDaRiga(id);
      }
    });
  });

  renderPaginationControls();
}

function aggiornaSezioneDaRiga(id) {
  const selectIndirizzoRiga = document.querySelector(`select.indirizzo-select[data-id='${id}']`);
  const inputMaschiRiga = document.querySelector(`input.maschi-input[data-id='${id}']`);
  const inputFemmineRiga = document.querySelector(`input.femmine-input[data-id='${id}']`);

  const s = sezioni.find(s => s.id === id);
  if (s) {
    s.indirizzo = selectIndirizzoRiga.value;
    s.maschi = parseInt(inputMaschiRiga.value) || 0;
    s.femmine = parseInt(inputFemmineRiga.value) || 0;

    // Aggiorno la cella totale
    const totale = s.maschi + s.femmine;
    const rigaTr = selectIndirizzoRiga.closest('tr');
    const cellTotale = rigaTr.querySelector('.totale-cell');
    if (cellTotale) {
      cellTotale.textContent = totale;
    }
  }
}

function deleteSezione(id) {
  // Trova la sezione da eliminare
  const sezione = sezioni.find(s => s.id === id);
  if (!sezione) return;

  // Mostra conferma con numero sezione (es. "Sezione n. 12")
  if (!confirm(`Sei sicuro di voler eliminare la sezione n. ${sezione.numero}?`)) return;

  // Rimuove la sezione
  sezioni = sezioni.filter(s => s.id !== id);

  const totalPages = Math.ceil(sezioni.length / righePerPagina);
  if (paginaCorrente > totalPages) {
    paginaCorrente = totalPages > 0 ? totalPages : 1;
  }

  renderSezioni();
}


function renderPaginationControls() {
  const totalPages = Math.ceil(sezioni.length / righePerPagina);
  const pagination = document.getElementById('paginationControls');
  pagination.innerHTML = '';

  if (totalPages <= 1) return;

  // Pulsante Precedente
  const prevBtn = document.createElement('button');
  prevBtn.className = 'btn btn-secondary mr-2';
  prevBtn.textContent = 'Precedente';
  prevBtn.disabled = paginaCorrente === 1;
  prevBtn.addEventListener('click', () => {
    if (paginaCorrente > 1) {
      paginaCorrente--;
      renderSezioni();
    }
  });
  pagination.appendChild(prevBtn);

  // Pulsanti numerici
  for (let i = 1; i <= totalPages; i++) {
    const pageBtn = document.createElement('button');
    pageBtn.className = 'btn mr-1 ' + (i === paginaCorrente ? 'btn-primary' : 'btn-light');
    pageBtn.textContent = i;
    pageBtn.addEventListener('click', () => {
      paginaCorrente = i;
      renderSezioni();
    });
    pagination.appendChild(pageBtn);
  }

  // Pulsante Successivo (chiamato "Successivo" senza "Ok")
  const nextBtn = document.createElement('button');
  nextBtn.className = 'btn btn-secondary ml-2';
  nextBtn.textContent = 'Successivo';
  nextBtn.disabled = paginaCorrente === totalPages;
  nextBtn.addEventListener('click', () => {
    if (paginaCorrente < totalPages) {
      paginaCorrente++;
      renderSezioni();
    }
  });
  pagination.appendChild(nextBtn);
}


saveBtn.addEventListener('click', () => {
  const numeroVal = parseInt(inputNumero.value);
  const indirizzoVal = selectIndirizzo.value;
  const maschiVal = parseInt(inputMaschi.value) || 0;
  const femmineVal = parseInt(inputFemmine.value) || 0;

  if (!numeroVal || !indirizzoVal) {
    alert('Compila tutti i campi obbligatori.');
    return;
  }

  // Controllo duplicati numero
  if (sezioni.some(s => s.numero === numeroVal)) {
    alert('Numero sezione già esistente.');
    return;
  }

  // Aggiungo nuova sezione
  const newId = sezioni.length > 0 ? Math.max(...sezioni.map(s => s.id)) + 1 : 1;
  sezioni.push({
    id: newId,
    numero: numeroVal,
    indirizzo: indirizzoVal,
    maschi: maschiVal,
    femmine: femmineVal,
  });

  // Reset form
  inputNumero.value = numeroVal + 1;
  selectIndirizzo.value = '';
  inputMaschi.value = '';
  inputFemmine.value = '';

  // Se pagine totali sono cambiate, muovo alla ultima
  paginaCorrente = Math.ceil(sezioni.length / righePerPagina);

  renderSezioni();
});
document.getElementById('popolaBtn').addEventListener('click', () => {
  const select = document.getElementById('selectConsultazione');
  const selectedId = select.value;

  if (!selectedId) {
    alert('Seleziona prima una consultazione.');
    return;
  }

  if (confirm('Attenzione: tutti i dati caricati verranno sostituiti. Procedere?')) {
    // Qui metti la logica per popolamento dati
    // Per esempio, puoi chiamare una funzione JS o fare una chiamata AJAX

    // Esempio semplice:
    popolaElettori(selectedId);
  }
});

function popolaElettori(consultazioneId) {
 const consultazione = consultazioni.find(c => c.id == consultazioneId);
  const nome = consultazione ? consultazione.nome : 'Consultazione sconosciuta';

  alert('Popolamento elettori dalla consultazione: ' + nome);

  // Qui dovresti aggiornare l'array sezioni (sezioni) con i dati nuovi,
  // poi ricaricare la tabella:

  // Esempio:
  // sezioni = dati_nuovi_da_consultazione;
  // paginaCorrente = 1;
  // renderSezioni();

  // Al momento demo: resetto tabella
  sezioni = [];  // o sostituisci con dati reali
  paginaCorrente = 1;
  renderSezioni();
}

populateIndirizzi();
renderSezioni();
</script>
