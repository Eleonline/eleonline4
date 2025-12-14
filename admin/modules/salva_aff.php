<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/************************************************************************/
/* Modulo salva affluenze                                               */
/* Amministrazione                                                      */
/************************************************************************/
if (!defined('ADMIN_FILE')) {
    die ("You can't access this file directly...");
}


if (isset($_GET['id_cons_gen'])) $id_cons_gen=intval($_GET['id_cons_gen']); else $id_cons_gen='0';
if (isset($_GET['op'])) $op=intval($_GET['op']); else $op='0';
if (isset($_GET['id_circ'])) $id_circ=intval($_GET['id_circ']); else $id_circ='0';
if (isset($_GET['id_sez'])) $id_sez=intval($_GET['id_sez']); else $id_sez='';
if (isset($_GET['id_sede'])) $id_sede=intval($_GET['id_sede']); else $id_sede='0';
if (isset($_GET['voti_u'])) $voti_u=intval($_GET['voti_u']); else $voti_u='0';
if (isset($_GET['voti_d'])) $voti_d=intval($_GET['voti_d']); else $voti_d='0';
if (isset($_GET['voti_t'])) $voti_t=intval($_GET['voti_t']); else $voti_t='0';
if (isset($_GET['orario'])) $orario=addslashes($_GET['orario']); else $orario='';
if (isset($_GET['data'])) $data=addslashes($_GET['data']); else $data='01-01-1900';
if (isset($_GET['id_comune'])) $id_comune=intval($_GET['id_comune']); else $id_comune='0';
if (isset($_GET['id_gruppo'])) $id_gruppo=intval($_GET['id_gruppo']); else $id_gruppo='0';
if (isset($_GET['genere'])) $genere=intval($_GET['genere']); else $genere='0';
if (isset($_GET['delete'])) $delete=addslashes($_GET['delete']); else $delete='';
if (isset($_GET['copia'])) $copia=intval($_GET['copia']); else $copia='0';
global $prefix,$id_parz,$tempo,$username,$aid,$dbi,$genere;
if (!isset($fileout)) $fileout='';
if(($voti_u+$voti_d) and !$voti_t) $voti_t=$voti_u+$voti_d;

$salvato=0;
$query="select id_cons,chiusa from ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen' and id_comune='$id_comune'";
$res = $dbi->prepare("$query");
$res->execute();
list($id_cons,$chiusa)=$res->fetch(PDO::FETCH_NUM);
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
	if ($fileout) {
		while (!$fp = fopen($fileout,"a"));
		fwrite($fp,"$sql;\n"); 
		fclose($fp);
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
$BASE=substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['REQUEST_URI'], "/")-16);

Header("Location: admin.php?op=voti&id_cons_gen=$id_cons_gen&id_circ=$id_circ&id_sede=$id_sede&id_sez=$id_sez&ops=1&do=spoglio");


?>
