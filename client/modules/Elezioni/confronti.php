<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Roberto Gigli & Luciano Apolito                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* Modulo confronti                                                     */
/*                                                                      */
/************************************************************************/
/*
1)visualizza l'elenco delle consultazioni nel comune con una check box per la selezione
2)gli id_cons delle consultazioni selezionate vengono inseriti in $_SESSION['confronti][]
3)per ogni consultazione chiede di associare le liste al gruppo1, al gruppo2 o a nessun gruppo
4)per ogni gruppo1 inserisci id_lista in $gruppo1[], così per ogni gruppo2
5)somma i voti delle liste in gruppo1 per ogni consultazione e inserisci in array1, così per gruppo2
6)proponi la scelta della modalita' di visualizzazione: per voti ottenuti, percentuale su voti validi, percentuale su elettori.
*/
if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;

if (isset($param['grp1'])) $grp1=addslashes($param['grp1']); else $grp1='';//_GRUPPO1;
if (isset($param['grp2'])) $grp2=addslashes($param['grp2']); else $grp2='';//_GRUPPO2;
if (isset($param['grp3'])) $grp3=addslashes($param['grp3']); else $grp3='';
if (isset($param['pag'])) $pag=intval($param['pag']); else $pag=0;
if (isset($param['pags'])) $pags=intval($param['pags']); else $pags=0;
if (isset($param['listecol'])) $listecol=intval($param['listecol']); else $listecol=0;

$grp1= htmlentities($grp1); 
$grp2= htmlentities($grp2); 
$grp3= htmlentities($grp3); 

echo "<table><tr><td align=\"center\">"._CNFR_CONS."</td></tr></table>";

//visualizza le consultazioni tra le quali scegliere quelle da confrontare
function sceglicons(){
global $param,$id_cons_gen, $dbi, $prefix, $id_comune;
$_SESSION['confr']=array();
$_SESSION['grulis']=array();
$_SESSION['grp1']=array();
$_SESSION['grp2']=array();
$_SESSION['grp3']=array();
$x=1;

while (isset($_SESSION['num_lista'.$x]))
	unset($_SESSION['num_lista'.$x]);
/*$x=1;
while (isset($param['num_lista'.$x])) {
	if ($param['num_lista'.$x]==$gruppo) array_push($collegate,$_SESSION['num_lista'.$x]);
	elseif ($param['num_lista'.$x]!=0) array_push($collperd,$_SESSION['num_lista'.$x]);
	$x++;
}*/
$sql="SELECT t1.descrizione,t2.id_cons FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2, ".$prefix."_ele_tipo as t3 where t1.id_cons_gen=t2.id_cons_gen and t1.tipo_cons=t3.tipo_cons and t3.circo='0' and t3.genere>'2' and t2.id_comune='$id_comune' order by t1.data_fine,t1.descrizione";
$res = $dbi->prepare("$sql");
$res->execute();
if ($res->rowCount()){
	echo "<form id=\"cons\" action=\"modules.php\">";
	echo "<table><tr><td>"
	."<input type=\"hidden\" name=\"op\" value=\"come\"/><input type=\"hidden\" name=\"info\" value=\"confronti\"/>";
	echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\"/>";
	echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"/>";	

	echo "<table><tr>
		<td>Dai un nome al il primo gruppo</td><td><input type=\"text\" name=\"grp1\" value=\"\"/></td></tr>
		<tr><td>Dai un nome al secondo gruppo</td><td><input type=\"text\" name=\"grp2\" value=\"\"/></td></tr>
		<tr><td>Se vuoi un raffronto con il totale degli altri gruppi</td><td><input type=\"checkbox\" name=\"grp3\"/></td></tr>
		<tr><td align=\"center\" colspan=\"2\"><br/>"._SCELTA_CONS."</td></tr>";
	$x=1;
	while (list($descr_cons,$id_cons)= $res->fetch(PDO::FETCH_NUM)){
		echo "<tr><td>$descr_cons</td><td><input type=\"checkbox\" name=\"check$x\"/>";
		echo "<input type=\"hidden\" name=\"cons$x\" value=\"$id_cons\"/>";
		echo "<input type=\"hidden\" name=\"verifica\" value=\"1\"/></td></tr>";	
		$x++;
	}
	echo "<tr><td><input type=\"hidden\" name=\"pags\" value=\"$x\"/></td><td><input type=\"submit\" name=\"invia\" value=\""._OK."\"/></td></tr></table>
	</td>
	<td class=\"modulo\"><h2>Help on line</h2><br/>
	<h3>Esempi di uso</h3><br /> 
	<b>Raffronto centrodestra-centrosinistra</b><br/>
	- Mettere i nomi ai gruppi da assemblare<br/>
	- Scegliere se confrontarli anche con i restanti gruppi<br />
	- Scegliere le consultazioni, almeno una, da raffrontare e premere ok<br/>
	- Scegliere le liste da abbinare ad ogni gruppo<br /><br/>
	<b>Raffronto andamento per un solo gruppo o lista</b><br />
	- Immettere solo un nome del gruppo lasciando vuoto l'altro<br />
	- Scegliere le consultazioni premere ok<br />
	- Scegliere le liste da abbinare ad ogni gruppo<br /><br/>
	
	 		




</td></tr></table>

</form>";
}
}

