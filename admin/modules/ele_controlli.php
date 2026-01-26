<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/************************************************************************/
/* Modulo controlla affluenze                                               */
/* Amministrazione                                                      */
/************************************************************************/
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("Location: ../index.php");
    exit;
}

include('config/variabili.php');

function controllo_aff($id_cons,$id_sez,$id_parz){
	global $prefix,$dbi,$id_sede,$id_con_gen,$genere;

	$err=0; 
	$sql="select maschi,femmine from ".$prefix."_ele_sezione where id_sez='$id_sez'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($maschi,$femmine)=$res->fetch(PDO::FETCH_NUM);

	$sql="select voti_uomini,voti_donne,voti_complessivi,id_gruppo from ".$prefix."_ele_voti_parziale where id_sez='$id_sez' order by id_gruppo,data,orario";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$votiu2=0;$votid2=0;$votit2=0;$idg2=-1;
	while(list($voti_u,$voti_d,$voti_t,$idg)=$res->fetch(PDO::FETCH_NUM)) {
		if($idg2!=$idg) {
			$votiu2=0;$votid2=0;$votit2=0;
			$idg2=$idg;
		}
		if((($voti_u+$voti_d!=$voti_t && $voti_u+$voti_d>0) || $voti_u>$maschi || $voti_d>$femmine || $voti_t>$maschi+$femmine ||$voti_u<$votiu2 ||$voti_d<$votid2 || $voti_t<$votit2) ) {
			$err=1; break;}
		$votiu2=$voti_u;$votid2=$voti_d;$votit2=$voti_t;
	}
	$tipo='affluenze';
	$sql="select id from ".$prefix."_ele_controllo where tipo='$tipo' and id_sez='$id_sez' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$righe=$res->rowCount();
	if($righe){
		while(list($id)=$res->fetch(PDO::FETCH_NUM)){#die("$sql");
#			if($id==$id_parz){
				if(!$err){ 
					$sql="delete from ".$prefix."_ele_controllo where tipo='$tipo' and id_sez='$id_sez' ";
					$res = $dbi->prepare("$sql");
					$res->execute();
					
				}
				$err=0;
				break;
#			}					
		}
	}
	if($err){
		$sql="insert into ".$prefix."_ele_controllo value('$id_cons','$id_sez','$tipo','$idg2')";#die("$sql");
		$res = $dbi->prepare("$sql");
		$res->execute();		
	}

	$sql="SELECT validi,nulli,bianchi,contestati,voti_nulli FROM ".$prefix."_ele_sezione as t1 where t1.id_sez='$id_sez'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($validi, $nulli, $bianchi,$contestati,$votinulli)=$res->fetch(PDO::FETCH_NUM);
	if (($validi+$nulli+$bianchi+$contestati+$votinulli)>0)
		if($genere==0) controllo_votir($id_cons,$id_sez,'aff');
		else controllo_voti($id_cons,$id_sez);

}


	
function controllo_voti($id_cons,$id_sez){
	global $prefix,$dbi,$id_sede,$id_con_gen,$genere;
		##############################
	$err=0;
	$sql="SELECT id_gruppo FROM ".$prefix."_ele_gruppo where id_cons='$id_cons'";
	$resref = $dbi->prepare("$sql");
	$resref->execute();	
	if($genere==0){ 
		$numscru=$resref->rowCount(); $rifscru=0;
		while(list($idrefgruppo)=$resref->fetch(PDO::FETCH_NUM)) {
			$sql="SELECT si,no,validi,nulli,bianchi,contestati FROM ".$prefix."_ele_voti_ref where id_sez='$id_sez' and id_gruppo='$idrefgruppo'";
			$res2 = $dbi->prepare("$sql");
			$res2->execute();
			$refscru=$res2->rowCount(); 
			$sql="SELECT max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_sez='$id_sez' and id_gruppo='$idrefgruppo'";
			$res3 = $dbi->prepare("$sql");
			$res3->execute();
			list($voti)=$res3->fetch(PDO::FETCH_NUM);
			$rifscru++;
			list($si,$no,$validi,$nulli,$bianchi,$contestati)=$res2->fetch(PDO::FETCH_NUM);
			if($validi and ($si+$no==$validi) and ($validi+$nulli+$bianchi+$contestati==$voti)) 
				continue;
			else {$err=1; break;}	
		}
	}else{
		#per le altre consultazione
		$sql="SELECT max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_sez='$id_sez'";
		$res3 = $dbi->prepare("$sql");
		$res3->execute();
		list($voti)=$res3->fetch(PDO::FETCH_NUM);
		$sql="SELECT validi FROM ".$prefix."_ele_sezione where id_sez='$id_sez' and id_cons='$id_cons' ";
		$res2 = $dbi->prepare("$sql");
		$res2->execute();
		list($validi) = $res2->fetch(PDO::FETCH_NUM);
		if($validi) {
			$status=0;
			$query="SELECT validi,nulli,bianchi,contestati,voti_nulli,solo_gruppo,validi_lista,contestati_lista,voti_nulli_lista,solo_lista FROM ".$prefix."_ele_sezione as t1 where t1.id_sez='$id_sez'";
			$sql="$query";
			$res4 = $dbi->prepare("$sql");
			$res4->execute();
			list($validi, $nulli, $bianchi,$contestati,$votinulli)=$res4->fetch(PDO::FETCH_NUM);
			if (($validi+$nulli+$bianchi+$contestati+$votinulli)!=$voti)
				{$err=1;}
		} 
	}
	$tipo='votanti';
	$sql="select id from ".$prefix."_ele_controllo where tipo='$tipo' and id_sez='$id_sez' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$righe=$res->rowCount();
	if($righe){
		if(!$err){ 
			$sql="delete from ".$prefix."_ele_controllo where tipo='$tipo' and id='$id_sez' ";
			$res = $dbi->prepare("$sql");
			$res->execute();
		}
		$err=0;
	}
	if($err){
		$sql="insert into ".$prefix."_ele_controllo value('$id_cons','$id_sez','$tipo','$id_sez')";
		$res = $dbi->prepare("$sql");
		$res->execute();		
	}
	if ($genere==4){
		$sql="select id_lista from ".$prefix."_ele_voti_lista where id_cons='$id_cons' and id_sez='$id_sez'";
		$res = $dbi->prepare("$sql");
		$res->execute();
		if($res->rowCount()) controllo_votil($id_cons,$id_sez,'0');
	}else{
		$sql="select id_gruppo from ".$prefix."_ele_voti_gruppo where id_cons='$id_cons' and id_sez='$id_sez'";
		$res = $dbi->prepare("$sql");
		$res->execute();
		if($res->rowCount()) controllo_votig($id_cons,$id_sez,$genere);
	}
 
}


