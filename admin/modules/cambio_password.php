<?php
require_once '../includes/check_access.php';

// Simulazione utente loggato
//$_SESSION['username'] = $_SESSION['username'] ?? 'mario.rossi';
$username = $_SESSION['username'];
$messaggio = '';

// Connessione MySQL con PDO (commentata)
// require_once '../includes/db_connection.php'; // Assicurati che questo file definisca $pdo
#if (!function_exists('cambio_password')) {
    function cambio_password($vecchia_password, $nuova_password) {
		global $prefix,$aid,$dbi,$id_comune;
        $username = $_SESSION['username'];
#		$vecchia_password=md5($vecchia_password);# die("UPDATE ".$prefix."_authors SET pwd = '$hash' WHERE aid = '$username'");
        // Recupero hash corrente
        $stmt = $dbi->prepare("SELECT pwd FROM ".$prefix."_authors WHERE aid = '$username'");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || $vecchia_password!=$row['pwd']) {
            return 'Vecchia password errata.';
        }
        // Aggiornamento con nuova password
#        $hash = md5($nuova_password);
        $stmt = $dbi->prepare("UPDATE ".$prefix."_authors SET pwd = '$nuova_password' WHERE aid = '$username'");
        $stmt->execute();
		if($stmt->rowCount())
        return true;
		else return 'Errore durante l\'aggiornamento.';
      
    }
#}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vecchia_password = $_POST['vecchia_password'];
    $nuova_password = $_POST['nuova_password'];
    $conferma_password = $_POST['conferma_password'];

    if ($nuova_password !== $conferma_password) {
        $messaggio = '<div class="alert alert-danger">Le nuove password non coincidono.</div>';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $nuova_password)) {
        $messaggio = '<div class="alert alert-warning">La nuova password deve contenere almeno 8 caratteri, una maiuscola, una minuscola, un numero e un carattere speciale.</div>';
    } else {
        $test = cambio_password(md5($vecchia_password), md5($nuova_password));
        if ($test === true)
         $messaggio = <<<HTML
	 alert(<?= $test ?>)

HTML;

        elseif (is_string($test))
            $messaggio = '<div class="alert alert-danger">'.$test.'</div>';
        else
            $messaggio = '<div class="alert alert-danger">Aggiornamento password fallito. (simulazione)</div>';
    }
}
?>


<section class="content">
  <div class="container-fluid mt-4">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-key me-2"></i>Cambio Password</h3>
      </div>
      <form method="post" onsubmit="return validaPassword(2);">
        <div class="card-body">

          <?php echo $messaggio; ?>

          <div class="form-group">
            <label>Utente:</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($username); ?>" disabled>
          </div>

          <div class="form-group mt-3">
            <label for="vecchia_password">Vecchia Password</label>
            <div class="input-group">
              <input type="password" name="vecchia_password" id="vecchia_password" class="form-control" required>
              <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('vecchia_password', this)">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-group mt-3">
            <label for="nuova_password">Nuova Password</label>

            <!-- Requisiti -->
            <ul id="requisitiPassword" class="mt-2 small text-muted">
              <li id="requisito-lunghezza">❌ Almeno 8 caratteri</li>
              <li id="requisito-maiuscola">❌ Una lettera maiuscola</li>
              <li id="requisito-minuscola">❌ Una lettera minuscola</li>
              <li id="requisito-numero">❌ Un numero</li>
              <li id="requisito-speciale">❌ Un carattere speciale</li>
            </ul>

            <div class="input-group">
              <input type="password" name="nuova_password" id="nuova_password" class="form-control" required oninput="valutaForzaPassword()" onblur="validaPassword(1)">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('nuova_password', this)">
                <i class="fas fa-eye"></i>
              </button>
            </div>

            <!-- Barra forza -->
            <div class="mt-2">
              <div id="barraForza" style="height: 8px; background-color: #e0e0e0; border-radius: 4px;">
                <div id="forzaLivello" style="height: 100%; width: 0%; background-color: red; border-radius: 4px;"></div>
              </div>
              <small id="testoForza" class="form-text text-muted mt-1"></small>
            </div>
          </div>

          <div class="form-group mt-3">
            <label for="conferma_password">Conferma Nuova Password</label>
            <div class="input-group">
              <input type="password" name="conferma_password" id="conferma_password" class="form-control" required oninput="controllaConfermaPassword()">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('conferma_password', this)">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <small id="messaggioConferma" class="form-text mt-1" style="display: none;"></small>
          </div>

        </div>
        <div class="card-footer text-end">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Salva</button>
        </div>
      </form>
    </div>
  </div>
