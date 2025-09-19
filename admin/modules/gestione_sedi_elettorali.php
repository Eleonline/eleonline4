<?php
require_once '../includes/check_access.php';

// --- CONFIGURAZIONE DATABASE ---
// Decommenta e modifica con i tuoi dati di connessione
/*
$host = 'localhost';
$dbname = 'nome_database';
$user = 'username_db';
$password = 'password_db';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Errore di connessione al DB: " . $e->getMessage());
}
*/

// --- Lettura sedi da DB ---
// Decommenta per leggere le sedi dal DB
/*
$sedi = [];
try {
  $stmt = $pdo->query("SELECT circoscrizione, indirizzo, telefono, fax, responsabile FROM sedi_ele");
  $sedi = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Errore nel recupero dati sedi: " . $e->getMessage());
}
*/

// --- Per test senza DB usa array statico ---
$sedi = [
  [
    'circoscrizione' => 'Italia Insulare (Sicilia - Sardegna)',
    'indirizzo' => 'Via Roma 123, Capo d\'Orlando',
    'telefono' => '0941 123456',
    'fax' => '0941 654321',
    'responsabile' => 'Mario Rossi'
  ],
  [
    'circoscrizione' => 'Italia Insulare (Sicilia - Sardegna)',
    'indirizzo' => 'Via Milano 45, Palermo',
    'telefono' => '091 987654',
    'fax' => '',
    'responsabile' => 'Luisa Bianchi'
  ],
];

// --- Salvataggio / modifica sedi tramite POST ---
// Decommenta per salvare i dati (esempio base senza protezione CSRF)
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $circoscrizione = $_POST['circoscrizione'] ?? '';
  $indirizzo = $_POST['indirizzo'] ?? '';
  $telefono = $_POST['telefono'] ?? '';
  $fax = $_POST['fax'] ?? '';
  $responsabile = $_POST['responsabile'] ?? '';
  $id = $_POST['id'] ?? null;  // id per modifica, null per nuovo inserimento

  if (!$circoscrizione || !$indirizzo) {
    die("Campi obbligatori mancanti");
  }

  try {
    if ($id) {
      // Aggiorna sede esistente
      $stmt = $pdo->prepare("UPDATE sedi_ele SET circoscrizione = ?, indirizzo = ?, telefono = ?, fax = ?, responsabile = ? WHERE id = ?");
      $stmt->execute([$circoscrizione, $indirizzo, $telefono, $fax, $responsabile, $id]);
    } else {
      // Inserisci nuova sede
      $stmt = $pdo->prepare("INSERT INTO sedi_ele (circoscrizione, indirizzo, telefono, fax, responsabile) VALUES (?, ?, ?, ?, ?)");
      $stmt->execute([$circoscrizione, $indirizzo, $telefono, $fax, $responsabile]);
    }
    // Dopo salvataggio reindirizza o aggiorna la pagina
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
  } catch (PDOException $e) {
    die("Errore salvataggio dati: " . $e->getMessage());
  }
}
*/

// --- Eliminazione sede tramite GET con parametro id ---
// Decommenta per eliminare la sede
/*
if (isset($_GET['elimina_id'])) {
  $id = intval($_GET['elimina_id']);
  try {
    $stmt = $pdo->prepare("DELETE FROM sedi_ele WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
  } catch (PDOException $e) {
    die("Errore eliminazione sede: " . $e->getMessage());
  }
}
*/
?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
       <h3 id="titoloGestioneSedi" class="card-title"><i class="fas fa-map-marker-alt me-2"></i>Gestione Sedi Elettorali</h3>

      </div>

      <div class="card-body">
        <form id="formSede" class="mb-3" onsubmit="salvaSede(event)">
          <input type="hidden" id="idSede" value=""> <!-- Per ID modifica -->
          <div class="row mb-2">
            <div class="col-md-4">
              <label>Circoscrizione</label>
              <select id="circoscrizione" class="form-control" required>
                <option value="">Seleziona circoscrizione</option>
                <option value="Italia Insulare (Sicilia - Sardegna)">Circoscrizione Elettorale V - Italia Insulare (Sicilia - Sardegna)</option>
              </select>
            </div>
            <div class="col-md-8 rigaMappa">
              <label>Indirizzo</label>
              <div class="input-group">
                <input type="text" id="indir" name="indir" class="form-control indir" required>
                <button type="button" class="btn btn-outline-secondary btnApriMappa btnApriMappaForm">
                  <i class="fas fa-map-pin me-2"></i>Apri mappa
                </button>
              </div>

              <input type="hidden" class="nome_comune" name="nome_comune" value="Capo d'Orlando" />
              <input type="hidden" class="lat" name="lat" value="" />
              <input type="hidden" class="lng" name="lng" value="" />
            </div>
          </div>

          <div class="row">
            <div class="col-md-3">
              <label>Telefono</label>
              <input type="text" id="telefono" class="form-control">
            </div>
            <div class="col-md-3">
              <label>Fax</label>
              <input type="text" id="fax" class="form-control">
            </div>
            <div class="col-md-4">
              <label>Responsabile</label>
              <input type="text" id="responsabile" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100" id="btnSalvaSede">Aggiungi</button>
            </div>
          </div>
        </form>
      </div>
<div class="card shadow-sm mb-3">
  <div class="card-header bg-secondary text-white">
    <h3 class="card-title">Lista Sedi</h3>
  </div>
  <div class="card-body table-responsive" style="max-height:450px; overflow-y:auto; border: 1px solid #dee2e6; border-radius: 0 0 0.25rem 0.25rem;">
    <table class="table table-striped mb-0" id="tabellaSedi">
      <thead>
        <tr>
          <th>Circoscrizione</th>
          <th>Indirizzo</th>
          <th>Mappa</th>
          <th>Telefono</th>
          <th>Fax</th>
          <th>Responsabile</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody id="righeSedi">
        <!-- Righe sedi generate da JS -->
      </tbody>
    </table>
  </div>

  <div class="card-footer">
    <nav>
      <ul class="pagination justify-content-center mb-0" id="pagination"></ul>
    </nav>
  </div>
