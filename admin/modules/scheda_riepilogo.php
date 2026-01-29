<?php 
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';
//$tipo_consultazione = "camera"; 
// possibili: regionali, camera, comunali, europee, referendum

$layout = [

    "regionali" => [
        "votanti" => true,
        "gruppi" => true,
        "uninominale" => false,
        "liste" => true,
		"referendum" => false
    ],

    "camera" => [
        "votanti" => true,
        "gruppi" => false,
        "uninominale" => true,
        "liste" => true,
		"referendum" => false
    ],

	"senato" => [
        "votanti" => true,
        "gruppi" => false,
        "uninominale" => true,
        "liste" => true,
		"referendum" => false
    ],

    "comunali" => [
        "votanti" => true,
        "gruppi" => true,
        "uninominale" => false,
        "liste" => true,
		"referendum" => false
    ],

    "europee" => [
        "votanti" => true,
        "gruppi" => false,
        "uninominale" => false,
        "liste" => true,
		"referendum" => false
    ],

    "referendum" => [
        "votanti" => true,
        "gruppi" => false,
        "uninominale" => false,
        "liste" => false,
		"referendum" => true
    ]

];

// ================== DEFAULT SICURI ==================

$iscritti = 0;
$uomini = 0;
$donne = 0;
$voti_uomini = 0;
$voti_donne = 0;
$voti_espressi = 0;

$pres_validi = 0;
$pres_nulle = 0;
$pres_bianche = 0;
$pres_contestati = 0;
$pres_non_validi = 0;

$uni_validi = 0;
$uni_nulle = 0;
$uni_bianche = 0;
$uni_contestati = 0;
$uni_non_validi = 0;

$liste_validi = 0;
$liste_nulli = 0;
$liste_contestati = 0;
$solo_candidato = 0;

$config = $layout[$tipo_consultazione];

?>

<section class="content pt-3">
<div class="container-fluid">

<?php if($config['votanti']) include "moduli_scheda_riepilogo/blocco_votanti.php"; ?>

<?php if($config['gruppi']) include "moduli_scheda_riepilogo/blocco_gruppi.php"; ?>

<?php if($config['uninominale']) include "moduli_scheda_riepilogo/blocco_uninominale.php"; ?>

<?php if($config['liste']) include "moduli_scheda_riepilogo/blocco_liste.php"; ?>

<?php if($config['referendum']) include "moduli_scheda_riepilogo/blocco_referendum.php"; ?>

</div>
</section>
