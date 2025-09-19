<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Roberto Gigli & Luciano Apolito                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* Modulo previsione seggi                                                       */
/* Amministrazione                                                     */
/************************************************************************/
 if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}
# controllo 
if ($hondt<=0){ Header("Location: index.php");
	die();
}
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['gruppo'])) $gruppo=intval($param['gruppo']); else $gruppo='';
if (isset($param['numgruppo'])) $numgruppo=intval($param['numgruppo']); else $numgruppo='';
if (isset($param['listecol'])) $listecol=intval($param['listecol']); else $listecol=0;
$sql = "select id_conf,id_fascia from ".$prefix."_ele_cons_comune where id_cons='$id_cons'";
$result = $dbi->prepare("$sql");
$result->execute();
list($id_conf,$fascia) = $result->fetch(PDO::FETCH_NUM);
$sql = "SELECT limite,consin,infpremio,supsbarramento,suppremio,listinfsbar,listinfconta,listsupconta,supminpremio,infminpremio,inffisso from ".$prefix."_ele_conf where id_conf='$id_conf'";
$res = $dbi->prepare("$sql");
$res->execute();
list($limite,$consin,$infpremio,$supsbarramento,$suppremio,$listinfsbar,$listinfconta,$listsupconta,$supminpremio,$infminpremio,$inffisso) = $res->fetch(PDO::FETCH_NUM);
$numcons=0;
echo "<table><tr><td align=\"center\">"._PROIEZCONS."</td></tr></table>";


function consiglio(){
global $param,$id_cons_gen, $dbi, $prefix, $id_comune, $gruppo, $numgruppo, $listecol, $numcons,$id_conf,$fascia,$id_cons,$validi,$tema;
global $limite,$consin,$infpremio,$supsbarramento,$suppremio,$listinfsbar,$listinfconta,$listsupconta,$supminpremio,$infminpremio,$inffisso;
$collegate= array();
$collperd= array();
$x=1;
$primoturno=0;
while (isset($param['num_lista'.$x])) {
	if ($param['num_lista'.$x]==$gruppo) array_push($collegate,$_SESSION['num_lista'.$x]);
#	elseif ($param['num_lista'.$x]!=0) array_push($collperd,$_SESSION['num_lista'.$x]);
	$x++;
}
$sql = "SELECT t1.tipo_cons,t2.id_cons,t2.id_fascia,t2.id_conf FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_cons_gen='$id_cons_gen' and t2.id_comune='$id_comune'";
$res = $dbi->prepare("$sql");
$res->execute();

if ($res->rowCount()){
	list($tipo_cons,$id_cons,$fascia,$conf) = $res->fetch(PDO::FETCH_NUM);
	$sql = "select capoluogo from ".$prefix."_ele_comuni where id_comune='$id_comune'";
$result = $dbi->prepare("$sql");
$result->execute();

	list($capoluogo) = $result->fetch(PDO::FETCH_NUM);
	$sql = "select inffisso,fascia_capoluogo from ".$prefix."_ele_conf where id_conf='$conf'";
$result = $dbi->prepare("$sql");
$result->execute();

	list($inffisso,$fascia2) = $result->fetch(PDO::FETCH_NUM);
	if($fascia<$fascia2 and $capoluogo) $fascia=$fascia2;

		$sql = "SELECT seggi from ".$prefix."_ele_fasce where id_fascia='$fascia' and id_conf=$id_conf";
$result = $dbi->prepare("$sql");
$result->execute();

		list($numcons) = $result->fetch(PDO::FETCH_NUM);
$sql = "SELECT id_cand, sum(voti) from ".$prefix."_ele_voti_candidati where id_cons='$id_cons' group by id_cand";
$res_val = $dbi->prepare("$sql");
$res_val->execute();

$num_cons= $res_val->rowCount();
if ($num_cons<$numcons){
	echo "Il numero di candidati al consiglio inseriti con preferenza ($num_cons) e' inferiore al numero di seggi previsti ($numcons). Non e' possibile procedere con il calcolo";
	include("footer.php");
	die();
}
	if (!$gruppo){
		$sql = "SELECT sum(validi) from ".$prefix."_ele_sezioni where id_cons='$id_cons'";
$res_val = $dbi->prepare("$sql");
$res_val->execute();

		list($validi) = $res_val->fetch(PDO::FETCH_NUM);
		$sql = "SELECT t1.num_gruppo,sum(t2.voti) as voti from ".$prefix."_ele_gruppo as t1,  ".$prefix."_ele_voti_gruppo as t2 where t1.id_cons='$id_cons' and t1.id_gruppo=t2.id_gruppo group by t1.num_gruppo order by voti desc limit 0,2";
$res_lis = $dbi->prepare("$sql");
$res_lis->execute();

		$test=0;$flag=0;
		while (list($num_gruppo,$voti)= $res_lis->fetch(PDO::FETCH_NUM)){
			if ($voti>($validi/2)) {$gruppo=$num_gruppo;$primoturno=1;}
			if ($voti==$test) $flag=1; else $test=$voti;
		}
	}
	if ($fascia<=$limite){
		$sql = "SELECT t1.num_gruppo,sum(t2.voti) as voti from ".$prefix."_ele_gruppo as t1,  ".$prefix."_ele_voti_gruppo as t2 where t1.id_cons='$id_cons' and t1.id_gruppo=t2.id_gruppo group by t1.num_gruppo order by voti desc limit 0,3";
		$res_lis = $dbi->prepare("$sql");
		$res_lis->execute();
		if($res_lis->rowCount()==1 or $inffisso)
		{
			list($num_gruppo1,$voti1)= $res_lis->fetch(PDO::FETCH_NUM);
			$num_gruppo2=0; $voti2=0;
			if($res_lis->rowCount()>1)
				list($num_gruppo2,$voti2)= $res_lis->fetch(PDO::FETCH_NUM);
			$sql = "SELECT sum(maschi+femmine) from ".$prefix."_ele_sezioni where id_cons='$id_cons'";
			$res_val = $dbi->prepare("$sql");
			$res_val->execute();
			list($elettori)=$res_val->fetch(PDO::FETCH_NUM);
			$sql = "select sum(voti_complessivi) from ".$prefix."_ele_voti_parziale where id_cons='$id_cons' group by data,orario order by data desc, orario desc limit 0,1";
			$res_val = $dbi->prepare("$sql");
			$res_val->execute();
			list($votanti)=$res_val->fetch(PDO::FETCH_NUM);
			if($votanti<($elettori/2) || $voti1<($votanti/2))
			{
				echo "<div style=\"text-align:center;\"><br><br>Il numero di votanti è inferiore al 50%, non è possibile assegnare i seggi. <br>La consultazione è nulla</div>";
				include("footer.php");
				die();
			} 
		}else{
			$row = $res_lis->fetchAll();
			$num_gruppo1=$row[0]['num_gruppo']; $voti1=$row[0]['voti'];
			$num_gruppo2=$row[1]['num_gruppo']; $voti2=$row[1]['voti'];
		}
		if ($voti1>$voti2)
			$numgruppo=$num_gruppo1;
		else
			$numgruppo=$num_gruppo2;
	}
	if ($fascia<=$limite and $numgruppo) {
		if($inffisso){
			if(isset($row[2]['num_gruppo'])) 
			{
				$num_gruppo3=$row[2]['num_gruppo']; 
				$voti3=$row[2]['voti'];
				if($voti2==$voti3) 
				{
					$sql = "SELECT t2.num_gruppo,sum(t3.voti) from ".$prefix."_ele_lista as t2, ".$prefix."_ele_voti_lista as t3 where t2.id_cons='$id_cons' and t2.id_lista=t3.id_lista and (t2.num_gruppo=$num_gruppo2 or t2.num_gruppo=$num_gruppo3) group by t2.num_gruppo order by voti desc";
					$res_lis = $dbi->prepare("$sql");
					$res_lis->execute();
					$row = $res_lis->fetchAll();
					if($row[0]['voti']>$row[1]['voti'])
						{$num_gruppo2=$row[0]['num_gruppo']; $voti2=$row[0]['voti'];}
					else{
						echo "Parità di voti a sindaco e liste collegate - Inserire controllo ";
						include("footer.php");
						die();
					}
				}
			}
			if($voti2<$validi/5) $num_gruppo2=0; # il candidato sindaco è eletto consigliere solo se ottiene il 20%
			consmin4($fascia,$numgruppo,$voti1,$num_gruppo2,$voti2); #die("TEST");
		}else
			consmin($fascia,$numgruppo);
	}
	elseif ($gruppo>0) conssup($fascia,$gruppo,$collegate,$collperd,$primoturno);
	elseif ($numgruppo>0){
		$sqllis = "SELECT t1.id_lista,t1.num_lista,t1.descrizione,t1.id_gruppo from ".$prefix."_ele_lista as t1, ".$prefix."_ele_gruppo as t2 where t1.id_cons='$id_cons' and t1.id_gruppo=t2.id_gruppo and t2.num_gruppo not in (".$_SESSION['ballo1'].",".$_SESSION['ballo2'].")";
$res_lis = $dbi->prepare("$sqllis");
$res_lis->execute();

		$yy=$res_lis->rowCount(); 
			$sql = "select sum(voti) from ".$prefix."_ele_voti_lista where id_cons='$id_cons'";
$res_voti = $dbi->prepare("$sql");
$res_voti->execute();

			list($validilista) = $res_voti->fetch(PDO::FETCH_NUM);
		if ($yy){
while(list($id_lista,$num_lista,$descr,$pgrup) = $res_lis->fetch(PDO::FETCH_NUM)) {
			$sql = "select sum(voti) from ".$prefix."_ele_voti_lista where id_lista='$id_lista'";
$res_voti = $dbi->prepare("$sql");
$res_voti->execute();

			list($votilista) = $res_voti->fetch(PDO::FETCH_NUM);
			if(!isset($voti[$pgrup])) $voti[$pgrup]=0;
			$voti[$pgrup]+=$votilista;
}
			foreach ($voti as $key=>$val){if($val<($validilista*3/100)) unset($voti[$key]);} ##################################################
			$res_lis = $dbi->prepare("$sqllis");
			$res_lis->execute();
			echo "<br>";
			echo "<form id=\"gruppo\" action=\"modules.php\">";
			echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\"><tr class=\"bggray\"><td colspan=\"4\">"._COLLEGAMENTI."</td></tr><tr class=\"bggray\"><td>";
			if($tema=='bootstrap')
				echo "<input type=\"hidden\" name=\"op\" value=\"31\"/>";
			else
				echo "<input type=\"hidden\" name=\"op\" value=\"consiglieri\"/>";
			echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\"/>";
			echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"/></td>";

echo "<td><b>".$_SESSION['grp1']."</b></td>";
			echo "<td><b>".$_SESSION['grp2']."</b></td>";
			echo "<td><b>"._NONCOLLE."</b></td></tr>";
			
			$z=1;
			while(list($id_lista,$num_lista,$descr,$pgrup) = $res_lis->fetch(PDO::FETCH_NUM)) {
				if(!isset($voti[$pgrup])) continue;
 				$x=$_SESSION['ballo1'];
				echo "<tr><td>$descr</td><td><input type=\"radio\" name=\"num_lista$z\" value=\"$x\"/></td>";
				$x=$_SESSION['ballo2'];
				$_SESSION['num_lista'.$z]=$num_lista;
				echo "<td><input type=\"radio\" name=\"num_lista$z\" value=\"$x\"/></td>";
				echo "<td><input type=\"radio\" name=\"num_lista$z\" value=\"0\" checked=\"checked\"/></td></tr>";
				$z++;		
			}

			echo "<tr><td colspan=\"4\"><input type=\"hidden\" name=\"listecol\" value=\"$x\"/><input type=\"hidden\" name=\"gruppo\" value=\"$numgruppo\"/>";
			echo "<input type=\"submit\" name=\"invia\" value=\""._OK."\"/></td></tr></table></form>";
		}else conssup($fascia,$numgruppo,$collegate,$collperd,$primoturno);
	}else {
		echo "<br>";
		echo "<form id=\"numgruppo\" action=\"modules.php\">";
		echo "<table><tr><td>"._SCELTASIN.":</td><td align=\"left\">";
		if($tema=='bootstrap')
			echo "<input type=\"hidden\" name=\"op\" value=\"31\"/>";
		else
			echo "<input type=\"hidden\" name=\"op\" value=\"consiglieri\"/>";
		echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\"/>";
		echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"/>";
		$sql = "SELECT t1.id_gruppo,t1.num_gruppo,t1.descrizione, sum(t2.voti) as pref FROM ".$prefix."_ele_gruppo as t1, ".$prefix."_ele_voti_gruppo as t2 where t1.id_gruppo=t2.id_gruppo and t1.id_cons='$id_cons' group by t1.id_gruppo,t1.num_gruppo,t1.descrizione order by pref desc limit 0,2";
		$res = $dbi->prepare("$sql");
		$res->execute();

		while(list($id_gruppo,$num_gruppo, $descr_gruppo,$pref) = $res->fetch(PDO::FETCH_NUM)) {
			if (!isset($_SESSION['ballo1'])) {
				$_SESSION['ballo1']=$num_gruppo;
				$_SESSION['grp1']=$descr_gruppo;
				$_SESSION['idgrp1']=$id_gruppo;
			}else{
				$_SESSION['ballo2']=$num_gruppo;
				$_SESSION['grp2']=$descr_gruppo;
				$_SESSION['idgrp2']=$id_gruppo;
			}
			echo "<input type=\"radio\" name=\"numgruppo\" value=\"$num_gruppo\"/>$descr_gruppo<br>";
		}
		echo "</td>";
		echo "<td><input type=\"submit\" name=\"invia\" value=\""._OK."\"/></td></tr></table></form>";
	
		}
	}
}

