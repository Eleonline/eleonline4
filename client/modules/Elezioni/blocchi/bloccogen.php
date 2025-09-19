<?php
/* blocco generale */
global $genere,$id_cons_gen,$id_comune;

echo " 
      <h5> 
        Notizie Utili </h5>
        
            <h6><a href=\"modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=come\">Come si vota</a></h6>
	    

	    <h6><a href=\"modules.php?name=Elezioni&amp;op=circo&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune\">Dove si vota</a></h6>
	    
            
	    <h6><a href=\"modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=servizi\">Servizi Elettorali</a></h6>
            <h6><a href=\"modules.php?name=Elezioni&amp;op=come&amp;file=index&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;info=numeri \">Numeri Utili</a></h6>
      	
";
?>

