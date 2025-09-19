<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* tema tour per far girare le pagine in automatico */
/* le pagine vanno messe nel file config,php */
global $tour;
if (file_exists("temi/tour/config.php")) {
	include_once 'temi/tour/config.php';
}

if(!isset($lista_ruota)){
$lista_ruota=array(     'affluenze_graf',
                        'graf_votanti',
                        'graf_gruppo',
			'graf_lista'
                 );
}
$blocco=0;

$param=strip_tags(strtolower($_SERVER['REQUEST_METHOD'])) == 'get' ? $_GET : $_POST;

####################################
// rotazione pagina
// bottone file button.php 
####################################

if (isset($param['tour'])){
   $tour=$param['tour'];
   $_SESSION['newtour']="$tour";
        }
if (isset($_SESSION['newtour'])) $tour=$_SESSION['newtour']; 


if($tour=='1'){

  $max_ruota=count($lista_ruota)-1;
  if (isset($_SESSION['ruota'])) { $ruota=$_SESSION['ruota'];}
  else {$ruota=0;$_SESSION['ruota']=0;}
  if ($ruota>=$max_ruota) {$ruota=0;}
  else {$ruota++;}
  $_SESSION['ruota']=$ruota;
  $op=$lista_ruota[$ruota];
}else{
if (isset($_SESSION['ruota'])) unset($_SESSION['ruota']);
}
//fine rotazione pagine
$nometema=$tema;
if (isset($_SESSION['ruota'])){ include("temi/$tema/button.php");tour();
echo "<span class=\"piccolo\"> eleonline by l.apolito & r.gigli - www.eleonline.it</span>";} #bottone stop


####################################
function testata(){
####################################

global $nometema,$file,$bgcolor,$sitename,$dbi,$prefix,$blocco,$lang,$siteistat;
if (isset($param['id_comune'])) $id_comune=intval($param['id_comune']); else $id_comune=$siteistat;

$sql="SELECT descrizione FROM ".$prefix."_ele_comuni where id_comune='$id_comune' ";
$res = $dbi->prepare("$sql");
$res->execute();
list($descr_com) = $res->fetch(PDO::FETCH_NUM);


// logo
echo ' 
<div id="header">
    <div>
        <h1>';
		echo "<a href=\"modules.php?name=Elezioni\"> $descr_com</a>";
echo '</h1>
	<b>consultazioni elettorali on line </b> <a href="http://www.eleonline.it"> <i>by l.apolito e r.gigli</i></a>
	<br/>
	<div id="bottoni">&nbsp;';
		language();
		flash();
#		noblocco();
		tour();
echo ' </div>

    </div>
</div>

';

// menu
echo '<div id="menu"><br/>';
	if ($file=="index") menu();

echo "</div>";

echo '
	 <div id="page"> 
		<div class="table-main">';

if($blocco==1) echo '<div id="content">';

}
##################################
function piede(){
##################################
global $nometema,$blocco,$lang;
//echo "<div></div>";


// carica i blocchi
if($blocco==1) {blocco();
echo "</div></div>";
// carica il file foooter
$tmpl_file = "temi/language/$lang/footer.html";
$thefile = implode("", file($tmpl_file));
$thefile = addslashes($thefile);
$thefile = "\$r_file=\"".$thefile."\";";
eval($thefile);
print $r_file;
die();
}
}
####################################
function blocco(){
####################################
global $name,$blocco,$bgcolor, $nometema,$id_comune,$tipo_cons,$id_cons_gen,$id_cons,$prefix,$dbi, $votog,$votol,$votoc,$circo,$genere,$lang,$op,$id_circ;

if ($blocco==1) echo "</div><div class=\"td-149\">";

echo "<a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;block=0\"><img class=\"noblocco\" src=\"modules/Elezioni/images/close.gif\" alt=\"NoBlocco\" title=\"NoBlocco\" /></a>";



// Blocco generale

if(file_exists("temi/language/$lang/bloccogen.html")){
$tmpl_file = "temi/language/$lang/bloccogen.html";
$thefile = implode("", file($tmpl_file));
$thefile = addslashes($thefile);
$thefile = "\$r_file=\"".$thefile."\";";
eval($thefile);
print $r_file;
}
caricablocchi();

// Blocco consultazioni
if ($genere>2){
$tmpl_file = "temi/language/$lang/bloccocand.html";
$thefile = implode("", file($tmpl_file));
$thefile = addslashes($thefile);
$thefile = "\$r_file=\"".$thefile."\";";
eval($thefile);
print $r_file;
}else{
if ($genere==0){
//Blocco Referendum
$tmpl_file = "temi/language/$lang/bloccoref.html";
$thefile = implode("", file($tmpl_file));
$thefile = addslashes($thefile);
$thefile = "\$r_file=\"".$thefile."\";";
eval($thefile);
print $r_file;
}
}
// Blocco link

$sql="select mid, title, preamble, content,editimage from ".$prefix."_ele_link where id_cons='$id_cons' order by mid ";
$result = $dbi->prepare("$sql");
$result->execute();
   if ($result->rowCount() == 0) {
	//echo "</div></td></tr></table>";
        
	
	
    } else {
	echo "<h5>"._LINK."</h5><p>";
	while (list($mid, $title, $preamble,$content,  $editimage) = $result->fetch(PDO::FETCH_NUM)) {
  		if ($title != "" && $content != "") {
			$content = stripslashes($content);
    			$content = substr($content,0,45);
			echo "<b><a href=\"$preamble\">
			$title</a></b><br />

			$content<br/>";
		}		     
	}
	
   }	


######## footer ######################################

echo "<h5>Note</h5>
<h6><a href=\"modules.php?name=Elezioni&amp;file=index&amp;op=contatti\"> Contatti</a> </h6>"; 

include_once("versione.php");

 echo "<br />[<a href=\"http://www.eleonline.it\"><b>Eleonline $versione</b></a> - "._GESRIS." ]
";
echo '<br/><br/>

<!-- w3c -->
	<div class="w3cbutton3">
  		<a href="http://www.w3.org/WAI/WCAG1AA-Conformance" title="pagina di spiegazione degli standard">
    		<span class="w3c">W3C</span>
    		<span class="spec">WAI-<span class="specRed">AA</span></span>
  		</a>
	</div>
	<div class="w3cbutton3">
  		<a href="http://jigsaw.w3.org/css-validator/" title="Validatore css">
		<span class="w3c">W3C</span>
    		<span class="spec">CSS</span>
  		</a>
	</div>
	<div class="w3cbutton3">
  		<a href="http://validator.w3.org/" title="Validatore XHTML ">
    		<span class="w3c">W3C</span>
    		<span class="spec">XHTML 1.0</span>
		</a>
	</div>';

echo "
</div>"; 


}

include("temi/tour/button.php");
//if (isset($_SESSION['ruota'])){ echo "<div style:\"margin auto; text-align:center\">Eleonline</div>";} 
?>
