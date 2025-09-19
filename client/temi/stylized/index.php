<?php
# stylizzed

include_once("modules/Elezioni/funzioni.php");
include_once("temi/inc/button.php");


########## no blocco x grafici e risultati
if (!isset($param['op'])) $param['op']='';
if($blocco!=1 || $param['op']=="graf_gruppo" || $param['op']=="gruppo_circo" || $param['op']=="gruppo_sezione"
|| $param['op']=="lista_circo" || $param['op']=="lista_sezione"  || $param['op']=="candidato_circo" || $param['op']=="candidato_sezione"
)$blocco=''; else $blocco=1;


function testata(){


global $file,$blocco;

echo "<div align=\"right\">";
/* icone linguaggio */
language();
/* icona flash */
flash();
noblocco();
echo "</div>";



// logo
echo ' <div id="logo">
        <h1><a href="modules.php?name=Elezioni">eleonline</a></h1>
	<h2> <a href="http://www.eleonline.it">consultazioni elettorali on line + by l.apolito e r.gigli</a>
</h2>
</div>
';

// menu
echo '<div id="menu">';
	if ($file=="index") menu();
echo "</div>";

echo '
	<div id="page">';

echo "<table cellpadding=\"0\" cellspacing=\"0\"><tr>";

	 
          	$check=check_block("dx"); // check exist box
		
	if ($blocco=='1' && $check!=0){
		echo "<td valign=\"top\" class=\"sidebar\">";
    		block("dx");
		echo "</td><td>&nbsp;&nbsp;</td><td valign=\"top\">"; 
		
	}else { 
		echo "<td valign=\"top\">";
	}
		
}

function piede(){
	global $blocco;
	$check=check_block("sx"); // check exist box
	if ($blocco=='1' && $check!=0){
		echo "</td><td>&nbsp;&nbsp;</td><td valign=\"top\" class=\"sidebar\">";
    		block("sx");
		
	}
	echo "</td></tr></table>";
        echo "</div>"; #container

}		
		



?>