function consmin4($fascia,$grp,$votisindaco,$gruppo2,$voticsec) 
{
	#il secondo più votato ha diritto al seggio se ha avuto più del 20% dei voti
	#se altra lista ottiene il 50% + 1 dei voti validi le vengono assegnati i 2/3 dei seggi
	#il seggio spettante al candidato sindaco secondo eletto viene sempre dedotto da quelli assegnati alla minoranza
	#se più liste non collegate al sindaco ottengono lo stesso più alto numero di voti si va a sorteggio per l'ultimo seggio
	#se per l'ultimo seggio due liste hanno stesso quoziente il seggio va alla lista che ha ottenuto più voti
	#se due candidati della stessa lista hanno stesso numero di voti ha la precedenza chi ha numero di lista inferiore
	#!!!! considerare il caso di due candidati sindaco non eletti, e liste a loro collegate, che abbiano stessi voti

	global $id_cons, $prefix,$dbi,$num_candlst,$PNE,$CSEC,$validi,$consin,$numcons,$inffisso,$quozienti;
	global $infpremio,$fisso,$sincons, $votol,$listinfsbar,$stampasind;
	if (!isset($fisso)) $fisso=0; #se fisso=1 il premio di maggioranza è fisso
	if (isset($votol)) {$votolista=$votol; $fisso=$votol;} #se votolista=1 c'e' voto di lista
	$premiomaggioranza=ceil($numcons*2/3);
	$sql = "SELECT sum(validi_lista) from ".$prefix."_ele_sezioni where id_cons='$id_cons'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($valista)=$res->fetch(PDO::FETCH_NUM);
	$sogliagruppo=$valista/5; #il candidato sindaco può essere eletto consigliere se supera il 20% dei voti validi
	$soglialista=$valista/(100/$listinfsbar);
	if($voticsec>=$sogliagruppo) $sincons=1; else $sincons=0; #se sincons=1 il sindaco eletto occupa un posto di consigliere
	#eif($votisindaco>=
	$sql = "SELECT t2.num_gruppo,sum(t3.voti) as votisum from ".$prefix."_ele_lista as t2, ".$prefix."_ele_voti_lista as t3 where t2.id_cons='$id_cons' and t2.id_lista=t3.id_lista group by t2.num_gruppo order by votisum desc";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$totvotilista=0;
	$maxvotilista=0;
	$gruppomaxvoti=$gruppo2;
	while(list($num_gruppo,$voti)= $res->fetch(PDO::FETCH_NUM))
	{
		if($voti<$soglialista) continue;
		$totvotilista+=$voti;
		if($num_gruppo==$grp)
			$votilistavin=$voti;
		elseif($voti>$maxvotilista)
		{
			$maxvotilista=$voti;
			$gruppomaxvoti=$num_gruppo;
		}
	}
	if($votilistavin>=($valista*0.4)) $infpremio=1; else $infpremio=0; #vanno considerati tutti i voti validi o solo quelli delle liste ammesse?
	$premiomin=0;
	if($maxvotilista>($valista*0.5)) $premiomin=1;	
	if(!$infpremio) 
	{
		$argruppi[$grp]=$votilistavin; 
		$argruppi[$gruppomaxvoti]=$maxvotilista; 
		$seggi=seggipergruppo($argruppi,$numcons,$totvotilista);
		$seggi[$gruppomaxvoti]-=$sincons;
	}else{
		if($premiomin==1)
		{
			$seggi[$gruppomaxvoti]=$premiomaggioranza;
			$seggi[$grp]=($numcons-$premiomaggioranza)-$sincons;			
		}else{
			$seggi[$grp]=$premiomaggioranza;
			$seggi[$gruppomaxvoti]=($numcons-$premiomaggioranza)-$sincons;			
		}
	}

	$consel[]=array(_LISTA,_VOTI,_SEGGI,_CANDIDATO,_CIFRAELE,_QUOZIENTI);	
	$PNE=_PRIMONON;
	$CSEC=_SINDCONS;
	$sorteggio=0;
	$sindel=0;
	$csmin[]=$gruppo2;
	$num_candlst=array();
	$sql = "SELECT num_lista,count(num_cand) from ".$prefix."_ele_candidati where id_cons=$id_cons GROUP BY num_lista order by num_lista";
	$res_can = $dbi->prepare("$sql");
	$res_can->execute();
	while(list($x,$num)=$res_can->fetch(PDO::FETCH_NUM))
		$num_candlst[$x]=$num;

	$listagruppo=array();
	#funzione di calcolo per comuni fino a 15.000 abitanti (più esattamente fino al valore di $limite)
	$grpcond='';
	if($grp) $grpcond="and t1.num_gruppo='$grp'";
	if ($sincons) $numcons--;
	#carica numero di liste e voti, i voti sono quelli del gruppo se non c'e' voto di lista
	$seggimag=array();
	#se due candidati a sindaco hanno lo stesso numero di preferenze si va al ballottaggio

	$sql = "SELECT t1.descrizione,t1.num_gruppo from ".$prefix."_ele_gruppo as t1 where t1.id_cons='$id_cons' and (t1.num_gruppo=$grp or t1.num_gruppo=$gruppo2)";
	$res_per = $dbi->prepare("$sql");
	$res_per->execute();
	while(list($descr,$num)=$res_per->fetch(PDO::FETCH_NUM))
		$desgruppi[$num]=$descr;
	$votig=0;
	$conselcsne=array();
	$arlismag=array();
	$arlismin=array();
	$lisvin=0;
	$gruvin=0;
	$grumin=0;
	$votigrumin=0;
	$i=0;
    $precvoti=0;
	$sql = "SELECT t2.id_lista,t2.num_lista,t2.descrizione,sum(t3.voti) from ".$prefix."_ele_lista as t2, ".$prefix."_ele_voti_lista as t3 where t2.id_cons='$id_cons' and t2.id_lista=t3.id_lista and t2.num_gruppo=$grp group by t2.id_lista,t2.num_lista,t2.descrizione order by voti desc";
	$res_lis = $dbi->prepare("$sql");
	$res_lis->execute();
	while(list($il,$nl,$dl,$vl)=$res_lis->fetch(PDO::FETCH_NUM))
	{
		$arlismag[$nl]=$vl;
		$lists[$nl]=$vl;
		$idlst[$nl]=$il;
		$desliste[$nl]=$dl;
	}
	$sql = "SELECT t2.id_lista,t2.num_lista,t2.descrizione,sum(t3.voti) from ".$prefix."_ele_lista as t2, ".$prefix."_ele_voti_lista as t3 where t2.id_cons='$id_cons' and t2.id_lista=t3.id_lista and t2.num_gruppo=$gruppomaxvoti group by t2.id_lista,t2.num_lista,t2.descrizione order by voti desc";
	$res_lis = $dbi->prepare("$sql");
	$res_lis->execute();
	while(list($il,$nl,$dl,$vl)=$res_lis->fetch(PDO::FETCH_NUM))
	{
		$arlismin[$nl]=$vl;
		$lists[$nl]=$vl;
		$idlst[$nl]=$il;
		$desliste[$nl]=$dl;
	}
	$votillrif=0;
	$seggimag=calcoloseggi($arlismag,$seggi[$grp],1);
	$quozientimag=$quozienti;
	$seggimin=calcoloseggi($arlismin,$seggi[$gruppomaxvoti],1);
	$quozientimin=$quozienti;
	$tempng=array();
	foreach($seggimag as $lst=>$val){
		$id_lista=$idlst[$lst];
		$x=$lst;
		$y=$lists[$x];
		$pos=0;
		$z=0;
		$arvin[$x][$pos++]=$desliste[$lst]; 
		$sql = "SELECT concat(substring(concat('0',t1.num_cand),-2),') ',t1.cognome,' ',substring(t1.nome from 1 for 1),'.') as descr,sum(t2.voti) as voti from ".$prefix."_ele_candidati as t1, ".$prefix."_ele_voti_candidati as t2 where t1.id_lista='$id_lista' and t1.id_cand=t2.id_cand GROUP BY t1.num_cand,t1.cognome,t1.nome order by voti desc,t1.num_cand";
		$res_can = $dbi->prepare("$sql");
		$res_can->execute();
		$num_candlst[$x]=$res_can->rowCount();
		while(list($cand,$pre)=$res_can->fetch(PDO::FETCH_NUM)){ 
			$cifra[$x][$pos]=$y+$pre;
			$arvin[$x][$pos++]=$cand;
		}	
		$listemag[$x]=$y;
		$percliste[$x]="<br>$y (".number_format($y*100/$validi,2)."%)";
		$z++;
		$x=0;
	}	
	foreach ($seggimag as $key2=>$val2){ 
		if($val2==0) continue;
		if(isset($grpinc[$key2])) { $tempng=$grpinc[$key2];}
		if(isset($listdec[$tempng]))
		foreach($listdec[$tempng] as $ark=>$arv) {
			if(!isset($ultquoz[($sindseggio[$tempng])]) and $sindseggio[$tempng]) {$ultquoz[($sindseggio[$tempng])]=$quozientimag[$arv][($val2-1)];$lastlist[$tempng]=$arv;}
			elseif ($ultquoz[($sindseggio[$tempng])]>$quozientimag[$arv][($val2-1)]) 
			{
				$ultquoz[($sindseggio[$tempng])]=$quozientimag[$arv][($val2-1)];$lastlist[($sindseggio[$tempng])]=$arv;
			}
		}
	} 
	foreach ($seggimag as $key2=>$val2){ 
		for ($z=0;$z<$val2;$z++){ 
			if ($z) $consel[]=array("","","",$arvin[$key2][($z+1)],$cifra[$key2][($z+1)],number_format($quozientimag[$key2][$z],2));
			else
			{
				$consel[]=array($arvin[$key2][0],$percliste[$key2],$val2,$arvin[$key2][($z+1)],$cifra[$key2][($z+1)],number_format($quozientimag[$key2][$z],2));
				$arlisdesv[]=$arvin[$key2][0];$arlissegv[]=$val2;$arlisnumv[]=$key2;
			}
		}
		$x++;
		if($val2)
		$consel[]=array($arvin[$key2][0],"$PNE","",$arvin[$key2][($z+1)],$cifra[$key2][($z+1)],number_format($quozientimag[$key2][$z],2));
	}
# fine seggi sindaco
	if($sincons) {
					$consel[]=array("$CSEC",$desgruppi[$gruppo2]);
	}
	unset($arvin);		
	foreach($seggimin as $lst=>$val){
		$id_lista=$idlst[$lst];
		$x=$lst;
		$y=$lists[$x];
		$pos=0;
		$z=0;
		$arvin[$x][$pos++]=$desliste[$lst]; 
		$sql = "SELECT concat(substring(concat('0',t1.num_cand),-2),') ',t1.cognome,' ',substring(t1.nome from 1 for 1),'.') as descr,sum(t2.voti) as voti from ".$prefix."_ele_candidati as t1, ".$prefix."_ele_voti_candidati as t2 where t1.id_lista='$id_lista' and t1.id_cand=t2.id_cand GROUP BY t1.num_cand,t1.cognome,t1.nome order by voti desc,t1.num_cand";
		$res_can = $dbi->prepare("$sql");
		$res_can->execute();
		$num_candlst[$x]=$res_can->rowCount();
		while(list($cand,$pre)=$res_can->fetch(PDO::FETCH_NUM)){ 
			$cifra[$x][$pos]=$y+$pre;
			$arvin[$x][$pos++]=$cand;
		}	
		$listemag[$x]=$y;
		$percliste[$x]="<br>$y (".number_format($y*100/$validi,2)."%)";
		$z++;
		$x=0;
	}	
	foreach ($seggimin as $key2=>$val2){ 
		if($val2==0) continue;
		if(isset($grpinc[$key2])) { $tempng=$grpinc[$key2];}
		if(isset($listdec[$tempng]))
		foreach($listdec[$tempng] as $ark=>$arv) {
			if(!isset($ultquoz[($sindseggio[$tempng])]) and $sindseggio[$tempng]) {$ultquoz[($sindseggio[$tempng])]=$quozientimin[$arv][($val2-1)];$lastlist[$tempng]=$arv;}
			elseif ($ultquoz[($sindseggio[$tempng])]>$quozientimin[$arv][($val2-1)]) 
			{
				$ultquoz[($sindseggio[$tempng])]=$quozientimin[$arv][($val2-1)];$lastlist[($sindseggio[$tempng])]=$arv;
			}
		}
	} 
	foreach ($seggimin as $key2=>$val2){ 
		for ($z=0;$z<$val2;$z++){ 
			if ($z) $consel[]=array("","","",$arvin[$key2][($z+1)],$cifra[$key2][($z+1)],number_format($quozientimin[$key2][$z],2));
			else
			{
				$consel[]=array($arvin[$key2][0],
				$percliste[$key2],
				$val2,$arvin[$key2][($z+1)],
				$cifra[$key2][($z+1)],
				number_format($quozientimin[$key2][$z],2));
				$arlisdesv[]=$arvin[$key2][0];$arlissegv[]=$val2;$arlisnumv[]=$key2;
			}
		}
		$x++;
		if($val2)
		$consel[]=array($arvin[$key2][0],"$PNE","",$arvin[$key2][($z+1)],$cifra[$key2][($z+1)],number_format($quozientimin[$key2][$z],2));
	}
	$stampasind= "<table summary=\"Tabella dei consiglieri eletti\" class=\"table-docs\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" rules=\"all\">";
	$stampasind.= "<tr class=\"bggray\"><td scope=\"row\"><b>";
	$stampasind.= _SINDACO.": ".$desgruppi[$grp]."</b></td></tr></table>";
	stampalista($consel);
	unset($_SESSION['ballo1']);unset($_SESSION['ballo2']);unset($_SESSION['grp1']);unset($_SESSION['grp2']);
}

