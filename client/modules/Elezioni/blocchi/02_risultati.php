<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
# lettura feed rss interno

if (!defined('MODULE_FILE')) {
    //die ("You can't access this file dirrectly...");
}

global $descr_cons,$circo,$genere,$id_circ,$id_sez;

#echo "TEST: id_circ=$id_circ - circo=$circo - sez=$id_sez<br>";
if($genere!='0'){ // referendum e circoscrizionali
    list ($gruppo,$pro)=grupporss();
    if ($gruppo!=''){
		echo "<h5>Risultati </h5>";
    //$content .="<div style=\"text-align:left;\"><strong>$descr_cons</strong></div><br/>";
		echo "<table>";
		if(count($gruppo))
			for($x=0;$x<count($gruppo);$x++){
		echo "<tr><td class=\"td-big\">&middot;</td><td>".$gruppo[$x]." </td><td  style=\"text-align:right\"><b><span style=\"color:#ff0000;\">".$pro[$x] ."%</span></b></td></tr>\n";
		}
		echo "</table>";
	}
  //echo $content;
}




?>
