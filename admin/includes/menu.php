<?php
require_once '../includes/check_access.php';
#require_once '../includes/versione.php';
require_once '../includes/query.php';
// NOTIFICHE DINAMICHE

function getUltimaRevisioneOnline() {
/*    $url = 'https://trac.eleonline.it/ele3/log/trunk';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept-Language: it-IT,it;q=0.9,en;q=0.8'
    ]);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    // ATTENZIONE: curl_setopt(CURLOPT_HTTPHEADER) è chiamato due volte nel tuo codice originario,
    // l'ho unificato in una sola chiamata per evitare conflitti
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html === false) {
        return ['rev' => 0, 'tempo' => ''];
    }

    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    $xpath = new DOMXPath($dom);

    // Trova il primo <td class="rev">
    $rev_td = $xpath->query('//td[contains(@class, "rev")]')->item(0);
    if (!$rev_td) {
        return ['rev' => 0, 'tempo' => ''];
    }

    // Cerca il primo <a> dentro <td class="rev"> che contiene rev= in href
    $a_rev = $xpath->query('.//a[contains(@href, "rev=")]', $rev_td)->item(0);
    if (!$a_rev) {
        return ['rev' => 0, 'tempo' => ''];
    }

    $href = $a_rev->getAttribute('href');
    $rev = 0;
    if (preg_match('/rev=(\d+)/', $href, $m)) {
        $rev = (int)$m[1];
    }

    // Prendi il <tr> padre e cerca <td class="age"> in quella riga
    $tr = $rev_td->parentNode;
    $age_td = null;
    foreach ($tr->childNodes as $child) {
        if ($child->nodeType === XML_ELEMENT_NODE && strpos($child->getAttribute('class'), 'age') !== false) {
            $age_td = $child;
            break;
        }
    }

    $tempo = '';
    if (isset($age_td)) {
        $a_age = $xpath->query('.//a', $age_td)->item(0);
        if ($a_age) {
            $tempo = trim($a_age->textContent);
        }
    }
*/
	$newrev=0;
	$headers = @get_headers('http://mail.eleonline.it/version4/risposta.php');
	if($headers)
		if ($stream = fopen('http://mail.eleonline.it/version4/risposta.php', 'r')) { 
			$newrev= stream_get_contents($stream, 4);
			fclose($stream);
		}
	$rev=(int) filter_var($newrev, FILTER_SANITIZE_NUMBER_INT);
	$tempo='';

    return ['rev' => $rev, 'tempo' => $tempo ?: 'Adesso'];
}
global $patch,$id_cons_gen,$id_cons,$id_comune;
if(!isset($_SESSION['tipo_cons'])) $_SESSION['tipo_cons']='';
// Estrai build corrente
#preg_match('/rev\s*(\d+)/i', $versione, $match);
$build_corrente = $patch;
#isset($match[1]) ? (int)$match[1] : 0;

// Ottieni revisione online e tempo
$build_info = getUltimaRevisioneOnline();
$build_nuovo = $build_info['rev'];
$tempo_aggiornamento = $build_info['tempo'];

// Notifiche
$notifiche = [];

if ($build_nuovo > $build_corrente) {
    $notifiche[] = [
        'icona' => 'fas fa-info-circle',
        'testo' => "Aggiornamento (rev $build_nuovo)",
        'tempo' => $tempo_aggiornamento,
        'link' => 'modules.php?op=7'
    ];
}

// --- Esempio di altre notifiche statiche ---
$notifiche[] = ['icona' => 'fas fa-envelope', 'testo' => '1 nuovo messaggio', 'tempo' => '3 min'];
$notifiche[] = ['icona' => 'fas fa-users', 'testo' => '2 nuove richieste', 'tempo' => '12 ore', 'link' => 'modules.php?op=7'];

?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="modules.php" class="nav-link">Home</a>
    </li>
  </ul>

<?php
$role=$_SESSION['ruolo'];
// Recupera i valori GET oppure imposta default
if($_SESSION['ruolo']=='superuser' and isset($_POST['id_comune'])) {
	$id_comune=$_POST['id_comune'];
	$_SESSION['id_comune']=$id_comune;
}else
	$id_comune = $_SESSION['id_comune']; 
if(isset($_POST['id_cons_gen'])and (count(verifica_cons($_POST['id_cons_gen'])) or $_SESSION['ruolo']=='superuser')) 
	$id_cons_gen=$_POST['id_cons_gen'];
if(!isset($id_cons_gen) and isset($_SESSION['id_cons_gen'])) $id_cons_gen=$_SESSION['id_cons_gen'];
if(!isset($id_cons_gen) or !$id_cons_gen) $id_cons_gen=default_cons();
if(!$id_cons_gen and $role!='superuser') header("Location: ../logout.php");
$_SESSION['id_cons_gen']=$id_cons_gen;
// Array esempio consultazioni, id => nome
$row=elenco_cons();
$consultazioni=array();
foreach($row as $key=>$val){
	$consultazioni[$val['id_cons_gen']] = $val['descrizione'];
	if($val['id_cons_gen']==$id_cons_gen) $_SESSION['tipo_cons']=$val['tipo_cons'];
}
if(!isset($_SESSION['tipo_cons'])) $tipo_cons=0; 
else
	$tipo_cons=$_SESSION['tipo_cons'];
