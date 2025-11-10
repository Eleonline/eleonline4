<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
if(!isset($predefinito))
{
	$row=configurazione();
	$predefinito=$row[0]['siteistat'];
}
$row=elenco_comuni();
foreach($row as $key=>$val) {
	if($predefinito===$val['id_comune']) $pred=true; else $pred=false;
$enti[]=['id'=>($key+1),'denominazione'=>$val['descrizione'],'codice_istat'=>$val['id_comune'],'capoluogo'=>$val['capoluogo'],'indirizzo'=>$val['indirizzo'],'abitanti'=>$val['fascia'],'fax'=>$val['fax'],'email'=>$val['email'],'cap'=>$val['cap'],'centralino'=>$val['centralino'],'stemma'=>$val['stemma'],'predefinito'=>$pred];
}
$row=elenco_fasce(1);
$i=1;
foreach($row as $key=>$val){
	$fasce[$val['id_fascia']] = number_format($i,0,',','.')." - ".number_format(($val['abitanti']-1),0,',','.');
$i=$val['abitanti'];
if($val['id_fascia']==8) break;
}
$fasce[8] = "Oltre 1.000.000";
foreach($enti as $key=>$val){ 
  echo "<tr><td><input type=\"hidden\" id=\"cap$key\" value=\"".$val['cap']."\"><input type=\"hidden\" id=\"email$key\" value=\"".$val['email']."\"><input type=\"hidden\" id=\"centralino$key\" value=\"".$val['centralino']."\"><input type=\"hidden\" id=\"fax$key\" value=\"".$val['fax']."\"> </td>
  <td></td>
  <td id=\"denominazione$key\">".$val['denominazione']."</td>
  <td id=\"indirizzo$key\">".$val['indirizzo']."</td>
  <td><input type=\"hidden\" id=\"abitanti$key\" value=\"".$val['abitanti']."\">".$fasce[$val['abitanti']]."</td>
  <td id=\"codiceIstat$key\">".$val['codice_istat']."</td><td>"; 
  if($val['capoluogo']) {
	  echo "<input type=\"hidden\" id=\"capoluogo$key\" value=\"1\">";
	  echo 'si'; 
  }else{
	  echo "<input type=\"hidden\" id=\"capoluogo$key\" value=\"0\">";
	  echo 'no'; 
  }
  echo "</td><td><button type=\"button\" class=\"btn btn-danger btn-sm\" onclick=\"editEnte($key)\">Modifica</button> <button type=\"button\" class=\"btn btn-danger btn-sm\" onclick=\"deleteEnte($key)\">Elimina</button></td></tr>";
}			
?>