#####################

function consmin($fascia,$grp) {
global $id_cons, $prefix,$dbi,$num_candlst,$quozienti,$PNE,$CSEC,$consin,$numcons,$inffisso,$votolista;
global $infpremio,$fisso,$sincons, $votol,$stampasind;
if (!isset($fisso)) $fisso=0; #se fisso=1 il premio di maggioranza è fisso
if (isset($votol)) {$votolista=$votol; $fisso=$votol;} #se votolista=1 c'e' voto di lista
if (!isset($sincons)) $sincons=0; #se sincons=1 il sindaco eletto occupa un posto di consigliere
$sql="SELECT t1.num_gruppo,sum(t2.voti) as voti from ".$prefix."_ele_gruppo as t1,  ".$prefix."_ele_voti_gruppo as t2 where t1.id_cons='$id_cons' and t1.id_gruppo=t2.id_gruppo group by t1.num_gruppo order by voti desc limit 0,2";
$res = $dbi->prepare("$sql");
$res->execute();
$numgruppi=$res->rowCount();
$PNE=_PRIMONON;
$CSEC=_SINDCONS;
$sorteggio=0;
$num_candlst=array();
#funzione di calcolo per comuni fino a 15.000 abitanti (più esattamente fino al valore di $limite)

if ($sincons) $numcons--;
#$numcons--;
$consel=array();
$conselcsne=array();
$conselmin=array();
//$consel[]=array("Lista","Voti","Seggi","Nominativo","Cifra Elettorale","Quoziente");
$consel[]=array(_LISTA,_VOTI,_SEGGI,_CANDIDATO,_CIFRAELE,_QUOZIENTI);
#carica numero di liste e voti, i voti sono quelli del gruppo se non c'e' voto di lista
if($inffisso=='1')
	$sql = "SELECT sum(validi+contestati) from ".$prefix."_ele_sezioni where id_cons='$id_cons'";
elseif($votolista=='0')
	$sql = "SELECT sum(validi) from ".$prefix."_ele_sezioni where id_cons='$id_cons'";
else
	$sql = "SELECT sum(voti) from ".$prefix."_ele_voti_lista where id_cons='$id_cons'";
$res_val = $dbi->prepare("$sql");
$res_val->execute();
list($validi) = $res_val->fetch(PDO::FETCH_NUM);
#se votolista==1, è abilitato il voto di lista ed è quello su cui si calcola l'assegnazione dei seggi
if ($fisso==1){
	#seleziona il sindaco (gruppo con più voti) e lista collegata
	$sql = "SELECT t1.descrizione,t1.num_gruppo,t2.id_lista,t2.num_lista,t2.descrizione,sum(t3.voti) as voti from ".$prefix."_ele_gruppo as t1, ".$prefix."_ele_lista as t2, ".$prefix."_ele_voti_gruppo as t3 where t1.id_cons='$id_cons' and t1.id_gruppo=t2.id_gruppo and t1.id_gruppo=t3.id_gruppo group by t1.descrizione, t1.num_gruppo,t2.id_lista,t2.num_lista,t2.descrizione order by voti desc limit 0,1";
	$res_per = $dbi->prepare("$sql");
	$res_per->execute();
	list($descr,$num_gruppo,$id_lista1,$num_lista,$descr_lista,$voti)= $res_per->fetch(PDO::FETCH_NUM);
	#seleziona la lista di minoranza con più voti
	$sql = "SELECT t2.id_lista, sum(t2.voti) as voti from ".$prefix."_ele_voti_lista as t2 where t2.id_cons='$id_cons' and t2.id_lista!='$id_lista1' group by t2.id_lista order by voti desc limit 0,1";
	$res_per = $dbi->prepare("$sql");
	$res_per->execute();
	list($id_lista2,$voti)= $res_per->fetch(PDO::FETCH_NUM);
      #e la lista di minoranza
	$ordine= $id_lista1>$id_lista2 ? "desc":"";
	$sql = "SELECT t1.descrizione,t1.num_gruppo,t2.id_lista,t2.num_lista,t2.descrizione,sum(t3.voti) as voti from ".$prefix."_ele_gruppo as t1, ".$prefix."_ele_lista as t2, ".$prefix."_ele_voti_lista as t3 where (t2.id_lista='$id_lista1' or t2.id_lista='$id_lista2') and t1.id_gruppo=t2.id_gruppo and t2.id_lista=t3.id_lista group by t1.descrizione,t1.num_gruppo,t2.id_lista,t2.num_lista,t2.descrizione order by t2.id_lista $ordine";
	$res_per = $dbi->prepare("$sql");
	$res_per->execute();
}else{
	$sql = "SELECT t1.descrizione,t1.num_gruppo,t2.id_lista,t2.num_lista,t2.descrizione,sum(t3.voti) as voti from ".$prefix."_ele_gruppo as t1, ".$prefix."_ele_lista as t2, ".$prefix."_ele_voti_gruppo as t3 where t1.id_cons='$id_cons' and t1.id_gruppo=t2.id_gruppo and t1.id_gruppo=t3.id_gruppo group by t1.descrizione,t1.num_gruppo,t2.id_lista,t2.num_lista,t2.descrizione order by voti desc";
	$res_per = $dbi->prepare("$sql");
	$res_per->execute();
}  
$groups=array();
$seggimag=array();
$premio=0;
$x=0;
#carica l'array dei gruppi e della cifra di gruppo
while (list($descr,$num_gruppo,$id_lista,$num_lista,$descr_lista,$voti)= $res_per->fetch(PDO::FETCH_NUM)){
    $desgruppi[$num_gruppo]=$descr;
    $desliste[$num_lista]=$num_lista.") ".$descr_lista;
    $idlst[$num_lista]=$id_lista;
    $listagruppo[$num_lista]=$num_gruppo;
    $lists[$num_lista]=$voti;
    if ($grp){
		if ($grp!=$num_gruppo) {$groups[($num_gruppo)]=$voti;$listemin[$num_lista]=$voti;}
		else {$gruppo[($num_gruppo)]=$voti;$listemag[$num_lista]=$voti;$lisvin=$num_lista;}
    }else{
    	if ($x) {$groups[($num_gruppo)]=$voti;$listemin[$num_lista]=$voti;}
    	else {$gruppo[($num_gruppo)]=$voti;$listemag[$num_lista]=$voti;$lisvin=$num_lista;}
    }
    $x++;
    }#controllo del premio di maggioranza
	//    if ($gruppo[$listagruppo[$lisvin]]>($validi*2/3))
	if($numgruppi==1) $fisso=1;
	if ($gruppo[$listagruppo[$lisvin]]>($validi*$infpremio/100) and $fisso==1) 
	{
		$seggimag[$lisvin]=number_format($numcons*($gruppo[$listagruppo[$lisvin]]*100/$validi)/100);
		##echo "<br> seggimag:".$seggimag[$lisvin];
		$num_cons=number_format($numcons-$seggimag[$lisvin]);
		#    	$num_cons=$numcons;
	} else {   
//    	$seggimag[$lisvin]=number_format($numcons*2/3);
//    	$num_cons=number_format($numcons/3);
		$seggimag[$lisvin]=number_format($numcons*$infpremio/100);
		$num_cons=number_format($numcons-$seggimag[$lisvin]);
	}
    foreach ($listagruppo as $lista=>$val){
		$id_lista=$idlst[$lista];
		$sql = "SELECT concat(substring(concat('0',t1.num_cand),-2),') ',t1.cognome,' ',substring(t1.nome from 1 for 1),'.') as descr,sum(t2.voti) as voti from ".$prefix."_ele_candidati as t1, ".$prefix."_ele_voti_candidati as t2 where t1.id_lista='$id_lista' and t1.id_cand=t2.id_cand GROUP BY descr order by voti desc,descr";
		$res_can = $dbi->prepare("$sql");
		$res_can->execute();
		$num_candlst[$lista]=$res_can->rowCount();
		$pos=0;
		while(list($cand,$pre)=$res_can->fetch(PDO::FETCH_NUM)){
			if(!isset($lists[$lista])) $lists[$lista]=0;
			$cifra[$lista][$pos]=$lists[$lista]+$pre;
			$arvin[$lista][$pos++]=$cand;
		}	    
    }
    if ($num_candlst[$lisvin]<$seggimag[$lisvin]) {
    	$num_cons+=$seggimag[$lisvin]-$num_candlst[$lisvin];
    	$seggimag[$lisvin]=$num_candlst[$lisvin];
    }
    if (isset($gruppo[$listagruppo[$lisvin]])) $seggimag=calcoloseggi($listemag,$seggimag[$lisvin],1);
   if(isset($mex)) 
    echo "$mex";
	foreach ($seggimag as $lista=>$val)
      	for ($z=0;$z<$val;$z++){
				if ($z) $consel[]=array("","","",$arvin[$lista][($z)],$cifra[$lista][($z)],number_format($quozienti[$lista][$z],2));
				else $consel[]=array($desliste[$lista],$lists[$lista],$val,$arvin[$lista][($z)],$cifra[$lista][($z)],number_format($quozienti[$lista][$z],2));
    		}
    		if($arvin[$lista][($z)]) $consel[]=array($desliste[$lista],"$PNE","",$arvin[$lista][($z)],$cifra[$lista][($z)],number_format($quozienti[$lista][$z],2));
    $seggimin=array();
	$assegnato=0;
    $seggimin=calcoloseggi($listemin,$num_cons,1);
    foreach ($seggimin as $lista=>$val){
    	if ($consin and $val>0 and (!$assegnato or !$inffisso)){ 
    		$conselcsne[]=array("$CSEC","","",$desgruppi[$listagruppo[$lista]],"","");
    		$val--;
			$assegnato=1;
    	}
      for ($z=0;$z<$val;$z++){
        	if ($z) $conselmin[]=array("","","",$arvin[$lista][($z)],$cifra[$lista][($z)],number_format($quozienti[$lista][$z],2));
        	else $conselmin[]=array($desliste[$lista],$lists[$lista],$val,$arvin[$lista][($z)],$cifra[$lista][($z)],number_format($quozienti[$lista][$z],2));
		}
		if($arvin[$lista][($z)]) $conselmin[]=array($desliste[$lista],"$PNE","",$arvin[$lista][($z)],$cifra[$lista][($z)],number_format($quozienti[$lista][$z],2));
    }
	foreach($conselcsne as $key=>$val) 
	{
		$consel[]=array($val[0],$val[3]);
	}
	foreach($conselmin as $key=>$val) 
	{
		$consel[]=array($val[0],$val[1],$val[2],$val[3],$val[4],$val[5]);
	}
        
    
    
    $stampasind= "<table summary=\"Tabella dei consiglieri eletti\" class=\"table-docs\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" rules=\"all\">";
    $stampasind.= "<tr class=\"bggray\"><td scope=\"row\">";
    $stampasind.= _SINDACO.": ".$desgruppi[$listagruppo[$lisvin]]."</td></tr></table>";
	stampalista($consel);


}

