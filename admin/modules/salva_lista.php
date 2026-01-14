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
if (isset($param['id_lista'])) $id_lista=$param['id_lista']; else $id_lista='';
if (isset($param['id_gruppo'])) $id_gruppo=$param['id_gruppo']; else $id_gruppo=0;
if (isset($param['num_gruppo'])) $num_gruppo=$param['num_gruppo']; else $num_gruppo=0;
if (isset($param['flag_simbolo'])) $flag_simbolo=addslashes($param['flag_simbolo']); else $flag_simbolo=0;
global $id_comune,$id_cons_gen,$id_cons,$prefix,$aid,$dbi;

$pathdoc="../client/documenti/$id_comune/$id_cons/";
$pathbak="../client/documenti/backup/$id_comune/$id_cons/";
$nameimg="img_lista".$numero."_".str_replace(" ","_",$descrizione).".jpg";

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

#####

$salvato=0;
if($op=='cancella_parziale' and $flag_simbolo==1) {
	if (is_file($pathdoc."img/".$nameimg))
		rename($pathdoc."img/".$nameimg,$pathbak."img/".$nameimg);
	$sql="update ".$prefix."_ele_lista set simbolo='' where id_lista=:id_lista";
	$sql2=$sql;
	$compl = $dbi->prepare("$sql");
	try {
	$compl->bindParam(':id_lista', $id_lista, PDO::PARAM_INT);
	$compl->execute();
	} catch(PDOException $e) {
		echo $e->getMessage();
		$salvato=1;
	}
	
}else{
	if($op=='cancella') {
		if (is_file($pathdoc."img/".$nameimg))
			rename($pathdoc."img/".$nameimg,$pathbak."img/".$nameimg);
		$sql="delete from ".$prefix."_ele_lista where id_lista=:id_lista";
		$sql2=$sql;
		$compl = $dbi->prepare("$sql");
		try {
		$compl->bindParam(':id_lista', $id_lista, PDO::PARAM_INT);
		$compl->execute();
		} catch(PDOException $e) {
			echo $e->getMessage();
			$salvato=1;
		}
	}else{
		$query="select * from ".$prefix."_ele_lista where id_lista=:id_lista";
		$res = $dbi->prepare("$query");
		$res->execute([
			':id_lista' => $id_lista
		]);
		if($res->rowCount()) { //update

			$sql="update ".$prefix."_ele_lista set descrizione=:descrizione,num_lista=:numero,id_gruppo=:id_gruppo,num_gruppo=:num_gruppo $nomestemma where  id_lista=:id_lista";
			try {
				$compl = $dbi->prepare("$sql");
				$compl->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
				$compl->bindParam(':numero', $numero, PDO::PARAM_INT);
				$compl->bindParam(':id_gruppo', $id_gruppo, PDO::PARAM_INT);
				$compl->bindParam(':num_gruppo', $num_gruppo, PDO::PARAM_INT);
				if(mb_strlen($nomestemma))
					$compl->bindParam(':nameimg', $nameimg, PDO::PARAM_STR);
				$compl->bindParam(':id_lista', $id_lista, PDO::PARAM_INT);		
				$compl->execute(); 
			} catch(PDOException $e) {
				echo $e->getMessage();
				$salvato=1;
			}
		}else{
			#insert
			$sql="insert into ".$prefix."_ele_lista (id_cons, num_lista, descrizione, id_gruppo, num_gruppo $campi) values( :id_cons, :numero, :descrizione, :id_gruppo, :num_gruppo $preimg )";
			try {
				$compl = $dbi->prepare("$sql");
				$compl->bindParam(':id_cons', $id_cons, PDO::PARAM_INT);
				$compl->bindParam(':numero', $numero, PDO::PARAM_INT);
				$compl->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
				if(mb_strlen($nomestemma))
					$compl->bindParam(':nameimg', $nameimg, PDO::PARAM_STR);
				$compl->bindParam(':id_gruppo', $id_gruppo, PDO::PARAM_INT);		
				$compl->bindParam(':num_gruppo', $num_gruppo, PDO::PARAM_INT);		
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
include('modules/elenco_liste.php');

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
