<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it rgigli@libero.it                                   */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}

$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ?
	$_GET : $_POST;

$id_comune= (isset($param['id_comune'])) ? $param['id_comune']:$siteistat;
if (isset($param['id_cons_gen'])) $id_cons_gen=intval($param['id_cons_gen']); else $id_cons_gen='';
if (isset($param['op'])) $op=$param['op']; else $op='';
if (isset($param['minsez'])) $minsez=intval($param['minsez']); else $minsez='';
if (isset($param['id_lista'])) $id_lista=intval($param['id_lista']); else $id_lista='';
if (isset($param['id_circ'])) $id_circ=intval($param['id_circ']);
if (isset($param['csv'])) $csv=intval($param['csv']); else $csv='';
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
if (isset($param['id_gruppo'])) $id_gruppo=intval($param['id_gruppo']); else $id_gruppo='';
if (isset($param['tipo_cons'])) $tipo_cons=intval($param['tipo_cons']); else $tipo_cons='';
if (isset($param['xls'])) $xls=intval($param['xls']); else $xls='';
if (isset($param['pdf'])) $pdf=intval($param['pdf']); else $pdf='';

# anti-xss nov. 2009 
$id_comune=htmlentities($id_comune);
$perc=floatval($perc);
$perc_lista=floatval($perc_lista);
#$datipdf= htmlentities($datipdf);
$op= htmlentities($op);
$info= htmlentities($info);
$files=htmlentities($files);
$lettera=htmlentities($lettera);
$id_comune=intval($id_comune);

//$id_cons_gen=$_GET['id_cons_gen'];
$sql="SELECT t1.tipo_cons,t3.genere,t2.id_cons FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2, ".$prefix."_ele_tipo as t3 where t1.tipo_cons=t3.tipo_cons and t1.id_cons_gen=t2.id_cons_gen and t2.id_cons_gen='$id_cons_gen' and t2.id_comune='$id_comune'" ;
$res = $dbi->prepare("$sql");
$res->execute();
list($tipo_cons,$genere,$id_cons) = $res->fetch(PDO::FETCH_NUM);
global $lang,$circo,$id_circ;
if(!isset($id_circ)) $id_circ=0;
if(isset($circo)) $_SESSION['id_circ']=$id_circ;
if (isset($circo) and $circo) {$circos="and t2.id_circ='$id_circ'"; $circos4="and t4.id_circ='$id_circ'";}
else {$circos=''; $circos4='';}
if (isset($param['ops'])) $ops=$param['ops']; else $ops='';
if (isset($param['pag'])) $pag=$param['pag']; else $pag=0;
if (isset($param['num_ref'])) $num_ref=$param['num_ref'];
if (isset($param['num_refs'])) $num_refs=$param['num_refs'];
$bgcolor2='#cacaca';
if (!IsSet($num_ref)) { 
	$num_ref=1;
	$sql="SELECT id_gruppo from ".$prefix."_ele_gruppo where id_cons=$id_cons";
	$resg = $dbi->prepare("$sql");
	$resg->execute();
	$num_refs= $resg->rowCount(); //quante pagine?
}
//**************************************************************************
//        ELE
//**************************************************************************
//controllo_finale($id_cons);

