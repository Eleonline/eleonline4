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
if (isset($param['sender_name'])) $sender_name=addslashes($param['sender_name']); else $sender_name="";
if (isset($param['sender_email'])) $sender_email=addslashes($param['sender_email']); else $sender_email="";
if (isset($param['opi'])) $opi=addslashes($param['opi']); else $opi="";
if (isset($param['message'])) $message=addslashes($param['message']); else $message="";

$ip = $_SERVER["REMOTE_ADDR"];
global $adminmail;
echo"<br/>";

$form_block = "
    <center><font class=\"title\"><b><br/>"._FEEDBACKTITLE."</b></font>
    <br/><br/><font class=\"content\">"._FEEDBACKNOTE."</font>
    </center>
    <FORM METHOD=\"post\" ACTION=\"modules.php\">
    <P><strong>"._YOURNAME.":</strong><br/>
    <INPUT class=\"modulo\" type=\"text\" NAME=\"sender_name\" VALUE=\"$sender_name\" SIZE=30></p>
    <P><strong>"._YOUREMAIL.":</strong><br/>
    <INPUT class=\"modulo\" type=\"text\" NAME=\"sender_email\" VALUE=\"$sender_email\" SIZE=30></p>
    <P><strong>"._MESSAGE.":</strong><br/>
    <TEXTAREA class=\"modulo\" NAME=\"message\" COLS=70 ROWS=5 >$message</TEXTAREA></p>
    <INPUT type=\"hidden\" name=\"opi\" value=\"ds\">
    <INPUT type=\"hidden\" name=\"file\" value=\"index\">
    <INPUT type=\"hidden\" name=\"op\" value=\"contatti\">
    <INPUT type=\"hidden\" name=\"name\" value=\"Elezioni\">
    <P><INPUT class=\"modulo-button\" TYPE=\"submit\" NAME=\"submit\" VALUE=\""._SEND."\"></p>
    </FORM>
";


if ($opi != "ds") {
    echo "$form_block";
} elseif ($opi == "ds") {
	$send="";
	$subject="From EleOnLine";
    if ($sender_name == "") {
	$name_err = "<font class=\"message\"><i>"._FBENTERNAME."</i></font><br/><br/>";
	$send = "no";
    } 
    if ($sender_email == "") {
	$email_err = "<font class=\"message\"><i>"._FBENTEREMAIL."</i></font><br/><br/>";
	$send = "no";
    } 
    if ($message == "") {
    	$message_err = "<font class=\"message\"><i>"._FBENTERMESSAGE."</i></font><br/>";
	$send = "no";
    } 
    if ($send != "no") {
    
	$sender_name = strtr($sender_name, "\015\012", ' ');
	
	$sender_email = strtr($sender_email, "\015\012", ' ');
	$msg = "$sitename\n\n";
	$msg .= ""._SENDERNAME.": $sender_name\n";
	$msg .= ""._SENDEREMAIL.": $sender_email\n";
	$msg .= "IP: $ip\n";
	$msg .= "\n\n"._MESSAGE.": $message\n\n";
	$to = $adminmail;
	$mailheaders = "From: $sender_name <$sender_email>\n";
	$mailheaders .= "Reply-To: $sender_email\n\n";
	mail($to, $subject, $msg, $mailheaders);
	echo "<P><center>"._FBMAILSENT."</center></p>";
	echo "<P><center>"._FBTHANKSFORCONTACT."</center></p>";
    } elseif ($send == "no") {
	
	echo "$name_err";
	echo "$email_err";
	echo "$message_err";
	
	echo "<br/><br/>";
	echo "$form_block";  
    } 
}

  


?>
