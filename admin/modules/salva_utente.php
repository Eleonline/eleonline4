<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/************************************************************************/
/* Modulo salva affluenze                                               */
/* Amministrazione                                                      */
/************************************************************************/
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';

$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['username'])) $username=addslashes($param['username']); else $username='';
if (isset($param['password'])) $password=addslashes($param['password']); else $password='';
if (isset($param['email'])) $email=addslashes($param['email']); else $email='';
if (isset($param['nominativo'])) $nominativo=addslashes($param['nominativo']); else $nominativo='';
if (isset($param['op'])) $op=addslashes($param['op']); else $op='';
if (isset($param['admin'])) $admin=addslashes($param['admin']); else $admin='0';
if($admin == 'true') { $admin=1; $operatore=0;}
else {$admin=0; $operatore=1;}
global $prefix,$aid,$dbi,$id_comune;
$salvato=0;
$query="select * from ".$prefix."_authors where aid='$username'";
$res = $dbi->prepare("$query");
$res->execute();
if($res->rowCount()) {
	if($op=='salva') {
			if($password!='********') {$password=md5($password); $pass=",pwd='$password'"; }else $pass="";
			#update
			$sql="update ".$prefix."_authors set name='$nominativo',adminop='$operatore',admincomune='$admin', email='$email' $pass where aid='$username'";
			$compl = $dbi->prepare("$sql");
			$compl->execute(); 
			if($compl->rowCount()) $salvato=1;
	}elseif($op=='cancella'){
		#delete
		$sql="delete from ".$prefix."_authors where  aid='$username'";
		$compl = $dbi->prepare("$sql");
		$compl->execute(); 
		if($compl->rowCount()) $salvato=1;
	}
}else{
	#insert
		$sql="insert into ".$prefix."_authors (aid, name, id_comune, email, pwd, adminop, admincomune, admlanguage) values( '$username','$nominativo','$id_comune','$email','$password','$operatore','$admin','it')";
		$compl = $dbi->prepare("$sql");
		$compl->execute(); 
		if($compl->rowCount()) $salvato=1;
}

if($salvato){
	$datal=date('Y-m-d');
	$orariol=date(' H:i:s');
	$riga=addslashes($sql);
	$sqlog="insert into ".$prefix."_ele_log values('$id_cons','0','$aid','$datal','$orariol','','$riga','".$prefix."_ele_comune')";
	$res = $dbi->prepare("$sqlog");
	$res->execute();
#		echo "Nuovo orario di rilevazione inserito";
}else{
	echo "<tr><td colspan=\"8\">Errore, impossibile salvare i dati - $sql</td></tr>";
}
include('modules/elenco_utenti.php');

?>