if (in_array($tipo_cons, [1, 12, 13])) {
    $tipo_consultazione = 'provinciali';
} elseif (in_array($tipo_cons, [2])) {
    $tipo_consultazione = 'referendum';
} elseif (in_array($tipo_cons, [3, 5])) {
$tipo_consultazione = 'comunali';
} elseif (in_array($tipo_cons, [4])) {
    $tipo_consultazione = 'circoscrizionali';
} elseif (in_array($tipo_cons, [6, 11, 15, 18])) {
    $tipo_consultazione = 'camera';
} elseif (in_array($tipo_cons, [7, 10, 16, 19])) {
    $tipo_consultazione = 'senato';
} elseif (in_array($tipo_cons, [8, 14])) {
    $tipo_consultazione = 'europee';
} elseif (in_array($tipo_cons, [9, 17])) {
    $tipo_consultazione = 'regionali';
} else {
    $tipo_consultazione = 'sconosciuto';
}
//$tipo_consultazione = tipo_consultazione($_SESSION['tipo_cons']);
$row=verifica_cons($id_cons_gen);
if(!count($row))$row[0]['id_cons']=0;
$_SESSION['id_cons']=$row[0]['id_cons'];
$id_cons=$_SESSION['id_cons'];
// Array esempio comuni, id => nome
$row=elenco_comuni();
foreach($row as $key=>$val)
	$comuni[$val['id_comune']] = $val['descrizione'];
?>
<!-- Select consultazione desktop -->
<ul class="navbar-nav d-none d-sm-flex">
  <li class="nav-item">
    <form class="form-inline" method="POST" action="modules.php" id="form-consultazione-desktop">
      <label for="consultazione-desktop" class="mr-2 mb-0">Consultazione:</label>
      <select class="form-control form-control-sm" name="id_cons_gen" id="consultazione-desktop">
        <?php include('../modules/elenco_cons_menu.php'); ?>
      </select>
    </form>
  </li>
</ul>

<!-- Select consultazione mobile compatto -->
<ul class="navbar-nav d-block d-sm-none">
  <li class="nav-item w-100">
    <form class="form-inline w-100" method="POST" action="modules.php" id="form-consultazione-mobile">
      <select class="form-control form-control-sm w-100" name="id_cons_gen" id="consultazione-mobile">
        <?php include('../modules/elenco_cons_menu.php'); ?>
      </select>
    </form>
  </li>
</ul>

<style>
  /* Solo smartphone: adatta select stretta e larga senza label */
  @media (max-width: 575.98px) {
    #consultazione-mobile {
      max-width: 90vw;
    }
  }
</style>


<script>
  // Submit automatico desktop
  document.getElementById('consultazione-desktop').addEventListener('change', () => {
    document.getElementById('form-consultazione-desktop').submit();
  });

  // Submit automatico mobile
  document.getElementById('consultazione-mobile').addEventListener('change', () => {
    document.getElementById('form-consultazione-mobile').submit();
  });
</script>
<script>

function aggiornaSelect() {
	const selectCons = new FormData();
    selectCons.append('funzione', 'menuConsultazione');
//    formData.append('id_cons_gen', id_cons_gen);
    fetch('../principale.php', {
        method: 'POST',
        body: selectCons 
    })
    .then(response => response.text()) 
    .then(data => {
		var elementExists = document.getElementById("consultazione-mobile");
		if(elementExists!== null)
			document.getElementById ("consultazione-mobile").innerHTML = data;
    })

}
	
  // Submit automatico al cambio select desktop e mobile
  ['consultazione'].forEach(id => {
    document.getElementById(id + '-mobile').addEventListener('change', () => {
      document.getElementById('form-consultazione-mobile').submit();
    });
  });
</script>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">

    <!-- Fullscreen -->
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Fullscreen">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>

    <!-- Notifiche -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#" title="Notifiche">
        <i class="far fa-bell"></i>
        <?php if (count($notifiche) > 0): ?>
          <span class="badge badge-warning navbar-badge"><?= count($notifiche) ?></span>
        <?php endif; ?>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-header"><?= count($notifiche) ?> Notifiche</span>
        <div class="dropdown-divider"></div>
        <?php foreach ($notifiche as $notifica): ?>
          <a href="<?= isset($notifica['link']) ? $notifica['link'] : '#' ?>" class="dropdown-item">
            <i class="<?= $notifica['icona'] ?> mr-2"></i> <?= $notifica['testo'] ?>
            <span class="float-right text-muted text-sm"><?= $notifica['tempo'] ?></span>
          </a>
          <div class="dropdown-divider"></div>
        <?php endforeach; ?>
        <a href="modules.php?op=7" class="dropdown-item dropdown-footer">Vedi tutte le notifiche</a>
      </div>
    </li>
<!-- Gestione profilo (dropdown utente) -->
<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#" title="Gestione profilo">
    <i class="fas fa-user-circle"></i>
  </a>
  <div class="dropdown-menu dropdown-menu-right">
    <span class="dropdown-header">Profilo utente</span>
    <div class="dropdown-divider"></div>
    <a href="modules.php?op=101" class="dropdown-item">
      <i class="fas fa-key mr-2"></i> Cambia password
    </a>
    <div class="dropdown-divider"></div>
    <a href="../logout.php" class="dropdown-item">
      <i class="fas fa-sign-out-alt mr-2"></i> Esci
    </a>
  </div>
</li>

  </ul>
</nav>

<?php include '../includes/sidebar.php'; ?>

<!-- Content Wrapper -->
<div class="content-wrapper">
