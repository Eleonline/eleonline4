<?php
require_once '../includes/check_access.php';

$upload_dir_simboli = __DIR__ . '/simboli/';
$upload_dir_programmi = __DIR__ . '/programmi/';
$upload_dir_cv = __DIR__ . '/cv/';
$upload_dir_certificati = __DIR__ . '/certificati/';

// Variabile tipo candidato: presidente, sindaco, uninominale

switch ($tipo_consultazione) {
    case 'comunali':
        $tipo_candidato = 'sindaco';
        break;
    case 'regionali':
        $tipo_candidato = 'presidente';
        break;
    case 'camera':
    case 'senato':
        $tipo_candidato = 'uninominale';
        break;
}


// Lista candidati esempio con tipo
$candidati = [
  ['id'=>1,'tipo'=>'presidente','posizione'=>1,'nome'=>'Mario Rossi','simbolo'=>'simboli/listaA.png', 'programma'=>'programmi/listaA.pdf'],
  ['id'=>2,'tipo'=>'sindaco','posizione'=>1,'nome'=>'Giulia Neri','simbolo'=>'simboli/listaB.png', 'programma'=>'programmi/listaB.pdf', 'cv'=>'cv/neri_cv.pdf', 'certificato'=>'certificati/neri_cert.pdf'],
  ['id'=>3,'tipo'=>'uninominale','posizione'=>1,'nome'=>'Anna Verdi','simbolo'=>'simboli/listaC.png', 'programma'=>'programmi/listaC.pdf']
];

// Filtra candidati per tipo
$candidati_filtrati = array_filter($candidati, fn($c) => $c['tipo'] === $tipo_candidato);

