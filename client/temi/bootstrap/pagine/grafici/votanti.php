<?php
// Dati da PHP
$rowscrutinate=voti_sezione();
$sezionitotali=sezioni_totali();
$scrutinate=0;
foreach($rowscrutinate as $val)
	if($val['validi']+$val['nulli']+$val['bianchi']!=0) $scrutinate++;
$voti=voti_totali();
$validi=number_format($voti[0][0],0,'','.');
$nulli=number_format($voti[0][1],0,'','.');
$bianchi=number_format($voti[0][2],0,'','.');
$contestati=number_format($voti[0][3],0,'','.');


$labels = [
    "Schede Valide: $validi", 
    "Schede Nulle: $nulli",
    "Schede Bianche: $bianchi" 
];  // Etichette con i valori inclusi
$voti = [$voti[0][0], $voti[0][1], $voti[0][2]];  // Dati dei voti
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
        <h4 class="fw-semibold text-primary mobile-expanded mt-2">Distribuzione dei Voti<br>
		</h4>
    </div>
	<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th>Votanti</th>
<?php $oplink="graf_votanti"; $infolink=""; include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>				
				<?php if ($scrutinate == $sezionitotali) {?> 
					<th class="text-end">Dati finali</th>
				<?php } else {?>
					<th class="text-end"><span>Sezioni scrutinate: <?php echo $scrutinate; ?> su <?php echo $sezionitotali; ?></span>
					</th>
				<?php }?>
			</tr>
		</thead>
	</table>
</div>
    <div id="chartContainer">
        <canvas id="votiChart"></canvas>
    </div>
</div>

<script>
    var ctx = document.getElementById('votiChart').getContext('2d');

    // Passaggio dei dati PHP a JavaScript
    var labels = <?php echo json_encode($labels); ?>;
    var voti = <?php echo json_encode($voti); ?>;

    var votiChart = new Chart(ctx, {
        type: 'pie', // Tipo grafico torta
        data: {
            labels: labels,  // Etichette (Schede valide, ecc.)
            datasets: [{
                data: voti, // Dati per ciascun tipo di voto
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)', // Schede valide
                    'rgba(255, 206, 86, 0.6)', // Schede bianche
                    'rgba(255, 99, 132, 0.6)'  // Schede nulle
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, // Grafico responsivo
            maintainAspectRatio: false, // Mantiene proporzioni
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Titillium Web',  // Applica il font anche alla legenda
                            weight: '600',  // Imposta il peso del font
                            size: 14  // Imposta la dimensione del font
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Distribuzione dei Voti (Schede)',
                    font: {
                        family: 'Titillium Web',  // Applica il font al titolo
                        weight: '600',  // Imposta il peso del font
                        size: 16  // Imposta la dimensione del font
                    }
                }
            },
            layout: {
                padding: 20 // Aggiunge padding al grafico per migliorare l'aspetto
            }
        }
    });
</script>
