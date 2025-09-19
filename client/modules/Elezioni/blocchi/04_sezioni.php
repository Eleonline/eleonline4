<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

if (!defined('MODULE_FILE')) {
    die ("You can't access this file dirrectly...");
}
global  $prefix, $dbi,$id_cons_gen;
$sql = "select chiusa  from ".$prefix."_ele_cons_comune where id_cons_gen='$id_cons_gen'";
	$res = $dbi->prepare("$sql");
	$res->execute();

		list($chiusa) = $res->fetch(PDO::FETCH_NUM);
		
		//if($chiusa!='1') numeri_sezione(); # se la consultazione non è chiusa
		


numeri_sezione(); //lancia la funzione 

function numeri_sezione() {
global  $prefix, $dbi, $circo, $genere,$id_cons_gen,$id_cons,$id_circ,$tipo_cons,$votog,$id_comune;


if (isset($circo) and $circo) $circos="and t2.id_circ='$id_circ'";
else $circos=''; 

		if ($genere==0) $tab="ref";elseif($genere=='4' || $votog) $tab="lista";
		 else $tab="gruppo";
		


		# numero sezioni
		$sql="select t1.id_sez,t1.num_sez  from ".$prefix."_ele_sezioni as t1 left join ".$prefix."_ele_sede as t2 on t1.id_sede=t2.id_sede where t1.id_cons='$id_cons' $circos order by t1.num_sez";
		$res = $dbi->prepare("$sql");
		$res->execute();

		$max = $res->rowCount();
		if(!isset($html)) $html='';
		$html = "\n<table  style=\"margin:0px auto;border:0px; width:90%\"><tr>";
		
		$i=0;$id_circ_old=0;$e=0;
		while(list($sez_id, $sez_num) = $res->fetch(PDO::FETCH_NUM)) {
			$i++;
			/****************************************************************/     
			/* suddivisione in circoscrizione - attivare se è il caso 

			 $result = mysql_query("SELECT id_circ FROM ".$prefix."_ele_sede where id_cons='$id_cons' and id_sede='$sede_id' ", $dbi);
			list($circ_id) = mysql_fetch_row($result);
			if($circ_id!=$id_circ_old){ 
			      $id_circ_old=$circ_id;
			      $result2 = mysql_query("SELECT descrizione FROM ".$prefix."_ele_circoscrizione where id_cons='$id_cons' and id_circ='$circ_id' ", $dbi);
			      list($descrizione) = mysql_fetch_row($result2);
			      echo "</tr></table><table><tr><td>$descrizione</td></tr></table>";
			      echo "\n<table align=\"left\" border=\"0\" width=\"90%\"><tr bgcolor=\"$bgcolor1\">";
			}
			*/
			
			#colora la sezione
			# verifica se la sezione è scrutinata
			 $sql="select *  from ".$prefix."_ele_voti_".$tab." where   id_sez='$sez_id'";
	$res2 = $dbi->prepare("$sql");
	$res2->execute();

			 $numero=$res2->rowCount(); 
			 if ($numero!=0){$e++;$bgsez="#FFFF00";}else{$bgsez="";}
			

			if ($genere==0) $pos="gruppo_sezione";elseif($genere=='4' || $votog) $pos="lista_sezione";
		 	else $pos="gruppo_sezione"; 

			$html .="<td style=\"margin:0px auto; text-align:center; width:5%;background-color:$bgsez;\"><a style=\"background-color:$bgsez;\" href=\"modules.php?id_cons_gen=$id_cons_gen&amp;name=Elezioni&amp;id_comune=$id_comune&amp;perc=true&amp;file=index&amp;op=$pos&amp;minsez=$sez_num&amp;offsetsez=$sez_num\"><b>$sez_num</b></a></td>";

			if (($i%5) ==0) $html .="</tr>\n<tr>";
		}
		while($i%5!=0) {$i++; $html.="<td></td>";}
		$html .="</tr></table>\n";
    // stampa
    if($e!='0'){
	  echo "<div><h5>"._SEZSCRU."</h5></div>";
	  echo "<img style=\"display:block;margin:0px auto;text-align:center;\" alt=\"Grafico\" src=\"modules/Elezioni/grafici/ledex2.php?sez=$e&amp;max=$max\" />";
	   
    echo $html;	}	
}


?>
