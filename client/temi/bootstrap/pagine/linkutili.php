<main>
	<div class="container">
		<div class="row text-center">
			<h4 class="fw-semibold text-primary mobile-expanded mt-2">Link utili</h4>
		</div>
	</div>
	<div class="link-list-wrapper">
		<ul class="link-list">
			 <?php
			$row=elenco_link();
			if(count($row))
				foreach($row as $campo=>$val) {	?>
					<li class="box_linkutili">
						<a class="list-item icon-left" href="<?php echo $val[3];?>" target="blank"> 
							<span class="list-item-title-icon-wrapper">
								<svg class="icon icon-primary"><use href="<?php echo $curdir?>/svg/sprites.svg#it-link"></use></svg>
								<span class="list-item-title"><?php echo $val[2];?></span>
							</span>
							<?php
							if(!empty($val[4])) {?>
								<p><?php echo $val[4];?></p>
							<?php } ?>
						</a>
						<li><span class="divider"></span>
						</li>
					</li>
			  <?php } ?>
		</ul>
	</div>
</main>