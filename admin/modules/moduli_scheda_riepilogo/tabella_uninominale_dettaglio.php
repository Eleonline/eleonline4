<div class="card-header bg-primary text-white">
    <h3 class="card-title text-uppercase mb-0">Dettaglio Candidato Uninominale</h3>
</div>

<div class="card-body p-2">

<div class="table-responsive">

<table class="table table-bordered table-striped text-center mb-0">

<thead>
<tr>
<th>#</th>
<th>Candidato</th>
<th>Voti Validi</th>
<th>Senza Indicazione di Lista</th>
<th>Voti Nulli di Lista</th>
</tr>
</thead>

<tbody>

<?php

$lista_uninominale = [

["Pulvirenti Fabrizio Benedetto Maria",522,3,0],
["Di Stefano Gaspare",49,1,0],
["Coppolino Mario Pietro",120,0,0],
["Costantino Valentina",1210,5,0],
["La Monica Patrizia",14,2,0],
["Baglio Katia",1079,6,0],
["Calderone Tommaso Antonio",2362,17,0],
["Arena Giuseppe",894,14,0],
["Di Natale Antonio Giuseppe",69,1,0]

];

$tot_voti = 0;
$tot_solo = 0;
$i = 1;

foreach($lista_uninominale as $riga){

$tot_voti += $riga[1];
$tot_solo += $riga[2];

echo "
<tr>
<td>$i</td>
<td>$riga[0]</td>
<td>$riga[1]</td>
<td>$riga[2]</td>
<td>$riga[3]</td>
</tr>
";

$i++;
}
?>

<tr class="bg-light font-weight-bold">
<td colspan="2">TOTALE</td>
<td><?= $tot_voti ?></td>
<td><?= $tot_solo ?></td>
<td>0</td>
</tr>

</tbody>

</table>

</div>
</div>
</div>
