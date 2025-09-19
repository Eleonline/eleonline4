<?php
# blog
include_once("modules/Elezioni/funzioni.php");
include_once("temi/inc/button.php");
########## no blocco x grafici e risultati
if (!isset($param['op'])) $param['op']='';
if($blocco!=1 || $param['op']=="graf_gruppo" || $param['op']=="gruppo_circo" || $param['op']=="gruppo_sezione"
|| $param['op']=="lista_circo" || $param['op']=="lista_sezione"  || $param['op']=="candidato_circo" || $param['op']=="candidato_sezione"
)$blocco=''; else $blocco=1;

####################################
function testata(){
####################################

global $file,$blocco;

// logo
echo '
<div id="header">
	<div id="search">&nbsp;';
		language();
		flash();
		noblocco();
	echo '</div>
<h1><a href="modules.php?name=Elezioni">Home Page</a> / <a href="modules.php?name=Elezioni"><b>Elezioni on line</b></a></h1>

</div>
';
// menu

		if ($file=="index") menu();
echo "<table style=\"border-collapse: collapse;\"><tr>";

	 
          	$check=check_block("dx"); // check exist box
		
	if ($blocco=='1' && $check!=0){
		echo "<td valign=\"top\"><div class=\"sidebar\">";
		echo "<div class=\"blocco\"><br />";
    		block("dx");
		echo "<div class=\"fondo\"><br/></div></div>";
		echo "</td><td>&nbsp;&nbsp;</td><td valign=\"top\">"; 
		
	}else { 
		echo "<td valign=\"top\">";
	}
		
}

function piede(){
	global $blocco;
	$check=check_block("sx"); // check exist box
	if ($blocco=='1' && $check!=0){
		echo "</td><td>&nbsp;&nbsp;</td><td valign=\"top\"><div class=\"sidebar\">";
		echo "<div class=\"blocco\"><br />";
    		block("sx");
		echo "<div class=\"fondo\"><br></div></div></div>";
	}
	echo "</td></tr></table>";
        echo "</div>"; #container

}


?>
