<?php
require_once '../includes/check_access.php';

$currentUserRole = $_SESSION['ruolo'] ?? 'operatore';
$row=configurazione();
$predefinito=$row[0]['siteistat'];
$row=elenco_comuni();
foreach($row as $key=>$val) {
	if($predefinito===$val['id_comune']) $pred=true; else $pred=false;
$enti[]=['id'=>($key+1),'denominazione'=>$val['denominazione'],'codice_istat'=>$val['id_comune'],'capoluogo'=>$val['capoluogo'],'indirizzo'=>$val['indirizzo'],'abitanti'=>$val['fascia'],'fax'=>$val['fax'],'email'=>$val['email'],'cap'=>$val['cap'],'centralino'=>$val['centralino'],'stemma'=>$val['stemma'],'predefinito'=>$pred];
}
/*
$enti = [
  ['id'=>1, 'denominazione'=>'Comune A', 'codice_istat'=>'001', 'capoluogo'=>'Sì', 'indirizzo'=>'Via Roma 1', 'abitanti'=>'3000-10000', 'fax'=>'0123456789', 'email'=>'comuneA@pec.it', 'cap'=>'00100', 'centralino'=>'123456', 'stemma'=>'', 'predefinito' => true],
  ['id'=>2, 'denominazione'=>'Comune B', 'codice_istat'=>'002', 'capoluogo'=>'No', 'indirizzo'=>'Via Milano 2', 'abitanti'=>'10000-15000', 'fax'=>'9876543210', 'email'=>'comuneB@pec.it', 'cap'=>'00200', 'centralino'=>'654321', 'stemma'=>'', 'predefinito' => false],
  ['id'=>3, 'denominazione'=>'Comune C', 'codice_istat'=>'003', 'capoluogo'=>'Sì', 'indirizzo'=>'Via Napoli 3', 'abitanti'=>'15000-30000', 'fax'=>'0112233445', 'email'=>'comuneC@pec.it', 'cap'=>'00300', 'centralino'=>'112233', 'stemma'=>'', 'predefinito' => false],
]; */
//require_once '../includes/db_connection.php'; // Assumendo che qui apri la connessione $conn (mysqli)

// Aggiunta, modifica, eliminazione enti in MySQL

// --- ELIMINAZIONE ---
/*
if (isset($_GET['delete_ente_id'])) {
    $delete_id = intval($_GET['delete_ente_id']);
    // Eliminazione
    $sql_delete = "DELETE FROM enti WHERE id = ?";
    if ($stmt = $conn->prepare($sql_delete)) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            // messaggio eliminazione riuscita (gestisci come vuoi)
            $message = "Eliminazione ente ID $delete_id avvenuta con successo.";
        } else {
            $message = "Errore durante eliminazione ente.";
        }
        $stmt->close();
    }
}
*/

