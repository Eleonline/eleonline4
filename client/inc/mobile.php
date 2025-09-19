<?php
# riconoscimento  mobile 
# per Eleonline
# by l.apolito 2012
# www.linuxap.it
#####################################################
/*
	$is_mobile=is_mobile();
	if($is_mobile){
		setcookie ("mobile", "yes", time()+3600, "/");
		$mobile="yes";
	}else{
		setcookie ("mobile", "no", time()+3600, "/"); 
		$mobile="no";	
	}

*/

 # funzione verifica se mobile    

    function is_mobile(){
     
    // returns true if one of the specified mobile browsers is detected
     
    $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
    $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
    $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
    $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
    $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
    $regex_match.=")/i";
    return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }


     
?>