// Funzione per verifica duplicati posizione (escludendo id modificato)
function posizioneDuplicata($posizione, $id = null, $tipo = null) {
    global $candidati;
    foreach($candidati as $c) {
        if ($tipo !== null && $c['tipo'] !== $tipo) continue;
        if ($c['posizione'] == $posizione && $c['id'] != $id) {
            return true;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $posizione = isset($_POST['posizione']) ? intval($_POST['posizione']) : 0;
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $candidato_id = isset($_POST['candidato_id']) ? intval($_POST['candidato_id']) : null;
    $tipo = $_POST['tipo_candidato'] ?? 'presidente';

    if ($posizione < 0 || $posizione > 99) {
        $errors[] = "La posizione deve essere un numero tra 0 e 99.";
    } elseif (posizioneDuplicata($posizione, $candidato_id, $tipo)) {
        $errors[] = "La posizione $posizione è già assegnata ad un altro candidato di questo tipo.";
    }

    if ($nome === '') {
        $errors[] = "Il campo Nome e Cognome è obbligatorio.";
    }

    // Simbolo upload (img max 300x300)
    if (isset($_FILES['simbolo']) && $_FILES['simbolo']['error'] !== UPLOAD_ERR_NO_FILE) {
        // gestione come prima...
    }

    // Programma upload (pdf)
    if (isset($_FILES['programma']) && $_FILES['programma']['error'] !== UPLOAD_ERR_NO_FILE) {
        // gestione come prima...
    }

    // Solo se sindaco: cv e certificato upload (pdf)
    if ($tipo === 'sindaco') {
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
            // controlla pdf e salva
        }
        if (isset($_FILES['certificato']) && $_FILES['certificato']['error'] !== UPLOAD_ERR_NO_FILE) {
            // controlla pdf e salva
        }
    }

    if (!empty($errors)) {
        echo "<div class='alert alert-danger'><ul>";
        foreach ($errors as $e) {
            echo "<li>" . htmlspecialchars($e) . "</li>";
        }
        echo "</ul></div>";
    } else {
        echo "<div class='alert alert-success'>Salvataggio completato con successo.</div>";
        // Logica inserimento/aggiornamento qui
    }
}

// Ordina la lista filtrata per posizione
usort($candidati_filtrati, fn($a, $b) => $a['posizione'] <=> $b['posizione']);

?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-user-tie"></i> Candidati <?= htmlspecialchars(ucfirst($tipo_candidato)) ?></h2>

    <div class="card mb-4" id="formCandidatoCard">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Aggiungi / Modifica Candidato</h3>
      </div>
      <div class="card-body">
        <form id="formCandidato" method="post" enctype="multipart/form-data">
          <input type="hidden" id="candidato_id" name="candidato_id">
          <input type="hidden" name="tipo_candidato" value="<?= htmlspecialchars($tipo_candidato) ?>">

          <div class="form-row" style="align-items:center; gap:0.5rem;">
            <div class="form-group" style="flex: 0 0 80px; margin-bottom:0;">
              <label for="posizione" style="font-weight:600; font-size:0.9rem;">Posizione*</label>
              <input type="number" id="posizione" name="posizione" min="0" max="99" step="1" class="form-control" required style="padding: 0.375rem 0.5rem; font-size:0.95rem; text-align:center;">
            </div>
            <div class="form-group flex-grow-1" style="margin-bottom:0;">
              <label for="nome" style="font-weight:600; font-size:0.9rem;">Nome e Cognome*</label>
              <input type="text" id="nome" name="nome" class="form-control" required style="font-size:0.95rem; padding: 0.375rem 0.75rem;">
            </div>
            <div class="form-group" style="flex: 0 0 130px; margin-bottom:0;">
              <label for="simbolo" style="font-weight:600; font-size:0.85rem;">Simbolo<br><small>(max 300x300)</small></label>
              <input type="file" id="simbolo" name="simbolo" class="form-control-file" accept="image/*" style="font-size:0.85rem;">
            </div>
            <div class="form-group" style="flex: 0 0 130px; margin-bottom:0;">
              <label for="programma" style="font-weight:600; font-size:0.85rem;">Programma<br><small>(PDF)</small></label>
              <input type="file" id="programma" name="programma" class="form-control-file" accept="application/pdf" style="font-size:0.85rem;">
            </div>
          </div>

          <?php if ($tipo_candidato === 'sindaco'): ?>
          <div class="form-row mt-2" style="display:flex; gap:0.5rem;">
            <div class="form-group flex-fill" style="margin-bottom:0;">
              <label for="cv" style="font-weight:600; font-size:0.85rem;">Curriculum Vitae<br><small>(PDF)</small></label>
              <input type="file" id="cv" name="cv" class="form-control-file" accept="application/pdf" style="font-size:0.85rem;">
            </div>
            <div class="form-group flex-fill" style="margin-bottom:0;">
              <label for="certificato" style="font-weight:600; font-size:0.85rem;">Certificato Penale<br><small>(PDF)</small></label>
              <input type="file" id="certificato" name="certificato" class="form-control-file" accept="application/pdf" style="font-size:0.85rem;">
            </div>
          </div>
          <?php endif; ?>

          <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">Salva</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Lista Candidati</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle" style="font-size: 0.95rem;">
          <thead class="table-light">
            <tr>
              <th style="width:80px; text-align:center;">Posizione</th>
              <th>Nome e Cognome</th>
              <th style="text-align:center;">Simbolo</th>
              <th style="text-align:center;">Programma</th>
              <?php if ($tipo_candidato === 'sindaco'): ?>
                <th style="text-align:center;">Curriculum Vitae</th>
                <th style="text-align:center;">Certificato Penale</th>
              <?php endif; ?>
              <th style="text-align:center;">Azioni</th>
            </tr>
          </thead>
          <tbody id="tabellaCandidati">
            <!-- La tabella sarà popolata via JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script>
// Passa i dati PHP a JS
let candidati = <?= json_encode(array_values($candidati_filtrati), JSON_UNESCAPED_SLASHES) ?>;
const tipo_candidato = <?= json_encode($tipo_candidato) ?>;

const form = document.getElementById('formCandidato');
const btnSalva = form.querySelector('button[type="submit"]');

// Crea il bottone Annulla dinamicamente (ma nascosto inizialmente)
let btnAnnulla = document.createElement('button');
btnAnnulla.type = 'button';
btnAnnulla.textContent = 'Annulla';
btnAnnulla.className = 'btn btn-secondary ms-2'; // margine a sinistra per distanziare
btnAnnulla.style.display = 'none';
btnSalva.parentNode.appendChild(btnAnnulla);

// Funzione per pulire e resettare il form a "Nuovo inserimento"
function resetForm() {
  form.reset();
  document.getElementById('candidato_id').value = '';

  // Calcola la posizione max + 1
  let maxPos = 0;
  candidati.forEach(c => {
    if (c.posizione > maxPos) maxPos = c.posizione;
  });
  document.getElementById('posizione').value = maxPos + 1;

  btnSalva.textContent = 'Salva';
  btnAnnulla.style.display = 'none';
}



// Funzione per sanificare testo html (per sicurezza)
function escapeHtml(text) {
  if (!text) return '';
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Funzione per popolare la tabella
function popolaTabella() {
  const tbody = document.getElementById('tabellaCandidati');
  tbody.innerHTML = ''; // svuota

  candidati.sort((a,b) => a.posizione - b.posizione);

  candidati.forEach(c => {
    const tr = document.createElement('tr');

    tr.innerHTML = `
      <td style="text-align:center;">${c.posizione}</td>
      <td>${escapeHtml(c.nome)}</td>
      <td style="text-align:center;">${c.simbolo ? `<img src="${escapeHtml(c.simbolo)}" alt="Simbolo" style="height:30px;">` : ''}</td>
      <td style="text-align:center;">${c.programma ? `<a href="${escapeHtml(c.programma)}" target="_blank"><i class="fas fa-file-pdf text-danger"></i> PDF</a>` : ''}</td>
      ${tipo_candidato === 'sindaco' ? `
      <td style="text-align:center;">${c.cv ? `<a href="${escapeHtml(c.cv)}" target="_blank"><i class="fas fa-file-pdf text-danger"></i> PDF</a>` : ''}</td>
      <td style="text-align:center;">${c.certificato ? `<a href="${escapeHtml(c.certificato)}" target="_blank"><i class="fas fa-file-pdf text-danger"></i> PDF</a>` : ''}</td>
      ` : ''}
      <td style="text-align:center;">
        <button class="btn btn-sm btn-primary" onclick='modifica(${JSON.stringify(c)})'>
          <i class="fas fa-edit"></i> Modifica
        </button>
        <button class="btn btn-sm btn-danger" onclick="confermaEliminazione(${c.id}, '${c.nome.replace(/'/g, "\\'")}')">
          <i class="fas fa-trash"></i> Elimina
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// Carica dati candidato nel form per modifica
function modifica(candidato) {
  document.getElementById('candidato_id').value = candidato.id;
  document.getElementById('posizione').value = candidato.posizione;
  document.getElementById('nome').value = candidato.nome;
  // File non precompilabili, da ricaricare manualmente se necessario
  document.getElementById('simbolo').value = '';
  document.getElementById('programma').value = '';
  if(tipo_candidato === 'sindaco'){
    document.getElementById('cv').value = '';
    document.getElementById('certificato').value = '';
  }

  btnSalva.textContent = 'Salva Modifica';
  btnAnnulla.style.display = 'inline-block';

  // Scroll fluido fino al titolo <h2>
  const titolo = document.querySelector('h2');
  if(titolo) {
    titolo.scrollIntoView({behavior: 'smooth'});
  }
}

// Al click annulla, resetta il form
btnAnnulla.addEventListener('click', () => {
  resetForm();

  // Scroll in cima (opzionale)
  const titolo = document.querySelector('h2');
  if(titolo) {
    titolo.scrollIntoView({behavior: 'smooth'});
  }
});

// Conferma eliminazione
function confermaEliminazione(id, nome) {
  if(confirm(`Sei sicuro di voler eliminare il candidato "${nome}"?`)) {
    eliminaCandidato(id);
  }
}

// Funzione per eliminare candidato lato client e fare POST di eliminazione
function eliminaCandidato(id) {
  // Rimuove lato client
  candidati = candidati.filter(c => c.id !== id);
  popolaTabella();

  // Esegue chiamata POST per eliminazione lato server
  fetch('tuo_script_php.php', {  // cambia con lo script PHP giusto
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({azione: 'elimina', id_candidato: id})
  })
  .then(r => r.text())
  .then(response => {
    console.log('Eliminazione server:', response);
  })
  .catch(e => console.error(e));
}

// Controllo posizione duplicata prima di inviare form
form.addEventListener('submit', function(event){
  const posizioneInput = document.getElementById('posizione').value.trim();
  const candidatoId = document.getElementById('candidato_id').value.trim();

  if(!posizioneInput) {
    alert('La posizione è obbligatoria.');
    event.preventDefault();
    return;
  }

  // Controlla se esiste già la posizione in un altro candidato (diverso da quello modificato)
  const duplicato = candidati.some(c => c.posizione == posizioneInput && c.id.toString() !== candidatoId);

  if(duplicato){
    alert(`La posizione ${posizioneInput} è già assegnata ad un altro candidato. Scegli un'altra posizione.`);
    event.preventDefault();
    return;
  }

  // Se arriva qui, posizione valida, si può proseguire col submit

  // Qui puoi aggiungere eventuale aggiornamento candidati lato client
  // oppure lasciare che il server aggiorni i dati e la pagina venga ricaricata
});

// Al caricamento pagina
document.addEventListener('DOMContentLoaded', () => {
  popolaTabella();
  resetForm(); // per impostare la posizione al caricamento
});
</script>


