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
global $sezione_attiva;
echo '<script>

function selezionaSezione(str) {
  if (str == "") {
    document.getElementById("txtHint").innerHTML = "";
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
				document.getElementById("sezioneContent").innerHTML = this.responseText;
		}
    }
    xmlhttp.open("GET","../principale.php?funzione=leggiBarraSezioni&num_sez="+str,true);
    xmlhttp.send();
  }
}

function salva_voti() { 
	const validi = parseInt(document.getElementById("votiValidi").value);
	const nulle = parseInt(document.getElementById("schedeNulle").value);
	const bianche = parseInt(document.getElementById("schedeBianche").value);
	const vnulli = parseInt(document.getElementById("votiNulli").value);
	const vcontestati = parseInt(document.getElementById("votiContestati").value);
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			;	document.getElementById("sezioneContent").innerHTML = this.responseText;
		}
	}
	xmlhttp.open("GET","../principale.php?funzione=salvaVoti&id_sez="+idSez+"&validi="+validi+"&nulle="+nulle+"&bianche="+bianche+"&vnulli="+vnulli+"&vcontestati="+vcontestati,true);
	xmlhttp.send();
}
  
function salva_voti_lista(numListe,idSez) {
	let url = ""
	for(let i=1 ; i<=numListe ; i++) {
		url += "&lista"+document.getElementById("lista"+i).name+"="+document.getElementById("lista"+i).value
	}
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			;	document.getElementById("sezioneContent").innerHTML = this.responseText;
		}
	}
	xmlhttp.open("GET","../principale.php?funzione=salvaVotiLista&id_sez="+idSez+url,true);
	xmlhttp.send();
  
}

</script>';
#$sezione_attiva = 1; // Se vuoi segnare una sezione iniziale come attiva

/*
// Liste fisse
$liste = [
  ['id' => 1, 'nome' => 'ALLEANZA VERDI E SINISTRA'],
  ['id' => 2, 'nome' => 'MOVIMENTO 5 STELLE'],
  ['id' => 3, 'nome' => 'FRATELLI D\'ITALIA']
];

// Voti per ogni lista e per ogni sezione
$votiSezione = [
  1 => [1 => 18, 2 => 44, 3 => 56],
  2 => [1 => 20, 2 => 50, 3 => 30],
  3 => [1 => 0, 2 => 0, 3 => 0],
  4 => [1 => 30, 2 => 35, 3 => 50],
  5 => [1 => 25, 2 => 30, 3 => 45]
];
*/
// Dati voti totali per ogni sezione

