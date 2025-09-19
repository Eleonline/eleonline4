<?php

/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/


/*******************************************************
     #'Pagina precedente' e 'Pagina Successiva' #
        immettere nel file da dividere la lettura
	del numero complessivo dei dati:
	Es: $max=sql_num_rows($res);

	l'offset settato al numero dei dati da
	visualizzare per pagina:
	Es: $offset=5;

	l'azzeramento della variabile $min
	Es: if (!isset($min)) $min=0;

	il nome del file (opzione) da ricaricare)
	Es: 	$file="gruppopercsez"; // nome op per pagine


*/



function page($id_cons,$go,$max,$min,$prev,$next,$offset,$file){
global $lettera,$ordine,$id_comune;


      echo"<br /><div class=\"modulo\">";
      $prev=$min-$offset;
      if ($prev>=0) {
              echo "<a href=\"modules.php?name=Elezioni&amp;op=$go&amp;min=$prev&amp;id_cons_gen=$id_cons&amp;id_comune=$id_comune&amp;file=$file&amp;lettera=$lettera&amp;ordine=$ordine\">";
	      echo "[ <b>$offset "._PREV_MATCH."</b> ]</a>";
      }

      $next=$min+$offset;
      if ($next>=($offset-1)) {
          if($next>=$max) $next = $max;
	  else {

              echo "<a href=\"modules.php?name=Elezioni&amp;op=$go&amp;min=$next&amp;id_cons_gen=$id_cons&amp;id_comune=$id_comune&amp;file=$file&amp;lettera=$lettera&amp;ordine=$ordine\">";
              echo " [<b>$offset "._NEXT_MATCH."</b> ] </a>";
        }
      }
     echo "</div><br /><br/>";

}





// *********************************
// Funzioni formattazione data 
// ***********************************


function giorno()
{
$giorni= array('--','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
foreach($giorni as $gi)
	$giorno .= "<option value=$gi>$gi</option>";
$giorno .= "</select>";
echo "$giorno";
}


function mese()
{
$mesi= array('--','01','02','03','04','05','06','07','08','09','10','11','12');
foreach($mesi as $me)
 	$mese .= "<option value=$me>$me </option>";
$mese .= "</select>";
echo "$mese";
}

function anno()
{
$curr=date("Y",time());
$anni=array('--',$curr--,$curr--,$curr--,$curr--,$curr--,$curr--,$curr--);
foreach($anni as $an)
	$anno .= "<option value=$an>$an</option>";
$anno .= "</select>";
echo "$anno";
}

function ore()
{
$ore= array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24');
foreach($ore as $ori)
	$ore .= "<option value=$ori>$ori</option>";
$ore .= "</select>";
echo "$ore";
}


function minuti()
{
$minuti= array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14',
'15', '16', '17', '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33',
'34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50',
'51','52','53','54','55','56','57','58','59');
foreach($minuti as $minu)
 	$minuti .= "<option value=$minu>$minu </option>";
$minuti .= "</select>";
echo "$minuti";
}

function secondi()
{
$secondi= array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14',
'15', '16', '17', '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33',
'34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50',
'51','52','53','54','55','56','57','58','59');
foreach($secondi as $sec)
	$secondi .= "<option value=$sec>$sec</option>";
$secondi .= "</select>";
echo "$secondi";
}



function form_data($data)

	{
        list($anno,$mese,$giorno) = explode("-", $data) ;
        if ($giorno>0)
               return("$giorno-$mese-$anno");
        else
               return("&nbsp; ");
        }


function taglio($parole,$descrizione)
{

/* funzione per il taglio di una frase giusta senza
troncare le parole by luc 13 febbraio 2007
uso:
taglio(numero di parole, frase)
*/

$lunghezza=strlen($descrizione);
$altra='0';
$testo='';
$lettera='';


for ($x=0;$x<$parole;$x++){
	if($testo!='')$testo .=" ";
	
	for ($j=$altra;$j<$lunghezza;$j++){
		$lettera=$descrizione[$j];
		if ($lettera!=" "){ 
			$testo .=$lettera;
		}else{ 
			$altra=$j+1; $j=$lunghezza;
		}
	
	}
}


return "$testo";

}
#### legge freed rss 1.0

