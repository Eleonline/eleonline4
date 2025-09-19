<?php 
if (!defined('ADMIN_FILE')) {
    die ("You can't access this file directly...");
}
$array = json_decode(json_encode($response), true);
if($array==null) { echo "<div style=\"background-color:tomato;width:400px;font-size:14pt;\">Esito della connessione al WS: KO - Connessione negata</div>";  CloseTable();include("footer.php");exit;}
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
							if($key5=='idTerritorio') {
								$idTerritorio2=$territorio5;
								continue;
							}elseif($key5=='tipoTerritorio') {
								$tipoTerritorio2=$territorio5;
								continue;
							}elseif($key5=='descrizioneTerritorio') {
								$descrizioneTerritorio2=$territorio5;
								continue;
							}
						
				}
				$sezarr=$territorio5;
			} #print_r($sezarr);
			foreach($sezarr as $key6=>$val6){ #echo "<br>TEST: $key6".print_r($sezarr);
				if($key6=='territorio')
					foreach($val6 as $key7=>$val7)
					foreach($val7 as $key8=>$val8)
						switch($key8) {
							case 'idTerritorio' :
								$idterritorio=$val8;
								break;
							case 'tipoTerritorio' :
								$tipo=$val8;
								break;
							case 'descrizioneTerritorio':
								$descr=$val8;
								break;
/*							case 'idComunicazione' :
								$idcomunicazione=$val8;
								break;
							case 'descrizioneComunicazione':
								$descrcomunicazione=$val8;
								break;
							case 'numSezioni':
								$numsezioni=$val8;
								break; */
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
								$arr[$progr]=array("$idterritorio","$tipo","$descr","$progr","$ubi","$ind","$osp");#,"$idcomunicazione","$descrcomunicazione","$numsezioni"
								break;
						}
	/*			foreach($arr as $key=>$val) { #print_r($val);
					$sql="select id_sede from ".$prefix."_ele_sezioni where id_cons='$id_cons' and num_sez='$key'";
 #echo "<br>$sql<br>";
					$sth = $dbi->prepare("$sql");
					$sth->execute();
					list($idsederem) = $sth->fetch(PDO::FETCH_NUM);
					if($val[4]=='Ordinaria') {
						$osp=1;
						if($val[2]=='UFFICI COMUNALI') $ubi=2; else $ubi=1;
					}else{$osp=2; $ubi=3;}
					$sql="update ".$prefix."_ele_sede set indirizzo='".$val[3]."', id_ubicazione='$ubi', ospedaliera='$osp' where id_sede=$idsederem"; 
					#echo "<br>$sql<br>";
					$sth = $dbi->prepare("$sql");
					$sth->execute();
				}	*/
			}
		}
	
}

