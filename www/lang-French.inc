<?php 
/*
	lang-French.php
	fait partie de FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.
	
	Traduction par Régis Caubet (caubet.r@gmail.com)
	
	Bas&eacute; sur m0n0wall (http://m0n0.ch/wall)
	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
	All rights reserved.
	
	La redistribution et l'utilisation sous forme binaire ou de code source, 
	avec ou sans modification, est autoris&eacute;e &agrave; condition que les conditions suivantes soient respect&eacute;es: 
	
	1.Les redistributions du code source doivent conserver l'indication de copyright ci-dessus,
	cette liste de conditions et la renonciation suivante. 

	2.Les redistributions sous forme binaire doivent reproduire l'indication de copyright ci-dessus,
	cette liste de conditions et la renonciation suivante dans la documentation et/ou les autres accessoires
	fournis avec la distribution. 

	CE LOGICIEL EST FOURNI PAR LES TITULAIRES DU COPYRIGHT ET LES PARTICIPANTS "TEL QUEL"
	ET TOUTES LES GARANTIES EXPLICITES OU IMPLICITES, Y COMPRIS, MAIS NON LIMITÉES A CELLES-CI,
	LES GARANTIES IMPLICITES DE COMMERCIALISATION ET D'ADÉQUATION À UN USAGE PARTICULIER SONT DÉNIÉES.
	EN AUCUN CAS LES TITULAIRES DU COPYRIGHT OU LES PARTICIPANTS NE PEUVENT ÊTRE TENUS POUR RESPONSABLE
	DES DOMMAGES DIRECTS, INDIRECTS, FORTUITS, PARTICULIERS, EXEMPLAIRES OU CONSÉCUTIFS À UNE ACTION 
	(Y COMPRIS, MAIS NON LIMITÉS À CEUX-CI, L'ACQUISITION DE MARCHANDISES OU DE SERVICES DE REMPLACEMENT;
	LES PERTES D'UTILISATIONS, DE DONNÉES OU FINANCIÈRES; OU L'INTERRUPTION D'ACTIVITÉS)
	DE QUELQUE MANIÈRE QUE CES DOMMAGES SOIENT CAUSÉS ET CECI POUR TOUTES LES THÉORIES DE RESPONSABILITÉS,
	QUE CE SOIT DANS UN CONTRAT, POUR DES RESPONSABILITÉS STRICTES OU DES PRÉJUDICES
	(Y COMPRIS DUS À UNE NEGLIGENCE OU AUTRE CHOSE) SURVENANT DE QUELQUE MANIÈRE QUE CE SOIT
	EN DEHORS DE L'UTILISATION DE CE LOGICIEL, MÊME EN CAS D'AVERTISSEMENT DE LA POSSIBILITÉ DE TELS DOMMAGES. 
*/
// locale defines
define('_CHARSET','ISO-8859-15');
define('_LOCALE','fr_FR');
define('_LOCALEWIN', 'fra');
// date and time defines
define('_DATE','Date');
define('_DATEBRIEF','%b %d, %Y');
define('_DATELONG','%A, %B %d, %Y');
define('_DATESTRING','%A, %B %d @ %H:%M:%S');
define('_DATETIMEBRIEF','%b %d, %Y - %I:%M %p');
define('_DATETIMELONG','%A, %B %d, %Y - %I:%M %p');
define('_DAY_OF_WEEK_LONG','Dimanche Lundi Mardi Mercredu Jeudi Vendredi Samedi');
define('_DAY_OF_WEEK_SHORT','Dim Lun Mar Mer Jeu Ven Sam');
define('_MONTH_LONG','Janvier Fevrier Mars Avril Mai Juin Juillet Août Septembre Octobre Novembre Decembre');
define('_MONTH_SHORT','Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec');
define('_TIME', 'Time');
define('_TIMEBRIEF','%I:%M %p');
define('_SECOND', 'seconde');
define('_SECONDS', 'secondes');
define('_MINUTE', 'minute');
define('_MINUTES', 'minutes');
define('_HOUR', 'heure');
define('_HOURS', 'heures');
define('_DAY', 'jour');
define('_DAYS', 'jours');
define('_WEEK', 'semaine');
define('_WEEKS', 'semaines');
define('_WEEKDAYS', 'jour de la semaine');
define('_MONTH', 'mois');
define('_MONTHS', 'mois');
define('_YEAR', 'ann&eacute;e');
define('_YEARS', 'ann&eacute;es');

// common words
define('_ALL','tout');
define('_AND','et');
define('_BY','par');
define('_DOWN','bas');
define('_NO','Non');
define('_OF','off');
define('_OK','OK');
define('_ON','on');
define('_TO','&agrave;');
define('_THE', 'le');
define('_UP','haut');
define('_URL', 'URL');
define('_YES','Oui');

// common actions
define('_ACTIVATE','Activer');
define('_ADD','Ajouter');
define('_CANCEL', 'Annuler');
define('_COMMIT', 'Update');
define('_CREATE', 'Cr&eacute;er');
define('_DEACTIVATE','Desactiver');
define('_DELETE','Supprimer');
define('_EDIT','Editer');
define('_ENABLE','Activer');
define('_HELP', 'Aide');
define('_IGNORE','Ignorer');
define('_LOGIN','Connexion');
define('_LOGOUT','D&eacute;connexion');
define('_MODIFY','Modifier');
define('_NEW','Nouveau');
define('_SEARCH', 'Chercher');
define('_SAVE', 'Enregister');
define('_SUBMIT','Submit');
define('_UPDATE', 'Mise &agrave; jour');
define('_VIEW', 'Visualiser');
define('_APPLY', 'Appliquer les changements');
define('_WARNING', 'Attention');
define('_LOAD', 'Charger');

//FreeNAS common words
define('_SIZE', 'Taille');
define('_STATUS', 'Etat');
define('_TYPE', 'Type');
define('_CONFIGURING', 'en cours de configuration');
define('_DISK', 'Disque');
define('_DISKS', 'Disques');
define('_PARTITION', 'Partition');
define('_FILESYSTEM', 'Syst&egrave;me de fichier');
define('_DESC', 'Description');
define('_SOFTRAID', 'RAID logiciel');
define('_SERVICE','Service');
define('_SERVICES','Services');
define('_TCPPORT','Port TCP');
define('_CONFIRMATION','Confirmation');
define('_NOTE','Note');

// common messages
define('_MESSAGES_SUCCESS', 'Les modifications ont &eacute;t&eacute; appliqu&eacute;es avec succ&egrave;s.');
define('_MESSAGES_SUCCESSWITHREBOOT', 'Les modifications ont &eacute;t&eacute; enregistr&eacute;es. Vous devez <a href=reboot.php>red&eacute;marrer</a> votre serveur NAS pour que les modifications prennent effet.');
define('_MESSAGES_ERROR', 'Erreur: Les modifications ne peuvent &ecirc;tre appliqu&eacute;es (code d\'erreur '.$ok.').');

define('_MSGVALIDIP','Une adresse IP valide doit &ecirc;tre sp&eacute;cifi&eacute;e.');
define('_MSGVALIDNETBIT','Un nombre de bit valide doit &ecirc;tre sp&eacute;cifi&eacute;.');
define('_MSGVALIDTCPPORT','Le port TCP doit &ecirc;tre un nombre valide compris entre 1024 et 65564.');
define('_MSGVALIDPASSWORD','Les mots de passe ne correspondent pas.');

