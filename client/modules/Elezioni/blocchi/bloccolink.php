<?php
# blocco link

global $genere,$id_cons_gen,$id_cons,$id_comune,$prefix,$dbi;
	$sql="select mid, title, preamble, content,editimage from ".$prefix."_ele_link where id_cons='$id_cons' order by mid ";
	$res = $dbi->prepare("$sql");
	$res->execute();

    if ($res->rowCount() == 0) {
	return;
    } else {
	echo "<h5>"._LINK."</h5><p>";
	while (list($mid, $title, $preamble,$content,  $editimage) = $res->fetch(PDO::FETCH_NUM)) {
  		if ($title != "" && $content != "") {
			$content = stripslashes($content);
    			$content = substr($content,0,45);
			echo "<b><a href=\"$preamble\">$title</a></b><br />
			$content";
		}		     
	}
	
   }	


?>

