<?php

/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}
if(!defined("_VER_FLASH")) include('modules/Elezioni/language/lang-it.php');
if(!function_exists('verificasimbolo')) include('modules/Elezioni/funzioni.php');

$sql="SELECT t1.descrizione, t1.tipo_cons,t2.genere, t2.voto_g, t2.voto_l, t2.voto_c, t2.circo FROM ".$prefix."_ele_consultazione as t1,".$prefix."_ele_tipo as t2 where t1.tipo_cons=t2.tipo_cons and t1.id_cons_gen='$id_cons_gen' ";
$res = $dbi->prepare("$sql");
$res->execute();

list($descr_cons,$tipo_cons,$genere,$votog,$votol,$votoc,$circo) = $res->fetch(PDO::FETCH_NUM);
$sql="SELECT t2.id_cons FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_cons_gen='$id_cons_gen' and t2.id_comune='$id_comune'" ;
$res = $dbi->prepare("$sql");
$res->execute();

list($id_cons) = $res->fetch(PDO::FETCH_NUM);


if($flash=='1') include "class/charts.php";

/***********************************
/* Grafica affluenze
/**********************************/

function affluenze_graf(){
global $bgcolor1, $bgcolor2, $prefix, $dbi, $offset,$genere,$votog,$votol,$votoc,$circo, $min,$id_cons,$tipo_cons,$id_cons_gen,$csv,$id_comune,$id_circ, $tema;
// icone
	if ($circo) $circos="and t2.id_circ='$id_circ'";
	else $circos="";
	if (!$csv)
	echo "<div>
	<a href=\"
	modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=affluenze_sez\">"._DETTAGLIO."  "._AFFLUENZE."<img class=\"image\" src=\"modules/Elezioni/images/dettagli.png\" alt=\" "._AFFLUENZE."\" /></a>
	<a href=\" modules.php?name=Elezioni&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;op=affluenze_graf&amp;csv=1\">"._VER_STAMPA." <img class=\"image\" src=\"modules/Elezioni/images/printer.png\" alt=\"stampa\" /></a></div>";
      
	$sql="SELECT t1.* FROM ".$prefix."_ele_sezioni as t1, ".$prefix."_ele_sede as t2 where t1.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circos order by num_sez";
	$res2 = $dbi->prepare("$sql");
	$res2->execute();
	$sezioni=$res2->rowCount();
	
// barre
    $l_size = getimagesize("modules/Elezioni/images/barre/leftbar.jpg");
    $m_size = getimagesize("modules/Elezioni/images/barre/mainbar.jpg");
    $r_size = getimagesize("modules/Elezioni/images/barre/rightbar.jpg");
    $l_size2 = getimagesize("modules/Elezioni/images/barre/leftbar2.jpg");
    $m_size2 = getimagesize("modules/Elezioni/images/barre/mainbar2.jpg");
    $r_size2 = getimagesize("modules/Elezioni/images/barre/rightbar2.jpg");
    // totali

	if($tema=='tour') 
		$andcond="select orario,data  from ".$prefix."_ele_voti_parziale where id_cons='$id_cons' order by data desc,orario desc limit 0,1"; 
	else 
		$andcond="select orario,data  from ".$prefix."_ele_rilaff where id_cons_gen='$id_cons_gen' order by data,orario";       
    $sql="$andcond";
	$res = $dbi->prepare("$sql");
	$res->execute();
    while(list($orario,$data) = $res->fetch(PDO::FETCH_NUM)) {
		list ($ore,$minuti,$secondi)=explode(':',$orario);
		list ($anno,$mese,$giorno)=explode('-',$data);
		$tot_v_m=0;$tot_v_d=0;$tot_t=0;
		echo "<br /><div><h5>"._VOTANTI." "._ALLE." "._ORE." $ore,$minuti "._DEL."  $giorno/$mese/$anno</h5></div>";               
  		$sql="SELECT count(data) FROM ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' and data='$data' and orario='$orario' $circos group by t3.id_gruppo";
		$res1 = $dbi->prepare("$sql");
		$res1->execute();                                                                                                                            
		list($numero)=$res1->fetch(PDO::FETCH_NUM);	
		$sql="select sum(t3.voti_complessivi), t4.num_gruppo , t4.id_gruppo   from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede left join ".$prefix."_ele_gruppo as t4 on (t3.id_gruppo=t4.id_gruppo) where t3.id_cons='$id_cons' and t3.orario='$orario' and t3.data='$data' $circos  group by t4.num_gruppo, t4.id_gruppo order by t4.num_gruppo ";
		$res1 = $dbi->prepare("$sql");
		$res1->execute();
                                                                                                                             
		while(list($voti_t, $num_gruppo,$id_gruppo) = $res1->fetch(PDO::FETCH_NUM)) {
			$query="select sum(voti_complessivi) from ".$prefix."_ele_voti_parziale where orario='$orario' and data='$data' and id_cons='$id_cons'";
			if ($genere==0){$query.=" and id_gruppo=$id_gruppo";}
			$sql=$query;
			$res_aff = $dbi->prepare("$sql");
			$res_aff->execute();
			$voti_numero=$res_aff->rowCount();
        	$query="SELECT sum(maschi+femmine) FROM ".$prefix."_ele_sezioni as t1, ".$prefix."_ele_sede as t2 where t1.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circos";
			$sql=$query;
			$res1234 = $dbi->prepare("$sql");
			$res1234->execute();
			list($tot)=$res1234->fetch(PDO::FETCH_NUM);				
			$perc=number_format($voti_t*100/$tot,2);
			echo "<table class=\"td-80\"><tr class=\"bggray\">";
			if ($genere==0){echo "<td>Referendum N.</td>";}
			echo "<td><b>"._VOTANTI."</b></td><td><b>"._PERCE."</b></td>";
			echo "<td><b>"._SEZIONI."</b></td>";
			echo "</tr>";
			echo "<tr class=\"bggray2\">";
			if ($genere==0){echo "<td><h2>$num_gruppo</h2></td>";}
			echo "<td>$voti_t</td><td>$perc %</td><td>$numero</td></tr></table>";
 // barre
			echo "<table><tr><td><table><tr><td>&nbsp;"._VOTANTI."       : </td><td><img src=\"modules/Elezioni/images/barre/leftbar2.jpg\" height=\"$l_size2[1]\" width=\"$l_size2[0]\" alt=\"votanti\" /><img src=\"modules/Elezioni/images/barre/mainbar2.jpg\" alt=\"votanti\" height=\"$m_size2[1]\" width=\"". ($perc * 2)."\" /><img src=\"modules/Elezioni/images/barre/rightbar2.jpg\" height=\"$r_size2[1]\" width=\"$r_size2[0]\" alt=\"votanti\" /><span class=\"red\"> $perc</span> % ($voti_t)<br /></td></tr>\n";
			$tot_gen=$tot;
			echo  "<tr><td>&nbsp;"._AVENTI.": </td><td><img src=\"modules/Elezioni/images/barre/leftbar.jpg\" height=\"$l_size[1]\" width=\"$l_size[0]\" alt=\"elettori\" /><img src=\"modules/Elezioni/images/barre/mainbar.jpg\" alt=\"elettori\" height=\"$m_size[1]\" width=\"".(100 * 2)."\" /><img src=\"modules/Elezioni/images/barre/rightbar.jpg\" height=\"$r_size[1]\" width=\"$r_size[0]\" alt=\"elettori\" /> 100 % ($tot_gen)</td></tr></table>";
			 echo "</td></tr></table>";
		}	
    }
}

