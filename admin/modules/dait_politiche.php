<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* Funzione di import dati da DAIT										*/
/* by Roberto Gigli & Alessandro Candido                                */
/* http://www.eleonline.it                                              */
/* info@eleonline.it 									                */
/************************************************************************/
/* Amministrazione                                                      */
/************************************************************************/

// if (!defined('ADMIN_FILE')) {
    // die ("You can't access this file directly...");
// }
// $perms=ChiSei(0);
// if ($perms<32) die("Non hai i permessi per effettuare questa operazione!");
// $language=$_SESSION['lang'];
// $param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
// $id_cons_gen=intval($param['id_cons_gen']);
// global $id_comune,$tipo_cons;
// $sql="select id_cons from ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen' and id_comune='$id_comune'";
// $res = $dbi->prepare("$sql");
// $res->execute();	
// list($id_cons)=$res->fetch(PDO::FETCH_NUM);
// include("modules/Elezioni/ele.php");

// Variabili di controllo
$fileUrl = isset($_POST['file_url']) ? $_POST['file_url'] : "";
$circoscrizione = isset($_POST['circoscrizione']) ? $_POST['circoscrizione'] : "";
$collegio = isset($_POST['collegio']) ? $_POST['collegio'] : "";
$confermaDati = isset($_POST['conferma_dati']) ? $_POST['conferma_dati'] : "";
$csvData = [];
$circoscrizioni = [];
$collegi = [];
$filteredData = [];

//ele();
// Funzione per scaricare il file CSV
function downloadCSV($url) {
	$posuni = strrpos($url,'_uni.csv',0);
	$pospluri = strrpos($url,'_pluri.csv',0);
	if(!$posuni && !$pospluri) exit;
	elseif(!$posuni){
		$urluni=str_replace('_pluri.csv','_uni.csv',$url);
		$urlpluri=$url;
	}else{
		$urluni=$url;
		$urlpluri=str_replace('_uni.csv','_pluri.csv',$url);		
	}
    $data = file_get_contents($urluni);
    $ar[0] = explode("\n", $data);
    $data = file_get_contents($urlpluri);
    $ar[1] = explode("\n", $data);
	foreach ($ar[0] as $lineuni) 
	if ($lineuni) {
		$csvData[0][]=str_getcsv($lineuni);
	}
	foreach ($ar[1] as $linepluri) 
	if ($linepluri) {
		$csvData[1][]=str_getcsv($linepluri);
	}	
    return $csvData;
}	
function filtradati($csvData)
{
#più collegi uninominali in un collegio plurinominale
	global $collegio,$tipo_cons;
	if($tipo_cons==7 or $tipo_cons==10 or $tipo_cons==16 or $tipo_cons==19)
		$corrispondenza=file_get_contents("../includes/collegi_senato.txt");
	else
		$corrispondenza=file_get_contents("../includes/collegi_camera.txt");
	$tmp=explode("\n",$corrispondenza);
	foreach($tmp as $val){
		$collegicamera[]=explode("\t",strtoupper($val));
#		echo "<br>TEST: val:$val";
	}
    $filtrato = array();
#	$filtrato[]=array("DESCR_LISTA","CANDIDATO","DESCR_LISTA_UNI","CANDIDATO_UNI");
	foreach($collegicamera as $key=>$val)
	{
#		$pos=array_search("PIEMONTE 1 - U01 (TORINO: CIRCOSCRIZIONE 2 - SANTA RITA - MIRAFIORI NORD - MIRAFIORI SUD)",array_column($csvData[0],1));
#		$pos=0;
		if(strncmp($collegio,$val[1],strlen($val[1]))) { continue; } 
		$collegiuni[]=$val[1]; # echo "<br>TEST4:$collegio: ".$val[1];
		$collegipluri[]=$val[0];
		foreach($csvData[0] as $index => $string) {
			$pos=strncmp($string[1], $val[1], strlen($val[1]));
        if ( $pos !== 0 or $string[0]!='ITALIA' )
			continue;
#		echo "<br>$pos:TEST$index: ".print_r($string)." ; ".print_r($val);
		$filtrato[]=array($string[3],$string[2]);
#		$pos++;
		}		
	}
#		echo "<br>TEST: $pos:".print_r($collegiuni);
#		echo "<br>TEST: --- :".print_r($collegipluri);
#	echo "TEST:".var_dump($collegicamera);
if(!isset($filtrato) or (isset($filtrato) and count($filtrato)==0)) { 
	$linesuni=$csvData[0];
	$linespluri=$csvData[1];
#					echo "<br>TEST: ".var_dump($linesuni[0])."---".var_dump($linespluri[0]);
$i=0;$y=0;
    foreach ($linesuni as $lineuni) { $i++;
#        if ($lineuni) {
#			$aruni=str_getcsv($lineuni);
			if($lineuni[1]!=$collegio or $lineuni[2]=='DESCR_LISTA') continue;
			foreach($linespluri as $linepluri)
#				if($linepluri)
				{ $y++;#echo "<br>TEST:collegio:$collegio::".$linepluri[1].":lista:".$lineuni[1]." __ ".$linepluri[2];
#					$arpluri=str_getcsv($linepluri); 
#					echo "<br>TEST0: $collegio - ".$linepluri[0]." - ".$linepluri[1];
#						echo "<br>TEST3: $collegio ; ".$linepluri[1]." ; ".$lineuni[1]." ; ".$linepluri[2]." ; ".$linepluri[3];
					$flag=0;
#					$collegiorif=substr($tmpcollegio,0,strlen($lineuni[1]))
					foreach($collegipluri as $tmpcollegio) {
						if($linepluri[1]==$tmpcollegio and $lineuni[2]==$linepluri[2]) { $flag=1;}
# echo "<br>TEST:$flag:$tmpcollegio::".$linepluri[1]." - ".$lineuni[2]." - ".$linepluri[2]; 
					}
					if(!$flag) continue;
					if(isset($lineuni[2]) and isset($linepluri[2])){ # and strrpos($lineuni[2],$linepluri[2],0)
						$filtrato[] = array($lineuni[3],$linepluri[2],$linepluri[3]);
					}
				}
 #       }
    }
}	
#						echo "<br>TEST: ".$lineuni[1]."::".$lineuni[2]."::".$lineuni[3]."::".$linepluri[2]."::".$linepluri[3];
#echo "TEST2: ".print_r($filtrato[1]);
#foreach($filtrato as $val) echo var_dump($val);
    return $filtrato;
}

