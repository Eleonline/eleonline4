<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
global $currentUserRole;
$non_autorizzati=elenco_utenti_no_permessi(); 
# carica elenco sedi in select nascosta
$sedi=elenco_sedi();
$sezioni=elenco_sezioni();
# carica elenco sezioni in select nascosta  <div class="form-group col-md-3">
$row=dati_consultazione(0);
$descrizione=$row[0]['descrizione'];
if(!count ($non_autorizzati)) {
	$nascondi='style="display:none;"';
	$testo='Non sono presenti altri utenti da autorizzare';
}else{
	$nascondi='';
	$testo='Aggiungi il permesso per un utente';
}	
?> 
  <div class="container-fluid mt-3">
    <h2><i class="fas fa-users-cog"></i> Gestione Permessi per la consultazione "<?= $descrizione ?>"</h2>
</div>
<!-- FORM -->
<div class="card mb-4">
  <div class="card-header bg-primary text-white">
    <h3 class="card-title" id="form-title"><?= $testo ?></h3>
  </div>
  <div class="card-body" id="card-body" <?= $nascondi ?>>
    <form id="userForm" onsubmit="aggiungiUser(event)">
      <div id="risultato1">

		<div class="form-row" >
			<div class="form-group col-md-2" id="divutente">
				<label for="utente">Utente</label>
				<select class="form-control" id="utente" name="utente">
				<?php if($non_autorizzati) $nascondi=''; else $nascondi='style="display:none;"';
				foreach($non_autorizzati as $key=>$val) {
					if(!$val['aid']) continue;
					echo "<option value=\"".$val['aid']."\">".$val['aid']."</option>";
				}
				?>
				</select>
			</div>

			<div class="form-group col-md-3" id="divlivello">
				<label for="livello">Livello di autorizzazione</label>
				<select class="form-control" id="livello" name="livello" onchange='scegliTipo()'>
                <option value="0">Tutte le sezioni</option>
                <option value="1">Singola sede</option>
                <option value="2">Singola sezione</option>
              </select>				
			</div>
			<div class="form-group col-md-2" id="divelencosedi" style="display:none;">
				<label for="sedi">Sedi</label>
				<select class="form-control" id="sedi" name="sedi">
				<?php foreach($sedi as $key=>$val) 
					echo "<option value=\"".$val['id_sede']."\">".$val['indirizzo']."</option>";
				?>
				</select>
			</div>
			<div class="form-group col-md-2" id="divelencosezioni" style="display:none;">
				<label for="sezioni">Sezioni</label>
				<select class="form-control" id="sezioni" name="sezioni">
				<?php foreach($sezioni as $key=>$val) 
					echo "<option value=\"".$val['id_sez']."\">".$val['num_sez']."</option>";
					
				?>
				</select>			
			</div>
		</div>
		<div class="form-group col-md-2" id="divazioni">
			<button type="submit" class="btn btn-success" id="submitBtn"  <?= $nascondi ?>>Aggiungi Utente <?= $nascondi ?></button>
		</div>



     </div>

    </form>
  </div>
</div>
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
global $currentUserRole; #return;
$row=elenco_permessi();

foreach($row as $key=>$val){
	$key++;
	echo "<tr id=\"riga$key\"><td id=\"utente$key\">".$val['aid']."</td><td id=\"sedi$key\">".$val['indirizzo']."</td><td id=\"sezioni$key\">".$val['num_sez']."</td><td><button class=\"btn btn-sm btn-warning me-1\" onclick=\"editUser($key)\">Modifica</button>";
	if($currentUserRole != 'operatore' and $val['adminsuper']!=1 and $val['admincomune']!='1') echo "<button class=\"btn btn-sm btn-danger\" onclick=\"deleteUser($key)\">Elimina</button>"; echo"</td></tr>";
}

 ?>
</tbody></table>
