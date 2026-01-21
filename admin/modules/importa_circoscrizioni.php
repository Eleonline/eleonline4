<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/************************************************************************/
/* Modulo salva affluenze                                               */
/* Amministrazione                                                      */
/************************************************************************/
if(is_file('includes/check_access.php'))
	require_once 'includes/check_access.php';
else
	require_once '../includes/check_access.php';

$param=strtolower($_SERVER['REQUEST_METHOD']) == 'get' ? $_GET : $_POST;
if (isset($param['descrizione'])) $descrizione=addslashes($param['descrizione']); else $descrizione='';
if (isset($param['id_consultazione_origine'])) $id_cons_origine=intval($param['id_consultazione_origine']); else $id_cons_origine='0';
if (isset($param['id_consultazione_dest'])) $id_cons_dest=addslashes($param['id_consultazione_dest']); else $id_cons_dest='0';

global $prefix,$aid,$dbi,$id_cons_gen,$id_cons,$id_comune;
$row = dati_cons_comune($id_cons_origine);
$idcpred = $row[0]['id_cons'];
$idccorr = $id_cons;
$sql="delete from ".$prefix."_ele_sezione where id_cons='$idccorr'";
$compl = $dbi->prepare("$sql");
$compl->execute();
$sql="delete from ".$prefix."_ele_sede where id_cons='$idccorr'";
$compl = $dbi->prepare("$sql");
$compl->execute();
$sql="delete from ".$prefix."_ele_circoscrizione where id_cons='$idccorr'";
$compl = $dbi->prepare("$sql");
$compl->execute();

$salvato=0;
$row = elenco_circoscrizioni($idcpred);
foreach($row as $val){
	$sql="insert into ".$prefix."_ele_circoscrizione (id_cons, num_circ, descrizione) values( :id_cons,:numero,:descrizione )";
	$compl = $dbi->prepare("$sql");		
	try {
		$compl->execute([
		':id_cons' => $idccorr,
		':numero' => $val['num_circ'],
		':descrizione' => $val['descrizione']
		]); 
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
		$salvato=1;
	}
	$query="select id_circ from ".$prefix."_ele_circoscrizione where num_circ='".$val['num_circ']."' and id_cons='$idccorr'";
	$res = $dbi->prepare("$query");
	$res->execute();
	list($idcirctmp)=$res->fetch(PDO::FETCH_NUM);
	$sql="select * from ".$prefix."_ele_sede where id_circ='".$val['id_circ']."'";
	$sth = $dbi->prepare("$sql");
	$sth->execute();
	$rowsede = $sth->fetchAll(PDO::FETCH_BOTH);
	foreach($rowsede as $valsede) {
		$sql="insert into ".$prefix."_ele_sede (id_cons, id_circ, indirizzo, telefono1, telefono2, fax, responsabile, mappa, filemappa, latitudine, longitudine, id_ubicazione, ospedaliera) values( :id_cons,:id_circ,:indirizzo,:telefono1,:telefono2,:fax,:responsabile,:mappa,:filemappa,:latitudine,:longitudine,:id_ubicazione,:ospedaliera )"; 
		$compl = $dbi->prepare("$sql");		
		try {
			$compl->execute([
			':id_cons' => $id_cons,
			':id_circ' => $idcirctmp,
			':indirizzo' => $valsede['indirizzo'],
			':telefono1' => $valsede['telefono1'],
			':telefono2' => $valsede['telefono2'],
			':fax' => $valsede['fax'],
			':responsabile' => $valsede['responsabile'],
			':mappa' => $valsede['mappa'],
			':filemappa' => $valsede['filemappa'],
			':latitudine' => $valsede['latitudine'],
			':longitudine' => $valsede['longitudine'],
			':id_ubicazione' => $valsede['id_ubicazione'],
			':ospedaliera' => $valsede['ospedaliera']
			]); 
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			$salvato=1;
		}
		$query="select id_sede from ".$prefix."_ele_sede where id_cons=:id_cons and indirizzo=:indirizzo";
		$res = $dbi->prepare("$query");
		$res->execute([
			':id_cons' => $idcpred,
			':indirizzo' => $valsede['indirizzo']
		]);
		list($idsedetmp)=$res->fetch(PDO::FETCH_NUM);
		$sql="select * from ".$prefix."_ele_sezione where id_sede=:id_sede"; 
		$sth = $dbi->prepare("$sql");
		$sth->execute([
			':id_sede' => $idsedetmp
		]);
		$rowsezioni = $sth->fetchAll(PDO::FETCH_BOTH);
		foreach($rowsezioni as $valsezione) {
			$sql="insert into ".$prefix."_ele_sezione (id_cons, id_sede, num_sez, maschi, femmine, validi, nulli, bianchi, contestati, solo_gruppo, autorizzati_m, autorizzati_f, voti_nulli, validi_lista, contestati_lista, voti_nulli_lista, solo_lista, colore) values( :id_cons, :id_sede, :num_sez, :maschi, :femmine, :validi, :nulli, :bianchi, :contestati, :solo_gruppo, :autorizzati_m, :autorizzati_f, :voti_nulli, :validi_lista, :contestati_lista, :voti_nulli_lista, :solo_lista, :colore )"; 
			$compl = $dbi->prepare("$sql");		
			try {
				$compl->execute([
				':id_cons' => $id_cons,
				':id_sede' => $idsedetmp, 
				':num_sez' => $valsezione['num_sez'], 
				':maschi' => $valsezione['maschi'], 
				':femmine' => $valsezione['femmine'], 
				':validi' => $valsezione['validi'], 
				':nulli' => $valsezione['nulli'], 
				':bianchi' => $valsezione['bianchi'], 
				':contestati' => $valsezione['contestati'], 
				':solo_gruppo' => $valsezione['solo_gruppo'], 
				':autorizzati_m' => $valsezione['autorizzati_m'], 
				':autorizzati_f' => $valsezione['autorizzati_f'], 
				':voti_nulli' => $valsezione['voti_nulli'], 
				':validi_lista' => $valsezione['validi_lista'], 
				':contestati_lista' => $valsezione['contestati_lista'], 
				':voti_nulli_lista' => $valsezione['voti_nulli_lista'], 
				':solo_lista' => $valsezione['solo_lista'], 
				':colore' => $valsezione['colore']
				]); 
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
				$salvato=1;
			}

		}	
	}
}

if(!$salvato){
	$datal=date('Y-m-d');
	$orariol=date(' H:i:s');
	$riga=addslashes($sql);
	$sqlog="insert into ".$prefix."_ele_log values('$id_cons','0','$aid','$datal','$orariol','','$riga','".$prefix."_ele_comune')";
	$res = $dbi->prepare("$sqlog");
	$res->execute();
#		echo "Nuovo orario di rilevazione inserito";
}else{	
	echo "<tr><td colspan=\"3\">Errore, impossibile salvare i dati - $sql</td></tr>";
}
include('modules/elenco_circoscrizioni.php');

?>
