<?php
// Dati dinamici da PHP
//sum(voti_uomini),sum(voti_donne),sum(voti_complessivi) as complessivi,data,orario
$row=totale_iscritti(0);
$tot=$row[0][2];
$row=scrutinio_affluenze(0);
$scrutinate=$row[0][0];
$sezionitotali=sezioni_totali();
$row=affluenze_totali(0);
$num_aff=count($row);
$labels=array();
$voti_percentuali=array();
$voti=array();
foreach($row as $val) {
	$labels[]=[date('d-m-Y', strtotime($val[3])),$val[4]]; #."$tot"
	$voti_percentuali[]=number_format($val[2]/$tot*100,2);
	$voti[]=number_format($val[2],0,'','.');
}
$rowpre=precedente_consultazione();
if(count($rowpre)){
	$preidcg=$rowpre[0]['id_cons_gen'];
	$row=dati_consultazione($preidcg);
	$descr_cons=$row[0]['descrizione'];
	$rowpre=conscomune($preidcg);
	$preidcons=$rowpre[0][0];
	$rowpre=totale_iscritti($preidcons);
	$totprec=$rowpre[0][2];
	$rowpre=affluenze_totali($preidcons);
	$num_aff_pre=count($rowpre);
#	$labels=array();
	$voti_prec_percentuali=array();
	$voti_prec=array();
	if($num_aff!=$num_aff_pre) {$rowpre=array(array_pop($rowpre));$diff=1;$num_aff_pre=1;} else $diff=0;
	foreach($rowpre as $val) {
		while($num_aff>$num_aff_pre) {
			array_unshift($voti_prec_percentuali,0);
			array_unshift($voti_prec,0);
			$num_aff_pre++; 
		}
		if($num_aff<$num_aff_pre) {
			$num_aff_pre--; 
			array_shift($voti_prec_percentuali);
			array_shift($voti_prec);
			continue;
		}
		$voti_prec_percentuali[]= $diff ? 0 : number_format($val[2]/$totprec*100,2);
		$voti_prec[]= $diff ? 0 : number_format($val[2],0,'','.');
	}
		if($diff) {array_shift($voti_prec_percentuali);array_shift($voti_prec);$voti_prec_percentuali[]=number_format($val[2]/$totprec*100,2);$voti_prec[]=number_format($val[2],0,'','.');}

}else{
	foreach($voti as $val){
		$voti_prec_percentuali[]='';
		$voti_prec[]='';
	}
}

?>
<!-- Chart.js -->
<script src="temi/bootstrap/pagine/grafici/js/chart.umd.js"></script>
<style>
    #chartContainer {
        width: 100%;
        height: <?php echo min(300 + count($labels) * 30, 900); ?>px; /* L'altezza si adatta al numero di liste */
        margin: 0 auto;
    }
</style>

<div class="container">
    <div class="row text-center">
        <h4 class="fw-semibold text-primary mobile-expanded mt-2">Affluenza</h4>
    </div>
	<?php $oplink="come"; $infolink="affluenze_sez"; include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>
	<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th>Affluenza</th> 
				<?php if ($scrutinate == $sezionitotali) {?> 
					<th class="text-end">Dati finali</th>
				<?php } else {?>
					<th class="text-end"><span>Sezioni scrutinate: <?php echo $scrutinate; ?> su <?php echo $sezionitotali; ?></span></th>
				<?php }?>
			</tr>
		</thead>
	</table>
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
    var voti_percentuali = <?php echo json_encode($voti_percentuali); ?>;
    var voti = <?php echo json_encode($voti); ?>;
    var voti_prec_percentuali = <?php echo json_encode($voti_prec_percentuali); ?>;
    var voti_prec = <?php echo json_encode($voti_prec); ?>;

    // Funzione per sostituire i valori nulli o vuoti con 0
    function handleNullValues(array) {
        return array.map(value => value === null || value === undefined ? 0 : value);
    }

    // Gestire i valori nulli per i dataset
    voti_prec_percentuali = handleNullValues(voti_prec_percentuali);
    voti_prec = handleNullValues(voti_prec);

    var affluenzaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Affluenza Attuale',
                    data: voti_percentuali,
                    backgroundColor: 'rgba(240,128,128)',  // Rosso brillante
                    borderWidth: 1
                } <?php if(count($rowpre)) { ?>,
                {
                    label: 'Affluenza Consultazione: <?php echo addslashes($descr_cons); if($diff) echo " (Dato Finale)";?>',
                    data: voti_prec_percentuali,
                    backgroundColor: 'rgba(70,130,180)',  // Blu acciaio
                    borderWidth: 1
                }
				<?php } ?>
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100
                },
                y: {
                    ticks: {
                        stepSize: 1
                    },
                    // Dinamica della larghezza delle barre
                    barThickness: (window.innerWidth <= 768) ? 30 : 50  // Modifica la larghezza della barra per dispositivi mobili
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        title: function (tooltipItems) {
                            // Manteniamo le due righe di etichetta in formato leggibile
                            const index = tooltipItems[0].dataIndex;
                            return labels[index].join(' - ');
                        },
                        label: function (context) {
                            const index = context.dataIndex;
                            const datasetIndex = context.datasetIndex;

                            // Selezione del dataset giusto: attuale o precedente
                            const isCurrentDataset = datasetIndex === 0;
                            const percentuale = isCurrentDataset
                                ? voti_percentuali[index]
                                : voti_prec_percentuali[index];
                            const votiValue = isCurrentDataset
                                ? voti[index]
                                : voti_prec[index];

                            const affluenzaType = isCurrentDataset ? 'Attuale' : 'Precedente';

                            return `${affluenzaType}: ${percentuale}% (${votiValue} voti)`;
                        }
                    }
                }
            }
        },
        plugins: [
            {
                id: 'insideBarPercentage',
                afterDatasetDraw: function (chart) {
                    const ctx = chart.ctx;

                    chart.data.datasets.forEach((dataset, datasetIndex) => {
                        if (!chart.isDatasetVisible(datasetIndex)) {
                            return;
                        }

                        dataset.data.forEach((value, index) => {
                            const meta = chart.getDatasetMeta(datasetIndex).data[index];

                            // Se il valore è zero, non disegnare la barra ma non eliminarla completamente
                            if (value === 0) {
                                return; // Non disegnare il testo se il valore è zero
                            }

                            const votoValue = datasetIndex === 0
                                ? voti[index] || 0
                                : voti_prec[index] || 0;

                            // Mostra la percentuale e i voti in modo coerente
                            const text = value + '% (' + votoValue + ' voti)';

                            const x = meta.base + (meta.width + 65);
                            const y = meta.y;

                            ctx.save();
                            ctx.fillStyle = '#000000';  // Scritta nera
                            ctx.font = 'bold 14px Titillium Web, Arial';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(text, x, y);
                            ctx.restore();
                        });
                    });
                }
            }
        ]
    });
</script>
