<?php
require_once '../includes/check_access.php';

$op = isset($_GET['op']) ? $_GET['op'] : null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : null;
?>
<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="modules.php" class="brand-link">
    <img src="../logo/logo eleonline rettangolo.png" alt="Logo" class="brand-image elevation-3">
    <span class="brand-text font-weight-light">Eleonline</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><a href="modules.php" class="nav-link"><i class="nav-icon fas fa-home text-primary"></i><p>Home</p></a></li>
        <li><hr style="border-color: white; margin: 5px 0;"></li>
		
<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin'])): ?>
        <!-- DASHBOARD -->
       <li class="nav-item has-treeview <?php echo in_array($op, [1, 2, 5]) ? 'menu-open' : ''; ?>">
  <a href="#" class="nav-link <?php echo in_array($op, [1, 2]) ? 'active' : ''; ?>">
    <i class="nav-icon fas fa-desktop text-primary"></i>
    <p>
      Sistema
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="modules.php?op=1" class="nav-link <?php echo ($op == 1) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-server text-success"></i>
        <p>Stato Sistema</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="modules.php?op=2" class="nav-link <?php echo ($op == 2) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-clipboard-list text-info"></i>
        <p>Log Attività</p>
      </a>
    </li>
	<li class="nav-item">
  <a href="modules.php?op=5" class="nav-link <?php echo ($op == 5) ? 'active' : ''; ?>">
    <i class="nav-icon fas fa-user-lock text-danger"></i>
    <p>Sblocco IP</p>
  </a>
</li>
  </ul>
</li>
<?php endif; ?>

<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin'])): ?>
        <!-- CONFIGURAZIONE SISTEMA -->
        <li class="nav-item has-treeview <?php echo in_array($op, [3, 4, 5, 6, 7]) ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo in_array($op, [3, 4, 5, 6, 7]) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-tools text-danger"></i>
            <p>
              Impostazione e dati generali
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="modules.php?op=3" class="nav-link <?php echo ($op == 3) ? 'active' : ''; ?>"><i class="nav-icon fas fa-sliders-h text-danger"></i><p>Configurazione Sito</p></a></li>
            <li class="nav-item"><a href="modules.php?op=4" class="nav-link <?php echo ($op == 4) ? 'active' : ''; ?>"><i class="nav-icon fas fa-palette text-warning"></i><p>Tema colore</p></a></li>
            <!-- <li class="nav-item"><a href="modules.php?op=200" class="nav-link <?php echo ($op == 5) ? 'active' : ''; ?>"><i class="nav-icon fas fa-chart-pie text-primary"></i><p>Config. D'Hondt</p></a></li>-->
            <li class="nav-item"><a href="modules.php?op=6" class="nav-link <?php echo ($op == 6) ? 'active' : ''; ?>"><i class="nav-icon fas fa-city text-secondary"></i><p>Anagrafica Enti/Comuni</p></a></li>
			<li class="nav-item"><a href="modules.php?op=7" class="nav-link <?php echo ($op == 7) ? 'active' : ''; ?>"><i class="nav-icon fas fa-sync-alt text-warning"></i><p>Aggiornamento Rev</p></a></li>         
		 </ul>
        </li>
<?php endif; ?>

<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin', 'operatore'])): ?>
        <!-- UTENTI E PERMESSI -->
        <li class="nav-item has-treeview <?php echo in_array($op, [8, 36]) ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo in_array($op, [8, 36]) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users-cog text-secondary"></i>
            <p>
              Utenti e Permessi
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="modules.php?op=8" class="nav-link <?php echo ($op == 8) ? 'active' : ''; ?>"><i class="nav-icon fas fa-user-shield text-secondary"></i><p>Gestione Utenti</p></a></li>
            <li class="nav-item"><a href="modules.php?op=36" class="nav-link <?php echo ($op == 36) ? 'active' : ''; ?>"><i class="nav-icon fas fa-key text-danger"></i><p>Permessi</p></a></li>
          </ul>
        </li>
<?php endif; ?>

