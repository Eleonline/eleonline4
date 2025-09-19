<?php
if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}
### Variabili di programma
#
$offsetliste=16;           	# Risultati numero di liste visualizzate nella pagina
$offsetgruppi=15;          	# Risultati numero di gruppi visualizzati nella pagina
$numcandvis=16;            	# Numero di candidati visualizzati per pagina
$numsezvis=26;            	# Numero di sezioni visualizzate per pagina
$datigenvis=20;            	# Elementi visualizzati in scheda Dati generali
$giorniviselday=29;			# Massimo numero di giorni in cui viene visualizzata la sezione Election day 
							# (visualizza i pulsanti di accesso diretto alle singole consultazioni)

#La variabile $arcon stabilisce l'ordine di visualizzazione delle consultazioni nel menu bootstrap
# 1, Provinciali
# 2, Referendum
# 3, Comunali
# 4, Circoscrizionali
# 5, Ballottaggio Comunali
# 6, Camera
# 7, Senato
# 8, Europee
# 9, Regionali
# 13, Ballottaggio Provinciali

$arcon="
6,
7,
8,
9,
3,
5,
2,
4,
1,
13
";
#Fine ordine delle consultazioni nel menu

# Testo della legge sulle Elezioni Trasparenti da visualizzare nella corrispondente pagina
$leggeTrasparenza="L'art. 1, comma 14, della Legge 09/01/2019, n. 3 prevede che entro il quattordicesimo giorno antecedente la data delle competizioni elettorali di qualunque genere, escluse quelle relative a comuni con meno di 15.000 abitanti, i partiti e i movimenti politici, nonche' le liste di cui al comma 11, primo periodo, hanno l'obbligo di pubblicare nel proprio sito internet il curriculum vitae fornito dai loro candidati e il relativo certificato penale rilasciato dal casellario giudiziale non oltre novanta giorni prima della data fissata per la consultazione elettorale. Ai fini dell'ottemperanza agli obblighi di pubblicazione nel sito internet di cui al presente comma non è richiesto il consenso espresso degli interessati.<br>Al comma 15 la stessa legge prevede che siano pubblicati in apposita sezione, denominata \"Elezioni trasparenti\", del sito internet dell'ente cui si riferisce la consultazione elettorale, entro il settimo giorno antecedente la data della consultazione elettorale, per ciascuna lista o candidato ad essa collegato nonchè per ciascun partito o movimento politico che presentino candidati alle elezioni di cui al comma 14 in maniera facilmente accessibile il curriculum vitae e il certificato penale dei candidati rilasciato dal casellario giudiziale non oltre novanta giorni prima della data fissata per l'elezione, già pubblicati nel sito internet del partito o movimento politico ovvero della lista o del candidato con essa collegato di cui al comma 11, primo periodo, previamente comunicati agli enti di cui al presente periodo.<br>La pubblicazione deve consentire all'elettore di accedere alle informazioni ivi riportate attraverso la ricerca per circoscrizione, collegio, partito e per cognome e nome del singolo candidato."
#Fine testo legge trasparenza

?>