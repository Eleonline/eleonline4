<?php
require_once '../includes/check_access.php';

// dati simulati
$_SESSION['candidati_filtrati'] = $_SESSION['candidati_filtrati'] ?? [
    ['id'=>1,'posizione'=>1,'nome'=>'Mario Rossi'],
    ['id'=>2,'posizione'=>2,'nome'=>'Luigi Bianchi'],
];
$candidati_filtrati = $_SESSION['candidati_filtrati'];

$_SESSION['liste'] = $_SESSION['liste'] ?? [
    ['id'=>1, 'posizione'=>1, 'denominazione'=>'Lista A', 'simbolo'=>'', 'candidato_uninominale'=>1, 'link_lista'=>'https://example.com/a'],
    ['id'=>2, 'posizione'=>2, 'denominazione'=>'Lista B', 'simbolo'=>'', 'candidato_uninominale'=>2, 'link_lista'=>'https://example.com/b'],
];
$liste = $_SESSION['liste'];

// ✅ AJAX delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_delete'])) {
    $id_del = intval($_POST['ajax_delete']);
    foreach ($liste as $k => $l) {
        if ($l['id'] == $id_del) {
            unset($liste[$k]);
            break;
        }
    }
    $_SESSION['liste'] = array_values($liste);
    echo json_encode(['success' => true]);
    exit;
}

// POST inserimento/modifica
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_delete'])) {
    $id = $_POST['lista_id'] ?? '';
    $posizione = intval($_POST['posizione'] ?? 0);
    $denominazione = trim($_POST['denominazione'] ?? '');
    $candidato_uninominale = $_POST['candidato_uninominale'] ?? '';
    $link_lista = trim($_POST['link_lista'] ?? '');

    $posizione_duplicata = false;
    foreach ($liste as $l) {
        if ($l['posizione'] == $posizione && $l['id'] != $id) {
            $posizione_duplicata = true;
            break;
        }
    }

    if ($posizione_duplicata) {
        echo "<script>alert('Errore: Posizione già assegnata ad un\'altra lista.'); window.history.back();</script>";
        exit;
    }

    if ($posizione > 0 && $denominazione && ($tipo_consultazione === 'europee' || $candidato_uninominale)) {
        $simbolo_path = '';
        if (!empty($_FILES['simbolo']['tmp_name'])) {
            $fileTmp = $_FILES['simbolo']['tmp_name'];
            $fileName = basename($_FILES['simbolo']['name']);
            $uploadDir = 'uploads/simboli_liste/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $targetFile = $uploadDir . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
            $imageSize = getimagesize($fileTmp);
            if ($imageSize && $imageSize[0] <= 300 && $imageSize[1] <= 300) {
                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $simbolo_path = $targetFile;
                }
            }
        }

        if ($id !== '') {
            foreach ($liste as &$l) {
                if ($l['id'] == $id) {
                    $l['posizione'] = $posizione;
                    $l['denominazione'] = $denominazione;
                    if ($simbolo_path) $l['simbolo'] = $simbolo_path;
                    $l['candidato_uninominale'] = $candidato_uninominale;
                    $l['link_lista'] = $link_lista;
                }
            }
            unset($l);
        } else {
            $new_id = $liste ? max(array_column($liste, 'id')) + 1 : 1;
            $liste[] = [
                'id'=>$new_id,
                'posizione'=>$posizione,
                'denominazione'=>$denominazione,
                'simbolo'=>$simbolo_path,
                'candidato_uninominale'=>$candidato_uninominale,
                'link_lista'=>$link_lista,
            ];
        }

        $_SESSION['liste'] = $liste;
        header('Location: carica_lista.php');
        exit;
    }
}
?>

