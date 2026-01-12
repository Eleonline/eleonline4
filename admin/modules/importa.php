<?php
require_once '../includes/check_access.php';
/*
$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
$id_cons_gen=intval($_SESSION['id_cons_gen']);
if (isset($param['datafile'])) $datafile=addslashes($param['datafile']); else $datafile='';
*/
function insgruppo()
{
	global $prefix, $dbi;
	global $ar_gruppo,$ar_lista,$ar_candi,$idcns,$dbname;

	foreach ($ar_gruppo as $rigagruppo){
		$newidg=0;
		$oldidg=0;
		$isnew=0;
		$numcampi=count($rigagruppo);
		$sql="SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '".$prefix."_ele_gruppo'";
		$resnew = $dbi->prepare("$sql");
		$resnew->execute();	
		list ($campiloc) = $resnew->fetch(PDO::FETCH_NUM);		
		foreach($rigagruppo as $key=>$campo){
			if ($key==0) $valori="'$idcns',";
			elseif ($key==1) {$valori.= "null"; $oldidg=$campo;}
			elseif ($campo==null) $valori.= ",null";
			elseif ($key==6) $valori.= ",0";
			elseif($key==4) $valori.=",'".utf8_encode($campo)."'";
			elseif($key==7) {if($numcampi==9) $valori.=",0"; $valori.= ",'".$campo."'";}
			elseif($key==8)  {$valori.=",'".utf8_encode($campo)."'";$isnew=1;}
			else $valori.= ",'".$campo."'";
			if ($key==2) $numgruppo= $campo;
		}		
		$i=$numcampi;
		if($numcampi<$campiloc) 
			while($i<$campiloc) 
			{
				if($i==13) $valori.=",'0'";
				else $valori.=",null";
				$i++;
			}
		if(isset($valori)){ 
			$sql="insert into ".$prefix."_ele_gruppo values($valori)";
			try {
				$res_gruppo = $dbi->prepare("$sql");
				$res_gruppo->execute();	
			}
			catch(PDOException $e)
			{
				echo $sql . "<br>" . $e->getMessage();
			}                  
			$sql="select id_gruppo from ".$prefix."_ele_gruppo where num_gruppo='$numgruppo' and id_cons='$idcns'";
			$resnew = $dbi->prepare("$sql");
			$resnew->execute();	
			list ($newidg) = $resnew->fetch(PDO::FETCH_NUM);
			unset($valori);
			if($oldidg){
				$_SESSION['gruppi']['idg_'.$oldidg]=$newidg;
				$_SESSION['gruppi']['numg_'.$numgruppo]=$numgruppo;						
			}
		}
	}
}

function inslista()
{
global $prefix, $dbi,$dbname;
global $ar_lista,$idcns;
	foreach ($ar_lista as $rigalista){ 
		if(!isset($rigalista[3])) continue;
		$oldidl=0;
		$okl=0;
		$numgruppo=0;
		$oldidg=$rigalista[3];
		if(isset($_SESSION['gruppi'])) {
			$newidg=$_SESSION['gruppi']['idg_'.$oldidg]; 
			$sql="select num_gruppo from ".$prefix."_ele_gruppo where id_gruppo='$newidg' and id_cons='$idcns'";
			$resnew = $dbi->prepare("$sql");
			$resnew->execute();	
			list ($numgruppo) = $resnew->fetch(PDO::FETCH_NUM);
		}	
		else $newidg=0;
		$ultimocampo=count($rigalista);
		$sql="SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '".$prefix."_ele_lista'";
		$resnew = $dbi->prepare("$sql");
		$resnew->execute();	
		list ($campiloc) = $resnew->fetch(PDO::FETCH_NUM);		
		foreach($rigalista as $key=>$campo){
			if ($key==0) $valori=$idcns.",";
			elseif ($key==1) {$valori.= "null,";$oldidl=$campo;}
			elseif ($key==2) {$valori.="'$campo',";$numlista= $campo;}
			elseif ($key==3) {$valori.= "'$newidg',"; if ($campo!=$oldidg) $okl=1;}
			elseif ($key==4) $valori.= "$numgruppo,"; 
			elseif ($key==($ultimocampo-1) ) $valori.= "'$campo'"; 
			elseif ($campo==null) $valori.= "null,";
			else $valori.="'".$campo."',";
		}
		$i=$ultimocampo;
		if($ultimocampo<$campiloc) 
			while($i<$campiloc) 
			{
				if($i==11) $valori.=",null";
				else $valori.=",null";
				$i++;			
			}
#		if($ultimocampo==9) $valori.=",''";
#		if($key==$ultimocampo){
			if ($okl) {$okl=0;continue;}
			$sql="insert into ".$prefix."_ele_lista values($valori)";
			try {
				$res_lista = $dbi->prepare("$sql");
				$res_lista->execute();
			}
			catch(PDOException $e)
			{
				echo $sql . "<br>" . $e->getMessage(); die();
			}                  
				
			$sql="select id_lista from ".$prefix."_ele_lista where num_lista='$numlista' and id_cons='$idcns'";
			$reslnew = $dbi->prepare("$sql");
			$reslnew->execute();	
			list ($newidl) = $reslnew->fetch(PDO::FETCH_NUM);
			unset($valori);
			if($oldidl){
				$_SESSION['liste']['idl_'.$oldidl]=$newidl; 
			}
#		} 
	}
}