function seggipergruppo($gruppi,$num_cons,$votitot){
global $ultimo,$mex,$sorteggio,$quozienti,$num_cand,$num_candlst,$suppremio;
foreach($gruppi as $gruppo=>$voti)
{
	$eletti[$gruppo]=$num_cons*($voti/$votitot);
}
return($eletti);
}

function calcoloseggi($gruppi,$num_cons,$flag){
global $ultimo,$mex,$sorteggio,$quozienti,$num_cand,$num_candlst;

	#carica le preferenze
	$pref = array();
	$ultimo=0;
	$mex='';
	$sorteggio=0;
	$eletti = array();
	$ele = array();
	$quozienti = array();
	$num_quoz= $num_cons;
	#inizializza l'array degli eletti ($x=numero lista - $val=numero voti)
	foreach ($gruppi as $x=>$val){
		$eletti[$x]=0;
	}
	#carica gli array dei quozienti
	foreach($gruppi as $y=>$tmp){
		if($flag and isset($num_candlst[$y])) $num_quoz= $num_cons<$num_candlst[$y] ? $num_cons:$num_candlst[$y];
		if(!isset($ele[$y][0])) $ele[$y][0]=0;
		for ($x=0;$x<=$num_quoz;$x++){
			$ele[$y][$x]= $tmp/($x+1);
			$quozienti[$y][$x]= $tmp/($x+1); ###echo "<br>[$y][$x]=".$tmp/($x+1);
		}
	}
	#estrae i quozienti piu' alti
	for ($y=0;$y<$num_cons;$y++){
		$temp=0;
		$cand=0;
		if(! isset($pref['0'])) $pref['0']='';
		if(! isset($pref['1'])) $pref['1']='';
		foreach($gruppi as $x=>$tmp){
			if(!isset($ele[$x][0])) $ele[$x][0]=0;
			if(!isset($pref[$x])) $pref[$x]=0;
			if ($ele[$x][0]==$temp and $pref[$x]==$pref[$cand] and ($y+1)==$num_cons) {$sorteggio=1; $mex="Per attribuire l'ultimo seggio è necessario un sorteggio tra la lista n. ".($x+1)." e la lista n. ".($cand+1);}
			if ($ele[$x][0]>$temp or ($ele[$x][0]==$temp and $pref[$x]>$pref[$cand])) {
				$temp=$ele[$x][0];
				$cand=$x;
				$sorteggio=0;$mex='';
			}
		}
		if (!$sorteggio and $cand){
			$eletti[$cand]++;
			$ultimo=$cand;
			array_shift($ele[$cand]);
		}
	}
	return ($eletti);
}

