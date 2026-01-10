<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/check_access.php';

// Parametri
$param = strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
$id_cons_gen = intval($_SESSION['id_cons_gen']);
$id_comune = intval($_SESSION['id_comune']); // presupposto
if (isset($param['datafile'])) $datafile = addslashes($param['datafile']); else $datafile='';

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
			elseif ($key==6) $valori.= ",0";
			elseif($key==4) $valori.=",'".utf8_encode($campo)."'";
			elseif($key==7) {if($numcampi==9) $valori.=",0"; $valori.= ",'".$campo."'";}
			elseif($key==8)  {$valori.=",'".utf8_encode($campo)."'";$isnew=1;}
			else $valori.= ",'".$campo."'";
			if ($key==2) $numgruppo= $campo;
		}		
		$i=$numcampi;
		if($numcampi<$campiloc) while($i<$campiloc) {$valori.=",''";$i++;}
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
			else $valori.="'".$campo."',";
		}
		$i=$ultimocampo;
		if($ultimocampo<$campiloc) while($i<$campiloc) {$valori.=",''";$i++;}
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
			for($x=count($rigacandi);$x<$campiloc;$x++) $valori.=",''";
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

// Gestione upload
if (!isset($_FILES['datafile']['tmp_name']) || !is_uploaded_file($_FILES['datafile']['tmp_name'])) {
    // Mostra form
    include('importa_lista_2.php');
    exit;
} else {
    $idcns = $id_cons_gen; 

    // Pulizia dati vecchi
    $tables = ['ele_voti_ref','ele_voti_candidato','ele_voti_lista','ele_voti_gruppo','ele_voti_parziale','ele_candidato','ele_lista','ele_gruppo'];
    foreach($tables as $tbl){
        $sql = "DELETE FROM ".$prefix."_".$tbl." WHERE id_cons=$idcns";
        $res_del = $dbi->prepare($sql);
        $res_del->execute();
    }
    // Reset sezioni
    $sql = "UPDATE ".$prefix."_ele_sezione SET validi='0', contestati='0', validi_lista='0', nulli='0', bianchi='0', contestati_lista='0', voti_nulli_lista='0' WHERE id_cons=$idcns";
    $res_del = $dbi->prepare($sql);
    $res_del->execute();

    // Lettura file
    $datafile = $_FILES['datafile']['tmp_name'];
    $arrFile = file($datafile);
    $handle = fopen($datafile, "r");

    // Variabili di gestione
    $currentLine = 0;
    $cntFile = count($arrFile);
    $tabs = array($prefix."_ele_gruppo",$prefix."_ele_lista",$prefix."_ele_candidato",$prefix."_ele_circoscrizione");
    $x = $k = $y = $z = 0;
    $primog = $primol = 0;
    $ar_gruppo = array(array());
    $ar_lista = array(array());
    $ar_candi = array(array());
    $tab = substr($arrFile[$currentLine],1,-2);
    $conf = $tabs[$x];

    // Scansione file
    if($k==0){
        while (substr($arrFile[$currentLine],1,-2)!=$conf && $currentLine <= $cntFile) $currentLine++; 
        $k++;
    }
    $currentLine++;

    $fine = 0;
    while($currentLine <= $cntFile && $fine==0){
        if(isset($arrFile[$currentLine])){ 
            $appo = substr($arrFile[$currentLine],1,-2);
            if($appo==$prefix."_ele_candidati") $appo=$prefix."_ele_candidato";
        }else $appo='';

        if (isset($tabs[($x+1)]) && $appo==$tabs[($x+1)]){ $x++; $conf=$tabs[$x]; $currentLine++; continue; }

        $test = explode(':',$appo);
        if(!is_array($test)) die("Errore di import<br>");

        foreach($test as $key=>$val){
            if($conf==$prefix."_ele_gruppo") $ar_gruppo[$z][$key]=addslashes(base64_decode($val));
            elseif($conf==$prefix."_ele_lista"){
                if($primog==0){ insgruppo(); $primog=1; unset($ar_gruppo); }
                $ar_lista[$z][$key]=addslashes(base64_decode($val));
            }
            elseif($conf==$prefix."_ele_candidato"){ 
                if($primog==0){ insgruppo(); $primog=1; unset($ar_gruppo); }
                elseif($primol==0){ inslista(); $primol=1; unset($ar_lista); }
                $ar_candi[$z][$key]=addslashes(base64_decode($val));
            }
            elseif($conf==$prefix."_ele_circoscrizione"){
                if($primog==0){ insgruppo(); $primog=1; unset($ar_gruppo); }
                elseif($primol==0){ inslista(); $primol=1; unset($ar_lista); }
                inscandi(); unset($ar_candi); $fine=1; break;
            }
        }
        $currentLine++; $z++;
    }
    fclose($handle);

    // Redireziona
    if (!empty($ar_gruppo)) header("Location: modules.php?op=27&id_cons_gen=$id_cons_gen");
    else header("Location: modules.php?op=28&id_cons_gen=$id_cons_gen");
}
?>
