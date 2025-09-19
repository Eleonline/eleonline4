<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
if (isset($tema) and $tema=='bootstrap') die();
global $csv;

	if (!$csv)
		piede();
$PHP_SELF=$_SERVER['PHP_SELF'];
if (stristr($PHP_SELF,"footer.php")) {
    Header("Location: index.php");
    die();
}
include("versione.php");
echo "<table class=\"bgfooter\"><tr align=\"center\"><td>
<div> "._INVIOSEGN." <a href=\"modules.php?name=Elezioni&amp;file=index&amp;op=contatti\"> "._CLICCAQUI."</a> "; 

 
if(isset($is_mobile) and $is_mobile) //variabile presa dall'header per il riconoscimento del mobile
echo "<br /><h1><a href=\"modules.php?name=Elezioni&nocell=0\">Versione Mobile - Smartphone e Tablet</a></h1>";


/************************************************************************/
/*    LE SEGUENTI LINEE DI PROGRAMMA NON DEVONO ESSERE MODIFICATE       */
/************************************************************************/

 echo "<br />[<a target=\"_blank\" href=\"http://www.eleonline.it\"><b>Eleonline $versione</b></a> - "._GESRIS." ]<br />
	<!-- <a href=\"modules.php?name=Elezioni&amp;op=evvai\">Gruppo di lavoro "._COMUNE."  $descr_com</a> -->
<br /><br /></div>";
//global $id_comune;

if($id_comune!="58047"){

echo '

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
}
echo '</td></tr></table>';
echo "</body>\n"
."</html>";
die();
?>