function inscandi()
{
	global $prefix, $dbi,$dbname;
	global $ar_candi,$idcns;
	$sql="SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '".$prefix."_ele_lista'";
	$resnew = $dbi->prepare("$sql");
	$resnew->execute();	
	list ($campiloc) = $resnew->fetch(PDO::FETCH_NUM);		
	foreach ($ar_candi as $rigacandi){
		if(!isset($rigacandi[2])) continue;
		$okc=0;
		$oldidl=$rigacandi[2];
		$newidl=$_SESSION['liste']['idl_'.$oldidl];
		foreach($rigacandi as $key=>$campo){
#			if (count($rigacandi)!=12) {unset($valori);continue;}
			if ($key==0) $valori= "null,";
			elseif ($key==1) $valori.="'$idcns',";
			elseif ($key==2) {$valori.= "'$newidl'"; if ($campo!=$oldidl) $okc=1;}
			else $valori.= ",'".utf8_encode($campo)."'";
		}
		if(isset($valori) and $valori!=''){
			for($x=count($rigacandi);$x<$campiloc;$x++) 
				if($x==10) $valori.=",''";
				elseif($x==11) $valori.=",''";
				elseif($x==12) $valori.=",'0'";
				else $valori.=",''";
			if ($okc) {$okc=0;continue;}
			$sql="insert into ".$prefix."_ele_candidato values($valori)";
			try {
				$res_lista = $dbi->prepare("$sql");
				$res_lista->execute();
			}
			catch(PDOException $e)
			{
				echo "<br>key:$key sql:".$sql . "<br>" . $e->getMessage();
			}                  
		}
	}
}




