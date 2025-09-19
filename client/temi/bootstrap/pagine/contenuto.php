<?php 
if(isset($op) and $op!='gruppo') $pagina=$op;
elseif(isset($_GET['op'])) $pagina = intval($_GET['op']); 
else $pagina=0;
switch ($pagina) {
    case 0:
        include('intro.php');
        break;
	//informazioni generali
    case 1:
       include('comesivota.php');
        break;
    case 2:
	include('numeriutili.php');
        break;
	case 3:
	include('servizi.php');
        break;
	case 4:
	include('linkutili.php');
        break;
	case 5:
	include('datigenerali.php');
        break;
	//Affluenze e Risultati	
	case 11:
	include('affluenza.php');
        break;
	case 12:
        include('votanti.php');
        break;
	case 13:
        include('candidatopersezioni.php');
        break;
	case 14:
        include('listapersezioni.php');
        break;
	case 15:
        include('candidatilistapersezioni.php');
        break;
	case 16:
        include('candidatopercirco.php');
        break;
	case 17:
        include('listapercirco.php');
        break;
	case 18:
        include('candidatilistapercirco.php');
        break;
	case 19:
        include('paginavuota.php');
        break;
	case 21:
        include('affluenzagrafico.php');
        break;
	case 22:
        include('votigrafici.php');
        break;
	case 23:
        include('paginavuota.php');
        break;
	case 24:
        include('paginavuota.php');
        break;
	case 25:
        include('paginavuota.php');
        break;
	case 26:
        include('paginavuota.php');
        break;
	case 27:
        include('datimancanti.php');
        break;
	case 28:
        include('listaecandidatitrasparenza.php');
        break;
	case 29:
        include('referendumpersezioni.php');
        break;
	case 30:
        include('datigeneralisezioni.php');
        break;
	case 31: 
        include('seggi.php');
        break;
		//Grafici
	case 41:
        include('grafici/affluenza.php');
        break;
	case 42:
        include('grafici/votanti.php');
        break;
	case 43:
        include('grafici/votidigruppo.php');
        break;
	case 44:
        include('grafici/votidilista.php');
        break;
	case 51:
	// grafici Referendum
	include('grafici/affluenza_referendum.php');
        break;
	case 52:
	include('grafici/votanti_referendum.php');
        break;
	case 53:
	include('grafici/voti_referendum.php');
        break;
	// altre pagine
	case 50:
	include('privacy.php');
        break;
		case 100:
	include('test.php');
        break;
}
?>