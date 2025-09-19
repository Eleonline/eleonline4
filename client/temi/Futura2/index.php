<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

$nometema=$tema;
#require_once("class/db/db.php"); //classe db
global $tema,$id_comune,$descr_cons,$genere,$tipo_cons,$multicomune;

# colore tema mobile


include("temi/$tema/config.php");

#if($colortheme=='')$colortheme="c";
# descrizione comune
if(!$id_comune or $id_comune=='') $id_comune=$siteistat;
$sql="SELECT descrizione FROM ".$prefix."_ele_comuni where id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

list($descr_com) = $res->fetch(PDO::FETCH_NUM);


####################################
function testata(){
####################################

global $op,$tema,$dbi,$nometema,$file,$bgcolor,$sitename,$prefix,$blocco,$lang,$siteistat,$id_cons_gen,$descr_cons,$minsez,$offsetsez,$multicomune,$id_comune,$multicomune,$rss,$colortheme,$descr_com;

include("temi/$tema/function_theme.php");

$numerodisezioni=numerodisezioni();
 
$logo= "<img src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_comune=".$id_comune."\" alt=\"logo\" width=\"70\" align=\"left\"/>";

$sql="SELECT descrizione FROM ".$prefix."_ele_comuni where id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($descr_com) = $res->fetch(PDO::FETCH_NUM);
        $descr_com =stripslashes($descr_com); 

echo '
<div data-role="page" data-theme="a">
	<div data-role="header" data-position="inline">';
	
	/*
	if($op!="gruppo") 
		echo '<a href="#" onClick="javascript:history.back()" data-role="button" data-icon="arrow-left">indietro</a>';
	else 	echo '<a href="modules.php?tema=facebook" target="_blank" data-icon="off" data-role="button">Vers Web</a>';
	*/
	//
	# se aperta o sezioni scrutinate 
	# se non è il menu di config o about sceglie elezione
	if($numerodisezioni[0]>0 and $op!="conf_mob" and $op!="about"){			 
		echo '<a href="#" data-role="button">sezioni <img src="modules/Elezioni/grafici/ledex1.php?sez='.$numerodisezioni[0].'&max='.$numerodisezioni[1].'" /></a><h2></h2>';
	}else{ 
	   echo '<h2>Elezioni on line</h2>'; 
	}

	   echo '<a href="modules.php?name=Elezioni&op=about&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-theme="c" data-icon="user">About</a>';
	

####### menu in alto
# definizione attivo


if($op=="gruppo" ) $active_home=" class=\"ui-btn-active\""; else $active_home='';
if($op=="informazioni" || $op=="dati_generali" || $op=="come_si_vota" || $op=="numeri_mob" || $op=="servizi_mob") $active_info=" class=\"ui-btn-active\""; else $active_info='';
if($op=="risultati" || $op=="affluenze_all" || $op=="gruppo_mob" || $op=="candidato_mob" || $op=="liste_mob" || $op=="votanti_mob") $active_ris=" class=\"ui-btn-active\""; else $active_ris='';
if($op=="conf_mob" ) $active_conf=" class=\"ui-btn-active\""; else $active_conf='';
if($op=="grafica_mob" ) $active_graf=" class=\"ui-btn-active\""; else $active_graf='';



echo ' <div data-role="navbar">
	<ul>
	<li><a href="modules.php?id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'?" data-icon="home"   data-theme="'.$colortheme.'" '.$active_home.' >Home</a></li>
	<li><a href="modules.php?op=informazioni&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-icon="file"  data-theme="'.$colortheme.'" '.$active_info.' >Info</a></li>
	<li><a href="modules.php?op=risultati&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-icon="list"  data-theme="'.$colortheme.'" '.$active_ris.' >Risultati</a></li>
	<li><a href="modules.php?op=grafica_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'"" data-icon="th-large"  data-theme="'.$colortheme.'" '.$active_graf.'>Grafica</a></li>
	<li><a href="modules.php?op=conf_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-icon="cogs"  data-theme="'.$colortheme.'" '.$active_conf.' >Opzioni</a></li>
	</ul>
   </div>
</div>';


# se non è il menu di config o about sceglie elezione
if($op!="conf_mob" and $op!="about"){
	echo'<div data-role="fieldcontain" style="text-align:center;">
	<span style="text-align:center;width:100%;height:110px;background-color:#fff;color:#000;">'.$logo.' Comune di '.$descr_com.'</span>';



	$sql="SELECT t1.id_cons_gen,t1.descrizione FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_comune='$id_comune' and t2.chiusa!='2' order by t1.data_fine desc"; 
	$res = $dbi->prepare("$sql");
	$res->execute();
	
	$esiste=$res->rowCount();

		echo " <form  method=\"post\" action=\"modules.php\">
<label for=\"consultazione\">
		
		<input id=\"modulo\" type=\"hidden\" name=\"name\" value=\"Elezioni\" />
      		<input type=\"hidden\" name=\"prima\" value=\"1\" />
		<select name=\"id_cons_gen\" id=\"select-choice-a\" 
		onchange=\"javascript:top.location.href='modules.php?op=gruppo&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;id_cons_gen='+this.options[this.options.selectedIndex].value\">";
	
	    while(list($id,$descrizione) = $res->fetch(PDO::FETCH_NUM)) {
#		$descrizione=substr(str_replace("+"," ",$descrizione),0,30);
     		$sel = ($id == $id_cons_gen) ? "selected=\"selected\"":"";		
	        echo "<option style=\"font-size:12px;\" value=\"$id\" $sel >$descrizione</option>";
	    }
 
	    echo '</select></label></form>

	</div>';
} # fine form scelta elezioni



if($op=="gruppo")home();// leggi il file config.php
elseif($op=="informazioni")info();
elseif($op=="affluenze")affluenze();
elseif($op=="dati_generali")dati_generali();
elseif($op=="risultati")risultati();
elseif($op=="grafica_mob")grafica_mob();
elseif($op=="come_si_vota")come_si_vota();
elseif($op=="numeri_mob")numeri_mob();
elseif($op=="servizi_mob")servizi_mob();
elseif($op=="affluenze_all")affluenze_all();
elseif($op=="gruppo_mob")gruppo_mob();
elseif($op=="candidato_mob")candidato_mob();
elseif($op=="liste_mob")liste_mob();
elseif($op=="votanti_mob")votanti_mob();
elseif($op=="conf_mob")conf_mob();
elseif($op=="about")about();
elseif($op=="grafvotanti_mob")grafvotanti_mob();
elseif($op=="grafgruppo")grafgruppo();
elseif($op=="grafsezione")grafsezione();

footer_mon();

die();
}