define('_MSGSCHED_TEXT','Note: Ctrl-clic (ou command-clic sur Mac) pour s&eacute;lectionner et d&eacute;s&eacute;lectionner minutes, heures, jours et mois.');

// states
define('_ACTIVE','Actif');
define('_INACTIVE','Inactif');
define('_SELECTED','Selected');

// Webgui layout
define('_MENULEFT_SYSTEM','Syst&egrave;me');
define('_MENULEFT_SYSGENSETUP','Configuration globale');
define('_MENULEFT_SYSSTATROUTES','Routes Statiques');
define('_MENULEFT_SYSFIRMWARE','Logiciel');
define('_MENULEFT_SYSADVANCED','Configuration avanc&eacute;e');
define('_MENULEFT_INTERFACES','Interfaces');
define('_MENULEFT_INTASSIGN','Assigner');
define('_MENULEFT_INTLAN','LAN');
define('_MENULEFT_DISKS',_DISKS);
define('_MENULEFT_DISKMANAG','Gestion');
define('_MENULEFT_DISKMNGSOFTRAID',_SOFTRAID);
define('_MENULEFT_DISKMNGMOUNTPOINT','Point de montage');
define('_MENULEFT_SERVICES','Services');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVUNSN','Unison');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','Acc&egrave;s');
define('_MENULEFT_ACUSERGROUPS','Utilisateurs et groupes');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS',_STATUS);
define('_MENULEFT_STSYSTEM','Syst&egrave;me');
define('_MENULEFT_STPROCESS','Processus');
define('_MENULEFT_STINTERFACES','Interfaces');
define('_MENULEFT_STDISKS',_DISKS);
define('_MENULEFT_STWIRELESS','Sans fil');
define('_MENULEFT_EXTENSIONS','Extensions');
define('_MENULEFT_DIAGNOSTICS','Diagnostics');
define('_MENULEFT_DIAGLOGS','Journaux');
define('_MENULEFT_DIAGINF','Information');
define('_MENULEFT_DIAGPINGTRACE','Ping/Traceroute');
define('_MENULEFT_DIAGARPTAB','Table ARP');
define('_MENULEFT_DIAGEXEC','Commandes');
define('_MENULEFT_DIAGEDITFILE','Editer un fichier');
define('_MENULEFT_CMDBACKUPRESTORE','Sauvegarde/Restauration');
define('_MENULEFT_CMDFACTORYDEF','R&eacute;glages d\'origine');
define('_MENULEFT_CMDREBOOTSYS','Red&eacute;marrage du NAS');
define('_MENULEFT_CMDSHUTDOWNSYS','Arr&ecirc;t du NAS');
define('_MENULEFT_GRAPH','Graphe');

// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION',_MENULEFT_SYSGENSETUP);
define('_SYSTEMPHP_HOSTNAME','Nom syst&egrave;me');
define('_SYSTEMPHP_HOSTNAMETEXT','Nom du serveur NAS, sans pr&eacute;ciser le nom de domaine<br>ex. <em>nas');
define('_SYSTEMPHP_DOMAIN','Domaine');
define('_SYSTEMPHP_DOMAINTEXT','ex: <em>macompagnie.fr</em>');
define('_SYSTEMPHP_DNS','Serveurs DNS');
define('_SYSTEMPHP_DNSTEXT','Adresse IP des serveurs DNS');
define('_SYSTEMPHP_USERNAME','Nom utilisateur');
define('_SYSTEMPHP_USERNAMETEXT','Nom utilisateur pour acc&eacute;der &agrave; l\'interface WEB.');
define('_SYSTEMPHP_PASSWORD','Mot de passe');
define('_SYSTEMPHP_PASSWORDTEXT','Mot de passe d\'acc&egrave;s &agrave; l\'interface WEB.<br>N\'utilisez pas le caract&egrave;re ":"');
define('_SYSTEMPHP_WEBGUIPROTOCOL','Protocole de l\'interface WEB');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','');
define('_SYSTEMPHP_WEBGUIPORT','port TCP de l\'interface WEB');
define('_SYSTEMPHP_WEBGUIPORTTEXT','Entrez le num&eacute;ro d\'un port si vous d&eacute;sirez changer celui par d&eacute;faut (80 pour HTTP, 443 pour HTTPS).');
define('_SYSTEMPHP_LANGUAGE','Langue');
define('_SYSTEMPHP_LANGUAGETEXT','Choisissez la langue utilis&eacute;e par le syst&egrave;me');
define('_SYSTEMPHP_TIMEZONE','Fuseau horaire');
define('_SYSTEMPHP_TIMEZONETEXT','S&eacute;lectionnez votre fuseau horaire');
define('_SYSTEMPHP_TIMEUPDATEINT','Intervalle de mise &agrave; jour');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','Minutes entre les synchronisations de temps<br>300	recommand&eacute;, ou 0 pour d&eacute;sactiver');
define('_SYSTEMPHP_NTPSERVER','Serveur de temps NTP');
define('_SYSTEMPHP_NTPSERVERTEXT','Utilisez un espace pour s&eacute;parer plusieurs entr&eacute;es (une seule obligatoire). V&eacute;rifiez d\'avoir configur&eacute; un serveur DNS avant d\'utiliser un nom ici!');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','Informations syst&egrave;me');
define('_INDEXPHP_NAME','Nom');
define('_INDEXPHP_VERSION','Version');
define('_INDEXPHP_OSVERSION','Version de l\'OS');
define('_INDEXPHP_PLATFORM','Plateforme');
define('_INDEXPHP_DATE','Date');
define('_INDEXPHP_UPTIME','Uptime');
define('_INDEXPHP_LASTCHANGE','Derni&egrave;re modification de la configuration');
define('_INDEXPHP_MEMUSE','Utilisation de la m&eacute;moire');
define('_INDEXPHP_LOADAVERAGE','Charge moyenne');
define('_INDEXPHP_SHOWPROCESSING','D&eacute;tail des processus');
define('_INDEXPHP_DISKS_USE','Remplissage des disques');
define('_INDEXPHP_NODISK','Aucun disque configur&eacute;');


// Status Processes Module
// file: status_process.php
define('_STATSPROCPHP_NAME',_STATUS);
define('_STATSPROCPHP_NAME_DESCRIPTION','Processus');
define('_STATSPROCPHP_PROC_INF','D&eacute;tail des processus');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION',_MENULEFT_SYSSTATROUTES);
define('_SYSTEMROUTESPHP_MSG_CHANGED','La configuration a &eacute;t&eacute; modifi&eacute;e.<br>Vous devez appliquer les modifications.');
define('_SYSTEMROUTESPHP_INT','Interface');
define('_SYSTEMROUTESPHP_NET','R&eacute;seau');
define('_SYSTEMROUTESPHP_GW','Passerelle par d&eacute;faut');
define('_SYSTEMROUTESPHP_DESC',_DESC);
define('_SYSTEMROUTESPHP_EDITROUTE','Edition de la route');
define('_SYSTEMROUTESPHP_MSGCONFIRM','Etes-vous sur de vouloir supprimer cette route?');
define('_SYSTEMROUTESPHP_DELROUTE','Suppression de la route');
define('_SYSTEMROUTESPHP_ADDROUTE','Ajouter une route');

