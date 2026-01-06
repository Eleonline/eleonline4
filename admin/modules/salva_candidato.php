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
global $id_lista;
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['cognome'])) $cognome=$param['cognome']; else $cognome='';
if (isset($param['nome'])) $nome=$param['nome']; else $nome='';
if (isset($param['op'])) $op=$param['op']; else $op='';
if (isset($param['numero'])) $numero=intval($param['numero']); else $numero='';
if (isset($param['id_candidato'])) $id_cand=$param['id_candidato']; else $id_cand='';
if (isset($param['id_lista'])) $id_lista=$param['id_lista']; else $id_lista=0;
if (isset($param['num_lista'])) $num_lista=$param['num_lista']; else $num_lista=0;
if (isset($param['flag_cv'])) $flag_cv=addslashes($param['flag_cv']); else $flag_cv=0;
if (isset($param['flag_cg'])) $flag_cg=addslashes($param['flag_cg']); else $flag_cg=0;
global $id_comune,$id_cons_gen,$id_cons,$prefix,$aid,$dbi;
if($op=='aggiorna') {
	$_SESSION['id_lista']=$id_lista;
	include('modules/elenco_candidati.php');
	return;
}
$pathdoc=getcwd()."/../client/documenti/$id_comune/$id_cons_gen/";
$pathbak=getcwd()."/../client/documenti/backup/$id_comune/$id_cons_gen/";
$namecv="cv_candidato".$numero."_".str_replace(" ","_","$cognome"."$nome").".pdf";
$namecg="cg_candidato".$numero."_".str_replace(" ","_",$cognome.$nome).".pdf";

if (!is_dir($pathdoc."img")) mkdir($pathdoc."img", 0777, true);
if (!is_dir($pathdoc."cv")) mkdir($pathdoc."cv", 0777, true);
if (!is_dir($pathdoc."cg")) mkdir($pathdoc."cg", 0777, true);
if (!is_dir($pathdoc."programmi")) mkdir($pathdoc."programmi", 0777, true);
if (!is_dir($pathbak."img")) mkdir($pathbak."img", 0777, true);
if (!is_dir($pathbak."cv")) mkdir($pathbak."cv", 0777, true);
if (!is_dir($pathbak."cg")) mkdir($pathbak."cg", 0777, true);
if (!is_dir($pathbak."programmi")) mkdir($pathbak."programmi", 0777, true);
//se è presente un nuovo file ed esiste il vecchio quest'ultimo viene spostato in bak e il nuovo lo sostituisce
$nomecv="";
$expcv="";
$nomecg="";
$expcg="";
$preimg="";
$campi="";

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
	if ($flag_cv) {$tmp=$pathdoc."cv/".$namecv;
		if(is_file($pathdoc."cv/".$namecv))
			rename($pathdoc."cv/".$namecv,$pathbak."cv/".$namecv);
		$cond.="cv=''";
	}
	if ($flag_cg){$tmp=$pathdoc."cg/".$namecg;
		if(is_file($pathdoc."cg/".$namecg))
			rename($pathdoc."cg/".$namecg,$pathbak."cg/".$namecg);
		if(mb_strlen($cond)) $cond.=',';
		$cond.="cg=''";
	}	
	$sql="update ".$prefix."_ele_candidato set $cond where id_cand=:id_cand";
	$compl = $dbi->prepare("$sql");
	try {
	$compl->bindParam(':id_cand', $id_cand, PDO::PARAM_INT);
	$compl->execute();
	} catch(PDOException $e) {
		echo $e->getMessage();
		$salvato=1;
	}
}else{
	if($op=='cancella') {
		if (is_file($pathdoc."cv".$namecv))
			rename($pathdoc."cv".$namecv,$pathbak."cv".$namecv);
		if (is_file($pathdoc."cg".$namecg))
			rename($pathdoc."cg".$namecg,$pathbak."cg".$namecg);
		$sql="delete from ".$prefix."_ele_candidato where id_cand=:id_cand";
		$compl = $dbi->prepare("$sql");
		try {
		$compl->bindParam(':id_cand', $id_cand, PDO::PARAM_INT);
		$compl->execute();
		} catch(PDOException $e) {
			echo $e->getMessage();
			$salvato=1;
		}
	}else{
		$query="select * from ".$prefix."_ele_candidato where id_cand=:id_cand";
		$res = $dbi->prepare("$query");
		$res->execute([
			':id_cand' => $id_cand
		]);
		if($res->rowCount()) { //update

			$sql="update ".$prefix."_ele_candidato set cognome=:cognome,nome=:nome,num_cand=:numero,id_lista=:id_lista,num_lista=:num_lista $nomecv $nomecg where  id_cand=:id_cand";
			try {
				$compl = $dbi->prepare("$sql");
				$compl->bindParam(':cognome', $cognome, PDO::PARAM_STR);
				$compl->bindParam(':nome', $nome, PDO::PARAM_STR);
				$compl->bindParam(':numero', $numero, PDO::PARAM_INT);
				$compl->bindParam(':id_lista', $id_lista, PDO::PARAM_INT);
				$compl->bindParam(':num_lista', $num_lista, PDO::PARAM_INT);
				if(mb_strlen($nomecv))
					$compl->bindParam(':namecv', $namecv, PDO::PARAM_STR);
				if(mb_strlen($nomecg))
					$compl->bindParam(':namecg', $namecg, PDO::PARAM_STR);
				$compl->bindParam(':id_cand', $id_cand, PDO::PARAM_INT);		
				$compl->execute(); 
			} catch(PDOException $e) {
				echo $e->getMessage();
				$salvato=1;
			}
		}else{
			#insert
			$sql="insert into ".$prefix."_ele_candidato (id_cons, num_cand, cognome, nome, id_lista, num_lista $campi) values( :id_cons, :numero, :cognome, :nome, :id_lista, :num_lista $preimg )";
			try {
				$compl = $dbi->prepare("$sql");
				$compl->bindParam(':id_cons', $id_cons, PDO::PARAM_INT);
				$compl->bindParam(':numero', $numero, PDO::PARAM_INT);
				$compl->bindParam(':cognome', $cognome, PDO::PARAM_STR);
				$compl->bindParam(':nome', $nome, PDO::PARAM_STR);
				if(mb_strlen($nomecv))
					$compl->bindParam(':namecv', $namecv, PDO::PARAM_STR);
				if(mb_strlen($nomecg))
					$compl->bindParam(':namecg', $namecg, PDO::PARAM_STR);
				$compl->bindParam(':id_lista', $id_lista, PDO::PARAM_INT);		
				$compl->bindParam(':num_lista', $num_lista, PDO::PARAM_INT);		
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
include('modules/elenco_candidati.php');

?>
