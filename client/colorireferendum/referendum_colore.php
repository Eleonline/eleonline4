<?php
include 'config_colori_quesiti.php';

$colore = $_POST['colore'] ?? '';

// Trova info scheda selezionata
$schedaSelezionata = null;
foreach ($coloriQuesiti as $info) {
    if ($info['colore'] === $colore) {
        $schedaSelezionata = $info;
        break;
    }
}

$immagineScheda = $schedaSelezionata['immagine'] ?? '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Referendum - Colore Scheda Ufficiale</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    table {
      border-collapse: collapse;
      margin-bottom: 20px;
      
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      vertical-align: top;
    }
    select {
      width: 100%;
      padding: 5px;
    }
    .immagine-scheda {
      max-width: 50px;
      height: auto;
      display: block;
      margin: 0 auto;
    }
  </style>
</head>
<body>

  <h2>Seleziona il Colore Ufficiale della Scheda Referendum</h2>

  <form method="POST">
    <table>
      <thead>
        <tr>
          <th>Colore</th>
        </tr>
      </thead>
      <tbody>
  <tr>
    <td>
      <select name="colore" onchange="this.form.submit()">
        <option value="" <?= $colore === '' ? 'selected' : '' ?>>Nessun colore</option>
        <?php
        foreach ($coloriQuesiti as $numero => $info) {
            $selected = ($colore == $numero) ? 'selected' : '';
            echo "<option value=\"$numero\" $selected>{$info['nome']}</option>";
        }
        ?>
      </select>
    </td>
  </tr>
  <tr>
    <td style="background-color: <?= isset($coloriQuesiti[$colore]) ? $coloriQuesiti[$colore]['colore'] : '' ?>; text-align: center;">
      <?php
      if (isset($coloriQuesiti[$colore])) {
          $immagineScheda = $coloriQuesiti[$colore]['immagine'];
          if ($immagineScheda && file_exists($immagineScheda)) {
              echo "<img src=\"$immagineScheda\" alt=\"Fac-simile scheda\" style=\"max-width: 50px; height: auto; display: block; margin: 0 auto;\">";
          } else {
              echo "<em>⚠️ Immagine non trovata.</em>";
          }
      } else {
          echo "<em></em>";
      }
      ?>
    </td>
  </tr>
</tbody>
    </table>
  </form>

</body>
</html>
