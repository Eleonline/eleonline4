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
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ?
        $_GET : $_POST;
if (isset($param['pos'])) $cur=$param['pos']; else $cur='';
$xml=$_SESSION[$cur];
unset($_SESSION[$cur]);
echo $xml;	
?>
