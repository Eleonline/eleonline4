<?php
/*
Widget realizzato da Daniele Margotti
Funzioni Javascript prese da http://www.cookiechoices.org/
*/
include("modules/Elezioni/blocchi/privacy/config.php"); 

if($js_law=="1"){ // verifica js
?>
	<script src="modules/Elezioni/blocchi/privacy/cookiechoices.js"></script>";
	<script>
  	document.addEventListener('DOMContentLoaded', function(event) {
    	cookieChoices.showCookieConsentBar("Il nostro sito utilizza i cookie per rendere migliore la tua esperienza di navigazione. Continuando la navigazione accetti 		l'utilizzo dei cookie secondo quanto descritto nell'",
        'Chiudi', 'Informativa', '<?php echo $informativa;?>');
  });
	</script>
<noscript>



<?php
}

######################################################################
/* cookie law : widget per informativa sui cookie senza javascript */
/* vers. 1.0 - maggio 2015 by apolito luciano */
       
	# preleva la var per la registrazione del cookie
	$par=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
	if (isset($par['c_law'])) $c_law=$par['c_law']; else $c_law='';
	//$c_law='';
	//$c_law = $_POST['c_law'] ;
	$url_law=$_SERVER['REQUEST_URI']; // url della pagina per il reload	



	#--> verifica se esiste il cookie e stampa l'avviso
	if (isset($_COOKIE ["cook_law"])){
	}else{
	echo "<div style=\"position:fixed; width:100%; height:30px; background-color:".$varlaw['colsfondo']."; border: 1px solid ".$varlaw['colbordo']."; color:".$varlaw['coltesto']." ; margin:0; left:0; top:0; padding:4px; z-index:1000; text-align:center;\">";

		echo "<table style=\"width:80%;text-align:center;border:none;\">
			<tr>
			   <td style=\"text-align:right;border:none;color:".$varlaw['coltesto']."\"> ".$varlaw['testo']."  
			   </td>
			   <td style=\"text-align:center;border:none;\">  
				<form method=\"post\" name=\"ok\" action=\"\">		
				<input type=\"hidden\" name=\"c_law\" value=\"ok\">
				<input type=\"submit\" value=\"".$varlaw['continua']."\">
				</form>";
			if($varlaw['info']!=''){
				echo "	</td><td style=\"text-align:center;border:none;\">
				<form method=\"post\"  name=\"info\" action=\"\">
				<input type=\"hidden\" name=\"c_law\" value=\"info\">
				<input type=\"hidden\" name=\"informativa\" value=\"$informativa\">
				<input type=\"submit\" value=\"".$varlaw['info']."  \">
				</form></form>";
			}
		echo " 	</td></tr></table>
		</div>";
	}

if($js_law=="1") echo "</noscript>";

#--> Blocco per eleonline
echo "<h5>Privacy</h5>";

echo"<div style=\"text-align:center;\">
	<form method=\"post\" name=\"ko\" action=\"\">		
	<input type=\"hidden\" name=\"c_law\" value=\"ko\">
	<input type=\"submit\" value=\"Informazione sulla Privacy\">
	</form>
	</div>";

?>