/*********************************/
/* Grafica votanti               */
/**********************************/

function graf_votanti(){
global $op, $prefix, $dbi, $offset, $min,$descr_cons,$genere,$votog,$votol,$votoc,$circo, $id_cons,$tipo_cons,$id_comune,$id_cons_gen,$id_circ,$csv,$w,$l,$siteistat,$flash,$tour,$tema,$copy;


$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ?
	$_GET : $_POST;
if (isset($param['anim'])) $anim=intval($param['anim']); else $anim='';

if ($siteistat==$id_comune) $logo="$siteistat"; else $logo=''; // logo per il  comune 
$logo=verificasimbolo(); // carica_logo da funzioni.php
// menu
	if (!$csv && $anim!=1){ $anim=1;$versione=_VER_HTML;
	}else{ $anim='';$versione=_VER_FLASH;}
	if($csv!=1){
	echo " <div>
	<a href=\"
	modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=votanti\">"._DETTAGLIO." "._VOTANTI."<img class=\"image\" src=\"modules/Elezioni/images/dettagli.png\" alt=\"votanti\" /></a>
	<a href=\" modules.php?name=Elezioni&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;op=graf_votanti&amp;csv=1&amp;flash=\">"._VER_STAMPA." <img class=\"image\" src=\"modules/Elezioni/images/printer.png\" alt=\"stampa\" /></a>";
	if($flash==1) 
		echo "<a href=\" modules.php?name=Elezioni&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;op=graf_votanti&amp;csv=&amp;anim=$anim\">$versione <img class=\"image\" src=\"modules/Elezioni/images/grafici.gif\" alt=\"flash\" /></a>";
        echo "</div>";
      }


/* Scelta circoscrizioni da implementare
if ($circo){ // elenco per scelta circoscrizione
		echo "<form id=\"test\" action=\"modules.php\"> <input type=\"hidden\" name=\"pagina\" value=\"modules.php?name=Elezioni&amp;op=$op&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=\">";
		$res_sez = mysql_query("SELECT id_circ,descrizione,num_circ from ".$prefix."_ele_circoscrizione where id_cons=$id_cons",$dbi);
		echo "<table><tr><td>Scegli la Circoscrizione: <select name=\"id_circ\" onChange=\"top.location.href=this.form.pagina.value+this.form.id_circ.options[this.form.id_circ.selectedIndex].value;return false\">";
		while(list($id_rif,$descrizione,$num_cir)=mysql_fetch_row($res_sez)) {
				if (!$id_circ) $id_circ=$id_rif;
				$sel = ($id_rif == $id_circ) ? "selected" : "";
				echo "<option value=\"$id_rif\" $sel>";
				for ($j=strlen($num_cir);$j<2;$j++) { echo "&nbsp;&nbsp;";}
				echo $num_cir.") ".$descrizione;
			}
			echo "</select></td></tr></table></form>";
		
	} 


*/

	if ($genere==0) $tab="ref";elseif($genere=='4' || $votog) $tab="lista";
		 else $tab="gruppo";
	if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
	else $circos=''; 

        // Verificare per la circoscrizione aprile 2008 Nicola?
        //if ($genere==0) {$tab="ref";}else{$tab="gruppo";}
        //if ($votog or $genere==4) {$tab="lista";}else{$tab="gruppo";}

	/*
        if($circo){
                if(!$id_circ){
                $res = mysql_query("select id_circ from ".$prefix."_ele_circoscrizione where id_cons='$id_cons' ", $dbi);
                list($id_circ)=mysql_fetch_row($res);
                }
		
                 $res = mysql_query("select t1.id_sez,sum(t1.voti)  from ".$prefix."_ele_voti_$tab as t1, ".$prefix."_ele_$tab as t2 where t1.id_$tab=t2.id_$tab and t1.id_cons='$id_cons' and t2.id_circ='$id_circ' group by t1.id_sez", $dbi);
        }else 
	 */
	 if($genere==0)
		$sql="select distinct t1.id_sez  from ".$prefix."_ele_voti_".$tab." as t1 left join ".$prefix."_ele_gruppo as t2 on t1.id_gruppo=t2.id_gruppo where t1.id_cons='$id_cons' $circos";
	else
		$sql="select * from ".$prefix."_ele_sezioni where id_cons='$id_cons' and validi+nulli+bianchi+contestati>0";
	$res = $dbi->prepare("$sql");
	$res->execute();


	if($res)
		$numero=$res->rowCount();
	else
	$numero=1;
	$sql="select t1.*  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos";
	$res = $dbi->prepare("$sql");
	$res->execute();

	$sezioni=$res->rowCount();
	if ($numero!=0){




	$sql="SELECT sum(maschi+femmine) from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();

	list($tot_aventi)  = $res1->fetch(PDO::FETCH_NUM);
     
	if ($genere!=0) {
	echo "<center><h2>";
	echo "<b>"._DETTAGLIO." "._VOTIE."</b> - ";
	echo "<i> "._SEZSCRU." $numero "._SU." $sezioni </i>";
	echo "</h2></center>";

		$sql="SELECT sum(validi+nulli+bianchi+contestati) as tot,
		sum(validi),sum(nulli),sum(bianchi),sum(contestati), '0'
		from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos having tot>0";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();

	}else{

		if($tema=='tour'){
			$sql="SELECT count(0)
		from ".$prefix."_ele_gruppo  where id_cons=$id_cons";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();


		  list($max_ruotag) =$res1->fetch(PDO::FETCH_NUM);
			if (isset($_SESSION['ruotag'])) { $ruotag=$_SESSION['ruotag'];}
			else {$ruotag=1;$_SESSION['ruotag']=1;}
			if ($ruotag>=$max_ruotag) {$ruotag=1;}
			else {$ruotag++;}
			$_SESSION['ruotag']=$ruotag;
			$sql="SELECT count(0) from ".$prefix."_ele_voti_ref AS t1 LEFT JOIN ".$prefix."_ele_gruppo AS t2 ON t1.id_gruppo = t2.id_gruppo WHERE t1.id_cons ='$id_cons' AND t2.num_gruppo ='$ruotag'";
			$res1 = $dbi->prepare("$sql");
			$res1->execute();

		list($numero)=$res1->fetch(PDO::FETCH_NUM);
	echo "<center><h2>";
	echo "<b>"._DETTAGLIO." "._VOTIE."</b> - ";
	echo "<i> "._SEZSCRU." $numero "._SU." $sezioni </i>";
	echo "</h2></center>";
		
		$sql="SELECT sum( t1.validi + t1.nulli + t1.bianchi + t1.contestati ) AS tot, sum( t1.validi ) , sum( t1.nulli ) , sum( t1.bianchi ) , sum( t1.contestati ) , t1.id_gruppo
FROM ".$prefix."_ele_voti_ref AS t1 LEFT JOIN ".$prefix."_ele_gruppo AS t2 ON t1.id_gruppo = t2.id_gruppo
WHERE t1.id_cons ='$id_cons' AND t2.num_gruppo ='$ruotag' GROUP BY t1.id_gruppo HAVING tot >0";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();

	}	else
		$sql="SELECT sum(validi+nulli+bianchi+contestati) as tot,
		sum(validi),sum(nulli),sum(bianchi),sum(contestati), id_gruppo
		from ".$prefix."_ele_voti_ref  where id_cons=$id_cons group by id_gruppo having tot>0";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();

	}
	while  (list($tot_votanti,$validi,$nulli,$bianchi,$contestati,$id)  = $res1->fetch(PDO::FETCH_NUM)){
		$arperc=array();
		$arval=array($validi,$nulli,$bianchi,$contestati);
		$arperc=arrayperc($arval,$tot_votanti);
		$tot_votanti=$validi+$bianchi+$nulli+$contestati;
		$perc_validi=number_format($arperc[0],2);
		$perc_nulli=number_format($arperc[1],2);
		$perc_bianchi=number_format($arperc[2],2);
		$perc_conte=number_format($arperc[3],2);
		$perc_votanti=number_format($tot_votanti*100/$tot_aventi,2);
		$non_votanti=($tot_aventi - $tot_votanti);
		$perc_non=100-$perc_votanti;

		if ($genere==0) {
			$sql="SELECT num_gruppo,descrizione from ".$prefix."_ele_gruppo where id_gruppo=$id";
	$res = $dbi->prepare("$sql");
	$res->execute();

			list($num_gruppo,$descr_gruppo)  = $res->fetch(PDO::FETCH_NUM);
		}

		

		$a1=_VALIDI;$b1=_NULLI;$c1=_BIANCHI;$d1=_CONTESTATI;$titolo=""._PERCE." "._VOTIE."";
		$e1=_VOTANTI;$f1=""._NON." "._VOTANTI."";$titolo2=""._PERCE." "._AFFLUENZE."";

		
		
		    
		echo "<table  width=\"100%\" class=\"modulo\"><tr>";
		if ($genere==0) echo "<td colspan=\"2\" valign=\"top\" >";
		else echo "<td valign=\"top\" >";
		
		echo "<table cellspacing=\"5\" style=\"text-align:center;\">";
		if ($genere==0) {echo "<tr><td colspan=\"4\"><h4>"._GRUPPO." $num_gruppo <br/> $descr_gruppo</h4></td></tr>";}
		echo "
		<tr class=\"bggray3\"><td style=\"text-align: right;\"><b>"._AVENTI."</b></td><td>$tot_aventi</td><td><span class=\"red\">100%</span></td></td></td></tr>

		<tr class=\"bggray3\"><td style=\"text-align: right;\"><b>"._VOTANTI."</b></td><td>$tot_votanti</td><td><span class=\"red\">$perc_votanti%</span></td></tr>
		
		<tr class=\"bggray3\"><td style=\"text-align: right;\">"._VALIDI."</td><td>$validi</td><td><span class=\"red\">$perc_validi%</span></td></tr>
		
		<tr class=\"bggray3\"><td style=\"text-align: right;\">"._NULLI."</td><td>$nulli</td><td><span class=\"red\">$perc_nulli%</span></td></tr>
		
		<tr class=\"bggray3\"><td style=\"text-align: right;\">"._BIANCHI."</td><td>$bianchi</td><td><span class=\"red\">$perc_bianchi%</span></td></tr>
		
		<tr class=\"bggray3\"><td style=\"text-align: right;\">"._CONTESTATI."</td><td>$contestati</td><td><span class=\"red\">$perc_conte%</span></td></tr>

		</table>";
		echo "</td></tr><tr><td style=\"text-align:center;\">
		<h1>"._PERCE." "._VOTANTI."</h1>";
		if ($genere==0) echo "<h2> "._GRUPPO." $num_gruppo</h2>";
		
		
		//if ((!$csv && $flash!=1 && $anim!=1) or ($tour==1 && $flash!=1 && $anim!=1)){ # rotazione x tour marzo 2009 a.l.
		if ((!$csv && $flash!=1 || $anim!=1) and ($tour!=1)) { # rotazione x tour marzo 2009 a.l. 
			echo "<br /><img alt=\"Grafico\"  src=\"modules/Elezioni/grafici/votanti_graf.php?cop=$copy&amp;titolo=$titolo2&amp;e=$perc_votanti&amp;f=$perc_non&amp;e1=$e1&amp;f1=$f1&amp;logo=$logo\" /><br /><br />";
		}else{
		// Flash affluenze 
			$gruppos1=array("",_VOTANTI,_NONVOTANTI);
			$pre1=array("",$perc_votanti,$perc_non);
			flash_torta($gruppos1,$pre1,50,50); 
		}
		
		if($csv) echo "</td><td>";else echo "</td></tr><tr><td style=\"text-align:center;\">"; # rotazione x tour marzo 2009 a.l.
		echo "<h1>"._PERCE." "._VOTIE."</h1>";
		if ($genere==0) echo "<h2>  "._GRUPPO." $num_gruppo<h2> ";

		//$logo=verificasimbolo(); // carica_logo da funzioni.php
		
		if ((!$csv && $flash!=1 || $anim!=1) and ($tour!=1)){ # rotazione x tour marzo 2009 a.l.
			echo "<br /><img  alt=\"Grafico\" src=\"modules/Elezioni/grafici/voti_graf.php?cop=$copy&amp;titolo=$titolo&amp;a=$perc_validi&amp;b=$perc_nulli&amp;c=$perc_bianchi&amp;d=$perc_conte&amp;a1=$a1&amp;b1=$b1&amp;c1=$c1&amp;d1=$d1&amp;logo=$logo\" /><br /><br />";
		}else{
			
			$gruppos2=array("","Validi","Nulli", "Bianchi", "Contestati");
			$pre2=array("",$perc_validi,$perc_nulli,$perc_bianchi,$perc_conte);
			flash_torta($gruppos2,$pre2,50,50); 
		}
                
		
		
		echo "</td></tr></table>";


		}
	}

}

