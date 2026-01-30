<?php
// DEMO DATI (sostituisci con query DB)
$quesiti = [
  [
    'titolo' => 'Quesito 1 - Abrogazione articolo X',
    'si' => 1500,
    'no' => 700
  ],
  [
    'titolo' => 'Quesito 2 - Modifica legge Y',
    'si' => 2100,
    'no' => 1200
  ]
];
?>

<div class="card bg-light" id="box-referendum">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-vote-yea"></i> Referendum</h3>
	<div class="card-tools d-flex align-items-center">
	   <span class="badge badge-info" id="badge-sezioni">
            Sezioni <?php echo $sezioni_scrutinate; ?> su <?php echo $totale_sezioni; ?>
        </span>
    </div>
  </div>

  <div class="card-body">

    <div id="referendum-contenuto">

      <?php foreach($quesiti as $q):

        $tot = $q['si'] + $q['no'];
        $p_si = $tot ? ($q['si'] * 100 / $tot) : 0;
        $p_no = $tot ? ($q['no'] * 100 / $tot) : 0;

      ?>

      <!-- BLOCCO QUESITO -->
      <div class="mb-3 p-2 border rounded">

        <b><?= htmlspecialchars($q['titolo']) ?></b>

        <div class="row text-center mt-2">

          <div class="col-6"><b>SÌ</b></div>
          <div class="col-6"><b>NO</b></div>

          <div class="col-6">
            <?= number_format($q['si']) ?>
          </div>
          <div class="col-6">
            <?= number_format($q['no']) ?>
          </div>

          <div class="col-6 text-success">
            <?= number_format($p_si,1) ?>%
          </div>
          <div class="col-6 text-danger">
            <?= number_format($p_no,1) ?>%
          </div>

        </div>

      </div>

      <?php endforeach; ?>

    </div>

  </div>
</div>

<!-- AUTO REFRESH -->
<script>
function aggiornaReferendum() {
    fetch('dashboard/cards/dati_referendum.php')
      .then(r => r.text())
      .then(html => {
          document.getElementById('referendum-contenuto').innerHTML = html;
      })
      .catch(e => console.error("Errore referendum:", e));
}

// primo caricamento
aggiornaReferendum();

// refresh ogni 60s
setInterval(aggiornaReferendum, 60000);
</script>
