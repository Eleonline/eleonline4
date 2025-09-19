<?php require_once '../includes/check_access.php'; ?>
<?php 
require_once '../../client/temi/bootstrap/pagine/config_colori_quesiti.php';

if (!isset($_SESSION['quesiti'])) $_SESSION['quesiti'] = [];
$quesiti =& $_SESSION['quesiti'];

function uploadPdfFile($file) {
    $uploadDir = __DIR__ . '/uploads/quesiti/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = basename($file['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext !== 'pdf') return [false, "Solo file PDF sono consentiti."];

    $newName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
    $targetPath = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $relativePath = 'uploads/quesiti/' . $newName;
        return [true, $relativePath];
    } else {
        return [false, "Errore nel caricamento del file."];
    }
}

$messaggio = '';
$messaggioClass = '';

if (isset($_POST['azione'])) {
    if ($_POST['azione'] === 'aggiungi') {
        $numero = $_POST['numero'] ?? null;
        $denominazione = trim($_POST['denominazione'] ?? '');
        $colore_id = $_POST['colore'] ?? null;
        $pdfPath = '';

        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            list($ok, $result) = uploadPdfFile($_FILES['pdf_file']);
            if ($ok) {
                $pdfPath = $result;
            } else {
                $messaggio = $result;
                $messaggioClass = 'alert-danger';
            }
        }

        if ($numero && $denominazione && isset($coloriQuesiti[$colore_id])) {
            $quesiti[] = [
                'numero' => (int)$numero,
                'denominazione' => $denominazione,
                'colore_id' => $colore_id,
                'pdf' => $pdfPath,
            ];
            $messaggio = "Quesito aggiunto con successo.";
            $messaggioClass = 'alert-success';
        } elseif (!$messaggio) {
            $messaggio = "Compila tutti i campi e seleziona un colore valido.";
            $messaggioClass = 'alert-warning';
        }
    } elseif ($_POST['azione'] === 'modifica') {
        $indice = (int)($_POST['indice'] ?? -1);
        if (isset($quesiti[$indice])) {
            $numero = $_POST['numero'] ?? null;
            $denominazione = trim($_POST['denominazione'] ?? '');
            $colore_id = $_POST['colore'] ?? null;
            $pdfPath = $quesiti[$indice]['pdf']; // manteniamo pdf vecchio se non cambia

            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
                // Carica nuovo PDF e cancella vecchio
                list($ok, $result) = uploadPdfFile($_FILES['pdf_file']);
                if ($ok) {
                    // cancella vecchio file se esiste
                    if (!empty($pdfPath)) {
                        $fileToDelete = __DIR__ . '/' . $pdfPath;
                        if (file_exists($fileToDelete)) unlink($fileToDelete);
                    }
                    $pdfPath = $result;
                } else {
                    $messaggio = $result;
                    $messaggioClass = 'alert-danger';
                }
            }

            if ($numero && $denominazione && isset($coloriQuesiti[$colore_id])) {
                $quesiti[$indice] = [
                    'numero' => (int)$numero,
                    'denominazione' => $denominazione,
                    'colore_id' => $colore_id,
                    'pdf' => $pdfPath,
                ];
                $messaggio = "Quesito modificato.";
                $messaggioClass = 'alert-success';
            } elseif (!$messaggio) {
                $messaggio = "Compila tutti i campi validi per la modifica.";
                $messaggioClass = 'alert-warning';
            }
        }
    }
}

if (isset($_GET['elimina'])) {
    $indice = (int)$_GET['elimina'];
    if (isset($quesiti[$indice])) {
        if (!empty($quesiti[$indice]['pdf'])) {
            $fileToDelete = __DIR__ . '/' . $quesiti[$indice]['pdf'];
            if (file_exists($fileToDelete)) unlink($fileToDelete);
        }
        unset($quesiti[$indice]);
        $quesiti = array_values($quesiti);
        $messaggio = "Quesito eliminato.";
        $messaggioClass = 'alert-success';
    }
}

