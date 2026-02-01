<?php
function colora_sezione() {
    if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
        header("Location: ../index.php");
        exit;
    }

    global $id_cons,$id_circ,$id_sez,$dbi,$prefix,$genere,$id_cons_gen;

    // Controllo consultazione
    $sql = "SELECT t1.voto_c,circo 
            FROM ".$prefix."_ele_tipo as t1 
            LEFT JOIN ".$prefix."_ele_consultazione as t2 ON t1.tipo_cons=t2.tipo_cons 
            WHERE id_cons_gen='$id_cons_gen'";
    $res = $dbi->prepare($sql);
    $res->execute();
    list($votoc,$circo) = $res->fetch(PDO::FETCH_NUM);
    $iscirco = $circo ? "AND id_circ=$id_circ" : '';

    // Controllo errori
    $sql = "SELECT * FROM ".$prefix."_ele_controllo WHERE id_cons='$id_cons' AND id_sez='$id_sez'";
    $resc = $dbi->prepare($sql);
    $resc->execute();
    $perr = $resc->rowCount();

    $sezstat = 0;

    if($perr) {
        $sezstat = 1;
        $sql = "UPDATE ".$prefix."_ele_sezione SET colore='#dc3545' WHERE id_cons='$id_cons' AND id_sez='$id_sez'"; // Rosso
        $res = $dbi->prepare($sql);
        $res->execute();
    } else { 
        // Candidati
        $sql = "SELECT t2.id_lista 
                FROM ".$prefix."_ele_voti_candidato as t1 
                LEFT JOIN ".$prefix."_ele_candidato as t2 ON t1.id_cand=t2.id_cand 
                WHERE t1.id_cons='$id_cons' AND t1.id_sez='$id_sez' 
                GROUP BY t2.id_lista";
        $res = $dbi->prepare($sql);
        $res->execute();
        $liste = $res->rowCount();
        list($listescru) = $res->fetch(PDO::FETCH_NUM);
        if($res->rowCount() && $listescru==0) { $listescru=1; $liste=0; }

        // Conteggio liste totali
        $sql = "SELECT count(id_lista) FROM ".$prefix."_ele_lista WHERE id_cons='$id_cons' $iscirco";
        $res = $dbi->prepare($sql);
        $res->execute();
        list($ltot) = $res->fetch(PDO::FETCH_NUM);

        if($liste && $liste==$ltot){
            $sezstat = 2;
            $sql = "UPDATE ".$prefix."_ele_sezione SET colore='#28a745' WHERE id_cons='$id_cons' AND id_sez='$id_sez'"; // Verde
            $res = $dbi->prepare($sql);
            $res->execute();
        } elseif($liste){
            $sezstat = 2;
            $sql = "UPDATE ".$prefix."_ele_sezione SET colore='#ffc107' WHERE id_cons='$id_cons' AND id_sez='$id_sez'"; // Giallo
            $res = $dbi->prepare($sql);
            $res->execute();
        }

        if(!$sezstat) { 
            // Liste o gruppi
            if($genere==2)
                $sql = "SELECT id_gruppo FROM ".$prefix."_ele_voti_gruppo WHERE id_cons='$id_cons' AND id_sez='$id_sez'";
            else
                $sql = "SELECT id_lista FROM ".$prefix."_ele_voti_lista WHERE id_cons='$id_cons' AND id_sez='$id_sez'";
            $res = $dbi->prepare($sql);
            $res->execute();

            if($res->rowCount() > 0){
                $sezstat = 3;
                if($genere>3 && !$votoc)
                    $sql = "UPDATE ".$prefix."_ele_sezione SET colore='#17a2b8' WHERE id_cons='$id_cons' AND id_sez='$id_sez'"; // Turquoise
                else
                    $sql = "UPDATE ".$prefix."_ele_sezione SET colore='#28a745' WHERE id_cons='$id_cons' AND id_sez='$id_sez'"; // Verde
                $res = $dbi->prepare($sql);
                $res->execute();
            } elseif(!$sezstat){ 
                // Gruppi
                if($genere != 4){
                    if($genere)
                        $sql = "SELECT id_gruppo FROM ".$prefix."_ele_voti_gruppo WHERE id_sez='$id_sez'";
                    else{
                        $sql = "SELECT id_gruppo FROM ".$prefix."_ele_gruppo WHERE id_cons='$id_cons'";
                        $res = $dbi->prepare($sql);
                        $res->execute();
                        $righeref = $res->rowCount();
                        $sql = "SELECT id_gruppo FROM ".$prefix."_ele_voti_ref WHERE id_sez='$id_sez'";
                    }
                    $res = $dbi->prepare($sql);
                    $res->execute();
                    $righe = $res->rowCount();
                } else $righe = 0;

                if($righe){ 
                    $sezstat = 4;
                    if(($genere==0 && $righe==$righeref) || $genere==1)
                        $sql = "UPDATE ".$prefix."_ele_sezione SET colore='#28a745' WHERE id_cons='$id_cons' AND id_sez='$id_sez'"; // Verde
                    else
                        $sql = "UPDATE ".$prefix."_ele_sezione SET colore='#fd7e14' WHERE id_cons='$id_cons' AND id_sez='$id_sez'"; // Arancio
                    $res = $dbi->prepare($sql);
                    $res->execute();
                } elseif(!$sezstat){ 
                    // Voti
                    $sql = "SELECT validi+nulli+bianchi+contestati as voti FROM ".$prefix."_ele_sezione WHERE id_cons='$id_cons' AND id_sez='$id_sez'";
                    $res = $dbi->prepare($sql);
                    $res->execute();
                    list($voti) = $res->fetch(PDO::FETCH_NUM);

                    if($voti) {
                        $sezstat = 5;
                        $sql = "UPDATE ".$prefix."_ele_sezione SET colore='#6f42c1' WHERE id_cons='$id_cons' AND id_sez='$id_sez'"; // Viola
                        $res = $dbi->prepare($sql);
                        $res->execute();
                    } elseif(!$sezstat) {
                        $sql = "SELECT count(0) FROM ".$prefix."_ele_voti_parziale WHERE id_cons='$id_cons' AND id_sez='$id_sez'";
                        $res = $dbi->prepare($sql);
                        $res->execute();
                        list($righe) = $res->fetch(PDO::FETCH_NUM);
                        $num_ril = $righe % 4;

                        if($num_ril==0) {$cursez="#007bff";}    // Blu
                        elseif($num_ril==1) {$cursez="#20c997";}  // Intermedio 1
                        elseif($num_ril==2) {$cursez="#fd86a6";}  // Intermedio 2
                        elseif($num_ril==3) {$cursez="#6bc0ff";}  // Intermedio 3
                        $sql = "UPDATE ".$prefix."_ele_sezione SET colore='$cursez' WHERE id_cons='$id_cons' AND id_sez='$id_sez'";
                        $res = $dbi->prepare($sql);
                        $res->execute();
                    }
                }
            }
        }
    }
}
?>

