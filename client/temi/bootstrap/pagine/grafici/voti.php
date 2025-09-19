<?php
// Simulazione dei dati da un database o altra fonte
$labels = ['Lista A', 'Lista B', 'Lista C', 'Lista D', 'Lista E']; //Aggiungi i Nomi
$voti_percentuali = [65, 72, 58, 80, 15]; //Aggiungi Percentuali
$voti = [1430, 1570, 1200, 2000, 300]; // Aggiungi i voti 
?>

<!-- Chart.js -->
<script src="temi/bootstrap/pagine/grafici/js/chart.umd.js"></script>

<style>
    #chartContainer {
        width: 100%;
        height: 400px;
        margin: 0 auto;
    }

    /* Applica il font Titillium Web al grafico */
    body, .chartjs-tooltip, .chartjs-legend, .chartjs-tooltip-table, .chartjs-title {
        font-family: 'Titillium Web', sans-serif;
    }
</style>

<div class="container">
    <div class="row text-center">
        <h4 class="fw-semibold text-primary mobile-expanded mt-2">Voti espressi di Lista</h4>
    </div>
    <div id="chartContainer">
        <canvas id="affluenzaChart"></canvas>
    </div>
</div>

<!-- Script per il grafico -->
<script>
    var ctx = document.getElementById('affluenzaChart').getContext('2d');

    // Passaggio dei dati PHP a JavaScript
    var labels = <?php echo json_encode($labels); ?>;
    var dataPercentuali = <?php echo json_encode($voti_percentuali); ?>;
    var dataVoti = <?php echo json_encode($voti); ?>;

    var affluenzaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,  // Dati delle etichette (Lista A, Lista B, ecc.)
            datasets: [
                {   // Percentuali di voti
                    label: 'Voti %',
                    data: dataPercentuali,  // Dati percentuali
                    backgroundColor: 'rgba(135,206,250, 0.8)',  // Azzurro
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',  // Barre orizzontali
            scales: {
                x: {
                    stacked: true,
                    beginAtZero: true,
                    max: 100
                },
                y: {
                    stacked: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var index = context.dataIndex;
                            var percentuale = context.raw;
                            var numero_voti = dataVoti[index];
                            return percentuale + '% (' + numero_voti.toLocaleString() + ' voti)';
                        }
                    }
                }
            }
        },
        plugins: [
            {
                id: 'insideBarPercentage',
                afterDatasetDraw: function(chart) {
                    const ctx = chart.ctx;

                    chart.data.datasets[0].data.forEach((value, index) => {
                        const meta = chart.getDatasetMeta(0).data[index];  // Seleziona la barra corretta
                        
                        // Calcolo orizzontale per il centro della barra
                        const x = meta.base + (meta.width / 2);
                        
                        // Calcolo verticale per il centro della barra
                        const y = meta.y;

                        // Voti numerici da mostrare
                        var numero_voti = dataVoti[index];

                        ctx.save();
                        ctx.fillStyle = '#000000';  // Colore del testo nero per un buon contrasto
                        ctx.font = 'bold 14px "Titillium Web", sans-serif';  // Usa il font Titillium Web
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';  // Centro del testo verticalmente
                        ctx.fillText(value + '% (' + numero_voti.toLocaleString() + ' voti)', x, y);  // Testo centrato
                        ctx.restore();
                    });
                }
            }
        ]
    });
</script>
