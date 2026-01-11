<?php
#$tipo_consultazione = 2;
//echo "AVVISO: OP:$op - TIPOCONS: $tipo_cons<br>";
switch ($tipo_cons) {
    case 8:
	case 14:
        header("Location: modules.php?op=80"); // dait_europee.php
        break;
    case 6:
    case 7:
    case 10:
    case 11:
    case 15:
    case 16:
	case 18:
    case 19:
        header("Location: modules.php?op=81"); //dait_politiche.php
        break;
    default:
        header("Location: modules.php?op=79"); 
        break;
		//echo "Tipo consultazione non valido: ".$tipo_consultazione;
}
?>
