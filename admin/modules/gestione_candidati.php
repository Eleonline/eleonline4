<?php
require_once '../includes/check_access.php';

global $tipo_cons;
$tipo_cons=$_SESSION['tipo_cons'];
if(!isset($_SESSION['id_lista'])) {
	$row=dati_lista(1);
	$id_lista=$row[0]['id_lista'];
	$_SESSION['id_lista']=$id_lista;
}	
$id_lista=$_SESSION['id_lista'];
// Variabile tipo candidato: presidente, sindaco, uninominale
$row = elenco_candidati($id_lista);
if (count($row)) {
    $ultimo = end($row);
    $maxNumero = $ultimo['num_cand'];
} else {
    $maxNumero = 0;
}
$maxNumero++;
$candidato = htmlspecialchars(ucfirst(_CANDIDATO));
?>

<section class="content">
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-user-tie mr-2"></i>Gestione <?= $candidato ?> </h2>

    <div class="card mb-4" id="formCandidatoCard">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="titoloCandidato">Aggiungi <?= $candidato ?></h3>
      </div>
      <div class="card-body">
        <form id="candidatoForm" method="post" enctype="multipart/form-data" onsubmit="aggiungiCandidato(event)">
          <input type="hidden" id="id_candidato" name="id_candidato">
          <input type="hidden" name="tipo_candidato" value="<?= $tipo_cons ?>">

          <div class="form-row" style="align-items:center; gap:0.5rem;">
			<?php $row = elenco_liste(); if(count($row)) $visualizza=''; else $visualizza='style="display:none;"';?>

            <div class="row" <?= $visualizza ?>>
              <label  class="col-sm-3 col-form-label">Lista</label>
			  <div class="col-sm-9">
              <select class="form-control form-control-sm" id="idLista" onchange="aggiornaLista()">
				<option value="0"></option>
				<?php foreach($row as $key=>$val) { ?>
                <option value="<?= $val['id_lista'] ?>" <?php if($val['id_lista']==$id_lista) echo "selected";?>><?= $val['descrizione'] ?></option>
				<?php } ?>
              </select>
			<?php foreach($row as $key=>$val) { ?>
			  <div id="ng<?= $val['id_lista'] ?>"  style="display:none;"><?= $val['num_lista'] ?></div>
			<?php } ?>
			</div>
            </div>
		</div>
          <div class="form-row" style="align-items:center; gap:0.5rem;">		
            <div class="form-group" style="flex: 0 0 80px; margin-bottom:0;">
              <label for="numero" style="font-weight:600; font-size:0.9rem;">Posizione*</label>
              <input type="number" class="form-control" id="numero" min="1" value="<?= $maxNumero; ?>" required>
            </div>
            <div class="form-group flex-grow-1" style="margin-bottom:0;">
              <label for="cognome" style="font-weight:600; font-size:0.9rem;">Cognome*</label>
              <input type="text" id="cognome" name="cognome" class="form-control" required style="font-size:0.95rem; padding: 0.375rem 0.75rem;">
            </div>
            <div class="form-group flex-grow-1" style="margin-bottom:0;">
              <label for="nome" style="font-weight:600; font-size:0.9rem;">Nome</label>
              <input type="text" id="nome" name="nome" class="form-control" required style="font-size:0.95rem; padding: 0.375rem 0.75rem;">
            </div>
		  </div>	
          <div class="form-row mt-2" style="display:flex; gap:0.5rem;">
          <?php if ($tipo_cons === 3): ?>
            <div class="form-group flex-fill" style="margin-bottom:0;">
              <label for="cv" style="font-weight:600; font-size:0.85rem;">Curriculum Vitae<br><small>(PDF)</small></label>
              <input type="file" id="cv" name="cv" class="form-control-file" accept="application/pdf" style="font-size:0.85rem;">
            </div>
            <div class="form-group flex-fill" style="margin-bottom:0;">
              <label for="cg" style="font-weight:600; font-size:0.85rem;">Certificato Penale<br><small>(PDF)</small></label>
              <input type="file" id="cg" name="cg" class="form-control-file" accept="application/pdf" style="font-size:0.85rem;">
            </div>
          </div>
          <?php endif; ?>


          <div class="form-group mt-2">
            <button type="submit" id="btnAggiungi" class="btn btn-success">Salva</button>
			<button type="button" class="btn btn-secondary d-none" id="btnAnnulla" onclick="annullaModifica()">Annulla</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header bg-secondary text-white">
        <h3 class="card-title">Elenco Candidati</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped align-middle" style="font-size: 0.95rem;">
          <thead class="table-light">
            <tr>
              <th style="width:80px; text-align:center;">Posizione</th>
              <th <?= $visualizza ?>>Lista</th>
              <th>Cognome</th>
              <th>Nome</th>
			  <?php if ($tipo_cons === 3): ?>
			  <th>Curriculum Vitae</th>
              <th>Certificato Penale</th>
			  <?php endif; ?>
              <th style="text-align:center;">Azioni</th>
            </tr>
          </thead>
          <tbody id="risultato">
            <!-- La tabella sarà popolata via JS -->
			<?php include('elenco_candidati.php'); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<!-- Modal conferma eliminazione circoscrizione -->
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
    Sei sicuro di voler eliminare il candidato
    <strong id="deleteCandidato"></strong>?
  </p>

  <hr>
  <?php if ($tipo_cons === 3): ?>

  <p class="mb-2"><strong>Eliminazione selettiva (opzionale):</strong></p>

  <div class="form-check">
    <input class="form-check-input" type="checkbox" id="flag_cv">
    <label class="form-check-label" for="flag_cv">
      Curriculum Vitae
    </label>
  </div>

  <div class="form-check">
    <input class="form-check-input" type="checkbox" id="flag_cg">
    <label class="form-check-label" for="flag_cg">
      Certificato Penale
    </label>
  </div>

  <small class="text-muted d-block mt-2">
    Se non selezioni nulla verrà eliminato il candidato.
  </small>
  <?php endif; ?>

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
// FUNZIONE AGGIUNGI / MODIFICA CANDIDATO
// ===========================
function aggiungiCandidato(e) {
    e.preventDefault();

    const id_candidato = document.getElementById("id_candidato").value;
    const idListaSelect = document.getElementById("idLista");
    const id_lista = idListaSelect.value;
    const num_lista = id_lista != 0 ? document.getElementById("ng"+id_lista).innerText : 0;

    const numero = document.getElementById("numero").value.trim();
    const cognome = document.getElementById("cognome").value.trim();
    const nome = document.getElementById("nome").value.trim();

    const cvFile = document.getElementById('cv')?.files[0];
    const cgFile = document.getElementById('cg')?.files[0];

    // Controllo duplicati solo nella stessa lista
    if (posizioneDuplicataCandidato(numero, id_lista, id_candidato)) {
        alert("ATTENZIONE: la posizione " + numero + " è già presente in questa lista!");
        document.getElementById("numero").focus();
        return false;
    }

    const formData = new FormData();
    formData.append('funzione', 'salvaCandidato');
    formData.append('id_candidato', id_candidato);
    formData.append('id_lista', id_lista);
    formData.append('num_lista', num_lista);
    formData.append('numero', numero);
    formData.append('cognome', cognome);
    formData.append('nome', nome);
    if (cvFile) formData.append('cv', cvFile);
    if (cgFile) formData.append('cg', cgFile);
    formData.append('op', 'salva');

    fetch('../principale.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(data => {
        document.getElementById('risultato').innerHTML = data;
        resetFormCandidato();
        aggiornaNumero();
        document.getElementById('titoloCandidato').textContent =
            "Aggiungi <?= $candidato ?> <?= ($candidato !== 'Listino bloccato') ? ' di Lista' : '' ?>";
    });
}

