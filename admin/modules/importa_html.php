<?php

	echo "<form name=\"importa\" enctype=\"multipart/form-data\" method=\"post\" action=\"modules.php\" >"
	."<input type=\"hidden\" name=\"op\" value=\"21\">";
	echo "<input type=\"hidden\" name=\"id_cons_gen\" value=\"$id_cons_gen\">";
	echo "<input type=\"hidden\" name=\"id_comune\" value=\"$id_comune\">";
	echo "<table cellspacing=\"0\" cellpadding=\"2\" border=\"1\"><tr class=\"bggray\"><td colspan=\"2\" align=\"center\">"._SEL_DATA_FILE2."</td></tr><tr><td><input name=\"datafile\" type=\"file\"></td>";
	echo "<td align=\"center\"><input type=\"submit\" name=\"add\" value=\""._OK."\"></td></tr></table></form>";

?>
