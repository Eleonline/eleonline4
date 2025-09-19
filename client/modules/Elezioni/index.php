<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* Ultima modifica 22 maggio 2009 luc - candidati europee */

if (!defined('MODULE_FILE')) {
    die ("Non puoi accedere al file direttamente...");
}

$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ?
	$_GET : $_POST;


if (isset($param['rss'])) $rss=intval($param['rss']); else $rss='0';
if (isset($param['xls'])) $xls=intval($param['xls']); else $xls='0';
if (isset($param['pdf'])) $pdf=intval($param['pdf']); else $pdf='0';
#if (isset($param['datipdf'])) $datipdf=addslashes($param['datipdf']); else $datipdf='';
if(isset($param['visgralista'])) $visgralista=1;

#global $id_comune,$id_cons_gen;
if (isset($param['id_comune'])) $id_comune=intval($param['id_comune']); else $id_comune=$siteistat;
if (isset($param['id_cons_gen'])) $id_cons_gen=intval($param['id_cons_gen']); else 
{
        $sql="SELECT id_cons FROM ".$prefix."_ele_comune where id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

        list($id_cons_pred)=$res->fetch(PDO::FETCH_NUM);
        $sql="SELECT id_cons_gen FROM ".$prefix."_ele_cons_comune where id_cons='$id_cons_pred' ";
		$res = $dbi->prepare("$sql");
		$res->execute();

        list($id_cons_gen)=$res->fetch(PDO::FETCH_NUM);
}       
if(!count($param)) $op='gruppo';
elseif (isset($param['op'])) $op=$param['op']; else $op='';
if (isset($param['minsez'])) $minsez=intval($param['minsez']); else $minsez='';
if (isset($param['id_lista'])) $id_lista=intval($param['id_lista']); else $id_lista='';
if (isset($param['id_circ'])) $id_circ=intval($param['id_circ']); else $id_circ='0';
if (isset($param['csv'])) $csv=intval($param['csv']); else $csv=0;
if (isset($param['min'])) $min=intval($param['min']); else $min= 0;
if (isset($param['orvert'])) $orvert=intval($param['orvert']); else $orvert='';
if (isset($param['offset'])) $offset=intval($param['offset']); else $offset='';
if (isset($param['offsetsez'])) $offsetsez=intval($param['offsetsez']); else $offsetsez='';
if (isset($param['perc'])) $perc=$param['perc']; else $perc='';
if (isset($param['info'])) $info=addslashes($param['info']); else $info='';
if (isset($param['files'])) $files=addslashes($param['files']); else $files='';
if (isset($param['voti_lista'])) $voti_lista=intval($param['voti_lista']); else $voti_lista= 0;
if (isset($param['perc_lista'])) $perc_lista=$param['perc_lista']; else $perc_lista= 0;
if (isset($param['lettera'])) $lettera=addslashes($param['lettera']); else $lettera='';
if (isset($param['ordine'])) $ordine=addslashes($param['ordine']); else $ordine='';
if (isset($param['id_gruppo'])) $id_gruppo=intval($param['id_gruppo']); else $id_gruppo='';
if (isset($param['tipo_cons'])) $tipo_cons=intval($param['tipo_cons']); else $tipo_cons='';
if (isset($param['descr_circ'])) $descr_circ=intval($param['descr_circ']); else $descr_circ='';

if($info) $_SESSION['info']=$info;

# anti-xss nov. 2009 
$id_comune=intval($id_comune); 
$perc=htmlentities($perc); 
$perc_lista=floatval($perc_lista); 
#$datipdf= htmlentities($datipdf); 
$op= htmlentities($op); 
$info= htmlentities($info); 
$files=htmlentities($files); 
$lettera=htmlentities($lettera); 
$ordine=htmlentities($ordine);
$cap=''; 
global $limite;
$sql="SELECT id_conf FROM ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen' and id_comune='$id_comune'" ;
	$res = $dbi->prepare("$sql");
	$res->execute();

list($hondt) = $res->fetch(PDO::FETCH_NUM);
#$TEST1=": comune:$id_comune -- cons:$id_cons_gen<br>";
#$TEST2=": ";
#if(isset($_SESSION['id_comune'])) $TEST2.=$_SESSION['id_comune']."<br>";

$sql = "SELECT t3.genere,t1.tipo_cons,t1.descrizione,t2.id_cons_gen FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2, ".$prefix."_ele_tipo as t3 where t1.tipo_cons=t3.tipo_cons and t2.id_comune=$id_comune and t1.id_cons_gen=t2.id_cons_gen and t2.id_cons_gen='$id_cons_gen' and t2.chiusa!='2' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

$tot=$res->rowCount();
if ($tot>0 and $id_cons_gen>0) {
	$sql = "SELECT t3.genere,t1.tipo_cons,t1.descrizione,t2.id_cons_gen FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2, ".$prefix."_ele_tipo as t3 where t1.tipo_cons=t3.tipo_cons and t2.id_comune=$id_comune and t1.id_cons_gen=t2.id_cons_gen and t2.id_cons_gen='$id_cons_gen' and t2.chiusa!='2'";
}else{
	$sql = "SELECT t3.genere,t1.tipo_cons,t1.descrizione,t2.id_cons_gen FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2, ".$prefix."_ele_tipo as t3 where t1.tipo_cons=t3.tipo_cons and t2.id_comune=$id_comune and t1.id_cons_gen=t2.id_cons_gen and t2.chiusa!='2' order by t1.data_fine desc limit 0,1 ";
}
$res = $dbi->prepare("$sql");
$res->execute();

if ($res) list($genere,$tipo_cons,$descr_cons,$id_cons_gen) = $res->fetch(PDO::FETCH_NUM);

if ($tipo_cons!=3) $limite=0;

$sql="SELECT t2.id_cons FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_cons_gen='$id_cons_gen' and t2.id_comune='$id_comune'" ;
$res = $dbi->prepare("$sql");
$res->execute();

list($id_cons) = $res->fetch(PDO::FETCH_NUM);

$sql="SELECT t1.descrizione, t1.tipo_cons, t2.genere, t2.voto_g, t2.voto_l, t2.voto_c, t2.circo FROM ".$prefix."_ele_consultazione as t1,".$prefix."_ele_tipo as t2 where t1.tipo_cons=t2.tipo_cons and t1.id_cons_gen='$id_cons_gen' ";
$res = $dbi->prepare("$sql");
$res->execute();

list($descr_cons,$tipo_cons,$genere,$votog,$votol,$votoc,$circo) = $res->fetch(PDO::FETCH_NUM);

// esiste consultazione e toglie blocco nel caso non esista
$sql="SELECT t1.id_cons_gen,t1.descrizione FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_comune='$id_comune' and t2.chiusa!='2' order by t1.data_fine desc" ;
	$res = $dbi->prepare("$sql");
	$res->execute();
 
	$esiste_cons=$res->rowCount();
	if($esiste_cons<='0')$blocco=0;

//carica limite e fascia per il comune
$sql="SELECT limite FROM ".$prefix."_ele_conf where id_conf='$hondt'" ;
	$res = $dbi->prepare("$sql");
	$res->execute();

list($limite) = $res->fetch(PDO::FETCH_NUM);
$sql="SELECT id_fascia FROM ".$prefix."_ele_cons_comune where id_comune='$id_comune' and id_cons='$id_cons'";
	$res = $dbi->prepare("$sql");
	$res->execute();

