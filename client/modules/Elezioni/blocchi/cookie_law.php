<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* cookie law : widget per informativa sui cookie */
/* vers. 1.0 - maggio 2015 by apoluc */
/* ps: la sola funzione puo' essere usata anche in un sito o pagine singole, blog o altro oppure per chi non usa javascript per accessibilità */

#-> la var $varlaw puo' essere dichiarata anche altrove, per esempio in un file di configurazione

$varlaw=array();

#--> lancia la funzione
cookie_law($varlaw);

###############
function cookie_law($varlaw){
	# by apoluc 2015
	#--> configurazione variabili
	# disabilitare tutte nel caso si proceda all'invio della var $varlaw tramite richiamo alla funzione. es:cookie_law($varlaw); 
	global $id_comune,$id_cons_gen,$op,$info;
	$varlaw["testo"]="Questo sito utilizza limitatamente i cookie per questioni tecniche e di funzionalità"; // testo messaggio
	$varlaw["continua"]="Accetta"; // testo bottone accettazione
	$varlaw["info"]="Maggiori info"; // testo bottone info . lasciare vuoto nel caso non si hanno altre info 
	$varlaw["link"]="modules.php?name=Elezioni&id_comune=$id_comune&file=index&id_cons_gen=$id_cons_gen&op=privacy"; // link del doc maggiori info - puo' essere il link anche esterno, o indirizzato ad una pagina html....
	$varlaw["colsfondo"]="#ff0000"; // colore sfondo del messaggio
	$varlaw["coltesto"]="#ffffff"; // colore testo del messaggio
	$varlaw["colbordo"]="#ffffff"; // colore bordo del messaggio
	



	# preleva la var per la registrazione del cookie
	$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
	if (isset($param['c_law'])) $c_law=htmlentities($param['c_law']); else $c_law='';
#	$url_law=$_SERVER['REQUEST_URI']; // url della pagina per il reload	
	$url_law="modules.php?name=Elezioni&id_comune=$id_comune&file=index&id_cons_gen=$id_cons_gen&op=$op"; // url della pagina per il reload	
if($info) $url_law.="&info=$info";
	# verifica e scrive il cookie di avvenuto avviso	
	if($c_law=="ok"){ 
		$value="ok";
		setcookie ("cookie_law", $value,time()+3600*24*365 ); /* verrà cancellato dopo  1anno */
		header("location:$url_law ");

	} 


	#--> verifica se esiste il cookie e stampa l'avviso
	if (isset($_COOKIE ["cookie_law"])){
	}else{
	echo "<div style=\"position:fixed; width:100%; height:30px; background-color:".$varlaw['colsfondo']."; border: 1px solid ".$varlaw['colbordo']."; color:".$varlaw['coltesto']." ; margin:0; left:0; top:0; padding:4px; z-index:1000; text-align:center;\">";

		echo "<table style=\"width:80%;text-align:center;border:none;\">
			<tr>
			   <td style=\"text-align:right;border:none;\"> ".$varlaw['testo']."  
			   </td>
			   <td style=\"text-align:center;border:none;color:".$varlaw['coltesto']."\">  
				<form method=\"post\" data-ajax=\"false\" name=\"ok\" action=\"\">
                                <input type=\"hidden\" name=\"file\" value=\"index\">
                                <input type=\"hidden\" name=\"name\" value=\"Elezioni\">
                                <input type=\"hidden\" name=\"op\" value=\"$op\">
                                <input type=\"hidden\" name=\"info\" value=\"$info\">
                                <input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\">
                                <input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\">

				<input type=\"hidden\" name=\"c_law\" value=\"ok\">
				<input type=\"submit\" value=\"".$varlaw['continua']."\">
				</form>";
			if($varlaw['info']!=''){
				echo "	</td><td style=\"text-align:center;border:none;\">
				<form method=\"get\" data-ajax=\"false\" name=\"info\" action=\"".$varlaw['link']."\">
				<input type=\"hidden\" name=\"file\" value=\"index\">
				<input type=\"hidden\" name=\"name\" value=\"Elezioni\">
				<input type=\"hidden\" name=\"op\" value=\"privacy\">
                                <input type=\"hidden\" name=\"info\" value=\"$info\">
				<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\">
				<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\">
				<input type=\"submit\" value=\"".$varlaw['info']."  \">
				</form>";
			}
		echo " 	</td></tr></table>
		</div>";
	}

} #fine funzione 


#--> Blocco per eleonline
echo "<h5>Privacy</h5>";
global $id_comune,$id_cons_gen,$op,$info;
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['c_law'])) $c_law=addslashes($param['c_law']); else $c_law='';
if (isset($param['info'])) $info=htmlentities($param['info']); 
#if (isset($param['id_comune'])) $id_comune=intval($param['id_comune']);  else $id_comune=-1; #$c_law='';
$url_law="modules.php?file=index&name=Elezioni&op=$op&id_comune=$id_comune&id_cons_gen=$id_cons_gen"; #$_SERVER['REQUEST_URI']; // url della pagina per il reload
if($_SESSION['info']) 
	{
	$info=$_SESSION['info'];
	$url_law.="&info=".$_SESSION['info'];
	}
if($c_law=="ko"){ // azzera i cookie 
	
	setcookie("cookie_law","");
	header("location:$url_law ");
}

echo"<div style=\"text-align:center;\">
	<form method=\"get\" data-ajax=\"false\" name=\"ko\" action=\"modules.php\">		
	<input type=\"hidden\" name=\"c_law\" value=\"ko\">
        <input type=\"hidden\" name=\"file\" value=\"index\">
        <input type=\"hidden\" name=\"name\" value=\"Elezioni\">
        <input type=\"hidden\" name=\"op\" value=\"$op\">
        <input type=\"hidden\" name=\"info\" value=\"$info\">
        <input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\">
        <input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\">
	<input type=\"hidden\" name=\"c_law\" value=\"ko\">
	<input type=\"submit\" value=\"Informazione sulla Privacy\">
	</form>
	</div>";




?>
