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


$sql="SELECT t1.descrizione, t1.tipo_cons,t2.genere, t2.voto_g, t2.voto_l, t2.voto_c, t2.circo FROM ".$prefix."_ele_consultazione as t1,".$prefix."_ele_tipo as t2 where t1.tipo_cons=t2.tipo_cons and t1.id_cons_gen='$id_cons_gen' ";
$res = $dbi->prepare("$sql");
$res->execute();
list($descr_cons,$tipo_cons,$genere,$votog,$votol,$votoc,$circo) = $res->fetch(PDO::FETCH_NUM);
$sql="SELECT t2.id_cons FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_cons_gen='$id_cons_gen' and t2.id_comune='$id_comune'";
$res = $dbi->prepare("$sql");
$res->execute();
list($id_cons) = $res->fetch(PDO::FETCH_NUM);

/*********************************/
/* Grafica votanti               */
/**********************************/

function votanti_mobile(){
global $op, $prefix, $dbi, $offset, $min,$descr_cons,$genere,$votog,$votol,$votoc,$circo, $id_cons,$tipo_cons,$id_comune,$id_cons_gen,$id_circ,$csv,$w,$l,$siteistat,$flash,$tour,$tema;
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ?
	$_GET : $_POST;
if ($siteistat==$id_comune) $logo="$siteistat"; else $logo=''; // logo per il  comune 
$logo=verificasimbolo(); // carica_logo da funzioni.php
	$tab="gruppo"; $tabr="gruppo";
	if ($genere==0) $tabr="ref";elseif($genere=='4' || $votog) {$tab="lista"; $tabr="lista";}
#		 else $tab="gruppo";
	if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
	else $circos=''; 


	$sql="select t1.id_sez from ".$prefix."_ele_voti_".$tabr." as t1 left join ".$prefix."_ele_$tab as t2 on t1.id_$tab=t2.id_$tab where t1.id_cons='$id_cons' $circos group by t1.id_sez ";
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
		echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
		echo "<center><h2>";
		echo "<b>Grafica "._DETTAGLIO." "._VOTIE."</b>";
		//echo "<i> "._SEZSCRU." $numero "._SU." $sezioni </i>";
		echo "</h2></center></li>";

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

		  list($max_ruotag) = $res1->fetch(PDO::FETCH_NUM);
			if (isset($_SESSION['ruotag'])) { $ruotag=$_SESSION['ruotag'];}
			else {$ruotag=1;$_SESSION['ruotag']=1;}
			if ($ruotag>=$max_ruotag) {$ruotag=1;}
			else {$ruotag++;}
			$_SESSION['ruotag']=$ruotag;
			$sql="SELECT count(0) from ".$prefix."_ele_voti_ref AS t1 LEFT JOIN ".$prefix."_ele_gruppo AS t2 ON t1.id_gruppo = t2.id_gruppo WHERE t1.id_cons ='$id_cons' AND t2.num_gruppo ='$ruotag'";
		$res1 = $dbi->prepare("$sql");
		$res1->execute();
		list($numero)=$res1->fetch(PDO::FETCH_NUM);
	echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
	echo "<center><h2>";
	echo "<b>Grafica "._DETTAGLIO." "._VOTIE."</b>  ";
	//echo "<i> "._SEZSCRU." $numero "._SU." $sezioni </i>";
	echo "</h2></center></li>";
		
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

		
		
		    
		
		echo "<div style=\"text-align:center\">
		<h1>"._PERCE." "._VOTANTI."</h1>";
		if ($genere==0) echo "<h2> "._GRUPPO." $num_gruppo</h2>";
		
		
		
			echo "<br /><img alt=\"Grafico\" width=\"300\" src=\"modules/Elezioni/grafici/votanti_graf.php?titolo=$titolo2&amp;e=$perc_votanti&amp;f=$perc_non&amp;e1=$e1&amp;f1=$f1&amp;logo=$logo\" /><br /><br /></div>";

		echo "<div style=\"text-align:center\">";
		echo "<h1>"._PERCE." "._VOTIE."</h1>";
		if ($genere==0) echo "<h2>  "._GRUPPO." $num_gruppo<h2> ";

		
			echo "<br /><img  alt=\"Grafico\" width=\"300\" src=\"modules/Elezioni/grafici/voti_graf.php?cop=&amp;titolo=$titolo&amp;a=$perc_validi&amp;b=$perc_nulli&amp;c=$perc_bianchi&amp;d=$perc_conte&amp;a1=$a1&amp;b1=$b1&amp;c1=$c1&amp;d1=$d1&amp;logo=$logo\" /><br /><br /></div>";


		}
	}

}






/***********************************
/* Grafica Gruppo
/**********************************/