function importa($cons) {
	global $prefix, $id_comune, $id_cons_gen, $dbi,$idcns;
$sql="SELECT t1.id_cons, t2.descrizione FROM ".$prefix."_ele_cons_comune as t1 left join ".$prefix."_ele_consultazione as t2 on t1.id_cons_gen=t2.id_cons_gen where t1.id_comune='$id_comune' and t2.id_cons_gen='$id_cons_gen'";
$res = $dbi->prepare("$sql");
$res->execute();	
list($id_cons,$descrizione) = $res->fetch(PDO::FETCH_NUM);
#Esegue il backup della consultazione corrente per eventuale recupero
include_once('../includes/backup.php');
#if (!isset($_FILES['datafile']['tmp_name']) or !is_uploaded_file($_FILES['datafile']['tmp_name'])) 
if(!$cons)
{
	if (isset($_GET['help'])) $help=intval($_GET['help']);
   	global $help,$language;
	if (isset($help)) include("language/$language/ele_importa.html");
	include('importa_html.php');
}else{
	$idcns=$id_cons; 
	$sql="delete from ".$prefix."_ele_voti_ref where id_cons=$idcns";
	$res_del = $dbi->prepare("$sql");
	$res_del->execute();	
	$sql="delete from ".$prefix."_ele_voti_candidato where id_cons=$idcns";
	$res_del = $dbi->prepare("$sql");
	$res_del->execute();	
	$sql="delete from ".$prefix."_ele_voti_lista where id_cons=$idcns";
	$res_del = $dbi->prepare("$sql");
	$res_del->execute();	
	$sql="delete from ".$prefix."_ele_voti_gruppo where id_cons=$idcns";
	$res_del = $dbi->prepare("$sql");
	$res_del->execute();	
	$sql="update ".$prefix."_ele_sezione set validi='0', contestati='0', validi_lista='0', nulli='0',bianchi='0',contestati_lista='0', voti_nulli_lista='0'  where id_cons=$idcns";
	$res_del = $dbi->prepare("$sql");
	$res_del->execute();	
	$sql="delete from ".$prefix."_ele_voti_parziale where id_cons=$idcns";
	$res_del = $dbi->prepare("$sql");
	$res_del->execute();	
	$sql="delete from ".$prefix."_ele_candidato where id_cons=$idcns";
	$res_del = $dbi->prepare("$sql");
	$res_del->execute();	
	$sql="delete from ".$prefix."_ele_lista where id_cons=$idcns";
	$res_del = $dbi->prepare("$sql");
	$res_del->execute();	
	$sql="delete from ".$prefix."_ele_gruppo where id_cons=$idcns";
	$res_del = $dbi->prepare("$sql");
	$res_del->execute();	
#	$datafile=$_FILES['datafile']['tmp_name'];
#$lines = preg_split('/\n|\r\n?/', $cons); echo $lines[0]."TEST".$;
	$arrFile = preg_split('/\n|\r\n?/', $cons); #file($datafile);
	$test=array();
	$errore=0;
	$fine=0;
	$numgruppo=0;
	$numlista=0;
// Set counters
    $currentLine = 0;
    $cntFile = count($arrFile);
	$tabs=array($prefix."_ele_gruppo",$prefix."_ele_lista",$prefix."_ele_candidato",$prefix."_ele_circoscrizione");
	$x=0;$k=0;
	$scarto=0;
	$primog=0;
	$primol=0;
	$conta=array();
	$currentLine = 0;
	$x=0;$k=0;
	$y=0;
	global $ar_gruppo, $ar_lista, $ar_candi;
	$ar_gruppo=array(array());
	$ar_lista=array(array());
	$ar_candi=array(array());
	$z=0;
	$tab=substr($arrFile[$currentLine],1,-1);
	$conf=$tabs[$x];
	if($k==0) {while (substr($arrFile[$currentLine],1,-1)!=$conf and $currentLine <= $cntFile) $currentLine++; $k++;}
	$currentLine++;
	while($currentLine <= $cntFile and $fine==0){
		if(isset($arrFile[$currentLine])){ 
			$appo=substr($arrFile[$currentLine],1,-1); 
			if($appo==$prefix."_ele_candidati") $appo=$prefix."_ele_candidato";
		}else $appo='';
		if (isset($tabs[($x+1)]) and $appo==$tabs[($x+1)]){ $x++;$conf=$tabs[$x];$currentLine++; continue;}
		$test=explode(':',$appo);
		if(!is_array($test)) {die("errore di import<br>");}
		foreach($test as $key=>$val) { 
			if ($conf==$prefix."_ele_gruppo"){ 
				$ar_gruppo[$z][$key]=addslashes(base64_decode($val));
			}
			elseif ($conf==$prefix."_ele_lista"){
				if($primog==0){
					$gruppofil= array_filter($ar_gruppo);
					$numgruppo=count($gruppofil);
					insgruppo();
					$primog=1;
					unset($ar_gruppo);
				}
				$ar_lista[$z][$key]=addslashes(base64_decode($val));
			}
			elseif ($conf==$prefix."_ele_candidato" or $conf==$prefix."_ele_candidati"){ 
				if($primog==0){
					$gruppofil= array_filter($ar_gruppo);
					$numgruppo=count($gruppofil);
					insgruppo();
					$primog=1;
					unset($ar_gruppo);
				}
				elseif($primol==0){
					$listafil= array_filter($ar_lista);
					$numlista=count($listafil);
					inslista();
					$primol=1;
					unset($ar_lista);
				}
				$ar_candi[$z][$key]=addslashes(base64_decode($val));
			}
			elseif ($conf==$prefix."_ele_circoscrizione"){
				if($primog==0){
					$gruppofil= array_filter($ar_gruppo);
					$numgruppo=count($gruppofil);
					insgruppo();
					$primog=1;
					unset($ar_gruppo);
				}
				elseif($primol==0){
					$listafil= array_filter($ar_lista);
					$numlista=count($listafil);
					inslista();
					$primol=1;
					unset($ar_lista);
				}else{
					inscandi();
					unset($ar_candi);
					$fine=1;
					break;
				}
	}}
			$currentLine++;
			$z++;
	}
	if ($numgruppo){
		Header("Location: modules.php?op=27&id_cons_gen=$id_cons_gen");
	}
	elseif ($numlista) {
		Header("Location: modules.php?op=28&id_cons_gen=$id_cons_gen");
	}
	else Header("Location: modules.php?op=28&id_cons_gen=$id_cons_gen");

}
}
?>