<?php
<?php
function converti_colore_to_4() {
    if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
        header("Location: ../index.php");
        exit;
    }

    global $dbi, $prefix;

    // Preleva tutte le sezioni
    $sql = "SELECT id_cons, id_sez, colore FROM ".$prefix."_ele_sezione";
    $res = $dbi->prepare($sql);
    $res->execute();
    $sezioni = $res->fetchAll(PDO::FETCH_ASSOC);

    // Mappa dei colori legacy verso i nuovi HEX definiti
    $mappa_colori = [
        '#ff3300' => '#dc3545',  // Errore
        '#99cc33' => '#28a745',  // Completato
        '#99ee33' => '#ffc107',  // Parziale
        '#48d1cc' => '#17a2b8',  // Turquoise
        '#b0c4de' => '#fd7e14',  // Da verificare
        '#f5deb3' => '#6f42c1',  // Voti inseriti
        '#dcdcdc' => '#007bff',  // Stato iniziale
        '#add8e6' => '#20c997',  // Intermedio 1
        '#7fffd4' => '#fd86a6',  // Intermedio 2
        '#e0ffff' => '#6bc0ff',  // Intermedio 3
    ];

    foreach ($sezioni as $sez) {
        // Se colore è NULL o vuoto, impostiamo il colore iniziale blu
        $colore_old = strtolower(trim($sez['colore'] ?? ''));
        if ($colore_old === '') {
            $colore_old = '#007bff'; // default: stato iniziale
        }

        // Se il colore esiste nella mappa, converte
        $colore_new = $mappa_colori[$colore_old] ?? $colore_old;

        // Aggiorna il colore nel DB solo se è cambiato
        if ($colore_new !== $sez['colore']) {
            $sql_update = "UPDATE ".$prefix."_ele_sezione 
                           SET colore=:colore 
                           WHERE id_cons=:id_cons AND id_sez=:id_sez";
            $stmt = $dbi->prepare($sql_update);
            $stmt->execute([
                ':colore' => $colore_new,
                ':id_cons' => $sez['id_cons'],
                ':id_sez' => $sez['id_sez']
            ]);
        }
    }

    echo "Conversione colori completata secondo la mappa finale!";
}
?>