function stampalista($ar) {
global $PNE,$CSEC,$stampa,$stampasind,$tema;
$cmin=_SEGGIMIN;	
$csin="";	
	$bg='bgw';
	
	$tmpbg='bggray2'; 
	$tmpbg1='bgw';
	$tmpbg2='bggray';
	$tmpbg3='bggray2';
	$fmin=2;
   ob_start();
	echo "<table width=\"100%\" summary=\"Tabella dei consiglieri di maggioranza\" class=\"table-docs\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" rules=\"all\">";
#	echo "<caption style=\"caption-side: top;\"><br>Tabella dei consiglieri di maggioranza</caption>";
		$y=1;$i='';$e=0;
		foreach ($ar as $riga) {
			$e++;
			if($riga[0]==$CSEC and $fmin==2)
			{
				$fmin=1;		
				echo "</table>";
#   echo "</div><div class=\"row\">";
				echo "<table summary=\"Tabella dei candidati a sindaco eletti consigliere\" class=\"table-docs\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" rules=\"all\">";
#				echo "<caption style=\"caption-side: top;page-break-before: always;\"><br>Tabella dei candidati alla carica di sindaco eletti consigliere</caption>";
				echo "<tr class=\"bggray\"><td colspan=\"2\"><b>";
				echo $csin;
				echo "</b></td></tr>";	
				echo "<tr class=\"bggray\"><td><b>"._CANDIDATO."</b></td><td><b>"._NOMINATIVO."</b></td></tr>";
			}
			if($riga[0]!=$CSEC and $fmin==1)
			{	$fmin=0;		
				echo "</table>";
#   echo "</div><div class=\"row\">";
				echo "<table summary=\"Tabella dei consiglieri di minoranza\" class=\"table-docs\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" rules=\"all\">";
#	echo "<caption style=\"caption-side: top;\"><br>Tabella dei consiglieri di minoranza</caption>";
#				echo "<tr class=\"bggray\"><td colspan=\"6\"><b>";
#				echo $cmin;
#				echo "</b></td></tr>";	
				echo "<tr class=\"bggray\"><td><b>"._LISTA."</b></td><td><b>"._VOTI."</b></td><td><b>"._SEGGI."</b></td><td><b>"._NOMINATIVO."</b></td><td><b>"._CIFRAELE."</b></td><td><b>"._QUOZIENTI."</b></td></tr>";
			}
			if($riga[1]==$PNE) echo "<tr class=\"red\">";
			else{
				$bg= ($riga[1]) ? $tmpbg3:$tmpbg1;
				if($y) {
					echo "<tr class=\"bggray\">";
				}else{
					echo "<tr class=\"$bg\">";
				}
			}
			$z=0;
			foreach ($riga as $cella) {
			 if ($e==1){ 
				$t="<th";$f="</th>";
			}else{ 
				$t="<td";$f="</td>";	
			}
			if($z==0 or $z==3)
				echo "$t $i style=\"text-align: left;\">$cella $f";
			else
				echo "$t $i style=\"text-align: left;\">$cella $f";
			$i='';
			$z++;	
			}
			if ($y) $y=0;
			echo "</tr>";
		}
		echo "</table>";
#   echo "</div>";
#	$stampa=ob_get_contents();
	$tmpstampa=ob_get_clean();
	$stampa=$stampasind.$tmpstampa;
	if(isset($tema) and $tema!='bootstrap')	
		echo $stampa;
}

