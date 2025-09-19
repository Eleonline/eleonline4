
<?php 
$id_gruppo=0;
if($genere==2 or $genere==4) $tab='lista'; else $tab='gruppo';
if(isset($_GET['data'])) $data=$_GET['data'];
if(isset($_GET['orario'])) $orario=$_GET['orario'];
if(isset($_GET['num_gruppo'])) $num_gruppo=$_GET['num_gruppo'];
if(!isset($data)) {
	if($genere==0){
		$quesiti=elenco_gruppi($tab);
		if(isset($_GET['num_gruppo'])) $num_gruppo=intval($_GET['num_gruppo']); else $num_gruppo=1;
		foreach($quesiti as $key=>$val) if ($num_gruppo==$val[1]) {$id_gruppo=$val['id_gruppo'];}
		$row=ultime_affluenze_referendum($id_gruppo);
	}else
		$row=ultime_affluenze(0); #ultime_affluenze(0);
}else{
	if($genere==0){
		$quesiti=elenco_gruppi($tab);
		if(!isset($_GET['num_gruppo'])) $num_gruppo=1;
		foreach($quesiti as $key=>$val) if ($num_gruppo==$val[1]) {$id_gruppo=$val['id_gruppo'];}
		$row=ultime_affluenze_referendum($id_gruppo);
	}else
		$row=ultime_affluenze(0); #ultime_affluenze(0);
}
if(count($row)) {
	$udata=$row[0]['data'];
	$uorario=$row[0]['orario'];
}
if(!isset($data)) {
	if(!count($row)) 
		$row=elenco_orari();
	if(count($row)) {
		$data=$row[0]['data'];
		$orario=$row[0]['orario'];
		if(!isset($udata)){
			$udata=$data;
			$uorario=$orario;
		}
	}else{
		$data='00/00/0000'; 
		$orario='00:00:00';
	}
}
#if($genere>0)
	$row=elenco_sezioni(0);
	$iscrittif=array();
	$iscrittim=array();
	$totiscrittim=0;
	$totiscrittif=0;
	foreach($row as $key=>$val){
		$iscrittif[$val['num_sez']]=$val['femmine'];
		$iscrittim[$val['num_sez']]=$val['maschi'];
		$totiscrittif+=$val['femmine'];
		$totiscrittim+=$val['maschi'];
	}
	$row=elenco_orari();
	$aff2=affluenze_sezione(0,$data,$orario,$id_gruppo);
	$scrutinate=count($aff2);
	$sezionitotali=sezioni_totali();
	$getref='';
	if ($genere==0) {
		$numquesiti=count($quesiti);
		$getref="&num_ref=$num_gruppo&num_refs=$numquesiti";	
		if($numquesiti>1){
		/*	else{
				foreach($row as $campo=>$val) {}
				$data=$val['data'];	
				$orario=$val['orario'];
			} */
		?>
		<!-- Blocco select quesito referndum-->	
		<div class="container pb-2">
			<label for="defaultSelect">Seleziona Quesito</label>
			<select id="defaultSelect" onchange="location = this.value;">
				<!-- option selected>Selezione Quesito</option -->
				<?php
					$desc='';
					foreach($quesiti as $key=>$val) { if ($num_gruppo==$val[1]) {$id_gruppo=$val['id_gruppo']; $sel='selected';} else {$sel='';} ?>
						<option <?php echo $sel; ?> value=" <?php echo "modules.php?op=11&id_comune=$id_comune$cirpar&file=index&id_cons_gen=$id_cons_gen&data=$data&orario=$orario&num_gruppo=".$val[1];?>">Quesito <?php echo $val['num_gruppo'];?></option>
				<?php }?>
			</select>
		</div>
		<!-- fine Blocco select quesito referndum-->
		<?php 
		echo $desc;
		} else {
			$id_gruppo=$quesiti[0]['id_gruppo'];
			$num_gruppo= $quesiti[0]['num_gruppo'];
		}
	#if(!isset($scrutinate)) $id_gruppo=$quesiti[0]['id_gruppo'];
	$i=1;
	if($id_gruppo and !isset($scrutinate)) foreach($scrutinatetemp as $val2) { if($id_gruppo==$val2['id_gruppo']) $scrutinate=$i++; } 
	}?>
	<?php/* 
	$linkopendata="modules.php?op=come&info=affluenze_sez&csv=1&id_comune=$id_comune$cirpar&id_cons_gen=$id_cons_gen$getref";
	$nosez=1;
	include 'opendata.php'; */?>			
	<div class="container">
		 <div class="row text-center">
			<h4 class="fw-semibold text-primary mobile-expanded mt-2">Affluenza</h4>
		 </div>
	</div>
	<div class="w-100 lightgrey-bg-b2 text-white mt-2 ">
        <div class="container">
			<div class="row py-4 divided">
			<?php 
			$numril=count($row);
			$curril=0;
			foreach($row as $campo=>$val) {  
				//controllo data e ora che si sta visualizzando
				$curril++; 
				if(isset($udata) and (($val['data']>$udata) or ($val['data']==$udata and $val['orario']>$uorario))) break;
				if(!isset($_GET['data'])){ if(!isset($data)){$data=$val['data'];$orario=$val['orario'];}} else {$data=$_GET['data'];$orario=$_GET['orario'];}  
				if(date_format(date_create($val['data']) ,'Y/m/d')==date_format(date_create($data) ,'Y/m/d') &&  $val['orario']==$orario){?>
					<div class="col-12 col-md-4 text-primary fw-semibold primary-bg-a2">
				<?php 
				} else {
				?>   
					<div class="col-12 col-md-4 text-primary fw-semibold">
				<?php 
				}
				$imgora=substr($val['orario'],0,2);
				?> 
				<a href="modules.php?op=11&data=<?php echo htmlspecialchars(date_format(date_create($val['data']) ,'Y/m/d'))."&orario=".$val['orario']; ?>&id_comune=<?php echo $id_comune.$cirpar;?>&file=index&id_cons_gen=<?php echo $id_cons_gen;?><?php if(isset($num_gruppo) )echo "&num_gruppo=$num_gruppo";?>" class="pt-2 pt-md-0 d-flex align-items-center justify-content-center text-decoration-none" data-focus-mouse="false">
					<div class="icon icon-lg me-3">
						<img src="temi/bootstrap/pagine/img/ore<?php echo $imgora; ?>.svg" class="w-100" alt="Immagine Orologio ore <?php echo $imgora; ?>">
					</div>
					<h6 class="align-self-center"><?php echo date_format(date_create($val['data']) ,'d/m/Y')."<br>ORE ".$val['orario']; ?></h6>
				</a>
					</div>
				<?php 
					if($data=='00/00/0000') break;
				} ?>
			</div>
		</div>
	</div>  
