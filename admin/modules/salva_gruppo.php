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
if (isset($param['numero'])) $numero=intval($param['numero']); else $numero='';
if (isset($param['id_gruppo'])) $id_gruppo=$param['id_gruppo']; else $id_gruppo='';
if (isset($param['id_circ'])) $id_circ=$param['id_circ']; else $id_circ=0;
if (isset($param['num_circ'])) $num_circ=$param['num_circ']; else $num_circ=0;
if (isset($param['flag_simbolo'])) $flag_simbolo=$param['flag_simbolo']; else $flag_simbolo=0;
if (isset($param['flag_programma'])) $flag_programma=$param['flag_programma']; else $flag_programma=0;
if (isset($param['flag_cv'])) $flag_cv=$param['flag_cv']; else $flag_cv=0;
if (isset($param['flag_cg'])) $flag_cg=$param['flag_cg']; else $flag_cg=0;
global $id_comune,$id_cons_gen,$id_cons,$prefix,$aid,$dbi;

$pathdoc="../client/documenti/$id_comune/$id_cons_gen/";
$pathbak="../client/documenti/backup/$id_comune/$id_cons_gen/";
$nameimg="img_gruppo".$numero."_".str_replace(" ","_",$descrizione).".jpg";
$namecv="cv_gruppo".$numero."_".str_replace(" ","_",$descrizione).".pdf";
$namecg="cg_gruppo".$numero."_".str_replace(" ","_",$descrizione).".pdf";
$nameprg="prg_gruppo".$numero."_".str_replace(" ","_",$descrizione).".pdf";

if (!is_dir($pathdoc."img")) mkdir($pathdoc."img", 0777, true);
if (!is_dir($pathdoc."cv")) mkdir($pathdoc."cv", 0777, true);
if (!is_dir($pathdoc."cg")) mkdir($pathdoc."cg", 0777, true);
if (!is_dir($pathdoc."programmi")) mkdir($pathdoc."programmi", 0777, true);
if (!is_dir($pathbak."img")) mkdir($pathbak."img", 0777, true);
if (!is_dir($pathbak."cv")) mkdir($pathbak."cv", 0777, true);
if (!is_dir($pathbak."cg")) mkdir($pathbak."cg", 0777, true);
if (!is_dir($pathbak."programmi")) mkdir($pathbak."programmi", 0777, true);
//se è presente un nuovo file ed esiste il vecchio quest'ultimo viene spostato in bak e il nuovo lo sostituisce
$nomestemma="";
$expstemma="";
$nomeprg="";
$expprg="";
$nomecv="";
$expcv="";
$nomecg="";
$expcg="";
$preimg="";
$campi="";
if(isset($_FILES['simbolo']['name']) and $_FILES['simbolo']['name']) {
	if(is_file($pathdoc."img/".$nameimg))
		rename($pathdoc."img/".$nameimg,$pathbak."img/".$nameimg);
	$filestemma=$_FILES['simbolo']['tmp_name'];
	$filestemma=imgresize($filestemma);
	file_put_contents($pathdoc."img/".$nameimg, $filestemma);
	$nomestemma=", simbolo = :nameimg";
	$preimg= ", :nameimg";
	$campi.= ",simbolo ";
}
if(isset($_FILES['programma']['name']) and $_FILES['programma']['name']) {
	if(is_file($pathdoc."programmi/".$nameprg)) 
		rename($pathdoc."programmi/".$nameprg,$pathbak."programmi/".$nameprg);
	move_uploaded_file($_FILES['programma']['tmp_name'],$pathdoc."programmi/".$nameprg);
	$nomeprg=", prognome=:nameprg";
	$preimg.=",:nameprg";
	$campi.= ", prognome ";
}
if(isset($_FILES['cv']['name']) and $_FILES['cv']['name']) {
	if(is_file($pathdoc."cv/".$namecv)) 
		rename($pathdoc."cv/".$namecv,$pathbak."cv/".$namecv);
	move_uploaded_file($_FILES['cv']['tmp_name'],$pathdoc."cv/".$namecv);
	$nomecv=", cv=:namecv";
	$preimg.=",:namecv";
	$campi.= ", cv ";
}
if(isset($_FILES['cg']['name']) and $_FILES['cg']['name']) {
	if(is_file($pathdoc."cg/".$namecg)) 
		rename($pathdoc."cg/".$namecg,$pathbak."cg/".$namecg);
	move_uploaded_file($_FILES['cg']['tmp_name'],$pathdoc."cg/".$namecg);
	$nomecg=", cg=:namecg";
	$preimg.=",:namecg";
	$campi.= ", cg ";
}
#####