/***********************************
/* Grafica Gruppo
/**********************************/

function graf_gruppo($visgralista){
global $admin, $bgcolor1, $bgcolor5, $prefix, $dbi, $offset, $min,$descr_cons,$genere,$votog,$votol,$votoc,$circo, $id_cons,$id_cons_gen,$id_comune,$id_circ,$tipo_cons,$w,$l,$op,$siteistat,$flash,$tema,$copy;
	if ($siteistat==$id_comune) $logo=$siteistat; else $logo=''; // logo per il  comune
	$logo=verificasimbolo(); // carica_logo da funzioni.php
	if (!$id_circ and $circo){
		$sql="SELECT id_circ from ".$prefix."_ele_circoscrizione where id_cons=$id_cons order by num_circ limit 0,1";
		$res_sez = $dbi->prepare("$sql");
		$res_sez->execute();

		list($id_circ)=$res_sez->fetch(PDO::FETCH_NUM);
	}
	$circond='';$circondt1='';
	if ($genere!=0){$tab="gruppo";}else{$tab="ref";}
	if ($genere==4 or $visgralista){$tab="lista";}
	if ($votog){$tab="lista";}
	$sql="select id_sez  from ".$prefix."_ele_voti_$tab where id_cons='$id_cons' group by id_sez ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	if ($circo){
		$sql="select t1.id_sez from ".$prefix."_ele_voti_gruppo as t1, ".$prefix."_ele_gruppo as t2 where t1.id_gruppo=t2.id_gruppo and t1.id_cons='$id_cons' and t2.id_circ=$id_circ group by t1.id_sez ";
		$res = $dbi->prepare("$sql");
		$res->execute();

		$sql="select sum(t1.voti)  from ".$prefix."_ele_voti_gruppo as t1, ".$prefix."_ele_gruppo as t2 where t1.id_gruppo=t2.id_gruppo and t1.id_cons='$id_cons' and t2.id_circ=$id_circ";
		$restotv = $dbi->prepare("$sql");
		$restotv->execute();

		$circond="and id_circ=$id_circ";$circondt1="and t1.id_circ=$id_circ";
	}
	if ($res) $numero=$res->rowCount();else $numero=0;
	$sql="select t2.*  from ".$prefix."_ele_sezioni as t2, ".$prefix."_ele_sede as t1 where t2.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circondt1";
	$res = $dbi->prepare("$sql");
	$res->execute();

	if ($res) $sezioni=$res->rowCount();else $sezioni=0;


	if ($numero>0 ){
		echo "<center><h2>";
		if ($genere!=4 and !$visgralista){
			echo "<b>"._PREFERENZE." "._GRUPPO." $descr_cons </b>";
		}else{
			echo "<b>"._PREFERENZE." "._LISTA." $descr_cons </b>";
		}
		echo "<i>- "._SEZSCRU." $numero "._SU." $sezioni </i>";
		echo "</h2></center>";

		if ($genere!=0){
		// tot voti
			if (!$circo) 
				$sql="select sum(voti)  from ".$prefix."_ele_voti_$tab where id_cons=$id_cons ";
			else
				$sql="select sum(t1.voti)  from ".$prefix."_ele_voti_$tab as t1 left join ".$prefix."_ele_$tab as t2 on t1.id_$tab=t2.id_$tab where t2.id_circ=$id_circ ";
			if ($votog) 
				$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons=$id_cons ";	
			$restotv = $dbi->prepare("$sql");
			$restotv->execute();			
			list($tot)  = $restotv->fetch(PDO::FETCH_NUM);

			
			$i=0;
			// lista o gruppo
			if ($genere!=4 and !$visgralista){
			         
				if ($votog){
				
				$sql="select t1.id_gruppo, t1.num_gruppo, t1.descrizione, sum(t2.voti) as somma
				from ".$prefix."_ele_gruppo as t1,
				".$prefix."_ele_voti_lista as t2,
				".$prefix."_ele_lista as t3
        			where 	t1.id_cons='$id_cons'
				and t2.id_lista=t3.id_lista
				and t1.id_gruppo=t3.id_gruppo
				group by t1.id_gruppo
				order by somma desc";
				$cosa='id_gruppo';
				
				}else{
				
			
			        $sql="select t1.id_gruppo, t1.num_gruppo, t1.descrizione, sum(t2.voti) as somma
				from ".$prefix."_ele_gruppo as t1
        			left join ".$prefix."_ele_voti_$tab as t2 on (t1.id_gruppo=t2.id_gruppo)
				where 	t1.id_cons='$id_cons' and t1.id_cons=t2.id_cons $circondt1
				group by t1.id_gruppo, t1.num_gruppo, t1.descrizione
				order by somma desc";
				$cosa='id_gruppo';
			    }
				$res = $dbi->prepare("$sql");
				$res->execute();
			
			
			}else{
				$sql="select t1.id_lista, t1.num_lista, t1.descrizione, sum(t2.voti) as somma
				from ".$prefix."_ele_lista as t1
        		left join ".$prefix."_ele_voti_$tab as t2 on (t1.id_lista=t2.id_lista)
				where 	t1.id_cons='$id_cons' and t1.id_cons=t2.id_cons $circondt1
				group by t1.id_lista,t1.num_lista,t1.descrizione
				order by somma desc";
				$res = $dbi->prepare("$sql");
				$res->execute();
				$cosa='id_lista';
			}

		echo "<table>
			<tr>
				<td><br />
					<table style=\"border-collapse: collapse;\" rules=\"cols\">";
					// inizio tabella dati
                			// variabili stampa flash
					$e=0;
					$gruppos[$e]="";
					$pre[$e]="";
					$e=1;
					// fine 
				$gruppinum=$res->rowCount();
				$altrivoti=0;
####calcolo percentuale
				$arvoti=array();
				$arperc=array();
				while (list($id,$num,$descrizione,$voti)  = $res->fetch(PDO::FETCH_NUM)){
					$arvoti[$id]=$voti;
				}
				$arperc=arrayperc($arvoti,$tot);
				$res = $dbi->prepare("$sql");
				$res->execute();
				
####
				$altriperc=0;
				while (list($id,$num,$descrizione,$voti)  = $res->fetch(PDO::FETCH_NUM)){

				
				// verica chi ha preso meno del 3%
			    $menotre=(number_format($voti*100/$tot,2));
				
			    if($menotre>3){ 
				



				// funz per il taglio corretto della frase 13 feb 2007
				//$descrizione=taglio(4,$descrizione);
				
				$gruppo[$i]=(substr($descrizione,0,21));
				$gruppos[$e]=(substr($descrizione,0,21)); //flash

				

				if (strlen($descrizione)>21) $gruppo[$i].="...";
				if (strlen($descrizione)>21) $gruppos[$e].="...";
				$pro[$i]=number_format($arperc[$id],2); 
				$pre[$e]=number_format($arperc[$id],2); //flash


			     }else{
				//somma i voti sotto il 3%
				$altrivoti = $altrivoti + $voti;
				$altriperc += $arperc[$id];
			     }
				
					
				



				$votiv=number_format($voti,0,',','.');
				// formattazione numeri perc
				$prov=number_format($arperc[$id],2);

			// sviluppo tabella dati
				$bgcolor1= ($bgcolor1=="#cacaca") ? "#ffffff":"#cacaca";
				echo "<tr valign=\"top\">
				<td style=\"vertical-align=:top;\"><b><img src=\"modules.php?name=Elezioni&amp;file=foto&amp;$cosa=$id\" class=\"stemmapic\" alt=\"descrizione\" /></b></td>
			<td><b>$descrizione</b></td>
			<td>"._VOTI."</td><td align=\"right\">$votiv</td><td align=\"right\"> <i>($prov%)</i></td>
			</tr>";
                       
		        $e++; // flash
			$i++;
		}
		// aggiunge altri minori al 3%
		// esiste 
		// corretto 15 aprile 2006
				if ($altrivoti>0){
					
					$gruppo[]=_ALTRI;
					$gruppos[]=_ALTRI;
					$pro[]=$altriperc; #number_format($altrivoti*100/$tot,3);
					$pre[]=$altriperc; #number_format($altrivoti*100/$tot,3);
				}


		if ($i<=10) $i=10;
		$titolo=""._PERCE." "._VOTIE."";
		$dati1=serialize($pro);
		//$dati1=urlencode($dati1);
		//$gruppo=utf8_encode($gruppo);
		$dati2=serialize($gruppo);
		$dati2=urlencode($dati2);
		 
		$titolo=urlencode($titolo);
		if (isset($copy)) $copy=urlencode($copy); else $copy='';
		$descr_cons=urlencode($descr_cons);
		if ($genere==4){$w=800;$l=250;}else{$w=750;$l=220;}


		echo "</table></td>";
		global $tema;
		if($tema!="tour") echo "</tr><tr>";
		echo "<td style=\"vertical-align=:top; text-align:center;\"><br />";
		# grafico statico
		echo "<img  src='modules/Elezioni/grafici/barre.php?dati1=$dati1&amp;dati2=$dati2&amp;i=$i&amp;cop=$copy&amp;titolo=$titolo&amp;descr=$descr_cons&amp;l=$l&amp;w=$w&amp;logo=$logo'  alt=\"Grafico\" />";
		
		echo "</td></tr></table><table><tr><td>";
		# grafico flash
		if($flash=='1') flash_torta($gruppos,$pre,40,145);
				$uno='';
				foreach($gruppos as $k=>$v) $uno.="- $v";
				$due='due:';
				foreach($pre as $k=>$v) $due.="- $v";
		echo "</td></tr></table>";
                
               
	}else{
		// tot voti
		if($tema=='tour'){
				$sql="SELECT count(0) from ".$prefix."_ele_gruppo  where id_cons=$id_cons";
			$res1 = $dbi->prepare("$sql");
			$res1->execute();

		  list($max_ruotag) =$res1->fetch(PDO::FETCH_NUM);
			if (isset($_SESSION['ruotag'])) { $ruotag=$_SESSION['ruotag'];}
			else {$ruotag=1;$_SESSION['ruotag']=1;}
			$sql="select sum(t1.validi),t1.id_gruppo from ".$prefix."_ele_voti_$tab as t1 left join soraldo_ele_gruppo as t2 on t1.id_gruppo=t2.id_gruppo where t1.id_cons='$id_cons' and t2.num_gruppo='$ruotag' group by t1.id_gruppo";
		}else
		$sql="select sum(validi),id_gruppo  from ".$prefix."_ele_voti_$tab where id_cons=$id_cons group by id_gruppo";
		$res = $dbi->prepare("$sql");
		$res->execute();
	
		while (list($tot,$id_gruppo)  = $res->fetch(PDO::FETCH_NUM)){

			$s=0;
			$sql="select t1.id_gruppo, t1.num_gruppo, t1.descrizione, sum(t2.si),  sum(t2.no)
			from ".$prefix."_ele_gruppo as t1
        		left join ".$prefix."_ele_voti_$tab as t2 on (t1.id_gruppo=t2.id_gruppo)
			where 	t1.id_cons='$id_cons' and t1.id_gruppo='$id_gruppo' 
			group by t1.id_gruppo, t1.num_gruppo, t1.descrizione
			";
			$res1 = $dbi->prepare("$sql");
			$res1->execute();

			

			while (list($id_gruppo,$num_gruppo,$descrizione,$si,$no)  = $res1->fetch(PDO::FETCH_NUM)){
				if($tot){
				$percsi=number_format($si*100/$tot,3);
				$percno=number_format($no*100/$tot,3);
				$percsi=number_format($percsi,2);
				$percno=number_format($percno,2);
				}else{ $percsi="0.00"; $percno="0.00";}
             			$gruppo=array("si","no");
				$gruppos=array("","si","no");// flash
				$pro=array($percsi,$percno);
				$pre=array("",$percsi,$percno);//flash
//				echo "<br/><b><center>$descrizione</center><br/><br/>";
				// sviluppo tabella dati
				echo "<table><tr><td><table>"; // inizio tabella dati
				echo "<tr><td style=\"background-color:$bgcolor1;\">$num_gruppo - $descrizione</b></td></tr><tr></table>
				<table><tr>
				<td ><b>"._SI.":</b> </td><td>$si</td>
				<td ><b>%</b> </td><td>$percsi</td>
				<td ><b>"._NO.":</b> </td><td>$no</td>
				<td ><b>%</b> </td><td>$percno</td><tr>
				</table></td><td><center><br />";

				$i=8; // parametro lunghezza tavola
				$l=30; // larghezza label
				$titolo="Numero ".$num_gruppo."";
				$dati1=serialize($pro);
				//$dati1=urlencode($dati1); //IE
				$dati2=serialize($gruppo);
				$dati2=urlencode($dati2);
				$titolo=urlencode($titolo);
				if (isset($copy)) $copy=urlencode($copy); else $copy='';
				$descr=urlencode($descr_cons);
				echo "<center><img src='modules/Elezioni/grafici/barre.php?dati1=$dati1&amp;dati2=$dati2&amp;i=$i&amp;cop=$copy&amp;titolo=$titolo&amp;descr=$descr&amp;l=$l&amp;w=$w&amp;logo=$logo' alt=\"Grafico\" /></center>";
                                

				if($flash=='1')flash_torta($gruppos,$pre,20,70); 



				
                               
				echo "</td></tr></table>"; // fine tabella dati
				$s++;
			}


		}



	  }

	}


}