<?php
$linkopendata="modules.php?op=come&info=affluenze_sez&csv=1&id_comune=$id_comune$cirpar&id_cons_gen=$id_cons_gen$getref";
$nosez=1; 
include 'temi/bootstrap/pagine/tab_link_opendata.php'; ?>		
<div class="table-responsive overflow-x">
	<table class="table  mb-0">
		<thead class="title-content">
			<tr>
				<th>Affluenza per sezioni</th> 
				<?php if ($scrutinate == $sezionitotali) {?> 
					<th class="text-end">Dati finali</th>
				<?php } else {?>
					<th class="text-end"><span>Sezioni scrutinate: <?php echo $scrutinate; ?> su <?php echo $sezionitotali; ?></span></th>
				<?php }?>
			</tr>
		</thead>
	</table>
</div>
<div class="table-responsive overflow-x">
	<table class="table table-striped table-bordered table-sm align-middle">
		<thead>
			<tr>
				<th class="primary-bg-c1" scope="col">Sezione</th>
				<th class="primary-bg-c6" scope="col">Iscritti nella sezione</th>
				<th class="primary-bg-c6" scope="col">Uomini</th>
				<th class="primary-bg-c6" scope="col">Donne</th>
				<th class="primary-bg-c11 complessivi" scope="col">Complessivi</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$totelettori=0;
			$totuomini=0;
			$totdonne=0;
			$totcomplessivi=0;				
			$row=elenco_iscritti();
			if(isset($data))
			foreach($row as $campo=>$val) {
			foreach($aff2 as $affkey=>$affval) { if($affval['id_sez']==$val['id_sez'] and ($affval['id_gruppo']==$id_gruppo or $id_gruppo==0)) {$aff[0]['voti_uomini']=$affval['voti_uomini'];$aff[0]['voti_donne']=$affval['voti_donne'];$aff[0]['voti_complessivi']=$affval['voti_complessivi']; break;}}
				if(!isset($aff[0]['voti_uomini'])) {$aff[0]['voti_uomini']='-';$aff[0]['voti_donne']='-';$aff[0]['voti_complessivi']='-';}
	#			$aff=affluenze_sezione($val['id_sez'],date_format(date_create($data) ,'Y-m-d'),$orario,$id_gruppo);				
	#			if(!count($aff)) {$aff[0]['voti_uomini']='-';$aff[0]['voti_donne']='-';$aff[0]['voti_complessivi']='-';}
				
				$totelettori+=$val['elettori'];
				$totuomini+=intval($aff[0]['voti_uomini']);
				$totdonne+=intval($aff[0]['voti_donne']);
				$totcomplessivi+=intval($aff[0]['voti_complessivi']);
				$elettori = $val['elettori']; // totale elettori
                $uomini = intval($aff[0]['voti_uomini']);
				$donne = intval($aff[0]['voti_donne']);
				$voti = intval($aff[0]['voti_complessivi']); // voti espressi
				if($iscrittim[$val['num_sez']]>0) $percentualeuomini = ($uomini / $iscrittim[$val['num_sez']]) * 100;
				if($iscrittif[$val['num_sez']]>0)$percentualedonne = ($donne / $iscrittif[$val['num_sez']]) * 100;	
				if(($iscrittim[$val['num_sez']]+$iscrittif[$val['num_sez']])>0)$percentualesez = ($voti / ($iscrittif[$val['num_sez']]+$iscrittim[$val['num_sez']])) * 100;
				if($totiscrittim>0) $percentualetotuomini = ($totuomini / $totiscrittim) * 100;
				if($totiscrittif>0) $percentualetotdonne = ($totdonne / $totiscrittif) * 100;
				if(($totiscrittim+$totiscrittif)>0)$percentualetot = ($totcomplessivi / ($totiscrittim+$totiscrittif)) * 100;
				
		?>
		  <tr class="text-end">
			<th scope="row"><?php echo $val['num_sez'];?></th>
			<td><?php echo $val['elettori'];?></td>
			<td><?php echo $aff[0]['voti_uomini'];?><br>
						<span class="percentuale"><?php echo number_format($percentualeuomini, 2) . " %";?></span>
			</td>
			<td><?php echo $aff[0]['voti_donne'];?><br>
						<span class="percentuale"><?php echo number_format($percentualedonne, 2) . " %";?></span>
			</td>
			<td><?php echo $aff[0]['voti_complessivi'];?><br>
						<span class="percentuale"><?php echo number_format($percentualesez, 2) . " %";?></span>
			</td>
		  </tr>
		<?php
		  unset($aff);}
		?>
		</tbody>
    <tfoot>
      <tr class="primary-bg-c4 white-color align-middle text-end">
        <th scope="row">Totale</th>
		<th><?php echo $totelettori;?></th>
        <th><?php echo $totuomini;?><br>
						<span class="percentuale"><?php echo number_format($percentualetotuomini, 2) . " %";?></span>
		</th>
        <th><?php echo $totdonne;?><br>
						<span class="percentuale"><?php echo number_format($percentualetotdonne, 2) . " %";?></span>
		</th>
        <th><?php echo $totcomplessivi;?><br>
						<span class="percentuale"><?php echo number_format($percentualetot, 2) . " %";?></span>
		</th>
      </tr>
    </tfoot>
  </table>
</div>