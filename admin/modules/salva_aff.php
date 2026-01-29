<?php
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';


if (isset($_POST['id_cons_gen'])) $id_cons_gen=intval($_POST['id_cons_gen']); else $id_cons_gen='0';
if (isset($_POST['op'])) $op=intval($_POST['op']); else $op='0';
if (isset($_POST['id_circ'])) $id_circ=intval($_POST['id_circ']); else $id_circ='0';
if (isset($_POST['id_sez'])) $id_sez=intval($_POST['id_sez']); else $id_sez='';
if (isset($_POST['id_sede'])) $id_sede=intval($_POST['id_sede']); else $id_sede='0';
if (isset($_POST['voti_u'])) $voti_u=intval($_POST['voti_u']); else $voti_u='0';
if (isset($_POST['voti_d'])) $voti_d=intval($_POST['voti_d']); else $voti_d='0';
if (isset($_POST['voti_t'])) $voti_t=intval($_POST['voti_t']); else $voti_t='0';
if (isset($_POST['orario'])) $orario=addslashes($_POST['orario']); else $orario='';
if (isset($_POST['data'])) $data=addslashes($_POST['data']); else $data='01-01-1900';
if (isset($_POST['id_comune'])) $id_comune=intval($_POST['id_comune']); else $id_comune='0';
if (isset($_POST['id_gruppo'])) $id_gruppo=intval($_POST['id_gruppo']); else $id_gruppo='0';
if (isset($_POST['genere'])) $genere=intval($_POST['genere']); else $genere='0';
if (isset($_POST['delete'])) $delete=addslashes($_POST['delete']); else $delete='';
if (isset($_POST['copia'])) $copia=intval($_POST['copia']); else $copia='0';
if (isset($_POST['data'])) $data=addslashes($_POST['data']); else $data='01-01-1900';
global $prefix,$id_parz,$tempo,$username,$aid,$dbi,$genere;
if(($voti_u+$voti_d) and !$voti_t) $voti_t=$voti_u+$voti_d;
if($op=='aggiornaAffluenza') {include('pagina_rilevazioni.php'); return;}
$salvato=0;
$query="select id_cons,chiusa from ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen' and id_comune='$id_comune'";
$res = $dbi->prepare("$query");
$res->execute();
list($id_cons,$chiusa)=$res->fetch(PDO::FETCH_NUM);
die( "TEST: passa");
if($chiusa!=1){
	$query="select id_parz from ".$prefix."_ele_voti_parziale where data='$data' and orario='$orario' and id_sez='$id_sez' and id_gruppo='$id_gruppo'";
	$res = $dbi->prepare("$query");
	$res->execute();         
	$righe=$res->rowCount();
	list($id_parz)=$res->fetch(PDO::FETCH_NUM); 
	if($righe){ #la riga è presente e viene aggiorata
		$arr=$res->fetch(PDO::FETCH_BOTH);
		$sql="update ".$prefix."_ele_voti_parziale set voti_uomini='$voti_u',voti_donne='$voti_d',voti_complessivi='$voti_t' where  id_parz='$id_parz'";
		$compl = $dbi->prepare("$sql");
		$compl->execute(); 
		if($compl->rowCount()) $salvato=1;
		if($delete=="true"){
			$sql="delete from ".$prefix."_ele_voti_parziale where id_parz='$id_parz'";
			$res = $dbi->prepare("$sql");
			$res->execute();
			if($res->rowCount()) $salvato=1;
		}
	}else{ # è un nuovo inserimento
		if($genere==0 and $copia){
			$query="select id_gruppo from ".$prefix."_ele_gruppo where id_cons in (SELECT id_cons FROM ".$prefix."_ele_cons_comune where id_cons_gen=$id_cons_gen and id_comune=$id_comune)";
			$resg = $dbi->prepare("$query");
			$resg->execute();         
			while(list($id_gruppo)=$resg->fetch(PDO::FETCH_NUM)) {
				$sql="insert into ".$prefix."_ele_voti_parziale (id_cons, id_sez, orario, data, voti_uomini, voti_donne, voti_complessivi, id_gruppo) values ('$id_cons', '$id_sez','$orario','$data','$voti_u','$voti_d','$voti_t','$id_gruppo')";
				$res = $dbi->prepare("$sql");
				$res->execute();
				if($res->rowCount()) $salvato=1;					
			}
		}else{
			$sql="insert into ".$prefix."_ele_voti_parziale (id_cons, id_sez, orario, data, voti_uomini, voti_donne, voti_complessivi, id_gruppo) values ('$id_cons', '$id_sez','$orario','$data','$voti_u','$voti_d','$voti_t','$id_gruppo')";
			$res = $dbi->prepare("$sql");
			$res->execute();
			if($res->rowCount()) $salvato=1;
		}
	}
	if($salvato){
		$datal=date('Y-m-d');
		$orariol=date(' H:i:s');
		$riga=addslashes($sql);
		$sql="delete from ".$prefix."_ele_log where `id_cons`='$id_cons' and ((`ora` > '$orariol' and `data`='$datal') or `data` > '$datal')"; 
		$res = $dbi->prepare("$sql");
		$res->execute();
		$sqlog="insert into ".$prefix."_ele_log values('$id_cons','$id_sez','$username','$datal','$orariol','','$riga','".$prefix."_ele_voti_parziale')";
		$res = $dbi->prepare("$sqlog");
		$res->execute();
	}
	#controllo_aff($id_cons,$id_sez,$id_parz);  
	include("ele_controlli.php");
	controllo_aff($id_cons,$id_sez,$id_parz);
	include("ele_colora_sez.php");
}
include('pagina_rilevazioni.php');


?>
