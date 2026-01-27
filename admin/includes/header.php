<?php
include_once __DIR__ . '/../access.php';
include_once __DIR__ . '/../config/config.php';
// Dati del comune  da eliminare se nella riga 17 si mettere il valore giusto
$comune = [
    'nome' => 'Comune di esempio',
    'abitanti' => 12000,
    'superficie_km2' => 35.7,
    'elettori' => 10500,
    'sezioni' => 15,
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8" >
  <title>Eleonline - <?= htmlspecialchars($comune['nome']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Font Awesome (locale) -->
<link rel="stylesheet" href="../assets/css/all.min.css" >
<!-- AdminLTE CSS (locale) -->
<link rel="stylesheet" href="../css/adminlte.min.css" >
<!-- SortableJS (locale) -->
<script src="../assets/js/Sortable.min.js"></script>
<link rel="stylesheet" href="../css/altricss.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<!-- Dopo AdminLTE 
<link rel="stylesheet" href="../css/compact.css">-->

  <!-- Font Awesome 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />-->
  <!-- AdminLTE 
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" />CSS 
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>-->
  
  
</head>
 <body class="hold-transition sidebar-mini">
<!--<body class="hold-transition sidebar-mini sidebar-collapse layout-footer-fixed">-->
<div class="wrapper">
