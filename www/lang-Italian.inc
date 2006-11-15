<?php 
/*
	lang-Italian.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.
	
	Based on m0n0wall (http://m0n0.ch/wall)
	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
	All rights reserved.
	
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:
	
	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.
	
	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.
	
	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
/*
	Italian Translation by Pietro De Faccio
	mail defaccio DOT pietro AT tiscali DOT it
	for bugs about this translation
*/
// locale defines
define('_CHARSET','ISO-8859-1');
define('_LOCALE','it_IT');
define('_LOCALEWIN', 'ita');
// date and time defines
define('_DATE','Data');
define('_DATEBRIEF','%b %d, %Y');
define('_DATELONG','%A, %B %d, %Y');
define('_DATESTRING','%A, %B %d @ %H:%M:%S');
define('_DATETIMEBRIEF','%b %d, %Y - %I:%M %p');
define('_DATETIMELONG','%A, %B %d, %Y - %I:%M %p');
define('_DAY_OF_WEEK_LONG','Domenica Lunedi Martedì Mercoledì Giovedì Venerdì Sabato');
define('_DAY_OF_WEEK_SHORT','Dom Lun Mar Mer Gio Ven Sab');
define('_MONTH_LONG','Gennaio Febbraio Marzo Maggio Aprile Meggio Giugno Luglio Agosto Settembre Ottobre Novembre Dicembre');
define('_MONTH_SHORT','Gen Feb Mar Apr Mag Giu Lug Ago Set Ott Nov Dic');
define('_TIME', 'Ora');
define('_TIMEBRIEF','%I:%M %p');
define('_SECOND', 'secondo');
define('_SECONDS', 'secondi');
define('_MINUTE', 'minuto');
define('_MINUTES', 'minuti');
define('_HOUR', 'ora');
define('_HOURS', 'ore');
define('_DAY', 'giorno');
define('_DAYS', 'giorni');
define('_WEEK', 'settimana');
define('_WEEKS', 'settimane');
define('_WEEKDAYS', 'giorni della settimana');
define('_MONTH', 'mese');
define('_MONTHS', 'mesi');
define('_YEAR', 'anno');
define('_YEARS', 'anni');

// common words
define('_ALL','Tutti');
define('_AND','e');
define('_BY','da');
define('_DOWN','Down');
define('_NO','No');
define('_OF','off');
define('_OK','OK');
define('_ON','on');
define('_TO','A');
define('_THE', 'Il');
define('_UP','Up');
define('_URL', 'URL');
define('_YES','Sì');

// common actions
define('_ACTIVATE','Attiva');
define('_ADD','Aggiungi');
define('_CANCEL', 'Cancella');
define('_COMMIT', 'Aggiorna');
define('_CREATE', 'Crea');
define('_DEACTIVATE','Disattiva');
define('_DELETE','Cancella');
define('_EDIT','Modifica');
define('_ENABLE','Abilita');
define('_HELP', 'Aiuto');
define('_IGNORE','Ignora');
define('_LOGIN','Login');
define('_LOGOUT','Logout');
define('_MODIFY','Modifica');
define('_NEW','Nuovo');
define('_SEARCH', 'Cerca');
define('_SAVE', 'Salva');
define('_SUBMIT','Submit');
define('_UPDATE', 'Aggiorna');
define('_VIEW', 'Vedi');
define('_APPLY', 'Applica modifiche');
define('_WARNING', 'Attenzione');
define('_LOAD', 'Load');
define('_RETRY', 'Retry');

//FreeNAS common words
define('_SIZE', 'Dimensione');
define('_STATUS', 'Stato');
define('_TYPE', 'Tipo');
define('_CONFIGURING', 'Configurando');
define('_DISK', 'disco');
define('_DISKS', 'Dischi');
define('_PARTITION', 'Partizione');
define('_FILESYSTEM', 'File system');
define('_DESC', 'Descrizione');
define('_SOFTRAID', 'Software RAID');
define('_SERVICE','Servizio');
define('_SERVICES','Servizi');
define('_TCPPORT','porta TCP');
define('_CONFIRMATION','Confirmation');
define('_NOTE','Nota');
define('_SUCCESSFUL', 'Successful');
define('_FAILED', 'Failed');
define('_ERROR', 'Error');

// common messages
define('_MESSAGES_SUCCESS', 'I cambiamenti sono stati applicati con successo.');
define('_MESSAGES_SUCCESSWITHREBOOT', 'I cambiamenti sono stati salvati. E\' necessario <a href=\"/reboot.php\">riavviare</a> il NAS affinché i cambiamenti abbiano effetto.');
define('_MESSAGES_ERROR', 'ERRORE: non è stato possibile applicare i cambiamenti (error code '.$ok.').');

define('_MSGVALIDIP','Deve essere specificato un indirizzo IP valido.');
define('_MSGVALIDNETBIT','Deve essere specificata una netmask valida.');
define('_MSGVALIDTCPPORT','Il numero della porta TCP deve essere un numero valido.');
define('_MSGVALIDPASSWORD','La password non coincide.');

define('_MSGSCHED_TEXT','Nota: Ctrl-click (o command-click per i Mac) per selezionare e deselezionare minuti, ore, giorni e mesi.');

// states
define('_ACTIVE','Attivo');
define('_INACTIVE','Inattivo');
define('_SELECTED','Selected');

// Webgui layout
define('_MENULEFT_SYSTEM','Sistema');
define('_MENULEFT_SYSGENSETUP','Setup generale');
define('_MENULEFT_SYSSTATROUTES','Routes statiche');
define('_MENULEFT_SYSFIRMWARE','Firmware');
define('_MENULEFT_SYSADVANCED','Avanzato');
define('_MENULEFT_INTERFACES','Interfacce');
define('_MENULEFT_INTASSIGN','Assegna');
define('_MENULEFT_INTLAN','LAN');
define('_MENULEFT_DISKS',_DISKS);
define('_MENULEFT_DISKMANAG','Amministrazione');
define('_MENULEFT_DISKMNGSOFTRAID',_SOFTRAID);
define('_MENULEFT_DISKMNGMOUNTPOINT','Punto di Mount');
define('_MENULEFT_SERVICES','Servizi');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','Accesso');
define('_MENULEFT_ACUSERGROUPS','Utenti e Gruppi');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS',_STATUS);
define('_MENULEFT_STSYSTEM','Sistema');
define('_MENULEFT_STPROCESS','Processo');
define('_MENULEFT_STINTERFACES','Interfacce');
define('_MENULEFT_STDISKS',_DISKS);
define('_MENULEFT_STWIRELESS','Wireless');
define('_MENULEFT_EXTENSIONS','Estensioni');
define('_MENULEFT_DIAGNOSTICS','Diagnostica');
define('_MENULEFT_DIAGLOGS','Logs');
define('_MENULEFT_DIAGINF','Informazioni');
define('_MENULEFT_DIAGPINGTRACE','Ping/Traceroute');
define('_MENULEFT_DIAGARPTAB','ARP table');
define('_MENULEFT_DIAGEXEC','Command');
define('_MENULEFT_CMDBACKUPRESTORE','Backup/Restore');
define('_MENULEFT_CMDFACTORYDEF','Defaults originali');
define('_MENULEFT_CMDREBOOTSYS','Riavvio sistema');
define('_MENULEFT_CMDSHUTDOWNSYS','Spegnimento sistema');
define('_MENULEFT_GRAPH','Graph');

// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION',_MENULEFT_SYSGENSETUP);
define('_SYSTEMPHP_HOSTNAME','Hostname');
define('_SYSTEMPHP_HOSTNAMETEXT','nome dell\'host NAS, senza la parte del dominio<br>es. <em>nas');
define('_SYSTEMPHP_DOMAIN','Dominio');
define('_SYSTEMPHP_DOMAINTEXT','es. <em>mycorp.com</em>');
define('_SYSTEMPHP_DNS','Servers DNS');
define('_SYSTEMPHP_DNSTEXT','indirizzi IP');
define('_SYSTEMPHP_USERNAME','Username');
define('_SYSTEMPHP_USERNAMETEXT','Se si desidera cambiare lo username per l\'accesso tramite la webGUI, inserirlo qui.');
define('_SYSTEMPHP_PASSWORD','Password');
define('_SYSTEMPHP_PASSWORDTEXT','Se si desidera cambiare la password per l\'accesso tramite la webGUI, inserirla qui due volte.<br>Non utilizzare il carattere :');
define('_SYSTEMPHP_WEBGUIPROTOCOL','Protocollo WebGUI');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','');
define('_SYSTEMPHP_WEBGUIPORT','Porta WebGUI');
define('_SYSTEMPHP_WEBGUIPORTTEXT','Inserite qui sopra un numero di porta personalizzato per la webGUI se volete cambiare i valori di default (80 per HTTP, 443 per HTTPS).');
define('_SYSTEMPHP_LANGUAGE','Linguaggio');
define('_SYSTEMPHP_LANGUAGETEXT','Selezionate il linguaggio per il sistema');
define('_SYSTEMPHP_TIMEZONE','Fuso orario');
define('_SYSTEMPHP_TIMEZONETEXT','Selezionate la località più vicina a voi');
define('_SYSTEMPHP_TIMEUPDATEINT','Intervallo di aggiornamento dell\'ora');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','Minuti tra le sincronizzazioni dell\'ora via rete; 300	raccomandati, o 0 per disabilitare');
define('_SYSTEMPHP_NTPSERVER','NTP time server');
define('_SYSTEMPHP_NTPSERVERTEXT','Utilizzare uno spazio per separate hosts multipli (solo uno è richesto). Ricordarsi di definire almeno un server DNS se qui inserite un nome di host!');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','Informazioni sul sistema');
define('_INDEXPHP_NAME','Nome');
define('_INDEXPHP_VERSION','Versione');
define('_INDEXPHP_OSVERSION','Versione OS');
define('_INDEXPHP_PLATFORM','Piattaforma');
define('_INDEXPHP_DATE','Data');
define('_INDEXPHP_UPTIME','Uptime');
define('_INDEXPHP_LASTCHANGE','Ultima modifica della configurazione');
define('_INDEXPHP_MEMUSE','Utilizzo della memoria');
define('_INDEXPHP_LOADAVERAGE','Carico medio');
define('_INDEXPHP_SHOWPROCESSING','mostra informazioni sui processi');

// Status Processes Module
// file: status_processes.php
define('_STATSPROCPHP_NAME',_STATUS);
define('_STATSPROCPHP_NAME_DESCRIPTION','Processi');
define('_STATSPROCPHP_PROC_INF','Informazioni sui processi');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION',_MENULEFT_SYSSTATROUTES);
define('_SYSTEMROUTESPHP_MSG_CHANGED','La configurazione della route statica è stata cambiata.<br>E\' necessario applicare i cambiamenti affiché abbiano effetto.');
define('_SYSTEMROUTESPHP_INT','Interfacce');
define('_SYSTEMROUTESPHP_NET','Network');
define('_SYSTEMROUTESPHP_GW','Gateway');
define('_SYSTEMROUTESPHP_DESC',_DESC);
define('_SYSTEMROUTESPHP_EDITROUTE','Modifica Route');
define('_SYSTEMROUTESPHP_MSGCONFIRM','Si vuole realmente cancellare questa route?');
define('_SYSTEMROUTESPHP_DELROUTE','Cancella Route');
define('_SYSTEMROUTESPHP_ADDROUTE','Aggiungi Route');

// Static Routes Edit Module
// file: system_routes_edit.php
define('_SYSTEMROUTESPHP_MSGVALID','Deve essere indicata una rete di destinazione valida.');
define('_SYSTEMROUTESPHP_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SYSTEMROUTESPHP_MSGVALIDGW','Deve essere indicato un indirizzo IP di gateway valido.');
define('_SYSTEMROUTESPHP_MSGROUTEEXIST','Una route per questa rete di destinazione già esiste.');
define('_SYSTEMROUTESPHP_CHOOSEINT','Scegli a quale interfaccia applicare questa route.');
define('_SYSTEMROUTESPHP_DESTNET','Rete di destinazione');
define('_SYSTEMROUTESPHP_DESTNETROUTE','Rete di destinazione per questa route statica');
define('_SYSTEMROUTESPHP_GWTONET','Gateway da utilizzare per raggiungere la rete di destinazione');
define('_SYSTEMROUTESPHP_DESCREF','Inserire una descrizione come promemoria (non considerata).');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMFIRMWAREPHP_NAMEDESC',_MENULEFT_SYSFIRMWARE);
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','Il file immagine caricato non è per questa piattaforma');
define('_SYSTEMFIRMWAREPHP_OUTMEM','Upload dell\'immagine fallito (memoria esaurita?)');
define('_SYSTEMFIRMWAREPHP_INVSIG','La firma digitale di questa immagine non è valida.');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','Questa immagine non ha una firma digitale.');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','C\'è stato un errore nella verifica della firma digitale di questa immagine.');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','Il file immagine è corrotto');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','Il firmware è stato installato. FreeNAS si riavvierà automaticamente.');
define('_SYSTEMFIRMWAREPHP_NOTSUP','L\'upload del firmware non è supportato su questa piattaforma.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','Questo significa che l\'immagine che avete caricato ' .
		 	'non è una immagine ufficiale/supportata e può causare comportamenti inattesi o problemi di ' .
			'sicurezza. Installare solo immagini provenienti da fonti fidate, e accertatevi che '.
			'l\'immagine non sia stata alterata.<br><br>'.
			'Volete installare quest\'immagine comunque (a vostro proprio rischio)?');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','Clicca &quot;Abilita upload firmware&quot;
                         sotto, e scegliere il file immagine '.$g['fullplatform'].'-*.img)
			 da caricare.<br>Clicca &quot;Aggiorna firmware&quot; 
          per iniziare il processo di upgrade.');			
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','Abilita upload firmware');	
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','Disabilita upload firmware');		
define('_SYSTEMFIRMWAREPHP_FIRMFILE','File immagine del firmware');	
define('_SYSTEMFIRMWAREPHP_UPFIRM','Aggiorna firmware');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','E\'necessario riavviare il sistema prima di aggiornare il firmware.');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','Non interrompere l\'aggiornamento del firmware
			una volta iniziato. FreeNAS riavvierà automaticamente il sistema
			dopo aver installato il firmware. La configurazione sarà mantenuta.<br>
			Sono necessari almeno 128 Mb di RAM per l\'aggiornamento del firmware.');


