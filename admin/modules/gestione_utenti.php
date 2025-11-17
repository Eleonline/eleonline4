<?php
require_once '../includes/check_access.php';

$currentUserRole = $_SESSION['ruolo'] ?? 'operatore';
$row=elenco_utenti();
foreach($row as $val)
// Dati fittizi sedi e sezioni
$sedi = [
  'Scuola A' => range(1, 4),
  'Scuola B' => range(5, 6),
  'Scuola C' => range(7, 9),
  'Scuola D' => range(10, 11),
  'Scuola E' => range(12, 13)
];

$users = [
  ['id'=>1, 'username'=>'admin1', 'email'=>'admin1@example.com', 'role'=>'admin'],
  ['id'=>2, 'username'=>'operatore1', 'email'=>'op1@example.com', 'role'=>'operatore'],
  ['id'=>3, 'username'=>'pres1', 'email'=>'pres1@example.com', 'role'=>'operatore presidente', 'nome'=>'Mario', 'cognome'=>'Rossi', 'telefono'=>'3331112222', 'sede'=>'Scuola A', 'sezione'=>'1', 'stato'=>'attivo'],
  ['id'=>4, 'username'=>'pres2', 'email'=>'pres2@example.com', 'role'=>'operatore presidente', 'nome'=>'Luca', 'cognome'=>'Bianchi', 'telefono'=>'3332223333', 'sede'=>'Scuola B', 'sezione'=>'5', 'stato'=>'disattivo'],
  ['id'=>5, 'username'=>'super1', 'email'=>'super@example.com', 'role'=>'superuser'],
  ['id'=>6, 'username'=>'pres3', 'email'=>'pres3@example.com', 'role'=>'operatore presidente', 'nome'=>'Elena', 'cognome'=>'Verdi', 'telefono'=>'3334445555', 'sede'=>'Scuola C', 'sezione'=>'7', 'stato'=>'attivo'],
  ['id'=>7, 'username'=>'admin2', 'email'=>'admin2@example.com', 'role'=>'admin'],
  ['id'=>8, 'username'=>'pres4', 'email'=>'pres4@example.com', 'role'=>'operatore presidente', 'nome'=>'Marco', 'cognome'=>'Blu', 'telefono'=>'3339998888', 'sede'=>'Scuola D', 'sezione'=>'10', 'stato'=>'attivo'],
];

/*
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['user_id'])) {
    // Connessione al database
    require_once '../includes/db.php'; // o modifica con il tuo file di connessione

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO utenti (username, password, email, ruolo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $email, $role);
    $stmt->execute();
    $last_id = $stmt->insert_id;

    // Se ruolo è operatore presidente, inserisci i dati extra
    if ($role === 'operatore presidente') {
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $telefono = $_POST['telefono'];
        $sede = $_POST['sede'];
        $sezione = $_POST['sezione'];
        $stato = $_POST['stato'];

        $stmt2 = $conn->prepare("UPDATE utenti SET nome=?, cognome=?, telefono=?, sede=?, sezione=?, stato=? WHERE id=?");
        $stmt2->bind_param("ssssssi", $nome, $cognome, $telefono, $sede, $sezione, $stato, $last_id);
        $stmt2->execute();
    }
}
*/
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_id'])) {
    require_once '../includes/db.php';

    $id = intval($_POST['user_id']);
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Password facoltativa (solo se viene aggiornata)
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE utenti SET username=?, email=?, ruolo=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $email, $role, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE utenti SET username=?, email=?, ruolo=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $role, $id);
    }
    $stmt->execute();

    if ($role === 'operatore presidente') {
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $telefono = $_POST['telefono'];
        $sede = $_POST['sede'];
        $sezione = $_POST['sezione'];
        $stato = $_POST['stato'];

        $stmt2 = $conn->prepare("UPDATE utenti SET nome=?, cognome=?, telefono=?, sede=?, sezione=?, stato=? WHERE id=?");
        $stmt2->bind_param("ssssssi", $nome, $cognome, $telefono, $sede, $sezione, $stato, $id);
        $stmt2->execute();
    }
}
*/
/*
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_user'])) {
    require_once '../includes/db.php';

    $id = intval($_GET['delete_user']);

    $stmt = $conn->prepare("DELETE FROM utenti WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
*/

