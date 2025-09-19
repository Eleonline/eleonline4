<?php
require_once '../includes/check_access.php';

$currentUserRole = $_SESSION['ruolo'] ?? 'operatore';

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
      <input type="hidden" name="user_id" id="user_id" value="">
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
          <label>Ruolo*</label>
          <select class="form-control" id="role" required>
            <option value="">Seleziona...</option>
            <option value="admin">Admin</option>
            <option value="operatore">Operatore</option>
            <option value="operatore presidente">Operatore Presidente</option>
          </select>
        </div>
      </div>

      <div id="presidenteFields" style="display:none">
        <div class="form-row">
          <div class="form-group col-md-4">
            <label>Nome</label>
            <input type="text" class="form-control" id="nome">
          </div>
          <div class="form-group col-md-4">
            <label>Cognome</label>
            <input type="text" class="form-control" id="cognome">
          </div>
          <div class="form-group col-md-4">
            <label>Telefono</label>
            <input type="tel" class="form-control" id="telefono">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label>Sede*</label>
            <select id="sede" class="form-control" required></select>
          </div>
          <div class="form-group col-md-4">
            <label>Sezione*</label>
            <select id="sezione" class="form-control" required></select>
          </div>
          <div class="form-group col-md-4">
            <label>Stato*</label>
            <select id="stato" class="form-control" required>
              <option value="attivo">Attivo</option>
              <option value="disattivo">Disattivo</option>
            </select>
          </div>
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
          <th>Ruolo</th>
          <th>Sede</th>
          <th>Sezione</th>
          <th>Stato</th>
          <th>Azioni</th>
        </tr>
      </thead>
      <tbody id="userRows"></tbody>
    </table>
  </div>
</div>

<script>
const currentUserRole = '<?php echo $currentUserRole; ?>';
let users = <?php echo json_encode(array_values(array_filter($users, fn($u) => $u['role'] !== 'superuser'))); ?>;
const sediData = <?php echo json_encode($sedi); ?>;

const userForm = document.getElementById('userForm');
const userRows = document.getElementById('userRows');
const presidenteFields = document.getElementById('presidenteFields');
const roleSelect = document.getElementById('role');
const sedeSelect = document.getElementById('sede');
const sezioneSelect = document.getElementById('sezione');
const passwordField = document.getElementById('password');
const formTitle = document.getElementById('form-title');
const saveBtn = document.getElementById('saveBtn');

roleSelect.addEventListener('change', () => {
  presidenteFields.style.display = roleSelect.value === 'operatore presidente' ? 'block' : 'none';
});

function populateSedi() {
  sedeSelect.innerHTML = '<option value="">Seleziona sede</option><option value="tutte">Tutte le sedi</option>';
  Object.keys(sediData).forEach(sede => {
    sedeSelect.innerHTML += `<option value="${sede}">${sede}</option>`;
  });
}

sedeSelect.addEventListener('change', () => {
  const sede = sedeSelect.value;
  if (sede === 'tutte') {
    sezioneSelect.innerHTML = '<option value="tutte">Tutte le sezioni</option>';
  } else {
    sezioneSelect.innerHTML = sediData[sede].map(s => `<option value="${s}">${s}</option>`).join('');
    sezioneSelect.innerHTML += '<option value="tutte">Tutte le sezioni</option>';
  }
});

function renderUsers() {
  userRows.innerHTML = '';
  users.forEach(user => {
    if (currentUserRole === 'operatore' && user.role !== 'operatore presidente') return;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${user.username}</td>
      <td>${user.email || ''}</td>
      <td>${user.role}</td>
      <td>${user.sede || ''}</td>
      <td>${user.sezione || ''}</td>
      <td>${user.stato || ''}</td>
      <td>
        <button class="btn btn-sm btn-warning me-1" onclick="editUser(${user.id})">Modifica</button>
        ${currentUserRole !== 'operatore' ? `<button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Elimina</button>` : ''}
      </td>
    `;
    userRows.appendChild(tr);
  });
}

function editUser(id) {
  const u = users.find(u => u.id === id);
  if (!u) return;
  document.getElementById('user_id').value = u.id;
  document.getElementById('username').value = u.username;
  document.getElementById('email').value = u.email || '';
  passwordField.value = '';
  passwordField.required = false;
  roleSelect.value = u.role;
  roleSelect.dispatchEvent(new Event('change'));
  if (u.role === 'operatore presidente') {
    document.getElementById('nome').value = u.nome || '';
    document.getElementById('cognome').value = u.cognome || '';
    document.getElementById('telefono').value = u.telefono || '';
    sedeSelect.value = u.sede || '';
    sedeSelect.dispatchEvent(new Event('change'));
    sezioneSelect.value = u.sezione || '';
    document.getElementById('stato').value = u.stato || 'attivo';
  }
  formTitle.innerText = 'Modifica Utente';
  saveBtn.innerText = 'Modifica Utente';
  document.getElementById('form-title').scrollIntoView({ behavior: 'smooth', block: 'start' });

}

function deleteUser(id) {
  const u = users.find(u => u.id === id);
  if (!u) return;
  if (confirm(`Confermi l'eliminazione dell'utente "${u.username}"?`)) {
    users = users.filter(u => u.id !== id);
    renderUsers();
    alert('Utente eliminato con successo.');
  }
}

document.getElementById('cancelEdit').addEventListener('click', () => {
  userForm.reset();
  presidenteFields.style.display = 'none';
  passwordField.required = true;
  document.getElementById('user_id').value = '';
  formTitle.innerText = 'Aggiungi Utente';
  saveBtn.innerText = 'Salva';
});

saveBtn.addEventListener('click', () => {
  const id = parseInt(document.getElementById('user_id').value);
  const username = document.getElementById('username').value.trim();
  const password = passwordField.value.trim();
  const email = document.getElementById('email').value.trim();
  const role = roleSelect.value;

  if (!username || (!id && !password) || !role) return alert('Compila i campi obbligatori.');

  let user = { id: id || Date.now(), username, email, role };
  if (!id && password) user.password = password;

  if (role === 'operatore presidente') {
    user.nome = document.getElementById('nome').value.trim();
    user.cognome = document.getElementById('cognome').value.trim();
    user.telefono = document.getElementById('telefono').value.trim();
    user.sede = sedeSelect.value;
    user.sezione = sezioneSelect.value;
    user.stato = document.getElementById('stato').value;
  }

  const index = users.findIndex(u => u.id === id);
  if (index > -1) {
    users[index] = { ...users[index], ...user };
    alert('Utente aggiornato con successo.');
  } else {
    users.push(user);
    alert('Utente aggiunto con successo.');
  }

  userForm.reset();
  presidenteFields.style.display = 'none';
  passwordField.required = true;
  document.getElementById('user_id').value = '';
  formTitle.innerText = 'Aggiungi Utente';
  saveBtn.innerText = 'Aggiungi Utente';
  renderUsers();
});

populateSedi();
renderUsers();
</script>