<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin', 'operatore'])): ?>
<!-- GESTIONE DATI -->
<li class="nav-item has-treeview <?php echo in_array($op, [9, 22, 10, 11, 12, 13]) ? 'menu-open' : ''; ?>">
  <a href="#" class="nav-link <?php echo in_array($op, [9, 22, 10, 11, 12, 13]) ? 'active' : ''; ?>">
    <i class="nav-icon fas fa-database text-secondary"></i>
    <p>
      Setup Consultazione
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="modules.php?op=9" class="nav-link  <?php echo ($op == 9) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-tv text-info"></i>
        <p>Configura Consultazione</p>
      </a>
    </li>
	 <!--li class="nav-item">
	  <a href="modules.php?op=22" class="nav-link  <?php echo ($op == 22) ? 'active' : ''; ?>">
		<i class="nav-icon fas fa-check-circle text-success"></i>
		<p>Autorizza Comune</p>
	  </a>
	</li-->
    <li class="nav-item">
      <a href="modules.php?op=10" class="nav-link  <?php echo ($op == 10) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-users text-warning"></i>
        <p>Configura Affluenza</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="modules.php?op=11" class="nav-link <?php echo ($op == 11) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-map text-success"></i>
        <p>Configura Circoscrizioni</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="modules.php?op=12" class="nav-link <?php echo ($op == 12) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-building text-primary"></i>
        <p>Configura Sede Elettorale</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="modules.php?op=13" class="nav-link <?php echo ($op == 13) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-door-closed text-danger"></i>
        <p>Configura Sezione</p>
      </a>
    </li>
  </ul>
</li>
<?php endif; ?>

<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin', 'operatore'])): ?>
<li class="nav-item has-treeview <?php echo in_array($op, [18, 19, 20, 21, 79, 80, 81, 150]) ? 'menu-open' : ''; ?>">
  <a href="#" class="nav-link <?php echo in_array($op, [18, 19, 20, 21, 79, 80, 81, 150]) ? 'active' : ''; ?>">
    <i class="nav-icon fas fa-file-import text-primary"></i>
    <p>
      Importa Dati
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="modules.php?op=150&funzione=recuperaEventiElettorali" class="nav-link <?php echo ($op == 150) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-cloud text-info"></i>
        <p>Webservices</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="modules.php?op=19" class="nav-link <?php echo ($op == 19 or $op == 79 or $op == 80 or $op == 81) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-download text-success"></i>
        <p>Importa da DAIT</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="modules.php?op=20" class="nav-link <?php echo ($op == 20) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-file-download text-warning"></i>
        <p>Scarica liste</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="modules.php?op=21" class="nav-link <?php echo ($op == 21) ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-file-upload text-danger"></i>
        <p>Ripristina dati</p>
      </a>
    </li>
  </ul>
</li>
<?php endif; ?>

<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin', 'operatore'])): ?>
        <!-- Liste e candidati -->
        <li class="nav-item has-treeview <?php echo in_array($op, [23, 24, 25, 26, 27, 28, 29, 30]) ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo in_array($op, [23, 24, 25, 26, 27, 28, 29, 30]) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-list-alt text-warning"></i>
            <p>
               Gestione Liste e candidati 
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
			<?php if ($tipo_consultazione == 'regionali') { ?>
			<li class="nav-item"><a href="modules.php?op=23" class="nav-link <?php echo ($op == 23) ? 'active' : ''; ?>"><i class="nav-icon fas fa-user-tie text-warning"></i><p>Candidato Presidenti</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'comunali') { ?>
			<li class="nav-item"><a href="modules.php?op=24" class="nav-link <?php echo ($op == 24) ? 'active' : ''; ?>"><i class="nav-icon fas fa-user-tie text-warning"></i><p>Candidato Sindaco</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'camera' or $tipo_consultazione == 'senato' ) { ?>
			<li class="nav-item"><a href="modules.php?op=27" class="nav-link <?php echo ($op == 27) ? 'active' : ''; ?>"><i class="nav-icon fas fa-user-tag text-success"></i><p>Candidato Uninominale</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'europee' or $tipo_consultazione == 'comunali' or $tipo_consultazione == 'regionali') { ?>
			<li class="nav-item"><a href="modules.php?op=25" class="nav-link <?php echo ($op == 25) ? 'active' : ''; ?>"><i class="nav-icon fas fa-list-alt text-info"></i><p>Lista</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'camera' or $tipo_consultazione == 'senato' or $tipo_consultazione == 'senato' ) { ?>
			<li class="nav-item"><a href="modules.php?op=28" class="nav-link <?php echo ($op == 28) ? 'active' : ''; ?>"><i class="nav-icon fas fa-link text-primary"></i><p>Lista collegata</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'camera' or $tipo_consultazione == 'senato' ) { ?>
			<li class="nav-item"><a href="modules.php?op=29" class="nav-link <?php echo ($op == 29) ? 'active' : ''; ?>"><i class="nav-icon fas fa-lock text-danger"></i><p>Listino bloccato</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'referendum') { ?>
			<li class="nav-item"><a href="modules.php?op=30" class="nav-link <?php echo ($op == 30) ? 'active' : ''; ?>"><i class="nav-icon fas fa-question-circle text-warning"></i><p>Quesito Referendario</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione != 'referendum' && $tipo_consultazione != 'camera' && $tipo_consultazione != 'senato') { ?>
			<li class="nav-item"><a href="modules.php?op=26" class="nav-link <?php echo ($op == 26) ? 'active' : ''; ?>"><i class="nav-icon fas fa-user-check text-success"></i><p>Candidati</p></a></li>
			<?php } ?>
		 </ul>
        </li>
