<?php
// Dati demo (_data_simulati.php o definiti qui)
$comune = [
    'nome'=>'Demo Comune',
    'elettori'=>7500,
    'sezioni'=>6,
];
$affluenze = [
    ['data'=>'2025-12-19 08:00', 'val'=>500],
    ['data'=>'2025-12-19 10:00', 'val'=>1200],
    ['data'=>'2025-12-19 12:00', 'val'=>2500],
    ['data'=>'2025-12-19 14:00', 'val'=>4000],
    ['data'=>'2025-12-19 16:00', 'val'=>5500],
    ['data'=>'2025-12-19 18:00', 'val'=>6800],
];
?>

<div class="card bg-light">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Affluenze Demo</h3>
  </div>
  <div class="card-body" style="height:250px;">
    <canvas id="graficoAffluenzeOrario"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('graficoAffluenzeOrario').getContext('2d');

  const labels = [
    <?php foreach($affluenze as $a) echo "'".$a['data']."',"; ?>
  ];
  const dataValues = [
    <?php foreach($affluenze as $a) echo $a['val'].","; ?>
  ];

  new Chart(ctx, {
    type: 'bar', // grafico orizzontale a barre
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
      indexAxis: 'y', // rende il grafico orizzontale
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          beginAtZero: true,
          max: <?= $comune['elettori'] ?>,
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
