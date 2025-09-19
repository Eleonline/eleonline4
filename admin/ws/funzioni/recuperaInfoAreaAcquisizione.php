<?php 
if (!defined('ADMIN_FILE')) {
    die ("You can't access this file directly...");
}
$array = json_decode(json_encode($response), true);
if($array==null) { echo "<div style=\"background-color:tomato;width:400px;font-size:14pt;\">Esito della connessione al WS: KO</div>";  CloseTable();include("footer.php");exit;}
foreach($array as $key=>$val){
	if ($key=='esito') {
		foreach($val as $key2=>$esito) {
			if ($key2=='tipoEsito')
				if($esito=='OK') echo "<div style=\"background-color:lime;width:400px;\">Esito della connessione al WS: OK";
				else { echo "<div style=\"background-color:tomato;width:600px;font-size:14pt;\">Esito della connessione al WS: KO"; }
			if ($key2=='descrizioneEsito') echo " $esito</div>";
		}
		continue;
	}elseif($key=='territori')
		foreach($val as $key2=>$territorio) 
		foreach($territorio as $key3=>$territorio2)
			{
			
			if($key3=='tipoTerritorio') {
				$tipoTerritorio=$territorio2;
				continue;
			}elseif($key3=='descrizioneTerritorio') {
				$descrizioneTerritorio=$territorio2;
				continue;
			}
			$sezarr=$territorio2;
			if($tipoTerritorio=='Provincia') {
				foreach($territorio2 as $key4=>$territorio4) { 
					if($key4=='territorio')
						foreach($territorio4 as $key5=>$territorio5)
							if($key5=='tipoTerritorio') {
								$tipoTerritorio2=$territorio5;
								continue;
							}elseif($key5=='descrizioneTerritorio') {
								$descrizioneTerritorio2=$territorio5;
								continue;
							}elseif($key5=='idTerritorio') {
								$idTerritorio2=$territorio5;
								continue;
							}
						
				}
				$sezarr=$territorio5;
			} #print_r($sezarr);
			foreach($sezarr as $key6=>$val6){
				if($key6=='territorio')
					foreach($val6 as $key7=>$val7)
					foreach($val7 as $key8=>$val8)
						switch($key8) {
							case 'tipoTerritorio' :
								$tipo=$val8;
								break;
							case 'descrizioneTerritorio':
								$descr=$val8;
								break;
							case 'progressivo':
								$progr=$val8;
								break;
							case 'ubicazionePlesso':
								$ubi=$val8;
								break;
							case 'indirizzo':
								$ind=$val8;
								break;
							case 'ospedaliera':
								$osp=$val8;
								$arr[$progr]=array("$tipo","$descr","$ubi","$ind","$osp");
								break;
						}
	
			}
		}
	
}
				foreach($arr as $key=>$val) { #print_r($val);
					$sql="select id_sede from ".$prefix."_ele_sezioni where id_cons='$id_cons' and num_sez='$key'";
 #echo "<br>$sql<br>";
					$sth = $dbi->prepare("$sql");
					$sth->execute();
					if($sth->rowCount()) {
						list($idsederem) = $sth->fetch(PDO::FETCH_NUM);
						if($val[4]=='Ordinaria') {
							$osp=0;
							if($val[2]=='UFFICI COMUNALI') $ubi=2; else $ubi=1;
						}else{$osp=1; $ubi=3;}
						$sql="update ".$prefix."_ele_sede set indirizzo='".$val[3]."', id_ubicazione='$ubi', ospedaliera='$osp' where id_sede=$idsederem"; 
#	echo "<br>$sql<br>";
						$sth = $dbi->prepare("$sql");
						$sth->execute();
					}
				}

