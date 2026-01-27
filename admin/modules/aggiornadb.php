<?php
// query di aggiornamento   
$sql= "RENAME TABLE `soraldo_ele_candidati` TO `soraldo_ele_candidato`, `soraldo_ele_collegi` TO `soraldo_ele_collegio`, `soraldo_ele_comuni` TO `soraldo_ele_comune`, `soraldo_ele_comu_collegi` TO `soraldo_ele_comu_collegio`, `soraldo_ele_controlli` TO `soraldo_ele_controllo`, `soraldo_ele_documenti` TO `soraldo_ele_documento`, `soraldo_ele_fasce` TO `soraldo_ele_fascia`, `soraldo_ele_modelli` TO `soraldo_ele_modello`, `soraldo_ele_numeri` TO `soraldo_ele_numero`, `soraldo_ele_operatori` TO `soraldo_ele_operatore`, `soraldo_ele_province` TO `soraldo_ele_provincia`, `soraldo_ele_regioni` TO `soraldo_ele_regione`, `soraldo_ele_servizi` TO `soraldo_ele_servizio`, `soraldo_ele_sezioni` TO `soraldo_ele_sezione`, `soraldo_ele_temi` TO `soraldo_ele_tema`, `soraldo_ele_voti_candidati` TO `soraldo_ele_voti_candidato`, `soraldo_ws_funzioni` TO `soraldo_ws_funzione`, `soraldo_ws_sezioni` TO `soraldo_ws_sezione`";


// Simulazione di uno script di aggiornamento con output visibile in tempo reale
ob_start(); 
echo "<br>Inizio aggiornamento database...";
flush(); ob_flush();
sleep(1);

echo "<br>Connessione al database...";
flush(); ob_flush();
sleep(1);

// Simula una query riuscita
echo "<br><span style=\"color: green;\">- Query 5 eseguita correttamente</span>";
flush(); ob_flush();
sleep(1);

// Simula una query fallita
echo "<br><span style=\"color: red;\">- Aggiornamento Fallito: UPDATE tabella SET colonna = 'valore'</span>";
flush(); ob_flush();
sleep(1);

// Simula un'altra query
echo "<br><span style=\"color: green;\">- Query 2 completata</span>";
flush(); ob_flush();
sleep(1);

echo "<br>Fine aggiornamento database.";
flush(); ob_flush();
ob_end_flush();