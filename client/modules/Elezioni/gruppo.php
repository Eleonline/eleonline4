<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it                     rgigli@libero.it               */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}
if ($xls=='1' or $pdf=='1') {
include_once("modules/Elezioni/language/lang-$lang.php");
include_once("modules/Elezioni/funzioni.php");
}
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ?
	$_GET : $_POST;

if (isset($param['id_cons_gen'])) $id_cons_gen=intval($param['id_cons_gen']); else $id_cons_gen='';
//if (isset($param['op'])) $op=$param['op']; else $op='';
if (isset($param['minsez'])) $minsez=intval($param['minsez']); else $minsez='';
if (isset($param['id_lista'])) $id_lista=intval($param['id_lista']); else $id_lista='';
if (isset($param['id_circ'])) $id_circ=intval($param['id_circ']); else $id_circ='';
if (isset($param['csv'])) $csv=intval($param['csv']); else $csv='';
if (isset($param['min'])) $min=intval($param['min']); else $min= 0;
if (isset($param['orvert'])) $orvert=intval($param['orvert']); else $orvert='';
if (isset($param['grupposg']) and $param['grupposg']) $grupposg=TRUE; else $grupposg=FALSE;
if (isset($param['offset'])) $offset=intval($param['offset']); else $offset='';
if (isset($param['offsetsez'])) $offsetsez=intval($param['offsetsez']); else $offsetsez='';
if (isset($param['perc'])) $perc=$param['perc']; else $perc='';
if (isset($param['info'])) $info=addslashes($param['info']); else $info='';
if (isset($param['files'])) $files=addslashes($param['files']); else $files='';
if (isset($param['voti_lista'])) $voti_lista=intval($param['voti_lista']); else $voti_lista= 0;
if (isset($param['perc_lista'])) $perc_lista=$param['perc_lista']; else $perc_lista= 0;
if (isset($param['lettera'])) $lettera=addslashes($param['lettera']); else $lettera='';
if (isset($param['id_gruppo'])) $id_gruppo=intval($param['id_gruppo']); else $id_gruppo='';
#if (isset($param['tipo_cons'])) $tipo_cons=intval($param['tipo_cons']); else $tipo_cons='';
if (isset($param['pdf'])) $pdf=intval($param['pdf']); else $pdf='';
if (isset($param['orienta'])) $orienta=addslashes($param['orienta']); else $orienta='';
#if (isset($param['datipdf'])) $datipdf=addslashes($param['datipdf']); else $datipdf='';
if (isset($param['formato'])) $formato=addslashes($param['formato']); else $formato='';
# anti-xss nov. 2009 
$id_comune=htmlentities($id_comune);
$id_comune=intval($id_comune);
$perc=htmlentities($perc);
$perc_lista=floatval($perc_lista);
if(isset($_SESSION['datipdf'])) $datipdf= $_SESSION['datipdf'];
$op= htmlentities($op);
$info= htmlentities($info);
$files=htmlentities($files);
$lettera=htmlentities($lettera);
$orienta=htmlentities($orienta);
$formato=htmlentities($formato);
global $id_cons;


$test=phpversion();
if($test>=5.6)
	include("crea_paginaphp7.php");
else
	include("crea_paginaphp5.php");

$sql="SELECT descrizione from  ".$prefix."_ele_comuni where id_comune='$id_comune'" ;
$res = $dbi->prepare("$sql");
$res->execute();

list($descr_comune) = $res->fetch(PDO::FETCH_NUM);

$sql="SELECT t1.descrizione, t1.tipo_cons,t2.genere, t2.voto_g, t2.voto_l, t2.voto_c, t2.circo FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_tipo as t2 where t1.tipo_cons=t2.tipo_cons and t1.id_cons_gen='$id_cons_gen' ";
$res = $dbi->prepare("$sql");
$res->execute();

list($descr_cons,$tipo_cons,$genere,$votog,$votol,$votoc,$circo) = $res->fetch(PDO::FETCH_NUM);
$sql="SELECT t2.id_cons,t2.solo_gruppo,t2.disgiunto FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_cons_gen='$id_cons_gen' and t2.id_comune='$id_comune'" ;
$res = $dbi->prepare("$sql");
$res->execute();

list($id_cons,$dettnulli,$disgiunto) = $res->fetch(PDO::FETCH_NUM);






////////////////////////////////////////////////////////////
//   Visualizza i dati per liste, gruppi e referendum, per sezione o circoscrizione
////////////////////////////////////////////////////////////

