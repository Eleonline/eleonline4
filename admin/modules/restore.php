<?php
require_once '../includes/check_access.php';

global $LINK,$fileback,$id_cons,$id_cons_gen,$prefix,$dbi;


$filedati="../documenti/backup/file_bak_".$id_cons.".txt";

$handle = fopen($filedati, "r");
$arrFile = file($filedati);
fclose($handle);
$test=array();
$errore=0;

// Set counters
    $currentLine = 0;
    $cntFile = count($arrFile);
// Write contents, inserting $item as first item
$tabs=array($prefix."_ele_cons_comune",$prefix."_ele_link",$prefix."_ele_come",$prefix."_ele_numero",$prefix."_ele_servizio",$prefix."_ele_rilaff",$prefix."_ele_voti_parziale",$prefix."_ele_circoscrizione",$prefix."_ele_sede",$prefix."_ele_sezione",$prefix."_ele_gruppo",$prefix."_ele_lista",$prefix."_ele_candidato",$prefix."_ele_voti_candidato",$prefix."_ele_voti_gruppo",$prefix."_ele_voti_lista",$prefix."_ele_voti_parziale",$prefix."_ele_voti_ref");

$x=0;
$scarto=0;
$conta=array();
while( $currentLine <= $cntFile and isset($arrFile[$currentLine])){
	$appo=substr($arrFile[$currentLine],1,-2);
	$conta[$x]=0; 
	$conf=$tabs[$x];
	if ($appo==$conf){
		$currentLine++;
		while($currentLine <= $cntFile ){
			if(isset($arrFile[$currentLine])) 
				$appo=substr($arrFile[$currentLine],1,-2);
			else $appo='';
			if(isset($tabs[($x+1)])) 
				if ($appo==$tabs[($x+1)]){ $x++; break;}
			elseif($appo=='') { $x++; break;}
			$conta[$x]++;
			$currentLine++;
		}
	}else {$scarto++;$currentLine++;}
}
if ($scarto==0){
   $currentLine = 0;
	$x=0;
	$y=0;
    while( $currentLine <= $cntFile ){
		if(isset($arrFile[$currentLine]))
			$tab=substr($arrFile[$currentLine],1,-2);
		else $tab='';
		if(isset($tabs[$x]))
			$conf=$tabs[$x];
		else $conf='';
		if ($tab==$conf){ 
			$currentLine++;
			while($currentLine <= $cntFile ){
				if(isset($arrFile[$currentLine]))
					$appo=substr($arrFile[$currentLine],1,-2);
				else $appo='';
				if(isset($tabs[($x+1)])){
					if ($appo==$tabs[($x+1)]){ $x++; break;}}
					elseif($appo=='') { $x++; break;}
				
				if(isset($arrFile[$currentLine]))				
					$test=explode(':',$arrFile[$currentLine]); if(!is_array($test)) {die("errore di import<br>");}
				$valori='';
				foreach($test as $key=>$val)
					if($key==0){
						$valori.= "'".base64_decode($val)."'";
						if ($y==0) {$idcns=$valori;$y++;
						foreach($tabs as $tbs){
							if($tbs==$prefix."_ele_cons_comune" or $tbs==$prefix."_ele_rilaff")
								$sql="delete from $tbs where id_cons_gen=$id_cons_gen";
							else
								$sql="delete from $tbs where id_cons=$idcns";
							$res_del = $dbi->prepare("$sql");
							$res_del->execute();	
							} 
						}
					}else $valori.= ",'".addslashes(base64_decode($val))."'";
					$sql="insert into $tab values($valori)";
					try {				
						$res_comune = $dbi->prepare("$sql");
						$res_comune->execute();	
					}
					catch(PDOException $e)
					{
						echo $sql . "<br>" . $e->getMessage();
					}
					$currentLine++;
				}
			}

		}
	} else $errore=1;

echo "<center><h2>Ripristino dei dati terminato regolarmente ".date('d/m/Y H:i')." </h2></center>";

if (isset($errore) and $errore) echo _MEX_RESTORE_FAILED;


?>