function footer_mon(){
global $id_comune,$id_cons_gen,$tema,$colortheme,$rss,$prefix,$dbi;

$sql="SELECT tema FROM `".$prefix."_config`";
$res = $dbi->prepare("$sql");
$res->execute();
list($deftema) = $res->fetch(PDO::FETCH_NUM);

echo '
<div data-role="footer" data-theme="a" style="marig:0 auto; text-align:center;">
	<div data-role="footer" data-position="inline" data-icon="cogs">';
	
	echo '	<h5><a href="http://www.eleonline.it" target="_blank">Eleonline</a> <span style="font-size:12px;"> di luciano apolito & roberto gigli</span></h5></div>';

if($tema=="Futura2")
	//echo'<a href="backtoapp.html" data-rel="external" data-ajax="false">Chiudi</a>'; // per l'app precedente non piu usata
	echo '&nbsp;<a href="modules.php?name=Elezioni&tema='.$deftema.'&nocell=1&op=gruppo&id_cons_gen='.$id_cons_gen.'" data-rel="external" data-ajax="false" >Versione Desktop</a>';
else 
	echo '&nbsp;<a href="modules.php?name=Elezioni&tema=facebook&desktop=1&op=gruppo" data-rel="external" data-ajax="false" >Versione Desktop</a>';
	echo'</div>';
}



#######################################
####################################### inizio funzioni menu
#######################################



############################ menu home page
function home(){ 
global $id_comune,$id_cons_gen,$tema,$colortheme;
//include("temi/$tema/config.php");

echo '<div data-role="content" data-theme="a">
<a href="modules.php?op=informazioni&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">Informazioni Elettorali</a>
<!-- <a href="modules.php?op=affluenze&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="heart">Ultima Affluenza</a> -->
<a href="modules.php?op=risultati&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'"  data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">Risultati Elettorali</a>
</div>';
echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	//graf_gruppo_mob();
	echo "</ul>";
echo '';

}





