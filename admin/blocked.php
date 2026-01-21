<?php
$ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Accesso bloccato</title>
<link rel="stylesheet" href="css/adminlte.min.css">
</head>
<body class="hold-transition login-page">

<div class="login-box">
  <div class="card card-danger">
    <div class="card-header text-center">
      <h3>ACCESSO BLOCCATO</h3>
    </div>

    <div class="card-body text-center">

      <p><strong>Il tuo indirizzo IP è stato bloccato.</strong></p>

      <p>
      IP rilevato:<br>
      <span style="font-size:20px;color:red">
      <?= htmlspecialchars($ip) ?>
      </span>
      </p>

      <hr>

      <p>
      Contatta l'amministratore di sistema per lo sblocco.
      </p>

    </div>
  </div>
</div>

</body>
</html>