if (!empty($fileUrl)) {
    $csvData = downloadCSV($fileUrl);
    $header = $csvData[0][0]; // Intestazione delle colonne

    // Trova le posizioni delle colonne
    $circoscrizioneColIndex = array_search('CIRCOSCRIZIONE', $header);
    $collegioColIndex = array_search('COLLEGIO_UNINOMINALE', $header);
    // Estrai i valori unici per le circoscrizioni
    foreach ($csvData[0] as $row) {
        if (isset($row[$circoscrizioneColIndex]) && !in_array($row[$circoscrizioneColIndex], $circoscrizioni)) {
            $circoscrizioni[] = $row[$circoscrizioneColIndex];
        }
    }

    // Estrai i valori unici per i collegi se una circoscrizione è selezionata
    if (!empty($circoscrizione)) {
        foreach ($csvData[0] as $row) {
            if (isset($row[$circoscrizioneColIndex]) && $row[$circoscrizioneColIndex] === $circoscrizione) {
                if (!in_array($row[$collegioColIndex], $collegi)) {
                    $collegi[] = $row[$collegioColIndex];
                }
            }
        }
    }

    // Filtra i dati in base alla circoscrizione e al collegio selezionati
    if (!empty($collegio)) {
/*        foreach ($csvData as $row) {
            if (
                isset($row[$circoscrizioneColIndex], $row[$collegioColIndex]) &&
                $row[$circoscrizioneColIndex] === $circoscrizione &&
                $row[$collegioColIndex] === $collegio
            ) {
                $filteredData[] = $row;
            }
        } */
		$filteredData=filtradati($csvData);
    }
}

// Gestisci il clic su "No" per resettare la visualizzazione dei dati
if ($confermaDati === "no") {
    // Resetta il filtro e ricarica la pagina
    $circoscrizione = "";
    $collegio = "";
    $filteredData = [];
}
?>

