<?php
/************************************************************************/
/* Eleonline - Raccolta e diffusione dei dati elettorali                */
/* by Luciano Apolito & Roberto Gigli                                   */
/* http://www.eleonline.it                                              */
/* info@eleonline.it  luciano@aniene.net rgigli@libero.it               */
/************************************************************************/

//global $tipo_cons;
switch ($tipo_cons){
	case 1:
		define("_CONSULTAZIONE","Consultazione Elezioni Provinciali");
		define("_GRUPPI","Candidati Presidenti");
		define("_GRUPPO","Candidato Presidente");
		define("_GRUPPOLOGO","Logo del Presidente");
		define("_PRESI","Presidente");
		define("_CONSI","Consigliere");
		define("_SOLO_GRUPPO","Voti al solo gruppo");
		define("_VOTI_LISTA","Voti di lista");
		define("_CONSIGLIO","Consiglio Provinciale");
		break;
	case 2:
		define("_CONSULTAZIONE","Consultazione Referendaria");
		define("_GRUPPI","Quesiti Referendari");
		define("_GRUPPO","Quesito Referendario");
		define("_GRUPPOLOGO","Logo del Quesito");
		define("_PRESI","Quesito");
		break;
	case 3:
		define("_CONSULTAZIONE","Consultazione Elezioni Comunali");
		define("_GRUPPI","Candidati Sindaco");
		define("_GRUPPO","Candidato Sindaco");
		define("_GRUPPOLOGO","Logo del Sindaco");
		define("_PRESI","Sindaco");
		define("_CONSI","Consigliere");
		define("_SOLO_GRUPPO","Voti al solo candidato Sindaco");
		define("_CONSIGLIO","Consiglio Comunale");
		define("_VOTI_LISTA","Voti di lista");
		break;
        case 4:
	        define("_CONSULTAZIONE","Consultazione Elezioni Circoscrizione");
                define("_GRUPPI","Candidati Presidente");
		define("_GRUPPO","Candidato Presidente");
                define("_CONSI","Consigliere");
		define("_CONSIGLIO","Consiglio Circoscrizionale");
                break;
	case 5: //era 9 ma 9 non esiste vanno previsti i diversi casi di ballottaggio (prov. comune...)
		define("_CONSULTAZIONE","Sessione di Ballottaggio");
		define("_GRUPPI","Candidati Sindaco");
		define("_GRUPPO","Candidato Sindaco");
		define("_GRUPPOLOGO","Logo del Sindaco");
		define("_PRESI","Presidente");
		break;
	case 6:
		define("_CONSULTAZIONE","Consultazione Camera");
		define("_GRUPPI","Liste");
		define("_GRUPPO","Lista");
		define("_GRUPPOLOGO","Logo della Lista");
		break;
	case 7:
		define("_CONSULTAZIONE","Consultazione Senato");
		define("_GRUPPI","Liste");
		define("_GRUPPO","Lista");
		define("_GRUPPOLOGO","Logo della Lista");
		break;
	case 8:
		define("_CONSULTAZIONE","Consultazione Parlamentare Europea");
		define("_GRUPPI","Liste");
		define("_GRUPPO","Lista");
		define("_GRUPPOLOGO","Logo della Lista");
		define("_CONSI","Candidato Lista");
		define("_CONSIGLIO","Consiglio Europeo");
		break;
	case 9:
		define("_CONSULTAZIONE","Consultazione Regionale");
		define("_GRUPPI","Candidati Presidente");
		define("_GRUPPO","Candidato Presidente");
		define("_GRUPPOLOGO","Logo del Presidente");
		define("_CONSI","Candidato Lista");
		define("_CONSIGLIO","Consiglio Regionale");
		break;
	
	// Aggiunte per la nuova legge	2006
	case 10:
		define("_CONSULTAZIONE","Consultazione Senato");
		define("_GRUPPI","Coalizioni");
		define("_GRUPPO","Coalizione");
		//define("_LISTA","Lista");
		define("_LISTE","Liste");
		define("_GRUPPOLOGO","Logo della Lista");
		define("_CONSIGLIO","Senatori");
		break;
	
	case 11:
		
		define("_CONSULTAZIONE","Consultazione Camera");
		define("_GRUPPI","Coalizioni");
		define("_GRUPPO","Coalizione");
		//define("_LISTA","Lista");
		define("_LISTE","Liste");
		define("_GRUPPOLOGO","Logo della Lista");
		define("_CONSIGLIO","Deputati");
		break;


}
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
define("_GESTIONE","Gestione");
define("_CIRCO","Circoscrizione");
define("_CIRCOS","Circoscrizioni");
define("_A","a");
define("_ASOLA_LISTA","Alle Liste");
define("_ASOLO_GRUPPO","Ai Soli Gruppi");
define("_UOMINI","Uomini");
define("_DONNE","Donne");
define("_COMPLESSIVI","Complessivi");
define("_COMUNI","Comuni");
define("_CONSULTAZIONI","Consultazioni");
define("_INFO","Informazioni");
define("_CALCONS","Proiezione Consiglio");
define("_QUOZIENTI","Quozienti");
define("_CIFRAELE","Cifra<br/>Elettorale");
define("_PROIEZCONS","PROIEZIONE DELLA COMPOSIZIONE DEL CONSIGLIO COMUNALE");
define("_SCELTASIN","Scegli quale candidato deve essere considerato Sindaco Eletto");
define("_COLLEGAMENTI","Collegamenti per il turno di ballottaggio");
define("_NONCOLLE","Non collegate");
define("_SINDACO","Sindaco");
define("_RISULTATI","Risultati");
define("_DETTAGLIO","Dettaglio");
define("_YES","Si");
define("_RIGHT","Destra");
define("_DA","da");
define("_VIS_PERC","Visualizza percentuali");
define("_VOTIE","Voti espressi");
define("_VOTIL","Voti liste");
define("_SEZNOS","Sezioni non scrutinate");
define("_SEZSCR","Sezioni scrutinate");
define("_LISTA","Lista");
define("_PREFLISTA","Preferenze di Lista");
define("_CANDIDATO","Candidato");
define("_GESAMMIN","Gestione Amministrativa ");
define("_DESCR","Descrizione");
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
define("_IDCIRC","Id circos. ");
define("_IMM","Immissione");
define("_TEL","Telefono");
define("_INDIRIZZO","Indirizzo");
define("_FAX","Fax");
define("_RESP","Responsabile");
define("_SEZIONE","Sezione ");
define("_MASCHI","Maschi ");
define("_FEMMINE","Femmine ");
define("_AUT_M","Autorizzati Maschi ");
define("_AUT_F","Autorizzate Femmine ");
define("_SCHEDE_D","Schede disponibili ");
define("_SCHEDE_A","Schede autenticate ");
define("_TOTS","Totali ");
define("_TOT","Totale ");
define("_GESTIMM","Gestione Logo o Simbolo");
define("_SIMBOLO","Simbolo");
define("_INVIOFILE","<font color=\"red\">Invio file (jpg,gif o png) per Logo o Simbolo</font>");
define("_FILEDAINVIARE","Scegli il file da inviare");
define("_CANCELLAFILE","<font color=\"red\">Cancella file Logo o Simbolo</font>");
define("_SCELTAFILE","Scegli il file da cancellare");
define("_LISTALOGO","Logo della lista");
define("_COGNOME","Cognome");
define("_NOME","Nome");
define("_NOMINATIVO","Nominativo");
define("_FOTO","Foto");
define("_NOTE","Note");
define("_SEGGI","Seggi");
define("_PRIMONON","Primo non eletto");
define("_SINDCONS","Candidato Sindaco<br/>eletto Consigliere");
define("_SEGGIMIN","Consiglieri di minoranza");
define("_GESSPOGLIO","Gestione Spoglio Elettorale");
define("_SPOGLIO","Spoglio");
define("_VOTI","Voti");
define("_VOTANTI","Votanti");
define("_NONVOTANTI","Non Votanti");
define("_PREFERENZE","Preferenze");
define("_ORA","Orario");
define("_DATA","Data");
define("_VOTID","Voti Femmine");
define("_VOTIU","Voti Maschi");
define("_VOTIT","Voti Totali");
define("_VALIDI","Voti Validi");
define("_NULLI","Voti Nulli");
define("_BIANCHI","Voti Bianchi");
define("_CONTESTATI","Voti Contestati");
define("_TOTALEVOTI","Voti Totali");
define("_TOT_ULT","Votanti Ultima Ora");
define("_VOTIINS","Voti Inseriti");
define("_CANDINS","Candidati Inseriti");
define("_TOTNON","Tot. Voti non Validi");
define("_CANDIDATI","Candidati");
define("_DATIG","Dati Generali");
define("_CIRCS","Circoscrizioni");
define("_SEZIONI","Sezioni");
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
define("_MOSTRA","Visualizza");
define("_PERCOL","per colonne");
define("_PERRIGHE","per righe");
define("_VISUALIZZA","Visualizza i dati");
define("_TIPO","Tipo");
define("_CONSULTA","Consultazione");
define("_PERCE","Percentuale");
define("_RISULTA","Risultati");
define("_PER","per");
define("_SEZSCRU","Sezioni scrutinate ");
define("_SU","su");
define("_ADMINCOME","COME SI VOTA - Amministrazione");
define("_EDITORIAL","Informazioni Generali");
define("_EDITORIALADMIN","Amministrazione Informazioni");
define("_EDITINFO","Edita informazione");
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
// Note
define("_ENTE","Comune di Guidonia Montecelio (Provincia di Roma)");
define("_NOTACSV","Note: I dati sono estratti dal sito del ");
define("_DISC1","Ogni rappresentazione ed uso e' consentita citando la fonte dei dati");
define("_SITO","http://www.guidonia.org ");
// Copyright
define("_SORALDO","SorAldo: Modulo elettorale");
define("_VERS","versione 0.3 beta ");
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
define("_ORDINE","Ordine");
define("_MAPPA","Mappa");
define("_COMUNE","Comune di ");
define("_DISCLAIMER","Dati Provvisori suscettibili di modifica. I dati hanno puramente titolo informativo");
define("_COME","Come si vota");
define("_NUMERI","Numeri Utili");
define("_SERVIZI","Servizi");
define("_LINK","Link utili");
define("_DATI","Dati Generali");
define("_SINGOLA","singola sede elettorale");
define("_INIZIALI","Iniziali del Cognome");
//define("_ALL","Tutti");
define("_SCELTA_CIR","Scegli la Circoscrizione");
define("_CIRC_N","Circoscrizione n. ");
define("_PERC","Percentuale");
define("_VER_GRAF","Versione Grafica");
define("_VER_STAMPA","Versione Stampa");
define("_VER_FLASH","Grafica Flash");
define("_VER_HTML","Grafica Statica");
//define("_DETTAGLIO","Dettaglio");
define("_ISCR_SEZ","Iscritti<br />nella sezione");
define("_CONFRONTI","Raffronti");
define("_ALTROGRP","Altri");
define("_GRUPPO1","Gruppo 1");
define("_GRUPPO2","Gruppo 2");
define("_SCELTA_LISTE","ASSEGNA LE LISTE AI RAGGRUPPAMENTI");
define("_ALTRI","Altri sotto il 3%");
define("_CNFR_CONS","RAFFRONTO TRA CONSULTAZIONI<br/>si pu� impostare su liste singole o su raggruppamenti");
define("_SCELTA_CONS","<b>Scegli le consultazioni da mettere a confronto</b>");



