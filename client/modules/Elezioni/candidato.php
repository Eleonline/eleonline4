<?php
########## funzione candidato


if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}


/******************************************************/
/*Funzione di visualizzazione globale candidato         */
/*****************************************************/
function candidato() {
   global $tipo_cons, $prefix, $dbi, $offset, $min, $id_cons_gen, $id_cons,$file,$genere,$prev,$next,$lettera,$ltr,$ordine,$id_comune,$id_circ,$id_lista,$votog,$circo,$num;
  
 
 
  $offset=15;
  if ($circo==1) $offset=1000;
  if (!isset($min)) $min=0;
  $go="candi";


   if($circo==0)$id_circ='';




  # numero sezioni
  $sql = "select * from ".$prefix."_ele_sezioni where id_cons='$id_cons' ";
	$res = $dbi->prepare("$sql");
	$res->execute();

  $sezioni = $res->rowCount();



   /* Scelta alfabetica */
  
	echo "<br /><h2>"._CANDIDATI." "._CONSIGLIO."</h2>"; 
	echo "<div><b>"._INIZIALI."</b></div>";
        $alfa = array (""._ALL."", "A","B","C","D","E","F","G","H","I","J","K","L","M",
                            "N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $num = count($alfa) - 1;
        echo "<div>[ ";
        $counter = 0;
        // while (list(, $ltr) = each($alfa)) {
		
		foreach ($alfa as $ltr) {
	  
	  
            echo "<a href=\"modules.php?name=Elezioni&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=$id_circ&amp;op=candi&amp;lettera=$ltr&amp;ordine=$ordine\">$ltr</a>";
            if ( $counter == round($num/2) ) {
                echo " ]\n<br />\n[ ";
            } elseif ( $counter != $num ) {
                echo "&nbsp;|&nbsp;\n";
            }
            $counter++;
        }
        echo " ]\n</div>\n<br />\n";
 
// ordine
	if ($ordine=="") $ordine="cognome";
        echo "\n<div>\n"; // Start of HTML
        echo ""._ORDINE." <b>[</b> ";
        if ($ordine == "cognome" OR !$ordine) {
            echo ""._COGNOME."&nbsp;|&nbsp;";
        } else {
            echo "<a href=\"modules.php?name=Elezioni&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=$id_circ&amp;op=candi&amp;lettera=$lettera&amp;ordine=cognome&amp;min=$min&amp;offset=$offset\">"._COGNOME."</a>&nbsp;|&nbsp;";
        }
        
		if ($ordine == "id_lista") {
            echo ""._LISTA."&nbsp;|&nbsp;";
        } else {
            echo "<a href=\"modules.php?name=Elezioni&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=$id_circ&amp;op=candi&amp;lettera=$lettera&amp;ordine=id_lista\">"._LISTA."</a>&nbsp;";
        }
	
	/*
	if($genere!=4)	
        if ($ordine == "id_gruppo") {
            echo ""._GRUPPO."&nbsp;";
        } else {
	
            echo "<a href=\"modules.php?name=Elezioni&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=$id_circ&amp;op=candi&amp;lettera=$lettera&amp;ordine=id_gruppo\">"._GRUPPO."</a>&nbsp;";
        }
	*/
	
        echo " <b>]</b>\n</div>\n";
	    
	    if ($lettera ==""._ALL."") $lettera="";
  // Fine ordine
  
  
	# conta il numero dei candidati
	if  ($circo=="1"){
	  $sql="select id_lista from ".$prefix."_ele_lista where id_cons='$id_cons' and id_circ='$id_circ'";
		$res = $dbi->prepare("$sql");
		$res->execute();
	  while(list($id_lista2) = $res->fetch(PDO::FETCH_NUM)){
		$sql="select id_cand from ".$prefix."_ele_candidati where id_lista='$id_lista2'  and cognome like \"$lettera%\"";
		$res2 = $dbi->prepare("$sql");
		$res2->execute();
		while(list($id_cand2) = $res2->fetch(PDO::FETCH_NUM)){
			$max=$max+1;
		}
          }
	
	}else{
	
	$sql="SELECT id_cons FROM ".$prefix."_ele_candidati where id_cons='$id_cons' and cognome like \"$lettera%\"  ";
	$res = $dbi->prepare("$sql");
	$res->execute();
    if($res)	$max = $res->rowCount();
	
	}
        
	
	//if ($ordine=="" or !$ordine) $ordine="cognome";
     	
     	if ($lettera!="") echo "Lettera: <span class=\"red\">$lettera </span>";
     	if ($ordine=="cognome") echo " Ordine: <span class=\"red\"> Cognome</span>";
     	if ($ordine=="id_lista") echo " Ordine: <span class=\"red\">"._LISTA."</span>";
     	//if ($ordine=="id_gruppo") echo " Ordine:<span class=\"red\"> "._GRUPPO."</span>";
     	echo " Numero Candidati: <span class=\"red\">$max</span>";
     	echo "<br /><br />
	<table class=\"table-80\" rules=\"rows\" ><tr class=\"bggray\">"
	."<td><b>"._NUM."</b></td>"
	."<td ><b>"._NOME."</b></td>"
	."<td><b>"._LISTA."</b></td>";
	if ($genere!=4) echo "<td><b>"._GRUPPO."</b></td></tr>";
	
	
	
	if ($ordine=="id_gruppo") $ordine="id_lista";    
	$sql="select * from ".$prefix."_ele_candidati where id_cons='$id_cons'  and cognome like \"$lettera%\" order by  $ordine  LIMIT $min,$offset";
 	$result = $dbi->prepare("$sql");
	$result->execute();
   	
	
	while(list($id_cand,$id_cons2,$id_lista, $numlista, $cognome, $nome, $note,, $num_cand) = $result->fetch(PDO::FETCH_NUM)) {
        if ($circo=="1") $circos=" and id_circ='$id_circ'";else $circos='';// per circosc 5-06
        	// dati lista
		$sql="select descrizione,id_gruppo,id_circ from ".$prefix."_ele_lista where id_lista='$id_lista' $circos ";
		$res01 = $dbi->prepare("$sql");
		$res01->execute();
		
        if ($circo=="1") list($descr_lista,$id_gruppo,$id_circ2)=$res01->fetch(PDO::FETCH_NUM);
		else list($descr_lista,$id_gruppo,$id_circ_off)=$res01->fetch(PDO::FETCH_NUM);
		
		
		// dati gruppo
		
		if ($genere!=4){
		$sql="select descrizione from ".$prefix."_ele_gruppo where id_gruppo='$id_gruppo' ";
		$res2 = $dbi->prepare("$sql");
		$res2->execute();
        	list($descr_gruppo)=$res2->fetch(PDO::FETCH_NUM);
		
		}
	
	if (!$id_circ)$id_circ="";
	if (!isset($id_circ2)) $id_circ2="";	

	if ($id_circ==$id_circ2){
	

		echo "<tr class=\"table-main\"><td class=\"bggray\"><b>$num_cand</b>"
         	."</td><td style=\"text-align:left;\"><b>";
               if($votog){ echo "$cognome $nome";
		}else{
		      echo "  &nbsp;<a href=\"modules.php?name=Elezioni&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;op=candidato_sezione&amp;min=$num_cand&amp;offset=$num_cand&amp;id_lista=$id_lista&amp;orvert=1&amp;offsetsez=$sezioni&amp;id_circ=$id_circ\">$cognome $nome</a>";
		}
     
		echo "</b></td>
		<td><b><a href=\"modules.php?name=Elezioni&amp;id_gruppo=$id_gruppo&amp;id_circ=$id_circ&amp;id_cons_gen=$id_cons_gen&amp;id_lista=$id_lista&amp;op=partiti&amp;id_comune=$id_comune\">
		<img class=\"stemmapic\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_lista=$id_lista\"  alt=\"$cognome $nome\" /><br />$descr_lista  </a></b>";
		
		if ($genere!=4){
		 
		    echo "</td><td>
		    <a 					href=\"modules.php?name=Elezioni&amp;op=gruppo&amp;id_cons_gen=$id_cons_gen&amp;id_comune=$id_comune&amp;id_circ=$id_circ\"><img class=\"stemmapic\" src=\"modules.php?name=Elezioni&amp;file=foto&amp;id_gruppo=$id_gruppo \"   alt=\" \" /><br />$descr_gruppo</a>";
		    echo "</td></tr>";
		  }
		}
	    
 	}
	
	
    
    echo "</table>";

     
     if (!$circo) page($id_cons_gen,$go,$max,$min,$prev,$next,$offset,$file);

}









?>
