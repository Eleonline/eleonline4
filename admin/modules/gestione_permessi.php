<?php
require_once '../includes/check_access.php';
?>
<section class="content" id="risultato">
    <?php include('elenco_permessi.php'); ?>
</section>

<!-- Modal conferma eliminazione permesso -->
<div class="modal fade" id="confirmDeletePermessoModal" tabindex="-1" aria-labelledby="confirmDeletePermessoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeletePermessoLabel"><i class="fas fa-exclamation-triangle me-2"></i>Conferma eliminazione</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Sei sicuro di voler eliminare il permesso dell'utente <strong id="deleteUtente"></strong>? Questa azione non può essere annullata.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i>Annulla</button>
        <button type="button" class="btn btn-danger" id="confirmDeletePermessoBtn"><i class="fas fa-trash me-1"></i>Elimina</button>
      </div>
    </div>
  </div>
</div>

<script>
let deleteUtente = null;

// Gestione visibilità dei div in base al livello
function scegliTipo() {
    const val = document.getElementById("livello").value;
    document.getElementById('divelencosedi').style.display = val == '1' ? 'block' : 'none';
    document.getElementById('divelencosezioni').style.display = val == '2' ? 'block' : 'none';
}

// Modifica utente
function editUser(id) {
    const cardBody = document.getElementById("card-body");
    const selectUtente = document.getElementById("utente");
    const submitBtn = document.getElementById("submitBtn");
    const formTitle = document.getElementById("form-title");

    // Mostra il form
    cardBody.style.display = 'block';

    // Recupera nome utente selezionato
    const nomeUtente = document.getElementById('utente'+id).innerText;

    // Se l'opzione esiste già, selezionala, altrimenti la aggiungi
    let opTrovata = false;
    for(let i=0; i<selectUtente.options.length; i++){
        if(selectUtente.options[i].text === nomeUtente){
            selectUtente.selectedIndex = i;
            opTrovata = true;
            break;
        }
    }

    if(!opTrovata){
        // Svuota e aggiungi solo questa opzione
        selectUtente.innerHTML = "";
        const option = document.createElement("option");
        option.text = nomeUtente;
        selectUtente.add(option);
        selectUtente.selectedIndex = 0;
    }

    // Mostra il pulsante e aggiorna il testo
    submitBtn.style.display = 'block';
    submitBtn.textContent = "Salva modifiche";

    // Aggiorna titolo form
    formTitle.innerText = "Modifica permesso utente";
}

// Aggiungi o salva utente
function aggiungiUser(e) {
    e.preventDefault();
    const livello = document.getElementById("livello").value;
    const utente = document.getElementById("utente").value;
    let sedi = 0, sezioni = 0;

    if(livello == 1) sedi = document.getElementById("sedi").value;
    else if(livello == 2) sezioni = document.getElementById("sezioni").value;

    const formData = new FormData();
    formData.append('funzione', 'salvaPermesso');
    formData.append('utente', utente);
    formData.append('sedi', sedi);
    formData.append('sezioni', sezioni);
    formData.append('op', 'salva');

    fetch('../principale.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            const myForm = document.getElementById('userForm');
            const risultato = document.getElementById('risultato');

            // Aggiorna elenco utenti
            risultato.innerHTML = data;
            myForm.reset();

            const selectUtente = document.getElementById("utente");
            const submitBtn = document.getElementById("submitBtn");
            const formTitle = document.getElementById("form-title");

            // Seleziona solo il primo utente disponibile
            if(selectUtente.options.length > 0){
                selectUtente.selectedIndex = 0;
                submitBtn.style.display = 'block';
                submitBtn.textContent = "Aggiungi Utente";
                formTitle.innerText = "Aggiungi il permesso per un utente";
            } else {
                submitBtn.style.display = 'none';
                formTitle.innerText = "Non sono presenti altri utenti da autorizzare";
            }
        });
}

// Mostra modal conferma cancellazione
function deleteUser(index) {
    deleteUtente = document.getElementById("utente"+index).innerText;
    document.getElementById("deleteUtente").textContent = deleteUtente;
    $('#confirmDeletePermessoModal').modal('show');
}

// Conferma cancellazione
document.getElementById('confirmDeletePermessoBtn').addEventListener('click', function() {
    if(!deleteUtente) return;

    const formData = new FormData();
    formData.append('funzione', 'salvaPermesso');
    formData.append('utente', deleteUtente);
    formData.append('op', 'cancella');

    fetch('../principale.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            const risultato = document.getElementById('risultato');
            risultato.innerHTML = data;

            deleteUtente = null;
            $('#confirmDeletePermessoModal').modal('hide');

            const selectUtente = document.getElementById("utente");
            const submitBtn = document.getElementById("submitBtn");
            const formTitle = document.getElementById("form-title");

            // Seleziona il primo utente disponibile o mostra messaggio
            if(selectUtente.options.length > 0){
                selectUtente.selectedIndex = 0;
                submitBtn.style.display = 'block';
                submitBtn.textContent = "Aggiungi Utente";
                formTitle.innerText = "Aggiungi il permesso per un utente";
            } else {
                selectUtente.innerHTML = "";
                submitBtn.style.display = 'none';
                formTitle.innerText = "Non sono presenti altri utenti da autorizzare";
            }
        });
});
</script>

