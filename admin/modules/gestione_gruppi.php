<?php
require_once '../includes/check_access.php';

global $tipo_cons;
$tipo_cons=$_SESSION['tipo_cons'];
// Variabile tipo candidato: presidente, sindaco, uninominale
$row = elenco_gruppi();
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_gruppo'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-user-tie mr-2"></i>Gestione <?= htmlspecialchars(ucfirst(_GRUPPO)) ?></h2>

    <div class="card mb-4" id="formCandidatoCard">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title"><div id="form-title">Aggiungi <?= htmlspecialchars(ucfirst(_GRUPPO)) ?></div></h3>
      </div>
      <div class="card-body">
        <form id="gruppoForm" method="post" enctype="multipart/form-data" onsubmit="aggiungiGruppo(event)">
          <input type="hidden" id="id_gruppo" name="id_gruppo">
          <input type="hidden" name="tipo_candidato" value="<?= $tipo_cons ?>">

          <!-- Prima riga: numero e denominazione -->
          <div class="form-row" style="align-items:center; gap:0.5rem;">
            <div class="form-group" style="flex:0 0 80px; margin-bottom:0;">
              <label for="numero" style="font-weight:600; font-size:0.9rem;">Posizione*</label>
              <input type="number" class="form-control" id="numero" min="1" value="<?= $maxNumero; ?>" required>
            </div>
            <div class="form-group flex-grow-1" style="margin-bottom:0;">
              <label for="denominazione" style="font-weight:600; font-size:0.9rem;">Nome e Cognome*</label>
              <input type="text" id="denominazione" name="denominazione" class="form-control" required
                     style="font-size:0.95rem; padding: 0.375rem 0.75rem;">
            </div>
          </div>

          <!-- Seconda riga: simbolo e file PDF -->
          <div class="form-row mt-2" style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:flex-start;">
            
            <!-- Simbolo -->
            <div class="form-group flex-fill" style="margin-bottom:0;">
              <label for="simbolo" style="font-weight:600; font-size:0.85rem;">Simbolo<br><small>(max 1000x1000)</small></label>
              <div class="d-flex align-items-center gap-2 mt-1">
                <img id="anteprimaStemma" src="" alt="Anteprima stemma"
                     style="width:80px; height:80px; border:1px solid #ccc; padding:2px; object-fit:contain; visibility:hidden;">
                <input type="file" id="simbolo" name="simbolo" class="form-control-file" accept="image/*" style="flex-grow:1; font-size:0.85rem;">
              </div>
            </div>

            <!-- Programma PDF -->
            <div class="form-group flex-fill" style="margin-bottom:0;">
              <label for="programma" style="font-weight:600; font-size:0.85rem;">Programma<br><small>(PDF)</small></label>
              <input type="file" id="programma" name="programma" class="form-control-file" accept="application/pdf" style="font-size:0.85rem;">
            </div>

            <?php if ($tipo_cons === 3): ?>
              <!-- Curriculum Vitae PDF -->
              <div class="form-group flex-fill" style="margin-bottom:0;">
                <label for="cv" style="font-weight:600; font-size:0.85rem;">Curriculum Vitae<br><small>(PDF)</small></label>
                <input type="file" id="cv" name="cv" class="form-control-file" accept="application/pdf" style="font-size:0.85rem;">
              </div>

              <!-- Certificato Penale PDF -->
              <div class="form-group flex-fill" style="margin-bottom:0;">
                <label for="cg" style="font-weight:600; font-size:0.85rem;">Certificato Penale<br><small>(PDF)</small></label>
                <input type="file" id="cg" name="cg" class="form-control-file" accept="application/pdf" style="font-size:0.85rem;">
              </div>
            <?php endif; ?>

          </div>

          <!-- Terza riga: pulsanti -->
          <div class="form-row mt-3">
            <div class="form-group d-flex ">
              <button type="submit" id="btnAggiungi" class="btn btn-success me-2">Salva</button>
              <button type="button" class="btn btn-secondary" id="btnAnnulla" onclick="annullaModifica()">Annulla</button>
            </div>
          </div>

        </form>
      </div>
    </div>

    <!-- Tabella Candidati -->
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Lista Candidati</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle" style="font-size:0.95rem;">
          <thead class="table-light">
            <tr>
              <th style="width:80px; text-align:center;">Posizione</th>
              <th>Nome e Cognome</th>
              <th style="text-align:center;">Simbolo</th>
              <th style="text-align:center;">Programma</th>
              <?php if ($tipo_cons === 3): ?>
                <th style="text-align:center;">Curriculum Vitae</th>
                <th style="text-align:center;">Certificato Penale</th>
              <?php endif; ?>
              <th style="text-align:center;">Azioni</th>
            </tr>
          </thead>
          <tbody id="risultato">
            <?php include('elenco_gruppi.php'); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<!-- Modal conferma eliminazione -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Conferma eliminazione
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">

  <p>
    Sei sicuro di voler eliminare il gruppo
    <strong id="deleteGruppo"></strong>?
  </p>

  <hr>

  <p class="mb-2"><strong>Eliminazione selettiva (opzionale):</strong></p>

  <div class="form-check">
    <input class="form-check-input" type="checkbox" id="flag_simbolo">
    <label class="form-check-label" for="flag_simbolo">
      Simbolo
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="checkbox" id="flag_programma">
    <label class="form-check-label" for="flag_programma">
      Programma
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="checkbox" id="flag_cv">
    <label class="form-check-label" for="flag_cv">
      Curriculum Vitae
    </label>
  </div>
  <div class="form-check">
    <input class="form-check-input" type="checkbox" id="flag_cg">
    <label class="form-check-label" for="flag__cg">
      Casellario Giudiziale
    </label>
  </div>

  <small class="text-muted d-block mt-2">
    Se non selezioni nulla verrà eliminato l’intero gruppo.
  </small>

