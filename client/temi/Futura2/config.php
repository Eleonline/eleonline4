<?php
# fa partire subito i risultati in visualizzazione nel tema futura2
# commentare nel caso si voglia la partenza del tema di default
# if ($op=="gruppo")$op="gruppo_mob";

# devisualizz errori
ini_set('display_errors','0');
if(isset($_POST['rss'])) {$rss=intval($_POST['rss']);}
# verifica cambiamento colore 
# usata variabile rss gia esistente 
if($rss==7){$colortheme="a";$_SESSION['colortheme']=$colortheme;}
elseif($rss==2){$colortheme="b";$_SESSION['colortheme']=$colortheme;}
elseif($rss==3){$colortheme="c";$_SESSION['colortheme']=$colortheme;}
elseif($rss==4){$colortheme="d";$_SESSION['colortheme']=$colortheme;}
elseif($rss==5){$colortheme="e";$_SESSION['colortheme']=$colortheme;}
elseif($rss==6) {$colortheme="f";$_SESSION['colortheme']=$colortheme;}

$defcolortheme='f';
if (isset($_SESSION['colortheme'])) $colortheme=$_SESSION['colortheme']; else $colortheme=$defcolortheme;
#elseif($rss==6){$colortheme="f";$_SESSION['colortheme']=$colortheme;}

#colori
#f=arancio;e=azzurro-grigio;d=verde;c=rosso;b=azzurro;a=grigio

# verifica se arriva dalle app iphone e android
# usata la variabile rss=9 nel footer

?>