if(isset($arr) and count($arr)) {
#	echo "<div>$tipoTerritorio2: $descrizioneTerritorio2 (ID: $idTerritorio2)</div>";
	$sql="select t1.*, t2.indirizzo, t3.ubicazione from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede left join ".$prefix."_ws_ubicazione as t3 on t2.id_ubicazione=t3.id where t1.id_cons='$id_cons' order by num_sez";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	if(!count($row)) echo "Copia i dati dal Server SIEL al DB locale";
	$arrlocale=array();
	foreach($row as $key=>$val){
		if($val['ubicazione']=='OSPEDALI E CASE DI CURA CON ALMENO 200 POSTI-LETTO') $ospedaliera='Ospedaliera'; else $ospedaliera='Ordinaria';
		$arrlocale[$val['num_sez']]=array('Sezione', 'SEZIONE '.$val['num_sez'], $val['ubicazione'], $val['indirizzo'],$ospedaliera);
	}
#	echo "<br>LOCALE: ".count($arrlocale)."REMOTO: ".count($arr);
	end($arr);
	$lastsezrem = key($arr);
	if(count($arrlocale)){
		end($arrlocale);
		$lastsezloc = key($arrlocale);
	}else $lastsezloc=0;
	if($lastsezrem>$lastsezloc) $lastsez=$lastsezrem; else $lastsez=$lastsezloc; #echo"<br>Numero sezioni: $lastsez";
	$bg='';
	echo "<table class=\"table-docs\">";
	echo "<tr><th>Archivio</th><th>Sezione</th><th>Tipo</th><th>Denominazione</th><th>Ubicazione</th><th>Indirizzo</th><th>Ospedaliera</th><th>Funzioni</th></tr>";
	$stato=0;
	for($i=1;$i<=$lastsez;$i++) {
		if($bg=='style="background-color:#c0c0c0;"') $bg=''; else $bg='style="background-color:#c0c0c0;"';
		$ar1= isset($arr[$i]) ? serialize($arr[$i]):'';
		$ar2= isset($arrlocale[$i]) ? serialize($arrlocale[$i]): '';
		$test=strcmp($ar1,$ar2);
		if($test) {
			$tdfunz="Aggiorna su db"; 
			$tdfunzrem="Aggiorna su WS"; 
		}else{ 
			$tdfunz='';
			$tdfunzrem='';
		}
		$rigaloc='';
		$rigarem='';
		if(isset($arrlocale[$i]))
			$rigaloc="<td>".$arrlocale[$i][0]."</td><td>".$arrlocale[$i][1]."</td><td>".$arrlocale[$i][2]."</td><td>".$arrlocale[$i][3]."</td><td>".$arrlocale[$i][4]."</td><td><b>".$tdfunz."</b></td></tr>"; 
		else $rigaloc="<td colspan=\"5\">Dati non presenti nel DB</td><td><b>$tdfunz</b></td></tr>";
		if(isset($arr[$i])) 
			$rigarem="<td>".$arr[$i][0]."</td><td>".$arr[$i][1]."</td><td>".$arr[$i][2]."</td><td>".$arr[$i][3]."</td><td>".$arr[$i][4]."</td><td><b>$tdfunzrem</b></td></tr>";  
		else $rigarem="<td colspan=\"5\">Dati non presenti su WS</td><td><b>$tdfunzrem</b></td></tr>";		
		if($rigaloc==$rigarem) continue; #$bgcol='lime'; else $bgcol='yellow';
		$stato=1;	
		echo "<tr $bg><td>Dati in Locale</td><td>$i</td>";
		if(isset($arrlocale[$i])) echo $rigaloc;
		echo "<tr $bg><td>Dati su WS</td><td>$i</td>";	
		if(isset($arr[$i])) echo $rigarem;
	}
	$sql="select * from ".$prefix."_ws_funzioni where id_cons='$id_cons' and funzione='recuperaInfoAreaAcquisizione'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	if(count($row))
		$sql="update ".$prefix."_ws_funzioni set stato='$stato' where id_cons='$id_cons' and funzione='recuperaInfoAreaAcquisizione'";
	else
		$sql="insert into ".$prefix."_ws_funzioni values('$id_cons', 'recuperaInfoAreaAcquisizione','$stato')";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
}		
echo "</table>";
echo "<br>";
echo "<br>";

?>