global $lang, $fascia, $limite, $votog;
include_once("modules/Elezioni/language/lang-$lang.php");
# testata
$datipdf='';
if($csv==1){
	include_once("modules/Elezioni/funzioni.php");
	$sql="SELECT descrizione FROM ".$prefix."_ele_comune where id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($descr_com) = $res->fetch(PDO::FETCH_NUM);
    $descr_com =stripslashes($descr_com); 
	$datipdf .= "<div style=\"width:100%; margin:0px auto; text-align:center;\">";
	$siteistat=$id_comune;
	# salva sull'hardisk lo stemma del comune    style=\"vertical-align: text-bottom;\"
	$logo=verificasimbolo();
	$immagine= "<img src=\"modules/Elezioni/images/$logo\" alt=\"logo\" align=\"left\"/>";
	if($xls!=1) $datipdf .= "<table style=\"width:50%;margin:0px auto; text-align:center;\"><tr><td>$immagine</td><td>";
    $datipdf .= ""._COMUNE." $descr_com <br>
	"._RISULTA." "._CONSULTA."<br>";
	$datipdf .= "$descr_cons <br>"._DISCLAIMER."";
	if($xls!=1) $datipdf .=  "</td></tr></table>";
	$datipdf .="</div>";
	$html = "<style type=\"text/css\">
	<!--

	.td-89 {
		width: 89%;	
		border: 1px;
		text-align: left; 
	}
	.td-vuoto {
		width: 100%;	
		border: 1px;
		text-align: left; 
	}

	.td-5 {
		margin: 0px 0 0 0px;
		width: 5%;	
		/*border: none;*/
		padding: 0px;
		text-align: center; 
	}	

	.bgw	{
		background: #ffffff; 
		font-size: 13px; 
		font-family: Helvetica;
		text-align: right;
	}

	.bggray 	{
		background: #ffffff; 
		FONT-SIZE: 13px; 
		FONT-FAMILY: Helvetica;
		border: 1px;
		text-align:right;
	}

	.bggray2 	{
		background: #EFEFEF; 
		FONT-SIZE: 13px; 
		FONT-FAMILY: Helvetica;
		border: 1px;
		text-align:right;
		}
	-->
	</style>";
}
// icone stampa e grafici   style=\"margin:0px auto;
if ($csv!=1){
	if(isset($num_ref)) $curref="&amp;num_ref=$num_ref"; else $curref='';
	if (!isset($html)) $html='';
	$html .= "<div>
	<a href=\"modules.php?name=Elezioni&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;op=graf_votanti\">
	"._VER_GRAF." <img class=\"image\" src=\"modules/Elezioni/images/grafici.png\" alt=\"\" /></a>
	<a href=\"modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=votanti&amp;csv=1&amp;pag=$pag&amp;num_ref=$num_ref&amp;num_refs=$num_refs\">"._VER_STAMPA."
	<img class=\"image\" src=\"modules/Elezioni/images/printer.png\" alt=\"\" /></a>
	<a href=\"modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=votanti&amp;xls=1&csv=1;&amp;pag=$pag&amp;num_ref=$num_ref&amp;num_refs=$num_refs\">
	<img class=\"image\" src=\"modules/Elezioni/images/csv.gif\" alt=\"\" /></a>
	<a href=\"modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=votanti&amp;pdf=1&csv=1;&amp;pag=$pag&amp;num_ref=$num_ref&amp;num_refs=$num_refs\">
	<a href=\"modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=votanti&amp;pdf=1&csv=1;&amp;pag=$pag$curref\">
	<img class=\"image\" src=\"modules/Elezioni/images/pdf.gif\" alt=\"\" /></a>
	</div>";
}
if($circo)
	$sql="SELECT sum(t1.maschi),sum(t1.femmine) FROM ".$prefix."_ele_sezione as t1,".$prefix."_ele_sede as t2 where t1.id_sede=t2.id_sede $circos";
else
	$sql="SELECT sum(maschi),sum(femmine) FROM ".$prefix."_ele_sezione where id_cons='$id_cons'";
$res = $dbi->prepare("$sql");
$res->execute();
list($totm,$totf) = $res->fetch(PDO::FETCH_NUM);
$totel=$totm+$totf;
if (!IsSet($pag)) {$pag=0;} //inizializza il numero di pagina 
/*
if (!IsSet($num_ref)) { 
	$num_ref=1;
	$resg = mysql_query("SELECT id_gruppo from ".$prefix."_ele_gruppo where id_cons=$id_cons", $dbi);
	$num_refs= mysql_num_rows($resg); //quante pagine?
}
*/
if(($genere!=4) and $pag==0){ //diverso da liste a piu' candidati
	$ops=4;	//gestione gruppi (anche liste uninominali)
}else{
	$ops=3; //gestione liste
}
$cols=4;
$sql="SELECT id_gruppo,num_gruppo from ".$prefix."_ele_gruppo where id_cons=$id_cons and num_gruppo=$num_ref";
$resg = $dbi->prepare("$sql");
$resg->execute();
list($idg,$numg) = $resg->fetch(PDO::FETCH_NUM);
$sql="SELECT id_sez,num_sez,t1.id_sede as id_sede,t2.id_circ as id_circ FROM ".$prefix."_ele_sezione as t1,".$prefix."_ele_sede as t2 where t1.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circos order by num_sez ";
$res = $dbi->prepare("$sql");
$res->execute();
$max = $res->rowCount(); //quante sezioni?
$sql="SELECT id_sez,num_sez,t1.id_sede as id_sede,t2.id_circ as id_circ FROM ".$prefix."_ele_sezione as t1,".$prefix."_ele_sede as t2 where t1.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circos order by num_sez ";
$res = $dbi->prepare("$sql");
$res->execute();
$num_sez = $res->rowCount(); //quante sezioni?
#for ($i=1;$i<=$num_sez;$i++){
$i=1;	
while ($sezione[$i] = $res->fetch(PDO::FETCH_BOTH)) $ar[$i++]=0;

