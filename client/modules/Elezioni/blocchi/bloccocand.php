<?php
global $genere,$id_cons_gen,$id_comune,$id_circ;
if ($genere>2){
echo "

<h5> Candidati e Liste </h5><p>
 		<b><a href=\"modules.php?name=Elezioni&amp;op=gruppo&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=$id_circ\">Lista</a></b><br />
		Liste e candidati<br />
		<b><a href=\"modules.php?name=Elezioni&amp;op=candi&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune\">Candidati</a></b><br />
		Tutti i candidati<br /></p>
";
}
?>