list($fascia) = $res->fetch(PDO::FETCH_NUM);
if(!$id_circ){		
$sql="SELECT id_circ FROM ".$prefix."_ele_circoscrizione where id_cons='$id_cons limit 0,1 order num_circ asc' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

list($id_circ) = $res->fetch(PDO::FETCH_NUM);
}
// rss oppure foglio elettronico
if ($rss!=1   && $xls!=1 && $pdf!=1){ 
    $index = 1;
	$sql="SELECT descrizione,simbolo,cap FROM ".$prefix."_ele_comune where id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($descr_com,$simbolo,$cap) = $res->fetch(PDO::FETCH_NUM);
        $descr_com =stripslashes($descr_com); 
	# titolo pagina 2015
	include_once("modules/Elezioni/funzioni.php");
	$pagetitle= pagetitle($op,$info);
	$pagetitle= $descr_com." - ".$descr_cons." - ".$pagetitle; 	

	include("header.php");
    if($csv!=1){
	  //include_once("modules/Elezioni/funzioni.php");
		echo "<table style=\"vertical-align: middle;\"><tr><td style=\"text-align: center;\"><table><tr><td style=\"vertical-align:middle;\">";
		$siteistat=$id_comune;
		if($simbolo!=''){
			echo "<img style=\"width: 70px; height: auto;\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_comune=".$id_comune."\" alt=\"logo\" >";
		}else{
			echo "<img src=\"modules/Elezioni/images/logo.gif\" alt=\"logo\" height=\"100\" >";
		}



		//echo "<img src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_comune=".$id_comune."\" alt=\"mappa\" />";
			echo "</td><td>
		"._COMUNE."<b> $descr_com </b><br>"._RISULTA." "._CONSULTA." <h1>$descr_cons</h1><br>";

		if ($circo){ // elenco per scelta circoscrizione
			echo "</td></tr><tr><td colspan=\"2\" class=\"bggray\"><table class=\"table-80\"><tr><td class=\"table-main\"><form id=\"circo\" method=\"post\" action=\"modules.php\">";
			$sql="SELECT id_circ,descrizione,num_circ from ".$prefix."_ele_circoscrizione where id_cons=$id_cons";
			$res_sez = $dbi->prepare("$sql");
			$res_sez->execute();

			$pop=$op;
			if($pop=='partiti') $pop='gruppo';
			echo "<input type=\"hidden\" name=\"pagina\" value=\"modules.php?name=Elezioni&amp;op=$pop&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=$info&amp;id_circ=\"></input>";
			echo ""._SCELTA_CIR.":<b> 
			<select name=\"id_circ\" class=\"blu\" onChange=\"top.location.href=this.form.pagina.value+this.form.id_circ.options[this.form.id_circ.selectedIndex].value;return false\">";
			while(list($id_rif,$descrizione,$num_cir)=$res_sez->fetch(PDO::FETCH_NUM)) {
				if (!$id_circ) $id_circ=$id_rif;
				$sel = ($id_rif == $id_circ) ? "selected=\"selected\"" : "";
				echo "<option value=\"$id_rif\" $sel>";
				for ($j=strlen($num_cir);$j<2;$j++) { echo "&nbsp;&nbsp;";}
				echo "$num_cir) ".$descrizione."</option>";
			}
			echo "</select></b></form></td></tr></table>";
		
		}
		echo ""._DISCLAIMER."";
		echo  "</td></tr></table></td></tr></table><br>";
    }
  }

if (!isset($min)) $min=0;

/************************
Funzione Menu a cascata
*************************/
function menu() {
	global $hondt,$lang,$multicomune, $tema, $op, $prefix, $dbi, $offset, $min,$descr_cons,$info,$dati, $votog,$votol,$votoc,$circo, $id_cons,$tipo_cons,$genere,$descr_cons,$id_cons_gen,$id_comune,$id_circ,$minsez,$offsetsez, $limite,$hondt,$tema_on,$js,$visgralista;

	$tema=htmlentities($tema); //xss        
	# include menu da tema
	if (file_exists("temi/$tema/menu.php")) {
		include_once("temi/$tema/menu.php");
    }else{
		include_once("modules/Elezioni/menu.php");
	}

}



/********************************************
Funzione Come si vota, link, numeri e servizi
visuallizza la stringa dei dati generali
********************************************/

function come($info) {
global  $prefix, $dbi, $offset, $min,$id_cons,$tipo_cons,$descr_cons;

$tab='';
if ($info=="come") $tab="_ele_come";
elseif ($info=="numeri") $tab="_ele_numeri";
elseif ($info=="servizi") $tab="_ele_servizi";
elseif ($info=="link") $tab="_ele_link";
else $tab="_ele_come";


    global  $user, $admin, $cookie, $textcolor2, $prefix, $dbi;
     $sql="select mid, title, preamble, content,editimage from ".$prefix."$tab where id_cons='$id_cons' order by mid ";
	$result = $dbi->prepare("$sql");
	$result->execute();

    if ($result->rowCount() == 0) {
	return;
    } else {
	while (list($mid, $title, $preamble,$content,  $editimage) = $result->fetch(PDO::FETCH_NUM)) {
  	if ($title != "" && $preamble != "") {
               
                if ($info=="link"){
			
			echo "<div class=\"message\">
			<b><a href=\"$preamble\">$title</a></b>
			$content
			</div>";
			
		}else{
			echo "<div><b>$title</b><br></div>";
                
		
		echo "<div class=\"message\">$preamble<br><br></div>";
		
		echo "<div class=\"message\">$content</div>";
		}
		
		echo "<br>";

	}
    }
 }

}



/****************
Funzione dati Generali
visuallizza la stringa dei dati generali
****************/


function dati() {
/*Funzione di visualizzazione dati generali                  */
	global $admin, $prefix, $dbi, $offset, $votog, $votol, $votoc, $min,$id_cons,$tipo_cons,$descr_cons,$id_cons_gen,$id_comune,$genere,$id_circ;
	
	$sql="select * from ".$prefix."_ele_circoscrizione where id_cons='$id_cons' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	$sql="select * from ".$prefix."_ele_sede where id_cons='$id_cons' ";
	$ressede = $dbi->prepare("$sql");
	$ressede->execute();

	$sql="select * from ".$prefix."_ele_sezione where id_cons='$id_cons' ";
	$res3 = $dbi->prepare("$sql");
	$res3->execute();

	$circo = $res->rowCount();
	$sedi = $ressede->rowCount();
	$sez = $res3->rowCount();
		
	echo "<div><b>"._DATIG."</b></div> ";
	echo "<table class=\"table-80\"><tr class=\"bggray\">";
	echo "<td ><b>"._AVENTI."</b></td>"
	."<td ><b>"._MASCHI."</b></td>"
	."<td ><b>"._FEMMINE."</b></td>";
	if ($circo>1)
		echo "<td ><b><a href=\"modules.php?name=Elezioni&amp;op=circo&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune\">"._CIRCS."</a></b></td>";
	else	
		echo "<td ><b><a href=\"modules.php?name=Elezioni&amp;op=circo&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune\">"._SEDI."</a></b></td>";
	echo "<td><b><a href=\"modules.php?name=Elezioni&amp;op=sezione&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune\">"._SEZIONI."</a></b></td>"
	."<td ><b><a href=\"modules.php?name=Elezioni&amp;op=gruppo&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=$id_circ\">"._GRUPPI."</a></b></td>";
        
        
	
	
	
	// camera e senato con raggruppamenti
	 $sql="select * from ".$prefix."_ele_lista where id_cons='$id_cons' ";
	$res3 = $dbi->prepare("$sql");
	$res3->execute();

     $liste = $res3->rowCount();
	if($liste and $genere!=4){
	 echo "<td><b><a href=\"modules.php?name=Elezioni&amp;op=liste&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune\">"._LISTE."</a></b></td>";
	}
	
	

	$candi=0;
	
	// se non referendum
	if ($genere>0 and !$votoc){
		echo "<td><b><a href=\"modules.php?name=Elezioni&amp;op=candi&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune\">"._CANDIDATI."</a></b></td>";
		$sql="select id_cons from ".$prefix."_ele_candidato where id_cons='$id_cons' ";
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
        
	if($circo==1) $circo=$sedi;
	$sql="select sum(maschi),sum(femmine), sum(maschi+femmine)  from ".$prefix."_ele_sezione where id_cons=$id_cons";
	$res4 = $dbi->prepare("$sql");
	$res4->execute();

 	if($res4) list($maschi,$femmine,$tot) = $res4->fetch(PDO::FETCH_NUM);
 	echo "</tr><tr class=\"bggray2\">"

	."<td><b>$tot</b>"
	."</td><td><b>$maschi</b>"
	."</td><td><b>$femmine</b>"
	."</td><td><b>$circo</b>"
	."</td><td><b>$sez</b>"
	."</td><td><b>$gruppo</b>";
//        if ($tipo_cons >9) echo"</td><td><b>$liste</b>";
        if ($liste and $genere!=4) echo"</td><td><b>$liste</b>";

	if ($genere>2 && !$votoc) echo"</td><td><b>$candi</b>";
	
	echo "</td></tr></table>";
	//CloseTable();
}
//////////////////////////////////////////////////////////////
// votanti
//////////////////////////////////////////////////////////////



function circo() {

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
		echo "<div><b>"._CIRCS."</b></div><br><br>
		<table class=\"table-80\"><tr class=\"bggray\">"
		."<td ><b>"._NUM."</b></td>"
		."<td ><b>"._CIRCO."</b></td>"
		."<td ><b>"._INDIRIZZO."</b></td>"
		."<td><b>"._TEL."</b></td></tr>";
	}else{
		echo "<div><b></b></div><br><br>
		<table class=\"table-80\"><tr class=\"bggray\">"
		."<td ><b>"._INDIRIZZO."</b></td>"
		."<td><b>"._TEL."</b></td></tr>";
	}
     
	while(list($id_cons2,$id_circ,$num_circ,$descr_circ) = $result->fetch(PDO::FETCH_NUM)) {
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
				echo "<td><b><a href=\"modules.php?name=Elezioni&amp;op=sezione&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_sede=$id_sede\">$indir</a></b>"
				."</td><td><b>$tel1 </b></td><td><b>  $tel2</b></td></tr>";
				if ($i<$righe) echo"<tr class=\"bggray3\">";
				if ($numcirc>1) echo "<td></td><td></td>";
			}
		}
	}
    echo "</table>";   
    page($id_cons_gen,$go,$max,$min,$prev,$next,$offset,$file);