############################### menu informazioni
function info(){
global $id_comune,$id_cons_gen,$colortheme;
echo '
<div data-role="content" data-theme="a">
<a href="modules.php?op=dati_generali&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">
Dati Generali</a>
<a href="modules.php?op=come_si_vota&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">
Come si Vota</a>
<a href="modules.php?op=numeri_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">
Numeri Utili</a>
<a href="modules.php?op=servizi_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">
Servizi Utili</a>			
</div>
';

}

####################################### menu risultati
function risultati(){ 
global $id_comune,$id_cons_gen,$genere,$tipo_cons,$colortheme,$votoc;
echo '<div data-role="content" data-theme="'.$colortheme.'">
<a href="modules.php?op=affluenze_all&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">Affluenze</a>
<a href="modules.php?op=votanti_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">Votanti</a>
<a href="modules.php?op=gruppo_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'"  data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">'._GRUPPO.'</a>';

	if($genere==3  OR $genere==5) 
		echo '<a href="modules.php?op=liste_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" " data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">'._LISTA.'</a>';
        if(($genere==3 OR $genere==5) and !$votoc) 
		echo '<a href="modules.php?op=candidato_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" " data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">'._CANDIDATO.'</a>';


echo'</div>
';

}

####################################### menu grafica
function grafica_mob(){ 
global $id_comune,$id_cons_gen,$genere,$tipo_cons,$colortheme;
echo '<div data-role="content" data-theme="'.$colortheme.'">

<a href="modules.php?op=grafsezione&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'"  data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">Elenco Sezioni scrutinate</a>

<a href="modules.php?op=affluenze&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">Grafica Affluenze</a>
<a href="modules.php?op=grafvotanti_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">Grafica Votanti</a>';
/*
<a href="modules.php?op=grafgruppo&grafica=0&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'"  data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">'._GRUPPO.'</a>';

	if($genere==3  OR $genere==5) 
		echo '<a href="modules.php?op=liste_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" " data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">'._LISTA.'</a>';
        if(($genere==3 OR $genere==5) and $tipo_cons!="1" or $tipo_cons>="11") 
		echo '<a href="modules.php?op=candidato_mob&id_comune='.$id_comune.'&id_cons_gen='.$id_cons_gen.'" " data-role="button" data-theme="'.$colortheme.'" data-icon="arrow-right" data-iconpos="right">'._CANDIDATO.'</a>';
*/

echo'</div>';


}


#######################################
####################################### inizio funzioni visualizzazione
#######################################


# affluenza unica x grafica
function affluenze(){
	global $tema; 
        affluenza_unica();
	grafica_mob();
}

# votanti x menu grafica

function grafvotanti_mob(){
	global $tema,$dbi,$prefix,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	
	votanti_mobile();
	echo "</ul>";
	grafica_mob();
	
}

# gruppo per menu grafica
function grafgruppo(){
	global $tema,$dbi,$prefix,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	graf_gruppo_mob();	
	echo "</ul>";
	grafica_mob();
	
}

# gruppo per menu grafica
function grafsezione(){
	global $tema,$dbi,$prefix,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	graf_sezioni();
	echo "</ul>";
	grafica_mob();
	
}




############ menu informazioni
############

function dati_generali(){
	global $tema,$colortheme; 
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	
	dati_mob_fun();
	echo "</ul>";
	info();
}

function come_si_vota(){
	global $tema,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	come_mob("come");
	echo "</ul>";
	info();
}

function numeri_mob(){
	global $tema,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	come_mob("numeri");
	echo "</ul>";
	info();
}

function servizi_mob(){
	global $tema,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	come_mob("servizi");
	echo "</ul>";
	info();
}





######## menu risultati 
########

function affluenze_all(){
	global $tema,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	
	affluenze_mob();
	echo "</ul>";
	risultati();
}

function votanti_mob(){
	global $tema,$dbi,$prefix,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	votanti_tabella();
	risultati();
	echo "</ul>";
}


