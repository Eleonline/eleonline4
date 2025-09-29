<?php
if(is_file('../includes/check_access.php'))
	require_once '../includes/check_access.php';
else
	require_once 'includes/check_access.php';

function cambio_password($pass)
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	$aid=$_SESSION['username'];
	$mpass=md5($pass);
	if($_SESSION['ruolo']=='superuser') $id='0'; else $id=$id_comune;
	$sql="update ".$prefix."_authors set pwd='$mpass' where id_comune=$id and aid='$aid'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->rowCount();
	return($row);	
}

function configurazione()
{
	global $id_cons_gen,$prefix,$dbi;
	$sql="select * from ".$prefix."_config";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function dati_sezione($idsez,$numsez)
{
global $dbi,$prefix,$id_cons;
	if($idsez) $id="and id_sez='$idsez'"; 
	elseif($numsez) $id="and num_sez='$numsez'";
	else $id='';
	$sql="SELECT * FROM ".$prefix."_ele_sezione where id_cons='$id_cons' $id";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function default_cons()
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	$sql="select id_cons_gen from ".$prefix."_ele_cons_comune where id_comune=$id_comune order by preferita desc limit 0,1"; 
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($row) = $sth->fetch(PDO::FETCH_NUM);
	return($row);	
}

function elenco_autorizzati() #elenco dei comuni autorizzati per la consultazione attiva
{
	global $id_cons_gen,$prefix,$dbi;
	if(!$id) $id=$id_cons_gen;
	$sql="select * from ".$prefix."_ele_cons_comune where id_cons_gen=$id_cons_gen";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_cons()
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	$sql="select id_cons_gen,descrizione,tipo_cons from ".$prefix."_ele_consultazione order by data_inizio desc";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_comuni()
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	$sql="select * from ".$prefix."_ele_comune order by descrizione"; #id_comune,descrizione
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_liste()
{
	global $id_cons,$id_comune,$prefix,$dbi;
	$sql="select * from ".$prefix."_ele_lista where id_cons='$id_cons' order by num_lista";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_fasce($id)
{
	global $id_cons,$id_comune,$prefix,$dbi;
	$sql="select * from ".$prefix."_ele_fascia where id_conf='$id' order by id_fascia";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_rilevazioni()
{
	global $id_cons_gen,$prefix,$dbi;
	$sql="select * from ".$prefix."_ele_rilaff where id_cons_gen='$id_cons_gen' order by data,orario";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_utenti()
{
	global $id_cons,$id_comune,$prefix,$dbi;
	$sql="select * from ".$prefix."_author where id_comune='$id_comune' order by aid";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function tipo_consultazione($id)
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	$sql="select descrizione from ".$prefix."_ele_tipo where tipo_cons='$id'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($row) = $sth->fetch(PDO::FETCH_NUM);
	return($row);	
}

function totale_sezioni()
{
	global $id_cons_gen,$id_comune,$prefix,$dbi,$id_cons;
	$sql="select count(id_sez) from ".$prefix."_ele_sezione where id_cons='$id_cons'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($row) = $sth->fetch(PDO::FETCH_NUM);
	return($row);	
}

function ultime_affluenze_sezione($id_sez)
{
	global $id_cons,$prefix,$dbi;
	if($id_sez) $id="and t3.id_sez='$id_sez'"; else $id='';
	$sql="select t3.voti_complessivi,t3.voti_uomini,t3.voti_donne from ".$prefix."_ele_voti_parziale as t3 where t3.id_cons=$id_cons $id order by t3.data desc,t3.orario desc limit 0,1";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC); 
	return($row);	
	
}

function verifica_cons($id) #verifica se il comune corrente è presente tra i comuni autorizzati
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	if(!$id) $id=$id_cons_gen;
	$sql="select * from ".$prefix."_ele_cons_comune where id_cons_gen='$id' and id_comune='$id_comune'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function voti_lista_sezione($id_sez) 
{
	global $id_cons,$prefix,$dbi;
	$sql="select * from ".$prefix."_ele_voti_lista where id_cons='$id_cons' and id_sez='$id_sez' order by num_lista";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

?>
