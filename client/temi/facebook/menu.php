<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}

/************************
Funzione Menu a cascata
*************************/
// controlla javascript by linuxap
/*
$js=jsexist();
if($js=="b"){
$_SESSION['newtema']="default";
die("javascript non supportato - <a href=\"index.php\">torna all'homepage</a>");
}
*/
        
        // definizione variabile per button 'ok' nei form per il noscript
	$button="<br /><object><noscript><div><input name=\"vai\" type=\"image\" src=\"modules/Elezioni/images/ok2.jpg\" alt=\"ok\" title=\"ok\" /></div></noscript></object>";
	$sql="select descrizione,fascia from ".$prefix."_ele_comuni where id_comune=$id_comune";
	$rescomu = $dbi->prepare("$sql");
	$rescomu->execute();
	list($descr_com,$fascia)=$rescomu->fetch(PDO::FETCH_NUM);
	$sql="select id_fascia from ".$prefix."_ele_cons_comune where id_comune=$id_comune and id_cons_gen=$id_cons_gen";
	$rescomu = $dbi->prepare("$sql");
	$rescomu->execute();
	if($rescomu->rowCount()) list($fascia)=$rescomu->fetch(PDO::FETCH_NUM);

        echo '
	    <div id="contiene">
	    	
		    
		<ul id="nav"><li>
		 <a href="http://www.eleonline.it"><span style="font-size:18px;font-family: Comics;"><strong>elebook</strong></span></a>
		</li>
		<li>
		    <a href="index.php"><strong>Home</strong></a>
		</li>';

	
       
        // inizio tabella
	
	
	/*********************************** 
		Scelta Comune
	***********************************/
	
	if ($multicomune=='1')
	{
	      $sql="select t1.id_comune,t1.descrizione,count(0) from ".$prefix."_ele_comuni as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_comune=t2.id_comune and t2.chiusa!='2' group by t1.id_comune,t1.descrizione order by t1.descrizione asc";
	$rescomu = $dbi->prepare("$sql");
	$rescomu->execute();

	      $esiste_multi=$rescomu->rowCount();
	      if ($esiste_multi>=1) {
		    echo " <li>
				<a href=\"#\"><strong>"._COMUNI."</strong></a>
			  
			  <ul>";
			
		      while (list($id,$descrizione,)=$rescomu->fetch(PDO::FETCH_NUM)){
			    echo "<li><a href=\"modules.php?op=gruppo&amp;name=Elezioni&amp;id_comune=$id&amp;file=index\">
    <img src=\"modules/Elezioni/images/logo.gif\" width=\"16\" height=\"16\" class=\"nobordo\" alt=\"$descrizione\" /> $descrizione</a></li>";
			}
		      echo "</ul></li>";
			   
	      }		
	} // fine scelta comune
	
	
	
	/*********************************** 
		Scelta Consultazione
	***********************************/
       
	$sql="SELECT t1.id_cons_gen,t1.descrizione FROM ".$prefix."_ele_consultazione as t1, ".$prefix."_ele_cons_comune as t2 where t1.id_cons_gen=t2.id_cons_gen and t2.id_comune='$id_comune' and t2.chiusa!='2' order by t1.data_fine desc" ;
	$res = $dbi->prepare("$sql");
	$res->execute();
 
	$esiste=$res->rowCount();
	//se esiste consultazione fa vedere i dati
	if ($esiste>=1) {
	echo " <li>
	      <a href=\"#\"><strong>"._ELEZIONI."</strong></a>
	    <ul>";
	 
	    while(list($id,$descrizione) = $res->fetch(PDO::FETCH_NUM)) {
			$descrizione=ucfirst(strtolower($descrizione));
		echo "<li class=\"icon matita\" ><a href=\"modules.php?op=gruppo&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;id_cons_gen=$id\">
	      ".substr($descrizione,0,33)."</a></li>";
     		
	    }
 
	   
	echo "</ul></li>";





	/*********************************** 
		Scelta Info
	***********************************/
        //$temp = array('confronti'=>'','come'=>'','numeri'=>'','servizi'=>'','link'=>'','dati'=>'','affluenze_sez'=>'','votanti'=>'');

       echo " <li><a href=\"#\"><strong>"._INFO."</strong></a>
	    <ul>";
	    echo "
	    <li  class=\"sep\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;op=come&amp;id_comune=$id_comune&amp;file=index&amp;info=confronti\">"._CONFRONTI."</a><span></span></li>
	    <li class=\"icon voto\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;op=come&amp;id_comune=$id_comune&amp;file=index&amp;info=come\"> "._COME."</a></li>
	    <li class=\"icon numeri\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;op=come&amp;id_comune=$id_comune&amp;file=index&amp;info=numeri\">"._NUMERI."</a></li>
	   <li class=\"icon servizi\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;op=come&amp;id_comune=$id_comune&amp;file=index&amp;info=servizi\">"._SERVIZI."</a></li>
	  <li><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;op=come&amp;id_comune=$id_comune&amp;file=index&amp;info=link\">"._LINK."</a></li>
	  <li  class=\"sep\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;op=come&amp;id_comune=$id_comune&amp;file=index&amp;info=dati\">"._DATI."</a><span></span></li>
	  <li class=\"icon affluenze\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;op=come&amp;id_comune=$id_comune&amp;file=index&amp;info=affluenze_sez\">"._AFFLUENZE."</a></li>
	  <li class=\"icon votanti\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;op=come&amp;id_circ=$id_circ&amp;id_comune=$id_comune&amp;file=index&amp;info=votanti\">"._VOTANTI."</a></li>

";


	  echo "</ul></li>";


		/*********************************** 
		Scelta Dati
		***********************************/

 	$sql="SELECT count(0) FROM ".$prefix."_ele_circoscrizione where id_cons='$id_cons' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	list($num_circ) = $res->fetch(PDO::FETCH_NUM);
       	 echo " <li><a href=\"#\"><strong>"._RISULTATI."</strong></a>
	    <ul>";
		if (($genere==5 and $votog) or !$votog) {
	      if (!$circo and $num_circ>1)
	      echo "<li class=\"icon candi\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=gruppo_circo\">".substr(_GRUPPO." "._PER." "._CIRCO,0,36)."</a></li>";
	      echo "<li class=\"icon candi\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=gruppo_sezione\">".substr(_GRUPPO." "._PER." "._SEZIONI,0,36)."</a></li>";
	  }

	if (!$votol and ($fascia>$limite || $limite==0)){ // si vota per la lista
			if ($genere>2) {
				if (!$circo and $num_circ>1)
				  echo "<li class=\"icon liste\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=lista_circo\">".substr(_LISTA." "._PER." "._CIRCO,0,36)."</a></li>";
				  
				  echo "<li class=\"icon liste\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=lista_sezione\">".substr(_LISTA." "._PER." "._SEZIONI,0,36)."</a></li>";

			         
			      }
		    	  
		}
	  if ($genere>3 and !$votoc) {
				if (!$votoc){
			      		if(!$circo and $num_circ>1)
					echo "<li class=\"icon consi\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=candidato_circo\">".substr(_CONSI." "._PER." "._CIRCO,0,36)."</a></li>";
				  echo "<li class=\"icon consi\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=candidato_sezione\">".substr(_CONSI." "._PER." "._SEZIONI,0,36)."</a></li>";
				
			}
      		}
       		$sql="SELECT proiezione FROM ".$prefix."_ele_cons_comune where id_cons='$id_cons'" ;
			$resc = $dbi->prepare("$sql");
			$resc->execute();

      		list($proiezione)=$resc->fetch(PDO::FETCH_NUM); 
			if ($hondt>=1 and $proiezione==1) {
		     echo "<li class=\"icon dontd\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=consiglieri\">"._CALCONS."</a></li>"; 			

      		}		


	  echo "</ul></li>";




	/*********************************** 
		Scelta Grafici
	***********************************/

      echo " <li><a href=\"#\"><strong>"._GRAFICI."</strong></a>
	    <ul>";


		echo "<li class=\"icon stat\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=affluenze_graf\">"._AFFLUENZE."</a></li>"; 

		echo "<li class=\"sep\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=graf_votanti\">"._VOTI."</a><span></span></li>"; 
		if (($genere==5 and $votog) or !$votog)
			echo "<li class=\"icon graf\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=graf_gruppo\">"._GRUPPO."</a></li>"; 
		if($hondt==0) $limite=0;
		if(($genere>2) and ($fascia>$limite || $limite==0))
		echo "<li class=\"icon graf\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=graf_lista&amp;visgralista=1\">"._LISTA."</a></li>"; 
		
		if ($genere>1 and !$votoc){
			echo "<li class=\"icon consi\"><a href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index&amp;op=graf_candidato\">"._CONSI."</a></li>"; 
		}

	 echo "</ul></li>";


	} // fine verifica esistenza consultazione : variabile $esiste
	  


           ################ tema ##### 
            if ($tema_on=="1"){
		
	   	
           	include("temi/facebook/tema.php");		
	   	
	   }	