<?php require_once '../includes/check_access.php'; ?>
<section class="content">
  <div class="container-fluid mt-4">
    <!-- Form di filtro CSV / circoscrizione / collegio -->
    <div class="card card-primary shadow-sm mb-4">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cogs me-2"></i>Importazione Liste e Candidati</h3>
      </div>
      <div class="card-body">
        <?php if (empty($confermaDati) || $confermaDati === "no"): ?>
           <?php
            // $sql="SELECT tipo_cons FROM ".$prefix."_ele_consultazione WHERE id_cons_gen=$id_cons_gen";
            // $sth = $dbi->prepare($sql);
            // $sth->execute();
            // list($tipocons) = $sth->fetch(PDO::FETCH_NUM);
            // $descr = in_array($tipocons, [6,11,15,18]) ? "Camera" : (in_array($tipocons, [7,10,16,19]) ? "Senato" : "");
           ?>
          <p><strong>Importa da DAIT per consultazione:</strong> <?php //echo htmlspecialchars($descr); ?></p>
          <p><strong>Procedura:</strong><br>
            1 – Vai su <a href="https://dait.interno.gov.it/elezioni/trasparenza" target="_blank">Trasparenza DAIT</a><br>
            2 – Accedi alla consultazione<br>
            3 – Scarica i contrassegni<br>
            4 – Copia il link CSV di "Liste e Candidati"<br>
            5 – Inserisci qui sotto e procedi</p>

          <form id="filter-form" method="post" action="modules.php">
            <input type="hidden" name="op" value="81">
            <input type="hidden" name="id_cons_gen" value="<?php echo $id_cons_gen; ?>">

            <div class="mb-3">
              <label for="file_url" class="form-label">Link del file CSV:</label>
              <input type="text" class="form-control" id="file_url" name="file_url"
                     value="<?php echo htmlspecialchars($fileUrl); ?>" required>
            </div>
            <button type="submit" name="carica_dati" value="carica" class="btn btn-primary">Carica</button>
          </form>

          <?php if (!empty($fileUrl) && !empty($csvData[0])): ?>
            <hr>
            <form method="post" action="modules.php">
              <input type="hidden" name="op" value="81">
              <input type="hidden" name="id_cons_gen" value="<?php echo $id_cons_gen; ?>">
              <input type="hidden" name="file_url" value="<?php echo htmlspecialchars($fileUrl); ?>">

              <div class="mb-3">
                <label for="circoscrizione" class="form-label">Seleziona Circoscrizione:</label>
                <select id="circoscrizione" name="circoscrizione" class="form-select"
                        onchange="this.form.submit()">
                  <option value="">-- Seleziona --</option>
                  <?php foreach ($circoscrizioni as $circ): ?>
                    <option value="<?php echo htmlspecialchars($circ); ?>"
                      <?php echo ($circ === $circoscrizione ? 'selected' : ''); ?>>
                      <?php echo htmlspecialchars($circ); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <?php if (!empty($circoscrizione)): ?>
                <div class="mb-3">
                  <label for="collegio" class="form-label">Seleziona Collegio:</label>
                  <select id="collegio" name="collegio" class="form-select"
                          onchange="this.form.submit()">
                    <option value="">-- Seleziona --</option>
                    <?php foreach ($collegi as $coll): ?>
                      <option value="<?php echo htmlspecialchars($coll); ?>"
                        <?php echo ($coll === $collegio ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($coll); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              <?php endif; ?>

            </form>
          <?php endif; ?>

        <?php endif; ?>
      </div>
      <div class="card-footer"></div>
    </div>

    <!-- Sezione dati filtrati -->
    <?php if (!empty($collegio) && !empty($filteredData)): ?>
      <div class="card card-success shadow-sm">
        <div class="card-header">
          <h3 class="card-title">Dati per Collegio: <?php echo htmlspecialchars($collegio); ?></h3>
        </div>
        <div class="card-body">
          <form method="post" action="modules.php" class="mb-3">
            <input type="hidden" name="op" value="81">
            <input type="hidden" name="id_cons_gen" value="<?php echo $id_cons_gen; ?>">
            <input type="hidden" name="file_url" value="<?php echo htmlspecialchars($fileUrl); ?>">
            <input type="hidden" name="circoscrizione" value="<?php echo htmlspecialchars($circoscrizione); ?>">
            <input type="hidden" name="collegio" value="<?php echo htmlspecialchars($collegio); ?>">

            <p><strong>I dati sono corretti?</strong></p>
            <button type="submit" name="conferma_dati" value="si" class="btn btn-success me-2">Sì</button>
            <button type="submit" name="conferma_dati" value="no" class="btn btn-danger">No</button>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <?php
                    $excludeColumns = ['CIRCOSCRIZIONE','COLLEGIO_PLURINOMINALE'];
                    $header = ['CANDIDATO UNINOMINALE','LISTA','CANDIDATI PLURINOMINALE'];
                    foreach ($header as $colName):
                      if (!in_array($colName, $excludeColumns)):
                  ?>
                    <th><?php echo htmlspecialchars($colName); ?></th>
                  <?php endif; endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($filteredData as $row): ?>
                  <tr>
                    <?php foreach ($row as $index => $cell): ?>
                      <?php if (!in_array($header[$index], $excludeColumns)): ?>
                        <td><?php echo htmlspecialchars($cell); ?></td>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer"></div>
      </div>
    <?php endif; ?>

    <!-- Importazione definitiva dati -->
    <?php if ($confermaDati === "si"){ 	
	$sql="delete from ".$prefix."_ele_gruppo where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$sql="delete from ".$prefix."_ele_voti_gruppo where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$sql="delete from ".$prefix."_ele_lista where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$sql="delete from ".$prefix."_ele_voti_lista where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$sql="delete from ".$prefix."_ele_candidato where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$sql="delete from ".$prefix."_ele_voti_candidato where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$gruppo='';
	$numgruppo=1;
	$lista='';
	$numlista=1;#	echo "TEST1: ".count($filteredData);
	foreach ($filteredData as $row){ 
		if($gruppo!=$row[0]){
			$gruppo=$row[0];
			$valori = $id_cons.", null,'".$numgruppo."',".$dbi->quote($row[0]).",'','','0','0','',null,'','','0',null"; 
			$sql="insert into ".$prefix."_ele_gruppo values($valori)";#echo "<br>TEST: $valori";
			try {
				$res_gruppo = $dbi->prepare("$sql");
				$res_gruppo->execute();
			}
			catch(PDOException $e)
			{
				echo $sql . "<br>" . $e->getMessage();
			}                  
			$sql="select id_gruppo,num_gruppo from ".$prefix."_ele_gruppo where num_gruppo='".$numgruppo++."' and id_cons='$id_cons'";
			$reslnew = $dbi->prepare("$sql");
			$reslnew->execute();	
			list ($newidg,$newnumg) = $reslnew->fetch(PDO::FETCH_NUM);
			unset($valori);
		}	
		if($lista!=$row[1]){
			$lista=$row[1];
			$numcand=1;
			$valori = $id_cons.", null,'".$numlista."','$newidg','$newnumg','0','0',".$dbi->quote($row[1]).",'',null,''";
			$sql="insert into ".$prefix."_ele_lista values($valori)"; #echo "<br>TEST: $valori";
			try {
				$res_lista = $dbi->prepare("$sql");
				$res_lista->execute();
			}
			catch(PDOException $e)
			{
				echo $sql . "<br>" . $e->getMessage();
			}                  
				
			$sql="select id_lista,num_lista from ".$prefix."_ele_lista where num_lista='".$numlista++."' and id_cons='$id_cons'";
			$reslnew = $dbi->prepare("$sql");
			$reslnew->execute();	
			list ($newidl,$newnuml) = $reslnew->fetch(PDO::FETCH_NUM);
			unset($valori);
		}
		if(!isset($row[2])) continue;
		$valori="null,'$id_cons','$newidl','$newnuml',".$dbi->quote($row[2]).",'','','','".$numcand++."','','','0'";
		$sql="insert into ".$prefix."_ele_candidato values($valori)";  #die("VAL:$sql");
		try {
			$res_lista = $dbi->prepare("$sql");
			$res_lista->execute();
		}
		catch(PDOException $e)
		{
			echo "<br>sql:".$sql . "<br>" . $e->getMessage();
		}                  
		unset($valori);		
	}
	Header("Location: modules.php?op=27");

}

?>
  </div>
</section>

