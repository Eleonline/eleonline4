<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
?>
<style>
/* Rimuove freccette nei campi number (Chrome, Safari) */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
/* Rimuove freccette nei campi number (Firefox) */
input[type="number"] {
  -moz-appearance: textfield;
}

/* Ottimizzazione larghezze tabelle */
.smartable th:nth-child(1),
.smartable td:nth-child(1) {
  width: 50px;
  text-align: center;
}
.smartable th:nth-child(3),
.smartable td:nth-child(3) {
  width: 100px;
  text-align: center;
}
.smartable td input {
  text-align: right;
  width: 100%;
}

/* Spazi verticali ridotti */
section.content > .container-fluid > * {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

h3, h5 {
  margin-top: 0.75rem;
  margin-bottom: 0.75rem;
}

/* Rimuove margine extra da btn-group */
.mb-3 {
  margin-bottom: 0.5rem !important;
}

/* Riduce margine checkbox + bottone */
.d-flex.align-items-center.mt-2 {
  margin-top: 0.5rem !important;
  margin-bottom: 0.5rem !important;
}
input[type=number].text-end {
  text-align: right !important;
}
  #boxMessaggio {
    max-width: 500px;
    margin: 20px auto; /* centro orizzontale con margine sopra e sotto */
    display: none;
    text-align: center;
  }
  .btn-verde {
  border: 3px solid #28a745 !important;
  box-shadow: 0 0 5px #28a745;
}
.btn-rosso {
  border: 3px solid #dc3545 !important;
  box-shadow: 0 0 5px #dc3545;
}

</style>

<?php
global $sezione_attiva,$id_sez,$tipo;
#echo "<script>const idSez = " . json_encode($id_sez) . ";</script>";	
$tipo=2;
$row=elenco_gruppi();
$numgruppi=count($row);
/*
foreach($row as $key=>$val)
	$liste[]=['id' => $val['id_lista'],'num' => $val['num_lista'],'nome' => $val['descrizione']];
$row=voti_lista_sezione($id_sez);
$tot_voti_lista=0;
foreach($row as $key=>$val){
	$votiSezione[$id_sez][$val['num_lista']]= $val['voti'];
$tot_voti_lista+=$val['voti'];} */
?>
<section class="content" id="sezioneContent">
<div id="divBarraSezioni"> <?php include_once("barra_sezioni.php"); ?> </div>
</section>
<div id="divPaginaGruppi"> <?php include('pagina_voti_gruppo.php'); ?> </div>
<div id="divVotiFinale"> <?php include('pagina_voti_finali.php'); ?> </div>



<script>

function salva_voti(e) {
    e.preventDefault(); // blocca il submit normale

	const str = parseInt(document.getElementById("id_sezione").value);
	const validi = parseInt(document.getElementById("votiValidi").value);
	const nulle = parseInt(document.getElementById("schedeNulle").value);
	const bianche = parseInt(document.getElementById("schedeBianche").value);
	const vnulli = parseInt(document.getElementById("votiNulli").value);
	const vcontestati = parseInt(document.getElementById("votiContestati").value);

    const formData = new FormData(); 
	formData.append('funzione', 'salvaVotiFinale');
    formData.append('validi', validi);
    formData.append('nulle', nulle);
    formData.append('bianche', bianche);
    formData.append('vnulli', vnulli);
    formData.append('vcontestati', vcontestati);
    formData.append('id_sez', str);
    formData.append('op', 'salva');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => {
        if(!response.ok) throw new Error('Server risponde con status ' + response.status);
        return response.text();
    })
    .then(data => {
		document.getElementById('divVotiFinale').innerHTML = data;
		aggiorna_sezione(str);
    })
    .catch(error => {
        console.error('Errore fetch:', error);
    });
  
}
  
function salva_voti_gruppo(e) {

    e.preventDefault(); // blocca il submit normale
	const str = document.getElementById("numSezGruppo").value;
	const id_sez = document.getElementById("idSezGruppo").value;
	const numGruppi = document.getElementById("numGruppi").value;
    const formData = new FormData(); 
    formData.append('funzione', 'salvaVotiGruppo');
	for(let i=1 ; i<=numGruppi ; i++) {
		formData.append('gruppo-'+document.getElementById("gruppo"+i).name, document.getElementById("gruppo"+i).value);
	}
    formData.append('id_sez', id_sez);
    formData.append('op', 'salva');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => {
        if(!response.ok) throw new Error('Server risponde con status ' + response.status);
        return response.text();
    })
    .then(data => {
		document.getElementById('divPaginaGruppi').innerHTML = data;
		aggiorna_sezione(id_sez);
    })
    .catch(error => {
        console.error('Errore fetch:', error);
    });
  
}


function selezionaSezione(str) {
	const numsez=str;
    const formData = new FormData(); 
	formData.append('funzione', 'leggiBarraSezioni');
    formData.append('num_sez', str);
    formData.append('tipo', '2');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => {
        if(!response.ok) throw new Error('Server risponde con status ' + response.status);
        return response.text();
    })
    .then(data => {
		document.getElementById('divBarraSezioni').innerHTML = data;
		aggiorna_gruppo(numsez);
		aggiorna_voti(numsez);
    })
    .catch(error => {
        console.error('Errore fetch:', error);
    });
}

function aggiorna_gruppo(str) {

    const formData = new FormData(); 
	formData.append('funzione', 'salvaVotiGruppo');
    formData.append('num_sez', str);
    formData.append('op', 'aggiornaGruppo');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => {
        if(!response.ok) throw new Error('Server risponde con status ' + response.status);
        return response.text();
    })
    .then(data => {
		document.getElementById('divPaginaGruppi').innerHTML = data;
    })
    .catch(error => {
        console.error('Errore fetch:', error);
    });
  
}

function aggiorna_sezione(str) {

	const num_sez=document.getElementById("numSezGruppo").value;
    const formData = new FormData(); 
	formData.append('funzione', 'leggiBarraSezioni');
    formData.append('id_sez', str);
    formData.append('num_sez', num_sez);
    formData.append('tipo', '2');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => {
        if(!response.ok) throw new Error('Server risponde con status ' + response.status);
        return response.text();
    })
    .then(data => {
		document.getElementById('divBarraSezioni').innerHTML = data;
    })
    .catch(error => {
        console.error('Errore fetch:', error);
    });
  
}


function aggiorna_voti(str) {

    const formData = new FormData(); 
	formData.append('funzione', 'salvaVotiFinale');
    formData.append('num_sez', str);
    formData.append('op', 'aggiorna_voti');

    fetch('../principale.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => {
        if(!response.ok) throw new Error('Server risponde con status ' + response.status);
        return response.text();
    })
    .then(data => {
		document.getElementById('divVotiFinale').innerHTML = data;
    })
    .catch(error => {
        console.error('Errore fetch:', error);
    });
  
}
</script>