//visualizza le liste per consultazione e permette di associarle ai gruppi
function scegliliste(){
	global $bgcolor1,$bgcolor2,$param,$id_cons_gen, $dbi, $prefix, $id_comune, $pag, $pags,$id_cons,$grp1,$grp2,$grp3,$fascia;
	$limite=0;
	// ipotesi di campi vuoti
	if(!$grp1 && !$grp2 && !$grp3){echo "<span class=\"red\">Metti il nome di almeno un gruppo oppure il check al raffronto totale</span>"; include("footer.php"); exit;}
	
	


	
	$verifica=0;
	if (!$pag) //alla prima esecuzione filtra le consultazioni selezionate
	{
		$pag=1;
		$x=1;
		$y=1;
		while (isset($param['cons'.$x])) {
			if(isset($param[('check'.$x)]) and $param[('check'.$x)]) 
				
				{
				$_SESSION['confr'][$y]=$param[('cons'.$x)];
				$y++;
				$verifica++; // verifica di scelte consultazioni
				}
			else $pags--;
			$x++;
	
			}
		if ($verifica<=1){echo "<span class=\"red\">Scegli almeno due consultazioni</span>"; include("footer.php"); exit;} //esce 
	}else{
		$_SESSION['grp1'][$pag]=array();
		$_SESSION['grp2'][$pag]=array();
		$_SESSION['grp3'][$pag]=array();
		$x=1;
		while (isset($param['num_lista'.$x])) {
			
				if ($param['num_lista'.$x]=='grp1') 	{array_push($_SESSION['grp1'][$pag],$_SESSION['num_lista'.$x]);}

				if ($param['num_lista'.$x]=='grp2') {array_push($_SESSION['grp2'][$pag],$_SESSION['num_lista'.$x]);}

				if ($param['num_lista'.$x]=='grp3') {array_push($_SESSION['grp3'][$pag],$_SESSION['num_lista'.$x]);}
				unset($_SESSION['num_lista'.$x]);

				$x++;
		}
		$pag++;
	}
	if($pag>=$pags) return(1); //in $pags il numero delle consultazioni
	$id_cons2=$_SESSION['confr'][$pag];
	$sql="SELECT t1.descrizione,t1.tipo_cons,t2.id_conf from ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_cons='$id_cons2'";
	$res_lis = $dbi->prepare("$sql");
	$res_lis->execute();

	list($descr,$tipoc,$legge)=$res_lis->fetch(PDO::FETCH_NUM);
	if($legge){
		$sql="SELECT limite from  ".$prefix."_ele_conf where id_conf='$legge'";
		$res = $dbi->prepare("$sql");
		$res->execute();

		list($limite)=$res->fetch(PDO::FETCH_NUM);
	}
if($tipoc==3 and $fascia<=$limite){
	$_SESSION['grulis'][$id_cons2]='1';
	$sql="SELECT t2.id_gruppo,t1.descrizione,t2.descrizione from ".$prefix."_ele_lista as t1 left join ".$prefix."_ele_gruppo as t2 on t1.id_gruppo=t2.id_gruppo where t1.id_cons='$id_cons2' order by t2.num_gruppo";

}else{
	$sql="SELECT t1.id_lista,t1.descrizione,t2.descrizione from ".$prefix."_ele_lista as t1 left join ".$prefix."_ele_gruppo as t2 on t1.id_gruppo=t2.id_gruppo where t1.id_cons='$id_cons2' order by t2.num_gruppo";
	$_SESSION['grulis'][$id_cons2]='0';
	}
$res_lis = $dbi->prepare("$sql");
$res_lis->execute();

	$yy=$res_lis->rowCount();
	if ($yy){
		echo "<form name=\"liste\" action=\"modules.php\">"
		."<input type=\"hidden\" name=\"op\" value=\"come\"/><input type=\"hidden\" name=\"info\" value=\"confronti\">";
		echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\"/>";
		echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"/>";
		if($grp1)echo "<input type=\"hidden\" name=\"grp1\" value=\"$grp1\"/>";
		if($grp2)echo "<input type=\"hidden\" name=\"grp2\" value=\"$grp2\"/>";
		if($grp3)echo "<input type=\"hidden\" name=\"grp3\" value=\"$grp3\"/>";
		echo "<br/>";
		echo "<table style=\"border-collapse: collapse; border: 2px solid black;\"><tr class=\"bggray\"><td style=\"padding:5px;background-color:$bgcolor1;\">"._CONSULTAZIONE.": <b>$descr</b><br/>"._SCELTA_LISTE."</td></tr><tr class=\"bggray\"><td style=\"padding:2px;background-color:$bgcolor1;\"></td>";
		if($grp1)echo "<td style=\"background-color:$bgcolor1;\"><b>$grp1</b></td>";
		if($grp2)echo "<td style=\"background-color:$bgcolor1;\"><b>$grp2</b></td>";
		echo "<td style=\"background-color:$bgcolor1;\"><b>"._ALTROGRP."</b></td>";
		echo "</tr>";
			$z=1;
			while(list($id_lista,$descr,$gruppo) = $res_lis->fetch(PDO::FETCH_NUM)) {
				$_SESSION['num_lista'.$z]=$id_lista;
				echo "<tr><td>$gruppo</td><td>$descr</td>";
				if($grp1)echo "<td><input type=\"radio\" name=\"num_lista$z\" value=\"grp1\"/></td>";
				if($grp2)echo "<td><input type=\"radio\" name=\"num_lista$z\" value=\"grp2\"/></td>";
				echo "<td><input type=\"radio\" name=\"num_lista$z\" value=\"grp3\" checked=\"checked\"/></td>";
				
				echo "</tr>";
				$z++;		
			}
			echo "<tr><td colspan=\"5\"><input type=\"hidden\" name=\"pag\" value=\"$pag\"/><input type=\"hidden\" name=\"pags\" value=\"$pags\"/>";
			echo "<input type=\"submit\" name=\"invia\" value=\""._OK."\"/></td></tr></table></form>";
		}
	return(0);
}