function gruppo_circo(){
	global $prefix, $dbi, $descr_cons, $id_cons, $id_cons_gen,$tipo_cons,$votog,$votol,$votoc,$circo, $genere,$id_gruppo,$id_lista,$bgcolor1,$bgcolor2,$id_comune,$descr_comune,$id_circ;
	global $id_comune,$id_cons_gen,$op,$minsez,$id_lista,$id_circ,$csv,$min,$orienta,$formato,$dettnulli,$disgiunto,
	$orvert,$grupposg,$offset,$offsetsez,$perc,$info,$files,$nomefile,$datipdf;
	$nomefile="";
	#Denominazione pagine
	if($op=="gruppo_circo") $pagina=_GRUPPO." "._PER." "._CIRCO;
	if($op=="gruppo_sezione") $pagina=_GRUPPO." "._PER." "._SEZIONI;
	if($op=="lista_circo") $pagina=_LISTA." "._PER." "._CIRCO;
	if($op=="lista_sezione") $pagina=_LISTA." "._PER." "._SEZIONI;
	if($op=="candidato_circo") $pagina=_CONSI." "._PER." "._CIRCO;
	if($op=="candidato_sezione") $pagina=_CONSI." "._PER." "._SEZIONI;
	if($op=="consiglieri") $pagina=_CALCONS;
	if (strstr( $op,'circo')) { //$op=='gruppo_circo' or $op=='lista_circo') {
		$tab1="circ";
		$tab2="t5.num_circ,t5.descrizione";
		$tab3="t5.num_circ";
		$tipo1=_DA." "._CIRCO;
		$tipo2=_CIRCOS;
		$tipo3=_CIRCO;
	}else{
		$tab1="sez";
		$tab2="t3.num_sez,''";
		$tab3="t3.num_sez";
		$tipo1=_DA." "._SEZIONE;
		$tipo2=_SEZIONI;
		$tipo3=_SEZIONE;
	}
	if (strstr( $op,"gruppo")){
		$tab="gruppo";
	}elseif (strstr( $op,'lista')) {	
		$tab="lista";
	}else{
		$tab="candidati";
	}
	if ($orvert) {
		$righe='';
		$colonne='checked';
	}else{
		$righe='checked';
		$colonne='';
	}
	if ($grupposg) {
		$vissg='';
		$novissg='checked';
	}else{
		$vissg='checked';
		$novissg='';
	}		
	if ($orienta) {
		$land='';
		$port='checked';
	}else{
		$land='checked';
		$port='';
	}
	if ($formato) {
		$A3='';
		$A4='checked';
	}else{
		$A3='checked';
		$A4='';
	}



	if ($genere>0) {       //se non e' un referendum
		
		
		$voticompl=0;
		if (!($offset)) $offset=25;
		if (!($min)) $min=1;
		if (!($offsetsez)) $offsetsez=22; 
		if (!($minsez)) $minsez=1;
		if ($min>$offset) { 
			$appo=$min;
			$min=$offset;
			$offset=$appo;
		}
		if ($minsez>$offsetsez) { 
			$appo=$minsez;
			$minsez=$offsetsez;
			$offsetsez=$appo;
		}
		if (!$csv){
			echo "<form id=\"voti\" method=\"post\" action=\"modules.php\">";
			echo "<div><input type=\"hidden\" name=\"pag\" value=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=$id_circ&amp;id_lista=\"></input>";
			echo "<input type=\"hidden\" name=\"pagina\" value=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=\"></input>";
		}
		$condcirc='';
		$condcircns="";
		if ($circo){ //gestione circoscrizionali
			if(!$id_circ){
				$sql="SELECT id_circ from ".$prefix."_ele_circoscrizione where id_cons=$id_cons and num_circ=1";
				$res_cir = $dbi->prepare("$sql");
				$res_cir->execute();
	 //se non si e' scelta una circoscr. prende la prima
				list($id_circ)=$res_cir->fetch(PDO::FETCH_NUM);
			}
			$sql="SELECT num_circ from ".$prefix."_ele_circoscrizione where id_circ=$id_circ";
			$res_cir = $dbi->prepare("$sql");
			$res_cir->execute();
 //estrae il numero della circoscrizione
			list($num_circ)=$res_cir->fetch(PDO::FETCH_NUM);
			$condcirc="and id_circ=$id_circ";  //variabile aggiunta nelle select per le circ.
			$condcircns="and t2.id_circ=$id_circ";			
			$sql="SELECT count(t1.num_sez),min(t1.num_sez),max(t1.num_sez) from ".$prefix."_ele_sezioni as t1, ".$prefix."_ele_sede as t2 where t1.id_cons=$id_cons and t1.id_sede=t2.id_sede and t2.id_circ=$id_circ";
			$res_sez = $dbi->prepare("$sql");
			$res_sez->execute();
 //numero di sezioni nella circoscrizione
			$sql="SELECT min(t1.num_sez) from ".$prefix."_ele_sezioni as t1, ".$prefix."_ele_sede as t2 where t1.id_cons=$id_cons and t1.id_sede=t2.id_sede and t2.id_circ=$id_circ";
			$res_min = $dbi->prepare("$sql");
			$res_min->execute();
 //setta minsez sulla prima sezione della circoscrizione
			list($minsez)=$res_min->fetch(PDO::FETCH_NUM);
		}
		elseif (strstr( $op,'circo')){
			$sql="SELECT count(num_circ),min(num_circ),max(num_circ) from ".$prefix."_ele_circoscrizione where id_cons=$id_cons  $condcirc";
			$res_sez = $dbi->prepare("$sql");
			$res_sez->execute();
		}
		else{
			$sql="SELECT count(num_sez),min(num_sez),max(num_sez) from ".$prefix."_ele_sezioni where id_cons=$id_cons $condcirc";
			$res_sez = $dbi->prepare("$sql");
			$res_sez->execute();
		}
		if($res_sez->rowCount()) list($tot_sez,$numsezmin,$numsezmax)=$res_sez->fetch(PDO::FETCH_NUM); 
		else {$tot_sez=0;$numsezmin=0;$numsezmax=0;}
		$sql="SELECT count(t1.num_sez) from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons=$id_cons $condcircns";
		$res_sez = $dbi->prepare("$sql");
		$res_sez->execute();
		
		list($tuttelesez)=$res_sez->fetch(PDO::FETCH_NUM); 

		$num_sez=$tot_sez;//mysql_data_seek($res_sez,0);
		if ($circo) $offsetsez=$numsezmax; //$num_sez+$minsez-1;//setta offsetsez sull'ultima sezione della circoscrizione

		$visvot='';

		if (!$csv) echo "<div><h5>$pagina</h5></div>";
		if (!$csv) echo "<div style=\" text-align:left; margin-left:20px;\">";

		if(strstr( $op,'candidato')){
//			$numliste=mysql_num_rows($res_lis);
			$visvot="cand";
			if (!$csv){
				$sql="SELECT id_lista, descrizione,num_lista from ".$prefix."_ele_lista where id_cons=$id_cons $condcirc order by num_lista";
				$res_lis = $dbi->prepare("$sql");
				$res_lis->execute();

				//elenco delle liste per la scelta
				echo "<p>"._SCEGLI_LISTA.": 
				<select name=\"id_lista\" class=\"modulo\" onChange=\"top.location.href=this.form.pag.value+this.form.id_lista.options[this.form.id_lista.selectedIndex].value;return false\">";
				while(list($id_rif,$descrizione,$num_lis) = $res_lis->fetch(PDO::FETCH_NUM)) {
					if (!$id_lista) $id_lista=$id_rif;
					$sel = ($id_rif == $id_lista) ? "selected=\"selected\"" : "";
					echo "<option value=\"$id_rif\" $sel>";
					for ($j=strlen($num_lis);$j<2;$j++) { echo "&nbsp;&nbsp;";}
					echo $num_lis.") ".$descrizione."</option>";
				}
				echo "</select></p>";
			}
			$sql="SELECT count(t1.id_sez) from ".$prefix."_ele_voti_$tab as t1, ".$prefix."_ele_$tab as t2 where t2.id_lista=$id_lista and t1.id_cand=t2.id_cand group by t1.id_cand";
			$res_scr = $dbi->prepare("$sql");
			$res_scr->execute();

			$sqlc="SELECT id_cand, concat(cognome,' ', nome), num_cand from ".$prefix."_ele_$tab where id_cons=$id_cons and id_lista=$id_lista order by num_cand";
			$res_cand = $dbi->prepare("$sqlc");
			$res_cand->execute();

			if ($circo) {$condcirc="and t5.id_circ=$id_circ";$condcircdes="and t2.id_circ=$id_circ";}
			$sqldesc="select '','', t1.num_cand, concat(t1.cognome,' ', t1.nome) as nome, '','','','','','','' 
			from ".$prefix."_ele_candidati as t1, ".$prefix."_ele_lista as t2
			where t1.id_lista=$id_lista
			and t1.id_lista=t2.id_lista
			and t1.id_cons=$id_cons
			order by t1.num_cand";

			$sqlvoti="select $tab2, t1.num_cand, concat(t1.cognome,' ', t1.nome) as nome, sum(t2.voti),'','','','','','' 
			from ".$prefix."_ele_candidati as t1, ".$prefix."_ele_voti_candidati as t2, "
			.$prefix."_ele_sezioni as t3, ".$prefix."_ele_sede as t4, ".$prefix."_ele_circoscrizione as t5
			where t1.id_lista=$id_lista
			and t1.id_cons=$id_cons
			and t1.id_cand=t2.id_cand
			and t2.id_sez=t3.id_sez
			and t3.id_sede=t4.id_sede
			and t4.id_circ=t5.id_circ $condcirc
			group by t1.num_cand,$tab2, t1.cognome,t1.nome
			order by $tab3,t1.num_cand";
			$res_voti = $dbi->prepare("$sqlvoti");
			$res_voti->execute();
		}else{
			if (!$csv)
				echo "<input type=\"hidden\" name=\"id_lista\" value=\"\"></input>";
				
			// camera e senato nel 2006 aggiunte le somme della coalizione
			// divise per circo e sez. in quanto nella tabella del gruppo
			// all'atto dell'immsione non viene fatta la somma
			// quindi leggere prima i voti di lista e poi agganciali al gruppo
			// la var $tab diviene lista, $tab15 diviene gruppo in caso di somma
			// dei voti di lista...oltre naturalmnte alle condizioni messe in variabile
			// 4 aprile 2006 by luc
#echo "TEST; Condizione scelta lista/gruppo: if ($votog && $tab==gruppo)<br>circo: $circo -- condcirc: $condcirc<br>";
			$tabns=$tab; 
			$ecampo=",''";
			if ($votog && $tab=="gruppo"){ // camera e senato 2006
				$t="t9";
				$tab="lista";
				$tabns=$tab;
				$tab15="gruppo";
				$add_1= ",".$prefix."_ele_gruppo as t9";
				$and_1="and t1.id_gruppo=t9.id_gruppo";
			}else{
				if($dettnulli and $tab=="gruppo") $ecampo=",sum(t2.solo_gruppo)";
				$t="t1";
				$tab15=$tab;
				$add_1='';
				$and_1='';
			}
			if(($genere==5 and $circo)) $condcircns="and ".$prefix."_ele_$tabns.id_circ=$id_circ";	else $condcircns="";
			// fine della modifica
			$sql="SELECT count(".$prefix."_ele_voti_$tabns.id_sez) from ".$prefix."_ele_voti_$tabns left join ".$prefix."_ele_$tabns on ".$prefix."_ele_voti_$tabns.id_$tabns=".$prefix."_ele_$tabns.id_$tabns where ".$prefix."_ele_voti_$tabns.id_cons='$id_cons' $condcircns group by ".$prefix."_ele_voti_$tabns.id_$tabns"; 
//echo "TEST:	numero sezioni scrutinate: $sql";		 
			$res_scr = $dbi->prepare("$sql");
			$res_scr->execute();
 //numero sezioni scrutinate
			$sqlc="SELECT id_$tab15, descrizione, num_$tab15 from ".$prefix."_ele_$tab15 where id_cons='$id_cons' $condcirc order by num_$tab15";
			$res_cand = $dbi->prepare("$sqlc");
			$res_cand->execute();

			if ($circo) $condcirc="and t5.id_circ=$id_circ";			
			if ($tab=="gruppo" or $genere==4 or ($genere==5 and $votog) or defined('_NULLISTA'))
					$votigl=" sum(t3.validi),sum(t3.nulli),sum(t3.bianchi),sum(t3.contestati),sum(t3.voti_nulli)";
			else
				if ($votog) $votigl=" (t3.validi_lista),(t3.nulli),(t3.bianchi),(t3.contestati),(t3.voti_nulli_lista+t3.voti_nulli)";
				else $votigl=" sum(t3.validi_lista),sum(t3.nulli),sum(t3.bianchi),sum(t3.contestati_lista),sum(t3.voti_nulli_lista+t3.voti_nulli)";

			$sqldesc="select '','', $t.num_$tab15, $t.descrizione, '', '','','','',''
			from 
			".$prefix."_ele_$tab as t1 			
			where t1.id_cons=$id_cons 
			$condcirc
			order by t1.num_$tab15";

#include("TEST:$sqldesc");
			$sqlvoti="select $tab2, $t.num_$tab15, $t.descrizione, sum(t2.voti), $votigl $ecampo
			from 
			".$prefix."_ele_$tab as t1, 
			".$prefix."_ele_voti_$tab as t2, 
			".$prefix."_ele_sezioni as t3, 
			".$prefix."_ele_sede as t4, 
			".$prefix."_ele_circoscrizione as t5
			$add_1
			
			where t1.id_cons=$id_cons 
			and t1.id_$tab=t2.id_$tab
			$and_1
			
			and t2.id_sez=t3.id_sez
			and t3.id_sede=t4.id_sede
			and t4.id_circ=t5.id_circ $condcirc
			
			
			group by $t.num_$tab15,$tab3,$tab2, $t.descrizione
			order by $tab3,$t.num_$tab15";
			$res_voti = $dbi->prepare("$sqlvoti");
			$res_voti->execute(); 
		}
		if ($res_scr) list($tot_scr)=$res_scr->fetch(PDO::FETCH_NUM);else $tot_scr=0;
		if ($res_cand) $num_cand=$res_cand->rowCount(); else $num_cand=0;
		if(!$circo){ 
			if (!(0 < $minsez and $minsez<=$numsezmax)) $minsez=1;
			if (!(0<$offsetsez and $offsetsez<=$numsezmax)) $offsetsez=$numsezmax;
		}
		if (!(0 < $min and $min<=$num_cand)) $min=1;
		if (!(0<$offset and $offset<=$num_cand)) $offset=$num_cand;
		if (!$csv) {		
			if(strstr( $op,'lista')){$scelta=_SCEGLI_LISTA;}else{$scelta=_SCEGLI_CANDI;}
			echo "<p>$scelta "._DA.":&nbsp;  <select name=\"min\" class=\"modulo\">";
			while(list($id_rif,$descrizione,$num_lis) = $res_cand->fetch(PDO::FETCH_NUM)) {
				if (!$min) $min=$num_lis;
				$sel = ($num_lis == $min) ? "selected=\"selected\"" : "";
				echo "<option value=\"$num_lis\" $sel>";
				for ($j=strlen($num_lis);$j<2;$j++) { echo "&nbsp;&nbsp;";}
				echo $num_lis.") ".$descrizione."</option>";				
			}
			echo "</select>";
			echo "&nbsp;&nbsp;"._A.":&nbsp; <select name=\"offset\" class=\"modulo\"></p>";
			$res_cand = $dbi->prepare("$sqlc");
			$res_cand->execute();

			while(list($id_rif,$descrizione,$num_lis) = $res_cand->fetch(PDO::FETCH_NUM)) {
				if (!$offset) $offset=$num_lis;
				$sel = ($num_lis == $offset) ? "selected=\"selected\"" : "";
				echo "<option value=\"$num_lis\" $sel>";
				for ($j=strlen($num_lis);$j<2;$j++) { echo "&nbsp;&nbsp;";}
				echo $num_lis.") ".$descrizione."</option>";
				
			}
			echo "</select></p>";
			//echo "<div style=\"text-align:left; margin-left:20px;\">";
			if(!$circo){
				echo "<p>"._SCEGLI." $tipo1 n. <input  name=\"minsez\" value=\"$minsez\" size=\"4\" ></input>";
				echo _A." n. <input  name=\"offsetsez\" value=\"$offsetsez\" size=\"4\" ></input> (max. $numsezmax)</p>";
			}
			# pagine
############
			if(!$offsetsez) $offsetsez=0;
			if (strstr( $op,'circo'))
			$sql="SELECT count(num_circ) from ".$prefix."_ele_circoscrizione where id_cons=$id_cons and num_circ>=$minsez and num_circ<=$offsetsez";


			else
			$sql="SELECT count(num_sez) from ".$prefix."_ele_sezioni where id_cons=$id_cons and num_sez>=$minsez and num_sez<=$offsetsez";
				$resnsez = $dbi->prepare("$sql");
				$resnsez->execute();
				list($diff)=$resnsez->fetch(PDO::FETCH_NUM);
			########
			#	$diff=($offsetsez-$minsez);
			if ($minsez>1 and !$circo){
				$minsez_p= ($minsez-$diff)>1 ? $minsez-$diff:1;				 
				$offsetsez_p=$offsetsez-$diff;
				echo "<div style=\"float: right; width:200px;margin-left:10px;margin-right:400px;font-size:12px \"><a href=\"modules.php?name=Elezioni&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;op=$op&amp;min=$min&amp;offset=$offset&amp;minsez=$minsez_p&amp;offsetsez=$offsetsez_p&amp;perc=$perc&amp;grupposg=$grupposg&amp;id_lista=$id_lista\"> <- $tipo2 Precedenti</a></div>";
			}else{echo "";}
			if ($offsetsez<$numsezmax){
				$minsez_s=$minsez+$diff;
				$offsetsez_s= ($offsetsez+$diff)>$num_sez ? $num_sez: $offsetsez+$diff;
				echo "<div style=\"float: left; width:200px; margin-left:10px;\"><a href=\"modules.php?name=Elezioni&amp;file=index&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;orvert=$orvert&amp;grupposg=$grupposg&amp;min=$min&amp;offset=$offset&amp;minsez=$minsez_s&amp;offsetsez=$offsetsez_s&amp;perc=$perc&amp;id_lista=$id_lista\"> $tipo2 Successive -></a></div>";
			}else{echo "";}	
			if(!$circo)				
				echo "<div style=\"margin-left:10px;\"><br/>"._MOSTRA." $tipo2 "._PERCOL."<input type=\"radio\" name=\"orvert\" $righe value=\"0\"></input>"._PERRIGHE." <input
				type=\"radio\" name=\"orvert\" $colonne value=\"1\"></input>";
				
			echo "<input type=\"hidden\" name=\"name\" value=\"Elezioni\"></input></div>";
			echo "<p>";
			if($dettnulli and $tab=="gruppo") {
				echo ""._MOSTRA." "._SOLO_GRUPPO.": <input type=\"checkbox\" name=\"grupposg\" value=\"true\"";
				if($grupposg=='true') echo " checked=\"true\"";
				echo "></input><br/>";
			}			
			echo "</p><p>";
			if (!strstr( $op,'candidato')) {
				echo ""._VIS_PERC.": <input type=\"checkbox\" name=\"perc\" value=\"true\"";
				if($perc=='true') echo " checked=\"true\"";
				echo "></input><br/>";
			}
			echo "</p>";
			if($circo) 
				echo "<input type=\"hidden\" name=\"id_circ\" value=\"$id_circ\"></input>";	
				echo "<input type=\"hidden\" name=\"op\" value=\"$op\"></input>";			
				echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\"></input>";
				echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"></input>";			
	
				echo "<input type=\"hidden\" name=\"pag2\" value=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;csv=1&amp;orvert=$orvert&amp;min=$min&amp;offset=$offset&amp;minsez=$minsez&amp;offsetsez=$offsetsez&amp;perc=$perc&amp;id_lista=\"></input>";	

				 echo " <input type=\"submit\" name=\"update\" value=\""._RICARICA."\"></input></p></div></form>"; 


				#### recupera dati stampa supporti diversi dati
				//echo "</tr><tr><td><b>"._COMUNE." $descr_comune</b> - "._RISULTATI.": $descr_cons <br/>"; 
				//echo "tot:$tot_scr";
				//if ($tipo_cons!=4 && $tot_scr) echo " - Sezioni scrutinate: $tot_scr su $tuttelez";
############## 30/11/24
#########
# verificare la stampa sulle circoscrizioni					
				 echo "<div style=\"text-align:right;width:65%;margin-left:10px;margin-right:0px;font-size:12px; \">";
				 echo "<table style=\"text-align:center;margin-right:0px;border-top : 1px solid Blue;width: 280px;\"><tr style=\" background:#eceff5;\"><td>"._ESPORTA."<br />";
				 if($circo){ echo "<a href=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;csv=1&amp;orvert=$orvert&amp;min=$min&amp;offset=$offset&amp;minsez=$minsez&amp;offsetsez=$offsetsez&amp;perc=$perc&amp;grupposg=$grupposg&amp;id_lista=$id_lista&amp;id_circ=$id_circ\"  target=\"_blank\"><img class=\"image\"  src=\"modules/Elezioni/images/printer.gif\" alt=\"Stampa\" /></a>";
				}else{ 
					echo "<a href=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;csv=1&amp;orvert=$orvert&amp;min=$min&amp;offset=$offset&amp;minsez=$minsez&amp;offsetsez=$offsetsez&amp;perc=$perc&amp;grupposg=$grupposg&amp;id_lista=$id_lista\" target=\"_blank\"><img class=\"image\"  src=\"modules/Elezioni/images/printer.gif\" alt=\"Stampa\" /></a>";
					echo "<a href=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;csv=1&amp;orvert=$orvert&amp;min=$min&amp;offset=$offset&amp;minsez=$minsez&amp;offsetsez=$offsetsez&amp;perc=$perc&amp;grupposg=$grupposg&amp;id_lista=$id_lista&amp;xls=1\" ><img class=\"image\"  src=\"modules/Elezioni/images/csv.gif\" alt=\"Export Csv\" /></a>";
					echo "<img class=\"image\"  src=\"modules/Elezioni/images/rss.png\" alt=\"Export rss\" />";				
					echo "	</td>";				
	# stampa pdf
					echo "<td>";
					echo "<form id=\"pdf\" method=\"post\" action=\"modules.php\">";					
					echo "<input type=\"hidden\" name=\"id_circ\" value=\"$id_circ\"></input>";	
					echo "<input type=\"hidden\" name=\"op\" value=\"$op\"></input>";			
					echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\"></input>";
					echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"></input>";	
					echo "<input type=\"hidden\" name=\"csv\" value=\"1\"></input>";
					echo "<input type=\"hidden\" name=\"orvert\" value=\"$orvert\"></input>";
					echo "<input type=\"hidden\" name=\"min\" value=\"$min\"></input>";					
			        echo "<input type=\"hidden\" name=\"offset\" value=\"$offset\"></input>";
					echo "<input type=\"hidden\" name=\"minsez\" value=\"$minsez\"></input>";	
					echo "<input type=\"hidden\" name=\"offsetsez\" value=\"$offsetsez\"></input>";		
					echo "<input type=\"hidden\" name=\"perc\" value=\"$perc\"></input>";	
					echo "<input type=\"hidden\" name=\"grupposg\" value=\"$grupposg\"></input>";	
					echo "<input type=\"hidden\" name=\"id_lista\" value=\"$id_lista\"></input>";
					echo "<input type=\"hidden\" name=\"pdf\" value=\"1\"></input>";
#					echo "<input type=\"hidden\" name=\"datipdf\" value=\"$datipdf\"></input>";
	#				echo "<input type=\"hidden\" name=\"name\" value=\"$elezioni\"></input>";			
					echo "<input type=\"hidden\" name=\"name\" value=\"Elezioni\"></input>";
					echo "<input type=\"image\" name=\"submit\" src=\"modules/Elezioni/images/pdf.gif\" align=\"left\">";					
					echo "&nbsp; L &nbsp;<input type=\"radio\" name=\"orienta\" $land value=\"L\"></input>P &nbsp;<input
					type=\"radio\" name=\"orienta\" $port value=\"P\"></input><br />";
					echo "&nbsp; A3<input type=\"radio\" name=\"formato\" $A3 value=\"A3\"></input>A4<input
					type=\"radio\" name=\"formato\" $A4 value=\"A4\"></input>";					
					echo "	</form></td></tr></table> ";
				}
				echo "</div></br />";
			}		
				# liste e gruppi da.... a	  
				if (!strstr( $op,'candidato')) {
				    $sql="SELECT descrizione from ".$prefix."_ele_$tab15 where id_cons='$id_cons' and num_$tab15 ='$min'";
					$res_cand2 = $dbi->prepare("$sql");
					$res_cand2->execute();

				      list($descrizione)= $res_cand2->fetch(PDO::FETCH_NUM);
				      $list1 ="da $descrizione ";  



				      $sql="SELECT descrizione from ".$prefix."_ele_$tab15 where id_cons='$id_cons' and num_$tab15 ='$offset'";
						$res_cand3 = $dbi->prepare("$sql");
						$res_cand3->execute();

				      list($descrizione)= $res_cand3->fetch(PDO::FETCH_NUM);
				      $list1 .=" a $descrizione <br/>";
			      
				}else{$list1='';}

				# nome della lista
				if (!isset($list2)) $list2='';
				if (strstr( $op,'candidato')) { 
					$sql="SELECT num_lista, descrizione from ".$prefix."_ele_lista where id_lista=$id_lista";
					$res_lis2 = $dbi->prepare("$sql");
					$res_lis2->execute();

					list($num_lista2,$descr_lista2)= $res_lis2->fetch(PDO::FETCH_NUM);
					$nomefile=" Lista n. $num_lista2 - $descr_lista2 ";
					$list2 .=" Lista n. $num_lista2 - $descr_lista2 <br/>";
				
					$sql="SELECT concat(cognome,' ', nome) from ".$prefix."_ele_$tab where id_cons=$id_cons and id_lista=$id_lista and num_cand=$min";
					$res_cand4 = $dbi->prepare("$sql");
					$res_cand4->execute();

					list($descrizione)= $res_cand4->fetch(PDO::FETCH_NUM);
				         $list3 ="da $descrizione "; 
					$sql="SELECT concat(cognome,' ', nome) from ".$prefix."_ele_$tab where id_cons=$id_cons and id_lista=$id_lista and num_cand=$offset";
					$res_cand5 = $dbi->prepare("$sql");
					$res_cand5->execute();

					list($descrizione)= $res_cand5->fetch(PDO::FETCH_NUM);
				         $list3 .="a $descrizione <br/>";

				}else{ $list2 .='';$list3='';}
			if(!isset($tab15)) $tab15='';
			$nomefile.="$descr_cons $tab15";
			$_SESSION['nomefile']=strip_tags(str_replace(" ", "_", $nomefile));
			$datipdf="<table style=\"text-align:center;margin-right:0px;border-top : 1px solid Blue;width: 800px;\"><tr style=\" background:#eceff5;\"><td>";
			$datipdf.="<b><h2>"._COMUNE." $descr_comune</h2></b></td></tr><tr><td>"._RISULTATI.": $descr_cons</td></tr><tr><td><b>$pagina</b></td></tr><tr><td>$list1 $list2 $list3</td></tr></table>";
				$datipdf="<b>"._COMUNE." $descr_comune</b> - "._RISULTATI.": $descr_cons<br/><b>$pagina</b><br/><br/> ";
				$datipdf .="<br/><b>$list1 $list2 $list3</b>";
	            $datipdf=str_replace('"',"'",$datipdf);

			$_SESSION['datipdf']= $datipdf;
			if (!$csv){
				echo "<h5> Sezioni scrutinate";
				if ($tipo_cons!=4) ;echo ": $tot_scr su $tuttelesez &nbsp;&nbsp;&nbsp;  ";
					echo "</h5>&nbsp;";
			}
			$y=1;
			$ar[0][0]=$tipo3;
			$ra[0][0]=$tipo3;
			$num_sez++;
			$voticompl=0;
			$ominsez=$minsez-1;
			$valar=array();$percar=array();
		      ////////////////////////////////////////////////////////////////////
		      // sandro: carica i numeri di sezione dal DB - giugno 2009
		      // caso: sezioni in collegi diversi non consecutive
			if (strstr( $op,'circo'))
				$sql="select num_circ,id_circ from ".$prefix."_ele_circoscrizione where id_cons='$id_cons' and num_circ>='$minsez' and num_circ<='$offsetsez' order by num_circ";
			else{		      
			if($circo) { $secirco=" and t2.id_circ=$id_circ";} else $secirco="and t1.num_sez >= $minsez and t1.num_sez <= $offsetsez";
				$numsezioni = $offsetsez-$ominsez;
				$sql="SELECT t1.num_sez,t1.id_sez from ".$prefix."_ele_sezioni as t1, ".$prefix."_ele_sede as t2 where t1.id_cons=$id_cons and t1.id_sede=t2.id_sede $secirco order by t1.num_sez";
			}
			$res_numsez = $dbi->prepare("$sql");
			$res_numsez->execute();
			$sevaltot=0;
			$senultot=0;
			$sebiatot=0;
			$secontot=0;
			$sevnutot=0;
			while($res=$res_numsez->fetch(PDO::FETCH_NUM)) {
				$z=$res[0];
##############inserimento percentuale di scrutinio nella sezione
				$scruvalidi=0;
				$scrunulli=0;
				$grpercscru=0;
				$altri=0;
				$votiscru=0;
				if($op=="gruppo_sezione"){
					$sqlr="select sum(validi),sum(solo_lista) from ".$prefix."_ele_sezioni where id_sez='".$res[1]."'";
					$sqlv="select sum(voti) from ".$prefix."_ele_voti_gruppo where id_sez='".$res[1]."'";
					$resperc = $dbi->prepare("$sqlr");
					$resperc->execute();
					$votiperc = $dbi->prepare("$sqlv");
					$votiperc->execute();
				}	
				elseif($op=="lista_sezione"){
					$sqlr="select sum(validi),sum(contestati_lista+solo_gruppo+voti_nulli_lista) from ".$prefix."_ele_sezioni where id_sez='".$res[1]."'";
					$sqlv="select sum(voti) from ".$prefix."_ele_voti_lista where id_sez='".$res[1]."'";
					$resperc = $dbi->prepare("$sqlr");
					$resperc->execute();
					$votiperc = $dbi->prepare("$sqlv");
					$votiperc->execute();
				}	
				if (isset($resperc) and $resperc)
					list($scruvalidi,$scrunulli)=$resperc->fetch(PDO::FETCH_NUM);
				if (isset($votiperc) and $votiperc)
					list($votiscru)=$votiperc->fetch(PDO::FETCH_NUM);
				$sql="select sum(voti_complessivi) from ".$prefix."_ele_voti_parziale where id_sez='".$res[1]."' group by data,orario order by data desc, orario desc limit 0,1 ";
				$resperc = $dbi->prepare("$sql");
				$resperc->execute();

				if ($resperc)
					list($totschede)=$resperc->fetch(PDO::FETCH_NUM);
				if (isset($scruvalidi) and $scruvalidi>0 )
					$grpercscru=$votiscru ? number_format(($votiscru+$scrunulli)*100/$scruvalidi,0) : 0;

###################			<span class=\"red\"><i>".$temp3[$key]." %</i></span>		
				$ar[$z][0]=$res[0]; if($grpercscru) {$ar[$z][0].="<br /><span class=\"red\"><i>$grpercscru%</i></span>";if(!strstr($ar[0][0],'scrutinio')) $ar[0][0].="<br /><span class=\"red\"><i>% scrutinio</i></span>"; }
				$pos[$z]=$res[0];
				#$valar[$z]=array();
			}
			if (!isset($pos)) $pos[0]=0;
			$minpos=min($pos);
			$maxpos=max($pos);
			////////////////////////////////////////////////////////////////////
			if ($tab=="gruppo" or $genere==4 or ($genere==5 and $votog) or defined('_NULLISTA'))
					$sqlvoti2="select $tab3, sum(t3.validi),sum(t3.nulli),sum(t3.bianchi),sum(t3.contestati),sum(t3.voti_nulli) from ".$prefix."_ele_circoscrizione as t5, ".$prefix."_ele_sezioni as t3, ".$prefix."_ele_sede as t4 where t5.id_cons=$id_cons and t5.id_circ=t4.id_circ and t3.id_sede=t4.id_sede group by $tab3";
			else
				if (strstr( $op,'circo')) $sqlvoti2="select t1.num_circ, sum(t3.validi_lista),sum(t3.voti_nulli_lista),sum(t3.contestati_lista),sum(t3.solo_gruppo),sum(t3.voti_nulli_lista+t3.voti_nulli) from ".$prefix."_ele_circoscrizione as t1, ".$prefix."_ele_sezioni as t3, ".$prefix."_ele_sede as t4 where t1.id_cons=$id_cons and t1.id_circ=t4.id_circ and t3.id_sede=t4.id_sede group by t1.num_circ";
# togli circo vai con modo tab3, genere=2 e =4 prende stringa madre (poi va sistemato il tipo camera 2001 che registra i voti di lista sui gruppi
# camera 2008 e 2001 --> tipo cons=6 non funziona

                else $sqlvoti2="select t3.num_sez, (t3.validi_lista),(t3.voti_nulli_lista),(t3.contestati_lista),(t3.solo_gruppo),(t3.voti_nulli_lista+t3.voti_nulli) from ".$prefix."_ele_sezioni as t3 where t3.id_cons=$id_cons ";
			$res_voti = $dbi->prepare("$sqlvoti2");
			$res_voti->execute();
#			echo "TEST: ---- $sqlvoti2";	
			///////////////////////////
			if ($res_voti->rowCount())
			while (list($num_circ,$sevalidi,$senulli,$sebianchi,$secontestati,$sevonulli) = $res_voti->fetch(PDO::FETCH_NUM)){
#				if($genere==4) $sevalidi=$voti;
				$z=array_search($num_circ, $pos); 
				if (!isset($votitot[($z)])) {
					$votitot[($z)]=0; 
					if($sevalidi) $sevaltot+=$sevalidi;
					if($senulli) $senultot+=$senulli;
					if($sebianchi) $sebiatot+=$sebianchi;
					if($secontestati) $secontot+=$secontestati;
					if($sevonulli) $sevnutot+=$sevonulli;
				}
#				$votitot[($z)]+=$voti;
#				if(!isset($valsez[$z])) $valsez[$z]=0;
				$valsez[$z]=$sevalidi;
				$nulsez[$z]=$senulli;
				$biasez[$z]=$sebianchi;
				$consez[$z]=$secontestati;
				$vonsez[$z]=$sevonulli;
#				$voticompl+=$voti;
			}
#			if ($voticompl) {
				$res_voti = $dbi->prepare("$sqlvoti");
				$res_voti->execute();
#			}
			$piuvot=0;                    

			if ($visvot!='cand') if($tab15=="gruppo" or $genere==4 or ($genere==5 and $votog) or defined('_NULLISTA')) $piuvot=5; else $piuvot=4;
			for ($y=$min;$y<=($offset+$piuvot);$y++) $ar[0][$y]="&nbsp;";
			if (strstr( $op,'circo'))
				$sql="select num_circ from ".$prefix."_ele_circoscrizione where id_cons='$id_cons' and num_circ>='$minsez' and num_circ<='$offsetsez' order by num_circ";
			else
				$sql="select num_sez from ".$prefix."_ele_sezioni where id_cons='$id_cons' and num_sez>='$minsez' and num_sez<='$offsetsez' order by num_sez";
			$lis_sez = $dbi->prepare("$sql");
			$lis_sez->execute();
			$nsezsel=$lis_sez->rowCount();
			while(list($z)=$lis_sez->fetch(PDO::FETCH_NUM)) 
			{ 
				for ($y=$min;$y<=($offset+$piuvot);$y++) $ar[$z][$y]="&nbsp;";} //inizializza le celle interne

###################################################################
#			for ($y=$min;$y<=($offset+$piuvot);$y++) $ar[0][$y]="&nbsp;";
#			for ($z=1;$z<=($offsetsez-$ominsez);$z++)
 #				for ($y=$min;$y<=($offset+$piuvot);$y++) $ar[$z][$y]="&nbsp;"; //inizializza le celle interne
			$onetime=""; 
			if ($res_voti)
			{
				while (list($num_circ,$desc_circ,$num_cand,$nome,$voti,$sevalidi,$senulli,$sebianchi,$secontestati,$sevonulli) = $res_voti->fetch(PDO::FETCH_NUM)){
					if(!isset($votitot[$z])) $votitot[$z]=0;
					$z=array_search($num_circ, $pos);
					if ($z) {$valar[($z)][$num_cand]=$voti; $votitot[($z)]+=$voti;}						
				}
#echo "TEST: fase 1 num rec: su $z - $num_cand voti:".$voti."<br>";
				foreach ($valar as $key=>$val){ 
					if(isset($votitot[($key)]))
						$percar[$key]=arrayperc($val,$votitot[($key)]);
						
				} 
				$res_voti = $dbi->prepare("$sqlvoti");
				$res_voti->execute();
				if (!$res_voti->rowCount()) { 
					$res_voti = $dbi->prepare("$sqldesc");
					$res_voti->execute();
				}	
				
				while (list($num_circ,$desc_circ,$num_cand,$nome,$voti,$sevalidi,$senulli,$sebianchi,$secontestati,$sevonulli,$votisg) = $res_voti->fetch(PDO::FETCH_NUM)){ 
					if($num_cand<$min or $num_cand>$offset) continue;
				if($genere==4) $sevalidi=$voti;

					if (!isset($temp[$num_cand])) $temp[$num_cand]=0;
					$temp[$num_cand]+=intval($voti);
					if (!isset($tempsg[$num_cand])) $tempsg[$num_cand]=0;
					$tempsg[$num_cand]+=intval($votisg);
					$z=array_search($num_circ, $pos); 
					if (!$z) $z=$minpos; 
					if ($num_cand>=$min and $num_cand<=$offset){
						if(($num_circ>=$minpos and $num_circ <=$maxpos) or $num_circ==''){
							$ar[0][$num_cand]=$num_cand.") ".$nome;
							if ($desc_circ && $onetime!=$desc_circ) {$ar[($z)][0].=") ".$desc_circ; $onetime=$desc_circ;}
							$percento=$voti;
							if ($perc=='true' and $voti) 
							{
								$percento.="<br /><span class=\"red\" style=\"font-size:80%;\"><i>".number_format($percar[$z][$num_cand],2)." %</i></span>";
							}
							if($grupposg) $percento.="<br /><span style=\"color:blue;font-size:80%;\">".$votisg." </span>";
							$ar[($z)][$num_cand]=$percento;
						}	
					}
					if (!strstr( $op,'candidato')){
						if (!isset($tempar[$num_cand])) $tempar[$num_cand]=0;
						$tempar[$num_cand]+=intval($voti); #die("TEST: $id_lista");
					}
					if ($visvot!='cand'){ 
						$posvoti=($offset);
					if($tab15=="gruppo" or $genere==4 or ($genere==5 and $votog) or defined('_NULLISTA')){
							$ar[0][$posvoti+1]='<b>Voti Validi</b>';
							$ar[0][$posvoti+2]='<b>Schede Nulle</b>';
							$ar[0][$posvoti+3]='<b>Schede Bianche</b>';
							$ar[0][$posvoti+4]='<b>Voti Contestati</b>';
							$ar[0][$posvoti+5]='<b>Voti Nulli</b>';
					}else{
							$ar[0][$posvoti+1]='<b>Voti Validi</b>';
							$ar[0][$posvoti+2]='<b>Voti Nulli</b>';
							$ar[0][$posvoti+3]='<b>Voti Contestati</b>';
							$ar[0][$posvoti+4]='<b>Solo Gruppo</b>';						
					}
						if (($maxpos)>=$num_circ and $minpos<=$num_circ){ 
						//$posvoti++;
							$ar[($z)][$posvoti+1]="<b>$valsez[$z]</b>";
							$ar[($z)][$posvoti+2]="<b>$nulsez[$z]</b>";
							$ar[($z)][$posvoti+3]="<b>$biasez[$z]</b>";
							$ar[($z)][$posvoti+4]="<b>$consez[$z]</b>";
							if($tab15=="gruppo" or $genere==4 or ($genere==5 and $votog) or defined('_NULLISTA'))	$ar[($z)][$posvoti+5]="<b>$vonsez[$z]</b>"; 
						}
					} 
				}
			}

				if (($offsetsez+1)>=$num_sez){  
					$ar[(1+$numsezmax)][0]="<b>"._TOT."<br />"._COMPLESSIVO."</b>";
					if(isset($temp)) {
					if (!isset($tab15) or !$tab15) $tab15="candidati";
                        if($tab15=="gruppo" or $genere==4 or ($genere==5 and $votog) or defined('_NULLISTA')){
						if($votog) $tab15="lista";
						$votigl=" sum(validi),sum(nulli),sum(bianchi),sum(contestati),sum(voti_nulli)";
                    }elseif($votog){
                          $votigl=" sum(validi),sum(nulli),sum(bianchi),sum(contestati),sum(voti_nulli)";
                    }else{
 #                       if($dettnulli) 
							$votigl=" sum(validi_lista),sum(voti_nulli_lista),sum(contestati_lista),sum(solo_gruppo),0";
#						else $votigl=" sum(validi_lista),sum(nulli),sum(bianchi),sum(contestati_lista),sum(voti_nulli_lista+voti_nulli)";
                    }
                    if($circo) 
                    	$sql="SELECT $votigl from ".$prefix."_ele_sezioni AS t1 LEFT JOIN soraldo_ele_sede AS t2 ON t1.id_sede = t2.id_sede WHERE t1.id_cons='$id_cons' and t2.id_circ=$id_circ";
                    else 
						$sql="SELECT $votigl from ".$prefix."_ele_sezioni as t1 where t1.id_cons='$id_cons' and  t1.id_sez=(select t2.id_sez from ".$prefix."_ele_voti_$tab15 as t2 where t2.id_sez=t1.id_sez group by t2.id_sez)";
					$resv = $dbi->prepare("$sql");
					$resv->execute(); #echo "TEST2: --- $sql";
					list ($sevaltot,$senultot,$sebiatot,$secontot,$sevnutot)= $resv->fetch(PDO::FETCH_NUM);
					$voticompl=$sevaltot+$senultot+$sebiatot+$secontot+$sevnutot;
					$sql="SELECT voti from ".$prefix."_ele_voti_$tab15 where id_cons='$id_cons'";
					$resvt = $dbi->prepare("$sql");
					$resvt->execute();
					if($resvt) list($votlt)=$resvt->fetch(PDO::FETCH_NUM); else $votlt=0;
					if($perc) $temp3=arrayperc($temp,$sevaltot);

					foreach($temp as $key=>$voti) {
						if ($perc=='true' and $voticompl) 
						{
							$percento="<b>$voti<br /><span class=\"red\"><i>".number_format($temp3[$key],2)." %</i></span></b>";
						} else
							$percento="<b>$voti</b>";   #dettnulli
						if($grupposg and $tab=='gruppo') $percento.="<br /><span style=\"color:blue;font-size:80%;\">".$tempsg[$key]." </span>";
						$ar[1+$numsezmax][$key]=$percento;
					}
				}
				if ($visvot!='cand') {
				$key=$offset+1;

# if(!defined('_NULLISTA'))
                    if($tab15=="gruppo" or $genere==4 or ($genere==5 and $votog) or defined('_NULLISTA'))
						$tmp=array($sevaltot,$senultot,$sebiatot,$secontot,$sevnutot);
					else	$tmp=array($sevaltot,$senultot,$sebiatot,$secontot);
				if($perc) $temp3=arrayperc($tmp,$voticompl);

				foreach($tmp as $k=>$voti) {	
                    if ($perc=='true' and $voticompl)
                    {
                         $percento="<b>$voti<br /><span class=\"red\"><i>".$temp3[$k]." %</i></span></b>";
                     } else $percento="<b>$voti</b>";
                     $ar[1+$numsezmax][++$key]=$percento;

				}
				}
			}
			if($orvert!=1) {
				$i=0;
				foreach ( $ar as $riga) {
					$y=0;
					foreach($riga as $cella) {
						$ra[$y++][$i]=$cella;
					}
					$i++;
				}
				crea_tabella($ra);
			}else{
				crea_tabella($ar);
			}
//e' un referendum  
		}else{
			$sql="SELECT id_gruppo, descrizione,num_gruppo from ".$prefix."_ele_gruppo where id_cons=$id_cons order by num_gruppo";
			$res_lis = $dbi->prepare("$sql");
			$res_lis->execute();

			if($res_lis) $numliste=$res_lis->rowCount(); else $numliste=0;

			if (!isset($offset)) $offset=10;
			if (!isset($min)) $min=1;
			if (!isset($offsetsez)) $offsetsez=25; //lo 0 viene sostituito dal totale di sezioni presenti
			if (!isset($minsez)) $minsez=1;
			if (!$csv){
			        echo "<div><h5>$pagina</h5></div>";
				echo "<form id=\"voti\" method=\"post\" action=\"modules.php\">";
				echo "<div style=\"text-align:left;width:700px;margin:auto;font-size:12px \">
					<input type=\"hidden\" name=\"name\" value=\"Elezioni\"></input>";			
				echo "<input type=\"hidden\" name=\"op\" value=\"$op\"></input>";			
				echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\"></input>";			
				echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"></input>";			
				echo ""._SCELTA." "._CONSULTAZIONE.": <select name=\"id_gruppo\">";
				if($res_lis)
				    while(list($id_rif,$descrizione,$num_lis) = $res_lis->fetch(PDO::FETCH_NUM)) {
					if (!$id_gruppo) $id_gruppo=$id_rif;
					$sel = ($id_rif == $id_gruppo) ? "selected=\"selected\"" : "";
					echo "<option value=\"$id_rif\" $sel>";
					for ($j=strlen($num_lis);$j<2;$j++) { echo "&nbsp;&nbsp;";}
					echo $num_lis.") ".strip_tags(substr($descrizione,0,50))."</option>";
				    }
				echo "</select>";
				echo "<br />"._VIS_PERC.": <input type=\"checkbox\" name=\"perc\" value=\"true\"";
				if($perc=='true') echo " checked=\"true\"";
				echo ">";
				echo "<br /><input type=\"submit\" name=\"update\" value=\""._RICARICA."\"></form></div>";

			 

			}
			if(!$id_gruppo) $id_gruppo=0;
			$sql="select num_gruppo,descrizione from ".$prefix."_ele_gruppo where id_gruppo=$id_gruppo";
			$res_ref = $dbi->prepare("$sql");
			$res_ref ->execute();

			$sql="select $tab2, t1.num_gruppo, t1.descrizione , t1.simbolo, 
			sum(t2.si),  sum(t2.no),sum(t2.validi),  sum(t2.nulli),sum(t2.bianchi),  sum(t2.contestati)
			from ".$prefix."_ele_gruppo as t1
			left join ".$prefix."_ele_voti_ref as t2 on (t1.id_gruppo=t2.id_gruppo)
			left join ".$prefix."_ele_sezioni as t3 on (t2.id_sez=t3.id_sez)
			left join ".$prefix."_ele_sede as t4 on (t3.id_sede=t4.id_sede)
			left join ".$prefix."_ele_circoscrizione as t5 on (t4.id_circ=t5.id_circ)
			where 	t1.id_cons='$id_cons' and t1.id_gruppo=$id_gruppo
			group by t2.id_gruppo,$tab2, t1.num_gruppo, t1.descrizione , t1.simbolo
			order by $tab3, t1.num_gruppo
			";
			$res = $dbi->prepare("$sql");
			$res->execute();

			if($res->rowCount()) $num_sez=$res->rowCount(); 
			else 
			{	
				$num_sez=0;
			}
			if($res_ref->rowCount()) list($num_gruppo,$descr)= $res_ref->fetch(PDO::FETCH_NUM); else {$num_gruppo=0;$descr='';}
			if (!$csv){
				echo "<div style=\"text-align:right;width:900px;margin-left:10px;margin-right:20px;font-size:12px \">";
				echo "<table style=\"text-align:center;margin-right:0px;border-top : 1px solid Blue;width: 140px;\"><tr style=\" background:#eceff5;\"><td>"._ESPORTA."<br />";
				
			        echo "<a href=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;csv=1&amp;perc=$perc&amp;id_gruppo=$id_gruppo\" ><img class=\"image\"  src=\"modules/Elezioni/images/printer.gif\" alt=\"Stampa\" /></a>";
				  echo "<a href=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;csv=1&amp;perc=$perc&amp;id_gruppo=$id_gruppo&amp;xls=1\" ><img class=\"image\"  src=\"modules/Elezioni/images/csv.gif\" alt=\"Export Csv\" /></a>";

				  //echo "<a href=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;csv=1&amp;perc=$perc&amp;id_gruppo=$id_gruppo&amp;pdf=1&amp;datipdf=$datipdf\" ><img class=\"image\"  src=\"modules/Elezioni/images/pdf.gif\" alt=\"Export Pdf\" /></a>";
				

				echo "<img class=\"image\"  src=\"modules/Elezioni/images/rss.png\" alt=\"Export rss\" />";
				
				echo "	</td></tr>";
				
				
				echo "<form id=\"pdf\" method=\"post\" action=\"modules.php\">";
					
				echo "<input type=\"hidden\" name=\"id_circ\" value=\"$id_circ\"></input>";	
				echo "<input type=\"hidden\" name=\"op\" value=\"$op\"></input>";			
				echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\"></input>";
				echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"></input>";	
				echo "<input type=\"hidden\" name=\"csv\" value=\"1\"></input>";
				echo "<input type=\"hidden\" name=\"orvert\" value=\"$orvert\"></input>";
				echo "<input type=\"hidden\" name=\"min\" value=\"$min\"></input>";					
			    echo "<input type=\"hidden\" name=\"offset\" value=\"$offset\"></input>";
				echo "<input type=\"hidden\" name=\"minsez\" value=\"$minsez\"></input>";	
				echo "<input type=\"hidden\" name=\"offsetsez\" value=\"$offsetsez\"></input>";		
				echo "<input type=\"hidden\" name=\"perc\" value=\"$perc\"></input>";	
				echo "<input type=\"hidden\" name=\"id_gruppo\" value=\"$id_gruppo\"></input>";
				echo "<input type=\"hidden\" name=\"pdf\" value=\"1\"></input>";
#				echo "<input type=\"hidden\" name=\"datipdf\" value=\"$datipdf\"></input>";
				echo "<input type=\"hidden\" name=\"name\" value=\"Elezioni\"></input>";

				echo "<tr><td>";
				echo "<input type=\"image\" name=\"submit\" src=\"modules/Elezioni/images/pdf.gif\" align=\"left\">";
				echo "&nbsp; L &nbsp;<input type=\"radio\" name=\"orienta\" $land value=\"L\"></input>P &nbsp;<input
				type=\"radio\" name=\"orienta\" $port value=\"P\"></input><br />";
				echo "&nbsp; A3<input type=\"radio\" name=\"formato\" $A3 value=\"A3\"></input>A4<input
				type=\"radio\" name=\"formato\" $A4 value=\"A4\"></input>";
				
				
				echo "	</td></tr></table></form> ";
					








				echo "</div>";


// numero sezioni scrutinate
  	$sql="select t3.id_sez  from ".$prefix."_ele_voti_ref as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons'  group by t3.id_sez ";
	$res4 = $dbi->prepare("$sql");
	$res4->execute();

	$numero=$res4->rowCount();
	$sql="select t1.*  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' ";
	$res5 = $dbi->prepare("$sql");
	$res5->execute();

	$sezioni=$res5->rowCount();
	if ($numero!=0) echo "<h5><i> "._SEZSCRU." $numero "._SU." $sezioni </i></h5>";

}
	$sql="select count(0) from ".$prefix."_ele_sezioni where id_cons='$id_cons'";
	$res5 = $dbi->prepare("$sql");
	$res5->execute();

	list($num_sez) = $res5->fetch(PDO::FETCH_NUM);

				# stampa
				
				$datipdf="<b>"._COMUNE." $descr_comune</b> - "._RISULTATI.": $descr_cons<br/><b>$pagina</b><br/><br/> ";
				$datipdf .="<br/><b>Referendum n. ".$num_gruppo." </b><br/>".$descr."";
	            $datipdf=str_replace('"',"'",$datipdf);
				$_SESSION['datipdf']= $datipdf;




			if (!$csv)echo "<b>Referendum n. ".$num_gruppo." </b><br />".$descr."";
#			die("TEST: $csv - $num_gruppo - $descr");

		$y=1;
		$ar[0][0]=$tipo2;
		$ar[0][1]=_SI;
		$ar[0][2]=_NO;
		$ar[0][3]=_VALIDI;
		$ar[0][4]=_NULLI;
		$ar[0][5]=_BIANCHI;
		$ar[0][6]=_CONTESTATI;
		if($res_ref->rowCount())
		    while (list($num_gruppo,$desc_ref) = $res_ref->fetch(PDO::FETCH_NUM)){
			$ar[0][$i++]= $num_gruppo.") ".$desc_ref;
			$ar[1][$y++]= "SI";
			$ar[1][$y++]= "NO";
		    }
		$num_sez++;
		$tot_si=0;
		$tot_no=0;
		$tot_va=0;
		$tot_nu=0;
		$tot_bi=0;
		$tot_co=0;
		if($res->rowCount())
		    while (list($num_circ,$desc_circ,$num_gruppo,$desc_ref,$simbolo,$si,$no,$validi,$nulli,$bianchi, $contestati)  = $res->fetch(PDO::FETCH_NUM)){
			$i=1;
			$votanti=$validi+$nulli+$bianchi+$contestati;
			$tot_si+=$si;
			$tot_no+=$no;
			$tot_va+=$validi;
			$tot_nu+=$nulli;
			$tot_bi+=$bianchi;
			$tot_co+=$contestati;
			$ar[$num_circ][0]=$num_circ."<br />".$desc_circ;
			if($validi){
			$ar[$num_circ][$i++]= $perc=='true' ? $si."<br /><span class=\"red\"><i>".number_format($si*100/$validi,2)."%</i></span>":$si;
			$ar[$num_circ][$i++]= $perc=='true' ? $no."<br /><span class=\"red\"><i>".number_format($no*100/$validi,2)."%</i></span>":$no;
			}else{
			$ar[$num_circ][$i++]= $perc=='true' ? $si."<br /><span class=\"red\"><i>0.00%</i></span>":$si;
			$ar[$num_circ][$i++]= $perc=='true' ? $no."<br /><span class=\"red\"><i>0.00%</i></span>":$no;
			}
			if($votanti){
			$ar[$num_circ][$i++]= $perc=='true' ? $validi."<br /><span class=\"red\"><i>".number_format($validi*100/$votanti,2)."%</i></span>":$validi;
			$ar[$num_circ][$i++]= $perc=='true' ? $nulli."<br /><span class=\"red\"><i>".number_format($nulli*100/$votanti,2)."%</i></span>":$nulli;
			$ar[$num_circ][$i++]= $perc=='true' ? $bianchi."<br /><span class=\"red\"><i>".number_format($bianchi*100/$votanti,2)."%</i></span>":$bianchi;
			$ar[$num_circ][$i++]= $perc=='true' ? $contestati."<br /><span class=\"red\"><i>".number_format($contestati*100/$votanti,2)."%</i></span>":$contestati;
			}else{
			$ar[$num_circ][$i++]= $perc=='true' ? $validi."<br /><span class=\"red\"><i>0.00%</i></span>":$validi;
			$ar[$num_circ][$i++]= $perc=='true' ? $nulli."<br /><span class=\"red\"><i>0.00%</i></span>":$nulli;
			$ar[$num_circ][$i++]= $perc=='true' ? $bianchi."<br /><span class=\"red\"><i>0.00%</i></span>":$bianchi;
			$ar[$num_circ][$i++]= $perc=='true' ? $contestati."<br /><span class=\"red\"><i>0.00%</i></span>":$contestati;
			}
		    }
		$i=1;
		$tot_vo=$tot_va+$tot_nu+$tot_bi+$tot_co;
#		if($tot_va==0) $tot_va=1;
#		if($tot_vo==0) $tot_vo=1;
		$ar[$num_sez][0]=_TOT."<br />"._COMPLESSIVO;
		if($tot_va){
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_si."<br /><span class=\"red\">".number_format($tot_si*100/$tot_va,2)."%</span>":$tot_si;
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_no."<br /><span class=\"red\">".number_format($tot_no*100/$tot_va,2)."%</span>":$tot_no;
		}else{
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_si."<br /><span class=\"red\">0.00%</span>":$tot_si;
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_no."<br /><span class=\"red\">0.00%</span>":$tot_no;
		}
		if($tot_vo){
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_va."<br /><span class=\"red\">".number_format($tot_va*100/$tot_vo,2)."%</span>":$tot_va;
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_nu."<br /><span class=\"red\">".number_format($tot_nu*100/$tot_vo,2)."%</span>":$tot_nu;
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_bi."<br /><span class=\"red\">".number_format($tot_bi*100/$tot_vo,2)."%</span>":$tot_bi;
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_co."<br /><span class=\"red\">".number_format($tot_co*100/$tot_vo,2)."%</span>":$tot_co;
		}else{
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_va."<br /><span class=\"red\">0.00%</span>":$tot_va;
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_nu."<br /><span class=\"red\">0.00%</span>":$tot_nu;
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_bi."<br /><span class=\"red\">0.00%</span>":$tot_bi;
		$ar[$num_sez][$i++]= $perc=='true' ? $tot_co."<br /><span class=\"red\">0.00%</span>":$tot_co;
		}
		crea_tabella($ar);
	}
	if ($csv) echo "</body>\n</html>";
}

?>