#	$sezione[$i]=$res->fetch(PDO::FETCH_BOTH); //inizializza l'array delle sezioni
	
#}
$tab3="_ele_voti_lista";
$riga3 = "<tr class=\"bggray2\">
<td>"._SEZIONI."</td>
<td>"._VOTIU."</td>
<td>"._VOTID."</td>
<td>"._VOTIE."</td>"; //testata con nomi dei campi
$cols=4;
if ($genere==0) {  //se e' un referendum
	$riga3 .= "<td>"._SI."</td><td>"._NO."</td>";
	$cols+=2;
} elseif ((($genere==5) or ($genere==3)) and $pag==1){
	$riga3 .= "<td>Voti "._LISTE."</td>";
	$cols++;
	if(isdisgiunto()){
		$riga3 .= "<td>Voti "._PRESI."</td>";
		$riga3 .= "<td>"._SOLO_LISTA."</td>";
		$cols+=2;
	} // voto disgiunto
	if (!$votog) {$riga3 .= "<td>"._ASOLO_GRUPPO."</td>"; $cols++; }
}
$riga3 .= "<td>"._VALIDI."</td><td>"._NULLI."</td><td>"._BIANCHI."</td><td>"._CONTESTATI."</td>"
."</tr>\n";
$cols+=4;
if($pdf==1)
	$riga1 = "<table style=\"width:100%; margin-left: auto; margin-right: auto;\" summary=\"Tabella dei voti espressi\">";
else
	$riga1 = "<table style=\"width:60%; margin-left: auto; margin-right: auto;\" summary=\"Tabella dei voti espressi\">";
$riga1 .= "<tr>"; #"<tr class=\"bggray2\">";

