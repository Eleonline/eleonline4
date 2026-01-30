<?php
include_once __DIR__ . '/../../../config/config.php';
// Dati del comune  da eliminare se nella riga 17 si mettere il valore giusto
$row=dati_comune();
$descrizione=$row[0]['descrizione'];
$indirizzo=$row[0]['indirizzo'];
$fascia=$row[0]['indirizzo'];
$totale_sezioni=totale_sezioni();
$comune = [
    'abitanti' => 12000,
    'elettori' => 10500,
];
?>
<div class="card bg-light">
  <div class="card-header">
    <h3 class="card-title"><i class="fas fa-city"></i> Informazioni Comune</h3>
  </div>
  <div class="card-body">
    <p><strong>Nome:</strong> <?= $descrizione ?></p>
	<p><strong>Indirizzo:</strong> <?= $indirizzo ?></p>
    <p><strong>Abitanti:</strong> <?= number_format($comune['abitanti']) ?></p>
    <p><strong>Elettori:</strong> <?= number_format($comune['elettori']) ?></p>
    <p><strong>Sezioni:</strong> <?= $totale_sezioni ?></p>
  </div>
</div>
