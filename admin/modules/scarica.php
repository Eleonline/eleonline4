<?php

require_once '../includes/check_access.php';

$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;

if (isset($param['fase'])) $fase=intval($param['fase']); else $fase=0;
if (isset($param['id_cons_gen2'])) $id_cons_gen2=intval($param['id_cons_gen2']); else $id_cons_gen2='0';
if (isset($param['indirizzoweb'])) $indirizzoweb=addslashes($param['indirizzoweb']); else $indirizzoweb='https://www.eleonline.it/client/';
if (isset($param['id_comune2'])) $id_comune2=intval($param['id_comune2']); else $id_comune2='0';

##################################
?>
<section class="content">
  <div class="container-fluid">
  <h2><i class="fas fa-flag"></i> Scarica consultazioni da altre installazioni</h2>
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title" id="titoloScaricaConsultazione">Scarica consultazione</h3>
      </div>

      <div class="card-body table-responsive" style="max-height:600px; overflow-y:auto;">
        <form id="consultazioneForm" class="mb-3" action="modules.php">
          <div class="form-row mt-16">
			<?php if($fase==0) { ?>
			<input type="hidden" name="id_cons_gen" value="$id_cons_gen"><input type="hidden" name="op" value="20"><input type="hidden" name="fase" value="1">
            <div class="col-md-7" id="divUrl">
              <label for="indirizzoWeb">Url del server </label>
              <input type="text" class="form-control" id="indirizzoWeb" value="<?= $indirizzoweb ?>" required>
            </div>
			<?php }else{ ?>
            <div class="col-md-7" id="divUrl">
              <label for="indirizzoWeb">Url del server </label>
				<div id="indirizzoWeb" class=\"col-md-7\"><?= $indirizzoweb ?></div>
			</div>
			  <?php } ?>
		  </div>
          <div class="form-row mt-5">		
			  <?php if($fase==1) {
				$urlrem="$indirizzoweb/file.php?fase=1";
				$rem_cons="<script type=\"text/javascript\" src=\"$urlrem\"></script>";
				?>
			<input type="hidden" name="id_cons_gen" value="$id_cons_gen"><input type="hidden" name="op" value="20"><input type="hidden" name="fase" value="2">
            <div class="col-md-7" id="divConsultazione">
              <label for="id_cons_gen2">Scegli la consultazione</label>
			  <?php echo $rem_cons; ?> 
            </div>
			<?php } ?>
		  </div>
          <div class="form-row mt-5">		
			 <?php if($fase==2) { 
				$rem_cons="<script type=\"text/javascript\" src=\"$indirizzoweb/file.php?fase=2&id_cons_gen2=$id_cons_gen2\"></script>";
			?>			 
            <div class="col-md-7">
              <label for="id_cons_gen2">Consultazione</label>
			<div id="id_cons_gen2" class=\"col-md-7\"><?= $_GET[$_GET['id_cons_gen2']] ?></div>
			</div>
			<input type="hidden" name="id_cons_gen" value="<?= $id_cons_gen ?>"><input type="hidden" name="op" value="20"><input type="hidden" name="fase" value="3"><input type="hidden" name="indirizzoweb" value="<?= $indirizzoweb ?>">			 
			<div class="col-md-7" id="divComune">
              <label for="comuneWeb">Scegli il comune </label>
			  <?php echo $rem_cons; ?> 
            </div>
			<?php } ?>
		  </div>
		<div class="form-row mt-16">		
		<div class="col-md-7 d-flex align-items-end">
		  <button type="submit" class="btn btn-primary w-50 me-2" id="btnAggiungi">Invio</button>
		</div>
		</div>
			
        </form>
      </div>
	</div>
  </div>
<?php  if ($fase=='3'){
		$id_cons_gen2=$_GET['id_cons_gen2'];
		$id_comune2=$_GET['id_comune2'];
Header("Location: $indirizzoweb/modules.php?op=backup&id_cons_gen=$id_cons_gen2&id_comune=$id_comune2");} ?>
</section>