function graf_gruppo_mob(){
global $dbi,$admin, $bgcolor1, $bgcolor5, $prefix, $offset, $min,$descr_cons,$genere,$votog,$votol,$votoc,$circo, $id_cons,$id_cons_gen,$id_comune,$id_circ,$tipo_cons,$w,$l,$op,$siteistat,$flash,$visgralista,$graficogruppo,$rss;
# parte grafica
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ?
	$_GET : $_POST;
if (isset($param['grafica'])) $grafica=intval($param['grafica']); else $grafica='';


if ($siteistat==$id_comune) $logo=$siteistat; else $logo=''; // logo per il  comune

	if (!$id_circ and $circo){
		$sql="SELECT id_circ from ".$prefix."_ele_circoscrizione where id_cons=$id_cons order by num_circ limit 0,1";
		$res_sez = $dbi->prepare("$sql");
		$res_sez->execute();
		list($id_circ)=$res_sez->fetch(PDO::FETCH_NUM);
	}
	$circond='';$circondt1='';
	if ($genere!=0){$tab="ele_voti_gruppo";}else{$tab="ele_voti_ref";}
	if ($genere==4 or $visgralista){$tab="ele_voti_lista";}
	if ($votog){$tab="ele_voti_lista";}
	$sql="select id_sez from ".$prefix."_$tab where id_cons='$id_cons' group by id_sez ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	if ($circo){
		$sql="select t1.*  from ".$prefix."_ele_voti_gruppo as t1, ".$prefix."_ele_gruppo as t2 where t1.id_gruppo=t2.id_gruppo and t1.id_cons='$id_cons' and t2.id_circ=$id_circ group by t1.id_sez ";
		$res = $dbi->prepare("$sql");
		$res->execute();

		$sql="select sum(t1.voti)  from ".$prefix."_ele_voti_gruppo as t1, ".$prefix."_ele_gruppo as t2 where t1.id_gruppo=t2.id_gruppo and t1.id_cons='$id_cons' and t2.id_circ=$id_circ";
		$circond="and id_circ=$id_circ";$circondt1="and t1.id_circ=$id_circ";
		$restotv = $dbi->prepare("$sql");
		$restotv->execute();
	}
	if ($res) $numero=$res->rowCount();else $numero=0;
	$sql="select t2.*  from ".$prefix."_ele_sezioni as t2, ".$prefix."_ele_sede as t1 where t2.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circondt1";
		$res = $dbi->prepare("$sql");
		$res->execute();
	if ($res) $sezioni=$res->rowCount();else $sezioni=0;


	if ($numero>0){
		echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
		echo "<center><b>"._PREFERENZE." "._GRUPPO."</b>";
		//echo "<br/><i> "._SEZSCRU." $numero "._SU." $sezioni </i><br/>";
		echo "</center></li>";
		

		if ($genere!=0){
		// tot voti
			if (!$circo) 
				$sql="select sum(voti)  from ".$prefix."_$tab where id_cons=$id_cons ";
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
        			left join ".$prefix."_$tab as t2 on (t1.id_gruppo=t2.id_gruppo)
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
        		left join ".$prefix."_$tab as t2 on (t1.id_lista=t2.id_lista)
				where 	t1.id_cons='$id_cons' and t1.id_cons=t2.id_cons
				group by t2.id_lista
				order by somma desc";
				$res = $dbi->prepare("$sql");
				$res->execute();
				$cosa='id_lista';
			}

		
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
				//$db->sql_data_seek($res,0);
				$res = $dbi->prepare("$sql");
				$res->execute();
#				mysql_data_seek($res,0);
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
			
	if($grafica!="1"){
		echo "<table style=\" text-align:left;border:1px solid Black;width:100%;\"><tr>\n<td width=\"55\"><a href=\"#dati\">
<img src=\"modules.php?name=Elezioni&amp;file=foto&amp;$cosa=$id\"  width=\"50\" align=\"left\" alt=\"\" /></a></td>\n
<td><span style=\"font-size:10px;\">$descrizione</span><br/>
			
			"._VOTI." <span style=\"font-size:20px;\"><b>$votiv</b> <i>($prov%)</i></span></td>\n
			</tr></table>\n";
 	}
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
		if ($genere==4){$w=700;$l=300;}else{$w=500;$l=180;}


		if($grafica=="1")
		echo "<br/><center><img  width=\"300\" src='modules/Elezioni/grafici/barre.php?dati1=$dati1&amp;dati2=$dati2&amp;i=$i&amp;cop=$copy&amp;titolo=$titolo&amp;descr=$descr_cons&amp;l=$l&amp;w=$w&amp;logo=$logo'  alt=\"Grafico\" /><br/></center>";
		
		
		
                
               
	}else{
		// tot voti
		$sql="
		select sum(validi),id_gruppo  from ".$prefix."_$tab where id_cons=$id_cons group by id_gruppo";
				$res = $dbi->prepare("$sql");
				$res->execute();
		while (list($tot,$id_gruppo)  = $res->fetch(PDO::FETCH_NUM)){

			$s=0;
			$sql="select t1.id_gruppo, t1.num_gruppo, t1.descrizione, sum(t2.si),  sum(t2.no)
			from ".$prefix."_ele_gruppo as t1
        		left join ".$prefix."_$tab as t2 on (t1.id_gruppo=t2.id_gruppo)
			where 	t1.id_cons='$id_cons' and t1.id_gruppo='$id_gruppo' 
			group by t1.id_gruppo
			";
				$res1 = $dbi->prepare("$sql");
				$res1->execute();
			

			while (list($id_gruppo,$num_gruppo,$descrizione,$si,$no)  = $res1->fetch(PDO::FETCH_NUM)){
				if($tot){
					$percsi=number_format($si*100/$tot,3);
					$percno=number_format($no*100/$tot,3);
					$percsi=number_format($percsi,2);
					$percno=number_format($percno,2);
				}else{ 
					$percsi="0.00"; $percno="0.00";
				}

             			$gruppo=array("si","no");
				$gruppos=array("","si","no");// flash
				$pro=array($percsi,$percno);
				$pre=array("",$percsi,$percno);//flash
//				echo "<br/><b><center>$descrizione</center><br/><br/>";
				// sviluppo tabella dati
				echo "<li><table style=\"text-align:left;border : 1px solid Black;width:100%;\">"; // inizio tabella dati
				echo "<tr><td >$num_gruppo - $descrizione</b></td></tr></table>
				<table style=\"text-align:left;border : 1px solid Black;width:100%;\" bgcolor=\"#ffffff\"  width=\"100%\">
				<tr>
				
				<td width=\"33%\">"._SI."</td>
				<td width=\"33%\"><b>$si voti</b></td>
				<td width=\"33%\"><b><span style=\"color:#ff0000\">$percsi %</span></b></td><table>
				<table  style=\"text-align:left;border : 1px solid Black;width:100%;\">
				<tr>
				<td width=\"33%\">"._NO."</td>
				<td width=\"33%\"><b>$no voti</b></td>
				<td width=\"33%\"><b><span style=\"color:#ff0000\">$percno %</span></b></td><tr>
				</table></li>";

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
				
				
				


				
                               
				
				$s++;
			}


		}



	  }

	}


}

/***********************************
/* Grafica liste
/**********************************/

