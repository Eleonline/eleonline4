<?php
// Inizializzo l'array
$affluenze = [];

// Recupero dati
if ($tipo_cons == 2) {
    $row = affluenze_referendum(1, 0);
} else {
    $row = affluenze_totali(0);
}

$maxElettori = isset($comune['elettori']) ? $comune['elettori'] : 1000;

if (!empty($row)) {
    foreach ($row as $val) {
        $elettoriPresenti = $val['complessivi'] ?? 0;
        $percentuale = $maxElettori > 0 ? ($elettoriPresenti / $maxElettori) * 100 : 0;

        // Formatta la data in italiano
        $dataItaliano = '';
        if(!empty($val['data'])) {
            $dt = DateTime::createFromFormat('Y-m-d H:i:s', $val['data'] . ' ' . ($val['orario'] ?? '00:00:00'));
            if($dt) $dataItaliano = $dt->format('d/m/Y H:i');
        }

        $affluenze[] = [
            'data' => $dataItaliano ?: ($val['data'] . ' ' . ($val['orario'] ?? '')),
            'perc' => round($percentuale,1),
            'val'  => $elettoriPresenti
        ];
    }
}
?>


<div class="card bg-light">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Affluenze</h3>
	<div class="card-tools d-flex align-items-center">
	   <span class="badge badge-info" id="badge-sezioni">
            Sezioni <?php echo $sezioni_scrutinate; ?> su <?php echo $totale_sezioni; ?>
        </span>
    </div>
  </div>
  <div class="card-body">
    <?php if (empty($affluenze)) : ?>
        <span style="color:#888; font-weight:bold;">Nessun dato disponibile</span>
    <?php else : ?>
        <?php foreach($affluenze as $a): ?>
          <div class="mb-2">
            <div class="d-flex justify-content-between mb-1">
              <span><?= htmlspecialchars($a['data']) ?></span>
            </div>
            <div class="progress affluenze-bar" 
                 style="position: relative; background-color:#ddd;">
              <div class="progress-bar bg-info" role="progressbar" 
                   style="width: <?= $a['perc'] ?>%;" 
                   aria-valuenow="<?= $a['perc'] ?>" aria-valuemin="0" aria-valuemax="100">
              </div>
              <span class="affluenze-text">
                <?= $a['perc'] ?>% (<?= number_format($a['val'],0,'.','.') ?>)
              </span>
            </div>
          </div>
        <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<style>
/* Barra e testo responsive */
.affluenze-bar {
  height: 30px;
}

.affluenze-text {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  font-weight: bold;
  color: black;
  font-size: 1rem;
  white-space: nowrap;
}

/* Mobile: barra più alta e testo leggermente più piccolo */
@media (max-width: 767px) {
  .affluenze-bar {
    height: 25px;
  }
  .affluenze-text {
    font-size: 0.85rem;
  }
}
</style>

<script>
// Auto-refresh
function aggiornaAffluenze() {
  fetch('dashboard/cards/dati_grafico_affluenze.php')
    .then(res => res.json())
    .then(data => {
      const container = document.querySelector('.card-body');
      container.innerHTML = '';
      if(!data.length){
        container.innerHTML = '<span style="color:#888; font-weight:bold;">Nessun dato disponibile</span>';
        return;
      }
      data.forEach(a => {
        const div = document.createElement('div');
        div.className = 'mb-2';
        div.innerHTML = `
          <div class="d-flex justify-content-between mb-1">
            <span>${a.data}</span>
          </div>
          <div class="progress affluenze-bar" style="position: relative; background-color:#ddd;">
            <div class="progress-bar bg-info" role="progressbar" style="width:${a.perc}%" aria-valuenow="${a.perc}" aria-valuemin="0" aria-valuemax="100"></div>
            <span class="affluenze-text">
              ${a.perc}% (${a.val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")})
            </span>
          </div>
        `;
        container.appendChild(div);
      });
    })
    .catch(err => console.error("Errore aggiornamento affluenze:", err));
}

// Primo caricamento e refresh ogni 60 sec
aggiornaAffluenze();
setInterval(aggiornaAffluenze, 60000);
</script>