// Static Routes Edit Module
// file: system_routes_edit.php
define('_SYSTEMROUTESPHP_MSGVALID','Un r&eacute;seau de destination valide doit &ecirc;tre sp&eacute;cifi&eacute;.');
define('_SYSTEMROUTESPHP_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SYSTEMROUTESPHP_MSGVALIDGW','Une IP valide pour la passerelle par d&eacute;faut doit &ecirc;tre sp&eacute;cifi&eacute;e.');
define('_SYSTEMROUTESPHP_MSGROUTEEXIST','Une route pour cette destination existe d&eacute;ja.');
define('_SYSTEMROUTESPHP_CHOOSEINT','S&eacute;lectionner l\'interface correspondant &agrave; cette route.');
define('_SYSTEMROUTESPHP_DESTNET','R&eacute;seau de destination');
define('_SYSTEMROUTESPHP_DESTNETROUTE','R&eacute;seau de destination pour cette route statique');
define('_SYSTEMROUTESPHP_GWTONET','Routeur &agrave; utiliser pour atteindre la destination');
define('_SYSTEMROUTESPHP_DESCREF','Vous pouvez entrer une description ici.');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMFIRMWAREPHP_NAMEDESC',_MENULEFT_SYSFIRMWARE);
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','Le fichier image charg&eacute; n\'est pas valide pour cette plateforme');
define('_SYSTEMFIRMWAREPHP_OUTMEM','Echec du chargement de l\'image (pas assez de m&eacute;moire?)');
define('_SYSTEMFIRMWAREPHP_INVSIG','La signature num&eacute;rique de cette image est invalide.');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','Cette image n\'est pas sign&eacute;e num&eacute;riquement.');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','Une erreur s\'est produite lors de la v&eacute;rification de cette image.');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','Le test de l\'image a &eacute;chou&eacute;');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','Mise &agrave; jour en cours. FreeNAS red&eacute;marrera automatiquement.');
define('_SYSTEMFIRMWAREPHP_NOTSUP','Mise &agrave; jour du logiciel non support&eacute;e sur cette plateforme.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','Cela signifie que l\'image que vous avez charg&eacute;e ' .
		 	'n\'est pas une image officiellement support&eacute;e, ceci pouvant causer un comportement erratique ou provoquer' .
			'des failles de securite. N\'installez que des images venant de sources de confiance, et assurez-vous '.
			'que ces images n\'aient pas ete modifiees.<br><br>'.
			'Voulez-vous installer tout de meme cette image (a votre entiere responsabilite)?');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','Cliquez sur &quot;Activer le chargement de mise &agrave; jour&quot; ci-dessous, puis choisissez un fichier image '.$g['fullplatform'].'-*.img
			 &agrave; charger.<br>Cliquez sur &quot;Mettre &agrave; jour&quot; 
          pour lancer la mise &agrave; jour.');			
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','Activer le chargement de mise &agrave; jour');	
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','D&eacute;sactiver le chargement de mise &agrave; jour');		
define('_SYSTEMFIRMWAREPHP_FIRMFILE','Fichier image de mise &agrave; jour');	
define('_SYSTEMFIRMWAREPHP_UPFIRM','Mettre &agrave; jour');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','Vous devez red&eacute;marrer le systeme avant de pouvoir faire une mise a jour.');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','N\'ANNULEZ PAS la mise a jour une fois 
                    commenc&eacute;e. FreeNAS red&eacute;marrera automatiquement apr&egrave;s 
                    installation de la mise a jour.<br>La configuration sera maintenue.<br>
                    128 Mo de RAM sont n&eacute;cessaires pour la mise a jour.');


// System Advanced Module
// file: system_advanced.php
define('_SYSTEMADVANCEDPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMADVANCEDPHP_NAMEDESC',_MENULEFT_SYSADVANCED);
define('_SYSTEMADVANCEDPHP_MSGVALIDTCP','Le timeout TCP idle doit &ecirc;tre un entier.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERTKEY','Le certificat et la clef doivent &ecirc;tre sp&eacute;cifi&eacute;s ensemble.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERT','Ce certificat n\'est pas valide.');
define('_SYSTEMADVANCEDPHP_MSGVALIDKEY','La clef ne semble pas valide.');
define('_SYSTEMADVANCEDPHP_NOTE','Les options de cette page sont pr&eacute;vues pour des utilisateurs avanc&eacute;s uniquement et il n\'y a <strong>AUCUN</strong> support sur ces options.');
define('_SYSTEMADVANCEDPHP_WEBGUISSLKEY','Clef/certificat SSL de l\'interface web');
define('_SYSTEMADVANCEDPHP_WEBCERT','Certificat');
define('_SYSTEMADVANCEDPHP_WEBCERTTEXT','Copier un certificat au format X.509 PEM ici.');
define('_SYSTEMADVANCEDPHP_WEBKEY','Clef');
define('_SYSTEMADVANCEDPHP_WEBKEYTEXT','Copier une clef priv&eacute;e RSA au format PEM ici.');
define('_SYSTEMADVANCEDPHP_MISC','Divers');
define('_SYSTEMADVANCEDPHP_CON','Menu de la console');
define('_SYSTEMADVANCEDPHP_DISCON','D&eacute;sactiver le menu de la console');
define('_SYSTEMADVANCEDPHP_CONTEXT','Ce changement ne prend effet qu\'au prochain red&eacute;marrage.');
define('_SYSTEMADVANCEDPHP_FIRM','V&eacute;rification de nouvelle version logiciel');
define('_SYSTEMADVANCEDPHP_DISFIRM','D&eacute;sactivation de la v&eacute;rification de nouvelle version');
define('_SYSTEMADVANCEDPHP_FIRMTEXT','Ceci emp&egrave;che FreeNAS de v&eacute;rifier la disponibilit&eacute; de nouvelle version lorsque la page <a href=system_firmware.php>Syst&egrave;me: Logiciel</a> est affich&eacute;e.');
define('_SYSTEMADVANCEDPHP_TCPIDLE','Timeout inactivit&eacute; TCP');
define('_SYSTEMADVANCEDPHP_TCPIDLETEXT','Les connexions TCP inactives seront supprim&eacute;es de la table d\'&eacute;tat si aucun paquet n\'est recu durant le nombre de secondes sp&eacute;cifi&eacute;. N\'indiquez pas une valeur trop haute ou la table d\'&eacute;tat pourrait se remplir de connexions improprement termin&eacute;es. R&eacute;glage par defaut : 2.5 heures.');
define('_SYSTEMADVANCEDPHP_NAV','Navigation');
define('_SYSTEMADVANCEDPHP_NAVTEXT','Garder le menu diagnostics ouvert');
define('_SYSTEMADVANCEDPHP_BEEP','Bip Syst&egrave;me');
define('_SYSTEMADVANCEDPHP_DISBEEP','D&eacute;sactiver le bip du haut-parleur au d&eacute;marrage et &agrave; l\'arr&ecirc;t');
define('_SYSTEMADVANCEDPHP_SMART','D&eacute;mon S.M.A.R.T');
define('_SYSTEMADVANCEDPHP_ENSMART','Activer le demon S.M.A.R.T');
define('_SYSTEMADVANCEDPHP_SMARTTEXT','Surveiller les p&eacute;riph&eacute;riques S.M.A.R.T en enregistrant leur &eacute;tat dans le journal.');
define('_SYSTEMADVANCEDPHP_TUNING','Personnalisation');
define('_SYSTEMADVANCEDPHP_ENTUNING','Activer la personnalisation de quelques variables du noyau');
define('_SYSTEMADVANCEDPHP_ZEROCONF','ZeroConf');
define('_SYSTEMADVANCEDPHP_DISZEROCONF','D&eacute;sactiver l\'annonce du service ZeroConf');