function graf_liste_mob(){

global $id_cons,$id_cons_gen,$prefix,$dbi,$min,$offset,$op,$tipo_cons,$prev,$next,$votog,$votol,$circo,$genere,$id_comune,$colortheme;

$offset=10000;
if (!isset($min)) $min=0;

// numero sezioni scrutinate sul gruppo
	if ($circo) $circos = "and id_circ=$id_circ" ; else $circos='';
	if ($genere==0) $tab="ref"; else $tab="gruppo";
	$sql="select id_sez from ".$prefix."_ele_voti_".$tab." where id_cons='$id_cons'  $circos group by id_sez ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$numero=$res->rowCount();
	$sql="select *  from ".$prefix."_ele_sezioni where id_cons='$id_cons' $circos ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$sezioni=$res->rowCount();
	//$res = mysql_query("select chiusa  from ".$prefix."_ele_cons_comuni where id_cons='$id_cons' $circos ",$dbi);
	//$chiusa=mysql_num_rows($res);
	//if ($numero!=0 and $chiusa==0)
	
	if ($numero>0){
		
		echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
		echo "<center><b>Voti per Lista</b>";
		echo "<br/><i> "._SEZSCRU." $numero "._SU." $sezioni </i><br/>";
		echo "</center></li>";
	}

	
	// tot liste
		$sql="SELECT *  FROM ".$prefix."_ele_lista where id_cons='$id_cons'  ";
 				$res = $dbi->prepare("$sql");
				$res->execute();
   		$max = $res->rowCount();
		
		// tot voti liste
		$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons='$id_cons'";
				$res_lista_tutti = $dbi->prepare("$sql");
				$res_lista_tutti->execute();
		list($voti_lista_tutti) = $res_lista_tutti->fetch(PDO::FETCH_NUM);
		
    		$sql="select id_cons ,id_lista ,id_gruppo, num_lista, descrizione  from ".$prefix."_ele_lista where id_cons='$id_cons'  ORDER BY num_lista  LIMIT $min,$offset";
 				$result = $dbi->prepare("$sql");
				$result->execute();
		while(list($id_cons2,$id_lista,$id_gruppo, $num_lista, $descr_lista) = $result->fetch(PDO::FETCH_NUM)) {
  			if ($num_lista!=0) {
			// voti lista
			$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons='$id_cons' and id_lista='$id_lista'";
 				$res_lista = $dbi->prepare("$sql");
				$res_lista->execute();
			list($voti_lista) = $res_lista->fetch(PDO::FETCH_NUM);
			if ($voti_lista_tutti!=0)
			$perc_lista=number_format(($voti_lista*100)/$voti_lista_tutti,2);
		        else $perc_lista='';
			// gruppo
			$sql="select descrizione  from ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
 				$res_gruppo = $dbi->prepare("$sql");
				$res_gruppo->execute();
			list($descr_gruppo) = $res_gruppo->fetch(PDO::FETCH_NUM);


		 	/*echo "<table><tr class=\"bggray\">"
			."<td class=\"td-5\"><b>"._NUM."</b></td>"
			."<td ><b>"._DESCR."</b></td>"
			."<td class=\"td-5\"><b>"._SIMBOLO."</b></td>"
			."<td class=\"td-5\"><b>"._GRUPPO."</b></td></tr>";
			*/


			echo "<li><table style=\"text-align:left;border : 1px solid Black;width:100%\"><tr><td width=\"15\">$num_lista
			</td>
			<td width=\"60\">
			<img width=\"50\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_lista=$id_lista\" alt=\"$descr_lista\" /></td>

	
			<td width=\"320\"><span style=\"font-size:12px;\">$descr_lista</span><br />Voti:
			<b>$voti_lista </b><span style=\"color:#ff0000;font-size:12px;\"> ($perc_lista %)</span>";
			echo "</td>
			
			<td width=\"180\">
			<img class=\"stemma\" width=\"50\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_gruppo=$id_gruppo\" alt=\"$descr_gruppo\" />
			<br /><span style=\"font-size:10px;\">$descr_gruppo</span></td>
			</tr></table></li>";
			}
		}
  }





/***********************************
/* Grafica Candidato
/**********************************/

