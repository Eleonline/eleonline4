<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
?>
<div class="card card-primary">
<div class="card-header">
<h3 class="card-title">Votanti</h3>
</div>

<div class="card-body p-2">
<table class="table table-bordered text-center">
<thead>
<tr>
<th>Iscritti</th>
<th>Uomini</th>
<th>Donne</th>
<th>Voti Uomini</th>
<th>Voti Donne</th>
<th>Voti Espressi</th>
</tr>
</thead>
<tbody>
<tr>
<td><?= $iscritti ?></td>
<td><?= $uomini ?></td>
<td><?= $donne ?></td>
<td><?= $voti_uomini ?></td>
<td><?= $voti_donne ?></td>
<td><b><?= $voti_espressi ?></b></td>
</tr>
</tbody>
</table>
</div>
</div>