//CloseTable();
}

/******************************************************/
/*Funzione di visualizzazione globale sezioni         */
/*****************************************************/

function sezione() {
	global $admin, $prefix, $dbi, $offset, $min,$votog,$circo, $id_cons_gen,$id_circ,$descr_circ,$id_cons,$file,$prev,$next,$id_comune,$googlemaps;
	global $descr_com,$cap;
	if(!isset($_GET['id_circ'])) unset($id_circ);
	dati();
	$totali_t=0;$maschi_t=0;$femmine_t=0;
	$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
	//mappa
	if (isset($param['id_sede'])) $id_sede=intval($param['id_sede']); else $id_sede='0';
	if ($id_sede!='0' && $googlemaps!='1'){
		$sql="SELECT indirizzo FROM ".$prefix."_ele_sede where id_sede='$id_sede'";
		$res1 = $dbi->prepare("$sql");
		$res1->execute();
		list($ind) = $res1->fetch(PDO::FETCH_NUM);
		$indir=str_replace(" ","+",$ind.",+".$descr_com.",+".$cap);
		echo "<br><div><a href=\"https://maps.google.it/maps/place/".$indir."\"><img src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_sede=".$id_sede."\" alt=\"mappa\" ></a></div>";
	}elseif($id_sede!='0' && $googlemaps=='1'){
		$mappa=googlemaps(); //echo $mappa;
		echo "
		<div id=\"map\" style=\"width: 400px; height: 400px; margin: 0 auto;   position: relative; top:0; overflow: hidden;\"></div>
	";
	   // echo '<div id="map"></div>';
	} 

	$offset=15;
	if (!isset($min)) $min=0;
	if (!isset($id_circ)) $id_circ=0;
	$go="sezione";
	$sql="SELECT descrizione FROM ".$prefix."_ele_circoscrizione where id_cons='$id_cons'";
	$res2 = $dbi->prepare("$sql");
	$res2->execute();
	$numcirc = $res2->rowCount();
	$sql="SELECT descrizione FROM ".$prefix."_ele_circoscrizione where id_cons='$id_cons' AND id_circ='$id_circ' ";
	$res2 = $dbi->prepare("$sql");
	$res2->execute();

	list($descr_circ) = $res2->fetch(PDO::FETCH_NUM);
	if($numcirc>1){
	   echo "<div><b>"._SEZIONI." "; 
	   if ($id_circ) echo "di $descr_circ";
	   if ($id_sede) echo _SINGOLA;
	   echo "</b></div>";
	}
	echo "<br>"
    ."<table class=\"table-80\"><tr class=\"bggray\">"
	."<td class=\"td-5\"><b>"._NUM."</b></td>"
	."<td ><b>"._INDIRIZZO."</b></td>"
	."<td class=\"td-5\"><b>"._MASCHI."</b></td>"
	."<td class=\"td-5\"><b>"._FEMMINE."</b></td>"
	."<td><b>"._TOTS." "._AVENTI."</b></td></tr>";
    // link alle sedi
    
    // link alle circoscrizioni
    
    if ($id_circ) { 	
    	$sql="SELECT id_sede FROM ".$prefix."_ele_sede where id_cons='$id_cons' and id_circ='$id_circ' ";
		$res1 = $dbi->prepare("$sql");
		$res1->execute();
    	//$max = mysql_num_rows($res);
        $i=0;// n. sezioni x circo
    	while(list($id_sede) = $res1->fetch(PDO::FETCH_NUM)){	
       		$circos=" AND id_sede='$id_sede'";
    		$sql="SELECT * FROM ".$prefix."_ele_sezione where id_cons='$id_cons' $circos ";
			$res = $dbi->prepare("$sql");
			$res->execute();
    		//$tot_sez = mysql_num_rows($res);
			$sql="select id_cons,id_sez,id_sede,num_sez, maschi, femmine  from ".$prefix."_ele_sezione where id_cons='$id_cons' $circos ORDER BY num_sez  LIMIT $min,$offset";
			$result = $dbi->prepare("$sql");
			$result->execute();
			while(list($id_cons2,$id_sez,$id_sede,$num_sez, $maschi, $femmine) = $result->fetch(PDO::FETCH_NUM)) {
        		// dati circoscrizione
				$i++;
				$sql="select indirizzo from ".$prefix."_ele_sede where id_sede='$id_sede'";
				$result1 = $dbi->prepare("$sql");
				$result1->execute();
        		list($indir)=$result1->fetch(PDO::FETCH_NUM);       		
				$totali=$maschi+$femmine;
				$totali_t=$totali_t+$totali;
				$maschi_t=$maschi_t+$maschi;
				$femmine_t=$femmine_t+$femmine;
				echo "<tr><td><b>$num_sez</b>"
				."</td><td><b><a href=\"modules.php?name=Elezioni&amp;op=sezione&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_sede=$id_sede\">$indir</a></b>"
				."</td><td >$maschi"
				."</td><td >$femmine"
				."</td><td ><b>$totali</b></td></tr>";
    		}
    			
		}
		echo "<tr class=\"bggray2\" ><td>"._SEZIONI."<br>n. $i</td>
		<td><b>"._TOT."<br>$descr_circ</b>
		</td><td ><b>"._MASCHI."<br><span class=\"red\">$maschi_t</span></b>
		</td><td ><b>"._FEMMINE."<br><span class=\"red\">$femmine_t</span></b></td>
		<td ><b>"._TOTS."<br><span class=\"red\">$totali_t</span></b></td></tr>";
		echo "</table></center>";
    }else{
		$circos='';
		if ($id_sede) $circos=" AND id_sede='$id_sede'";		 
		$sql="SELECT * FROM ".$prefix."_ele_sezione where id_cons='$id_cons' $circos ";
		$res = $dbi->prepare("$sql");
		$res->execute();
		$max = $res->rowCount();
		$sql="select id_cons,id_sez,id_sede,num_sez, maschi, femmine  from ".$prefix."_ele_sezione where id_cons='$id_cons' $circos ORDER BY num_sez  LIMIT $min,$offset";
		$result = $dbi->prepare("$sql");
		$result->execute();
		while(list($id_cons2,$id_sez,$id_sed,$num_sez, $maschi, $femmine) = $result->fetch(PDO::FETCH_NUM)) {			
			// dati circoscrizione
			$sql="select indirizzo from ".$prefix."_ele_sede where id_sede='$id_sed'";
			$result1 = $dbi->prepare("$sql");
			$result1->execute();
			list($indir)=$result1->fetch(PDO::FETCH_NUM);
			$totali=$maschi+$femmine;
			$totali_t=$totali_t+$totali;
			$maschi_t=$maschi_t+$maschi;
			$femmine_t=$femmine_t+$femmine;
			echo "<tr class=\"bggray2\"><td><b>$num_sez</b>"
			."</td><td><b><a href=\"modules.php?name=Elezioni&amp;op=sezione&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_sede=$id_sed\"><img class=\"nobordo\" src=\"modules/Elezioni/images/mappa.gif\" style=\"text-align:left;\" alt=\"mappa\">
			$indir</a></b>"
			."</td><td>$maschi"
			."</td><td>$femmine"
			."</td><td><b>$totali</b></td></tr>";
		}
		if($id_sede)echo "<tr class=\"bggray\"><td><br><br></td>
		<td><b>"._TOTS."<br>$indir</b>
		</td><td ><b>"._MASCHI."<br><span class=\"red\">$maschi_t</span></b>
		</td><td ><b>"._FEMMINE."<br><span class=\"red\">$femmine_t</span></b></td>
		<td ><b>"._TOTS."<br><span class=\"red\">$totali_t</span></b></td></tr>";   
		echo "</table>";
	}
	if(!isset($max)) $max=0; 
    page($id_cons_gen,$go,$max,$min,$prev,$next,$offset,$file);


//CloseTable();
}



