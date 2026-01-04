<?php

function affluenze_referendum($id,$cons)
{
	global $id_cons,$prefix,$dbi;
	if(!$cons) $cons=$id_cons;
	if($id) $filtro="and t3.id_gruppo='$id'"; else $filtro='';
	$sql="select t3.id_gruppo,sum(t3.voti_complessivi),t3.data ,t3.orario from ".$prefix."_ele_voti_parziale as t3 where t3.id_cons=$cons $filtro group by t3.id_gruppo,t3.data ,t3.orario order by t3.data,t3.orario";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	
	
}

function affluenze_sezione($id_sez,$data,$orario,$id)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($id) $filtro="and id_gruppo='$id'"; else $filtro='';
	if($id_sez) $filtrosez="and Id_sez=$id_sez"; else $filtrosez='';
	
	if($circo){

		$filtrocirco="and id_sez in (select id_sez from ".$prefix."_ele_sezione where id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc'))";
	}else
		$filtrocirco='';

	$sql="select voti_uomini,voti_donne,voti_complessivi,id_sez,id_gruppo from ".$prefix."_ele_voti_parziale where data='$data' and orario='$orario' and id_cons=$id_cons $filtrosez $filtro $filtrocirco";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(); 
	return($row);	
	
}

function affluenze_totali($id)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
#	if($id) $filtro="and id_gruppo='$id'"; else $filtro='';
#	if($id_sez) $filtrosez="and Id_sez=$id_sez"; else $filtrosez='';
	if(!$id) $id=$id_cons;
	
	if($circo){

		$filtrocirco="and id_sez in (select id_sez from ".$prefix."_ele_sezione where id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc'))";
	}else
		$filtrocirco='';

	$sql="select sum(voti_uomini),sum(voti_donne),sum(voti_complessivi) as complessivi,data,orario from ".$prefix."_ele_voti_parziale where  id_cons=$id $filtrocirco group by data, orario";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(); 
	return($row);	
	
}

function conscomune($id)
{
global $dbi,$prefix,$id_comune,$id_cons_gen;
	if(!$id) $id=$id_cons_gen;
	$sql="SELECT * FROM ".$prefix."_ele_cons_comune where id_cons_gen=$id and id_comune=$id_comune and chiusa<'2'"; 
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll();
	return($row);	
}

function cons_pubblica($id)
{
	global $dbi,$prefix,$id_cons;
	if(!$id) $id=$id_cons;
	$sql="SELECT count(id_cons) from ".$prefix."_ele_gruppo where id_cons=$id";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	list($gruppi)=$sth->fetch(PDO::FETCH_NUM);
	$sql="SELECT count(id_cons) from ".$prefix."_ele_lista where id_cons=$id";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	list($liste)=$sth->fetch(PDO::FETCH_NUM);
	$sql="SELECT count(id_cons) from ".$prefix."_ele_candidato where id_cons=$id";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	list($candi)=$sth->fetch(PDO::FETCH_NUM);
	if($gruppi+$liste+$candi>0) return true;
	else return false;
}