$salvato=0;
if($op=='cancella_parziale') {
	$cond='';
	if ($flag_simbolo){
		if(is_file($pathdoc."img/".$nameimg))
			rename($pathdoc."img/".$nameimg,$pathbak."img/".$nameimg); else echo "TEST: ".$pathdoc."img/".$nameimg;
		$cond.="simbolo=''";
	}	
	if ($flag_cv) {
		if(is_file($pathdoc."cv".$namecv))
			rename($pathdoc."cv".$namecv,$pathbak."cv".$namecv);
		if(mb_strlen($cond)) $cond.=',';
		$cond.="cv=''";
	}
	if ($flag_cg){
		if(is_file($pathdoc."cg".$namecg))
			rename($pathdoc."cg".$namecg,$pathbak."cg".$namecg);
		if(mb_strlen($cond)) $cond.=',';
		$cond.="cg=''";
	}	
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
		if (is_file($pathdoc."img/".$nameimg))
			rename($pathdoc."img/".$nameimg,$pathbak."img/".$nameimg);
		if (is_file($pathdoc."cv".$namecv))
			rename($pathdoc."cv".$namecv,$pathbak."cv".$namecv);
		if (is_file($pathdoc."cg".$namecg))
			rename($pathdoc."cg".$namecg,$pathbak."cg".$namecg);
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

			$sql="update ".$prefix."_ele_gruppo set descrizione=:descrizione,num_gruppo=:numero $nomestemma $nomeprg $nomecv $nomecg where  id_gruppo=:id_gruppo";
			try {
				$compl = $dbi->prepare("$sql");
				$compl->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
				$compl->bindParam(':numero', $numero, PDO::PARAM_INT);
				if(mb_strlen($nomestemma))
					$compl->bindParam(':nameimg', $nameimg, PDO::PARAM_STR);
				if(mb_strlen($nomeprg))
					$compl->bindParam(':nameprg', $nameprg, PDO::PARAM_STR);
				if(mb_strlen($nomecv))
					$compl->bindParam(':namecv', $namecv, PDO::PARAM_STR);
				if(mb_strlen($nomecg))
					$compl->bindParam(':namecg', $namecg, PDO::PARAM_STR);
				$compl->bindParam(':id_gruppo', $id_gruppo, PDO::PARAM_INT);		
				$compl->execute(); 
			} catch(PDOException $e) {
				echo $e->getMessage();
				$salvato=1;
			}
		}else{
			#insert
			$sql="insert into ".$prefix."_ele_gruppo (id_cons, num_gruppo, descrizione, id_circ, num_circ $campi) values( :id_cons, :numero, :descrizione, :id_circ, :num_circ $preimg )";
			try {
				$compl = $dbi->prepare("$sql");
				$compl->bindParam(':id_cons', $id_cons, PDO::PARAM_INT);
				$compl->bindParam(':numero', $numero, PDO::PARAM_INT);
				$compl->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
				if(mb_strlen($nomestemma))
					$compl->bindParam(':nameimg', $nameimg, PDO::PARAM_STR);
				if(mb_strlen($nomeprg))
					$compl->bindParam(':nameprg', $nameprg, PDO::PARAM_STR);
				if(mb_strlen($nomecv))
					$compl->bindParam(':namecv', $namecv, PDO::PARAM_STR);
				if(mb_strlen($nomecg))
					$compl->bindParam(':namecg', $namecg, PDO::PARAM_STR);
				$compl->bindParam(':id_circ', $id_circ, PDO::PARAM_INT);		
				$compl->bindParam(':num_circ', $num_circ, PDO::PARAM_INT);		
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
include('modules/elenco_gruppi.php');

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