/******************************************************/
/*Funzione di visualizzazione globale    gruppo             */
/*****************************************************/

function gruppo() {
   global $fascia, $limite, $admin, $prefix, $dbi, $offset, $min, $id_cons_gen,$genere, $id_cons,$tipo_cons,$file,$prev,$next,$id_circ,$id_comune,$descr_circ,$id_sez,$votog,$votol,$circo,$limite;
   	//dati();
	// definizione variabile per button 'ok' nei form
	$button="<input name=\"vai\" type=\"image\" src=\"modules/Elezioni/images/ok2.jpg\" alt=\"ok\" title=\"ok\" />";

   // numero sezioni scrutinate sul gruppo
	// Verificare per la circoscrizione
	if ($genere==0) {$tab="ref";}else{$tab="gruppo";}
	if ($votog or $genere==4) {$tab="lista";}else{$tab="gruppo";}
	if($circo){
		if(!$id_circ){
		$sql="select id_circ from ".$prefix."_ele_circoscrizione where id_cons='$id_cons' limit 0,1";
	$res = $dbi->prepare("$sql");
	$res->execute();

		list($id_circ)=$res->fetch(PDO::FETCH_NUM);
		}
		 $sql="select t1.id_sez,sum(t1.voti)  from ".$prefix."_ele_voti_$tab as t1, ".$prefix."_ele_$tab as t2 where t1.id_$tab=t2.id_$tab and t1.id_cons='$id_cons' and t2.id_circ='$id_circ' group by t1.id_sez";
	}else $sql="select id_sez  from ".$prefix."_ele_voti_".$tab." where id_cons='$id_cons'  group by id_sez ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	$numero=$res->rowCount();
	if($circo) $circos="and id_circ='$id_circ'"; else $circos='';
	
	if($circo) $sql="select *  from ".$prefix."_ele_sezione as t1, ".$prefix."_ele_sede as t2 where t1.id_sede=t2.id_sede and t1.id_cons='$id_cons' and t2.id_circ=$id_circ";
	else $sql="select *  from ".$prefix."_ele_sezione where id_cons='$id_cons' $circos ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	$sezioni=$res->rowCount();
	if ($numero!=0) {
#	echo "<div><h2>"._SEZSCRU." $numero "._SU." $sezioni</h2></div>";

	####################### inserimento scrutinio in percentuale -- per i gruppi
	if($genere==0)
		$sql="SELECT COUNT(0) FROM ".$prefix."_ele_voti_ref WHERE id_cons ='$id_cons' group by id_sez";
	else
		$sql="SELECT COUNT(0) FROM ".$prefix."_ele_voti_lista WHERE id_cons ='$id_cons' group by id_sez";		
	$resn = $dbi->prepare("$sql");
	$resn->execute();

	$NicolaSezScrut = $resn->rowCount();
	$sql="SELECT COUNT( id_sez ) FROM ".$prefix."_ele_sezione WHERE id_cons ='$id_cons'";
	$res = $dbi->prepare("$sql");
	$res->execute();

	$NicolaSezTot = $res->fetch(PDO::FETCH_NUM);
	
	if($genere==0) $sql="select sum(si+no), sum(bianchi+nulli+contestati)   from ".$prefix."_ele_voti_ref where id_cons='$id_cons' group by id_gruppo";
	else	$sql="select sum(validi),sum(nulli+bianchi+contestati+voti_nulli) from ".$prefix."_ele_sezione where id_cons='$id_cons'";
	$resperc = $dbi->prepare("$sql");
	$resperc->execute();

		list($scruvalidi,$scrunulli)=$resperc->fetch(PDO::FETCH_NUM);
		$sql="select sum(voti_complessivi) from ".$prefix."_ele_voti_parziale where id_cons='$id_cons' group by data,orario order by data desc, orario desc limit 0,1 ";
	$resperc = $dbi->prepare("$sql");
	$resperc->execute();

		list($totschede)=$resperc->fetch(PDO::FETCH_NUM);
		if($totschede)
		$grpercscru=number_format(($scruvalidi+$scrunulli)*100/$totschede,2);
		else $grpercscru=0;
#	echo "<div><h2> $sql2 Dati riferiti a $numero "._SEZ." "._SU." $sezioni</h2></div>";
	echo "<table border=\"2\"><tr><td style=\"text-align:center; \">";
	if(!$votog and $genere!=4){	echo "<h2><b>"._GRUPPO.": </b>"; 
	echo "<br>Scrutinate ".($scruvalidi+$scrunulli)." schede su $totschede ($grpercscru %)  <br></h2>";
	}
#$numero sezioni su $sezioni<br> le schede scrutinate sono:  $grpercscru % 
#	echo "<div><h2> $numero "._SEZSCRU." ("._ALPERC." $grpercscru %) "._SU." $sezioni</h2></div>";
	####################### inserimento scrutinio in percentuale -- per le liste
	if ($genere>2 && ($fascia>$limite or !$limite)){
	
	if($genere==4 or ($genere==5 and $votog)) $sql="select sum(validi),sum(nulli+bianchi+voti_nulli+contestati) from ".$prefix."_ele_sezione where id_cons='$id_cons'";
	else $sql="select sum(validi_lista),sum(nulli+bianchi+voti_nulli+contestati+solo_gruppo+voti_nulli_lista+contestati_lista) from ".$prefix."_ele_sezione where id_cons='$id_cons'";
		$resperc = $dbi->prepare("$sql");
		$resperc->execute(); 
		list($scruvalidi,$scrunulli)=$resperc->fetch(PDO::FETCH_NUM);
		if($totschede)
		$listpercscru=number_format(($scruvalidi+$scrunulli)*100/$totschede,2);
		else $listpercscru=0;
		$listtotschede=$totschede;
		$listtotvoti=($scruvalidi+$scrunulli);
if($genere!=0 && $genere!=3)
		echo "<h6>Voti alle Liste: pervenute: $NicolaSezScrut sezioni su $NicolaSezTot[0] <br/>  Scrutinate $listtotvoti schede su $listtotschede ($listpercscru %)</h6>";
#	    echo "<h6>Voti alle Liste: Scrutinate $listtotvoti schede su $listtotschede ($listpercscru %)</h6>";
	}
		echo "</td></tr></table> ";
	####################### 725
	
	}
	
	
		
	$offset=15;
  	if (!isset($min)) $min=0;
  	$go="gruppo";
   if(!$votog and $genere!=4)	echo "<div><h2><b>"._GRUPPO." </b><br></h2></div>";
   if ($tipo_cons==18 or $tipo_cons==19) echo "<div><b>I voti di lista subiranno una integrazione al termine delle operazioni sul collegio con la ripartizione pro-quota derivata dai voti sull'uninominale</b></div>";

	if ($genere!=4){
	    $circos='';
	    $circol='';    
		// numero sezioni scrutinate per lista
  		if ($circo){$circos="and id_circ='$id_circ'";$circol="and t2.id_circ='$id_circ'";}


	      
		$sql="select t1.id_sez,sum(t1.voti)  from ".$prefix."_ele_voti_lista as t1, ".$prefix."_ele_lista as t2 where t1.id_lista=t2.id_lista and t1.id_cons='$id_cons' $circol group by t1.id_sez";
	$res_num_list = $dbi->prepare("$sql");
	$res_num_list->execute();

        	//$res_num_list = mysql_query("select *  from ".$prefix."_ele_voti_lista where id_cons='$id_cons' group by id_sez ",$dbi);
		$numero_l=$res_num_list->rowCount();
		// verifica delle sezioni in relazione ai candidati (comuni >=15000)  non c'e' il voto di lista e quindi ci metto se scrutinate le preferenze sulla lista [$numero_c] - 5/5/2009
		$sql="select id_sez from ".$prefix."_ele_voti_candidato where id_cons='$id_cons' group by id_sez ";
	$res_num_list = $dbi->prepare("$sql");
	$res_num_list->execute();

		$numero_c=$res_num_list->rowCount();
      
		$sezioni_l=$sezioni;
		
		
		$sql="SELECT * FROM ".$prefix."_ele_gruppo where id_cons='$id_cons' $circos ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	$max = $res->rowCount();		
	$t_circos="";
	if (!$votog){
		if ($circo) $t_circos=" and t2.id_circ='$id_circ'"; 
		$sql="select sum(t1.voti)  from ".$prefix."_ele_voti_gruppo as t1 , ".$prefix."_ele_gruppo as t2 where t1.id_gruppo=t2.id_gruppo and t1.id_cons='$id_cons' $t_circos ";

	// sommatoria dei voti di lista per camera e senato dal 2006 per coalizioni-->byluc 
	}else{ 
		$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons='$id_cons'";
	}
	$res_pres_tutti = $dbi->prepare("$sql");
	$res_pres_tutti->execute();
		
		list($voti_pres_tutti) = $res_pres_tutti->fetch(PDO::FETCH_NUM);
######gestione percentuali
		$arval=array();$arperc=array();
			if ($genere>0){ // no referendum 
				if (!$votog){ // no camere e senato per coalizioni 
					$sql="select sum(t1.voti),t1.id_gruppo from ".$prefix."_ele_voti_gruppo as t1 , ".$prefix."_ele_gruppo as t2 where t1.id_gruppo=t2.id_gruppo and t1.id_cons='$id_cons' $t_circos   group by id_gruppo";

				}else{ // sommatoria voti lista per coalizione per camere e senato
					$sql="select sum(t1.voti),t2.id_gruppo  from ".$prefix."_ele_voti_lista as t1 , ".$prefix."_ele_lista as t2 where t1.id_lista=t2.id_lista and t1.id_cons='$id_cons' $t_circos group by t2.id_gruppo";
				}
			$res_presidente = $dbi->prepare("$sql");
			$res_presidente->execute();
   			while(list($voti_pres,$id_gruppo2) = $res_presidente->fetch(PDO::FETCH_NUM)) {
    				$arval[$id_gruppo2]=$voti_pres;
    			}
   			$arperc=arrayperc($arval,$voti_pres_tutti);

    		}
#######		
		$sql="select id_cons ,id_gruppo ,num_gruppo, descrizione, prognome from ".$prefix."_ele_gruppo where id_cons='$id_cons' $circos ORDER BY num_gruppo  LIMIT $min,$offset";
		$result = $dbi->prepare("$sql");
		$result->execute();

		while(list($id_cons2,$id_gruppo2,$num_gruppo, $descr_gruppo,$prognome) = $result->fetch(PDO::FETCH_NUM)) {
  		   if ($num_gruppo!=0) {
	           echo "<table  class=\"table-80\">
				<tr>"
				."<td class=\"td-5\"><b>"._NUM."</b></td>"
				."<td class=\"bggray\"><b>"._DESCR."</b></td>"
				."<td class=\"td-5\"><b>"._SIMBOLO."</b></td>
				</tr>";
				if ($genere>0){ // no referendum 
					if (!$votog){ // no camere e senato per coalizioni 
						$sql="select sum(voti)  from ".$prefix."_ele_voti_gruppo  where id_cons='$id_cons' and id_gruppo='$id_gruppo2'";
					}else{ // sommatoria voti lista per coalizione per camere e senato
						$sql="select sum(t1.voti)  from ".$prefix."_ele_voti_lista as t1 , ".$prefix."_ele_lista as t2 where t1.id_lista=t2.id_lista and t1.id_cons='$id_cons' and t2.id_gruppo='$id_gruppo2'";
					}
					$res_presidente = $dbi->prepare("$sql");
					$res_presidente->execute();
					list($voti_pres) = $res_presidente->fetch(PDO::FETCH_NUM);					
					if ($voti_pres_tutti!=0){
						$perc_pres=number_format($arperc[$id_gruppo2],2); 
						$var1="<h2>voti: $voti_pres <span class=\"redbig\"> $perc_pres </span>%</h2>";	
					}else {$var1="";}
				}else{ //referendum
					$sql="select sum(si),sum(no),sum(validi),sum(bianchi),sum(nulli),sum(contestati) from ".$prefix."_ele_voti_ref where id_cons='$id_cons' and id_gruppo='$id_gruppo2'";
					$res_ref = $dbi->prepare("$sql");
					$res_ref->execute();
					if($res_ref->rowCount())
						list($voti_si,$voti_no,$validi,$bianchi,$nulli,$conte) = $res_ref->fetch(PDO::FETCH_NUM);
					if(($voti_si+$voti_no+$validi+$bianchi+$nulli+$conte)>0) 
					{
						$sql="select t3.orario,t3.data from ".$prefix."_ele_rilaff as t1 left join ".$prefix."_ele_cons_comune as t2 on t1.id_cons_gen=t2.id_cons_gen left join ".$prefix."_ele_voti_parziale as t3 on t2.id_cons=t3.id_cons where t1.id_cons_gen='$id_cons_gen' and t2.id_cons='$id_cons' order by t3.data desc, t3.orario desc limit 0,1";
						$aff = $dbi->prepare("$sql");
						$aff->execute();
						list($ora,$data) = $aff->fetch(PDO::FETCH_NUM);					
						$sql="select sum(voti_complessivi) from ".$prefix."_ele_voti_parziale where id_cons='$id_cons' and orario='$ora' and data='$data' and id_gruppo='$id_gruppo2'";
						$tot_rel = $dbi->prepare("$sql");
						$tot_rel->execute();
						list($tot_relativo) = $tot_rel->fetch(PDO::FETCH_NUM);
					}else{
						$voti_si=0;$voti_no=0;$validi=0;$bianchi=0;$nulli=0;$conte=0;$tot_relativo=0;
					}
					// totale assoluto
					$sql="select sum(maschi+femmine) from ".$prefix."_ele_sezione where id_cons='$id_cons'";
					$tot_ass = $dbi->prepare("$sql");
					$tot_ass->execute();

					list($tot_assoluto) = $tot_ass->fetch(PDO::FETCH_NUM);
					  // controlli del 15 giugno 2009 
					if($tot_assoluto)
						  $perc_tot=number_format(($tot_relativo*100)/$tot_assoluto,2);
					else $perc_tot=0;
					
					$tot_ref=$voti_si+$voti_no;
					if($tot_ref){
						$perc_si=number_format(($voti_si*100)/$tot_ref,2);
						$perc_no=number_format(($voti_no*100)/$tot_ref,2);
					}else{ $perc_si=0;$perc_no=0;}
					$var1="<table class=\"table-80\" style=\"text-align: center;\">";				
					if(isset($ora)){	
						list ($ore,$minuti,$secondi)=explode(':',$ora);
						$var1.=	"<tr>
							<td class=\"redbig\">
						<h2>"._PERC_ASS." $ore,$minuti:<span class=\"redbig\"> $perc_tot% </span></h2></td>
							</tr>";
					}				
					$var1 .="<tr>
						<td><h1>SI: $voti_si <span class=\"redbig\"> $perc_si </span>%</h1></td>
						</tr>
						<tr>
						<td><h1>NO: $voti_no<span class=\"redbig\"> $perc_no </span>% 
					</h1></td>
						</tr>
					</table>";				
				}
			echo "<tr>
			<td class=\"bggray\"><h1><b>$num_gruppo</b></h1></td>
			<td class=\"table-main\" style=\"text-align: left;\"><h1>$descr_gruppo</h1> $var1</td>";
			if($tipo_cons!=2 and $prognome)
				echo "<td onmouseover=\"this.style.cursor='pointer';\" onclick=\"javascript:window.open('modules.php?name=Elezioni&amp;file=foto&amp;id_gruppo=$id_gruppo2&amp;pdfvis=1')\">";
			else echo "<td>";
			echo "<b><img class=\"stemma\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_gruppo=$id_gruppo2\"   alt=\"immagine $descr_gruppo\" ><br>";
			if($tipo_cons!=2 and $prognome) echo _PROGRAM;
			echo "</b></td>";
			echo "</tr>
			</table>";			
			//Liste collegate
#			if ($numero!=0 and !$votol and $genere>1) 
			// verifica delle sezioni in relazione ai candidati (comuni >=15000  $LIMIT>=4 non c'e' il voto di lista 5/5/2009

#			if ($genere!=2 && $fascia>$limite)
#			      echo "<div><h6>Liste:"._SEZSCRU." $numero_l "._SU." $sezioni_l</h6></div>";
#			else
#			    if(!$circo and $votog) // non per le circoscrizionali, senato e camera
#			      echo "<div><h6>"._SEZSCRU." $numero_c "._SU." $sezioni_l</h6></div>";			
			echo "<table class=\"table-80\"><tr>";
			$sql="select id_cons ,id_lista ,num_lista, descrizione  from ".$prefix."_ele_lista where id_cons='$id_cons' and id_gruppo='$id_gruppo2'  ORDER BY num_lista " ;
			$result2 = $dbi->prepare("$sql");
			$result2->execute();
			$i=0;
			while(list($id_cons2,$id_lista2,$num_lista, $descr_lista) = $result2->fetch(PDO::FETCH_NUM)) {
  				if ($num_lista!=0) {			
					$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons='$id_cons' and id_lista='$id_lista2'";
					$res_lista = $dbi->prepare("$sql");
					$res_lista->execute();
					list($voti_lista) = $res_lista->fetch(PDO::FETCH_NUM);				
// calcolo della percentuale
					if ($circo){ // circoscrizioni
						$voti_lista_tutti=0;					
						$sql="select id_lista from ".$prefix."_ele_lista where id_circ='$id_circ'";
						$res_circ = $dbi->prepare("$sql");
						$res_circ->execute();
						while(list($lista_id) = $res_circ->fetch(PDO::FETCH_NUM)){
							$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_lista='$lista_id'";
							$res_circ_voti = $dbi->prepare("$sql");
							$res_circ_voti->execute();
							list($voti) = $res_circ_voti->fetch(PDO::FETCH_NUM);
							$voti_lista_tutti=$voti_lista_tutti+$voti;	
						}					
					}else{
				
// tutti 
						$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons='$id_cons'";
						$res_lista_tutti = $dbi->prepare("$sql");
						$res_lista_tutti->execute();
						list($voti_lista_tutti) = $res_lista_tutti->fetch(PDO::FETCH_NUM);
					}
					if($voti_lista_tutti!=0){
						$perc_lista=number_format(($voti_lista*100)/$voti_lista_tutti,5); 
						$perc_lista=number_format($perc_lista,3);// add luc 11 feb 2007
					}else{ 
						$perc_lista='';
					}
					$i++;
					echo "<td class=\"table-main\"><a href=\"modules.php?name=Elezioni&amp;id_gruppo=$id_gruppo2&amp;id_circ=$id_circ&amp;id_cons_gen=$id_cons_gen&amp;id_lista=$id_lista2&amp;op=partiti&amp;voti_lista=$voti_lista&amp;perc_lista=$perc_lista&amp;id_comune=$id_comune\">
					<img class=\"stemma\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_lista=$id_lista2\"  alt=\"stemma\" ><br>N. $num_lista  $descr_lista";				
					if ($voti_lista) echo "<br>voti: $voti_lista ";
					if ($perc_lista) echo "<span class=\"red\"> $perc_lista </span>%";					
					echo "</a></td>";
					}
					if (($i%3) ==0) echo "</tr><tr>";
				}				
				if (($i%3) !=0)echo "</tr></table>";else echo "<td></td></tr></table>";					
		    }
	    }	
		echo "";
	}else{
		// tot liste
		$sql="SELECT id_lista FROM ".$prefix."_ele_lista where id_cons='$id_cons' $circos ";
		$res = $dbi->prepare("$sql");
		$res->execute();
    	$max = $res->rowCount();		
		// tot voti liste
		if($circo) $sql="select sum(t1.voti)  from ".$prefix."_ele_voti_lista as t1, ".$prefix."_ele_lista as t2 where t1.id_lista=t2.id_lista and t1.id_cons='$id_cons' and t2.id_circ='$id_circ'";
		else $sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons='$id_cons'";
		$res_lista_tutti = $dbi->prepare("$sql");
		$res_lista_tutti->execute();		
		list($voti_lista_tutti) = $res_lista_tutti->fetch(PDO::FETCH_NUM);		
		$sql="select id_cons ,id_lista ,num_lista, descrizione  from ".$prefix."_ele_lista where id_cons='$id_cons' $circos ORDER BY num_lista  LIMIT $min,$offset";
		$result = $dbi->prepare("$sql");
		$result->execute();
		while(list($id_cons2,$id_lista,$num_lista, $descr_lista) = $result->fetch(PDO::FETCH_NUM)) {
			if ($num_lista!=0) {
				// voti lista
				$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons='$id_cons' and id_lista='$id_lista'";
				$res_lista = $dbi->prepare("$sql");
				$res_lista->execute();

				list($voti_lista) = $res_lista->fetch(PDO::FETCH_NUM);
				if($voti_lista_tutti)
					$perc_lista=number_format(($voti_lista*100)/$voti_lista_tutti,5);
				else $perc_lista=0;
				$perc_lista=number_format($perc_lista,2);
				echo "<table  class=\"table-80\">
				<tr>"
				."<td class=\"td-5\"><b>"._NUM."</b></td>"
				."<td class=\"bggray\"><b>"._DESCR."</b></td>"
				."<td class=\"td-5\"><b>"._SIMBOLO."</b></td>
				</tr>";
				echo "<tr><td class=\"bggray\"><h1><b>$num_lista</b></h1></td>
				<td class=\"table-main\"><a href=\"modules.php?name=Elezioni&amp;id_cons_gen=$id_cons_gen&amp;id_lista=$id_lista&amp;op=partiti&amp;voti_lista=$voti_lista&amp;perc_lista=$perc_lista&amp;id_comune=$id_comune\"><h1>$descr_lista<br></a>
				voti: $voti_lista <span class=\"redbig\">$perc_lista %</span></h1>";
				echo "</td><td><a href=\"modules.php?name=Elezioni&amp;id_cons_gen=$id_cons_gen&amp;id_lista=$id_lista&amp;op=partiti&amp;voti_lista=$voti_lista&amp;perc_lista=$perc_lista&amp;id_comune=$id_comune\">
				<img class=\"stemma\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_lista=$id_lista\"  alt=\"$descr_lista\" ></a>";
				echo "</td></tr></table>";
			}
		}
    //echo "</table>";
    }
    page($id_cons_gen,$go,$max,$min,$prev,$next,$offset,$file);
//CloseTable();
}



