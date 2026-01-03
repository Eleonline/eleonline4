<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Roberto Gigli & Luciano Apolito                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/* lang_it.php Linguaggio Italiano                                      */
/************************************************************************/
global $tipo_cons;

switch ($tipo_cons){
	case '':
		define("_CONSULTAZIONE","Consultazione");
		break;
	case 1:
		define("_CONSULTAZIONE","Consultazione Elezioni Provinciali"); //genere 3
		define("_GRUPPO","Candidato Presidente");
		define("_SOLO_GRUPPO","Voti al solo candidato Presidente");
		define("_GRUPPOLOGO","Logo del Presidente");
		define("_PRESI","Presidente");
		define("_LISTA","Lista");
		define("_CANDIDATO","Candidato");
		define("_CONSI","Consigliere");
		define("_DESCRCONS","PROVINCIALI");
		define("_TABGRUPPO","gruppo");
		break;
	case 2:
		define("_CONSULTAZIONE","Consultazione Referendaria"); //genere 0
		define("_GRUPPO","Quesito Referendario");
		define("_GRUPPOLOGO","Logo del Quesito");
		define("_PRESI","Quesito");
		define("_DESCRCONS","REFERENDUM");
		define("_TABGRUPPO","gruppo");
		define("_PROGRAM","Fac-Simile");
		break;
	case 3:
		define("_CONSULTAZIONE","Consultazione Elezioni Comunali"); //genere 5
		define("_GRUPPO","Candidato Sindaco");
		define("_GRUPPOLOGO","Logo del Sindaco");
		define("_PRESI","Sindaco");
		define("_CONSI","Consigliere");
		define("_LISTA","Lista");
		define("_LISTAPREF","Lista e Preferenza");
		define("_CANDIDATO","Candidato");
		define("_SOLO_GRUPPO","Voti al solo candidato Sindaco");
		define("_DESCRCONS","COMUNALI");
		define("_PROIEZIONE","Proiezione<br>Consiglio");
		define("_TABGRUPPO","gruppo");
		break;
	case 4:
		define("_CONSULTAZIONE","Consultazione Elezioni Circoscrizionali"); //genere 5
		define("_GRUPPO","Candidato Presidente");
		define("_SOLO_GRUPPO","Voti al solo candidato Presidente");
		define("_DESCRCONS","CIRCOSCRIZIONALI");
		define("_CANDIDATO","Candidato");
		define("_LISTA","Lista");
		define("_LISTAPREF","Lista e Preferenza");
		define("_TABGRUPPO","gruppo");
		break;
	case 5: 
		define("_CONSULTAZIONE","Sessione di Ballottaggio"); //genere 1
		define("_GRUPPO","Candidato Sindaco");
		define("_GRUPPOLOGO","Logo del Sindaco");
		define("_PRESI","Sindaco");
		define("_LISTA","Lista");
		define("_CANDIDATO","Candidato");
		define("_DESCRCONS","BALLOTTAGGIO");
		define("_TABGRUPPO","gruppo");
		break;
	case 6:
		define("_CONSULTAZIONE","Consultazione Camera"); //genere 2
		define("_GRUPPO","Lista");
		define("_CANDIDATO","Candidato");
		define("_GRUPPOLOGO","Logo della Lista");
		define("_DESCRCONS","CAMERA SENZA GRUPPI");
		define("_TABGRUPPO","lista");
		break;
	case 7:
		define("_CONSULTAZIONE","Consultazione Senato"); //genere 2
		define("_GRUPPO","Lista");
		define("_CANDIDATO","Candidato");
		define("_GRUPPOLOGO","Logo della Lista");
		define("_DESCRCONS","SENATO SENZA GRUPPI");
		define("_TABGRUPPO","lista");
		break;
	case 8:
		define("_CONSULTAZIONE","Consultazione Parlamentare Europea"); //genere 4
		define("_GRUPPO","Lista");
		define("_CANDIDATO","Candidato");
		define("_LISTA","Lista");
		define("_LISTAPREF","Lista e Preferenza");
		define("_GRUPPOLOGO","Logo della Lista");
		define("_CONSI","Candidato Lista");
		define("_DESCRCONS","EUROPEE");
		define("_TABGRUPPO","lista");
		break;
	case 9:
		define("_CONSULTAZIONE","Consultazione Elezioni Regionali"); //genere 5
		define("_GRUPPO","Candidato Presidente");
		define("_CONSI","Candidato Lista");
		define("_LISTA","Lista");
		define("_LISTAPREF","Lista e Preferenza");
		define("_CANDIDATO","Candidato");
		define("_SOLO_GRUPPO","Voti al solo candidato Presidente");
		define("_DESCRCONS","REGIONALI");
		define("_TABGRUPPO","gruppo");
		break;
	case 10: //dal 2006 per senato e camera si ha un raggruppamento delle liste ma senza possibilità di voto per il gruppo
		define("_CONSULTAZIONE","Consultazione Senato"); //genere 2
		define("_GRUPPO","Coalizione");
		define("_LISTA","Lista");
		define("_CANDIDATO","Candidato");
		define("_DESCRCONS","SENATO CON GRUPPI");
		define("_TABGRUPPO","lista");
		break;
	case 11:
		define("_CONSULTAZIONE","Consultazione Camera"); //genere 2
		define("_GRUPPO","Coalizione");
		define("_LISTA","Lista");
		define("_CANDIDATO","Candidato");
		define("_DESCRCONS","CAMERA CON GRUPPI");
		define("_TABGRUPPO","lista");
		break;
	case 12:
		define("_CONSULTAZIONE","Consultazione Elezioni Provinciali"); //genere 3
		define("_GRUPPO","Candidato Presidente");
		define("_SOLO_GRUPPO","Voti al solo candidato Presidente");
		define("_GRUPPOLOGO","Logo del Presidente");
		define("_PRESI","Presidente");
		define("_LISTA","Lista");
		define("_CANDIDATO","Candidato");
		define("_CONSI","Consigliere");
		define("_DESCRCONS","PROVINCIALI");
		define("_TABGRUPPO","gruppo");
		break;
	case 13: 
		define("_CONSULTAZIONE","Sessione di Ballottaggio"); //genere 1
		define("_GRUPPO","Candidato Presidente");
		define("_GRUPPOLOGO","Logo del Presidente");
		define("_PRESI","Presidente");
		define("_LISTA","Lista");
		define("_CANDIDATO","Candidato");
		define("_DESCRCONS","BALLOTTAGGIO");
		define("_TABGRUPPO","gruppo");
		break;
	case 14:
		define("_CONSULTAZIONE","Consultazione Parlamentare Europea"); //genere 4
		define("_GRUPPO","Lista");
		define("_GRUPPOLOGO","Logo della Lista");
		define("_CONSI","Candidato Lista");
		define("_LISTA","Lista");
		define("_LISTAPREF","Lista e Preferenza");
		define("_CANDIDATO","Candidato");
		define("_DESCRCONS","EUROPEE");
		define("_TABGRUPPO","lista");
		break;
	case 15:
		define("_CONSULTAZIONE","Consultazione Camera"); //genere 2 
		define("_GRUPPO","Coalizione");
		define("_LISTA","Lista");
		define("_CANDIDATO","Candidato");
		define("_DESCRCONS","CAMERA CON GRUPPI");
		define("_TABGRUPPO","lista");
		break;
	case 16: 
		define("_SOLO_GRUPPO","Voti al solo gruppo");
		define("_CONSULTAZIONE","Consultazione Senato"); //genere 2
		define("_GRUPPO","Coalizione");
		define("_LISTA","Lista");
		define("_CANDIDATO","Candidato");
		define("_DESCRCONS","SENATO CON GRUPPI");
		define("_TABGRUPPO","lista");
		break;
	case 17:
		define("_CONSULTAZIONE","Consultazione Elezioni Regionali"); //genere 5
		define("_GRUPPO","Candidato Presidente");
		define("_SOLO_GRUPPO","Voti al solo candidato Presidente");
		define("_LISTA","Lista");
		define("_LISTAPREF","Lista e Preferenza");
		define("_CANDIDATO","Candidato");
		define("_DESCRCONS","REGIONALI");
		define("_TABGRUPPO","gruppo");
		break;
    case 18:
		define("_CONSULTAZIONE","Consultazione Camera dei deputati"); //genere 5
		define("_GRUPPO","Candidato Uninominale");
		define("_SOLO_GRUPPO","Voti al solo Candidato Uninominale");
		define("_LISTA","Lista collegata");
		define("_LISTAPREF","Lista e Preferenza");
		define("_CANDIDATO","Listino bloccato");
		define("_ATT_UNI","Attenzione! Il dettaglio dei voti");
		define("_ATT_TOT_UNI","non corrisponde al totale per il solo candidato uninominale");
		define("_DESCRCONS","CAMERA");
		define("_NULLISTA",0);
		define("_TABGRUPPO","lista");
		break;
    case 19:
		define("_CONSULTAZIONE","Consultazione Senato della Repubblica"); //genere 5
		define("_GRUPPO","Candidato Uninominale");
		define("_SOLO_GRUPPO","Voti al solo Candidato Uninominale");
		define("_LISTA","Lista collegata");
		define("_LISTAPREF","Lista e Preferenza");
		define("_CANDIDATO","Listino bloccato");
		define("_ATT_UNI","Attenzione! Il dettaglio dei voti");
		define("_ATT_TOT_UNI","non corrisponde al totale per il solo candidato uninominale");
		define("_DESCRCONS","CAMERA");
		define("_NULLISTA",0);
		define("_TABGRUPPO","lista");
		break;


}

