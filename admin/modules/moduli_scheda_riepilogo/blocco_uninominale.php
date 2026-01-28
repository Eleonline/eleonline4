<div class="card card-info">

<div class="card-header">
<h3 class="card-title">Candidato Uninominale</h3>
</div>

<div class="card-body p-2">

<!-- RIEPILOGO -->
<div class="table-responsive mb-3">

<table class="table table-bordered text-center mb-0">

<thead>
<tr>
<th>Voti Validi</th>
<th>Schede Nulle</th>
<th>Schede Bianche</th>
<th>Voti Contestati</th>
<th>Tot. Non Validi</th>
</tr>
</thead>

<tbody>
<tr>
<td><?= $uni_validi ?></td>
<td><?= $uni_nulle ?></td>
<td><?= $uni_bianche ?></td>
<td><?= $uni_contestati ?></td>
<td><?= $uni_non_validi ?></td>
</tr>
</tbody>

</table>

</div>


<!-- DETTAGLIO -->
<div class="clearfix"></div>
<hr class="my-2">
<?php include "tabella_uninominale_dettaglio.php"; ?>

</div>
</div>