function partiti(){
// visualizza i dati di lista con i candidati

	global $genere,$admin, $prefix, $dbi, $offset, $min, $id_cons_gen,$votog,$votol,$circo, $id_cons,$tipo_cons,$file,$prev,$next,$id_circ,$id_comune,$id_lista,$id_gruppo,$voti_lista,$perc_lista;

  //dati();
  
  	if ($circo==1){
		$sql="select descrizione,num_circ  from ".$prefix."_ele_circoscrizione where id_circ='$id_circ'";
		$res_circ = $dbi->prepare("$sql");
		$res_circ->execute();
		list($descr_circ,$num_circ)=$res_circ->fetch(PDO::FETCH_NUM);
		if($num_circ) echo "<center><h1>"._CIRC_N." $num_circ: $descr_circ</h1>";
		# numero sezioni
	}		 
	if ($genere!=4){
		$sql="select descrizione  from ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
		$res_gruppo = $dbi->prepare("$sql");
		$res_gruppo->execute();
		list($descr_gruppo)=$res_gruppo->fetch(PDO::FETCH_NUM);		
	}else{
		$sql="select descrizione  from ".$prefix."_ele_lista where id_lista='$id_lista'";
		$res_gruppo = $dbi->prepare("$sql");
		$res_gruppo->execute();
		list($descr_gruppo)=$res_gruppo->fetch(PDO::FETCH_NUM);
  	}
  // numero sezioni scrutinate, escluse circorscrizioni (da aggiungere)
	if ($circo!=1)
	{
		$sqlcir="";
		$sql2="select max(num_sez)  from ".$prefix."_ele_sezione where id_cons='$id_cons'";
	}
	else
	{
		$sqlcir="and id_circ='$id_circ'";       
		$sql2="select count(num_sez)  from ".$prefix."_ele_sezione where id_sede in (select id_sede from ".$prefix."_ele_sede where id_circ='$id_circ')";
	}
	if ($votog)
		$sql="select t1.id_sez from ".$prefix."_ele_voti_candidato as t1 left join ".$prefix."_ele_candidato as t2 on t1.id_cand=t2.id_cand where t2.id_lista in (select id_lista from ".$prefix."_ele_lista where id_cons='$id_cons' $sqlcir) group by t1.id_sez ";
	else
		$sql="select id_sez from ".$prefix."_ele_voti_lista where id_lista in (select id_lista from ".$prefix."_ele_lista where id_cons='$id_cons' $sqlcir) group by id_sez ";
	$res1 = $dbi->prepare("$sql");
	$res1->execute();
	$numero=$res1->rowCount();
	$res2 = $dbi->prepare("$sql2");
	$res2->execute();
	list($sezioni)=$res2->fetch(PDO::FETCH_NUM);
	//$result = mysql_query("select id_cons ,id_lista ,num_lista, descrizione  from ".$prefix."_ele_lista where id_lista='$id_lista'", $dbi);
		 	
	$sql="select id_cons ,id_lista ,num_lista, descrizione  from ".$prefix."_ele_lista where id_lista='$id_lista'";
	$result = $dbi->prepare("$sql");
	$result->execute();
	list($id_cons2,$id_lista,$num_lista, $descr_lista) = $result->fetch(PDO::FETCH_NUM);
	echo " <center><h5>"._LISTA." Numero : <font color=\"red\">$num_lista</font><br></h5>";
	echo "<img src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_lista=$id_lista\" style=\"width:50; text-align:center;\"><h2> $descr_lista</h2><br >";
	if ($voti_lista OR $perc_lista){ 
		echo "<h5>";
		echo _VOTI.": <font color=\"red\">$voti_lista</font> "._PERC.": <font color=\"red\">$perc_lista %</font><br></h5>";
	}
	echo "<center>"._GRUPPO."<h1> $descr_gruppo</h1>";
	echo "<table width=\"60%\">";
	// candidati con voti ottenuti
		
	$sql="SELECT t1.id_cand , t1.cognome, t1.nome, t1.num_cand, t2.id_cand, sum(t2.voti) as somma  FROM ".$prefix."_ele_candidato as t1 , ".$prefix."_ele_voti_candidato as t2
	where t1.id_lista='$id_lista' and  t1.id_cand=t2.id_cand  group by t1.id_cand, t1.cognome, t1.nome, t1.num_cand, t2.id_cand order by somma desc, t1.num_cand asc" ;
	$res_candi = $dbi->prepare("$sql");
	$res_candi->execute();

	//$res_candi = mysql_query("SELECT id_cand , cognome, nome, num_cand  FROM ".$prefix."_ele_candidati 
	//where id_lista='$id_lista'  and id_cons='$id_cons order by num_cand" , $dbi);
	$num_candi=$res_candi->rowCount();
	if (!$num_candi) {
		$sql="SELECT id_cand , cognome, nome, num_cand  FROM ".$prefix."_ele_candidato 
		where id_lista='$id_lista'  and id_cons='$id_cons' order by num_cand" ;
		$res_candi = $dbi->prepare("$sql");
		$res_candi->execute();

		echo "<tr bgcolor=\"#EAEAEA\"><td >"._NUM."</td><td>"._CANDIDATO."</td></tr>";
		while(list($id_cand,$cognome,$nome, $num) = $res_candi->fetch(PDO::FETCH_NUM)) {
			
			echo "<tr><td>[ $num ]</td><td> $cognome $nome</td>";
		}
	}else{	
		echo "<tr bgcolor=\"#EAEAEA\"><td >"._NUM."</td><td>"._CANDIDATO."</td><td>"._PREFERENZE."</td></tr>";
		while(list($id_cand,$cognome,$nome, $num,$id_cand, $somma) = $res_candi->fetch(PDO::FETCH_NUM)) {
			
			echo "<tr><td>[ $num ]</td><td>
			<a href=\"modules.php?name=Elezioni&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;op=candidato_sezione&amp;min=$num&amp;offset=$num&amp;id_lista=$id_lista&amp;orvert=1&amp;offsetsez=$sezioni&id_circ=$id_circ\">
			$cognome $nome</a></td><td> $somma</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}

	
// funzione visualizzazione delle liste per camera e senato con raggruppamenti/coalizioni
function liste(){
	global $id_cons,$id_cons_gen,$prefix,$dbi,$min,$offset,$op,$tipo_cons,$prev,$next,$votog,$votol,$circo,$genere,$id_comune,$id_circ;
	//dati();
	$offset=10;
	if (!isset($min)) $min=0;

	// numero sezioni scrutinate sul gruppo
	if ($circo) $circos = "and t2.id_circ=$id_circ" ; else $circos='';
	if ($genere==0) {
		$sql="select *  from ".$prefix."_ele_voti_ref where id_cons='$id_cons' group by id_sez ";
	}else{
		$sql="select t1.*  from ".$prefix."_ele_voti_gruppo as t1 left join ".$prefix."_ele_gruppo as t2 on t1.id_gruppo=t2.id_gruppo where t2.id_cons='$id_cons'  $circos group by t1.id_sez ";
	}
	$res = $dbi->prepare("$sql");
	$res->execute();
	$numero=$res->rowCount();
	$sql="select t1.*  from ".$prefix."_ele_sezione as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t2.id_cons='$id_cons' $circos ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$sezioni=$res->rowCount();
	$sql="select chiusa  from ".$prefix."_ele_cons_comune where id_cons='$id_cons'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$chiusa=$res->rowCount();
	if ($numero!=0 and $chiusa==0) 
		echo "<div><h2>"._SEZSCRU." $numero "._SU." $sezioni</h2></div>";		
	echo "<div><h1>"._LISTE."</h1></div><br><br>";
	// tot liste
	$sql="SELECT t2.*  FROM ".$prefix."_ele_lista as t2 where t2.id_cons='$id_cons' $circos ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$max = $res->rowCount();	
	// tot voti liste
	$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons='$id_cons'";
	$res_lista_tutti = $dbi->prepare("$sql");
	$res_lista_tutti->execute();
	list($voti_lista_tutti) = $res_lista_tutti->fetch(PDO::FETCH_NUM);	
	$sql="select id_cons ,id_lista ,id_gruppo, num_lista, descrizione  from ".$prefix."_ele_lista as t2 where id_cons='$id_cons' $circos ORDER BY num_lista  LIMIT $min,$offset";
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
			echo "<table class=\"table-80\"><tr class=\"bggray\">"
			."<td class=\"td-5\"><b>"._NUM."</b></td>"
			."<td ><b>"._DESCR."</b></td>"
			."<td class=\"td-5\"><b>"._SIMBOLO."</b></td>"
			."<td class=\"td-5\"><b>"._GRUPPO."</b></td></tr>";
			echo "<tr><td class=\"bggray\"><h1>$num_lista</h1>"
			."</td>
			<td class=\"table-main\"><h1>$descr_lista<br>
			voti: $voti_lista <span class=\"redbig\"> $perc_lista</span> %</h1>";
			echo "</td>
			<td><a href=\"modules.php?name=Elezioni&amp;id_gruppo=$id_gruppo&amp;id_cons_gen=$id_cons_gen&amp;id_lista=$id_lista&amp;op=partiti&amp;voti_lista=$voti_lista&amp;perc_lista=$perc_lista&amp;id_comune=$id_comune\">
			<img class=\"stemma\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_lista=$id_lista\" alt=\"$descr_lista\" >";
			echo "</a></td>
			<td>
			<img class=\"stemma\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_gruppo=$id_gruppo\" alt=\"$descr_gruppo\" >
			<br>$descr_gruppo</td>
			</tr></table>";
		}
	}
    $file="index";
    $go=$op;
    page($id_cons_gen,$go,$max,$min,$prev,$next,$offset,$file);
}
	
	
	
