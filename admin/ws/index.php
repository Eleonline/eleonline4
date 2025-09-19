<?php
define('ADMIN_FILE','');
if (!defined('ADMIN_FILE')) {
    die ("You can't access this file directly...");
}
#include("modules/Elezioni/ele.php");
#ele();
#OpenTable();
include('config.php');
global $funzione,$p12File;
$funzione=$_GET['funzione'];
if(strlen($funzione)>0){ 
	include "WSSoapClient.php";
$operazione=$funzione;
#die("QUI".$p12File);
	//$wsdl = __DIR__ . '/../wsdl/baseWsPort.wsdl';
	switch ($operazione) {
		case 'recuperaEventiElettorali' :
		case 'recuperaInfoAreaAcquisizione' :
			$wsdl='https://elettoralews.preprod.interno.it/ServiziSielWSBase/baseWsPort?wsdl';
			break;
		case 'recuperaInfoAreaAcquisizioneSezioniElettori':
		case 'inviaModificaSezioniElettori':
			$wsdl='https://elettoralews.preprod.interno.it/ServiziSielWSSezioniElettori/sezioniElettoriWsWSDLSOAP?wsdl';
			break;
	}
	$options = ['trace' => 1, 'cache_wsdl' => 0,'soap_version' => 1];
#	$p12File = $certws; 
#	$p12Password = $certpsw;
	include('funzioni/chiamate.php');
$utente=base64_encode($userws);
$userpass=$passws;	
	$chiamata = "['utente' => ['UserID'=>$utente, 'Password'=>$userpass], 'dataElezione'=>$data]";
	
	$client = new WSSoapClient($wsdl, $options, $p12File, $p12Password);
	try {
		switch ($operazione) {
		case 'recuperaEventiElettorali' :
			include('funzioni/chiama_'.$operazione.'.php');
#			$response = $client->$operazione(['utente' => ['UserID'=>$utente,'Password'=>($userpass)], 'dataElezione'=>"$data"]);
#			$response = $client->$operazione("$chiamata");
			break;
		case 'recuperaInfoAreaAcquisizione' :
			include('funzioni/chiama_'.$operazione.'.php');
			break;
		case 'recuperaInfoAreaAcquisizioneSezioniElettori': 
			include('funzioni/chiama_'.$operazione.'.php');
#			$response = $client->$operazione(['utente' => ['UserID'=>$utente,'Password'=>$userpass], 'evento' => ['TipoElezione'=>$tipo,'DataElezione'=>$data,'DescrizioneElezione'=>$descrizione]]);
			break;
#			$response = $client->$operazione(['utente' => ['UserID'=>$utente,'Password'=>($userpass)], 'evento' => ['TipoElezione'=>'9','DataElezione'=>'3025-06-05','DescrizioneElezione'=>'Referendum']]);
#			break;
		case 'inviaModificaSezioniElettori' :
			include('funzioni/chiama_'.$operazione.'.php');
			break;
			
		}
	
	}
	catch(SoapFault $e) {
		echo("<br><br>".$e);
		//echo($e->getTrace()[0]['args'][0]);
	}
print_r($response);
	include("funzioni/$operazione.php");

}else include('funzioni/riepilogo.php');
#	CloseTable();
#	include("footer.php");
?>