// --- INSERIMENTO O AGGIORNAMENTO ---
// Se invii il form con POST
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanifica input
    $id = isset($_POST['ente_id']) ? intval($_POST['ente_id']) : 0;
    $denominazione = $_POST['denominazione'] ?? '';
    $codice_istat = $_POST['codice_istat'] ?? '';
    $capoluogo = $_POST['capoluogo'] ?? '';
    $indirizzo = $_POST['indirizzo'] ?? '';
    $centralino = $_POST['centralino'] ?? '';
    $abitanti = $_POST['abitanti'] ?? '';
    $fax = $_POST['fax'] ?? '';
    $email = $_POST['email'] ?? '';
    $cap = $_POST['cap'] ?? '';
    $predefinito = isset($_POST['predefinito']) ? 1 : 0;

    // Gestisci upload file stemma se presente
    $stemma_path = '';
    if (isset($_FILES['stemma']) && $_FILES['stemma']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['stemma']['tmp_name'];
        $name = basename($_FILES['stemma']['name']);
        $upload_dir = '../uploads/'; // crea questa cartella e rendila scrivibile
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $target_file = $upload_dir . time() . '_' . $name;
        if (move_uploaded_file($tmp_name, $target_file)) {
            $stemma_path = $target_file;
        }
    }

    // Se predefinito, azzera gli altri
    if ($predefinito) {
        $conn->query("UPDATE enti SET predefinito = 0");
    }

    if ($id > 0) {
        // UPDATE
        $sql_update = "UPDATE enti SET denominazione=?, codice_istat=?, capoluogo=?, indirizzo=?, centralino=?, abitanti=?, fax=?, email=?, cap=?, stemma=?, predefinito=? WHERE id=?";
        if ($stmt = $conn->prepare($sql_update)) {
            // Se non hai cambiato il file, mantieni lo stemma vecchio
            if (!$stemma_path) {
                // Prendi stemma attuale dal DB per questo id
                $res = $conn->query("SELECT stemma FROM enti WHERE id=$id");
                if ($res && $row = $res->fetch_assoc()) {
                    $stemma_path = $row['stemma'];
                }
            }
            $stmt->bind_param("ssssssssssii", $denominazione, $codice_istat, $capoluogo, $indirizzo, $centralino, $abitanti, $fax, $email, $cap, $stemma_path, $predefinito, $id);
            if ($stmt->execute()) {
                $message = "Ente aggiornato con successo.";
            } else {
                $message = "Errore durante aggiornamento ente.";
            }
            $stmt->close();
        }
    } else {
        // INSERT
        $sql_insert = "INSERT INTO enti (denominazione, codice_istat, capoluogo, indirizzo, centralino, abitanti, fax, email, cap, stemma, predefinito) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql_insert)) {
            $stmt->bind_param("ssssssssssi", $denominazione, $codice_istat, $capoluogo, $indirizzo, $centralino, $abitanti, $fax, $email, $cap, $stemma_path, $predefinito);
            if ($stmt->execute()) {
                $message = "Ente aggiunto con successo.";
            } else {
                $message = "Errore durante inserimento ente.";
            }
            $stmt->close();
        }
    }
}
*/

// --- Recupero dati da DB (per esempio) ---
/*
$sql = "SELECT * FROM enti ORDER BY denominazione";
$result = $conn->query($sql);
$enti = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $enti[] = $row;
    }
}
*/
?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2 id="form-title"><i class="fas fa-building"></i> Gestione Enti/Comuni</h2>

    <!-- FORM -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="form-subtitle">Aggiungi Ente</h3>
      </div>
      <div class="card-body">
        <form id="enteForm" enctype="multipart/form-data">
          <input type="hidden" name="ente_id" id="ente_id">

          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="stemma">Stemma</label>
              <input type="file" class="form-control-file" id="stemma" accept="image/*">
              <img id="anteprimaStemma" src="" alt="Anteprima stemma" style="max-height: 80px; margin-top: 5px; display: none;">
            </div>
            <div class="form-group col-md-5">
              <label for="denominazione">Denominazione*</label>
              <input type="text" class="form-control" id="denominazione" name="denominazione" required>
            </div>
            <div class="form-group col-md-4">
              <label for="indirizzo">Indirizzo*</label>
              <input type="text" class="form-control" id="indirizzo" name="indirizzo" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-2">
              <label for="cap">CAP*</label>
              <input type="text" class="form-control" id="cap" name="cap" required>
            </div>
            <div class="form-group col-md-4">
              <label for="email">E-mail*</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group col-md-3">
              <label for="centralino">Centralino*</label>
              <input type="text" class="form-control" id="centralino" name="centralino" required>
            </div>
            <div class="form-group col-md-3">
              <label for="fax">Fax</label>
              <input type="text" class="form-control" id="fax" name="fax">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="codice_istat">Codice ISTAT*</label>
              <input type="text" class="form-control" id="codice_istat" name="codice_istat" required>
            </div>
            <div class="form-group col-md-4">
              <label for="abitanti">Abitanti*</label>
              <select class="form-control" id="abitanti" name="abitanti" required>
              <option value="">Seleziona...</option>
			  <?php
			  $row=elenco_fasce(1);
			  $i=1;
			  foreach($row as $key=>$val){
                echo "<option value=\"".$val['id_fascia']."\">".number_format($i,0,',','.')." - ".number_format(($val['abitanti']-1),0,',','.')."</option>";
				$i=$val['abitanti'];
				if($val['id_fascia']==8) break;
			  }
			  ?>
                <option value="9">Oltre 1.000.000</option>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label for="capoluogo">Capoluogo/Provincia*</label>
              <select class="form-control" id="capoluogo" name="capoluogo" required>
                <option value="">Seleziona...</option>
                <option value="Sì">Sì</option>
                <option value="No">No</option>
              </select>
            </div>
            <div class="form-group col-md-3 d-flex align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="predefinito" name="predefinito">
                <label class="form-check-label" for="predefinito">Ente predefinito</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-success" id="submitBtn">Aggiungi ente</button>
          <button type="reset" class="btn btn-secondary" id="cancelEdit">Annulla</button>
        </form>
      </div>
    </div>

    <!-- LISTA -->
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Lista Enti/Comuni</h3>
      </div>
      <div class="card-body table-responsive" style="max-height:400px; overflow-y:auto;">
        <table class="table table-bordered table-hover" id="entiTable">
          <thead>
            <tr>
              <th style="width: 40px;"></th>
              <th style="width: 60px;">Stemma</th>
              <th>Denominazione</th>
              <th>Indirizzo</th>
              <th>Abitanti</th>
              <th>Codice ISTAT</th>
              <th>Capoluogo/Provincia</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody id="enteRows"></tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script>