<?php endif; ?>

<li><hr style="border-color: white; margin: 5px 0;"></li>

<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin', 'operatore'])): ?>
  <!-- INFORMAZIONI UTILI -->
  <li class="nav-item has-treeview <?php echo in_array($op, [14,15,16,17]) ? 'menu-open' : ''; ?>">
    <a href="#" class="nav-link <?php echo in_array($op, [14,15,16,17]) ? 'active' : ''; ?>">
      <i class="nav-icon fas fa-info-circle text-info"></i>
      <p>
        Carica Informazioni
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="modules.php?op=14" class="nav-link <?php echo ($op == 14) ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-vote-yea me-2 text-info"></i><p>Come si vota</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="modules.php?op=15" class="nav-link <?php echo ($op == 15) ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-phone-alt me-2 text-success"></i><p>Numeri utili</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="modules.php?op=16" class="nav-link <?php echo ($op == 16) ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-concierge-bell me-2 text-primary"></i><p>Servizi</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="modules.php?op=17" class="nav-link <?php echo ($op == 17) ? 'active' : ''; ?>">
          <i class="nav-icon fas fa-link me-2 text-info"></i><p>Link utili</p>
        </a>
      </li>
    </ul>
  </li>
<?php endif; ?>

<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin', 'operatore'])): ?>
        <!-- RILEVAZIONI DI VOTO -->
        <li class="nav-item has-treeview <?php echo in_array($op, [31, 32, 33, 34]) ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo in_array($op, [31, 32, 33, 34]) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-poll text-warning"></i>
            <p>
              Rilevazioni di voto
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="modules.php?op=31" class="nav-link <?php echo ($op == 31) ? 'active' : ''; ?>"><i class="nav-icon fas fa-users text-info"></i><p>Affluenza</p></a></li>
			<?php if ($tipo_consultazione == 'referendum') { ?>
			<li class="nav-item"><a href="modules.php?op=40" class="nav-link <?php echo ($op == 40) ? 'active' : ''; ?>"><i class="nav-icon fas fa-balance-scale text-danger"></i><p>Referendum</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'comunali') { ?>
			<li class="nav-item"><a href="modules.php?op=39" class="nav-link <?php echo ($op == 39) ? 'active' : ''; ?>"><i class="nav-icon fas fa-user-tie text-primary"></i><p>Candidato Sindaco</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'camera' or $tipo_consultazione == 'senato' ) { ?>
			<li class="nav-item"><a href="modules.php?op=39" class="nav-link <?php echo ($op == 39) ? 'active' : ''; ?>"><i class="nav-icon fas fa-user-tag text-success"></i><p>Candidato Uninominale</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'regionali') { ?>
			<li class="nav-item"><a href="modules.php?op=39" class="nav-link <?php echo ($op == 39) ? 'active' : ''; ?>"><i class="nav-icon fas fa-user-tie text-primary"></i><p>Candidato Presidenti</p></a></li>
			<?php } ?>
			<?php if ($tipo_consultazione == 'europee' or $tipo_consultazione == 'comunali' or $tipo_consultazione == 'regionali') { ?>
			<li class="nav-item"><a href="modules.php?op=32" class="nav-link <?php echo ($op == 32) ? 'active' : ''; ?>"><i class="nav-icon fas fa-list-ul text-warning"></i><p>Lista</p></a></li>
            <?php } ?>
			<?php if ($tipo_consultazione == 'camera' or $tipo_consultazione == 'senato' or $tipo_consultazione == 'senato' ) { ?>
			<li class="nav-item"><a href="modules.php?op=32" class="nav-link <?php echo ($op == 32) ? 'active' : ''; ?>"><i class="nav-icon fas fa-link text-primary"></i><p>Lista collegata</p></a></li>
            <?php } ?>
			<?php if ($tipo_consultazione == 'camera' or $tipo_consultazione == 'senato' ) { ?>
			<li class="nav-item"><a href="modules.php?op=32" class="nav-link <?php echo ($op == 32) ? 'active' : ''; ?>"><i class="nav-icon fas fa-lock text-danger"></i><p>Listino bloccato</p></a></li>
            <?php } ?>
			<?php if ($tipo_consultazione != 'referendum' && $tipo_consultazione != 'camera' && $tipo_consultazione != 'senato') { ?>
			<li class="nav-item"><a href="modules.php?op=33" class="nav-link <?php echo ($op == 33) ? 'active' : ''; ?>"><i class="nav-icon fas fa-star text-success"></i><p>Preferenze</p></a></li>
            <?php } ?>
			<li class="nav-item"><a href="modules.php?op=34" class="nav-link <?php echo ($op == 34) ? 'active' : ''; ?>"><i class="nav-icon fas fa-chart-pie text-info"></i><p>Visualizza Risultati</p></a></li>
			<?php if ($tipo_consultazione == 'comunali') { ?>
			<li class="nav-item"><a href="modules.php?op=35" class="nav-link <?php echo ($op == 35) ? 'active' : ''; ?>"><i class="nav-icon fas fa-tasks text-secondary"></i><p>Assegna Seggi</p></a></li>
			<?php } ?>
		  </ul>
        </li>
