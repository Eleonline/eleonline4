<?php
/************************************************************************/
/* Condivisione   	  	                                        */
/* blocco 2015                                                                     */
/************************************************************************/
echo '<h5>Condividi</h5>';

global $descr_com,$descr_cons,$simbolo;

$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$url = htmlentities(urlencode($url));



echo "
<div id=\"share-buttons\" style=\"text-align:center;\">
 
<!-- Facebook -->
<a href=\"http://www.facebook.com/sharer.php?t=modules/Elezioni/images/$simbolo&amp;u=$url\" target=\"_blank\"><img src=\"modules/Elezioni/images/facebook.png\" alt=\"Facebook\" /></a>
 

<!-- Twitter -->

<!-- Twitter -->
<a href=\"http://twitter.com/share?url=$url&amp;text=$descr_com $descr_cons&amp;hashtags=Eleonline\" target=\"_blank\"><img src=\"modules/Elezioni/images/twitter.png\" alt=\"Twitter\" /></a>
 

 
<!-- Google+ -->
<a href=\"https://plus.google.com/share?url=$url\" target=\"_blank\"><img src=\"modules/Elezioni/images/google.png\" alt=\"Google\" /></a>";
 

echo "</div>";