//echo "</div>";
echo " <li><a href=\"#\"><strong>"._OPTIONS."</strong></a>
	    <ul>";

language();

flash();

noblocco();
echo "</ul></li>";

echo "<li><a href=\"http://www.eleonline.it/site/modules.php?name=Contatti\"><i><span style=\"font-size:10px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
     by luciano apolito & roberto gigli</span></i></a>
	    </li>";

echo "</ul></div>";


# linguaggio x demo

function language(){
global $lang,$name,$op,$file,$filelang,$id_comune,$op,$id_cons,$id_cons_gen;
$filename=$filelang;
// linguaggio

$menulist='';
// scelta linguaggio
    	$langdir = dir("modules/Elezioni/language");
    	while($func=$langdir->read()) {
		if(substr($func, 0, 5) == "lang-") {
	    	$menulist .= "$func ";
		}
    	}
    	closedir($langdir->handle);
    	$menulist = explode(" ", $menulist);
    	sort($menulist);
    	for ($i=0; $i < sizeof($menulist); $i++) {
		if($menulist[$i]!="") {
	    		$tl = preg_replace("/lang-/","",$menulist[$i]);
	    		$tl = preg_replace("/.php/","",$tl);
	    		$altlang = ucfirst($tl);
                        
			if ($lang==$tl) $bordo="class=\"bordo\"";else $bordo="class=\"nobordo\"";
	    		echo "<li><a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;newl=$tl\"><img $bordo src=\"modules/Elezioni/images/$tl.gif\"  alt=\"$altlang\" title=\"$altlang\"  width=\"15\" /> "._LINGUA."  $tl</a></li>";
		}
    	}

}