// System Advanced Module
// file: system_advanced.php
define('_SYSTEMADVANCEDPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMADVANCEDPHP_NAMEDESC',_MENULEFT_SYSADVANCED);
define('_SYSTEMADVANCEDPHP_MSGVALIDTCP','TCP idle timeout deve essere un intero.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERTKEY','Certificato e chiave devono sempre essere specificati insieme.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERT','Questo certificato non sembra essere valido.');
define('_SYSTEMADVANCEDPHP_MSGVALIDKEY','Questa chiave non sembra essere valida.');
define('_SYSTEMADVANCEDPHP_NOTE','Le opzioni su questa pagina sono esclusivamente ad uso degli utenti esperti e per esse <strong>NON</strong> c\'è supporto.');
define('_SYSTEMADVANCEDPHP_WEBGUISSLKEY','webGUI SSL certificato\/chiave');
define('_SYSTEMADVANCEDPHP_WEBCERT','Certificate');
define('_SYSTEMADVANCEDPHP_WEBCERTTEXT','Incolla qui un certificato firmato in formato X.509 PEM.');
define('_SYSTEMADVANCEDPHP_WEBKEY','Chiave');
define('_SYSTEMADVANCEDPHP_WEBKEYTEXT','Incolla qui una chiave privata RSA in formato PEM.');
define('_SYSTEMADVANCEDPHP_MISC','Miscellanea');
define('_SYSTEMADVANCEDPHP_CON','Console menu');
define('_SYSTEMADVANCEDPHP_DISCON','Disabilita console menu');
define('_SYSTEMADVANCEDPHP_CONTEXT','I cambiamenti a questa opzione avranno effetto dopo il riavvio.');
define('_SYSTEMADVANCEDPHP_FIRM','Verifica versione firmware');
define('_SYSTEMADVANCEDPHP_DISFIRM','Disabilita verifica versione firmware');
define('_SYSTEMADVANCEDPHP_FIRMTEXT','Questo farà sì che FreeNAS non verifichi le nuove versioni del firmware quando si visualizza la pagina <a href=\"system_firmware.php\">Sistema: Firmware</a>.');
define('_SYSTEMADVANCEDPHP_TCPIDLE','TCP idle timeout');
define('_SYSTEMADVANCEDPHP_TCPIDLETEXT','Le connessioni TCP in idle saranno rimosse della tabella di stato dopo che nessun pacchetto è stato ricevuto per il numero di secondi specificato. Non imporlo troppo alto o la tabella di stato si riempirà di connessioni che sono state chiuse in modo improprio. Il valore di default e di 2.5 ore.');
define('_SYSTEMADVANCEDPHP_NAV','Navigazione');
define('_SYSTEMADVANCEDPHP_NAVTEXT','Mantieni aperto il menù diagnostica nella barra di navigazione');
define('_SYSTEMADVANCEDPHP_BEEP','Beep di sistema');
define('_SYSTEMADVANCEDPHP_DISBEEP','Disabilita il beep dello speaker all\'avvio o allo spegnimento');
define('_SYSTEMADVANCEDPHP_SMART','Demone S.M.A.R.T');
define('_SYSTEMADVANCEDPHP_ENSMART','Abilita il demone S.M.A.R.T');
define('_SYSTEMADVANCEDPHP_SMARTTEXT','Controlla la periferica S.M.A.R.T loggando lo stato nel file di log.');
define('_SYSTEMADVANCEDPHP_TUNING','Ottimizzazione');
define('_SYSTEMADVANCEDPHP_ENTUNING','Abilita l\'ottimizzazione di alcune variabili del kernel');
define('_SYSTEMADVANCEDPHP_ZEROCONF','ZeroConf');
define('_SYSTEMADVANCEDPHP_DISZEROCONF','Disabilita annunci del servizio ZeroConf');

