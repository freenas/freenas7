<?php 
/*
	lang-English.php
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
define('_LOCALE','ro_RO');
define('_LOCALEWIN', 'rom');
// date and time defines
define('_DATE','Data');
define('_DATEBRIEF','%b %d, %Y');
define('_DATELONG','%A, %B %d, %Y');
define('_DATESTRING','%A, %B %d @ %H:%M:%S');
define('_DATETIMEBRIEF','%b %d, %Y - %I:%M %p');
define('_DATETIMELONG','%A, %B %d, %Y - %I:%M %p');
define('_DAY_OF_WEEK_LONG','Duminica Luni Marti Miercuri Joi Vineri Simbata');
define('_DAY_OF_WEEK_SHORT','Dum Lun Mar Mie Joi Vin Sim');
define('_MONTH_LONG','Ianuarie Februarie Martie Aprilie Mai Iunie Iulie August Septembrie Octombrie Noiembrie Decembrie');
define('_MONTH_SHORT','Ian Feb Mar Apr MAi Iun Iul Aug Sep Oct Nov Dec');
define('_TIME', 'Timp');
define('_TIMEBRIEF','%I:%M %p');
define('_SECOND', 'secunda');
define('_SECONDS', 'secunde');
define('_MINUTE', 'minut');
define('_MINUTES', 'minute');
define('_HOUR', 'ora');
define('_HOURS', 'Ore');
define('_DAY', 'zi');
define('_DAYS', 'zile');
define('_WEEK', 'spatamina');
define('_WEEKS', 'saptamini');
define('_WEEKDAYS', 'zilele saptaminii');
define('_MONTH', 'luna');
define('_MONTHS', 'luni');
define('_YEAR', 'an');
define('_YEARS', 'ani');

// common words
define('_ALL','Toate');
define('_AND','si');
define('_BY','de');
define('_DOWN','Down');
define('_NO','Nu');
define('_OF','off');
define('_OK','OK');
define('_ON','on');
define('_TO','La');
define('_THE', '');
define('_UP','Up');
define('_URL', 'URL');
define('_YES','Da');

// common actions
define('_ACTIVATE','Activ');
define('_ADD','Adauga');
define('_CANCEL', 'Cancel');
define('_COMMIT', 'Actualizare');
define('_CREATE', 'Creare');
define('_DEACTIVATE','Deactivat');
define('_DELETE','Stergere');
define('_EDIT','Mditare');
define('_ENABLE','Validare');
define('_HELP', 'Ajutor');
define('_IGNORE','Ignora');
define('_LOGIN','Login');
define('_LOGOUT','Log out');
define('_MODIFY','Modificare');
define('_NEW','Nou');
define('_SEARCH', 'Cauta');
define('_SAVE', 'Salvare');
define('_SUBMIT','Trimite');
define('_UPDATE', 'Update');
define('_VIEW', 'Vizualizare');
define('_APPLY', 'Aplicare modificari');
define('_WARNING', 'Atentie');
define('_LOAD', 'Load');
define('_RETRY', 'Reincearca');

//FreeNAS common words
define('_SIZE', 'MArime');
define('_STATUS', 'Stare');
define('_TYPE', 'Tip');
define('_CONFIGURING', 'Configurare');
define('_DISK', 'Disk');
define('_DISKS', 'Disk-uri');
define('_PARTITION', 'Partitie');
define('_FILESYSTEM', 'Fisiere sistem');
define('_DESC', 'Descriere');
define('_SOFTRAID', 'Software RAID');
define('_SERVICE','Serviciu');
define('_SERVICES','Servicii');
define('_TCPPORT','Port TCP');
define('_CONFIRMATION','Confirmare');
define('_NOTE','Obs.');
define('_SUCCESSFUL','Cu Succes');
define('_FAILED','Incorect');
define('_ERROR','Eroare');
define('_HINT','Hint');

// common messages
define('_MESSAGES_SUCCESS', 'Modificarile au fost aplicate cu succes.');
define('_MESSAGES_SUCCESSWITHREBOOT', 'Modificarile au fost salvate. Trebuie sa <a href=reboot.php>reporniti</a> sistyemul pentru ca modificarile sa aiba efect.');
define('_MESSAGES_ERROR', 'EROARE: modificarile nu pot fi aplicate (cod erroare '.$ok.').');

// Special messages
define('_MSGVALIDIP','Trebuie sa introduceti o adresa IP valida.');
define('_MSGVALIDNETBIT','Trebuie sa introduceti un nr. vaild de retea.');
define('_MSGVALIDTCPPORT','Trebuie sa introduceti un port TCP valid.');
define('_MSGVALIDPASSWORD','Parolele sint diferite.');
define('_MSGSCHED_TEXT','Note: Ctrl-click (sau command-click pe Mac) pentru selectare/deselectare minute, ore, zile  si luni.');
define('_MSGFILESYSTEM','UFS (+ variante) reprezinta sistemul de fisiere NATIV pentru  FreeBSD (sistemul sursa al FreeNAS). Utilizarea altor sisteme de fisere cum ar fi FAT, FAT32, EXT2, EXT3, sau NTFS poate avea rezultate incerte (inclusiv pierderi de date)!');

// states
define('_ACTIVE','Activ');
define('_INACTIVE','Inactiv');
define('_SELECTED','Selectat');

// Webgui layout
define('_MENULEFT_SYSTEM','Sistem');
define('_MENULEFT_SYSGENSETUP','Setari Generale');
define('_MENULEFT_SYSSTATROUTES','Rute Statice');
define('_MENULEFT_SYSFIRMWARE','Firmware');
define('_MENULEFT_SYSADVANCED','Avansat');
define('_MENULEFT_INTERFACES','Interfete');
define('_MENULEFT_INTASSIGN','Atribuire');
define('_MENULEFT_INTLAN','LAN');
define('_MENULEFT_DISKS',_DISKS);
define('_MENULEFT_DISKMANAG','Management');
define('_MENULEFT_DISKMNGSOFTRAID',_SOFTRAID);
define('_MENULEFT_DISKMNGMOUNTPOINT','Mount Point');
define('_MENULEFT_SERVICES','Servicii');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVUNSN','Unison');
define('_MENULEFT_SRVUPNP','UPnP');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','Acces');
define('_MENULEFT_ACUSERGROUPS','Utilizatori si Grupuri');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS',_STATUS);
define('_MENULEFT_STSYSTEM','Sistem');
define('_MENULEFT_STPROCESS','Procese');
define('_MENULEFT_STINTERFACES','Interfete');
define('_MENULEFT_STDISKS',_DISKS);
define('_MENULEFT_STWIRELESS','Wireless');
define('_MENULEFT_EXTENSIONS','Extensii');
define('_MENULEFT_DIAGNOSTICS','Diagnostic');
define('_MENULEFT_DIAGLOGS','Jurnale');
define('_MENULEFT_DIAGINF','Informatii');
define('_MENULEFT_DIAGPINGTRACE','Ping/Traceroute');
define('_MENULEFT_DIAGARPTAB','Tabela ARP');
define('_MENULEFT_DIAGEXEC','Comanda');
define('_MENULEFT_DIAGEDITFILE','Editare Fisier');
define('_MENULEFT_CMDBACKUPRESTORE','Backup/Restore');
define('_MENULEFT_CMDFACTORYDEF','Setari Implicite');
define('_MENULEFT_CMDREBOOTSYS','Repornire Sistem');
define('_MENULEFT_CMDSHUTDOWNSYS','Oprire Sistem');
define('_MENULEFT_GRAPH','Graphic');

// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION',_MENULEFT_SYSGENSETUP);
define('_SYSTEMPHP_HOSTNAME','HostName');
define('_SYSTEMPHP_HOSTNAMETEXT','Numele sistemului NAS, fara domeniu<br>e.g. <em>nas</em>');
define('_SYSTEMPHP_DOMAIN','Domeniu');
define('_SYSTEMPHP_DOMAINTEXT','e.g. <em>mycorp.com</em>');
define('_SYSTEMPHP_DNS','Server DNS');
define('_SYSTEMPHP_DNSTEXT','Adresa IP');
define('_SYSTEMPHP_USERNAME','Nume Utilizator');
define('_SYSTEMPHP_USERNAMETEXT','Daca doriti sa schimbati numele sub care veti accesa Interfa Web FreeNas, introduceti-l aici.');
define('_SYSTEMPHP_PASSWORD','Parola');
define('_SYSTEMPHP_PASSWORDTEXT','Daca doriti sa schimbati parola de acces pentru Interfa Web FreeNas, introduceti-o aici de doua ori.<br>Nu folositi caracterele \':\'.');
define('_SYSTEMPHP_WEBGUIPROTOCOL','Protocol WebGUI');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','');
define('_SYSTEMPHP_WEBGUIPORT','Port WebGUI');
define('_SYSTEMPHP_WEBGUIPORTTEXT','Introduceti un nr. de port pentru WebGui daca vreti sa-l schimbati pe cel implicit (80 pentru HTTP, 443 pentru HTTPS).');
define('_SYSTEMPHP_LANGUAGE','Limba');
define('_SYSTEMPHP_LANGUAGETEXT','Selectati limba sistem');
define('_SYSTEMPHP_TIMEZONE','Fus Orar');
define('_SYSTEMPHP_TIMEZONETEXT','Selectati cea mai apropiata locatie.');
define('_SYSTEMPHP_TIMEUPDATEINT','Intervalul ptr. actualizare');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','Minute intre sincronizari; 300	recomandat, sau 0 pentru dezactivare.');
define('_SYSTEMPHP_NTPSERVER','Server NTP');
define('_SYSTEMPHP_NTPSERVERTEXT','Folositi un spatiu pentru separarea host-urilor multiple (daca e nevoie). Trebuie sa definiti cel putin 1 server DNS daca introduceti un nume de host aici!');
define('_SYSTEMPHP_MSGHOSTNAME','HostName-ul poate contine doar caracterele  a-z, 0-9 si \'-\'.');
define('_SYSTEMPHP_MSGDOMAIN','Numele de Domeniu poate contine doar caracterele  a-z, 0-9 si \'-\'.');
define('_SYSTEMPHP_MSGDNS','Trebuie sa furnizati o adesa IP valida pentru server-ul DNS Primar/Secundar.');
define('_SYSTEMPHP_MSGUSERNAME','Numele Utilizator poate contine doar caracterele a-z, A-Z and 0-9.');
define('_SYSTEMPHP_MSGWEBGUIPORT','Trebuie sa introduceti un port TCP/IP valid pentru webGUI.');
define('_SYSTEMPHP_MSGPASSWORD','Parolele nu coincid.');
define('_SYSTEMPHP_MSGLANGUAGE','Trebuie sa selectati o limba valida.');
define('_SYSTEMPHP_MSGTIMEUPDATEINTERVAL','Intervalul de actualizare poate fi fie 0 (dezactivat) sau o valoare intre 6 si 1440.');
define('_SYSTEMPHP_MSGTIMESERVERS','Numele Server-ului NTP poate contine doar caracterele a-z, 0-9, \'-\' si \'.\'.');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','Informatii Sistem');
define('_INDEXPHP_NAME','Nume');
define('_INDEXPHP_VERSION','Versiune');
define('_INDEXPHP_OSVERSION','Versiune OS');
define('_INDEXPHP_PLATFORM','Platforma');
define('_INDEXPHP_DATE','Data');
define('_INDEXPHP_UPTIME','Uptime');
define('_INDEXPHP_LASTCHANGE','Ultima Modificare Configuratie');
define('_INDEXPHP_MEMUSE','Utilizare memorie');
define('_INDEXPHP_LOADAVERAGE','Incarcare Medie');
define('_INDEXPHP_SHOWPROCESSING','afisare informatii procese');
define('_INDEXPHP_DISKS_USE','utilizare Spatiu Disk');
define('_INDEXPHP_NODISK','Niciun Disk Configurat');

// Status Processes Module
// file: status_process.php
define('_STATSPROCPHP_NAME',_STATUS);
define('_STATSPROCPHP_NAME_DESCRIPTION','Procese');
define('_STATSPROCPHP_PROC_INF','Informatii Procese');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION',_MENULEFT_SYSSTATROUTES);
define('_SYSTEMROUTESPHP_MSG_CHANGED','Ruta statica a fost schimbata. <br>Trebuie sa salvati modificarile pentru a deveni active.');
define('_SYSTEMROUTESPHP_INT','Interfata');
define('_SYSTEMROUTESPHP_NET','Retea');
define('_SYSTEMROUTESPHP_GW','Gateway');
define('_SYSTEMROUTESPHP_DESC',_DESC);
define('_SYSTEMROUTESPHP_EDITROUTE','Editare Ruta');
define('_SYSTEMROUTESPHP_MSGCONFIRM','Sigur Stergeti Aceasta Ruta?');
define('_SYSTEMROUTESPHP_DELROUTE','Stergere Ruta');
define('_SYSTEMROUTESPHP_ADDROUTE','Adaugare Ruta');

// Static Routes Edit Module
// file: system_routes_edit.php
define('_SYSTEMROUTESPHP_MSGVALID','Trebuie specificata o retea destinatie valida.');
define('_SYSTEMROUTESPHP_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SYSTEMROUTESPHP_MSGVALIDGW','Trebuie specificata o adresa IP de gateway valida.');
define('_SYSTEMROUTESPHP_MSGROUTEEXIST','Exista deja o ruta catre aceasta destinatie.');
define('_SYSTEMROUTESPHP_CHOOSEINT','Selectati interfata pentru aceasta ruta.');
define('_SYSTEMROUTESPHP_DESTNET','Reteaua Destinatie');
define('_SYSTEMROUTESPHP_DESTNETROUTE','Reteaua Destinatie pentru aceasta ruta statica');
define('_SYSTEMROUTESPHP_GWTONET','Gateway-ul pentru acces la reteaua destinatie');
define('_SYSTEMROUTESPHP_DESCREF','Puteti introduce aici o scurta descriere.');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMFIRMWAREPHP_NAMEDESC',_MENULEFT_SYSFIRMWARE);
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','Imaginea incarcata nu este pentru acesasta platforma');
define('_SYSTEMFIRMWAREPHP_OUTMEM','Eroare la incarcarea imaginii (memorie insuficienta?)');
define('_SYSTEMFIRMWAREPHP_INVSIG','Semnatura digitala invalida a imaginii.');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','Imaginea nu are nicio semnatura digitala asociata.');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','Eroare la verificarea semnaturii digitale a imaginii.');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','Fisier corupt');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','Se actualizeaza sistemul. FreeNas va reporni apoi in mod automat.');
define('_SYSTEMFIRMWAREPHP_NOTSUP','Actualizarea sistemului nu este posibila.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','Acest lucru insemna ca imaginea incarcata ' .
		 	'nu este in mod oficial suportata si poate produce efecte nedorite in functionare ' .
			'. Instalati doar imagini care provin din surse sigure si asigurati-va ca imaginea '.
			'respectiva nu a fost alterata.<br><br>'.
			'Doriti sa instalati oricum aceasta imagine (pe riscul dvs.)?');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','Click &quot;Activati actualizarea firmware-ului&quot; mai jos, apoi alegeti imaginea de incarcat.<br>Click &quot;Actualizare firmware&quot; pentru inceperea actualizarii.');			
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','Activare Actualizare Firmware');	
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','Dizactivare Actualizare Firmware');
define('_SYSTEMFIRMWAREPHP_FIRMFILE','Fisier Imagine Firmware');	
define('_SYSTEMFIRMWAREPHP_UPFIRM','Actualizare firmware');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','Sistemul trebuie repornit pentru activarea noului firmware.');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','NU opriti procesul de actualizare firmware. FreeNAS va reporni in mod automat dupa incarcarea noului firmware cu pastrarea configuratiei. <br>Aveti nevoie de minim 128 Mb RAM pentru a putea actualiza sistemul.');

// System Advanced Module
// file: system_advanced.php
define('_SYSTEMADVANCEDPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMADVANCEDPHP_NAMEDESC',_MENULEFT_SYSADVANCED);
define('_SYSTEMADVANCEDPHP_MSGVALIDTCP','Valoare pentru TCP idle timeout trebuie sa fie un numar intreg.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERTKEY','certificatul si cheia trebuiesc specificate impreuna.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERT','Acest certificat nu este valid.');
define('_SYSTEMADVANCEDPHP_MSGVALIDKEY','Aceasta cheie nu este valida.');
define('_SYSTEMADVANCEDPHP_NOTE','Optiuni <strong>DOAR</strong> pentru utilizatori avansati!.');
define('_SYSTEMADVANCEDPHP_WEBGUISSLKEY','cheie/cerfificat pentru webGUI SSL');
define('_SYSTEMADVANCEDPHP_WEBCERT','Certificat');
define('_SYSTEMADVANCEDPHP_WEBCERTTEXT','Introduceti un certificat semnat tip X.509 PEM.');
define('_SYSTEMADVANCEDPHP_WEBKEY','Cheie');
define('_SYSTEMADVANCEDPHP_WEBKEYTEXT','Introduceti o cheie privata RSA in format PEM.');
define('_SYSTEMADVANCEDPHP_MISC','Diverse');
define('_SYSTEMADVANCEDPHP_CON','Meniu Consola');
define('_SYSTEMADVANCEDPHP_DISCON','Dezactivare Meniu Consola');
define('_SYSTEMADVANCEDPHP_CONTEXT','Modificarile pentru aceasta optiune vor deveni active dupa repornirea sistemului.');
define('_SYSTEMADVANCEDPHP_FIRM','Verificare versiune Firmware');
define('_SYSTEMADVANCEDPHP_DISFIRM','Dezactivare Verificare veriune firmware');
define('_SYSTEMADVANCEDPHP_FIRMTEXT','In acest mod FreeNAS nu va verifica existenta unei versiuni mai noi cind pagina <a href=system_firmware.php>Sistem: Firmware</a> este vizualizata.');
define('_SYSTEMADVANCEDPHP_TCPIDLE','TCP idle timeout');
define('_SYSTEMADVANCEDPHP_TCPIDLETEXT','Conexiunile TCP inactive vor fi eliminate din tabela de stare dupa ce nu va mai fi receptionat niciun pachet corespunzator conexiunii pentru un numar specificat de secunde. Nu setati valoari prea mari altfel tabela se poate umple rapid cu conexiuni expirate, inutile. Timpul implicit este de 2.5 ore.');
define('_SYSTEMADVANCEDPHP_NAV','Navigare');
define('_SYSTEMADVANCEDPHP_NAVTEXT','Pastrare Diagnostic in modul expandat');
define('_SYSTEMADVANCEDPHP_BEEP','Beep Sistem');
define('_SYSTEMADVANCEDPHP_DISBEEP','Dezactivare sunet la pornire si la oprire');
define('_SYSTEMADVANCEDPHP_SMART','Daemon S.M.A.R.T');
define('_SYSTEMADVANCEDPHP_ENSMART','Activare daemon S.M.A.R.T');
define('_SYSTEMADVANCEDPHP_SMARTTEXT','Monitorizare dispozitiv S.M.A.R.T cu jurnalizare.');
define('_SYSTEMADVANCEDPHP_TUNING','Tuning');
define('_SYSTEMADVANCEDPHP_ENTUNING','Activare tuning pentru citeva variabile din kernel');
define('_SYSTEMADVANCEDPHP_ZEROCONF','ZeroConf');
define('_SYSTEMADVANCEDPHP_DISZEROCONF','Dezactivare anunt serviciu ZeroConf');

// Interface assing:
// file: interfaces_assign.php
define('_INTASSIGNPHP_NAME','Interfete');
define('_INTASSIGNPHP_NAMEDESC','Alocare porturi retea');
define('_INTASSIGNPHP_MSGVALIDPORT','Portul ');
define('_INTASSIGNPHP_MSGVALIDPORTWASASSIG',' a fost alocat ');
define('_INTASSIGNPHP_MSGVALIDINT',' interfetei:');
define('_INTASSIGNPHP_INT','Interfata');
define('_INTASSIGNPHP_PORT','Port retea');
define('_INTASSIGNPHP_DELINT','Stergere interfata');
define('_INTASSIGNPHP_ADDINT','Adaugare Interfata');
define('_INTASSIGNPHP_INTTEXT','Dupa ce faceti click pe &quot;Salvare&quot;, va trebui sa reporniti sistemul pentru ca modificarile sa fie activate. Va trebui eventual sa parcurgeti urmatorii pasi pentru a putea accesa sistemul din nou: </span></p>
<ul>
  <li><span class=\"vexpl\">schimbati adresa IP a calculatorului</span></li>
  <li><span class=\"vexpl\">accesati interfata FreeNas cu noua adresa</span></li>
</ul>');

// Interface LAN/OPT:
// file: interfaces_lan.php
// file: interfaces_opt.php
define('_INTLANPHP_NAME','Interfeta');
define('_INTLANPHP_NAMEDESC','LAN');
define('_INTPHP_MSGVALIDIP',_MSGVALIDIP);
define('_INTPHP_MSGVALIDMASK',_MSGVALIDNETBIT);
define('_INTPHP_MSGVALIDGW','Trebuie introdus un gateway valid.');
define('_INTPHP_MSGVALIDMTU','Trebuie introdus o valoare MTU corecta.');
define('_INTPHP_IP','Adresa IP');
define('_INTPHP_NETMASK','Masca Retea');
define('_INTPHP_GW','Gateway');
define('_INTPHP_MTU','MTU');
define('_INTPHP_MTUTEXT','valoarea starndard ptr. MTU e 1500, puteti folosi 9000 ptr. modul jumbo frame.');
define('_INTPHP_POLLING','Device polling');
define('_INTPHP_ENPOLLING','Activare Device Polling');
define('_INTPHP_POLLINGTEXT','Device polling este o tehnica de a lasa sistemul sa verifice periodic starea dispozitivelor de retea in loc sa functioneze pe baza de intreruperi. In acest mod se reduce incarcarea 	procesorului cu scaderea semnificativa a intirzierilor in retea (dispozitivul e accesat de                    aprox. 1000 ori pe sec.) Nu toate cartele de retea accepat aces mod de lucru. Verificati la pagina web m0n0wall lista cartelelor de retea suportate.');
define('_INTPHP_SPEED','Viteza');
define('_INTPHP_DUPLEX','Duplex');
define('_INTPHP_TEXT','dupa ce selectati &quot;Salvaree&quot;, va trebui eventual sa parcurgeti alti pasi pentru a accesa din nou interfata FreeNas: 
                    <ul>
                      <li>schimbati adresa IP a calculatorului</li>
                      <li>accesati interfata FreeNas cu noua adresa</li>
                    </ul>');
define('_INTPHP_ENOPT','Activare Optionala ');
define('_INTPHP_ENOPTINT',' interfata');
define('_INTPHP_DESC',_DESC);
define('_INTPHP_DESCTEXT','Scurta descriere (numele) a interfetei.');
define('_INTPHP_STATIC','Configuratie cu IP Static');
define('_INTPHP_DHCP','Configuratie DHCP client');
define('_INTPHP_GENERAL','Configuratie Generala');
define('_INTPHP_DHCPHOSTNAME','Hostname');
define('_INTPHP_DHCPHOSTNAMETEXT','Acesta valoare este trimisa ca DHCP hostname cind se cere o alocare DHCP.');
define('_INTPHP_DHCPCLIENTIDENTIFIER','Identificator Client');
define('_INTPHP_DHCPCLIENTIDENTIFIERTEXT','Acesta valoare este trimisa ca identificator DHCP client cind se cere o alocare DHCP.');
define('_INTPHP_CALCNETMASKBITS','Calcul Masca Retea');
define('_INTPHP_MSGCHANGED','Configuratia LAN a fost modificata.<br>Trebuie sa salvati acest modificari pentru a deveni active.');

// Disks: Manage
// Common text
define('_DISKSPHP_NAME',_DISKS);
define('_DISKSPHP_MANAGE','Configurare');
define('_DISKSPHP_FORMAT','Formatare');
define('_DISKSPHP_TOOLS','Unelte');
define('_DISKSPHP_ISCSIINIT','Initiator iSCSI');
define('_DISKSPHP_DISK',_DISK);
define('_DISKSPHP_FILESYSTEM',_FILESYSTEM);

// file: disks_manage.php
define('_DISKSMANAGEPHP_NAMEDESC','Management');
define('_DISKSMANAGEPHP_MSGCHANGED','Lista de disc-uri a fost modificata.<br>Trebuie sa salvati acest modificari pentru a deveni active.');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','Timp Standby');
define('_DISKSMANAGEPHP_STANDBYALWAYSON','Pornit Mereu');
define('_DISKSMANAGEPHP_FSUNKNOWN','Necunoscut sau neformatat');
define('_DISKSMANAGEPHP_EDIT','Editare disc');
define('_DISKSMANAGEPHP_DEL','Stergere disc');
define('_DISKSMANAGEPHP_DELCONF','Sigur stergeti disc-ul? Toate datele inclusiv partajarile vor fi sterse!');
define('_DISKSMANAGEPHP_ADD','Adaugare disc');
define('_DISKSMANAGEPHP_NOTE','Primul pas: Adaugati harddisc-ul la lista.');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','Acest disc este deja in lista.');
define('_DISKSMANAGEEDITPHP_UDMA','Mod UDMA');
define('_DISKSMANAGEEDITPHP_UDMATEXT','Puteti forta modul UDMA daca primiti mesajul de eroare "UDMA_ERROR.... LBA" de la harddisc.');
define('_DISKSMANAGEEDITPHP_STANDBY','Timp de standby harddisc');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','Trece harddisc-ul in modul standby cind timpul selectat de la ultima accesare expira. <em>Nu setati acest mod pentru cardurile CF.</em>');
define('_DISKSMANAGEEDITPHP_APM','Advanced Power Management');
define('_DISKSMANAGEEDITPHP_APM_0','Dezactivat');
define('_DISKSMANAGEEDITPHP_APM_1','Consum minim in modul Standby');
define('_DISKSMANAGEEDITPHP_APM_64','Consum mediu in modul Standby');
define('_DISKSMANAGEEDITPHP_APM_128','Consum maxim fara modul Standby');
define('_DISKSMANAGEEDITPHP_APM_192','Consum madiu fara modul Standby');
define('_DISKSMANAGEEDITPHP_APM_254','Performanta Maxima, consum maxim');
define('_DISKSMANAGEEDITPHP_APMTEXT','Acest mod permite scaderea consumului discului, cu afectarea performantelor.<em>Nu setati acest mod pentru cardurile CF.</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','Nivel Acustic');
define('_DISKSMANAGEEDITPHP_ACLEVEL_0','Dezactivat');
define('_DISKSMANAGEEDITPHP_ACLEVEL_1','Performanta minima, Zgomot minim');
define('_DISKSMANAGEEDITPHP_ACLEVEL_64','Nivel Mediu de Zgomot');
define('_DISKSMANAGEEDITPHP_ACLEVEL_127','Performanta maxima, Nivel Maxim Zgomot');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','Acest mod stabileste nivelul de zgomot in functionare pentru Harddisc.<em>Do not set this for CF cards.</em>');
define('_DISKSMANAGEEDITPHP_PREFS','Preformatat FS');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','Permite stabilirea tipului FS pentru hardisc-uri preformatate, avind date scrise.<br><em>Selectati "neformatat" pentru disc-uri neformatate si folositi <a href="disks_manage_init.php">meniul format</a>.</em>');

//file: disks_manage_init.php
define('_DISKSMANAGEINITPHP_NAMEDESC','Initializare');
define('_DISKSMANAGEINITPHP_DISKMOUNTERROR','Discul este montat! <a href=%s>De-montati</a> acest disc mai intii!');
define('_DISKSMANAGEINITPHP_INITTEXT','<strong>Detalii initializare Disc:</strong>');
define('_DISKSMANAGEINITPHP_INITINFO','\"fdisk: Geom not found\"nu este un mesaj de eroare!\n');
define('_DISKSMANAGEINITPHP_TEXT','Vor fi sterse toate partiile, va fi creata partiatia 1 si va fi formatat harddisc-ul cu sistemul de fisiere selectat.');
define('_DISKSMANAGEINITPHP_FORMATDISC','Formatare disc');
define('_DISKSMANAGEINITPHP_FORMATDISCCONF','Sigur doriti formatarea disc-ului? Veti pierde toate datele!');
define('_DISKSPHP_NOMBR','Nu stergeti MBR-ul');
define('_DISKSPHP_NOMBRTEXT','nu stergeti MBR-ul (util pentru unele contorlere RAID)');

//file: disks_manage_tools.php
define('_DISKSMANAGETOOLS_NAMEDESC','Unelte');
define('_DISKSMANAGETOOLS_COMMAND','Comenzi');
define('_DISKSMANAGETOOLS_SENDCMD','Comanda Send!');
define('_DISKSMANAGETOOLS_RAIDDISKNOTE','Utilizati <a href="%s">Unelte RAID</a> pentru acest disc!');
define('_DISKSMANAGETOOLS_CMDINFO','Iesire comanda:');
define('_DISKSMANAGETOOLS_MOUNTNOTE','Acest disc este montat! Punctul de montare disc va fi eliminat temporar pentru a efectua comanda selectata.');
define('_DISKSMANAGETOOLS_UMOUNT','De-montare disc/partitie');
define('_DISKSMANAGETOOLS_UMOUNTTEXT','Daca disc-ul/partitia selectata este montat va fi de-montat momentan pentru efectuarea comenzii, altfel comanda va fi rulata doar in modul citire.');

//file: disks_manage_iscsi.php
define('_DISKSMANAGEPHP_NAMEDESC','Management');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','Initiator iSCSI');
define('_DISKSMANAGEISCSIPHP_TARGETIP','Adresa IP Tinta');
define('_DISKSMANAGEISCSIPHP_TARGETIPTEXT','Adresa IP a tintei iSCSI.');
define('_DISKSMANAGEISCSIPHP_TARGETNAME','Nume Tinta');
define('_DISKSMANAGEISCSIPHP_TARGETNAMETEXT','Nume Tinta.');

//Software RAID
// Comon value
define('_DISKSRAIDPHP_MANAGE','Configurare RAID');
define('_DISKSRAIDPHP_FORMAT','Formatare RAID');
define('_DISKSRAIDPHP_TOOLS','Tools');
define('_DISKSRAIDPHP_INFO','Informatii');
define('_DISKSRAIDPHP_VOLUME','Nume Volum');
define('_DISKSRAIDPHP_RAIDVOLUMEMOUNTERROR','Volumul RAID este montat! Eliminati <a href=%s>punctul de montare</a> inainte de a continua.');

// Software RAID common word
define('_DISKSRAIDPHP_GVINUM','Geom Vinum');
define('_DISKSRAIDPHP_GMIRROR','Geom Mirror');
define('_DISKSRAIDPHP_GSTRIPE','Geom Stripe');
define('_DISKSRAIDPHP_GCONCAT','Geom Concat');
define('_DISKSRAIDPHP_GRAID5','Geom Raid5');
define('_DISKSRAIDPHP_UNSTABLE','(unstable)');

//file: disks_raid.php
define('_DISKSRAIDPHP_NAMEDESC','RAID');
define('_DISKSRAIDPHP_MSGCHANGED','Configuratia Raid a fost modificata.<br>Trebuie sa salvati modificarile pentru ca acestea sa devina active.');
define('_DISKSRAIDPHP_TYPE','Tip');
define('_DISKSRAIDPHP_EDITRAID','Editare RAID');
define('_DISKSRAIDPHP_DELRAID','Stergere RAID');
define('_DISKSRAIDPHP_ADDRAID','Adaugare RAID');
define('_DISKSRAIDPHP_DELCONF','Sigur stergeti volumul raid? Vor fi sterse toate elementele (date, partajari etc.)!');
define('_DISKSRAIDPHP_NOTE','Pas optional: Configurati ca disc RAID virtual <a href="disks_manage.php">un disc configurat anterior</a>.<br>Asteptati afisarea starii "Up" inainte de a-l formata si de a-l monta!');

//file: disks_raid_edit.php
define('_DISKSRAIDEDITPHP_NAMEDESC',_EDIT);
define('_DISKSRAIDEDITPHP_MSGADDDISKFIRST','Trebuia sa adaugati disc-ul mai intii.');
define('_DISKSRAIDEDITPHP_MSGVALIDNAME','Numele dispozitivului poate contine doar caracterele  a-z, A-Z, 0-9.');
define('_DISKSRAIDEDITPHP_MSGEXIST','Dispozitivul este deja in lista raid.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID0','Sint necesare cel putin 2 disc-uri pentru un volum RAID 0.');
define('_DISKSRAIDEDITPHP_MSGVALIDJBOD','Sint necesare cel putin 2 disc-uri pentru un JBOD.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID1','Sint necesare cel putin 2 disc-uri pentru un RAID 1.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID5','Sint necesare cel putin 3 disc-uri pentru un RAID 5.');
define('_DISKSRAIDEDITPHP_MSGERROR','ERROARE Configuratia RAID nu poate fi salvata.\n');
define('_DISKSRAIDEDITPHP_RAIDNAME','Nume Raid');
define('_DISKSRAIDEDITPHP_GVBALANCEALGO','Algoritm Balans');
define('_DISKSRAIDEDITPHP_GVBALANCEALGOTEXT','Selectati algoritmul de balans la citire.');
define('_DISKSRAIDEDITPHP_RAID0','striping');
define('_DISKSRAIDEDITPHP_RAID1','mirroring');
define('_DISKSRAIDEDITPHP_RAID5','paritate intretesere blocuri');
define('_DISKSRAIDEDITPHP_JBOD','JBOD');
define('_DISKSRAIDEDITPHP_MEMBERS','Membrii volumului');

//file: disks_raid_info.php
define('_DISKSRAIDINFOPHP_TEXT','Informatii despre volumul RAID Software');

//file: disks_raid_init.php
define('_DISKSRAIDINITPHP_INFO','Iesire Formatare Disc in mod UFS:');
define('_DISKSRAIDINITPHP_TEXT','Volumul RAID va fi formatat folosind sistemul de fisiere UFS (Unix FileSystem).');
define('_DISKSRAIDINITPHP_FORMAT','Formatare disc UFS!');

//file: disks_raid_tools.php
define('_DISKSRAIDTOOLSPHP_OBJNAME','NUme obiect');
define('_DISKSRAIDTOOLSPHP_COMMAND','Commnda');
define('_DISKSRAIDTOOLSPHP_INFO','iesire comanda:');
define('_DISKSRAIDTOOLSPHP_SENDCMD','Trimitere Comanda!');
define('_DISKSRAIDTOOLSPHP_TEXT','1. Folositi aceste comenzi speciale doar pentru analiza (debugging)<br>2. NU e nevoie sa folositi aceasta optiune a meniului pentru activarea volumului RAID (pornire automata).');

//Disk Mount Point
// Comon value
define('_DISKSMOUNTPHP_NAME','Punct Montare');
define('_DISKSMOUNTPHP_SHARENAME','Nume Share');
define('_DISKSMOUNTPHP_MANAGE','Configurare');
define('_DISKSMOUNTPHP_TOOLS','Unelte');

// disks_mount.php
define('_DISKSMOUNTPHP_MSGCHANGED','Lista punctelor de montare a fost schimbata.<br>Trebuie sa salvati modificarile pentru ca acestea sa devina active.');
define('_DISKSMOUNTPHP_NOTE','Pasul doi de configurare: Declararea sistemului de fisiere utilizat <a href="disks_manage.php">de disc-ul configurat anterior.</a><p>');
define('_DISKSMOUNTPHP_DELCONF','Doriti sa stergeti acest punct de montare? Toate elementele asociate, chiar active, vor fi sterse (inclusiv partajarile)!');
define('_DISKSMOUNTPHP_DEL','stergere pct. montare');
define('_DISKSMOUNTPHP_ADD','adaugare pct. montare');

// disks_mount_edit.php
define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','Numele pentru partajari poate contine doar caracterele a-z, A-Z, 0-9, _ , -.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','Numele contine caractere ilegale.');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','Partitia sistem 1 nu poate fi montata, partitia de date este partitia 2');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','Nume Partajare Duplicat.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDISK','Acest disc/partitie a fost deja configurat.');
define('_DISKSMOUNTEDITPHP_TEXT','1. Nu puteti monta partitia \'%s\' unde se afla fisierul de configuratie.<br>2. Sistemul NTFS pentru NTFS contine erori.');

// disks_mount_tools.php
define('_DISKSMOUNTTOOLS_COMMAND','Comanda');
define('_DISKSMOUNTTOOLS_SENDCMD','Trimitere Comanda!');
define('_DISKSMOUNTTOOLS_CMDINFO','Iesire Comanda:');
define('_DISKSMOUNTTOOLS_MOUNTTEXT','Mountare...');
define('_DISKSMOUNTTOOLS_UMOUNTTEXT','De-montare...');

// Service: CIFS/SAMBA
// Common text
define('_SRVCIFS_SETTINGS','Setar');
define('_SRVCIFS_SHARE','Partajare');
define('_SRVCIFS_SHARES','Partajari');

// file: services_samba.php
define('_SRVCIFS_NAMEDESC','CIFS');
define('_SRVCIFS_MSGVALIDNETBIOSNAME','caractere invalide in numele pentru Netbios.');
define('_SRVCIFS_MSGVALIDWORKGOUP','Caracter invalide in numele pentru Workgroup.');
define('_SRVCIFS_MSGVALIDWINS','Server-ul WINS trebuie sa fie o adresa IP.');
define('_SRVCIFS_MSGVALIDSNDBUFF','Valoarea ptr. buffer-ul SND trebuie sa fie un numar.');
define('_SRVCIFS_MSGVALIDRCVBUFF','Valoarea ptr. buffer-ul RCV trebuie sa fie un numar.');
define('_SRVCIFS_SHARE','Partajare CIFS');
define('_SRVCIFS_AUTH','Autentificare');
define('_SRVCIFS_NETBIOSNAME','Nume NetBios');
define('_SRVCIFS_WORKGROUP','Workgroup');
define('_SRVCIFS_WORKGROUPTEXT','Membru al Workgroup-ului.');
define('_SRVCIFS_DESCTEXT','Descriere Server. Poate fi nula.');
define('_SRVCIFS_DOSCHARSET','Caractere Dos');
define('_SRVCIFS_UNIXCHARSET','Caractere Unix');
define('_SRVCIFS_LOGLEVEL','Nivel Jurnal');
define('_SRVCIFS_LOCALMASTER','Master Browser Local');
define('_SRVCIFS_LOCALMASTERTEXT','Permite sistemului FreeNAS sa devina un Master Browser local.');
define('_SRVCIFS_TIMESRV','Server Baza de Timp');
define('_SRVCIFS_TIMESRVTEXT','Initializare FreeNAS ca server de timp pentru clientii Windows.');
define('_SRVCIFS_WINS','Server WINS');
define('_SRVCIFS_WINSTEXT','Adresa Server WINS.');
define('_SRVCIFS_RECYCLE','Recycle Bin');
define('_SRVCIFS_ENRECYCLE','Activare Recycle bin');
define('_SRVCIFS_ENRECYCLETEXT','Va fi creat un Recycle Bin in partajarea de tip CIFS');
define('_SRVCIFS_SNBBUFF','Marime Buffer Trimitere');
define('_SRVCIFS_SNBBUFFTEXT','Marimea buffer-ului de Trimitere (implicit 16384).');
define('_SRVCIFS_RCVBUFF','Marime Buffer Primire');
define('_SRVCIFS_RCVBUFFTEXT','Marimea buffer-ului de intrare (implicit 16384).');

// file: services_samba_share.php
define('_SRVCIFSSHARE_MSGCHANGED','Au fost modificat partajarile.<br>Modificarile trebuie salvate pentru a deveni active.');
define('_SRVCIFSSHARE_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHARE_BROWSEABLE','Rasfoire');
define('_SRVCIFSSHARE_DESC',_DESC);
define('_SRVCIFSSHARE_EDIT','Editare partajari');

// file: services_samba_share_edit.php
define('_SRVCIFSSHAREEDIT_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHAREEDIT_DESC',_DESC);
define('_SRVCIFSSHAREEDIT_BROWSEABLE','Rasfoire');
define('_SRVCIFSSHAREEDIT_BROWSEABLETEXT','Se stabileste daca partajarea definita este vizibila sau nu in modul Rasfoire.');

// Service: FTP
// file: services_ftp.php
define('_SRVFTP_NAMEDESC','FTP');
define('_SRVFTP_MSGVALIDPORT',_MSGVALIDTCPPORT);
define('_SRVFTP_MSGVALIDMAXCLIENT','Numar maxim de clienti invalid.');
define('_SRVFTP_MSGVALIDMAXIP','Numar maxim de conexiuni pe IP invalid.');
define('_SRVFTP_MSGVALIDIDLE','Timp maxim mod idle invalid.');
define('_SRVFTP_MSGVALIDPASVIP','Adresa pasv trebuie sa fie o adresa IP publica.');
define('_SRVFTP_MSGVALIDPASVMAX','Port pasv_max_port invalid.');
define('_SRVFTP_MSGVALIDPASVMIN','Port pasv_min_port invalid.');
define('_SRVFTP_MSGVALIDAUTH','Trebuie sa selectati cel putin modul anonymous sau/si cu autentificare locala.');
define('_SRVFTP_FTPSERVER','Server FTP');
define('_SRVFTP_TCP',_TCPPORT);
define('_SRVFTP_MAXCLIENT','Numarul de clienti');
define('_SRVFTP_MAXCLIENTTEXT','Numarul maxim de clienti.');
define('_SRVFTP_MAXIP','Max. con. pe IP');
define('_SRVFTP_MAXIPTEXT','Numar maxim de conexiuni pe IP (0 = nelimitat).');
define('_SRVFTP_TIMEOUT','Timeout');
define('_SRVFTP_TIMEOUTTEXT','Timp maximum idle in secunde.');
define('_SRVFTP_ANONYMOUS','Login Anonymous login');
define('_SRVFTP_ENANONYMOUS','Activare login anonymous.');
define('_SRVFTP_AUTH','Utilizator Local');
define('_SRVFTP_ENAUTH','Activare login utilizator local.');
define('_SRVFTP_FXP','FXP');
define('_SRVFTP_ENFXP','Activare protocol FXP.');
define('_SRVFTP_FXPTEXT','FXP permite transferul direct, la viteza maxima, intre doua servere fara ca traficul solicitat sa treaca si pe la clientul care a initiat transferul!');
define('_SRVFTP_NATMODE','Mod NAT');
define('_SRVFTP_ENNATMODE','Fortare mod NAT.');
define('_SRVFTP_NATMODETEXT','Activati daca server-ul dvs. FTP se gaseste in spatele unui proxy care nu suporta modul proxy pentru FTP.');
define('_SRVFTP_PASSIVEIP','Adresa IP pasiva');
define('_SRVFTP_PASSIVEIPTEXT','Folositi aceasta optiune pentru a suprascrie adresa IP furnizata de daemon-ul FTP ca raspuns la o comanda PASV.');
define('_SRVFTP_PASVMIN','pasv_min_port');
define('_SRVFTP_PASVMINTEXT','Portul minim alocat pentru modul de conectare PASV (0 = orice port).');
define('_SRVFTP_PASVMAX','pasv_max_port');
define('_SRVFTP_PASVMAXTEXT','Portul maxim alocat pentru modul de conectare PASV (0 = orice port).');
define('_SRVFTP_BANNER','Baner');
define('_SRVFTP_BANNERTEXT','Baner afisat la conectare de catre server-ul FTP.');

// Service: NFS
// file: services_nfs.php
define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','Trebuie introdusa o retea valida.');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','Server NFS');
define('_SRVNFS_MAPALLROOT','Mapare toti utilizatori in root');
define('_SRVNFS_MAPALLROOTTEXT','Toti utilizatorii vor avea privilegii de root.');
define('_SRVNFS_AUTHNET','Retea Autorizata');
define('_SRVNFS_AUTHNETTEXT','Retea autorizata pentru partajarea NTFS');
define('_SRVNFS_AUTHNETMASK','Masca Subretea');
define('_SRVNFS_TEXT','Numele directorului exportat: /mnt/sharename');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','Server');
define('_SRVRYNC_CLIENT','Client');
define('_SRVRYNC_LOCAL','Local');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','DaemonRsync Daemon');
define('_SRVRYNCD_READONLY','Doar citire');
define('_SRVRYNCD_MAPUSER','Mapare Utilizator');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_TCPORTTEXT','Port Alternativ TCP.');
define('_SRVRYNCD_MAXCON','Numar Maxim Conexiuni');
define('_SRVRYNCD_MAXCONTEXT','Numarul maxim de conexiuni simultane. Implicit 0 (nelimitat).');
define('_SRVRYNCD_MOTD','MOTD');
define('_SRVRYNCD_MOTDTEXT','Mesajul Zilei.');

// file: services_rsyncd_client.php
define('_SRVRYNCC_MSGMPFIRST','Trebuie sa configurati un punct de monatre mai intii.');
define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
define('_SRVRYNCC_RSYNCC','Sincronizare Rsync Client');
define('_SRVRYNCC_REMOTESERVER','Server Remote RSYNC');
define('_SRVRYNCC_REMOTEID','Adresa IP a unui server RSYNC');
define('_SRVRYNCC_OPTIONS','Optiuni RSYNC');
define('_SRVRYNCC_OPTDEL','Stergere fisiere care nu exista la expeditor.');
define('_SRVRYNCC_SHARES','Partajari de sincronizat');
define('_SRVRYNCC_SHARESTEXT','ACeeasi partajare local trebuie sa fie prezenta si la Remote Server.');
define('_SRVRYNCC_TIME','Timp Sincronizare');
define('_SRVRYNCC_TEXT',_MSGSCHED_TEXT);

// file: services_rsyncd_local.php
define('_SRVRYNCL_MSGVALID','Nu aveti acelasi punct de montare si la sursa si la destinatie!');
define('_SRVRYNCC_RSYNCL','Sincronizare Locala Rsync');
define('_SRVRYNCL_SOURCE','Sursa');
define('_SRVRYNCL_SOURCETEXT','Punct Sursa Montare.');
define('_SRVRYNCL_DESTINATION','Destinatie');
define('_SRVRYNCL_DESTINATIONTEXT','Punct Montare Destinatie (backup drive).');


// Service: SSHD
// file: services_sshd.php
define('_SRVSSHD_NAMEDESC','SSHD');
define('_SRVSSHD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVSSHD_MSGVALIDKEY','Cheie SSH invalida.');
define('_SRVSSHD_SSHD','Daemon SSH');
define('_SRVSSHD_READONLY','Doar Citire');
define('_SRVSSHD_TCPORT',_TCPPORT);
define('_SRVSSHD_TCPORTTEXT',_SRVRYNCD_TCPORTTEXT);
define('_SRVSSHD_PERMITROOTLOGIN','Permisiune login root');
define('_SRVSSHD_PERMITROOTLOGINTEXT','Specifica daca va puteti loga ca superutilizator (root) in mod direct.');
define('_SRVSSHD_KEY','Cheie SSH');
define('_SRVSSHD_PRIVATEKEY','Cheie Privata');
define('_SRVSSHD_PRIVATEKEYTEXT','Introduceti o CHEIE PRIVATA DSA in formatul PEM.');

// Service: Unison
// file: services_unison.php
define('_SRVUNSN_NAMEDESC','Unison');
define('_SRVUNSN_MSGVALIDWRKDIR','Combinatia de director si partajare nu exista.');
define('_SRVUNSN_UNISON','Sincronizare Sistem Fisiere Unison');
define('_SRVUNSN_SHARE','Partajare');
define('_SRVUNSN_SHAREDESC','Aveti nevoie de spatiu pentru a duplica toate fisierele care sint sincronizate');
define('_SRVUNSN_WORKDIR','Director de Lucru');
define('_SRVUNSN_WORKDIRDESC','Unde sint stocate fisierele de lucru');
define('_SRVUNSN_MAKEDIR','Creare');
define('_SRVUNSN_MAKEDIRLABEL','Creare director de lucru daca acesta nu exista');
define('_SRVUNSN_NOTESSH','<a href="/services_sshd.php">SSHD</a> trebuie activat pentru ca modul  Unison sa functioneze si <a href="/access_users.php">utilizatoruluser</a> trebuie sa aiba optiunea  Full Shell activata.');

// Service: UPnP
// file: services_upnp.php
define('_SRVUPNP_NAMEDESC','UPnP');
define('_SRVUPNP_UPNP','Server UPnP A/V');
define('_SRVUPNP_MSGCHANGED','Lista continutului directorului curent a fost modificata.<br>Trebuie sa salvati modificarile pentru ca acestea sa devina active.');
define('_SRVUPNP_NAME','Nume');
define('_SRVUPNP_NAMETEXT','Nume UPnP.');
define('_SRVUPNP_INTERFACE','Interfata');
define('_SRVUPNP_INTERFACETEXT','Interfata activa.');
define('_SRVUPNP_CONTENT','Continut');
define('_SRVUPNP_CONTENTTEXT','Directoare de partajat.');
define('_SRVUPNP_CONTENTINVALIDMSG','Niciun director de partajat.');
define('_SRVUPNP_DIRECTORY','Director');
define('_SRVUPNP_ADDDIR','Adaugare director');
define('_SRVUPNP_EDITDIR','Editare director');
define('_SRVUPNP_DELDIR','Stergere director');
define('_SRVUPNP_DELCONF','Doriti sa stergeti link-ul la director?');

// file: services_upnp_edit.php
define('_SRVUPNPEDIT_CONTENT','Continut');
define('_SRVUPNPEDIT_CONTENTTEXT','Directorul de partajat.');

// Service: AFP
// file: services_afp.php
define('_SRVAFP_NAMEDESC','AFP');
define('_SRVAFP_MSGVALIDAUTH','Trebuie sa selectati cel putin o metoda de autentificare.');
define('_SRVAFP_AFPSRV','Server AFP');
define('_SRVAFP_AFPNAME','Nume Server');
define('_SRVAFP_AUTH','Autentificare');
define('_SRVAFP_ENABLEGUEST','Activare acces mod guest.');
define('_SRVAFP_ENABLELOCAL','Activare autentificare locala.');

// Access
// common words
define('_ACCESS_NAME','Acces');
define('_ACCESS_USER','Utiliztor');
define('_ACCESS_USERS','Utilizatori');
define('_ACCESS_GROUP','Grup');
define('_ACCESS_GROUPS','Grupuri');
define('_ACCESS_FULLNAME','Nume Complet');

// file: access_users.php
define('_ACCESSUSERS_MSGCHANGED','Lista utilizatorilor a fost modificata.<br>Trebuie sa salvati modificarile pentru ca aceasta sa devina activa.');
define('_ACCESSUSERS_EDITUSER','Editare utilizator');
define('_ACCESSUSERS_DELUSER','Sterger utilizator');
define('_ACCESSUSERS_CONFDEL','Doriti sa stergeti utilizatorul?!');
define('_ACCESSUSERS_ADDUSER','Adaugare utilizator');

// file: access_users_edit.php
define('_ACCESSUSERSEDIT_MSGGROUPFIRST','trebuie creat un grup mai intii.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGIN','Numele de login contine caractere invalide.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED','Numele introdus reprezinta un nume rezervat.');
define('_ACCESSUSERSEDIT_MSGVALIDFULLNAME','Numele complet contine caractere invalide.');
define('_ACCESSUSERSEDIT_MSGVALIDDUPLICATE','Acest nume de utilizator exista deja.');
define('_ACCESSUSERSEDIT_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSUSERSEDIT_MSGVALIDPASSWDBADCHAR','Parola contine caracterul invalid ":"');
define('_ACCESSUSERSEDIT_LOGIN',_LOGIN);
define('_ACCESSUSERSEDIT_LOGINTEXT','Nume unic de login utilizator.');
define('_ACCESSUSERSEDIT_FULLNAME',_ACCESS_FULLNAME);
define('_ACCESSUSERSEDIT_FULLNAMETEXT','Nume complet utilizator.');
define('_ACCESSUSERSEDIT_PASSWORD','Parola');
define('_ACCESSUSERSEDIT_PASSWORDTEXT','Parola Utilizator.');
define('_ACCESSUSERSEDIT_PASSWORDCONF','Confirmare Parola');
define('_ACCESSUSERSEDIT_PASSWORDCONFTEXT','Confirmare Parola.');
define('_ACCESSUSERSEDIT_GROUPMEMBER','Membri Grup');
define('_ACCESSUSERSEDIT_FULLSHELL','Shell Complet');
define('_ACCESSUSERSEDIT_FULLSHELLTEXT','Shell complet pentru utilizator');

// file: access_users_groups.php
define('_ACCESSGROUPS_MSGCHANGED','Lista grupurilor a fost modificata.<br>Trebuie sa salvati modificarile pentru ca aceasta sa devina activa.');
define('_ACCESSGROUPS_DESC',_DESC);
define('_ACCESSGROUPS_GROUPEDIT','Editare grup');
define('_ACCESSGROUPS_GROUPDEL','Sterger grup');
define('_ACCESSGROUPS_GROUPDELCONF','Stergeti acest grup?!');
define('_ACCESSGROUPS_GROUPADD','Adaugare grup');

// file: access_users_groups_edit.php
define('_ACCESSGROUPSEDIT_MSGVALIDNAME','Numele de Grup contine caractere invalide.');
define('_ACCESSGROUPSEDIT_MSGVALIDDESC','Descrierea Grup-ului contine caractere invalide.');
define('_ACCESSGROUPSEDIT_MSGVALIDDUPLICATE','Acest grup exista deja.');
define('_ACCESSGROUPSEDIT_NAME','Nume');
define('_ACCESSGROUPSEDIT_NAMETEXT','Nume de grup.');
define('_ACCESSGROUPSEDIT_DESC',_DESC);
define('_ACCESSGROUPSEDIT_DESCTEXT','Descriere Grup.');

// file: access_ad.php
define('_ACCESSAD_NAME','Active Directory');
define('_ACCESSAD_MSGVALIDIP',_MSGVALIDIP);
define('_ACCESSAD_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSAD_ADNAME','Nume server AD');
define('_ACCESSAD_ADNAMETEXT','Nume server AD sau PDC.');
define('_ACCESSAD_IP','Adresa IP server AD');
define('_ACCESSAD_IPTEXT','Adresa IP pentru MS Active Directory.');
define('_ACCESSAD_DOMAIN','NUme Domeniu');
define('_ACCESSAD_DOMAINTEXT','Nume Domeniu in formatul vechi.');
define('_ACCESSAD_ADMINAME','Nume Administrator');
define('_ACCESSAD_ADMINNAMETEXT','Numele utilizator pentru administrator domeniu.');
define('_ACCESSAD_ADMINPASS','Parola Administrare');
define('_ACCESSAD_ADMINPASSTEXT','Parola administrator domeniu, introduceti-o de doua ori.');

// Diagnostics
define('_DIAG_NAME','Diagnostice');

// file: shutdown.php
define('_SHUT_NAME',_MENULEFT_CMDSHUTDOWNSYS);
define('_SHUT_MSGSHUT','Sistemul de opreste> Dureaza aprox. 1 min..');
define('_SHUT_CONF','Doriti sa opriti sistemul?');

// file: shutdown_sched.php
define('_SHUTSCHED_NAME','Oprire Programata');
define('_SHUTSCHED_TIME','Timp de Oprire');
define('_SHUTSCHED_TEXT',_MSGSCHED_TEXT);

// file: diag_edit.php
define('_DIAGEDITPHP_NAMEDESC','Editare Fisier');
define('_DIAGEDITPHP_FILEPATH','Cale Fiser');
define('_DIAGEDITPHP_SAVEMSG','Salvare fiser la');
define('_DIAGEDITPHP_FILENOTFOUND','Fisier negasit');
define('_DIAGEDITPHP_ROWS','Rinduri');
define('_DIAGEDITPHP_COLS','Coloane');
define('_DIAGEDITPHP_HIGHLIGHTING','Evidentiere');
define('_DIAGEDITPHP_HIGHLIGHTINGENABLED','Activat');
define('_DIAGEDITPHP_HIGHLIGHTINGDISABLED','Dezactivat');

// file: exec.php
define('_DIAGEXECPHP_NAMEDESC','Executare comanda');

// file: reboot.php
define('_DIAGREBOOT_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGREBOOT_NAMEDESC',_MENULEFT_CMDREBOOTSYS);
define('_DIAGREBOOT_REBOOTMSG','Sistemul reporneste. Dureza aprox. 1 min..');
define('_DIAGREBOOT_REBOOTCONF','Doriti sa reporniti sistemul?');

// file: license.php
define('_LICENSE_NAME','Licente');

// file: status_wireless.php
define('_STATSWIRELESS_NAME',_MENULEFT_STATUS);
define('_STATSWIRELESS_NAMEDESC','Wireless');
define('_STATSWIRELESS_INTERFACE','interfata %s (SSID \'%s\')');
define('_STATSWIRELESS_SIGNALSTRENGTHCACHE','Cache Tarie Semnal');
define('_STATSWIRELESS_MACADDRESS','Adresa MAC');
define('_STATSWIRELESS_IPADDRESS','Adresa IP');
define('_STATSWIRELESS_SIGNAL','Semnal');
define('_STATSWIRELESS_NOISE','Zgomot');
define('_STATSWIRELESS_QUALITY','Calitate');
define('_STATSWIRELESS_ASSOCIATEDSTATIONS','Statie Asociata');
define('_STATSWIRELESS_TXRATES','rate TX');
define('_STATSWIRELESS_MSGNOWINTFOUND','Nicio cartela wireless gasita (sint suportate doar cartelele wireless cu driver windows).');

// file: status_interfaces.php
define('_STATSINTERFACES_NAME',_MENULEFT_STATUS);
define('_STATSINTERFACES_NAMEDESC',_MENULEFT_INTERFACES);
define('_STATSINTERFACES_INTERFACE','%s interfata');
define('_STATSINTERFACES_STATUS','Stare');
define('_STATSINTERFACES_DHCP','DHCP');
define('_STATSINTERFACES_PPPOE','PPPoE');
define('_STATSINTERFACES_PPTP','PPTP');
define('_STATSINTERFACES_MACADDRESS','Adresa MAC');
define('_STATSINTERFACES_IPADDRESS','Adresa IP');
define('_STATSINTERFACES_SUBNETMASK','Masca Subnet');
define('_STATSINTERFACES_GATEWAY','Gateway');
define('_STATSINTERFACES_ISPDNSSERVERS','server DNS ISP');
define('_STATSINTERFACES_MEDIA','Media');
define('_STATSINTERFACES_CHANNEL','Chanal');
define('_STATSINTERFACES_SSID','SSID');
define('_STATSINTERFACES_IOPACKETS','pachete I/O');
define('_STATSINTERFACES_IOERRORS','erori I/O');
define('_STATSINTERFACES_COLLISIONS','Coliziuni');

// file: status_graph_cpu.php
define('_STATSGRAPHCPU_NAME',_MENULEFT_STATUS);
define('_STATSGRAPHCPU_NAMEDESC','Incarcare CPU');

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
define('_INTVLAN_INTERFACE','Interfata');
define('_INTVLAN_VLANTAG','tag VLAN');
define('_INTVLAN_DESC',_DESC);

// file: interfaces_vlan_edit.php
define('_INTVLANEDIT_NAME',_MENULEFT_INTERFACES);
define('_INTVLANEDIT_NAMEDESC',_INTASSIGNPHP_NAMEDESC);
define('_INTVLANEDIT_EDITVLAN','Editare VLAN');

// file: diag_ping.php
define('_DIAGPING_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGPING_NAMEDESC',_MENULEFT_DIAGPINGTRACE);

// file: diag_traceroute.php
define('_DIAGTRACEROUTE_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGTRACEROUTE_NAMEDESC',_MENULEFT_DIAGPINGTRACE);
define('_DIAGTRACEROUTE_NOTE','Trasarea rutei poate dura citeva momente. Puteti apasa in orice moment butonul Stop in browser pentru a vedea rutele descoperite pina in acest moment.');

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
define('_DIAGINFOS_PARTITIONS','Partitii');
define('_DIAGINFOS_SMART','S.M.A.R.T.');
define('_DIAGINFOS_ATAIDLE','ATAidle');
define('_DIAGINFOS_SPACEUSED','Spatiu Utilizat');
define('_DIAGINFOS_MOUNTS','Mountari');
define('_DIAGINFOS_SOFTWARERAID',_SOFTRAID);
define('_DIAGINFOS_ISCSI','iSCSI');
define('_DIAGINFOS_MSDOMAIN','Domeniu MS');

// file: diag_infos.php
define('_DIAGINFOSDISKS_ATA_INFO','Lista disc-urilor ATA detectate');
define('_DIAGINFOSDISKS_SCSI_INFO','Lista disc-urilor SCSI detectate');

// file: diag_infos_mount.php
define('_DIAGINFOSMOUNT_INFO','Lista punctelor de montare');

// file: diag_infos_space.php
define('_DIAGINFOSSPACE_INFO','Spatiu Liber');

// file: diag_defaults.php
define('_DIAGDEFAULTS_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGDEFAULTS_NAMEDESC',_MENULEFT_CMDFACTORYDEF);

// file: diag_backup.php
define('_DIAGBACKUP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGBACKUP_NAMEDESC',_MENULEFT_CMDBACKUPRESTORE);
define('_DIAGBACKUP_RESTORENOTE','FreeNAS va reporni dupa restaurarea configuratiei.');

// file: diag_arp.php
define('_DIAGARP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGARP_NAMEDESC',_MENULEFT_DIAGARPTAB);
define('_DIAGARP_IPADDRESS','Adresa IP');
define('_DIAGARP_MACADDRESS','Adresa MAC');
define('_DIAGARP_HOSTNAME','Hostname');
define('_DIAGARP_INTERFACE','Interfata');
define('_DIAGARP_DEL','Sterge intrare ARP');
define('_DIAGARP_DELALL','Sterge toate intrarile in tabela ARP');
define('_DIAGARP_NOTE','Adresa IP va fi inlocuita cu hostnames daca optiunea&quot;Rezolvare Adresa IP in HostName&quot; este activa in optiunea meniu <a href="diag_logs_settings.php">Diagnostic: Jurnale</a>.');

// file: status_graph.php
define('_STATSGRAPH_NAME',_MENULEFT_STATUS);
define('_STATSGRAPH_NAMEDESC','Grafice Trafic');

// file: graph.php
// Note for translator: Do not use international html coding here
define('_GRAPH_ERRORGETIFDATA','Nicio informatie disponibila despre interfeta');
define('_GRAPH_IN','In');
define('_GRAPH_OUT','Out');
define('_GRAPH_SWITCHTO','Comutare la');
define('_GRAPH_AUTOSCALE','AutoScalare');
define('_GRAPH_AUTOSCALE_FOLLOW','Urmarire');
define('_GRAPH_AUTOSCALE_UP','Up');
define('_GRAPH_SHOWLAST','Graphic final');
define('_GRAPH_COLLECTDATA','Colectare initiala date, va rugam asteptati...');
?>
