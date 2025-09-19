<?php
if(isset($linkopendatapdf))
	$linktmp=substr($linkopendatapdf,strrpos($linkopendata,'?')+1);
#	$nosez=0;
else
	$linktmp=substr($linkopendata,strrpos($linkopendata,'?')+1);
#	$nosez=1;
#}
$parametri=explode('&',$linktmp);
foreach($parametri as $key=>$val) {
	$arval[$key]=explode('=',$val);
}
?>
<div class="d-flex justify-content-end">
	<div class="table-responsive">
		<table class="table border text-center align-middle">
			<tbody>
				<tr>
					<th class="primary-bg-c11 text-white" scope="row">Open Data</th>
					<td>
						<a href="<?php echo $linkopendata;?>" target="_blank" data-focus-mouse="true">
							<svg class="icon icon-primary"><use href="<?php echo $curdir;?>/svg/sprites.svg#it-print"></use></svg>
						</a>
					</td>		
					<td>
						<a data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
							<svg class="icon icon-primary"><use href="<?php echo $curdir;?>/svg/sprites.svg#it-file-pdf-ext"></use></svg>
						</a>
					</td>
					<td>
						<a href="<?php echo $linkopendata;?>&xls=1" target="_blank" data-focus-mouse="true">
							<svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-file-csv"></use></svg>
						</a>
					</td>
				</tr>
				<tr class="collapse" id="collapseExample">
					<td colspan=4>
						<form name="formato" action="modules.php">
							<table class="table border text-center align-middle mt-0">
								<tr>
									<?php if(!$nosez) { ?>
									<td class="opendatatd">
										<fieldset>
											<legend>Stampa Sezioni</legend>
											<div class="form-check">
												<label for="sezmin"><span class="opendata"> Da Sezione</span></label>
												<input name="minsez" type="text" style="width: 80px;" id="sezmin" value="1">
											</div>
											<div class="form-check">
												<label for="maxsez"><span class="opendata"> A Sezione</span></label>
												<input name="offsetsez" type="text" style="width: 80px;" id="maxsez" value="18">
											</div>
										</fieldset>
									</td>
									<?php } ?>
									<td class="opendatatd">
										<fieldset>
											<legend>Formato documento</legend>
											<div class="form-check">
												<input name="formato" type="radio" id="radio1" value="A4" checked>
												<label for="radio1"><span class="opendata">A4</span></label>
											</div>
											<div class="form-check">
												<input name="formato" type="radio" id="radio2" value="A3">
												<label for="radio2"><span class="opendata">A3</span></label>
											</div>
										</fieldset>
									</td>
									<td>
										<fieldset>
											<legend>Orientamento</legend>
											<div class="form-check">
												<input name="orienta" type="radio" id="radio3" value="P" checked>
												<label for="radio3"><span class="opendata">Verticale</span></label>
											</div>
											<div class="form-check">
												<input name="orienta" type="radio" id="radio4" value="L">
												<label for="radio4"><span class="opendata">Orizzontale</span></label>
											</div>
										</fieldset>
									</td>
									<td>
										<?php foreach($arval as $val) echo "<input type=\"hidden\" name=\"".$val[0]."\" value=\"".$val[1]."\">"; ?>
										<input type="hidden" name="pdf" value="1">
										<button type="submit" class="btn btn-primary">Crea PDF</button>
									</td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
			</tbody>
		</table>		
	</div>
</div>