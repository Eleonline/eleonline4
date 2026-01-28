<div class="card card-success">
<div class="card-header">
<h3 class="card-title">Candidato Presidente</h3>
</div>

<div class="card-body p-2">

<table class="table table-bordered text-center">
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
<td><?= $pres_validi ?></td>
<td><?= $pres_nulle ?></td>
<td><?= $pres_bianche ?></td>
<td><?= $pres_contestati ?></td>
<td><?= $pres_non_validi ?></td>
</tr>
</tbody>
</table>

</div>
</div>

<?php include "moduli/tabella_presidente_dettaglio.php"; ?>
