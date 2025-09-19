<?php 
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Roberto Gigli & Luciano Apolito                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die ("You can't access this file dirrectly...");
}

global $op, $prefix, $dbi, $offset, $min,$descr_cons,$genere,$votog,$votol,$votoc,$circo, $id_cons,$tipo_cons,$id_comune,$id_cons_gen,$id_circ,$csv,$w,$l,$siteistat,$flash,$tour;

if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
else $circos=''; 

if($genere=='4' || $votog) $tab="lista";
		 else $tab="gruppo";
if ($genere==0) $tab="ref";


	if ($genere!=0) { // referendum

	$sql="select sum(t1.maschi+t1.femmine)   from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' and validi>0 $circos";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($tot_aventi)  = $res->fetch(PDO::FETCH_NUM);

	$sql="select sum(t1.validi+t1.nulli+t1.bianchi+t1.contestati+t1.voti_nulli) as tot,
		sum(t1.validi),sum(t1.nulli+t1.voti_nulli),sum(t1.bianchi),sum(t1.contestati), '0', '0', '0'
 		from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' and validi>0 $circos";
	$res = $dbi->prepare("$sql");
	$res->execute();


	}else{
		


	$sql="SELECT sum(maschi+femmine) FROM ".$prefix."_ele_sezioni where id_cons='$id_cons'";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($tot_aventi)  = $res->fetch(PDO::FETCH_NUM);
		$sql="SELECT  sum(validi+nulli+bianchi+contestati) as tot,
		sum(validi),sum(nulli),sum(bianchi),sum(contestati), id_gruppo, sum(si), sum(no)
		from ".$prefix."_ele_voti_ref  where id_cons=$id_cons group by id_gruppo having tot>'0'";
		$res = $dbi->prepare("$sql");
		$res->execute();

	}

	$i=0;

	while (list($tot_votanti,$validi,$nulli,$bianchi,$contestati,$id,$si,$no) = $res->fetch(PDO::FETCH_NUM)){

# calcolo sezioni 
if($genere==0) $sql="select count(0) from ".$prefix."_ele_voti_".$tab." where id_cons='$id_cons' and id_gruppo='$id'";
else 
	if (!$circos) $sql="select count(0) from ".$prefix."_ele_sezioni as t2 where t2.id_cons='$id_cons' and t2.validi+t2.nulli+t2.bianchi+t2.contestati>0 ";
	else $sql="select count(0) from ".$prefix."_ele_sezioni as t2 left join ".$prefix."_ele_sede as t1 on t1.id_sede=t2.id_sede where t1.id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$id_circ') and t2.validi+t2.nulli+t2.bianchi+t2.contestati>0 ";	

$res_sez = $dbi->prepare("$sql");
$res_sez->execute();

if($res_sez->rowCount())
        list($numero)=$res_sez->fetch(PDO::FETCH_NUM);
else
        $numero=0;
#$sql="select t3.* from ".$prefix."_ele_voti_".$tab." as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' $circos group by t3.id_sez ";
#	$res_sez = $dbi->prepare("$sql");
#	$res_sez->execute();

$sql="select t1.id_sez from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos";
	$res_sez = $dbi->prepare("$sql");
	$res_sez->execute();
 
$sezioni=$res_sez->rowCount();

		$tot_votanti=$validi+$bianchi+$nulli+$contestati;
		$arvoti=array($validi,$nulli,$bianchi,$contestati);
		$arperc=arrayperc($arvoti,$tot_votanti);
		if($tot_votanti){
			if($genere==0 and $validi) {$perc_si=number_format($si*100/$validi,2);$perc_no=number_format(100 - $si*100/$validi,2);}
			else {$perc_si="0.00"; $perc_no="0.00";}
		$perc_validi=number_format($arperc[0],2);#number_format($validi*100/$tot_votanti,2);
		$perc_nulli=number_format($arperc[1],2);
		$perc_bianchi=number_format($arperc[2],2);
		$perc_conte=number_format($arperc[3],2);
		}else {$perc_validi="0.00";$perc_nulli="0.00";$perc_bianchi="0.00";$perc_conte="0.00";}
		if($tot_aventi)
			$perc_votanti=number_format($tot_votanti*100/$tot_aventi,2);
		else $perc_votanti="0.00";
		$non_votanti=($tot_aventi - $tot_votanti);
		$perc_non=100-$perc_votanti;

		if ($genere==0) {
			$sql="SELECT num_gruppo,descrizione from ".$prefix."_ele_gruppo where id_gruppo=$id";
			$resg = $dbi->prepare("$sql");
			$resg->execute();

			list($num_gruppo,$descr_gruppo)  = $resg->fetch(PDO::FETCH_NUM);
		}
		  
		 $i++;
		 if($i==1)echo "<h5><b>"._DETTAGLIO." "._VOTIE."</b></h5>";
		 if($i==1)echo "<div style=\"text-align:center;\">[ <i>Percentuali Relative</i> ]</div>";		
		
		if ($genere==0) {echo "<br><div style=\"text-align:center;\">Referendum n. <b>[$num_gruppo]</b></div>";}
		echo "<div style=\"text-align:center;\"><i> "._SEZSCRU." $numero "._SU." $sezioni </i></div>";
		echo "<table bgcolor=\"gray\" width=\"100%\" cellspacing=\"1\">";
		echo "
		<tr bgcolor=\"#ffffff\"><td ><b>"._AVENTI."</b></td><td align=\"right\">$tot_aventi</td><td align=\"right\"><span class=\"red\">100.00%</span></td></tr>

		<tr bgcolor=\"#ffffff\"><td><b>"._VOTANTI."</b></td><td align=\"right\">$tot_votanti</td><td align=\"right\"><span class=\"red\">$perc_votanti%</span></td></tr>";
	if ($genere==0){		
		echo "<tr bgcolor=\"#ffffff\"><td>"._SI."</td><td align=\"right\">$si</td><td align=\"right\"><span class=\"red\">$perc_si%</span></td></tr>
		
		<tr bgcolor=\"#ffffff\"><td>"._NO."</td><td align=\"right\">$no</td><td align=\"right\"><span class=\"red\">$perc_no%</span></td></tr>";
		}
		echo "<tr bgcolor=\"#ffffff\"><td>"._VALIDI."</td><td align=\"right\">$validi</td><td align=\"right\"><span class=\"red\">$perc_validi%</span></td></tr>
		
		<tr bgcolor=\"#ffffff\"><td>"._NULLI."</td><td align=\"right\">$nulli</td><td align=\"right\"><span class=\"red\">$perc_nulli%</span></td></tr>
		
		<tr bgcolor=\"#ffffff\"><td>"._BIANCHI."</td><td align=\"right\">$bianchi</td><td align=\"right\"><span class=\"red\">$perc_bianchi%</span></td></tr>
		
		<tr bgcolor=\"#ffffff\"><td>"._CONTESTATI."</td><td align=\"right\">$contestati</td><td align=\"right\"><span class=\"red\">$perc_conte%</span></td></tr>

		</table>";

    
}

?>