function readrss($url) {
 
 
	$rdf = parse_url($url);
	$fp = fsockopen($rdf['host'], 80, $errno, $errstr, 15);
	if (!$fp) {
	    $content = "";
	    echo  $content;
	    return;
	}

	if ($fp) {
	    if (!empty($rdf['query']))
	        $rdf['query'] = "?" . $rdf['query'];

	    fputs($fp, "GET " . $rdf['path'] . $rdf['query'] . " HTTP/1.0\r\n");
	    fputs($fp, "HOST: " . $rdf['host'] . "\r\n\r\n");
	    $string	= "";

	    while(!feof($fp)) {
	    	$pagetext = fgets($fp,300);
	    	$string .= chop($pagetext);
	    }




	    fputs($fp,"Connection: close\r\n\r\n");
	    fclose($fp);
	    $items = explode("</item>",$string);
	    $content ="<h5>Risultati </h5>";
	    $content .= "<span class=\"content\">";
	    
	    for ($i=0;$i<20;$i++) {
		$link = preg_replace("/.*<link>/","",$items[$i]);
		$link = preg_replace("/</link>.*/","",$link);
		$title2 = preg_replace("/.*<title>/","",$items[$i]);
		$title2 = preg_replace("/</title>.*/","",$title2);
		$title2 = stripslashes($title2);
		$descr = preg_replace("/.*<description>/","",$items[$i]);
		$descr  = preg_replace("/</description>.*/","",$descr );
		$descr  = stripslashes($descr );

		if (empty($items[$i]) AND $cont != 1) {
		    $content = "";
		    
		    $cont = 0;

		    echo  $content;
		    return;
		} else {
		    if (strcmp($link,$title2) AND !empty($items[$i])) {
			$cont = 1;
			$content .= "<strong><big>&middot;</big></strong> <a href=\"$link\" target=\"new\">$title2</a> $descr<br />\n";
		    }
		}
	    
	  }
	
	
      }
   
    if (($cont == 0) OR (empty($content))) {
	$content = "<span class=\"content\">Problema feed rss</span>";
    }
    
	echo  $content;
    
}
### legge i risultati e li reinvia
### richiamare con 
### list ($gruppo,$pro)=grupporss();

function grupporss() {
global $admin, $bgcolor1, $bgcolor5, $prefix, $dbi, $offset, $min,$descr_cons,$genere,$votog,$votol,$votoc,$circo, $id_cons,$id_cons_gen,$id_comune,$id_circ,$tipo_cons,$w,$l,$op,$siteistat,$flash,$circondt1 ;

	if($circo and !$id_circ) $id_circ=$_SESSION['id_circ'];
	if($genere=='4' || $votog) $tab="ele_voti_lista";
	else $tab="ele_voti_gruppo";
	if($circo and !$circondt1) $circondt1="and t1.id_circ=$id_circ"; 
      
  
	$sql="select id_sez  from ".$prefix."_$tab where id_cons='$id_cons' group by id_sez ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	$numero=$res->rowCount();
	$sql="select t2.*  from ".$prefix."_ele_sezioni as t2, ".$prefix."_ele_sede as t1 where t2.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circondt1";
	$res = $dbi->prepare("$sql");
	$res->execute();

	if ($res) $sezioni=$res->rowCount();else $sezioni=0;
	$pro=array(); $gruppo=array();
	if ($numero>0){
		if ($genere!=0){
			$sql="select sum(voti)  from ".$prefix."_$tab where id_cons='$id_cons' ";
			$restotv = $dbi->prepare("$sql");
			$restotv->execute();
 
			list($tot)  = $restotv->fetch(PDO::FETCH_NUM);
			$i=0;
			// lista o gruppo
			if ($genere!=4){
				if ($votog){
					$sql="select t1.id_gruppo, t1.num_gruppo, t1.descrizione, sum(t2.voti) as somma
					from ".$prefix."_ele_gruppo as t1,
					".$prefix."_ele_voti_lista as t2,
					".$prefix."_ele_lista as t3
						where 	t1.id_cons='$id_cons'
					and t2.id_lista=t3.id_lista
					and t1.id_gruppo=t3.id_gruppo
					group by t1.id_gruppo, t1.num_gruppo, t1.descrizione
					order by somma desc";
				}else{
			        $sql="select t1.id_gruppo, t1.num_gruppo, t1.descrizione, sum(t2.voti) as somma
					from ".$prefix."_ele_gruppo as t1
        			left join ".$prefix."_ele_voti_gruppo as t2 on (t1.id_gruppo=t2.id_gruppo)
					where 	t1.id_cons='$id_cons' and t1.id_cons=t2.id_cons $circondt1
					group by t1.id_gruppo, t1.num_gruppo, t1.descrizione
					order by somma desc";
			    }
			}else{
				$sql="select t1.id_lista, t1.num_lista, t1.descrizione, sum(t2.voti) as somma
				from ".$prefix."_ele_lista as t1
        		left join ".$prefix."_$tab as t2 on (t1.id_lista=t2.id_lista)
				where 	t1.id_cons='$id_cons' and t1.id_cons=t2.id_cons
				group by t1.id_lista, t1.num_lista, t1.descrizione
				order by somma desc";
			}
				$res = $dbi->prepare("$sql");
				$res->execute();
				$gruppinum=$res->rowCount();
				$altrivoti=0;
###### gestione percentuali
				$arperc=array();
				$arval=array();
				$arvaltot=0;
				while (list($id,$num,$descrizione,$voti)  = $res->fetch(PDO::FETCH_NUM)){
					$arval[$id]=$voti;
					$arvaltot+=$voti;
				}
				$arperc=arrayperc($arval,$arvaltot);
				$res = $dbi->prepare("$sql");
				$res->execute();
				while (list($id,$num,$descrizione,$voti)  = $res->fetch(PDO::FETCH_NUM)){
					// funz per il taglio corretto della frase 13 feb 2007
					$descrizione=substr($descrizione,0,20);
					$gruppo[$i]=ucwords(strtolower($descrizione));
					if ($tot!='' and $tot!='0')
					$pro[$i]=number_format($voti*100/$tot,3);
					$pro[$i]=number_format($arperc[$id],2); // arrotondamento
					$i++;
				}
		


		
		$titolo=""._PERCE." "._VOTIE."";
		return array($gruppo,$pro);
		}

	}
}

