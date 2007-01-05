<?php 
/*
	lang-Dutch.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
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
// locale defines
define('_CHARSET','ISO-8859-1');
define('_LOCALE','nl_BE');
define('_LOCALEWIN', 'ned');
// date and time defines
define('_DATE','Datum');
define('_DATEBRIEF','%b %d, %Y');
define('_DATELONG','%A, %B %d, %Y');
define('_DATESTRING','%A, %B %d @ %H:%M:%S');
define('_DATETIMEBRIEF','%b %d, %Y - %I:%M %p');
define('_DATETIMELONG','%A, %B %d, %Y - %I:%M %p');
define('_DAY_OF_WEEK_LONG','zondag maandag dinsdag woensdag donderdag vrijdag zaterdag');
define('_DAY_OF_WEEK_SHORT','ma di wo do vr za zo');
define('_MONTH_LONG','januari februari maart april mei juni juli augustus september oktober november december');
define('_MONTH_SHORT','jan feb maa apr mei jun jul aug sep okt nov dec');
define('_TIME', 'Tijd');
define('_TIMEBRIEF','%I:%M %p');
define('_SECOND', 'seconde');
define('_SECONDS', 'seconden');
define('_MINUTE', 'minuut');
define('_MINUTES', 'minuten');
define('_HOUR', 'uur');
define('_HOURS', 'uren');
define('_DAY', 'dag');
define('_DAYS', 'dagen');
define('_WEEK', 'week');
define('_WEEKS', 'weken');
define('_WEEKDAYS', 'weekdagen');
define('_MONTH', 'maand');
define('_MONTHS', 'maanden');
define('_YEAR', 'jaar');
define('_YEARS', 'jaren');

// common words
define('_ALL','Alle');
define('_AND','en');
define('_BY','tegen');
define('_DOWN','Af');
define('_NO','Neen');
define('_OF','uit');
define('_OK','OK');
define('_ON','aan');
define('_TO','Tot');
define('_THE', 'De');
define('_UP','Op');
define('_URL', 'URL');
define('_YES','Ja');

// common actions
define('_ACTIVATE','Activeren');
define('_ADD','Toevoegen');
define('_CANCEL', 'Annuleren');
define('_COMMIT', 'Bevestigen');
define('_CREATE', 'Aanmaken');
define('_DEACTIVATE','Deactiveren');
define('_DELETE','Verwijderen');
define('_EDIT','Bewerken');
define('_ENABLE','Instellen');
define('_HELP', 'Help');
define('_IGNORE','Negeren');
define('_LOGIN','Aanmelden');
define('_LOGOUT','Afmelden');
define('_MODIFY','Wijzigen');
define('_NEW','Nieuw');
define('_SEARCH', 'Zoeken');
define('_SAVE', 'Bewaren');
define('_SUBMIT','Doorsturen');
define('_UPDATE', 'Bijwerken');
define('_VIEW', 'Bekijken');
define('_APPLY', 'Wijzigingen toepassen');
define('_WARNING', 'Waarschuwing');
define('_LOAD', 'Opladen');
define('_RETRY', 'Opnieuw proberen');

//FreeNAS common words
define('_SIZE', 'Grootte');
define('_STATUS', 'Status');
define('_TYPE', 'Type');
define('_CONFIGURING', 'Configureren');
define('_DISK', 'Disk');
define('_DISKS', 'Disks');
define('_PARTITION', 'Partitie');
define('_FILESYSTEM', 'Bestandssysteem');
define('_DESC', 'Omschrijving');
define('_SOFTRAID', 'Software RAID');
define('_SERVICE','Dienste');
define('_SERVICES','Diensten');
define('_TCPPORT','TCP port');
define('_CONFIRMATION','Bevestiging');
define('_NOTE','Nota');
define('_SUCCESSFUL','Succesvol');
define('_FAILED','Gefaald');
define('_ERROR','Fout');
define('_HINT','Hint');

// common messages
define('_MESSAGES_SUCCESS', 'De wijzigingen werden succesvol toegepast.');
define('_MESSAGES_SUCCESSWITHREBOOT', 'De wijzigingen werden bewaard. Je moet de NAS <a href=reboot.php>herstarten</a> om de wijzigingen te activeren.');
define('_MESSAGES_ERROR', 'FOUT: de wijzigingen konden niet toegepast worden (foutcode '.$ok.').');

define('_MSGVALIDIP','Er moet een geldig IP-adres opgegeven worden.');
define('_MSGVALIDNETBIT','Er moet een geldige bit-count voor het netwerk gegeven worden.');
define('_MSGVALIDTCPPORT','De TCP-poort moet geldig zijn.');
define('_MSGVALIDPASSWORD','De opgegeven wachtwoorden komen niet overeen.');

define('_MSGSCHED_TEXT','Hint: Ctrl-klik (of command-click op Mac) om minuten, uren, dagen en maanden te selecteren en de-selecteren.');

// Special Filesystem Warning

define('_MSGFILESYSTEM','UFS en varianten zijn de ORIGINELE bestandsformaten voor FreeBSD (het onderliggende besturingssysteem van FreeNAS).  Een poging tot gebruik van andere bestandsformaten als FAT, FAT32, EXT2, EXT3, or NTFS kan onvoorspelbare resultaten opleveren, zoals beschadiging van bestanden of zelfs verlies van bestanden.');

// states
define('_ACTIVE','Actief');
define('_INACTIVE','Inactief');
define('_SELECTED','Geselecteerd');

// Webgui layout
define('_MENULEFT_SYSTEM','Systeem');
define('_MENULEFT_SYSGENSETUP','Algemene setup');
define('_MENULEFT_SYSSTATROUTES','Statische routes');
define('_MENULEFT_SYSFIRMWARE','Firmware');
define('_MENULEFT_SYSADVANCED','Geavanceerd');
define('_MENULEFT_INTERFACES','Interfaces');
define('_MENULEFT_INTASSIGN','Toewijzen');
define('_MENULEFT_INTLAN','LAN');
define('_MENULEFT_DISKS',_DISKS);
define('_MENULEFT_DISKMANAG','Beheer');
define('_MENULEFT_DISKMNGSOFTRAID',_SOFTRAID);
define('_MENULEFT_DISKMNGMOUNTPOINT','Koppelpunt');
define('_MENULEFT_SERVICES','Diensten');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVUNSN','Unison');
define('_MENULEFT_SRVUPNP','UPnP');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','Toegang');
define('_MENULEFT_ACUSERGROUPS','Gebruikers en Groepen');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS',_STATUS);
define('_MENULEFT_STSYSTEM','Systeem');
define('_MENULEFT_STPROCESS','Proces');
define('_MENULEFT_STINTERFACES','Interfaces');
define('_MENULEFT_STDISKS',_DISKS);
define('_MENULEFT_STWIRELESS','Draadloos');
define('_MENULEFT_EXTENSIONS','Uitbreidingen');
define('_MENULEFT_DIAGNOSTICS','Diagnostiek');
define('_MENULEFT_DIAGLOGS','Logs');
define('_MENULEFT_DIAGINF','Informatie');
define('_MENULEFT_DIAGPINGTRACE','Ping/Traceroute');
define('_MENULEFT_DIAGARPTAB','ARP table');
define('_MENULEFT_DIAGEXEC','Commando');
define('_MENULEFT_DIAGEDITFILE','Bestand bewerken');
define('_MENULEFT_CMDBACKUPRESTORE','Backup/Restore');
define('_MENULEFT_CMDFACTORYDEF','Fabrieksinstellingen');
define('_MENULEFT_CMDREBOOTSYS','Systeem herstarten');
define('_MENULEFT_CMDSHUTDOWNSYS','Systeem uitschakelen');
define('_MENULEFT_GRAPH','Grafiek');

// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION',_MENULEFT_SYSGENSETUP);
define('_SYSTEMPHP_HOSTNAME','Hostnaam');
define('_SYSTEMPHP_HOSTNAMETEXT','Naam van de NAS host, zonder het domein gedeelte<br>b.v. <em>nas');
define('_SYSTEMPHP_DOMAIN','Domein');
define('_SYSTEMPHP_DOMAINTEXT','b.v. <em>mijndomein.com</em>');
define('_SYSTEMPHP_DNS','DNS servers');
define('_SYSTEMPHP_DNSTEXT','IP adressen');
define('_SYSTEMPHP_USERNAME','Gebruikersnaam');
define('_SYSTEMPHP_USERNAMETEXT','Geef hier de gebruikersnaam voor toegang tot de webinterface op als je deze wil veranderen.');
define('_SYSTEMPHP_PASSWORD','Wachtwoord');
define('_SYSTEMPHP_PASSWORDTEXT','Geef hier tweemaal het wachtwoord in als je dit wil veranderen.<br>Gebruik nooit het \':\' karakter.');
define('_SYSTEMPHP_WEBGUIPROTOCOL','Webinterface protocol');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','');
define('_SYSTEMPHP_WEBGUIPORT','Webinterface poort');
define('_SYSTEMPHP_WEBGUIPORTTEXT','Vul hier een specifiek poortnummer in voor de webinterface als je geen gebruik wil maken van de standaard poorten (80 voor HTTP, 443 voor HTTPS).');
define('_SYSTEMPHP_LANGUAGE','Taal');
define('_SYSTEMPHP_LANGUAGETEXT','Selecteer de systeemtaal');
define('_SYSTEMPHP_TIMEZONE','Tijdzone');
define('_SYSTEMPHP_TIMEZONETEXT','Selecteer de locatie het dichste bij jou.');
define('_SYSTEMPHP_TIMEUPDATEINT','Interval voor de bijwerking van de tijd.');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','Aantal minuten tussen een tijdsynchronisatie.; 300	aangeraden, of 0 om te desactiveren.');
define('_SYSTEMPHP_NTPSERVER','NTP tijd server');
define('_SYSTEMPHP_NTPSERVERTEXT','Gebruik een spatie om meerdere hosts van elkaar te scheiden (slechts &eacute;&eacute;n nodig). Denk eraan om minstens &eacute;&eacute;n DNS server op te geven als je hier een hostnaam invult!');
define('_SYSTEMPHP_MSGHOSTNAME','De hostnaam mag alleen de karakters a-z, 0-9 en \'-\' bevatten.');
define('_SYSTEMPHP_MSGDOMAIN','Het domein mag alleen maar de karakters a-z, 0-9, \'-\' en \'.\' bevatten.');
define('_SYSTEMPHP_MSGDNS','Er moet een geldig IP-adres gespecifieerd worden voor de primaire/secundaire DNS server.');
define('_SYSTEMPHP_MSGUSERNAME','De gebruikersnaam mag alleen de karakters a-z, A-Z en 0-9 bevatten.');
define('_SYSTEMPHP_MSGWEBGUIPORT','Er moet een geldige TCP/IP poort gespecifieerd worden voor de webinterface.');
define('_SYSTEMPHP_MSGPASSWORD','De wachtwoorden komen niet overeen.');
define('_SYSTEMPHP_MSGLANGUAGE','Je moet een geldige taal kiezen.');
define('_SYSTEMPHP_MSGTIMEUPDATEINTERVAL','Het interval voor de tijdsynchronisatie moet ofwel 0 (inactief) of tussen 6 en 1440 liggen.');
define('_SYSTEMPHP_MSGTIMESERVERS','De naam van een NTP tijdserver mag alleen de karakters a-z, 0-9, \'-\' en \'.\' bevatten.');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','Systeem informatie');
define('_INDEXPHP_NAME','Naam');
define('_INDEXPHP_VERSION','Versie');
define('_INDEXPHP_OSVERSION','OS Versie');
define('_INDEXPHP_PLATFORM','Platform');
define('_INDEXPHP_DATE','Datum');
define('_INDEXPHP_UPTIME','Uptime');
define('_INDEXPHP_LASTCHANGE','Laatste configuratiewijziging');
define('_INDEXPHP_MEMUSE','Geheugengebruik');
define('_INDEXPHP_LOADAVERAGE','Gemiddelden systeembelasting');
define('_INDEXPHP_SHOWPROCESSING','toon info over de processen');
define('_INDEXPHP_DISKS_USE','Gebruikte diskcapaciteit');
define('_INDEXPHP_NODISK','Geen disk geconfigureerd');

// Status Processes Module
// file: status_process.php
define('_STATSPROCPHP_NAME',_STATUS);
define('_STATSPROCPHP_NAME_DESCRIPTION','Processen');
define('_STATSPROCPHP_PROC_INF','Procesinformatie');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION',_MENULEFT_SYSSTATROUTES);
define('_SYSTEMROUTESPHP_MSG_CHANGED','De instellingen voor de statische routering werden aangepast.<br>Je moet op \'toepassen\' klikken vooraleer ze effectief gebruikt worden.');
define('_SYSTEMROUTESPHP_INT','Interface');
define('_SYSTEMROUTESPHP_NET','Netwerk');
define('_SYSTEMROUTESPHP_GW','Gateway');
define('_SYSTEMROUTESPHP_DESC',_DESC);
define('_SYSTEMROUTESPHP_EDITROUTE','Route bewerken');
define('_SYSTEMROUTESPHP_MSGCONFIRM','Ben je zeker dat je deze route wil verwijderen?');
define('_SYSTEMROUTESPHP_DELROUTE','Route verwijderen');
define('_SYSTEMROUTESPHP_ADDROUTE','Route toevoegen');

// Static Routes Edit Module
// file: system_routes_edit.php
define('_SYSTEMROUTESPHP_MSGVALID','Je moet een geldig bestemmingsnetwerk opgeven.');
define('_SYSTEMROUTESPHP_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SYSTEMROUTESPHP_MSGVALIDGW','Je moet een geldig IP adres opgeven voor de gateway.');
define('_SYSTEMROUTESPHP_MSGROUTEEXIST','Er bestaat al een route naar dit bestemmingsnetwerk.');
define('_SYSTEMROUTESPHP_CHOOSEINT','Kies de interface waarop deze route betrekking heeft.');
define('_SYSTEMROUTESPHP_DESTNET','Bestemmingsnetwerk');
define('_SYSTEMROUTESPHP_DESTNETROUTE','Bestemmingsnetwerk voor deze statische route');
define('_SYSTEMROUTESPHP_GWTONET','De gateway die gebruikt moet worden om het bestemmingsnetwerk te bereiken');
define('_SYSTEMROUTESPHP_DESCREF','Je mag hier een eigen omschrijving opgeven.');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMFIRMWAREPHP_NAMEDESC',_MENULEFT_SYSFIRMWARE);
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','Het opgeladen bestand is niet geschikt voor dit platform');
define('_SYSTEMFIRMWAREPHP_OUTMEM','Het opladen van het bestand is mislukt (te weinig geheugen?)');
define('_SYSTEMFIRMWAREPHP_INVSIG','De digitale handtekening van het opgeladen bestand is niet geldig.');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','Het opgeladen bestand is niet digitaal getekend.');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','Er heeft zich een fout voorgedaan bij het nakijken van de ondertekening van het opgeladen bestand.');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','Het opgeladen bestand is corrupt.');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','De systeemsoftware wordt nu geïnstalleerd. FreeNAS zal automatisch herstarten.');
define('_SYSTEMFIRMWAREPHP_NOTSUP','Het opladen van systeemsoftware wordt niet ondersteund op dit platform.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','Dit wil zeggen dat het bestand dat je hebt opgelenden ' .
		 	'geen officieel of ondersteund bestand is en dat dit kan leiden tot onverwacht systeemgedrag of beveiligingsmankementen. ' .
			'Installeer alleen maar bestanden die afkomstig zijn van betrouwbare bronnen, en zorg ervoor dat er niet '.
			'geknoeid werd met het bestand.<br><br>'.
			'Wil je het opgeladen bestand toch installeren (op eigen risico)?');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','Klik hieronder op &quot;Opladen systeemsoftware activeren&quot;, en kies daarna het op te laden bestand..<br>Klik op &quot;Systeemsoftware opladen&quot; om het opladen te starten.');
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','Opladen systeemsoftware activeren');	
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','Opladen systeemsoftware desactiveren');
define('_SYSTEMFIRMWAREPHP_FIRMFILE','Systeemsoftware bestand (firmware)');	
define('_SYSTEMFIRMWAREPHP_UPFIRM','Systeemsoftware opladen');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','Je moet het systeem herstarten vooraleer je de systeemsoftware kan opladen.');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','Het opladen van de systeemsoftware NOOIT ONDERBREKEN eens de procedure opgestart is. FreeNAS zal automatisch herstarten als de nieuwe systeemsoftware opgeladen is. De configuratieinstellingen zullen bewaard blijven.<br>Er is minimaal 128Mb RAM in het systeem nodig voor het opladen van de systeemsoftware.');

// System Advanced Module
// file: system_advanced.php
define('_SYSTEMADVANCEDPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMADVANCEDPHP_NAMEDESC',_MENULEFT_SYSADVANCED);
define('_SYSTEMADVANCEDPHP_MSGVALIDTCP','De inactiviteitsduur voor TCP-onderbreking moet een geheel getal zijn.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERTKEY','Certificaat en sleutel moeten altijd samen gespecifieerd worden.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERT','Dit certificaat is ongeldig.');
define('_SYSTEMADVANCEDPHP_MSGVALIDKEY','Deze sleutel is ongeldig');
define('_SYSTEMADVANCEDPHP_NOTE','De opties op deze pagina dienen alleen voor gespecialiseerd gebruik; er is <strong>GEEN</strong> ondersteuning voor deze opties.');
define('_SYSTEMADVANCEDPHP_WEBGUISSLKEY','webinterface SSL certificaat\/sleutel');
define('_SYSTEMADVANCEDPHP_WEBCERT','Certificaat');
define('_SYSTEMADVANCEDPHP_WEBCERTTEXT','Plak hier (copy/paste) een ondertekend certificaat in X.509 PEM formaat.');
define('_SYSTEMADVANCEDPHP_WEBKEY','Sleutel'); 
define('_SYSTEMADVANCEDPHP_WEBKEYTEXT','Plak hier (copy/paste) een RSA private sleutel in PEM formaat.');
define('_SYSTEMADVANCEDPHP_MISC','Allerlei');
define('_SYSTEMADVANCEDPHP_CON','Consolemenu');
define('_SYSTEMADVANCEDPHP_DISCON','Consolemenu uitschakelen');
define('_SYSTEMADVANCEDPHP_CONTEXT','Wijzigingen aan deze optie zullen pas actief zijn na het herstarten.');
define('_SYSTEMADVANCEDPHP_FIRM','Controle op de versie van de systeemsoftware');
define('_SYSTEMADVANCEDPHP_DISFIRM','Controle op de versie van systeemsoftware uitschakelen.');
define('_SYSTEMADVANCEDPHP_FIRMTEXT','Dit zorgt ervoor dat FreeNAS niet gaat controleren of er nieuwere versies van de systeemsoftware beschikbaar zijn wanneer de <a href=system_firmware.php>Systeem: Systeemsoftware</a> pagina wordt opgevraagd.');
define('_SYSTEMADVANCEDPHP_TCPIDLE','TCP inactiviteitsduur voor onderbreking');
define('_SYSTEMADVANCEDPHP_TCPIDLETEXT','Inactieve TCP connecties zullen verwijderd worden uit de statustabel als er gedurende het gespecifieerde aantal seconden geen pakketten ontvangen werden. Zet deze waarde niet te hoog, dit zou kunnen veroorzaken dat de statustabel vol verbindingen komt te staan die niet correct werden afgesloten. De standaard is 2,5 uur.');
define('_SYSTEMADVANCEDPHP_NAV','Navigatie');
define('_SYSTEMADVANCEDPHP_NAVTEXT','Hou de diagnosegegevens in de navigatie opgengeklapt');
define('_SYSTEMADVANCEDPHP_BEEP','Systeem beep');
define('_SYSTEMADVANCEDPHP_DISBEEP','Systeem beep uitschakelen bij het opstarten en afsluiten');
define('_SYSTEMADVANCEDPHP_SMART','S.M.A.R.T Daemon');
define('_SYSTEMADVANCEDPHP_ENSMART','De S.M.A.R.T daemon inschakelen');
define('_SYSTEMADVANCEDPHP_SMARTTEXT','De S.M.A.R.T toestellen overwaken door hun status te registreren in de het logbestand.');
define('_SYSTEMADVANCEDPHP_TUNING','Tuning');
define('_SYSTEMADVANCEDPHP_ENTUNING','Het tunen van sommige kernelvariabelen inschakelen');
define('_SYSTEMADVANCEDPHP_ZEROCONF','ZeroConf');
define('_SYSTEMADVANCEDPHP_DISZEROCONF','ZeroConf service-aankondiging uitschakelen');

// Interface assing:
// file: interfaces_assign.php
define('_INTASSIGNPHP_NAME','Interfaces');
define('_INTASSIGNPHP_NAMEDESC','Netwerkpoorten toekennen');
define('_INTASSIGNPHP_MSGVALIDPORT','Poort ');
define('_INTASSIGNPHP_MSGVALIDPORTWASASSIG',' werd toegekend aan ');
define('_INTASSIGNPHP_MSGVALIDINT',' interfaces:');
define('_INTASSIGNPHP_INT','Interface');
define('_INTASSIGNPHP_PORT','Netwerk poort');
define('_INTASSIGNPHP_DELINT','Interface verwijderen');
define('_INTASSIGNPHP_ADDINT','Interface toevoegen');
define('_INTASSIGNPHP_INTTEXT','Nadat je op &quot;Bewaren&quot; klikt moet je FreeNAS herstarten om de wijzigingen te activeren. Het kan zijn dat je ook nog &euml;&euml;n van volgende stappen moet uitvoeren om terug toegang te krijgen tot je NAS: </span></p>
<ul>
  <li><span class=\"vexpl\">het IP adres van je PC veranderen</span></li>
  <li><span class=\"vexpl\">de webinterface opvragen met het nieuwe IP adres</span></li>
</ul>');

// Interface LAN/OPT:
// file: interfaces_lan.php
// file: interfaces_opt.php
define('_INTLANPHP_NAME','Interfaces');
define('_INTLANPHP_NAMEDESC','LAN');
define('_INTPHP_MSGVALIDIP',_MSGVALIDIP);
define('_INTPHP_MSGVALIDMASK',_MSGVALIDNETBIT);
define('_INTPHP_MSGVALIDGW','Er moet een geldige gateway opgegeven worden.');
define('_INTPHP_MSGVALIDMTU','Er moet een geldige MTU grootte opgegeven worden.');
define('_INTPHP_IP','IP adres');
define('_INTPHP_NETMASK','Aantal bits voor het subnet');
define('_INTPHP_GW','Gateway');
define('_INTPHP_MTU','MTU');
define('_INTPHP_MTUTEXT','De standaard MTU is 1500, gebruik 9000 voor jumbo frame.');
define('_INTPHP_POLLING','Device polling');
define('_INTPHP_ENPOLLING','Device polling');
define('_INTPHP_POLLINGTEXT','Device polling is een techniek die het systeem regelmatig de netwerkadapters laat controleren op 
nieuwe data in plaats van voort te gaan op interrupts. Dit kan ervoor zorgen dat er minder CPU-belasting is, en daardoor meer 
doorvoer, ten koste van een iets hogere vertraging bij het doorsturen (de netwerkadapters worden 1000 maal per seconde gecontroleerd).
Niet alle netwerkadapters ondersteunen deze manier van werken; kijk op de m0n0wall homepage voor een lijst van ondersteunde netwerkadapters.');
define('_INTPHP_SPEED','Snelheid');
define('_INTPHP_DUPLEX','Duplex');
define('_INTPHP_TEXT','Nadat je op &quot;Bewaren&quot; klikt kan het zijn dat je ook nog &euml;&euml;n van volgende stappen moet uitvoeren om terug toegang te krijgen tot je NAS: </span></p>
<ul>
  <li><span class=\"vexpl\">het IP adres van je PC veranderen</span></li>
  <li><span class=\"vexpl\">de webinterface opvragen met het nieuwe IP adres</span></li>
</ul>');
define('_INTPHP_ENOPT','Activeer optioneel ');
define('_INTPHP_ENOPTINT',' interface');
define('_INTPHP_DESC',_DESC);
define('_INTPHP_DESCTEXT','Geef hier een omschrijving (naam) voor de interface.');
define('_INTPHP_STATIC','Statische IP instellingen');
define('_INTPHP_DHCP','DHCP client instellingen');
define('_INTPHP_GENERAL','Algemene instellingen');
define('_INTPHP_DHCPHOSTNAME','Hostnaam');
define('_INTPHP_DHCPHOSTNAMETEXT','De waarde in dit veld wordt als DHCP hostnaam doorgestuurd bij het aanvragen van een DHCP lease.');
define('_INTPHP_DHCPCLIENTIDENTIFIER','Klant-identificatie');
define('_INTPHP_DHCPCLIENTIDENTIFIERTEXT','De waarde in dit veld wordt doorgestuurd als DHCP klant-identificatie wanneer een DHCP-lease wordt aangevraagd.');
// define('_INTPHP_DHCPCLIENTIDENTIFIERTEXT','De waarde van dit veld wordt als DHCP klant-identificeerder verstuurd wanneer een DHCP-lease wordt aangevraagd.');
define('_INTPHP_CALCNETMASKBITS','Bereken de netmask bits');
define('_INTPHP_MSGCHANGED','De LAN instellingen werden gewijzigd.<br>Je moet op \'toepassen\' klikken vooraleer de wijzigingen actief zijn.');

// Disks: Manage
// Common text
define('_DISKSPHP_NAME',_DISKS);
define('_DISKSPHP_MANAGE','Beheer');
define('_DISKSPHP_FORMAT','Formatteer');
define('_DISKSPHP_TOOLS','Hulpmiddelen');
define('_DISKSPHP_ISCSIINIT','iSCSI initiator');
define('_DISKSPHP_DISK',_DISK);
define('_DISKSPHP_FILESYSTEM',_FILESYSTEM);

// file: disks_manage.php
define('_DISKSMANAGEPHP_NAMEDESC','Beheer');
define('_DISKSMANAGEPHP_MSGCHANGED','De lijst met schijven is gewijzigd.<br>Je moet op \'toepassen\' klikken vooraleer de wijzigingen actief zijn.');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','Standby tijd');
define('_DISKSMANAGEPHP_FSUNKNOWN','Ongekend of niet geformatteerd');
define('_DISKSMANAGEPHP_EDIT','Schijf bewerken');
define('_DISKSMANAGEPHP_DEL','Schijf verwijderen');
define('_DISKSMANAGEPHP_DELCONF','Ben je zeker dat je deze schijf wil verwijderen? Alle elementen die gebruik maken van deze schijf worden ongeldig. (bv gedeelde mappen)!');
define('_DISKSMANAGEPHP_ADD','Schijf toevoegen');
define('_DISKSMANAGEPHP_NOTE','Eerste stap voor de instellingen: voeg de schijf toe bij de lijst met schijven.');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','Deze schijf bestaat al in de lijst met schijven.');
define('_DISKSMANAGEEDITPHP_UDMA','UDMA modus');
define('_DISKSMANAGEEDITPHP_UDMATEXT','Je kan UDMA modus forceren als je "UDMA_ERROR.... LBA" berichten krijgt voor de harde schijf.');
define('_DISKSMANAGEEDITPHP_STANDBY','Standby tijd voor de harde schijf.');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','Plaatst de harde schijf in standby modus wanneer de geselecteerde tijd verstreken is na de laatste toegang. <em>Activeer dit niet voor CF kaarten.</em>');
define('_DISKSMANAGEEDITPHP_APM','Geavanceerd Energiebeheer');
define('_DISKSMANAGEEDITPHP_APMTEXT','Hiermee kan je het energieverbruik van de schijf verminderen, ten koste van performantie. <em>Activeer dit niet voor CF kaarten.</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','Akoestisch niveau');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','Hiermee kan je het geluidsniveau van de schijf instellen.<em>Gebruik dit niet voor CF kaarten.</em>');
define('_DISKSMANAGEEDITPHP_PREFS','Voorgeformatteerd bestandssysteem');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','Hiermee kan je het type bestandssysteem opgeven voor een voorgeformatteerde schijf met data.<br> <em>Laat op "ongeformatteerd" staan voor een nieuwe schijf gebruik dan het Formatteer menu.</em>');

//file: disks_manage_init.php
define('_DISKSMANAGEINITPHP_NAMEDESC','Initialiseer');
define('_DISKSMANAGEINITPHP_DISKMOUNTERROR','De schijf is momenteel gekoppeld! <a href=%s>Ontkoppel</a> deze schijf eerst vooraleer verder te gaan.');
define('_DISKSMANAGEINITPHP_INITTEXT','<strong>Initialisatiedetails van de schijf:</strong>');
define('_DISKSMANAGEINITPHP_INITINFO','\"fdisk: Geom not found\" is geen foutbericht!\n');
define('_DISKSMANAGEINITPHP_TEXT','Deze stap zal alle partities wissen, een nieuwe partitie aanmaken en deze formatteren met het opgegeven bestandssysteem.');
define('_DISKSMANAGEINITPHP_FORMATDISC','Formatteer schijf');
define('_DISKSMANAGEINITPHP_FORMATDISCCONF','Ben je zeker dat je deze schijf wil formatteren? Alle gegevens zullen verloren gaan!');
define('_DISKSPHP_NOMBR','Het MBR niet wissen');
define('_DISKSPHP_NOMBRTEXT','Het MBR niet wissen (zinvol voor sommige RAID controller kaarten)');

//file: disks_manage_tools.php
define('_DISKSMANAGETOOLS_NAMEDESC','Hulpmiddelen');
define('_DISKSMANAGETOOLS_COMMAND','Commando');
define('_DISKSMANAGETOOLS_SENDCMD','Commando versturen!');
define('_DISKSMANAGETOOLS_RAIDDISKNOTE','Maak gebruik van <a href="%s">RAID hulpmiddelen</a> voor deze schijf!');
define('_DISKSMANAGETOOLS_CMDINFO','Commando uitvoer:');
define('_DISKSMANAGETOOLS_MOUNTNOTE','De schijf is momenteel gekoppeld! Het koppelpunt zal tijdelijk verwijderd worden om het geselecteerde commando uit te voeren.');
define('_DISKSMANAGETOOLS_UMOUNT','Ontkoppel shijf/partitie');
define('_DISKSMANAGETOOLS_UMOUNTTEXT','Als de geselecteerde schijf/partitie gekoppeld is zal deze tijdelijk ontkoppeld worden om het geselecteerde commando uit te voeren, anders werken de commando\'s slechts in \'alleen-lezen\' modus..');

//file: disks_manage_iscsi.php
define('_DISKSMANAGEPHP_NAMEDESC','Beheer');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','iSCSI initiator');
define('_DISKSMANAGEISCSIPHP_TARGETIP','IP adres bestemming');
define('_DISKSMANAGEISCSIPHP_TARGETIPTEXT','IP adres van de iSCSI bestemming.');
define('_DISKSMANAGEISCSIPHP_TARGETNAME','Bestemmingsnaam');
define('_DISKSMANAGEISCSIPHP_TARGETNAMETEXT','Bestemmingsnaam.');

//Software RAID
// Comon value
define('_DISKSRAIDPHP_MANAGE','RAID beheer');
define('_DISKSRAIDPHP_FORMAT','RAID formatteren');
define('_DISKSRAIDPHP_TOOLS','Hulpmiddelen');
define('_DISKSRAIDPHP_INFO','Informatie');
define('_DISKSRAIDPHP_VOLUME','Volumenaam');
define('_DISKSRAIDPHP_RAIDVOLUMEMOUNTERROR','The RAID volume is currently mounted! Remove the <a href=%s>mount point</a> first before proceeding.');

// Software RAID common word
define('_DISKSRAIDPHP_GVINUM','Geom Vinum');
define('_DISKSRAIDPHP_GMIRROR','Geom Mirror');
define('_DISKSRAIDPHP_GSTRIPE','Geom Stripe');
define('_DISKSRAIDPHP_GCONCAT','Geom Concat');
define('_DISKSRAIDPHP_GRAID5','Geom Raid5');
define('_DISKSRAIDPHP_UNSTABLE','(unstable)');

//file: disks_raid.php
define('_DISKSRAIDPHP_NAMEDESC','RAID');
define('_DISKSRAIDPHP_MSGCHANGED','De Raid instellingen werden gewijzigd.<br>Je moet op \'toepassen\' klikken vooraleer de wijzigingen actief zijn.');
define('_DISKSRAIDPHP_TYPE','Type');
define('_DISKSRAIDPHP_EDITRAID','RAID bewerken');
define('_DISKSRAIDPHP_DELRAID','RAID verwijderen');
define('_DISKSRAIDPHP_ADDRAID','RAID toevoegen');
define('_DISKSRAIDPHP_DELCONF','Ben je zeker dat je dit RAID volume wil verwijderen? Alle elementen die er gebruik van maken zullen ongeldig worden (bv gedeelde mappen)!');
define('_DISKSRAIDPHP_NOTE','Optionele stap voor de instellingen: een virtuele RAID schijf instellen, gebruik makend van een <a href="disks_manage.php">voorheen geconfigureerde schijf</a>.<br>Wacht tot de status "op" is vooraleer de schijf te formatteren en te koppelen!');

//file: disks_raid_edit.php
define('_DISKSRAIDEDITPHP_NAMEDESC',_EDIT);
define('_DISKSRAIDEDITPHP_MSGADDDISKFIRST','Je moet eerst schijven toevoegen.');
define('_DISKSRAIDEDITPHP_MSGVALIDNAME','De toestelnaam mag alleen maar bestaan uit de karakters a-z, A-Z, 0-9.');
define('_DISKSRAIDEDITPHP_MSGEXIST','Dit toestel bestaat al in de RAID volumelijst.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID0','Er moeten minimaal 2 schijven zijn voor een RAID 0 volume.');
define('_DISKSRAIDEDITPHP_MSGVALIDJBOD','Er moeten minimaal 2 schijven zijn in een JBOD.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID1','Er moeten minimaal 2 schijven zijn in een RAID 1 volume.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID5','Er moeten minimaal 3 schijven zijn in een RAID 5 volume.');
define('_DISKSRAIDEDITPHP_MSGERROR','FOUT: kon de RAID instellingen niet opslaan.\n');
define('_DISKSRAIDEDITPHP_RAIDNAME','RAID naam');
define('_DISKSRAIDEDITPHP_GVBALANCEALGO','Balance algoritme');
define('_DISKSRAIDEDITPHP_GVBALANCEALGOTEXT','Selecteer het algoritme voor de leesbalans.');
define('_DISKSRAIDEDITPHP_RAID0','striping');
define('_DISKSRAIDEDITPHP_JBOD','JBOD');
define('_DISKSRAIDEDITPHP_RAID1','mirroring');
define('_DISKSRAIDEDITPHP_RAID5','rotated block-interleaved parity');
define('_DISKSRAIDEDITPHP_MEMBERS','Leden van dit volume');

//file: disks_raid_info.php
define('_DISKSRAIDINFOPHP_TEXT','Software RAID informatie en status');

//file: disks_raid_init.php
define('_DISKSRAIDINITPHP_INFO','Schijf formattering UFS uitvoer:');
define('_DISKSRAIDINITPHP_TEXT','Deze stap zal het RAID volume formatteren in het unix bestandssysteem (UFS).');
define('_DISKSRAIDINITPHP_FORMAT','Formatter UFS schijf!');

//file: disks_raid_tools.php
define('_DISKSRAIDTOOLSPHP_OBJNAME','Object naam');
define('_DISKSRAIDTOOLSPHP_COMMAND','Commando');
define('_DISKSRAIDTOOLSPHP_INFO','GVINUM commando uitvoer:');
define('_DISKSRAIDTOOLSPHP_SENDCMD','Commando versturen!');
define('_DISKSRAIDTOOLSPHP_TEXT','1. Gebruik deze speciale opdrachten alleen maar voor het debuggen!<br>2. Dit menu moet niet gebruikt worden om een RAID volume op te starten (dit start automatisch).');

//Disk Mount Point
// Comon value
define('_DISKSMOUNTPHP_NAME','Koppelpunt');
define('_DISKSMOUNTPHP_SHARENAME','Naam gedeelde map');
define('_DISKSMOUNTPHP_MANAGE','Beheer');
define('_DISKSMOUNTPHP_TOOLS','Hulpmiddelen');

// disks_mount.php
define('_DISKSMOUNTPHP_MSGCHANGED','De lijst van koppelpunten werd gewijzigd.<br>Je moet op \'toepassen\' klikken vooraleer de wijzigingen actief zijn.');
define('_DISKSMOUNTPHP_NOTE','Tweede stap voor de instellingen: het bestandssysteem defini&euml;ren voor de <a href="disks_manage.php">voorheen opgegeven schijf</a>.<p>');
define('_DISKSMOUNTPHP_DELCONF','Ben je zeker dat je dit koppelpunt wil verwijderen? Alle elementen die er gebruik van maken zullen ongeldig worden (bv gedeelde mappen)!');
define('_DISKSMOUNTPHP_DEL','koppelpunt verwijderen');
define('_DISKSMOUNTPHP_ADD','koppelpunt toevoegen');

// disks_mount_edit.php
define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','De naam van de gedeelde map mag alleen volgende karakters bevatten a-z, A-Z, 0-9, _ , -.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','De naam van de gedeelde map bevat ongeldige karakters.');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','Systeempartitie 1 kan niet gekoppeld worden, de DATA partitie is partitie2');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','Dubbele naam voor de gedeelde map.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDISK','Deze schijf/partiti is al ingesteld.');
define('_DISKSMOUNTEDITPHP_TEXT','1. Je kan geen partitie koppelen waarop de systeeminstellingen bewaard zijn.<br>2. FreeBSD NTFS vertoont nog veel fouten.');

// disks_mount_tools.php
define('_DISKSMOUNTTOOLS_COMMAND','Commando');
define('_DISKSMOUNTTOOLS_SENDCMD','Commando versturen!');
define('_DISKSMOUNTTOOLS_CMDINFO','Commando uitvoer:');
define('_DISKSMOUNTTOOLS_MOUNTTEXT','Bezig met koppelen...');
define('_DISKSMOUNTTOOLS_UMOUNTTEXT','Bezig met ontkoppelen...');

// Service: CIFS/SAMBA
// Common text
define('_SRVCIFS_SETTINGS','Instellingen');
define('_SRVCIFS_SHARE','Gedeelde map');
define('_SRVCIFS_SHARES','Gedeelde mappen');

// file: services_samba.php
define('_SRVCIFS_NAMEDESC','CIFS');
define('_SRVCIFS_MSGVALIDNETBIOSNAME','De Netbiosnaam bevat ongeldige karakters.');
define('_SRVCIFS_MSGVALIDWORKGOUP','De werkgroepnaam bevat ongeldige karakters.');
define('_SRVCIFS_MSGVALIDWINS','De WINS server moet een IP adres zijn.');
define('_SRVCIFS_MSGVALIDSNDBUFF','De verzendbufferwaarde moet een getal zijn.');
define('_SRVCIFS_MSGVALIDRCVBUFF','De ontvangbufferwaarde moet een getal zijn.');
define('_SRVCIFS_SHARE','CIFS gedeelde map');
define('_SRVCIFS_AUTH','Legitimatie');
define('_SRVCIFS_NETBIOSNAME','NetBiosnaam');
define('_SRVCIFS_WORKGROUP','Werkgroep');
define('_SRVCIFS_WORKGROUPTEXT','Werkgroep waarvan lid.');
define('_SRVCIFS_DESCTEXT','Omschrijving van de server. Dit kan gewoonlijk leeg gelaten worden.');
define('_SRVCIFS_DOSCHARSET','DOS karakterset');
define('_SRVCIFS_UNIXCHARSET','UNIX karakterset');
define('_SRVCIFS_LOGLEVEL','Log niveau');
define('_SRVCIFS_LOCALMASTER','Local Master Browser');
define('_SRVCIFS_LOCALMASTERTEXT','Dit staat FreeNAS toe om te proberen om local master browser te worden.');
define('_SRVCIFS_TIMESRV','Tijd server');
define('_SRVCIFS_TIMESRVTEXT','FreeNAS maakt zichzelf als tijdserver kenbaar aan Windows clienten.');
define('_SRVCIFS_WINS','WINS server');
define('_SRVCIFS_WINSTEXT','WINS Server IP adres.');
define('_SRVCIFS_RECYCLE','Prullenmand');
define('_SRVCIFS_ENRECYCLE','Prullenmand activeren');
define('_SRVCIFS_ENRECYCLETEXT','Dit maakt een prullenmand aan op de de CIFS gedeelde mappen');
define('_SRVCIFS_SNBBUFF','Grootte van de zendbuffer');
define('_SRVCIFS_SNBBUFFTEXT','Grootte van de zendbuffer (16384 standaard).');
define('_SRVCIFS_RCVBUFF','Grootte van de ontvangbuffer');
define('_SRVCIFS_RCVBUFFTEXT','Grootte van de ontvangbuffer (16384 standaard).');

// file: services_samba_share.php
define('_SRVCIFSSHARE_MSGCHANGED','De gedeelde mappen werden gewijzigd.<br>Je moet op \'toepassen\' klikken vooraleer de wijzigingen actief zijn.');
define('_SRVCIFSSHARE_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHARE_BROWSEABLE','Verkenbaar');
define('_SRVCIFSSHARE_DESC',_DESC);
define('_SRVCIFSSHARE_EDIT','Gedeelde map bewerken');

// file: services_samba_share_edit.php
define('_SRVCIFSSHAREEDIT_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHAREEDIT_DESC',_DESC);
define('_SRVCIFSSHAREEDIT_BROWSEABLE','Verkenbaar');
define('_SRVCIFSSHAREEDIT_BROWSEABLETEXT','Dit bepaalt of de gedeelde map zichtbaar is in de lijst van beschikbare gedeelde mappen in het netwerkoverzicht.');

// Service: FTP
// file: services_ftp.php
define('_SRVFTP_NAMEDESC','FTP');
define('_SRVFTP_MSGVALIDPORT',_MSGVALIDTCPPORT);
define('_SRVFTP_MSGVALIDMAXCLIENT','Het maximum aantal clients moet een getal zijn.');
define('_SRVFTP_MSGVALIDMAXIP','Het maximum aantal connecties per IP adres moet een getal zijn.');
define('_SRVFTP_MSGVALIDIDLE','De maximum inactiviteitsduur moet een getal zijn.');
define('_SRVFTP_MSGVALIDPASVIP','Het pasv adres moet een publiek IP adres zijn.');
define('_SRVFTP_MSGVALIDPASVMAX','De pasv_max_port poort moet een geldig poortnummer zijn.');
define('_SRVFTP_MSGVALIDPASVMIN','De pasv_min_port poort moet een geldig poortnummer zijn.');
define('_SRVFTP_MSGVALIDAUTH','Je moet minstens anonieme- en/of lokale gebruikersaanmelding selecteren.');
define('_SRVFTP_FTPSERVER','FTP server');
define('_SRVFTP_TCP',_TCPPORT);
define('_SRVFTP_MAXCLIENT','Aantal clients');
define('_SRVFTP_MAXCLIENTTEXT','Maximum aantal gelijktijdige connecties.');
define('_SRVFTP_MAXIP','Max. aant. conn. per IP');
define('_SRVFTP_MAXIPTEXT','Maximum aantal connecties per IP adres (0 = ongelimiteerd).');
define('_SRVFTP_TIMEOUT','Onderbreking');
define('_SRVFTP_TIMEOUTTEXT','Maximum inactiviteitsduur in seconden.');
define('_SRVFTP_ANONYMOUS','Anonieme aanmelding');
define('_SRVFTP_ENANONYMOUS','Anonieme aanmelding activeren.');
define('_SRVFTP_AUTH','Lokale gebruiker');
define('_SRVFTP_ENAUTH','Lokale gebruikersaanmelding activeren.');
define('_SRVFTP_FXP','FXP');
define('_SRVFTP_ENFXP','Het FXP protocol activeren.');
define('_SRVFTP_FXPTEXT','FXP laat bestandsoverdracht toe tussen twee externe servers zonder overdracht naar de client die de overdracht aanvraagt (niet veilig!).');
define('_SRVFTP_NATMODE','NAT modus');
define('_SRVFTP_ENNATMODE','Forceer NAT modus.');
define('_SRVFTP_NATMODETEXT','Activeren als de FTP server zich achter een NAT router bevindt die geen applicatieve proxy is voor FTP.');
define('_SRVFTP_PASSIVEIP','Passief IP address');
define('_SRVFTP_PASSIVEIPTEXT','Gebruik deze optie om het IP adres te omzeilen dat de FTP server zal adverteren in antwoord op het PASV commando.');
define('_SRVFTP_PASVMIN','pasv_min_port');
define('_SRVFTP_PASVMINTEXT','De minimum poort die toegewezen moet worden voor de PASV type dataverbindingen (0 = iedere poort is mogelijk).');
define('_SRVFTP_PASVMAX','pasv_max_port');
define('_SRVFTP_PASVMAXTEXT','De maximum poort die toegewezen moet worden voor de PASV type dataverbindingen (0 = iedere poort is mogelijk).');
define('_SRVFTP_BANNER','Banner');
define('_SRVFTP_BANNERTEXT','Begroetingsbanner die getoond wordt bij het maken van een FTP verbinding.');

// Service: NFS
// file: services_nfs.php
define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','Er moet een geldig netwerk moet gespecifieerd zijn.');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','NFS server');
define('_SRVNFS_MAPALLROOT','Link alle gebruikers aan root');
define('_SRVNFS_MAPALLROOTTEXT','Alle gebruikers gaan root-privilleges hebben.');
define('_SRVNFS_AUTHNET','Geautoriseerd netwerk');
define('_SRVNFS_AUTHNETTEXT','Netwerk dat geautoriseerd is om toegang te krijgen tot de NFS mappen');
define('_SRVNFS_AUTHNETMASK','Aantal bits voor het subnet');
define('_SRVNFS_TEXT','De namen van de ge&euml;xporteerde mappen zijn van de vorm: /mnt/mapnaam');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','Server');
define('_SRVRYNC_CLIENT','Client');
define('_SRVRYNC_LOCAL','Lokaal');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','Rsync Daemon');
define('_SRVRYNCD_READONLY','Alleen lezen');
define('_SRVRYNCD_MAPUSER','Linken naar gebruiker');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_TCPORTTEXT','Alternatieve TCP poort.');
define('_SRVRYNCD_MAXCON','Maximum aantal verbindingen');
define('_SRVRYNCD_MAXCONTEXT','Het maximum aantal gelijktijdige verbindingen. Standaard is dit 0 (ongelimiteerd).');
define('_SRVRYNCD_MOTD','BVDD');
define('_SRVRYNCD_MOTDTEXT','Bericht Van De Dag');

// file: services_rsyncd_client.php
define('_SRVRYNCC_MSGMPFIRST','Je moet eerst een koppelingspunt instellen.');
define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
define('_SRVRYNCC_RSYNCC','RSYNC client synchronisatie');
define('_SRVRYNCC_REMOTESERVER','RSYNC server op afstand');
define('_SRVRYNCC_REMOTEID','IP adres van de RSYNC server op afstand');
define('_SRVRYNCC_OPTIONS','RSYNC opties');
define('_SRVRYNCC_OPTDEL','Verwijder de bestanden niet niet op de "zender" staan.');
define('_SRVRYNCC_SHARES','Gedeelde mappen die moeten gesynchroniseerd worden');
define('_SRVRYNCC_SHARESTEXT','Deze zelfde lokale gedeelde map moet ook aanwezig zijn op de server op afstand.');
define('_SRVRYNCC_TIME','Synchronisatietijdstip');
define('_SRVRYNCC_TEXT',_MSGSCHED_TEXT);

// file: services_rsyncd_local.php
define('_SRVRYNCL_MSGVALID','Je kan niet hetzelfde koppelingspunt gebruiken voor bron en bestemming!');
define('_SRVRYNCC_RSYNCL','RSYNC lokale synchronisatie');
define('_SRVRYNCL_SOURCE','Bron');
define('_SRVRYNCL_SOURCETEXT','Bron koppelingspunt.');
define('_SRVRYNCL_DESTINATION','Bestemming');
define('_SRVRYNCL_DESTINATIONTEXT','Bestemming koppelingspunt (backup schijf).');


// Service: SSHD
// file: services_sshd.php
define('_SRVSSHD_NAMEDESC','SSHD');
define('_SRVSSHD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVSSHD_MSGVALIDKEY','Deze sleutel is ongeldig.');
define('_SRVSSHD_SSHD','SSH Daemon');
define('_SRVSSHD_READONLY','Alleen lezen');
define('_SRVSSHD_TCPORT',_TCPPORT);
define('_SRVSSHD_TCPORTTEXT',_SRVRYNCD_TCPORTTEXT);
define('_SRVSSHD_PERMITROOTLOGIN','root aanmelding toestaan');
define('_SRVSSHD_PERMITROOTLOGINTEXT','Specifies whether it is allowed to login as superuser (root) directly.');
define('_SRVSSHD_KEY','SSH sleutel');
define('_SRVSSHD_PRIVATEKEY','Private sleutel');
define('_SRVSSHD_PRIVATEKEYTEXT','Plak (copy/paste) een DSA PRIVATE SLEUTEL in PEM formaat hier.');

// Service: Unison
// file: services_unison.php
define('_SRVUNSN_NAMEDESC','Unison');
define('_SRVUNSN_MSGVALIDWRKDIR','De combinatie van gedeelde map en werkfolder bestaat niet.');
define('_SRVUNSN_UNISON','Unison bestandssynchronisatie');
define('_SRVUNSN_SHARE','Gedeelde map');
define('_SRVUNSN_SHAREDESC','Er moet voldoende plaats zijn om alle te synchroniseren bestanden te dupliceren');
define('_SRVUNSN_WORKDIR','Werkfolder');
define('_SRVUNSN_WORKDIRDESC','Plaats waar de werkbestanden zullen bewaard worden.');
define('_SRVUNSN_MAKEDIR','Werkmap aanmaken');
define('_SRVUNSN_MAKEDIRLABEL','Een werkmap aanmaken als deze niet bestaat');
define('_SRVUNSN_NOTESSH','<a href="/services_sshd.php">SSHD</a> moet geactiveerd zijn opdate Unison zou werken, en bijkomend moet voor de <a href="/access_users.php">gebruiker</a> de volledige gebruikersomgeving geactiveerd zijn.');

// Service: UPnP
// file: services_upnp.php
define('_SRVUPNP_NAMEDESC','UPnP');
define('_SRVUPNP_UPNP','UPnP A/V Media Server');
define('_SRVUPNP_MSGCHANGED','De inhoudmappenlijst is veranderd.<br>Je moet op \'toepassen\' klikken vooraleer de wijzigingen actief zijn.');
define('_SRVUPNP_NAME','Naam');
define('_SRVUPNP_NAMETEXT','UPnP herkenbare naam.');
define('_SRVUPNP_INTERFACE','Interface');
define('_SRVUPNP_INTERFACETEXT','Interface waarop UPnP moet luisteren.');
define('_SRVUPNP_CONTENT','Inhoud');
define('_SRVUPNP_CONTENTTEXT','Mappen die gedeeld moeten worden.');
define('_SRVUPNP_CONTENTINVALIDMSG','Er is geen inhoudmap die gedeeld wordt.');
define('_SRVUPNP_DIRECTORY','Map');
define('_SRVUPNP_ADDDIR','Map toevoegen');
define('_SRVUPNP_EDITDIR','Map bewerken');
define('_SRVUPNP_DELDIR','Map verwijderen');
define('_SRVUPNP_DELCONF','Ben je zeker dat je deze map wil verwijderen?');

// file: services_upnp_edit.php
define('_SRVUPNPEDIT_CONTENT','Inhoud');
define('_SRVUPNPEDIT_CONTENTTEXT','Map die gedeeld moet worden.');

// Service: AFP
// file: services_afp.php
define('_SRVAFP_NAMEDESC','AFP');
define('_SRVAFP_MSGVALIDAUTH','Je moet minstens &euml;&euml;n aanmeldingsmethode kiezen.');
define('_SRVAFP_AFPSRV','AFP Server');
define('_SRVAFP_AFPNAME','Server Naam');
define('_SRVAFP_AUTH','Aanmdelding');
define('_SRVAFP_ENABLEGUEST','Activeer gast-toegang.');
define('_SRVAFP_ENABLELOCAL','Activeer lokale gebruikersaanmelding.');

// Access
// common words
define('_ACCESS_NAME','Toegang');
define('_ACCESS_USER','Gebruiker');
define('_ACCESS_USERS','Gebruikers');
define('_ACCESS_GROUP','Groep');
define('_ACCESS_GROUPS','Groepen');
define('_ACCESS_FULLNAME','Volledige naam');

// file: access_users.php
define('_ACCESSUSERS_MSGCHANGED','De gebruikerslijst werd aangepast.<br>Je moet op \'toepassen\' klikken vooraleer de wijzigingen actief zijn.');
define('_ACCESSUSERS_EDITUSER','Gebruiker bewerken');
define('_ACCESSUSERS_DELUSER','Gebruiker verwijderen');
define('_ACCESSUSERS_CONFDEL','Ben je zeker dat je deze gebruiker wil verwijderen?!');
define('_ACCESSUSERS_ADDUSER','Gebruiker toevoegen');

// file: access_users_edit.php
define('_ACCESSUSERSEDIT_MSGGROUPFIRST','Je moet eerst een groep aanmaken.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGIN','De gebruikersnaam bevat ongeldige karakters.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED','De gebruikersnaam is een gereserveerde gebruikersnaam.');
define('_ACCESSUSERSEDIT_MSGVALIDFULLNAME','De  volledige naam bevat ongeldige karakters.');
define('_ACCESSUSERSEDIT_MSGVALIDDUPLICATE','Deze gebruiker bestaat al in de gebruikerslijst.');
define('_ACCESSUSERSEDIT_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSUSERSEDIT_MSGVALIDPASSWDBADCHAR','Het wachtwoord bevat het ongeldige ":" karakter');
define('_ACCESSUSERSEDIT_LOGIN',_LOGIN);
define('_ACCESSUSERSEDIT_LOGINTEXT','Unieke aanmeldingsnaam van de gebruiker.');
define('_ACCESSUSERSEDIT_FULLNAME',_ACCESS_FULLNAME);
define('_ACCESSUSERSEDIT_FULLNAMETEXT','Volledige naam.');
define('_ACCESSUSERSEDIT_PASSWORD','Wachtwoord');
define('_ACCESSUSERSEDIT_PASSWORDTEXT','Wachtwoord gebruiker.');
define('_ACCESSUSERSEDIT_PASSWORDCONF','Bevestig wachtwoord');
define('_ACCESSUSERSEDIT_PASSWORDCONFTEXT','Bevestig het wachtwoord.');
define('_ACCESSUSERSEDIT_GROUPMEMBER','Groepslid');
define('_ACCESSUSERSEDIT_FULLSHELL','Volledige gebruikersomgeving');
define('_ACCESSUSERSEDIT_FULLSHELLTEXT','Geef de volledige gebruikersomgeving aan de gebruiker');

// file: access_users_groups.php
define('_ACCESSGROUPS_MSGCHANGED','De groeplijst werd gewijzigd.<br>Je moet op \'toepassen\' klikken vooraleer de wijzigingen actief zijn.');
define('_ACCESSGROUPS_DESC',_DESC);
define('_ACCESSGROUPS_GROUPEDIT','Groep bewerken');
define('_ACCESSGROUPS_GROUPDEL','Groep verwijderen');
define('_ACCESSGROUPS_GROUPDELCONF','Ben je zeker dat je deze groep wil verwijderen?!');
define('_ACCESSGROUPS_GROUPADD','Groep toevoegen');

// file: access_users_groups_edit.php
define('_ACCESSGROUPSEDIT_MSGVALIDNAME','De naam van de groep bevat ongeldige karakters.');
define('_ACCESSGROUPSEDIT_MSGVALIDDESC','De omschrijving van de groep bevat ongeldige karakters.');
define('_ACCESSGROUPSEDIT_MSGVALIDDUPLICATE','Deze groep bestaat al in de groeplijst.');
define('_ACCESSGROUPSEDIT_NAME','Naam');
define('_ACCESSGROUPSEDIT_NAMETEXT','Groepsnaam.');
define('_ACCESSGROUPSEDIT_DESC',_DESC);
define('_ACCESSGROUPSEDIT_DESCTEXT','Groepsomschrijving.');

// file: access_ad.php
define('_ACCESSAD_NAME','Active Directory');
define('_ACCESSAD_MSGVALIDIP',_MSGVALIDIP);
define('_ACCESSAD_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSAD_ADNAME','AD servernaam');
define('_ACCESSAD_ADNAMETEXT','AD of PDC naam.');
define('_ACCESSAD_IP','AD server IP');
define('_ACCESSAD_IPTEXT','IP adres van de MS Active Directory server.');
define('_ACCESSAD_DOMAIN','Domeinnaam');
define('_ACCESSAD_DOMAINTEXT','Domeinnaam in oud formaat.');
define('_ACCESSAD_ADMINAME','Administrator naam');
define('_ACCESSAD_ADMINNAMETEXT','Gebruikersnaam van een domeinadministrator.');
define('_ACCESSAD_ADMINPASS','Administrator wachtwoord');
define('_ACCESSAD_ADMINPASSTEXT','Wachtwoord voor de opgegeven gebruikersnaam, geef hier twee maal in.');

// Diagnostics
define('_DIAG_NAME','Diagnostiek');

// file: shutdown.php
define('_SHUT_NAME',_MENULEFT_CMDSHUTDOWNSYS);
define('_SHUT_MSGSHUT','Het systeem wordt nu gestopt. Dit kan een minuut duren.');
define('_SHUT_CONF','Ben je zeker dat je het systeem wil uitschakelen?');

// file: shutdown_sched.php
define('_SHUTSCHED_NAME','Geplande uitschakeling');
define('_SHUTSCHED_TIME','Uitschakelingstijdstip');
define('_SHUTSCHED_TEXT',_MSGSCHED_TEXT);

// file: diag_edit.php
define('_DIAGEDITPHP_NAMEDESC','Bestand bewerken');
define('_DIAGEDITPHP_FILEPATH','Bestandspad');
define('_DIAGEDITPHP_SAVEMSG','Bestand bewaard op');
define('_DIAGEDITPHP_FILENOTFOUND','Bestand niet gevonden');
define('_DIAGEDITPHP_ROWS','Rijen');
define('_DIAGEDITPHP_COLS','Kolommen');
define('_DIAGEDITPHP_HIGHLIGHTING','Markeren');
define('_DIAGEDITPHP_HIGHLIGHTINGENABLED','Geactiveerd');
define('_DIAGEDITPHP_HIGHLIGHTINGDISABLED','Niet geactiveerd');

// file: exec.php
define('_DIAGEXECPHP_NAMEDESC','Commando uitvoeren');

// file: reboot.php
define('_DIAGREBOOT_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGREBOOT_NAMEDESC',_MENULEFT_CMDREBOOTSYS);
define('_DIAGREBOOT_REBOOTMSG','Het systeem is nu aan het herstarten. Dit kan een minuut duren.');
define('_DIAGREBOOT_REBOOTCONF','Ben je zeker dat je het systeem wil herstarten?');

// file: license.php
define('_LICENSE_NAME','Licentie');

// file: status_wireless.php
define('_STATSWIRELESS_NAME',_MENULEFT_STATUS);
define('_STATSWIRELESS_NAMEDESC','Draadloos');
define('_STATSWIRELESS_INTERFACE','%s interface (SSID \'%s\')');
define('_STATSWIRELESS_SIGNALSTRENGTHCACHE','Cache voor signaalsterkte');
define('_STATSWIRELESS_MACADDRESS','MAC adres');
define('_STATSWIRELESS_IPADDRESS','IP adres');
define('_STATSWIRELESS_SIGNAL','Signaal');
define('_STATSWIRELESS_NOISE','Ruis');
define('_STATSWIRELESS_QUALITY','Kwaliteit');
define('_STATSWIRELESS_ASSOCIATEDSTATIONS','Gekoppelde stations');
define('_STATSWIRELESS_TXRATES','TX (verzend) snelheden');
define('_STATSWIRELESS_MSGNOWINTFOUND','Er zijn geen ondersteunde draadloze interfaces gevonden om de status weer te geven (alleen kaarten die het wi[n] stuurprogramma gebruiken worden ondersteund).');

// file: status_interfaces.php
define('_STATSINTERFACES_NAME',_MENULEFT_STATUS);
define('_STATSINTERFACES_NAMEDESC',_MENULEFT_INTERFACES);
define('_STATSINTERFACES_INTERFACE','%s interface');
define('_STATSINTERFACES_STATUS','Status');
define('_STATSINTERFACES_DHCP','DHCP');
define('_STATSINTERFACES_PPPOE','PPPoE');
define('_STATSINTERFACES_PPTP','PPTP');
define('_STATSINTERFACES_MACADDRESS','MAC adres');
define('_STATSINTERFACES_IPADDRESS','IP adres');
define('_STATSINTERFACES_SUBNETMASK','Subnet mask');
define('_STATSINTERFACES_GATEWAY','Gateway');
define('_STATSINTERFACES_ISPDNSSERVERS','ISP DNS servers');
define('_STATSINTERFACES_MEDIA','Media');
define('_STATSINTERFACES_CHANNEL','Kanaal');
define('_STATSINTERFACES_SSID','SSID');
define('_STATSINTERFACES_IOPACKETS','I/O paketten');
define('_STATSINTERFACES_IOERRORS','I/O fouten');
define('_STATSINTERFACES_COLLISIONS','Botsingen');

// file: status_graph_cpu.php
define('_STATSGRAPHCPU_NAME',_MENULEFT_STATUS);
define('_STATSGRAPHCPU_NAMEDESC','CPU belasting');

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
define('_INTVLANEDIT_EDITVLAN','VLAN bewerken');

// file: diag_ping.php
define('_DIAGPING_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGPING_NAMEDESC',_MENULEFT_DIAGPINGTRACE);

// file: diag_traceroute.php
define('_DIAGTRACEROUTE_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGTRACEROUTE_NAMEDESC',_MENULEFT_DIAGPINGTRACE);
define('_DIAGTRACEROUTE_NOTE','Het kan even duren vooraleer traceroute klaar is. Ja mag altijd op de stop knop van je browser klikken om de vooruitgang te zien van de gefaalde traceroutes.');

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
define('_DIAGINFOS_PARTITIONS','Partities');
define('_DIAGINFOS_SMART','S.M.A.R.T.');
define('_DIAGINFOS_ATAIDLE','ATAidle');
define('_DIAGINFOS_SPACEUSED','Gebruikte ruimte');
define('_DIAGINFOS_MOUNTS','Koppelingen');
define('_DIAGINFOS_SOFTWARERAID',_SOFTRAID);
define('_DIAGINFOS_ISCSI','iSCSI');
define('_DIAGINFOS_MSDOMAIN','MS Domein');

// file: diag_infos.php
define('_DIAGINFOSDISKS_ATA_INFO','Lijst van gedetecteerde ATA schijven');
define('_DIAGINFOSDISKS_SCSI_INFO','Lijst van gedetecteerde SCSI schijven');

// file: diag_infos_mount.php
define('_DIAGINFOSMOUNT_INFO','Lijst van koppelpunten');

// file: diag_infos_space.php
define('_DIAGINFOSSPACE_INFO','Beschikbare (vrije) ruimte');

// file: diag_defaults.php
define('_DIAGDEFAULTS_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGDEFAULTS_NAMEDESC',_MENULEFT_CMDFACTORYDEF);

// file: diag_backup.php
define('_DIAGBACKUP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGBACKUP_NAMEDESC',_MENULEFT_CMDBACKUPRESTORE);
define('_DIAGBACKUP_RESTORENOTE','FreeNAS zal herstarten na het terugschrijven van de instellingen.');

// file: diag_arp.php
define('_DIAGARP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGARP_NAMEDESC',_MENULEFT_DIAGARPTAB);
define('_DIAGARP_IPADDRESS','IP adres');
define('_DIAGARP_MACADDRESS','MAC adres');
define('_DIAGARP_HOSTNAME','Hostnaam');
define('_DIAGARP_INTERFACE','Interface');
define('_DIAGARP_DEL','ARP ingang verwijderen');
define('_DIAGARP_DELALL','Alle ARP ingangen verwijderen uit de tabel');
define('_DIAGARP_NOTE','IP adressen worden vertaald naar hostnamen indien &quot;Resolve IP addresses to hostnames&quot; aangevinkt is op de <a href="diag_logs_settings.php">Diagnostiek: Logs</a> pagina.');

// file: status_graph.php
define('_STATSGRAPH_NAME',_MENULEFT_STATUS);
define('_STATSGRAPH_NAMEDESC','Grafiek van het netwerkverkeer');

// file: graph.php
// Note for translator: Do not use international html coding here
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

