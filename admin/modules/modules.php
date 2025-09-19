<?php


define('APP_RUNNING', true);
require_once '../access.php';
# Inserimento accesso al db

if (file_exists("../config/config.php")){ 
	$install="0"; @require_once("../config/config.php"); 
}else{ 
	$install="1";
}

# verifica se effettuata la configurazione
if(empty($dbname) || $install=="1") {
    die("<html><body><div style=\"text-align:center\"><br /><br /><img src=\"modules/Elezioni/images/logo.jpg\" alt=\"Eleonline\" title=\"Eleonline\"><br /><br /><strong>Sembra che <a href='http://www.eleonline.it' title='Eleonline'>Eleonline</a> non sia stato ancora installato.<br /><br />Puoi procedere <a href='../install/index.php'>cliccando qui</a> per iniziare l'installazione</strong></div></body></html>");
}
require_once('../config/variabili.php');
$dsn = "mysql:host=$dbhost";
$opt = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false);
if($prefix == '') {
	db_err ('stepBack','Non avete indicato il prefisso tabelle database.');
}
try 
{
	$dbi = new PDO($dsn, $dbuname, $dbpass, $opt);
}
catch(PDOException $e)
{
	echo "<br>" . $e->getMessage();die();
}
$sql = "use $dbname";
try
{
	$dbi->exec($sql);
}
catch(PDOException $e)
{
	echo $sql . "<br>" . $e->getMessage();
}                                                                                       	
$sth = $dbi->prepare("SET SESSION character_set_connection = 'utf8' ");
$sth->execute();
$sth = $dbi->prepare("SET SESSION character_set_client = 'utf8' ");
$sth->execute();
$sth = $dbi->prepare("SET SESSION character_set_database = 'utf8' ");
$sth->execute();
$sth = $dbi->prepare("SET CHARACTER SET utf8");
$sth->execute();

$sth = $dbi->prepare("SET NAMES 'utf8'");
$sth->execute();

ob_start(); // attiva output buffering
include '../includes/header.php'; 
include '../includes/menu.php'; 
include 'contenuto.php'; 
include '../includes/footer.php'; 

ob_end_flush();?>
