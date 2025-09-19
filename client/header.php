<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* ultima modfifica: aggiunta rotazione 18 marzo 2009 */

if (stristr($PHP_SELF,"header.php")) {
    Header("Location: index.php");
    die();
}
/*
if(!isset($nocell))$nocell='';
### tema mobile Futura 2 
include("inc/mobile.php"); // riconoscimento mobile
$is_mobile=is_mobile(); 
	if($is_mobile && $nocell!=1){
		$tema="bootstrap";
	}

if(!file_exists("temi/$tema/index.php")) {$tema='default'; $_SESSION['tema']=$tema;} */

if($tema!='bootstrap') include_once("temi/$tema/index.php"); 
include_once("modules/Elezioni/language/lang-$lang.php");

function head(){
	global $csv,$tema,$tour,$sitename,$pagetitle,$simbolo,$siteurl,$siteistat;
#	echo '
#	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
#	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
echo '<!DOCTYPE html><html lang="it">';
	echo "<head>\n";
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" >";
	//echo "<title>Eleonline - Elezioni on line</title>\n";
	echo '<title>'.$sitename.' '.$pagetitle.'</title>'."\n";
	echo "<meta name=\"title\" content=\"$sitename\" >\n";
	echo "<meta name=\"description\" content=\"$pagetitle\" >\n"; 

	if(file_exists("modules/Elezioni/images/$siteistat.png")){     
		echo "<link rel=\"image_src\" href=\"modules/Elezioni/images/$siteistat.png\" >\n"; #img fb
	}else{ 
		echo "<link rel=\"image_src\" href=\"modules/Elezioni/images/logo.gif\" >\n"; #img fb
	}

	if($tema!='bootstrap') 
	{	
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['c_law'])) $c_law=addslashes($param['c_law']); else $c_law='';
if (isset($param['informativa'])) $informativa=addslashes($param['informativa']); else $informativa='';
$url_law=$_SERVER['REQUEST_URI']; // url della pagina per il reload

#die("TEST: $c_law:$url_law:$informativa:");
if($c_law=="ko"){ // azzera i cookie 
	
	setcookie("cook_law","");
	header("location:$url_law ");
}
	# verifica e scrive il cookie di avvenuto avviso	
	if($c_law=="ok"){ 
		$value="ok";
		setcookie ("cook_law", $value,time()+3600*24*365 ); /* verrà cancellato dopo  1anno */
		header("location:$url_law ");

	} elseif($c_law=="info"){ // stampa le info
		header("location:$informativa");
	}

		echo "<link rel=\"stylesheet\" href=\"temi/$tema/style.css\" type=\"text/css\" >\n\n\n";
		if(file_exists("temi/$tema/head.php")) include("temi/$tema/head.php");
		


		include("inc/javascript.php"); # rotazione (18 marzo 2009) tema tour
		##### tema mobile
		if (file_exists("temi/$tema/themeutils.php")) {
		include("temi/$tema/themeutils.php"); #incluso x tema mobile
		}
		
		




		echo "\n\n\n</head>\n";
		# rotazione per tema tour
		if (isset($_SESSION['ruota'])){$csv=1; echo "<body onload=\"loadpage()\"";}
		else echo "<body ";
		if (!$csv) echo " style=\"background-image: url(temi/$tema/images/sfondo.jpg); background-repeat:repeat-x;\"";
		echo " >\n";
		include("inc/authors.php");
		include_once("modules/Elezioni/funzioni.php");
		if (!$csv)
			testata();
	}
}
head();  

?>
