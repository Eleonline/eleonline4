<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  rgigli@libero.it                                  */
/************************************************************************/

if (!defined('ADMIN_FILE')) {
    die ("You can't access this file directly...");
}

########################################
# Affluenze
function votanti($id_cons,$do,$id_circ,$id_sede,$id_sez,$ops,$ov,$mv,$gv,$msv,$av){

global $aid, $prefix, $dbi,$tipo_cons,$genere,$id_cons_gen,$id_comune,$ops;
##################
echo "<style>";
echo "DIV.table"; 
echo "{
    display:table;
}
FORM.tr
{
    display:table-row;
	text-align: center;
}
DIV.tr
{
    display:table-row;
	background-color: #D3D3D3;
	text-align: center;
}
SPAN.tdm
{
    display:table-cell;
	border: 1px solid black;
	padding: 5px;
}
SPAN.td
{
    display:table-cell;
}";
echo "</style>";


##################
$bgcolor1="#7777ff";
$bgcolor2=$_SESSION['bgcolor2'];
$sql="SELECT vismf from ".$prefix."_ele_cons_comune where id_cons='$id_cons'";
$res = $dbi->prepare("$sql");
$res->execute();
if($res) list($vismf)= $res->fetch(PDO::FETCH_NUM); else $vismf=0;
$sql="SELECT orario,data FROM ".$prefix."_ele_rilaff where id_cons_gen=$id_cons_gen order by data,orario ";
$res = $dbi->prepare("$sql");
$res->execute();
$num = $res->rowCount();
echo "<table><tr><td>";
$sql="SELECT maschi,femmine FROM ".$prefix."_ele_sezioni where id_sez=$id_sez";#echo $sql;
$ressez = $dbi->prepare("$sql");
$ressez->execute();
list($maschi,$femmine)=$ressez->fetch(PDO::FETCH_NUM);

$afferr=0;
$sql="select tipo from ".$prefix."_ele_controlli where id_sez='$id_sez' and tipo='affluenze'";
$rese = $dbi->prepare("$sql");
$rese->execute();
if($rese->rowCount()) $afferr=1;

$y=0;

$riga=array();
$rigat=array();	
$uscita=0;
while (list($ora,$giorno)= $res->fetch(PDO::FETCH_NUM)){
	$y++;
	$rigat[$y]='';
	$riga[$y]='';
	$rigat[$y]= "<div class=\"tr\">";
	if ($genere==0){ //e' un referendum 
		$rigat[$y].= "<span class=\"tdm\" bgcolor=\"$bgcolor1\" align=\"center\" width=\"32\"><b>"._NUM."</b></span>";
	}
	$rigat[$y].= "<span class=\"tdm\" bgcolor=\"$bgcolor1\" align=\"center\" width=\"32\"><b>"._ORA."</b></span>"
	."<span class=\"tdm\" bgcolor=\"$bgcolor1\" align=\"center\" width=\"32\"><b>"._DATA."</b></span>";
	$rigat[$y].= "<span class=\"tdm\" bgcolor=\"$bgcolor1\" align=\"center\"><b>"._VOTIU."</b></span>";
	$rigat[$y].= "<span class=\"tdm\" bgcolor=\"$bgcolor1\" align=\"center\"><b>"._VOTID."</b></span>"
	."<span class=\"tdm\" bgcolor=\"$bgcolor1\" align=\"center\"><b>"._VOTIT."</b></span>"; 
	if ($genere==0){ 
		$sql="SELECT * FROM ".$prefix."_ele_gruppo where id_cons='$id_cons'  ";
		$res2 = $dbi->prepare("$sql");
		$res2->execute();
		$max = $res2->rowCount();
	}else{ $max=1;} 
	$op="rec_add_votanti";
	$rigat[$y].= "</div>";
	$errvot1=0;
	$numril=0;
	$autofocus=0;
	for ($i=1;$i<=$max;$i++){
		$query="SELECT * FROM ".$prefix."_ele_voti_parziale as t1 left join ".$prefix."_ele_gruppo as t2 
		on (t1.id_gruppo=t2.id_gruppo) where t1.id_sez='$id_sez' 
		and t1.id_cons='$id_cons' and t1.orario='$ora' and t1.data='$giorno'";
		if ($genere==0){
			$query.=" and t2.num_gruppo=$i";
		}
		$sql=$query;
		$result = $dbi->prepare("$sql");
		$result->execute();
		list($id_cons2,$id_sez2,$id_parz,$orario,$data, $voti_u, $voti_d, $voti_t,$id_gruppo) = $result->fetch(PDO::FETCH_NUM);
	   	$sql="SELECT num_gruppo FROM ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo' ";
		$res2 = $dbi->prepare("$sql");
		$res2->execute();
		$sql="select count(0) FROM ".$prefix."_ele_voti_parziale where id_sez='$id_sez' and id_gruppo='$id_gruppo'";
		$resril = $dbi->prepare("$sql");
		$resril->execute();
        list($tmpril)=$resril->fetch(PDO::FETCH_NUM);
		if($tmpril>$numril) $numril=$tmpril;
		if ($res2)
			list($gruppo)= $res2->fetch(PDO::FETCH_NUM);
		else
			$gruppo=0;
		if (!$gruppo>0) {
			$gruppo=$i;
			$sql="SELECT id_gruppo FROM ".$prefix."_ele_gruppo where num_gruppo=$gruppo and id_cons=$id_cons";
			$res3 = $dbi->prepare("$sql");
			$res3->execute();
			if ($res3)
			list($id_gruppo)=$res3->fetch(PDO::FETCH_NUM);
		}
		if(!isset($precede[$id_gruppo])) $precede[$id_gruppo]=0; if(!isset($error)) $error=0;
		if($precede[$id_gruppo]>($voti_t) and $voti_t!='') $error=1;
		$precede[$id_gruppo]=$voti_t;
		if (($voti_u+$voti_d and $voti_u+$voti_d!=$voti_t) or $error){
			$riga[$y].= "<form class=\"tr\"  data-ajax=\"false\" style=\"background-color: rgb(255, 0, 0); text-align: center\" name=\"votanti\" action=\"principale.php\">";
		}else{
			$riga[$y].= "<form class=\"tr\" data-ajax=\"false\" action=\"principale.php\">";
		}
		if ($genere==0){ // e' un referendum
			$riga[$y].= "<span class=\"td\" align=\"center\">$gruppo</span>";
		}
		$riga[$y].= "<span class=\"td\"><input type=\"hidden\" name=\"op\" value=\"rec_add_votanti\"/>";
		$riga[$y].= "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\"/>"
		."<input type=\"hidden\" name=\"funzione\" value=\"2\"/>"
		."<input type=\"hidden\" name=\"genere\" value=\"$genere\"/>"
		."<input type=\"hidden\" name=\"id_sez\" value=\"$id_sez\"/>"
		."<input type=\"hidden\" name=\"id_circ\" value=\"$id_circ\"/>"
		."<input type=\"hidden\" name=\"id_sede\" value=\"$id_sede\"/>"; 
        list ($anno,$mese,$di)=explode('-',$giorno);
		$riga[$y].= "$ora</span><span class=\"td\" align=\"center\">$di-$mese-$anno</span>";
		if ($voti_u > $maschi) {$riga[$y].= "<span class=\"td\" align=\"center\" bgcolor=\"red\">"; $errvot1=1;}
		else $riga[$y].= "<span class=\"td\" align=\"center\">";
		$af='';
		$spuntacopia='';
		if(!$autofocus) {
			if($voti_u==0) {$af='autofocus'; $autofocus=1;
			if($genere==0 and $gruppo==1) $spuntacopia="<input type=\"checkbox\" name=\"copia\" value=\"1\"> Copia su tutti i referendum";
		}}
		if ($y<$num and $vismf==0 and $voti_u==0) { $riga[$y].= "<input type=\"hidden\" id=\"voti_u$y$i\" name=\"voti_u\" value=\"'$voti_u'\"";}else $riga[$y].= "<input type=\"text\" style=\"text-align:right\" id=\"voti_u$y$i\" name=\"voti_u\" value=\"$voti_u\" $af onfocus=\"select();\"";
		$riga[$y].= " size=\"5\"/></span>";
		if (!($y<$num and $vismf==0 and $voti_u==0)) $af='';
		if ($voti_d > $femmine) {$riga[$y].= "<span class=\"td\" align=\"center\" bgcolor=\"red\">"; $errvot1=1;}
		else $riga[$y].= "<span class=\"td\" align=\"center\">";
		if ($y<$num and $vismf==0 and $voti_d==0) { $riga[$y].= "<input type=\"hidden\" id=\"voti_d$y$i\" name=\"voti_d\" value=\"'$voti_d'\"";}else $riga[$y].= "<input type=\"text\" style=\"text-align:right\" id=\"voti_d$y$i\" name=\"voti_d\" value=\"$voti_d\" onfocus=\"select();\"";
		$riga[$y].= "  size=\"5\"/></span>";
		if ($voti_t > ($maschi+$femmine)) {$riga[$y].= "<span class=\"td\" align=\"center\" bgcolor=\"red\" ><input type=\"text\" id=\"voti_t$y$i\" name=\"voti_t\" value=\"$voti_t\" size=\"5\" style=\"text-align:right\" onfocus=\"select();\"/>"; $errvot1=1;}
		else $riga[$y].= "<span class=\"td\" align=\"center\"><input id=\"voti_t$y$i\" $af name=\"voti_t\" value=\"$voti_t\" size=\"5\" style=\"text-align:right\" onfocus=\"select(); \"/>";
		$riga[$y].= "<input type=\"hidden\" name=\"id_parz\" value=\"$id_parz\"/>"
		."<input type=\"hidden\" name=\"data\" value=\"$giorno\"/>"
		."<input type=\"hidden\" name=\"orario\" value=\"$ora\"/>"
		."<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"/>"
		."<input type=\"hidden\" name=\"id_gruppo\" value=\"$id_gruppo\"/>";
		$riga[$y].= "</span>";
		if (chisei($id_cons_gen)>=64 and $y==$numril) { $riga[$y].= "<span class=\"td\" style=\"text-align: right;\" rowspan=\"1\" colspan=\"6\">"._DELETE." <input type=\"checkbox\" name=\"delete\" value=\"true\"/></span>";}
		else $riga[$y].= "<span class=\"td\" style=\"text-align: right;\" rowspan=\"1\" colspan=\"6\"><input type=\"hidden\" name=\"delete\" value=\"\"/></span>";
		$riga[$y].= "<span class=\"td\" style=\"text-align: left;\" rowspan=\"1\" colspan=\"6\"><input type=\"submit\" name=\"update\" value=\""._OK."\"/> $spuntacopia</span>";
		$riga[$y].= "</form>";
		if($numril>$uscita) $uscita=$numril; 
	}
#		echo "</div>";
		$sql="select count(voti_complessivi) from ".$prefix."_ele_voti_parziale where data='$giorno' and orario='$ora' and id_sez=$id_sez";
		$compl = $dbi->prepare("$sql");
		$compl->execute();
		list ($complessivi)= $compl->fetch(PDO::FETCH_NUM);
	}
