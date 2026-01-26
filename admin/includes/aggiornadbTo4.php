<?php

#imposta il charset su utf8, qualsiasi altro valore per cambiarlo in latin1;
$newcs='utf8';
global $ctrlerr,$strlog; 
$strlog='';
/*
@require_once("../../config.php"); 
	try{
		$dbi = new PDO("mysql:host=$dbhost", $dbuname, $dbpass, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)); 
    	$sql = "use $dbname";
    	$dbi->exec($sql);	
    }
	catch(PDOException $e)
	{
	    die( $sql . "<br>" . $e->getMessage());
	} 
*/

###############
function aggiorna($sql,$dbi,$sql2,$num){
	$ret=0;
	global $strlog;
	try{
		$res = $dbi->prepare("$sql");
		$res->execute();
		$ret= 1;
    }
	catch(PDOException $e)
	{
		$ret=0;
		$ctrlerr=1;
		$strlog.= "<br><span style=\"color: red;\">- Aggiornamento Fallito: $sql</span>";
		return $ret;
	}

	if("$sql2"!=""){
		try{
		  $res = $dbi->prepare("$sql2");
		  $res->execute();		
		  $ret=2;
		}	
		catch(PDOException $e)
		{
		  $ret=0;
		  $ctrlerr=1;
		  $strlog.="<br><span style=\"color: red;\">- Aggiornamento Fallito: $sql2</span>";
		  return $ret;
		}
	} 
	$strlog= "<br><span style=\"color: green;\">- Aggiornamento eseguito correttamente</span>";
	return $ret;
}

function aggiorna_index($tab,$ind,$dbi,$sql2,$num){
	$ret=0;
#	$conn->getAttribute( constant( "PDO::ATTR_$val" ) )
	$sqltest="SHOW INDEX FROM `$tab` WHERE KEY_NAME = '$ind'";
	$res = $dbi->prepare("$sqltest");
	$res->execute();
		
	if($res->rowCount()) {
		if($ind=='PRIMARY')
			$sql="ALTER TABLE `$tab`  DROP PRIMARY KEY , $sql2 ";
		else
			$sql="ALTER TABLE `$tab` DROP INDEX `$ind`";
		try{
			$res = $dbi->prepare("$sql");
			$res->execute();
			$ret= 1;
			}
		catch(PDOException $e)
		{
			$ret=0;
			$ctrlerr=1;
			$strlog= "<br><span style=\"color: red;\">- Tabella: $tab - Indice: $ind - Aggiornamento Fallito: $sql</span>";
 			return $ret;
		}
	}
	if("$sql2"!="" and $ind!='PRIMARY'){
		try{
		  $res = $dbi->prepare("$sql2");
		  $res->execute();		
		  $ret=2;
		}	
		catch(PDOException $e)
		{
		  $ret=0;
		  $ctrlerr=1;
		  $strlog= "<br><span style=\"color: red;\">- Tabella: $tab - Indice: $ind - Aggiornamento Fallito: $sql2</span>";
   		  return $ret;
		}
	} 
	$strlog= "<br><span style=\"color: green;\">- Tabella: $tab - Indice: $ind - Index aggiornato</span>";
	return $ret;
}




function controllo($tabella,$campo,$num)
{
	global $dbi, $dbname;
	$sql="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '$tabella'";
	$res = $dbi->prepare("$sql");
	$res->execute();
	if(!$res->rowCount())
	{
		$strlog= "<br>$num) La tabella: $tabella non è presente nel database"; 
		return 0;
	}
	if($res->rowCount() and $campo=='') return 1;
	while(list($nome)=$res->fetch(PDO::FETCH_NUM)) {if($nome==$campo) { $strlog= "<br>".$num.") Il campo: $campo è presente nella tabella: $tabella"; return 1;}}
	if($campo) $strlog= "<br>$num) Il campo: $campo non è presente nella tabella: $tabella"; 
	return 0;
}