function dati_comune()
{
global $dbi,$prefix,$id_comune;
	$sql="SELECT * FROM ".$prefix."_ele_comune where id_comune=$id_comune";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll();
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

function dati_cons_comune($id)
{
global $dbi,$prefix,$id_cons_gen;
	if(!$id) $id=$id_cons_gen;
	$sql="SELECT * FROM ".$prefix."_ele_cons_comune where id_cons_gen='$id'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function dati_consultazione($id)
{
global $dbi,$prefix,$id_cons_gen;
	if(!$id) $id=$id_cons_gen;
	$sql="SELECT * FROM ".$prefix."_ele_consultazione where id_cons_gen='$id'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function dati_fascia($idconf,$idfascia)
{
global $dbi,$prefix,$id_cons_gen;
	$sql="SELECT * FROM ".$prefix."_ele_fascia where id_conf=$idconf and id_fascia=$idfascia";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll();
	return($row);	
}

function dati_generali()
{
	global $id_cons_gen,$id_cons,$id_comune,$prefix,$dbi,$genere,$circo,$idcirc;
	if(isset($circo) and $circo){ $filtro="and id_circ='$idcirc'";
	$filtrosedi="and id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc')";
	$filtrocand=" and id_lista in (select id_lista from ".$prefix."_ele_lista where id_circ='$idcirc')";
	}else{
	$filtro='';
	$filtrosedi='';
	$filtrocand='';
	}
	
	$sql="SELECT id_cons FROM ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen' and id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($id_cons)=$res->fetch(PDO::FETCH_NUM);

	$sql="select * from ".$prefix."_ele_circoscrizione where id_cons='$id_cons' $filtro";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$sql="select * from ".$prefix."_ele_sede where id_cons='$id_cons' $filtro";
	$ressede = $dbi->prepare("$sql");
	$ressede->execute();
	$sql="select * from ".$prefix."_ele_sezione where id_cons='$id_cons' $filtrosedi";
	$res3 = $dbi->prepare("$sql");
	$res3->execute();
	$numcirco = $res->rowCount();
	$sedi = $ressede->rowCount();
	$sez = $res3->rowCount();		
	$sql="select * from ".$prefix."_ele_lista where id_cons='$id_cons' $filtro";
	$res3 = $dbi->prepare("$sql");
	$res3->execute();
	$liste = $res3->rowCount();    
	$sql="select id_cons from ".$prefix."_ele_candidato where id_cons='$id_cons' $filtrocand";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();
	$candi = $res1->rowCount();	
	// se non europee (non liste e candidati)
	if ($genere!=4){
		$sql="select id_cons from ".$prefix."_ele_gruppo where id_cons='$id_cons' $filtro";
	}else{
		$sql="select id_cons from ".$prefix."_ele_lista where id_cons='$id_cons' $filtro";
	}
	$res2 = $dbi->prepare("$sql");
	$res2->execute();
	$gruppi = $res2->rowCount();
	$sql="select sum(maschi),sum(femmine), sum(maschi+femmine) from ".$prefix."_ele_sezione where id_cons=$id_cons $filtrosedi";
	$res4 = $dbi->prepare("$sql");
	$res4->execute();
 	if($res4) list($maschi,$femmine,$tot) = $res4->fetch(PDO::FETCH_NUM);
	$row=array($tot,$maschi,$femmine,$numcirco,$sedi,$sez,$gruppi,$liste,$candi);
	return($row);
}
function dati_sede($id_sede)
{
	global $id_cons,$prefix,$dbi;
	$sql="select * from ".$prefix."_ele_sede where id_sede=$id_sede";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(); 
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
global $dbi,$prefix,$id_comune,$id_cons_gen,$id_cons;
	$sql="SELECT id_cons FROM ".$prefix."_ele_comune where id_comune='$id_comune' ";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($id_cons_pred)=$sth->fetch(PDO::FETCH_NUM);
	if(!$sth->rowCount() or !$id_cons_pred)
	{
		if(!$id_cons_pred)
		{
			$sql="SELECT id_cons FROM ".$prefix."_ele_cons_comune where id_comune='$id_comune' and chiusa<'2' order by id_cons desc limit 0,1";
			$sth = $dbi->prepare("$sql");
			$sth->execute();
			list($id_cons_pred)=$sth->fetch(PDO::FETCH_NUM);
		}			
	}
	if($id_cons_pred)
	{
		$sql="SELECT t1.id_cons_gen,t1.id_cons,t1.proiezione,t2.tipo_cons FROM ".$prefix."_ele_cons_comune as t1 left join ".$prefix."_ele_consultazione as t2 on t1.id_cons_gen=t2.id_cons_gen where t1.id_cons='$id_cons_pred' and t1.chiusa<'2' order by t1.id_cons desc limit 0,1";
		$sth = $dbi->prepare("$sql");
		$sth->execute();
		if($sth->rowCount()) {
			$row = $sth->fetch(PDO::FETCH_BOTH);
			$id_cons_gen=$row['id_cons_gen'];
			$id_cons=$row['id_cons'];
			return($row);
		}
	}
	$sql="SELECT t1.id_cons_gen,t1.id_cons,t1.proiezione,t2.tipo_cons FROM ".$prefix."_ele_cons_comune as t1 left join ".$prefix."_ele_consultazione as t2 on t1.id_cons_gen=t2.id_cons_gen  where t1.id_comune='$id_comune' and chiusa<'2' order by t1.id_cons desc limit 0,1";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetch(PDO::FETCH_BOTH);
	$id_cons_gen=$row[0];
	$id_cons=$row[1];
	return ($row);	
}

function elenco_affluenze()
{
	global $id_cons,$prefix,$dbi;
	$sql="select voti_complessivi,voti_uomini,voti_donne, data, orario, id_gruppo, id_sez from ".$prefix."_ele_voti_parziale where id_cons=$id_cons order by data,orario asc";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(); 
	return($row);	
	
}

function elenco_affluenze_ref()
{
	global $id_cons,$prefix,$dbi;
	$sql="select voti_complessivi,voti_uomini,voti_donne, data, orario, id_gruppo, id_sez from ".$prefix."_ele_voti_parziale where id_cons=$id_cons order by data,orario asc";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(); 
	return($row);	
	
}

function elenco_tot_affluenze()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo){

		$filtro="and id_sez in (select id_sez from ".$prefix."_ele_sezione where id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc'))";
	}else
		$filtro='';

	$sql="select sum(t3.voti_complessivi) as complessivi,sum(t3.voti_uomini),sum(t3.voti_donne), t3.data, t3.orario, t3.id_gruppo from ".$prefix."_ele_voti_parziale as t3 where t3.id_cons=$id_cons $filtro group by t3.data, t3.orario, t3.id_gruppo order by t3.data,t3.orario asc";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(); 
	return($row);	
	
}

function elenco_candidati($idlista)
{
	global $id_cons,$prefix,$dbi;
	if($idlista) $filtro="and id_lista=$idlista"; else $filtro='';
	$sql="SELECT id_cand,num_lista,num_cand,concat(cognome,' ', nome) as nominativo,0,id_lista,cv,cg FROM ".$prefix."_ele_candidato where id_cons='$id_cons' $filtro order by num_lista,num_cand";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function elenco_candidati_liste($i)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if(isset($circo) and $circo) $filtro="and t2.id_circ='$idcirc'"; else $filtro='';
	if($i==1) $ordine="order by t1.cognome,t1.nome";
	else $ordine="order by t2.num_lista,t1.num_cand";
	$sql="select t1.id_cand, t1.num_lista,t1.num_cand,concat(t1.cognome,' ', t1.nome) as descrizione,0 as votisum,t1.id_lista,t1.eletto from ".$prefix."_ele_candidato as t1 left join ".$prefix."_ele_lista as t2 on t1.id_lista=t2.id_lista where t1.id_cons='$id_cons' $filtro $ordine";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function elenco_circoscrizioni()
{
	global $id_cons,$prefix,$dbi;
	$sql="select t1.*,sum(t3.validi),sum(t3.nulli),sum(t3.bianchi),sum(t3.contestati) from ".$prefix."_ele_circoscrizione as t1 left join ".$prefix."_ele_sede as t2 on t1.id_circ=t2.id_circ left join ".$prefix."_ele_sezione as t3 on t2.id_sede=t3.id_sede where t1.id_cons='$id_cons' group by t1.id_cons,t1.id_circ,t1.num_circ,t1.descrizione";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$row = $res->fetchAll(PDO::FETCH_ASSOC);
	return($row);	

}

function elenco_come()
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	$row=array();
	$sql="SELECT id_cons FROM ".$prefix."_ele_cons_comune where id_comune='$id_comune' and id_cons_gen=$id_cons_gen";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($id_cons)=$sth->fetch(PDO::FETCH_NUM);
	if($id_cons) {
		$sql="select * from ".$prefix."_ele_come where id_cons=$id_cons";
		$sth = $dbi->prepare("$sql");
		$sth->execute();	
		$row = $sth->fetchAll();
		return($row);	
	}
	return($row);
}

#elenco completo dei comuni 
function elenco_comuni()
{
global $dbi,$prefix;
	$sql="select t1.* from ".$prefix."_ele_comune as t1 where t1.id_comune in (select distinct id_comune from ".$prefix."_ele_cons_comune as t2) order by t1.descrizione asc";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll();
	return($row);	
}
	
# elenco consultazioni ordinate per data
function elenco_cons()
{
global $dbi,$prefix,$id_comune;
	$sql="SELECT t1.*,t2.chiusa,t2.id_conf,t2.preferita,t2.preferenze,t2.id_fascia,t2.vismf,t2.solo_gruppo,t2.disgiunto,t2.proiezione FROM ".$prefix."_ele_consultazione as t1,".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_comune=$id_comune and t2.chiusa<'2' order by t1.data_inizio desc";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

# elenco consultazioni ordinate per tipo
function elenco_cons_tipo()
{
global $dbi,$prefix,$id_comune;

	$sql="SELECT t1.descrizione, t1.id_cons_gen, t1.data_inizio,t2.id_cons,t1.link_trasparenza FROM ".$prefix."_ele_consultazione as t1,".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_comune=$id_comune and t2.chiusa<'2' and t1.data_inizio in (SELECT max(t3.data_inizio) FROM soraldo_ele_consultazione as t3 left join soraldo_ele_cons_comune as t4 on t3.id_cons_gen=t4.id_cons_gen where t4.id_comune=$id_comune)";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row1 = $sth->fetchAll();
	$tmp = array();
	foreach($row1 as $val) $tmp[]=$val[1];
	$elencoid=implode(',',$tmp);
	if($sth->rowCount()) $cond="and t1.id_cons_gen not in ($elencoid)"; else $cond='';
	$sql="SELECT t1.descrizione, t1.id_cons_gen, t1.data_inizio,t2.id_cons,t1.tipo_cons,t1.link_trasparenza FROM ".$prefix."_ele_consultazione as t1,".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_comune=$id_comune and t2.chiusa<'2' $cond order by t1.data_inizio desc";	
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll();
	return array($row1,$row);	
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

function elenco_gruppi()
{
	global $id_cons,$id_comune,$prefix,$dbi;
	$sql="select * from ".$prefix."_ele_gruppo where id_cons='$id_cons' order by num_gruppo";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_gruppi_bak($tab=null)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and id_circ='$idcirc'"; else $cond='';
	if($tab=='gruppo') $eletto=',eletto,prognome,id_colore'; else $eletto='';
	$sql="SELECT id_$tab, num_$tab,descrizione,0 $eletto FROM ".$prefix."_ele_$tab where id_cons='$id_cons' $cond order by num_$tab";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}
function elenco_gruppi_trasparenza()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and id_circ='$idcirc'"; else $cond='';
	$sql="SELECT * FROM ".$prefix."_ele_gruppo where id_cons='$id_cons' $cond order by num_gruppo";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function elenco_info($tab)
{
	global $id_cons,$id_comune,$prefix,$dbi;
	$sql="select * from ".$prefix."_ele_$tab where id_cons=$id_cons order by mid asc";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_iscritti()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo){

		$filtrocirco="and id_sez in (select id_sez from ".$prefix."_ele_sezione where id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc'))";
	}else
		$filtrocirco='';
	$sql="SELECT num_sez,id_sez,maschi,femmine,(maschi+femmine) as elettori, 0 as id_gruppo FROM ".$prefix."_ele_sezione where id_cons='$id_cons' $filtrocirco order by num_sez";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function elenco_tot_iscritti()
{
	global $id_cons,$prefix,$dbi;
	$sql="SELECT sum(maschi),sum(femmine),sum(maschi+femmine) as elettori FROM ".$prefix."_ele_sezione where id_cons='$id_cons'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function elenco_leggi()
{
	global $id_cons,$id_comune,$prefix,$dbi;
	$sql="select id_conf,descrizione from ".$prefix."_ele_conf order by id_conf";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_link()
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	$row=array();
	$sql="SELECT id_cons FROM ".$prefix."_ele_cons_comune where id_comune='$id_comune' and id_cons_gen=$id_cons_gen";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($id_cons)=$sth->fetch(PDO::FETCH_NUM);
	if($id_cons) {
		$sql="select * from ".$prefix."_ele_link where id_cons=$id_cons";
		$sth = $dbi->prepare("$sql");
		$sth->execute();	
		$row = $sth->fetchAll();
		return($row);	
	}
	return($row);
}

function elenco_liste()
{
	global $id_cons,$prefix,$dbi,$idcirc,$circo;
	if(isset($circo) and $circo) $filtro="and id_circ='$idcirc'"; else $filtro='';
	$sql="SELECT * FROM ".$prefix."_ele_lista where id_cons='$id_cons' $filtro order by num_lista";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	

}

function elenco_liste_gruppo($id_gruppo)
{
	global $id_cons,$prefix,$dbi,$idcirc,$circo;
	if(isset($circo) and $circo) $filtro="and id_circ='$idcirc'"; else $filtro='';
	$sql="SELECT id_lista,num_gruppo,num_lista,descrizione,0,link_trasparenza FROM ".$prefix."_ele_lista where id_cons='$id_cons' and id_gruppo='$id_gruppo' $filtro order by num_lista";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function elenco_orari()
{
	global $id_cons_gen,$prefix,$dbi;
	$sql="SELECT * FROM ".$prefix."_ele_rilaff where id_cons_gen='$id_cons_gen' order by data,orario"; 
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function elenco_numeri()
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	$row=array();
	$sql="SELECT id_cons FROM ".$prefix."_ele_cons_comune where id_comune='$id_comune' and id_cons_gen=$id_cons_gen";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($id_cons)=$sth->fetch(PDO::FETCH_NUM);
	if($id_cons) {
		$sql="select * from ".$prefix."_ele_numero where id_cons=$id_cons";
		$sth = $dbi->prepare("$sql");
		$sth->execute();	
		$row = $sth->fetchAll();
		return($row);	
	}
	return($row);
}

function elenco_permessi()
{
	global $id_cons,$id_comune,$prefix,$dbi;
	$sql="select t1.*, t2.indirizzo, t3.num_sez, t4.admincomune, t4.adminsuper from ".$prefix."_ele_operatore as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede left join ".$prefix."_ele_sezione as t3 on t1.id_sez=t3.id_sez left join ".$prefix."_authors as t4 on t1.aid=t4.aid where t1.id_cons='$id_cons' order by aid";
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

function elenco_sedi($i=0)
{
	global $id_cons_gen,$id_cons,$id_comune,$prefix,$dbi,$genere,$idcirc,$circo;
	if(isset($circo) and $circo) $filtro="and t1.id_circ='$idcirc'"; else $filtro='';
	if($i==1) $ordine='order by t2.num_circ'; else $ordine='order by t2.descrizione'; 
	$sql="select t1.*,t2.descrizione from ".$prefix."_ele_sede as t1, ".$prefix."_ele_circoscrizione as t2 where t1.id_circ=t2.id_circ and t1.id_cons='$id_cons' $filtro $ordine"; 
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	
	
}

function elenco_servizi()
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	$row=array();
	$sql="SELECT id_cons FROM ".$prefix."_ele_cons_comune where id_comune='$id_comune' and id_cons_gen=$id_cons_gen";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($id_cons)=$sth->fetch(PDO::FETCH_NUM);
	if($id_cons) {
		$sql="select * from ".$prefix."_ele_servizio where id_cons=$id_cons";
		$sth = $dbi->prepare("$sql");
		$sth->execute();	
		$row = $sth->fetchAll();
		return($row);	
	}
	return($row);
}

function elenco_sezioni($idsede=0)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo){

		$filtrocirco="and t1.id_sez in (select id_sez from ".$prefix."_ele_sezione where id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc'))";
	}else
		$filtrocirco='';
	if($idsede>0) 
		$filtrosede="and t1.id_sede='$idsede'";
	else
		$filtrosede='';
	$sql="select t1.*,t2.id_sede,t2.indirizzo from ".$prefix."_ele_sezione as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $filtrocirco $filtrosede order by num_sez";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	
}

function elenco_tipi()
{
global $dbi,$prefix,$id_cons_gen;
	$sql="SELECT * FROM ".$prefix."_ele_tipo";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll();
	return($row);	
}

function elenco_utenti()
{
	global $id_cons,$id_comune,$prefix,$dbi;
	$sql="select * from ".$prefix."_authors where id_comune='$id_comune' order by aid";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function elenco_utenti_no_permessi()
{
	global $id_cons,$id_comune,$prefix,$dbi;
	$sql="select * from ".$prefix."_authors where id_comune='$id_comune' and admincomune='0' and aid not in (select aid from ".$prefix."_ele_operatore where id_cons='$id_cons') order by aid";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	return($row);	
}

function id_referendum($num,$ic)
{
global $dbi,$prefix,$id_cons;
	if(!$num) $num=1;
	if(!$ic) $ic=$id_cons;
	$sql="SELECT id_gruppo FROM ".$prefix."_ele_gruppo where id_cons=$ic and num_gruppo=$num";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll();
	return($row);	
}

function numseggilista()
{
global $dbi,$prefix,$id_cons;
	$sql="SELECT num_lista,count(eletto) as eletti FROM ".$prefix."_ele_candidato where id_cons='$id_cons' and eletto='1' group by num_lista";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll();
	return($row);	
}

function precedente_consultazione()
{
	global $id_cons_gen,$prefix,$dbi,$tipocons,$datainizio,$id_comune;
	$tipo=$tipocons;
	if($tipocons==6 or $tipocons==11 or $tipocons==15 or $tipocons==18) $tipo="'6' or t1.tipo_cons='11' or t1.tipo_cons='15' or t1.tipo_cons='18'";
	if($tipocons==7 or $tipocons==10 or $tipocons==16 or $tipocons==19) $tipo="'7' or t1.tipo_cons='10' or t1.tipo_cons='16' or t1.tipo_cons='19'";
	
	$sql="select t1.* from  ".$prefix."_ele_consultazione as t1 left join ".$prefix."_ele_cons_comune as t2 on t2.id_cons_gen=t1.id_cons_gen where t2.id_comune=$id_comune and t2.chiusa<'2' and (t1.tipo_cons=$tipo) and t1.data_inizio<'$datainizio' order by t1.data_inizio desc limit 0,1"; 
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	
	
}

function presenza_immagine($tab,$id)
{
	global $id_cons,$prefix,$dbi;
	$sql = "select stemma from ".$prefix."_ele_$tab where id_$tab='$id'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($row) = $sth->fetch(PDO::FETCH_NUM);
	if($row) return (true); else return (false);
}

function scrutinio_affluenze($id)
{
	global $id_cons,$prefix,$dbi;
	$sql="select t3.data,t3.orario from ".$prefix."_ele_voti_parziale as t3 where t3.id_cons=$id_cons order by t3.data desc,t3.orario desc limit 0,1";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	if(count($row)) {
		$data=$row[0][0];
		$orario=$row[0][1];
		$sql="select count(t3.orario) from ".$prefix."_ele_voti_parziale as t3 where t3.id_cons=$id_cons and t3.data='$data' and t3.orario='$orario' and id_gruppo='$id'"; 
		$sth = $dbi->prepare("$sql");
		$sth->execute();
		$row = $sth->fetchAll();
	}
	return($row);	
	
}

function scrutinio_schede($tab)
{
	global $id_cons,$prefix,$dbi;
	$sql="select sum(t1.voti) from ".$prefix."_ele_voti_$tab as t1 left join ".$prefix."_ele_$tab as t2 on t1.id_$tab=t2.id_$tab where t1.id_cons='$id_cons'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}
function scrutinio_tot_gruppo($tab)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and t2.id_circ='$idcirc'"; else $cond='';
	if($tab=='gruppo') $eletto=',t2.eletto'; else $eletto='';
	$sql="select t2.id_$tab, t2.num_$tab,t2.descrizione,sum(t1.voti) $eletto from ".$prefix."_ele_voti_$tab as t1 left join ".$prefix."_ele_$tab as t2 on t1.id_$tab=t2.id_$tab where t1.id_cons='$id_cons' $cond group by t2.id_$tab,t2.num_$tab,t2.descrizione $eletto order by t2.num_$tab";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}
function scrutinio_tot_cand_finale($idlista)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($idlista) $filtro="and t2.id_lista=$idlista"; else $filtro='';
	$sql="select t2.id_cand, t2.num_lista,t2.num_cand,concat(t2.cognome,' ', t2.nome) as descrizione,sum(t1.voti) as votisum,t2.id_lista,t2.eletto from ".$prefix."_ele_voti_candidato as t1 left join ".$prefix."_ele_candidato as t2 on t1.id_cand=t2.id_cand where t1.id_cons='$id_cons' $filtro group by t2.id_cand,t2.num_lista,t2.num_cand,descrizione,t2.id_lista,t2.eletto order by votisum desc,t2.num_cand";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}
function scrutinio_tot_gruppo_finale($tab)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and t2.id_circ='$idcirc'"; else $cond='';
	if($tab=='gruppo') $eletto=',t2.eletto'; else $eletto='';
	$sql="select t2.id_$tab, t2.num_$tab,t2.descrizione,sum(t1.voti) as votisum $eletto from ".$prefix."_ele_voti_$tab as t1 left join ".$prefix."_ele_$tab as t2 on t1.id_$tab=t2.id_$tab where t1.id_cons='$id_cons' $cond group by t2.id_$tab,t2.num_$tab,t2.descrizione $eletto order by votisum desc";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}
function scrutinio_tot_lista_finale($tab)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and t2.id_circ='$idcirc'"; else $cond='';

	$sql="select t2.id_$tab,t2.num_gruppo, t2.num_$tab,t2.descrizione,sum(t1.voti) as votisum from ".$prefix."_ele_voti_$tab as t1 left join ".$prefix."_ele_$tab as t2 on t1.id_$tab=t2.id_$tab where t1.id_cons='$id_cons' $cond group by t2.id_$tab, t2.num_gruppo,t2.num_$tab,t2.descrizione order by votisum desc";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function scrutinate($tab)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($tab=='candidati') {
		if(isset($circo) and $circo) 
			$filtrotab=" and id_sez in(select id_sez from ".$prefix."_ele_sezione where id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc'))";
		else
			$filtrotab='';
		
		$sql="SELECT distinct id_sez FROM ".$prefix."_ele_voti_candidato WHERE id_cons=$id_cons $filtrotab"; 
	}else{ 
		if(isset($circo) and $circo) {
			$filtrotab=" and id_$tab in (select id_$tab from ".$prefix."_ele_$tab where id_circ='$idcirc')";
		}else $filtrotab='';
		$sql="select distinct id_sez from ".$prefix."_ele_voti_$tab where id_cons='$id_cons' $filtrotab";
	}
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->rowCount();
	return($row);	

}

function scrutinate_inizio($tab)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($tab=='candidati') {
		if(isset($circo) and $circo) 
			$filtrotab=" and id_sez in(select id_sez from ".$prefix."_ele_sezione where id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc'))";
		else
			$filtrotab='';
		
		$sql="SELECT sum(voti) FROM ".$prefix."_ele_voti_candidato WHERE id_cons=$id_cons $filtrotab"; 
	}else{ 
		if(isset($circo) and $circo) {
			$filtrotab=" and id_$tab in (select id_$tab from ".$prefix."_ele_$tab where id_circ='$idcirc')";
		}else $filtrotab='';
		$sql="select sum(voti) from ".$prefix."_ele_voti_$tab where id_cons='$id_cons' $filtrotab";
	}
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function scrutinate_referendum()
{
	global $id_cons,$prefix,$dbi;
	$sql="select distinct id_sez,id_gruppo from ".$prefix."_ele_voti_ref where id_cons='$id_cons'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function sezioni_totali()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo){

		$filtro="and id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc')";
	}else
		$filtro='';

	$sql="select id_sez from ".$prefix."_ele_sezione where id_cons='$id_cons' $filtro";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->rowCount();
	return($row);	

}

