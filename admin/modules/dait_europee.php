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
// global $id_comune;
// $sql="select id_cons from ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen' and id_comune='$id_comune'";
// $res = $dbi->prepare("$sql");
// $res->execute();	
// list($id_cons)=$res->fetch(PDO::FETCH_NUM);
// include("modules/Elezioni/ele.php"); 

//Funzione per scaricare il file CSV e verificare che venga scaricato correttamente die("TEST: qui");
// Protezione da accesso diretto
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("Location: ../index.php");
    exit;
}

function downloadCSV($url) {
    $data = @file_get_contents($url);
    if ($data === false) {
        return false;
    }
    return $data;
}
// Variabili iniziali
global $id_cons_gen, $id_comune,$prefix,$dbi;
$id_cons=$_SESSION['id_cons'];
$fileUrl = '';
$csvData = [];
$header = [];
$filteredData = [];
$circoscrizioni = [];
$circoscrizione = '';
$caricamentoCompletato = false;
$errorMessage = '';

// Gestione della logica per "No"
if (isset($_POST['verifica']) && $_POST['verifica'] === 'no') {
    $fileUrl = '';
    $csvData = [];
    $header = [];
    $filteredData = [];
    $circoscrizioni = [];
    $circoscrizione = '';
} elseif (isset($_POST['csv_url']) && !empty($_POST['csv_url'])) {
    $fileUrl = $_POST['csv_url'];
    // Verifica se l'URL è valido
    if (filter_var($fileUrl, FILTER_VALIDATE_URL) === false) {
        $errorMessage = "URL non valido.";
    } else {
        // Scarica il file CSV
        $data = downloadCSV($fileUrl);

        if ($data === false) {
            $errorMessage = "CSV non valido."; // Se non riesce a scaricare il CSV
        } else {
            // Legge il file CSV in righe
            $lines = explode("\n", $data);
            $csvData = [];
            $test  = str_getcsv($lines[0], ';');
			if(count($test)>1) $div=';'; // Usa ';' come delimitatore
			else $div=',';				 // Usa ',' come delimitatore
            // Parsa il CSV riga per riga
            foreach ($lines as $line) {
                if (trim($line)) {
                    $csvData[] = str_getcsv($line, $div); 
                }
            }

            // Se il CSV è vuoto
            if (count($csvData) === 0) {
                $errorMessage = "CSV non valido."; // Se il file è vuoto
            } else {
                // Trova le intestazioni delle colonne
				$header = $csvData[0];
                $header = array_map('trim', $header); // Rimuove spazi extra dalle intestazioni

                // Verifica che tutte le colonne necessarie siano presenti
                $requiredColumns = [
                    'DESCR_ENTE' => 'circoscrizioneColIndex',
                    'DESCR_LISTA' => 'listaColIndex',
                    'CAND_NOME' => 'nomeColIndex',
                    'CAND_COGNOME' => 'cognomeColIndex',
                    'ALT_NOME' => 'altNomeColIndex',
                    'ALT_NOME_1' => 'altNome1ColIndex',
                    'DATA_NASCITA' => 'dataNascitaColIndex',
                    'LUOGO_NASCITA' => 'luogoNascitaColIndex'
                ];

                foreach ($requiredColumns as $columnName => $varName) {
                    ${$varName} = array_search($columnName, $header);
                    if (${$varName} === false) {
                        $errorMessage = "CSV non valido."; // Se manca una colonna obbligatoria
                    }
                }

                if (empty($errorMessage)) {
                    // Estrai le circoscrizioni uniche
                    foreach ($csvData as $row) {
                        if (isset($row[$circoscrizioneColIndex]) && !in_array($row[$circoscrizioneColIndex], $circoscrizioni)) {
                            $circoscrizioni[] = $row[$circoscrizioneColIndex];
                        }
                    }

                    // Gestione del filtro per circoscrizione
                    $circoscrizione = isset($_POST['circoscrizione']) ? $_POST['circoscrizione'] : '';
					$lista='';
                    foreach ($csvData as $row) {
                        if (isset($row[$circoscrizioneColIndex]) && $row[$circoscrizioneColIndex] === $circoscrizione) {
                            // Crea la colonna unificata "Nome"
                            $row[$nomeColIndex] = trim($row[$nomeColIndex] . ' ' . $row[$altNomeColIndex] . ' ' . $row[$altNome1ColIndex]);

                            // Rimuove colonne non necessarie
                            unset($row[$circoscrizioneColIndex], $row[$altNomeColIndex], $row[$altNome1ColIndex]);
							$filteredData[] = $row;
                        }
                    }

                    // Rimuove colonne non necessarie dall'intestazione
                    unset($header[$circoscrizioneColIndex], $header[$altNomeColIndex], $header[$altNome1ColIndex]);
                    $header[$nomeColIndex] = 'Nome'; // Modifica il nome della colonna unificata
                    $header[$cognomeColIndex] = 'Cognome'; // Modifica il nome della colonna unificata
                    $header[$dataNascitaColIndex] = 'Data di nascita';
                    $header[$luogoNascitaColIndex] = 'Luogo di nascita';
                    $header[$listaColIndex] = 'Liste';
                }
            }
        }
    }
}

