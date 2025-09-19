<!doctype html>
<html lang="it">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Eleonline - gestione risultati elettorali ">
	<meta name="author" content="">
	<meta name="generator" content="">
	<meta name="robots" content="noindex">
	<link rel="icon" type="image/x-icon" href="favicon.ico">
	<title><?php echo $titolo_della_pagina;?></title>
	<!-- App styles -->
	<link href="<?php echo $curdir?>/css/bootstrap-italia.min.css" rel="stylesheet">
	<link href="<?php echo $curdir?>/css/altricss.css" rel="stylesheet">
	<link href="<?php echo $curdir?>/css/proiezione.css" rel="stylesheet">
<?php
$tema_colore = $tema_colore; #'default'; // fallback
?>
<script>
  const themeBasePath = "<?php echo $curdir; ?>/themes/";
  const fallbackTheme = "<?php echo htmlspecialchars($tema_colore); ?>";
</script>
<script src="<?php echo $curdir ?>/js/theme-switcher.js"></script>

</head>
<body>
<header class="it-header-wrapper it-header-sticky" data-bs-toggle="sticky" data-bs-position-type="fixed" data-bs-target="#header-nav-wrapper" data-bs-sticky-class-name="is-sticky" style="">
   <div class="it-nav-wrapper">
    <div class="it-header-center-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="it-header-center-content-wrapper">
              <div class="it-brand-wrapper">
                <a href="<?php echo $link_paginaconsultazione;?>">
				  <img class="icon rounded-4" src="modules.php?name=Elezioni&amp;file=foto&amp;id_comune=<?php echo $id_comune;?>" alt="Stemma del Comune di <?php echo $sitename;?>" >
                  <div class="it-brand-text">
					<div class="it-brand-tagline">Comune di <b><?php echo $sitename;?></b></div>
					<div class="it-brand-title titolo-consultazione">
  <?php echo $Consultazione; ?>
</div>

                    <div class="it-brand-tagline d-none d-md-block"><?php echo $desc_consultazione;?></div>
                  </div>
                </a>
              </div>
              <div class="it-right-zone">
                <a class="d-none d-lg-block navbar-brand" href="<?php echo $link_autore;?>" target="_blank"><?php echo $autore;?></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
	<?php include('top_nav.php');?>
  </div>
</header>