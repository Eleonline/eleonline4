<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}

# tema like-facebook
# for eleonline
include_once("modules/Elezioni/funzioni.php");
########## no blocco x grafici e risultati
if (!isset($param['op'])) $param['op']=''; 
if($blocco!=1 || $param['op']=="graf_gruppo" || $param['op']=="gruppo_circo" || $param['op']=="gruppo_sezione"
|| $param['op']=="lista_circo" || $param['op']=="lista_sezione"  || $param['op']=="candidato_circo" || $param['op']=="candidato_sezione"
)$blocco=''; else $blocco=1;
if(isset($param['visgralista'])) $visgralista=1;

function testata(){
	global $blocco,$file;
	echo "<div id=\"container\" >";
	if ($file=="index") menu();
	
	echo "<table  class=\"table-main\"><tr>";
	$check=check_block("dx"); // check exist box
		
	if ($blocco=='1' && $check!=0){
		echo "<td style=\"vertical-align:text-top;\" class=\"sidebar\">";
    		block("dx");
		echo "</td><td>&nbsp;&nbsp;</td><td style=\"vertical-align:top;\">"; 
		
	}else { 
		echo "<td style=\"vertical-align:top;\">";
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
