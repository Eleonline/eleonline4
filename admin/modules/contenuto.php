<?php
require_once '../includes/check_access.php';

// Mappa delle pagine admin valide
$pagesMap = [
    0 => 'dashboard.php',
    1 => 'status_system.php',
    2 => 'logs.php',
    3 => 'setup_sito.php',
	4 => 'tema_colore.php',
	200 => 'config_hondt.php',
	6 => 'gestione_enti_comuni.php',
	7 => 'aggiornamento.php',
	8 => 'gestione_utenti.php',
	9 => 'gestione_consultazioni.php',
	10 => 'gestione_affluenza.php',
	11 => 'gestione_circoscrizioni.php',
	12 => 'gestione_sedi_elettorali.php',
	13 => 'gestione_sezioni.php',
	14 => 'gestione_informazioni.php',
	15 => 'gestione_informazioni.php',
	16 => 'gestione_informazioni.php',
	17 => 'gestione_informazioni.php',
	18 => 'gestione_sezioni.php',
	18 => 'pagina_in_costruzionei.php',
	19 => 'importa_dait.php',
	20 => 'pagina_in_costruzionei.php',
	21 => 'pagina_in_costruzionei.php',
	22 => 'autorizza_comuni.php',
	23 => 'carica_candidato.php',
	24 => 'carica_candidato.php',
	25 => 'carica_lista.php',
	26 => 'carica_candidati.php',
	27 => 'carica_candidato.php',
	28 => 'carica_lista.php',
	29 => 'carica_candidati.php',
	30 => 'carica_referndum.php',
	31 => 'test_spoglio.php',
	32 => 'spoglio_voti_lista.php',
	33 => 'pagina_in_costruzionei.php',
	34 => 'pagina_in_costruzionei.php',
	35 => 'pagina_in_costruzionei.php',
	36 => 'pagina_in_costruzionei.php',
	37 => 'pagina_in_costruzionei.php',
	38 => 'pagina_in_costruzionei.php',
	39 => 'pagina_in_costruzionei.php',
	40 => 'pagina_in_costruzionei.php',
	100 => 'template_di_pagina_di_esempio.php',
	101 => 'cambio_password.php',
	150 => '../ws/index.php',
    // importa_dait
	80 => 'dait_europee.php',
	81 => 'dait_politiche.php',
	// Aggiungi altre pagine admin qui
];

// Pagina di default
$defaultPage = 0;
// Prendo 'op' dalla query string e lo valido
if(isset($_POST['op'])) $op=$_POST['op'];
elseif(isset($_GET['op'])) $op = $_GET['op'];
else $op=$defaultPage;
// Forzo $op a intero (evito iniezioni)
$op = filter_var($op, FILTER_VALIDATE_INT, [
    'options' => ['default' => $defaultPage, 'min_range' => 0]
]);

// Se non esiste nella mappa, uso default
if (!array_key_exists($op, $pagesMap)) {
    $op = $defaultPage;
}
echo "<img src=\"../logo.jpg\" alt=\"\" />";
// Includo la pagina corrispondente
include($pagesMap[$op]);
