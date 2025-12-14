<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
$inizioNoGenere=strtotime('2025/06/30');
$row=dati_consultazione(0);
$dataInizio=strtotime($row[0]['data_inizio']);
if($inizioNoGenere>$dataInizio) $nascondi=''; else $nascondi='display:none;';	
$row=elenco_sezioni();
if(count($row)) {
	$ultimo = end($row);
	$maxNumero = $ultimo['num_sez'];
}else
	$maxNumero = 0;
echo "<tr id=\"riga".(++$maxNumero)."\"><td colspan=\"8\" id=\"maxNumero\" style=\"display:none;\">".$maxNumero."</td></tr>";
foreach($row as $key=>$val){
	echo "<tr id=\"riga$key\"><td id=\"idSede$key\" style=\"display:none;\">".$val['id_sede']."</td><td id=\"idSezione$key\" style=\"display:none;\">".$val['id_sez']."</td><td id=\"numero$key\">".$val['num_sez']."</td><td id=\"indirizzo$key\">".$val['indirizzo']."</td>";
	 
		echo "<td id=\"maschi$key\" style=\"text-align:right; $nascondi\">".$val['maschi']."</td><td id=\"femmine$key\" style=\"text-align:right; $nascondi\">".$val['femmine']."</td>";
	echo "<td id=\"totale$key\" style=\"text-align:right;\">".number_format($val['maschi']+$val['femmine'],0,',','.')."</td><td><button class=\"btn btn-sm btn-warning me-1\" onclick=\"editSezione($key)\">Modifica</button> <button type=\"button\" class=\"btn btn-danger btn-sm\" onclick=\"deleteSezione($key)\">Elimina</button></td></tr>";
}

?>
