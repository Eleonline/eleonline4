<?php
# tema Blu


echo '<link rel="stylesheet" href="temi/'.$tema.'/layout/styles/layout.css" type="text/css" />
<!-- <script type="text/javascript" src="temi/'.$tema.'/layout/scripts/jquery.min.js"></script> -->
<!-- Superfish Menu
<script type="text/javascript" src="temi/'.$tema.'/layout/scripts/superfish/jquery.hoverIntent.js"></script>
<script type="text/javascript" src="temi/'.$tema.'/layout/scripts/superfish/superfish.js"></script>
<script type="text/javascript">
jQuery(function () {
    jQuery(\'ul.nav\').superfish();
});
</script> 
-->
';
## menu cascata 
 echo '<!-- <style type="text/css">
		html,body{margin:0;padding:0}
		div#contiene{margin:0 auto;background:  #E0E0E0;color:#292929}
	    </style> -->
	    <link rel="stylesheet" type="text/css" href="temi/'.$tema.'/menu/menu-dd.css"> 
	    <!--
		<script type="text/javascript" src="temi/'.$tema.'/menu/jquery-1.2.6.pack.js"></script>
	    <script type="text/javascript" src="temi/'.$tema.'/menu/jquery.hoverIntent.minified.js"></script>
	    <script type="text/javascript" src="temi/'.$tema.'/menu/jquery-ddi2.js"></script>
		-->
';

?>