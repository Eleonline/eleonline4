<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
	
$row=elenco_cons();
# ciclo ancora da adattare
foreach($row as $key=>$val){
	if($val['preferita']) $pref=1; else $pref='';
echo "<tr id=\"riga$key\"><td style=\"display:none;\" id=\"link_trasparenza$key\" name=\"link_trasparenza$key\">".$val['link_trasparenza']."</td><td style=\"display:none;\" id=\"id_cons_gen$key\" name=\"id_cons_gen$key\">".$val['id_cons_gen']."</td><td style=\"display:none;\" id=\"tipo_cons$key\" name=\"tipo_cons$key\">".$val['tipo_cons']."</td><td style=\"display:none;\" id=\"chiusa$key\" name=\"chiusa$key\">".$val['chiusa']."</td><td style=\"display:none;\" id=\"id_conf$key\" name=\"id_conf$key\">".$val['id_conf']."</td><td style=\"display:none;\" id=\"preferita$key\" name=\"preferita$key\">".$pref."</td><td style=\"display:none;\" id=\"preferenze$key\" name=\"preferenze$key\">".$val['preferenze']."</td><td style=\"display:none;\" id=\"id_fascia$key\" name=\"id_fascia$key\">".$val['id_fascia']."</td><td style=\"display:none;\" id=\"vismf$key\" name=\"vismf$key\">".$val['vismf']."</td><td style=\"display:none;\" id=\"solo_gruppo$key\" name=\"solo_gruppo$key\">".$val['solo_gruppo']."</td><td style=\"display:none;\" id=\"disgiunto$key\" name=\"disgiunto$key\">".$val['disgiunto']."</td><td style=\"display:none;\" id=\"proiezione$key\" name=\"proiezione$key\">".$val['proiezione']."</td><td>";
if($val['preferita']) echo "*";
echo "</td><td id=\"descrizione$key\">".$val['descrizione']."</td><td id=\"data_inizio$key\">".$val['data_inizio']."</td><td id=\"data_fine$key\">".$val['data_fine']."</td><td><button type=\"button\" class=\"btn btn-sm btn-warning me-1\" onclick=\"editConsultazione($key)\">Modifica</button><button type=\"button\" class=\"btn btn-danger btn-sm\" onclick=\"rimuoviConsultazione($key)\">Elimina</button></td></tr>";
}

?>
