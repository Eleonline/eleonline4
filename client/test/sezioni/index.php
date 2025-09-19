<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Stato Sezioni</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-italia@2.7.2/dist/css/bootstrap-italia.min.css">
  <style>
    .barra-sezioni {
      display: flex;
      flex-wrap: nowrap;
      overflow-x: auto;
      padding: 5px;
      border: 1px solid #000;
      background-color: #f8f9fa;
    }

    .sezione {
      width: 28px;
      height: 18px;
      margin-right: 3px;
      border-radius: 3px;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: bold;
      color: #000;
      border: 1px solid #ccc;
    }

    .giallo { background-color: #FFD700; }
    .rosso  { background-color: #DC143C; }
    .verde  { background-color: #28a745; }

    .barra-sezioni::-webkit-scrollbar {
      height: 5px;
    }

    .barra-sezioni::-webkit-scrollbar-thumb {
      background-color: #999;
      border-radius: 4px;
    }

    .barra-sezioni::-webkit-scrollbar-track {
      background: transparent;
    }
  </style>
</head>
<body>

<div class="container my-4">
  <h5>Stato sezioni</h5>
  <div class="barra-sezioni">

<?php
// Simulazione array sezioni (id => stato colore)
$sezioni = [
    1 => 'giallo',
    2 => 'rosso',
    3 => 'verde',
    4 => 'giallo',
    5 => 'rosso',
    6 => 'verde',
    7 => 'verde',
    8 => 'giallo',
    9 => 'rosso',
    10 => 'verde',
    11 => 'giallo',
    12 => 'verde',
    13 => 'rosso',
    14 => 'giallo',
    15 => 'verde'
];

// Genera i rettangolini numerati
foreach ($sezioni as $numero => $stato) {
    echo '<div class="sezione ' . htmlspecialchars($stato) . '">' . $numero . '</div>';
}
?>

  </div>
</div>

</body>
</html>