if ($genere>0) {  //se non e' un referendum
	if (!($genere==4) and $pag==0){  //se non e' una lista uninominale ed e' la prima pagina
		$tab="SELECT 0,t2.id_sez,t2.num_sez,t2.validi,'0','0',t2.validi,t2.nulli,t2.bianchi,t2.contestati, t4.id_circ,t2.id_sede,'0',t2.voti_nulli FROM ".$prefix."_ele_sezione as t2 left join ".$prefix."_ele_sede as t4 on (t2.id_sede=t4.id_sede) where t2.id_cons='$id_cons' and t2.validi+t2.nulli+t2.bianchi+t2.contestati>0 $circos4 group by t2.id_sez,t2.num_sez,t2.validi,t2.nulli,t2.bianchi,t2.contestati, t4.id_circ,t2.id_sede,t2.voti_nulli order by t2.num_sez ";
	}else{ // e' una lista uninominale o la seconda pagina
		# voto disgiunto regione sicilia aggiunge il campo solo lista
		if(isdisgiunto()){
		$tab="SELECT '0',t1.id_sez,t1.num_sez,sum(t2.voti),t1.solo_gruppo,t1.solo_lista,t1.validi,t1.nulli,t1.bianchi,t1.contestati, t4.id_circ,t1.id_sede,'0',t1.voti_nulli
		FROM ".$prefix."_ele_sezione as t1 left join ".$prefix.$tab3." as t2 on (t1.id_sez=t2.id_sez)
		left join ".$prefix."_ele_sede as t4 on (t1.id_sede=t4.id_sede)
		where t1.id_cons='$id_cons' and t1.id_cons=t2.id_cons $circos4 group by t1.id_sez,t1.num_sez,t1.solo_gruppo,t1.solo_lista,t1.validi,t1.nulli,t1.bianchi,t1.contestati,t4.id_circ,t1.id_sede,t1.voti_nulli order by t1.num_sez ";
		}else{
		$tab="SELECT '0',t1.id_sez,t1.num_sez,sum(t2.voti),t1.solo_gruppo,'0',t1.validi,t1.nulli,t1.bianchi,t1.contestati, t4.id_circ,t1.id_sede,'0',t1.voti_nulli
		FROM ".$prefix."_ele_sezione as t1 left join ".$prefix.$tab3." as t2 on (t1.id_sez=t2.id_sez)
		left join ".$prefix."_ele_sede as t4 on (t1.id_sede=t4.id_sede)
		where t1.id_cons='$id_cons' and t1.id_cons=t2.id_cons $circos4 group by t1.id_sez,t1.num_sez,t1.solo_gruppo,t1.validi,t1.nulli,t1.bianchi,t1.contestati,t4.id_circ,t1.id_sede,t1.voti_nulli order by t1.num_sez ";
		}
	}
#	$riga1 = "";
	if($pag==0)$riga1 .="<td colspan=\"$cols\"><div style=\"text-align: center;\"><h2>"._DETTAGLIO." "._VOTIE."</h2></div></td>";
	else $riga1 .="<td colspan=\"$cols\"><h4>"._DETTAGLIO." "._VOTIE." "._ASOLA_LISTA."</h4></td>";
}else{ // e' un referendum --> t3.id_gruppo vuota per allineare il while (da rifare con array)
	$tab="SELECT t1.id_gruppo,t1.id_sez,t2.num_sez,t1.si,t1.no,'0',t1.validi,t1.nulli,t1.bianchi,t1.contestati, t4.id_circ,t2.id_sede,t3.num_gruppo,'0'
	FROM ".$prefix."_ele_voti_ref as t1 left join ".$prefix."_ele_sezione as t2 on (t1.id_sez=t2.id_sez)
	left join  ".$prefix."_ele_gruppo as t3 on (t1.id_gruppo=t3.id_gruppo) left join ".$prefix."_ele_sede as t4 on (t2.id_sede=t4.id_sede)
	where t1.id_cons='$id_cons' and t1.id_gruppo='$idg' $circos4 order by t2.num_sez ";
	$riga1  .= "<td colspan=\"$cols\"><div style=\"width:100%;margin:0px auto;text-align:center;\">";
	$riga1  .="<h2>"._DETTAGLIO." "._VOTIE."</h2>";
	$sql="select descrizione from ".$prefix."_ele_gruppo where id_gruppo='$idg'";
	$des = $dbi->prepare("$sql");
	$des->execute();
	list($descrizione)=$des->fetch(PDO::FETCH_BOTH);
	$riga1 .="<h4>$descrizione</h4></div></td>";
}
$riga1.="</tr>";
$sql="$tab ";
$res = $dbi->prepare("$sql");
$res->execute();
$num_scr = $res->rowCount();
//$riga2= "<div>"._SEZSCR." $num_scr su $num_sez</div>";//sezioni scrutinate
#$riga2 = "<table style=\"width:100%;display: block; margin-left: auto; margin-right: auto; border:1px solid #6A6A6A;\" summary=\"Tabella dei voti espressi\">";

