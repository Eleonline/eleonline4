<?php
require_once '../includes/check_access.php';

global $tipo_cons;
$tipo_cons=$_SESSION['tipo_cons'];
// Variabile tipo candidato: presidente, sindaco, uninominale
$row = elenco_liste();
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_lista'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-user-tie mr-2"></i>Gestione <?= htmlspecialchars(ucfirst(_LISTA)) ?></h2>

    <div class="card mb-4" id="formCandidatoCard">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="titoloLista">Aggiungi <?= htmlspecialchars(ucfirst(_LISTA)) ?></h3>
      </div>
    <div class="card-body">
  <form id="listaForm" method="post" enctype="multipart/form-data" onsubmit="aggiungiLista(event)">
    <input type="hidden" id="id_lista" name="id_lista">
    <input type="hidden" name="tipo_candidato" value="<?= $tipo_cons ?>">

    <!-- PRIMA RIGA: NUMERO, GRUPPO, DENOMINAZIONE -->
    <div class="form-row" style="align-items:center; gap:0.5rem;">
      <div class="form-group" style="flex: 0 0 80px; margin-bottom:0;">
        <label for="numero" style="font-weight:600; font-size:0.9rem;">Posizione*</label>
        <input type="number" class="form-control" id="numero" min="1" value="<?= $maxNumero; ?>" required>
      </div>

      <?php $row = elenco_gruppi(); if(count($row)) $visualizza=''; else $visualizza='style="display:none;"'; ?>
      <div class="col-md-4" <?= $visualizza ?>>
        <label>Gruppo</label>
        <select id="idGruppo" class="form-control">
          <option value="0"></option>
          <?php foreach($row as $key=>$val) { ?>
            <option value="<?= $val['id_gruppo'] ?>"><?= $val['descrizione'] ?></option>
          <?php } ?>
        </select>
        <?php foreach($row as $key=>$val) { ?>
          <div id="ng<?= $val['id_gruppo'] ?>" style="display:none;"><?= $val['num_gruppo'] ?></div>
        <?php } ?>
      </div>

      <div class="form-group flex-grow-1" style="margin-bottom:0;">
        <label for="denominazione" style="font-weight:600; font-size:0.9rem;">Descrizione*</label>
        <input type="text" id="denominazione" name="denominazione" class="form-control" required
               style="font-size:0.95rem; padding: 0.375rem 0.75rem;">
      </div>
    </div>

    <!-- SECONDA RIGA: STEMMA + SFOGLIA -->
    <div class="form-row mt-2" style="display:flex; gap:0.5rem; align-items:flex-start;">
      <div class="form-group flex-fill" style="margin-bottom:0;">
        <label for="simbolo" style="font-weight:600; font-size:0.85rem;">
          Simbolo<br><small>(max 1000x1000)</small>
        </label>
        <div style="display:flex; align-items:center; gap:10px; margin-top:5px;">
          <!-- Anteprima stemma -->
          <img id="anteprimaStemma" src="" alt="Anteprima stemma"
               style="width:80px; height:80px; visibility:hidden; border:1px solid #ccc; padding:2px; object-fit:contain;">
          <!-- Bottone Sfoglia -->
          <input type="file" id="simbolo" name="simbolo" class="form-control-file" accept="image/*"
                 style="font-size:0.85rem; flex-grow:1;">
        </div>
      </div>
    </div>

   <!-- TERZA RIGA: PULSANTI SALVA E ANNULLA a sinistra -->
<div class="form-row mt-3">
  <div class="form-group d-flex gap-2">
    <button type="submit" id="btnAggiungi" class="btn btn-success">Salva</button>
    <button type="button" class="btn btn-secondary" id="btnAnnulla" onclick="annullaModifica()">Annulla</button>
  </div>
</div>


  </form>
