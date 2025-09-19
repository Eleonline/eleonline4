<?php

if (!defined('MODULE_FILE')) {
    die ("Non puoi accedere al file direttamente...");
}

global $id_circ;

	if (! isset($content)) $content="";
	if (! isset($tlist)) $tlist="";
	
	$content .="<li><a href=\"#\"><strong>&nbsp; &nbsp;&nbsp;"._TEMA."</strong></a>
	    <ul>";
	
	
        
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

				$files=ucfirst($tlist[$i]);
	$content .= "<li><a href=\"modules.php?name=Elezioni&amp;id_comune=$id_comune&amp;id_cons_gen=$id_cons_gen&amp;id_circ=$id_circ&amp;tema=$tlist[$i]\">$files</a></li>"; 



	    	

		}
    	}
	
    $content .="</ul></li>";

  echo $content;

     
?>