function tipo_consultazione($id)
{
	global $id_cons_gen,$id_comune,$prefix,$dbi;
	if(!$id) $id=$id_cons_gen;
	$sql="select descrizione from ".$prefix."_ele_tipo where tipo_cons='$id'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	list($row) = $sth->fetch(PDO::FETCH_NUM);
	return($row);	
}

function tipo_consultazione_bak($id)
{
global $dbi,$prefix,$id_cons_gen;
if(!$id) $id=$id_cons_gen;	
	$sql="SELECT t1.*,t2.data_inizio FROM ".$prefix."_ele_tipo as t1 left join ".$prefix."_ele_consultazione as t2 on t1.tipo_cons=t2.tipo_cons where t2.id_cons_gen=$id";
	$sth = $dbi->prepare("$sql");
	$sth->execute();	
	$row = $sth->fetchAll(PDO::FETCH_NUM);
	return($row);	
}

function totale_iscritti($id)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if(!$id) $id=$id_cons;
	if($circo){
		$lastcirc=$idcirc;
		if($id!=$id_cons) {
			$sql="select * from ".$prefix."_ele_circoscrizione where id_circ='$idcirc' ";
			$res = $dbi->prepare("$sql");
			$res->execute();
			$row = $res->fetchAll();
			$numcirc=$row[0]['num_circ'];
			$sql="select * from ".$prefix."_ele_circoscrizione where id_cons='$id' and num_circ='$numcirc'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			$row = $res->fetchAll();
			$lastcirc=$row[0]['id_circ'];
		}
		$filtro="and id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$lastcirc')";
	}else
		$filtro='';

	$sql="SELECT sum(maschi),sum(femmine),sum(maschi+femmine) as elettori FROM ".$prefix."_ele_sezione where id_cons='$id' $filtro";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
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

