<?php
$quesiti = elenco_gruppi('gruppo');
$num_gruppo = isset($_GET['num_gruppo']) ? intval($_GET['num_gruppo']) : 1;

$id_gruppo = null;
$nome_quesito = '';

foreach ($quesiti as $val) {
    if ($val['num_gruppo'] == $num_gruppo) {
        $id_gruppo = $val['id_gruppo'];
        $nome_quesito = $val['descrizione'];
        break;
    }
}

$rowscrutinate = voti_referendum($id_gruppo);
$sezionitotali = sezioni_totali();
$scrutinate = $si_raw = $no_raw = 0;

foreach ($rowscrutinate as $val) {
    if ($val['si'] + $val['no'] != 0) {
        $scrutinate++;
        $si_raw += $val['si'];
        $no_raw += $val['no'];
    }
}

$tot = $si_raw + $no_raw;
$perc_si = $tot > 0 ? number_format($si_raw / $tot * 100, 2) : 0;
$perc_no = $tot > 0 ? number_format($no_raw / $tot * 100, 2) : 0;

$si = number_format($si_raw, 0, '', '.');
$no = number_format($no_raw, 0, '', '.');

$quesito = [
    'Quesito ' . $num_gruppo => [[$perc_si, $si], [$perc_no, $no]],
];

$labels = array_keys($quesito);
?>

<!-- Chart.js -->
<script src="temi/bootstrap/pagine/grafici/js/chart.umd.js"></script>

<style>
    #chartContainer {
        width: 100%;
        height: 500px;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        #chartContainer {
            height: 700px;
        }
    }
</style>

<!-- Blocco select quesito referendum -->
<div class="container pb-2">
    <label for="defaultSelect">Seleziona Quesito</label>
    <select id="defaultSelect" onchange="location = this.value;">
        <?php foreach ($quesiti as $val) : ?>
            <option value="modules.php?op=53&id_comune=<?= $id_comune; ?>&file=index&id_cons_gen=<?= $id_cons_gen; ?>&num_gruppo=<?= $val['num_gruppo']; ?>"
                <?= $num_gruppo == $val['num_gruppo'] ? 'selected' : ''; ?>>
                Quesito <?= $val['num_gruppo']; ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Tabella risultati -->
<div class="container">
    <div class="row text-center">
        <h4 class="fw-semibold text-primary mobile-expanded mt-2">Risultati Referendum</h4>
    </div>
	<?php $oplink="come"; $infolink="affluenze_sez"; include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>	
    <div class="table-responsive overflow-x">
        <table class="table mb-0">
            <thead class="title-content">
                <tr>
                    <th>Risultati Referendum</th> 
                    <th class="text-end">
                        <?= $scrutinate == $sezionitotali ? 'Dati finali' : "Sezioni scrutinate: $scrutinate su $sezionitotali"; ?>
                    </th>
                </tr>
            </thead>
        </table>
    </div>
    
    <div id="chartContainer">
        <canvas id="risultatiChart"></canvas>
    </div>
</div>

<script>
    var ctx = document.getElementById('risultatiChart').getContext('2d');

    var labels = <?= json_encode($labels); ?>;
    var quesiti = <?= json_encode($quesito); ?>;
    const predefinedColors = ['rgb(54, 162, 235)', 'rgb(255, 99, 132)'];

    var datasets = [
        {
            label: 'Sì',
            data: labels.map(label => quesiti[label][0][0]),
            backgroundColor: predefinedColors[0],
            borderWidth: 1
        },
        {
            label: 'No',
            data: labels.map(label => quesiti[label][1][0]),
            backgroundColor: predefinedColors[1],
            borderWidth: 1
        }
    ];

    var chartHeight = Math.max(500, labels.length * 80);
    document.getElementById('chartContainer').style.height = chartHeight + 'px';

   var risultatiChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: datasets
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        scales: {
            x: { beginAtZero: true, max: 100 },
            y: {
    ticks: { stepSize: 1 },
    barThickness: "flex",  // Adatta lo spessore automaticamente
    maxBarThickness: 50  // Imposta un limite massimo per evitare che le barre diventino troppo grandi
}

        },
        plugins: {
            legend: { display: true },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        var datasetIndex = context.datasetIndex;
                        var dataIndex = context.dataIndex;
                        var tipoVoto = context.dataset.label;
                        var percentuale = context.raw;
                        var voti = quesiti[context.label][datasetIndex][1];
                        return tipoVoto + ': ' + percentuale + '% (' + voti + ' voti)';
                    }
                }
            }
        }
    },
    plugins: [{
        id: 'outsideBarLabels',
        afterDatasetDraw: function (chart) {
            const ctx = chart.ctx;
            chart.data.datasets.forEach(function (dataset, datasetIndex) {
                if (!chart.isDatasetVisible(datasetIndex)) return;

                dataset.data.forEach(function (value, index) {
                    const meta = chart.getDatasetMeta(datasetIndex).data[index];
                    const x = meta.x + 10; // Sposta il testo fuori dalla barra
                    const y = meta.y;

                    const text = value + '% (' + quesiti[chart.data.labels[index]][datasetIndex][1] + ' voti)';

                    ctx.save();
                    ctx.fillStyle = '#000000';
                    ctx.font = 'bold 16px Titillium Web, Arial';
                    ctx.textAlign = 'left'; // Allinea il testo fuori dalla barra
                    ctx.textBaseline = 'middle';
                    ctx.fillText(text, x, y);
                    ctx.restore();
                });
            });
        }
    }]
});

</script>
