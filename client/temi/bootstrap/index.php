<?php
// dati per top header 
global $dbi,$prefix,$id_comune,$id_cons_gen,$id_cons,$circo,$idcirc,$op;
include(dirname(__FILE__).'/query.php');
if(isset($_GET["id_cons_gen"])) $idcurcons=$_GET["id_cons_gen"]; else $idcurcons='';
if($idcurcons) {
	$row=conscomune($idcurcons); 
	if(!count($row)) {  #non esiste il comune
		$row=default_cons();
		$idcurcons=$row[0][0];
		$id_cons_gen=$idcurcons;
		$id_cons=$row[0][1];
		$proiezione=$row[0][2];
		unset($op);
	}
}
if(!$id_cons_gen) {
	$row=default_cons();
	$id_cons_gen=$row[0][0];
	$id_cons=$row[0][1];
	$proiezione=$row[0][2];
	unset($op);
}else{
	$row=conscomune(0);
	$id_cons=$row[0][0];
	$proiezione=$row[0][11];
}
if(scrutinate('candidati')<sezioni_totali()) $proiezione=0;
$proiezioneattivo=$proiezione;
$row=tipo_consultazione(0);
$genere=$row[0][3];
$votogruppo=$row[0][4];
$votolista=$row[0][5];
$votocandidato=$row[0][6];
$circo=$row[0][7];
if($circo){
	$elenco=elenco_circoscrizioni();
	if(isset($_GET['op'])) $op=$_GET['op']; else $op='';
	if(isset($_GET['idcirc'])) $idcirc=intval($_GET['idcirc']); else $idcirc=$elenco[0]['id_circ'];
}
if(isset($_GET['id_lista'])) $id_lista=intval($_GET['id_lista']);
$tipocons=$row[0][0];
$row=dati_config();
//$sitename=$row[0][0];
$siteurl="http://";
$multicomune=$row[0][17];
$temaattivo=$row[0][23];
if (stristr($row[0][0],"http")) $siteurl='';
$siteurl.=$row[0][1];
$multicomune=$row[0][17];
$row=dati_consultazione(0);
$Consultazione=$row[0][1];
$datainizio=$row[0][2];
$tipo_cons=$row[0][4];
#$intro seleziona la pagina da caricare: 1) listaecandidati - 4) referendum
$intro=1;
if($tipo_cons==2) $intro=4;
include(dirname(__FILE__).'/../../modules/Elezioni/language/lang-it.php');
//if($multicomune==1){
	$row=dati_comune();
	$sitename=$row[0][1];
//}
include(dirname(__FILE__).'/../../versione.php');

$pathpagine = dirname(__FILE__)."/pagine/";
$titolo_della_pagina="Eleonline Gestione Risultati Elettorali";
$link_paginaprincipale="modules.php";
$link_paginaconsultazione="modules.php?id_comune=$id_comune&id_cons_gen=$id_cons_gen";
$desc_consultazione="Risultati in tempo reale";
$autore="Eleonline | by alessandro candido & roberto gigli";
$link_autore="http://www.eleonline.it/site/modules.php?name=Contatti";
#$curdirass= substr($_SERVER['PHP_SELF'],0, strrpos($_SERVER['PHP_SELF'],'/'))."/temi/bootstrap";
$curdir="temi/bootstrap";
$versione_eleonline=$version;
$ultimoinserimento=ultimo_aggiornamento(); 
include(dirname(__FILE__).'/topheader.php');

//dati sistemati
#$sitename="Comune di Capo d'Orlando";
#$siteurl="https://www.eleonline.it";
//$link_paginaprincipale="../client/index.php";
//$desc_consulazione= $sitename + " - Risultati in tempo reale";
//$pathpagine = "paginetest/";


// dati per footer
// Contatti
//$Nomecontatto="Comune di Capo d'Orlando";
//$viacontatto="Via V. Emanuele - 98071 - Capo d'Orlando (ME)";
//$telefonocontatto="+390941915111 ";
//$cdf_piva="00356650838";
//$peccontatto="protocollo@pec.comune.capodorlando.me.it";

?>
<!-- top header -->
<!-- blocco header-->



<div class="container">
	<table class="table  mb-0 text-center">
		<thead class="title-content">
			<tr>
				<th>Dati Provvisori suscettibili di modifica. I dati hanno puramente titolo informativo</th>
			</tr>		
		</thead>
	</table>
	<?php if(count($ultimoinserimento)) { ?>
	<div class="text-end mt-3 align-middle">
		<span>Dato aggiornato al: <?php echo date_format(date_create($ultimoinserimento[0][0]) ,'d/m/Y')." ore ".substr($ultimoinserimento[0][1],0,5); ?></span>
	</div>
	<?php } ?>
	<?php
	#############
	# Scelta circoscrizione
	if($circo) { 
	?>
		<!-- Blocco select scelta Circoscrizione-->	
		<div class="container pb-2">
			<label for="defaultSelect">Seleziona Circoscrizione</label>
			<select id="defaultSelect" onchange="location = this.value;">
				<!-- option selected>Selezione Quesito</option -->
				<?php
					$desc='';
					foreach($elenco as $key=>$val) { if(!($idcirc)) $idcirc=$val[1]; if ($idcirc==$val[1]) { $sel='selected';} else {$sel='';} ?>
					<option <?php echo $sel; ?> value=" <?php echo "modules.php?op=$op&id_comune=$id_comune&file=index&id_cons_gen=$id_cons_gen&idcirc=".$val[1];?>"> <?php echo $val['descrizione'];?></option>
				<?php }?>
			</select>
		</div>
		<!-- fine Blocco select scelta circoscrizione-->
	<?php } ?>
	<div class="row">
		<div class="col-12 col-md-12 col-xl-12 py-md-3 px-md-3 bd-content">
			<?php include($pathpagine.'contenuto.php');?>
		</div>
	</div>
</div>
<!-- pulsante torna sopra -->
<div class="d-flex align-items-center">
	<a href="#" aria-hidden="true" tabindex="-1" data-bs-toggle="backtotop" class="back-to-top shadow">
		<svg class="icon icon-light"><use href="<?php echo $curdir?>/svg/sprites.svg#it-arrow-up"></use></svg>
	</a>
</div>

<!-- blocco footer-->
<?php include(dirname(__FILE__).'/footer.php');?>
<!-- window.__PUBLIC_PATH__ points to fonts folder location -->
<script>window.__PUBLIC_PATH__ = '<?php echo $curdir?>/fonts'</script>
<script src="<?php echo $curdir?>/js/bootstrap-italia.bundle.min.js"></script>
	<script>
	  bootstrap.loadFonts();
	</script>
	
	
	
</body>
</html>