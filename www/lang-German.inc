<?php
/*
    lang-Deutsch.php
    part of FreeNAS (http://www.freenas.org)
    Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
    All rights reserved.

    German translation done by
    Dominik Plaszewski domme555@gmx.net
    Falk Menzel fmenzel@htwm.de

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
define('_LOCALE','de_DE');
define('_LOCALEWIN', 'de');
// date and time defines
define('_DATE','Datum');
define('_DATEBRIEF','%b %d, %Y');
define('_DATELONG','%A, %B %d, %Y');
define('_DATESTRING','%A, %B %d @ %H:%M:%S');
define('_DATETIMEBRIEF','%b %d, %Y - %I:%M %p');
define('_DATETIMELONG','%A, %B %d, %Y - %I:%M %p');
define('_DAY_OF_WEEK_LONG','Sonntag Montag Dienstag Mittwoch Donnerstag Freitag Samstag');
define('_DAY_OF_WEEK_SHORT','So Mo Di Mi Do Fr Sa');
define('_MONTH_LONG','Januar Februar M&auml;rz April Mai Juni Juli August September Oktober November Dezember');
define('_MONTH_SHORT','Jan Feb M&auml;r Apr Mai Jun Jul Aug Sep Okt Nov Dez');
define('_TIME', 'Zeit');
define('_TIMEBRIEF','%I:%M %p');
define('_SECOND', 'Sekunde');
define('_SECONDS', 'Sekunden');
define('_MINUTE', 'Minute');
define('_MINUTES', 'Minuten');
define('_HOUR', 'Stunde');
define('_HOURS', 'Stunden');
define('_DAY', 'Tag');
define('_DAYS', 'Tage');
define('_WEEK', 'Woche');
define('_WEEKS', 'Wochen');
define('_WEEKDAYS', 'Wochentag');
define('_MONTH', 'Monat');
define('_MONTHS', 'Monate');
define('_YEAR', 'Jahr');
define('_YEARS', 'Jahre');

// common words
define('_ALL','Alle');
define('_AND','und');
define('_BY','mit');
define('_DOWN','Ab');
define('_NO','Nein');
define('_OF','Aus');
define('_OK','OK');
define('_ON','Ein');
define('_TO','Zu');
define('_THE', 'Der');        // <- Klar, M&auml;nner sind das non-plus-ultra, aber was ist mit Die und Das? ;-)
define('_UP','Auf');
define('_URL', 'URL');
define('_YES','Ja');

// common actions
define('_ACTIVATE','Aktivieren');
define('_ADD','Hinzuf&uuml;gen');
define('_CANCEL', 'Abbrechen');
define('_COMMIT', 'Best&auml;tigen');
define('_CREATE', 'Erstellen');
define('_DEACTIVATE','Deaktivieren');
define('_DELETE','L&ouml;schen');
define('_EDIT','&Auml;ndern');
define('_ENABLE','Freigeben');
define('_HELP', 'Hilfe');
define('_IGNORE','Ablehnen');
define('_LOGIN','Anmelden');
define('_LOGOUT','Abmelden');
define('_MODIFY','Modifizieren');
define('_NEW','Neu');
define('_SEARCH', 'Suchen');
define('_SAVE', 'Sichern');
define('_SUBMIT','&Uuml;bertragen');
define('_UPDATE', 'Update');
define('_VIEW', 'Ansicht');
define('_APPLY', '&Auml;nderung &uuml;bernehmen');
define('_WARNING', 'Warnung');
define('_LOAD', 'Laden');
define('_RETRY', 'Wiederholen');

//FreeNAS common words
define('_SIZE', 'Gr&ouml;sse');
define('_STATUS', 'Status');
define('_TYPE', 'Typ');
define('_CONFIGURING', 'Konfiguriere');
define('_DISK', 'Festplatte');
define('_DISKS', 'Festplatten');
define('_PARTITION', 'Partition');
define('_FILESYSTEM', 'Dateisystem');
define('_DESC', 'Beschreibung');
define('_SOFTRAID', 'Software RAID');
define('_SERVICE','Service');
define('_SERVICES','Services');
define('_TCPPORT','TCP Port');
define('_CONFIRMATION','Best&auml;tigung');
define('_NOTE','Hinweis');
define('_SUCCESSFUL', 'Erfolgreich');
define('_FAILED', 'Fehlgeschlagen');
define('_ERROR', 'Fehler');

// common messages
define('_MESSAGES_SUCCESS', 'Die &Auml;nderungen wurden &uuml;bernommen.');
define('_MESSAGES_SUCCESSWITHREBOOT', 'Die &Auml;nderungen wurden gespeichert. Sie m&uuml;ssen <a href="/reboot.php">neu starten</a> damit Ihr NAS die &Auml;nderungen &uuml;bernimmt.');
define('_MESSAGES_ERROR', 'ERROR: die &Auml;nderungen konnten nicht &uuml;bernommen werden (error code '.$ok.').');

define('_MSGVALIDIP','Eine g&uuml;ltige IP Adresse muss eingegeben werden.');
define('_MSGVALIDNETBIT','Eine g&uuml;ltige Subnet Adressierung muss eingegeben werden.');
define('_MSGVALIDTCPPORT','Der TCP/IP Port muss eingetragen werden.');
define('_MSGVALIDPASSWORD','Passwort FALSCH.');

define('_MSGSCHED_TEXT','Hinweis: Strg-Klick (oder Kommando-Klick auf dem Mac) um Stunden, Tage und Monate zu w&auml;hlen bzw. abzuw&auml;hlen.');

// states
define('_ACTIVE','Aktiv');
define('_INACTIVE','Inaktiv');
define('_SELECTED','Ausgew&auml;hlt');

// Webgui layout
define('_MENULEFT_SYSTEM','System');
define('_MENULEFT_SYSGENSETUP','Allgemeine Einstellungen');
define('_MENULEFT_SYSSTATROUTES','Statische Route');
define('_MENULEFT_SYSFIRMWARE','Firmware');
define('_MENULEFT_SYSADVANCED','Erweitert');
define('_MENULEFT_INTERFACES','Schnittstellen');
define('_MENULEFT_INTASSIGN','Zuweisen');
define('_MENULEFT_INTLAN','Netzwerk');
define('_MENULEFT_DISKS',_DISKS);
define('_MENULEFT_DISKMANAG','Management');
define('_MENULEFT_DISKMNGSOFTRAID',_SOFTRAID);
define('_MENULEFT_DISKMNGMOUNTPOINT','Mount Point');
define('_MENULEFT_SERVICES','Dienste');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVUNSN','Unison');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','Zugriff');
define('_MENULEFT_ACUSERGROUPS','Benutzer & Gruppen');
define('_MENULEFT_ACMSAD','Active Directory'); // Nicht übersetzen, technischer Begriff
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS',_STATUS);
define('_MENULEFT_STSYSTEM','System');
define('_MENULEFT_STPROCESS','Prozesse');
define('_MENULEFT_STINTERFACES','Schnittstellen');
define('_MENULEFT_STDISKS',_DISKS);
define('_MENULEFT_STWIRELESS','Drahtlos');
define('_MENULEFT_EXTENSIONS','Erweiterungen');
define('_MENULEFT_DIAGNOSTICS','Diagnose');
define('_MENULEFT_DIAGLOGS','Log Daten');
define('_MENULEFT_DIAGINF','Informationen');
define('_MENULEFT_DIAGPINGTRACE','Ping / Traceroute');
define('_MENULEFT_DIAGARPTAB','ARP Tabelle');
define('_MENULEFT_DIAGEXEC','Befehl');
define('_MENULEFT_DIAGEDITFILE','Datei bearbeiten');
define('_MENULEFT_CMDBACKUPRESTORE','Sichern/Wiederherstellen');
define('_MENULEFT_CMDFACTORYDEF','Werkseinstellungen');
define('_MENULEFT_CMDREBOOTSYS','System Neustarten');
define('_MENULEFT_CMDSHUTDOWNSYS','System Herunterfahren');
define('_MENULEFT_GRAPH','Graph');

// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION',_MENULEFT_SYSGENSETUP);
define('_SYSTEMPHP_HOSTNAME','Host Name');
define('_SYSTEMPHP_HOSTNAMETEXT','Name des NAS Host, ohne Domain<br>z.B. <em>nas');
define('_SYSTEMPHP_DOMAIN','Domain');
define('_SYSTEMPHP_DOMAINTEXT','z.B. <em>mycorp.com</em>');
define('_SYSTEMPHP_DNS','DNS Server');
define('_SYSTEMPHP_DNSTEXT','IP Addresse');
define('_SYSTEMPHP_USERNAME','Benutzername');
define('_SYSTEMPHP_USERNAMETEXT','Wenn Sie den Benutzernamen f&uuml;r die Web-Gui &auml;ndern m&ouml;chten, tragen Sie ihn hier ein.');
define('_SYSTEMPHP_PASSWORD','Passwort');
define('_SYSTEMPHP_PASSWORDTEXT','Wenn Sie den Benutzernamen f&uuml;r die Web-Gui &auml;ndern m&ouml;chten, tragen Sie ihn hier nochmals ein.<br>Bitte kein : benutzen');
define('_SYSTEMPHP_WEBGUIPROTOCOL','WebGUI Protokoll');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','');
define('_SYSTEMPHP_WEBGUIPORT','WebGUI Port');
define('_SYSTEMPHP_WEBGUIPORTTEXT','Tragen Sie einen Port f&uuml;r die WebGUI ein, wenn Sie den Standardport ver&auml;ndern wollen (80 f&uuml;r HTTP, 443 f&uuml;r HTTPS).');
define('_SYSTEMPHP_LANGUAGE','Sprache');
define('_SYSTEMPHP_LANGUAGETEXT','W&auml;hlen Sie die Systemsprache');
define('_SYSTEMPHP_TIMEZONE','Zeitzone');
define('_SYSTEMPHP_TIMEZONETEXT','W&auml;hlen Sie den Standort, der Ihnen am n&auml;chsten liegt');
define('_SYSTEMPHP_TIMEUPDATEINT','Zeit Update Interval');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','Minuten zwischen Netzwerkzeit Synchronisation.; 300 vorgeschlagen, oder 0 um auszuschalten');
define('_SYSTEMPHP_NTPSERVER','NTP Zeit Server');
define('_SYSTEMPHP_NTPSERVERTEXT','Benutzen Sie ein Leerzeichen um mehrere Hosts zu trennen (es ist nur ein Host n&ouml;tig). Denken Sie daran einen                   einen DNS Server einzutragen, wenn Sie hier einen Hostnamen eintragen!');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','System Information');
define('_INDEXPHP_NAME','Name');
define('_INDEXPHP_VERSION','Version');
define('_INDEXPHP_OSVERSION','Betriebssystemversion'); //OS Version
define('_INDEXPHP_PLATFORM','Plattform');
define('_INDEXPHP_DATE','Datum');
define('_INDEXPHP_UPTIME','Laufzeit');
define('_INDEXPHP_LASTCHANGE','Letzte &Auml;nderung abgespeichert');
define('_INDEXPHP_MEMUSE','Speicher Belegung');
define('_INDEXPHP_LOADAVERAGE','Lade Durchschnitte');
define('_INDEXPHP_SHOWPROCESSING','Zeige Prozess Informationen');
define('_INDEXPHP_DISKS_USE','Festplattenauslastung');
define('_INDEXPHP_NODISK','Keine Festplatte konfiguriert');

// Status Processes Module
// file: status_process.php
define('_STATSPROCPHP_NAME',_STATUS);
define('_STATSPROCPHP_NAME_DESCRIPTION',_MENULEFT_STPROCESS);
define('_STATSPROCPHP_PROC_INF','Prozessinformationen'); //Plural

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION',_MENULEFT_SYSSTATROUTES);
define('_SYSTEMROUTESPHP_MSG_CHANGED','"Static Route" wurde ge&auml;ndert! .<br>Sie m&uuml;ssen die &Auml;nderung der Einstellungen best&auml;tigen, damit diese &uuml;bernommen werden.'); //eine &uuml;bernommene &Auml;nderung nochmals &uuml;bernehmen ;-)
define('_SYSTEMROUTESPHP_INT','Schnittstelle');
define('_SYSTEMROUTESPHP_NET','Netzwerk');
define('_SYSTEMROUTESPHP_GW','Gateway');
define('_SYSTEMROUTESPHP_DESC',_DESC);
define('_SYSTEMROUTESPHP_EDITROUTE','Route bearbeiten');
define('_SYSTEMROUTESPHP_MSGCONFIRM','Wollen Sie diese Route wirklich l&ouml;schen?');
define('_SYSTEMROUTESPHP_DELROUTE','L&ouml;sche Route');
define('_SYSTEMROUTESPHP_ADDROUTE','F&uuml;ge Route hinzu');

// Static Routes Edit Module
// file: system_routes_edit.php
define('_SYSTEMROUTESPHP_MSGVALID','Ein g&uuml;ltiges Zielnetzwerk muss angegeben werden.'); //Netzwerk
define('_SYSTEMROUTESPHP_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SYSTEMROUTESPHP_MSGVALIDGW','Eine g&uuml;ltige Gateway IP Adresse muss angegeben werden.');
define('_SYSTEMROUTESPHP_MSGROUTEEXIST','Eine Route zu diesem Netzwerk besteht bereits.');
define('_SYSTEMROUTESPHP_CHOOSEINT','W&auml;hlen Sie eine Netzwerkschnittstelle zu dieser Route.');
define('_SYSTEMROUTESPHP_DESTNET','Zielnetzwerk');
define('_SYSTEMROUTESPHP_DESTNETROUTE','Zielnetzwerk f&uuml;r diese Statische Route');
define('_SYSTEMROUTESPHP_GWTONET','Gateway um dieses Netzwerk zu erreichen');
define('_SYSTEMROUTESPHP_DESCREF','Hier k&ouml;nnen Sie eine Beschreibung eingeben (nicht &uuml;berpr&uuml;ft!).');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMFIRMWAREPHP_NAMEDESC',_MENULEFT_SYSFIRMWARE);
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','Dieses Image ist nicht f&uuml;r diese Plattform geeignet');
define('_SYSTEMFIRMWAREPHP_OUTMEM','Image Upload fehlgeschlagen (zuwenig Speicher?)');
define('_SYSTEMFIRMWAREPHP_INVSIG','Die digitale Signatur dieses Image ist ung&uuml;ltig.');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','Dieses Image ist nicht digital signiert.');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','Ein Fehler bei der &Uuml;berpr&uuml;fung der Signatur des Image ist aufgetreten.');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','Dieses Image ist defekt');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','Diese Firmware wird nun installiert. FreeNAS wird danach automatisch neu starten.');
define('_SYSTEMFIRMWAREPHP_NOTSUP','Firmware Upload wird auf dieser Plattform nicht unterst&uuml;tzt.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','Das bedeutet, dass das Image, das Sie geladen haben ' .
      'kein offiziell unterst&uuml;tztes Image ist und eventuell die Sicherheit des Systems gef&auml;hrdet ' .
      'Installieren Sie nur Images, die nur von vertrauten Quellen stammen, und &uuml;berpr&uuml;fen '.
      'Sie, dass das Image nicht manipuliert wurde.<br><br>'.  //abgegeben ?
      'M&ouml;chten Sie das Image trotzdem installieren (auf eigene Gefahr)?');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','Dr&uuml;cken Sie unten &quot;Firmware Upload 
      zulassen&quot;, dann w&auml;hlen Sie das Image '.$g['fullplatform'].'-*.img
      zum hochladen.<br>Dr&uuml;cken Sie &quot;Upgrade Firmware&quot;
      zum Starten des Upgrade Prozesses.');
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','Firmware Upload zulassen');
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','Firmware Upload verbieten');
define('_SYSTEMFIRMWAREPHP_FIRMFILE','Firmware Image');
define('_SYSTEMFIRMWAREPHP_UPFIRM','Upgrade Firmware');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','Sie m&uuml;ssen das System neu starten, bevor Sie diese Firmware upgraden k&ouml;nnen.');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','Unterbrechen Sie das Firmware Upgrade NICHT, wenn es bereits gestartet wurde. FreeNAS wird automatisch neu gestartet, nachdem die neue Firmware geladen wurde. Ihre Konfiguration bleibt erhalten.<br>
      F&uuml;r das Upgrade werden mindestens 128 Mb RAM ben&ouml;tigt.');


// System Advanced Module
// file: system_advanced.php
define('_SYSTEMADVANCEDPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMADVANCEDPHP_NAMEDESC',_MENULEFT_SYSADVANCED);
define('_SYSTEMADVANCEDPHP_MSGVALIDTCP','Der TCP idle Timeout muss ein Integerwert sein.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERTKEY','Zertifikate und Schl&uuml;sse m&uuml;ssen immer zusammen eingegeben werden.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERT','Dieses Zertifikat scheint nicht g&uuml;ltig zu sein.');
define('_SYSTEMADVANCEDPHP_MSGVALIDKEY','Dieser Schl&uuml;ssel scheint nicht g&uuml;ltig zu sein .');
define('_SYSTEMADVANCEDPHP_NOTE','Die Optionen auf dieser Seite sind nur f&uuml;r erfahrene Benutzer(!), und es besteht <strong>KEIN</strong> Support.');
define('_SYSTEMADVANCEDPHP_WEBGUISSLKEY','Web GUI SSL Zertifikat/Schl&uuml;ssel');
define('_SYSTEMADVANCEDPHP_WEBCERT','Zertifikate');
define('_SYSTEMADVANCEDPHP_WEBCERTTEXT','F&uuml;gen Sie ein Zertifikat in X.509 PEM Format ein.');
define('_SYSTEMADVANCEDPHP_WEBKEY','Schl&uuml;ssel');
define('_SYSTEMADVANCEDPHP_WEBKEYTEXT','F&uuml;gen Sie einen "RSA private key" im PEM Format ein.'); //Privatschl&uuml;ssel
define('_SYSTEMADVANCEDPHP_MISC','Verschiedenes');
define('_SYSTEMADVANCEDPHP_CON','Konsolen Men&uuml;');
define('_SYSTEMADVANCEDPHP_DISCON','Konsolen Men&uuml; abschalten.');
define('_SYSTEMADVANCEDPHP_CONTEXT','&Auml;nderungen in diesen Optionen werden erst nach einem Neustart wirksam.');
define('_SYSTEMADVANCEDPHP_FIRM','Firmware Version &Uuml;berpr&uuml;fung');
define('_SYSTEMADVANCEDPHP_DISFIRM','Firmware Version &Uuml;berpr&uuml;fung abschalten');
define('_SYSTEMADVANCEDPHP_FIRMTEXT','Dies bewirkt, dass FreeNAS keine &Uuml;berpr&uuml;fung auf neue Firmware bei Anzeige der <a href="system_firmware.php">System: Firmware</a> durchf&uuml;hrt.');
define('_SYSTEMADVANCEDPHP_TCPIDLE','TCP idle Timeout');
define('_SYSTEMADVANCEDPHP_TCPIDLETEXT','Leerlauf TCP Verbindungen werden von der Statusanzeige nach Ablauf der angegebenen Sekunden entfernt, wenn nach dieser Zeit keine Pakete mehr empfangen werden. Diesen Wert nicht zu hoch ansetzen, oder die Statusanzeige wird von falsch beendeten Verbindungen &uuml;berflutet. Voreinstellung sind 2,5 Stunden.');
define('_SYSTEMADVANCEDPHP_NAV','Navigation');
define('_SYSTEMADVANCEDPHP_NAVTEXT','Diagnoseanzeige in Navigationsansicht einblenden');
define('_SYSTEMADVANCEDPHP_BEEP','Systemlautsprecher piept');
define('_SYSTEMADVANCEDPHP_DISBEEP','Systemlautsprecher beim Start und Shutdown ausschalten.');
define('_SYSTEMADVANCEDPHP_SMART','S.M.A.R.T Daemon');
define('_SYSTEMADVANCEDPHP_ENSMART','S.M.A.R.T daemon aktivieren');
define('_SYSTEMADVANCEDPHP_SMARTTEXT','S.M.A.R.T-f&auml;hige Ger&auml;te &uuml;berwachen durch Aufzeichnung ihres Status in Logdatei.');
define('_SYSTEMADVANCEDPHP_TUNING','Tuning');
define('_SYSTEMADVANCEDPHP_ENTUNING','Aktiviere "Tuning" einiger "Kernel" Variablen');
define('_SYSTEMADVANCEDPHP_ZEROCONF','ZeroConf');
define('_SYSTEMADVANCEDPHP_DISZEROCONF','Deaktiviere "ZeroConf service announce"');

// Interface assing:
// file: interfaces_assign.php
define('_INTASSIGNPHP_NAME','Schnittstellen');
define('_INTASSIGNPHP_NAMEDESC','Zuweisung Netzwerkports');
define('_INTASSIGNPHP_MSGVALIDPORT','Port ');
define('_INTASSIGNPHP_MSGVALIDPORTWASASSIG',' wurde zugewiesen an ');
define('_INTASSIGNPHP_MSGVALIDINT',' Schnittstellen:');
define('_INTASSIGNPHP_INT','Schnittstellen');
define('_INTASSIGNPHP_PORT','Netzwerkport');
define('_INTASSIGNPHP_DELINT','L&ouml;sche Schnittstelle');
define('_INTASSIGNPHP_ADDINT','F&uuml;ge Schnittstelle hinzu');
define('_INTASSIGNPHP_INTTEXT','Nach dem Anw&auml;hlen von &quot;Sichern&quot;, muss FreeNAS zur &Uuml;bernahme der &Auml;nderungen neu gestartet werden. Sie k&ouml;nnten auch einige der folgenden Dinge vor erneutem Zugriff auf ihren NAS ausf&uuml;hren m&uuml;ssen: </span></p>
<ul>
  <li><span class="vexpl">&Auml;ndern Sie die IP-Adresse ihres Computers </span></li>
  <li><span class="vexpl">Greifen Sie auf die webGUI mit der neuen IP-Addresse zu</span></li>
</ul>');

// Interface LAN/OPT:
// file: interfaces_lan.php
// file: interfaces_opt.php
define('_INTLANPHP_NAME','Schnittstellen');
define('_INTLANPHP_NAMEDESC','LAN');
define('_INTPHP_MSGVALIDIP',_MSGVALIDIP);
define('_INTPHP_MSGVALIDMASK',_MSGVALIDNETBIT);
define('_INTPHP_MSGVALIDGW','Ein g&uuml;ltiges Gateway muss angegeben werden.');
define('_INTPHP_MSGVALIDMTU','Eine g&uuml;ltige "MTU" muss angegeben werden.');
define('_INTPHP_IP','IP-Adresse');
define('_INTPHP_GW','Gateway');
define('_INTPHP_MTU','MTU');
define('_INTPHP_MTUTEXT','Standard MTU ist 1500, 9000 f&uuml;r "jumbo frame" benutzen.');
define('_INTPHP_POLLING','Device polling');
define('_INTPHP_ENPOLLING','Aktiviere "device polling"');
define('_INTPHP_POLLINGTEXT','"Device polling" ist eine Technik, welche das System periodisch Netzwerkger&auml;ten auf neue Daten anspricht
                    anstatt sich auf "interrupts" zu verlassen. Dies kann die Prozessorauslastung veringern und dadurch den Durchsatz erh&ouml;hen,
                     auf Kosten eines geringf&uuml;gig h&ouml;heren "forwarding delays" (die Ger&auml;te werden 1000 mal
                    je Sekunde abgefragt). Nicht alle Netzwerkkarten unterst&uuml;tzen "polling"; sehen Sie auf der m0n0wall Homepage in die Liste der unterst&uuml;tzen Netzwerkkarten.');
define('_INTPHP_SPEED','Geschwindigkeit');
define('_INTPHP_DUPLEX','Duplex');
define('_INTPHP_TEXT','Nach Klick auf &quot;Sichern&quot;, k&ouml;nnten Sie auch einige der folgenden Dinge
                    vor erneutem Zugriff auf ihren NAS ausf&uuml;hren m&uuml;ssen:
                    <ul>
                      <li>&Auml;ndern Sie die IP-Adresse ihres Computers</li>
                      <li>Greifen Sie auf die webGUI mit der neuen IP-Addresse zu</li>
                    </ul>');
define('_INTPHP_ENOPT','Aktiviere optionale ');
define('_INTPHP_ENOPTINT',' Schnittstelle');
define('_INTPHP_DESC',_DESC);
define('_INTPHP_DESCTEXT','Geben Sie eine Beschreibung (Name) f&uuml;r die Schnittstelle hier ein.');
define('_INTPHP_STATIC','Statische IP Einstellungen');
define('_INTPHP_DHCP','DHCP Client Einstellungen');
define('_INTPHP_GENERAL','Allgemeine Einstellungen');
define('_INTPHP_DHCPHOSTNAME','Hostname');
define('_INTPHP_DHCPHOSTNAMETEXT','The value in this field is sent as the DHCP hostname when requesting a DHCP lease.');
define('_INTPHP_DHCPCLIENTIDENTIFIER','Client Identifier');
define('_INTPHP_DHCPCLIENTIDENTIFIERTEXT','The value in this field is sent as the DHCP client identifier when requesting a DHCP lease.');
define('_INTPHP_CALCNETMASKBITS','Berechne Netmask Bits');

// Disks: Manage
// Common text
define('_DISKSPHP_NAME',_DISKS);
define('_DISKSPHP_MANAGE','Managen');
define('_DISKSPHP_FORMAT','Formatieren');
define('_DISKSPHP_TOOLS','Tools');
define('_DISKSPHP_ISCSIINIT','iSCSI initiator');
define('_DISKSPHP_DISK',_DISK);
define('_DISKSPHP_FILESYSTEM',_FILESYSTEM);

// file: disks_manage.php
define('_DISKSMANAGEPHP_NAMEDESC','Management');
define('_DISKSMANAGEPHP_MSGCHANGED','Der Festplattenstatus hat sich ge&auml;ndert.<br>Zur &Uuml;bernahme der &Auml;nderungen bitte best&auml;tigen.');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','Standbyzeit');
define('_DISKSMANAGEPHP_FSUNKNOWN','Unbekannt oder nicht formatiert');
define('_DISKSMANAGEPHP_EDIT','Bearbeite Festplatte');
define('_DISKSMANAGEPHP_DEL','L&ouml;sche Festplatte');
define('_DISKSMANAGEPHP_DELCONF','Wollen Sie diese Festplatte wirklich l&ouml;schen? Alle Elemente in derzeitiger Benutzung werden ung&uuml;ltig werden (z.B. Freigaben)!');
define('_DISKSMANAGEPHP_ADD','Festplatte hinzuf&uuml;gen');
define('_DISKSMANAGEPHP_NOTE','Erster Bearbeitungsschritt: F&uuml;gen Sie ihre Festplatte zur Festplatteliste hinzu.');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','Diese Festplatte ist bereits in der Festplatteliste enthalten.');
define('_DISKSMANAGEEDITPHP_UDMA','UDMA Modus');
define('_DISKSMANAGEEDITPHP_UDMATEXT','Sie k&ouml;nnen den UDMA Modus erzwingen falls Sie einen "UDMA_ERROR.... LBA" Meldung zu ihrer Festplatte erhalten.');
define('_DISKSMANAGEEDITPHP_STANDBY','Festplatten Standbyzeit');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','Festplatte nach dem letzten Zugriff nach der angegebenen Zeit
                    in Standby versetzen. <em>Nicht f&uuml;r CompactFlash (CF) Karten verwenden.</em>');
define('_DISKSMANAGEEDITPHP_APM','Advanced Power Management');
define('_DISKSMANAGEEDITPHP_APMTEXT','Verringert den Stromverbrauch der Festplatte auf Kosten der Geschwindigkeit.<em>Nicht f&uuml;r CompactFlash (CF) Karten verwenden.</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','Acoustic Level');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','Lautst&auml;rke des Laufwerks w&auml;hrend des Betriebs einstellen.<em>Nicht f&uuml;r CompactFlash (CF) Karten verwenden.</em>');
define('_DISKSMANAGEEDITPHP_PREFS','Preformated FS');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','Dateisystem von bereits mit Dateisystem und Daten versehenen Festplatten/Partitionen einstellen.<br>
                     <em>Voreinstellung "Unformated" f&uuml;r unformatierte Festplatte &uuml;bernehmen, dann das "Format menu" benutzen.</em>');

//file: disks_manage_init.php
define('_DISKSMANAGEINITPHP_NAMEDESC','Initialisierung');
define('_DISKSMANAGEINITPHP_DISKMOUNTERROR','Diese Festplatte is momentan gemountet. <a href=%s>Unmounten</a> Sie diese Festplatte zuerst bevor sie fortfahren.');
define('_DISKSMANAGEINITPHP_INITTEXT','<strong>Festplatteninitialisierungseigenschaften:</strong>');
define('_DISKSMANAGEINITPHP_INITINFO','"fdisk: Geom not found" ist keine Fehlermeldung!\n');
define('_DISKSMANAGEINITPHP_TEXT','Dieser Vorgang wird alle bestehenden Partitionen l&ouml;schen, Partition #1 anlegen und diese mit dem gew&auml;hlten Dateisystem formatieren.');
define('_DISKSMANAGEINITPHP_FORMATDISC','Festplatte formatieren!');
define('_DISKSMANAGEINITPHP_FORMATDISCCONF','Wollen Sie diese Festplatte wirklich formatieren? Alle Daten gehen dabei verloren!');
define('_DISKSPHP_NOMBR','MBR (MasterBootRecord) nicht l&ouml;schen');
define('_DISKSPHP_NOMBRTEXT','Nicht den MBR l&ouml;schen (bei einigen Hardware RAID Kontrollerkarten empfehlenswert)'); //neu hinzugekommen

//file: disks_manage_tools.php
define('_DISKSMANAGETOOLS_NAMEDESC','Tools');
define('_DISKSMANAGETOOLS_COMMAND','Befehl');
define('_DISKSMANAGETOOLS_SENDCMD','Sende Befehl!');
define('_DISKSMANAGETOOLS_RAIDDISKNOTE','Ben&uuml;tzen Sie <a href="%s">RAID tools</a> für diese Festplatte!');
define('_DISKSMANAGETOOLS_CMDINFO','Befehlsausgabe:');
define('_DISKSMANAGETOOLS_MOUNTNOTE','Diese Festplatte is momentan gemountet. Der Mount Point wird kurzzeitig entfernt um den ausgew&auml;hlten Befehl auszuf&uuml;hren.');
define('_DISKSMANAGETOOLS_UMOUNT','Unmount Festplatte/Partition');
define('_DISKSMANAGETOOLS_UMOUNTTEXT','Wenn die ausgew&auml;hlte Festplatte/Partition gemountet ist wird diese kurzzeitig ungemountet um den ausgew&auml;hlten Befehl auszuf&uuml;hren, sonst arbeiten die Befehle in nur-lesen Modus.');

//file: disks_manage_iscsi.php
define('_DISKSMANAGEPHP_NAMEDESC','Management');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','iSCSI Initiator');
define('_DISKSMANAGEISCSIPHP_TARGETIP','Ziel IP Adresse');
define('_DISKSMANAGEISCSIPHP_TARGETIPTEXT','IP Adresse des iSCSI target.');
define('_DISKSMANAGEISCSIPHP_TARGETNAME','Targetname');
define('_DISKSMANAGEISCSIPHP_TARGETNAMETEXT','Targetname.');

//Software RAID
// Comon value
define('_DISKSRAIDPHP_MANAGE','RAID-Einstellungen');
define('_DISKSRAIDPHP_FORMAT','RAID formatieren');
define('_DISKSRAIDPHP_TOOLS','Tools');
define('_DISKSRAIDPHP_INFO','Informationen');
define('_DISKSRAIDPHP_VOLUME','Volume Name');

// Software RAID common word
define('_DISKSRAIDPHP_GVINUM','Geom Vinum');
define('_DISKSRAIDPHP_GMIRROR','Geom Mirror');
define('_DISKSRAIDPHP_UNSTABLE','(unstable)');

//file: disks_raid.php
define('_DISKSRAIDPHP_NAMEDESC','RAID');
define('_DISKSRAIDPHP_MSGCHANGED','Die RAID-Einstellung wurde ver&auml;ndert.<br>Zur &Uuml;bernahme der &Auml;nderungen m&uuml;ssen diese best&auml;tigt werden.');
define('_DISKSRAIDPHP_TYPE','Type');
define('_DISKSRAIDPHP_EDITRAID','Bearbeite RAID');
define('_DISKSRAIDPHP_DELRAID','L&ouml;sche RAID');
define('_DISKSRAIDPHP_DELCONF','Soll dieses RAIDLaufwerk wirklich gel&ouml;scht werden? Alle Elemente, die gerade darauf zugreifen, werden ung&uuml;ltig werden (z.B. Freigaben)!');
define('_DISKSRAIDPHP_NOTE','Optionaler Konfigurationsschritt: Konfiguration einer "virtual RAID disk" unter Verwendung <a href="disks_manage.php">einer vorher eingestellten Festplatte/Partition</a>.<br>Den "up" Status vor Formatieren und Einbinden (Mounten) abwarten!');

//file: disks_raid_edit.php
define('_DISKSRAIDEDITPHP_NAMEDESC',_EDIT);
define('_DISKSRAIDEDITPHP_MSGADDDISKFIRST','Die Festplatten m&uuml;ssen zuerst hinzugef&uuml;gt werden.');
define('_DISKSRAIDEDITPHP_MSGVALIDNAME','Der Ger&auml;tename darf nur folgende Zeichen enthalten: a-z, A-Z, 0-9.');
define('_DISKSRAIDEDITPHP_MSGEXIST','Dieses Ger&auml;t ist bereits in der "raid volume list" enthalten.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID0','Mindestens zwei Festplatten sind f&uuml;r ein RAID 0 notwendig.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID1','Zwei Festplatten sind f&uuml;r ein RAID 1 notwendig.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID5','Mindestens drei Festplatten sind f&uuml;r ein RAID 5 notwendig.');
define('_DISKSRAIDEDITPHP_MSGERROR','Fehler: RAID-Konfiguration konnte nicht gespeichert werden.\n');
define('_DISKSRAIDEDITPHP_RAIDNAME','Raid Name');
define('_DISKSRAIDEDITPHP_GVBALANCEALGO','Balance Algorithmus');
define('_DISKSRAIDEDITPHP_GVBALANCEALGOTEXT','Auswahl des "Read Balance Algorithmus".');
define('_DISKSRAIDEDITPHP_RAID0','striping');
define('_DISKSRAIDEDITPHP_RAID1','mirroring');
define('_DISKSRAIDEDITPHP_RAID5','rotated block-interleaved parity');
define('_DISKSRAIDEDITPHP_MEMBERS','Verwendete Ger&auml;te f&uuml;r dieses RAID');

//file: disks_raid_info.php
define('_DISKSRAIDINFOPHP_TEXT','Software RAID Information und Status');

//file: disks_raid_init.php
define('_DISKSRAIDINITPHP_INFO','Disk format UFS output:');
define('_DISKSRAIDINITPHP_TEXT','Dies wird das RAID Volume mit dem Unix FileSystem (UFS) formatieren.');
define('_DISKSRAIDINITPHP_FORMAT','Formatiere UFS Festplatte!');

//file: disks_raid_tools.php
define('_DISKSRAIDTOOLSPHP_OBJNAME','Objektname');
define('_DISKSRAIDTOOLSPHP_COMMAND','Befehl');
define('_DISKSRAIDTOOLSPHP_INFO','GVINUM command output:');
define('_DISKSRAIDTOOLSPHP_SENDCMD','Sende Befehl!');
define('_DISKSRAIDTOOLSPHP_TEXT','1. Diese Massnahmen nur zur Fehlerbehebung benutzen!<br>2. Es ist nicht zum Start eines RAIDvolume notwendig (startet automatisch).');

//Disk Mount Point
// Comon value
define('_DISKSMOUNTPHP_NAME','Mount Point');
define('_DISKSMOUNTPHP_SHARENAME','Share Name');
define('_DISKSMOUNTPHP_MANAGE','Managen');
define('_DISKSMOUNTPHP_TOOLS','Tools');

// disks_mount.php
define('_DISKSMOUNTPHP_MSGCHANGED','Die Liste der Einbindungen (Mount Points) wurde ver&auml;ndert.<br>Zur &Uuml;bernahme der &Auml;nderungen m&uuml;ssen diese best&auml;tigt werden.');
define('_DISKSMOUNTPHP_NOTE','Erster Bearbeitungsschritt: Das Dateisystem der gerade eingerichteten Partition <a href="disks_manage.php">angeben</a>.');
define('_DISKSMOUNTPHP_DELCONF','Wollen Sie diesen MountPoint wirklich l&ouml;schen? Alle Elemente, die gerade darauf zugreifen, werden ung&uuml;ltig werden (z.B. Freigaben)');
define('_DISKSMOUNTPHP_DEL','MountPoint l&ouml;schen');
define('_DISKSMOUNTPHP_ADD','MountPoint hinzuf&uuml;gen');

// disks_mount_edit.php
define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','Der Freigabenname darf nur folgende Zeichen enthalten: a-z, A-Z, 0-9, _ , -.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','Die Beschreibung enth&auml;lt ung&uuml;ltige Zeichen.');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','Die Systempartition (1. Partition) kann nicht eingebunden werden, die notwendige Datenpartition ist die 2. Partition auf dem Datentr&auml;ger.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','Freigabenname duplizieren.');
define('_DISKSMOUNTEDITPHP_TEXT','1. Die Partition \'<?php echo htmlspecialchars($cfdevice);?>\' kann nicht eingebunden werden - sie enth&auml;lt die Konfigurationsdatei<br>2. Die NTFS-implemtentierung von FreeBSD hat viele Fehler.');

// disks_mount_tools.php
define('_DISKSMOUNTTOOLS_COMMAND','Befehl');
define('_DISKSMOUNTTOOLS_SENDCMD','Sende Befehl!');
define('_DISKSMOUNTTOOLS_CMDINFO','Befehlsausgabe:');
define('_DISKSMOUNTTOOLS_MOUNTTEXT','Mounting...');
define('_DISKSMOUNTTOOLS_UMOUNTTEXT','Unmounting...');

// Service: CIFS/SAMBA
// file: services_samba.php
define('_SRVCIFS_NAMEDESC','CIFS - Common Internet File System');
define('_SRVCIFS_MSGVALIDNETBIOSNAME','Der Netbios-Name enth&auml;lt ung&uuml;ltige Zeichen.');
define('_SRVCIFS_MSGVALIDWORKGOUP','Der Arbeitsgruppen-Name enth&auml;lt ung&uuml;ltige Zeichen..');
define('_SRVCIFS_MSGVALIDWINS','Der WINS-Server muss eine IP address haben.');
define('_SRVCIFS_MSGVALIDSNDBUFF','Der "SND Buffer"-Wert muss eine Nummer sein.');
define('_SRVCIFS_MSGVALIDRCVBUFF','Der "RCV Buffer"-Wert muss eine Nummer sein.');
define('_SRVCIFS_SHARE','CIFS Freigabe');
define('_SRVCIFS_AUTH','Authentifizierung');
define('_SRVCIFS_NETBIOSNAME','NetBiosName');
define('_SRVCIFS_WORKGROUP','Arbeitsgruppe');
define('_SRVCIFS_WORKGROUPTEXT','Arbeitsgruppenzugeh&ouml;rigkeit.');
define('_SRVCIFS_DESCTEXT','Serverbeschreibung. Kann f&uuml;r gew&ouml;hnlich freigelassen werden.');
define('_SRVCIFS_DOSCHARSET','Dos charset');
define('_SRVCIFS_UNIXCHARSET','Unix charset');
define('_SRVCIFS_LOGLEVEL','Log Level');
define('_SRVCIFS_LOCALMASTER','Local Master Browser');
define('_SRVCIFS_LOCALMASTERTEXT','Versucht FreeNAS als "Local Master Browser" einzurichten.');
define('_SRVCIFS_TIMESRV','Zeitserver');
define('_SRVCIFS_TIMESRVTEXT','FreeNAS meldet sich selbst als Zeitserver gegen&uuml;ber Windows clients.');
define('_SRVCIFS_WINS','WINS server');
define('_SRVCIFS_WINSTEXT','WINS Server IP Adresse.');
define('_SRVCIFS_RECYCLE','Abfalleimer');
define('_SRVCIFS_ENRECYCLE','Abfalleimer aktivieren');
define('_SRVCIFS_ENRECYCLETEXT','Richtet einen Abfalleimer auf den CIFS-Freigaben ein.');
define('_SRVCIFS_HIDE','Zu verbergende Freigaben.');
define('_SRVCIFS_HIDETEXT','Zu verbergende CIFS-Freigabe ausw&auml;hlen.');
define('_SRVCIFS_SNBBUFF','Send Buffer Size');
define('_SRVCIFS_SNBBUFFTEXT','Gr&ouml;sse des "send buffer" (16384 Voreinstellung).');
define('_SRVCIFS_RCVBUFF','Receive Buffer Size');
define('_SRVCIFS_RCVBUFFTEXT','Gr&ouml;sse des "receive buffer" (16384 Voreinstellung).');

// Service: FTP
// file: services_ftp.php

define('_SRVFTP_NAMEDESC','FTP');
define('_SRVFTP_MSGVALIDPORT',_MSGVALIDTCPPORT);
define('_SRVFTP_MSGVALIDMAXCLIENT','Die h&ouml;chste Anzahl an Clients muss eine Zahl sein.');
define('_SRVFTP_MSGVALIDMAXIP','Die h&ouml;chste Anzahl an Verbindungen von einer IP-Adresse muss eine Zahl sein.');
define('_SRVFTP_MSGVALIDIDLE','Die maximale Leerlaufzeit muss eine Zahl sein.');
define('_SRVFTP_MSGVALIDPASVIP','Die "pasv address" muss eine  "public IP address" sein.');
define('_SRVFTP_MSGVALIDPASVMAX','Der "pasv_max_port-Port" muss eine g&uuml;ltige Portnummer sein.');
define('_SRVFTP_MSGVALIDPASVMIN','Der "pasv_min_port-Port" muss eine g&uuml;ltige Portnummer sein.');
define('_SRVFTP_MSGVALIDAUTH','Mindestens einen Nutzer "anonymous" und/oder einen lokalen Benutzer angeben.');
define('_SRVFTP_FTPSERVER','FTP Server');
define('_SRVFTP_TCP',_TCPPORT);
define('_SRVFTP_MAXCLIENT','Anzahl der Clients');
define('_SRVFTP_MAXCLIENTTEXT','Maximale Anzahl der gleichzeitig eingew&auml;hlten Clients.');
define('_SRVFTP_MAXIP','Max. Anzahl Verb, einer IP');
define('_SRVFTP_MAXIPTEXT','Max. Anzahl Verbindungen von einer IP-Adresse.');
define('_SRVFTP_TIMEOUT','Timeout');
define('_SRVFTP_TIMEOUTTEXT','Maximum Leerlaufzeit in Sekunden.');
define('_SRVFTP_ANONYMOUS','Anonymous login');
define('_SRVFTP_ENANONYMOUS','Aktiviere Anonymous login');
define('_SRVFTP_AUTH','Lokaler Benutzer');
define('_SRVFTP_ENAUTH','Aktiviere local User login');
define('_SRVFTP_FXP','FXP');
define('_SRVFTP_ENFXP','Aktiviere FXP Protokoll.');
define('_SRVFTP_FXPTEXT','FXP erm&ouml;glicht &Uuml;bertragungen zwischen zwei Servern ohne Zwischen&uuml;bertragung von Daten &uuml;ber den anfragenden Rechner (unsicher!).');
define('_SRVFTP_NATMODE','NAT Mode');
define('_SRVFTP_ENNATMODE','Erzwinge NAT Mode');
define('_SRVFTP_NATMODETEXT','Einschalten, falls der FTP Server hinter einer NAT (Network Address Translation) betrieben wird, die kein "applicative FTP proxying" unterst&uuml;tzt');
define('_SRVFTP_PASSIVEIP','Passive IP-Adresse');
define('_SRVFTP_PASSIVEIPTEXT','Diese Einstellung benutzen, um die IP-Adresse zu ersetzen, die der FTP Dienst normalerweise auf die PASV-Anfrage senden wird.');
define('_SRVFTP_PASVMIN','pasv_min_port');
define('_SRVFTP_PASVMINTEXT','Der kleinstm&ouml;gliche zuweisbare Port, der "PASV style" Datenverbindungen erm&ouml;glicht.');
define('_SRVFTP_PASVMAX','pasv_max_port');
define('_SRVFTP_PASVMAXTEXT','Der h&ouml;chstm&ouml;gliche zuweisbare Port, der "PASV style" Datenverbindungen erm&ouml;glicht.');
define('_SRVFTP_BANNER','Banner');
define('_SRVFTP_BANNERTEXT','Grussbotschaft - Angezeigt bei der ersten Verbindung via FTP.');

// Service: NFS
// file: services_nfs.php

define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','Ein g&uuml;ltiges Netzwerk muss angegeben werden.');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','NFS Server');
define('_SRVNFS_MAPALLROOT','Map all user to root');
define('_SRVNFS_MAPALLROOTTEXT','Alle Benutzer erhalten Administratorrechte.');
define('_SRVNFS_AUTHNET','Zugelassenes Netzwerk');
define('_SRVNFS_AUTHNETTEXT','Zugriffsberechtigtes Netzwerk auf die NFS-freigabe');
define('_SRVNFS_TEXT','Die Namenskonvention f&uuml;r exportierte Verzeichnisse: /mnt/Freigabenname');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','Server');
define('_SRVRYNC_CLIENT','Client');
define('_SRVRYNC_LOCAL','Local');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','Rsync Daemon');
define('_SRVRYNCD_READONLY','Nur lesen');
define('_SRVRYNCD_MAPUSER','Map to user');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_TCPORTTEXT','Alternativer TCP Port.');
define('_SRVRYNCD_MAXCON','Max. Verbindungen');
define('_SRVRYNCD_MAXCONTEXT','Max. Anzahl gleichzeitiger Verbindungen. Voreinstellung ist 0 (unbegrenzt).');
define('_SRVRYNCD_MOTD','MOTD');
define('_SRVRYNCD_MOTDTEXT','Nachricht des Tages.');

// file: services_rsyncd_client.php
define('_SRVRYNCC_MSGMPFIRST','Es muss zuerst ein "Mount Point" konfiguriert werden.');
define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
define('_SRVRYNCC_RSYNCC','Rsync Client Synchronisation');
define('_SRVRYNCC_REMOTESERVER','Remote RSYNC Server');
define('_SRVRYNCC_REMOTEID','IP-Adresse des remote RSYNC Servers');
define('_SRVRYNCC_OPTIONS','RSYNC Optionen');
define('_SRVRYNCC_OPTDEL','Daten l&ouml;schen, die beim Sender nicht existieren.');
define('_SRVRYNCC_SHARES','Abzugleichende Freigaben');
define('_SRVRYNCC_SHARESTEXT','Die gleiche lokale Freigabe muss auch beim Remote Server angegeben sein.');
define('_SRVRYNCC_TIME','Synchronisationzeit');
define('_SRVRYNCC_TEXT',_MSGSCHED_TEXT);

// file: services_rsyncd_local.php
define('_SRVRYNCL_MSGVALID','Sie k&ouml;nnen nicht den selben Mount Point f&uuml;r Quelle und Ziel verwenden!');
define('_SRVRYNCC_RSYNCL','Rsync Local Synchronization');
define('_SRVRYNCL_SOURCE','Quelle');
define('_SRVRYNCL_SOURCETEXT','Quell Mount Point.');
define('_SRVRYNCL_DESTINATION','Ziel');
define('_SRVRYNCL_DESTINATIONTEXT','Ziel Mount Point (Backup Laufwerk).');


// Service: SSHD
// file: services_sshd.php
define('_SRVSSHD_NAMEDESC','SSHD');
define('_SRVSSHD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVSSHD_MSGVALIDKEY','Dieser Schl&uuml;ssel scheint nicht g&uuml;ltig zu sein.');
define('_SRVSSHD_SSHD','SSH Daemon');
define('_SRVSSHD_READONLY','Nur-lesen');
define('_SRVSSHD_TCPORT',_TCPPORT);
define('_SRVSSHD_TCPORTTEXT',_SRVRYNCD_TCPORTTEXT);
define('_SRVSSHD_PERMITROOTLOGIN','Permit root login'); // Nicht übersetzen
define('_SRVSSHD_PERMITROOTLOGINTEXT','Spezifiziert ob es dem Superuser (root) erlaubt ist, sich direkt einzuloggen.');
define('_SRVSSHD_KEY','SSH Schl&uuml;ssel');
define('_SRVSSHD_PRIVATEKEY','Private Key');
define('_SRVSSHD_PRIVATEKEYTEXT','F&uuml;gen Sie einen "RSA private key" im PEM Format hier ein.');

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
define('_SRVAFP_MSGVALIDAUTH','Sie m&uuml;ssen mind. eine Authentifizierungsmethode ausw&auml;hlen.');
define('_SRVAFP_AFPSRV','AFP Server');
define('_SRVAFP_AFPNAME','Server Name');
define('_SRVAFP_AUTH','Authentifizierung');
define('_SRVAFP_ENABLEGUEST','Aktiviere Gast-Zugriff.');
define('_SRVAFP_ENABLELOCAL','Aktiviere Lokaler Benutzer-zugriff.');

// Access
// common words
define('_ACCESS_NAME','Zugriff');
define('_ACCESS_USER','Benutzer');
define('_ACCESS_USERS','Benutzer');
define('_ACCESS_GROUP','Gruppe');
define('_ACCESS_GROUPS','Gruppen');
define('_ACCESS_FULLNAME','Vollst&auml;ndiger Name');

// file: access_users.php
define('_ACCESSUSERS_MSGCHANGED','Die Benutzerliste wurde ver&auml;ndert.<br>Zur &Uuml;bernahme der &Auml;nderungen bitte best&auml;tigen.');
define('_ACCESSUSERS_EDITUSER','Bearbeite Benutzer');
define('_ACCESSUSERS_DELUSER','L&ouml;sche Benutzer');
define('_ACCESSUSERS_CONFDEL','Benutzer wirklich l&ouml;schen?');
define('_ACCESSUSERS_ADDUSER','Lege Benutzer an');

// file: access_users_edit.php
define('_ACCESSUSERSEDIT_MSGGROUPFIRST','Zun&auml;chst eine Gruppe einrichten.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGIN','Der Login-Name enth&auml;lt ung&uuml;ltige Zeichen.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED','Der Login-Name ist reserviert.');
define('_ACCESSUSERSEDIT_MSGVALIDFULLNAME','Der vollst&auml;ndige Name enth&auml;lt ung&uuml;ltige Zeichen.');
define('_ACCESSUSERSEDIT_MSGVALIDDUPLICATE','Dieser Benutzer existiert bereits.');
define('_ACCESSUSERSEDIT_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSUSERSEDIT_LOGIN',_LOGIN);
define('_ACCESSUSERSEDIT_LOGINTEXT','Eindeutiger Loginname des Benutzers.');
define('_ACCESSUSERSEDIT_FULLNAME',_ACCESS_FULLNAME);
define('_ACCESSUSERSEDIT_FULLNAMETEXT','Vollst&auml;ndiger Name.');
define('_ACCESSUSERSEDIT_PASSWORD','Passwort');
define('_ACCESSUSERSEDIT_PASSWORDTEXT','Benutzerpasswort.');
define('_ACCESSUSERSEDIT_PASSWORDCONF','Best&auml;tige Passwort');
define('_ACCESSUSERSEDIT_PASSWORDCONFTEXT','Das Passwort bitte best&auml;tigen.');
define('_ACCESSUSERSEDIT_GROUPMEMBER','Gruppenzugeh&ouml;rigkeit');
define('_ACCESSUSERSEDIT_FULLSHELL','Komplettes Terminal');
define('_ACCESSUSERSEDIT_FULLSHELLTEXT','Vollst&auml;ndigen Zugriff zur "Shell" f&uuml;r Benutzer erlauben');

// file: access_users_groups.php

define('_ACCESSGROUPS_MSGCHANGED','Die Gruppenliste wurde ver&auml;ndert.<br>Zur &Uuml;bernahme der &Auml;nderungen bitte best&auml;tigen.');
define('_ACCESSGROUPS_DESC',_DESC);
define('_ACCESSGROUPS_GROUPEDIT','Bearbeite Gruppe');
define('_ACCESSGROUPS_GROUPDEL','L&ouml;sche Gruppe');
define('_ACCESSGROUPS_GROUPDELCONF','Diese Gruppe wirklich l&ouml;schen?');
define('_ACCESSGROUPS_GROUPADD','Lege Gruppe an');

// file: access_users_groups_edit.php

define('_ACCESSGROUPSEDIT_MSGVALIDNAME','Der Gruppenname enth&auml;lt ung&uuml;ltige Zeichen.');
define('_ACCESSGROUPSEDIT_MSGVALIDDESC','Die Gruppenbeschreibung enth&auml;lt ung&uuml;ltige Zeichen.');
define('_ACCESSGROUPSEDIT_MSGVALIDDUPLICATE','Diese Gruppe gibt es bereits.');
define('_ACCESSGROUPSEDIT_NAME','Name');
define('_ACCESSGROUPSEDIT_NAMETEXT','Gruppenname.');
define('_ACCESSGROUPSEDIT_DESC',_DESC);
define('_ACCESSGROUPSEDIT_DESCTEXT','Gruppenbeschreibung.');

// file: access_ad.php

define('_ACCESSAD_NAME','Active Directory'); // Nicht übersetzen.
define('_ACCESSAD_MSGVALIDIP',_MSGVALIDIP);
define('_ACCESSAD_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSAD_ADNAME','AD Servername');
define('_ACCESSAD_ADNAMETEXT','AD oder PDC-Name.');
define('_ACCESSAD_IP','AD Server IP');
define('_ACCESSAD_IPTEXT','IP-Adresse des MS Active Directory Server.');
define('_ACCESSAD_DOMAIN','Domain Name');
define('_ACCESSAD_DOMAINTEXT','Domain Name im alten Format.');
define('_ACCESSAD_ADMINAME','Administrator Name');
define('_ACCESSAD_ADMINNAMETEXT','Benutzername eines "Domain Administrator Account".');
define('_ACCESSAD_ADMINPASS','Administration Passwort');
define('_ACCESSAD_ADMINPASSTEXT','Passwort eines "Domain Administrator Account", hier zweifach eingeben.');

// Diagnostics
define('_DIAG_NAME','Diagnose');

// file: shutdown.php
define('_SHUT_NAME',_MENULEFT_CMDSHUTDOWNSYS);
define('_SHUT_MSGSHUT','Das System wird heruntergefahren, dies kann kurz dauern.');
define('_SHUT_CONF','Wirklich das System herunterfahren?');

// file: shutdown_sched.php
define('_SHUTSCHED_NAME','Geplanter Shutdown');
define('_SHUTSCHED_TIME','Shutdownzeit');
define('_SHUTSCHED_TEXT',_MSGSCHED_TEXT);

// file: diag_edit.php
define('_DIAGEDITPHP_NAMEDESC','Bearbeite Datei');
define('_DIAGEDITPHP_FILEPATH','Dateipfad');
define('_DIAGEDITPHP_SAVEMSG','Habe Datei gespeichert nach');
define('_DIAGEDITPHP_FILENOTFOUND','Datei nicht gefunden');
define('_DIAGEDITPHP_ROWS','Zeilen');
define('_DIAGEDITPHP_COLS','Spalten');
define('_DIAGEDITPHP_HIGHLIGHTING','Hervorheben');
define('_DIAGEDITPHP_HIGHLIGHTINGENABLED','Aktiviert');
define('_DIAGEDITPHP_HIGHLIGHTINGDISABLED','Deaktiviert');

// file: exec.php
define('_DIAGEXECPHP_NAMEDESC','Befehl ausf&uuml;hren');

// file: reboot.php
define('_DIAGREBOOT_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGREBOOT_NAMEDESC',_MENULEFT_CMDREBOOTSYS);
define('_DIAGREBOOT_REBOOTMSG','Das System wird jetzt neu gestartet. Dies kann einige Minuten dauern.');
define('_DIAGREBOOT_REBOOTCONF','Wollen Sie das System wirklich neu starten?');

// file: license.php
define('_LICENSE_NAME','Lizenz');

// file: status_wireless.php
define('_STATSWIRELESS_NAME',_MENULEFT_STATUS);
define('_STATSWIRELESS_NAMEDESC','Drahtlos');

// file: status_interfaces.php
define('_STATSINTERFACES_NAME',_MENULEFT_STATUS);
define('_STATSINTERFACES_NAMEDESC','Schnittstellen');

// file: status_graph_cpu.php
define('_STATSGRAPHCPU_NAME',_MENULEFT_STATUS);
define('_STATSGRAPHCPU_NAMEDESC','CPU Auslastung');

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
define('_INTVLAN_INTERFACE','Schnittstelle');
define('_INTVLAN_VLANTAG','VLAN tag');
define('_INTVLAN_DESC',_DESC);

// file: interfaces_vlan_edit.php
define('_INTVLANEDIT_NAME',_MENULEFT_INTERFACES);
define('_INTVLANEDIT_NAMEDESC',_INTASSIGNPHP_NAMEDESC);
define('_INTVLANEDIT_EDITVLAN','Bearbeite VLAN');

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
define('_DIAGINFOSDISKS_ATA_INFO','Liste von gefundenen ATA Laufwerken');
define('_DIAGINFOSDISKS_SCSI_INFO','Liste von gefundenen SCSI Laufwerken');

// file: diag_infos_mount.php
define('_DIAGINFOSMOUNT_INFO','Liste von Mount Points');

// file: diag_infos_space.php
define('_DIAGINFOSSPACE_INFO','Freier Speicherplatz');

// file: diag_defaults.php
define('_DIAGDEFAULTS_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGDEFAULTS_NAMEDESC',_MENULEFT_CMDFACTORYDEF);

// file: diag_backup.php
define('_DIAGBACKUP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGBACKUP_NAMEDESC',_MENULEFT_CMDBACKUPRESTORE);
define('_DIAGBACKUP_RESTORENOTE','FreeNAS wird nach dem wiederherstellen der Konfiguration neu gestartet.');

// file: diag_arp.php
define('_DIAGARP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGARP_NAMEDESC',_MENULEFT_DIAGARPTAB);

// file: status_graph.php
define('_STATSGRAPH_NAME',_MENULEFT_STATUS);
define('_STATSGRAPH_NAMEDESC','Traffic Graph');

// file: graph.php
define('_GRAPH_ERRORGETIFDATA','Kann keine Daten erfassen von Schnittstelle');
define('_GRAPH_IN','In');
define('_GRAPH_OUT','Out');
define('_GRAPH_SWITCHTO','Umschalten zu');
define('_GRAPH_AUTOSCALE','Autom. Skalierung');
define('_GRAPH_AUTOSCALE_FOLLOW','Follow');
define('_GRAPH_AUTOSCALE_UP','Up');
define('_GRAPH_SHOWLAST','Graph zeigt die letzten');
define('_GRAPH_COLLECTDATA','Ermittle Daten, bitte warten...');
?>
