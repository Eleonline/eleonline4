<?php 
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Roberto Gigli & Luciano Apolito                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/
/* widget cerca candidato
  by luciano apolito 2015 */
# http://localhost/ele3/trunk/client/modules.php?cognome=vince&id_comune=58047&op=gruppo&name=Elezioni&file=index&id_cons_gen=66
# http://localhost/ele3/trunk/client/modules.php?op=gruppo&name=Elezioni&id_comune=58047&file=index&id_cons_gen=66
if (!defined('MODULE_FILE')) {
    die ("You can't access this file dirrectly...");
}
global $id_comune,$id_cons_gen,$op,$genere;
if($genere>0){
# validatore form
echo '
 <script>
function validateForm()
	{
	var x=document.forms["form_candi"]["cognome"].value;
	if (x==null || x=="")
	  {
	  
	  return false;
	  }
	}
</script>
';

# form
#$url=$_SERVER['REQUEST_URI']; // url della pagina per il reload

echo "
		<div><h5>Cerca il candidato</h5>  
		<form method=\"get\" name=\"form_candi\" action=\"modules.php\" onsubmit='return validateForm()' >
		Inserisci il cognome o/e il nome intero del candidato da cercare<br/>
		<input type=\"text\" name=\"cognome\" maxlength=\"30\" size=\"10\" value=\"\">";
echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\"><input type=\"hidden\" name=\"op\" value=\"$op\"><input type=\"hidden\" name=\"name\" value=\"Elezioni\"><input type=\"hidden\" name=\"file\" value=\"index\"><input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\">";
echo	"<input type=\"submit\" value=\"Cerca\">
		</form>
		</div>
";





$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['cognome'])) $cognome=addslashes($param['cognome']); else $cognome='';
$chiave="$cognome";
$cerca_cand="";
$_SESSION['cerca_cand']='';

if($cognome!=''){
	# divide nome e cognome
	$arr = explode(" ", $cognome);
	$num_arr=count($arr);
	if ($num_arr==2){
		$cognome=$arr[0];
		$nome=$arr[1];
		$numeratore= " AND "; // cognome e nome 
	}else{
		$nome=$cognome;
		$numeratore= " OR "; // cognome o nome
	}

	$sql="SELECT * FROM ".$prefix."_ele_candidati  where ((cognome like '%$cognome%' $numeratore nome like '%$nome%') OR (cognome like '%$nome%' 
	$numeratore nome like '%$cognome%')) and id_cons in(select id_cons from ".$prefix."_ele_cons_comune where id_comune='$id_comune')";
	$res = $dbi->prepare("$sql");
	$res->execute();

	$num_tot = $res->rowCount(); 
	$sql="SELECT * FROM ".$prefix."_ele_candidati  where ((cognome like '%$cognome%' $numeratore nome like '%$nome%') OR (cognome like '%$nome%' 	  $numeratore nome like '%$cognome%'))  and id_cons in(select id_cons from ".$prefix."_ele_cons_comune where id_comune='$id_comune') ORDER BY id_cand DESC LIMIT 0,7";
	$res = $dbi->prepare("$sql");
	$res->execute();

	while (list($id_cand,$id_cons2,$id_lista,$cognome,$nome,$note,$simbolo,$num_candidato) = $res->fetch(PDO::FETCH_NUM)) {
		$sql="SELECT id_cons_gen FROM ".$prefix."_ele_cons_comune  where id_cons='$id_cons2'" ;
		$resl = $dbi->prepare("$sql");
		$resl->execute();
		
			list($id_cons_gen_cand) = $resl->fetch(PDO::FETCH_NUM);
		$sql="SELECT descrizione, tipo_cons FROM ".$prefix."_ele_consultazione  where id_cons_gen='$id_cons_gen_cand'" ;
	$res2 = $dbi->prepare("$sql");
	$res2->execute();

			list($descr_consultazione,$tipo_consul) = $res2->fetch(PDO::FETCH_NUM);
		$sql="SELECT descrizione FROM ".$prefix."_ele_lista  where id_lista='$id_lista'" ;
	$res3 = $dbi->prepare("$sql");
	$res3->execute();

			list($descr_lista) = $res3->fetch(PDO::FETCH_NUM);

		$sql="select *  from ".$prefix."_ele_sezioni where id_cons='$id_cons2'";
	$res4 = $dbi->prepare("$sql");
	$res4->execute();

		$sezioni=$res4->rowCount();
		$cerca_cand .= "<div style=\"text-align:left\"><hr/>";
		$cerca_cand .= "<img src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_lista=$id_lista\" alt=\"foto\" style=\"width:30px; text-align:left;\">";

		if($tipo_consul!='4'){ // non è circoscrizione
			$cerca_cand .="<a href=\"modules.php?name=Elezioni&amp;id_cons_gen=$id_cons_gen_cand&amp;id_comune=$id_comune&amp;op=candidato_sezione&amp;min=$num_candidato&amp;offset=$num_candidato&amp;id_lista=$id_lista&amp;orvert=1&amp;offsetsez=$sezioni&id_circ=\">
			$cognome $nome</a> $descr_consultazione - $descr_lista ";
		}else{
			$cerca_cand .= "$cognome $nome</a> $descr_consultazione - $descr_lista ";
		}
		$cerca_cand .='</div>';

	}

	$cerca_cand .="<hr/>Trovati n.$num_tot con chiave <b>$chiave</b>";
	if($num_tot>="8") $cerca_cand .="<br/>Raffina la ricerca...";

	$_SESSION['cerca_cand']=$cerca_cand;
#	header("location:$url");
	//$cognome='';
}
if(isset($_SESSION['cerca_cand'])) $cerca_cand=$_SESSION['cerca_cand']; else $cerca_cand="";
echo $cerca_cand;
$_SESSION['cerca_cand']='';
}

?>