let enti = <?php echo json_encode($enti); ?>;

const enteForm = document.getElementById('enteForm');
const enteRows = document.getElementById('enteRows');
const formTitle = document.getElementById('form-subtitle');
const submitBtn = document.getElementById('submitBtn');
const formMainTitle = document.getElementById('form-title');

function renderEnti() {
  enteRows.innerHTML = '';
  enti.forEach(ente => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${ente.predefinito ? '<i class="fas fa-star" style="color:gold;"></i>' : '<i class="far fa-star"></i>'}</td>
      <td>${ente.stemma ? `<img src="${ente.stemma}" alt="Stemma" style="height:30px;">` : ''}</td>
      <td>${ente.denominazione}</td>
      <td>${ente.indirizzo}</td>
      <td>${ente.abitanti}</td>
      <td>${ente.codice_istat}</td>
      <td>${ente.capoluogo}</td>
      <td>
        <button class="btn btn-sm btn-warning" onclick="editEnte(${ente.id})">Modifica</button>
        <button class="btn btn-sm btn-danger" onclick="deleteEnte(${ente.id})">Elimina</button>
      </td>
    `;
    enteRows.appendChild(tr);
  });
}

function editEnte(id) {
  const e = enti.find(e => e.id === id);
  if (!e) return;

  document.getElementById('ente_id').value = e.id;
  document.getElementById('denominazione').value = e.denominazione;
  document.getElementById('codice_istat').value = e.codice_istat;
  document.getElementById('capoluogo').value = e.capoluogo;
  document.getElementById('indirizzo').value = e.indirizzo;
  document.getElementById('centralino').value = e.centralino;
  document.getElementById('abitanti').value = e.abitanti;
  document.getElementById('fax').value = e.fax;
  document.getElementById('email').value = e.email;
  document.getElementById('cap').value = e.cap;
  document.getElementById('anteprimaStemma').src = e.stemma || '';
  document.getElementById('anteprimaStemma').style.display = e.stemma ? 'block' : 'none';
  document.getElementById('predefinito').checked = e.predefinito;

  formTitle.innerText = 'Modifica Ente';
  submitBtn.innerText = 'Salva ente';

  formMainTitle.scrollIntoView({ behavior: 'smooth' });
}

function deleteEnte(id) {
  const enteToDelete = enti.find(e => e.id === id);
  if (!enteToDelete) return;

  if (enteToDelete.predefinito) {
    const altro = enti.find(e => e.id !== id);
    if (altro) {
      if (!confirm(`Stai eliminando l'ente predefinito "${enteToDelete.denominazione}". Verrà impostato come predefinito "${altro.denominazione}". Procedere?`)) {
        return;
      }
      altro.predefinito = true; // nuovo predefinito
    } else {
      if (!confirm(`Stai eliminando l'ente predefinito "${enteToDelete.denominazione}". Non ci sono altri enti disponibili. Procedere?`)) {
        return;
      }
    }
  } else {
    if (!confirm(`Sei sicuro di voler eliminare l'ente "${enteToDelete.denominazione}"?`)) {
      return;
    }
  }

  enti = enti.filter(e => e.id !== id);
  renderEnti();
  predefinitoAttuale = enti.find(e => e.predefinito);
  alert(`Eliminazione dell'ente "${enteToDelete.denominazione}" avvenuta con successo.`);
}

