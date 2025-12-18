<?php
require_once '../includes/check_access.php';
?>
<!-- CONTENUTO HTML -->
<section class="content"  id="risultato">



			<?php include('elenco_permessi.php'); ?>




</section>	  

<script>

function editUser(id) {
  document.getElementById("card-body").style.display = 'block';
  var x = document.getElementById("utente");
  var option = document.createElement("option");
  option.text = document.getElementById('utente'+id).innerText;
  x.add(option);
  const elementi=x.options.length - 1;
  document.getElementById('utente').selectedIndex = elementi;

  document.getElementById ( "submitBtn" ).textContent = "Salva modifiche";
  document.getElementById ( "submitBtn" ).style.display = "block";

}


function aggiungiUser(e) { //nuovi inserimenti e modifiche
    e.preventDefault();
	var sedi = 0;
	var sezioni = 0;
	const livello =  document.getElementById ( "livello" ).value
	const utente = document.getElementById ( "utente" ).value
	if (livello == 1) 
		sedi = document.getElementById ( "sedi" ).value
	else if (livello == 2)
		sezioni = document.getElementById ( "sezioni" ).value

    const formData = new FormData();
    formData.append('funzione', 'salvaPermesso');
    formData.append('utente', utente);
    formData.append('sedi', sedi);
    formData.append('sezioni', sezioni);
    formData.append('op', 'salva');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) 
    .then(data => {
		const myForm = document.getElementById('userForm');
        risultato.innerHTML = data; // Mostra la risposta del server risultato
		myForm.reset();
		document.getElementById ( "submitBtn" ).textContent = "Aggiungi Utente"
		var x = document.getElementById("utente");
		const elementi=x.options.length;
		if(elementi == 0) {
			document.getElementById ( "submitBtn" ).style.display='none';
			document.getElementById ( "form-title" ).innerText = "Non sono presenti altri utenti da autorizzare";
		}
    })
	
}

  function deleteUser(index) {
	if (confirm("Confermi l'eliminazione?") == true) {  

	const utente = document.getElementById ( "utente"+index ).innerText
    const formData = new FormData();
    formData.append('funzione', 'salvaPermesso');
    formData.append('utente', utente);
    formData.append('op', 'cancella');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text()) 
    .then(data => {
		const myForm = document.getElementById('userForm');
        risultato.innerHTML = data; // Mostra la risposta del server
		myForm.reset();
		document.getElementById ( "submitBtn" ).style.display='block';
		document.getElementById ( "submitBtn" ).textContent = "Aggiungi Utente";
		document.getElementById ( "form-title" ).innerText = "Aggiungi il permesso per un utente";
    })


	}
  }
</script>