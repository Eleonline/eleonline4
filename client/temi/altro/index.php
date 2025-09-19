<?php
# altro

include_once("modules/Elezioni/funzioni.php");
include_once("temi/inc/button.php");

########## no blocco x grafici e risultati
if (!isset($param['op'])) $param['op']='';
if($blocco!=1 || $param['op']=="graf_gruppo" || $param['op']=="gruppo_circo" || $param['op']=="gruppo_sezione"
|| $param['op']=="lista_circo" || $param['op']=="lista_sezione"  || $param['op']=="candidato_circo" || $param['op']=="candidato_sezione"
)$blocco=''; else $blocco=1;

function testata(){
global $file,$blocco;



echo '<table class="logotable">
      <tr>
        <td class="logoleft"></td>
        <td class="logo"><a href="modules.php?name=Elezioni">Eleonline</a></td>
        <td class="logoright"></td>	
        <td class="tag">Consultazioni elettorali</td>
        <td class="tag">';

	language();
	flash();
	noblocco();

echo '  </td>  </tr></table> ';


if ($file=="index") menu();

	echo "<table  class=\"table-main\"><tr>";
	$check=check_block("dx"); // check exist box
		
	if ($blocco=='1' && $check!=0){
		echo "<td valign=\"top\" class=\"sidebar\" style=\"width:220px;\"><table><tr><td>";
    		block("dx");
		echo "</td></tr></table></td><td>&nbsp;&nbsp;</td><td valign=\"top\">"; 
		
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
        

}





?>