// Interface assing:
// file: interfaces_assign.php
define('_INTASSIGNPHP_NAME','Interfaces');
define('_INTASSIGNPHP_NAMEDESC','Assigner les cartes r&eacute;seau');
define('_INTASSIGNPHP_MSGVALIDPORT','La carte ');
define('_INTASSIGNPHP_MSGVALIDPORTWASASSIG',' a &eacute;t&eacute; assign&eacute;e &agrave; ');
define('_INTASSIGNPHP_MSGVALIDINT',' interfaces:');
define('_INTASSIGNPHP_INT','Interface');
define('_INTASSIGNPHP_PORT','Carte r&eacute;seau');
define('_INTASSIGNPHP_DELINT','Supprimer interface');
define('_INTASSIGNPHP_ADDINT','Ajouter interface');
define('_INTASSIGNPHP_INTTEXT','Apr&egrave;s avoir cliqu&eacute; sur &quot;Enregistrer&quot;, vous devez red&eacute;marrer FreeNAS pour prendre en compte les changements. Vous devriez &eacute;galement v&eacute;rifier les points suivants avant de pouvoir vous reconnecter sur votre NAS: </span></p>
<ul>
  <li><span class=\"vexpl\">V&eacute;rifiez que l\'adresse IP de votre ordinateur soit dans le meme r&eacute;seau que la nouvelle adresse du NAS</span></li>
  <li><span class=\"vexpl\">acc&eacute;dez &agrave; l\'interface avec la nouvelle adresse IP</span></li>
</ul>');

// Interface LAN/OPT:
// file: interfaces_lan.php
// file: interfaces_opt.php
define('_INTLANPHP_NAME','Interfaces');
define('_INTLANPHP_NAMEDESC','LAN');
define('_INTPHP_MSGVALIDIP',_MSGVALIDIP);
define('_INTPHP_MSGVALIDMASK',_MSGVALIDNETBIT);
define('_INTPHP_MSGVALIDGW','Une passerelle valide doit &ecirc;tre sp&eacute;cifi&eacute;e.');
define('_INTPHP_MSGVALIDMTU','Une taille mtu valide doit &ecirc;tre sp&eacute;cifi&eacute;e.');
define('_INTPHP_IP','Addresse IP');
define('_INTPHP_GW','Passerelle');
define('_INTPHP_MTU','MTU');
define('_INTPHP_MTUTEXT','Le MTU standard est de 1500, utilisez 9000 pour les jumbo frames.');
define('_INTPHP_POLLING','Sondage de p&eacute;riph&eacute;rique');
define('_INTPHP_ENPOLLING','Activer le Sondage de p&eacute;riph&eacute;rique');
define('_INTPHP_POLLINGTEXT','Le sondage de p&eacute;riph&eacute;rique est une technique permettant au syst&egrave;me d\'interroger p&eacute;riodiquement les p&eacute;riph&eacute;riques r&eacute;seau pour v&eacute;rifier la pr&eacute;sence de nouvelles donn&eacute;es plutot que de se baser sur les interruptions. Ceci peut r&eacute;duire la charge CPU et donc augmenter le d&eacute;bit, au d&eacute;triment d\'un d&eacute;lai de transfert plus &eacute;lev&eacute; (les p&eacute;riph&eacute;riques sont sond&eacute;s 1000 fois par seconde). Toutes les cartes r&eacute;seau ne supportent pas cette technique; voyez le site de <a href=http://m0n0.ch/wall>m0n0wall</a> pour trouver la liste des cartes support&eacute;es.');
define('_INTPHP_SPEED','Vitesse');
define('_INTPHP_DUPLEX','Duplex');
define('_INTPHP_TEXT','Apr&egrave;s avoir cliqu&eacute; sur &quot;Enregistrer&quot;, Vous pourriez &eacute;galement avoir besoin de v&eacute;rifier les points suivants avant de pouvoir vous reconnecter sur votre NAS: 
                    <ul>
                      <li>changez l\'adresse IP de votre ordinateur</li>
                      <li>acc&eacute;dez &agrave; l\'interface avec la nouvelle adresse IP</li>
                    </ul>');
define('_INTPHP_ENOPT','Activer l\'interface OPT ');
define('_INTPHP_ENOPTINT',' ');
define('_INTPHP_DESC',_DESC);
define('_INTPHP_DESCTEXT','Entrez une description pour l\'interface ici.');
define('_INTPHP_STATIC','Configuration avec IP statique');
define('_INTPHP_DHCP','Configuration avec client DHCP');
define('_INTPHP_GENERAL','Configuration g&eacute;n&eacute;rale');
define('_INTPHP_DHCPHOSTNAME','Nom d\'hote');
define('_INTPHP_DHCPHOSTNAMETEXT','Le contenu de ce champ est envoy&eacute; en tant que nom d\'hote DHCP lors de la demande de bail DHCP.');
define('_INTPHP_DHCPCLIENTIDENTIFIER','Identificateur du client');
define('_INTPHP_DHCPCLIENTIDENTIFIERTEXT','La valeur de ce champ est envoy&eacute;&eacute;e en tant qu\'identificateur du client lors de la demande de bail DHCP.');
define('_INTPHP_CALCNETMASKBITS','Calculer le nombre de bits du masque de sous r&eacute;seau');

// Disks: Manage
// Common text
define('_DISKSPHP_NAME',_DISKS);
define('_DISKSPHP_MANAGE','G&eacute;rer');
define('_DISKSPHP_FORMAT','Formater');
define('_DISKSPHP_TOOLS','Tools');
define('_DISKSPHP_ISCSIINIT','initiateur iSCSI');
define('_DISKSPHP_DISK',_DISK);
define('_DISKSPHP_FILESYSTEM',_FILESYSTEM);

// file: disks_manage.php
define('_DISKSMANAGEPHP_NAMEDESC','Gestion');
define('_DISKSMANAGEPHP_MSGCHANGED','La liste des disques a chang&eacute;.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','D&eacute;lai de mise en veille');
define('_DISKSMANAGEPHP_FSUNKNOWN','Inconnu ou non format&eacute;');
define('_DISKSMANAGEPHP_EDIT','Modifier le disque');
define('_DISKSMANAGEPHP_DEL','Supprimer le  disque');
define('_DISKSMANAGEPHP_DELCONF','Voulez-vous vraiment supprimer ce disque? Tous les &eacute;l&eacute;ments l\'utilisant seront invalid&eacute;s (par ex. share)!');
define('_DISKSMANAGEPHP_ADD','Ajouter un disque');
define('_DISKSMANAGEPHP_NOTE','Premi&egrave;re &eacute;tape: Ajoutez votre disque dur &agrave; la liste des disques.');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','Ce disque existe d&eacute;j&agrave; dans la liste des disques.');
define('_DISKSMANAGEEDITPHP_UDMA','Mode UDMA');
define('_DISKSMANAGEEDITPHP_UDMATEXT','Vous pouvez forcer le mode UDMA si vous avez le message \"UDMA_ERROR.... LBA\" avec votre disque dur.');
define('_DISKSMANAGEEDITPHP_STANDBY','Delai de mise en veille du disque');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','Mets le disque en veille lorsque la dur&eacute;e sp&eacute;cifi&eacute;e depuis le dernier acc&egrave;s est &eacute;coul&eacute;e. <em>N\'activez PAS cette option pour les cartes Compact Flash.</em>');
define('_DISKSMANAGEEDITPHP_APM','Gestion d\'&eacute;nergie avanc&eacute;e');
define('_DISKSMANAGEEDITPHP_APMTEXT','Ceci vous permet de baisser la consommation d\'&eacute;nergie du disque, au d&eacute;triment de ses performances.<em>N\'activez PAS cette option pour les cartes Compact Flash.</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','Niveau acoustique');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','Ceci vous permet de contr&ocirc;ler le niveau sonore du disque en fonctionnement.<em>N\'activez PAS cette option pour les cartes Compact Flash.</em>');
define('_DISKSMANAGEEDITPHP_PREFS','Syst&egrave;me de fichier pr&eacute;existant');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','Ceci vous permet d\'indiquer le type de syst&egrave;me de fichier d\'un disque contenant des donn&eacute;es.<br>
                     <em>Laissez "unformated" pour des disques non format&eacute;s puis utilisez le menu Formater.</em>');