#	echo "Errori:";
	if(isset($errvot1) and $errvot1) echo "<table><tr><td align=\"center\" bgcolor=\"red\"> <h1 style=\"color:white;\">I votanti inseriti superano il numero di elettori</h1></td></tr></table></br>";
	if(isset($error) and $error) echo "<table><tr><td align=\"center\" bgcolor=\"red\"> <h1 style=\"color:white;\">Numero di votanti inferiore della rilevazione precedente</h1></td></tr></table></br>";
	echo "<table border=\"1\"><tr><td><b>Iscritti nella sezione</b> Maschi: $maschi - Femmine: $femmine - Totale: ".($maschi+$femmine)."</td></tr></table></br>"; 
	foreach($riga as $key=>$val){
		echo $rigat[$key];
		echo $riga[$key];
		if($key>$uscita) { break;}
	}
	echo "</div></td></tr></table>";
}


//////////////////////////////////////////////////////////////////////
// da qui va la sezione per le preferenze candidati consiglieri
//////////////////////////////////////////////////////////////////////
function preferenze($id_cons,$do,$id_circ,$id_sede,$id_sez,$ops){
 	global $aid, $prefix, $dbi, $id_lista,$genere,$id_cons_gen,$id_gruppo,$sezi,$circo,$votog,$votol,$votoc,$conscirc,$op;
global $tipo_cons,$limite,$dettnulli,$disgiunto,$votoc;
$bgcolor1="#7777ff";
$bgcolor2=$_SESSION['bgcolor2'];
echo "<SCRIPT type=\"text/javascript\">\n";
echo "function vai_lista(idrif){\n";
echo "var element=document.getElementById('pag')\n";
echo "var elista=document.getElementById(idrif)\n";
echo "var url=element.value+elista.value\n";
echo "window.document.location.href=url \n";
echo "}\n";
echo "</script>\n"; 
if ($genere==4){
	$sql="SELECT voti_uomini,voti_donne, voti_complessivi FROM ".$prefix."_ele_voti_parziale where id_sez='$id_sez' and id_cons='$id_cons' order by data desc,orario desc limit 0,1";
	$result = $dbi->prepare("$sql");
	$result->execute();


	list( $voti_u, $voti_d, $voti_t) = $result->fetch(PDO::FETCH_NUM);
	echo "<table  class=\"table-menu\" style=\"width: 50%; color: black;\">"
	."<tr><td></td><td align=\"center\"></td><td bgcolor=\"$bgcolor1\" align=\"center\">"._VOTIU."</td><td bgcolor=\"$bgcolor1\" align=\"center\">"._VOTID."</td><td bgcolor=\"$bgcolor1\" align=\"center\">"._VOTIT."</td></tr>"
	."<tr><td></td><td bgcolor=\"$bgcolor1\" align=\"center\">"._TOT_ULT."</td><td bgcolor=\"$bgcolor2\" align=\"center\">$voti_u</td><td align=\"center\" bgcolor=\"$bgcolor2\">$voti_d</td><td bgcolor=\"$bgcolor2\" align=\"center\">$voti_t</td></tr>";
	echo "</table>";
}
$sql="select validi,nulli,bianchi,contestati from ".$prefix."_ele_sezioni where id_sez='$id_sez' ";
$result = $dbi->prepare("$sql");
$result->execute();
list($v1,$v2,$v3,$v4)=$result->fetch(PDO::FETCH_NUM);
if($v1+$v2+$v3+$v4>0){
    //controlla se sono stati inseriti i votanti
$sql="SELECT preferenze, id_fascia, id_conf,solo_gruppo, disgiunto from ".$prefix."_ele_cons_comune where id_cons='$id_cons'";
$result = $dbi->prepare("$sql");
$result->execute();
 

list($numprefs,$fascia,$id_conf,$flagsg,$disgiunto)=$result->fetch(PDO::FETCH_NUM);
$dettnulli=$flagsg;
####### 06-11-2014 - circoscrizionali - nello spostamento ad altra sezione resta sullo stesso numero di lista anche se sono sezioni di diverse circoscrizioni
if ($circo and $id_lista){
	$sql="SELECT id_circ from ".$prefix."_ele_lista where id_lista=$id_lista";
	$result = $dbi->prepare("$sql");
	$result->execute();
	list($tmp_circ)=$result->fetch(PDO::FETCH_NUM);
	if($tmp_circ==$id_circ){
		$sql="SELECT num_lista from ".$prefix."_ele_lista where id_lista=$id_lista";
		$res_lis = $dbi->prepare("$sql");
		$res_lis->execute();
		list($num_lista)=$res_lis->fetch(PDO::FETCH_NUM);
	} else $id_lista=0;
}

if(!$votog and ($genere==3 or $genere==5)){
	$sql="select validi_lista,nulli,bianchi,contestati_lista,voti_nulli_lista from ".$prefix."_ele_sezioni where id_cons='$id_cons' and id_sez='$id_sez' ";
$result = $dbi->prepare("$sql");
$result->execute();


}else{
	$sql="select validi,nulli,bianchi,contestati,voti_nulli from ".$prefix."_ele_sezioni where id_cons='$id_cons' and id_sez='$id_sez' ";
$result = $dbi->prepare("$sql");
$result->execute();
}

    list($validi,$nulli,$bianchi,$contestati,$votinulli) = $result->fetch(PDO::FETCH_NUM);
		echo "<table class=\"table-menu\" style=\"width: 100%;\"><tr>";
		$sql="SELECT id_lista, descrizione,num_lista from ".$prefix."_ele_lista where id_cons=$id_cons $circo order by num_lista";
$res_lis = $dbi->prepare("$sql");
$res_lis->execute();


		$num_liste = $res_lis->rowCount();
		$ele_lista='';
		if (($genere==4 or $genere==5) and !$votoc) { //liste a piu' candidati
			if(($genere==5 and $id_conf and $fascia<=$limite and !$disgiunto) and !$id_lista) { //esclude il voto di lista per le comunali nei comuni sotto fascia limite
				$sql="SELECT id_lista from ".$prefix."_ele_lista where id_cons=$id_cons $circo order by num_lista limit 0,1";
$result = $dbi->prepare("$sql");
$result->execute();


				list($id_lista)=$result->fetch(PDO::FETCH_NUM);
			}
			echo "<td colspan=\"2\">";
			echo "<form name=\"liste\" data-ajax=\"false\" action=\"admin.php\">";  # data-ajax=\"false\" 
			echo "<input type=\"hidden\" id=\"pag\" name=\"pag\" value=\"admin.php?id_cons_gen=$id_cons_gen&amp;op=$op&amp;id_sez=$id_sez&amp;id_circ=$id_circ&amp;id_sede=$id_sede&amp;do=spoglio&amp;ops=3&amp;id_lista=\">";
			echo "<select id=\"id_lista\" name=\"id_lista\" onChange=\"vai_lista('id_lista');\">";
			if ($id_lista){    #TEST spostare la valorizzazione di $ele_lista 
				echo "<option value=\"0\">"._VOTI_LISTA;
				$ele_lista=" and t1.id_lista='$id_lista' ";
			}else{
				echo "<option value=\"0\" selected>"._VOTI_LISTA;
				$ele_lista=" group by t1.id_lista ";
				if(!isset($votolista)) $votolista=0;
			}
			$preflis[]=array();
			while(list($id_rif,$descrizione,$num_lis) = $res_lis->fetch(PDO::FETCH_NUM)) {
				$segna='';
				$sql="SELECT t1.voti from ".$prefix."_ele_voti_candidati as t1 left join ".$prefix."_ele_candidati as t2 on (t1.id_cand=t2.id_cand) where t1.id_sez=$id_sez and t2.id_lista=$id_rif limit 0,1";
				$result = $dbi->prepare("$sql");
				$result->execute();
				if($result->rowCount()) $ctrvoticand=1; else $ctrvoticand=0;
				$sql="SELECT sum(t1.voti),0,0 from ".$prefix."_ele_voti_candidati as t1 left join ".$prefix."_ele_candidati as t2 on (t1.id_cand=t2.id_cand) where t1.id_sez=$id_sez and t2.id_lista=$id_rif";
				$result = $dbi->prepare("$sql");
				$result->execute();
				list($votisezcand)=$result->fetch(PDO::FETCH_NUM);
				if((($fascia>$limite or $disgiunto) or !$id_conf) or $genere==4){
					$sql="SELECT voti,solo_lista from ".$prefix."_ele_voti_lista where id_sez='$id_sez' and id_lista='$id_rif'";
					$result = $dbi->prepare("$sql");
					$result->execute();
				}else{
					$sql="SELECT id_gruppo from ".$prefix."_ele_lista where id_lista='$id_rif'";
					$result = $dbi->prepare("$sql");
					$result->execute();
					list($id_gruppo)=$result->fetch(PDO::FETCH_NUM);
					$sql="SELECT sum(voti),0 from ".$prefix."_ele_voti_gruppo where id_sez='$id_sez' and id_gruppo='$id_gruppo'";
					$result = $dbi->prepare("$sql");
					$result->execute();
				}
				list($votisezlis,$sgpl)=$result->fetch(PDO::FETCH_NUM);
				$preflis[$id_rif]=$votisezlis;
				if($votisezcand>($votisezlis*$numprefs))
					{$segna="style=\"background-color: #dd0000;\""; }#$segna2=$segna;
				elseif($ctrvoticand)
					$segna="style=\"background-color: #99CC33;\"";
				$sql="SELECT t1.voti,t2.num_cand FROM ".$prefix."_ele_voti_candidati as t1 left join ".$prefix."_ele_candidati as t2 on t1.id_cand=t2.id_cand where t1.id_cons='$id_cons' and  t1.id_sez='$id_sez' and t2.id_lista='$id_rif'";
				$res4 = $dbi->prepare("$sql");
				$res4->execute();
				$errcand[$id_rif]=array();
				while(list($testvotic,$numcand)=$res4->fetch(PDO::FETCH_NUM))
					{if($votisezlis<$testvotic) { $segna="style=\"background-color: #dd0000;\"";$errcand[$id_rif][]=$numcand;}}
				$sel = ($id_rif == $id_lista) ? "selected" : "";
				echo "<option $segna value=\"$id_rif\" $sel>";
				for ($j=strlen($num_lis);$j<2;$j++) { echo "&nbsp;&nbsp;";}
				echo $num_lis.") ".$descrizione;
#				echo $num_lis.") ".substr($descrizione,0,30);
			}
			echo "</select></form></td></tr><tr><td style=\"vertical-align: top;\">&nbsp;</td>\n";
		}else {
			$id_lista=0;
		}
		echo "<td style=\"vertical-align: top;\">";
		if ((!$id_lista)){$tab="_ele_voti_lista";} else {$tab="_ele_voti_candidati";}
		if(($genere==4 or ($genere==5 and $votog)) and !$id_lista)	{
			$sql="SELECT sum(t1.voti),t2.validi, t2.solo_gruppo,t2.contestati_lista,t2.voti_nulli,t2.bianchi,t2.nulli,t2.contestati,0,0 from ".$prefix."_ele_sezioni as t2 left join ".$prefix.$tab." as t1 on (t1.id_sez=t2.id_sez) where t2.id_sez=$id_sez group by t1.id_sez,t2.validi, t2.solo_gruppo,t2.contestati_lista,t2.voti_nulli,t2.bianchi,t2.nulli,t2.contestati";
			$result = $dbi->prepare("$sql");
			$result->execute();
		}else{
			$sql="SELECT sum(t1.voti),t2.validi_lista, t2.solo_gruppo,t2.contestati_lista,t2.voti_nulli_lista,t2.bianchi,t2.nulli,t2.contestati,t2.voti_nulli,t2.solo_gruppo,t1.id_sez from ".$prefix."_ele_sezioni as t2 left join ".$prefix.$tab." as t1 on (t1.id_sez=t2.id_sez) where t2.id_sez='$id_sez' group by t1.id_sez,t2.validi_lista, t2.solo_gruppo,t2.contestati_lista,t2.voti_nulli_lista,t2.bianchi,t2.nulli,t2.contestati,t2.voti_nulli,t2.solo_gruppo";
			$result = $dbi->prepare("$sql");
			$result->execute();
		}

// aggiunte le variabili $sgpl e $vnulli2 per la gestione dei voti 1) al solo presidente per singola lista - 2) al solo presidente per singola lista perché nullo o contestato quello di lista		
		$isscr= $result->rowCount();
		list( $voti_sez, $validi2, $sg,$cont2,$vnulli2,$bia2,$nul2,$con2,$vnul2,$sgpl) = $result->fetch(PDO::FETCH_NUM);
		if(($genere==5 and !$disgiunto and $voti_sez and $flagsg and ($tipo_cons==18 or $tipo_cons==19))){ 
			$sql="SELECT sum(solo_gruppo) from ".$prefix."_ele_voti_gruppo where id_cons=$id_cons and id_sez=$id_sez";
			$resg = $dbi->prepare("$sql");
			$resg->execute();
			list($sgpl)=$resg->fetch(PDO::FETCH_NUM);
			if ($sg != $sgpl) echo "<table class=\"table-menu\" style=\"width: 50%;\"><tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br><b> "._ATT_UNI." ".$sgpl." "._ATT_TOT_UNI." ".$sg."</b><br></td></table>";
		}
		$valista=$validi2;
#######    controlli
		$sql="select * from ".$prefix."_ele_controlli where id_cons='$id_cons' and id_sez='$id_sez' ";
		$resc = $dbi->prepare("$sql");
		$resc->execute();
		if($resc->rowCount()){	
			if(!$id_lista or $genere==3){	//controllo di congruenza
				$sql="SELECT max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_cons='$id_cons' and id_sez='$id_sez'";
				$res2 = $dbi->prepare("$sql");
				$res2->execute();
				list($tot) = $res2->fetch(PDO::FETCH_NUM);
				if ($validi2+$vnulli2+$cont2+$sg+$bia2+$nul2+$con2+$vnul2!=$tot and $validi+$sg>0){
					echo "<table class=\"table-menu\" style=\"width: 50%;\"><tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br><b> "._ATT_VOTANTI." ".$tot." "._NO_TOT_VOTI." ".($validi2+$vnulli2+$cont2+$sg+$bia2+$nul2+$con2+$vnul2)."</b><br></td></table>";
				}
				if((($voti_sez)!=$validi2) and ($voti_sez>0)){
					echo "<table class=\"table-menu\" style=\"width: 50%;\"><tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br><b> "._ATT_VOTI." ".($voti_sez)." "._NO_VAL_VOTI." ".$validi2."</b><br></td></tr></table>";
				}
			}
			if($genere==5 or $genere==4){
				if($id_lista){
				$sql="SELECT max(t1.voti),sum(t1.voti) from ".$prefix."_ele_voti_candidati as t1 left join ".$prefix."_ele_candidati as t2 on (t1.id_cand=t2.id_cand) where t1.id_sez=$id_sez and t2.id_lista='$id_lista'";
				$result = $dbi->prepare("$sql");
				$result->execute();
				list($votimaxcand,$votisezcand)=$result->fetch(PDO::FETCH_NUM);
				if(($fascia>$limite or $disgiunto) or !$id_conf){
					$sql="SELECT sum(t2.voti) from ".$prefix."_ele_voti_lista as t2 where t2.id_sez='$id_sez' and t2.id_lista='$id_lista'"; 
					$result = $dbi->prepare("$sql");
					$result->execute();
				}else{
					$sql="SELECT id_gruppo from ".$prefix."_ele_lista where id_lista='$id_lista'";
					$result = $dbi->prepare("$sql");
					$result->execute();
					list($id_gruppo)=$result->fetch(PDO::FETCH_NUM);
					$sql="SELECT sum(voti) from ".$prefix."_ele_voti_gruppo where id_sez='$id_sez' and id_gruppo='$id_gruppo'";
					$result = $dbi->prepare("$sql");
					$result->execute();
				}
				list($votisezlis)=$result->fetch(PDO::FETCH_NUM);
				$errpres=0; 
				if($votisezcand>($votisezlis*$numprefs))
				{
					echo "<table class=\"table-menu\" style=\"width: 50%;\"><tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br/><b> ATTENZIONE!<BR/>I voti di preferenza $votisezcand <br/>superano quelli ammessi ".$votisezlis*$numprefs."</b><br></td></tr></table>";
					$segna2="style=\"background-color: #dd0000;\"";
					$errpres=1;
				}
				if($votimaxcand>$votisezlis and !$errpres)
				{
					echo "<table class=\"table-menu\" style=\"width: 50%;\"><tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br/><b> ATTENZIONE!<BR/>I voti di preferenza di un candidato $votimaxcand <br/>superano i voti di lista $votisezlis</b><br></td></tr></table>";
					$segna2="style=\"background-color: #dd0000;\"";
				}

			}#else{
					$sql="select solo_gruppo from ".$prefix."_ele_sezioni where id_sez='$id_sez' ";
					$resgs = $dbi->prepare("$sql");
					$resgs->execute();
					list($sgs3)=$resgs->fetch(PDO::FETCH_NUM);
					$sql="select sum(solo_gruppo) from ".$prefix."_ele_voti_gruppo where id_sez='$id_sez' ";
					$resgs = $dbi->prepare("$sql");
					$resgs->execute();
					list($vsg3)=$resgs->fetch(PDO::FETCH_NUM);
					if($sgs3!=$vsg3 and $flagsg)
						 echo "<table class=\"table-menu\" style=\"width: 50%;\"><tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br/><b> ATTENZIONE!<BR/>I voti al solo gruppo: $sgs3 non corrispondono a quelli inseriti nella scheda dei gruppi $vsg3 </b><br></td></tr></table>";
	
		}
#die("TEST: if($sgs3!=$vsg3)");
#controllo voti a liste collegate <= voto di gruppo per voto non disgiunto
	$errgrulis=array();
	if($genere==5 and !$disgiunto  and ($tipo_cons==18 or $tipo_cons==19)){
		$sql="SELECT id_gruppo FROM ".$prefix."_ele_gruppo where id_cons='$id_cons' $circo";
		$resref = $dbi->prepare("$sql");
		$resref->execute();
#	die("qui: $sql");
		$sql="SELECT count(0) FROM ".$prefix."_ele_voti_lista where id_sez='$id_sez'";
		$res4 = $dbi->prepare("$sql");
		$res4->execute();
		list($sezscrl)=$res4->fetch(PDO::FETCH_NUM); 
		if ($sezscrl)
			while ( list($id_gruppo)=$resref->fetch(PDO::FETCH_NUM))
				{
				$sql="SELECT sum(voti),sum(solo_gruppo) FROM ".$prefix."_ele_voti_gruppo where id_sez='$id_sez' and id_gruppo='$id_gruppo'";
				$res3 = $dbi->prepare("$sql");
				$res3->execute();
				$sql="SELECT sum(voti+nulli_lista) FROM ".$prefix."_ele_voti_lista where id_sez='$id_sez' and  id_lista in (select id_lista from ".$prefix."_ele_lista where id_gruppo='$id_gruppo')";
				$res4 = $dbi->prepare("$sql");
				$res4->execute();
				list($vgruppo3,$vsl3)=$res3->fetch(PDO::FETCH_NUM);
				$vgruppo=$vgruppo3-$vsl3;
				list($vliste)=$res4->fetch(PDO::FETCH_NUM);
				if($vliste>$vgruppo)
					{
					$sql="SELECT descrizione FROM ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
					$result = $dbi->prepare("$sql");
					$result->execute();
					list($dgruppo)=$result->fetch(PDO::FETCH_NUM);
					echo "<table class=\"table-menu\" style=\"width: 50%;\"><tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br/><b> ATTENZIONE!<BR/>I voti delle liste collegate<br/> superano i voti assegnati al gruppo $dgruppo </b><br></td></tr></table>";
					$errgrulis[$id_gruppo]=1;
				}
				elseif($vliste<$vgruppo)
					{
					$sql="SELECT descrizione FROM ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
					$result = $dbi->prepare("$sql");
					$result->execute();
					list($dgruppo)=$result->fetch(PDO::FETCH_NUM);
					echo "<table class=\"table-menu\" style=\"width: 50%;\"><tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br/><b> ATTENZIONE!<BR/>I voti assegnati al gruppo $dgruppo<br/> superano i voti delle liste collegate </b><br></td></tr></table>";
					$errgrulis[$id_gruppo]=1;
				}
			}
		}
}


		echo "\n<form name=\"sezioni\" data-ajax=\"false\" action=\"modules/Elezioni/salva_liste.php\">"
		."<input type=\"hidden\" name=\"op\" value=\"rec_voti\">"
		."<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\">"
		."<input type=\"hidden\" name=\"id_cons\" value=\"$id_cons\">"
		."<input type=\"hidden\" name=\"genere\" value=\"$genere\">"
		."<input type=\"hidden\" name=\"id_sez\" value=\"$id_sez\">"
		."<input type=\"hidden\" name=\"id_circ\" value=\"$id_circ\">"
		."<input type=\"hidden\" name=\"id_sede\" value=\"$id_sede\">"
		."<input type=\"hidden\" name=\"tabella\" value=\"$tab\">"
		."<input type=\"hidden\" name=\"id_lista\" value=\"$id_lista\">\n"
		."<input type=\"hidden\" name=\"do\" id=\"do\" value=\"0\">\n";
		echo "<table class=\"table-menu\" style=\"width: 90%; color: black\">";
		echo "<tr><td bgcolor=\"$bgcolor1\" align=\"left\" width=\"32\">";
		if ($genere<4){
//			echo "<td bgcolor=\"$bgcolor1\" align=\"center\" width=\"32\"><b>"._LISTA."</b></td>";
		}
		echo "<b>"._NUM."</b></td>";
		if($id_lista){
			echo "<td bgcolor=\"$bgcolor1\" align=\"left\"><b>"._CANDIDATO."</b></td>"
			."<td bgcolor=\"$bgcolor1\" align=\"center\"><b>"._VOTI."</b> (alla lista ".$preflis[$id_lista].")</td>";
		}else{
			echo "<td bgcolor=\"$bgcolor1\" align=\"left\"><b>"._DESCR."</b></td>"
			."<td bgcolor=\"$bgcolor1\" align=\"center\"><b>"._VOTI."</b></td>";
#funzione di inserimento del dettaglio di voti nulli sospesa, non ritenuta utile
#			if($dettnulli && !$disgiunto  && $tipo_cons!=18 && $tipo_cons!=19)
#			echo "<td bgcolor=\"$bgcolor1\" align=\"center\"><b>"._NULLI_LISTE."</b></td>";
			if($disgiunto){
			echo "<td bgcolor=\"$bgcolor1\" align=\"center\"><b>"._SOLOLIS."</b></td>";
			}
		}
		echo "</tr>\n";


if($id_lista)		$sql="select t1.* from ".$prefix."_ele_candidati as t1 left join ".$prefix."_ele_lista as t2 on (t1.id_lista=t2.id_lista) WHERE t1.id_cons=$id_cons and t1.id_cons=t2.id_cons $ele_lista ORDER BY t2.num_lista,t1.num_cand";
else $sql="select 0,'$id_cons',t1.id_lista,'','','','','',0 from ".$prefix."_ele_candidati as t1 left join ".$prefix."_ele_lista as t2 on (t1.id_lista=t2.id_lista) WHERE t1.id_cons=$id_cons and t1.id_cons=t2.id_cons $circo $ele_lista ORDER BY t2.num_lista";
$result = $dbi->prepare("$sql");
$result->execute();


		$max = $result->rowCount();
		$tot_pref=0;
		$i=1;

		if($id_lista) {
			while(list($id_cand,$id_cons2,$id_lista2,$nl, $cognome, $nome, $note, $simbolo, $num_cand) = $result->fetch(PDO::FETCH_NUM)){
				// dati lista
				$sql="select id_lista, descrizione,simbolo,num_lista from ".$prefix."_ele_lista where id_lista='$id_lista2'";
				$result1 = $dbi->prepare("$sql");
				$result1->execute();
				list($id_lista3,$descr_lista,$simb_lista,$num_lista)=$result1->fetch(PDO::FETCH_NUM);
			// dati gruppo
				$sql="select descrizione,simbolo from ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
				$result2 = $dbi->prepare("$sql");
				$result2->execute();
				list($descr_gruppo,$simb_gruppo)=$result2->fetch(PDO::FETCH_NUM);
								echo "<tr bgcolor=\"$bgcolor2\">";
				if ($genere<4){
					echo "<td align=\"left\"><b><img src=\"images/lista/$simb_lista \" width=\"32\" heigth=\"32\" align=\"center\" ALT=\"$descr_lista\" > </b></td>";
				}
				echo "<td align=\"center\"><b> $num_cand </b></td>"
				."<td align=\"left\"><b>$cognome $nome</b></td>";
				$cond_sele="and id_cand=$id_cand";
				$sql="SELECT voti FROM ".$prefix."$tab where id_cons='$id_cons' and id_sez='$id_sez' $cond_sele";
				$res = $dbi->prepare("$sql");
				$res->execute();
				list($voti)= $res->fetch(PDO::FETCH_NUM); #if(!$voti) $voti2=''; else $voti2=$voti;
				if(in_array($num_cand,$errcand[$id_lista2])) $errcolor="style=\"background-color: rgb(255, 0, 0);\""; else $errcolor='';
				echo "<td align=\"right\" $errcolor><input name=\"voti$num_cand\" value=\"".$voti."\" size=\"7\"  style=\"text-align:right\" onfocus=\"select();\">";
				echo "<input type=\"hidden\" name=\"id_cand$num_cand\" value=\"$id_cand\"></td></tr>\n";
				$i++;
				$tot_pref+=$voti;
			} 
		}else {
			if($circo) $circot1=" and t1.id_circ=$id_circ"; else $circot1='';
			$sql="select t2.voti,t1.id_lista, descrizione,simbolo,t1.num_lista,t2.nulli_lista,t2.solo_lista,t1.id_gruppo 
			from ".$prefix."_ele_lista as t1, ".$prefix."_ele_voti_lista as t2
			where t1.id_cons='$id_cons' 
			and t1.id_lista=t2.id_lista
			and t2.id_sez=$id_sez
			$circot1
			order by t1.num_lista";

			try {
				$result1 = $dbi->prepare("$sql");
				$result1->execute();
			}
			catch(PDOException $e)
			{
				echo $sql . "<br>" . $e->getMessage();
			}                  
			$scruliste=$result1->rowCount();
			if (!$scruliste){
				$sql="select null,id_lista, descrizione,simbolo,num_lista,'','',id_gruppo
				from ".$prefix."_ele_lista where id_cons='$id_cons' $circo
				order by num_lista";
				$result1 = $dbi->prepare("$sql");
				$result1->execute();
			}
			$tvnpl=0;
			$tslpl=0;
#			$segna2='';
			$sezscru=0;
			while (list($voti,$id_lista3,$descr_lista,$simb_lista,$num_lista,$vnpl,$slpl,$rifgruppo)=$result1->fetch(PDO::FETCH_NUM)){
				echo "<tr bgcolor=\"$bgcolor2\">";
//				if ($genere<4 or $votoc){
//				}
				$sql="select max(t1.voti),sum(t1.voti), count(t1.voti) from ".$prefix."_ele_voti_candidati as t1, ".$prefix."_ele_candidati as t2 where t1.id_cand=t2.id_cand and t1.id_sez='$id_sez' and t2.id_lista='$id_lista3'";
				$rese = $dbi->prepare("$sql");
				$rese->execute();
				list($maxvotic,$sumvotic,$numrec)=$rese->fetch(PDO::FETCH_NUM);#die("TEST: $numrec");
				if($numrec>0 or ($votoc and $scruliste)) {$sezscru=1; $statuscol="#99CC33;";} else $statuscol='';
				if($maxvotic>$voti or $sumvotic>$voti*$numprefs) $statuscol='#dd0000;'; #$segna="style=\"background-color: #dd0000;\"";
				$sql="select * from ".$prefix."_ele_controlli where tipo='lista' and id_sez='$id_sez' and id='$id_lista3'";
				$rese = $dbi->prepare("$sql");
				$rese->execute();
				if($rese->rowCount()) $statuscol='#dd0000;'; #$segna="style=\"background-color: #dd0000;\"";
				list($sezcol)=$rese->fetch(PDO::FETCH_NUM);
				$segna="style=\"background-color: $statuscol\"";
 				if ($statuscol) $errcolor=$segna; else $errcolor='';
				if ($statuscol=='#dd0000;')
					$bordcolor="border-color:red;"; 
				else $bordcolor='';
				if(isset($errgrulis[$rifgruppo])) {
					$errcolor="style=\"background-color: rgb(255, 0, 0);\""; 
					$bordcolor="border-color:red;";
				}elseif(!$errcolor && $numrec) $errcolor="style=\"background-color: #99CC33;\"";
				if($genere>=4 and !$votoc) $stilcur="style=\"cursor: pointer;\""; else $stilcur='';
				echo "<td align=\"center\" $errcolor><b> $num_lista </b></td>"
				."<td align=\"left\" $stilcur onClick=\"vai_lista('id_rif$i');\"><b>$descr_lista</b></td>";
##				$cond_sele="and id_lista=$id_lista3";
				echo "<td align=\"right\"><input  name=\"voti$i\" value=\"".$voti."\" size=\"7\" onfocus=\"select();\" style=\"text-align:right; $bordcolor;\"><input id=\"id_rif$i\" type=\"hidden\" name=\"id_lista$i\" value=\"$id_lista3\">";
#funzione non ritenuta utile
#				if($dettnulli && !$disgiunto  && $tipo_cons!=18 && $tipo_cons!=19) { echo "</td><td align=\"right\"><input  name=\"vnpl$i\" value=\"".$vnpl."\" size=\"7\"  style=\"text-align:right\">"; $tvnpl+=$vnpl;}
				if($disgiunto) {echo "</td><td align=\"right\"><input  name=\"slpl$i\" value=\"".$slpl."\" size=\"7\"  style=\"text-align:right\" onfocus=\"select();\">"; $tslpl+=intval($slpl);}
				echo "</td>";
//				echo "<td align=\"right\">$sgpl";
				echo "</tr>\n";
				$i++;
				$tot_pref+=$voti;
			}
		}
		if(!isset($segna2)) $segna2='';
		if(!$id_lista) {
			if ($tot_pref!=$validi2 and $sezscru) $segna2="style=\"background-color: #dd0000;\"";
			echo "<tr bgcolor=\"$bgcolor1\"><td></td><td><font size=\"3\">"._TOT._VOTI_LISTA."</font></td><td align=\"center\" $segna2><font size=\"3\">$tot_pref</font></td>";
#			if($dettnulli && !$disgiunto  && $tipo_cons!=18 && $tipo_cons!=19)
#				echo "<td bgcolor=\"$bgcolor1\" align=\"center\"><font size=\"3\">".$tvnpl."</font></td>";
			if($disgiunto)
				echo "<td bgcolor=\"$bgcolor1\" align=\"center\"><font size=\"3\">".$tslpl."</font></td>";
			echo "</tr>";
		}
		else echo "<tr bgcolor=\"$bgcolor1\"><td></td><td>"._TOTPREF."</td><td align=\"center\"  $segna2>$tot_pref</td></tr>";
		// toglie ai candidati la visual... del solo_gruppo 
		if(!$votog) {
		   if (($genere==3 OR $genere==5) and (!$id_lista) and (($disgiunto or $fascia>$limite) or !$id_conf)) { //gruppo e liste
			echo "<tr bgcolor=\"$bgcolor2\"><td></td><td><b>"._VALIDI_LISTA."</b></td><td align=\"center\"><input type=\"hidden\" name=\"id_sez\" value=\"$id_sez\"><input name=\"valista\" value=\"$valista\" size=\"5\" style=\"text-align:right;\" onfocus=\"select();\"></td></tr>";
			echo "<tr bgcolor=\"$bgcolor2\"><td></td><td><b>"._SOLO_GRUPPO."</b></td><td align=\"center\"><input name=\"sg\" value=\"$sg\" size=\"5\" style=\"text-align:right;\" onfocus=\"select();\">";
			if(defined('_NULLISTA'))
				echo "<input type=\"hidden\" name=\"votinulli\" value=\"$votinulli\"><input  type=\"hidden\" name=\"contestati\" value=\"$contestati\"></td></tr>";
			else
				echo "</td></tr><tr bgcolor=\"$bgcolor2\"><td></td><td><b>"._NULLI_LISTE."</b></td><td align=\"center\"><input  name=\"votinulli\" value=\"$votinulli\" size=\"5\" style=\"text-align:right;\" onfocus=\"select();\">"
	."</td></tr><tr bgcolor=\"$bgcolor2\"><td></td><td><b>"._CONTESTATI_LISTE."</b></td><td align=\"center\"><input  name=\"contestati\" value=\"$contestati\" size=\"5\" style=\"text-align:right;\" onfocus=\"select();\"></td></tr>";
			
		   }elseif (($genere==3 OR $genere==5) and !$votoc and (($disgiunto or $fascia>$limite) or !$id_conf)){ //}elseif ($tipo_cons!=10 and $tipo_cons!=11){
			echo "<tr bgcolor=\"$bgcolor1\"><td></td><td><b>"._SOLO_GRUPPO."</b></td><td align=\"center\">$sg</td></tr>";
		   }
/* visualizzazione tolta perché inappropriata 04/03/2022
######modifica del 16-04-2009 per visualizzare i voti al solo sindaco nei comuni con meno di 15000 abitanti
elseif(($genere==3 OR $genere==5) and ($id_lista) and ($fascia<=$limite or !$id_conf) and $numprefs==1) {
$sql="SELECT id_gruppo FROM ".$prefix."_ele_lista where id_lista='$id_lista'";
$resvg = $dbi->prepare("$sql");
$resvg->execute();


list($id_gruppo) = $resvg->fetch(PDO::FETCH_NUM);
$sql="SELECT sum(voti) FROM ".$prefix."_ele_voti_gruppo where id_gruppo='$id_gruppo' and id_sez='$id_sez'";
$resvg = $dbi->prepare("$sql");
$resvg->execute();


        list($voti_sind) = $resvg->fetch(PDO::FETCH_NUM);

echo "<tr bgcolor=\"$bgcolor1\"><td></td><td><b>"._SOLO_GRUPPO."</b></td><td align=\"center\">".($voti_sind - $tot_pref)."</td></tr>";
} */
###### fine modifica del 16-04-2009

	}
		echo "<tr>";
		echo "<td></td><td></td>";
		echo "<td align=\"center\"><input type=\"submit\" id=\"update\" name=\"update\" value=\""._OK."\"></td>";
		echo "</tr></table>";
		if(chisei($id_cons_gen)>=64){
			echo "<td></td><td> <input type=\"checkbox\" id=\"pwd3\" name=\"pwd3\" value=\"1\">"._DELETE."</td>";
		} # onclick=\"javascript:del_dati()\"
		echo "</form></tr></table>";
		


}
#if (!((!$votog) and ($genere==3 OR $genere==5) and ($fascia>$limite)))
	finale($id_cons,$do,$id_circ,$id_sede,$id_sez,$ops);
 }


