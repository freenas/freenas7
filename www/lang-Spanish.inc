<?php 
/*
	lang-Spanish.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard <olivier@freenas.org>.
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
define('_LOCALE','en_US');
define('_LOCALEWIN', 'eng');

// date and time defines
define('_DATE','Date');
define('_DATEBRIEF','%b %d, %Y');
define('_DATELONG','%A, %B %d, %Y');
define('_DATESTRING','%A, %B %d @ %H:%M:%S');
define('_DATETIMEBRIEF','%b %d, %Y - %I:%M %p');
define('_DATETIMELONG','%A, %B %d, %Y - %I:%M %p');
define('_DAY_OF_WEEK_LONG','Sunday Monday Tuesday Wednesday Thursday Friday Saturday');
define('_DAY_OF_WEEK_SHORT','Sun Mon Tue Wed Thu Fri Sat');
define('_MONTH_LONG','January February March April May June July August September October November December');
define('_MONTH_SHORT','Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec');
define('_TIME', 'Time');
define('_TIMEBRIEF','%I:%M %p');
define('_SECOND', 'second');
define('_SECONDS', 'seconds');
define('_MINUTE', 'minute');
define('_MINUTES', 'minutes');
define('_DAY', 'day');
define('_DAYS', 'days');
define('_WEEK', 'week');
define('_WEEKS', 'weeks');
define('_MONTH', 'month');
define('_MONTHS', 'months');
define('_YEAR', 'year');
define('_YEARS', 'years');

// common words
define('_ALL','All');
define('_AND','and');
define('_BY','by');
define('_DOWN','Down');
define('_NO','No');
define('_OF','of');
define('_OK','OK');
define('_ON','on');
define('_TO','To');
define('_THE', 'The');
define('_UP','Up');
define('_URL', 'URL');
define('_YES','Yes');

// common actions
define('_ACTIVATE','Activar');
define('_ADD','Add');
define('_CANCEL', 'Cancelar');
define('_COMMIT', 'Update');
define('_CREATE', 'Crear');
define('_DEACTIVATE','Deactivate');
define('_DELETE','Borrar');
define('_EDIT','Editar');
define('_HELP', 'Ayuda');
define('_IGNORE','Ignorar');
define('_LOGIN','Log in');
define('_LOGOUT','Log out');
define('_MODIFY','Modificar');
define('_NEW','Nuevo');
define('_SEARCH', 'Buscar');
define('_SAVE', 'Guardar');
define('_SUBMIT','Enviar');
define('_UPDATE', 'Actualizar');
define('_VIEW', 'Mostrar');
define('_APPLY', 'Aplicar Cambios');
define('_WARNING', 'Precaución');
define('_LOAD', 'Load');
define('_RETRY', 'Retry');

//FreeNAS common words
define('_SIZE', 'Size');
define('_STATUS', 'Status');
define('_TYPE', 'Type');
define('_CONFIGURING', 'Configuring');
define('_DISK', 'Disk');
define('_DISKS', 'Disks');
define('_PARTITION', 'Partition');
define('_FILESYSTEM', 'File system');
define('_DESC', 'Description');
define('_SOFTRAID', 'Software RAID');
define('_SERVICE','Service');
define('_SERVICES','Services');
define('_TCPPORT','TCP port');
define('_CONFIRMATION','Confirmation');
define('_NOTE','Note');
define('_SUCCESSFUL','Successful');
define('_FAILED','Failed');
define('_ERROR','Error');
define('_HINT','Hint');

// common messages
define('_MESSAGES_SUCCESS', 'Los cambios han sido aplicados satisfactoriamente.');
define('_MESSAGES_SUCCESSWITHREBOOT', 'Los cambios han sido aplicados. Tiene que <a href=\"/reboot.php\">reiniciar</a> su NAS para que los cambios tengan efecto.');
define('_MESSAGES_ERROR', 'ERROR: los cambios no pudieron ser aplicados(código de error '.$ok.').');

define('_MSGVALIDIP','A valid IP address must be specified.');
define('_MSGVALIDNETBIT','A valid network bit count must be specified.');
define('_MSGVALIDTCPPORT','The TCP port must be a valid port number.');
define('_MSGVALIDPASSWORD','Password don\'t match.');

define('_MSGSCHED_TEXT','Note: Ctrl-click (or command-click on the Mac) to select and de-select minutes, hours, days and months.');

// states
define('_ACTIVE','Active');
define('_INACTIVE','Inactive');
define('_SELECTED','Selected');

// Webgui layout
// Webgui layout
define('_MENULEFT_SYSTEM','Sistema');
define('_MENULEFT_SYSGENSETUP','Configuración');
define('_MENULEFT_SYSSTATROUTES','Rutas Estáticas');
define('_MENULEFT_SYSFIRMWARE','Sistema base');
define('_MENULEFT_SYSADVANCED','Avanzado');
define('_MENULEFT_INTERFACES','Interfaces');
define('_MENULEFT_INTASSIGN','Asignar');
define('_MENULEFT_INTLAN','LAN');
define('_MENULEFT_DISKS','Discos');
define('_MENULEFT_DISKMANAG','Administrar');
define('_MENULEFT_DISKMNGSOFTRAID','Software RAID');
define('_MENULEFT_DISKMNGMOUNTPOINT','Punto de Montaje');
define('_MENULEFT_SERVICES','Servicios');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','Acceso');
define('_MENULEFT_ACUSERGROUPS','Usuarios y Grupos');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS','Estado');
define('_MENULEFT_STSYSTEM','Sistema');
define('_MENULEFT_STPROCESS','Procesos');
define('_MENULEFT_STINTERFACES','Interfaces');
define('_MENULEFT_STDISKS','Discos');
define('_MENULEFT_STWIRELESS','Inalámbrico');
define('_MENULEFT_EXTENSIONS','Extensiones');
define('_MENULEFT_DIAGNOSTICS','Diagnóstico');
define('_MENULEFT_DIAGLOGS','Registros');
define('_MENULEFT_DIAGINF','Información');
define('_MENULEFT_DIAGPINGTRACE','Ping/Traceroute');
define('_MENULEFT_DIAGARPTAB','Tabla ARP');
define('_MENULEFT_DIAGEXEC','Ejecutar comando');
define('_MENULEFT_CMDBACKUPRESTORE','Salvar/Restaurar');
define('_MENULEFT_CMDFACTORYDEF','Valores de Frábica');
define('_MENULEFT_CMDREBOOTSYS','Reiniciar Sistema');
define('_MENULEFT_CMDSHUTDOWNSYS','Apagar Sistema');
define('_MENULEFT_GRAPH','Graph');

// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME','Sistema');
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION','Configuración General');
define('_SYSTEMPHP_HOSTNAME','Nombre de Host');
define('_SYSTEMPHP_HOSTNAMETEXT','nombre del host, sin la parte del dominio<br>e.j. <em>nas');
define('_SYSTEMPHP_DOMAIN','Dominio');
define('_SYSTEMPHP_DOMAINTEXT','e.j. <em>micorp.com</em>');
define('_SYSTEMPHP_DNS','Servidores DNS');
define('_SYSTEMPHP_DNSTEXT','Direcciones IP');
define('_SYSTEMPHP_USERNAME','Usuario');
define('_SYSTEMPHP_USERNAMETEXT','Si desa cambiar el usuario para acceder la WebGUI, introdúscalo aquí.');
define('_SYSTEMPHP_PASSWORD','Contraseña');
define('_SYSTEMPHP_PASSWORDTEXT','Si desea cambiar la contraseña para acceder la webGUI, introdúscala aquí dos veces.');
define('_SYSTEMPHP_WEBGUIPROTOCOL','Protocolo de la WebGUI');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','');
define('_SYSTEMPHP_WEBGUIPORT','Puerto de la WebGUI');
define('_SYSTEMPHP_WEBGUIPORTTEXT','Introduzca un puerto personalizado si desea reemplazar los valores por defecto (80 para HTTP, 443 para HTTPS).');
define('_SYSTEMPHP_LANGUAGE','Idioma');
define('_SYSTEMPHP_LANGUAGETEXT','Seleccione el idioma del sistema');
define('_SYSTEMPHP_TIMEZONE','Zona horaria');
define('_SYSTEMPHP_TIMEZONETEXT','Seleccione el lugar más cerca a usted');
define('_SYSTEMPHP_TIMEUPDATEINT','Intervalo de actualización de tiempo');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','Minutos entre la sincronización de tiempo por la red.; 300	recomendado, o 0 para deshabilitar');
define('_SYSTEMPHP_NTPSERVER','Servidor NTP');
define('_SYSTEMPHP_NTPSERVERTEXT','Use espacio para separar varios servidores (solo se requiere uno). Recuerde configurar al menos un servidor DNS si introduce un nombre aquí!');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','Información del Sistema');
define('_INDEXPHP_NAME','Nombre');
define('_INDEXPHP_VERSION','Versión');
define('_INDEXPHP_PLATFORM','Plataforma');
define('_INDEXPHP_DATE','Fecha');
define('_INDEXPHP_UPTIME','Tiempo Encendido');
define('_INDEXPHP_LASTCHANGE','Ultima configuración');
define('_INDEXPHP_MEMUSE','Uso de memoria');
define('_INDEXPHP_LOADAVERAGE','Promedio de carga');
define('_INDEXPHP_SHOWPROCESSING','mostrar información de procesos');

// Disks: Manage
// Common text
define('_DISKSPHP_NAME',_DISKS);
define('_DISKSPHP_MANAGE','Manage');
define('_DISKSPHP_FORMAT','Format');
define('_DISKSPHP_TOOLS','Tools');
define('_DISKSPHP_ISCSIINIT','iSCSI initiator');
define('_DISKSPHP_DISK',_DISK);
define('_DISKSPHP_FILESYSTEM',_FILESYSTEM);

// file: disks_manage.php
define('_DISKSMANAGEPHP_NAMEDESC','Management');
define('_DISKSMANAGEPHP_MSGCHANGED','The disk list has been changed.<br>You must apply the changes in order for them to take effect.');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','Standby time');
define('_DISKSMANAGEPHP_FSUNKNOWN','Unknown or unformatted');
define('_DISKSMANAGEPHP_EDIT','Edit disk');
define('_DISKSMANAGEPHP_DEL','Delete disk');
define('_DISKSMANAGEPHP_DELCONF','Do you really want to delete this disk? All elements that still use it will become invalid (e.g. share)!');
define('_DISKSMANAGEPHP_ADD','Add disk');
define('_DISKSMANAGEPHP_NOTE','First configuration step: Add your hardrive to the disk list.');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','This disk already exists in the disk list.');
define('_DISKSMANAGEEDITPHP_UDMA','UDMA mode');
define('_DISKSMANAGEEDITPHP_UDMATEXT','You can force UDMA mode if you have "UDMA_ERROR.... LBA" message with your hard drive.');
define('_DISKSMANAGEEDITPHP_STANDBY','Hard disk standby time');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','Puts the hard disk into standby mode when the selected amount of time after the last
                    access has elapsed. <em>Do not set this for CF cards.</em>');
define('_DISKSMANAGEEDITPHP_APM','Advanced Power Management');
define('_DISKSMANAGEEDITPHP_APMTEXT','This allows  you  to lower the power consumption of the drive, at the expense of performance.<em>Do not set this for CF cards.</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','Acoustic level');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','This allows you to set how loud the drive is while it\'s  operating.<em>Do not set this for CF cards.</em>');
define('_DISKSMANAGEEDITPHP_PREFS','Preformated FS');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','This allows you to set FS type for preformated disk with data.<br>
                     <em>Leave "unformated" for unformated disk and then use Format menu.</em>');

//file: disks_manage_init.php
define('_DISKSMANAGEINITPHP_NAMEDESC','Initialize');
define('_DISKSMANAGEINITPHP_DISKMOUNTERROR','The disk is currently mounted! <a href=%s>Unmount</a> this disk first before proceeding.');
define('_DISKSMANAGEINITPHP_INITTEXT','<strong>Disk initialization details:</strong>');
define('_DISKSMANAGEINITPHP_INITINFO','\"fdisk: Geom not found\"is not an error message!\n');
define('_DISKSMANAGEINITPHP_TEXT','This step will erase all your partition, create partition number 1 and format the hard drive with the file system specified.');
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
define('_DISKSMANAGEPHP_NAMEDESC','Management');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','iSCSI Initiator');
define('_DISKSMANAGEISCSIPHP_TARGETIP','Target IP address');
define('_DISKSMANAGEISCSIPHP_TARGETIPTEXT','IP address of the iSCSI target.');
define('_DISKSMANAGEISCSIPHP_TARGETNAME','Targetname');
define('_DISKSMANAGEISCSIPHP_TARGETNAMETEXT','Targetname.');

//Software RAID
// Comon value
define('_DISKSRAIDPHP_MANAGE','Manage RAID');
define('_DISKSRAIDPHP_FORMAT','Format RAID');
define('_DISKSRAIDPHP_TOOLS','Tools');
define('_DISKSRAIDPHP_INFO','Information');
define('_DISKSRAIDPHP_VOLUME','Volume Name');

// Software RAID common word
define('_DISKSRAIDPHP_GVINUM','Geom Vinum');
define('_DISKSRAIDPHP_GMIRROR','Geom Mirror');
define('_DISKSRAIDPHP_GRAID5','Geom Raid5');
define('_DISKSRAIDPHP_UNSTABLE','(unstable)');

//file: disks_raid.php
define('_DISKSRAIDPHP_NAMEDESC','RAID');
define('_DISKSRAIDPHP_MSGCHANGED','The Raid configuration has been changed.<br>You must apply the changes in order for them to take effect.');
define('_DISKSRAIDPHP_TYPE','Type');
define('_DISKSRAIDPHP_EDITRAID','Edit RAID');
define('_DISKSRAIDPHP_DELRAID','Delete RAID');
define('_DISKSRAIDPHP_ADDRAID','Add RAID');
define('_DISKSRAIDPHP_DELCONF','Do you really want to delete this raid volume? All elements that still use it will become invalid (e.g. share)!');
define('_DISKSRAIDPHP_NOTE','Optional configuration step: Configuring a virtual RAID disk using your <a href="disks_manage.php">previsously configured disk</a>.<br>Wait for the "up" status before format and mount it!');

//file: disks_raid_edit.php
define('_DISKSRAIDEDITPHP_NAMEDESC',_EDIT);
define('_DISKSRAIDEDITPHP_MSGADDDISKFIRST','You must add disks first.');
define('_DISKSRAIDEDITPHP_MSGVALIDNAME','The device name may only consist of the characters a-z, A-Z, 0-9.');
define('_DISKSRAIDEDITPHP_MSGEXIST','This device already exists in the raid volume list.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID0','There must be a minimum of 2 disks in a RAID 0 volume.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID1','There must be 2 disks in a RAID 1 volume.');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID5','There must be a minimum of 3 disks in a RAID 5 volume.');
define('_DISKSRAIDEDITPHP_MSGERROR','ERR Could not save RAID configuration.\n');
define('_DISKSRAIDEDITPHP_RAIDNAME','Raid name');
define('_DISKSRAIDEDITPHP_GVBALANCEALGO','Balance algorithm');
define('_DISKSRAIDEDITPHP_GVBALANCEALGOTEXT','Select your read balance algorithm.');
define('_DISKSRAIDEDITPHP_RAID0','striping');
define('_DISKSRAIDEDITPHP_RAID1','mirroring');
define('_DISKSRAIDEDITPHP_RAID5','rotated block-interleaved parity');
define('_DISKSRAIDEDITPHP_MEMBERS','Members of this volume');

//file: disks_raid_info.php
define('_DISKSRAIDINFOPHP_TEXT','Software RAID information and status');

//file: disks_raid_init.php
define('_DISKSRAIDINITPHP_INFO','Disk format UFS output:');
define('_DISKSRAIDINITPHP_TEXT','This step will format the RAID volume in Unix FileSystem (UFS).');
define('_DISKSRAIDINITPHP_FORMAT','Format UFS disk!');

//file: disks_raid_tools.php
define('_DISKSRAIDTOOLSPHP_OBJNAME','Object name');
define('_DISKSRAIDTOOLSPHP_COMMAND','Command');
define('_DISKSRAIDTOOLSPHP_INFO','GVINUM command output:');
define('_DISKSRAIDTOOLSPHP_SENDCMD','Send Command!');
define('_DISKSRAIDTOOLSPHP_TEXT','1. Use these specials actions for debugging only!<br>2. There is no need of using this menu for start a RAID volume (start automaticaly).');

//Disk Mount Point
// Comon value
define('_DISKSMOUNTPHP_NAME','Mount Point');
define('_DISKSMOUNTPHP_SHARENAME','Share Name');
define('_DISKSMOUNTPHP_MANAGE','Manage');
define('_DISKSMOUNTPHP_TOOLS','Tools');

// disks_mount.php
define('_DISKSMOUNTPHP_MSGCHANGED','The mount point list has been changed.<br>You must apply the changes in order for them to take effect.');
define('_DISKSMOUNTPHP_NOTE','Second configuration step: Declaring the filesystem used by your <a href="disks_manage.php">previously configured disk</a>.');
define('_DISKSMOUNTPHP_DELCONF','Do you really want to delete this mount point? All elements that still use it will become invalid (e.g. share)!');
define('_DISKSMOUNTPHP_DEL','delete mount');
define('_DISKSMOUNTPHP_ADD','add mount');

// disks_mount_edit.php
define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','The share name may only consist of the characters a-z, A-Z, 0-9, _ , -.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','The description name contain invalid characters.');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','Can\'t mount the system partition 1, the DATA partition is the 2.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','Duplicate Share Name.');
define('_DISKSMOUNTEDITPHP_TEXT','1. You can\'t mount the partition \'%s\' where the config file is stored.<br>2. FreeBSD NTFS has lots of bugs.');

// disks_mount_tools.php
define('_DISKSMOUNTTOOLS_COMMAND','Command');
define('_DISKSMOUNTTOOLS_SENDCMD','Send Command!');
define('_DISKSMOUNTTOOLS_CMDINFO','Command output:');
define('_DISKSMOUNTTOOLS_MOUNTTEXT','Mounting...');
define('_DISKSMOUNTTOOLS_UMOUNTTEXT','Unmounting...');

// Service: CIFS/SAMBA
// Common text
define('_SRVCIFS_SETTINGS','Settings');
define('_SRVCIFS_SHARE','Share');
define('_SRVCIFS_SHARES','Shares');

// file: services_samba.php
define('_SRVCIFS_NAMEDESC','CIFS');
define('_SRVCIFS_MSGVALIDNETBIOSNAME','The Netbios name contains invalid characters.');
define('_SRVCIFS_MSGVALIDWORKGOUP','The Workgroup name contains invalid characters.');
define('_SRVCIFS_MSGVALIDWINS','The WINS server must be an IP address.');
define('_SRVCIFS_MSGVALIDSNDBUFF','The SND Buffer value must be a number.');
define('_SRVCIFS_MSGVALIDRCVBUFF','The RCV Buffer value must be a number.');
define('_SRVCIFS_SHARE','CIFS share');
define('_SRVCIFS_AUTH','Authentication');
define('_SRVCIFS_NETBIOSNAME','NetBiosName');
define('_SRVCIFS_WORKGROUP','Workgroup');
define('_SRVCIFS_WORKGROUPTEXT','Workgroup to be member of.');
define('_SRVCIFS_DESCTEXT','Server description. This can usually be left blank.');
define('_SRVCIFS_DOSCHARSET','Dos charset');
define('_SRVCIFS_UNIXCHARSET','Unix charset');
define('_SRVCIFS_LOGLEVEL','Log Level');
define('_SRVCIFS_LOCALMASTER','Local Master Browser');
define('_SRVCIFS_LOCALMASTERTEXT','Allows FreeNAS to try and become a local master browser.');
define('_SRVCIFS_TIMESRV','Time server');
define('_SRVCIFS_TIMESRVTEXT','FreeNAS advertises itself as a time server to Windows clients.');
define('_SRVCIFS_WINS','WINS server');
define('_SRVCIFS_WINSTEXT','WINS Server IP address.');
define('_SRVCIFS_RECYCLE','Recycle Bin');
define('_SRVCIFS_ENRECYCLE','Enable Recycle bin');
define('_SRVCIFS_ENRECYCLETEXT','This will create a recycle bin on the CIFS shares');
define('_SRVCIFS_SNBBUFF','Send Buffer Size');
define('_SRVCIFS_SNBBUFFTEXT','Size of send buffer (16384 by default).');
define('_SRVCIFS_RCVBUFF','Receive Buffer Size');
define('_SRVCIFS_RCVBUFFTEXT','Size of receive buffer (16384 by default).');

// file: services_samba_share.php
define('_SRVCIFSSHARE_MSGCHANGED','The shares has been modified.<br>You must apply the changes in order for them to take effect.');
define('_SRVCIFSSHARE_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHARE_BROWSEABLE','Browseable');
define('_SRVCIFSSHARE_DESC',_DESC);
define('_SRVCIFSSHARE_EDIT','Edit share');

// file: services_samba_share_edit.php
define('_SRVCIFSSHAREEDIT_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHAREEDIT_DESC',_DESC);
define('_SRVCIFSSHAREEDIT_BROWSEABLE','Browseable');
define('_SRVCIFSSHAREEDIT_BROWSEABLETEXT','This controls whether this share is seen in the list of available shares in a net view and in the browse list.');

// Service: FTP
// file: services_ftp.php
define('_SRVFTP_NAMEDESC','FTP');
define('_SRVFTP_MSGVALIDPORT',_MSGVALIDTCPPORT);
define('_SRVFTP_MSGVALIDMAXCLIENT','The maximum Number of client must be a number.');
define('_SRVFTP_MSGVALIDMAXIP','The max con per ip must be a number.');
define('_SRVFTP_MSGVALIDIDLE','The maximum idle time be a number.');
define('_SRVFTP_MSGVALIDPASVIP','The pasv address must be a public IP address.');
define('_SRVFTP_MSGVALIDPASVMAX','The pasv_max_port port must be a valid port number.');
define('_SRVFTP_MSGVALIDPASVMIN','The pasv_min_port port must be a valid port number.');
define('_SRVFTP_MSGVALIDAUTH','You must select at minium anonymous or/and local user authentication.');
define('_SRVFTP_FTPSERVER','FTP Server');
define('_SRVFTP_TCP',_TCPPORT);
define('_SRVFTP_MAXCLIENT','Number of clients');
define('_SRVFTP_MAXCLIENTTEXT','Maximum number of simultaneous clients.');
define('_SRVFTP_MAXIP','Max. conn. per IP');
define('_SRVFTP_MAXIPTEXT','Maximum number of connections per IP address (0 = unlimited).');
define('_SRVFTP_TIMEOUT','Timeout');
define('_SRVFTP_TIMEOUTTEXT','Maximum idle time in seconds.');
define('_SRVFTP_ANONYMOUS','Anonymous login');
define('_SRVFTP_ENANONYMOUS','Enable anonymous login.');
define('_SRVFTP_AUTH','Local User');
define('_SRVFTP_ENAUTH','Enable local user login.');
define('_SRVFTP_FXP','FXP');
define('_SRVFTP_ENFXP','Enable FXP protocol.');
define('_SRVFTP_FXPTEXT','FXP allows transfers between two remote servers without any file data going to the client asking for the transfer (insecure!).');
define('_SRVFTP_NATMODE','NAT mode');
define('_SRVFTP_ENNATMODE','Force NAT mode');
define('_SRVFTP_NATMODETEXT','Enable it if your FTP server is behind a NAT box that doesn\'t support applicative FTP proxying.');
define('_SRVFTP_PASSIVEIP','Passive IP address');
define('_SRVFTP_PASSIVEIPTEXT','Use this option to override the IP address that FTP daemon will advertise in response to the PASV command.');
define('_SRVFTP_PASVMIN','pasv_min_port');
define('_SRVFTP_PASVMINTEXT','The minimum port to allocate for PASV style data connections (0 = use any port).');
define('_SRVFTP_PASVMAX','pasv_max_port');
define('_SRVFTP_PASVMAXTEXT','The maximum port to allocate for PASV style data connections (0 = use any port).');
define('_SRVFTP_BANNER','Banner');
define('_SRVFTP_BANNERTEXT','Greeting banner displayed by FTP when a connection first comes in.');

// Service: NFS
// file: services_nfs.php
define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','A valid network must be specified.');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','NFS Server');
define('_SRVNFS_MAPALLROOT','Map all user to root');
define('_SRVNFS_MAPALLROOTTEXT','All users will have the root privilege.');
define('_SRVNFS_AUTHNET','Authorised network');
define('_SRVNFS_AUTHNETTEXT','Network that is authorised to access to NFS share');
define('_SRVNFS_TEXT','The name of the exported directories are : /mnt/sharename');

// Status Processes Module
// file: status_processes.php
define('_STATSPROCPHP_NAME','Estado');
define('_STATSPROCPHP_NAME_DESCRIPTION','Procesos');
define('_STATSPROCPHP_PROC_INF','Información de procesos');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME','Sistema');
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION','Rutas Estáticas');
define('_SYSTEMROUTESPHP_MSG_CHANGED','La configuración de las rutas estáticas ha cambiado.<br>Usted tiene que aplicar los cambios para que la configuración tenga efecto.');
define('_SYSTEMROUTESPHP_INT','Interface');
define('_SYSTEMROUTESPHP_NET','Red');
define('_SYSTEMROUTESPHP_GW','Puerta de Enlace');
define('_SYSTEMROUTESPHP_DESC','Descripción');
define('_SYSTEMROUTESPHP_EDITROUTE','Editar Ruta');
define('_SYSTEMROUTESPHP_MSGCONFIRM','Confirma que desea borrar esta ruta?');
define('_SYSTEMROUTESPHP_DELROUTE','Borrar Ruta');
define('_SYSTEMROUTESPHP_ADDROUTE','Añadir Ruta');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME','Sistema');
define('_SYSTEMFIRMWAREPHP_NAMEDESC','Sistema Base');
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','El fichero de la imagen no es para esta plataforma');
define('_SYSTEMFIRMWAREPHP_OUTMEM','Fallo en la subida de la imagen(memoria agotada?)');
define('_SYSTEMFIRMWAREPHP_INVSIG','La firma digital en esta imagen no es válida.');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','Esta imagen no está firmada digitalmente.');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','Ha ocurrido un error verificando la firma en esta imagen.');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','El fichero de la imagen está corrupto.');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','El Sistema Base se está instalando. Frenas se reiniciará automáticamente.');
define('_SYSTEMFIRMWAREPHP_NOTSUP','El envio del Sistema Base no está soportado en esta plataforma.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','Esto significa que la imagen enviada ' .
		 	'no es una imagen oficial/soportada y puede traer consigo comportamientos inesperados o ' .
			'compromisos de seguridad. Solo instale imágenes provenientes de fuentes de confianza, y asegure '.
			'quela imagen no ha sido falseada.<br><br>'.
			'La desea instalar de todas formas ? (bajo su propio riesgo)?');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','Click &quot;Habilitar la actualización del Sistema Base &quot; a continuación, luego seleccione el fichero de la imagen a enviar.<br>Click &quot;Actualizar Sistema Base&quot; para comenzar el proceso de actualización.');	
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','Habilitar la actualización del Sistema Base');
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','Deshabilitar la actualización del Sistema Base');	
define('_SYSTEMFIRMWAREPHP_FIRMFILE','Fichero Imagen del Sistema Base');
define('_SYSTEMFIRMWAREPHP_UPFIRM','Actualizar Sistema Base');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','Usted debe reiniciar el sistema antes de actualizar el sistema base.');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','No aborte el proceso una vez que comience. FreeNAS reiniciará automáticamente después de salvar el nuevo Sistema Base. La configuración se mantendrá.<br>Necesita un mínimo de 128 Mb RAM para actualizar el Sistema Base.');

// System Advanced Module
// file: system_advanced.php
define('_SYSTEMADVANCEDPHP_NAME','Sistema');
define('_SYSTEMADVANCEDPHP_NAMEDESC','Configuración Avanzada');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','Server');
define('_SRVRYNC_CLIENT','Client');
define('_SRVRYNC_LOCAL','Local');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','Rsync Daemon');
define('_SRVRYNCD_READONLY','Read only');
define('_SRVRYNCD_MAPUSER','Map to user');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_TCPORTTEXT','Alternate TCP port.');
define('_SRVRYNCD_MAXCON','Maximum connections');
define('_SRVRYNCD_MAXCONTEXT','Maximum number of simultaneous connections. Default is 0 (unlimited).');
define('_SRVRYNCD_MOTD','MOTD');
define('_SRVRYNCD_MOTDTEXT','Message of the day.');

// file: services_rsyncd_client.php
define('_SRVRYNCC_MSGMPFIRST','You must configure mount point first.');
define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
define('_SRVRYNCC_RSYNCC','Rsync Client Synchronization');
define('_SRVRYNCC_REMOTESERVER','Remote RSYNC Server');
define('_SRVRYNCC_REMOTEID','IP address of remote RSYNC server');
define('_SRVRYNCC_OPTIONS','RSYNC Options');
define('_SRVRYNCC_OPTDEL','Delete files that don\'t exist on sender.');
define('_SRVRYNCC_SHARES','Shares to be synchronized');
define('_SRVRYNCC_SHARESTEXT','This same local share must be present on the Remote Server.');
define('_SRVRYNCC_TIME','Synchronization Time');
define('_SRVRYNCC_TEXT',_MSGSCHED_TEXT);

// file: services_rsyncd_local.php
define('_SRVRYNCL_MSGVALID','You can\'t have the same mount point for source and destination!');
define('_SRVRYNCC_RSYNCL','Rsync Local Synchronization');
define('_SRVRYNCL_SOURCE','Source');
define('_SRVRYNCL_SOURCETEXT','Source Mount Point.');
define('_SRVRYNCL_DESTINATION','Destination');
define('_SRVRYNCL_DESTINATIONTEXT','Destination Mount Point (backup drive).');


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
define('_DIAGARP_IPADDRESS','IP address');
define('_DIAGARP_MACADDRESS','MAC address');
define('_DIAGARP_HOSTNAME','Hostname');
define('_DIAGARP_INTERFACE','Interface');
define('_DIAGARP_DEL','Delete ARP entry');
define('_DIAGARP_DELALL','Remove all entries from ARP table');
define('_DIAGARP_NOTE','IP addresses are resolved to hostnames if &quot;Resolve IP addresses to hostnames&quot; is checked on the <a href="diag_logs_settings.php">Diagnostics: Logs</a> page.');

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