if($tipo_cons==12 or $tipo_cons==14 or $tipo_cons==15 or $tipo_cons==16 or $tipo_cons==17){
define("_SCEGLI_CIRCO","Scegli il collegio");
define("_ELE_CIRCO","Elezione Circoscrizionale");
define("_CIRCO","Collegio");
define("_CIRCOS","Collegi");
define("_IDCIRC","Id collegio ");
define("_CIRCS","Collegi");
}else{
define("_SCEGLI_CIRCO","Scegli la Circoscrizione");
define("_ELE_CIRCO","Elezione Circoscrizionale");
define("_CIRCO","Circoscrizione");
define("_CIRCOS","Circoscrizioni");
define("_IDCIRC","Id circos. ");
define("_CIRCS","Circoscrizioni");
}
define("_SCELTASIN","Seleziona il candidato Sindaco Eletto");
define("_COLLEGAMENTI","Liste collegate per il turno di ballottaggio");
define("_NONCOLLE","Non collegate");
define("_CG","Casellario<brGiudiziario");
define("_CV","Curriculum<br>Vitae");
define("_ESTRAIDATI", "File csv consigliere");
define("_AGGIORNA","Aggiornamento Rev");
define("_VISAFFMF","Affluenze per genere");
define("_DISGIUNTO","Voto disgiunto?");
define("_RIEPILOGO","Scheda di riepilogo");
define("_SOLOLIS","Voti alle sole liste");
define("_VALIDI_LISTA","Voti di lista validi");
define("_SCELTA_LISTA","Devi scegliere una lista.");
define("_SCELTA_COGNO","Devi inserire il cognome del candidato.");
if($tipo_cons!=2)
	define("_PROGRAM","Programma");