function ultime_affluenze($id)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if(!$id) $id=$id_cons;
	if($circo){
		$lastcirc=$idcirc;
		if($id!=$id_cons) {
			$sql="select * from ".$prefix."_ele_circoscrizione where id_circ='$idcirc' ";
			$res = $dbi->prepare("$sql");
			$res->execute();
			$row = $res->fetchAll();
			$numcirc=$row[0]['num_circ'];
			$sql="select * from ".$prefix."_ele_circoscrizione where id_cons='$id' and num_circ='$numcirc'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			$row = $res->fetchAll();
			$lastcirc=$row[0]['id_circ'];
		}
		$filtro="and id_sez in (select id_sez from ".$prefix."_ele_sezione where id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$lastcirc'))";
	}else
		$filtro='';
	$sql="select sum(t3.voti_complessivi) as complessivi,sum(t3.voti_uomini),sum(t3.voti_donne), t3.data, t3.orario from ".$prefix."_ele_voti_parziale as t3 where t3.id_cons=$id $filtro group by t3.data, t3.orario order by t3.data desc,t3.orario desc limit 0,1";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	
	
}

function ultime_affluenze_referendum($id)
{
	global $id_cons,$prefix,$dbi;
	$sql="select sum(t3.voti_complessivi) as complessivi,sum(t3.voti_uomini),sum(t3.voti_donne),t3.data ,t3.orario from ".$prefix."_ele_voti_parziale as t3 where t3.id_cons=$id_cons and t3.id_gruppo='$id' group by t3.id_gruppo,t3.data ,t3.orario order by t3.data desc,t3.orario desc limit 0,1"; 
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
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

function ultime_affluenze_sezref($id_sez,$id_gruppo)
{
	global $id_cons,$prefix,$dbi;
	$sql="select t3.voti_complessivi,t3.voti_uomini,t3.voti_donne from ".$prefix."_ele_voti_parziale as t3 where t3.id_sez='$id_sez' and t3.id_gruppo=$id_gruppo order by t3.data desc,t3.orario desc limit 0,1"; 
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll(); 
	return($row);	
	
}

function ultimo_aggiornamento()
{
	global $id_cons,$prefix,$dbi;
	$sql="select data, ora from ".$prefix."_ele_log where id_cons=$id_cons order by data desc,ora desc limit 0,1";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
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

function voti_candidati($numlista)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and t3.id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc')"; else $cond='';
	if($numlista) $filtro="and t2.num_lista=$numlista"; else $filtro='';
	$sql="select t1.id_sez,t3.num_sez,t2.num_cand,concat(cognome,' ', nome) as nominativo,t1.voti from ".$prefix."_ele_voti_candidato as t1 left join ".$prefix."_ele_candidato as t2 on t1.id_cand=t2.id_cand left join ".$prefix."_ele_sezione as t3 on t1.id_sez=t3.id_sez where t1.id_cons='$id_cons' $filtro $cond order by t2.num_lista,t1.num_cand";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_candidati_circo($numlista)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($numlista) $filtro="and t2.num_lista=$numlista"; else $filtro='';
	$sql="select t5.id_circ,t5.num_circ,t2.num_cand,concat(cognome,' ', nome) as nominativo,sum(t1.voti) from ".$prefix."_ele_voti_candidato as t1 left join ".$prefix."_ele_candidato as t2 on t1.id_cand=t2.id_cand left join ".$prefix."_ele_sezione as t3 on t1.id_sez=t3.id_sez left join ".$prefix."_ele_sede as t4 on t3.id_sede=t4.id_sede left join ".$prefix."_ele_circoscrizione as t5 on t4.id_circ=t5.id_circ where t1.id_cons='$id_cons' $filtro group by t5.id_circ,t5.num_circ,t2.num_cand,nominativo order by t2.num_lista,t1.num_cand";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_gruppo($tab)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and t2.id_circ='$idcirc'"; else $cond='';
	$sql="select t1.id_sez,t3.num_sez,t2.num_$tab,t2.descrizione,t1.voti from ".$prefix."_ele_voti_$tab as t1 left join ".$prefix."_ele_$tab as t2 on t1.id_$tab=t2.id_$tab left join ".$prefix."_ele_sezione as t3 on t1.id_sez=t3.id_sez where t1.id_cons='$id_cons' $cond order by t2.num_$tab";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_gruppo_circo($tab)
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	
	$sql="select t5.id_circ,t5.num_circ,t2.num_$tab,t2.descrizione,sum(t1.voti) from ".$prefix."_ele_voti_$tab as t1 left join ".$prefix."_ele_$tab as t2 on t1.id_$tab=t2.id_$tab left join ".$prefix."_ele_sezione as t3 on t1.id_sez=t3.id_sez left join ".$prefix."_ele_sede as t4 on t3.id_sede=t4.id_sede left join ".$prefix."_ele_circoscrizione as t5 on t4.id_circ=t5.id_circ where t1.id_cons='$id_cons' group by t5.id_circ,t5.num_circ,t2.num_$tab,t2.descrizione order by t2.num_$tab";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
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

function voti_referendum($id)
{
	global $id_cons,$prefix,$dbi;
	$sql="select t1.id_gruppo,t1.num_gruppo, t1.si,t1.no,t1.validi,t1.nulli,t1.bianchi,t1.contestati,t1.id_sez,t2.num_sez from ".$prefix."_ele_voti_ref as t1 left join ".$prefix."_ele_sezione as t2 on t1.id_sez=t2.id_sez where t1.id_cons='$id_cons' and t1.id_gruppo=$id";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_sezione()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo){

		$filtro="and id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc')";
	}else
		$filtro='';

	$sql="SELECT * FROM ".$prefix."_ele_sezione where id_cons='$id_cons' $filtro";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_totali()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo){

		$filtro="and id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$idcirc')";
	}else
		$filtro='';
	$sql="SELECT sum(validi) as valide,sum(nulli) as nulle,sum(bianchi) as bianche,sum(contestati) as contestate FROM ".$prefix."_ele_sezione where id_cons='$id_cons' $filtro";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_tot_candidato($idlista)
{
	global $id_cons,$prefix,$dbi;
	if($idlista) $filtro="and t2.id_lista=$idlista"; else $filtro='';
	$sql="select t1.id_cand,t2.num_lista,t2.num_cand,concat(t2.cognome,' ', t2.nome) as nominativo,sum(t1.voti),t2.id_lista from ".$prefix."_ele_voti_candidato as t1 left join ".$prefix."_ele_candidato as t2 on t1.id_cand=t2.id_cand where t1.id_cons='$id_cons' $filtro group by t1.id_cand,t2.num_lista,t2.num_cand,nominativo,t2.id_lista order by t2.num_lista,t2.num_cand";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_tot_lista()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and t2.id_circ='$idcirc'"; else $cond='';
	$sql="select t1.id_lista,t2.num_gruppo,t2.num_lista,t2.descrizione,sum(t1.voti) from ".$prefix."_ele_voti_lista as t1 left join ".$prefix."_ele_lista as t2 on t1.id_lista=t2.id_lista where t1.id_cons='$id_cons' $cond group by t1.id_lista,t2.num_gruppo,t2.num_lista,t2.descrizione order by t2.num_gruppo,t2.num_lista";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_lista_graf()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and t2.id_circ='$idcirc'"; else $cond='';
	$sql="select t1.id_lista,t2.num_gruppo,t2.num_lista,t2.descrizione,sum(t1.voti) as votisum from ".$prefix."_ele_voti_lista as t1 left join ".$prefix."_ele_lista as t2 on t1.id_lista=t2.id_lista where t1.id_cons='$id_cons' $cond group by t1.id_lista,t2.num_gruppo,t2.num_lista,t2.descrizione order by votisum desc,t2.num_lista";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_tot_gruppo()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and t2.id_circ='$idcirc'"; else $cond='';
	$sql="select t2.num_gruppo,t2.descrizione,sum(t1.voti) from ".$prefix."_ele_voti_gruppo as t1 left join ".$prefix."_ele_gruppo as t2 on t1.id_gruppo=t2.id_gruppo where t1.id_cons='$id_cons' $cond group by t2.num_gruppo,t2.descrizione order by t2.num_gruppo";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_gruppo_graf()
{
	global $id_cons,$prefix,$dbi,$circo,$idcirc;
	if($circo) $cond="and t2.id_circ='$idcirc'"; else $cond='';
	$sql="select t2.num_gruppo,t2.descrizione,sum(t1.voti) as votisum from ".$prefix."_ele_voti_gruppo as t1 left join ".$prefix."_ele_gruppo as t2 on t1.id_gruppo=t2.id_gruppo where t1.id_cons='$id_cons' $cond group by t2.num_gruppo,t2.descrizione order by votisum desc,t2.num_gruppo";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

function voti_tot_referendum()
{
	global $id_cons,$prefix,$dbi;
	$sql="select id_gruppo,num_gruppo,sum(si) as si,sum(no),sum(validi),sum(nulli),sum(bianchi),sum(contestati) from ".$prefix."_ele_voti_ref where id_cons='$id_cons' group by id_gruppo,num_gruppo order by num_gruppo";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$row = $sth->fetchAll();
	return($row);	

}

?>