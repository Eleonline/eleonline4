<?php
require_once '../includes/check_access.php';
global $id_cons_gen;
$row=dati_consultazione($id_cons_gen);
$dataInizio=$row[0]['data_inizio'];
$dataFine=$row[0]['data_fine'];
// Connessione al DB (commentata)
// $conn = new mysqli("localhost", "user", "password", "nome_database");
// if ($conn->connect_error) {
//   die("Connessione fallita: " . $conn->connect_error);
// }

// Caricamento dati dal DB (commentato)
// $affluenze = [];
// $result = $conn->query("SELECT data, ora, minuto FROM affluenze ORDER BY data DESC, ora DESC, minuto DESC");
// while ($row = $result->fetch_assoc()) {
//   $affluenze[] = $row;
// }
// $datiJson = json_encode($affluenze);
?>

<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-clock me-2"></i>Gestione Orari di Affluenza</h3>
      </div>

      <div class="card-body">
        <form id="affluenzaForm" onsubmit="aggiungiAffluenza(event)">
          <div class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
              <label for="data" class="form-label">Data</label>
              <input type="date" id="data" class="form-control" required 
                min="<?php echo $dataInizio; ?>" max="<?php echo $dataFine; ?>" value="<?php echo $dataInizio; ?>">
            </div>

            <div class="col-6 col-md-2">
              <label for="ora" class="form-label">Ora</label>
              <select id="ora" class="form-control" required>
                <?php for ($i = 0; $i < 24; $i++): ?>
                  <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="col-6 col-md-2">
              <label for="minuto" class="form-label">Minuti</label>
              <select id="minuto" class="form-control" required>
                <?php foreach ([0, 15, 30, 45] as $m): ?>
                  <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>"><?= str_pad($m, 2, '0', STR_PAD_LEFT) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12 col-md-2">
              <button type="submit" class="btn btn-primary w-100">Aggiungi</button>
            </div>
          </div>
        </form>

        <table class="table table-striped mt-4" id="affluenzeTable">
          <thead>
            <tr>
              <th style="width: 30%">Data</th>
              <th style="width: 20%">Orario</th>
              <th style="width: 20%">Azione</th>
            </tr>
          </thead>
          <tbody id="risultato">
		  <?php include('elenco_rilevazioni.php'); ?>
		  </tbody>
        </table>
      </div>

      <div class="card-footer">
        <p class="text-muted mb-0">Puoi aggiungere uno o più orari in cui rilevare l'affluenza.</p>
      </div>
    </div>
  </div>
</section>

<script>

  function aggiungiAffluenza(e) {
    e.preventDefault();
    const ora = document.getElementById('ora').value;
    const minuto = document.getElementById('minuto').value;
    const data = document.getElementById('data').value;
    if (!data) {
      alert("Seleziona una data valida");
      return;
    }


    // Salvataggio nel DB (commentato)
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
				document.getElementById("risultato").innerHTML = this.responseText;
		}
    }
    xmlhttp.open("GET","../principale.php?funzione=salvaAffluenza&data="+data+"&ora="+ora+"&minuto="+minuto,true);
    xmlhttp.send();
    /*
    fetch(window.location.href, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'insert', data, ora, minuto })
    }).then(r => r.text()).then(console.log);
    */
  }

  function rimuoviAffluenza(index) {
	var data = document.getElementById ( "data"+index ).innerText
	var orario = document.getElementById ( "orario"+index ).innerText
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
				document.getElementById("risultato").innerHTML = this.responseText;
		}
    }
    xmlhttp.open("GET","../principale.php?funzione=salvaAffluenza&data="+data+"&ora="+orario+"&minuto=&op=cancella",true);
    xmlhttp.send();

    // Eliminazione nel DB (commentato)
    /*
    fetch(window.location.href, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete', data: aff.data, ora: aff.ora, minuto: aff.minuto })
    }).then(r => r.text()).then(console.log);
    
*/
	document.getElementById("riga"+index).style.display = 'none'
  }
</script>

<?php
// GESTIONE POST JSON (commentata)
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');
  $input = json_decode(file_get_contents('php://input'), true);
  if (!$input || !isset($input['action'])) {
    echo json_encode(['success' => false, 'error' => 'Dati non validi']);
    exit;
  }

  $data = $input['data'] ?? '';
  $ora = $input['ora'] ?? '';
  $minuto = $input['minuto'] ?? '';

  if ($input['action'] === 'insert') {
    $stmt = $conn->prepare("INSERT INTO affluenze (data, ora, minuto) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $data, $ora, $minuto);
    echo $stmt->execute()
      ? json_encode(['success' => true])
      : json_encode(['success' => false, 'error' => $stmt->error]);
  }

  if ($input['action'] === 'delete') {
    $stmt = $conn->prepare("DELETE FROM affluenze WHERE data=? AND ora=? AND minuto=?");
    $stmt->bind_param("sss", $data, $ora, $minuto);
    echo $stmt->execute()
      ? json_encode(['success' => true])
      : json_encode(['success' => false, 'error' => $stmt->error]);
  }

  exit;
}
*/
?>
