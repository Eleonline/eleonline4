<?php 
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Roberto Gigli & Luciano Apolito                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

global $circo,$prefix,$dbi,$id_cons,$genere,$id_circ;

if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
else $circos=''; 
// numero sezioni scrutinate
//		$res2 = mysql_query("select *  from ".$prefix."_ele_sezioni where id_cons='$id_cons'",$dbi);
		$sql="select t1.*  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos";
		$res2 = $dbi->prepare("$sql");
		$res2->execute();

		$sezioni=$res2->rowCount();
		
//echo "select *  from ".$prefix."_ele_sezioni where id_cons='$id_cons' $circos";	
	
		
	
// barre
    $l_size = getimagesize("modules/Elezioni/images/barre/leftbar.jpg");
    $m_size = getimagesize("modules/Elezioni/images/barre/mainbar.jpg");
    $r_size = getimagesize("modules/Elezioni/images/barre/rightbar.jpg");
    $l_size2 = getimagesize("modules/Elezioni/images/barre/leftbar2.jpg");
    $m_size2 = getimagesize("modules/Elezioni/images/barre/mainbar2.jpg");
    $r_size2 = getimagesize("modules/Elezioni/images/barre/rightbar2.jpg");
                                                                                                                           
#    $res = mysql_query("select orario,data  from ".$prefix."_ele_rilaff where id_cons_gen='$id_cons_gen' order  by data desc,orario DESC limit 1", $dbi);
#x Luciano: quella sopra diventa quella sotto. in rilaff ci sono tutte le date e orari di affluenza mentre in vot_parziale solo quelli inseriri
# inoltre va messo il desc anche alla data altrimenti il risultato ha la data piu' bassa e l'ora piu' alta
    $sql="select orario,data  from ".$prefix."_ele_voti_parziale where id_cons='$id_cons' order  by data desc,orario desc limit 1";
	$res = $dbi->prepare("$sql");
	$res->execute();

    if($res){

        while(list($orario,$data) = $res->fetch(PDO::FETCH_NUM)) {
        	list ($ore,$minuti,$secondi)=explode(':',$orario);
        	list ($anno,$mese,$giorno)=explode('-',$data);
        	$tot_v_m=0;$tot_v_d=0;$tot_t=0;
	
  		$sql="select t3.id_sez from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' $circos  group by t3.id_sez ";
		$res1 = $dbi->prepare("$sql");
		$res1->execute();


		$numero=$res1->rowCount();
	
		echo "<br /><div><h5>Ultime Affluenze</h5></div>";
               echo "<div style=\"text-align:center;color:#ff0000\"><b>"._ORE." $ore,$minuti "._DEL."  $giorno/$mese/$anno</b></div>";
                                                                                                                             

                
                
		
#modifica del 26giugno 09 per gestione circoscrizionali
if($genere==0)		
	$sql="select sum(t1.voti_complessivi), t2.num_gruppo , t2.id_gruppo from ".$prefix."_ele_voti_parziale as t1 left join ".$prefix."_ele_gruppo as t2 on (t1.id_gruppo=t2.id_gruppo) where t1.id_cons='$id_cons' and t1.orario='$orario' and t1.data='$data' group by t2.num_gruppo,t2.id_gruppo order by t2.num_gruppo " ;


else
  	$sql="select sum(t3.voti_complessivi),0,0  from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' $circos";
$res1 = $dbi->prepare("$sql");
$res1->execute();

#fine modifica
                                                                                                                                       

		
                                                                                                                             
                while(list($voti_t, $num_gruppo,$id_gruppo) = $res1->fetch(PDO::FETCH_NUM)) {
					if(!$id_gruppo) $id_gruppo=0;
//  		$res1 = mysql_query(,$dbi);
//                	$query="select sum(voti_complessivi) from ".$prefix."_ele_voti_parziale where orario='$orario' and data='$data' and id_cons='$id_cons'";
$sql="select sum(t3.voti_complessivi)  from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' $circos";		
    if ($genere==0){$sql.=" and t3.id_gruppo=$id_gruppo";}
		$res_aff = $dbi->prepare("$sql");
		$res_aff->execute();

			$voti_numero=$res_aff->rowCount();
 //               	$query="select sum(maschi+femmine) from ".$prefix."_ele_voti_parziale as t1 , ".$prefix."_ele_sezioni as t2 where t1.id_cons=$id_cons and t1.id_sez=t2.id_sez and orario='$orario' and data='$data'";
			$sql="select sum(t1.maschi+t1.femmine)  from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' $circos";		
			
			if ($genere==0){$sql.=" and id_gruppo=$id_gruppo";}
				$res1234 = $dbi->prepare("$sql");
				$res1234->execute();

                	list($tot)=$res1234->fetch(PDO::FETCH_NUM);
                	if ($tot)
                	$perc=number_format($voti_t*100/$tot,2);
			else {$tot=0;$perc="0.00";}																	
			echo "<table class=\"td-80\"><tr class=\"bggray\">";
			if ($genere==0){echo "<td>N.</td>";}
				echo "<td><b>"._VOTANTI."</b></td><td><b>"._PERCE."</b></td>";
				echo "<td><b>"._SEZIONI."</b></td>";
				echo "</tr>";
        		echo "<tr class=\"bggray2\">";
        		if ($genere==0){echo "<td>$num_gruppo</td>";}
//        		echo "<td>$voti_t</td><td>$perc %</td><td>$numero</td>
        		echo "<td>$voti_t</td><td>$perc %</td><td>$numero</td>
			</tr></table>";
	

        // barre
                                                                                                                             
        	echo "<table><tr><td><table><tr><td>&nbsp;</td><td>
<img src=\"modules/Elezioni/images/barre/leftbar2.jpg\" height=\"$l_size2[1]\" width=\"$l_size2[0]\" alt=\"\" /><img src=\"modules/Elezioni/images/barre/mainbar2.jpg\" alt=\"\" height=\"$m_size2[1]\" width=\"". ($perc * 1)."\" /><img src=\"modules/Elezioni/images/barre/rightbar2.jpg\" height=\"$r_size2[1]\" width=\"$r_size2[0]\" alt=\"\" /><span class=\"red\"> $perc</span> % <br /></td></tr>\n";
		
		$tot_gen=$tot;


		echo  "<tr><td></td><td><img src=\"modules/Elezioni/images/barre/leftbar.jpg\" height=\"$l_size[1]\" width=\"$l_size[0]\" alt=\"\" /><img src=\"modules/Elezioni/images/barre/mainbar.jpg\" alt=\"\" height=\"$m_size[1]\" width=\"".(100 * 1)."\" /><img src=\"modules/Elezioni/images/barre/rightbar.jpg\" height=\"$r_size[1]\" width=\"$r_size[0]\" alt=\"\" /> 100 % </td></tr></table>";
		 echo "</td></tr></table>";
		 
	}	

        }
}
?>
