<?php

$sql="select data_inizio from ".$prefix."_ele_consultazione where id_cons_gen=$id_cons_gen";
$sth = $dbi->prepare("$sql");
$sth->execute();
list($data)=$sth->fetch(PDO::FETCH_NUM);
$sql="select codicews,data,descrizione from ".$prefix."_ws_consultazione where id_cons=$id_cons";
$sth = $dbi->prepare("$sql");
$sth->execute();
list($tipows,$data,$descrizione)=$sth->fetch(PDO::FETCH_NUM);
$response = $client->$operazione(['utente' => ['UserID'=>$utente,'Password'=>$userpass], 'evento' => ['TipoElezione'=>$tipows,'DataElezione'=>$data,'DescrizioneElezione'=>$descrizione]]);
?>