//file: disks_manage_init.php
define('_DISKSMANAGEINITPHP_NAMEDESC','Initialiser');
define('_DISKSMANAGEINITPHP_INITTEXT','D&eacute;tails de l\'initialisation:');
define('_DISKSMANAGEINITPHP_INITINFO','\"fdisk: Geom not found\"n\'est pas un message d\'erreur!\n');
define('_DISKSMANAGEINITPHP_TEXT','Ceci efface toutes les partitions, cr&eacute;&eacute; la partition num&eacute;ro 1 et formate le disque avec le syst&egrave;me de fichier sp&eacute;cifi&eacute;.');
define('_DISKSMANAGEINITPHP_FORMATDISC','Formater le disque');
define('_DISKSMANAGEINITPHP_FORMATDISCCONF','Voulez-vous vraiment formater ce disque? Toutes les donn&eacute;es seront perdues!');
define('_DISKSPHP_NOMBR','Ne pas effacer le MBR');
define('_DISKSPHP_NOMBRTEXT','Conserve le MBR intact (utile avec certains controleurs RAID)');

//file: disks_manage_tools.php
define('_DISKSMANAGETOOLS_NAMEDESC','Tools');
define('_DISKSMANAGETOOLS_COMMAND','Commande');
define('_DISKSMANAGETOOLS_SENDCMD','Valider la commande!');
define('_DISKSMANAGETOOLS_RAIDDISKNOTE','Use <a href="disks_raid_gmirror.php">RAID tools</a> for this disk!');
define('_DISKSMANAGETOOLS_CMDINFO','Command output:');
define('_DISKSMANAGETOOLS_MOUNTNOTE','The disk is currently mounted! The mount will be removed temporary to perform selected command.');

//file: disks_manage_iscsi.php
define('_DISKSMANAGEPHP_NAMEDESC','Gestion');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','Initiateur iSCSI');
define('_DISKSMANAGEISCSIPHP_TARGETIP','Adresse IP cible');
define('_DISKSMANAGEISCSIPHP_TARGETIPTEXT','Addresse IP de la cible iSCSI.');
define('_DISKSMANAGEISCSIPHP_TARGETNAME','Nom de la cible');
define('_DISKSMANAGEISCSIPHP_TARGETNAMETEXT','Nom de la cible.');

//Software RAID
// Comon value
define('_DISKSRAIDPHP_MANAGE','Gestion RAID');
define('_DISKSRAIDPHP_FORMAT','Formatage RAID');
define('_DISKSRAIDPHP_TOOLS','Outils');
define('_DISKSRAIDPHP_INFO','Information');
define('_DISKSRAIDPHP_VOLUME','Nom de volume');

// Software RAID common word
define('_DISKSRAIDPHP_GVINUM','Geom Vinum');
define('_DISKSRAIDPHP_GMIRROR','Geom Mirror');
define('_DISKSRAIDPHP_UNSTABLE','(instable)');

//file: disks_raid.php
define('_DISKSRAIDPHP_NAMEDESC','RAID');
define('_DISKSRAIDPHP_MSGCHANGED','La configuration RAID a &eacute;t&eacute; chang&eacute;e.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_DISKSRAIDPHP_TYPE','Type');
define('_DISKSRAIDPHP_EDITRAID','Modifier raid');
define('_DISKSRAIDPHP_DELRAID','Supprimer raid');
define('_DISKSRAIDPHP_DELCONF','Voulez-vous vraiment supprimer ce volume RAID? Tous les &eacute;l&eacute;ments l\'utilisant seront invalid&eacute;s (par ex. share)!');
define('_DISKSRAIDPHP_NOTE','Etape optionnelle: Configurer un disque RAID virtuel en utilisant votre<br><a href=disks_manage.php>disque configur&eacute; pr&eacute;c&eacute;demment</a>.<br>Attendez l\'apparition de l\'&eacute;tat "UP" avant de le formater et de le monter!.');

//file: disks_raid_edit.php
define('_DISKSRAIDEDITPHP_NAMEDESC',_EDIT);
define('_DISKSRAIDEDITPHP_MSGADDDISKFIRST','Vous devez d\'abord ajouter un disque.');
define('_DISKSRAIDEDITPHP_MSGVALIDNAME','Le nom de p&eacute;riph&eacute;rique doit contenir uniquement les caract&egrave;res a-z, A-Z, 0-9.');
define('_DISKSRAIDEDITPHP_MSGEXIST','Ce p&eacute;riph&eacute;rique existe d&eacute;j&agrave; dans la liste des volumes RAID.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID0','Un volume RAID0 doit &ecirc;tre compos&eacute; d\'au moins 2 disques.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID1','Un volume RAID1 doit &ecirc;tre compos&eacute; de 2 disques.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID5','Un volume RAID5 doit &ecirc;tre compos&eacute; d\'au moins 3 disques.');
define('_DISKSRAIDEDITPHP_MSGERROR','ERREUR Impossible de sauvegarder la configuration RAID.\n');
define('_DISKSRAIDEDITPHP_RAIDNAME','Nom du Raid');
define('_DISKSRAIDEDITPHP_RAID0','striping');
define('_DISKSRAIDEDITPHP_RAID1','mirroring');
define('_DISKSRAIDEDITPHP_RAID5','rotated block-interleaved parity');
define('_DISKSRAIDEDITPHP_MEMBERS','Membres de ce volume');


//file: disks_raid_info.php
define('_DISKSRAIDINFOPHP_TEXT','Information et &eacute;tat du RAID logiciel');

//file: disks_raid_init.php
define('_DISKSRAIDINITPHP_INFO','Formatage du disque en UFS:');
define('_DISKSRAIDINITPHP_TEXT','Le volume RAID sera format&eacute; avec le syst&egrave;me de fichiers Unix (UFS).');
define('_DISKSRAIDINITPHP_FORMAT','Formatage du disque en UFS!');

//file: disks_raid_tools.php
define('_DISKSRAIDTOOLSPHP_OBJNAME','Nom de l\'objet');
define('_DISKSRAIDTOOLSPHP_COMMAND','Commande');
define('_DISKSRAIDTOOLSPHP_INFO','GVINUM command output:');
define('_DISKSRAIDTOOLSPHP_SENDCMD','Valider la commande!');
define('_DISKSRAIDTOOLSPHP_TEXT','1. N\'utilisez ces actions sp&eacute;ciales qu\'&agrave; des fins de d&eacute;bogage!<br>2. Nul besoin d\'utiliser ce menu pour d&eacute;marrer un volume RAID (d&eacute;marrage automatique).');

//Disk Mount Point
// Comon value
define('_DISKSMOUNTPHP_NAME','Points de montage');
define('_DISKSMOUNTPHP_SHARENAME','Nom du partage');