</div>

      <div class="card-footer text-muted">
        Puoi gestire qui tutte le sedi elettorali collegate alle circoscrizioni.
      </div>
    </div>
  </div>
</section>

<script>
let sedi = <?php echo json_encode($sedi); ?> || [];
let currentEditingIndex = null;  // tiene traccia se sto modificando una sede

// Parametri paginazione
const itemsPerPage = 5;
let currentPage = 1;

function aggiornaTabella() {
  const tbody = document.getElementById("righeSedi");
  tbody.innerHTML = "";

  // Calcolo indici da visualizzare per pagina
  const start = (currentPage - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const pageItems = sedi.slice(start, end);

  pageItems.forEach((s, i) => {
    const realIndex = start + i; // indice reale nell'array completo
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${s.circoscrizione}</td>
      <td>${s.indirizzo}</td>
      <td>
        <button class="btn btn-sm btn-outline-secondary btnApriMappa" data-index="${realIndex}">
          <i class="fas fa-map-pin"></i> Apri mappa
        </button>
      </td>
      <td>${s.telefono || ""}</td>
      <td>${s.fax || ""}</td>
      <td>${s.responsabile || ""}</td>
      <td>
        <button class="btn btn-sm btn-warning me-1" onclick="modificaSede(${realIndex})">Modifica</button>
        <button class="btn btn-sm btn-danger" onclick="eliminaSede(${realIndex})">Elimina</button>
      </td>
    `;
    tbody.appendChild(tr);
  });

  aggiornaPaginazione();

  // Attacco eventi ai bottoni mappa
  document.querySelectorAll('#righeSedi .btnApriMappa').forEach(btn => {
    btn.onclick = () => {
      const index = parseInt(btn.getAttribute('data-index'));
      apriMappaSingola(index);
    };
  });
}

function aggiornaPaginazione() {
  const pagination = document.getElementById("pagination");
  pagination.innerHTML = "";

  const pageCount = Math.ceil(sedi.length / itemsPerPage);
  if (pageCount <= 1) return; // niente paginazione se 1 pagina

  for (let i = 1; i <= pageCount; i++) {
    const li = document.createElement("li");
    li.classList.add("page-item");
    if (i === currentPage) li.classList.add("active");

    const a = document.createElement("a");
    a.classList.add("page-link");
    a.href = "#";
    a.textContent = i;
    a.onclick = (e) => {
      e.preventDefault();
      currentPage = i;
      aggiornaTabella();
    };

    li.appendChild(a);
    pagination.appendChild(li);
  }
}

function salvaSede(event) {
  event.preventDefault();

  const circoscrizione = document.getElementById('circoscrizione').value.trim();
  const indirizzo = document.getElementById('indir').value.trim();
  const telefono = document.getElementById('telefono').value.trim();
  const fax = document.getElementById('fax').value.trim();
  const responsabile = document.getElementById('responsabile').value.trim();

  if (!circoscrizione || !indirizzo) {
    alert("Circoscrizione e Indirizzo sono obbligatori.");
    return;
  }

  const nuovaSede = { circoscrizione, indirizzo, telefono, fax, responsabile };

  if (currentEditingIndex !== null) {
    sedi[currentEditingIndex] = nuovaSede;
    currentEditingIndex = null;
    document.getElementById('btnSalvaSede').textContent = "Aggiungi";
  } else {
    sedi.push(nuovaSede);
  }

  // Reset form
  document.getElementById('formSede').reset();

  aggiornaTabella();
}

function modificaSede(index) {
  currentEditingIndex = index;
  const s = sedi[index];
  document.getElementById('circoscrizione').value = s.circoscrizione;
  document.getElementById('indir').value = s.indirizzo;
  document.getElementById('telefono').value = s.telefono || "";
  document.getElementById('fax').value = s.fax || "";
  document.getElementById('responsabile').value = s.responsabile || "";
  document.getElementById('btnSalvaSede').textContent = "Salva Modifica";

  // Scrolla al form (o a un campo specifico)
// Scrolla al titolo della gestione sedi
  document.getElementById('titoloGestioneSedi').scrollIntoView({ behavior: 'smooth' });
}

function eliminaSede(index) {
  const sede = sedi[index];
  if (!confirm(`Sei sicuro di voler eliminare questa sede?\nIndirizzo: ${sede.indirizzo}`)) return;
  sedi.splice(index, 1);

  // Se l'eliminazione ha portato a pagine vuote, torniamo indietro di pagina
  const maxPage = Math.ceil(sedi.length / itemsPerPage);
  if (currentPage > maxPage) currentPage = maxPage > 0 ? maxPage : 1;

  aggiornaTabella();
}


// Funzione per aprire popup mappa per la singola sede (dummy)
function apriMappaSingola(index) {
  const s = sedi[index];
  alert(`Apri mappa per:\n${s.indirizzo}\n(Circoscrizione: ${s.circoscrizione})`);
  // Qui puoi integrare il tuo modulo mappa con lat/lng
}

// Carica tabella all'avvio
aggiornaTabella();

// --- Gestione pulsante apri mappa nel form ---
// Dummy alert (sostituire con apertura popup mappa reale)
document.querySelector('.btnApriMappaForm').addEventListener('click', () => {
  alert("Apri mappa per inserimento/modifica sede (da implementare)");
});
</script>

<style>
.rigaMappa {
  position: relative;
}
/* Aggiungi eventuali stili per la mappa o popup qui */
</style>