$num=0;		
if(controllo($prefix.'_authors','pwd',++$num))
{
	$sql="ALTER TABLE `soraldo_authors` CHANGE `pwd` `pwd` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL; ";
	$ret=aggiorna($sql,$dbi,'',$num);
	$strlog= "<br> La tabella dei permessi è stata aggiornata<br>";
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_authors non richiede questo aggiornamento</span><br>";	
if(controllo($prefix.'_authors','admincomune',++$num))
{
	$sql="UPDATE `".$prefix."_authors` SET `adminsuper` = '0',`admincomune` = '0' WHERE `".$prefix."_authors`.`aid` != 'admin' AND `".$prefix."_authors`.`adminsuper` != '1'";
	$ret=aggiorna($sql,$dbi,'',$num);
	$sql="UPDATE `".$prefix."_authors` SET `adminop` = '0', `adminsuper` = '0',`admincomune` = '1' WHERE `".$prefix."_authors`.`aid` = 'admin' and `".$prefix."_authors`.`adminsuper` != '1'";
	$ret=aggiorna($sql,$dbi,'',$num);
	$sql="UPDATE `".$prefix."_authors` SET `adminop` = '0', `adminsuper` = '1',`admincomune` = '0' WHERE `".$prefix."_authors`.`aid` = 'suser' or `".$prefix."_authors`.`adminsuper` = '1'";
	$ret=aggiorna($sql,$dbi,'',$num);
	$strlog= "<br> La tabella dei permessi è stata aggiornata<br>";
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_authors non richiede questo aggiornamento</span><br>";	

$sql="SELECT * from `".$prefix."_ele_widget` WHERE `soraldo_ele_widget`.`id` = 29";
$res = $dbi->prepare("$sql");
$res->execute();
if($res->rowCount()) {
	$sql="DELETE FROM ".$prefix."_ele_widget WHERE `soraldo_ele_widget`.`id` = 29";
	$res = $dbi->prepare("$sql");
	$res->execute();
	$strlog= "<br> Il record cookie_law.php è stato eliminato dalla tabella dei widget, usare privacy.php<br>";
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_widget non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_dashboard_layout','',++$num))
{
	$sql="CREATE TABLE $prefix.'_dashboard_layout (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  card_id VARCHAR(50) NOT NULL,
  posizione INT NOT NULL,
  visibile TINYINT(1) NOT NULL
);";
	$strlog= "<br>".$num.") Creazione tabella per dashboard personalizzata ";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ws_consultazione non richiede questo aggiornamento</span><br>";

# CREATE TABLE `".$prefix."_ws_stranieri` (`id_cons` INT(11) NOT NULL, `id_sez` INT(11) NOT NULL , `id_nazione` INT(11) NOT NULL, `numero` INT(11) NOT NULL DEFAULT '0');
# CREATE TABLE `".$prefix."_ws_nazioni` (`id_nazione` varchar(3) NOT NULL , `descrizione` VARCHAR(60) NOT NULL );
# Modifiche per gestione Webservices
if(!controllo($prefix.'_ws_consultazione','id_cons',++$num))
{
	$sql="CREATE TABLE `".$prefix."_ws_consultazione` (`id_cons` INT(11) NOT NULL , `codicews` INT(2) NOT NULL , `data` DATE NOT NULL , `descrizione` VARCHAR(60) NULL DEFAULT NULL";
	$strlog= "<br>".$num.") Creazione tabella per webservices: consultazione ";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ws_consultazione non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_ws_tipo','id_locale',++$num))
{
	$sql="CREATE TABLE `".$prefix."_ws_tipo` (`id_locale` INT(11) NOT NULL , `id_ws` INT(2) NOT NULL )";
	$sql2="INSERT INTO `soraldo_ws_tipo` (`id_locale`, `id_ws`) VALUES ('2', '9')";
	$strlog= "<br>".$num.") Creazione tabella per webservices: tipo  ";
	$ret=aggiorna($sql,$dbi,$sql2,$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ws_tipo non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ws_funzioni','id_cons',++$num))
{
	$sql="CREATE TABLE `".$prefix."_ws_funzioni` (`id_cons` INT(11) NOT NULL , `funzione` VARCHAR(40) NOT NULL , `stato` INT(2) NOT NULL )";
	$strlog= "<br>".$num.") Creazione tabella per webservices: funzioni  ";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ws_funzioni non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ws_comunicazione','id_cons',++$num))
{
	$sql="CREATE TABLE `".$prefix."_ws_comunicazione` (`id_comunicazione` INT(15) NOT NULL , `descrizione` VARCHAR(50) NOT NULL , `attiva` INT(1) NOT NULL DEFAULT '1' , `id_cons` INT(11) NOT NULL)";
	$strlog= "<br>".$num.") Creazione tabella per webservices: comunicazione  ";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ws_comunicazione non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ws_sezioni','id_cons',++$num))
{
	$sql="CREATE TABLE `".$prefix."_ws_sezioni` (`id_cons` INT(11) NOT NULL, `id_sez` INT(11) NOT NULL , `idsez_ws` INT(11) NOT NULL )";
	$strlog= "<br>".$num.") Creazione tabella per webservices: comunicazione  ";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ws_sezioni non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ele_comuni','id_ws',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_comuni` ADD `id_ws` VARCHAR(15) NULL AFTER `cap`";
	$strlog= "<br>".$num.") Aggiunta campo id_ws alla tabella ele_comuni per webservices";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_comune non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ele_cons_comune','proiezione',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_cons_comune` ADD `proiezione` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `disgiunto`";
	$ret=aggiorna($sql,$dbi,'',$num);
	$sql="update `".$prefix."_ele_cons_comune` set proiezione='1' where chiusa='1' and id_conf>0";
	$ret=aggiorna($sql,$dbi,'',$num);	
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_cons_comune non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_config','versione',++$num))
{
	$sql="alter table `".$prefix."_config` change column `Versione` `versione` int(3)";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_config non richiede questo aggiornamento</span><br>";
if(controllo($prefix.'_config','patch',++$num))
{
	$sql="ALTER TABLE `soraldo_config` CHANGE `patch` `patch` VARCHAR(60) NULL;";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_config non richiede questo aggiornamento</span><br>";	
if(controllo($prefix.'_config','secret',++$num))
{
	$sql="alter table `".$prefix."_config` DROP `secret`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_config non richiede questo aggiornamento</span><br>";
if(controllo($prefix.'_config','aggiornamento',++$num))
{
	$sql="alter table `".$prefix."_config` DROP `aggiornamento`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_config non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_config','tema_colore',++$num))
{
	$sql="ALTER TABLE `".$prefix."_config` ADD `tema_colore` varchar(50) DEFAULT 'default' AFTER `ed_user`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- Campo tema_colore già presente nella tabella ".$prefix."_config</span><br>";
if(!controllo($prefix.'_ele_conf','votolista',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_conf` ADD `votolista` enum('0', '1') NOT NULL DEFAULT '0' AFTER `supdisgiunto`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_conf non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_ele_conf','inffisso',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_conf` ADD `inffisso` enum('0', '1') NOT NULL DEFAULT '0' AFTER `votolista`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_conf non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_ele_conf','supfisso',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_conf` ADD `supfisso` enum('0', '1') NOT NULL DEFAULT '0' AFTER `inffisso`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_conf non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_ele_conf','fascia_capoluogo',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_conf` ADD `fascia_capoluogo` int(2) NOT NULL DEFAULT '0' AFTER `supfisso`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_conf non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_ele_consultazione','link_trasparenza',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_consultazione` ADD `link_trasparenza` VARCHAR(255) NULL AFTER `tipo_cons`"; 
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- Campo link_trasparenza presente. La tabella ".$prefix."_ele_consultazione non richiede questo aggiornamento</span><br>";
if(controllo($prefix.'_ele_sede','indirizzo',++$num))
{
	$sql="ALTER TABLE `soraldo_ele_sede` CHANGE `indirizzo` `indirizzo` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'NULL'"; 
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- Campo indirizzo aggiornato.</span><br>";
if(!controllo($prefix.'_ele_sede','latitudine',++$num))
{
	$sql="ALTER TABLE `soraldo_ele_sede` ADD `latitudine` VARCHAR(20) NULL AFTER `filemappa`, ADD `longitudine` VARCHAR(20) NULL AFTER `latitudine`"; 
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- Campo latitudine già presente. La tabella ".$prefix."_ele_sede non richiede questo aggiornamento</span><br>";
if(controllo($prefix.'_ele_sezioni','bianchi_lista',++$num))
{
	$sql="alter table `".$prefix."_ele_sezioni` DROP `bianchi_lista`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_sezioni non richiede questo aggiornamento</span><br>";
if(controllo($prefix.'_ele_sezioni','nulli_lista',++$num))
{
	$sql="alter table `".$prefix."_ele_sezioni` DROP `nulli_lista`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_sezioni non richiede questo aggiornamento</span><br>";
if(controllo($prefix.'_ele_voti_parziale','data',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_voti_parziale` CHANGE `data` `data` DATE NOT NULL DEFAULT '1900-01-01'";
	$ret=aggiorna($sql,$dbi,'',$num);
	$strlog= "<br>";
}	
++$num;
$sql="ALTER TABLE `".$prefix."_ele_come` CHANGE `title` `title` VARCHAR(150) NOT NULL DEFAULT ' ', CHANGE `preamble` `preamble` TEXT, CHANGE `content` `content` TEXT, CHANGE `editimage` `editimage` VARCHAR(100) NOT NULL DEFAULT ' '"; 
$ret=aggiorna($sql,$dbi,'',$num);
$strlog= "<br>";
flush(); ob_flush();
if(!$ret) $strlog= "Il tuo sistema non necessita di questo aggiornamento, questo avviso di errore va ignorato<br>";
++$num;
$sql="ALTER TABLE `".$prefix."_ele_link` CHANGE `title` `title` VARCHAR(150) NOT NULL DEFAULT ' ', CHANGE `preamble` `preamble` TEXT, CHANGE `content` `content` TEXT, CHANGE `editimage` `editimage` VARCHAR(100) NOT NULL DEFAULT ' '"; 
$ret=aggiorna($sql,$dbi,'',$num);
$strlog= "<br>";
flush(); ob_flush();
if(!$ret) $strlog= "Il tuo sistema non necessita di questo aggiornamento, questo avviso di errore va ignorato<br>";
++$num;
$sql="ALTER TABLE `".$prefix."_ele_servizi` CHANGE `title` `title` VARCHAR(150) NOT NULL DEFAULT ' ', CHANGE `preamble` `preamble` TEXT, CHANGE `content` `content` TEXT, CHANGE `editimage` `editimage` VARCHAR(100) NOT NULL DEFAULT ' '"; 
$ret=aggiorna($sql,$dbi,'',$num);
$strlog= "<br>";
flush(); ob_flush();
if(!$ret) $strlog= "Il tuo sistema non necessita di questo aggiornamento, questo avviso di errore va ignorato<br>";

if(controllo($prefix.'_ele_rilaff','data',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_rilaff` CHANGE `data` `data` DATE NOT NULL DEFAULT '1900-01-01'";
	$ret=aggiorna($sql,$dbi,'',$num);
	$strlog= "<br>";
}	

if(!controllo($prefix.'_ele_gruppo','num_circ',++$num))
{	
	$sql="ALTER TABLE `".$prefix."_ele_gruppo` ADD `num_circ` INT(2) UNSIGNED NOT NULL AFTER `id_circ`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_gruppo non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ele_gruppo','cv',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_gruppo` ADD `cv` VARCHAR(255) NULL AFTER `programma`, ADD `cg` VARCHAR(255) NULL AFTER `cv`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_gruppo non richiede questo aggiornamento</span><br>"; 
 
if(!controllo($prefix.'_ele_gruppo','eletto',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_gruppo` ADD `eletto` TINYINT NOT NULL DEFAULT '0' AFTER `cg`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- Campo 'eletto' gia' presente nella tabella ".$prefix."_ele_gruppo, aggiornamento non necessario</span><br>"; 
 
if(!controllo($prefix.'_ele_gruppo','id_colore',++$num))
{
	$sql="ALTER TABLE `soraldo_ele_gruppo` ADD `id_colore` INT(11) NULL DEFAULT NULL AFTER `eletto`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- Campo 'id_colore' gia' presente nella tabella ".$prefix."_ele_gruppo, aggiornamento non necessario</span><br>"; 

if(!controllo($prefix.'_ele_voti_gruppo','num_gruppo',++$num))
{	
	$sql="ALTER TABLE `".$prefix."_ele_voti_gruppo` ADD `num_gruppo` INT(2) UNSIGNED NOT NULL AFTER `id_sez`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_gruppo non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ele_lista','num_gruppo',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_lista` ADD `num_gruppo` INT(2) UNSIGNED NOT NULL AFTER `id_gruppo`";
	$sql2="update `".$prefix."_ele_lista` as t1 set t1.num_gruppo=(select t2.num_gruppo from `".$prefix."_ele_gruppo` as t2 where t2.id_gruppo=t1.id_gruppo) where t1.num_gruppo=0 and (select t2.num_gruppo from `".$prefix."_ele_gruppo` as t2 where t2.id_gruppo=t1.id_gruppo) is not null";
	$ret=aggiorna($sql,$dbi,$sql2,$num);
}else{ 
	$sql="update `".$prefix."_ele_lista` as t1 set t1.num_gruppo=(select t2.num_gruppo from `".$prefix."_ele_gruppo` as t2 where t2.id_gruppo=t1.id_gruppo) where t1.num_gruppo=0 and (select t2.num_gruppo from `".$prefix."_ele_gruppo` as t2 where t2.id_gruppo=t1.id_gruppo) is not null";
	$ret=aggiorna($sql,$dbi,'',$num);
}
$strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_lista è stata aggiornata</span><br>";
flush(); ob_flush();
if(!controllo($prefix.'_ele_lista','num_circ',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_lista` ADD `num_circ` INT(2) UNSIGNED NOT NULL AFTER `id_circ`";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_lista non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_ele_lista','link_trasparenza',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_lista` ADD `link_trasparenza` VARCHAR(255) NULL AFTER `stemma`";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_lista non richiede l'aggiunta del campo link_trasparenza</span><br>";
if(!controllo($prefix.'_ele_operatori','id_circ',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_operatori` ADD `id_circ` INT(11) NOT NULL  DEFAULT 0 AFTER `aid`";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_operatori non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ele_operatori','id_sez',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_operatori` ADD `id_sez` INT(11) NOT NULL  DEFAULT 0 AFTER `id_circ`";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_operatori non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ele_temi','id',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_temi` ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`)";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_operatori non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ele_voti_lista','num_lista',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_voti_lista` ADD `num_lista` INT(2) UNSIGNED NOT NULL AFTER `id_sez`";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_voti_lista non richiede questo aggiornamento</span><br>";

if(!controllo($prefix.'_ele_voti_ref','num_gruppo',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_voti_ref` ADD `num_gruppo` INT(2) UNSIGNED NOT NULL AFTER `id_sez`";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_voti_ref non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_ele_candidati','cv',++$num))
{
	$sql="ALTER TABLE `soraldo_ele_candidati` ADD `cv` VARCHAR(255) NULL AFTER `num_cand`, ADD `cg` VARCHAR(255) NULL AFTER `cv`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_candidati non richiede questo aggiornamento</span><br>"; 
if(controllo($prefix.'_ele_candidati','Sesso',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_candidati` DROP `Sesso`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_candidati non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_ele_voti_candidati','num_cand',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_voti_candidati` ADD `num_cand` INT(2) UNSIGNED NOT NULL AFTER `id_sez`";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_voti_candidati non richiede questo aggiornamento</span><br>";
if(!controllo($prefix.'_ele_candidati','eletto',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_candidati` ADD `eletto` TINYINT NOT NULL DEFAULT '0' AFTER `cg`";
	$ret=aggiorna($sql,$dbi,'',$num);
}	else $strlog= "<br><span style=\"color: green;\">- Campo 'eletto' già presente nella tabella ".$prefix."_candidati, non è richiesto questo aggiornamento</span><br>"; 

/*if(controllo($prefix.'_ele_candidati','num_lista',++$num))
{
	$sql="update `".$prefix."_ele_candidati` as t3 set t3.num_lista=(select t2.num_lista from `".$prefix."_ele_lista` as t2 left join `".$prefix."_ele_candidati` as t1 on t2.id_lista=t1.id_lista where t2.id_lista=t3.id_lista) where t3.num_lista=0";
	$ret=aggiorna($sql,$dbi,'',$num);
	$strlog= "<br>Aggiornata la tabella ".$prefix."_ele_candidati";
}*/
if(!controllo($prefix.'_ele_candidati','num_lista',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_candidati` ADD `num_lista` INT(2) UNSIGNED NOT NULL AFTER `id_lista`";
	$sql2="update `".$prefix."_ele_candidati` as t1 set t1.num_lista=(select t2.num_lista from `".$prefix."_ele_lista` as t2 where t2.id_lista=t1.id_lista) where (select t2.num_lista from `".$prefix."_ele_lista` as t2 where t2.id_lista=t1.id_lista) is not null";
	$ret=aggiorna($sql,$dbi,$sql2,$num);
}else{
	$sql="update `".$prefix."_ele_candidati` as t1 set t1.num_lista=(select t2.num_lista from `".$prefix."_ele_lista` as t2 where t2.id_lista=t1.id_lista) where (select t2.num_lista from `".$prefix."_ele_lista` as t2 where t2.id_lista=t1.id_lista) is not null";
	$ret=aggiorna($sql,$dbi,'',$num);
	}
$strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_candidati è stata aggiornata</span><br>";
flush(); ob_flush();

if(!controllo($prefix.'_ele_sezioni','colore',++$num))
{
	$sql="ALTER TABLE `".$prefix."_ele_sezioni` ADD `colore` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '#FAFAD2' AFTER `solo_lista`";
	$ret=aggiorna($sql,$dbi,'',$num);
}else{
	$sql="ALTER TABLE `".$prefix."_ele_sezioni` CHANGE `colore` `colore` VARCHAR(50) DEFAULT '#FAFAD2'";
	$ret=aggiorna($sql,$dbi,'',$num);
}	$strlog= "<br>";

if(!controllo($prefix.'_ele_sede','id_ubicazione',++$num))
{
	$sql="ALTER TABLE `soraldo_ele_sede` ADD `id_ubicazione` INT(10) NULL DEFAULT NULL;";
	$ret=aggiorna($sql,$dbi,'',$num);
}

if(!controllo($prefix.'_ele_sede','id_ubicazione',++$num))
{
	$sql="ALTER TABLE `soraldo_ele_sede` ADD `ospedaliera` INT(2) NULL DEFAULT NULL;";
	$ret=aggiorna($sql,$dbi,'',$num);
}
 
$strlog= "<br>Aggiornamento per nuovo sistema dei controlli di congruità";
flush(); ob_flush();

if(!controllo($prefix.'_ele_controlli','id_cons',++$num))
{
	$sql="CREATE TABLE if not exists`".$prefix."_ele_controlli` ( `id_cons` INT(11) NOT NULL , `id_sez` INT(11) NOT NULL , `tipo` VARCHAR(10) NOT NULL , `id` INT(11) NOT NULL , INDEX `sezione` (`id_sez`)) ENGINE = MyISAM";
	$strlog= "<br>".$num.") Creazione tabella dei controlli: ";
	$ret=aggiorna($sql,$dbi,'',$num);
}else $strlog= "<br><span style=\"color: green;\">- La tabella ".$prefix."_ele_controlli non richiede questo aggiornamento</span><br>";

$strlog= "<br>".++$num.") Eliminazione della vecchia tabella dei controlli: ";
flush(); ob_flush();
if(controllo($prefix.'_ele_controllosez','',$num))
{
$sql="DROP TABLE if exists `".$prefix."_ele_controllosez`";
$ret=aggiorna($sql,$dbi,'',$num);
} else $strlog= "<br><span style=\"color: green;\">- Tabella non presente</span><br>";

$strlog= "<br>".++$num.") Aggiornamento tabella ".$prefix."_ele_voti_gruppo";
flush(); ob_flush();
$sql="update `".$prefix."_ele_voti_gruppo` as t1 left join `".$prefix."_ele_gruppo` as t2 on t1.id_gruppo=t2.id_gruppo set t1.num_gruppo=t2.num_gruppo;";
$ret=aggiorna($sql,$dbi,'',$num);

$strlog= "<br>".++$num.") Aggiornamento tabella ".$prefix."_ele_conf";
flush(); ob_flush();
$sql="update `".$prefix."_ele_conf` SET `supdisgiunto` = '1' WHERE `soraldo_ele_conf`.`id_conf` = 7;";
$ret=aggiorna($sql,$dbi,'',$num);

$strlog= "<br><br>".++$num.") Aggiornamento dei valori di default: ".$prefix."_ele_voti_lista";
flush(); ob_flush();
$sql="ALTER TABLE `".$prefix."_ele_voti_lista` CHANGE `num_lista` `num_lista` INT(2) UNSIGNED NULL DEFAULT '0';";
$ret=aggiorna($sql,$dbi,'',$num);

$strlog= "<br><br>".++$num.") Aggiornamento tabella ".$prefix."_ele_voti_lista";
flush(); ob_flush();
$sql="update `".$prefix."_ele_voti_lista` as t1 left join `".$prefix."_ele_lista` as t2 on t1.id_lista=t2.id_lista set t1.num_lista=t2.num_lista;";
$ret=aggiorna($sql,$dbi,'',$num);
##############################################

$strlog= "<br><br>".++$num.") Aggiornamento dei valori di default: ".$prefix."_ele_gruppo";
flush(); ob_flush();
$sql="ALTER TABLE `".$prefix."_ele_gruppo` CHANGE `num_circ` `num_circ` INT(2) UNSIGNED NOT NULL DEFAULT '1';";
$ret=aggiorna($sql,$dbi,'',$num);

$sql="ALTER TABLE `".$prefix."_authors` CHANGE `adminsuper` `adminsuper` TINYINT(2) NOT NULL DEFAULT '0';";
$ret=aggiorna($sql,$dbi,'',$num);

$sql="ALTER TABLE `".$prefix."_ele_lista` CHANGE `num_gruppo` `num_gruppo` INT(2) UNSIGNED NOT NULL DEFAULT '0';";
$ret=aggiorna($sql,$dbi,'',$num);

$sql="ALTER TABLE `".$prefix."_ele_lista` CHANGE `num_circ` `num_circ` INT(2) UNSIGNED NOT NULL DEFAULT '1';";
$ret=aggiorna($sql,$dbi,'',$num);

$strlog= "<br><br>".++$num.") Aggiornamento della tabella _ele_conf per la nuova gestione della L.R. Sicilia";
flush(); ob_flush();
$sql="UPDATE `".$prefix."_ele_conf` SET `inffisso` = '1' WHERE `id_conf` = 4;";
$ret=aggiorna($sql,$dbi,'',$num);

$strlog= "<br><br>".++$num.") Aggiornamento della tabella _ele_voti_parziale - campo id_gruppo";
flush(); ob_flush();
$sql="update ".$prefix."_ele_voti_parziale set id_gruppo=0 where id_cons in(select t1.id_cons from ".$prefix."_ele_cons_comune as t1 left join ".$prefix."_ele_consultazione as t2 on t1.id_cons_gen=t2.id_cons_gen left join ".$prefix."_ele_tipo as t3 on t2.tipo_cons=t3.tipo_cons where t3.genere>0);";
$ret=aggiorna($sql,$dbi,'',$num);

$strlog= "<br><br>".++$num.") Aggiornamento del campo numero candidato della tabella _ele_voti_candidati per le consultazioni precedenti all'aggiunta del campo stesso";
flush(); ob_flush();
$sql="update `".$prefix."_ele_voti_candidati` as t1 left join `".$prefix."_ele_candidati` as t2 on t1.id_cand=t2.id_cand set t1.num_cand=t2.num_cand where t1.num_cand=0 and t2.num_cand>0;";
$ret=aggiorna($sql,$dbi,'',$num);

$strlog= "<br><br>".++$num.") Ricostruzione della tabella ".$prefix."_ele_fasce`";
flush(); ob_flush();
$sql="DROP TABLE `".$prefix."_ele_fasce`";
$ret=aggiorna($sql,$dbi,'',$num);

$sql="CREATE TABLE `".$prefix."_ele_fasce` (
  `id_fascia` int(2) NOT NULL,
  `abitanti` int(11) NOT NULL,
  `seggi` int(4) NOT NULL,
  `id_conf` int(11) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET 'utf8';";


$sql2="INSERT INTO `".$prefix."_ele_fasce` (`id_fascia`, `abitanti`, `seggi`, `id_conf`) VALUES
(1, 3000, 12, 1),
(2, 10000, 16, 1),
(3, 15000, 20, 1),
(4, 30000, 20, 1),
(5, 100000, 30, 1),
(6, 250000, 40, 1),
(7, 500000, 46, 1),
(8, 1000000, 50, 1),
(9, 100000000, 60, 1),
(1, 3000, 12, 2),
(2, 10000, 16, 2),
(3, 15000, 20, 2),
(4, 30000, 20, 2),
(5, 100000, 30, 2),
(6, 250000, 40, 2),
(7, 500000, 46, 2),
(8, 1000000, 50, 2),
(9, 100000000, 60, 2),
(1, 3000, 9, 3),
(2, 5000, 9, 3),
(4, 15000, 16, 3),
(3, 10000, 12, 3),
(5, 30000, 16, 3),
(6, 100000, 24, 3),
(7, 250000, 32, 3),
(8, 500000, 36, 3),
(9, 1000000, 40, 3),
(10, 100000000, 48, 3),
(1, 3000, 6, 4),
(2, 5000, 7, 4),
(3, 10000, 12, 4),
(4, 15000, 16, 4),
(5, 30000, 16, 4),
(6, 100000, 24, 4),
(7, 250000, 32, 4),
(8, 500000, 36, 4),
(9, 1000000, 40, 4),
(10, 100000000, 48, 4),
(1, 3000, 9, 5),
(2, 5000, 9, 5),
(3, 10000, 12, 5),
(4, 15000, 16, 5),
(5, 30000, 16, 5),
(6, 100000, 24, 5),
(7, 250000, 32, 5),
(8, 500000, 36, 5),
(9, 1000000, 40, 5),
(10, 100000000, 48, 5),
(1, 3000, 6, 6),
(2, 5000, 7, 6),
(3, 10000, 10, 6),
(4, 15000, 16, 6),
(5, 30000, 16, 6),
(6, 100000, 24, 6),
(7, 250000, 32, 6),
(8, 500000, 36, 6),
(9, 1000000, 40, 6),
(10, 100000000, 48, 6),
(1, 3000, 10, 7),
(2, 10000, 12, 7),
(3, 15000, 16, 7),
(4, 30000, 16, 7),
(5, 100000, 24, 7),
(6, 250000, 32, 7),
(7, 500000, 36, 7),
(8, 1000000, 40, 7),
(9, 100000000, 48, 7);";
$ret=aggiorna($sql,$dbi,$sql2,$num);
#if(!$ret) $strlog= "<br>".$num++.") Fallito: $sql"; else $strlog= "<br>".$num++.") Aggiornato<br>";

$sql="ALTER TABLE `".$prefix."_ele_fasce`
  ADD KEY `id_fascia` (`id_fascia`);";
$ret=aggiorna($sql,$dbi,'',$num);
#if(!$ret) $strlog= "<br>".$num++.") Fallito: $sql"; else $strlog= "<br>".$num++.") Aggiornato<br>";

$strlog= "<br><br>".++$num.") Ricostruzione e aggioramento indici";
flush(); ob_flush();

$tab=$prefix."_ws_comunicazione";
$ind="progressivo";
$sql2="ALTER TABLE `".$prefix."_ws_comunicazione` ADD UNIQUE `progressivo` (`id_cons`, `id_comunicazione`)";
$ret=aggiorna_index($tab, $ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_voti_ref";
$ind="id_cons";
$sql2="ALTER TABLE `".$prefix."_ele_voti_ref` ADD INDEX `id_cons` (`id_cons`, `id_gruppo`) USING BTREE";
$ret=aggiorna_index($tab, $ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_sezioni";
$ind="id_cons";
$sql2="ALTER TABLE `".$prefix."_ele_sezioni` ADD UNIQUE `id_cons` (`id_cons`, `num_sez`) USING BTREE"; 
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_voti_lista";
$ind="id_cons";
$sql2="ALTER TABLE `".$prefix."_ele_voti_lista` ADD INDEX `id_cons` (`id_cons`, `id_sez`, `id_lista`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_voti_gruppo";
$ind="id_cons";
#$sql="ALTER TABLE `".$prefix."_ele_voti_gruppo` DROP INDEX if exists `id_cons`";
$sql2="ALTER TABLE `".$prefix."_ele_voti_gruppo` ADD INDEX `id_cons` (`id_cons`, `id_sez`, `id_gruppo`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_voti_candidati";
$ind="id_cons";
#$sql="ALTER TABLE `".$prefix."_ele_voti_candidati` DROP INDEX if exists `id_cons`";
$sql2="ALTER TABLE `".$prefix."_ele_voti_candidati` ADD INDEX `id_cons` (`id_cons`, `id_sez`, `id_cand`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_lista";
$ind="id_cons";
#$sql="ALTER TABLE `".$prefix."_ele_lista` DROP INDEX if exists `id_cons`";
$sql2="ALTER TABLE `".$prefix."_ele_lista` ADD INDEX `id_cons` (`id_cons`, `id_gruppo`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_lista";
$ind="PRIMARY";
#$sql="ALTER TABLE `".$prefix."_ele_lista` DROP INDEX if exists `PRIMARY`";
$sql2="ADD PRIMARY KEY (`id_lista`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_gruppo";
$ind="id_cons";
#$sql="ALTER TABLE `".$prefix."_ele_gruppo` DROP INDEX if exists `id_cons`";
$sql2="ALTER TABLE `".$prefix."_ele_gruppo` ADD INDEX `id_cons` (`id_cons`, `id_circ`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_gruppo";
$ind="id_gruppo";
#$sql="ALTER TABLE `".$prefix."_ele_gruppo` DROP INDEX if exists `id_cons`";
$sql2="ALTER TABLE `".$prefix."_ele_gruppo` ADD UNIQUE `id_gruppo` (`id_cons`, `id_gruppo`, `eletto`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_gruppo";
$ind="PRIMARY";
#$sql="ALTER TABLE `".$prefix."_ele_gruppo` DROP INDEX if exists `PRIMARY`";
$sql2="ADD PRIMARY KEY (`id_gruppo`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_candidati";
$ind="id_cand";
$sql2="ALTER TABLE `".$prefix."_ele_candidati` ADD UNIQUE `id_cand` (`id_cons`, `id_cand`, `eletto`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_candidati";
$ind="id_cons";
#$sql="ALTER TABLE `".$prefix."_ele_candidati` DROP INDEX if exists `id_cons`";
$sql2="ALTER TABLE `".$prefix."_ele_candidati` ADD INDEX `id_cons` (`id_cons`, `num_lista`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_candidati";
$ind="PRIMARY";
#$sql="ALTER TABLE `".$prefix."_ele_candidati` DROP INDEX if exists `PRIMARY`";
$sql2="ADD PRIMARY KEY (`id_cand`) USING BTREE";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

$tab=$prefix."_ele_consultazione";
$ind="descrizione";
#$sql="ALTER TABLE `".$prefix."_ele_consultazione` DROP INDEX if exists `descrizione`";
$sql2="ALTER TABLE `".$prefix."_ele_consultazione` ADD UNIQUE `descrizione` (`descrizione`(100))";
$ret=aggiorna_index($tab,$ind,$dbi,$sql2,$num);

########################
$sql= "RENAME TABLE `soraldo_ele_candidati` TO `soraldo_ele_candidato`, `soraldo_ele_collegi` TO `soraldo_ele_collegio`, `soraldo_ele_comuni` TO `soraldo_ele_comune`, `soraldo_ele_comu_collegi` TO `soraldo_ele_comu_collegio`, `soraldo_ele_controlli` TO `soraldo_ele_controllo`, `soraldo_ele_documenti` TO `soraldo_ele_documento`, `soraldo_ele_fasce` TO `soraldo_ele_fascia`, `soraldo_ele_modelli` TO `soraldo_ele_modello`, `soraldo_ele_numeri` TO `soraldo_ele_numero`, `soraldo_ele_operatori` TO `soraldo_ele_operatore`, `soraldo_ele_province` TO `soraldo_ele_provincia`, `soraldo_ele_regioni` TO `soraldo_ele_regione`, `soraldo_ele_servizi` TO `soraldo_ele_servizio`, `soraldo_ele_sezioni` TO `soraldo_ele_sezione`, `soraldo_ele_temi` TO `soraldo_ele_tema`, `soraldo_ele_voti_candidati` TO `soraldo_ele_voti_candidato`, `soraldo_ws_funzioni` TO `soraldo_ws_funzione`, `soraldo_ws_sezioni` TO `soraldo_ws_sezione`";
try{
	$res = $dbi->prepare("$sql");
	$res->execute();
}
catch(PDOException $e)
{
	die( $sql . "<br>" . $e->getMessage() . "<br>" . $strlog);
} 


	$sql="update `".$prefix."_config` set versione='4', patch='1'";
	$ret=aggiorna($sql,$dbi,'',$num);

#######################

$strlog= "<br><br>".++$num.") Modifica Charset del database";
flush(); ob_flush();
if($newcs=='utf8') {
	$cset='utf8';
	$ccollate='utf8_general_ci';
	$preset='latin1';
}else{
	$cset='latin1';
	$ccollate='latin1_swedish_ci';
	$preset='utf8';
}
#$sql="ALTER DATABASE $dbname CHARACTER SET '$cset' COLLATE '$ccollate'";
#$res = $dbi->prepare("$sql");
#$res->execute();

$sql="SELECT table_name,column_name,column_default,column_type,is_nullable FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbname' and (character_set_name='$preset' or collation_name like '$preset%')";
$res = $dbi->prepare("$sql");
$res->execute();
$tab='';
$agg=$res->rowCount();

while(list($nometab,$campo,$def,$tipo,$nul)=$res->fetch(PDO::FETCH_NUM)) {
	if($tab!=$nometab){
		$sql="alter table $nometab DEFAULT CHARSET=$cset COLLATE $ccollate";
		try{
			$res2 = $dbi->prepare("$sql");
			$res2->execute();
        }
		catch(PDOException $e)
		{
			die( $sql . "<br>" . $e->getMessage());
		} 
		$tab=$nometab; $strlog= "<br><span style=\"color: green;\">- Tabella: $nometab</span>";
	}
	if($def!='') $default="DEFAULT '$def'"; else $default='';
	if($nul=='NO') $nullable='NOT NULL'; else $nullable='NULL';
	$sql="ALTER TABLE $nometab CHANGE $campo $campo $tipo CHARACTER SET '$cset' COLLATE '$ccollate' $nullable $default;"; 	
	try{
		$res2 = $dbi->prepare("$sql");
		$res2->execute();
	}
	catch(PDOException $e)
	{
		$default="DEFAULT $def";
		$sql="ALTER TABLE $nometab CHANGE $campo $campo $tipo CHARACTER SET '$cset' COLLATE '$ccollate' $nullable $default;"; 	
		try{
				$res2 = $dbi->prepare("$sql");
				$res2->execute();
			}
			catch(PDOException $e)
			{
				die( $sql . "<br>" . $e->getMessage());
			} 
	}
	$strlog= "<br><span style=\"color: green;\">-- $campo</span>";

}
 $sql="SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE TABLE_SCHEMA = '$dbname' and table_collation like '$preset%'";
 $res = $dbi->prepare("$sql");
 $res->execute();
$tab='';
if(!$agg) $agg=$res->rowCount();
while(list($nometab)=$res->fetch(PDO::FETCH_NUM)) {
		$sql="alter table $nometab DEFAULT CHARSET=$cset COLLATE $ccollate";
		try{
			$res2 = $dbi->prepare("$sql");
			$res2->execute();
        }
		catch(PDOException $e)
		{
			die( $sql . "<br>" . $e->getMessage());
		} 
		$strlog= "<br><span style=\"color: green;\">- Tabella: $nometab</span>";
 }
if(!$agg) $strlog= "<br><span style=\"color: green;\">- Nessuna tabella da aggiornare</span>";
$strlog= "<br><br>";
flush(); ob_flush();
?>