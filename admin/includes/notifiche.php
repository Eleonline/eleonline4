<?php
// notifiche_json.php

require_once '../includes/check_access.php';  // aggiusta il percorso se serve
require_once '../includes/versione.php';      // contiene $versione

/**
 * Funzione che recupera l'ultima revisione online
 */
function getUltimaRevisioneOnline() {
    $link_versione = "https://raw.githubusercontent.com/eleonline/eleonline/main/.github/workflows/build.yml";

    $file = @file_get_contents($link_versione);
    if ($file === false) {
        // Se non riesce a leggere, ritorna revisione 0 e tempo attuale
        return ["rev" => 0, "tempo" => date("d/m/Y H:i:s")];
    }

    $file = explode("\n", $file);
    $rev = 0;
    $tempo = "";
    foreach ($file as $line) {
        if (strpos($line, "revision") !== false) {
            $parts = explode(":", $line);
            if (isset($parts[1])) {
                $rev = (int)trim($parts[1]);
            }
        }
        if (strpos($line, "date") !== false) {
            $parts = explode(":", $line, 2);
            if (isset($parts[1])) {
                $tempo = trim($parts[1]);
            }
        }
    }

    return ["rev" => $rev, "tempo" => $tempo];
}

// --- INIZIO LOGICA NOTIFICHE ---

// Estrai la build corrente da $versione
preg_match('/rev\s*(\d+)/i', $versione, $match);
$build_corrente = isset($match[1]) ? (int)$match[1] : 0;

// Prendi info build online
$build_info = getUltimaRevisioneOnline();
$build_nuovo = $build_info['rev'];
$tempo_aggiornamento = $build_info['tempo'];

$notifiche = [];

// Se c'è una versione nuova disponibile
if ($build_nuovo > $build_corrente) {
    $notifiche[] = [
        'icona' => 'fas fa-info-circle',
        'testo' => "Aggiornamento disponibile (rev $build_nuovo)",
        'tempo' => $tempo_aggiornamento,
        'link' => 'modules.php?op=7'  // link alla pagina aggiornamento
    ];
}

// Esempio: aggiungi notifiche statiche per demo (puoi rimuoverle o ampliarle)
$notifiche[] = [
    'icona' => 'fas fa-envelope',
    'testo' => '1 nuovo messaggio',
    'tempo' => '3 min',
    'link' => '#'
];
$notifiche[] = [
    'icona' => 'fas fa-users',
    'testo' => '2 nuove richieste di accesso',
    'tempo' => '12 ore',
    'link' => '#'
];

// Output JSON con header corretto
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'count' => count($notifiche),
    'notifiche' => $notifiche
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

exit;
