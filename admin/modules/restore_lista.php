<?php
require_once '../includes/check_access.php';

// Le variabili necessarie
global $LINK,$fileback,$id_cons,$id_cons_gen,$prefix,$dbi;

$log = [];
$errore = 0;

// Percorso del file di backup
$filedati="../documenti/backup/file_bak_".$id_cons.".txt";

// Controllo se il file esiste
if(!file_exists($filedati)){
    $errore = 1;
    $log[] = "File di backup non trovato: $filedati";
} 
// Se il file esiste e il pulsante è stato premuto, esegui il ripristino
else if(isset($_POST['startRestore'])) {

    $handle = fopen($filedati, "r");
    $arrFile = file($filedati);
    fclose($handle);
    $test=array();

    // Set counters
    $currentLine = 0;
    $cntFile = count($arrFile);

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
                if(isset($tabs[($x+1)]) && $appo==$tabs[$x+1]){ $x++; break;}
                elseif($appo=='') { $x++; break; }
                $conta[$x]++;
                $currentLine++;
            }
        } else {
            $scarto++;
            $currentLine++;
        }
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
                    if(isset($tabs[($x+1)]) && $appo==$tabs[$x+1]){ $x++; break;}
                    elseif($appo=='') { $x++; break; }

                    if(isset($arrFile[$currentLine]))                
                        $test=explode(':',$arrFile[$currentLine]); 
                    if(!is_array($test)) { 
                        $log[] = "Errore di import"; 
                        break; 
                    }

                    $valori='';
                    foreach($test as $key=>$val) {
                        if($key==0){
                            $valori.= "'".base64_decode($val)."'";
                            if ($y==0) {
                                $idcns=$valori;
                                $y++;
                                foreach($tabs as $tbs){
                                    if($tbs==$prefix."_ele_cons_comune" or $tbs==$prefix."_ele_rilaff")
                                        $sql="delete from $tbs where id_cons_gen=$id_cons_gen";
                                    else
                                        $sql="delete from $tbs where id_cons=$idcns";
                                    $res_del = $dbi->prepare("$sql");
                                    $res_del->execute();    
                                } 
                            }
                        } else {
                            $valori.= ",'".addslashes(base64_decode($val))."'";
                        }
                    }

                    $sql="insert into $tab values($valori)";
                    try {                
                        $res_comune = $dbi->prepare("$sql");
                        $res_comune->execute();    
                        $log[] = "Riga importata correttamente: $valori";
                    }
                    catch(PDOException $e)
                    {
                        $log[] = "Errore: " . $e->getMessage();
                    }

                    $currentLine++;
                }
            }
        }
    } else {
        $errore = 1;
        $log[] = "Struttura backup non valida!";
    }
}
?>

<section class="content">
  <div class="container-fluid">

    <h2><i class="fas fa-database"></i> Ripristino Backup Eleonline</h2>

    <div class="card card-primary shadow-sm mb-3">
      <div class="card-header">
        <h3 class="card-title">Ripristina Backup</h3>
      </div>

      <div class="card-body">
        <p>Premi il bottone per avviare il ripristino dei dati dal backup Eleonline. Il processo potrebbe richiedere alcuni minuti a seconda della dimensione del backup.</p>
        
        <!-- Bottone che apre il modal -->
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#confirmRestoreModal">
            Avvia Ripristino
        </button>

        <div class="mt-3 table-responsive" style="max-height:400px; overflow:auto;">
          <table class="table table-bordered table-hover text-sm">
            <thead class="thead-light">
              <tr>
                <th>Messaggio</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if(!empty($log)){
                    foreach($log as $r){
                        echo "<tr><td>$r</td></tr>";
                    }
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer">
        <?php 
        if($errore){
            echo '<span class="text-danger">Ripristino fallito!</span>';
        } else if(!empty($log)){
            echo '<span class="text-success">Ripristino completato '.date('d/m/Y H:i').'</span>';
        }
        ?>
      </div>
    </div>

  </div>
</section>

<!-- Modal conferma -->
<div class="modal fade" id="confirmRestoreModal" tabindex="-1" aria-labelledby="confirmRestoreLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="confirmRestoreLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Conferma Ripristino
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Sei sicuro di voler avviare il ripristino dei dati dal backup? Tutti i dati esistenti potrebbero essere sovrascritti.
      </div>
      <div class="modal-footer">
        <form method="post">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
            <button type="submit" name="startRestore" class="btn btn-warning">Ripristina</button>
        </form>
      </div>
    </div>
  </div>
</div>
