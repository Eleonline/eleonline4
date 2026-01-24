<?php

include("config.php");
	try{
	$dbi = new PDO("mysql:host=$dbhost;charset=utf8", $dbuname, $dbpass, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION)); 
     	$sql = "use $dbname";
    	$dbi->exec($sql);
        }
	catch(PDOException $e)
	{
	    echo $sql . "<br>" . $e->getMessage();
	}             
	$sth = $dbi->prepare("SET NAMES 'utf8'");
	$sth->execute();


$fase=intval($_GET['fase']);


if ($fase=='1'){
	$sql="SELECT id_cons_gen,descrizione from ".$prefix."_ele_consultazione order by descrizione";
	$res = $dbi->prepare("$sql");
	$res->execute();

Header("content-type: application/x-javascript; charset=utf-8");
echo "document.write(\"<b><select name=\'id_cons_gen2\'>";

while(list($id_cons_gen2,$descr) = $res->fetch(PDO::FETCH_NUM)) {
			echo "<option value=\'$id_cons_gen2\'>".htmlentities($descr)."</option>";
		}
		echo "</select>";
	$res = $dbi->prepare("$sql");
	$res->execute();
while(list($id_cons_gen2,$descr) = $res->fetch(PDO::FETCH_NUM)) {
			echo "<input type=\'hidden\' name=\'$id_cons_gen2\' value=\'$descr\'>";
		}
echo "</b>\")";

}elseif ($fase=='2'){

    $id_cons_gen2=intval($_GET['id_cons_gen2']);

    $sql="SELECT t2.id_comune,t2.descrizione 
          from ".$prefix."_ele_cons_comune as t1 
          left join ".$prefix."_ele_comune as t2 
          on t1.id_comune=t2.id_comune 
          where t1.id_cons_gen=$id_cons_gen2 
          order by t2.descrizione";

    $res = $dbi->prepare($sql);
    $res->execute();

    Header("content-type: application/x-javascript; charset=utf-8");

    echo "document.write(\"<b>
          <input type=\'hidden\' name=\'id_cons_gen2\' value=\'$id_cons_gen2\'>
          <select name=\'id_comune2\'>";

    while(list($id_comune2,$descr) = $res->fetch(PDO::FETCH_NUM)) {
        echo "<option value=\'$id_comune2\'>".htmlentities($descr)."</option>";
    }

    echo "</select>";

    // 🔥 AGGIUNTA CHIAVE
    $res = $dbi->prepare($sql);
    $res->execute();

    while(list($id_comune2,$descr) = $res->fetch(PDO::FETCH_NUM)) {
        echo "<input type=\'hidden\' name=\'$id_comune2\' value=\'$descr\'>";
    }

    echo "</b>\")";
}
?>