/***********************************
/* Grafica Candidato
/**********************************/

function graf_candidato(){
global $descr_com, $bgcolor1, $bgcolor5,$bgcolor5, $prefix, $dbi, $offset, $min,$descr_cons, $id_cons,$tipo_cons,$copy,$id_comune,$id_istat,$genere,$votog,$votol,$votoc,$circo,$siteistat,$id_circ;

$logo=verificasimbolo(); // carica_logo da funzioni.php	
if($circo) $circos=" and t2.id_circ='$id_circ'"; else $circos='';
		$tab="ele_voti_candidati";

		$sql="select id_sez  from ".$prefix."_ele_voti_candidati where id_sez in (select t1.id_sez from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t2.id_cons=$id_cons $circos) group by id_sez";
		$res = $dbi->prepare("$sql");
		$res->execute();

		$numero=$res->rowCount();
		$sql="select t1.id_sez from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t2.id_cons=$id_cons $circos";
		$res = $dbi->prepare("$sql");
		$res->execute();

		$sezioni=$res->rowCount();
		if ($numero!=0){
		echo "<table  border=\"0\"  align=\"center\"><tr><td>";
			echo "<h5>"._PREFERENZE." "._CONSI." $descr_cons </h5>";
			echo "<h5>"._SEZSCRU." $numero "._SU." $sezioni </h5>
			<h5>I 30 piu' votati</h5>";
			echo "</td></tr></table>";
			// tot voti
			$sql="select sum(voti)  from ".$prefix."_ele_voti_candidati where id_sez in (select t1.id_sez from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t2.id_cons=$id_cons $circos)";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($tot)  = $res->fetch(PDO::FETCH_NUM);
			
			// gruppi o liste per simbolo

			if ($genere==4){
				$scelta="_ele_lista as t2 on (t1.id_lista=t2.id_lista)";
			}else{
				$scelta="_ele_lista as t2 on (t1.id_lista=t2.id_lista)";
			}
			$i=0;
			$sql="select t1.id_lista,  t1.id_cand, t1.nome , t1.cognome, sum(t3.voti) as somma
				from ".$prefix."_ele_candidati as t1
        		left join ".$prefix."_ele_voti_candidati as t3 on (t1.id_cand=t3.id_cand)
				left join ".$prefix.$scelta."
				where t1.id_cons='$id_cons' $circos
				group by t1.id_lista, t1.id_cand, t1.nome, t1.cognome				 
				order by somma desc";
			$res = $dbi->prepare("$sql");
			$res->execute();

			$n_candi=$res->rowCount();
			echo "<table><tr><td><table>"; // inizio tabella dati
			while (list($id_lista,$id_cand,$nome,$cognome,$voti)  = $res->fetch(PDO::FETCH_NUM)){
             			$candidato[$i]=$cognome;
				if ($tot) $pro[$i]=number_format($voti*100/$tot,2); else $pro[$i]=0;
				// sviluppo tabella dati
				$e=$i+1;
				
				echo "<tr><td>[$e]</td><td><b><img alt=\"$nome $cognome\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_lista=$id_lista\" align=\"middle\" width=\"30\"></b></td>
                		<td bgcolor=\"$bgcolor1\"><b>$nome $cognome</b></td>
				<td bgcolor=\"$bgcolor1\">"._VOTI."</td><td>$voti</td>
				<td bgcolor=\"$bgcolor1\">%</td><td>$pro[$i]</td></tr>";
				
				if ($e=='30' || $e==$n_candi){ 
					
					
					
					echo "</table></td></tr></table>"; // fine tabella dati
					
					include("footer.php");
					exit;
				}

				

				$i++;
			
			
			
			
			}
          
        }

}