function conssup($fascia,$gruppo,$collegate,$collperd,$primoturno) {
	global $id_cons, $id_cons_gen, $id_comune, $prefix,$dbi;
	global $groups,$lists,$eletti,$ultimo,$quozienti,$num_candlst,$mex,$PNE,$CSEC,$consin;
	global $supsbarramento, $supminpremio, $suppremio;
	global $listsupconta,$numcons,$stampasind;
	#funzione di calcolo per comuni oltre 15.000 abitanti
	#carica il numero di consiglieri da eleggere$groups=array();
	$PNE=_PRIMONON;
	$CSEC=_SINDCONS;
	$lists=array();
	$eletti=array();
	$num_candlst=array();
	#$quozienti = array();
	$oldlists=array();
	$oldlstgrp=array();
	$conselb=array();
	$premio=0;
	/* "Ai fini della determinazione nel secondo turno, della cifra elettorale complessiva delle liste collegate deve tenersi conto anche del collegamento intervenuto in vista del ballottaggio" (Cons. St. Sez. V 4 maggio 2001 n. 2519; 20 settembre 2000 n. 4894; 19 marzo 1996 n. 290)   
	 */

	if (!isset($_SESSION['ballo1'])) $_SESSION['ballo1']='';
	if (!isset($_SESSION['ballo2'])) $_SESSION['ballo2']='';
	$gruppoperd= ($gruppo==$_SESSION['ballo1']) ? $_SESSION['ballo2'] : $_SESSION['ballo1'];

	#per voti validi non si intendono i voti validi alle liste ma i voti validi espressi
	#$res_val = mysql_query("SELECT sum(validi_lista) from ".$prefix."_ele_sezioni where id_cons='$id_cons'",$dbi); 
	$sql = "SELECT sum(voti) from ".$prefix."_ele_voti_gruppo where id_cons='$id_cons'";
	$res_val = $dbi->prepare("$sql");
	$res_val->execute();
	 
	list($validi) = $res_val->fetch(PDO::FETCH_NUM);

	$sbarra=($validi*$supsbarramento)/100; 
	$sql = "SELECT t1.descrizione,t1.num_gruppo,t2.id_lista,t2.num_lista,t2.descrizione,sum(t3.voti) as voti from ".$prefix."_ele_gruppo as t1,  ".$prefix."_ele_lista as t2, ".$prefix."_ele_voti_lista as t3 where t1.id_cons='$id_cons' and t1.id_gruppo=t2.id_gruppo and t2.id_lista=t3.id_lista group by t1.descrizione,t1.num_gruppo,t2.id_lista,t2.num_lista,t2.descrizione order by voti desc";
	$res_per = $dbi->prepare("$sql");
	$res_per->execute();

	$groups=array();
	$premio=0;
	//10-05-2009 gestione differenziata delle norme elettorali
	#carica l'array dei gruppi e della cifra di gruppo
	while (list($descr,$num_gruppo,$id_lista,$num_lista,$descr_lista,$voti)= $res_per->fetch(PDO::FETCH_NUM)){
	 if ($listsupconta or $voti>=$sbarra){
		if (! isset($groups[($num_gruppo)])) $groups[($num_gruppo)]=0;
		$desgruppi[$num_gruppo]=$descr;
		$desliste[$num_lista]=$num_lista.") ".$descr_lista;
		$idlst[$num_lista]=$id_lista;
		$listagruppo[$num_lista]=$num_gruppo;
		$lists[$num_lista]=$voti;
		$groups[($num_gruppo)]+=$voti;
	  }
	}   
################### carica array ... 25  maggio 2014
	foreach($collegate as $key=>$val){	
		$sql = "SELECT id_gruppo from ".$prefix."_ele_lista where num_lista='$val' and id_cons='$id_cons'";
		$res = $dbi->prepare("$sql");
		$res->execute();
		list($tempig)=$res->fetch(PDO::FETCH_NUM);
		$sql = "SELECT num_gruppo from ".$prefix."_ele_gruppo where id_gruppo='$tempig' and id_cons='$id_cons'";
		$res = $dbi->prepare("$sql");
		$res->execute();
		list($tempng)=$res->fetch(PDO::FETCH_NUM);
		$grpinc[$val]=$tempng;
	}
	$elencog=array();
	$sindseggiopre=array();
	foreach($groups as $testk=>$valk) if($testk!=$gruppo) {$elencog[$testk]=$valk;}
	$testseggio=calcoloseggi($elencog,floor($numcons*(100-$suppremio)/100),0);
	foreach($testseggio as $testk=>$valk) { $sindseggiopre[]=$testk;}
##################
	$descrsind=$desgruppi[$gruppo];
	foreach ($groups as $key=>$val){
#controlla se un gruppo di liste, tra quelle perdenti, ha superato il 50%
		if ($key!=$gruppo and $val> $validi/2) $premio=2;
#elimina gruppi che non hanno superato lo sbarramento
		if ($val<$sbarra){
			foreach ($listagruppo as $lst=>$grp)
				if ($grp==$key){
					unset($listagruppo[$lst]);
					unset($desliste[$lst]);
					unset($lists[$lst]);
				}    	
			unset($groups[($key)]);
			unset($desgruppi[($key)]);

		}
	}
    foreach ($collegate as $lst) 
    	if (isset($lists[$lst])){
    		 if($premio){
    		 	$oldlstgrp[$lst]=$listagruppo[$lst];
    		 	$oldlists[$lst]=$lists[$lst];
    		 }
    		 $groups[$listagruppo[$lst]]-=$lists[$lst];
    		 $listagruppo[$lst]=$gruppo;
    		 $groups[$gruppo]+=$lists[$lst];
    	}
#controlla se la percentuale del gruppo vincente e' tra il 40 e il 60% o il sindaco e' eletto al secondo turno
#e se nessun altro gruppo ha superato il 50% assegna il premio di maggioranza
#e se nessun altro gruppo ha superato il 50% e nessuno ha ottenuto piu' del 60% dei seggi, assegna il premio di maggioranza
	$consmin=$numcons;
	$gruppomin=calcoloseggi($groups,$consmin,0);
	$nopremio=1;
	if (($groups[$gruppo]>=(($validi*$supminpremio)/100) or ! $primoturno) and $groups[$gruppo]<(($validi*$suppremio)/100) and !$premio and $nopremio) $premio=1;
	else $premio=0;
	$consel=array();
	$consel[]=array(_LISTA,_VOTI,_SEGGI,_CANDIDATO,_CIFRAELE,_QUOZIENTI);
	$groupsappo=$groups;
	$candidati=array();
	$grpperd=$gruppoperd;
//maggio 2011: a qui viene spostato in modo da aggiungere i voti di lista delle collegate per il perdente solo dopo aver controllato se supera il 50%, in questo modo si evita che il collegamento tra perdenti faccia decadere il premio di maggioranza se solo insieme superano il 50% 
//maggio 2011: da qui
	if(!isset($groups[$gruppoperd])) $groups[$gruppoperd]=0;
		foreach ($collperd as $lst) 
			if (isset($lists[$lst])){ 
				$oldlstgrp[$lst]=$listagruppo[$lst];
				 $listagruppo[$lst]=$gruppoperd;
				 $oldlists[$lst]=$lists[$lst];
				 $groups[$gruppoperd]+=$lists[$lst];
				 $groups[$oldlstgrp[$lst]]-=$lists[$lst];
	}   
////maggio 2011: a qui (mettendolo dopo è come se l'avessi tolto ma possono esserci altre implicazioni visto che si modificano le percentuali del gruppo, così per ora non lo tolgo) va tolto se non vanno sommati i voti delle liste collegate al secondo turno con quelli del gruppo che perde il ballottaggio, se non si collegano viene favorita l'elezione del candidato sindaco con cui era collegata al primo turno mentre se si collegano viene favorito il principio di aggregazione. Per ora i perdenti sono considerati con la situazione al primo turno. Implementiamo cos�: il 50% deve essere superato dalla minoranza nel primo turno, quindo senza somma dei voti delle liste aggiunte nel secondo turno - la suddivisione dei seggi viene fatta considerando i collegamenti al secondo turno, le liste collegate partecipano alla suddivisione dei seggi con questo gruppo quindi si confronta con le liste del gruppo in cui era al primo turno e valutando i coefficienti si stabilisce quale lista cede il seggio al candidato sindaco non acceduto al ballottaggio.
	if ($premio) {
################### nell'array sindseggio vengono inseriti i candidati sindaco che restano senza seggio per gli apparentamenti
		$elencog=array();
		$testseggio=array();
		$sindseggio=array();
		foreach($groups as $testk=>$valk) {if($testk!=$gruppo) $elencog[$testk]=$valk;}
		$perdente[$gruppoperd]=$groups[$gruppoperd];
		$testseggio=calcoloseggi($elencog,floor($numcons-$numcons*($suppremio)/100),0);
		foreach($testseggio as $testk=>$valk) 
		foreach($sindseggiopre as $testk=>$valk){ if(isset($testseggio[$valk]) and $testseggio[$valk]) continue; $sindseggio[$valk]=$valk;} 
		$sindaco[$gruppo]=$groups[$gruppo]; $groups[$gruppo]=0;
		$gruppomag=calcoloseggi($sindaco,ceil($numcons*$suppremio/100),0);
#######calcola i seggi per lista
		foreach ($gruppomag as $key=>$val){
			foreach($listagruppo as $lst=>$grp){
				if($grp!=$key) continue;
				$id_lista=$idlst[$lst];
				$x=$lst;
				$y=$lists[$x];
				$pos=0;
				$z=0;
				$arvin[$x][$pos++]=$desliste[$lst]; 
				$sql = "SELECT concat(substring(concat('0',t1.num_cand),-2),') ',t1.cognome,' ',substring(t1.nome from 1 for 1),'.') as descr,sum(t2.voti) as voti from ".$prefix."_ele_candidati as t1, ".$prefix."_ele_voti_candidati as t2 where t1.id_lista='$id_lista' and t1.id_cand=t2.id_cand GROUP BY t1.num_cand,t1.cognome,t1.nome order by voti desc,t1.num_cand";
				$res_can = $dbi->prepare("$sql");
				$res_can->execute();
				$num_candlst[$x]=$res_can->rowCount();
				while(list($cand,$pre)=$res_can->fetch(PDO::FETCH_NUM)){ 
					$cifra[$x][$pos]=$y+$pre;
					$arvin[$x][$pos++]=$cand;
				}	
				$listemag[$x]=$y;
				$desliste[$x]=$descr;
				$percliste[$x]="<br>$y (".number_format($y*100/$validi,2)."%)";
				$z++;
				$seggimag=array();
				$x=0;
#####25 maggio 2014 - il candidato sindaco non eletto ha diritto al seggio anche se di maggioranza
### individua ultimi seggi assegnati a liste collegate
				if($grp==$gruppo){
					$listdec =array(); 
					$seggitmp=calcoloseggi($listemag,$val,1); 
					foreach ($seggitmp as $keyl=>$vall){
						if($vall==0) continue; 
						$sql = "SELECT id_gruppo from ".$prefix."_ele_lista where num_lista='$keyl' and id_cons='$id_cons'";
						$res = $dbi->prepare("$sql");
						$res->execute();
						list($tempig)=$res->fetch(PDO::FETCH_NUM);
						$sql = "SELECT num_gruppo from ".$prefix."_ele_gruppo where id_gruppo='$tempig' and id_cons='$id_cons'";
						$res = $dbi->prepare("$sql");
						$res->execute();
						list($tempng)=$res->fetch(PDO::FETCH_NUM); 
						if (isset($sindseggio[$tempng])) {$listdec[$tempng][]=$keyl;}
					} 
				}
			}
			$seggimag=calcoloseggi($listemag,$val,1);
####cerca ultimo seggio assegnato per gruppo di liste collegate al primo turno
			foreach ($seggimag as $key2=>$val2){ 
				if($val2==0) continue;
				if(isset($grpinc[$key2])) { $tempng=$grpinc[$key2];}
				if(isset($listdec[$tempng]))
				foreach($listdec[$tempng] as $ark=>$arv) {
					if(!isset($ultquoz[($sindseggio[$tempng])]) and $sindseggio[$tempng]) {$ultquoz[($sindseggio[$tempng])]=$quozienti[$arv][($val2-1)];$lastlist[$tempng]=$arv;}
					elseif ($ultquoz[($sindseggio[$tempng])]>$quozienti[$arv][($val2-1)]) 
					{
						$ultquoz[($sindseggio[$tempng])]=$quozienti[$arv][($val2-1)];$lastlist[($sindseggio[$tempng])]=$arv;
					}
				}
			} 
			foreach ($seggimag as $key2=>$val2){ 
## condizione per esclusione di un seggio da destinare al sindaco non eletto    in_array($key2,$lastlist)
				if(isset($lastlist))
				foreach($lastlist as $key3=>$val3){
					if($key2==$val3) { 
						$arappo=array_shift($arvin[$key2]);$tpmgrp=0;
						array_unshift($arvin[$key2],$desgruppi[$key3]); array_unshift($arvin[$key2],$arappo);array_unshift($cifra[$key2],"--");array_unshift($cifra[$key2],"--");
					}
				}
## fine condizione - impostare variabile in db per differenziare il comportamento per consultazioni diverse
				for ($z=0;$z<$val2;$z++){ 
					if ($z) $consel[]=array("","","",$arvin[$key2][($z+1)],$cifra[$key2][($z+1)],number_format($quozienti[$key2][$z],2));
					else
					{
						$consel[]=array($arvin[$key2][0],$percliste[$key2],$val2,$arvin[$key2][($z+1)],$cifra[$key2][($z+1)],number_format($quozienti[$key2][$z],2));
						$arlisdesv[]=$arvin[$key2][0];$arlissegv[]=$val2;$arlisnumv[]=$key2;
					}
				}
				$x++;
				if($val2)
				$consel[]=array($arvin[$key2][0],"$PNE","",$arvin[$key2][($z+1)],$cifra[$key2][($z+1)],number_format($quozienti[$key2][$z],2));
			}
		}
	}
	if ($premio) $consmin=floor($numcons*(100-$suppremio)/100);
	else $consmin=$numcons;
#####calcolo per la minoranza o in caso non ci sia premio di maggioranza   
	$gruppomin=calcoloseggi($groups,$consmin,0);
	$ordinati[$gruppo]=$gruppomin[$gruppo];
	foreach ($gruppomin as $key=>$val){
		if($key!=$gruppo) $ordinati[$key]=$val;
	}
	$gruppomin=$ordinati;
	foreach ($gruppomin as $key=>$val){
		if($premio and $key==$gruppo) continue;
		$listemin=array();
		$cifra=array();
		foreach($listagruppo as $lst=>$grp){
			if($grp!=$key) continue;			
			$id_lista=$idlst[$lst];
			$x=$lst;
			$y=$lists[$x];
			$pos=0;
			$z=0;
			$pos=0;$z=0;
			if(!$premio and $key==$gruppo) $arvin[$x][$pos++]=$desliste[$lst];
			else $arper[$x][$pos++]=$desliste[$lst]; 
			$sql = "SELECT concat(substring(concat('0',t1.num_cand),-2),') ',t1.cognome,' ',substring(t1.nome from 1 for 1),'.') as descr,sum(t2.voti) as voti from ".$prefix."_ele_candidati as t1, ".$prefix."_ele_voti_candidati as t2 where t1.id_lista='$id_lista' and t1.id_cand=t2.id_cand GROUP BY descr order by voti desc,t1.num_cand";
			$res_can = $dbi->prepare("$sql");
			$res_can->execute();
			$num_candlst[$x]=$res_can->rowCount();
			while(list($cand,$pre)=$res_can->fetch(PDO::FETCH_NUM)) {
				$cifra[$x][$pos]=$y+$pre;
				if(!$premio and $key==$gruppo)
				$arvin[$x][$pos++]=$cand;
				else
				$arper[$x][$pos++]=$cand;
			}
			$listemin[$x]=$y;
			$desliste[$x]=$descr;
			$percliste[$x]="<br>$y (".number_format($y*100/$validi,2)."%)";
		}
		$seggimin=array();
		echo "$mex";
		$ultimo='';
		$seggimin=calcoloseggi($listemin,$val,1);
		echo "$mex";
		if(!$premio and $key==$gruppo)
		foreach ($seggimin as $lista=>$valc){
			$arper[$lista]=$arvin[$lista];
		}
		if ($val and $key!=$gruppo and $consin) {$conselsin[]=array("$CSEC",$desgruppi[$key]); $arcansin[]=$desgruppi[$key];}
		foreach ($seggimin as $lista=>$val)
			if(isset($oldlstgrp[$lista]) and !isset($oldseggi[$lista])) {$oldseggi[$lista]=$val;
		}
		if($val==0){
			if($ultimo==''){
				foreach($oldlists as $lst=>$vot)
				{
					if (!isset($quozienti[$lst][($val)])) $quozienti[$lst][($val)]=$vot;
					if ($oldlstgrp[$lst]!= $key or !isset($oldseggi[$lst]) or $oldseggi[$lst]==0) continue;
					if($ultimo=='') $ultimo=$lst;
					if($quozienti[$ultimo][($val)]==$last[$lst]) 
					{
						if($lists[$ultimo]==$lists[$lst] and $ultimo!=$lst) $mex="Per attribuire l'ultimo seggio è necessario un sorteggio tra la lista n. $ultimo e la lista n. $lst";
						elseif($lists[$ultimo]>$lists[$lst]) {$ultimo=$lst;$mex="";}
					}
					if ($quozienti[$ultimo][($val)]> $last[$lst]) {$ultimo=$lst;$mex="";}
				}
				$lst=$ultimo;
				if($ultimo and $consin){
					if($conselb[$ttl[($lst)]][2]>1) $conselb[$ttl[($lst)]][2]--;else $conselb[$ttl[($lst)]][2]='';
					$daunset[]=$tt[($lst)];
					$conselsin[]=array("$CSEC",$desgruppi[$key]);
					$arcansin[]=$desgruppi[$key];
				}
			}
		}
		foreach ($seggimin as $lista=>$val){
			if($ultimo==$lista and $key!=$gruppo and $consin) $val--; 
		}
		foreach ($seggimin as $lista=>$val){
			if($ultimo==$lista and $key!=$gruppo and $consin) $val--; 
			for ($z=0;$z<$val;$z++){
				if ($z) $conselb[]=array("","","",$arper[$lista][($z+1)],$cifra[$lista][($z+1)],number_format($quozienti[$lista][$z],2));
				else{
					if(!isset($arper[$lista][($z+1)])) $arper[$lista][($z+1)]=0;
					if(!isset($cifra[$lista][($z+1)])) $cifra[$lista][($z+1)]=0;
					$conselb[]=array($arper[$lista][0],$percliste[$lista],$val,$arper[$lista][($z+1)],$cifra[$lista][($z+1)],number_format($quozienti[$lista][$z],2));
					$ttl[$lista]=(count($conselb)-1);
				}
			}
			if (isset($oldlists[$lista]))
			{ 
				$tt[$lista]=(count($conselb)-1);
				if($z) $last[$lista]=$quozienti[$lista][($z-1)]; else $last[$lista]=0;				
			} 
			if($val){
				if(!isset($arper[$lista][($z+1)])) $arper[$lista][($z+1)]=0;
				if(!isset($cifra[$lista][($z+1)])) $cifra[$lista][($z+1)]=0;
				if(!isset($quozienti[$lista][$z])) $quozienti[$lista][$z]=0;
				$conselb[]=array($arper[$lista][0],"$PNE","",$arper[$lista][($z+1)],$cifra[$lista][($z+1)],number_format($quozienti[$lista][$z],2)); 
			}
		}
	}//chiude foreach gruppomin
	$stampasind= "<table summary=\"Tabella dei consiglieri eletti\" class=\"table-docs\" cellspacing=\"0\" cellpadding=\"2\" border=\"1\" rules=\"all\">";
	$stampasind.= "<tr class=\"bggray\"><td scope=\"row\"><b>";
	$stampasind.= _SINDACO.": ".$desgruppi[$gruppo]."</b></td></tr></table>";
	if(isset($daunset)){
		if ((sort($daunset,SORT_NUMERIC))==false) echo "Errore di programma!";
		ELSE { 
			$tmpda=array_reverse($daunset); 
			foreach($tmpda as $key=>$val) {
				$conselb[$val][0]=$conselb[($val+1)][0];$conselb[$val][1]=$conselb[($val+1)][1];
				unset($conselb[($val+1)]);
			}
		}
	}
	if (!$premio)
	{
		foreach($conselb as $key=>$val) 
		{
			if ($val[2]){
				$nlst=intval($val[0]);
				$arlisdesv[]=$val[0];
				$arlissegv[]=$val[2];
			}
			if($listagruppo[$nlst]!=$gruppo) continue;
			$consel[]=array($val[0],$val[1],$val[2],$val[3],$val[4],$val[5]);
		}
	}
	if (isset($conselsin)) foreach($conselsin as $key=>$val) 
	{
		$consel[]=array($val[0],$val[1]);
	}
	foreach($conselb as $key=>$val) 
	{
		if ($val[2]){
			$nlst=intval($val[0]);
			$arlisdesp[]=$val[0];
			$arlissegp[]=$val[2];
		}
		if($listagruppo[$nlst]==$gruppo) continue;
		$consel[]=array($val[0],$val[1],$val[2],$val[3],$val[4],$val[5]);
	}
	stampalista($consel);
	unset($_SESSION['ballo1']);unset($_SESSION['ballo2']);unset($_SESSION['grp1']);unset($_SESSION['grp2']);
}

?>
