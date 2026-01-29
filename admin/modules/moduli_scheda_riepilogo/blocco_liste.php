<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
?>
<div class="card card-warning">
<div class="card-header">
<h3 class="card-title">Voti di Lista</h3>
</div>

<div class="card-body p-2">

<table class="table table-bordered text-center">
<thead>
<tr>
<th>Voti Validi</th>
<th>Voti Nulli</th>
<th>Voti Contestati</th>
<th>Solo Candidato</th>
</tr>
</thead>

<tbody>
<tr>
<td><?= $liste_validi ?></td>
<td><?= $liste_nulli ?></td>
<td><?= $liste_contestati ?></td>
<td><?= $solo_candidato ?></td>
</tr>
</tbody>
</table>

</div>
</div>
