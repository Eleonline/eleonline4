<?php
if (!defined('MODULE_FILE')) {
    die ("Non puoi accedere al file direttamente...");
}
global $id_circ;

	if (! isset($content)) $content="";
	if (! isset($tlist)) $tlist="";
	
	$content .="
	<div class=\"form\">
	<form action=\"modules.php\" method=\"post\">
     	<select name=\"tema\" class=\"moduloform\" onchange=\"javascript:top.location.href='modules.php?name=Elezioni&amp;id_comune=$id_comune&amp;id_cons_gen=$id_cons_gen&amp;op=$op&amp;id_circ=$id_circ&amp;tema='+this.options[this.options.selectedIndex].value\">";
	
        
	$path = "temi/";
	$handle=opendir($path);
    	while ($file = readdir($handle)) {

			if ( (preg_match('/^([_0-9a-zA-Z]+)([_0-9a-zA-Z]{3})$/',$file)) ) {

		   $tlist .= "$file ";
		}
   	}

    	closedir($handle);
    	$tlist = explode(" ", $tlist);
    	sort($tlist);
    	for ($i=0; $i < sizeof($tlist); $i++) {
		if(($tlist[$i]!="") && ($tlist[$i]!="language")) {
	    		if ($tema == $tlist[$i]) {
				$sel = "selected";
	    		} else {
				$sel = "";
	    		}

				$files=$tlist[$i];
	    	$content .="<option name=\"tema\" value=\"$tlist[$i]\" $sel>Tema--->$files\n";

		}
    	}
	$content .="</select>
	
	</form>
	</div>";
echo $content;

     
?>