# risultati semplici gruppo
function gruppo_mob(){
	global $tema,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	graf_gruppo_mob();
	//graf_risultati();
	echo "</ul>";
	risultati();
}

function liste_mob(){
	global $tema,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	//echo '<div data-role="content" data-theme="a"><p>'; 
	graf_liste_mob();
	echo "</ul>";
	risultati();
}


function candidato_mob(){
	global $tema,$colortheme;
	echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
	graf_candidato_mob();
	echo "</ul>";
	risultati();
}


function conf_mob(){
###############################################
# scelta comune in caso di multicomune tema
global $multicomune,$tema,$prefix,$dbi,$colortheme,$id_cons_gen,$info,$id_comune,$rss;

echo "<center>
	<div data-role=\"fieldcontain\" style=\"margin:0 auto;text-align:center;\">	
	<form method=\"post\" action=\"modules.php\">";



if ($multicomune=='1')
	{

	      $sql="select t1.id_comune,t1.descrizione,count(0) from ".$prefix."_ele_comuni as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_comune=t2.id_comune and t2.chiusa!='2' group by t1.id_comune,t1.descrizione order by t1.descrizione asc";
				$rescomu = $dbi->prepare("$sql");
				$rescomu->execute();
	      $esiste_multi=$rescomu->rowCount();
	      if ($esiste_multi>=1) {
		
		echo "  <input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\" />
			<input type=\"hidden\" name=\"id_cos_gen\" value=\"$id_cons_gen\" />
		 	<input type=\"hidden\" name=\"op\" value=\"gruppo\" />
			<select name=\"id_comune\" data-native-menu=\"false\">
			<option>Scegli il comune</option>";
	
		while (list($id,$descrizione,)=$rescomu->fetch(PDO::FETCH_NUM)){
			   $sel=($id == $id_comune) ? "selected=\"selected\"":"";
			    echo "<option value=\"$id\" $sel >$descrizione</option>";
			}
	
			    echo "</select>";
	  }
	}

### colore tema
# colore del tema
# la variabile rss, non usta in questo tema, è stata presa in prestito per determinare il colore del tema
$selez='';
		echo "<select name=\"rss\" data-native-menu=\"false\"  > ";
		echo "<option>Scegli il colore del tema</option>";			       
		echo "<option  value=\"2\" $selez >Blu</option>";
		echo "<option  value=\"3\" $selez >Rosso</option>";
		echo "<option  value=\"4\" $selez >Verde</option>";
		echo "<option  value=\"5\" $selez >Ciano</option>";
		echo "<option  value=\"6\" $selez >Giallo</option>";
		 echo "<option  value=\"7\" $selez >Grigio</option>";
  		echo '</select><p>
			<fieldset class="ui-grid-a" >					
			<div class="ui-block-b" style=width:100%;><button type="submit" data-theme="'.$colortheme.'">Conferma</button></div></fieldset>
			</p></form></div></center>';
}

function about(){
###############################################
# scelta comune in caso di multicomune tema
global $tema,$dbi,$prefix,$colortheme,$id_comune,$descr_com;

		echo '<ul data-role="listview" data-inset="true" data-divider-theme="'.$colortheme.'">';
		echo "<li style=\"text-align:center;\" data-role=\"list-divider\" data-icon=\"arrow-up\" >";
		echo "<b>About e Credits</b></li>";
		echo "<div  data-role=\"content\" data-theme=\"$colortheme\">";
		echo "<a href=\"http://www.eleonline.it\">
			<img src=\"temi/$tema/images/mobile.jpg\" align=\"left\">EleONLine</a><br/> 
			
							Software Open Source per ogni elezione<br/>
							di <a href=\"mailto:luciano@linuxap.it\">Luciano Apolito</a> & 
							<a href=\"mailto:rgigli@libero.it\">Roberto Gigli</a><br/>
							<i>stai usando lo \"Smartphone theme <b>FUTURA n.2</b>\"<br/>
							</div>";
		echo "<li style=\"text-align:center;\" data-role=\"list-divider\" data-icon=\"arrow-up\" >";
		echo "<b>Gruppo di Lavoro Comune di $descr_com</b></li></ul>";	
		include ("pagine/gruppo.html");
		//echo "</div>aaa";



}

?>
