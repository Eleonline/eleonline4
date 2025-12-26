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
if (isset($param['mid'])) $mid=$param['mid']; else $mid='0';
if (isset($param['title'])) $title=addslashes($param['title']); else $title='';
if (isset($param['preamble'])) $preamble=$param['preamble']; else $preamble='';
if (isset($param['content'])) $content=addslashes($param['content']); else $content='';
if (isset($param['op'])) $op=addslashes($param['op']); else $op='';
$tab=$_SESSION['tipo_info']; 
global $prefix,$aid,$dbi,$id_cons_gen,$id_cons,$id_comune;
$salvato=0;
$query="select * from ".$prefix."_ele_$tab where mid='$mid'";
$res = $dbi->prepare("$query");
$res->execute();
if($res->rowCount()) {
	if($op=='salva') {
		#update
		$sql="update ".$prefix."_ele_$tab set title=:title,preamble=:preamble, content=:content where mid=:mid";
		try {
			$compl = $dbi->prepare("$sql");
			$compl->execute([
				':title' => $title,
				':preamble' => $preamble,
				':content' => $content,
				':mid' => $mid
			]);
		}
		catch(PDOException $e) {
			echo $e->getMessage();
		}
		if(!$compl->rowCount()) $salvato=1;
	}elseif($op=='cancella'){	
		#delete
		$sql="select * from ".$prefix."_ele_$tab where mid='$mid'";
		$compl = $dbi->prepare("$sql");
		$compl->execute();
		if($compl->rowCount()){
			$sql="delete from ".$prefix."_ele_$tab where mid='$mid'";
			$compl = $dbi->prepare("$sql");
			$compl->execute();
			if(!$compl->rowCount()) $salvato=1;
		}else
			$salvato=2;
	}
}else{
	#insert
		$sql="insert into ".$prefix."_ele_$tab (id_cons,title, preamble, content) values( :id_cons,:title,:preamble,:content )";
		$compl = $dbi->prepare("$sql");		
		$compl->execute([
			':id_cons' => $id_cons,
			':title' => $title,
			':preamble' => $preamble,
			':content' => $content
		]); 
		if(!$compl->rowCount()) $salvato=1;
}

if(!$salvato){
	$datal=date('Y-m-d');
	$orariol=date(' H:i:s');
	$riga=addslashes($sql);
	$sqlog="insert into ".$prefix."_ele_log values('$id_cons','0','$aid','$datal','$orariol','','$riga','".$prefix."_ele_comune')";
	$res = $dbi->prepare("$sqlog");
	$res->execute();
#		echo "Nuovo orario di rilevazione inserito";
}elseif($salvato==2){
	echo "<tr><th colspan=\"3\" style=\"text-align:center\">ATTENZIONE - Non è possibile individuare il record da cancellare</th></tr>";
}else{	
	echo "<tr><td colspan=\"3\">Errore, impossibile salvare i dati - $sql</td></tr>";
}

include("modules/elenco_info.php");

?>
