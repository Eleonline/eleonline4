<?php include __DIR__.'/_data_simulati.php'; ?>
<div class="card bg-light">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Grafico Demo</h3>
  </div>
  <div class="card-body">
    <canvas id="graficoDemo" style="height:200px;"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('graficoDemo').getContext('2d');
  const graficoDemo = new Chart(ctx, {
    type: 'bar', // tipo di grafico: bar, line, pie, doughnut, ecc.
    data: {
      labels: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno'],
      datasets: [{
        label: 'Elettori',
        data: [
          <?= $comune['elettori'] - 500 ?>,
          <?= $comune['elettori'] - 300 ?>,
          <?= $comune['elettori'] - 200 ?>,
          <?= $comune['elettori'] ?>,
          <?= $comune['elettori'] + 100 ?>,
          <?= $comune['elettori'] + 200 ?>
        ],
        backgroundColor: 'rgba(54, 162, 235, 0.7)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
});
</script>
