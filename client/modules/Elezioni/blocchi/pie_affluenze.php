<?php 
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Roberto Gigli & Luciano Apolito                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* widget pie google affluenze 
  by luc 25 giugno 2009 */

if (!defined('MODULE_FILE')) {
    die ("You can't access this file dirrectly...");
}



global $circo,$prefix,$dbi,$id_cons,$genere,$id_circ,$id_comune,$id_cons_gen;
$query='';
if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
else $circos=''; 
// numero sezioni scrutinate

	$sql="select t1.*  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos";
	$res2 = $dbi->prepare("$sql");
	$res2->execute();
	$numsez=$res2->rowCount();
	$sezioni=$res2->fetch(PDO::FETCH_NUM);

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
			echo "<h5>Ultime Affluenze</h5>";
			if($numero==$numsez)
				echo "<div style=\"text-align:center;color:#ff0000\">"._PERC."<br><b>"._ORE." $ore,$minuti "._DEL."  $giorno/$mese/$anno</b></div>";
			else
				echo "<div style=\"text-align:center;color:#ff0000\">"._PERC_TEND."<br><b>"._ORE." $ore,$minuti "._DEL."  $giorno/$mese/$anno</b></div>";

#		$res1 = mysql_query("select sum(t1.voti_complessivi), t2.num_gruppo , t2.id_gruppo from ".$prefix."_ele_voti_parziale as t1 left join ".$prefix."_ele_gruppo as t2 on (t1.id_gruppo=t2.id_gruppo) where t1.id_cons='$id_cons' and t1.orario='$orario' and t1.data='$data' group by t2.num_gruppo,t2.id_gruppo order by t2.num_gruppo " , $dbi);
#modifica del 26giugno 09 per gestione circoscrizionali
			if($genere==0)	
			{	
				$sql="select sum(t1.voti_complessivi), t2.num_gruppo , t2.id_gruppo from ".$prefix."_ele_voti_parziale as t1 left join ".$prefix."_ele_gruppo as t2 on (t1.id_gruppo=t2.id_gruppo) where t1.id_cons='$id_cons' and t1.orario='$orario' and t1.data='$data' group by t2.num_gruppo,t2.id_gruppo order by t2.num_gruppo " ;
				$res1 = $dbi->prepare("$sql");
				$res1->execute();

			}else{
				$sql="select sum(t3.voti_complessivi),0,0  from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' $circos";
				$res1 = $dbi->prepare("$sql");
				$res1->execute();
			}
#fine modifica
                                                                                                                            
            while(list($voti_t, $num_gruppo,$id_gruppo) = $res1->fetch(PDO::FETCH_NUM)) {

				$sql="select sum(t3.voti_complessivi)  from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' $circos";		
						if ($genere==0){$query.=" and t3.id_gruppo=$id_gruppo";}
				$res_aff = $dbi->prepare("$sql");
				$res_aff->execute();

				$voti_numero=$res_aff->rowCount();
					
				$sql="select sum(t1.maschi+t1.femmine)  from ".$prefix."_ele_voti_parziale as t3 left join ".$prefix."_ele_sezioni as t1 on t3.id_sez=t1.id_sez left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t3.id_cons='$id_cons' and t3.data='$data' and t3.orario='$orario' $circos";		
				
				if ($genere==0){$query.=" and id_gruppo=$id_gruppo";}
				$res1234 = $dbi->prepare("$sql");
				$res1234->execute();

				list($tot)=$res1234->fetch(PDO::FETCH_NUM);
				if ($tot) $perc=number_format($voti_t*100/$tot,2);
				else {$tot=0;$perc="0.00";}
		  
				$resto=100-$perc;
				if ($genere==0){echo "<div style=\"text-align:center\">referendum n. $num_gruppo</div";}
				echo '<center>
				<div id="piechart" style="width:100%; min-height:200px;"></div><br/>
				<a href="modules.php?id_cons_gen='.$id_cons_gen.'&name=Elezioni&id_comune='.$id_comune.'&file=index&op=affluenze_graf">Tutte le affluenze</a>
				</center>
				<script>google.charts.load("current", {"packages":["corechart"]});
				google.charts.setOnLoadCallback(drawChart);
		  
				function drawChart() {		  
				  var data = google.visualization.arrayToDataTable([
					["Task", "Affluenze"],
					["",  '.$resto.'],
					["'.$perc.'%", '.$perc.']
				  ]);
				  var options = {
					title: "",
					is3D:true,
					 legend: "none",
					 pieSliceText: "label",
					 pieSliceTextStyle: {
						color: "#000",
						bold:true,
						fontSize:12
					  },
					 slices: {
						0: { color: "#ff0000", textStyle:{color:"#fff"}},
						1: { color: "#ffff00" }
					  }
				  };
				  var chart = new google.visualization.PieChart(document.getElementById("piechart"));

				  chart.draw(data, options);
				}</script>';
			}	

		}
	}
?>
