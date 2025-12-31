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

function scegliTipo() {
    const val = document.getElementById("livello").value;
    if(val=='2'){
        document.getElementById('divelencosedi').style.display = 'none';
        document.getElementById('divelencosezioni').style.display = 'block';
    } else if(val=='1') {
        document.getElementById('divelencosedi').style.display = 'block';
        document.getElementById('divelencosezioni').style.display = 'none';
    } else {
        document.getElementById('divelencosedi').style.display = 'none';
        document.getElementById('divelencosezioni').style.display = 'none';
    }
}

function editUser(id) {
    document.getElementById("card-body").style.display = 'block';
    var x = document.getElementById("utente");
    var option = document.createElement("option");
    option.text = document.getElementById('utente'+id).innerText;
    x.add(option);
    const elementi = x.options.length - 1;
    document.getElementById('utente').selectedIndex = elementi;

    document.getElementById("submitBtn").textContent = "Salva modifiche";
}

function aggiungiUser(e) {
    e.preventDefault();
    var sedi = 0;
    var sezioni = 0;
    const livello = document.getElementById("livello").value;
    const utente = document.getElementById("utente").value;
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
            risultato.innerHTML = data;
            myForm.reset();
            document.getElementById("submitBtn").textContent = "Aggiungi Utente";
            var x = document.getElementById("utente");
            if(x.options.length == 0) {
                document.getElementById("submitBtn").style.display='none';
                document.getElementById("form-title").innerText = "Non sono presenti altri utenti da autorizzare";
            }
        });
}

function deleteUser(index) {
    deleteUtente = document.getElementById("utente"+index).innerText;
    document.getElementById("deleteUtente").textContent = deleteUtente;
    $('#confirmDeletePermessoModal').modal('show');
}

document.getElementById('confirmDeletePermessoBtn').addEventListener('click', function() {
    if(deleteUtente) {
        const formData = new FormData();
        formData.append('funzione', 'salvaPermesso');
        formData.append('utente', deleteUtente);
        formData.append('op', 'cancella');

        fetch('../principale.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                risultato.innerHTML = data;
                deleteUtente = null;
                $('#confirmDeletePermessoModal').modal('hide');
                document.getElementById("submitBtn").style.display='block';
                document.getElementById("submitBtn").textContent = "Aggiungi Utente";
                document.getElementById("form-title").innerText = "Aggiungi il permesso per un utente";
            });
    }
});
</script>
