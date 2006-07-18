<?php 
/*
	lang-French.php
	fait partie de FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.
	
	Traduction par Régis Caubet (caubet.r@gmail.com)
	
	Basé sur m0n0wall (http://m0n0.ch/wall)
	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
	All rights reserved.
	
	La redistribution et l'utilisation sous forme binaire ou de code source, 
	avec ou sans modification, est autorisée à condition que les conditions suivantes soient respectées: 
	
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
define('_YEAR', 'année');
define('_YEARS', 'années');

// common words
define('_ALL','tout');
define('_AND','et');
define('_BY','par');
define('_DOWN','bas');
define('_NO','Non');
define('_OF','off');
define('_OK','OK');
define('_ON','on');
define('_TO','à');
define('_THE', 'le');
define('_UP','haut');
define('_URL', 'URL');
define('_YES','Oui');

// common actions
define('_ACTIVATE','Activer');
define('_ADD','Ajouter');
define('_CANCEL', 'Annuler');
define('_COMMIT', 'Update');
define('_CREATE', 'Créer');
define('_DEACTIVATE','Desactiver');
define('_DELETE','Supprimer');
define('_EDIT','Editer');
define('_ENABLE','Activer');
define('_HELP', 'Aide');
define('_IGNORE','Ignorer');
define('_LOGIN','Connexion');
define('_LOGOUT','Déconnexion');
define('_MODIFY','Modifier');
define('_NEW','Nouveau');
define('_SEARCH', 'Chercher');
define('_SAVE', 'Enregister');
define('_SUBMIT','Submit');
define('_UPDATE', 'Mise à jour');
define('_VIEW', 'Visualiser');
define('_APPLY', 'Appliquer les changements');
define('_WARNING', 'Attention');

//FreeNAS common words

define('_SIZE', 'Taille');
define('_STATUS', 'Etat');
define('_TYPE', 'Type');
define('_CONFIGURING', 'en cours de configuration');
define('_DISK', 'Disque');
define('_DISKS', 'Disques');
define('_PARTITION', 'Partition');
define('_FILESYSTEM', 'Système de fichier');
define('_DESC', 'Description');
define('_SOFTRAID', 'RAID logiciel');
define('_SERVICE','Service');
define('_SERVICES','Services');
define('_TCPPORT','Port TCP');

// common messages
define('_MESSAGES_SUCCESS', 'Les modifications ont été appliquées avec succès.');
define('_MESSAGES_SUCCESSWITHREBOOT', 'Les modifications ont été enregistrées. Vous devez <a href=reboot.php>redémarrer</a> votre serveur NAS pour que les modifications prennent effet.');
define('_MESSAGES_ERROR', 'Erreur: Les modifications ne peuvent être appliquées (code d\'erreur '.$ok.').');

define('_MSGVALIDIP','Une adresse IP valide doit être spécifiée.');
define('_MSGVALIDNETBIT','Un nombre de bit valide doit être spécifié.');
define('_MSGVALIDTCPPORT','Le port TCP doit être un nombre valide compris entre 1024 et 65564.');
define('_MSGVALIDPASSWORD','Les mots de passe ne correspondent pas.');

define('_MSGSCHED_TEXT','Note: Ctrl-clic (ou command-clic sur Mac) pour sélectionner et désélectionner minutes, heures, jours et mois.');

// states
define('_ACTIVE','Actif');
define('_INACTIVE','Inactif');

// Webgui layout
define('_MENULEFT_SYSTEM','Système');
define('_MENULEFT_SYSGENSETUP','Configuration globale');
define('_MENULEFT_SYSSTATROUTES','Routes Statiques');
define('_MENULEFT_SYSFIRMWARE','Logiciel');
define('_MENULEFT_SYSADVANCED','Configuration avancée');
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
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','Accès');
define('_MENULEFT_ACUSERGROUPS','Utilisateurs et groupes');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS',_STATUS);
define('_MENULEFT_STSYSTEM','Système');
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
define('_MENULEFT_CMDBACKUPRESTORE','Sauvegarde/Restauration');
define('_MENULEFT_CMDFACTORYDEF','Reglages de depart');
define('_MENULEFT_CMDREBOOTSYS','Redémarrage du NAS');
define('_MENULEFT_CMDSHUTDOWNSYS','Arrêt du NAS');


// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION',_MENULEFT_SYSGENSETUP);
define('_SYSTEMPHP_HOSTNAME','Nom système');
define('_SYSTEMPHP_HOSTNAMETEXT','Nom du serveur NAS, sans préciser le nom de domaine<br>ex. <em>nas');
define('_SYSTEMPHP_DOMAIN','Domaine');
define('_SYSTEMPHP_DOMAINTEXT','ex: <em>macompagnie.fr</em>');
define('_SYSTEMPHP_DNS','Serveurs DNS');
define('_SYSTEMPHP_DNSTEXT','Adresse IP des serveurs DNS<br>');
define('_SYSTEMPHP_USERNAME','Nom utilisateur');
define('_SYSTEMPHP_USERNAMETEXT','Nom utilisateur pour accéder à l\'interface WEB.');
define('_SYSTEMPHP_PASSWORD','Mot de passe');
define('_SYSTEMPHP_PASSWORDTEXT','Mot de passe d\'accès à l\'interface WEB.<br>N\'utilisez pas le caractère :');
define('_SYSTEMPHP_WEBGUIPROTOCOL','Protocole de l\'interface WEB');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','');
define('_SYSTEMPHP_WEBGUIPORT','port TCP de l\'interface WEB');
define('_SYSTEMPHP_WEBGUIPORTTEXT','Entrez le numéro d\'un port si vous désirez changer celui par défaut (80 pour HTTP, 443 pour HTTPS).');
define('_SYSTEMPHP_LANGUAGE','Langage');
define('_SYSTEMPHP_LANGUAGETEXT','Choisissez la langue utilisée par le système');
define('_SYSTEMPHP_TIMEZONE','Fuseau horaire');
define('_SYSTEMPHP_TIMEZONETEXT','Sélectionner un lieu proche de la votre');
define('_SYSTEMPHP_TIMEUPDATEINT','Intervalle de mise à jour horaire');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','Minutes entre les synchronisations de temps; 300	recommandé, ou 0 pour désactiver');
define('_SYSTEMPHP_NTPSERVER','Serveur de temps NTP');
define('_SYSTEMPHP_NTPSERVERTEXT','Utilisez un espace pour séparer plusieurs entrées (une seule obligatoire). Vérifiez d\'avoir configuré un serveur DNS avant d\'utiliser un nom ici!');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','Informations système');
define('_INDEXPHP_NAME','Nom');
define('_INDEXPHP_VERSION','Version');
define('_INDEXPHP_OSVERSION','Version de l\'OS');
define('_INDEXPHP_PLATFORM','Plateforme');
define('_INDEXPHP_DATE','Date');
define('_INDEXPHP_UPTIME','Uptime');
define('_INDEXPHP_LASTCHANGE','Dernière modification de la configuration');
define('_INDEXPHP_MEMUSE','Utilisation de la mémoire');
define('_INDEXPHP_LOADAVERAGE','Charge moyenne');
define('_INDEXPHP_SHOWPROCESSING','Détail des processus');

// Status Processes Module
// file: status_processes.php
define('_STATSPROCPHP_NAME',_STATUS);
define('_STATSPROCPHP_NAME_DESCRIPTION','Processus');
define('_STATSPROCPHP_PROC_INF','Détail des processus');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION',_MENULEFT_SYSSTATROUTES);
define('_SYSTEMROUTESPHP_MSG_CHANGED','La configuration a été modifiée.<br>Vous devez appliquer les modifications.');
define('_SYSTEMROUTESPHP_INT','Interface');
define('_SYSTEMROUTESPHP_NET','Réseau');
define('_SYSTEMROUTESPHP_GW','Passerelle par défaut');
define('_SYSTEMROUTESPHP_DESC',_DESC);
define('_SYSTEMROUTESPHP_EDITROUTE','Edition de la route');
define('_SYSTEMROUTESPHP_MSGCONFIRM','Etes-vous sur de vouloir supprimer cette route?');
define('_SYSTEMROUTESPHP_DELROUTE','Suppression de la route');
define('_SYSTEMROUTESPHP_ADDROUTE','Ajouter une route');

// Static Routes Edit Module
// file: system_routes_edit.php
define('_SYSTEMROUTESPHP_MSGVALID','Un réseau de destination valide doit être spécifié.');
define('_SYSTEMROUTESPHP_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SYSTEMROUTESPHP_MSGVALIDGW','Une IP valide pour la passerelle par défaut doit être spécifiée.');
define('_SYSTEMROUTESPHP_MSGROUTEEXIST','Une route pour cette destination existe déja.');
define('_SYSTEMROUTESPHP_CHOOSEINT','Sélectionner l\'interface correspondant à cette route.');
define('_SYSTEMROUTESPHP_DESTNET','Réseau de destination');
define('_SYSTEMROUTESPHP_DESTNETROUTE','Réseau de destination pour cette route statique');
define('_SYSTEMROUTESPHP_GWTONET','Routeur à utiliser pour atteindre la destination');
define('_SYSTEMROUTESPHP_DESCREF','Vous pouvez entrer une description ici.');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMFIRMWAREPHP_NAMEDESC',_MENULEFT_SYSFIRMWARE);
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','Le fichier image chargé n\'est pas valide pour cette plateforme');
define('_SYSTEMFIRMWAREPHP_OUTMEM','Echec du chargement de l\'image (pas assez de mémoire?)');
define('_SYSTEMFIRMWAREPHP_INVSIG','La signature numérique de cette image est invalide.');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','Cette image n\'est pas signée numériquement.');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','Une erreur s\'est produite lors de la vérification de cette image.');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','Le test de l\'image a échoué');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','Mise à jour en cours. FreeNAS redémarrera automatiquement.');
define('_SYSTEMFIRMWAREPHP_NOTSUP','Mise à jour du logiciel non supportée sur cette plateforme.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','Cela signifie que l\'image que vous avez chargée ' .
		 	'n\'est pas une image officiellement supportée, ceci pouvant causer un comportement erratique ou provoquer' .
			'des failles de securite. N\'installez que des images venant de sources de confiance, et assurez-vous '.
			'que ces images n\'aient pas ete modifiees.<br><br>'.
			'Voulez-vous installer tout de meme cette image (a votre entiere responsabilite)?');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','Cliquez sur &quot;Activer le chargement de mise à jour&quot; ci-dessous, puis choisissez un fichier image '.$g['fullplatform'].'-*.img
			 à charger.<br>Cliquez sur &quot;Mettre à jour&quot; 
          pour lancer la mise à jour.');			
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','Activer le chargement de mise à jour');	
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','Désactiver le chargement de mise à jour');		
define('_SYSTEMFIRMWAREPHP_FIRMFILE','Fichier image de mise à jour');	
define('_SYSTEMFIRMWAREPHP_UPFIRM','Mettre à jour');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','Vous devez redémarrer le systeme avant de pouvoir faire une mise a jour.');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','N\'ANNULEZ PAS la mise a jour une fois 
                    commencée. FreeNAS redémarrera automatiquement après 
                    installation de la mise a jour. La configuration sera maintenue.<br>
                    80 Mo de RAM sont nécessaires pour la mise a jour.');


// System Advanced Module
// file: system_advanced.php
define('_SYSTEMADVANCEDPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMADVANCEDPHP_NAMEDESC',_MENULEFT_SYSADVANCED);
define('_SYSTEMADVANCEDPHP_MSGVALIDTCP','Le timeout TCP idle doit être un entier.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERTKEY','Le certificat et la clef doivent être spécifiés ensemble.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERT','Ce certificat n\'est pas valide.');
define('_SYSTEMADVANCEDPHP_MSGVALIDKEY','La clef ne semble pas valide.');
define('_SYSTEMADVANCEDPHP_NOTE','<strong>Note: </strong></span>Les options de cette page sont prévues pour des utilisateurs avancés uniquement et il n\'y a <strong>AUCUN</strong> support sur ces options.');
define('_SYSTEMADVANCEDPHP_WEBGUISSLKEY','Clef/certificat SSL de l\'interface web');
define('_SYSTEMADVANCEDPHP_WEBCERT','Certificat');
define('_SYSTEMADVANCEDPHP_WEBCERTTEXT','Copier un certificat au format X.509 PEM ici.');
define('_SYSTEMADVANCEDPHP_WEBKEY','Clef');
define('_SYSTEMADVANCEDPHP_WEBKEYTEXT','Copier une clef privée RSA au format PEM ici.');
define('_SYSTEMADVANCEDPHP_MISC','Divers');
define('_SYSTEMADVANCEDPHP_CON','Menu de la console');
define('_SYSTEMADVANCEDPHP_DISCON','Désactiver le menu de la console');
define('_SYSTEMADVANCEDPHP_CONTEXT','Ce changement ne prend effet qu\'au prochain redémarrage.');
define('_SYSTEMADVANCEDPHP_FIRM','Vérification de nouvelle version logiciel');
define('_SYSTEMADVANCEDPHP_DISFIRM','Désactivation de la vérification de nouvelle version');
define('_SYSTEMADVANCEDPHP_FIRMTEXT','Ceci empèche FreeNAS de vérifier la disponibilité de nouvelle version lorsque la page <a href=system_firmware.php>Système: Logiciel</a> est affichée.');
define('_SYSTEMADVANCEDPHP_TCPIDLE','Timeout inactivité TCP');
define('_SYSTEMADVANCEDPHP_TCPIDLETEXT','Les connexions TCP inactives seront supprimées de la table d\'état si aucun paquet n\'est recu durant le nombre de secondes spécifié. N\'indiquez pas une valeur trop haute ou la table d\'état pourrait se remplir de connexions improprement terminées. Réglage par defaut : 2.5 heures.');
define('_SYSTEMADVANCEDPHP_NAV','Navigation');
define('_SYSTEMADVANCEDPHP_NAVTEXT','Garder le menu diagnostics ouvert');
define('_SYSTEMADVANCEDPHP_BEEP','Bip Systeme');
define('_SYSTEMADVANCEDPHP_DISBEEP','Désactiver le bip du haut-parleur au démarrage et à l\'arrêt');
define('_SYSTEMADVANCEDPHP_SMART','Demon S.M.A.R.T');
define('_SYSTEMADVANCEDPHP_ENSMART','Activer le demon S.M.A.R.T');
define('_SYSTEMADVANCEDPHP_SMARTTEXT','Surveiller les périphériques S.M.A.R.T en enregistrant leur état dans le journal.');
define('_SYSTEMADVANCEDPHP_TUNING','Personnalisation');
define('_SYSTEMADVANCEDPHP_ENTUNING','Activer la personnalisation de quelques variables du noyau');
define('_SYSTEMADVANCEDPHP_ZEROCONF','ZeroConf');
define('_SYSTEMADVANCEDPHP_DISZEROCONF','Désactiver l\'annonce du service ZeroConf');

// Interface assing:
// file: interfaces_assign.php
define('_INTASSIGNPHP_NAME','Interfaces');
define('_INTASSIGNPHP_NAMEDESC','Assigner les cartes réseau');
define('_INTASSIGNPHP_MSGVALIDPORT','La carte ');
define('_INTASSIGNPHP_MSGVALIDPORTWASASSIG',' a été assignée à ');
define('_INTASSIGNPHP_MSGVALIDINT',' interfaces:');
define('_INTASSIGNPHP_INT','Interface');
define('_INTASSIGNPHP_PORT','Carte réseau');
define('_INTASSIGNPHP_DELINT','supprimer interface');
define('_INTASSIGNPHP_ADDINT','ajouter interface');
define('_INTASSIGNPHP_INTTEXT','Après avoir cliqué sur &quot;Enregistrer&quot;, vous devez redémarrer FreeNAS pour prendre en compte les changements. Vous devriez également vérifier les points suivants avant de pouvoir vous reconnecter sur votre NAS: </span></p>
<ul>
  <li><span class=\"vexpl\">changez l\'adresse IP de votre ordinateur</span></li>
  <li><span class=\"vexpl\">accédez à l\'interface avec la nouvelle adresse IP</span></li>
</ul>');

// Interface LAN/OPT:
// file: interfaces_lan.php
// file: interfaces_opt.php
define('_INTLANPHP_NAME','Interfaces');
define('_INTLANPHP_NAMEDESC','LAN');
define('_INTPHP_MSGVALIDIP',_MSGVALIDIP);
define('_INTPHP_MSGVALIDMASK',_MSGVALIDNETBIT);
define('_INTPHP_MSGVALIDGW','Une passerelle valide doit être spécifiée.');
define('_INTPHP_MSGVALIDMTU','Une taille mtu valide doit être spécifiée.');
define('_INTPHP_IP','Addresse IP');
define('_INTPHP_GW','Passerelle');
define('_INTPHP_MTU','MTU');
define('_INTPHP_MTUTEXT','Le MTU standard est de 1500, utilisez 9000 pour les jumbo frames.');
define('_INTPHP_POLLING','Device polling');
define('_INTPHP_ENPOLLING','Enable device polling');
define('_INTPHP_POLLINGTEXT','Device polling is a technique that lets the system periodically poll network devices for new
					data instead of relying on interrupts. This can reduce CPU load and therefore increase
					throughput, at the expense of a slightly higher forwarding delay (the devices are polled 1000 times
					per second). Not all NICs support polling; see the m0n0wall homepage for a list of supported cards.');
define('_INTPHP_SPEED','Vitesse');
define('_INTPHP_DUPLEX','Duplex');
define('_INTPHP_TEXT','Après avoir cliqué sur &quot;Enregistrer&quot;, Vous pourriez également avoir besoin de vérifier les points suivants avant de pouvoir vous reconnecter sur votre NAS: 
                    <ul>
                      <li>changez l\'adresse IP de votre ordinateur</li>
                      <li>accédez à l\'interface avec la nouvelle adresse IP</li>
                    </ul>');
define('_INTPHP_ENOPT','Activer Optional ');
define('_INTPHP_ENOPTINT',' interface');
define('_INTPHP_DESC',_DESC);
define('_INTPHP_DESCTEXT','Entrez une description pour l\'interface ici.');

// Disks: Manage
// Common text
define('_DISKSPHP_NAME',_DISKS);
define('_DISKSPHP_MANAGE','Gerer');
define('_DISKSPHP_FORMAT','Formater');
define('_DISKSPHP_ISCSIINIT','initiateur iSCSI');
define('_DISKSPHP_DISK',_DISK);
define('_DISKSPHP_FILESYSTEM',_FILESYSTEM);

// file: disks_manage.php

define('_DISKSMANAGEPHP_NAMEDESC','Gestion');
define('_DISKSMANAGEPHP_MSGCHANGED','La liste des disques a changé.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','Délai de mise en veille');
define('_DISKSMANAGEPHP_FSUNKNOWN','inconnu ou non formaté');
define('_DISKSMANAGEPHP_EDIT','modifier le disque');
define('_DISKSMANAGEPHP_DEL','supprimer le  disque');
define('_DISKSMANAGEPHP_DELCONF','Voulez-vous vraiment supprimer ce disque? Tous les éléments l\'utilisant seront invalidés (par ex. share)!');
define('_DISKSMANAGEPHP_ADD','ajouter un disque');
define('_DISKSMANAGEPHP_NOTE','<strong>Note:<br>
                </strong>Premiere etape: Ajoutez votre disque dur à la liste des disques.');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','Ce disque existe déjà dans la liste des disques.');
define('_DISKSMANAGEEDITPHP_UDMA','Mode UDMA');
define('_DISKSMANAGEEDITPHP_UDMATEXT','Vous pouvez forcer le mode UDMA si vous avez le message \"UDMA_ERROR.... LBA\" avec votre disque dur.');
define('_DISKSMANAGEEDITPHP_STANDBY','Delai de mise en veille du disque');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','Mets le disque en veille lorsque la durée spécifiée depuis le dernier accès est écoulée. <em>N\'activez PAS cette option pour les cartes Compact Flash.</em>');
define('_DISKSMANAGEEDITPHP_APM','Gestion d\'énergie avancée');
define('_DISKSMANAGEEDITPHP_APMTEXT','Ceci vous permet de baisser la consommation d\'énergie du disque, au détriment de ses performances.<em>N\'activez PAS cette option pour les cartes Compact Flash.</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','Niveau acoustique');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','Ceci vous permet de contrôler le niveau sonore du disque en fonctionnement.<em>N\'activez PAS cette option pour les cartes Compact Flash.</em>');
define('_DISKSMANAGEEDITPHP_PREFS','Système de fichier préexistant');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','Ceci vous permet d\'indiquer le type de système de fichier d\'un disque contenant des données.<br>
                     <em>Laissez "unformated" pour des disques non formatés puis utilisez le menu Formater.</em>');

//file: disks_manage_init.php

define('_DISKSMANAGEINITPHP_NAMEDESC','Initialiser');
define('_DISKSMANAGEINITPHP_INITTEXT','Détails de l\'initialisation:');
define('_DISKSMANAGEINITPHP_INITINFO','\"fdisk: Geom not found\"n\'est pas un message d\'erreur!\n');
define('_DISKSMANAGEINITPHP_TEXT','Ceci efface toutes les partitions, créé la partition numéro 1 et formate le disque avec le systeme de fichier spécifié.');

//file: disks_manage_iscsi.php
define('_DISKSMANAGEPHP_NAMEDESC','Gestion');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','Initiateur iSCSI');
define('_DISKSMANAGEISCSIPHP_TARGETIP','Address IP cible');
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
define('_DISKSRAIDPHP_MSGCHANGED','La configuration RAID a été changée.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_DISKSRAIDPHP_TYPE','Type');
define('_DISKSRAIDPHP_EDITRAID','Modifier raid');
define('_DISKSRAIDPHP_DELRAID','Supprimer raid');
define('_DISKSRAIDPHP_DELCONF','Voulez-vous vraiment supprimer ce volume RAID? Tous les éléments l\'utilisant seront invalidés (par ex. share)!');
define('_DISKSRAIDPHP_NOTE','Note:<br>
                </strong></span>Etape optionnelle: Configurer un disque RAID virtuel en utilisant votre<br>
                <a href=disks_manage.php>disque configuré précédemment</a>.<br>Attendez l\'apparition de l\'état "UP" avant de le formater et de le monter!.');

//file: disks_raid_edit.php

define('_DISKSRAIDEDITPHP_NAMEDESC',_EDIT);
define('_DISKSRAIDEDITPHP_MSGADDDISKFIRST','Vous devez d\'abord ajouter un disque.');
define('_DISKSRAIDEDITPHP_MSGVALIDNAME','Le nom de périphérique doit contenir uniquement les caractères a-z, A-Z, 0-9.');
define('_DISKSRAIDEDITPHP_MSGEXIST','Ce périphérique existe déjà dans la liste des volumes RAID.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID0','Un volume RAID0 doit être composé d\'au moins 2 disques.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID1','Un volume RAID1 doit être composé de 2 disques.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID5','Un volume RAID5 doit être composé d\'au moins 3 disques.');
define('_DISKSRAIDEDITPHP_MSGERROR','ERREUR Impossible de sauvegarder la configuration RAID.\n');
define('_DISKSRAIDEDITPHP_RAIDNAME','Nom du Raid');
define('_DISKSRAIDEDITPHP_RAID0','striping');
define('_DISKSRAIDEDITPHP_RAID1','mirroring');
define('_DISKSRAIDEDITPHP_RAID5','rotated block-interleaved parity');
define('_DISKSRAIDEDITPHP_MEMBERS','Membres de ce volume');


//file: disks_raid_info.php

define('_DISKSRAIDINFOPHP_TEXT','Information et état du RAID logiciel');

//file: disks_raid_init.php

define('_DISKSRAIDINITPHP_INFO','Formatage du disque en UFS:');
define('_DISKSRAIDINITPHP_TEXT','Le volume RAID sera formaté avec le système de fichiers Unix (UFS).');
define('_DISKSRAIDINITPHP_FORMAT','Formatage du disque en UFS!');

//file: disks_raid_tools.php
define('_DISKSRAIDTOOLSPHP_OBJNAME','Nom de l\'objet');
define('_DISKSRAIDTOOLSPHP_COMMAND','Commande');
define('_DISKSRAIDTOOLSPHP_INFO','GVINUM command output:');
define('_DISKSRAIDTOOLSPHP_SENDCMD','Valider la commande!');
define('_DISKSRAIDTOOLSPHP_TEXT','1. N\'utilisez ces actions spéciales qu\'à des fins de débogage!<br>2. Nul besoin d\'utiliser ce menu pour démarrer un volume RAID (démarrage automatique).');

//Disk Mount Point
// Comon value
define('_DISKSMOUNTPHP_NAME','Point de montage');
define('_DISKSMOUNTPHP_SHARENAME','Nom du partage');

// disks_mount.php

define('_DISKSMOUNTPHP_MSGCHANGED','La liste des points de montage a été modifiée.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_DISKSMOUNTPHP_NOTE','Note:<br>
                </strong></span>Deuxieme étape de la configuration: Declarer le système de fichiers utilisé par votre<br>
                <a href="disks_manage.php">disque configuré précédemment');
define('_DISKSMOUNTPHP_DELCONF','Voulez-vous vraiment supprimer ce point de montage? Tous les éléments l\'utilisant seront invalidés (par ex. share)!');
define('_DISKSMOUNTPHP_DEL','Supprimer le point de montage');
define('_DISKSMOUNTPHP_ADD','Ajouter un point de montage');

// disks_mount_edit.php

define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','Le nom de partage doit contenir uniquement les caractères a-z, A-Z, 0-9, _ , -.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','La description contient des caractères invalides.');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','Impossible de monter la partition système 1, la partition de données est la deuxième.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','Nom de partage dupliqué.');
define('_DISKSMOUNTEDITPHP_TEXT','1. Vous ne pouvez pas monter la partition \'<?php echo htmlspecialchars($cfdevice);?>\' où le fichier de configuration est stocké<br>2. Le support NTFS de FreeBSD est buggé.');

// Service: CIFS/SAMBA
// file: services_samba.php

define('_SRVCIFS_NAMEDESC','CIFS');
define('_SRVCIFS_MSGVALIDNETBIOSNAME','Le nom Netbios contient des caractères invalides.');
define('_SRVCIFS_MSGVALIDWORKGOUP','Le groupe de travail contient des caractères invalides.');
define('_SRVCIFS_MSGVALIDWINS','Le serveur WINS doit être une adresse IP.');
define('_SRVCIFS_MSGVALIDSNDBUFF','La valeur du tampon d\'envoi doit être un entier.');
define('_SRVCIFS_MSGVALIDRCVBUFF','La valeur du tampon de réception doit être un entier.');
define('_SRVCIFS_SHARE','CIFS share');
define('_SRVCIFS_AUTH','Authentification');
define('_SRVCIFS_NETBIOSNAME','Nom Netbios');
define('_SRVCIFS_WORKGROUP','Groupe de travail');
define('_SRVCIFS_WORKGROUPTEXT','Groupe de travail auquel rattacher le NAS.');
define('_SRVCIFS_DESCTEXT','Description du serveur. Ce champ peut rester vide.');
define('_SRVCIFS_DOSCHARSET','Jeu de caractères Dos');
define('_SRVCIFS_UNIXCHARSET','Jeu de caractères Unix');
define('_SRVCIFS_LOGLEVEL','Niveau de journalisation');
define('_SRVCIFS_LOCALMASTER','Maître explorateur local');
define('_SRVCIFS_LOCALMASTERTEXT','Permet à FreeNAS d\'essayer de s\'enregistrer comme maître explorateur local.');
define('_SRVCIFS_TIMESRV','Serveur de temps');
define('_SRVCIFS_TIMESRVTEXT','FreeNAS s\'annonce commme un serveur de temps pour les clients Windows.');
define('_SRVCIFS_WINS','Serveur WINS');
define('_SRVCIFS_WINSTEXT','Adresse IP du serveur WINS.');
define('_SRVCIFS_RECYCLE','Corbeille');
define('_SRVCIFS_ENRECYCLE','Activer la corbeille');
define('_SRVCIFS_ENRECYCLETEXT','Ceci crééra une corbeille pour les partages CIFS');
define('_SRVCIFS_HIDE','Partage à cacher.');
define('_SRVCIFS_HIDETEXT','Sélectionnez le partage CIFS à cacher dans l\'explorateur du client.');
define('_SRVCIFS_SNBBUFF','Taille du tampon d\'envoi');
define('_SRVCIFS_SNBBUFFTEXT','Taille du tampon d\'envoi (8192 par défaut).');
define('_SRVCIFS_RCVBUFF','Taille du tampon de réception');
define('_SRVCIFS_RCVBUFFTEXT','Taille du tampon de réception (8192 par défaut).');

// Service: FTP
// file: services_ftp.php

define('_SRVFTP_NAMEDESC','FTP');
define('_SRVFTP_MSGVALIDPORT',_MSGVALIDTCPPORT);
define('_SRVFTP_MSGVALIDMAXCLIENT','Le nombre maximum de clients doit être un entier.');
define('_SRVFTP_MSGVALIDMAXIP','Le nombre maximum de connexions par IP doit être un entier.');
define('_SRVFTP_MSGVALIDIDLE','La durée maximum d\'inactivité doit être un entier.');
define('_SRVFTP_MSGVALIDPASVIP','L\'adresse pasv doit être une adresse IP publique.');
define('_SRVFTP_MSGVALIDPASVMAX','La valeur pasv_max_port doit être un numéro de port valide.');
define('_SRVFTP_MSGVALIDPASVMIN','La valeur pasv_min_port doit être un numéro de port valide.');
define('_SRVFTP_FTPSERVER','Serveur FTP');
define('_SRVFTP_TCP',_TCPPORT);
define('_SRVFTP_MAXCLIENT','Nombre de clients');
define('_SRVFTP_MAXCLIENTTEXT','Nombre maximum de clients simultanés.');
define('_SRVFTP_MAXIP','Max conn per ip');
define('_SRVFTP_MAXIPTEXT','Nombre maximum de connexions par IP.');
define('_SRVFTP_TIMEOUT','Timeout');
define('_SRVFTP_TIMEOUTTEXT','Durée maximum d\'inactivité en secondes.');
define('_SRVFTP_ANONYMOUS','Activer Anonymous');
define('_SRVFTP_ANONYMOUSTEXT','Accepter les connexions anonymes.');
define('_SRVFTP_AUTH','Activer l\'authentification des utilisateurs');
define('_SRVFTP_AUTHTEXT','Accepter les connexions des utilisateurs authentifiés.');
define('_SRVFTP_PASVIP','Addresse IP passive');
define('_SRVFTP_PASVIPTEXT','Utilisez cette option pour outrepasser l\'adresse IP que le démon FTP envoie en réponse aux commandes PASV.');
define('_SRVFTP_PASVMIN','pasv_min_port');
define('_SRVFTP_PASVMINTEXT','N° de port minimum à allouer pour les connexions passives.');
define('_SRVFTP_PASVMAX','pasv_max_port');
define('_SRVFTP_PASVMAXTEXT','N° de port maximum à allouer pour les connexions passives.');
define('_SRVFTP_BANNER','Bannière');
define('_SRVFTP_BANNERTEXT','Bannière d\'accueil affichée par le serveur FTP au début d\'une connexion.');

// Service: NFS
// file: services_nfs.php

define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','Un réseau valide doit être spécifié.');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','Serveur NFS');
define('_SRVNFS_MAPALLROOT','Associer tous les utilisateurs à root');
define('_SRVNFS_MAPALLROOTTEXT','Tous les utilisateurs auront les privilèges root.');
define('_SRVNFS_AUTHNET','Réseau autorisé');
define('_SRVNFS_AUTHNETTEXT','Réseau autorisé à accéder au partage NFS');
define('_SRVNFS_TEXT','Le nom de partage exporté est du type : /mnt/partage');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','Serveur');
define('_SRVRYNC_CLIENT','Client');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','Démon Rsync');
define('_SRVRYNCD_READONLY','Lecture seule');
define('_SRVRYNCD_MAPUSER','Associer à l\'utilisateur');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_MAXCON','Maximum de connexions');
define('_SRVRYNCD_MAXCONTEXT','Nombre maximum number de connexions simultanées. 0 par défaut (illimité)');
define('_SRVRYNCD_MOTD','MOTD');
define('_SRVFTP_MOTDTEXT','Message du jour');
define('_SRVRYNCD_TCPORTTEXT','Port TCP alternatif.');

// file: services_rsyncd_client.php

define('_SRVRYNCC_MSGMPFIRST','Vous devez d\'abord choisir un point de montage.');
define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
define('_SRVRYNCC_RSYNCC','Synchronisation client Rsync');
define('_SRVRYNCC_REMOTESERVER','Serveur RSYNC distant');
define('_SRVRYNCC_REMOTEID','Adresse IP du Serveur RSYNC distant');
define('_SRVRYNCC_OPTIONS','Options RSYNC');
define('_SRVRYNCC_OPTDEL','Supprimer les fichiers qui n\'existent pas sur la machine source.');
define('_SRVRYNCC_SHARES','Partages à synchroniser');
define('_SRVRYNCC_SHARESTEXT','Un partage du même nom doit exister sur le serveur distant.');
define('_SRVRYNCC_TIME','Heure de Synchronisation');
define('_SRVRYNCC_TEXT',_MSGSCHED_TEXT);

// Service: SSHD
// file: services_sshd.php

define('_SRVSSHD_NAMEDESC','SSHD');
define('_SRVSSHD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVSSHD_MSGVALIDKEY','Cette clef semble invalide.');
define('_SRVSSHD_SSHD','Démon SSH');
define('_SRVSSHD_READONLY','Lecture seule');
define('_SRVSSHD_TCPORT',_TCPPORT);
define('_SRVSSHD_KEY','Clef SSH');
define('_SRVSSHD_PRIVATEKEY','Clef privée');
define('_SRVSSHD_PRIVATEKEYTEXT','Collez une clef privée DSA au format PEM ici.');

// Service: AFP
// file: services_afp.php

define('_SRVAFP_NAMEDESC','AFP');
define('_SRVAFP_MSGVALIDAUTH','Vous devez sélectionner au moins un méthode d\'authentification.');
define('_SRVAFP_AFPSRV','Serveur AFP');
define('_SRVAFP_AUTH','Authentification');
define('_SRVAFP_ENABLEGUEST','Activer l\'accès invité.');
define('_SRVAFP_ENABLELOCAL','Activer l\'authentification locale des utilisateurs.');

// Access
// common words

define('_ACCESS_NAME','Accès');
define('_ACCESS_USER','Utilisateur');
define('_ACCESS_USERS','Utilisateurs');
define('_ACCESS_GROUP','Groupe');
define('_ACCESS_GROUPS','Groupes');
define('_ACCESS_FULLNAME','Nom complet');

// file: access_users.php

define('_ACCESSUSERS_MSGCHANGED','La liste des utilisateurs a été modifiée.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_ACCESSUSERS_EDITUSER','modifier l\'utilisateur');
define('_ACCESSUSERS_DELUSER','Supprimer l\'utilisateur');
define('_ACCESSUSERS_CONFDEL','Voulez-vous vraiment supprimer cet utilisateur?!');
define('_ACCESSUSERS_ADDUSER','ajouter un utilisateur');

// file: access_users_edit.php

define('_ACCESSUSERSEDIT_MSGGROUPFIRST','Vous devez d\'abord créer un groupe.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGIN','L\'identifiant contient un caractère invalide.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED','L\'identifiant est un identifiant réservé du système.');
define('_ACCESSUSERSEDIT_MSGVALIDFULLNAME','Le nom complet contient un caractère invalide.');
define('_ACCESSUSERSEDIT_MSGVALIDDUPLICATE','Cet utilisateur existe déjà.');
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
define('_ACCESSUSERSEDIT_FULLSHELLTEXT','Donne l\'accès complet au shell');

// file: access_users_groups.php

define('_ACCESSGROUPS_MSGCHANGED','La liste des groupes a été modifiée.<br>Vous devez appliquer les changements pour qu\'ils prennent effet.');
define('_ACCESSGROUPS_DESC',_DESC);
define('_ACCESSGROUPS_GROUPEDIT','modifer le groupe');
define('_ACCESSGROUPS_GROUPDEL','supprimer le groupe');
define('_ACCESSGROUPS_GROUPDELCONF','Voulez-vous vraiment supprimer ce groupe?!');
define('_ACCESSGROUPS_GROUPADD','ajouter un groupe');

// file: access_users_groups_edit.php

define('_ACCESSGROUPSEDIT_MSGVALIDNAME','Le nom du groupe contient un caractère invalide.');
define('_ACCESSGROUPSEDIT_MSGVALIDDESC','La description du groupe contient un caractère invalide.');
define('_ACCESSGROUPSEDIT_MSGVALIDDUPLICATE','Ce groupe existe déjà.');
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
define('_ACCESSAD_DOMAINTEXT','Nom du domaine à l\'ancien format (NETBIOS).');
define('_ACCESSAD_ADMINAME','Nom de l\'administrateur');
define('_ACCESSAD_ADMINNAMETEXT','Identifiant d\'un compte administrateur du domaine.');
define('_ACCESSAD_ADMINPASS','Mot de passe admin');
define('_ACCESSAD_ADMINPASSTEXT','Mot de passe d\'un compte administrateur du domaine, à entrer 2 fois.');

// Diagnostics

define('_DIAG_NAME','Diagnostics');

// file: shutdown.php

define('_SHUT_NAME','Arrêt du NAS');
define('_SHUT_MSGSHUT','Le système est en cours d\'arrêt. Ceci peut prendre jusqu\'à  une minute.');
define('_SHUT_CONF','Etes-vous sûr de vouloir arrêter le NAS?');

// file: shutdown_sched.php

define('_SHUTSCHED_NAME','Arrêt programmé');
define('_SHUTSCHED_TIME','Heure d\'arrêt');
define('_SHUTSCHED_TEXT',_MSGSCHED_TEXT);



?>

