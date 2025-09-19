<main>
<div class="container">
	<div class="row text-center">
		<h4 class="fw-semibold text-primary mobile-expanded mt-2">Come si vota</h4>
	</div>
</div>
	<div class="card-wrapper">
		<div class="card card card-bg">
			<div class="container px-4 my-4">
				<div class="row">
					<div class="col-lg-12 px-lg-4 py-lg-2">
					</div>
				</div>
				<?php
				$row=elenco_come();
				if(count($row))
					foreach($row as $campo=>$val) {?>
						<div class="box_infogenerali">
							<section class="col-lg-12 it-page-sections-container">
								<article id="descrizione" class="it-page-section anchor-offset">
								  <p class="font-serif">
									<?php
											echo "<h3>".$val[2]."</h3>";
											if(strlen($val[3])>0) echo "<br>".$val[3];
											echo $val[4];
									?>
								  </p>
								</article>
							</section>
						</div>
					<?php } ?>
			</div>
		</div>
	</div>
</main>