// disks_mount.php
define('_DISKSMOUNTPHP_MSGCHANGED','La liste des points de montage a &eacute;t&eacute; modifi&eacute;e.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_DISKSMOUNTPHP_NOTE','Deuxi&egrave;me &eacute;tape de la configuration: D&eacute;clarer le syst&egrave;me de fichiers utilis&eacute; par votre <a href="disks_manage.php">disque configur&eacute; pr&eacute;c&eacute;demment');
define('_DISKSMOUNTPHP_DELCONF','Voulez-vous vraiment supprimer ce point de montage? Tous les &eacute;l&eacute;ments l\'utilisant seront invalid&eacute;s (par ex. share)!');
define('_DISKSMOUNTPHP_DEL','Supprimer le point de montage');
define('_DISKSMOUNTPHP_ADD','Ajouter un point de montage');

// disks_mount_edit.php
define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','Le nom de partage doit contenir uniquement les caract&egrave;res a-z, A-Z, 0-9, _ , -.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','La description contient des caract&egrave;res invalides.');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','Impossible de monter la partition syst&egrave;me 1, la partition de donn&eacute;es est la deuxi&egrave;me.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','Nom de partage dupliqu&eacute;.');
define('_DISKSMOUNTEDITPHP_TEXT','1. Vous ne pouvez pas monter la partition \'<?php echo htmlspecialchars($cfdevice);?>\' car elle contient le fichier de configuration<br>2. Le support NTFS de FreeBSD est bogu&eacute;.');

// Service: CIFS/SAMBA
// file: services_samba.php
define('_SRVCIFS_NAMEDESC','CIFS');
define('_SRVCIFS_MSGVALIDNETBIOSNAME','Le nom Netbios contient des caract&egrave;res invalides.');
define('_SRVCIFS_MSGVALIDWORKGOUP','Le groupe de travail contient des caract&egrave;res invalides.');
define('_SRVCIFS_MSGVALIDWINS','Le serveur WINS doit &ecirc;tre une adresse IP.');
define('_SRVCIFS_MSGVALIDSNDBUFF','La valeur du tampon d\'envoi doit &ecirc;tre un entier.');
define('_SRVCIFS_MSGVALIDRCVBUFF','La valeur du tampon de r&eacute;ception doit &ecirc;tre un entier.');
define('_SRVCIFS_SHARE','CIFS share');
define('_SRVCIFS_AUTH','Authentification');
define('_SRVCIFS_NETBIOSNAME','Nom Netbios');
define('_SRVCIFS_WORKGROUP','Groupe de travail');
define('_SRVCIFS_WORKGROUPTEXT','Groupe de travail auquel rattacher le NAS.');
define('_SRVCIFS_DESCTEXT','Description du serveur. Ce champ peut rester vide.');
define('_SRVCIFS_DOSCHARSET','Jeu de caract&egrave;res Dos');
define('_SRVCIFS_UNIXCHARSET','Jeu de caract&egrave;res Unix');
define('_SRVCIFS_LOGLEVEL','Niveau de journalisation');
define('_SRVCIFS_LOCALMASTER','Ma&icirc;tre explorateur local');
define('_SRVCIFS_LOCALMASTERTEXT','Permet &agrave; FreeNAS d\'essayer de s\'enregistrer comme ma&icirc;tre explorateur local.');
define('_SRVCIFS_TIMESRV','Serveur de temps');
define('_SRVCIFS_TIMESRVTEXT','FreeNAS s\'annonce commme un serveur de temps pour les clients Windows.');
define('_SRVCIFS_WINS','Serveur WINS');
define('_SRVCIFS_WINSTEXT','Adresse IP du serveur WINS.');
define('_SRVCIFS_RECYCLE','Corbeille');
define('_SRVCIFS_ENRECYCLE','Activer la corbeille');
define('_SRVCIFS_ENRECYCLETEXT','Ceci cr&eacute;&eacute;ra une corbeille pour les partages CIFS');
define('_SRVCIFS_HIDE','Partage &agrave; cacher');
define('_SRVCIFS_HIDETEXT','S&eacute;lectionnez le partage CIFS &agrave; cacher dans l\'explorateur du client.');
define('_SRVCIFS_SNBBUFF','Taille du tampon d\'envoi');
define('_SRVCIFS_SNBBUFFTEXT','Taille du tampon d\'envoi (16384 par d&eacute;faut).');
define('_SRVCIFS_RCVBUFF','Taille du tampon de r&eacute;ception');
define('_SRVCIFS_RCVBUFFTEXT','Taille du tampon de r&eacute;ception (16384 par d&eacute;faut).');

// Service: FTP
// file: services_ftp.php

define('_SRVFTP_NAMEDESC','FTP');
define('_SRVFTP_MSGVALIDPORT',_MSGVALIDTCPPORT);
define('_SRVFTP_MSGVALIDMAXCLIENT','Le nombre maximum de clients doit &ecirc;tre un entier.');
define('_SRVFTP_MSGVALIDMAXIP','Le nombre maximum de connexions par IP doit &ecirc;tre un entier.');
define('_SRVFTP_MSGVALIDIDLE','La dur&eacute;e maximum d\'inactivit&eacute; doit &ecirc;tre un entier.');
define('_SRVFTP_MSGVALIDPASVIP','L\'adresse pasv doit &ecirc;tre une adresse IP publique.');
define('_SRVFTP_MSGVALIDPASVMAX','La valeur pasv_max_port doit &ecirc;tre un num&eacute;ro de port valide.');
define('_SRVFTP_MSGVALIDPASVMIN','La valeur pasv_min_port doit &ecirc;tre un num&eacute;ro de port valide.');
define('_SRVFTP_MSGVALIDAUTH','Vous devez s&eacute;lectionner au minimum une authentification anonyme et/ou locale.');
define('_SRVFTP_FTPSERVER','Serveur FTP');
define('_SRVFTP_TCP',_TCPPORT);
define('_SRVFTP_MAXCLIENT','Nombre de clients');
define('_SRVFTP_MAXCLIENTTEXT','Nombre maximum de clients simultan&eacute;s.');
define('_SRVFTP_MAXIP','Max conn per ip');
define('_SRVFTP_MAXIPTEXT','Nombre maximum de connexions par IP.');
define('_SRVFTP_TIMEOUT','Timeout');
define('_SRVFTP_TIMEOUTTEXT','Dur&eacute;e maximum d\'inactivit&eacute; en secondes.');
define('_SRVFTP_ANONYMOUS','Login anonyme');
define('_SRVFTP_ENANONYMOUS','Accepter les connexions anonymes');
define('_SRVFTP_AUTH','Login local');
define('_SRVFTP_ENAUTH','Accepter les connexions des utilisateurs authentifi&eacute;s.');
define('_SRVFTP_FXP','FXP');
define('_SRVFTP_ENFXP','Active le protocole FXP.');
define('_SRVFTP_FXPTEXT','FXP permet les transferts entre serveurs sans passer par un client (non s&eacute;curis&eacute;).');
define('_SRVFTP_NATMODE','mode NAT');
define('_SRVFTP_ENNATMODE','Force le mode NAT');
define('_SRVFTP_NATMODETEXT','Activez ce mode si votre serveur FTP se trouve derriere un routeur NAT ne faisant pas office de proxy FTP applicatif');
define('_SRVFTP_PASSIVEIP','Addresse IP passive');
define('_SRVFTP_PASSIVEIPTEXT','Utilisez cette option pour outrepasser l\'adresse IP que le serveur FTP envoie en r&eacute;ponse aux commandes PASV.');
define('_SRVFTP_PASVMIN','pasv_min_port');
define('_SRVFTP_PASVMINTEXT','No de port minimum &agrave; allouer pour les connexions passives.');
define('_SRVFTP_PASVMAX','pasv_max_port');
define('_SRVFTP_PASVMAXTEXT','No de port maximum &agrave; allouer pour les connexions passives.');
define('_SRVFTP_BANNER','Banni&egrave;re');
define('_SRVFTP_BANNERTEXT','Banni&egrave;re d\'accueil affich&eacute;e par le serveur FTP au d&eacute;but d\'une connexion.');