// ===========================
// DELETE CANDIDATO
// ===========================
let deleteIdCandidato = null;
let deleteCognome = '';
let deleteNome = '';
let deleteNumero = '';

function deleteCandidato(index) {
    deleteCognome = document.getElementById("cognome"+index).innerText;
    deleteNome = document.getElementById("nome"+index).innerText;
    deleteNumero = document.getElementById("numero"+index).innerText;
    deleteIdCandidato = document.getElementById("id_candidato"+index).innerText;

    document.getElementById("deleteCandidato").textContent = deleteNumero + " - " + deleteCognome;
    $('#confirmDeleteModal').modal('show');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteIdCandidato) return;

    const delCv = document.getElementById('flag_cv')?.checked;
    const delCg = document.getElementById('flag_cg')?.checked;

    const formData = new FormData();
    formData.append('funzione', 'salvaCandidato');
    formData.append('id_candidato', deleteIdCandidato);
    formData.append('cognome', deleteCognome);
    formData.append('nome', deleteNome);
    formData.append('numero', deleteNumero);
	formData.append('id_lista', document.getElementById("idLista").value); // <-- questa riga

    if (delCv || delCg) {
        formData.append('op', 'cancella_parziale');
        if (delCv) formData.append('flag_cv', 1);
        if (delCg) formData.append('flag_cg', 1);
    } else {
        formData.append('op', 'cancella');
    }

    fetch('../principale.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(data => {
        document.getElementById('risultato').innerHTML = data;
        $('#confirmDeleteModal').modal('hide');
        deleteIdCandidato = null;
        resetFormCandidato();
        aggiornaNumero();
    });
});