function graf_candidato_mob(){
global $dbi,$bgcolor1, $bgcolor5,$bgcolor5, $prefix, $offset, $min,$descr_cons, $id_cons,$tipo_cons,$copy,$id_comune,$id_istat,$genere,$votog,$votol,$votoc,$circo,$siteistat;
if ($siteistat==$id_comune) $logo='1'; else $logo=''; // logo per il  comune
$bgcolor1="";	

		$tab="ele_voti_candidati";

		$sql="select id_sez  from ".$prefix."_ele_voti_candidati where id_cons='$id_cons' group by id_sez ";
		$res = $dbi->prepare("$sql");
		$res->execute();
		$numero=$res->rowCount();
		$sql="select *  from ".$prefix."_ele_sezioni where id_cons='$id_cons'";
		$res = $dbi->prepare("$sql");
		$res->execute();
		$sezioni=$res->rowCount();


		if ($numero>0){
		
		echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
		echo "<center><b>Elenco candidati in ordine di voti</b>";
		echo "<br/><i> "._SEZSCRU." $numero "._SU." $sezioni </i><br/>";
		echo "</center></li>";
	
		
			// tot voti
			$sql="
			select sum(voti)  from ".$prefix."_ele_voti_candidati where id_cons=$id_cons ";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($tot)  = $res->fetch(PDO::FETCH_NUM);
			
			// gruppi o liste per simbolo

			if ($genere==4){
				$scelta="_ele_lista as t3 on (t1.id_lista=t3.id_lista)";
			}else{
				$scelta="_ele_lista as t3 on (t1.id_lista=t3.id_lista)";
			}
			$i=0;
			$sql="select t1.id_lista,  t1.id_cand, t1.nome , t1.cognome, sum(t2.voti) as somma
				from ".$prefix."_ele_candidati as t1
        		left join ".$prefix."_ele_voti_candidati as t2 on (t1.id_cand=t2.id_cand)
				left join ".$prefix.$scelta."
				where t1.id_cons='$id_cons'
				group by t1.id_lista, t1.id_cand, t1.nome, t1.cognome
				order by somma desc";
			$res = $dbi->prepare("$sql");
			$res->execute();
			$n_candi=$res->rowCount();
			
			while (list($id_lista,$id_cand,$nome,$cognome,$voti)  = $res->fetch(PDO::FETCH_NUM)){
             			$candidato[$i]=$cognome;
				$pro[$i]=number_format($voti*100/$tot,2);
				// sviluppo tabella dati
				$e=$i+1;
				echo "<li><table style=\"text-align:left;border : 1px solid Black;width:100%;\">"; // inizio tabella dati
				echo "<tr><td>".$e."°</td><td><b><img alt=\"$nome $cognome\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_lista=$id_lista\" align=\"middle\" width=\"30\"></b></td>
                		<td width=\"70%\" bgcolor=\"$bgcolor1\"><b>$nome $cognome</b><br/>
				"._VOTI.": <b>$voti </b>
				( <span style=\"color:#ff0000;\">$pro[$i]%</span> )</td></tr>";
				echo "</table></li>"; // fine tabella dati
				/* tolto il conteggio
				if ($e=='5000' || $e==$n_candi){ 
					
					
					
					
					
					//include("footer.php");
					exit;
				}
	
				*/

				$i++;
			
			       
			
			
			}
          
        }

}


######################################################
function numerodisezioni() {
/*Funzione numero sezioni scrutinate
/*****************************************************
Ritorna i dati in un array con quest'ordine:
numero di sezioni totali , numero di sezioni scrutinate
*******************************************************/


	
	global  $dbi,$db, $prefix, $circo, $genere,$id_cons_gen,$id_cons,$id_circ,$tipo_cons,$votog,$id_comune;
	if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
	else $circos=''; 

	//if ($genere==0) $tab="ref";elseif($genere=='4' || $votog) $tab="lista";
	if($genere=='4' || $votog) $tab="lista";
		 else $tab="gruppo";
	if ($genere==0) $tab="ref";


	$sql="select t1.*  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos";
			$res2 = $dbi->prepare("$sql");
			$res2->execute();
	$result_sezioni[1]=$res2->rowCount();



	// numero sezioni 
	$sql="select t3.id_sez  from ".$prefix."_ele_voti_".$tab." as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' $circos  group by t3.id_sez ";
	$sez_num = $dbi->prepare("$sql");
	$sez_num->execute();
	$result_sezioni[0]=$sez_num->rowCount();

//	$sez_scrut = $db->sql_query("select t1.*  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos");
	//$result_sezioni[1]=$db->sql_numrows($sez_scrut);


	$sql="select chiusa  from ".$prefix."_ele_cons_comune where id_cons='$id_cons' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	//$chiusa=$db->sql_numrows($res);
	list($chiusa) = $res->fetch(PDO::FETCH_NUM);
	//echo "-----------> $chiusa - $id_comune"; 

if($chiusa!=0)$result_sezioni[0]=0;	
return $result_sezioni;
		

	


}
####################################################
# Come 
####################################################

function come_mob($info) {
global  $prefix, $dbi, $offset, $min,$id_cons,$tipo_cons,$descr_cons,$id_comune;

$tab='';
if ($info=="come"){ $tab="_ele_come";$vista="Come si vota";}
elseif ($info=="numeri"){ $tab="_ele_numeri";$vista="Numeri Utili";}
elseif ($info=="servizi"){ $tab="_ele_servizi";$vista="Servizi Elettorali";}
elseif ($info=="link"){ $tab="_ele_link";$vista="Link Utili";}
else{ $tab="_ele_come";$vista="Come si vota";}


    global  $user, $admin, $cookie, $textcolor2;
    $sql="select mid, title, preamble, content,editimage from ".$prefix."$tab where id_cons='$id_cons' order by mid ";
	$result = $dbi->prepare("$sql");
	$result->execute();

	echo "<li data-role=\"list-divider\" >";
		echo "<center><b>$vista</b>";
		echo "</center></li>";



    if ($result->rowCount() == 0) {
	return;
    } else {
	while (list($mid, $title, $preamble,$content,  $editimage) = $result->fetch(PDO::FETCH_NUM)) {
  	if ($title != "" && $content != "") {
               
                if ($info=="link"){
			
			echo "<li>
			<b><a href=\"$preamble\">$title</a></b>
			$content
			</li>";
			
		}else{
			echo "<li><div><b>$title</b><br /></div>";
                
		
		echo "<div class=\"message\">$preamble<br /><br /></div>";
		
		echo "<div class=\"message\">$content</div></li>";
		}
		
		

	}
    }
 }

}



/****************
Funzione dati Generali
visuallizza la stringa dei dati generali
****************/


function dati_mob($print) {
global $db,$prefix, $dbi,  $votog, $votol, $votoc, $circo, $id_cons,$tipo_cons,$descr_cons,$id_cons_gen,$id_comune,$genere,$id_circ;
	$print=intval($print);

	$sql="select * from ".$prefix."_ele_circoscrizione where id_cons='$id_cons' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$sql="select * from ".$prefix."_ele_sede where id_cons='$id_cons' ";
	$ressede = $dbi->prepare("$sql");
	$ressede->execute();
	$sql="select * from ".$prefix."_ele_sezioni where id_cons='$id_cons' ";
	$res3 = $dbi->prepare("$sql");
	$res3->execute();
	$circo = $res->rowCount();
	$sedi = $ressede->rowCount();
	$sez = $res3->rowCount();

	//Variabili linguaggio
	$DATIG=_DATIG;$AVENTI=_AVENTI;$MASCHI=_MASCHI;$FEMMINE=_FEMMINE;$SEZIONI=_SEZIONI; $GRUPPI=_GRUPPI;$CANDIDATI=_CANDIDATI;$LISTE=_LISTE;
	if ($circo>1)$SEDE=_CIRCS; else $SEDE=_SEDI;	
    
	$candi=0;

	// se non referendum
	if ($genere!=0 and !$votoc){
		$sql="select id_cons from ".$prefix."_ele_candidati where id_cons='$id_cons' ";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();
		$candi = $res1->rowCount();
	}
	
	// se non europee (non liste e candidati)
	if ($genere!=4){
		$sql="select id_cons from ".$prefix."_ele_gruppo where id_cons='$id_cons' ";
	}else{
		$sql="select id_cons from ".$prefix."_ele_lista where id_cons='$id_cons' ";
	}
	$res2 = $dbi->prepare("$sql");
	$res2->execute();
	
	$gruppo = $res2->rowCount();
        
	// camera e senato con raggruppamenti
	//if($votog){
	$sql="select * from ".$prefix."_ele_lista where id_cons='$id_cons' ";
	$res3 = $dbi->prepare("$sql");
	$res3->execute();
         $liste = $res3->rowCount();
	//}

	if($circo==1) $circo=$sedi;
	$sql="select sum(maschi),sum(femmine), sum(maschi+femmine)  from ".$prefix."_ele_sezioni where id_cons=$id_cons";
	$res4 = $dbi->prepare("$sql");
	$res4->execute();
 	if($res4) list($maschi,$femmine,$tot) = $res4->fetch(PDO::FETCH_NUM);


 	
		$ris = array($tot,$maschi,$femmine,$circo,$sez,$gruppo,$candi,$liste);
		return $ris;
	

}

####################################
## funzione che richiama i dati generali
## function dati_mob e li stampa

function dati_mob_fun(){
$dati=dati_mob(0);
global $genere;
if($genere==0)$gruppo="Quesiti";else$gruppo="Gruppi";
echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
echo "<b> <center>Dati Generali della consultazione </center></b></li>";
echo "<br/><div style=\"margin:0 auto;width:300px;\"><ul>";
echo "<li>Aventi Diritto: $dati[0]</li>";
echo "<li> Maschi: $dati[1]</li>";
echo "<li>Femmine: $dati[2]</li>";
if($dati[3]!=0) echo "<li>Num. Circoscrizioni: $dati[3]</li>";
echo "<li>Num: Sezioni: $dati[4]</li>";
if($dati[5]!=0)echo "<li>Num: $gruppo: $dati[5]</li>";
if($dati[6]!=0)echo "<li>Num. Candidati: $dati[6]</li>";
if($dati[7]!=0)echo "<li>Num. Liste: $dati[7]</li>";
echo "</ul></div>";
}




function circo_mob() {

/******************************************************/
/*Funzione di visualizzazione sede                    */
/*****************************************************/
    global $admin, $prefix, $dbi, $offset, $min,$id_cons,$file,$id_cons_gen,$id_comune ,$prev,$next;
    $sql="SELECT * FROM ".$prefix."_ele_circoscrizione where id_cons='$id_cons'  ";
	$res = $dbi->prepare("$sql");
	$res->execute();
    $max = $res->rowCount();
    
    //OpenTable();
    
    dati();
    
    
    $offset=10;
    if (!isset($min)) $min=0;
    $go="circo";
    
    $sql="select * from ".$prefix."_ele_circoscrizione where id_cons='$id_cons'  ORDER BY num_circ 
    LIMIT $min,$offset";
	$result = $dbi->prepare("$sql");
	$result->execute();
$numcirc=$result->rowCount();
if ($numcirc>1){
  	echo "<div><b>"._CIRCS."</b></div><br /><br />
	<table class=\"table-80\"><tr class=\"bggray\">"
	."<td ><b>"._NUM."</b></td>"
	."<td ><b>"._CIRCO."</b></td>"
	."<td ><b>"._INDIRIZZO."</b></td>"
	."<td><b>"._TEL."</b></td></tr>";
}else{
  	echo "<div><b></b></div><br /><br />
	<table class=\"table-80\"><tr class=\"bggray\">"
	."<td ><b>"._INDIRIZZO."</b></td>"
	."<td><b>"._TEL."</b></td></tr>";
}
     
	while(list($id_cons2,$id_circ,$num_circ,$descr_circ) = $result->fetch(PDO::FETCH_NUM)) {
#if($numcirc==1) {$descr_circ=''; $num_circ='';}
     	if (!($num_circ===0)) {
     		
		echo "<tr class=\"bggray3\">";
		if ($numcirc>1) {
		echo "<td><b>$num_circ</b>"
		."</td><td><b>";
		echo "<a href=\"modules.php?name=Elezioni&amp;op=sezione&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=$id_circ&amp;descr_circ=$descr_circ\">$descr_circ</a></b></td>";
		}
       
       // dati sede
		$sql="select id_sede,indirizzo,telefono1,telefono2, mappa, filemappa from ".$prefix."_ele_sede where id_cons='$id_cons' and id_circ='$id_circ'";
	$result1 = $dbi->prepare("$sql");
	$result1->execute();
		$righe=$result1->rowCount();$i=0;
        	while(list($id_sede,$indir,$tel1,$tel2,$mappa,$filemappa)=$result1->fetch(PDO::FETCH_NUM)){
		
        	$i++;
		echo "<td><b><a href=\"modules.php?name=Elezioni&file=iphone&amp;op=sezione&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_sede=$id_sede&amp;indirizzo=$indir\">$indir</a></b>"
		."</td><td><b>$tel1 </b></td><td><b>  $tel2</b></td></tr>";

	      	if ($i<$righe) echo"<tr class=\"bggray3\">";
	      	if ($numcirc>1) echo "<td></td><td></td>";

	}
    }
  }
    echo "</table>";
    
    //page($id_cons_gen,$go,$max,$min,$prev,$next,$offset,$file);

//CloseTable();
}

/******************************************************/
/*Funzione di visualizzazione globale sezioni         */
/*****************************************************/

function sezione_mob() {
   global $admin, $prefix, $dbi, $offset, $min,$votog,$circo, $id_cons_gen,$id_circ,$descr_circ,$id_cons,$file,$prev,$next,$id_comune,$googlemaps;

 if(!isset($_GET['id_circ'])) unset($id_circ);
 //dati();
 $totali_t=0;$maschi_t=0;$femmine_t=0;
 $param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
 //mappa
if (isset($param['id_sede'])) $id_sede=intval($param['id_sede']); else $id_sede='0';
if (isset($param['indirizzo'])) $indirizzo=$param['indirizzo']; else $indirizzo='';
 if ($id_sede!='0' && $googlemaps!='1'){

 echo "$indirizzo<br /><div><img src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_sede=".$id_sede."\" alt=\"mappa\" /></div>";
 }elseif($id_sede!='0' && $googlemaps=='1'){
	echo "$indirizzo";
    $mappa=googlemaps(); echo $mappa;
 } 


//CloseTable();
}


function genere(){
global $prefix,$db,$id_cons_gen;
$sql="SELECT descrizione,genere FROM ".$prefix."_ele_consultazione where id_cons_gen='$id_cons_gen' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($descr_cons,$genere) = $res->fetch(PDO::FETCH_NUM);
        //$descr_cons =stripslashes($descr_com); 
		


echo $genere;
}


########################################################
# Affluenze
function affluenze_mob() {
global $bgcolor1, $bgcolor2, $prefix, $dbi, $offset,$genere,$votog,$votol,$votoc,$circo, $min,$id_cons,$tipo_cons,$id_cons_gen,$csv,$id_comune,$id_circ, $tema;
// icone
	if ($circo) $circos="and t2.id_circ='$id_circ'";
	else $circos="";
	if (!$csv)

 
	        // numero sezioni scrutinate
                //if ($circo)$circos="and id_circ='$id_circ'";
        if (!isset($data1)) $data1='';
        if (!isset($ora_ril)) $ora_ril='';
  		$sql="SELECT count(data) FROM ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' and data='$data1' and orario='$ora_ril' $circos group by t3.id_gruppo";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();
# mysql_query("select *  from ".$prefix."_ele_voti_parziale where id_cons='$id_cons' $circos  group by id_sez ",$dbi);
		$numero=$res1->rowCount();
		$sql="SELECT t1.* FROM ".$prefix."_ele_sezioni as t1, ".$prefix."_ele_sede as t2 where t1.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circos order by num_sez";
	$res2 = $dbi->prepare("$sql");
	$res2->execute();
#mysql_query("select *  from ".$prefix."_ele_sezioni where id_cons='$id_cons' $circos",$dbi);
		$sezioni=$res2->rowCount();
		
	
	
		
	
// barre
    $l_size = getimagesize("modules/Elezioni/images/barre/leftbar.gif");
    $m_size = getimagesize("modules/Elezioni/images/barre/mainbar.gif");
    $r_size = getimagesize("modules/Elezioni/images/barre/rightbar.gif");
    $l_size2 = getimagesize("modules/Elezioni/images/barre/leftbar2.gif");
    $m_size2 = getimagesize("modules/Elezioni/images/barre/mainbar2.gif");
    $r_size2 = getimagesize("modules/Elezioni/images/barre/rightbar2.gif");
                                                                                                                           // totali

	if($tema=='tour') $andcond="select orario,data  from ".$prefix."_ele_voti_parziale where id_cons='$id_cons' order by data desc,orario desc limit 0,1"; 
	else $andcond="select orario,data  from ".$prefix."_ele_rilaff where id_cons_gen='$id_cons_gen' order by data,orario";    

    $sql="$andcond";
 	$res = $dbi->prepare("$sql");
	$res->execute();
       while(list($orario,$data) = $res->fetch(PDO::FETCH_NUM)) {
        	list ($ore,$minuti,$secondi)=explode(':',$orario);
        	list ($anno,$mese,$giorno)=explode('-',$data);
        	$tot_v_m=0;$tot_v_d=0;$tot_t=0;
	
		echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
		echo "<center>"._VOTANTI." "._ALLE." "._ORE." $ore,$minuti "._DEL."  $giorno/$mese/$anno</center></li>";
               
  		$sql="SELECT count(data) FROM ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' and data='$data' and orario='$orario' $circos group by t3.id_gruppo";                                                                                                                             
	$res1 = $dbi->prepare("$sql");
	$res1->execute();
list($numero)=$res1->fetch(PDO::FETCH_NUM);	

		
		$sql="select sum(t3.voti_complessivi), t4.num_gruppo , t4.id_gruppo   from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede left join ".$prefix."_ele_gruppo as t4 on (t3.id_gruppo=t4.id_gruppo) where t3.id_cons='$id_cons' and t3.orario='$orario' and t3.data='$data' $circos  group by t4.num_gruppo, t4.id_gruppo order by t4.num_gruppo ";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();

		
		
                                                                                                                             
                while(list($voti_t, $num_gruppo,$id_gruppo) = $res1->fetch(PDO::FETCH_NUM)) {
 /*               	$query="select sum(voti_complessivi) from ".$prefix."_ele_voti_parziale where orario='$orario' and data='$data' and id_cons='$id_cons'";
		
                	if ($genere==0){$query.=" and id_gruppo=$id_gruppo";}
                	
					$res_aff = $dbi->prepare("$query");
					$res_aff->execute();
					$voti_numero=$res_aff->rowCount();*/
                	$query="SELECT sum(maschi+femmine) FROM ".$prefix."_ele_sezioni as t1, ".$prefix."_ele_sede as t2 where t1.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circos";

                	
					$res1234 = $dbi->prepare("$query");
					$res1234->execute();
                	list($tot)=$res1234->fetch(PDO::FETCH_NUM);
                	
                	$perc=number_format($voti_t*100/$tot,2);
                                                                                                                            			
echo "<li><table style=\"text-align:left;border : 1px solid Black;width:100%\"><tr>";
			if ($genere==0){echo "<td>Refer. N.</td>";}
                	echo "<td><b>"._VOTANTI."</b></td><td><b>Percent.</b></td>";
                	echo "<td><b>"._SEZIONI."</b></td>";
			echo "</tr>";
        		echo "<tr>";
        		if ($genere==0){echo "<td><h2>$num_gruppo</h2></td>";}
        		echo "<td>$voti_t</td><td>$perc %</td><td>$numero</td>
			</tr></table>";
	

        // barre
                                                                                                                             
        	echo "<table style=\"width:100%\"><tr><td><table><tr><td>&nbsp;"._VOTANTI."       : </td><td>
<img src=\"modules/Elezioni/images/barre/leftbar2.gif\" height=\"$l_size2[1]\" width=\"$l_size2[0]\" alt=\"\" /><img src=\"modules/Elezioni/images/barre/mainbar2.gif\" alt=\"\" height=\"$m_size2[1]\" width=\"". ($perc * 1)."\" /><img src=\"modules/Elezioni/images/barre/rightbar2.gif\" height=\"$r_size2[1]\" width=\"$r_size2[0]\" alt=\"\" /> $perc% <br /></td></tr>\n";
		
		$tot_gen=$tot;


		echo  "<tr><td>&nbsp; </td><td><img src=\"modules/Elezioni/images/barre/leftbar.gif\" height=\"$l_size[1]\" width=\"$l_size[0]\" alt=\"\" /><img src=\"modules/Elezioni/images/barre/mainbar.gif\" alt=\"\" height=\"$m_size[1]\" width=\"".(100 * 1)."\" /><img src=\"modules/Elezioni/images/barre/rightbar.gif\" height=\"$r_size[1]\" width=\"$r_size[0]\" alt=\"\" /> 100% </td></tr></table>";
		 echo "</td></tr></table></li><br/>";
		 
	}	

        }


}

## risultati semplici
function graf_risultati(){
global $descr_cons,$circo,$genere;

	if($genere!='0' && !$circo){ // referendum e circoscrizionali
	    list ($gruppo,$pro)=grupporss();
	    if ($gruppo!=''){ 
		echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
		echo "<center><h2>";
		echo "<b>Risultati</b>  ";
		//echo "<i> "._SEZSCRU." $numero "._SU." $sezioni </i>";
		echo "</h2></center></li>";
		}
		
	    //$content .="<div style=\"text-align:left;\"><strong>$descr_cons</strong></div><br/>";
	    echo "<li><table bgcolor=\"gray\" width=\"100%\" cellspacing=\"1\">";
	    for($x=0;$x<count($gruppo);$x++){
		$nume=$x+1;
		echo "<tr bgcolor=\"#ffffff\"><td>$nume - ".$gruppo[$x]." </td><td  align=\"right\"><b><span style=\"color:#ff0000;\">".$pro[$x] ."%</span></b></td></tr>\n";
	    }
	    echo "</table></center>";

	}

}

###############################
# grafico affluenza unica

function affluenza_unica(){
global $dbi,$circo,$prefix,$id_cons,$genere,$id_circ,$id_comune,$id_cons_gen;

if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
else $circos=''; 
$circos='';
// numero sezioni scrutinate

	$sql="select t1.*  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons'";
	$res2 = $dbi->prepare("$sql");
	$res2->execute();
	$sezioni=$res2->rowCount();

    $sql="select orario,data  from ".$prefix."_ele_voti_parziale where id_cons='$id_cons' order  by data desc,orario desc limit 1";
	$res = $dbi->prepare("$sql");
	$res->execute();
    if($res){

        while(list($orario,$data) = $res->fetch(PDO::FETCH_NUM)) {
        	list ($ore,$minuti,$secondi)=explode(':',$orario);
        	list ($anno,$mese,$giorno)=explode('-',$data);
        	$tot_v_m=0;$tot_v_d=0;$tot_t=0;
	

  		$sql="select t3.id_sez from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' group by t3.id_sez ";
		$res1 = $dbi->prepare("$sql");
		$res1->execute();
		$numero=$res1->rowCount();
	
		echo "<div style=\"text-align:center;\">Ultime Affluenze<br/>";
               echo "<b>"._ORE." $ore,$minuti "._DEL."  $giorno/$mese/$anno</b></div>";
                                                                                                                             

    
                                                                                                                                      
$sql="select sum(t3.voti_complessivi), t4.num_gruppo , t4.id_gruppo   from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede left join ".$prefix."_ele_gruppo as t4 on (t3.id_gruppo=t4.id_gruppo) where t3.id_cons='$id_cons' and t3.orario='$orario' and t3.data='$data' $circos  group by t4.num_gruppo, t4.id_gruppo order by t4.num_gruppo ";		
	$res1 = $dbi->prepare("$sql");
	$res1->execute();
		
		
                                                                                                                             
                while(list($voti_t, $num_gruppo,$id_gruppo) = $res1->fetch(PDO::FETCH_NUM)) {

			$query="select sum(t3.voti_complessivi)  from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' $circos";		
             if ($genere==0){$query.=" and t3.id_gruppo=$id_gruppo";}
                	
			$res_aff = $dbi->prepare("$query");
			$res_aff->execute();
			$voti_numero=$res_aff->rowCount();
              	
#			$query="select sum(t1.maschi+t1.femmine)  from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' ";	

			$query="SELECT sum(maschi+femmine) FROM ".$prefix."_ele_sezioni as t1, ".$prefix."_ele_sede as t2 where t1.id_cons='$id_cons' and t1.id_sede=t2.id_sede ";
			
			//if ($genere==0){$query.=" and t3.id_gruppo=$id_gruppo";}
			$tot='';
			$res1234 = $dbi->prepare("$query");
			$res1234->execute();
            list($tot)=$res1234->fetch(PDO::FETCH_NUM);
                	if (isset($tot)){$perc=number_format($voti_t*100/$tot,2);}
			else{$tot=0;$perc="0.00";}
			if($voti_t<=$tot){
	  
			$resto=100-$perc;
			if ($genere==0){echo "<div style=\"text-align:center\"><b>Referendum n. $num_gruppo</b></div>";}

			echo "<div style=\"margin:0 auto;width:300px;\"><img src=\"http://chart.apis.google.com/chart?
			chs=300x200
			&chd=t:$perc
			&cht=gom
			&chl=$perc%
			&chco=ff0000,ffff00 \"
			alt=\"Sample chart\" />
			 
			</div>";

			}

	}	

        }
}

}


###############################
# votanti in tabella

function votanti_tabella(){

global $op, $prefix, $offset, $min,$descr_cons,$genere,$votog,$votol,$votoc,$circo, $id_cons,$tipo_cons,$id_comune,$id_cons_gen,$id_circ,$csv,$w,$l,$siteistat,$flash,$tour,$dbi;




if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
else $circos=''; 
$circos='';
//if ($genere==0) $tab="ref";elseif($genere=='4' || $votog) $tab="lista";
if($genere=='4' || $votog) $tab="lista";
		 else $tab="gruppo";
if ($genere==0) $tab="ref";

  	$sql="select t3.id_sez from ".$prefix."_ele_voti_".$tab." as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' $circos  group by t3.id_sez ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$numero=$res->rowCount();
	$sql="select t1.*  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$sezioni=$res->rowCount();
	
	if ($numero!=0){

	echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
	echo "<center><b>"._DETTAGLIO." "._VOTIE."</b></center></li>";
	echo "<div style=\"text-align:center;\"><i> "._SEZSCRU." $numero "._SU." $sezioni </i></div>";
	



	if ($genere!=0) {

	$sql="select sum(t1.maschi+t1.femmine)   from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' and validi>0 $circos";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($tot_aventi) = $res->fetch(PDO::FETCH_NUM);

	$sql="select sum(t1.validi+t1.nulli+t1.bianchi+t1.contestati) as tot,
		sum(t1.validi),sum(t1.nulli),sum(t1.bianchi),sum(t1.contestati), '0', '0', '0'
 		from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' and validi>0 $circos";
	$res = $dbi->prepare("$sql");
	$res->execute();

	}else{
	$sql="SELECT sum(maschi+femmine) FROM ".$prefix."_ele_sezioni where id_cons='$id_cons'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($tot_aventi) = $res->fetch(PDO::FETCH_NUM);
	$sql="SELECT  sum(validi+nulli+bianchi+contestati) as tot,
		sum(validi),sum(nulli),sum(bianchi),sum(contestati), id_gruppo, sum(si), sum(no)
		from ".$prefix."_ele_voti_ref  where id_cons=$id_cons group by id_gruppo having tot>'0'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	}

	while (list($tot_votanti,$validi,$nulli,$bianchi,$contestati,$id,$si,$no) = $res->fetch(PDO::FETCH_NUM)){
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
		  

		    
		
		echo "<li><table bgcolor=\"gray\" width=\"100%\" cellspacing=\"1\">";
		if ($genere==0) {echo "<br/>Referendum n. <b>[$num_gruppo]</b><br/>";}
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

		</table></li>";

    }
}
}



######## fine






####################################
# grafico sezioni

function graf_sezioni(){
global  $prefix, $dbi,$id_cons_gen;
$sql = "select chiusa  from ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen'";
	$res = $dbi->prepare("$sql");
	$res->execute();
		list($chiusa) = $res->fetch(PDO::FETCH_NUM);
		
		//if($chiusa!='1') numeri_sezione(); # se la consultazione non è chiusa
		


//numeri_sezione(); //lancia la funzione 

function numeri_sezione() {
global  $prefix, $dbi, $circo, $genere,$id_cons_gen,$id_cons,$id_circ,$tipo_cons,$votog,$id_comune;


if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
else $circos=''; 

		if ($genere==0) $tab="ref";elseif($genere=='4' || $votog) $tab="lista";
		 else $tab="gruppo";
		


		# numero sezioni
		$sql="select t1.id_sez,t1.num_sez  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos order by t1.num_sez";
	$res = $dbi->prepare("$sql");
	$res->execute();
		$max = $res->rowCount();
		if(!isset($html)) $html='';
		$html = "\n<table  style=\"margin:0px auto;border:0px; width:90%\"><tr>";
		
		$i=0;$id_circ_old=0;$e=0;
		while(list($sez_id, $sez_num) = $res->fetch(PDO::FETCH_NUM)) {
			$i++;

			$sql="select *  from ".$prefix."_ele_voti_".$tab." where   id_sez='$sez_id'";
			$res2 = $dbi->prepare("$sql");
			$res2->execute();
			$numero=$res2->rowCount(); 
			 if ($numero!=0){$e++;$bgsez="#FFFF00";}else{$bgsez="";}
			

			if ($genere==0) $pos="gruppo_sezione";elseif($genere=='4' || $votog) $pos="lista_sezione";
		 	else $pos="gruppo_sezione"; 
			
			//$html .="<td style=\"margin:0px auto; text-align:center; width:5%;\" bgcolor=\"$bgsez\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&name=Elezioni&id_comune=$id_comune&perc=true&file=index&op=$pos&minsez=$sez_num&offsetsez=$sez_num\"><b>$sez_num</b></a></td>";
			$html .="<td style=\"margin:0px auto; text-align:center; width:5%;\" bgcolor=\"$bgsez\"><b>$sez_num</b></td>";

			if (($i%8) ==0) $html .="</tr>\n<tr>";
		}
		
		$html .="</tr></table>\n";
    // stampa
    if($e!='0'){
	echo "<li data-role=\"list-divider\" data-icon=\"arrow-up\" >";
		echo "<center>";
		echo "<b>"._SEZSCRU."</b>  ";
	 	echo "</center></li><br/></center>";
	  echo "<center><img  alt=\"Grafico\" src=\"modules/Elezioni/grafici/ledex2.php?sez=$e&max=$max\" /></center>";
	   
    echo $html;	}	
}
numeri_sezione(); //lancia la funzione 
}

?>