if(isset($arr) and count($arr)) {

	$sql="update ".$prefix."_ele_comuni set id_ws='".$idTerritorio2."' where id_comune=$id_comune"; 
	$sth = $dbi->prepare("$sql");
	$sth->execute();

	echo "<div>$tipoTerritorio2: $descrizioneTerritorio2 (ID: $idTerritorio2)</div>";
	$sql="select t1.*, t2.indirizzo,t2.ospedaliera, t3.ubicazione, t4.idsez_ws from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede left join ".$prefix."_ws_ubicazione as t3 on t2.id_ubicazione=t3.id left join ".$prefix."_ws_sezioni as t4 on t1.id_sez=t4.id_sez where t1.id_cons='$id_cons' order by num_sez";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	$arrlocale=array();
	$idnumsez=array();
	foreach($row as $key=>$val){
		if($val['ospedaliera']=='1') $ospedaliera='Ospedaliera'; else $ospedaliera='Ordinaria';
#		$arrlocale[$val['num_sez']]=array('Sezione', 'SEZIONE '.$val['num_sez'], $val['ubicazione'], $val['indirizzo'],$ospedaliera, $val['idsez_ws']);
		$arrlocale[$val['num_sez']]=array((string)$val['idsez_ws'],'Sezione', 'SEZIONE '.$val['num_sez'],(string) $val['num_sez'], $val['ubicazione'], $val['indirizzo'],$ospedaliera );
		$idnumsez[$val['num_sez']]=$val['id_sez'];
	}
#	echo "<br>LOCALE: ".count($arrlocale)."REMOTO: ".count($arr);  "$idterritorio","$tipo","$descr","$progr","$ubi","$ind","$osp"
end($arr);
$lastsezrem = key($arr);
if(count($arrlocale)) {
end($arrlocale);
$lastsezloc = key($arrlocale);
}else $lastsezloc=0;
if($lastsezrem>$lastsezloc) $lastsez=$lastsezrem; else $lastsez=$lastsezloc; #echo"<br>Numero sezioni: $lastsez";
$bg='';
echo "<table class=\"table-docs\">";
echo "<tr><th></th><th></th><th>Id Territorio</th><th>Tipo</th><th>Denominazione</th><th>Progressivo</th><th>Ubicazione</th><th>Indirizzo</th><th>Ospedaliera</th><th>Funzioni</th></tr>"; #<th>IdComunicazione</th><th>Descriz. Comun.</th><th>Num. Sezioni</th
$stato=0;
for($i=1;$i<=$lastsez;$i++) {
	if($bg=='style="background-color:#c0c0c0;"') $bg=''; else $bg='style="background-color:#c0c0c0;"';
	$ar1= isset($arr[$i]) ? serialize($arr[$i]):'';
	$ar2= isset($arrlocale[$i]) ? serialize($arrlocale[$i]): '';#die("<br>TESTrem: $ar1 <br>TESTloc: $ar2");
	$test=strcmp($ar1,$ar2);#foreach($arr[$i] as $k=>$v) echo "<br>$k - $v "; foreach($arrlocale[$i] as $k=>$v) echo "<br>$k - $v ";die("<br>$ar1<br>$ar2");
	if($test) {
		$tdfunz="Aggiorna su db"; 
		$tdfunzrem="<a href=\"https://www.eleonline.it/test/admin/admin.php?op=ws&funzione=inviaModificaSezioniElettori&id_cons_gen=6&id_comune=29041\">Aggiorna su WS</a>"; 
	}else{ 
		$tdfunz='';
		$tdfunzrem='';
	} #array("$idterritorio","$tipo","$descr","$idcomunicazione","$descrcomunicazione","$numsezioni","$progr","$ubi","$ind","$osp");
	$rigaloc='';
	$rigarem='';
	if(isset($arrlocale[$i]))
		$rigaloc="<td>".$arrlocale[$i][0]."</td><td>".$arrlocale[$i][1]."</td><td>".$arrlocale[$i][2]."</td><td>".$arrlocale[$i][3]."</td><td>".$arrlocale[$i][4]."</td><td>".$arrlocale[$i][5]."</td><td>".$arrlocale[$i][6]."</td><td><b>".$tdfunz."</b></td></tr>"; 
	else $rigaloc="<td colspan=\"7\">IN QUESTA SCHEDA E' STATO AGGIUNTO L'ID TERRITORIO - DA INSERIRE NEL DB</td><td><b>$tdfunz</b></td></tr>";
	if(isset($arr[$i])) {
		$rigarem="<td>".$arr[$i][0]."</td><td>".$arr[$i][1]."</td><td>".$arr[$i][2]."</td><td>".$arr[$i][3]."</td><td>".$arr[$i][4]."</td><td>".$arr[$i][5]."</td><td>".$arr[$i][6]."</td><td><b>$tdfunzrem</b></td></tr>";
// aggiorna id su db locale
		if(isset($idnumsez[$arr[$i][3]])) {
			$sql="select * from ".$prefix."_ws_sezioni where id_sez='".$idnumsez[$arr[$i][3]]."' and id_cons=$id_cons";
			$sth = $dbi->prepare("$sql");
			$sth->execute();
			$row = $sth->fetchAll();
			if(count($row))
				$sql="update ".$prefix."_ws_sezioni set idsez_ws='".$arr[$i][0]."' where id_cons='$id_cons' and id_sez='".$idnumsez[$arr[$i][3]]."'";
			else
				$sql="insert into ".$prefix."_ws_sezioni values('$id_cons', '".$idnumsez[$arr[$i][3]]."', '".$arr[$i][0]."')";
			$sth = $dbi->prepare("$sql");
			$sth->execute();
		}

#	if(count($row))
	} else $rigarem="<td colspan=\"5\">Dati non presenti su WS</td><td><b>$tdfunzrem</b></td></tr>";		
	if($rigaloc==$rigarem) continue; #$bgcol='lime'; else $bgcol='yellow';
	$stato=1;	
	echo "<tr $bg><td>Dati in Locale</td><td>$i</td>";
	if(isset($arrlocale[$i])) echo $rigaloc;
	echo "<tr $bg><td>Dati su WS</td><td>$i</td>";	
	if(isset($arr[$i])) echo $rigarem;
}
$sql="select * from ".$prefix."_ws_funzioni where id_cons='$id_cons' and funzione='recuperaInfoAreaAcquisizioneSezioniElettori'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	if(count($row))
		$sql="update ".$prefix."_ws_funzioni set stato='$stato' where id_cons='$id_cons' and funzione='recuperaInfoAreaAcquisizioneSezioniElettori'";
	else
		$sql="insert into ".$prefix."_ws_funzioni values('$id_cons', 'recuperaInfoAreaAcquisizioneSezioniElettori','$stato')";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
}		
echo "</table>";
echo "<br>";
echo "<br>";

?>