function controllo_votig($id_cons,$id_sez){
	global $prefix,$dbi,$id_sede,$id_cons_gen,$genere,$votoscollegato;
		##############################
	$err=0; $err2=0;
	$sql="SELECT id_gruppo FROM ".$prefix."_ele_gruppo where id_cons='$id_cons'"; 
	$resref = $dbi->prepare("$sql");
	$resref->execute();	
	$sql="SELECT sum(voti),count(voti) FROM ".$prefix."_ele_voti_gruppo where id_sez='$id_sez'";
	$resref = $dbi->prepare("$sql");
	$resref->execute();
	list($votig,$numrec)=$resref->fetch(PDO::FETCH_NUM);
	$sql="SELECT max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_sez='$id_sez'";
	$res3 = $dbi->prepare("$sql");
	$res3->execute();
	list($voti)=$res3->fetch(PDO::FETCH_NUM);
	$sql="SELECT validi FROM ".$prefix."_ele_sezione where id_sez='$id_sez' and id_cons='$id_cons' ";
	$res2 = $dbi->prepare("$sql");
	$res2->execute();
	list($validi) = $res2->fetch(PDO::FETCH_NUM);
	if($validi and $numrec) {
		$status=0;
		$query="SELECT validi,nulli,bianchi,contestati,voti_nulli,solo_gruppo,validi_lista,contestati_lista,voti_nulli_lista,solo_lista FROM ".$prefix."_ele_sezione as t1 where t1.id_sez='$id_sez'";
		$sql="$query";
		$res4 = $dbi->prepare("$sql");
		$res4->execute();
		list($validi, $nulli, $bianchi,$contestati,$votinulli,$solog,$validil,$contestatil,$nullil,$solol)=$res4->fetch(PDO::FETCH_NUM);
		if (($validi+$nulli+$bianchi+$contestati+$votinulli)!=$voti or ($validi-$solol*$votoscollegato)!=$votig)
			{$err=1;} #die("TEST:if (($validi+$nulli+$bianchi+$contestati+$votinulli)!=$voti or ($validi-$solol*$votoscollegato)!=$votig)");
	} 
	$tipo='gruppo';
	$sql="select id from ".$prefix."_ele_controllo where tipo='$tipo' and id_sez='$id_sez'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$righe=$res->rowCount();
	if($righe){
		if(!$err){ 
			$sql="delete from ".$prefix."_ele_controllo where tipo='$tipo' and id='$id_sez' ";
			$res = $dbi->prepare("$sql");
			$res->execute();
		}
		$err=0;
	}
	if($err){
		$sql="insert into ".$prefix."_ele_controllo value('$id_cons','$id_sez','$tipo','$id_sez')";
		$res = $dbi->prepare("$sql");
		$res->execute();		
	}
	$sql="select id_lista from ".$prefix."_ele_voti_lista where id_cons='$id_cons' and id_sez='$id_sez'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	if($res->rowCount()) controllo_votil($id_cons,$id_sez,'0');

}