function caricablocchi(){
#### carica i blocchi presenti in modules/Elezioni/blocchi
#### nome del file da carica xx_nome.php 
#### dove xx sta per un numero per visualizzare in ordine
### 01_votanto.php è un nome valido
### off_votanto.php per disabilitare la visualizzazione



$list = array();
$bl=opendir('modules/Elezioni/blocchi');
	while ($file = readdir($bl)) {
		if (!is_dir("modules/Elezioni/blocchi/$file") and (!preg_match('/^\./',$file)) and !stristr($file,"off_")) {
			array_push($list, $file);
		}
	}
	closedir($bl);

	if(count($list)>0)
		sort($list);
	for ($item_num=0; $item_num < count($list); $item_num++) {
		$tmp=preg_replace('/^[0-9][0-9]_/i',"",$list[$item_num]);
		$title=str_replace("_"," ",str_replace(".php","",$tmp));
		// backward theme compatibility
		
		include ("modules/Elezioni/blocchi/$list[$item_num]");
		
		//echo "<br />";
	}
}

function block($pos){

global $prefix,$dbi;

	if($pos=="dx") $p=0; elseif($pos=="sx")$p=1;else $p='';
	
	$sql="SELECT nome_file FROM ".$prefix."_ele_widget where pos_or='$p' and attivo='1' order by pos_ver asc"; 
	$resp = $dbi->prepare("$sql");
	$resp->execute();
	if($resp->rowCount()){
		while (list($nome) = $resp->fetch(PDO::FETCH_NUM)) { 
#		$nome=$row['nome_file'];
		include ("modules/Elezioni/blocchi/$nome");
		}	
	}
}		

function check_block($pos){
# verifica se ci sono blocchi nel db
global $prefix,$dbi;
if($pos=="dx") $p=0; elseif($pos=="sx")$p=1;else $p='';
	
	$sql="SELECT * FROM ".$prefix."_ele_widget where pos_or='$p' and attivo='1' order by pos_ver asc";
	$res = $dbi->prepare("$sql");
	$res->execute();
	if ($res) $numero=$res->rowCount();else $numero=0;	

		return 	$numero;
		
}


