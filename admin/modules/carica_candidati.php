<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/check_access.php';

// Simulazione dati
if (!isset($_SESSION['liste'])) $_SESSION['liste'] = [
    ['denominazione' => 'Lista A'],
    ['denominazione' => 'Lista B'],
    ['denominazione' => 'Lista C']
];

if (!isset($_SESSION['candidati'])) $_SESSION['candidati'] = [
    ['posizione' => 1, 'cognome' => 'Rossi', 'nome' => 'Mario', 'lista' => 'Lista A'],
    ['posizione' => 2, 'cognome' => 'Bianchi', 'nome' => 'Luigi', 'lista' => 'Lista A'],
    ['posizione' => 1, 'cognome' => 'Verdi', 'nome' => 'Anna', 'lista' => 'Lista B']
];

// GESTIONE ELIMINAZIONE AJAX
if (isset($_GET['elimina'])) {
    $i = intval($_GET['elimina']);
    if (isset($_SESSION['candidati'][$i])) {
        unset($_SESSION['candidati'][$i]);
        $_SESSION['candidati'] = array_values($_SESSION['candidati']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Indice non valido']);
    exit;
}
// Controllo file PDF lato server
if (isset($_FILES['curriculum']) && $_FILES['curriculum']['error'] === UPLOAD_ERR_OK) {
    $mimeCurriculum = mime_content_type($_FILES['curriculum']['tmp_name']);
    if ($mimeCurriculum !== 'application/pdf') {
        die("Errore: il file del Curriculum Vitae deve essere un PDF.");
    }
}

if (isset($_FILES['certificato']) && $_FILES['certificato']['error'] === UPLOAD_ERR_OK) {
    $mimeCertificato = mime_content_type($_FILES['certificato']['tmp_name']);
    if ($mimeCertificato !== 'application/pdf') {
        die("Errore: il file del Certificato Penale deve essere un PDF.");
    }
}

$index = null;
$cognome = '';
$nome = '';
$lista = '';
$posizione = '';
$errors = [];

// Leggo i dati da POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posizione = trim($_POST['posizione']);
    $cognome = trim($_POST['cognome']);
    $nome = trim($_POST['nome']);
    $lista = $_POST['lista'] ?? '';
    $index = isset($_POST['index']) && $_POST['index'] !== '' ? intval($_POST['index']) : null;

    if ($posizione === '' || !is_numeric($posizione)) $errors[] = "Posizione obbligatoria e numerica.";
    if ($cognome === '') $errors[] = "Cognome obbligatorio.";
    if ($nome === '') $errors[] = "Nome obbligatorio.";
    if ($lista === '') $errors[] = "Seleziona una lista.";

    foreach ($_SESSION['candidati'] as $i => $candidato) {
        if ($i !== $index && $candidato['posizione'] == $posizione && $candidato['lista'] === $lista) {
            $errors[] = "Quella posizione è già presente in questa lista.";
            break;
        }
    }

    $ultima_lista_inserita = '';

// Dopo l'inserimento o modifica, se non ci sono errori, salva la lista
if (empty($errors)) {
    $data = [
        'posizione' => intval($posizione),
        'cognome' => $cognome,
        'nome' => $nome,
        'lista' => $lista
    ];
    if ($index !== null) {
        $_SESSION['candidati'][$index] = $data;
    } else {
        $_SESSION['candidati'][] = $data;
    }
    $ultima_lista_inserita = $lista;  // <-- salvo la lista inserita prima di azzerare

    // Reset campi dopo inserimento/modifica
    $posizione = $cognome = $nome = $lista = '';
    $index = null;
} else {
    // Se ci sono errori, mantieni la lista inserita nel form
    $ultima_lista_inserita = $lista;
}
}

// Titolo dinamico del form
$titoloForm = ($index !== null) ? 'Modifica Candidato di Lista' : 'Inserimento Candidato di Lista';
?>

<?php
$candidati = $_SESSION['candidati'];
?>

<section class="content">
  <div class="container-fluid mt-3">

    <!-- FORM CANDIDATO -->
    <div class="card mb-4">
      <div id="cardTitoloInserimento" class="card-header bg-primary text-white">
        <h3 class="card-title" id="cardTitoloInserimento"><?= $titoloForm ?></h3>
      </div>

      <div class="card-body">
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul><?php foreach ($errors as $error) echo "<li>$error</li>"; ?></ul>
          </div>
        <?php endif; ?>

        <form method="post" id="formCandidato" class="needs-validation" novalidate>
          <input type="hidden" name="index" id="index" value="<?= htmlspecialchars($index) ?>">
          <div class="form-row align-items-center" style="gap:0.5rem;">
            <div class="form-group" style="flex:0 0 80px;">
              <label for="posizione">Posizione*</label>
              <input type="number" id="posizione" name="posizione" class="form-control" placeholder="Posizione" value="<?= htmlspecialchars($posizione) ?>" required min="1" max="99" style="text-align:center;">
              <div class="invalid-feedback">Inserisci la posizione</div>
            </div>

            <div class="form-group flex-grow-1">
              <label for="cognome">Cognome*</label>
              <input type="text" id="cognome" name="cognome" class="form-control" placeholder="Cognome" value="<?= htmlspecialchars($cognome) ?>" required>
              <div class="invalid-feedback">Inserisci il cognome</div>
            </div>

            <div class="form-group flex-grow-1">
              <label for="nome">Nome*</label>
              <input type="text" id="nome" name="nome" class="form-control" placeholder="Nome" value="<?= htmlspecialchars($nome) ?>" required>
              <div class="invalid-feedback">Inserisci il nome</div>
            </div>

            <div class="form-group flex-grow-1">
              <label for="lista">Lista*</label>
              <select id="lista" name="lista" class="form-control" required>
                <option value="">Seleziona lista</option>
                <?php foreach ($_SESSION['liste'] as $l): ?>
                  <option value="<?= htmlspecialchars($l['denominazione']) ?>" <?= $lista === $l['denominazione'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($l['denominazione']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Seleziona una lista</div>
            </div>
<?php if (isset($tipo_consultazione) && $tipo_consultazione === 'comunali'): ?>
  <div class="form-group flex-grow-1">
    <label for="curriculum">Curriculum Vitae (PDF)</label>
    <input type="file" id="curriculum" name="curriculum" class="form-control" accept="application/pdf">
  </div>
  <div class="form-group flex-grow-1">
    <label for="certificato">Certificato Penale (PDF)</label>
    <input type="file" id="certificato" name="certificato" class="form-control" accept="application/pdf">
  </div>
<?php endif; ?>

            <div class="form-group" style="flex:0 0 auto; margin-top: 30px;">
              <button type="submit" class="btn btn-success" id="btnSalva">Salva</button>
<button type="button" class="btn btn-secondary" id="btnResetForm">Annulla</button>

            </div>

          </div>
        </form>
      </div>
    </div>

    <!-- TABELLA CANDIDATI CON PAGINAZIONE -->
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Elenco Candidati</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover" id="tabellaCandidati">
          <thead>
            <tr>
              <th style="width:80px; text-align:center;">Posizione</th>
              <th>Cognome</th>
              <th>Nome</th>
              <th>Lista</th>
			  <?php if (isset($tipo_consultazione) && $tipo_consultazione === 'comunali'): ?>
  <th>Curriculum</th>
  <th>Certificato</th>
<?php endif; ?>

              <th style="width:130px; text-align:center;">Azioni</th>
            </tr>
          </thead>
          <tbody id="righeCandidati">
            <!-- Righe generate da JS -->
          </tbody>
        </table>

        <nav>
          <ul class="pagination justify-content-center" id="paginazioneCandidati"></ul>
        </nav>
      </div>
    </div>

  </div>
</section>

<script>
let candidati = <?= json_encode($candidati) ?>;
let ultimaListaInserita = <?= json_encode($ultima_lista_inserita) ?>;
let paginaCorrente = 1;
const candidatiPerPagina = 5;
let totalePagine = Math.ceil(candidati.length / candidatiPerPagina);

function ordinaCandidati() {
  candidati.sort((a, b) => a.posizione - b.posizione);
}

function aggiornaTabellaCandidati() {
  ordinaCandidati();
  const tbody = document.getElementById("righeCandidati");
  tbody.innerHTML = "";

  totalePagine = Math.ceil(candidati.length / candidatiPerPagina);
  if (paginaCorrente > totalePagine) paginaCorrente = totalePagine || 1;

  const start = (paginaCorrente - 1) * candidatiPerPagina;
  const end = start + candidatiPerPagina;
  const candidatiPagina = candidati.slice(start, end);

  function escapeHtml(text) {
    if (!text) return '';
    return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  candidatiPagina.forEach((candidato, i) => {
    const index = start + i;

    function pdfLink(url) {
      if (url && url.toLowerCase().endsWith('.pdf')) {
        const safeUrl = escapeHtml(url);
        return `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer">
                  <i class="fas fa-file-pdf text-danger"></i> PDF
                </a>`;
      }
      return '';
    }

    const pdf1 = pdfLink(candidato.curriculum ?? '');  // Usa campo corretto se serve
    const pdf2 = pdfLink(candidato.certificato ?? '');

    const tr = document.createElement("tr");
   let html = `
  <td style="text-align:center;">${escapeHtml(candidato.posizione.toString())}</td>
  <td>${escapeHtml(candidato.cognome)}</td>
  <td>${escapeHtml(candidato.nome)}</td>
  <td>${escapeHtml(candidato.lista)}</td>
`;

<?php if (isset($tipo_consultazione) && $tipo_consultazione === 'comunali'): ?>
html += `
  <td style="text-align:center;">${pdf1}</td>
  <td style="text-align:center;">${pdf2}</td>
`;
<?php endif; ?>

html += `
  <td style="text-align:center; display: flex; justify-content: center; gap: 0.3rem;">
    <button class="btn btn-sm btn-primary" onclick="modificaCandidato(${index})">Modifica</button>
    <button class="btn btn-sm btn-danger" onclick="eliminaCandidato(${index})">Elimina</button>
  </td>
`;

tr.innerHTML = html;

    tbody.appendChild(tr);
  });

  if (candidati.length === 0) {
    const tr = document.createElement("tr");
    tr.innerHTML = `<td colspan="7" class="text-center">Nessun candidato inserito.</td>`;
    tbody.appendChild(tr);
  }

  aggiornaPaginazioneCandidati();
}

function aggiornaPaginazioneCandidati() {
  const ul = document.getElementById("paginazioneCandidati");
  ul.innerHTML = "";

  if (totalePagine <= 1) {
    ul.style.display = "none";
    return;
  } else {
    ul.style.display = "flex";
  }

  function creaLi(text, page, disabled = false, active = false) {
    const li = document.createElement("li");
    li.className = `page-item ${disabled ? "disabled" : ""} ${active ? "active" : ""}`;
    const a = document.createElement("a");
    a.className = "page-link";
    a.href = "#";
    a.textContent = text;
    if (!disabled && !active) {
      a.addEventListener("click", (e) => {
        e.preventDefault();
        paginaCorrente = page;
        aggiornaTabellaCandidati();
      });
    }
    li.appendChild(a);
    return li;
  }

  // Prev
  ul.appendChild(creaLi("«", paginaCorrente - 1, paginaCorrente === 1));

  for (let i = 1; i <= totalePagine; i++) {
    ul.appendChild(creaLi(i, i, false, i === paginaCorrente));
  }

  // Next
  ul.appendChild(creaLi("»", paginaCorrente + 1, paginaCorrente === totalePagine));
}

function modificaCandidato(index) {
  const candidato = candidati[index];
  if (!candidato) return;

  document.getElementById("posizione").value = candidato.posizione;
  document.getElementById("cognome").value = candidato.cognome;
  document.getElementById("nome").value = candidato.nome;
  document.getElementById("lista").value = candidato.lista;
  document.getElementById("index").value = index;

 // Cambia bottone e titolo
  document.getElementById("cardTitoloInserimento").querySelector("h3").textContent = "Modifica Candidato di Lista";

  document.getElementById('btnSalva').textContent = 'Salva Modifica';
  // Scroll form in vista
  document.getElementById("formCandidato").scrollIntoView({behavior: "smooth"});
}

function eliminaCandidato(index) {
  if (!confirm("Sei sicuro di voler eliminare questo candidato?")) return;

  fetch("?elimina=" + index)
    .then(resp => resp.json())
    .then(data => {
      if (data.success) {
        // Rimuovi da array e aggiorna tabella
        candidati.splice(index, 1);
        aggiornaTabellaCandidati();
        // Se eri in modifica su questo candidato, resetta form
        if (document.getElementById("index").value == index) resetForm();
      } else {
        alert("Errore durante eliminazione: " + (data.error || "Sconosciuto"));
      }
    })
    .catch(() => alert("Errore di rete durante eliminazione."));
}

function aggiornaUltimaPosizione() {
  const listaSelezionata = document.getElementById("lista").value;
  if (!listaSelezionata) return;

  // Filtra i candidati appartenenti alla lista selezionata
  const candidatiLista = candidati.filter(c => c.lista === listaSelezionata);

  let ultimaPosizione = 0;
  if (candidatiLista.length > 0) {
    ultimaPosizione = Math.max(...candidatiLista.map(c => parseInt(c.posizione) || 0));
  }

  // Imposta la prossima posizione disponibile nel campo input
  document.getElementById("posizione").value = ultimaPosizione + 1;
}

window.addEventListener("DOMContentLoaded", () => {
  const selectLista = document.getElementById("lista");

  // Se nessuna lista è selezionata, seleziona la prima disponibile
  if (!selectLista.value && selectLista.options.length > 1) {
    selectLista.selectedIndex = 1;
  }

  aggiornaUltimaPosizione(); // imposta posizione iniziale

  // Cambiando lista, aggiorna posizione
  selectLista.addEventListener("change", () => {
    aggiornaUltimaPosizione();
  });
});

function resetForm() {
    document.getElementById("formCandidato").reset();
  document.getElementById("index").value = "";
  document.getElementById("cardTitoloInserimento").querySelector("h3").textContent = "Inserimento Candidato di Lista";
  document.getElementById('btnSalva').textContent = 'Salva';
   aggiornaUltimaPosizione();
}
function focusCognomeELista() {
  const inputCognome = document.getElementById('cognome');
  const selectLista = document.getElementById('lista');
  if (inputCognome) {
    inputCognome.focus();
  }
  if (selectLista && ultimaListaInserita) {
    selectLista.value = ultimaListaInserita;
  }
}

// Chiama questa funzione dopo il caricamento e dopo l’aggiornamento della tabella
window.onload = () => {
  aggiornaTabellaCandidati();
  focusCognomeELista();
};

// Pulsante annulla
document.getElementById("btnResetForm").addEventListener("click", (e) => {
  e.preventDefault();
  resetForm();
});

// Bootstrap form validation
(() => {
  "use strict";
  const form = document.getElementById("formCandidato");
  form.addEventListener("submit", event => {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add("was-validated");
  }, false);
})();

// Inizializza tabella
aggiornaTabellaCandidati();
</script>

