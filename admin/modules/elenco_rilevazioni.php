<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
	
$row=elenco_rilevazioni();
foreach($row as $key=>$val){
	list ($anno,$mese,$giorno)=explode('-',$val['data']);
	$data="$giorno/$mese/$anno";
	echo "<tr id=\"riga$key\"><td id=\"data$key\">".$data."</td><td id=\"orario$key\">".$val['orario']."</td><td><button type=\"button\" class=\"btn btn-danger btn-sm\" onclick=\"rimuoviAffluenza($key)\">Elimina</button></td></tr>";
} #die("TEST:  --- ".count($row));

?>