</div>


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times me-1"></i>Annulla
        </button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
          <i class="fas fa-trash me-1"></i>Elimina
        </button>
      </div>

    </div>
  </div>
</div>


<script>
// ===========================
// GESTIONE ANTEPRIMA STEMMA
// ===========================

// Anteprima immediata quando l'utente seleziona un file
const simboloInput = document.getElementById('simbolo');
if(simboloInput){
    simboloInput.addEventListener('change', function(event) {
		if(event.target.files[0]==null) return;
        const file = event.target.files[0];
        const preview = document.getElementById('anteprimaStemma');

        if (file) {
            const img = new Image();
            const reader = new FileReader();

            reader.onload = function(e) {
                img.src = e.target.result;

                img.onload = function() {
                    // Controllo dimensioni massime 1000x1000
                    if (img.width > 1000 || img.height > 1000) {
                        alert("Attenzione: l'immagine supera le dimensioni massime 1000x1000 px.");
                        preview.src = '';
                        preview.style.visibility = 'hidden';
                        simboloInput.value = ''; // reset file input
                        return;
                    }
                    // Mostra anteprima
                    preview.src = img.src;
                    preview.style.visibility = 'visible';
                }
            }

            reader.readAsDataURL(file);
        } else {
            // Se l'input viene svuotato, nasconde anteprima
            preview.src = '';
            preview.style.visibility = 'hidden';
        }
    });
}

// ===========================
// AGGIUNGI / MODIFICA GRUPPO
// ===========================
function aggiungiGruppo(e) {
    e.preventDefault();
const numero = document.getElementById("numero").value;

// CONTROLLO POSIZIONE GIÀ USATA
if (posizioneUsata(numero)) {
    alert("ATTENZIONE: la posizione " + numero + " è già assegnato!");
    document.getElementById("numero").focus();
    return; // BLOCCA IL SALVATAGGIO
}

    const formData = new FormData();
    formData.append('funzione', 'salvaGruppo');
    formData.append('descrizione', document.getElementById("denominazione").value.trim());
    formData.append('numero', document.getElementById("numero").value);
    formData.append('id_gruppo', document.getElementById("id_gruppo").value);
    formData.append('op', 'salva');

    // Aggiungo file solo se selezionati
    ['simbolo','programma','cv','cg'].forEach(id => {
        const input = document.getElementById(id);
        if(input && input.files[0]){
            formData.append(id, input.files[0]);
        }
    });

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.text())
    .then(data => {
		if(data=='1') alert('Questo numero di posizione è già assegnato ad altro gruppo');
		else {	document.getElementById('risultato').innerHTML  = data;

			// Mostra eventuali messaggi dal server
			console.log(data);

			// Reset form
			resetFormGruppo();
			aggiornaNumero();
			document.getElementById('form-title').textContent = "Aggiungi <?= htmlspecialchars(ucfirst(_GRUPPO)) ?>";
		}
        // Ricarica lista aggiornata
//        ricaricaListaGruppi();
    });
}

