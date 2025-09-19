<?php

$sql="select data_inizio from ".$prefix."_ele_consultazione where id_cons_gen=$id_cons_gen";
$sth = $dbi->prepare("$sql");
$sth->execute();
list($data)=$sth->fetch(PDO::FETCH_NUM);
$sql="select max(id_comunicazione),descrizione,attiva from ".$prefix."_ws_comunicazione where id_cons=$id_cons group by descrizione,attiva";
$sth = $dbi->prepare("$sql");
$sth->execute();
$row = $sth->fetchAll();
// momentaneamente non gestisco il campo descrizione, ricordami di farlo più avanti dopo aver capito meglio a cosa serve
if(isset($row[0][0]) and $row[0][0]>0) {
	$idComunicazione=$row[0][0];
	$descrComunicazione=$row[0][1];
	$sql="update ".$prefix."_ws_comunicazione set attiva='0' where id_cons=$id_cons and id_comunicazione='$idComunicazione'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
}else{ 
	$idComunicazione=0;
	$descrComunicazione='Sezioni/ Elettori 45°';
}
$sql="insert into ".$prefix."_ws_comunicazione set id_comunicazione='".++$idComunicazione."', id_cons=$id_cons, descrizione='$descrComunicazione', attiva='1'";
$sth = $dbi->prepare("$sql");
$sth->execute();
$sql="select codicews,data,descrizione from ".$prefix."_ws_consultazione where id_cons=$id_cons";
$sth = $dbi->prepare("$sql");
$sth->execute();
list($tipows,$data,$descrConsultazione)=$sth->fetch(PDO::FETCH_NUM);
$sql="select id_ws,descrizione from ".$prefix."_ele_comuni where id_comune=$id_comune";
$sth = $dbi->prepare("$sql");
$sth->execute();
list($idWs,$descrComune)=$sth->fetch(PDO::FETCH_NUM);
echo "(['utente' => ['UserID'=>$utente,'Password'=>$userpass], 'evento' => ['TipoElezione'=>$tipows,'DataElezione'=>$data,'DescrizioneElezione'=>$descrConsultazione], 'territorio' => ['idTerritorio' => $idWs, 'tipoTerritorio' => 'Comune','descrizioneTerritorio' => $descrComune, 'comunicazioneAttiva' => ['idComunicazione' => $idComunicazione, 'descrizioneComunicazione' => '45°'], 'territori' => ['territorio' => ['id_territorio' => '134946989', 'tipoTerritorio' => 'Sezione', 'descrizioneTerritorio' => 'Sezione 15', 'progressivo' => '15', 'ubicazionePlesso' => 'EDIFICI SCOLASTICI', 'indirizzo' => 'Via Vittorio Veneto, 83, Rovigo, RO modificata', 'ospedaliera' => 'N', 'numeroElettoriFemmine' => '50', 'numeroElettoriMaschi' => '50', 'numeroElettoriTotali' => '100']]]])";
$response = $client->$operazione(['utente' => ['UserID'=>$utente,'Password'=>$userpass], 'evento' => ['TipoElezione'=>$tipows,'DataElezione'=>$data,'DescrizioneElezione'=>$descrConsultazione], 'territorio' => ['idTerritorio' => $idWs, 'tipoTerritorio' => 'Comune','descrizioneTerritorio' => $descrComune, 'comunicazioneAttiva' => ['idComunicazione' => $idComunicazione, 'descrizioneComunicazione' => '45°'], 'territori' => ['territorio' => ['id_territorio' => '134946989', 'tipoTerritorio' => 'Sezione', 'descrizioneTerritorio' => 'Sezione 15', 'progressivo' => '15', 'ubicazionePlesso' => 'EDIFICI SCOLASTICI', 'indirizzo' => 'Via Vittorio Veneto, 83, Rovigo, RO modificata', 'ospedaliera' => 'N', 'numeroElettoriFemmine' => '50', 'numeroElettoriMaschi' => '50', 'numeroElettoriTotali' => '100']]]]);

?>