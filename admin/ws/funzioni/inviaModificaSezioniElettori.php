<?php 
if (!defined('ADMIN_FILE')) {
    die ("You can't access this file directly...");
}
echo '<script>
function attiva(idws,data,descrizione) {
var val = document.getElementById("label"+idws).value;
var idcons=document.getElementById("validcons").value;
var inputVal = document.getElementById("tdidcons");
  if (idws == "" || data == "") {
    document.getElementById("txtHint").innerHTML = "";
    return;
  } else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
		  document.getElementById("validcons").value=parseInt(this.responseText);
		  if (parseInt(this.responseText) == NaN) {
			  document.getElementById("label"+idws).innerHTML = this.responseText;
		  }else{
			if (parseInt(this.responseText) == "0") {
//				if (val == "Attivata") {
					document.getElementById("label"+idws).innerHTML = "Disponibile da attivare";
					inputVal.style.backgroundColor = "yellow";
					document.getElementById("validcons").value=0;
			}else{
				document.getElementById("label"+idws).innerHTML = "Attivata";
				document.getElementById("validcons").value=parseInt(this.responseText);
				inputVal.style.backgroundColor = "lime";
			}
			
//			}
		  }	
      }
    }
    xmlhttp.open("GET","principale.php?funzione=101&tipo=1&id_cons="+idcons+"&idws="+idws+"&data="+data+"&descr="+descrizione,true);
    xmlhttp.send();
  }
}
</script>';

$array = json_decode(json_encode($response), true);
if($array==null) { echo "<div style=\"background-color:tomato;width:400px;font-size:14pt;\">Esito della connessione al WS: KO</div>";  CloseTable();include("footer.php");exit;}foreach($array as $key=>$val){
	if ($key=='esito') {
		foreach($val as $key2=>$esito) {
			if ($key2=='tipoEsito')
				if($esito=='OK') echo "<div style=\"background-color:lime;width:200px;\">Esito della connessione al WS: OK</div>";
				else { echo "<div style=\"background-color:tomato;width:400px;font-size:14pt;\">Esito della connessione al WS: KO"; }
				if ($key2=='descrizioneEsito') echo "<br>$esito</div>";
		}
		continue;
	}elseif($key=='eventi')
		foreach($val as $key2=>$evento) 
		foreach($evento as $key3=>$evento2)
			{
			if($key3=='TipoElezione') {
				$tipoElezione=$evento2;
				$arrcons[$key]=$evento;
				continue;
			}elseif($key3=='DataElezione') {
				$dataElezione=$evento2;
				$arrcons[$key]=$evento;
				continue;
			}elseif($key3=='DescrizioneElezione') {
				$descrizioneElezione=$evento2;
				$arrcons[$key]=$evento;
			}
			$arr[]=$arrcons;

	}
	
}

#	tabella consultazioniws - consultazioni attivate per invio dati: id_cons, codicews, data, descrizione
#   tabella tipiws - corrispondenza codice consultazione: id_locale, id_ws
if(isset($arr) and count($arr)){ 	
	$eventi=$arr[0];
	end($eventi);
	$lastrec = key($eventi);
	$bg='';
	echo "<br>";
	echo "<table class=\"table-docs\" style=\"font-size: 14pt;\">";
	echo "<tr><th>Codice Elezione</th><th>Data Elezione</th><th>Descrizione Elezione</th><th>Funzioni</th></tr>";
	#for($i=0;$i<=$lastrec;$i++) {
	foreach($eventi as $key=>$val){ 
		$sql="select id_locale from ".$prefix."_ws_tipo where id_ws='".$val['TipoElezione']."'";
		$sth = $dbi->prepare("$sql");
		$sth->execute();
		list($tipolocale)=$sth->fetch(PDO::FETCH_NUM);
	$sql="select * from ".$prefix."_ws_consultazione where id_cons in (select id_cons from ".$prefix."_ele_cons_comune as t1 left join ".$prefix."_ele_consultazione as t2 on t1.id_cons_gen=t2.id_cons_gen where t1.id_comune='$id_comune' and t2.tipo_cons='$tipolocale')";	
		$sth = $dbi->prepare("$sql");
		$sth->execute();
		$row = $sth->fetchAll();
		
	if(count($row)) {$idconsloc=$row[0]['id_cons'];$bgcol='lime'; $stato="Attivata"; $idconsloc=$row[0]['id_cons'];} else {$bgcol='yellow'; $stato="Disponibile da attivare"; $idconsloc='0';}
		if($bg=='style="background-color:#c0c0c0;"') $bg=''; else $bg='style="background-color:#c0c0c0;"';
		$tdfunz='';

		echo "<tr $bg>";	
		echo "<td style=\"font-size: 12pt;text-align:center\">".$val['TipoElezione']."</td><td style=\"font-size: 12pt;text-align:center\">".$val['DataElezione']."</td><td style=\"font-size: 12pt;text-align:center\">".$val['DescrizioneElezione']."</td><td id=\"tdidcons\" style=\"width:200px;font-size: 12pt;text-align:center; background-color:$bgcol;\"><input type=\"hidden\" id=\"validcons\" value=\"$idconsloc\"><label id=\"label".$val['TipoElezione']."\" for=\"".$val['TipoElezione']."\" onclick=\"attiva('".$val['TipoElezione']."','".htmlentities($val['DataElezione'])."','".$val['DescrizioneElezione']."')\">$stato</label> </td></tr>"; 

	}
}
echo "</table>";
echo "<br>";
echo "<br>";

?>