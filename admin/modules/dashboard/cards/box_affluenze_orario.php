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
    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Affluenze</h3>
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
<script>
let chartAffluenze;

function aggiornaGraficoAffluenze() {
  fetch('dashboard/cards/dati_grafico_affluenze.php')
    .then(res => res.json())
    .then(data => {
      const labels = data.map(a => a.data);
      const values = data.map(a => parseFloat(a.val));

      const maxElettori = <?= isset($comune['elettori']) ? $comune['elettori'] : 1000 ?>;
      const ctx = document.getElementById('graficoAffluenzeOrario').getContext('2d');

      if(chartAffluenze) {
        // Aggiorna dati già esistenti
        chartAffluenze.data.labels = labels;
        chartAffluenze.data.datasets[0].data = values;
        chartAffluenze.options.scales.x.max = maxElettori;
        chartAffluenze.update();
      } else if(data.length) {
        // Crea il grafico la prima volta
        chartAffluenze = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Elettori Presenti',
              data: values,
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
              x: { beginAtZero: true, max: maxElettori, ticks: { stepSize: 500 } },
              y: { ticks: { autoSkip: false } }
            },
            plugins: { legend: { display: true, position: 'top' }, tooltip: { mode: 'index', intersect: false } }
          }
        });
      }
    })
    .catch(err => console.error("Errore aggiornamento grafico:", err));
}

// Primo caricamento
aggiornaGraficoAffluenze();

// Auto-refresh ogni 60 secondi
setInterval(aggiornaGraficoAffluenze, 60000);
</script>

