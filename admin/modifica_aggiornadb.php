<?php
// questo adatta il file aggiornadb per la versione 4
// File di origine e destinazione
$inputFile = 'aggiornadb.php';      // Cambia con il nome del tuo file
$outputFile = 'modificatodb.php';    // Il file con gli echo aggiornati

// Leggi tutte le righe del file
$lines = file($inputFile);
$newLines = [];

// Aggiungo all’inizio apertura buffer
$newLines[] = "<?php\nob_start();\n";

foreach ($lines as $line) {
    // Ignoro eventuale apertura php iniziale già presente per evitare doppio <?php
    if (preg_match('/^\s*<\?php\s*$/', $line)) {
        // salto questa riga per evitare duplicati
        continue;
    }

    // Ignoro eventuale chiusura php finale per inserirla io dopo
    if (preg_match('/^\s*\?>\s*$/', $line)) {
        // salto questa riga per evitare duplicati
        continue;
    }

    $newLines[] = $line;

    // Controlla se la riga contiene un echo non commentato
    if (preg_match('/^\s*echo\s+.*?;/', $line)) {
        $indentation = str_repeat(' ', strlen($line) - strlen(ltrim($line)));
        $newLines[] = $indentation . "flush(); ob_flush();\n";
    }
}

// Aggiungo chiusura buffer e chiusura php
$newLines[] = "ob_end_flush();\n?>";


// Salva nel nuovo file
file_put_contents($outputFile, implode('', $newLines));

echo "Modifica completata. Controlla il file: $outputFile\n";
?>

