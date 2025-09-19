<?php
# tema default
# for eleonline
include_once("modules/Elezioni/funzioni.php");
include_once("temi/inc/button.php");


########## no blocco x grafici e risultati
if (!isset($param['op'])) $param['op']='';
if($blocco!=1 || $param['op']=="graf_gruppo" || $param['op']=="gruppo_circo" || $param['op']=="gruppo_sezione"
|| $param['op']=="lista_circo" || $param['op']=="lista_sezione"  || $param['op']=="candidato_circo" || $param['op']=="candidato_sezione"
)$blocco=''; else $blocco=1;

function testata(){
global $tema,$file,$sitename,$blocco;
echo "<div id=\"container\" >";
 echo '	<a href="modules.php?name=Elezioni">
			<img  class="nobordo" src="temi/'.$tema.'/images/logo.gif" alt="$sitename" width="762" height="89" />
      			</a><br />';
// bottoni
language();flash();noblocco();





	
	
	if ($file=="index") menu();

	echo "<table class=\"table-main\"><tr>";
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
