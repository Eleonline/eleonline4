<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
global $currentUserRole;
$row=elenco_utenti();
# ciclo ancora da adattare
foreach($row as $key=>$val){
	$key++;
	echo "<tr id=\"riga$key\"><td id=\"username$key\">".$val['aid']."</td><td style=\"display:none;\" id=\"admin$key\">".$val['admincomune']."</td><td style=\"display:none;\" id=\"password$key\">".$val['pwd']."</td><td id=\"email$key\">".$val['email']."</td><td id=\"nominativo$key\">".$val['name']."</td><td><button class=\"btn btn-sm btn-warning me-1\" onclick=\"editUser($key)\">Modifica</button>";
	if($currentUserRole != 'operatore' and $val['adminsuper']!=1 and $val['admincomune']!='1') echo "<button class=\"btn btn-sm btn-danger\" onclick=\"deleteUser($key)\">Elimina</button>"; echo"</td></tr>";
} #die("TEST:  --- ".count($row));

?>