function grafici($id_cons) {
	global $visgralista;
	//graf_votanti();
	graf_gruppo($visgralista);
	//graf_candidato();
}

//visualizzaione a seconda dello stato della consultazione
// finita si basa sui gruppi o liste per tutte le sezioni
if (!$op){
    $circos=''; // definizione provvisoria
	if ($genere==0) {$tab="ref";}else{$tab="gruppo";}
	if ($votog) {$tab="lista";}else{$tab="gruppo";}
	$sql="select id_sez from ".$prefix."_ele_voti_".$tab." where id_cons='$id_cons'  $circos group by id_sez ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$numero=$res->rowCount();
	$sql="select *  from ".$prefix."_ele_sezione where id_cons='$id_cons' $circos ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$sezioni=$res->rowCount();
	if ($numero==0) $op="gruppo";
	if ($numero==$sezioni) $op="graf_gruppo";	 
}
 
$test=phpversion();
if($test>=5.6) $phpver=1; else $phpver=0;
# echo "TEST: comune:$id_comune -- cons:$id_cons_gen<br>$TEST1 $TEST2";
switch ($op){
    case "circo":
    	circo();
    break;

    case "sezione":
    	sezione();
    break;

    case "candi":
      include("candidato.php");
      candidato();
    	//candi();
    break;

    case "gruppo":
    	gruppo();
    break;
    
    case "partiti":
    	partiti();
    BREAK;
    
    case "liste":
    	liste();
    break;
    
    case "come":
  	switch ($info){
		case 'dati':
			circo();
    		break;
	   case "confronti":
   		include("confronti.php");
   		break;
	
		case "affluenze_sez":
			if($phpver)
				include("affluenze.php");
			else
				include("affluenzephp5.php");
		break;
		case "votanti":
			if($phpver)
				include("votanti.php");
			else
				include("votantiphp5.php");
		break;
		default:
    		come($info);
	}
   break;

// esterni

   case "consiglieri":
   include("consiglieri.php");
   consiglio();
   break;

   case "gruppo_circo":
   include("gruppo.php");
   gruppo_circo();
   break;

   case "gruppo_sezione":
   include("gruppo.php");
   gruppo_circo();
   break;

   case "lista_circo":
   include("gruppo.php");
   gruppo_circo();
   break;

   case "lista_sezione":
   include("gruppo.php");
   gruppo_circo();
   break;

   case "candidato_circo":
   include("gruppo.php");
   gruppo_circo();
   break;



   case "candidato_sezione":
   include("gruppo.php");
   gruppo_circo();
   break;

   case "affluenze_graf":
   include("grafici.php");
   affluenze_graf();
   break;

   case "graf_votanti":
   include("grafici.php");
   graf_votanti();
   break;

   case "graf_candidato":
   include("grafici.php");
#   if (!$circo)graf_candidato();
   graf_candidato();
   break;

   case "graf_gruppo":
   include("grafici.php");
   graf_gruppo(0);
   break;
   
   case "graf_lista":
   include("grafici.php");
   graf_gruppo(1);
   break;


   

   case "tema":
   	include("theme.php");
   break;

   case "top":
   	include("top.php");
   break;
   
   case "contatti":
   	include("contatti.php");
   break;

   case "rss":
   	include("rss.php");
   break;
   
   case "evvai":
   	include("evvai.php");
   break;
   case "privacy":
   	include("blocchi/privacy/privacy.php");
   break;	
   default:
	include("blocchi/privacy/privacy.php");

}

if ($csv!=1 && $rss!=1){
########## icona rss da sistemare in un altra parte con calma...
global $circo;
if($genere!=0 && $id_circ==''){ // no referendum ne circoscrizioni
	echo "<div align=\"right\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=rss&amp;rss=1\"><img class =\"nobordo\" width=\"60\" src=\"modules/Elezioni/images/valid-rss.png\" alt=\"rss\"></a></div>";
}

include("footer.php");
}
?>