// stampa a video i risultati
function outgraf($ar1,$ar2,$ar3){
global $pags,$id_comune,$prefix,$dbi,$grp1,$grp2,$grp3;

$riga1="<table border=\"1\"><tr class=\"bggray\"><td></td>";
$riga5="<tr align=\"center\"><td>"._VOTANTI."</td>";
foreach($_SESSION['confr'] as $y=>$x){
	$sql="SELECT sum(maschi+femmine) from ".$prefix."_ele_sezioni where id_cons='$x'";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($elet)=$res->fetch(PDO::FETCH_NUM);
	if($_SESSION['grulis'][$x]=='1')
		$sql="SELECT sum(voti) from ".$prefix."_ele_voti_gruppo where id_cons='$x'";
	else
		$sql="SELECT sum(voti) from ".$prefix."_ele_voti_lista where id_cons='$x'";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($tot[$y])=$res->fetch(PDO::FETCH_NUM);
	$sql="SELECT t1.descrizione FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_comune='$id_comune' and t2.id_cons='$x'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($descr)=$res->fetch(PDO::FETCH_NUM);
	$riga1.="<td>$descr</td>";
	$riga5.="<td>".$tot[$y]."<br/>".number_format($tot[$y]*100/$elet)."%</td>";
	// dati per grafico
	$descriz[$y]=$descr;
	$perctot[$y]=number_format($tot[$y]*100/$elet);

	}
$riga1.="</tr>";
if($grp1)$riga2="<tr align=\"center\"><td>$grp1</td>";
if($grp2)$riga3="<tr align=\"center\"><td>$grp2</td>";
if($grp3)$riga4="<tr align=\"center\"><td>"._ALTROGRP."</td>"; else $riga4='';
#for ($x=1;$x<=$pags;$x++){
foreach($_SESSION['confr'] as $x=>$y){
	$perc1=number_format(($ar1[$x]*100/$tot[$x]),2);
	$perc2=number_format(($ar2[$x]*100/$tot[$x]),2);
	$perc3=number_format(($ar3[$x]*100/$tot[$x]),2);
	$perc4=number_format(($ar1[$x]*100/$tot[$x]),3);
	$perc5=number_format(($ar2[$x]*100/$tot[$x]),3);
	$perc6=number_format(($ar3[$x]*100/$tot[$x]),3);
	if (($perc1+$perc2+$perc3)>(100.00)){
		if((($perc4*1000)%10)<(($perc5*1000) % 10)) $max=1; else $max=2;
		if ($max==1) if((($perc4*1000)%10)>(($perc6*1000)%10)) $max=3;
		elseif((($perc5*1000)%10)>(($perc6*1000)%10)) $max=3; 
		if($max==1)$perc1-=0.01;
		elseif($max==2)$perc2-=0.01;
		else $perc3-=0.01;
	}elseif (($perc4+$perc5+$perc6)<(100.00))
		{
		if((($perc4*1000)%10)>(($perc5*1000) % 10)) $max=1; else $max=2;
		if ($max==1) if((($perc4*1000)%10)<(($perc6*1000)%10)) $max=3;
		elseif((($perc5*1000)%10)<(($perc6*1000)%10)) $max=3; 
		if($max==1)$perc1+=0.01;
		elseif($max==2)$perc2+=0.01;
		else $perc3+=0.01;
		}
	if($grp1)$riga2.= "<td>".$ar1[$x]."<br/>$perc1%</td>";
	if($grp2)$riga3.= "<td>".$ar2[$x]."<br/>$perc2%</td>";
	if($grp3)$riga4.= "<td>".$ar3[$x]."<br/>$perc3%</td>";
	// per grafici
	$percg1[$x]=$perc1;
	$percg2[$x]=$perc2;
	$percg3[$x]=$perc3;
}
if($grp1)$riga2.="</tr>";if($grp2)$riga3.="</tr>";if($grp3)$riga4.="</tr>";$riga5.="</tr></table>";
echo $riga1.$riga2.$riga3.$riga4.$riga5;

/* dati da inviare in array al grafico
$descrizione: consultazione
$grp1 e grp2: nome dei gruppi
$ar1 fino a 3 : numero voti gruppi e altri
percg1 fino a 3 : percentuali gruppi e altri
$tot : totale voti
$perctot : percentuale totale voti
$altro="Altro";

echo "<br/>";
foreach($_SESSION['confr'] as $x=>$y){
echo "$descriz[$x]<br/>";
echo "$grp1 : $ar1[$x] - $percg1[$x]%<br/>";
echo "$grp2 : $ar2[$x] - $percg2[$x]%<br/>";
echo "Altri : $ar3[$x] - $percg3[$x]%<br/>";
echo "Totali: $tot[$x] - $perctot[$x]%<hr>";


}
*/
// preparazione per grafico
foreach($ar1 as $val) $ars1[]=$val;
foreach($ar2 as $val) $ars2[]=$val;
foreach($ar3 as $val) $ars3[]=$val;
foreach($percg1 as $val) $per1[]=$val;
foreach($percg2 as $val) $per2[]=$val;
foreach($percg3 as $val) $per3[]=$val;
foreach($descriz as $val) $desc[]=$val;

// includere nel linguaggio
define("_TITOLOVOTI","Raffronti per voto");
define("_TITOLOPERC","Raffronti percentuali");
define("_ALTRO","Altri");

if(!$grp1 && !$grp2)$altro=""._ALL."";
else $altro=""._ALTRO."";

$titolovoti=urlencode(_TITOLOVOTI);
$titoloperc=urlencode(_TITOLOPERC);
$altro=urlencode($altro);
$desc=serialize($desc);
//$desc=urlencode($desc);


$grp1=urlencode($grp1);
$grp2=urlencode($grp2);
$grp3=urlencode($grp3);
$altro=urlencode($altro);
// voti
$ars1=serialize($ars1);
$ars1=urlencode($ars1);
$ars2=serialize($ars2);
$ars2=urlencode($ars2);
$ars3=serialize($ars3);
$ars3=urlencode($ars3);
// percentuali
$per1=serialize($per1);
$per1=urlencode($per1);
$per2=serialize($per2);
$per2=urlencode($per2);
$per3=serialize($per3);
$per3=urlencode($per3);

$logo=verificasimbolo();

	echo "<table><tr><td>
		<img src='modules/Elezioni/grafici/raffrontivoto.php?altro=$altro&amp;desc=$desc&amp;grp1=$grp1&amp;grp2=$grp2&amp;grp3=$grp3&amp;ar1=$ars1&amp;ar2=$ars2&amp;ar3=$ars3&amp;tot=$tot&amp;titvoti=$titolovoti&amp;logo=$logo'  alt=\"Grafico\" /></td></tr></table>";

	echo "<table><tr><td>
		<img src='modules/Elezioni/grafici/raffrontiperc.php?altro=$altro&amp;desc=$desc&amp;grp1=$grp1&amp;grp2=$grp2&amp;grp3=$grp3&amp;percg1=$per1&amp;percg2=$per2&amp;percg3=$per3&amp;perctot=$perctot&amp;titperc=$titoloperc&amp;logo=$logo'  alt=\"Grafico\" /></td></tr></table>";


}

