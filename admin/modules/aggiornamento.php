<?php 
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';

if(!isset($_GET['errmex'])) {

    $row = configurazione();
    $rev_locale = $row[0]['patch'];

    if ($stream = fopen('http://mail.eleonline.it/version4/risposta.php', 'r')) {

        $rev_online = trim(stream_get_contents($stream));
		$_SESSION['remoterev'] = $rev_online;
        fclose($stream);
        $rev_locale = $row[0]['patch'] ?? '';
        $_SESSION['remoterev'] = $rev_online;


    } else {

        $errmex = 2;
        Header("Location: modules/modules.php?op=aggiorna&id_cons_gen=$id_cons_gen&errmex=$errmex");
        exit;
    }

    $host = "https://trac.eleonline.it";

    if ($rev_locale != $rev_online) {

        // Crea cartella tmp se non esiste
        $log_dir = realpath(__DIR__ . '/../tmp');

        if (!$log_dir) {
            $log_dir = __DIR__ . '/../tmp';
            if (!is_dir($log_dir)) {
                mkdir($log_dir, 0755, true);
            }
        }

        $log_file = "$log_dir/changelog_rev_{$rev_locale}_{$rev_online}.log";

        // ===== LETTURA RSS =====

        libxml_use_internal_errors(true);

        $rssUrl = "$host/eleonline4/log?format=rss&mode=stop_on_copy&rev=$rev_online&stop_rev=$rev_locale&max=100";

        $log_contents = '';
        $data_di_aggiornamento = '';

        $rss = simplexml_load_file($rssUrl);

        if ($rss !== false) {

            $currentDate = '';

            foreach ($rss->channel->item as $item) {

                preg_match('/Revision\s+([A-Za-z0-9]+)/', (string)$item->title, $m);
				$rev = $m[1] ?? '';


                $timestamp = strtotime((string)$item->pubDate);
                $data = date('Y-m-d', $timestamp);

                if ($data_di_aggiornamento === '' && $timestamp) {
                    $data_di_aggiornamento = date('d/m/Y', $timestamp);
                }

                if ($data !== $currentDate) {

                    $dt = DateTime::createFromFormat('Y-m-d', $data);
                    $dataIt = $dt ? $dt->format('d/m/Y') : $data;

                    $log_contents .= "
                    <div class='mt-3 mb-2'>
                        <strong>
                            <i class='far fa-calendar-alt me-1'></i> $dataIt
                        </strong>
                    </div>";

                    $currentDate = $data;
                }

                $desc = strip_tags((string)$item->description);
                $righe = preg_split('/\r\n|\r|\n/', $desc);

                $log_contents .= "<div class='mb-2'>
                    <span class='badge badge-info me-2'>rev $rev</span>
                    <ul>";

                foreach ($righe as $riga) {
                    if (trim($riga) != '') {
                        $log_contents .= "<li>" . htmlspecialchars(trim($riga)) . "</li>";
                    }
                }

                $log_contents .= "</ul></div>";
            }

        } else {

            $log_contents = "<div class='alert alert-warning'>
                Impossibile caricare il changelog remoto.
            </div>";
        }

        if ($log_contents == '') {

            $log_contents = "<div class='alert alert-info'>
                Nessuna voce di changelog disponibile.
            </div>";
        }

    }

}
?>