// Contatti
define("_SEND","Invia");
define("_YOURNAME","Nome");
define("_MESSAGE","Il Messaggio");
define("_YOUREMAIL","Email");
define("_FEEDBACKNOTE","
Attraverso questo form potrai richiedere all'Ente informazioni ed inviare commenti e proposte migliorative al software ed al servizio.
Ogni commento in merito sara' ben accetto. Grazie!");
define("_FEEDBACKTITLE","Proposte e contatti");
define("_FEEDBACK","Contatti");
define("_FBENTERNAME","Nominativo mancante, grazie!");
define("_FBENTEREMAIL","Indirizzo email mancante, grazie!");
define("_FBENTERMESSAGE","Nessun messaggio immesso, grazie!");
define("_SENDEREMAIL","Email dell'utente");
define("_SENDERNAME","Nome dell'utente");
define("_FBMAILSENT","Il messaggio e' stato inviato!");
define("_FBTHANKSFORCONTACT","Grazie per averci contattato. ");
define("_GESRIS","gestione risultati elettorali");
define("_INVIOSEGN","Per rendere questo servizio piu' efficiente puoi inviare segnalazioni e suggerimenti a");
define("_CLICCAQUI","questo indirizzo");
define("_GOBACK","[ <a href=\"javascript:history.go(-1)\">Indietro</a> ]");

?>


