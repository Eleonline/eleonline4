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
if (isset($param['descrizione'])) $descrizione=$param['descrizione']; else $descrizione='';
if (isset($param['op'])) $op=$param['op']; else $op='';
if (isset($param['numero'])) $numero=intval($param['numero']); else $numero='0';
if (isset($param['id_gruppo'])) $id_gruppo=$param['id_gruppo']; else $id_gruppo='0';
if (isset($param['id_colore'])) $id_colore=$param['id_colore']; else $id_colore='0';
if (isset($param['flag_fac-simile'])) $flag_programma=$param['flag_fac-simile']; else $flag_programma=0;
global $id_comune,$id_cons_gen,$id_cons,$prefix,$aid,$dbi;
$id_circ=0; $num_circ=0;
$descrcorta=strtoupper((substr($descrizione,0,50)));
$pathdoc="../client/documenti/$id_comune/$id_cons/";
$pathbak="../client/documenti/backup/$id_comune/$id_cons/";
$nameimg="img_gruppo".$numero."_".str_replace(" ","_",$descrcorta).".jpg";
$nameprg="prg_gruppo".$numero."_".str_replace(" ","_",$descrcorta).".pdf";

if (!is_dir($pathdoc."img")) mkdir($pathdoc."img", 0777, true);
if (!is_dir($pathdoc."cv")) mkdir($pathdoc."cv", 0777, true);
if (!is_dir($pathdoc."cg")) mkdir($pathdoc."cg", 0777, true);
if (!is_dir($pathdoc."programmi")) mkdir($pathdoc."programmi", 0777, true);
if (!is_dir($pathbak."img")) mkdir($pathbak."img", 0777, true);
if (!is_dir($pathbak."cv")) mkdir($pathbak."cv", 0777, true);
if (!is_dir($pathbak."cg")) mkdir($pathbak."cg", 0777, true);
if (!is_dir($pathbak."programmi")) mkdir($pathbak."programmi", 0777, true);
//se è presente un nuovo file ed esiste il vecchio quest'ultimo viene spostato in bak e il nuovo lo sostituisce
$nomeprg="";
$expprg="";
$preimg="";
$campi="";

if(isset($_FILES['filepdf']['name']) and $_FILES['filepdf']['name']) {
	if(is_file($pathdoc."programmi/".$nameprg)) 
		rename($pathdoc."programmi/".$nameprg,$pathbak."programmi/".$nameprg);
	move_uploaded_file($_FILES['filepdf']['tmp_name'],$pathdoc."programmi/".$nameprg);
	$nomeprg=", prognome=:nameprg";
//	$preimg.=",:nameimg";
	$campi.= ", prognome ";
}

