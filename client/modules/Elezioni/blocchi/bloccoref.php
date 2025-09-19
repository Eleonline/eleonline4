<?php
# blocco referendum


global $genere,$id_cons_gen,$id_comune,$id_circ;

if ($genere==0){
echo "
<h5>Referendum</h5><p>
 		<b><a href=\"modules.php?name=Elezioni&amp;op=gruppo&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune\">
		Quesito Referendario</a></b><br />
		<b><a href=\"modules.php?name=Elezioni&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;op=affluenze_graf\">Affluenze</a></b><br /></p>";
}
?>