// Interface assing:
// file: interfaces_assign.php
define('_INTASSIGNPHP_NAME','Interfacce');
define('_INTASSIGNPHP_NAMEDESC','Assegna porte di rete');
define('_INTASSIGNPHP_MSGVALIDPORT','Porta ');
define('_INTASSIGNPHP_MSGVALIDPORTWASASSIG',' era assegnata a ');
define('_INTASSIGNPHP_MSGVALIDINT',' interfacce:');
define('_INTASSIGNPHP_INT','Interfaccia');
define('_INTASSIGNPHP_PORT','Porta di rete');
define('_INTASSIGNPHP_DELINT','cancella interfaccia');
define('_INTASSIGNPHP_ADDINT','aggiungi interfaccia');
define('_INTASSIGNPHP_INTTEXT','Dope aver premuto &quot;Salva&quot;, è neccessario riavviare FreeNAS affinché le modifiche abbiano effetto. E\' possibile anche che per accedere nuovamente al server FreeNAS sia necessario effettuare uno o più dei passi seguenti: </span></p>
<ul>
  <li><span class=\"vexpl\">cambiare l\'indirizzo IP del vostro computer</span></li>
  <li><span class=\"vexpl\">accedere alla webGUI con il nuovo indirizzo IP</span></li>
</ul>');

// Interface LAN/OPT:
// file: interfaces_lan.php
// file: interfaces_opt.php
define('_INTLANPHP_NAME','Interfacce');
define('_INTLANPHP_NAMEDESC','LAN');
define('_INTPHP_MSGVALIDIP',_MSGVALIDIP);
define('_INTPHP_MSGVALIDMASK',_MSGVALIDNETBIT);
define('_INTPHP_MSGVALIDGW','Deve essere specificato un gateway valido.');
define('_INTPHP_MSGVALIDMTU','Deve essere specificata una dimensione di mtu valida.');
define('_INTPHP_IP','IP indirizzo');
define('_INTPHP_GW','Gateway');
define('_INTPHP_MTU','MTU');
define('_INTPHP_MTUTEXT','Lo standard MTU è 1500, utilizzare 9000 per i frame jumbo.');
define('_INTPHP_POLLING','Device polling');
define('_INTPHP_ENPOLLING','Abilita il device polling');
define('_INTPHP_POLLINGTEXT','Il Device polling è una tecnica che consente al sistema di dialogare con le interfacce
                    di rete per nuovi dati anziché affidarsi agli interrupts. Ciò può ridurre il carico della CPU
                    e quindi aumentare il flusso di dati a scapito di un piccolo ritardo di inoltro (le interfacce
                    sono interrogate 1000 volte al secondo). Non tutte le NICs supportano il polling;
                    vedere la homepage di m0n0wall per una lista delle schede supportate.');
define('_INTPHP_SPEED','Velocità');
define('_INTPHP_DUPLEX','Duplex');
define('_INTPHP_TEXT','dopo aver premuto &quot;Salva&quot;, è possibile che per accedere
                    nuovamente al server FreeNAS sia necessario effettuare uno o più dei
                    passi seguenti: 
                    <ul>
                      <li>cambiare l\'indirizzo IP del vostro computer</li>
                      <li>accedere alla webGUI con il nuovo indirizzo</li>
                    </ul>');
define('_INTPHP_ENOPT','Abilita Opzionale ');
define('_INTPHP_ENOPTINT',' interfaccia');
define('_INTPHP_DESC',_DESC);
define('_INTPHP_DESCTEXT','Inserire qui una descrizione (nome) per l\'interfaccia.');
define('_INTPHP_STATIC','Static IP configuration');
define('_INTPHP_DHCP','DHCP client configuration');
define('_INTPHP_GENERAL','General configuration');
define('_INTPHP_DHCPHOSTNAME','Hostname');
define('_INTPHP_DHCPHOSTNAMETEXT','The value in this field is sent as the DHCP hostname when requesting a DHCP lease.');
define('_INTPHP_DHCPCLIENTIDENTIFIER','Client Identifier');
define('_INTPHP_DHCPCLIENTIDENTIFIERTEXT','The value in this field is sent as the DHCP client identifier when requesting a DHCP lease.');
define('_INTPHP_CALCNETMASKBITS','Calculate netmask bits');

// Disks: Manage
// Common text
define('_DISKSPHP_NAME',_DISKS);
define('_DISKSPHP_MANAGE','Gestione');
define('_DISKSPHP_FORMAT','Formatta');
define('_DISKSPHP_TOOLS','Tools');
define('_DISKSPHP_ISCSIINIT','iSCSI initiator');
define('_DISKSPHP_DISK',_DISK);
define('_DISKSPHP_FILESYSTEM',_FILESYSTEM);

// file: disks_manage.php
define('_DISKSMANAGEPHP_NAMEDESC','Amministrazione');
define('_DISKSMANAGEPHP_MSGCHANGED','La lista dei dischi è stata cambiata.<br>E\' necessario applicare i cambiamenti affinché abbiano effetto.');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','Tempo per standby');
define('_DISKSMANAGEPHP_FSUNKNOWN','sconosciuto o non formattato');
define('_DISKSMANAGEPHP_EDIT','edita disco');
define('_DISKSMANAGEPHP_DEL','cancella disco');
define('_DISKSMANAGEPHP_DELCONF','Si vuole realmente cancellare questo disco? Tutti gli elementi che ancora lo utilizzano non saranno più validi (es. share)!');
define('_DISKSMANAGEPHP_ADD','aggiungi disco');
define('_DISKSMANAGEPHP_NOTE','Primo passo della configurazione: Aggiungere un hard drive alla lista dei dischi.');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','Esiste già questo disco nella lista dei dischi.');
define('_DISKSMANAGEEDITPHP_UDMA','UDMA mode');
define('_DISKSMANAGEEDITPHP_UDMATEXT','E\' possibile forzare l\'UDMA mode se il vostro harddrive vi dà messeggi del tipo "UDMA_ERROR.... LBA".');
define('_DISKSMANAGEEDITPHP_STANDBY','Tempo per lo standby dell\'hard disk');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','Mette l\'hard disk in standby quando è passata la quantità di tempo selezionata
                    dall\'ultimo accesso.
                    <em>Non settarlo per le schede CF.</em>');
define('_DISKSMANAGEEDITPHP_APM','Advanced Power Management');
define('_DISKSMANAGEEDITPHP_APMTEXT','Questo consente di diminuire il consumo dei dischi a scapito delle prestazioni.
                    <em>Non settarlo per le schede CF.</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','acoustic level');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','Questo consente di settare il livello acustico dei dischi mentre sono operativi.
                    <em>Non settarlo per le schede CF.</em>');
define('_DISKSMANAGEEDITPHP_PREFS','FS preformattato');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','Questo consente di settare il tipo di FS per dischi preformattati con dati.<br>
                    <em>Lasciare "non formattato" per dischi non formattati e poi utilizzare il menù Formatta.</em>');

//file: disks_manage_init.php
define('_DISKSMANAGEINITPHP_NAMEDESC','Inizializza');
define('_DISKSMANAGEINITPHP_DISKMOUNTERROR','The disk is currently mounted! <a href=%s>Unmount</a> this disk first before proceeding.');
define('_DISKSMANAGEINITPHP_INITTEXT','<strong>Dettagli dell\'inizializzazione del disco:</strong>');
define('_DISKSMANAGEINITPHP_INITINFO','\"fdisk: Geom not found\"non è un messaggio di errore!\n');
define('_DISKSMANAGEINITPHP_TEXT','Questo passo cancellerà tutte le partizioni, creerà la partizione numero 1 e
                    formatterà l\'hard drive con il file system specificato.');
define('_DISKSMANAGEINITPHP_FORMATDISC','Format disk');
define('_DISKSMANAGEINITPHP_FORMATDISCCONF','Do you really want to format this disk? All data will be lost!');
define('_DISKSPHP_NOMBR','Don\'t Erase MBR');
define('_DISKSPHP_NOMBRTEXT','don\'t erase the MBR (useful for some RAID controller cards)');

//file: disks_manage_tools.php
define('_DISKSMANAGETOOLS_NAMEDESC','Tools');
define('_DISKSMANAGETOOLS_COMMAND','Command');
define('_DISKSMANAGETOOLS_SENDCMD','Send Command!');
define('_DISKSMANAGETOOLS_RAIDDISKNOTE','Use <a href="%s">RAID tools</a> for this disk!');
define('_DISKSMANAGETOOLS_CMDINFO','Command output:');
define('_DISKSMANAGETOOLS_MOUNTNOTE','The disk is currently mounted! The mount point will be removed temporary to perform selected command.');
define('_DISKSMANAGETOOLS_UMOUNT','Unmount disk/partition');
define('_DISKSMANAGETOOLS_UMOUNTTEXT','If the selected disk/partition is mounted it will be unmounted temporary to perform selected command, otherwise the commands work in read-only mode.');

//file: disks_manage_iscsi.php
define('_DISKSMANAGEPHP_NAMEDESC','Amministrazione');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','iSCSI Initiator');
define('_DISKSMANAGEISCSIPHP_TARGETIP','Indirizzo IP target');
define('_DISKSMANAGEISCSIPHP_TARGETIPTEXT','Indirizzo IP del target iSCSI.');
define('_DISKSMANAGEISCSIPHP_TARGETNAME','nome target');
define('_DISKSMANAGEISCSIPHP_TARGETNAMETEXT','nome target.');

//Software RAID
// Comon value
define('_DISKSRAIDPHP_MANAGE','Amministra il RAID');
define('_DISKSRAIDPHP_FORMAT','Formatta il RAID');
define('_DISKSRAIDPHP_TOOLS','Strumenti');
define('_DISKSRAIDPHP_INFO','Informazioni');
define('_DISKSRAIDPHP_VOLUME','Nome volume');

// Software RAID common word
define('_DISKSRAIDPHP_GVINUM','Geom Vinum');
define('_DISKSRAIDPHP_GMIRROR','Geom Mirror');
define('_DISKSRAIDPHP_UNSTABLE','(instabile)');

//file: disks_raid.php
define('_DISKSRAIDPHP_NAMEDESC','RAID');
define('_DISKSRAIDPHP_MSGCHANGED','La configurazione del Raid è stata cambiata.<br>E\' necessario applicare i cambiamenti affinché abbiano effetto.');
define('_DISKSRAIDPHP_TYPE','Tipo');
define('_DISKSRAIDPHP_EDITRAID','modifica raid');
define('_DISKSRAIDPHP_DELRAID','cancella raid');
define('_DISKSRAIDPHP_DELCONF','Si vuole realmente cancellare questo volume raid? Tutti gli elementi che ancora lo utilizzano non saranno più validi (es. share)!');
define('_DISKSRAIDPHP_NOTE','Passo di configurazione facoltativo: Configurare un disco RAID virtuale utilizzando <a href=\"disks_manage.php\">i dischi precedentemente configurati</a>.<br>Attendete lo stato \"up\" prima di formattarli e montarli!');

//file: disks_raid_edit.php
define('_DISKSRAIDEDITPHP_NAMEDESC',_EDIT);
define('_DISKSRAIDEDITPHP_MSGADDDISKFIRST','E\' necessario prima aggiungere i dischi.');
define('_DISKSRAIDEDITPHP_MSGVALIDNAME','Il nome della device può essere formato solo dai caratteri a-z, A-Z, 0-9.');
define('_DISKSRAIDEDITPHP_MSGEXIST','Questa device esiste già nella lista del volume raid.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID0','Il numero minimo di dischi in un volume RAID 0 è di 2.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID1','Ci devono essere 2 dischi in un volume RAID 1.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID5','Il numero minimo di dischi in un volume RAID 5 è di 3.');
define('_DISKSRAIDEDITPHP_MSGERROR','ERR Non è possibile salvare la configurazione RAID.\n');
define('_DISKSRAIDEDITPHP_RAIDNAME','Nome raid');
define('_DISKSRAIDEDITPHP_GVBALANCEALGO','Algoritmo di bilanciamento');
define('_DISKSRAIDEDITPHP_GVBALANCEALGOTEXT','Selezionare un algoritmo di bilanciamento.');
define('_DISKSRAIDEDITPHP_RAID0','striping');
define('_DISKSRAIDEDITPHP_RAID1','mirroring');
define('_DISKSRAIDEDITPHP_RAID5','rotated block-interleaved parity');
define('_DISKSRAIDEDITPHP_MEMBERS','Membri di questo volume');


//file: disks_raid_info.php

define('_DISKSRAIDINFOPHP_TEXT','Informazioni e stato software RAID');

//file: disks_raid_init.php

define('_DISKSRAIDINITPHP_INFO','Output formattazione disco UFS:');
define('_DISKSRAIDINITPHP_TEXT','Questo passo formatterà il volume RAID con lo Unix FileSystem (UFS).');
define('_DISKSRAIDINITPHP_FORMAT','Formatta disco con UFS!');

//file: disks_raid_tools.php
define('_DISKSRAIDTOOLSPHP_OBJNAME','Nome oggetto');
define('_DISKSRAIDTOOLSPHP_COMMAND','Comando');
define('_DISKSRAIDTOOLSPHP_INFO','Output comando GVINUM:');
define('_DISKSRAIDTOOLSPHP_SENDCMD','Lancia comando!');
define('_DISKSRAIDTOOLSPHP_TEXT','1. Usare queste azioni speciali solo per debug!<br>2. Non c\'è ragione di usare questo menù per inizializzare un volume RAID (parte automaticamente).');

//Disk Mount Point
// Comon value
define('_DISKSMOUNTPHP_NAME','Punto di Mount');
define('_DISKSMOUNTPHP_SHARENAME','Nome Share');
define('_DISKSMOUNTPHP_MANAGE','Manage');
define('_DISKSMOUNTPHP_TOOLS','Tools');

// disks_mount.php
define('_DISKSMOUNTPHP_MSGCHANGED','La lista dei punti di mount è stata cambiata.<br>E\' necessario applicare i cambiamenti affinché abbiano effetto.');
define('_DISKSMOUNTPHP_NOTE','Secondo passo di configurazione: Dichiarare il filesystem usato dal vostro <a href="disks_manage.php">disco precedentemente configurato');
define('_DISKSMOUNTPHP_DELCONF','Si vuole realmente cancellare questo punto di mount? Tutti gli elementi che ancora lo utilizzano non saranno più validi (es. share)!');
define('_DISKSMOUNTPHP_DEL','cancella mount');
define('_DISKSMOUNTPHP_ADD','aggiungi mount');

// disks_mount_edit.php
define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','Il nome della share può essere formato solo dai caratteri a-z, A-Z, 0-9, _ , -.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','La descrizione contiene caratteri non validi.');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','Non riesco a montare la partizione 1, la partizione DATA è la 2.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','Nome Share Duplicato.');
define('_DISKSMOUNTEDITPHP_TEXT','1. Non è possibile montare la partizione \'<?php echo htmlspecialchars($cfdevice);?>\' dov\'è contenuto il file di configurazione<br>2. Il supporto NTFS di FreeBSD ha molti bachi.');

// disks_mount_tools.php
define('_DISKSMOUNTTOOLS_COMMAND','Command');
define('_DISKSMOUNTTOOLS_SENDCMD','Send Command!');
define('_DISKSMOUNTTOOLS_CMDINFO','Command output:');
define('_DISKSMOUNTTOOLS_MOUNTTEXT','Mounting...');
define('_DISKSMOUNTTOOLS_UMOUNTTEXT','Unmounting...');

// Service: CIFS/SAMBA
// file: services_samba.php
define('_SRVCIFS_NAMEDESC','CIFS');
define('_SRVCIFS_MSGVALIDNETBIOSNAME','Il nome Netbios contiene caratteri non validi.');
define('_SRVCIFS_MSGVALIDWORKGOUP','Il nome del Workgroup contiene caratteri non validi.');
define('_SRVCIFS_MSGVALIDWINS','Il server WINS deve essere un indirizzo IP.');
define('_SRVCIFS_MSGVALIDSNDBUFF','Il valore del buffer SND deve essere numerico.');
define('_SRVCIFS_MSGVALIDRCVBUFF','Il valore del buffer RCV deve essere numerico.');
define('_SRVCIFS_SHARE','CIFS share');
define('_SRVCIFS_AUTH','Autenticazione');
define('_SRVCIFS_NETBIOSNAME','Nome NetBios');
define('_SRVCIFS_WORKGROUP','Workgroup');
define('_SRVCIFS_WORKGROUPTEXT','Workgroup del quale essere membri.');
define('_SRVCIFS_DESCTEXT','Descrizione del server. Questa solitamente può essere vuota.');
define('_SRVCIFS_DOSCHARSET','Set caratteri Dos');
define('_SRVCIFS_UNIXCHARSET','Set caratteri Unix');
define('_SRVCIFS_LOGLEVEL','Livello di Log');
define('_SRVCIFS_LOCALMASTER','Local Master Browser');
define('_SRVCIFS_LOCALMASTERTEXT','Consenti a FreeNAS di provare e di diventare un master browser.');
define('_SRVCIFS_TIMESRV','Time server');
define('_SRVCIFS_TIMESRVTEXT','FreeNAS si propone come un time server ai clients Windows.');
define('_SRVCIFS_WINS','Server WINS');
define('_SRVCIFS_WINSTEXT','Indirizzo IP server WINS.');
define('_SRVCIFS_HIDE','Share da nascondere.');
define('_SRVCIFS_HIDETEXT','Selezionare la share CIFS da nascondere alla navigazione.');
define('_SRVCIFS_SNBBUFF','Dimensione Buffer Invio');
define('_SRVCIFS_SNBBUFFTEXT','Dimensione del buffer di invio (8192 di default).');
define('_SRVCIFS_RCVBUFF','Dimensione Buffer Ricezione');
define('_SRVCIFS_RCVBUFFTEXT','Dimensione del buffer di ricezione (8192 di default).');

// Service: FTP
// file: services_ftp.php
define('_SRVFTP_NAMEDESC','FTP');
define('_SRVFTP_MSGVALIDPORT',_MSGVALIDTCPPORT);
define('_SRVFTP_MSGVALIDMAXCLIENT','Il massimo numero di client deve essere numerico.');
define('_SRVFTP_MSGVALIDMAXIP','Il max con per ip deve essere numerico.');
define('_SRVFTP_MSGVALIDIDLE','Il max tempo di idle deve essere numerico.');
define('_SRVFTP_MSGVALIDPASVIP','L\'indirizzo pasv deve essere un indirizzo IP pubblico.');
define('_SRVFTP_MSGVALIDPASVMAX','La porta pasv_max_port deve essere un numero di porta valido.');
define('_SRVFTP_MSGVALIDPASVMIN','La porta pasv_min_port deve essere un numero di porta valido.');
define('_SRVFTP_FTPSERVER','Server FTP');
define('_SRVFTP_TCP',_TCPPORT);
define('_SRVFTP_MAXCLIENT','Numero di clients');
define('_SRVFTP_MAXCLIENTTEXT','Numero massimo di clients simultaneo.');
define('_SRVFTP_MAXIP','Max conn per ip');
define('_SRVFTP_MAXIPTEXT','Massimo di connessioni per indirizzo IP.');
define('_SRVFTP_TIMEOUT','Timeout');
define('_SRVFTP_TIMEOUTTEXT','Massimo tempo di idle in secondi.');
define('_SRVFTP_ANONYMOUS','Abilita Anonimo');
define('_SRVFTP_ANONYMOUSTEXT','Accetta connessioni anonime.');
define('_SRVFTP_AUTH','Abilita utenti autenticati');
define('_SRVFTP_AUTHTEXT','Accetta connessioni da utenti autenticati.');
define('_SRVFTP_PASVIP','Indirizzo IP passivo');
define('_SRVFTP_PASVIPTEXT','Usa questa opzione per imporre l\'indirizzo IP che il demone FTP pubblica 
                    in risposta al comandi PASV.');
define('_SRVFTP_PASVMIN','pasv_min_port');
define('_SRVFTP_PASVMINTEXT','La porta minima da assegnare per le connessioni dati di tipo PASV.');
define('_SRVFTP_PASVMAX','pasv_max_port');
define('_SRVFTP_PASVMAXTEXT','La porta massima da assegnare per le connessioni dati di tipo PASV.');
define('_SRVFTP_BANNER','Banner');
define('_SRVFTP_BANNERTEXT','Banner di benvenuto mostrato da FTP quando si instaura una connessione.');

// Service: NFS
// file: services_nfs.php
define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','Deve essere specificata una rete valida.');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','Server NFS');
define('_SRVNFS_MAPALLROOT','mappa tutti gli utenti come root');
define('_SRVNFS_MAPALLROOTTEXT','Tutti gli utenti avranno i privilegi di root.');
define('_SRVNFS_AUTHNET','Rete autorizzata');
define('_SRVNFS_AUTHNETTEXT','Rete che è autorizzata all\'accesso alla share NFS');
define('_SRVNFS_TEXT','Il nome delle directories esportate è : /mnt/sharename');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','Server');
define('_SRVRYNC_CLIENT','Client');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','Demone Rsync');
define('_SRVRYNCD_READONLY','Sola lettura');
define('_SRVRYNCD_MAPUSER','Mappa come utente');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_TCPORTTEXT','Porta TCP alternativa.');
define('_SRVRYNCD_MAXCON','Maximum connections');
define('_SRVRYNCD_MAXCONTEXT','Maximum number of simultaneous connections. Default is 0 (unlimited).');
define('_SRVRYNCD_MOTD','MOTD');
define('_SRVRYNCD_MOTDTEXT','Message of the day.');

// file: services_rsyncd_client.php
define('_SRVRYNCC_MSGMPFIRST','E\'necessario prima configurare un punto di mount.');
define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
define('_SRVRYNCC_RSYNCC','Sincronizzazione client Rsync');
define('_SRVRYNCC_REMOTESERVER','Server RSYNC Remoto');
define('_SRVRYNCC_REMOTEID','Indirizzo IP del server RSYNC remoto');
define('_SRVRYNCC_OPTIONS','Opzioni RSYNC');
define('_SRVRYNCC_OPTDEL','Cancella files che no esistono più sul sorgente.');
define('_SRVRYNCC_SHARES','Shares da sincronizzare');
define('_SRVRYNCC_SHARESTEXT','Questa stessa share locale deve essere presente sul Server Remoto.');
define('_SRVRYNCC_TIME','Tempo di Sincronizzazione');
define('_SRVRYNCC_TEXT',_MSGSCHED_TEXT);

// file: services_rsyncd_local.php
define('_SRVRYNCL_MSGVALID','You can\'t have the same mount point for source and destination!');
define('_SRVRYNCC_RSYNCL','Rsync Local Synchronization');
define('_SRVRYNCL_SOURCE','Source');
define('_SRVRYNCL_SOURCETEXT','Source Mount Point.');
define('_SRVRYNCL_DESTINATION','Destination');
define('_SRVRYNCL_DESTINATIONTEXT','Destination Mount Point (backup drive).');


// Service: SSHD
// file: services_sshd.php
define('_SRVSSHD_NAMEDESC','SSHD');
define('_SRVSSHD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVSSHD_MSGVALIDKEY','Questa chiave non sembra essere valida.');
define('_SRVSSHD_SSHD','Demone SSH');
define('_SRVSSHD_READONLY','Sola lettura');
define('_SRVSSHD_TCPORT',_TCPPORT);
define('_SRVSSHD_KEY','Chiave SSH');
define('_SRVSSHD_PRIVATEKEY','Chiave Privata');
define('_SRVSSHD_PRIVATEKEYTEXT','Incolla qui una CHIAVE PRIVATA DSA in formato PEM.');

// Service: Unison
// file: services_unison.php
define('_SRVUNSN_NAMEDESC','Unison');
define('_SRVUNSN_MSGVALIDWRKDIR','The combination of share and working directory does not exist.');
define('_SRVUNSN_UNISON','Unison File Synchronisation');
define('_SRVUNSN_SHARE','Share');
define('_SRVUNSN_SHAREDESC','You may need enough space to duplicate all files being synced');
define('_SRVUNSN_WORKDIR','Working Directory');
define('_SRVUNSN_WORKDIRDESC','Where the working files will be stored');
define('_SRVUNSN_MAKEDIR','Create');
define('_SRVUNSN_MAKEDIRLABEL','Create work directory if it doesn\'t exist');
define('_SRVUNSN_NOTESSH','<a href="/services_sshd.php">SSHD</a> must be enabled for Unison to work, and the <a href="/access_users.php">user</a> must have Full Shell enabled.');

// Service: AFP
// file: services_afp.php
define('_SRVAFP_NAMEDESC','AFP');
define('_SRVAFP_MSGVALIDAUTH','E\' necessario scegliere almeno un metodo di autenticazione.');
define('_SRVAFP_AFPSRV','Server AFP');
define('_SRVAFP_AFPNAME','Nome Server');
define('_SRVAFP_AUTH','Autenticazione');
define('_SRVAFP_ENABLEGUEST','Abilita accesso guest.');
define('_SRVAFP_ENABLELOCAL','Abilita autenticazione utenti locali.');

// Access
// common words
define('_ACCESS_NAME','Accesso');
define('_ACCESS_USER','Utente');
define('_ACCESS_USERS','Utenti');
define('_ACCESS_GROUP','Gruppo');
define('_ACCESS_GROUPS','Gruppi');
define('_ACCESS_FULLNAME','Nome completo');

// file: access_users.php
define('_ACCESSUSERS_MSGCHANGED','La lista degli utenti è stata cambiata.<br>E\' necessario applicare i cambiamenti affinché abbiano effetto.');
define('_ACCESSUSERS_EDITUSER','modifica utente');
define('_ACCESSUSERS_DELUSER','cancella utente');
define('_ACCESSUSERS_CONFDEL','Si vuole realmente cancellare questo utente?!');
define('_ACCESSUSERS_ADDUSER','aggiungi utente');

// file: access_users_edit.php
define('_ACCESSUSERSEDIT_MSGGROUPFIRST','è necessario creare prima un gruppo.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGIN','Il nome di login contiene caratteri non validi.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED','Il nome di login è un nome di login riservato.');
define('_ACCESSUSERSEDIT_MSGVALIDFULLNAME','Il nome completo contiene caratteri non validi.');
define('_ACCESSUSERSEDIT_MSGVALIDDUPLICATE','Questo utente esiste già nella lista degli utenti.');
define('_ACCESSUSERSEDIT_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSUSERSEDIT_LOGIN',_LOGIN);
define('_ACCESSUSERSEDIT_LOGINTEXT','Nome di login univoco dell\'utente.');
define('_ACCESSUSERSEDIT_FULLNAME',_ACCESS_FULLNAME);
define('_ACCESSUSERSEDIT_FULLNAMETEXT','Nome completo dell\'utente.');
define('_ACCESSUSERSEDIT_PASSWORD','Password');
define('_ACCESSUSERSEDIT_PASSWORDTEXT','Password utente.');
define('_ACCESSUSERSEDIT_PASSWORDCONF','Conferma Password');
define('_ACCESSUSERSEDIT_PASSWORDCONFTEXT','Conferma la password.');
define('_ACCESSUSERSEDIT_GROUPMEMBER','Membro Gruppo');
define('_ACCESSUSERSEDIT_FULLSHELL','Shell Completa');
define('_ACCESSUSERSEDIT_FULLSHELLTEXT','Concedi una shell completa all\'utente');

// file: access_users_groups.php
define('_ACCESSGROUPS_MSGCHANGED','La lista dei gruppi è stata cambiata.<br>E\'necessario applicare i cambiamenti affinché abbiano effetto.');
define('_ACCESSGROUPS_DESC',_DESC);
define('_ACCESSGROUPS_GROUPEDIT','modifica gruppo');
define('_ACCESSGROUPS_GROUPDEL','cancella gruppo');
define('_ACCESSGROUPS_GROUPDELCONF','Si vuole realmente cancellare questo gruppo?!');
define('_ACCESSGROUPS_GROUPADD','aggiungi gruppo');

// file: access_users_groups_edit.php
define('_ACCESSGROUPSEDIT_MSGVALIDNAME','Il nome del gruppo contiene caratteri non validi.');
define('_ACCESSGROUPSEDIT_MSGVALIDDESC','La desc del gruppo contiene caratteri non validi.');
define('_ACCESSGROUPSEDIT_MSGVALIDDUPLICATE','Questo gruppo esiste già nella lista dei gruppi.');
define('_ACCESSGROUPSEDIT_NAME','Nome');
define('_ACCESSGROUPSEDIT_NAMETEXT','Nome Gruppo.');
define('_ACCESSGROUPSEDIT_DESC',_DESC);
define('_ACCESSGROUPSEDIT_DESCTEXT','Descrizione gruppo.');

// file: access_ad.php

define('_ACCESSAD_NAME','Active Directory');
define('_ACCESSAD_MSGVALIDIP',_MSGVALIDIP);
define('_ACCESSAD_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSAD_ADNAME','Nome server AD');
define('_ACCESSAD_ADNAMETEXT','Nome AD o PDC.');
define('_ACCESSAD_IP','IP server AD');
define('_ACCESSAD_IPTEXT','indirizzo IP address del server MS Active Directory.');
define('_ACCESSAD_DOMAIN','Nome Dominio');
define('_ACCESSAD_DOMAINTEXT','Nome Dominio nel vecchio formato.');
define('_ACCESSAD_ADMINAME','Nome amministratore');
define('_ACCESSAD_ADMINNAMETEXT','Nome Utente di un account di amministratore del dominio.');
define('_ACCESSAD_ADMINPASS','Password di amministrazione');
define('_ACCESSAD_ADMINPASSTEXT','Password dell\'account di amministrazione del dominio, inserirla qui due volte.');

// Diagnostics

define('_DIAG_NAME','Diagnostica');

// file: shutdown.php

define('_SHUT_NAME',_MENULEFT_CMDSHUTDOWNSYS);
define('_SHUT_MSGSHUT','Il sistema sta per spegnersi. Potrebbe volerci un minuto.');
define('_SHUT_CONF','Si è sicuri di voler spegnere il sistema?');

// file: shutdown_sched.php

define('_SHUTSCHED_NAME','Spegnimento Programmato');
define('_SHUTSCHED_TIME','Orario di spegnimento');
define('_SHUTSCHED_TEXT',_MSGSCHED_TEXT);

// file: reboot.php
define('_DIAGREBOOT_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGREBOOT_NAMEDESC',_MENULEFT_CMDREBOOTSYS);
define('_DIAGREBOOT_REBOOTMSG','The system is rebooting now. This may take one minute.');
define('_DIAGREBOOT_REBOOTCONF','Are you sure you want to reboot the system?');

// file: license.php
define('_LICENSE_NAME','License');

// file: status_wireless.php
define('_STATSWIRELESS_NAME',_MENULEFT_STATUS);
define('_STATSWIRELESS_NAMEDESC','Wireless');

// file: status_interfaces.php
define('_STATSINTERFACES_NAME',_MENULEFT_STATUS);
define('_STATSINTERFACES_NAMEDESC',_INTERFACES);

// file: status_graph_cpu.php
define('_STATSGRAPHCPU_NAME',_MENULEFT_STATUS);
define('_STATSGRAPHCPU_NAMEDESC','CPU load');

// file: status_disks.php
define('_STATSDISKS_NAME',_MENULEFT_STATUS);
define('_STATSDISKS_NAMEDESC',_DISKS);
define('_STATSDISKS_DISK',_DISK);
define('_STATSDISKS_SIZE',_SIZE);
define('_STATSDISKS_DESC',_DESC);
define('_STATSDISKS_STATUS',_STATUS);

// file: interfaces_vlan.php
define('_INTVLAN_NAME',_MENULEFT_INTERFACES);
define('_INTVLAN_NAMEDESC',_INTASSIGNPHP_NAMEDESC);
define('_INTVLAN_INTERFACE','Interface');
define('_INTVLAN_VLANTAG','VLAN tag');
define('_INTVLAN_DESC',_DESC);

// file: interfaces_vlan_edit.php
define('_INTVLANEDIT_NAME',_MENULEFT_INTERFACES);
define('_INTVLANEDIT_NAMEDESC',_INTASSIGNPHP_NAMEDESC);
define('_INTVLANEDIT_EDITVLAN','Edit VLAN');

// file: diag_ping.php
define('_DIAGPING_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGPING_NAMEDESC',_MENULEFT_DIAGPINGTRACE);

// file: diag_traceroute.php
define('_DIAGTRACEROUTE_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGTRACEROUTE_NAMEDESC',_MENULEFT_DIAGPINGTRACE);
define('_DIAGTRACEROUTE_NOTE','Traceroute may take a while to complete. You may hit the Stop button on your browser at any time to see the progress of failed traceroutes.');

// file: diag_logs.php
// file: diag_logs_daemon.php
// file: diag_logs_ftp.php
// file: diag_logs_rsyncd.php
// file: diag_logs_samba.php
// file: diag_logs_settings.php
// file: diag_logs_smartd.php
// file: diag_logs_sshd.php
define('_DIAGLOGS_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGLOGS_NAMEDESC',_MENULEFT_DIAGLOGS);

// file: diag_infos.php
// file: diag_infos_ad.php
// file: diag_infos_ataidle.php
// file: diag_infos_iscsi.php
// file: diag_infos_mount.php
// file: diag_infos_part.php
// file: diag_infos_raid.php
// file: diag_infos_smart.php
// file: diag_infos_space.php
define('_DIAGINFOS_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGINFOS_NAMEDESC',_MENULEFT_DIAGINF);
define('_DIAGINFOS_DISKS',_DISKS);
define('_DIAGINFOS_PARTITIONS','Partitions');
define('_DIAGINFOS_SMART','S.M.A.R.T.');
define('_DIAGINFOS_ATAIDLE','ATAidle');
define('_DIAGINFOS_SPACEUSED','Space Used');
define('_DIAGINFOS_MOUNTS','Mounts');
define('_DIAGINFOS_SOFTWARERAID',_SOFTRAID);
define('_DIAGINFOS_ISCSI','iSCSI');
define('_DIAGINFOS_MSDOMAIN','MS Domain');

// file: diag_infos.php
define('_DIAGINFOSDISKS_ATA_INFO','List of detected ATA disks');
define('_DIAGINFOSDISKS_SCSI_INFO','List of detected SCSI disks');

// file: diag_infos_mount.php
define('_DIAGINFOSMOUNT_INFO','List of mount points');

// file: diag_infos_space.php
define('_DIAGINFOSSPACE_INFO','Free Space');

// file: diag_defaults.php
define('_DIAGDEFAULTS_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGDEFAULTS_NAMEDESC',_MENULEFT_CMDFACTORYDEF);

// file: diag_backup.php
define('_DIAGBACKUP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGBACKUP_NAMEDESC',_MENULEFT_CMDBACKUPRESTORE);
define('_DIAGBACKUP_RESTORENOTE','FreeNAS will reboot after restoring the configuration.');

// file: diag_arp.php
define('_DIAGARP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGARP_NAMEDESC',_MENULEFT_DIAGARPTAB);

// file: status_graph.php
define('_STATSGRAPH_NAME',_MENULEFT_STATUS);
define('_STATSGRAPH_NAMEDESC','Traffic graph');

// file: graph.php
define('_GRAPH_ERRORGETIFDATA','Cannot get data about interface');
define('_GRAPH_IN','In');
define('_GRAPH_OUT','Out');
define('_GRAPH_SWITCHTO','Switch to');
define('_GRAPH_AUTOSCALE','AutoScale');
define('_GRAPH_AUTOSCALE_FOLLOW','Follow');
define('_GRAPH_AUTOSCALE_UP','Up');
define('_GRAPH_SHOWLAST','Graph shows last');
define('_GRAPH_COLLECTDATA','Collecting initial data, please wait...');
?>