</div>


    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Elenco Liste</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle" style="font-size: 0.95rem;">
          <thead class="table-light">
            <tr>
              <th style="width:80px; text-align:center;">Posizione</th>
              <th <?= $visualizza ?>>Gruppo</th>
              <th>Denominazione lista</th>
              <th style="text-align:center;">Simbolo</th>
              <th style="text-align:center;">Azioni</th>
            </tr>
          </thead>
          <tbody id="risultato">
            <!-- La tabella sarà popolata via JS -->
			<?php include('elenco_liste.php'); ?>
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
    Sei sicuro di voler eliminare la lista
    <strong id="deleteLista"></strong>?
  </p>

  <hr>

  <p class="mb-2"><strong>Eliminazione selettiva (opzionale):</strong></p>

  <div class="form-check">
    <input class="form-check-input" type="checkbox" id="flag_simbolo">
    <label class="form-check-label" for="flag_simbolo">
      Simbolo
    </label>
  </div>

  <small class="text-muted d-block mt-2">
    Se non selezioni nulla verrà eliminato l’intera lista.
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
document.getElementById('simbolo').addEventListener('change', function(event) {
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
                    event.target.value = ''; // reset file input
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

// ===========================
// FUNZIONE AGGIUNGI LISTA
// ===========================
function aggiungiLista(e) {
    e.preventDefault();

    const fileInput = document.getElementById('simbolo');
    const simbolo = fileInput.files[0];
    const id_lista = document.getElementById("id_lista").value;

    let id_gruppo = 0, num_gruppo = 0;
    if (document.getElementById("idGruppo").value != 0) {
        id_gruppo = document.getElementById("idGruppo").value;
        num_gruppo = document.getElementById("ng"+id_gruppo).innerText;
    }

    const numero = document.getElementById("numero").value;
    const denominazione = document.getElementById("denominazione").value.trim();

    const formData = new FormData();
    formData.append('funzione', 'salvaLista');
    formData.append('descrizione', denominazione);
    formData.append('numero', numero);
    formData.append('id_lista', id_lista);
    formData.append('id_gruppo', id_gruppo);
    formData.append('num_gruppo', num_gruppo);

    if (simbolo) formData.append('simbolo', simbolo);
    formData.append('op', 'salva');

    fetch('../principale.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('risultato').innerHTML = data;
        resetFormLista();
        aggiornaNumero();
        document.getElementById("titoloLista").textContent = "Aggiungi <?= htmlspecialchars(ucfirst(_LISTA)) ?>";
    });
}

// ===========================
// DELETE LISTA
// ===========================
function deleteLista(index) {
    const denominazione = document.getElementById("denominazione"+index).innerText;
    const numero = document.getElementById("numero"+index).innerText;
    const deleteIdLista = document.getElementById("id_lista"+index).innerText;

    document.getElementById("deleteLista").textContent = numero + " - " + denominazione;
    $('#confirmDeleteModal').modal('show');

    document.getElementById('confirmDeleteBtn').onclick = function() {
        const delSimbolo = document.getElementById('flag_simbolo')?.checked;
        const formData = new FormData();
        formData.append('funzione', 'salvaLista');
        formData.append('id_lista', deleteIdLista);
        formData.append('descrizione', denominazione);
        formData.append('numero', numero);

        if (delSimbolo) {
            formData.append('op', 'cancella_parziale');
            formData.append('flag_simbolo', 1);
        } else {
            formData.append('op', 'cancella');
        }

        fetch('../principale.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            document.getElementById('risultato').innerHTML = data;
            $('#confirmDeleteModal').modal('hide');
            resetFormLista();
            aggiornaNumero();
            document.getElementById("titoloLista").textContent = "Aggiungi <?= htmlspecialchars(ucfirst(_LISTA)) ?>";
        });
    }
}

// ===========================
// ANNULLA / RESET FORM
// ===========================
function annullaModifica() {
    const form = document.getElementById('listaForm');
    form.reset();
    document.getElementById('id_lista').value = '';
    document.getElementById('btnAggiungi').textContent = "Aggiungi";
    document.getElementById("titoloLista").textContent = "Aggiungi <?= htmlspecialchars(ucfirst(_LISTA)) ?>";

    const preview = document.getElementById('anteprimaStemma');
    preview.src = '';
    preview.style.visibility = 'hidden';

    // Bottone Annulla sempre visibile
    document.getElementById('btnAnnulla').classList.remove('d-none');
}

// ===========================
// EDIT LISTA
// ===========================
function editLista(index) {
    const preview = document.getElementById("anteprimaStemma");
    const simboloNome = document.getElementById("simbolo"+index).innerText;

    if (simboloNome) {
        const url = "../../client/documenti/<?= $id_comune."/".$id_cons ?>/img/" + simboloNome;
        preview.src = url;
        preview.style.visibility = 'visible';
		preview.style.backgroundColor = '#fff'; 
    } else {
        preview.src = '';
        preview.style.visibility = 'hidden';
    }

    document.getElementById("denominazione").value = document.getElementById("denominazione"+index).innerText;
    document.getElementById("numero").value = document.getElementById("numero"+index).innerText;
    document.getElementById("id_lista").value = document.getElementById("id_lista"+index).innerText;

    if (document.getElementById("num_gruppo"+index)?.innerText !== null)
        document.getElementById("idGruppo").selectedIndex = document.getElementById("num_gruppo"+index).innerText;

    // SVUOTA IL FILE INPUT (così puoi scegliere un nuovo file)
    const fileInput = document.getElementById('simbolo');
    fileInput.value = '';
	
	document.getElementById("btnAggiungi").textContent = "Salva modifiche";
    document.getElementById("titoloLista").textContent = "Modifica <?= htmlspecialchars(ucfirst(_LISTA)) ?>";

    // Bottone Annulla sempre visibile
    document.getElementById('btnAnnulla').classList.remove('d-none');

    scrollToGestioneLista();
}

// ===========================
// SCROLL FORM
// ===========================
function scrollToGestioneLista() {
    const target = document.getElementById('titoloLista');
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// ===========================
// RESET FORM COMPLETO
// ===========================
function resetFormLista() {
    const form = document.getElementById('listaForm');
    form.reset();
    document.getElementById('id_lista').value = '';
    document.getElementById('btnAggiungi').textContent = "Aggiungi";
    document.getElementById("titoloLista").textContent = "Aggiungi <?= htmlspecialchars(ucfirst(_LISTA)) ?>";

    const preview = document.getElementById('anteprimaStemma');
    preview.src = '';
    preview.style.visibility = 'hidden';

    // Bottone Annulla sempre visibile
    document.getElementById('btnAnnulla').classList.remove('d-none');
}

// ===========================
// AGGIORNA NUMERO LISTA
// ===========================
function aggiornaNumero() {
    const maxNum = document.getElementById("maxNumero").innerText;
    document.getElementById('numero').value = maxNum;
}

</script>
