<?php
if (is_file('includes/check_access.php')) {
    require_once 'includes/check_access.php';
} else {
    require_once '../includes/check_access.php';
}

global $currentUserRole;

$non_autorizzati = elenco_utenti_no_permessi();
$sedi    = elenco_sedi();
$sezioni = elenco_sezioni();

$row = dati_consultazione(0);
$descrizione = $row[0]['descrizione'];

if (!count($non_autorizzati)) {
    $nascondi = 'style="display:none;"';
    $testo   = 'Non sono presenti altri utenti da autorizzare';
} else {
    $nascondi = '';
    $testo   = 'Aggiungi il permesso per un utente';
}
?>

<div class="container-fluid mt-3">
    <h2>
        <i class="fas fa-users-cog"></i>
        Gestione Permessi per la consultazione "<?= $descrizione ?>"
    </h2>
</div>

<!-- FORM -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title" id="form-title"><?= $testo ?></h3>
    </div>

    <div class="card-body" id="card-body" <?= $nascondi ?>>
        <form id="userForm" onsubmit="aggiungiUser(event)">
            <div id="risultato1">

                <div class="form-row">

                    <!-- UTENTE -->
                    <div class="form-group col-md-2" id="divutente">
                        <label for="utente">Utente</label>
                        <select class="form-control" id="utente" name="utente">
                            <?php foreach ($non_autorizzati as $val): ?>
                                <?php if (!$val['aid']) continue; ?>
                                <option value="<?= $val['aid'] ?>">
                                    <?= $val['aid'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- LIVELLO -->
                    <div class="form-group col-md-3" id="divlivello">
                        <label for="livello">Livello di autorizzazione</label>
                        <select class="form-control" id="livello" name="livello" onchange="scegliTipo()">
                            <option value="0">Tutte le sezioni</option>
                            <option value="1">Singola sede</option>
                            <option value="2">Singola sezione</option>
                        </select>
                    </div>

                    <!-- SEDI -->
                    <div class="form-group col-md-2" id="divelencosedi" style="display:none;">
                        <label for="sedi">Sedi</label>
                        <select class="form-control" id="sedi" name="sedi">
                            <?php foreach ($sedi as $val): ?>
                                <option value="<?= $val['id_sede'] ?>">
                                    <?= $val['indirizzo'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- SEZIONI -->
                    <div class="form-group col-md-2" id="divelencosezioni" style="display:none;">
                        <label for="sezioni">Sezioni</label>
                        <select class="form-control" id="sezioni" name="sezioni">
                            <?php foreach ($sezioni as $val): ?>
                                <option value="<?= $val['id_sez'] ?>">
                                    <?= $val['num_sez'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <!-- AZIONI -->
               <div class="form-group col-md-12 mt-3" id="divazioni">
    <button type="submit" class="btn btn-success" id="submitBtn" <?= $nascondi ?>>
        Aggiungi Utente
    </button>
    <button type="button" class="btn btn-secondary" id="cancelBtn" style="display:none;" onclick="annullaModifica()">
        Annulla
    </button>
</div>
            </div>
        </form>
    </div>
</div>

<!-- TABELLA -->
<table class="table table-bordered table-hover" id="usersTable">
    <thead>
        <tr>
            <th>Username</th>
            <th>Sede</th>
            <th>Sezione</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>

    <?php
    $row = elenco_permessi();
    $i = 0;
    foreach ($row as $val):
        $i++;
    ?>
        <tr id="riga<?= $i ?>">
            <td id="utente<?= $i ?>"><?= $val['aid'] ?></td>
            <td id="sedi<?= $i ?>"><?= $val['indirizzo'] ?></td>
            <td id="sezioni<?= $i ?>"><?= $val['num_sez'] ?></td>
            <td>
                <button class="btn btn-sm btn-warning me-1" onclick="editUser(<?= $i ?>)">
                    Modifica
                </button>

                <?php if (
                    $currentUserRole != 'operatore' &&
                    $val['adminsuper'] != 1 &&
                    $val['admincomune'] != '1'
                ): ?>
                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $i ?>)">
                        Elimina
                    </button>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>
<script>
function scegliTipo() { //visualizza/nasconde le select di sedi e sezioni
	const val=document.getElementById ( "livello" ).value;
	if(val=='2'){
		document.getElementById('divelencosedi').style.display = 'none'
		document.getElementById('divelencosezioni').style.display = 'block'
	}else if(val=='1') {
		document.getElementById('divelencosedi').style.display = 'block'
		document.getElementById('divelencosezioni').style.display = 'none'
	
	}else {
		document.getElementById('divelencosedi').style.display = 'none'
		document.getElementById('divelencosezioni').style.display = 'none'
		
	}
		
}

function editUser(index) {
    const cardBody = document.getElementById('card-body');
    cardBody.style.display = 'block';

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.style.display = 'inline-block';
    submitBtn.textContent = 'Salva modifiche';
    submitBtn.dataset.modifica = index;

    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.style.display = 'inline-block';

    document.getElementById("utente").value = document.getElementById("utente" + index).innerText;
    const sedeSelect = document.getElementById("sedi");
    if (sedeSelect) sedeSelect.value = document.getElementById("sedi" + index).innerText;
    const sezioniSelect = document.getElementById("sezioni");
    if (sezioniSelect) sezioniSelect.value = document.getElementById("sezioni" + index).innerText;

    cardBody.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function annullaModifica() {
    const myForm = document.getElementById('userForm');
    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const cardBody = document.getElementById('card-body');

    myForm.reset();
    submitBtn.textContent = "Aggiungi Utente";
    delete submitBtn.dataset.modifica;
    cancelBtn.style.display = 'none';

    // Se non ci sono utenti rimasti nel select, nascondi il form
    const x = document.getElementById("utente");
    if (x.options.length == 0) {
        cardBody.style.display = 'none';
        submitBtn.style.display = 'none';
        document.getElementById("form-title").innerText = "Non sono presenti altri utenti da autorizzare";
    } else {
        document.getElementById("form-title").innerText = "Aggiungi il permesso per un utente";
    }
}





function aggiungiUser(e) { // nuovi inserimenti e modifiche
    e.preventDefault();
    var sedi = 0;
    var sezioni = 0;
    const livello = document.getElementById("livello").value;
    const utente = document.getElementById("utente").value;
    if (livello == 1) 
        sedi = document.getElementById("sedi").value;
    else if (livello == 2)
        sezioni = document.getElementById("sezioni").value;

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
        const cardBody = document.getElementById('card-body');
        const submitBtn = document.getElementById('submitBtn');

        risultato.innerHTML = data; // Mostra la risposta del server
        myForm.reset();
        submitBtn.textContent = "Aggiungi Utente";
        submitBtn.style.display = 'inline-block';

        // Se era una modifica, nascondi di nuovo il form
        if (submitBtn.textContent === "Aggiungi Utente") {
            cardBody.style.display = 'none';
        }

        // Controlla se ci sono ancora utenti non autorizzati
        const x = document.getElementById("utente");
        if (x.options.length == 0) {
            submitBtn.style.display = 'none';
            document.getElementById("form-title").innerText = "Non sono presenti altri utenti da autorizzare";
        } else {
            document.getElementById("form-title").innerText = "Aggiungi il permesso per un utente";
        }
    });
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