function flash_torta($gruppos,$pre,$y,$x){
//if (!defined('FLASH')) die();
include_once ("class/charts.php");
// inizio grafico torta flash
		
               
                
		$chart[ 'chart_data' ] = array ($gruppos, $pre);
//		$chart[ 'chart_data' ] = array ( array ("","Ciao", "Bella"), array ( "",40, 60));
		$chart[ 'chart_grid_h' ] = array ( 'thickness'=>0 );
		$chart[ 'chart_pref' ] = array ( 'rotation_x'=>60 ); 
		$chart[ 'chart_rect' ] = array ( 'x'=>$x, 'y'=>$y, 'width'=>350, 'height'=>200, 'positive_alpha'=>0 );
		$chart[ 'chart_transition' ] = array ( 'type'=>"spin", 'delay'=>.5, 'duration'=>.75, 'order'=>"category" );
		$chart[ 'chart_type' ] = "3d pie";
		$chart[ 'chart_value' ] = array ( 'color'=>"000000", 'alpha'=>150, 'font'=>"arial", 'bold'=>true, 'size'=>10, 'position'=>"inside", 'prefix'=>"", 'suffix'=>"", 'decimals'=>2, 'separator'=>"", 'as_percentage'=>true );

		$chart[ 'draw' ] = array ( array ( 'type'=>"text", 'color'=>"000000", 'alpha'=>4, 'size'=>40, 'x'=>50, 'y'=>260, 'width'=>500, 'height'=>50, 'text'=>"56789012345678901234", 'h_align'=>"center", 'v_align'=>"middle" )) ;

		$chart[ 'legend_label' ] = array ( 'layout'=>"horizontal", 'bullet'=>"circle", 'font'=>"arial", 'bold'=>true, 'size'=>10, 'color'=>"000000", 'alpha'=>85 ); 
		$chart[ 'legend_rect' ] = array ( 'x'=>0, 'y'=>45, 'width'=>50, 'height'=>210, 'margin'=>10, 'fill_color'=>"00ff00", 'fill_alpha'=>30, 'line_color'=>"000000", 'line_alpha'=>0, 'line_thickness'=>0 );  
		$chart[ 'legend_transition' ] = array ( 'type'=>"dissolve", 'delay'=>0, 'duration'=>4 );

		$chart[ 'series_color' ] = array ( "00ff88", "ffaa00","44aaff", "aa00ff" ); 
		$chart[ 'series_explode' ] = array ( 25, 75, 0, 0 );
		$fileout="";
/////////////////////////////// MODIFICHE PER TOGLIERE FILETEMP
//		$fileout=SendChartData ( $chart );
		$xml=SendChartData ( $chart );
		$max=isset($_SESSION['max']) ? $_SESSION['max']:0;
		$max++;
		$cur="xml$max";
		$_SESSION[$cur]=$xml;
		$_SESSION['max']=$max;
		$fileout="modules.php?file=graf&pos=$cur";
///////////////////////////////	
		echo InsertChart ( "class/charts.swf", "class/charts_library","$fileout" );
		
		// Fine Grafico torta flash
		echo "<noembed>Elemento non supportato!<br/>Grafico a torta</noembed>";
  

}







?>