/*$votitotali = [
  '1' => ['validi' => 120, 'nulle' => 2, 'bianche' => 3, 'nulli' => 1, 'contestati' => 0],
  '2' => ['validi' => 150, 'nulle' => 1, 'bianche' => 4, 'nulli' => 2, 'contestati' => 1],
  '3' => ['validi' => 0, 'nulle' => 0, 'bianche' => 0, 'nulli' => 0, 'contestati' => 0],
  '4' => ['validi' => 115, 'nulle' => 173, 'bianche' => 2, 'nulli' => 0, 'contestati' => 0],
  '5' => ['validi' => 100, 'nulle' => 3, 'bianche' => 2, 'nulli' => 0, 'contestati' => 0],
];
*/
// Passaggio dati al JavaScript
include_once("barra_sezioni.php");
#echo "<script>const votitotaliSezioni = " . json_encode($votitotali) . ";</script>";
?>
<?php /*

<script>
function aggiornaCampiSezione(sezione) {
  const dati = votitotaliSezioni[sezione];
  if (!dati) {
    console.warn("Sezione non trovata:", sezione);
    return;
  }

  // Imposta valori input
  document.getElementById('votiValidi').value = dati.validi;
  document.getElementById('schedeNulle').value = dati.nulle;
  document.getElementById('schedeBianche').value = dati.bianche;
  document.getElementById('votiNulli').value = dati.nulli;
  document.getElementById('votiContestati').value = dati.contestati;

  // Calcolo totali
  const totNonValidi = dati.nulle + dati.bianche + dati.nulli + dati.contestati;
  const totaliVoti = dati.validi + totNonValidi;

  // Aggiorna il testo delle celle totali
  document.getElementById('totNonValidi').textContent = totNonValidi;
  document.getElementById('totaliVoti').textContent = totaliVoti;
}

// Esempio: carica dati per la sezione 1 appena si carica la pagina
window.onload = () => {
  const primaSezione = '1';
  aggiornaCampiSezione(primaSezione);
  aggiornaVotiSezione(primaSezione);
  aggiornaTotaleVotiLista();

  // Attiva stile bottone primario per la prima sezione
  document.querySelector(`#sezioniBtn button[data-sezione="${primaSezione}"]`)
    ?.classList.replace('btn-outline-primary', 'btn-primary');

  // Colora tutti i bottoni
  Object.keys(votiSezioniJS).forEach(sezione => {
    validaSezione(sezione, false); // solo colore, nessun messaggio
  });

  // Mostra messaggio errore solo per la sezione attiva (1)
  validaSezione(primaSezione, true);
};


function aggiornaTotaleVotiLista() {
  let totale = 0;
  document.querySelectorAll('.voto-lista').forEach(input => {
    let val = parseInt(input.value) || 0;
    totale += val;
  });
  document.getElementById('totaleVotiLista').textContent = totale;
  aggiornaTotaliFinali();
}

function aggiornaTotaliFinali() {
  const votiValidi = parseInt(document.getElementById('votiValidi').value) || 0;
  const nulle = parseInt(document.getElementById('schedeNulle').value) || 0;
  const bianche = parseInt(document.getElementById('schedeBianche').value) || 0;
  const nulli = parseInt(document.getElementById('votiNulli').value) || 0;
  const contestati = parseInt(document.getElementById('votiContestati').value) || 0;

  const nonValidi = nulle + bianche + nulli + contestati;
  const totali = votiValidi + nonValidi;

  document.getElementById('totNonValidi').textContent = nonValidi;
  document.getElementById('totaliVoti').textContent = totali;
}

// Eventi per aggiornare automaticamente i totali
document.querySelectorAll('.voto-lista').forEach(input => {
  input.addEventListener('input', aggiornaTotaleVotiLista);
});
document.querySelectorAll('#schedeNulle, #schedeBianche, #votiNulli, #votiContestati').forEach(input => {
  input.addEventListener('input', aggiornaTotaliFinali);
});

// Gestione reset voti
const checkboxElimina = document.getElementById('eliminaRiga');
const btnConfermaElimina = document.getElementById('btnConfermaElimina');

checkboxElimina.addEventListener('change', function () {
  btnConfermaElimina.style.display = this.checked ? 'inline-block' : 'none';
});

btnConfermaElimina.addEventListener('click', function () {
  if (checkboxElimina.checked) {
    document.querySelectorAll('.voto-lista').forEach(input => input.value = '');
    aggiornaTotaleVotiLista();
    checkboxElimina.checked = false;
    btnConfermaElimina.style.display = 'none';
  }
});


// Passo dati PHP a JS
  const datiVotantiUltimaOra = <?php echo json_encode($votantiUltimaOra); ?>;


  // Imposto il primo bottone come attivo all'avvio
  document.querySelector('#sezioniBtn button').classList.replace('btn-outline-primary', 'btn-primary');
  
  // Dati voti PHP passati a JS
const votiSezioniJS = <?php echo json_encode($votiSezione); ?>;

// Funzione per aggiornare la tabella voti in base alla sezione selezionata
function aggiornaVotiSezione(sezione) {
  const voti = votiSezioniJS[sezione];
  if (!voti) return;

  document.querySelectorAll('.voto-lista').forEach(input => {
    const idLista = input.getAttribute('data-lista-id');
    input.value = voti[idLista] || 0;
  });
  aggiornaTotaleVotiLista();
}

document.querySelectorAll('#sezioniBtn button').forEach(btn => {
  btn.addEventListener('click', function () {
    const sezione = this.getAttribute('data-sezione');

    // Aggiorna intestazione
    document.getElementById('titoloSezione').textContent = 'Sezione n. ' + sezione;

    // Cambia stile bottone attivo
    document.querySelectorAll('#sezioniBtn button').forEach(b => {
      b.classList.remove('btn-primary');
      b.classList.add('btn-outline-primary');
    });
    this.classList.remove('btn-outline-primary');
    this.classList.add('btn-primary');

    // Aggiorna dati sezione
    aggiornaCampiSezione(sezione);
    aggiornaVotiSezione(sezione);
    aggiornaTotaleVotiLista();

    // Aggiorna votanti ultima ora
    const dati = datiVotantiUltimaOra[sezione];
    if (dati) {
      document.getElementById('tabellaVotanti').innerHTML = `
        <tr>
          <td><strong>Votanti Uomini</strong><br>${dati.uomini}</td>
          <td><strong>Votanti Donne</strong><br>${dati.donne}</td>
          <td><strong>Totali</strong><br>${dati.totali}</td>
        </tr>`;
    }

    // Valida solo questa sezione e mostra messaggio se errore
    validaSezione(sezione, true);
  });
});



// Inizializza totale voti lista all'avvio
aggiornaTotaleVotiLista();

</script>
<script>
  // Variabile globale che segnala se ci sono errori nella validazione
  let erroreVal = false;

  function mostraMessaggio(errore, titolo, contenuto) {
    const box = document.getElementById('boxMessaggio');
    const boxBody = document.getElementById('boxBody');

    boxBody.className = 'card-body ' + (errore ? 'bg-danger text-white' : 'bg-success text-white');

    document.getElementById('titoloMsg').textContent = titolo;
    document.getElementById('contenutoMsg').textContent = contenuto;

    box.style.display = "block";
  }

  document.addEventListener("DOMContentLoaded", () => {
    const btnOk = document.getElementById("btnOkFinale");
    const btnSezioni = document.querySelectorAll("#sezioniBtn .btn");

    // Bottone verde di salvataggio (o simile)
    document.querySelector('button.btn-success').addEventListener('click', function (e) {
      // Nascondi messaggio precedente
      document.getElementById('boxMessaggio').style.display = 'none';

      // Rimuovi is-invalid da tutti i campi interessati
      const campi = ['votiValidi', 'schedeNulle', 'schedeBianche', 'votiNulli', 'votiContestati'];
      campi.forEach(id => {
        document.getElementById(id).classList.remove('is-invalid');
      });

      const vv = parseInt(document.getElementById('votiValidi').value) || 0;
      const sn = parseInt(document.getElementById('schedeNulle').value) || 0;
      const sb = parseInt(document.getElementById('schedeBianche').value) || 0;
      const vn = parseInt(document.getElementById('votiNulli').value) || 0;
      const vc = parseInt(document.getElementById('votiContestati').value) || 0;

      const tdTotali = document.querySelector('#tabellaVotanti tr td:nth-child(3)');
      const ta = parseInt(tdTotali.childNodes[2].textContent.trim()) || 0;

      const tl = Array.from(document.querySelectorAll('.voto-lista')).reduce((a, el) => a + (parseInt(el.value) || 0), 0);

      const totNonVal = sn + sb + vn + vc;
      const totVoti = vv + totNonVal;

      let errore = false;
      let titolo = "";
      let contenuto = "";

      if (tl !== vv) {
        errore = true;
        erroreVal = true;
        titolo = "Attenzione!";
        contenuto = `Il totale voti di lista (${tl}) non corrisponde ai voti validi (${vv}).`;

        // Solo votiValidi invalidi
        document.getElementById('votiValidi').classList.add('is-invalid');
      }
      else if (ta !== totVoti) {
        errore = true;
        erroreVal = true;
        titolo = "Attenzione!";
        contenuto = `I votanti (${ta}) non corrispondono ai voti totali (${totVoti}).`;

        // Tutti i campi invalidi
        campi.forEach(id => {
          document.getElementById(id).classList.add('is-invalid');
        });
      }
      else {
  errore = false;
  erroreVal = false;
  titolo = "OK";
  contenuto = "I dati sono stati salvati correttamente.";

  // ✅ Rimuovi classi di errore se tutto è corretto
  campi.forEach(id => {
    document.getElementById(id).classList.remove('is-invalid');
  });
  document.querySelectorAll('.voto-lista.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}



      mostraMessaggio(errore, titolo, contenuto);

      if (errore) {
        // Porta il focus al primo campo invalido
        const primoInvalido = document.querySelector('.is-invalid');
        if (primoInvalido) primoInvalido.focus();
      }
    });

    // Gestione colore bottone OK Finale
    btnOk.addEventListener("click", () => {
      // Trova il bottone attivo (primario o colorato)
      const btnAttivo = document.querySelector("#sezioniBtn .btn-primary, #sezioniBtn .btn-verde, #sezioniBtn .btn-rosso");
      if (!btnAttivo) return;

     // Pulisci solo il bottone attivo
btnAttivo.classList.remove("btn-verde", "btn-rosso");


      if (erroreVal) {
        // Se errore di validazione mantiene rosso
        btnAttivo.classList.add("btn-rosso");
      } else {
        // Altrimenti verifica la somma voti lista == voti validi
        const validi = parseInt(document.getElementById("votiValidi").value || 0);
        let sommaLista = 0;
        document.querySelectorAll(".voto-lista").forEach(input => {
          sommaLista += parseInt(input.value || 0);
        });

        if (sommaLista === validi) {
          btnAttivo.classList.add("btn-verde");
        } else {
          btnAttivo.classList.add("btn-rosso");
        }
      }

      
    // Scrolla verso l'inizio della pagina (alto)
	document.getElementById('boxMessaggio').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });
  

function validaSezione(sezione, mostraMessaggio = false) {
  // Nasconde sempre il box messaggio prima di qualsiasi validazione
  if (!mostraMessaggio) {
  // Nascondi solo se non devi mostrare un nuovo messaggio
  document.getElementById('boxMessaggio').style.display = 'none';
}


  const btn = document.querySelector(`#sezioniBtn button[data-sezione="${sezione}"]`);
  if (!btn) return;

  const voti = votiSezioniJS[sezione] || {};
  const votanti = datiVotantiUltimaOra[sezione] || {};
  const dati = votitotaliSezioni[sezione] || {};

  const votiValidi = parseInt(dati.validi) || 0;
  const nulle = parseInt(dati.nulle) || 0;
  const bianche = parseInt(dati.bianche) || 0;
  const nulli = parseInt(dati.nulli) || 0;
  const contestati = parseInt(dati.contestati) || 0;

  const totNonValidi = nulle + bianche + nulli + contestati;
  const totaliVoti = votiValidi + totNonValidi;

  const sommaListe = Object.values(voti).reduce((acc, val) => acc + (parseInt(val) || 0), 0);
  const votantiTotali = parseInt(votanti.totali) || 0;

 // Se tutti i dati (escluso votanti) sono zero o null, non validare né colorare
if (
  votiValidi === 0 &&
  totNonValidi === 0 &&
  sommaListe === 0 &&
  Object.values(votiSezioniJS[sezione] || {}).every(val => !parseInt(val)) &&
  Object.values(votitotaliSezioni[sezione] || {}).every(val => !parseInt(val))
) {
  // Togli colore e messaggi
  btn.classList.remove("btn-verde", "btn-rosso");
  if (mostraMessaggio) {
    document.getElementById('boxMessaggio').style.display = 'none';
  }
  return;
}


  let errore = false;
  let titolo = "";
  let contenuto = "";

  if (sommaListe !== votiValidi) {
    errore = true;
    titolo = "Errore!";
    contenuto = `Il Totale Voti di lista (${sommaListe}) non corrisponde ai Voti Validi (${votiValidi}).`;
  } else if (totaliVoti !== votantiTotali) {
    errore = true;
    titolo = "Errore!";
    contenuto = `I Votanti Totali (${votantiTotali}) non corrispondono ai Voti Totali (${totaliVoti}).`;
  }

  btn.classList.remove("btn-verde", "btn-rosso");

  if (errore) {
    btn.classList.add("btn-rosso");
    if (mostraMessaggio) mostraMessaggioErrore(titolo, contenuto, sezione);
  } else {
    btn.classList.add("btn-verde");
  }
}


function mostraMessaggioErrore(titolo, contenuto, sezione = null) {
  const box = document.getElementById('boxMessaggio');
  const boxBody = document.getElementById('boxBody');
  document.getElementById('titoloMsg').textContent = titolo;
  document.getElementById('contenutoMsg').textContent = contenuto;

  boxBody.className = 'card-body bg-danger text-white';
  box.style.display = "block";

  // Scroll verso l'inizio
  box.scrollIntoView({ behavior: 'smooth', block: 'start' });

  if (sezione) {
    // Prova a trovare l'input dei voti validi per quella sezione (es. id="validi_12")
    const campoErrore = document.querySelector(`#validi_${sezione}`);
    if (campoErrore) {
      campoErrore.classList.add('is-invalid');  // aggiunge evidenza errore
      //setTimeout(() => campoErrore.focus(), 500); // focus dopo un piccolo delay
    }
  }
}
</script>

*/
?>