// Simulazione caricamento nel database
if (isset($_POST['verifica']) && $_POST['verifica'] === 'si') {
#    $caricamentoCompletato = true; // Simula il successo del caricamento
	$sql="delete from ".$prefix."_ele_lista where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$sql="delete from ".$prefix."_ele_voti_lista where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$sql="delete from ".$prefix."_ele_candidati where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$sql="delete from ".$prefix."_ele_voti_candidati where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	
	$sql="delete from ".$prefix."_ele_voti_parziale where id_cons='$id_cons'";
	$reslnew = $dbi->prepare("$sql");
	$reslnew->execute();	

	$lista='';
	$numlista=1;	
	foreach ($filteredData as $row){ 
		if($lista!=$row[1]){
			$lista=$row[1];
			$numcand=1;
			$valori = $id_cons.", null,'".$numlista."','0','0','0','0',".$dbi->quote($row[1]).",'',null,''";
			$sql="insert into ".$prefix."_ele_lista values($valori)";
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
		$valori="null,'$id_cons','$newidl','$newnuml',".$dbi->quote($row[3]).",".$dbi->quote($row[2]).",'','','".$numcand++."','','','0'";
		$sql="insert into ".$prefix."_ele_candidati values($valori)";
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
	Header("Location: modules.php?op=80&id_cons_gen=$id_cons_gen");

}
//ele();

?>

<?php require_once '../includes/check_access.php'; ?>
<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cogs me-2"></i>Importazione Liste e Candidati - Europee</h3>
      </div>
      <div class="card-body">
        <?php if (empty($csvData) && empty($errorMessage)) : ?>
          <h5>Procedura per scaricare Liste e Candidati:</h5>
          <ol>
            <li>Andare sul sito del Ministero dell'interno nell'Elenco Trasparenza: 
              <a href="https://dait.interno.gov.it/elezioni/trasparenza" target="_blank">https://dait.interno.gov.it/elezioni/trasparenza</a>
            </li>
            <li>Entra nella pagina dell'elezione da caricare</li>
            <li>Verificare se ci sono i contrassegni e salvarli in una cartella</li>
            <li>Andare su "Lista e Candidati" e copiare il link del file.csv</li>
            <li>Inserire il link qui sotto e procedere</li>
          </ol>

          <form method="post" action="modules.php">
            <!--input type="hidden" name="op" value="importadaiteuro"-->
            <input type="hidden" name="op" value="80">
            <div class="form-group">
              <label for="csv_url">URL del CSV:</label>
              <input type="text" class="form-control" id="csv_url" name="csv_url" value="<?php echo htmlspecialchars($fileUrl); ?>" placeholder="Inserisci l'URL del CSV">
            </div>
            <button type="submit" class="btn btn-primary mt-2">Carica CSV</button>
          </form>

        <?php elseif (!empty($errorMessage)) : ?>
          <form method="post" action="modules.php">
            <!--input type="hidden" name="op" value="importadaiteuro"-->
            <input type="hidden" name="op" value="80">
            <div class="form-group">
              <label for="csv_url">URL del CSV:</label>
              <input type="text" class="form-control" id="csv_url" name="csv_url" value="<?php echo htmlspecialchars($fileUrl); ?>" placeholder="Inserisci l'URL del CSV">
            </div>
            <button type="submit" class="btn btn-primary mt-2">Carica CSV</button>
          </form>
          <p class="text-danger mt-2"><?php echo htmlspecialchars($errorMessage); ?></p>

        <?php elseif (empty($circoscrizione)) : ?>
          <h5>Scegli una Circoscrizione</h5>
          <form method="post" action="modules.php">
            <input type="hidden" name="csv_url" value="<?php echo htmlspecialchars($fileUrl); ?>">
            <!--input type="hidden" name="op" value="importadaiteuro"-->
            <input type="hidden" name="op" value="80">
            <div class="form-group">
              <label for="circoscrizione">Circoscrizione:</label>
              <select name="circoscrizione" id="circoscrizione" class="form-control">
                <option value="">-- Seleziona una Circoscrizione --</option>
                <?php foreach ($circoscrizioni as $circ) : ?>
                  <option value="<?php echo htmlspecialchars($circ); ?>" <?php echo ($circoscrizione === $circ) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($circ); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Filtra</button>
          </form>

        <?php elseif ($caricamentoCompletato) : ?>
          <div class="alert alert-success">I dati sono stati caricati nel database.</div>

        <?php else : ?>
          <h5>Dati per la circoscrizione: <strong><?php echo htmlspecialchars($circoscrizione); ?></strong></h5>
          <form method="post" action="modules.php?op=80" class="mb-3">
            <input type="hidden" name="csv_url" value="<?php echo htmlspecialchars($fileUrl); ?>">
            <!--input type="hidden" name="op" value="importadaiteuro"-->
            <input type="hidden" name="id_cons_gen" value="<?php echo $id_cons_gen; ?>">
            <input type="hidden" name="circoscrizione" value="<?php echo htmlspecialchars($circoscrizione); ?>">
            <p>I dati sono corretti?</p>
            <button type="submit" name="verifica" value="si" class="btn btn-success me-2">Sì</button>
            <button type="submit" name="verifica" value="no" class="btn btn-danger">No</button>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="table-primary">
                <tr>
                  <?php foreach ($header as $col) : ?>
                    <th><?php echo htmlspecialchars($col); ?></th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($filteredData as $row) : ?>
                  <tr>
                    <?php foreach ($row as $cell) : ?>
                      <td><?php echo htmlspecialchars($cell); ?></td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <div class="card-footer text-end">
        <!-- Eventuale footer -->
      </div>
    </div>
  </div>
</section>

