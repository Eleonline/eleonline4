<?php
if (!defined('ADMIN_FILE')) {
    die ("You can't access this file directly...");
}

	switch ($operazione) {
		case 'recuperaEventiElettorali' :
			$sql="select data_inizio from ".$prefix."_ele_consultazione where id_cons_gen=$id_cons_gen";
			$sth = $dbi->prepare("$sql");
			$sth->execute();
			list($data)=$sth->fetch(PDO::FETCH_NUM);
#			$chiamata="['utente' => ['UserID'=>$utente,'Password'=>($userpass)], 'dataElezione'=>$data";
			break;
		case 'recuperaInfoAreaAcquisizione' :
		case 'recuperaInfoAreaAcquisizioneSezioniElettori':
		case 'inviaModificaSezioniElettori' :
			$sql="select codicews,data,descrizione from ".$prefix."_ws_consultazione where id_cons=$id_cons";
			$sth = $dbi->prepare("$sql");
			$sth->execute();
			list($tipows,$data,$descrizione)=$sth->fetch(PDO::FETCH_NUM);
#			$chiamata="['utente' => ['UserID'=>$utente,'Password'=>($userpass)], 'evento' => ['TipoElezione'=>'$tipo','DataElezione'=>'$data','DescrizioneElezione'=>'$descrizione']";
			#break;
		/*	switch ($tipoinvio) {
				case 'Sezione' :
					
					$id_territorio=1;
					$tipo_territorio=$tipoinvio;
					break;
			} */
	}
	
/*<xsd:territorio>
<com:idCollegioSenato>106781597</com:idCollegioSenato>
<com:idCollegioCamera>106773241</com:idCollegioCamera>
<com:idTerritorio>106773246</com:idTerritorio>
<com:tipoTerritorio>Comune</com:tipoTerritorio>
<com:descrizioneTerritorio>ANTEY-SAINT-ANDRE'</com:descrizioneTerritorio>
<com:comunicazioneAttiva>
<com:idComunicazione>3588814</com:idComunicazione>
<com:descrizioneComunicazione>15°</com:descrizioneComunicazione>
</com:comunicazioneAttiva>
<com:numSezioni>1</com:numSezioni>
<com:diCuiOspedaliere>0</com:diCuiOspedaliere>
<com:numeroElettoriFemmine>50</com:numeroElettoriFemmine>
<com:numeroElettoriMaschi>50</com:numeroElettoriMaschi>
<com:numeroElettoriTotali>100</com:numeroElettoriTotali>
</xsd:territorio>			
*/			



#$chiamata.=']';
/*
"select * from ".$prefix."_ws_consultazione where id_cons in (select id_cons from ".$prefix."_ele_cons_comune as t1 left join ".$prefix."_ele_consultazione as t2 on t1.id_cons_gen=t2.id_cons_gen where t1.id_comune='$id_comune' and t2.tipo_cons='$tipolocale')";	
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
*/
?>