$zz=0;
if (!$pags) sceglicons();
elseif($pag<=$pags) {$zz=scegliliste();	
}

if($zz !=0) {
	for ($x=1;$x<$pags;$x++){
		$ar1[$x]=0;
		$ar2[$x]=0;
		$ar3[$x]=0;
		$id_cons2=$_SESSION['confr'][$x];
		foreach ($_SESSION['grp1'][$x] as $key=>$val){
			if($_SESSION['grulis'][$id_cons2]=='1')
				$sql="SELECT sum(voti) from ".$prefix."_ele_voti_gruppo where id_gruppo='$val'";
			else
				$sql="SELECT sum(voti) from ".$prefix."_ele_voti_lista where id_lista='$val'";
			$res = $dbi->prepare("$sql");
			$res->execute();

			list($voti)=$res->fetch(PDO::FETCH_NUM);
			$ar1[$x]+=$voti;
		}
		foreach ($_SESSION['grp2'][$x] as $key=>$val){
			if($_SESSION['grulis'][$id_cons2]=='1')
				$sql="SELECT sum(voti) from ".$prefix."_ele_voti_gruppo where id_gruppo='$val'";
			else
				$sql="SELECT sum(voti) from ".$prefix."_ele_voti_lista where id_lista='$val'";
			$res = $dbi->prepare("$sql");
			$res->execute();

			list($voti)=$res->fetch(PDO::FETCH_NUM);
			$ar2[$x]+=$voti;
		}
		foreach ($_SESSION['grp3'][$x] as $key=>$val){
			if($_SESSION['grulis'][$id_cons2]=='1')
				$sql="SELECT sum(voti) from ".$prefix."_ele_voti_gruppo where id_gruppo='$val'";
			else
				$sql="SELECT sum(voti) from ".$prefix."_ele_voti_lista where id_lista='$val'";
			$res = $dbi->prepare("$sql");
			$res->execute();

			list($voti)=$res->fetch(PDO::FETCH_NUM);
			$ar3[$x]+=$voti;
		}
	}
	outgraf($ar1,$ar2,$ar3);

}

?>