// ===========================
// CANCELLA GRUPPO
// ===========================
function deleteGruppo(index) {
    denominazione = document.getElementById("denominazione"+index).innerText;
    numero = document.getElementById("numero"+index).innerText;
    deleteIdGruppo = document.getElementById("id_gruppo"+index).innerText;

    document.getElementById("deleteGruppo").textContent = numero + " - " + denominazione;

    $('#confirmDeleteModal').modal('show');


document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteIdGruppo) return;

    const flags = ['simbolo','programma','cv','cg'];
    let eliminazioneParziale = false;

    const formData = new FormData();
    formData.append('funzione','salvaGruppo');
    formData.append('id_gruppo', deleteIdGruppo);
    formData.append('descrizione', denominazione);
    formData.append('numero', numero);

    flags.forEach(f => {
        const checkbox = document.getElementById('flag_'+f);
        if(checkbox?.checked){
            formData.append('flag_'+f, 1);
            eliminazioneParziale = true;
        }
    });

    formData.append('op', eliminazioneParziale ? 'cancella_parziale' : 'cancella');

    fetch('../principale.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
		if(data=='2') alert('Non è possibile cancellare il gruppo perché ci sono liste collegate');
		else if(data=='3') alert('Non è possibile cancellare il gruppo perché ci sono dei voti assegnati');
		else { document.getElementById('risultato').innerHTML  = data;
        document.getElementById('risultato').innerHTML = data;
        console.log(data);
        $('#confirmDeleteModal').modal('hide');
        deleteIdGruppo = null;
        resetFormGruppo();
        aggiornaNumero();
		}
    });
});
}
// ===========================
// ANNULLA MODIFICA
// ===========================
function annullaModifica() {
    const form = document.getElementById('gruppoForm');
    form.reset();
    document.getElementById('id_gruppo').value = '';
    document.getElementById('btnAggiungi').textContent = "Aggiungi";
    document.getElementById('form-title').textContent = "Aggiungi <?= htmlspecialchars(ucfirst(_GRUPPO)) ?>";

    // Reset anteprima stemma
    const preview = document.getElementById('anteprimaStemma');
    preview.src = '';
    preview.style.visibility = 'hidden';

    // Bottone Annulla sempre nascosto
    document.getElementById('btnAnnulla').classList.add('d-none');
}

// ===========================
// MODIFICA GRUPPO
// ===========================
function editGruppo(index) {
    const den = document.getElementById("denominazione"+index).innerText;
    const num = document.getElementById("numero"+index).innerText;
    const id = document.getElementById("id_gruppo"+index).innerText;
    const simboloNome = document.getElementById("simbolo"+index).innerText;

    document.getElementById("denominazione").value = den;
    document.getElementById("numero").value = num;
    document.getElementById("id_gruppo").value = id;

    // Mostra anteprima stemma ma resetta il file input
    const preview = document.getElementById("anteprimaStemma");
    if(simboloNome){
        preview.src = "../../client/documenti/<?= $id_comune."/".$id_cons ?>/img/" + simboloNome;
        preview.style.visibility = 'visible';
    } else {
        preview.src = '';
        preview.style.visibility = 'hidden';
    }
    document.getElementById('simbolo').value = ''; // RESET file input

    document.getElementById("btnAggiungi").textContent = "Salva modifiche";
    document.getElementById("form-title").textContent = "Modifica <?= htmlspecialchars(ucfirst(_GRUPPO)) ?>";

    // Mostra sempre Annulla
    document.getElementById('btnAnnulla').classList.remove('d-none');

    scrollToFormTitle();
}

// ===========================
// RESET FORM
// ===========================
function resetFormGruppo() {
    const form = document.getElementById('gruppoForm');
    form.reset();
    document.getElementById('id_gruppo').value = '';
    document.getElementById('btnAggiungi').textContent = "Aggiungi";
    document.getElementById('form-title').textContent = "Aggiungi <?= htmlspecialchars(ucfirst(_GRUPPO)) ?>";

    // Reset anteprima stemma
    const preview = document.getElementById('anteprimaStemma');
    preview.src = '';
    preview.style.visibility = 'hidden';
}

// ===========================
// UTILI
// ===========================
function aggiornaNumero() {
    const maxNum = document.getElementById("maxNumero").innerText;
    document.getElementById('numero').value = maxNum;
}

function posizioneUsata(num, idEditing = '') {

    let trovata = false;

    document.querySelectorAll('[id^="numero"]').forEach(el => {

        const n = el.innerText.trim();
        const idRow = el.id.replace('numero','');

        // se sto modificando salto la riga corrente
        if(idEditing && idRow === idEditing) return;

        if(n == num) {
            trovata = true;
        }
    });

    return trovata;
}

function scrollToFormTitle() {
    const target = document.getElementById('form-title');
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