<?php
$show_candidato = true;
$label_candidato = 'Candidato';
if ($tipo_consultazione === 'europee') {
    $show_candidato = false;
} elseif ($tipo_consultazione === 'regionali') {
    $label_candidato = 'Candidato Presidente';
} elseif (in_array($tipo_consultazione, ['camera', 'senato'])) {
    $label_candidato = 'Candidato Uninominale';
} elseif ($tipo_consultazione === 'comunali') {
    $label_candidato = 'Candidato Sindaco';
}
$elementi = $liste;
?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-list"></i> Gestione Liste</h2>

    <!-- Form -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Aggiungi / Modifica Lista</h3>
      </div>
      <div class="card-body">
        <form id="formLista" method="post" enctype="multipart/form-data">
          <input type="hidden" id="lista_id" name="lista_id">
          <div class="form-row" style="align-items:center; gap:0.5rem;">
            <div class="form-group" style="flex:0 0 80px;">
              <label>Posizione*</label>
              <input type="number" name="posizione" id="posizione" class="form-control" min="1" required>
            </div>
            <div class="form-group flex-grow-1">
              <label>Denominazione Lista*</label>
              <input type="text" name="denominazione" id="denominazione" class="form-control" required>
            </div>
            <div class="form-group" style="flex:0 0 130px;">
              <label>Simbolo<br><small>(max 300×300)</small></label>
              <input type="file" id="simbolo" name="simbolo" accept="image/*" style="display:none;" onchange="document.getElementById('simbolo-label').textContent = this.files[0]?.name || '';">
              <button type="button" onclick="document.getElementById('simbolo').click();" class="btn btn-secondary btn-sm">Scegli file</button>
              <span id="simbolo-label" style="margin-left:8px;">Nessun file scelto</span>
            </div>
            <?php if ($show_candidato): ?>
            <div class="form-group flex-grow-1">
              <label><?= htmlspecialchars($label_candidato) ?>*</label>
              <select name="candidato_uninominale" id="candidato_uninominale" class="form-control" required>
                <option value="">-- Seleziona candidato --</option>
                <?php foreach($candidati_filtrati as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['posizione'] . ' - ' . $c['nome']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php endif; ?>
            <div class="form-group flex-grow-1">
              <label>Link sito lista</label>
              <input type="url" name="link_lista" id="link_lista" class="form-control" placeholder="https://">
            </div>
          </div>
          <div class="form-group mt-2">
            <button type="submit" class="btn btn-success" id="btnSalva">Salva</button>
            <button type="button" class="btn btn-secondary" onclick="resetForm()">Annulla</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabella -->
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Elenco Liste</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th style="width:80px;">Posizione</th>
              <th>Denominazione</th>
              <th style="text-align:center;">Simbolo</th>
              <th><?= htmlspecialchars($label_candidato) ?></th>
              <th>Link</th>
              <th style="text-align:center;">Azioni</th>
            </tr>
          </thead>
          <tbody id="righeElementi">
            <!-- Qui riempito da JS -->
          </tbody>
        </table>

        <!-- Paginazione -->
        <nav>
          <ul class="pagination justify-content-center" id="paginazione"></ul>
        </nav>
      </div>
    </div>
  </div>
</section>

<script>
  // Dati lato client da PHP
  let elementi = <?php echo json_encode($elementi); ?>;
  let candidati = <?php echo json_encode($candidati_filtrati); ?>;

  // Stato
  let indiceModifica = null;
  const elementiPerPagina = 5;
  let paginaCorrente = 1;
  let totalePagine = Math.ceil(elementi.length / elementiPerPagina);

  // Ordina per posizione e id
  function ordinaElementi() {
    elementi.sort((a, b) => {
      if (a.posizione === b.posizione) return a.id - b.id;
      return a.posizione - b.posizione;
    });
  }

  // Aggiorna tabella e paginazione
  function aggiornaTabella() {
    ordinaElementi();

    const tbody = document.getElementById("righeElementi");
    tbody.innerHTML = "";

    totalePagine = Math.ceil(elementi.length / elementiPerPagina);
    if (paginaCorrente > totalePagine) paginaCorrente = totalePagine || 1;

    const start = (paginaCorrente - 1) * elementiPerPagina;
    const end = start + elementiPerPagina;
    const elementiPagina = elementi.slice(start, end);

    elementiPagina.forEach((el, i) => {
      // Trovo nome candidato
      let nomeCandidato = '';
      for (let c of candidati) {
        if (c.id == el.candidato_uninominale) {
          nomeCandidato = c.posizione + ' - ' + c.nome;
          break;
        }
      }

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td style="text-align:center;">${el.posizione}</td>
        <td>${el.denominazione}</td>
        <td style="text-align:center;">${el.simbolo ? '<img src="' + el.simbolo + '" style="height:30px;">' : ''}</td>
        <td>${nomeCandidato}</td>
        <td>${el.link_lista ? '<a href="' + el.link_lista + '" target="_blank">Sito</a>' : ''}</td>
        <td style="text-align:center;">
          <button class="btn btn-sm btn-warning me-1" onclick="modificaElemento(${start + i})">Modifica</button>
          <button class="btn btn-sm btn-danger" onclick="mostraConfermaEliminazione(${start + i})">Elimina</button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    aggiornaPaginazione();
  }

  // Aggiorna paginazione
  function aggiornaPaginazione() {
    const ul = document.getElementById("paginazione");
    ul.innerHTML = "";

	if (totalePagine < 2) {
    return; // Non mostra niente
  }

    const liPrev = document.createElement("li");
    liPrev.className = `page-item ${paginaCorrente === 1 ? "disabled" : ""}`;
    liPrev.innerHTML = `<a class="page-link" href="#" onclick="cambiaPagina(${paginaCorrente - 1}); return false;">&laquo;</a>`;
    ul.appendChild(liPrev);

    for (let i = 1; i <= totalePagine; i++) {
      const li = document.createElement("li");
      li.className = `page-item ${paginaCorrente === i ? "active" : ""}`;
      li.innerHTML = `<a class="page-link" href="#" onclick="cambiaPagina(${i}); return false;">${i}</a>`;
      ul.appendChild(li);
    }

    const liNext = document.createElement("li");
    liNext.className = `page-item ${paginaCorrente === totalePagine ? "disabled" : ""}`;
    liNext.innerHTML = `<a class="page-link" href="#" onclick="cambiaPagina(${paginaCorrente + 1}); return false;">&raquo;</a>`;
    ul.appendChild(liNext);
  }

  // Cambia pagina
  function cambiaPagina(num) {
    if (num < 1 || num > totalePagine) return;
    paginaCorrente = num;
    aggiornaTabella();
  }

  // Mostra conferma eliminazione
 // Mostra conferma eliminazione con nome lista
function mostraConfermaEliminazione(idx) {
  const nomeLista = elementi[idx].denominazione || 'questa lista';
  if (confirm(`Sei sicuro di voler eliminare la lista "${nomeLista}"?`)) {
    eliminaElemento(idx);
  }
}


  // Elimina elemento
  function eliminaElemento(idx) {
    elementi.splice(idx, 1);
    if (indiceModifica === idx) {
      resetForm();
    }
    aggiornaTabella();
  }

  // Modifica elemento
 function modificaElemento(idx) {
  indiceModifica = idx;
  const el = elementi[idx];

  // Imposta i valori del form con quelli dell'elemento da modificare
  document.getElementById('lista_id').value = el.id;
  document.getElementById('posizione').value = el.posizione;
  document.getElementById('denominazione').value = el.denominazione;
  document.getElementById('link_lista').value = el.link_lista || '';
  if ('candidato_uninominale' in el) {
    document.getElementById('candidato_uninominale').value = el.candidato_uninominale || '';
  }
  // Reset label file simbolo
  document.getElementById('simbolo-label').textContent = 'Nessun file scelto';

  // Cambia testo bottone Salva in "Salva modifica"
  document.getElementById('btnSalva').textContent = 'Salva modifica';

  // Scrolla in cima al form (opzionale)
  window.scrollTo({top: 0, behavior: 'smooth'});
}


  // Reset form
 function resetForm() {
  indiceModifica = null;
  document.getElementById('formLista').reset();
  document.getElementById('lista_id').value = '';
  document.getElementById('simbolo-label').textContent = 'Nessun file scelto';

  // Riporta testo bottone a "Salva"
  document.getElementById('btnSalva').textContent = 'Salva';
}


  // Gestione submit form: aggiorna array o aggiunge nuovo
  document.getElementById("formLista").addEventListener("submit", function(e) {
    e.preventDefault();

    const id = document.getElementById("lista_id").value;
    const posizione = parseInt(document.getElementById("posizione").value, 10);
    const denominazione = document.getElementById("denominazione").value.trim();
    const link_lista = document.getElementById("link_lista").value.trim();

    let candidato_uninominale = null;
    if (document.getElementById("candidato_uninominale")) {
      candidato_uninominale = document.getElementById("candidato_uninominale").value;
    }

    // Per simbolo, in demo teniamo solo nome file (nel reale fare upload)
    let simbolo = document.getElementById("simbolo-label").textContent !== "Nessun file scelto" ? document.getElementById("simbolo-label").textContent : null;

    if (indiceModifica !== null) {
      // Modifica
      elementi[indiceModifica].posizione = posizione;
      elementi[indiceModifica].denominazione = denominazione;
      elementi[indiceModifica].link_lista = link_lista;
      elementi[indiceModifica].simbolo = simbolo;
      elementi[indiceModifica].candidato_uninominale = candidato_uninominale;
    } else {
      // Nuovo id incrementale
      const nuovoId = elementi.length > 0 ? Math.max(...elementi.map(e => e.id)) + 1 : 1;

      elementi.push({
        id: nuovoId,
        posizione: posizione,
        denominazione: denominazione,
        link_lista: link_lista,
        simbolo: simbolo,
        candidato_uninominale: candidato_uninominale
      });
    }

    resetForm();
    aggiornaTabella();
  });

  // Inizializzo tabella e paginazione
  aggiornaTabella();

</script>
