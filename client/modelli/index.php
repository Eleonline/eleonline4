<?php
/*
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principale</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .menu {
            list-style-type: none;
            padding: 0;
        }
        .menu li {
            margin: 15px 0;
        }
        .menu a {
            text-decoration: none;
            color: #007BFF;
            font-size: 18px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .menu a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
*/
$fase=(isset($param['fase'])) ? intval($param['fase']):"0";
if(is_file('temi/bootstrap/query.php')) 
	include('temi/bootstrap/query.php');
else include('../temi/bootstrap/query.php');
switch ($op) {
	case 'affluenza':
		include('modelli/form_affluenza.php');
		break;
	case 'scrutinio_liste':
		include('');
		break;
	default:
		break;
}
	
if(!$fase) { echo "TEST:$fase";?>
    <div class="container">
    <h1>Menu Principale</h1>
    <ul class="menu">
        <li class="menu-section"><strong>Per tutti i tipi di consultazione</strong></li>
        <li><a href="form_comunicazioni.php">Modulo Comunicazione Apertura Seggi</a></li>
        <li><a href="modules.php?name=modelli&op=affluenza&id_cons_gen=<?php echo $id_cons_gen;?>&id_comune=<?php echo $id_comune;?>&fase=1">Modulo Affluenza</a></li>

        <li class="menu-section"><strong>Solo consultazione Europee</strong></li>
        <li><a href="form_scrutinio_liste_europee.php">Form Scrutinio Liste</a></li>
        <li><a href="form_scrutinio_candidati_europee.php">Form Scrutinio Candidati</a></li>

        <li class="menu-section"><strong>Solo consultazione Regionali</strong></li>
        <li><a href="form_scrutinio_presidente_liste.php">Scrutinio Candidati Presidente + Liste</a></li>
		<li><a href="form_scrutinio_preferenze_consiglieri.php">Scrutinio Preferenze (Consiglieri)</a></li>
						
		<li class="menu-section"><strong>Solo consultazione Comunali</strong></li>
        <li><a href="form_scrutinio_sindaco_liste.php">Scrutinio Candidati Sindaci + Liste</a></li>
		<li><a href="form_scrutinio_preferenze_consiglieri.php">Scrutinio Preferenze (Consiglieri)</a></li>
		
		<li class="menu-section"><strong>Ballottaggio</strong></li>
        <li><a href="form_scrutinio_ballottaggio.php">Scrutinio Ballottagio</a></li>
		
		<li class="menu-section"><strong>Solo consultazione Referendum</strong></li>
        <li><a href="form_affluenza_referendum.php">Affluenza Referendum</a></li>
        <li><a href="form_scrutinio_referendum.php">Scrutinio Referendum</a></li>
		
    </ul>
</div>
</body>
</html>

<?php } ?>