function controllo_votir($id_cons,$id_sez){
	global $prefix,$dbi,$id_sede,$id_cons_gen,$genere;
		##############################
	$err=0; $err2=0; $idrg=0;
	$sql="SELECT id_gruppo FROM ".$prefix."_ele_gruppo where id_cons='$id_cons'";
	$resref = $dbi->prepare("$sql");
	$resref->execute();	
	$numscru=$resref->rowCount(); $rifscru=0;
	while(list($idrefgruppo)=$resref->fetch(PDO::FETCH_NUM)) {
		$sql="SELECT si,no,validi,nulli,bianchi,contestati FROM ".$prefix."_ele_voti_ref where id_sez='$id_sez' and id_gruppo='$idrefgruppo'";
		$res2 = $dbi->prepare("$sql");
		$res2->execute();
		$refscru=$res2->rowCount(); 
		$sql="SELECT max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_sez='$id_sez' and id_gruppo='$idrefgruppo'";
		$res3 = $dbi->prepare("$sql");
		$res3->execute();
		list($voti)=$res3->fetch(PDO::FETCH_NUM);
		$rifscru++;
		list($si,$no,$validi,$nulli,$bianchi,$contestati)=$res2->fetch(PDO::FETCH_NUM);
		if(!$validi or (($si+$no==$validi) and ($validi+$nulli+$bianchi+$contestati==$voti))) 
			continue;
		else {$err=1; $idrg=$idrefgruppo; break;}	

	}

#############
		$tipo='referendum';
		$sql="select id from ".$prefix."_ele_controllo where tipo='$tipo' and id_sez='$id_sez'";
		$res = $dbi->prepare("$sql");
		$res->execute();
		$righe=$res->rowCount();
		if($righe){
			if(!$err){
				$sql="delete from ".$prefix."_ele_controllo where tipo='$tipo' and id_sez='$id_sez' ";
				$res = $dbi->prepare("$sql");
				$res->execute();
			}
			$err=0;
		}
		if($err){
			$sql="insert into ".$prefix."_ele_controllo value('$id_cons','$id_sez','$tipo','$idrg')";
			$res = $dbi->prepare("$sql");
			$res->execute();		
		}
#############

}

function controllo_votic($id_cons,$id_sez,$id_lista){
	global $prefix,$dbi,$id_sede,$id_cons_gen,$validi;
		##############################
	$err=0;
	$sql="SELECT preferenze,disgiunto,solo_gruppo,id_conf,id_fascia FROM ".$prefix."_ele_cons_comune where id_cons='$id_cons'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($prefs,$disg,$solog,$legge,$fascia)=$res->fetch(PDO::FETCH_NUM);
	$sql="SELECT limite FROM ".$prefix."_ele_conf where id_conf='$legge'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	if($res->rowCount())
		list($limite)=$res->fetch(PDO::FETCH_NUM);
	else
		$limite=-1;
	if($fascia<=$limite){
		$sql="select id_gruppo from ".$prefix."_ele_lista where id_lista='$id_lista'";
		$res = $dbi->prepare("$sql");
		$res->execute(); 
		list($id_gruppo)=$res->fetch(PDO::FETCH_NUM);
		$sql="select voti from ".$prefix."_ele_voti_gruppo where id_gruppo='$id_gruppo' and id_sez='$id_sez'";		
	} else 	$sql="select voti from ".$prefix."_ele_voti_lista where id_lista='$id_lista' and id_sez='$id_sez'";
	$res = $dbi->prepare("$sql");
	$res->execute(); 
	list($votil)=$res->fetch(PDO::FETCH_NUM);
	$sql="SELECT sum(voti),max(voti) FROM ".$prefix."_ele_voti_candidato where id_sez='$id_sez' and id_cand in (select id_cand from ".$prefix."_ele_candidato where id_lista='$id_lista')";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($votic,$mvc)=$res->fetch(PDO::FETCH_NUM);
	if(($votic)>($votil*$prefs) || $mvc>$votil) 
	{$err=1;} 
	return $err;
}