// Service: NFS
// file: services_nfs.php

define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','Un r&eacute;seau valide doit &ecirc;tre sp&eacute;cifi&eacute;.');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','Serveur NFS');
define('_SRVNFS_MAPALLROOT','Associer tous les utilisateurs &agrave; root');
define('_SRVNFS_MAPALLROOTTEXT','Tous les utilisateurs auront les privil&egrave;ges root.');
define('_SRVNFS_AUTHNET','R&eacute;seau autoris&eacute;');
define('_SRVNFS_AUTHNETTEXT','R&eacute;seau autoris&eacute; &agrave; acc&eacute;der au partage NFS');
define('_SRVNFS_TEXT','Le nom de partage export&eacute; est du type : /mnt/partage');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','Serveur');
define('_SRVRYNC_CLIENT','Client');
define('_SRVRYNC_LOCAL','Local');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','D&eacute;mon Rsync');
define('_SRVRYNCD_READONLY','Lecture seule');
define('_SRVRYNCD_MAPUSER','Associer &agrave; l\'utilisateur');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_TCPORTTEXT','Port TCP alternatif.');
define('_SRVRYNCD_MAXCON','Maximum de connexions');
define('_SRVRYNCD_MAXCONTEXT','Nombre maximum de connexions simultan&eacute;es. 0 par d&eacute;faut (illimit&eacute;).');
define('_SRVRYNCD_MOTD','MOTD');
define('_SRVRYNCD_MOTDTEXT','Message du jour.');

// file: services_rsyncd_client.php
define('_SRVRYNCC_MSGMPFIRST','Vous devez d\'abord choisir un point de montage.');
define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
define('_SRVRYNCC_RSYNCC','Synchronisation client Rsync');
define('_SRVRYNCC_REMOTESERVER','Serveur RSYNC distant');
define('_SRVRYNCC_REMOTEID','Adresse IP du Serveur RSYNC distant');
define('_SRVRYNCC_OPTIONS','Options RSYNC');
define('_SRVRYNCC_OPTDEL','Supprimer les fichiers qui n\'existent pas sur la machine source.');
define('_SRVRYNCC_SHARES','Partages &agrave; synchroniser');
define('_SRVRYNCC_SHARESTEXT','Un partage du m&ecirc;me nom doit exister sur le serveur distant.');
define('_SRVRYNCC_TIME','Heure de Synchronisation');
define('_SRVRYNCC_TEXT',_MSGSCHED_TEXT);

// file: services_rsyncd_local.php
define('_SRVRYNCL_MSGVALID','Vous pouvez avoir le m&ecirc;me point de montage comme source et destination!');
define('_SRVRYNCC_RSYNCL','Synchronisation Rsync locale');
define('_SRVRYNCL_SOURCE','Source');
define('_SRVRYNCL_SOURCETEXT','Point de montage source.');
define('_SRVRYNCL_DESTINATION','Destination');
define('_SRVRYNCL_DESTINATIONTEXT','Point de montage destination (disque de sauvegarde).');


// Service: SSHD
// file: services_sshd.php
define('_SRVSSHD_NAMEDESC','SSHD');
define('_SRVSSHD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVSSHD_MSGVALIDKEY','Cette clef semble invalide.');
define('_SRVSSHD_SSHD','D&eacute;mon SSH');
define('_SRVSSHD_READONLY','Lecture seule');
define('_SRVSSHD_TCPORT',_TCPPORT);
define('_SRVSSHD_TCPORTTEXT',_SRVRYNCD_TCPORTTEXT);
define('_SRVSSHD_PERMITROOTLOGIN','Autoriser le login root');
define('_SRVSSHD_PERMITROOTLOGINTEXT','Indique s\'il est permis de se connecter directement en superutilisateur (root).');
define('_SRVSSHD_KEY','Clef SSH');
define('_SRVSSHD_PRIVATEKEY','Clef priv&eacute;e');
define('_SRVSSHD_PRIVATEKEYTEXT','Collez une clef priv&eacute;e DSA au format PEM ici.');

// Service: Unison
// file: services_unison.php
define('_SRVUNSN_NAMEDESC','Unison');
define('_SRVUNSN_MSGVALIDWRKDIR','Cette combinaison partage/r&eacute;pertoire de travail n\'existe pas.');
define('_SRVUNSN_UNISON','Synchronisation de fichiers Unison');
define('_SRVUNSN_SHARE','Partage');
define('_SRVUNSN_SHAREDESC','Vous pourriez avoir besoin de suffisamment d\'espace pour dupliquer tous les fichiers devant etre synchronis&eacute;s');
define('_SRVUNSN_WORKDIR','R&eacute;pertoire de travail');
define('_SRVUNSN_WORKDIRDESC','L\'endroit ou seront stock&eacute;s les fichiers');
define('_SRVUNSN_MAKEDIR','Cr&eacute;er');
define('_SRVUNSN_MAKEDIRLABEL','Cr&eacute;er le r&eacute;pertoire de travail s\'il n\' existe pas');
define('_SRVUNSN_NOTESSH','<a href="/services_sshd.php">SSHD</a> doit etre activ&eacute; pour qu\' Unison fonctionne, et l\'<a href="/access_users.php">utilisateur</a> doit avoir un acc&egrave;s complet au shell.');

// Service: AFP
// file: services_afp.php
define('_SRVAFP_NAMEDESC','AFP');
define('_SRVAFP_AFPNAME','Nom du serveur AFP');
define('_SRVAFP_MSGVALIDAUTH','Vous devez s&eacute;lectionner au moins une m&eacute;thode d\'authentification.');
define('_SRVAFP_AFPSRV','Serveur AFP');
define('_SRVAFP_AUTH','Authentification');
define('_SRVAFP_ENABLEGUEST','Activer l\'acc&egrave;s invit&eacute;.');
define('_SRVAFP_ENABLELOCAL','Activer l\'authentification locale des utilisateurs.');

// Access
// common words
define('_ACCESS_NAME','Acc&egrave;s');
define('_ACCESS_USER','Utilisateur');
define('_ACCESS_USERS','Utilisateurs');
define('_ACCESS_GROUP','Groupe');
define('_ACCESS_GROUPS','Groupes');
define('_ACCESS_FULLNAME','Nom complet');

// file: access_users.php

define('_ACCESSUSERS_MSGCHANGED','La liste des utilisateurs a &eacute;t&eacute; modifi&eacute;e.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_ACCESSUSERS_EDITUSER','modifier l\'utilisateur');
define('_ACCESSUSERS_DELUSER','supprimer l\'utilisateur');
define('_ACCESSUSERS_CONFDEL','Voulez-vous vraiment supprimer cet utilisateur?!');
define('_ACCESSUSERS_ADDUSER','ajouter un utilisateur');

// file: access_users_edit.php

