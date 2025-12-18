<?php
require_once '../includes/check_access.php';
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
    <form id="userForm" onsubmit="aggiungiUser(event)">
      <div class="form-row">
        <div class="form-group col-md-3">
          <label>Username*</label>
          <input type="text" class="form-control" id="username" required>
        </div>
        <div class="form-group col-md-3">
          <label>Password*</label>
          <input type="password" class="form-control" id="password" onfocus="select()" required>
        </div>
        <div class="form-group col-md-3">
          <label>Email</label>
          <input type="email" class="form-control" id="email">
        </div>
        <div class="form-group col-md-3">
          <label>Nominativo</label>
          <input type="text" class="form-control" id="nominativo">
        </div>
        <?php if($_SESSION['ruolo']=='superuser') $nascondi=''; else $nascondi="d-none"; ?>
        <div class="form-group form-check <?= $nascondi ?>" >
          <input type="checkbox" class="form-check-input" id="admin" name="admin">
          <label class="form-check-label" for="admin">Admin</label>
        </div>
      </div>


          <button type="submit" class="btn btn-success" id="submitBtn">Aggiungi Utente</button>
    
          <button type="button" class="btn btn-secondary" id="cancelEdit" onclick="resetFormUser()">Annulla</button>
      
      </div>

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
      <tbody id="risultato"><?php include('elenco_utenti.php'); ?></tbody>
    </table>
  </div>
</div>

<script>
function editUser(id) {
    document.getElementById('username').value = document.getElementById('username'+id).innerText;
    document.getElementById('password').value = '********';
    document.getElementById('email').value = document.getElementById('email'+id).innerText;
    document.getElementById('nominativo').value = document.getElementById('nominativo'+id).innerText;
    
    const adminVal = document.getElementById('admin'+id)?.innerText;
    document.getElementById('admin').checked = adminVal == 1;

    document.getElementById("submitBtn").textContent = "Salva modifiche";
}

function resetFormUser() {
    const form = document.getElementById('userForm');
    form.reset();
    document.getElementById('submitBtn').textContent = "Aggiungi Utente";
    document.getElementById('admin').checked = false;
}

function aggiungiUser(e) {
    e.preventDefault();

    const admin = document.getElementById('admin').checked;
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const email = document.getElementById('email').value;
    const nominativo = document.getElementById('nominativo').value;

    const formData = new FormData();
    formData.append('funzione', 'salvaUtente');
    formData.append('admin', admin);
    formData.append('username', username);
    formData.append('password', password);
    formData.append('email', email);
    formData.append('nominativo', nominativo);
    formData.append('op', 'salva');

    fetch('../principale.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('risultato').innerHTML = data;
        resetFormUser();
    });
}

function deleteUser(index) {
    if (confirm("Confermi l'eliminazione?")) {
        const username = document.getElementById("username"+index).innerText;
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("risultato").innerHTML = this.responseText;
            }
        }
        xmlhttp.open("GET","../principale.php?funzione=salvaUtente&username="+username+"&op=cancella",true);
        xmlhttp.send();
    }
}
</script>
