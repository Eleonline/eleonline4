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

//$blocco=0;

# citta' di Guidonia Montecelio

global $id_comune;
if($id_comune=="58047") $filename="guidonia.html";
else $filename="gruppo.html"; # per altre città inserire nella variabile $filename il nome del file html messo in pagine/

//$filename="$nomefile.hml";

if (!isset($filename)) {  Header("Location: index.php"); }

// verifica se la pagina e' un html
	if(substr($filename,-4)!=".htm" && substr($filename,-5)!=".html"){
		echo "Autorizzazione negata...";
		echo "<hr>"._GOBACK."";
	
	}elseif ( substr($filename,0,1)!="." && substr($filename,0,4)!="http" ){
  		//echo substr($file,-4);
		include ("pagine/$filename");
		echo "<hr />"._GOBACK."";
  		
	}else {
		echo "Autorizzazione negata...";
		echo "<hr>"._GOBACK."";
		
	}



?>