#####
$salvato=0;
if($op=='cancella_parziale') {
	$cond='';
	if ($flag_programma){
		if(is_file($pathdoc."programmi".$nameprg))
			rename($pathdoc."programmi".$nameprg,$pathbak."programmi".$nameprg);
		if(mb_strlen($cond)) $cond.=',';
		$cond.="prognome=''";
	}
	$sql="update ".$prefix."_ele_gruppo set $cond where id_gruppo=:id_gruppo";
	$sql2=$sql;
	$compl = $dbi->prepare("$sql");
	try {
	$compl->bindParam(':id_gruppo', $id_gruppo, PDO::PARAM_INT);
	$compl->execute();
	} catch(PDOException $e) {
		echo $e->getMessage();
		$salvato=1;
	}
}else{
	if($op=='cancella') {
		$query="select * from ".$prefix."_ele_voti_ref where id_gruppo=:id_gruppo";
		$res = $dbi->prepare("$query");
		$res->execute([
			':id_gruppo' => $id_gruppo
		]);
		if (is_file($pathdoc."programmi".$nameprg))
			rename($pathdoc."programmi".$nameprg,$pathbak."programmi".$nameprg);
		$sql="delete from ".$prefix."_ele_gruppo where id_gruppo=:id_gruppo";
		$sql2=$sql;
		$compl = $dbi->prepare("$sql");
		try {
		$compl->bindParam(':id_gruppo', $id_gruppo, PDO::PARAM_INT);
		$compl->execute();
		} catch(PDOException $e) {
			echo $e->getMessage();
			$salvato=1;
		}
	}else{
		$query="select * from ".$prefix."_ele_gruppo where id_gruppo=:id_gruppo";
		$res = $dbi->prepare("$query");
		$res->execute([
			':id_gruppo' => $id_gruppo
		]);
		if($res->rowCount()) { //update
			$query="select * from ".$prefix."_ele_gruppo where id_cons=:id_cons and num_gruppo=:num_gruppo and id_gruppo!=:id_gruppo";
			$res = $dbi->prepare("$query");
			$res->execute([
				':id_cons' => $id_cons,
				':num_gruppo' => $numero,
				':id_gruppo' => $id_gruppo
			]);
			$row=$res->fetch(PDO::FETCH_BOTH);
			if($res->rowCount()) { echo "1"; return;}
			$sql="update ".$prefix."_ele_gruppo set descrizione=:descrizione,num_gruppo=:numero,id_colore=:id_colore $nomeprg where  id_gruppo=:id_gruppo";
			try {
				$compl = $dbi->prepare("$sql");
				$compl->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
				$compl->bindParam(':numero', $numero, PDO::PARAM_INT);
				if(mb_strlen($nomeprg))
					$compl->bindParam(':nameprg', $nameprg, PDO::PARAM_STR);
				$compl->bindParam(':id_gruppo', $id_gruppo, PDO::PARAM_INT);		
				$compl->bindParam(':id_colore', $id_colore, PDO::PARAM_INT);		
				$compl->execute(); 
			} catch(PDOException $e) {
				echo $e->getMessage();
				$salvato=1;
			} echo "TEST: id_colore==$id_colore";
		}else{
			$query="select * from ".$prefix."_ele_gruppo where id_cons=:id_cons and num_gruppo=:num_gruppo and id_gruppo!=:id_gruppo";
			$res = $dbi->prepare("$query");
			$res->execute([
				':id_cons' => $id_cons,
				':num_gruppo' => $numero,
				':id_gruppo' => $id_gruppo
			]);
			$row=$res->fetch(PDO::FETCH_BOTH);
			if($res->rowCount()) { echo "1"; return;}
			#insert
			$sql="insert into ".$prefix."_ele_gruppo (id_cons, num_gruppo, descrizione, id_circ, num_circ, id_colore $campi) values( :id_cons, :numero, :descrizione, :id_circ, :num_circ, :id_colore $nomeprg )";
			try {
				$compl = $dbi->prepare("$sql");
				$compl->bindParam(':id_cons', $id_cons, PDO::PARAM_INT);
				$compl->bindParam(':numero', $numero, PDO::PARAM_INT);
				$compl->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
//				if(mb_strlen($nomestemma))
//					$compl->bindParam(':nameimg', $nameimg, PDO::PARAM_STR);
				if(mb_strlen($nomeprg))
					$compl->bindParam(':nameprg', $nameprg, PDO::PARAM_STR);
				$compl->bindParam(':id_circ', $id_circ, PDO::PARAM_INT);		
				$compl->bindParam(':num_circ', $num_circ, PDO::PARAM_INT);		
				$compl->bindParam(':id_colore', $id_colore, PDO::PARAM_INT);		
				$compl->execute();
			} catch(PDOException $e) {
				echo $e->getMessage();
				$salvato=1;
			}
		}
	}
}
if(!$salvato){
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
include('modules/elenco_referendum.php');

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
	imagejpeg($tmp, NULL, 100); // break; // best quality
    $final_image = ob_get_contents();
    ob_end_clean();
    return $final_image;
}

?>
