<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
		
if (stristr(htmlentities($_SERVER['PHP_SELF']), "javascript.php")) {
    Header("Location: ../index.php");
    die();
}

##################################################
# Include funzioni javascript                    #
##################################################
#timer per tema tour
# inizio rotazione
if (isset($_SESSION['ruota'])) 
{ 
?>

		<script type="text/javascript" language="javascript">
<!--
function loadpage() {

thetimer = setTimeout("changepage()", 20000);


}

function changepage() {

newlocation = "<?php echo "modules.php?csv=1&block=0&id_cons_gen=".$_GET['id_cons_gen']."&id_comune=".$_GET['id_comune']; ?>"
location = newlocation
}
// --></script> 

<?php
}
# fine rotazione
# googlemaps per sezioni
# variabili nel config.php 
# gkey= chiave google informazioni su come reperirla per il proprio sito qui 
# https://cloud.google.com/maps-platform/user-guide/account-changes/#no-plan
# googlemaps 1=attivo 2: disattivo
# funzione by eleonline.it
#########################################################







function googlemaps(){
global $dbi,$prefix,$id_comune,$googlemaps,$op,$gkey,$lang;
# recupera gli inidirizzi
    $id_sede=$_GET['id_sede'];
	$sql="SELECT descrizione FROM ".$prefix."_ele_comuni where id_comune='$id_comune' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($comune) = $res->fetch(PDO::FETCH_NUM);
	$sql="SELECT indirizzo from ".$prefix."_ele_sede where id_sede='$id_sede' ";
	$res = $dbi->prepare("$sql");
	$res->execute();
	list($indirizzo) = $res->fetch(PDO::FETCH_NUM);

    $address=rawurlencode("$indirizzo,$comune,58047");

	$resultGeoCode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&amp;sensor=false');
	$output= json_decode($resultGeoCode);
	
	if($output->status == 'OK'){
		$latitude 	= $output->results[0]->geometry->location->lat; //Returns Latitude
		$longitude 	= $output->results[0]->geometry->location->lng; // Returns Longitude
		$location	= $output->results[0]->formatted_address;
	}else{
		$latitude=0;
		$longitude=0;
		$location='';
	}


	$coords['lat'] = $latitude;
	$coords['long'] = $longitude;





/*



	
        $url = sprintf('http://maps.google.com/maps?output=js&q=%s',rawurlencode($address));
	$result = false;
	
	if($result = file_get_contents($url)) {
	    $coords = array();
	
		if(strpos($result,'errortips') > 1 || strpos($result,'Did you mean:') !== false) {
			 return false;
		}
		
		preg_match('!center:\s*{lat:\s*(-?\d+\.\d+),lng:\s*(-?\d+\.\d+)}!U', $result, $matches);
		
		$coords['lat'] = $matches[1];
		$coords['long'] = $matches[2];
	}

*/




# type="text/javascript"


echo '
<script src="http://maps.google.com/maps/api/js?sensor=true&language=it"></script>
   <script>
      function maps() {
         var latlng = new google.maps.LatLng('.$coords['lat'].','.$coords['long'].'); // centro della mappa
         var myLatlng = new google.maps.LatLng('.$coords['lat'].','.$coords['long'].'); // segnapunto
         // definizione della mappa
         var myOptions = {
             zoom: 16,
             center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR}
         }
         mymap = new google.maps.Map(document.getElementById("map"), myOptions);
         // definizione segnapunto
         var marker = new google.maps.Marker({
            position: myLatlng,
            map: mymap,
            title:"'.$address.'"
         });

      }
   </script>
';
}
echo '<script src="https://www.gstatic.com/charts/loader.js"></script>';

?>