define("_ELIMINA","ELIMINA");
define("_SPUNTAELIMINA","Metti la spunta per abilitare la cancellazione dei dati (solo voti e votanti):");
define("_DATI","i dati dello spoglio");
define("_AGGDATI","Aggiorna i dati del DB");
define("_SPUNTALABEL1","Abilita la cancellazione");
define("_SPUNTALABEL2","Cancella anche candidati e struttura");
define("_VIASPUNTAELIMINA","Metti la spunta per poter eliminare completamente la consultazione:");
define("_NOTAOP","L'utente amministratore unico di un comune deve chiamarsi obbligatoriamente admin<br>Si stanno inserendo utenti per il comune di");
define("_HELP","Help Contestuale");
define("_INDIRIZZOWEB1","URL del server");
define("_SISTEMA_MAGGIORITARIO","SISTEMA MAGGIORITARIO");
define("_SISTEMA_PROPORZIONALE","SISTEMA PROPORZIONALE");
define("_SCARICACONS","Scegli la consultazione");
define("_SCELTACOMUNE","Scegli la zona");
define("_IMPORTA","Importa liste");
define("_SCARICA","Scarica liste");
define("_TEMAATTIVO","Visualizzare la scelta del tema?");
define("_GKEY","Chiave per google maps");
define("_GOOGLEMAPS","Attivare l'accesso alle google maps?");
define("_EDITOR","Utilizzare CKeditor?");
define("_EDUSER","Permessi d'uso di FCKeditor");
define("_NOMESITO","Nome del sito per le elezioni");
define("_CONFIGDEFAULT","Impostazioni di configurazione generale");
define("_CONFIGURAZIONE","Configurazione sito");
define("_TEMA","Tema predefinito");
define("_TESTATA","Immagine per la testata");
define("_BLOCCO","Visualizzare il blocco laterale?");

