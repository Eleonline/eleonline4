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
if (isset($param['descrizione'])) $descrizione=$param['descrizione']; else $descrizione='';
if (isset($param['op'])) $op=$param['op']; else $op='';
if (isset($param['data_inizio'])) $data_inizio=$param['data_inizio']; else $data_inizio='';
if (isset($param['data_fine'])) $data_fine=$param['data_fine']; else $data_fine='';
if (isset($param['link_dait'])) $link_dait=$param['link_dait']; else $link_dait='';
if (isset($param['tipo'])) $tipo_cons=$param['tipo']; else $tipo_cons='';
if (isset($param['id_cons_gen'])) $id_cons_gen=$param['id_cons_gen']; else $id_cons_gen='';
if (isset($param['chiusa'])) $chiusa=$param['chiusa']; else $chiusa='';
if (isset($param['id_conf'])) $id_conf=$param['id_conf']; else $id_conf='';
if (isset($param['preferita'])) $preferita=$param['preferita']; else $preferita='0';
if (isset($param['preferenze'])) $preferenze=$param['preferenze']; else $preferenze='';
if (isset($param['id_fascia'])) $id_fascia=$param['id_fascia']; else $id_fascia='';
if (isset($param['vismf'])) $vismf=$param['vismf']; else $vismf='';
if (isset($param['solo_gruppo'])) $solo_gruppo=$param['solo_gruppo']; else $solo_gruppo='';
if (isset($param['disgiunto'])) $disgiunto=$param['disgiunto']; else $disgiunto='';
if (isset($param['proiezione'])) $proiezione=$param['proiezione']; else $proiezione='';
if($preferita == 'true') {
	$preferita=1; 
	$sql="update ".$prefix."_ele_cons_comune set preferita='0' where preferita='1'";
	$compl = $dbi->prepare("$sql");
	$compl->execute();
}else $preferita=0;
global $prefix,$aid,$dbi,$id_cons_gen,$id_comune;
$salvato=0;
$query="select * from ".$prefix."_ele_consultazione where id_cons_gen=:id_cons_gen";
$res = $dbi->prepare("$query");
$res->execute([
	':id_cons_gen' => $id_cons_gen
]);
if($res->rowCount()) {
	if($op=='salva') {
		#update
		$sql="update ".$prefix."_ele_consultazione set descrizione=:descrizione,data_inizio=:data_inizio,data_fine=:data_fine,link_trasparenza=:link_dait,tipo_cons=:tipo_cons where id_cons_gen=:id_cons_gen";
		try {
			$compl = $dbi->prepare("$sql");
			$compl->execute([
				':descrizione' => $descrizione,
				':data_inizio' => $data_inizio,
				':data_fine' => $data_fine,
				':link_dait' => $link_dait,
				':tipo_cons' => $tipo_cons,
				':id_cons_gen' => $id_cons_gen
			]);
		}
		catch(PDOException $e) {
			$salvato=1;
		}
		$query="select * from ".$prefix."_ele_cons_comune where id_cons_gen=:id_cons_gen";
		$res = $dbi->prepare("$query");
		$res->execute([				
			':id_cons_gen' => $id_cons_gen
			]);

		if($res->rowCount()) {
			$sql="update ".$prefix."_ele_cons_comune set chiusa=:chiusa,id_conf=:id_conf,preferita=:preferita,id_fascia=:id_fascia,vismf=:vismf,solo_gruppo=:solo_gruppo,disgiunto=:disgiunto,preferenze=:preferenze,proiezione=:proiezione where id_cons_gen=:id_cons_gen";
			try {
				$compl = $dbi->prepare("$sql");
				$compl->execute([
				':chiusa' => $chiusa,
				':id_conf' => $id_conf,
				':preferita' => $preferita,
				':id_fascia' => $id_fascia,
				':vismf' => $vismf,
				':solo_gruppo' => $solo_gruppo,
				':disgiunto' => $disgiunto,
				':preferenze' => $preferenze,
				':proiezione' => $proiezione,
				':id_cons_gen' => $id_cons_gen
			]);
			}
			catch(PDOException $e) {
				$salvato=1;
			}		
		}
	}elseif($op=='cancella'){	
		#delete
		$sql="select * from ".$prefix."_ele_cons_comune where id_cons_gen=:id_cons_gen";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons_gen' => $id_cons_gen
			]);
		$row=$compl->fetchAll(PDO::FETCH_ASSOC);
		$id_cons=$row[0]['id_cons'];
		$sql="delete from ".$prefix."_ele_voti_candidato where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_voti_lista where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_voti_gruppo where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_voti_parziale where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_candidato where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_lista where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_gruppo where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_come where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_link where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_numero where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_controllo where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_operatore where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_sezione where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_sede where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		$sql="delete from ".$prefix."_ele_circoscrizione where id_cons=:id_cons";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons' => $id_cons
			]);
		
		$sql="delete from ".$prefix."_ele_consultazione where  id_cons_gen=:id_cons_gen";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons_gen' => $id_cons_gen
			]);
		$sql="delete from ".$prefix."_ele_cons_comune where  id_cons_gen=:id_cons_gen";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':id_cons_gen' => $id_cons_gen
			]);
		if(!$compl->rowCount()) $salvato=1;
	}
}else{
	#insert
		$sql="insert into ".$prefix."_ele_consultazione (descrizione, data_inizio, data_fine, tipo_cons, link_trasparenza) values( :descrizione,:data_inizio,:data_fine,:tipo_cons,:link_dait)";
		$compl = $dbi->prepare("$sql");		
		$compl->execute([
			':descrizione' => $descrizione,
			':data_inizio' => $data_inizio,
			':data_fine' => $data_fine,
			':link_dait' => $link_dait,
			':tipo_cons' => $tipo_cons
		]);
		$sql="select id_cons_gen from ".$prefix."_ele_consultazione where descrizione=:descrizione and data_inizio=:data_inizio and data_fine=:data_fine";
		$compl = $dbi->prepare("$sql");		
		$compl->execute([
			':descrizione' => $descrizione,
			':data_inizio' => $data_inizio,
			':data_fine' => $data_fine
		]);
		list($id_cons_gen2)=$compl->fetch(PDO::FETCH_BOTH);
		$sql="insert into ".$prefix."_ele_cons_comune (chiusa, id_comune, id_cons_gen, id_conf, preferita, preferenze, id_fascia, vismf, solo_gruppo, disgiunto, proiezione) values(:chiusa,:id_comune,:id_cons_gen2,:id_conf,:preferita,:preferenze,:id_fascia,:vismf,:solo_gruppo,:disgiunto,:proiezione)";
		$compl = $dbi->prepare("$sql");
		$compl->execute([
			':chiusa' => $chiusa,
			':id_comune' => $id_comune,
			':id_conf' => $id_conf,
			':preferita' => $preferita,
			':id_fascia' => $id_fascia,
			':vismf' => $vismf,
			':solo_gruppo' => $solo_gruppo,
			':disgiunto' => $disgiunto,
			':preferenze' => $preferenze,
			':proiezione' => $proiezione,
			':id_cons_gen2' => $id_cons_gen2
		]);
		if(!$compl->rowCount()) $salvato=1;
}

if(!$salvato){
	$datal=date('Y-m-d');
	$orariol=date(' H:i:s');
	$riga=addslashes($sql);
	$sqlog="insert into ".$prefix."_ele_log values(:id_cons,'0',:aid,:datal,:orariol,'',:riga,'".$prefix."_ele_comune')";
	$res = $dbi->prepare("$sqlog");
	$res->execute([
		':id_cons' => $id_cons,
		':aid' => $aid,
		':datal' => $datal,
		':orariol' => $orariol,
		':riga' => $riga
	]);
#		echo "Nuovo orario di rilevazione inserito";
}else{
	echo "<tr><td colspan=\"8\">Errore, impossibile salvare i dati - $sql</td></tr>";
}
include('modules/elenco_consultazioni.php');

?>