<section class="content">
  <div class="container-fluid mt-4">

    <!-- Card iniziale: messaggio aggiornamento disponibile o no -->
    <div id="cardIniziale" class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cogs me-2"></i>Aggiornamento Sistema</h3>
      </div>
      <div class="card-body">
        <?php if ($rev_online != $rev_locale): ?>
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            È disponibile un aggiornamento alla revisione <strong><?= $rev_online ?></strong>.
          </div>
          <h5>Log delle modifiche:</h5>
          <?= $log_contents ?>
        <?php else: ?>
  <div class="alert alert-success">
    <i class="fas fa-check-circle me-2"></i>
    Non sono disponibili nuovi aggiornamenti.<br>
    Il sistema è aggiornato (revisione <?= $rev_locale ?>).
  </div>

  <div class="alert alert-info mt-4">
    <strong>Procedo con verifica e aggiornamento del solo db (all'ultima versione scaricata)?</strong><br>
    <em>È consigliato procedere solo dopo aver prodotto una copia di backup.</em>
    <div class="mt-3">
      <button id="btnAggiornaDB" class="btn btn-warning">
        <i class="fas fa-database me-2"></i>Aggiorna solo DB
      </button>
    </div>
  </div>

 <?php endif; ?>

      </div>
      <div class="card-footer text-end">
        <?php
		// if ($rev_online > $rev_locale): 
		 if ($rev_online != $rev_locale):
		 ?>
          <button id="btnAggiorna" class="btn btn-primary"><i class="fas fa-download me-2"></i>Aggiorna ora</button>
        <?php else: ?>
          <a href="" class="btn btn-secondary"><i class="fas fa-sync-alt me-2"></i>Verifica di nuovo</a>
        <?php endif; ?>
      </div>
    </div>
 <!-- Area log per aggiornamento DB -->
  
  

    <!-- Card aggiornamento in corso: nascosta inizialmente -->
    <div id="cardAggiornamento" class="card card-info shadow-sm" style="display:none;">
      <div class="card-header">
        <h3 class="card-title" id="titoloAggiornamento"><i class="fas fa-spinner fa-spin me-2"></i>Aggiornamento in corso...</h3>
      </div>
      <div class="card-body">
        <pre id="logAggiornamento" style="white-space: pre-wrap; background:#f5f5f5; border:1px solid #ccc; padding:10px; height:300px; overflow:auto; font-family: monospace;"></pre>
      </div>
    </div>
 <!-- Card aggiornamento db in corso: nascosta inizialmente -->
<div id="cardAggiornaDB" class="card card-info shadow-sm" style="display:none;">
      <div class="card-header">
        <h3 class="card-title" id="titoloAggiornaDB"><i class="fas fa-spinner fa-spin me-2"></i>Aggiornamento Database in corso...</h3>
      </div>
      <div class="card-body">
        <div id="logAggiornaDB" class="mt-3" style="display:none; white-space: pre-wrap; background:#f0f0f0; border:1px solid #ccc; padding:10px; max-height:200px; overflow:auto; font-family: monospace;"></div>
      </div>
    </div>

  </div>
</section>

<script src="../plugins/jquery/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

  $('#btnAggiorna').click(function(){
    $('#cardIniziale').hide();
    $('#cardAggiornamento').show();
    $('#logAggiornamento').html('');
    $('#btnAggiorna').prop('disabled', true);

    let steps = {}; // chiave = nome step, valore = stato

    function renderSteps() {
      let outputHtml = '';
      for (let step in steps) {
        if (steps[step] === 'ok') {
          outputHtml += '<div>' + step + ' <span style="color:green;">✔ OK</span></div>';
        } else {
          outputHtml += '<div>' + step + '</div>';
        }
      }
      $('#logAggiornamento').html(outputHtml);
      $('#logAggiornamento').scrollTop($('#logAggiornamento')[0].scrollHeight);
    }

    $.ajax({
      url: '../principale.php',  // <-- Ecco dove parte l'aggiornamento reale lato server
      type: 'POST',
      data: {
		funzione: 'aggiornaRev',  
        data_rev: <?= json_encode($data_di_aggiornamento ?: '') ?>,
        rev_locale: <?= json_encode($rev_locale) ?>,
        rev_online: <?= json_encode($rev_online) ?>
      },
      xhrFields: {
        onprogress: function(e) {
          let raw = e.currentTarget.response;
          let lines = raw.split('\n').filter(line => line.trim() !== '');

          lines.forEach(function(line) {
            if (line.startsWith("__STEP__")) {
              let key = line.replace("__STEP__", "").trim();
              if (!steps[key]) steps[key] = 'in_corso';
              renderSteps();
            } else if (line.startsWith("__OK__")) {
              let key = line.replace("__OK__", "").trim();
              steps[key] = 'ok';
              renderSteps();
            } else if (line.startsWith("__FINISH__")) {
              let msg = line.replace("__FINISH__", "");
              $('#titoloAggiornamento').html('<i class="fas fa-check-circle me-2"></i>Aggiornamento completato');
              $('#logAggiornamento').append('<div style="color:green; margin-top:10px;">' + msg + '</div>');
              $('#btnAggiorna').prop('disabled', false);
            } else {
              // aggiungi testo log normale
              $('#logAggiornamento').append('<div>' + $('<div>').text(line).html() + '</div>');
              $('#logAggiornamento').scrollTop($('#logAggiornamento')[0].scrollHeight);
            }
          });
        }
      },
      success: function() {
        // eventuale azione al termine
      },
      error: function() {
        $('#logAggiornamento').append('<div style="color:red;">Errore durante l\'aggiornamento.</div>');
        $('#btnAggiorna').prop('disabled', false);
      }
    });
  });

document.getElementById('btnAggiornaDB').addEventListener('click', async function () {
  document.getElementById('cardIniziale').style.display = 'none';
  document.getElementById('cardAggiornaDB').style.display = 'block';

  const log = document.getElementById('logAggiornaDB');
  const btn = document.getElementById('btnAggiornaDB');
  const titolo = document.getElementById('titoloAggiornaDB');

  log.innerHTML = '';
  log.style.display = 'block';
  btn.disabled = true;
  btn.textContent = 'Aggiornamento DB in corso...';

  try {
    const response = await fetch('aggiornadb_output.php');

    if (!response.ok || !response.body) throw new Error("Errore nella risposta");

    const reader = response.body
      .pipeThrough(new TextDecoderStream())
      .getReader();

    let { value, done } = await reader.read();
    while (!done) {
      const lines = value.split('\n').filter(line => line.trim() !== '');
      lines.forEach(line => {
        const safeLine = document.createElement('div');
        safeLine.textContent = line;
        log.appendChild(safeLine);
      });
      log.scrollTop = log.scrollHeight;
      ({ value, done } = await reader.read());
    }

    btn.disabled = false;
    btn.textContent = 'Aggiorna solo DB';
    titolo.innerHTML = '<i class="fas fa-check-circle text-success me-2"></i>Aggiornamento completato';
    const success = document.createElement('div');
    success.style.color = 'green';
    success.style.marginTop = '10px';
    success.textContent = 'Aggiornamento DB completato con successo.';
    log.appendChild(success);
    log.scrollTop = log.scrollHeight;

  } catch (err) {
    btn.disabled = false;
    btn.textContent = 'Aggiorna solo DB';
    titolo.innerHTML = '<i class="fas fa-times-circle text-danger me-2"></i>Errore aggiornamento DB';
    const error = document.createElement('div');
    error.style.color = 'red';
    error.textContent = 'Errore durante l\'aggiornamento del DB.';
    log.appendChild(error);
    log.scrollTop = log.scrollHeight;
  }
});
});
</script>