</section>

<script>
function validaPassword(passo) {
  const nuova = document.getElementById('nuova_password').value;
  const conferma = document.getElementById('conferma_password').value;
  const complessa = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

  if (nuova !== conferma && passo === 2) {
    alert("Le nuove password non coincidono.");
    return false;
  }

  if (!complessa.test(nuova)) {
    alert("La nuova password deve contenere almeno 8 caratteri, una maiuscola, una minuscola, un numero e un carattere speciale.");
    return false;
  }

  return true;
}

function valutaForzaPassword() {
  const password = document.getElementById('nuova_password').value;
  const barra = document.getElementById('forzaLivello');
  const testo = document.getElementById('testoForza');
  const conferma = document.getElementById('conferma_password');

  let forza = 0;
  if (password.length >= 8) forza++;
  if (password.length >= 12) forza++;
  if (/[a-z]/.test(password)) forza++;
  if (/[A-Z]/.test(password)) forza++;
  if (/\d/.test(password)) forza++;
  if (/[\W_]/.test(password)) forza++;

  let colore = 'red', larghezza = '20%', messaggio = 'Password debole';

  if (forza < 4) {
    colore = 'red'; larghezza = '20%'; messaggio = 'Password debole'; 
    conferma.disabled = true; 
    conferma.value = '';
  } else if (forza < 6) {
    colore = 'orange'; larghezza = '60%'; messaggio = 'Password forte'; 
    conferma.disabled = false;
  } else {
    colore = 'green'; larghezza = '100%'; messaggio = 'Password fortissima 💪'; 
    conferma.disabled = false;
  }

  barra.style.width = larghezza;
  barra.style.backgroundColor = colore;
  testo.textContent = messaggio;

  document.getElementById('requisito-lunghezza').textContent = (password.length >= 8 ? '✅' : '❌') + ' Almeno 8 caratteri';
  document.getElementById('requisito-maiuscola').textContent = (/[A-Z]/.test(password) ? '✅' : '❌') + ' Una lettera maiuscola';
  document.getElementById('requisito-minuscola').textContent = (/[a-z]/.test(password) ? '✅' : '❌') + ' Una lettera minuscola';
  document.getElementById('requisito-numero').textContent = (/\d/.test(password) ? '✅' : '❌') + ' Un numero';
  document.getElementById('requisito-speciale').textContent = (/[\W_]/.test(password) ? '✅' : '❌') + ' Un carattere speciale';
}

// Spostiamo la chiamata fuori da ogni blocco
document.addEventListener("DOMContentLoaded", function () {
  controllaConfermaPassword(); // fa partire subito il controllo alla pagina caricata
  if (document.getElementById('overlay-success')) {
    document.body.style.overflow = 'hidden'; // blocca lo scroll quando il popup è visibile
  }
});


function controllaConfermaPassword() {
  const password = document.getElementById('nuova_password').value;
  const conferma = document.getElementById('conferma_password').value;
  const messaggio = document.getElementById('messaggioConferma');

  if (conferma === '') {
    messaggio.style.display = 'none';
  } else if (password === conferma) {
    messaggio.textContent = '✅ Le password coincidono';
    messaggio.style.color = 'green';
    messaggio.style.display = 'block';
  } else {
    messaggio.textContent = '❌ Le password non coincidono';
    messaggio.style.color = 'red';
    messaggio.style.display = 'block';
  }
}

function togglePassword(id, btn) {
  const input = document.getElementById(id);
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}
document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById('overlay-success')) {
    document.body.style.overflow = 'hidden'; // blocca lo scroll
  }
});
</script>