// ===========================
// EDIT CANDIDATO
// ===========================
function editCandidato(index) {
    document.getElementById("cognome").value = document.getElementById("cognome"+index).innerText;
    document.getElementById("nome").value = document.getElementById("nome"+index).innerText;
    document.getElementById("numero").value = document.getElementById("numero"+index).innerText;
    document.getElementById("id_candidato").value = document.getElementById("id_candidato"+index).innerText;

    const num_listaDiv = document.getElementById("num_lista"+index);
    if (num_listaDiv) document.getElementById("idLista").selectedIndex = num_listaDiv.innerText;

    document.getElementById("btnAggiungi").textContent = "Salva modifiche";
    document.getElementById("titoloCandidato").textContent =
        "Modifica <?= $candidato ?> <?= ($candidato !== 'Listino bloccato') ? ' di Lista' : '' ?>";

    document.getElementById("btnAnnulla").classList.remove('d-none');
    scrollToGestioneCandidato();
}

// ===========================
// RESET / ANNULLA FORM
// ===========================
function annullaModifica() {
    resetFormCandidato();
}

function resetFormCandidato() {
    const form = document.getElementById('candidatoForm');
    const listaIndex = document.getElementById('idLista').selectedIndex;
    form.reset();
    document.getElementById('idLista').selectedIndex = listaIndex;
    document.getElementById('id_candidato').value = '';
    document.getElementById("btnAggiungi").textContent = "Aggiungi";
    document.getElementById('titoloCandidato').textContent =
        "Aggiungi <?= $candidato ?> <?= ($candidato !== 'Listino bloccato') ? ' di Lista' : '' ?>";
    document.getElementById('btnAnnulla').classList.add('d-none');
}

// ===========================
// SCROLL FORM
// ===========================
function scrollToGestioneCandidato() {
    document.getElementById('titoloCandidato')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ===========================
// AGGIORNA ELENCO LISTA
// ===========================
function aggiornaLista() {
    const id_lista = document.getElementById("idLista").value;
    const formData = new FormData();
    formData.append('funzione', 'salvaCandidato');
    formData.append('id_lista', id_lista);
    formData.append('tipo_cons', <?= $tipo_cons ?>);
    formData.append('op', 'aggiorna');

    fetch('../principale.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(data => {
        document.getElementById('risultato').innerHTML = data;
        resetFormCandidato();
        aggiornaNumero();
    });
}

// ===========================
// NUMERO SUCCESSIVO PER LISTA
// ===========================
function aggiornaNumero() {
    const maxNumElem = document.getElementById("maxNumero");
    if (maxNumElem) document.getElementById('numero').value = maxNumElem.textContent;
}

// ===========================
// CONTROLLO DUPLICATI PER LISTA
// ===========================
function posizioneDuplicataCandidato(numeroInserito, idListaEditing, idCandidatoEditing) {
    const righe = document.querySelectorAll('#risultato tr');
    for (let riga of righe) {
        const tdNumero = riga.querySelector('td[id^="numero"]');
        const idListaDiv = riga.querySelector('div[id^="id_lista"]'); // <-- qui prendiamo l'id_lista reale
        const idCandidatoDiv = riga.querySelector('div[id^="id_candidato"]');

        if (!tdNumero || !idListaDiv || !idCandidatoDiv) continue;

        const numeroTabella = tdNumero.textContent.trim();
        const idListaTabella = idListaDiv.textContent.trim();
        const idCandidatoTabella = idCandidatoDiv.textContent.trim();

        if (idCandidatoEditing && idCandidatoEditing === idCandidatoTabella) continue;

        if (numeroTabella === numeroInserito && idListaTabella === idListaEditing) {
            return true; // duplicato trovato nella stessa lista
        }
    }
    return false;
}

</script>
