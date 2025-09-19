<?php
# configurazione blocco privacy
# $url=$_SERVER['REQUEST_URI']; // url della pagina per il reload#

#### modifica le variabili
$js_law=''; // 1= uso con javascript e 0= senza javascript
# $scegli_info=''; // scelta dell'informativa da proporre

global $name, $id_comune, $id_cons_gen;

$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
#if (isset($_GET['name']) && isset($_GET['id_comune']) && isset($_GET['id_cons_gen']) && $_GET['name'] && $_GET['id_comune'] && $_GET['id_cons_gen']) {
	
$informativa = parse_url($base_url, PHP_URL_SCHEME) . "://" . parse_url($base_url, PHP_URL_HOST) . parse_url($base_url, PHP_URL_PATH) . "?op=privacy&name=$name&id_comune=$id_comune&id_cons_gen=$id_cons_gen";
#}
#else {
#	$informativa = parse_url($base_url, PHP_URL_SCHEME) . "://" . parse_url($base_url, PHP_URL_HOST) . parse_url($base_url, PHP_URL_PATH) . "?op=privacy&name=Elezioni";

#}

/* Variabili formattazione del blocco di informativa senza javascript */

$varlaw=array();
$varlaw["testo"]="Questo sito utilizza limitatamente i cookie per questioni tecniche e di funzionalità"; // testo messaggio
$varlaw["continua"]="Accetta"; // testo bottone accettazione
$varlaw["info"]="Maggiori info"; // testo bottone info . lasciare vuoto nel caso non si hanno altre info 
$varlaw["link"]=$informativa; // link del doc maggiori info - puo' essere il link anche esterno, o indirizzato ad una pagina html....
$varlaw["colsfondo"]="#ff0000"; // colore sfondo del messaggio
$varlaw["coltesto"]="#ffffff"; // colore testo del messaggio
$varlaw["colbordo"]="#ffffff"; // colore bordo del messaggio

/*
Per cambiare i file di informazione andare su modules/Elezioni/blocchi/privacy
Il file incluso nel tema è il fine privacy_ele.html
*/


?>
