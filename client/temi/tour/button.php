<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

# linguaggio x demo
###########################
if(!function_exists("language")){

function language(){
###########################

global $lang,$name,$op,$file,$filelang,$id_comune,$op,$id_cons,$id_cons_gen;
$filename=$filelang;
// linguaggio

$menulist='';
// scelta linguaggio
    	$langdir = dir("modules/Elezioni/language");
    	while($func=$langdir->read()) {
		if(substr($func, 0, 5) == "lang-") {
	    	$menulist .= "$func ";
		}
    	}
    	closedir($langdir->handle);
    	$menulist = explode(" ", $menulist);
    	sort($menulist);
    	for ($i=0; $i < sizeof($menulist); $i++) {
		if($menulist[$i]!="") {
	    		$tl = preg_replace("/lang-/","",$menulist[$i]);
	    		$tl = preg_replace("/.php/","",$tl);
	    		$altlang = ucfirst($tl);
                        
			if ($lang==$tl) $bordo="class=\"bordo\"";else $bordo="class=\"nobordo\"";
	    		echo "<a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;newl=$tl\"><img $bordo src=\"modules/Elezioni/images/$tl.gif\"  alt=\"$altlang\" title=\"$altlang\" /></a> ";
		}
    	}

}
}
# flash x demo

###############################
if(!function_exists("flash")){
function flash(){
###############################
global $flash,$name,$id_comune,$op,$id_cons,$id_cons_gen;

if ($flash=='1'){ 
     echo "<a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;flash=0\"><img class=\"nobordo\" src=\"modules/Elezioni/images/flashno.gif\" alt=\"NoFlash\" title=\"NoFlash\" /></a>";

}else{

echo "<a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;flash=1\"><img class=\"nobordo\" src=\"modules/Elezioni/images/flashyes.gif\" alt=\"YesFlash\" title=\"YesFlash\" /></a>";

}

}
} 
 
# blocco no
############################
if(!function_exists("noblocco")){
function noblocco(){
############################
global $blocco,$name,$id_comune,$op,$id_cons,$id_cons_gen;

if ($blocco=='1'){ 
     	echo "<a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;block=0\"><img class=\"nobordo\" src=\"modules/Elezioni/images/close.gif\" alt=\"NoBlocco\" title=\"NoBlocco\" /></a>";

}else{

	echo "<a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;block=1\"><img class=\"nobordo\" src=\"modules/Elezioni/images/open.gif\" alt=\"YesBlocco\" title=\"YesBlocco\" /></a>";

}

}
}

# tour


############################
if(!function_exists("tour")){ 
function tour(){
############################
global $tour,$name,$id_comune,$op,$id_cons,$id_cons_gen;

if ($tour=='1'){ 
 
echo "<a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;tour=0&amp;csv=0&amp;block=1\">
	<img class=\"nobordo\" src=\"modules/Elezioni/images/stop.gif\" alt=\"NoBlocco\" title=\"Stop\" /></a>";

}else{

	
	
	echo "<a href=\"modules.php?name=$name&amp;csv=1&amp;block=0&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;tour=1\">
	
	
	<img class=\"nobordo\" src=\"modules/Elezioni/images/play.gif\" alt=\"YesBlocco\" title=\"Play\" /></a>";

}

}
}

?>