if (isset($_POST['indice']) && isset($_GET['ajax_elimina'])) {
    $indice = (int)$_POST['indice'];
    $response = ['success' => false, 'message' => 'Indice non valido.'];
    if (isset($quesiti[$indice])) {
        if (!empty($quesiti[$indice]['pdf'])) {
            $fileToDelete = __DIR__ . '/' . $quesiti[$indice]['pdf'];
            if (file_exists($fileToDelete)) unlink($fileToDelete);
        }
        unset($quesiti[$indice]);
        $quesiti = array_values($quesiti);
        $_SESSION['quesiti'] = $quesiti; // aggiorna sessione
        $response = ['success' => true];
    } else {
        $response['message'] = 'Quesito non trovato.';
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Variabile per passare i quesiti a JS per caricamento in form
$quesitiJSON = json_encode($quesiti);
// Calcolo numero automatico: massimo numero + 1
$maxNumero = 0;
foreach ($quesiti as $q) {
    if (isset($q['numero']) && $q['numero'] > $maxNumero) {
        $maxNumero = (int)$q['numero'];
    }
}
$numeroAutomatico = $maxNumero + 1;

?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cogs me-2"></i>Gestione Quesiti Referendari</h3>
      </div>
      <div class="card-body">

        <?php if ($messaggio): ?>
          <div class="alert <?= $messaggioClass ?>" role="alert"><?= htmlspecialchars($messaggio) ?></div>
        <?php endif; ?>

        <!-- FORM INSERIMENTO / MODIFICA -->
        <form id="formQuesito" method="POST" enctype="multipart/form-data" class="mb-4">
          <input type="hidden" name="azione" id="azione" value="aggiungi">
          <input type="hidden" name="indice" id="indice" value="">
          <div class="row g-2 align-items-start">
            <div class="col-md-1">
              <label>Numero</label>
              <input type="number" name="numero" id="numero" class="form-control" required value="<?= $numeroAutomatico ?>">
            </div>
            <div class="col-md-4">
  <label>Denominazione</label>
  <textarea name="denominazione" id="denominazione" class="form-control" required
    style="height: 150px;"></textarea>
</div>
            <div class="col-md-3">
              <label>Colore Scheda</label>
              <select name="colore" id="colore" class="form-control" onchange="mostraAnteprima(this.value)" required>
                <option value="">Seleziona...</option>
                <?php foreach ($coloriQuesiti as $id => $col): ?>
                  <option value="<?= $id ?>"><?= htmlspecialchars($col['nome']) ?></option>
                <?php endforeach; ?>
              </select>
              <div id="anteprima" class="border p-1 text-center mt-2" style="height: 70px;">
                <img src="" id="imgScheda" style="max-height:60px;" alt="Anteprima scheda" />
              </div>
            </div>
            <div class="col-md-3">
              <label>Fac-Simile (Solo file PDF)</label>
              <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf" class="form-control">
              <div id="pdfPreview" class="mt-1 text-center" style="font-size: 0.9em; color:#555;"></div>
            </div>
            <div class="col-md-1 d-flex align-items-end flex-column">
              <button type="submit" id="btnSubmit" class="btn btn-success w-100 mb-1">Aggiungi</button>
              <button type="button" id="btnAnnulla" class="btn btn-secondary w-100" style="display:none;" onclick="annullaModifica()">Annulla</button>
            </div>
          </div>
        </form>

        <table class="table table-bordered table-sm text-center align-middle">
          <thead class="table-secondary">
            <tr>
              <th style="width:5%;">#</th>
              <th style="width:30%;">Denominazione</th>
              <th style="width:20%;">Colore Scheda</th>
              <th style="width:20%;">Fac-Simile (PDF)</th>
              <th style="width:15%;">Azioni</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($quesiti as $i => $q): ?>
              <tr>
                <td><?= (int)$q['numero'] ?></td>
                <td><?= htmlspecialchars($q['denominazione']) ?></td>
                <td style="background-color: <?= isset($q['colore_id']) && isset($coloriQuesiti[$q['colore_id']]) ? $coloriQuesiti[$q['colore_id']]['colore'] : 'transparent' ?>">
                  <strong><?= isset($q['colore_id']) && isset($coloriQuesiti[$q['colore_id']]) ? htmlspecialchars($coloriQuesiti[$q['colore_id']]['nome']) : '' ?></strong><br>
                  <img src="../../client/temi/bootstrap/pagine/<?= isset($q['colore_id']) && isset($coloriQuesiti[$q['colore_id']]) ? $coloriQuesiti[$q['colore_id']]['immagine'] : 'default.png' ?>" style="max-height:60px;" alt="Immagine scheda">
                </td>
                <td class="align-middle">
                  <?php if (!empty($q['pdf']) && file_exists(__DIR__ . '/' . $q['pdf'])): ?>
                    <a href="<?= htmlspecialchars($q['pdf']) ?>" target="_blank" class="btn btn-sm btn-primary">Visualizza PDF</a>
                  <?php else: ?>
                    <span class="text-muted">Nessun PDF</span>
                  <?php endif; ?>
                </td>
                <td>
                  <button class="btn btn-sm btn-warning me-1" onclick="modificaQuesito(<?= $i ?>)" title="Modifica"><i class="fas fa-edit"></i></button>
<button class="btn btn-sm btn-danger btn-elimina" data-indice="<?= $i ?>" title="Elimina"><i class="fas fa-trash"></i></button>

                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($quesiti)): ?>
              <tr><td colspan="5" class="text-muted">Nessun quesito presente.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</section>

<script>
  // Dati quesiti dal PHP in JS
  const quesiti = <?= $quesitiJSON ?>;
  const coloriQuesiti = <?= json_encode($coloriQuesiti) ?>;

function mostraAnteprima(idColore) {
  const imgScheda = document.getElementById('imgScheda');
  const anteprima = document.getElementById('anteprima');
  if (idColore && coloriQuesiti[idColore]) {
    imgScheda.src = '../../client/temi/bootstrap/pagine/' + coloriQuesiti[idColore].immagine;
    anteprima.style.backgroundColor = coloriQuesiti[idColore].colore || 'transparent';
  } else {
    imgScheda.src = '';
    anteprima.style.backgroundColor = 'transparent';
  }
}


function modificaQuesito(indice) {
  const q = quesiti[indice];
  if (!q) return alert("Quesito non trovato.");

  document.getElementById('numero').value = q.numero;
  document.getElementById('denominazione').value = q.denominazione;
  document.getElementById('colore').value = q.colore_id;
  mostraAnteprima(q.colore_id);
  document.getElementById('indice').value = indice;
  document.getElementById('azione').value = 'modifica';
  document.getElementById('btnSubmit').textContent = 'Modifica';
  document.getElementById('btnAnnulla').style.display = 'block';
}

function annullaModifica() {
  document.getElementById('numero').value = <?= $numeroAutomatico ?>;
  document.getElementById('denominazione').value = '';
  document.getElementById('colore').value = '';
  mostraAnteprima('');
  document.getElementById('indice').value = '';
  document.getElementById('azione').value = 'aggiungi';
  document.getElementById('btnSubmit').textContent = 'Aggiungi';
  document.getElementById('btnAnnulla').style.display = 'none';
  document.getElementById('pdf_file').value = '';
  document.getElementById('pdfPreview').textContent = '';
}

document.querySelectorAll('.btn-elimina').forEach(btn => {
  btn.addEventListener('click', function() {
    if (!confirm('Sei sicuro di voler eliminare questo quesito?')) return;
    const indice = this.getAttribute('data-indice');

    fetch('?ajax_elimina=1', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'indice=' + encodeURIComponent(indice)
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Quesito eliminato con successo.');
        location.reload();
      } else {
        alert('Errore: ' + data.message);
      }
    })
    .catch(() => alert('Errore nella comunicazione con il server.'));
  });
});

document.getElementById('pdf_file').addEventListener('change', function() {
  const file = this.files[0];
  const preview = document.getElementById('pdfPreview');
  if (file && file.type === 'application/pdf') {
    preview.textContent = 'File selezionato: ' + file.name;
  } else {
    preview.textContent = 'Seleziona un file PDF valido.';
    this.value = '';
  }
});


</script>

