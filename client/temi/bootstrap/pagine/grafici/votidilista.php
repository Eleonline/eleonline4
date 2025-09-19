<?php
// Simulazione dei dati da un database o altra fonte
$scrutinate=scrutinate('lista');
$sezionitotali=sezioni_totali();
$dati = voti_lista_graf(); #voti_tot_lista();
# t1.id_lista,t2.num_gruppo,t2.num_lista,t2.descrizione,sum(t1.voti)
$descr = [];
$perc = [];
$voti = [];
$tot = 0;
$i = 0;
foreach ($dati as $val) 
    $tot += $val[4];
foreach ($dati as $val) {
    $descr[] = $val[3];
    $perc[] = number_format($val[4] / $tot * 100, 2);
    $voti[] = $val[4];
    $i++;
}
$labels = $descr; // Nomi di Lista 
$voti_percentuali = $perc; // Percentuali di Lista 
$voti_lista = $voti; // Aggiungi i voti per ciascuna lista

// Palette di colori per le barre (fino a 30 liste)
$palette = [
    '#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd', '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf',
    '#aec7e8', '#ffbb78', '#98df8a', '#ff9896', '#c5b0d5', '#c49c94', '#f7b6d2', '#c7c7c7', '#dbdb8d', '#9edae5',
    '#393b79', '#637939', '#8c6d31', '#843c39', '#7b4173', '#5254a3', '#8ca252', '#bd9e39', '#ad494a', '#d6616b'
];
?>

<!-- Chart.js -->
<script src="temi/bootstrap/pagine/grafici/js/chart.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<style>
    #chartContainer {
        width: 100%;
        height: <?php echo min(300 + count($labels) * 30, 900); ?>px; /* L'altezza si adatta al numero di liste */
        margin: 0 auto;
    }
    #legendContainer {
        text-align: center;
        margin-bottom: 20px;
    }
    #legendContainer div {
        display: inline-block;
        margin-right: 15px;
    }
</style>

<div class="container">
    <div class="row text-center">
        <h4 class="fw-semibold text-primary mobile-expanded mt-2">Voti di Lista<br>
		</h4>
    </div>
<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th><?php echo _LISTE;?></th> 
				<?php $oplink="graf_lista"; $infolink=""; include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>	
				<?php if ($scrutinate == $sezionitotali) {?> 
					<th class="text-end">Dati finali</th>
				<?php } else {?>
					<th class="text-end"><span>Sezioni scrutinate: <?php echo $scrutinate; ?> su <?php echo $sezionitotali; ?></span></th>
				<?php }?>
			</tr>
		</thead>
	</table>
</div>	
    <!-- Legenda sopra il grafico -->
    <div class="row mb-3">
        <div class="col">
            <div id="legendContainer"></div>
        </div>
    </div>

    <div id="chartContainer">
        <canvas id="affluenzaChart"></canvas>
    </div>
</div>

<!-- Script per il grafico -->
<script>
    var ctx = document.getElementById('affluenzaChart').getContext('2d');

    var labels = <?php echo json_encode($labels); ?>;
    var dataPercentuali = <?php echo json_encode($voti_percentuali); ?>;
    var dataVoti = <?php echo json_encode($voti_lista); ?>;
    var palette = <?php echo json_encode($palette); ?>;

    var datasets = [{
        label: 'Voti %',
        data: dataPercentuali,
        backgroundColor: palette.slice(0, dataPercentuali.length),
        borderWidth: 1,
        barThickness: 30,  // Larghezza delle barre
        categoryPercentage: 1.0,  // Occupa tutta la larghezza disponibile
        barPercentage: 0.8  // Percentuale della larghezza della barra
    }];

    var affluenzaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',  // Barre orizzontali
            scales: {
                x: {
                    stacked: true,
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 10 // Aumenta la dimensione dei tick dell'asse X
                    }
                },
                y: {
                    stacked: true,
                    ticks: {
                        beginAtZero: true,
                        padding: 15 // Spazio tra le barre
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Nasconde la legenda di Chart.js, verrà gestita manualmente
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var index = context.dataIndex;
                            var percentuale = context.raw;
                            var numero_voti = dataVoti[index];
                            return percentuale + '% (' + numero_voti.toLocaleString() + ' voti)';
                        }
                    }
                },
                // Plugin per inserire i valori percentuali fuori dalla barra
                datalabels: {
                    display: true,
                    color: '#000000',  // Colore del testo
                    align: 'end',      // Posiziona il testo alla fine della barra
                    formatter: function(value, context) {
                        var percentuale = dataPercentuali[context.dataIndex];
                        return percentuale + '%'; // Mostra solo la percentuale
                    },
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    anchor: 'end',  // Ancorato alla fine della barra
                    offset: 5       // Distanza tra la barra e la percentuale
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // Creazione della legenda personalizzata sopra il grafico
    var legendContainer = document.getElementById('legendContainer');
    labels.forEach((label, index) => {
        var legendItem = document.createElement('div');
        legendItem.innerHTML = `<span style="display:inline-block;width:12px;height:12px;background-color:${palette[index]};margin-right:5px;"></span>${label}`;
        legendContainer.appendChild(legendItem);
    });
</script>