<?php endif; ?>

<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin', 'operatore'])): ?>
        <!-- RILEVAZIONI DI VOTO -->
        <li class="nav-item has-treeview <?php echo in_array($op, [37, 38]) ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo in_array($op, [37, 38]) ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-print text-warning"></i>
    <p>
      Risultati e Stampa
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="modules.php?op=37" class="nav-link <?= ($op == 37) ? 'active' : '' ?>">
        <i class="nav-icon fas fa-users text-warning"></i>
        <p>Affluenza</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="modules.php?op=38" class="nav-link <?= ($op == 38) ? 'active' : '' ?>">
        <i class="nav-icon fas fa-vote-yea text-danger"></i>
        <p>Risultati</p>
      </a>
    </li>
  </ul>
        </li>
<?php endif; ?>

<?php if (in_array($_SESSION['ruolo'], ['superuser', 'admin', 'operatore'])): ?>
        <!-- DOCUMENTAZIONE E SUPPORTO -->
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-book-open text-primary"></i>
            <p>
              Documentazione e Supporto
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="https://www.eleonline.it/site/phpBB3/viewforum.php?f=10&sid=be8e62762997ed9fe122f1d8046d3ea3" target="_blank" class="nav-link"><i class="nav-icon fas fa-book text-primary"></i><p>Manuale Utente</p></a></li>
            <li class="nav-item"><a href="https://www.eleonline.it/site/phpBB3/index.php?sid=1bc2744acad328b5629c371ae6b3e0ef" target="_blank" class="nav-link"><i class="nav-icon fas fa-comments text-info"></i><p>Forum</p></a></li>
            <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-envelope text-success"></i><p>Contatti</p></a></li>
          </ul>
        </li>
<?php endif; ?>
        <li><hr style="border-color: white; margin: 5px 0;"></li>

        <li class="nav-item">
          <a href="../logout.php" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
            <p>Esci</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>