define("_MULTICOMUNE","Gestione multicomune?");
define("_MOBILE","Usare il tema per il cellulare?");
define("_PREFIX","Prefisso delle tabelle");
define("_ADMINMAIL","mail dell'amministratore");
define("_SITEURL","URL del sito o IP");
define("_SITENAME","Nome del sito");
define("_SITEISTAT","Comune<br>visualizzato per default");
define("_LANGUAGE","Codice della lingua da utilizzare");
define("_FLASH","Utilizzare le animazioni flash?");
define("_DISPLAYERRORS","Attivare il debug");
define("_FILEOUT","Nome del file per esportazione dati sql");
define("_LIMITE","Limite sist. maggioritario");
define("_CONSPRED","Imposta la consultazione predefinita");
define("_STEMMA","Stemma");
define("_CONSIN","Il candidato sindaco viene eletto consigliere?");
define("_INFPREMIO","Premio di maggioranza %");
define("_LISTINFSBAR","Sbarramento %");
define("_INFMINPREMIO","Premio di maggioranza<br>solo oltre %");
define("_LISTINFCONTA","I voti di lista sotto lo sbarramento<br>vengono conteggiati per il gruppo?");
define("_SUPPREMIO","Premio di maggiornaza %");
define("_SUPSBAR","Sbarramento %");
define("_SUPMINPREMIO","Premio di maggioranza<br>solo oltre %");
define("_LISTSUPCONTA","I voti di lista sotto lo sbarramento<br>vengono conteggiati per il gruppo?");
define("_NOFASCIA","Solo maggioritario");
define("_RESTORE","Restore");
define("_CONFCONS","Config. D'Hondt");
define("_CONF","Legge elettorale");
define("_BACKUP","Backup");
define("_SEL_DATA_FILE","FUNZIONE DI RIPRISTINO DEI DATI DI UNA CONSULTAZIONE<br><br>Attenzione! si possono ripristinare solamente dati provenienti dallo stesso database (o da sue repliche).<br>Scegli il file di dati da ripristinare");
define("_SEL_DATA_FILE2","FUNZIONE DI IMPORTAZIONE DI LISTE E CANDIDATI<br><br>Attenzione! I dati sostituiscono completamente quelli eventualmente gia' presenti nella consultazione corrente.<br>Scegli il file di dati da importare");
define("_MEX_RESTORE_FAILED","ATTENZIONE! L'operazione di restore e' fallita! .");
define("_MEX_RESTORE_OK","Operazione di restore dei dati effettuata con successo");
define("_TEST","Se vuoi testare il programma inizia inserendo il nome utente \"suser\" e la password \"test\"");
define("_GESTIONE","gestione dello spoglio elettorale e pubblicazione dei risultati");
define("_GESTIPO","Gestione Tipi di Consultazione");
define("_LINGUA","Lingua");
define("_FASCIA","Abitanti");
define("_TIPO_ADM","Gestione Tipi");
define("_TUTTESEDI","Tutte le sedi");
define("_ATT_VOTI","Attenzione! I voti  inseriti");
define("_ATT_VOTANTI","Attenzione! I votanti finali inseriti");
define("_NO_TOT_VOTI","non corrispondono al totale dei voti");
define("_NO_VAL_VOTI","non corrispondono ai voti validi");
define("_ATT_VOTI_REF","Attenzione! I voti  inseriti per il Referendum n.");
define("_ATT_VOTANTI_REF","Attenzione! I votanti finali inseriti per il Referendum n.");
define("_NO_SOMMA","non corrispondono alla somma dei voti:");
define("_COD_NV","Codice ISTAT non valido");
define("_REFERENDUM","Referendum");
define("_CON_GRUPPI","Con raggruppamenti");
define("_NO_VOTO_GRUPPO","Non si vota per il Gruppo");
define("_LISTE_UNI","Liste uninominali");
define("_NO_VOTO_LISTA","Non si vota per la lista");
define("_NO_VOTO_CANDI","Non si vota per i candidati");
define("_BALLO","Ballottaggio");
define("_DEFCONS","Definisci il genere della consultazione");
define("_VOTI_LISTA","Voti di lista");
define("_GENCONS0","Referendum");
define("_GENCONS1","Ballottaggio");
define("_GENCONS2","Liste Uninominali");
define("_GENCONS3","Gruppi e liste senza preferenze ai candidati");
define("_GENCONS4","Liste a piu' candidati");
define("_GENCONS5","Gruppi e liste con preferenze ai candidati");
define("_CAMBIOPWD","Modifica Password");
define("_CHGPWD","Modifica della Password");
define("_ERRPWD","ATTENZIONE: ERRORE NELL'INSERIMENTO DELLA PASSWORD!");
define("_OLDPWD","Vecchia Password");
define("_NEWPWD1","Nuova Password");
define("_NEWPWD2","Conferma Nuova Password");
define("_YES","Si");
define("_ISCRITTI","Iscritti");
define("_INSEZ","nella sezione");
define("_PERC","Perc.");
define("_DOMAGGIORNA","Vuoi veramente aggiornare il numero di elettori della");
define("_UPDATE","Aggiorna");
define("_ERRORE","Errore");
define("_COME","Come si vota");
define("_NUMERI","Numeri utili");
define("_SERVIZI","Servizi");
define("_LINK","Link utili");
define("_CONSULTAZIONI","Consultazioni");
define("_OPERATORI","Operatori");
define("_AGGIUNGI","Aggiungi");
define("_COLLEGI","Collegi");
define("_DEFCOMUNE","Comuni");
define("_PROV","Capoluogo<br>Provincia");
define("_CODICE","Codice<br>ISTAT");
define("_CENTRALINO","Centralino");
define("_EMAIL","E-mail");
define("_FILTRO","Inserisci Filtro");
define("_INSCOMUNE","Autorizza Comuni");
define("_CONSULTAZIONE_ADM","Consultazione");
define("_AMMINISTRATORI", "Amministratori");
define("_PASSWORD","Password");
define("_RIPETI","Ripeti Password");
define("_MOD_INSERITI","Modelli gia' inseriti");
define("_SCELTA_MODELLO","Impostazione dei modelli di stampa per la Prefettura");
define("_COMPLESSIVO","complessivo");
define("_CONTR_CONS","Controllo voti alle liste");
define("_CONTR_GRUP","Controllo voti ai gruppi");
define("_DATAIN","Data inizio");
define("_STAMPE_PREF","Stampe per Prefettura");
define("_ELENCO_STAMPE","Elenco dei modelli di stampa per le comunicazioni alla Prefettura");
define("_MODELLI","Modelli per stampe");
define("_DATAFINE","Data fine");
define("_GEST","Gestione");
define("_MODIFY","Modifica");
define("_ESCI","Esci");
define("_POPOLA","Popola");
define("_SOSPESO","Sospeso");
define("_IMPOSTA_DATI","Responsabile");
define("_INSERISCE_DATI","Operatore");
define("_UTENTE","Utente");
define("_PERMESSI","Permessi");
define("_NULLA","NULLA");
define("_ATTIVA","ATTIVA");
define("_ATTIVO","Attivo");
define("_CHIUSA","CHIUSA");
define("_ASOLA_LISTA","Alle Liste");
define("_ASOLO_GRUPPO","Ai Soli Gruppi");
define("_COLLEGIO","collegio");
define("_A","a");
define("_DA","da");
define("_DI","di");
define("_COMUNE","Comune");
define("_VIS_PERC","Visualizza percentuali");
define("_SEZIONE","Sezione");
define("_SEZIONI","Sezioni");
define("_UOMINI","Uomini");
define("_DONNE","Donne");
define("_COMPLESSIVI","Complessivi");
define("_VOTIE","Voti espressi");
define("_SEZNOS","Sezioni non scrutinate");
define("_SEZSCR","Sezioni scrutinate");
define("_PREFLISTA","Preferenze di Lista");
define("_GESAMMIN","Gestione Amministrativa ");
define("_DESCR","Denominazione");
define("_IMMCONS","Immissione consultazione");
define("_DOMCANCELLA","Sicuro di voler cancellare ");
define("_FUNZIONI","Funzioni di modifica ");
define("_NEXT_MATCH","Successivi");
define("_PREV_MATCH","Precedenti");
define("_NEXT","Successiva");
define("_PREV","Precedente");
define("_SCELTA","Scelta");
define("_SCEGLI","Scegli ");
define("_SEDE","Sede elettorale ");
define("_NUM","Numero ");
define("_IDCONS","Id consult. ");
define("_IMM","Immissione");
define("_TEL","Telefono");
define("_INDIRIZZO","Indirizzo");
define("_FAX","Fax");
define("_RESP","Responsabile");
define("_MASCHI","Maschi ");
define("_FEMMINE","Femmine ");
define("_AUT_M","Autorizzati Maschi ");
define("_AUT_F","Autorizzate Femmine ");
define("_SCHEDE","Schede ");
define("_SCHEDE_D","Schede disponibili ");
define("_SCHEDE_A","Schede autenticate ");
define("_TOTS","Totali ");
define("_TOT","Totale ");
define("_TOTPREF","Totale preferenze");
define("_GESTIMM","Gestione Logo o Simbolo");
define("_SIMBOLO","Simbolo");
define("_COLORE","Colore Scheda");
define("_INVIOFILE","<font color=\"red\">Invio file (jpg,gif o png) per Logo o Simbolo</font>");
define("_FILEDAINVIARE","Scegli il file da inviare");
define("_CANCELLAFILE","<font color=\"red\">Cancella file Logo o Simbolo</font>");
define("_SCELTAFILE","Scegli il file da cancellare");
define("_LISTALOGO","Logo della lista");
define("_COGNOME","Cognome");
define("_NOME","Nome");
define("_FOTO","Foto");
define("_NOTE","Note");
define("_GESSPOGLIO","Gestione Spoglio Elettorale");
define("_PRIMONON","Primo non eletto");
define("_SINDCONS","Candidato Sindaco<br/>eletto Consigliere");
define("_SEGGI","Seggi");
define("_CIFRAELE","Cifra Elettorale");
define("_QUOZIENTI","Quozienti");
define("_SPOGLIO","Spoglio");
define("_VOTI","Voti");
define("_VOTANTI","Votanti");
define("_PREFERENZE","Preferenze");
define("_ORA","Orario");
define("_TITOLO","Titolo");
define("_PREAMBOLO","Sommario");
define("_CONTENUTO","Testo");
define("_DATA","Data");
define("_VOTID","Voti Donne");
define("_VOTIU","Voti Uomini");
define("_VOTIT","Voti Totali");
define("_VALIDI","Voti Validi");
define("_NULLI","Schede Nulle");
define("_VOTINULLI","Voti Nulli");
define("_NULLI_LISTE","Voti di Lista Nulli");
define("_BIANCHI","Schede Bianche");
define("_CONTESTATI","Voti Contestati");
define("_CONTESTATI_LISTE","Voti di Lista Contestati");
define("_TOTALEVOTI","Voti Totali");
define("_TOT_ULT","Votanti Ultima Ora");
define("_VOTIINS","Voti Inseriti");
define("_CANDINS","Candidati Inseriti");
define("_TOTNON","Tot. Voti non Validi");
define("_CANDIDATI","Candidati");
define("_CONSIGLIO","Consiglio Provinciale");
define("_DATIG","Dati Generali");
define("_AVENTI","Aventi Diritto");
define("_SI","Si");
define("_NO","No");
define("_AFFLUENZE","Affluenze");
define("_NOTA","I dati immessi su questo sito, suscettibili di modifiche, sono ufficiosi. Essi hanno valore puramente informativo.");
define("_ORE","ore");
define("_ALLE","alle");
define("_DEL","del");
define("_STATO","Stato");
define("_OK","Ok");
define("_ANCHE","anche");
define("_VISUALIZZA","Visualizza i dati");
define("_TIPO","Tipo");
define("_CONSULTA","Consultazione");
define("_PERCE","Percentuale");
define("_RISULTA","Risultati");
define("_PER","per");
define("_SEZSCRU","Sezioni scrutinate ");
define("_SU","su");
define("_LA","la");
define("_ADMINID","Utente");
define("_ADMINCOME","COME SI VOTA - Amministrazione");
define("_EDITORIAL","Informazioni Generali");
define("_EDITORIALADMIN","Amministrazione Informazioni");
define("_EDITINFO","Edita informazione");
define("_EDIT","Edita");
define("_ADD","Aggiungi");
define("_NEW","Nuovo");
define("_DELETE","Elimina");
define("_COPIA","Copia");
define("_STRUTTURA","Struttura");
define("_ADDCOME","Aggiungi informazione");
define("_ADDPRES","Aggiungi candidato presidentee");
define("_ALLCOME","Informazioni presenti");
define("_REMOVEINFO","Sicuro di voler rimuovere l'informazione? ");
define("_IMAGE","Immagine informazione");
define("_CONTINUA","continua <img src=\"images/site.gif\" align=\"center\" border=\"0\">");
define("_MESSAGEPREAMBLE","Preambolo (max 500 car)");
define("_ADMINNUMERI","Numeri Utili - Amministrazione");
define("_ADMINSERVIZI","Servizi Utili - Amministrazione");
define("_ADMINLINK","Links Utili - Amministrazione");
define("_CONTR_ESPR","Controllo Voti Espressi");
define("_CONTR_PREF","Controllo Preferenze");
// Note
define("_ENTE","Comune di Guidonia Montecelio (Provincia di Roma)");
define("_NOTACSV","Note: I dati sono estratti dal sito del ");
define("_DISC1","Ogni rappresentazione ed uso e' consentita citando la fonte dei dati");
define("_SITO","http://www.guidonia.org ");
// Copyright
define("_SORALDO","SorAldo: Modulo elettorale");
define("_VERS","versione 0.5 beta ");
define("_BYNOI","di Luciano Apolito e Roberto Gigli");
define("_EMAILBY","Email: luciano@aniene.net - rgigli@libero.it");
define("_NON","Non");
define("_EXPORT","Esporta");
define("_TABULA","(formato con tabulazione)");
define("_GRAFICI","Grafici ");
define("_ALL","Tutti ");
define("_MAIN","Qui sono e saranno presenti i dati e i risultati delle
consultazioni elettorali svolte nel ");
define("_THEMES","Tema Grafico");
define("_ACTIVE","Attivo ");
define("_BLOCCHI","Blocchi laterali");
define("_BLOCKS","Blocchi");
define("_MENUCONF","Menu di configurazione dell'aspetto grafico del sito");
define("_GESFILE","Gestione Files Immagini");
define("_LOGO","Immagine o logo");
define("_ELETTORI","Elettori");
define("_EDIFICIO","Edificio");
define("_MAPPA","Mappa");
define("_NUMERITEL","Numeri telefonici");
define("_DESCRAPP","Descrizione approfondita");
define("_DESCRLINK","Breve descrizione del link");
define("_HELPHTML","Sono consentiti tutti i tag HTML. Esempio: <font color=\"#ff0000\">&lt br &gt </font>ritorno a capo</pre>");
define("_SUPER","S<br>U<br>P<br>E<br>R<br>U<br>S<br>E<br>R");
define("_ADMIN","A<br>D<br>M<br>I<br>N<br>I<br>S<br>T<br>R<br>A<br>T<br>O<br>R");
define("_OPER","O<br>P<br>E<br>R<br>A<br>T<br>O<br>R");
define("_SCELTA_CONS","Scelta della Consultazione");
define("_WIDGET","Widget");
define("_PLUGINS","Plugins");
define("_CONFIGWIDGET","Configurazione dei box laterali");
define("_SEGNALA","Segnala l'installazione a Eleonline");


?>