let predefinitoAttuale = enti.find(e => e.predefinito);

enteForm.addEventListener('submit', function(e){
  e.preventDefault();

  const id = parseInt(document.getElementById('ente_id').value);
  const predefinito = document.getElementById('predefinito').checked;
  const isModifica = !isNaN(id);
  const eraPredefinito = predefinitoAttuale && predefinitoAttuale.id === id;

  if (isModifica && eraPredefinito && !predefinito) {
    if (!confirm(`Stai rimuovendo lo stato di predefinito dall'ente "${predefinitoAttuale.denominazione}". Continuare?`)) {
      return;
    } else {
      // Se confermi la rimozione, assegno il predefinito al primo ente diverso da questo (se esiste)
      const altriEnti = enti.filter(e => e.id !== id);
      if (altriEnti.length > 0) {
        altriEnti.forEach(e => e.predefinito = false); // resetta tutti
        altriEnti[0].predefinito = true; // assegna al primo
        alert(`L'ente "${altriEnti[0].denominazione}" è stato impostato automaticamente come predefinito.`);
      } else {
        alert(`Non ci sono altri enti a cui assegnare lo stato di predefinito.`);
      }
    }
  }

  const newEnte = {
    id: id || (enti.length ? Math.max(...enti.map(e => e.id)) + 1 : 1),
    denominazione: document.getElementById('denominazione').value,
    codice_istat: document.getElementById('codice_istat').value,
    capoluogo: document.getElementById('capoluogo').value,
    indirizzo: document.getElementById('indirizzo').value,
    centralino: document.getElementById('centralino').value,
    abitanti: document.getElementById('abitanti').value,
    fax: document.getElementById('fax').value,
    email: document.getElementById('email').value,
    cap: document.getElementById('cap').value,
    stemma: document.getElementById('anteprimaStemma').src || '',
    predefinito: predefinito
  };

  if (predefinito) {
    enti.forEach(e => e.predefinito = false);
  }

  if (isModifica) {
    const index = enti.findIndex(e => e.id === id);
    enti[index] = newEnte;
    alert(`Ente "${newEnte.denominazione}" modificato con successo.`);
  } else {
    enti.push(newEnte);
    alert(`Ente "${newEnte.denominazione}" aggiunto con successo.`);
  }

  enteForm.reset();
  document.getElementById('anteprimaStemma').style.display = 'none';
  formTitle.innerText = 'Aggiungi Ente';
  submitBtn.innerText = 'Aggiungi ente';

  renderEnti();

  predefinitoAttuale = enti.find(e => e.predefinito);

  formMainTitle.scrollIntoView({behavior: 'smooth'});
});

document.getElementById('cancelEdit').addEventListener('click', () => {
  enteForm.reset();
  document.getElementById('anteprimaStemma').style.display = 'none';
  formTitle.innerText = 'Aggiungi Ente';
  submitBtn.innerText = 'Aggiungi ente';
});

renderEnti();

</script>
