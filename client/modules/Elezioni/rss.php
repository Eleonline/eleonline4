<?php

/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* Ultima modifica 22 aprile 2008 */

$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ?
	$_GET : $_POST;


#header('Content-Type: text/html;');
header("Content-Type: text/xml");

// data
$gmtdiff = date("O", time());
$gmtstr = substr($gmtdiff, 0, 3) . ":" . substr($gmtdiff, 3, 9);
$now = date("Y-m-d\TH:i:s", time());
$now = $now . $gmtstr;
// comune
$sql="SELECT descrizione FROM ".$prefix."_ele_comune where id_comune='$id_comune' ";
$res = $dbi->prepare("$sql");
$res->execute();

	list($descr_com) = $res->fetch(PDO::FETCH_NUM);



echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
echo "<rss version=\"2.0\" \n";
echo "  xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n";
echo "  xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\"\n";
echo "  xmlns:admin=\"http://webns.net/mvcb/\"\n";
echo "  xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\">\n\n";
echo "<channel>\n";
echo "<title>".htmlspecialchars($descr_com)." - $descr_cons</title>\n";
echo "<link>".htmlspecialchars($siteurl)."</link>\n";
echo "<description>".$sitename."</description>\n";
echo "<dc:language>it-IT</dc:language>\n";
echo "<dc:creator>".$adminmail."</dc:creator>\n";
echo "<dc:date>".$now."</dc:date>\n\n";
echo "<sy:updatePeriod>hourly</sy:updatePeriod>\n";
echo "<sy:updateFrequency>1</sy:updateFrequency>\n";
echo "<sy:updateBase>".$now."</sy:updateBase>\n\n";

list ($gruppo,$pro,$titolo)=grupporss();


for($x=0;$x<count($gruppo);$x++){

    // format: 2004-08-02T12:15:23-06:00
    //$date = date("Y-m-d\TH:i:s", strtotime($time));
    //$date = $date . $gmtstr;
    
    echo "<item>\n";
    echo "<title>".$gruppo[$x]." ".$pro[$x] ."% </title>\n";
    echo "<link>$siteurl/modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;file=index</link>\n";
    
#    echo "<description><![CDATA['aa']]></description>\n";

	



    //echo "<guid isPermaLink=\"false\">noreply@ciao.it</guid>\n";
    echo "<dc:subject>$titolo</dc:subject>\n";
    echo "<dc:date>".$now."</dc:date>\n";
    echo "<dc:creator>Postato da ".$sitename."</dc:creator>\n";
    echo "</item>\n\n";
}

echo "</channel>\n";
echo "</rss>\n";




// gruppo
function grupporss(){
global $admin, $bgcolor1, $bgcolor5, $prefix, $dbi, $offset, $min,$descr_cons,$genere,$votog,$votol,$votoc,$circo, $id_cons,$id_cons_gen,$id_comune,$id_circ,$tipo_cons,$w,$l,$op,$siteistat,$flash,$circondt1,$lang;



	
	if ($genere!=0){$tab="ele_voti_gruppo";}
	if ($genere==4){$tab="ele_voti_lista";}
	if ($votog){$tab="ele_voti_lista";}
	$sql="select *  from ".$prefix."_$tab where id_cons='$id_cons' group by id_sez ";
	$res = $dbi->prepare("$sql");
	$res->execute();

	
	if ($res) $numero=$res->rowCount();else $numero=0;
	$sql="select t2.*  from ".$prefix."_ele_sezione as t2, ".$prefix."_ele_sede as t1 where t2.id_cons='$id_cons' and t1.id_sede=t2.id_sede $circondt1";
	$res = $dbi->prepare("$sql");
	$res->execute();

	if ($res) $sezioni=$res->rowCount();else $sezioni=0;
	
	if ($numero>0){
		if ($genere!=0){
		// tot voti
			if (!$circo) 
				$sql="select sum(voti)  from ".$prefix."_$tab where id_cons=$id_cons ";
			if ($votog) 
				$sql="select sum(voti)  from ".$prefix."_ele_voti_lista where id_cons=$id_cons ";	
			$restotv = $dbi->prepare("$sql");
			$restotv->execute();
			list($tot)  = $restotv->fetch(PDO::FETCH_NUM);
			
			$i=0;
			// lista o gruppo
			if ($genere!=4){
			         
				if ($votog){
				
				$sql="select t1.id_gruppo, t1.num_gruppo, t1.descrizione, sum(t2.voti) as somma
				from ".$prefix."_ele_gruppo as t1,
				".$prefix."_ele_voti_lista as t2,
				".$prefix."_ele_lista as t3
        			where 	t1.id_cons='$id_cons'
				and t2.id_lista=t3.id_lista
				and t1.id_gruppo=t3.id_gruppo
				group by t1.id_gruppo
				order by somma desc";
				$res = $dbi->prepare("$sql");
				$res->execute();
				$cosa='id_gruppo';
				
				}else{
				
			
			        $sql="select t1.id_gruppo, t1.num_gruppo, t1.descrizione, sum(t2.voti) as somma
				from ".$prefix."_ele_gruppo as t1
        			left join ".$prefix."_$tab as t2 on (t1.id_gruppo=t2.id_gruppo)
				where 	t1.id_cons='$id_cons' and t1.id_cons=t2.id_cons $circondt1
				group by t2.id_gruppo
				order by somma desc";
				$res = $dbi->prepare("$sql");
				$res->execute();
				$cosa='id_gruppo';
			       }
			
			
			}else{
				$sql="select t1.id_lista, t1.num_lista, t1.descrizione, sum(t2.voti) as somma
				from ".$prefix."_ele_lista as t1
        		left join ".$prefix."_$tab as t2 on (t1.id_lista=t2.id_lista)
				where 	t1.id_cons='$id_cons' and t1.id_cons=t2.id_cons
				group by t2.id_lista
				order by somma desc";
				$res = $dbi->prepare("$sql");
				$res->execute();
				$cosa='id_lista';
			}

		
					
#				$gruppinum=$res->fetch(PDO::FETCH_NUM);
				$altrivoti=0;
				while (list($id,$num,$descrizione,$voti)  = $res->fetch(PDO::FETCH_NUM)){

				
				
				// funz per il taglio corretto della frase 13 feb 2007
				
				$gruppo[$i]=substr($descrizione,0,25);
				
				//if (strlen($descrizione)>25) $gruppo[$i].="...";
				
				$pro[$i]=number_format($voti*100/$tot,2);
				


					
				


			
		       
			$i++;
		}
		


		include_once("language/lang-$lang.php");
		$titolo=""._PERCE." "._VOTIE."";
		
		
		

		
		

		return array($gruppo,$pro,$titolo);
                
               

	  }

	}


}



?>
