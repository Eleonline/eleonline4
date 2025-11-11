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
if (isset($param['descrizione'])) $descrizione=addslashes($param['descrizione']); else $descrizione='';
if (isset($param['op'])) $op=addslashes($param['op']); else $op='';
if (isset($param['indirizzo'])) $indirizzo=addslashes($param['indirizzo']); else $indirizzo='';
if (isset($param['cap'])) $cap=addslashes($param['cap']); else $cap='';
if (isset($param['email'])) $email=addslashes($param['email']); else $email='';
if (isset($param['centralino'])) $centralino=addslashes($param['centralino']); else $centralino='';
if (isset($param['fax'])) $fax=addslashes($param['fax']); else $fax='';
if (isset($param['fascia'])) $fascia=intval($param['fascia']); else $fascia='0';
if (isset($param['id_comune'])) $id_comune=addslashes($param['id_comune']); else $id_comune='';
if (isset($param['capoluogo'])) $capoluogo=intval($param['capoluogo']); else $capoluogo='0';
#if (isset($param['predefinito'])) $predefinito=addslashes($param['predefinito']); else $predefinito='';

$stemmanome=''; $stemmablob='';
$cond2=''; 
$cond3='';
if(isset($_FILES['stemma'])) {
	$STEMM=$_FILES['stemma'];
	$filestemma=$STEMM['tmp_name'];
	$nomestemma=$STEMM['name'];
	#### Controllo della dimensione del file immagine
	if ($filestemma){
		$filestemma=imgresize($filestemma);
		$stemmablob=addslashes($filestemma);
		$stemmanome=addslashes($nomestemma);
		$cond2=", simbolo='$stemmanome', stemma='$stemmablob'";
		$cond3="and simbolo='$stemmanome' and stemma='$stemmablob'";
	} else {
	#		if ( $delsimb=='false') $cond2=", simbolo='', stemma=''"; # aggiungere controllo per eliminazione stemma
	#		else 
				$cond2='';
				$cond3='';
	}
}


global $prefix,$id_parz,$tempo,$username,$aid,$dbi,$genere;
$salvato=0;
$query="select * from ".$prefix."_ele_comune where id_comune='$id_comune'";
$res = $dbi->prepare("$query");
$res->execute();
if($res->rowCount()) {
	if($op=='salva') {
		$sql="select * from ".$prefix."_ele_comune where descrizione='$descrizione' and indirizzo='$indirizzo' and cap='$cap' and email='$email' and centralino='$centralino' and fax='$fax' and fascia='$fascia' and capoluogo='$capoluogo' $cond3";
		$res = $dbi->prepare("$sql");
		$res->execute();
		if(!$res->rowCount()) {
			#update
			$sql="update ".$prefix."_ele_comune set descrizione='$descrizione',indirizzo='$indirizzo',cap='$cap',email='$email',centralino='$centralino',fax='$fax',fascia='$fascia',capoluogo='$capoluogo' $cond2 where  id_comune='$id_comune'";
			$compl = $dbi->prepare("$sql");
			$compl->execute(); 
			if($compl->rowCount()) $salvato=1;
		}else $salvato=1;
	}elseif($op=='cancella'){
		#delete
		$sql="delete from ".$prefix."_ele_comune where  id_comune='$id_comune'";
		$compl = $dbi->prepare("$sql");
		$compl->execute(); 
		if($compl->rowCount()) $salvato=1;
	}
}else{
	#insert
		$sql="insert into ".$prefix."_ele_comune values( '$id_comune','$descrizione','$indirizzo','$centralino','$fax','$email','$fascia','$capoluogo','$stemmanome','$stemmablob','0','$cap','')";
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
include('modules/elenco_comuni.php');

function imgresize($file) {
    $source_pic = $file;
    $max_width = 100;
    $max_height = 100;
    list($width, $height, $image_type) = getimagesize($file);
    switch ($image_type)
    {
        case 1: $src = imagecreatefromgif($file); break;
        case 2: $src = imagecreatefromjpeg($file);  break;
        case 3: $src = imagecreatefrompng($file); break;
        default: return '';  break;
    }
    $x_ratio = $max_width / $width;
    $y_ratio = $max_height / $height;
    if( ($width <= $max_width) && ($height <= $max_height) ){
        $tn_width = $width;
        $tn_height = $height;
        }elseif (($x_ratio * $height) < $max_height){
            $tn_height = ceil($x_ratio * $height);
            $tn_width = $max_width;
        }else{
            $tn_width = ceil($y_ratio * $width);
            $tn_height = $max_height;
    }
    $tmp = imagecreatetruecolor($tn_width,$tn_height);
    /* Controllo della trasparenza*/
    if(($image_type == 1) OR ($image_type==3))
    {
        imagealphablending($tmp, false);
        imagesavealpha($tmp,true);
        $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
        imagefilledrectangle($tmp, 0, 0, $tn_width, $tn_height, $transparent);
    }
    imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);
    ob_start();
    switch ($image_type)
    {
        case 1: imagegif($tmp); break;
        case 2: imagejpeg($tmp, NULL, 100);  break; // best quality
        case 3: imagepng($tmp, NULL, 0); break; // no compression
        default: echo ''; break;
    }
    $final_image = ob_get_contents();
    ob_end_clean();
    return $final_image;
}

?>
