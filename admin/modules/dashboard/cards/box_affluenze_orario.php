<?php
// Inizializzo sempre l'array
$affluenze = [];

// Recupero dati
if ($tipo_cons == 2) {
    $row = affluenze_referendum(1, 0); // numero referendum, id_cons
} else {
    $row = affluenze_totali(0); // id_cons o 0 per quella corrente
}

// Popolo l'array delle affluenze
if (!empty($row)) {
    foreach ($row as $val) {
        $affluenze[] = [
            'data' => $val['data'] . ' ' . $val['orario'],
            'val' => $val['complessivi'] ?? 0
        ];
    }
}

// Valore massimo per il grafico
$maxElettori = isset($comune['elettori']) ? $comune['elettori'] : 1000;
?>

<div class="card bg-light">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Affluenze Demo</h3>
  </div>
  <div class="card-body" style="height:250px; display:flex; align-items:center; justify-content:center;">
    <?php if (empty($affluenze)) : ?>
        <span style="color:#888; font-weight:bold;">Nessun dato disponibile</span>
    <?php else : ?>
        <canvas id="graficoAffluenzeOrario"></canvas>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($affluenze)) : ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('graficoAffluenzeOrario').getContext('2d');

  const labels = [
    <?php foreach ($affluenze as $a) echo "'" . addslashes($a['data']) . "',"; ?>
  ];
  const dataValues = [
    <?php foreach ($affluenze as $a) echo floatval($a['val']) . ","; ?>
  ];

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Elettori Presenti',
        data: dataValues,
        backgroundColor: 'rgba(54,162,235,0.7)',
        borderColor: 'rgba(54,162,235,1)',
        borderWidth: 1
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          beginAtZero: true,
          max: <?= $maxElettori ?>,
          ticks: { stepSize: 500 }
        },
        y: {
          ticks: { autoSkip: false }
        }
      },
      plugins: {
        legend: { display: true, position: 'top' },
        tooltip: { mode: 'index', intersect: false }
      }
    }
  });
});
</script>
<?php endif; ?>