function finale($id_cons,$do,$id_circ,$id_sede,$id_sez,$ops){
 global $aid, $prefix, $dbi,$id_cons_gen,$genere,$votog,$fascia,$limite,$scrtest;
////////////////////////////////////////////
// da qua va la sezione per i voti finali
///////////////////////////////////////////
$bgcolor1="#7777ff";
$bgcolor2=$_SESSION['bgcolor2'];
echo "<SCRIPT type=\"text/javascript\">\n\n<!--\n"
."//-->\n";
echo "function setsez() {\n";
echo "document.getElementById('spogliovoti').submit(); }\n";
echo "</script>\n";	

$sql="select * from ".$prefix."_ele_voti_lista where id_cons=$id_cons and id_sez=$id_sez and id_lista=0";
$res = $dbi->prepare("$sql");
$res->execute(); 
$stato=$res->rowCount();
if($stato) $stato='checked'; else $stato='';
echo "<form name=\"spogliovoti\" id=\"spogliovoti\" data-ajax=\"false\" action=\"modules/Elezioni/salva_voti.php\">"
."<input type=\"hidden\" name=\"op\" value=\"rec_finale\">";
echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\">"
."<input type=\"hidden\" name=\"id_cons\" value=\"$id_cons\">"
."<input type=\"hidden\" name=\"id_sez\" value=\"$id_sez\">"
."<input type=\"hidden\" name=\"id_circ\" value=\"$id_circ\">"
."<input type=\"hidden\" name=\"id_sede\" value=\"$id_sede\">";
if($scrtest)
	echo "<div style=\"text-align:right;\">Considerare la sezione come scrutinata? <input type=\"checkbox\" name=\"scrutinata\" id=\"scrutinata\" value=\"true\" $stato onclick=\"setsez()\";> <br></div>";

	echo "<table border=\"0\" width=\"100%\" align=\"center\"><tr bgcolor=\"$bgcolor1\" align=\"center\">";
/*	if($ops==3 and ($genere==3 or ($genere==5 and $fascia>$limite)) )
		echo "<td width=\"32\"><b>"._VOTI_LISTA."</b></td>";
	else */
		echo "<td width=\"15%\"><b>"._VALIDI."</b></td>";
	echo "<td width=\"82\"><b>"._NULLI."</b></td>"
    ."<td><b>"._BIANCHI."</b></td>"
	."<td width=\"82\"><b>"._VOTINULLI."</b></td>"
	."<td><b>"._CONTESTATI."</b></td>"
    ."<td><b>"._TOTNON."</b></td>"
	."<td><b>"._TOTALEVOTI."</b></td>"
	."<td bgcolor=\"#ffffff\"></td> </tr>";
/*if($ops==3 and ($genere==3 or ($genere==5 and $fascia>$limite)) )
    $result = mysql_query("select id_cons,id_sez,validi_lista,nulli,bianchi,contestati_lista,voti_nulli_lista,solo_gruppo,contestati,voti_nulli from ".$prefix."_ele_sezioni where id_cons='$id_cons' and id_sez='$id_sez' ";
$res = $dbi->prepare("$sql");
$res->execute();


else */
    $sql = "select id_cons,id_sez,validi,nulli,bianchi,contestati,voti_nulli,'0','0','0',solo_lista from ".$prefix."_ele_sezioni where id_cons='$id_cons' and id_sez='$id_sez' ";
$result = $dbi->prepare("$sql");
$result->execute();


    list($id_cons2,$id_sez2,$validi, $nulli, $bianchi, $contestati,$votinulli,$sg,$conts,$nullis,$sololista) = $result->fetch(PDO::FETCH_NUM);
    $tot_nulli=$nulli+$bianchi+$contestati+$votinulli;
    $tot_voti=$validi+$tot_nulli+$conts+$nullis;
//	."</td><td><input  name=\"sololista\" value=\"$sololista\" size=\"5\" style=\"text-align:right\">" ---     ."<td><b>"._SOLOLIS."</b></td>"


	echo "<tr bgcolor=\"$bgcolor2\" align=\"center\"><td align=\"left\"><input  name=\"validi\" value=\"$validi\" size=\"7\" style=\"text-align:right;\" onfocus=\"select();\">";
/*	if(($genere==3 or $genere==5) and !$votog and $ops==3){
	echo "</td><td>$nulli"
	."</td><td>$bianchi";
	echo "<input type=\"hidden\" name=\"nulli\" value=\"$nulli\"><input type=\"hidden\" name=\"bianchi\" value=\"$bianchi\">";
	}else{ */
	echo "</td><td><input  name=\"nulli\" value=\"$nulli\" size=\"5\" style=\"text-align:right;\" onfocus=\"select();\">"
	."</td><td><input  name=\"bianchi\" value=\"$bianchi\" size=\"5\" style=\"text-align:right;\" onfocus=\"select();\">";
//	}
	echo "</td><td><input  name=\"votinulli\" value=\"$votinulli\" style=\"text-align:right;\" onfocus=\"select();\">"
	."</td><td><input  name=\"contestati\" value=\"$contestati\" size=\"5\" style=\"text-align:right\" onfocus=\"select();\">"
	."</td><td>$tot_nulli"
	."</td><td>$tot_voti</td><td>"
    	."<input type=\"hidden\" name=\"genere\" value=\"$genere\">"
    	."<input type=\"hidden\" name=\"ops\" value=\"$ops\">"
       	."<input type=\"submit\" name=\"update\" value=\""._OK."\">"
      	."</td></tr></table></form>"; //</td></tr>";


//    echo "</table>";
/*	echo "<SCRIPT type=\"text/javascript\">\n\n<!--\n";
	if (!$validi) {
		echo "document.spogliovoti.validi.focus()\n";
		echo "document.spogliovoti.validi.select()\n";
	}
	echo "//-->\n"
	."</script>\n"; */


    }