function controllo_votil($id_cons,$id_sez,$id_lista){
	global $prefix,$dbi,$id_sede,$id_cons_gen,$validi,$votoscollegato;
		##############################
	$sql="select id from ".$prefix."_ele_controllo where tipo='lista' and id_sez='$id_sez' and id>0"; 
	$res = $dbi->prepare("$sql");
	$res->execute();
	while (list($idl)=$res->fetch(PDO::FETCH_NUM)) {
		$err=controllo_votic($id_cons,$id_sez,$idl);
		if(!$err){
			$sql="delete from ".$prefix."_ele_controllo where tipo='lista' and id_sez='$id_sez' and id=$idl"; 
			$res = $dbi->prepare("$sql");
			$res->execute();
		} 
	}		
	$err=0;
	$tipo='lista';
	$sql="SELECT t1.genere,t1.voto_g FROM ".$prefix."_ele_tipo as t1 left join ".$prefix."_ele_consultazione as t2 on t1.tipo_cons=t2.tipo_cons where id_cons_gen='$id_cons_gen'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($genere,$votog)=$res->fetch(PDO::FETCH_NUM);
	$sql="SELECT preferenze,disgiunto,solo_gruppo,id_fascia,id_conf FROM ".$prefix."_ele_cons_comune where id_cons='$id_cons'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($prefs,$disg,$solog,$fascia,$id_conf)=$res->fetch(PDO::FETCH_NUM);
	$sql="SELECT supdisgiunto FROM ".$prefix."_ele_conf where id_conf='$id_conf'"; 
	$res = $dbi->prepare("$sql");
	$res->execute();
	if($res->rowCount()) 
		list($supdis)=$res->fetch(PDO::FETCH_NUM);
	else $supdis=0;
	if($id_lista){
		$err=controllo_votic($id_cons,$id_sez,$id_lista);
		$tipo='lista';
	}else{

		if($genere==4 or $votog)
			$sql="SELECT id_lista, voti, '0' FROM ".$prefix."_ele_voti_lista where id_sez='$id_sez'";
		else
			$sql="SELECT id_gruppo, voti, solo_gruppo FROM ".$prefix."_ele_voti_gruppo where id_sez='$id_sez'";
		$resref = $dbi->prepare("$sql");
		$resref->execute();
		$totlis=0;
		$totgru=0;
		$totsg=0;
		$totsl=0;
		$tnl=0;
		if($genere==4 or $votog){
		    $sql="SELECT validi,contestati,nulli,bianchi,voti_nulli FROM ".$prefix."_ele_sezione where id_cons='$id_cons' and id_sez='$id_sez'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($validil,$contestatil,$nullil,$bianchi,$vnulli) = $res->fetch(PDO::FETCH_NUM);
		    $sql="SELECT max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_cons='$id_cons' and id_sez='$id_sez'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($votit) = $res->fetch(PDO::FETCH_NUM);
			while (list($idg,$votig,$svg)=$resref->fetch(PDO::FETCH_NUM)) {
				$err=controllo_votic($id_cons,$id_sez,$idg);
				if($err){ $tipo='lista'; $id_lista=$idg; break; }
				$sql="SELECT voti, nulli_lista, solo_lista FROM ".$prefix."_ele_voti_lista where id_sez='$id_sez' and id_lista='$idg'";
				$res2 = $dbi->prepare("$sql");
				$res2->execute();
				$totgru+=$votig;
				$totsg+=$svg;
				if($res2->rowCount()){
					list($votil,$nl,$svl)=$res2->fetch(PDO::FETCH_NUM);
					$totlis+=$votil;
					$totsl+=$svl;
					$tnl+=$nl;
					if(($votig+$svl)<($votil+$svg+$nl))
					{ $err=1; $tipo='lista';$id_lista=$idg; break; }
				}				
			} 
			if (($validil+$contestatil+$nullil+$bianchi+$vnulli!=$votit and $validil+$contestatil+$nullil+$bianchi+$vnulli>0) or ($totlis!=$validil and $totlis>0)) {$err=1;$tipo='lista';}
		}else{
			$sql="SELECT validi,validi_lista,contestati_lista,voti_nulli_lista,solo_gruppo,solo_lista FROM ".$prefix."_ele_sezione where id_cons='$id_cons' and id_sez='$id_sez'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($votiv,$validil,$contestatil,$nullil,$solovg,$solol) = $res->fetch(PDO::FETCH_NUM);
			$vl=0;
			$tvl=0;
			$tmpnulli=$nullil+$contestatil;
			if($resref->rowCount()){
				$totg=0;$totl=0;
				while (list($idg,$votig,$svg)=$resref->fetch(PDO::FETCH_NUM)) {
					$sql="SELECT id_lista FROM ".$prefix."_ele_lista where id_gruppo='$idg'";
					$res2 = $dbi->prepare("$sql");
					$res2->execute();
					while(list($idl)=$res2->fetch(PDO::FETCH_NUM)){
						$err=controllo_votic($id_cons,$id_sez,$idl);
						if($err){ $tipo='lista'; break; }
						$sql="SELECT voti FROM ".$prefix."_ele_voti_lista where id_lista='$idl' and id_sez='$id_sez'";
						$res3 = $dbi->prepare("$sql");
						$res3->execute();
						list($vl)=$res3->fetch(PDO::FETCH_NUM);
						$tvl+=$vl;
					}
					if($err) break;
					$sql="SELECT sum(voti), sum(nulli_lista),sum(solo_lista) FROM ".$prefix."_ele_voti_lista where id_sez='$id_sez' and id_lista in (select id_lista from ".$prefix."_ele_lista where id_gruppo='$idg')";
					$res2 = $dbi->prepare("$sql");
					$res2->execute();
					$totgru+=$votig;
					$totsg+=$svg;
					if($res2->rowCount()){
						list($votil,$nl,$svl)=$res2->fetch(PDO::FETCH_NUM);
						$totlis+=$votil;
						$totsl+=$svl;
						$tnl+=$nl;
						if(($votig+$svl)>($votil+$svg+$nl) and !$disg and !$votoscollegato) $tmpnulli-=(($votig+$svl)-($votil+$svg+$nl));
						if(((($votig+$svl)<($votil+$svg+$nl) and !$disg and !$votoscollegato) and (!$supdis and !$disg)) or $tmpnulli<0)
						{ $err=1; $tipo='lista'; break; }
					} 
					$totg+=($votig+$svl);$totl+=($votil+$svg+$nl);
				}
				if((($totg)<($totl+$tnl+$contestatil) and !$disg) or ($totsg!=$solovg and $solog)) 
				{$err=12; $tipo='lista';}
			}else{
			#inserire controllo per consultazioni con voto alle liste ma senza voto di gruppo	
			}
			if (!$totsg) $totsg=$solovg;
			if (($totlis!=$validil or $validil+$contestatil+$nullil+$totsg!=$votiv) and ($validil+$contestatil+$nullil+$solol>0 or $tvl>0)) {$err=13;$tipo='lista';}
			if(($solovg && !$disg && ($tnl!=$nullil && $disg)) || $totsl!=$solol || ($totsg!=$solovg)) {$err=14;$tipo='lista'; }
		}		          
	}
	if(!$err){
		if($id_lista) $andlis=" and id='$id_lista' "; else $andlis="";
		$sql="delete from ".$prefix."_ele_controllo where tipo='lista' and id_sez='$id_sez' $andlis"; 
		$res = $dbi->prepare("$sql");
		$res->execute();
	} 

	if($err){
		$sql="select * from ".$prefix."_ele_controllo where tipo='lista' and id_sez='$id_sez' and id='$id_lista'"; 
		$res = $dbi->prepare("$sql");
		$res->execute();
		if(!$res->rowCount()) {
			if(!$id_lista) $id_lista=0;
			$sql="insert into ".$prefix."_ele_controllo value('$id_cons','$id_sez','$tipo','$id_lista')";
			$res = $dbi->prepare("$sql");
			$res->execute();
		}
	}

}
?>