function arrayperc($temp,$sevaltot)
{

					$temp2tot=0;$temp2=array();$temp3=array();

					foreach($temp as $key=>$voti) {
						if($sevaltot>0){
							$temp4=(ceil($voti*10000/$sevaltot)-($voti*10000/$sevaltot));
							if ($temp4) $temp2[$key]=$temp4; 
                    		$valperc=number_format($voti*100/$sevaltot,2);
							$temp3[$key]=$valperc;
							$temp2tot+=$valperc;
						}
					}
					if($temp2tot>100 && ($temp2tot-100)>0.01)
					//if($temp2tot>100)
					while ($temp2tot>100){
						    foreach ($temp2 as $key => $val) {
        					if ($val == max($temp2)) {$temp3[$key]-=0.01;$temp2tot-=0.01;$temp2[$key]=0; break;} 
        					}
					}
					if("$temp2tot"=="100" or $temp2tot==0) return($temp3);
					while ($temp2tot<100.00){
						    foreach ($temp2 as $key => $val) {
        					if ($val == min($temp2)) {$temp3[$key]+=0.01;$temp2tot+=0.01;$temp2[$key]=1;break;} 
							}
					 
					} 
					return($temp3);


}

# ritorna l'estensione del file
function file_extension($filename)
{
    $path_info = pathinfo($filename);
    return $path_info['extension'];
}


# verifica stemma e lo salva come file per pdf e grafici
function verificasimbolo()
{
	global $prefix,$dbi,$id_comune,$ins_logo;
	if($ins_logo=="0"){
		$logo="vuoto.png";  //var nel config.php
	}else{

		$sql="SELECT simbolo,stemma FROM ".$prefix."_ele_comuni where id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
		list($simbolo,$stemma) = $res->fetch(PDO::FETCH_NUM);
		if($simbolo!=''){
			if (!file_exists("modules/Elezioni/images/$simbolo")) {
		   		if(!is_writeable("modules/Elezioni/images"))
					$logo="logo.gif";
				else {					
					$filename=$simbolo;
					$file=fopen("modules/Elezioni/images/$filename",'w');
					if (fwrite($file,$stemma));
					$close = fclose($file);
					$logo=$simbolo;
				}	
			} else $logo=$simbolo;
		}else{
			$logo="logo.gif";
		}

	}return $logo;
}

# funzione per verifica se esiste il voto disgunto tra sindaco e lista nelle amministrative siciliane
function isdisgiunto(){
global $prefix,$dbi,$id_cons,$limite,$fascia,$ops;
	#voti di lista e di gruppo completamente disgiunti 
	$sql="select infdisgiunto, supdisgiunto from ".$prefix."_ele_conf as t2 left join ".$prefix."_ele_cons_comune as t1 on t1.id_conf=t2.id_conf where id_cons = '$id_cons' ";
	$result = $dbi->prepare("$sql");
	$result->execute();
	$glsep='';
	if ($result) {
		list($infdis,$supdis)=$result->fetch(PDO::FETCH_NUM);
		if((($fascia>$limite and $supdis) or ($fascia<$limite and $infdis)) and $ops==3) 
			$glsep='_lista';
	}
return $glsep;
}


# funzione per il titolo della pagina
function pagetitle($op,$info){
$pagetitle='';

	if($op=="consiglieri")$pagetitle="Proiezione del Consiglio Comunale"; //mettere l'elenco
	if($op=="gruppo_circo")$pagetitle="Risultati per Circoscrizioni";
	if($op=="gruppo_sezione")$pagetitle="Risultati per Sezione";
	if($op=="lista_circo")$pagetitle="Risultati Lista per Circoscrizione";
	if($op=="lista_sezione")$pagetitle="Risultati Lista per Sezione";
	if($op=="candidato_circo")$pagetitle="Risultati Candidati per Circoscrizione";
	if($op=="candidato_sezione")$pagetitle="Risultati Candidati per Sezione";



	if($op=="affluenze_graf")$pagetitle="Grafici Affluenze";
	if($op=="graf_votanti")$pagetitle="Grafici Votanti";
	if($op=="graf_candidato")$pagetitle="Grafici Candidati";
	if($op=="graf_gruppo")$pagetitle="Grafico Principale";
	if($op=="graf_lista")$pagetitle="Grafico Lista";

	if($op=="gruppo")$pagetitle="Quadro elezioni riassuntivo";
	if($op=="liste")$pagetitle="Voti Liste";
	if($op=="partiti")$pagetitle="Voti Partito";
	if($op=="contatti")$pagetitle="Contatti";


	if($info=="come")$pagetitle="Come si vota";
	if($info=="numeri")$pagetitle="Numeri Utili";
	if($info=="servizi")$pagetitle="Servizi elettorali";
	if($info=="link")$pagetitle="Link Utili";
	if($info=="dati")$pagetitle="Dati Generali";

	return $pagetitle;
}




?>