$sql="$tab ";
$res = $dbi->prepare("$sql");
$res->execute();
$num_scr = $res->rowCount();
$righe= "";
$scrutinate=1;
$tot_u=0;$tot_d=0;$tot_voti=0; $tot_si=0;$tot_no=0;$tot_validi=0;$tot_nulli=0;$tot_bianchi=0;$tot_contestati=0;
$tot_sololista=0;$tot_gruppo=0;
#$si e $no sono valide anche per voti lista e solo gruppo per i non referendum
while (list($id_gruppo,$id,$num,$si,$no,$sololista,$validi,$nulli,$bianchi,$contestati,$id_circ,$id_sede,$gruppo,$votinulli) = $res->fetch(PDO::FETCH_NUM)){
	$nulli+=$votinulli;
	// inserimento numeri di sez non scrutinate
	while ($sezione[$scrutinate][1] < $num) { 
		$righe.= "<tr><td><span style=\"color: rgb(255, 0, 0);\">".$sezione[$scrutinate][1]."</span></td></tr>\n";
		$scrutinate++;
	}
	# voti sindaco, gruppo o presidente
	$tab5="SELECT sum(voti) FROM ".$prefix."_ele_voti_gruppo where id_cons='$id_cons' and id_sez='$id'";
	$sql="$tab5";
	$res3 = $dbi->prepare("$sql");
	$res3->execute();
	list($sindaco) = $res3->fetch(PDO::FETCH_NUM);	
	$scrutinate++; 
	// fine inserimento	
	$tab2="SELECT max(voti_donne),max(voti_uomini),max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_cons='$id_cons' and id_sez='$id'";
	if ($genere==0) $tab2 .= " and id_gruppo=$id_gruppo";
	$sql=$tab2;
	$res2 = $dbi->prepare("$sql");
	$res2->execute();
	list($votid,$votiu,$voti) = $res2->fetch(PDO::FETCH_NUM);
//		$voti=$votiu+$votid;
	$tot_gruppo+=$sindaco;
	$tot_u+=$votiu;
	$tot_d+=$votid;
	$tot_voti+=$voti;
	$tot_si+=$si;
	$tot_no+=$no;
	$tot_validi+=$validi;
	$tot_nulli+=$nulli;
	$tot_bianchi+=$bianchi;
	$tot_contestati+=$contestati;
	$tot_sololista+=$sololista;
	if($num % 2)
		$righe .= "<tr class=\"bgw\">";
	else
		$righe .= "<tr class=\"bggray2\">";
	$righe .= "<td>$num</td>
	<td>".number_format($votiu,0,',','.')."</td>
	<td>".number_format($votid,0,',','.')."</td>
	<td>".number_format($voti,0,',','.')."</td>";
	if ($genere==0 or ((($genere==5) or ($genere==3)) and $pag==1)){
		$righe .= "<td>".number_format($si,0,',','.')."</td>";
		if(isdisgiunto()){
			$righe .= "<td>".number_format($sindaco,0,',','.')."</td>";
			$righe .= "<td>".number_format($sololista,0,',','.')."</td>";
		}
		if (!$votog) $righe .= "<td>".number_format($no,0,',','.')."</td>";	
	}
	$righe .= "<td>".number_format($validi,0,',','.')."</td>
	<td>$nulli</td>
	<td>$bianchi</td>
	<td>$contestati</td></tr>";
}
#if ($num<$num_sez)
if(isset($sezione[$scrutinate][1]) and $num<$sezione[$scrutinate][1])	{
	while(isset($sezione[$scrutinate][1])) {
		$righe .= "<tr><td align=\"center\">";
		$righe .="<span style=\"color: rgb(255, 0, 0);\">".$sezione[$scrutinate++][1]."</span></td></tr>";
	}
}
$righet='';
if($num_scr){
	$righet = "<tr class=\"bggray\">
	<td ></td>
	<td>"._VOTIU."</td>
	<td>"._VOTID."</td>
	<td>"._VOTIE."</td>"; //testata con nomi dei campi
	if ($genere==0) {  //se e' un referendum
		$righet .= "<td>"._SI."</td><td>"._NO."</td>";
	} elseif ((($genere==5) or ($genere==3)) and $pag==1){
		$righet .= "<td>Voti "._LISTE."</td>";
		if(isdisgiunto()){
			$righet .= "<td>Voti "._PRESI."</td>";
			$righet .= "<td>"._SOLO_LISTA."</td>";
		} // voto disgiunto	
		if (!$votog) $righet .= "<td>"._ASOLO_GRUPPO."</td>";		
	}
	if($totel==0) $totelrip="0.00"; else $totelrip=number_format($tot_voti*100/$totel,2);
	if($totf==0) $totfrip="0.00"; else $totfrip=number_format($tot_d*100/$totf,2);
	if($totm==0) $totmrip="0.00"; else $totmrip=number_format($tot_u*100/$totm,2);
	$righet .= "<td>"._VALIDI."</td><td>"._NULLI."</td><td>"._BIANCHI."</td><td>"._CONTESTATI."</td>"
	."</tr><tr class=\"bgw\"><td><b>"._TOT."</b></td><td><b>".number_format($tot_u,0,',','.')."</b><br /><i>(".$totmrip."%)</i></td><td><b>".number_format($tot_d,0,',','.')."</b><br /><i>(".$totfrip."%)</i></td><td><b>".number_format($tot_voti,0,',','.')."</b><br /><i>(".$totelrip."%)</i></td>";
		// se e' un referendum o una consultazione con raggruppamenti
	if($tot_validi){
		if ($genere==0 or ((($genere==5) or ($genere==3)) and $pag==1)){
			$righet .= "<td><b>".number_format($tot_si,0,',','.')."</b><br /><i>(".number_format($tot_si*100/$tot_validi,2)."%)</i></td>";
			if(isdisgiunto()){
				$righet .="<td><b>".number_format($tot_gruppo,0,',','.')."</b><br /><i>(".number_format($tot_gruppo*100/$tot_validi,2)."%)</i></td>";
				$righet .="<td><b>".number_format($tot_sololista,0,',','.')."</b><br /><i>(".number_format($tot_sololista*100/$tot_validi,2)."%)</i></td>";
			}
			if(!$votog) $righet .="<td><b>".number_format($tot_no,0,',','.')."</b><br /><i>(".number_format($tot_no*100/$tot_validi,2)."%)</i></td>";	
		}
		$righet .= "<td><b>".number_format($tot_validi,0,',','.')."</b><br /><i>(".($tot_voti ? number_format($tot_validi*100/$tot_voti,2):'0.00')."%)</i></td><td><b>"
		.number_format($tot_nulli,0,',','.')."</b><br /><i>(".($tot_voti ? number_format($tot_nulli*100/$tot_voti,2):'0.00')."%)</i></td><td><b>".number_format($tot_bianchi,0,',','.')."</b><br /><i>(".($tot_voti ? number_format($tot_bianchi*100/$tot_voti,2):'0.00')."%)</i></td><td><b>".number_format($tot_contestati,0,',','.')."</b><br /><i>(".($tot_voti ? number_format($tot_contestati*100/$tot_voti,2):'0.00')."%)</i></td></tr>";
	}else{
		if ($genere==0 or ((($genere==5) or ($genere==3)) and $pag==1)){
			$righet .= "<td><b>".number_format($tot_si,0,',','.')."</b><br /><i>(0.00%)</i></td><td><b>".number_format($tot_no,0,',','.')."</b><br /><i>(0.00%)</i></td>";
		}
		$righet .= "<td><b>0</b><br /><i>(0.00%)</i></td><td><b>"
		.number_format($tot_nulli,0,',','.')."</b><br /><i>(".($tot_voti ? number_format($tot_nulli*100/$tot_voti,2):'0,00')."%)</i></td><td><b>".number_format($tot_bianchi,0,',','.')."</b><br /><i>(".($tot_voti ? number_format($tot_bianchi*100/$tot_voti,2):'0,00')."%)</i></td><td><b>".number_format($tot_contestati,0,',','.')."</b><br /><i>(".($tot_voti ? number_format($tot_contestati*100/$tot_voti,2):'0,00')."%)</i></td></tr>";
	}
}
#$righe .= "</table>";
if(!isset($html)) $html='';
$html .= $riga1; #."<div style=\"width:100%;margin:0px auto; text-align:center;\">";
#$html .= $riga2;
$html .= $righet;
$html .= $riga3;
$html .= $righe;
#$html .="";
if($genere==0){ //se e' referendum
	if ($xls!='1' && $pdf!='1' && $csv!='1'){
		#'Pagina precedente' e 'Pagina Successiva'
		$cur=$num_ref;
		$html.="<tr><td colspan=\"$cols\">";
		if ($cur>1) {
			  $num_ref--;
			  $html .= "<a href=\"modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=votanti&amp;num_ref=$num_ref&amp;num_refs=$num_refs&amp;csv=$csv&amp;xls=$xls&amp;pdf=$pdf\">";
			  $html .= "[ <b>"._PREV_MATCH."</b> ]</a>";
		}
		if ($cur<$num_refs) {
			$cur++;        
			$html .= "<a href=\"modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=votanti&amp;num_ref=$cur&amp;num_refs=$num_refs&amp;csv=$csv&amp;xls=$xls&amp;pdf=$pdf\">";
			
			$html .= "[ <b>"._NEXT_MATCH."</b> ]</a>";
		}
		$html.="</td></tr>";
	}
}
if($genere==5 or $genere==3){ //se vi sono raggruppamenti
	if($csv!=1 and $fascia>$limite){
		$pag=($pag==0 ? 1:0);
		$html .= "<tr><td colspan=\"$cols\"><a href=\"modules.php?name=Elezioni&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;op=come&amp;info=votanti&amp;pag=$pag&amp;csv=$csv\"><b>";
		if($pag) $html .=  _VOTIL;
		   //_CONTR_CONS;
		else $html .= _VOTIE; 
		//_CONTR_ESPR;
		$html .= "</b></a></td></tr>";
	}
}
#$html .= "</div>";
#$html.="</table>";
if($csv==1){
  $data=date("d-m-y G:i");
  $html .="<tr><td colspan=\"$cols\" style=\"text-align: center; border-top: 1px;\"><br/><i>Stampato: $data</i>";
  $html .="<br/><i>Eleonline by l. apolito & r. gigli - www.eleonline.it</i></td></tr></table>";		
}	#die($html);
?>
