<?php

$quesiti=elenco_gruppi('gruppo');
if(isset($_GET['num_gruppo'])) $num_gruppo=intval($_GET['num_gruppo']); else $num_gruppo=1;
foreach($quesiti as $val) if($val['num_gruppo']==$num_gruppo) {$id_gruppo=$val['id_gruppo']; $nome_quesito='Quesito '. $val['num_gruppo'];}
$rowscrutinate=voti_referendum($id_gruppo);
$sezionitotali=sezioni_totali();
$scrutinate=0;
$validi=0;
$nulli=0;
$bianchi=0;
$contestati=0;
foreach($rowscrutinate as $val)
	if($val['validi']+$val['nulli']+$val['bianchi']!=0) {
		$scrutinate++;
		$validi+=$val['validi'];
		$nulli+=$val['nulli'];
		$bianchi+=$val['bianchi'];
		$contestati+=$val['contestati'];
	}
#$validi=number_format($validi,0,'','.');
#$nulli=number_format($nulli,0,'','.');
#$bianchi=number_format($bianchi,0,'','.');
#$contestati=number_format($contestati,0,'','.');
// Simulazione dei dati per un quesito
 $quesito = [
    "Schede Valide" => $validi,
    "Schede Bianche" => $bianchi,
    "Schede Nulle" => $nulli
]; /*
$nome_quesito = $quesiti"Quesito 1"; // Nome del quesito dinamico
$scrutinate =10;
$sezionitotali=15;*/
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .chart-container {
        width: 50%;
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
    }
</style>
<!-- Blocco select quesito referndum-->	
		<div class="container pb-2">
			<label for="defaultSelect">Seleziona Quesito</label>
			<select id="defaultSelect" onchange="location = this.value;">
				<!-- option selected>Selezione Quesito</option -->
					<?php
					$desc='';
					foreach($quesiti as $key=>$val) { 
						if ($num_gruppo==$val[1]) {
							$id_gruppo=$val['id_gruppo']; $sel='selected';
						} else {
							$sel='';
						}?>
						<option <?php echo $sel; ?> value=" <?php echo "modules.php?op=52&id_comune=$id_comune&id_cons_gen=$id_cons_gen&num_gruppo=".$val[1];?>">Quesito <?php echo $val['num_gruppo'];?></option>
					<?php }?>
			</select>
		</div>
		<!-- fine Blocco select quesito referndum-->
<div class="container">
	<div class="row text-center">
		<h4 class="fw-semibold text-primary mobile-expanded mt-2">Dettaglio Voti espressi</h4>
	</div>
</div>
<?php $oplink="come"; $infolink="affluenze_sez"; include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>	
<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th>Votanti per sezione</th> 
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
<div class="container text-center">
    <div class="chart-container">
        <canvas id="chartQuesito"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var dati = <?php echo json_encode($quesito); ?>;
        var nomeQuesito = <?php echo json_encode($nome_quesito); ?>;
        var ctx = document.getElementById("chartQuesito").getContext("2d");
        
        new Chart(ctx, {
            type: "pie",
            data: {
                labels: Object.keys(dati),
                datasets: [{
                    data: Object.values(dati),
                    backgroundColor: [
                        "rgba(54, 162, 235, 0.6)",
                        "rgba(255, 206, 86, 0.6)",
                        "rgba(255, 99, 132, 0.6)"
                    ],
                    borderColor: [
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(255, 99, 132, 1)"
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: "top" },
                    title: {
                        display: true,
                        text: nomeQuesito,
                        font: { size: 16, weight: "600" }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return "Voti: " + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