define('_ACCESSUSERSEDIT_MSGGROUPFIRST','Vous devez d\'abord cr&eacute;er un groupe.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGIN','L\'identifiant contient un caract&egrave;re invalide.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED','L\'identifiant est un identifiant r&eacute;serv&eacute; du syst&egrave;me.');
define('_ACCESSUSERSEDIT_MSGVALIDFULLNAME','Le nom complet contient un caract&egrave;re invalide.');
define('_ACCESSUSERSEDIT_MSGVALIDDUPLICATE','Cet utilisateur existe d&eacute;j&agrave;.');
define('_ACCESSUSERSEDIT_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSUSERSEDIT_LOGIN',_LOGIN);
define('_ACCESSUSERSEDIT_LOGINTEXT','Identifiant unique d\'un utilisateur.');
define('_ACCESSUSERSEDIT_FULLNAME',_ACCESS_FULLNAME);
define('_ACCESSUSERSEDIT_FULLNAMETEXT','Nom complet de l\'utilisateur.');
define('_ACCESSUSERSEDIT_PASSWORD','Mot de passe');
define('_ACCESSUSERSEDIT_PASSWORDTEXT','Mot de passe de l\'utilisateur.');
define('_ACCESSUSERSEDIT_PASSWORDCONF','Confirmez le mot de passe');
define('_ACCESSUSERSEDIT_PASSWORDCONFTEXT','Confirmez le mot de passe.');
define('_ACCESSUSERSEDIT_GROUPMEMBER','Membre du groupe');
define('_ACCESSUSERSEDIT_FULLSHELL','Shell complet');
define('_ACCESSUSERSEDIT_FULLSHELLTEXT','Donne l\'acc&egrave;s complet au shell');

// file: access_users_groups.php

define('_ACCESSGROUPS_MSGCHANGED','La liste des groupes a &eacute;t&eacute; modifi&eacute;e.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_ACCESSGROUPS_DESC',_DESC);
define('_ACCESSGROUPS_GROUPEDIT','modifier le groupe');
define('_ACCESSGROUPS_GROUPDEL','supprimer le groupe');
define('_ACCESSGROUPS_GROUPDELCONF','Voulez-vous vraiment supprimer ce groupe?!');
define('_ACCESSGROUPS_GROUPADD','ajouter un groupe');

// file: access_users_groups_edit.php

define('_ACCESSGROUPSEDIT_MSGVALIDNAME','Le nom du groupe contient un caract&egrave;re invalide.');
define('_ACCESSGROUPSEDIT_MSGVALIDDESC','La description du groupe contient un caract&egrave;re invalide.');
define('_ACCESSGROUPSEDIT_MSGVALIDDUPLICATE','Ce groupe existe d&eacute;j&agrave;.');
define('_ACCESSGROUPSEDIT_NAME','Nom');
define('_ACCESSGROUPSEDIT_NAMETEXT','Nom du groupe.');
define('_ACCESSGROUPSEDIT_DESC',_DESC);
define('_ACCESSGROUPSEDIT_DESCTEXT','Description du groupe.');

// file: access_ad.php

define('_ACCESSAD_NAME','Active Directory');
define('_ACCESSAD_MSGVALIDIP',_MSGVALIDIP);
define('_ACCESSAD_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSAD_ADNAME','Nom du serveur AD');
define('_ACCESSAD_ADNAMETEXT','Nom du serveur Active Directory ou du Controleur Principal de Domaine NT4.');
define('_ACCESSAD_IP','IP du serveur AD');
define('_ACCESSAD_IPTEXT','Adresse IP du serveur Active Directory.');
define('_ACCESSAD_DOMAIN','Nom de domaine');
define('_ACCESSAD_DOMAINTEXT','Nom du domaine &agrave; l\'ancien format (NETBIOS).');
define('_ACCESSAD_ADMINAME','Nom de l\'administrateur');
define('_ACCESSAD_ADMINNAMETEXT','Identifiant d\'un compte administrateur du domaine.');
define('_ACCESSAD_ADMINPASS','Mot de passe admin');
define('_ACCESSAD_ADMINPASSTEXT','Mot de passe d\'un compte administrateur du domaine, &agrave; entrer 2 fois.');

// Diagnostics

define('_DIAG_NAME','Diagnostics');

// file: shutdown.php

define('_SHUT_NAME',_MENULEFT_CMDSHUTDOWNSYS);
define('_SHUT_MSGSHUT','Le syst&egrave;me est en cours d\'arr&ecirc;t. Ceci peut prendre jusqu\'&agrave;  une minute.');
define('_SHUT_CONF','Etes-vous s&ucirc;r de vouloir arr&ecirc;ter le NAS?');

// file: shutdown_sched.php

define('_SHUTSCHED_NAME','Arr&ecirc;t programm&eacute;');
define('_SHUTSCHED_TIME','Heure d\'arr&ecirc;t');
define('_SHUTSCHED_TEXT',_MSGSCHED_TEXT);

// file: diag_edit.php
define('_DIAGEDITPHP_NAMEDESC','Editer un fichier');
define('_DIAGEDITPHP_FILEPATH','Chemin du fichier');
define('_DIAGEDITPHP_SAVEMSG','Sauvegarder le fichier dans');
define('_DIAGEDITPHP_FILENOTFOUND','Fichier non trouv&eacute;');
define('_DIAGEDITPHP_ROWS','Lignes');
define('_DIAGEDITPHP_COLS','Colonnes');
define('_DIAGEDITPHP_HIGHLIGHTING','Surlignage');
define('_DIAGEDITPHP_HIGHLIGHTINGENABLED','Activ&eacute;');
define('_DIAGEDITPHP_HIGHLIGHTINGDISABLED','D&eacute;sactiv&eacute;');

// file: exec.php
define('_DIAGEXECPHP_NAMEDESC','Ex&eacute;cuter une commande');

// file: reboot.php
define('_DIAGREBOOT_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGREBOOT_NAMEDESC',_MENULEFT_CMDREBOOTSYS);
define('_DIAGREBOOT_REBOOTMSG','The system is rebooting now. This may take one minute.');
define('_DIAGREBOOT_REBOOTCONF','Are you sure you want to reboot the system?');

// file: license.php
define('_LICENSE_NAME','Licence');

// file: status_wireless.php
define('_STATSWIRELESS_NAME',_MENULEFT_STATUS);
define('_STATSWIRELESS_NAMEDESC','Sans-fil');

// file: status_interfaces.php
define('_STATSINTERFACES_NAME',_MENULEFT_STATUS);
define('_STATSINTERFACES_NAMEDESC',_INTERFACES);

// file: status_graph_cpu.php
define('_STATSGRAPHCPU_NAME',_MENULEFT_STATUS);
define('_STATSGRAPHCPU_NAMEDESC','Charge CPU');

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
define('_INTVLANEDIT_EDITVLAN','Modifier le VLAN');

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
define('_STATSGRAPH_NAMEDESC','Graphe de trafic');

// file: graph.php
define('_GRAPH_ERRORGETIFDATA','Impossible de collecter des donn&eacute;es sur cette interface');
define('_GRAPH_IN','Entr&eacute;e');
define('_GRAPH_OUT','Sortie');
define('_GRAPH_SWITCHTO','Basculer vers');
define('_GRAPH_AUTOSCALE','Echelle auto');
define('_GRAPH_AUTOSCALE_FOLLOW','Suivre');
define('_GRAPH_AUTOSCALE_UP','Remonter');
define('_GRAPH_SHOWLAST','Graphe en dernier');
define('_GRAPH_COLLECTDATA','Collecte des premi&egrave;res donn&eacute;es, veuillez patienter...');
?>