?>

<!-- CONTENUTO HTML -->
<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-users-cog"></i> Gestione Utenti</h2>

<!-- FORM -->
<div class="card mb-4">
  <div class="card-header bg-primary text-white">
    <h3 class="card-title" id="form-title">Aggiungi Utente</h3>
  </div>
  <div class="card-body">
    <form id="userForm">
      <div class="form-row">
        <div class="form-group col-md-3">
          <label>Username*</label>
          <input type="text" class="form-control" id="username" required>
        </div>
        <div class="form-group col-md-3">
          <label>Password*</label>
          <input type="password" class="form-control" id="password" required>
        </div>
        <div class="form-group col-md-3">
          <label>Email</label>
          <input type="email" class="form-control" id="email">
        </div>
		<div class="form-group col-md-3">
			<label>Nominativo</label>
			<input type="text" class="form-control" id="nominativo">
		</div>
      </div>


      <button type="button" class="btn btn-success" id="saveBtn">Aggiungi Utente</button>
      <button type="button" class="btn btn-secondary" id="cancelEdit">Annulla</button>
    </form>
  </div>
</div>

<!-- LISTA -->
<div class="card">
  <div class="card-header bg-secondary text-white">
    <h3 class="card-title">Lista Utenti</h3>
  </div>
  <div class="card-body table-responsive">
    <table class="table table-bordered table-hover" id="usersTable">
      <thead>
        <tr>
          <th>Username</th>
          <th>Email</th>
          <th>Nominativo</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody id="userRows"><?php include('elenco_utenti.php'); ?></tbody>
    </table>
  </div>
</div>

<script>

function editUser(id) {
  const u = users.find(u => u.id === id);
  if (!u) return;
  document.getElementById('username').value = document.getElementById('username'+id).innerText;
  document.getElementById('password').value = '';
  document.getElementById('email').value = document.getElementById('email'+id).innerText;
  document.getElementById('nominativo').value = document.getElementById('nominativo'+id).innerText;;
  document.getElementById ( "saveBtn" ).textContent = "Salva modifiche";

}


function aggiungiUser(e) {
    e.preventDefault();

	const username = document.getElementById ( "username" ).value
	const password = document.getElementById ( "password" ).value
	const email = document.getElementById ( "email" ).value
	const nominativo = document.getElementById ( "nominativo" ).value

    // Crea un oggetto FormData e aggiungi il file
    const formData = new FormData();
    formData.append('funzione', 'salvaUtente');
    formData.append('username', username);
    formData.append('password', password);
    formData.append('email', email);
    formData.append('nominativo', nominativo);
    formData.append('op', 'salva');

    // Invia la richiesta AJAX usando Fetch
    fetch('../principale.php', {
        method: 'POST',
        body: formData // FormData viene gestito automaticamente da Fetch per l'upload
    })
    .then(response => response.text()) // O .json() se il server risponde con JSON
    .then(data => {
        risultato.innerHTML = data; // Mostra la risposta del server
		document.getElementById ( "saveBtn" ).textContent = "Aggiungi Utente";
		document.getElementById ( "username" ).value = '';
		document.getElementById ( "password" ).value = "";
		document.getElementById ( "email" ).value = '';
		document.getElementById ( "nominativo" ).value = '';
    })
    .catch(error => {
        console.error('Errore durante l\'upload:', error);
        risultato.innerHTML = 'Si è verificato un errore durante l\'upload.';
    });
};

  function deleteUtente(index) {
	var username = document.getElementById ( "username"+index ).innerText
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
				document.getElementById("risultato").innerHTML = this.responseText;
		}
    }
    xmlhttp.open("GET","../principale.php?funzione=salvaUtente&username="+username+"&op=cancella",true);
    xmlhttp.send();

//	document.getElementById("riga"+index).style.display = 'none'
  }

</script>
