<?php
header('Content-Type: text/plain');
ob_implicit_flush(true);
ob_end_flush();

echo "Inizio aggiornamento database...\n";
flush();

// simulazione aggiornamento passo per passo
sleep(1);
echo "Step 1: Connessione al DB...\n";
flush();
sleep(1);
echo "Step 2: Esecuzione script SQL...\n";
flush();
sleep(1);
echo "Step 3: Pulizia dati temporanei...\n";
flush();
sleep(1);
echo "Aggiornamento completato.\n";
flush();
