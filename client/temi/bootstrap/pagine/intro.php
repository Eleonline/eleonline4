<?php 
//if(isset($_GET['op'])) $pagina = $_GET['op']; else $pagina=1;
global $intro;
switch ($intro) {
	//affluenza
    case 1:
       include('listaecandidati.php');
        break;
	//spoglio
    case 2:
	include('spoglio.php');
        break;
	//risultati
	case 3:
	include('risultati.php');
        break;
	//risultati
	case 4:
	include('referendum_risultati.php');
        break;
}
?>