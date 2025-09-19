<?php
# tema default
# for eleonline
include_once("modules/Elezioni/funzioni.php");
include_once("temi/inc/button.php");


########## no blocco x grafici e risultati
if (!isset($param['op'])) $param['op']='';
if($blocco!=1 || $param['op']=="graf_gruppo" || $param['op']=="gruppo_circo" || $param['op']=="gruppo_sezione"
|| $param['op']=="lista_circo" || $param['op']=="lista_sezione"  || $param['op']=="candidato_circo" || $param['op']=="candidato_sezione"
)$blocco=''; else $blocco=1;

function testata(){
global $tema,$file,$sitename,$blocco,$dbi,$prefix,$id_comune,$descr_cons,$op;

$sql="SELECT descrizione,simbolo FROM ".$prefix."_ele_comuni where id_comune='$id_comune' ";
$res = $dbi->prepare("$sql");
$res->execute();

	list($descr_com,$simbolo) = $res->fetch(PDO::FETCH_NUM);
        $descr_com =stripslashes($descr_com); 


?>

<!-- Page Wrapper -->
			<div id="page-wrapper">

				<!-- Header -->
					<header id="header">
						<h3><a href="index.php"><?php echo "$descr_com";?></a></h3>
						<nav class="nav">
							<ul>
								<li class="special">
									<a href="#menu" class="menuToggle"><span>Menu</span></a>
									<div id="menu">
<?php
if ($file=="index") menu(); 

?>


										
									</div>
								</li>
							</ul>
							

						</nav>

						
					</header>
<?php
/*
				<!-- Banner -->
					<section id="banner">
						<div class="inner">
							<h2>Elezioni on Line</h2>
							<p><?php echo "Comune di $descr_com";?><br />
							site template freebie<br />
							crafted by <a href="http://html5up.net">HTML5 UP</a>.</p>
							<ul class="actions">
								<li><a href="#" class="button special">Activate</a></li>
							</ul>
						</div>
						<a href="#one" class="more scrolly">Learn More</a>
					</section>
*/
?>



					<!-- Main -->
					
					<article id="main">
					<?php	
					if($op=="gruppo"){ 
	
						echo '<header>
							<h2>'.$descr_cons.'</h2>
							<p>Elezioni on line in tempo reale</p>	
							<p style="font-size:0.5em;"><i><a href="https://www.eleonline.it">Software Eleonine by Luciano Apolito & Roberto Gigli</a></i>
						</header>';
					}
					?>		
					<!-- One -->
					<section id="one" class="wrapper style5">
							<div class="inner">

					
<?php
			





	

 
	$check=check_block("dx"); // check exist box
		


}

function piede(){
global $blocco,$tema;


	echo' <section id="three" class="wrapper style3 special">
						<div class="inner">
							<ul class="features">';
								
				$check=check_block("dx");
				if ($blocco=='1' && $check!=0)block_qua("dx");
				$check=check_block("sx");
				if ($blocco=='1' && $check!=0)block_qua("sx");
				
	echo '</ul></div></section>';
	echo "</div></section></article></div>";
	
		
    		







if($tema=="spectral"){
			echo '<!-- Scripts -->
			<script src="temi/'.$tema.'/assets/js/jquery.min.js"></script>
			<script src="temi/'.$tema.'/assets/js/jquery.scrollex.min.js"></script>
			<script src="temi/'.$tema.'/assets/js/jquery.scrolly.min.js"></script>
			<script src="temi/'.$tema.'/assets/js/skel.min.js"></script>
			<script src="temi/'.$tema.'/assets/js/util.js"></script>
			<!--[if lte IE 8]><script src="temi/'.$tema.'/assets/js/ie/respond.min.js"></script><![endif]-->
			<script src="temi/'.$tema.'/assets/js/main.js"></script>
			';
}		

}

function block_qua($pos){

global $prefix,$dbi;

	if($pos=="dx") $p=0; elseif($pos=="sx")$p=1;else $p='';
	
	$sql="SELECT * FROM ".$prefix."_ele_widget where pos_or='$p' and attivo='1' order by pos_ver asc";
$resblk = $dbi->prepare("$sql");
$resblk->execute();
	if($resblk->rowCount()){
		
		while ($row = $resblk->fetch(PDO::FETCH_BOTH)) {
			$id_w=$row['id'];
			$nome=$row['nome_file'];
			if($id_w>=1){
				echo '<li class="icon fa-laptop">';
				include ("modules/Elezioni/blocchi/$nome");
				echo '</li>';
			}
		}	
		
	}
}		





?>