########################### Blocchi
# flash x demo
function flash(){
global $flash,$name,$id_comune,$op,$id_cons,$id_cons_gen;

if ($flash=='1'){ 
     echo "<li><a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;flash=0\"><img class=\"nobordo\" src=\"modules/Elezioni/images/flashno.gif\" alt=\"NoFlash\" title=\"NoFlash\" width=\"15\"/> "._OFF." Flash</a></li>";

}else{

echo "<li><a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;flash=1\"><img class=\"nobordo\" src=\"modules/Elezioni/images/flashyes.gif\" alt=\"YesFlash\" title=\"YesFlash\" width=\"15\" /> "._ON." Flash</a></li>";


}

}
 
 
# blocco no
function noblocco(){
global $blocco,$name,$id_comune,$op,$id_cons,$id_cons_gen;

if ($blocco=='1'){ 
     	echo "<li><a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;block=0\"><img class=\"nobordo\" src=\"modules/Elezioni/images/close.gif\" alt=\"NoBlocco\" title=\"NoBlocco\" width=\"15\"/> "._OFF." "._BLOCCO."</a></li>";

}else{

	echo "<li><a href=\"modules.php?name=$name&amp;op=$op&amp;id_comune=$id_comune&amp;id_cons=$id_cons&amp;id_cons_gen=$id_cons_gen&amp;block=1\"><img class=\"nobordo\" src=\"modules/Elezioni/images/open.gif\" alt=\"YesBlocco\" title=\"YesBlocco\" width=\"15\" /> "._ON." "._BLOCCO."</a></li>";

}

}



      		


	
?>