function preferenze_gruppi($id_cons,$do,$id_circ,$id_sede,$id_sez,$ops){
 global $aid, $prefix, $dbi, $tipo_cons, $genere,$id_cons_gen,$sezi,$circo,$dettnulli,$scrtest;
////////////////////////////////////////////
// da qua va la sezione per le preferenze ai gruppi
///////////////////////////////////////////
// Controllo immmissioni

$bgcolor1="#7777ff";
$bgcolor2=$_SESSION['bgcolor2'];
$sql="SELECT * FROM ".$prefix."_ele_gruppo where id_cons='$id_cons' $circo ";
$res = $dbi->prepare("$sql");
$res->execute();


    $max = $res->rowCount();
    $max = $max-1;
/*	 echo "<SCRIPT type=\"text/javascript\">\n\n<!--\n";
	if ($genere==0) {
		echo "document.sezioni.si1.focus()\n";
		echo "document.sezioni.si1.select()\n";
	} else {
		echo "document.sezioni.voti1.focus()\n"
		."document.sezioni.voti1.select()\n";
	}
	echo "//-->\n"
	."</script>\n"; */
// tabella votanti
	echo "<center>";
    if ($genere!=0){
	$sql="SELECT voti_uomini,voti_donne, voti_complessivi FROM ".$prefix."_ele_voti_parziale where id_sez='$id_sez' and id_cons='$id_cons' order by data desc,orario desc limit 0,1";
$result = $dbi->prepare("$sql");
$result->execute();


    list( $voti_u, $voti_d, $voti_t) = $result->fetch(PDO::FETCH_NUM);
   	echo "<table  class=\"table-menu\" style=\"width: 50%; color: black;\">"
	."<tr><td></td><td align=\"center\"></td><td bgcolor=\"$bgcolor1\" align=\"center\">"._VOTIU."</td><td bgcolor=\"$bgcolor1\" align=\"center\">"._VOTID."</td><td bgcolor=\"$bgcolor1\" align=\"center\">"._VOTIT."</td></tr>"
	."<tr><td></td><td bgcolor=\"$bgcolor1\" align=\"center\">"._TOT_ULT."</td><td bgcolor=\"$bgcolor2\" align=\"center\">$voti_u</td><td align=\"center\" bgcolor=\"$bgcolor2\">$voti_d</td><td bgcolor=\"$bgcolor2\" align=\"center\">$voti_t</td></tr>";
   	echo "</table>";
    }
	echo "<table  class=\"table-menu\" style=\"width: 50%; color: black\">";
    if ($genere==0){
    	$sql="SELECT id_gruppo,si+no,validi,nulli,bianchi,contestati FROM ".$prefix."_ele_voti_ref where id_cons='$id_cons' and id_sez='$id_sez'  ";
		$res = $dbi->prepare("$sql");
		$res->execute();
		while (list($id_gruppo,$voti_parz,$validi,$nulli,$bianchi,$contestati) = $res->fetch(PDO::FETCH_NUM)){
			if ($voti_parz!=$validi){
				$sql="SELECT num_gruppo FROM ".$prefix."_ele_gruppo where id_cons='$id_cons' and id_gruppo='$id_gruppo'  ";
				$res2 = $dbi->prepare("$sql");
				$res2->execute();
				list($num_gruppo) = $res2->fetch(PDO::FETCH_NUM);
				echo "<tr><td style=\"background-color: rgb(255, 0, 0); text-align:center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br><b> "._ATT_VOTI_REF." $num_gruppo: ".$voti_parz." "._NO_VAL_VOTI.": ".$validi."</b><br></td></tr>";
			}
			$sql="SELECT max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_cons='$id_cons' and id_sez='$id_sez' and id_gruppo='$id_gruppo' ";
			$res2 = $dbi->prepare("$sql");
			$res2->execute();
			list($tot) = $res2->fetch(PDO::FETCH_NUM);
			if (($validi+$nulli+$bianchi+$contestati)!= $tot ){
				$sql="SELECT num_gruppo FROM ".$prefix."_ele_gruppo where id_cons='$id_cons' and id_gruppo='$id_gruppo'";
				$res2 = $dbi->prepare("$sql");
				$res2->execute();
				list($num_gruppo) = $res2->fetch(PDO::FETCH_NUM);
				echo "<tr><td style=\"background-color: rgb(255, 0, 0); text-align:center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br><b> "._ATT_VOTANTI_REF." $num_gruppo: ".$tot." "._NO_SOMMA." ".($validi+$nulli+$bianchi+$contestati)."</b><br></td></tr>";
			}
		}
    }else{
		$sql="select solo_gruppo,disgiunto from ".$prefix."_ele_cons_comune where id_cons='$id_cons' ";
		$result = $dbi->prepare("$sql");
		$result->execute();
		list($flagsg,$disgiunto)=$result->fetch(PDO::FETCH_NUM);
		$dettnulli=$flagsg;
		$sql="SELECT sum(voti) FROM ".$prefix."_ele_voti_gruppo where id_cons='$id_cons' and id_sez='$id_sez'";
		$res = $dbi->prepare("$sql");
		$res->execute();
		list($voti_parz) = $res->fetch(PDO::FETCH_NUM);
		$sql="SELECT validi,nulli,bianchi,contestati,solo_lista,voti_nulli FROM ".$prefix."_ele_sezioni where id_cons='$id_cons' and id_sez='$id_sez'";
		$res = $dbi->prepare("$sql");
		$res->execute();
		list($validi,$nulli,$bianchi,$contestati,$solo_lista,$votinulli) = $res->fetch(PDO::FETCH_NUM);
		if ($voti_parz!=($validi-$solo_lista) and $voti_parz>0){
			echo "<tr><td style=\"background-color: rgb(255, 0, 0); text-align:center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br><b> "._ATT_VOTI." ".$voti_parz." "._NO_VAL_VOTI." ".($validi-$solo_lista)."</b><br></td><tr>";
		}
		$sql="SELECT max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_cons='$id_cons' and id_sez='$id_sez'";
		$res2 = $dbi->prepare("$sql");
		$res2->execute();
		list($tot) = $res2->fetch(PDO::FETCH_NUM);
		if ($validi+$nulli+$bianchi+$contestati+$votinulli!=$tot and $validi+$nulli+$bianchi+$contestati+$votinulli>0){
			echo "<tr><td style=\"background-color: rgb(255, 0, 0); text-align:center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br><b> "._ATT_VOTANTI." ".$tot." "._NO_TOT_VOTI." ".($validi+$nulli+$bianchi+$contestati+$votinulli)."</b><br></td></tr>";
    	}
		$sql="SELECT sum(voti) from ".$prefix."_ele_voti_lista where id_cons=$id_cons and id_sez=$id_sez";
		$resg = $dbi->prepare("$sql");
		$resg->execute();
		list($voti_sez)=$resg->fetch(PDO::FETCH_NUM);
		if(($genere==5 and !$disgiunto and $voti_sez and $flagsg and ($tipo_cons==18 or $tipo_cons==19))){
			$sql="SELECT sum(solo_gruppo) from ".$prefix."_ele_voti_gruppo where id_cons=$id_cons and id_sez=$id_sez";
			$resg = $dbi->prepare("$sql");
			$resg->execute();
			list($sgpl)=$resg->fetch(PDO::FETCH_NUM);
			$sql="SELECT solo_gruppo from ".$prefix."_ele_sezioni where id_cons=$id_cons and id_sez=$id_sez";
			$resg = $dbi->prepare("$sql");
			$resg->execute();
			list($sg)=$resg->fetch(PDO::FETCH_NUM);
			if ($sg != $sgpl) echo "<tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br><b> "._ATT_UNI." ".$sgpl." "._ATT_TOT_UNI." ".$sg."</b><br></td></tr>";
		}
#controllo voti a liste collegate <= voto di gruppo per voto non disgiunto
		if($genere==5 and !$disgiunto and $flagsg  and ($tipo_cons==18 or $tipo_cons==19)){
			$sql="SELECT id_gruppo FROM ".$prefix."_ele_gruppo where id_cons='$id_cons' $circo";
			$resref = $dbi->prepare("$sql");
			$resref->execute();
			$errgrulis=array();
			$sql="SELECT count(0) FROM ".$prefix."_ele_voti_lista where id_sez='$id_sez'";
			$res4 = $dbi->prepare("$sql");
			$res4->execute();
			list($sezscrl)=$res4->fetch(PDO::FETCH_NUM); 
			if ($sezscrl)
                while ( list($id_gruppo)=$resref->fetch(PDO::FETCH_NUM))
                {
					if($disgiunto || $flagsg){	
						$sql="SELECT sum(voti-solo_gruppo) FROM ".$prefix."_ele_voti_gruppo where id_sez='$id_sez' and id_gruppo='$id_gruppo'";
						$res3 = $dbi->prepare("$sql");
						$res3->execute();
						if($flagsg){
							$sql="SELECT sum(voti+nulli_lista) FROM ".$prefix."_ele_voti_lista where id_sez='$id_sez' and  id_lista in (select id_lista from ".$prefix."_ele_lista where id_gruppo='$id_gruppo')";
							$res4 = $dbi->prepare("$sql");
							$res4->execute();
						}else{
							$sql="SELECT sum(voti) FROM ".$prefix."_ele_voti_lista where id_sez='$id_sez' and  id_lista in (select id_lista from ".$prefix."_ele_lista where id_gruppo='$id_gruppo')";						
							$res4 = $dbi->prepare("$sql");
							$res4->execute();							
						}
					}else{
						$sql="SELECT sum(voti) FROM ".$prefix."_ele_voti_gruppo where id_sez='$id_sez' and id_gruppo='$id_gruppo'";
						$res3 = $dbi->prepare("$sql");
						$res3->execute();
						$sql="SELECT sum(voti) FROM ".$prefix."_ele_voti_lista where id_sez='$id_sez' and  id_lista in (select id_lista from ".$prefix."_ele_lista where id_gruppo='$id_gruppo')";						
						$res4 = $dbi->prepare("$sql");
						$res4->execute();
					}
                    list($vgruppo)=$res3->fetch(PDO::FETCH_NUM);
                    list($vliste)=$res4->fetch(PDO::FETCH_NUM);
                    if($vliste>$vgruppo and !$disgiunto )
                    {
                    	$sql="SELECT descrizione FROM ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
 						$result = $dbi->prepare("$sql");
						$result->execute();
						list($dgruppo)=$result->fetch(PDO::FETCH_NUM);
                    	echo "<tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br/><b> ATTENZIONE!<BR/>I voti delle liste collegate $vliste<br/> superano i voti assegnati al gruppo $dgruppo $vgruppo</b><br></td></tr>";
                    	$errgrulis[$id_gruppo]=1;
                   	}
                    elseif($vliste<$vgruppo and !$disgiunto )
                   	{
                    	$sql="SELECT descrizione FROM ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo'";
 						$result = $dbi->prepare("$sql");
						$result->execute();
                    	list($dgruppo)=$result->fetch(PDO::FETCH_NUM);
                    	echo "<tr><td style=\"background-color: rgb(255, 0, 0); color: black; text-align: center\"><img src=\"modules/Elezioni/images/alert.gif\" align=\"middle\" alt=\"\"><br/><b> ATTENZIONE!<BR/>I voti assegnati al gruppo $dgruppo<br/> superano i voti delle liste collegate </b><br></td></tr>";
                    	$errgrulis[$id_gruppo]=1;
                    }
                }
		}
	}
    if(!isset($votinulli)) $votinulli=0;
	echo "<tr><td></td></tr></table>";
	if ($validi+$nulli+$bianchi+$contestati+$votinulli>0 or $genere==0) {
		if($genere==0)   echo "<table class=\"table-menu\" style=\" width: 60%; color: black\"><tr><td colspan=\"3\"><form name=\"sezioni\" data-ajax=\"false\" action=\"modules/Elezioni/salva_ref.php\">";
		else echo "<table class=\"table-menu\" style=\" width: 60%; color: black\"><tr><td colspan=\"3\"><form name=\"sezioni\" data-ajax=\"false\" action=\"modules/Elezioni/salva_gruppi.php\">";
		echo "<input type=\"hidden\" name=\"op\" value=\"rec_voti_gruppiq\">"
		."<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\">"
		."<input type=\"hidden\" name=\"id_cons\" value=\"$id_cons\">"
		."<input type=\"hidden\" name=\"id_sez\" value=\"$id_sez\">"
		."<input type=\"hidden\" name=\"id_circ\" value=\"$id_circ\">"
		."<input type=\"hidden\" name=\"id_sede\" value=\"$id_sede\">"
		."<input type=\"hidden\" name=\"circo\" value=\"$circo\">"
		."<input type=\"hidden\" name=\"genere\" value=\"$genere\">"
		."<input type=\"hidden\" name=\"do\" id=\"do\" value=\"0\">";
		if ($genere==0){
			echo "<br><br> <table  class=\"table-menu\" style=\"width: 100%; color: black\"><tr align=\"center\" bgcolor=\"$bgcolor1\">"
			."<td colspan=\"6\"><b>"._GRUPPO."</b></td></tr>";
			$campitesta= "<tr align=\"center\" bgcolor=\"$bgcolor1\"><td><b>Votanti "._SI."</b></td>"
			."<td><b>Votanti "._NO."</b></td>"
			."<td><b>"._VALIDI."</b></td>"
			."<td><b>"._BIANCHI."</b></td>"
			."<td><b>"._CONTESTATI."</b></td>"
			."<td><b>"._NULLI."</b></td></tr>";
			
			$campiriep="<tr  style=\"background-color: $bgcolor1; text-align:center\"><td colspan=\"2\"><b>"._TOTNON."</b></td>"
			."<td colspan=\"2\"><b>"._TOTALEVOTI."</b></td>"
			."<td colspan=\"2\"><b>"._VOTANTI."</b></td></tr>";
		}else{
			echo "<br><br><table  class=\"table-menu\" style=\"width: 100%; color: black\"><tr align=\"center\" bgcolor=\"$bgcolor1\">"
			."<td width=\"3%\"><b>"._NUM."</b></td>"
			."<td width=\"50%\"><b>"._GRUPPO."</b></td>";
			echo "<td width=\"5%\"><b>"._VOTI."</b></td>";
			if($dettnulli)		echo "<td bgcolor=\"$bgcolor1\" align=\"center\"><b>"._SOLO_GRUPPO."</b></td>";
			echo "</tr>";
		}
		$sql="SELECT * FROM ".$prefix."_ele_gruppo where id_cons='$id_cons' $circo ORDER BY num_gruppo ";
		$res = $dbi->prepare("$sql");
		$res->execute();
		$max = $res->rowCount();
		//echo "Massimo:$max - id=$id_cons - circo: $circo";
		$sql="select * from ".$prefix."_ele_gruppo where id_cons='$id_cons' $circo ORDER BY num_gruppo  ";
		$result = $dbi->prepare("$sql");
		$result->execute();
		$i=1;
		$tot_pref=0;
		$totsg=0;
		while(list($id_cons2,$id_gruppo,$num_gruppo, $descr_gruppo, $simbolo) = $result->fetch(PDO::FETCH_NUM)){
			echo "<SCRIPT type=\"text/javascript\">\n\n<!--\n"
			."//-->\n";
			echo "function controlloref$i() {\n";
			echo "var a=Number(window.document.sezioni.si$i.value); var b=Number(window.document.sezioni.no$i.value); if(a=='NaN') {a=0} if(b=='NaN') {b=0} var c=a + b; window.document.sezioni.val$i.value=c\n";
			echo "}\n";
			echo "</script>\n";    
			if ($num_gruppo != ''){
				if ($genere==0){
					$sql="SELECT max(voti_complessivi) FROM ".$prefix."_ele_voti_parziale where id_cons='$id_cons' and id_sez='$id_sez' and id_gruppo='$id_gruppo' ";
					$res = $dbi->prepare("$sql");
					$res->execute();
					list($tot) = $res->fetch(PDO::FETCH_NUM);
					$sql="SELECT * FROM ".$prefix."_ele_voti_ref where id_cons='$id_cons' and id_sez='$id_sez' and id_gruppo='$id_gruppo' ";
					$res = $dbi->prepare("$sql");
					$res->execute();
					$numpro=$res->rowCount();
					$pro= $res->fetch(PDO::FETCH_BOTH);
					echo "<tr><td colspan=\"6\">&nbsp;</td></tr>";
					if ($numpro and ($pro['si']+$pro['no']!=$pro['validi'] or ($pro['validi']+$pro['nulli']+$pro['bianchi']+$pro['contestati']!=$tot and $pro['validi']+$pro['nulli']+$pro['bianchi']+$pro['contestati']!=0))){
						echo "<tr style=\"background-color: rgb(255, 0, 0); text-align:center\">";
					}elseif($numpro and ($pro['validi']+$pro['nulli']+$pro['bianchi']+$pro['contestati']!=0))
						echo "<tr style=\"background-color:#99CC33; text-align:center\">"; 
					else 
						echo "<tr style=\"background-color:$bgcolor2; text-align:center\">";
#					$descr = explode('.',$descr_gruppo, 100);
					echo "<td colspan=\"6\" align=\"center\"><input type=\"hidden\" name=\"id_gruppo$i\" value=\"$id_gruppo\"><b>$num_gruppo) </b>"
					."<b> $descr_gruppo </b></td></tr>";
					$pro['si']=(isset($pro['si']) and $pro['si']>=0) ? $pro['si']:'';
					$pro['no']=(isset($pro['no']) and $pro['no']>=0) ? $pro['no']:'';
					$pro['validi']=(isset($pro['validi']) and $pro['validi']>=0) ? $pro['validi']:'';
					$pro['bianchi']=(isset($pro['bianchi']) and $pro['bianchi']>=0) ? $pro['bianchi']:'';
					$pro['contestati']=(isset($pro['contestati']) and $pro['contestati']>=0) ? $pro['contestati']:'';
					$pro['nulli']=(isset($pro['nulli']) and $pro['nulli']>=0) ? $pro['nulli']:'';
					$tot_nulli=intval($pro['nulli'])+intval($pro['bianchi'])+intval($pro['contestati']);
					$tot_voti=intval($pro['validi'])+$tot_nulli;
					echo $campiriep; # style=\"text-align: center; border: 1px; border-color: black; border-collaps:collaps;\"
					echo "<tr style=\"background-color: $bgcolor2; text-align:center\"><td colspan=\"2\" >$tot_nulli</td>";
					echo "<td colspan=\"2\">$tot_voti</td>";
					echo "<td colspan=\"2\">$tot</td></tr>";
					echo $campitesta;	
					echo "<tr><td align=\"right\" width=\"3%\"><input  name=\"si$i\" value=\"".$pro['si']."\" size=\"7\"  style=\"text-align:right\" onchange=controlloref$i() onfocus=\"select();\"></td>";
					echo "</td><td align=\"right\" width=\"3%\"><input  name=\"no$i\" value=\"".$pro['no']."\" size=\"7\"  style=\"text-align:right\" onchange=controlloref$i() onfocus=\"select();\"></td>";
					echo "</td><td align=\"right\" width=\"3%\"><input  name=\"val$i\" value=\"".$pro['validi']."\" size=\"7\" style=\"text-align:right;\" onfocus=\"select();\"></td>";
					echo "</td><td align=\"right\"><input  name=\"bia$i\" value=\"".$pro['bianchi']."\" size=\"7\"  style=\"text-align:right;\" onfocus=\"select();\"></td>";
					echo "</td><td align=\"right\"><input  name=\"con$i\" value=\"".$pro['contestati']."\" size=\"7\"  style=\"text-align:right;\" onfocus=\"select();\"></td>";
					echo "</td><td align=\"right\"><input  name=\"nul$i\" value=\"".$pro['nulli']."\" size=\"7\"  style=\"text-align:right;\" onfocus=\"select();\"></td>";
				}else{
					echo "<tr style=\"background-color: $bgcolor2; text-align:center\"><td align=\"center\"><input type=\"hidden\" name=\"num_gruppo$i\" value=\"$num_gruppo\"><b>$num_gruppo</b>"
					."</td><td align=\"left\"><b> $descr_gruppo </b>";
					$sql="SELECT * FROM ".$prefix."_ele_voti_gruppo where id_cons='$id_cons' and id_sez='$id_sez' and id_gruppo='$id_gruppo' ";
					$res = $dbi->prepare("$sql");
					$res->execute();
					$pro= $res->fetch(PDO::FETCH_BOTH);
					if(!$res->rowCount()) {$pro['voti']=''; $pro['solo_gruppo']='';}
					$errcolor='';
					if(isset($errgrulis[$id_gruppo])) {$errcolor="style=\"background-color: rgb(255, 0, 0);\"";}
					echo "</td><td align=\"right\" $errcolor><input  name=\"voti$i\" value=\"".$pro['voti']."\" size=\"7\"  style=\"text-align:right;\" onfocus=\"select();\"></td>";
					if($dettnulli) {echo "<td align=\"right\"><input name=\"solog$i\" value=\"".$pro['solo_gruppo']."\" size=\"7\"  style=\"text-align:right;\" onfocus=\"select();\"></td>"; $totsg+=intval($pro['solo_gruppo']);} 
					$tot_pref += intval($pro['voti']);
					echo "</tr>";
				}
				$i++;
			}
		}
		if ($genere!=0) {
			$sql="SELECT disgiunto FROM ".$prefix."_ele_cons_comune where id_cons='$id_cons' ";
			$res = $dbi->prepare("$sql");
			$res->execute();
			list($disgiunto)=$res->fetch(PDO::FETCH_NUM);
			$sql="SELECT solo_lista FROM ".$prefix."_ele_sezioni where id_sez='$id_sez' ";
			$res = $dbi->prepare("$sql");
			$res->execute();
			if ($tot_pref!=$validi and ($tot_pref>0 and $validi>0)) $segna2="style=\"background-color: #dd0000;\""; else $segna2='';
			list($sololis)=$res->fetch(PDO::FETCH_NUM);
			echo "<tr style=\"background-color: $bgcolor1; text-align:center\"><td></td><td>"._TOTPREF."</td><td $segna2>$tot_pref</td>";
			if($dettnulli) echo "<td>$totsg</td>";
			echo "</tr>";
			if($disgiunto)
				echo "<tr style=\"background-color: $bgcolor1; text-align:center\"><td></td><td>"._SOLOLIS."</td><td><input  name=\"sololista\" value=\"".$sololis."\" size=\"7\"  style=\"text-align:right;\" onfocus=\"select();\"></td></tr>";
		}
		echo "<tr><td></td><td></td><td align=\"center\"><input type=\"submit\" name=\"update\" id=\"update\" value=\" "._OK. "\"></td>";
		echo "</tr></table></form></td></tr>";
		if(chisei($id_cons_gen)>=64){
			echo "<tr><td><input type=\"checkbox\" id=\"pwd3\" name=\"pwd3\" value=\"\" onclick=\"javascript:del_dati()\">"._DELETE."";
		}
		echo "</td></tr></table></center>";
		$scrtest=0;
	}else{$scrtest=1;}
	if ($genere!=0){
		finale($id_cons,$do,$id_circ,$id_sede,$id_sez,$ops);
	}
}




?>