<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("Location: ../index.php");
    exit;
}
### Variabili di programma
#Impostazione come sito di produzione o come copia di backup
#$BACKUP=0 per il sito di lavoro -- $BACKUP=1; per il sito di backup
$BACKUP=0; 
# $CLIENT = Indirizzo del client di produzione, esempio: 'https://www.eleonline.it/client'
$CLIENT='';

# $contr_agg se =0 non effettua il controllo della presenza di nuovi aggiornamenti nella fase di accesso alla procedura da parte dell'amministratore.
# il controllo viene comunque eseguito se si seleziona la funzione di menu: Aggiornamento
# Evita il rellentamento in accesso per l'amministratore ma non si ha la visualizzazione in grassetto della voce Aggiornamento 
$contr_agg=1;

# se uguale a 1 il voto di lista e di gruppo sono completamente scollegati (ad esempio possibilità di esprimere voto di lista senza voto al gruppo)
$votoscollegato=0;

#numero di liste e gruppi visualizzati nella pagina
$offsetliste=16;
$offsetgruppi=15;

# giorni di autorizzazione dopo la scadenza della consultazione per gli operatori
$giorniaut=30;

# data di entrata in vigore della legge 72/2025 - abolizione liste di genere, formato 'YYYY/MM/DD' 
$inizioNoGenere=strtotime('2025/06/30');

# $LINK e fileback vanno lasciate vuote, vengono gestite da programma per contenere il nome del file di backup e l'indirizzo completo per scaricare la consultazione e come discrimine per la funzione di restore
$LINK='';
$fileback=''; 
?>