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


          <button type="submit" class="btn btn-success" id="submitBtn">Aggiungi Utente</button>
          <button type="reset" class="btn btn-secondary" id="cancelEdit">Annulla</button>
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
  document.getElementById('nominativo').value = document.getElementById('nominativo'+id).innerText;;
  document.getElementById ( "submitBtn" ).textContent = "Salva modifiche";

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
		const myForm = document.getElementById('userForm');
        risultato.innerHTML = data; // Mostra la risposta del server
		myForm.reset();
		document.getElementById ( "submitBtn" ).textContent = "Aggiungi Utente"

    })
};

  function deleteUser(index) {
	if (confirm("Confermi l'eliminazione?") == true) {  
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
  }
</script>
