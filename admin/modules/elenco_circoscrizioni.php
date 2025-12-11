<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
	
$row=elenco_circoscrizioni();
foreach($row as $key=>$val){
	echo "<tr id=\"riga$key\"><td id=\"id_cons$key\" style=\"display:none;\">".$val['id_cons']."</td><td id=\"id_circ$key\" style=\"display:none;\">".$val['id_circ']."</td><td id=\"numero$key\">".$val['num_circ']."</td><td id=\"denominazione$key\">".$val['descrizione']."</td><td><button class=\"btn btn-sm btn-warning me-1\" onclick=\"editCircoscrizione($key)\">Modifica</button> <button type=\"button\" class=\"btn btn-danger btn-sm\" onclick=\"deleteCircoscrizione($key)\">Elimina</button></td></tr>";
} #die("TEST:  --- ".count($row));

?>
