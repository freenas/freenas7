<?php 
/*
	lang-English.php
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
define('_HOUR', 'hour');
define('_HOURS', 'hours');
define('_DAY', 'day');
define('_DAYS', 'days');
define('_WEEK', 'week');
define('_WEEKS', 'weeks');
define('_WEEKDAYS', 'week days');
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
define('_OF','off');
define('_OK','OK');
define('_ON','on');
define('_TO','To');
define('_THE', 'The');
define('_UP','Up');
define('_URL', 'URL');
define('_YES','Yes');

// common actions
define('_ACTIVATE','Activate');
define('_ADD','Add');
define('_CANCEL', 'Cancel');
define('_COMMIT', 'Update');
define('_CREATE', 'Create');
define('_DEACTIVATE','Deactivate');
define('_DELETE','Delete');
define('_EDIT','Edit');
define('_ENABLE','Enable');
define('_HELP', 'Help');
define('_IGNORE','Ignore');
define('_LOGIN','Login');
define('_LOGOUT','Log out');
define('_MODIFY','Modify');
define('_NEW','New');
define('_SEARCH', 'Search');
define('_SAVE', 'Save');
define('_SUBMIT','Submit');
define('_UPDATE', 'Update');
define('_VIEW', 'View');
define('_APPLY', 'Apply changes');
define('_WARNING', 'Warning');

//FreeNAS common words

define('_SIZE', 'Size');
define('_STATUS', 'Status');
define('_TYPE', 'Type');
define('_CONFIGURING', 'configuring');
define('_DISK', 'Disk');
define('_DISKS', 'Disks');
define('_PARTITION', 'Partition');
define('_FILESYSTEM', 'File system');
define('_DESC', 'Description');
define('_SOFTRAID', 'Software RAID');
define('_SERVICE','Service');
define('_SERVICES','Services');
define('_TCPPORT','TCP port');

// common messages
define('_MESSAGES_SUCCESS', 'The changes have been applied successfully.');
define('_MESSAGES_SUCCESSWITHREBOOT', 'The changes have been saved. You must <a href=\"/reboot.php\">reboot</a> your NAS for changes to take effect.');
define('_MESSAGES_ERROR', 'ERROR: the changes could not be applied (error code '.$ok.').');

define('_MSGVALIDIP','A valid IP address must be specified.');
define('_MSGVALIDNETBIT','A valid network bit count must be specified.');
define('_MSGVALIDTCPPORT','The TCP port must be a valid port number.');
define('_MSGVALIDPASSWORD','Password don\'t match.');

define('_MSGSCHED_TEXT','Note: Ctrl-click (or command-click on the Mac) to select and de-select minutes, hours, days and months.');


// states
define('_ACTIVE','Active');
define('_INACTIVE','Inactive');

// Webgui layout
define('_MENULEFT_SYSTEM','System');
define('_MENULEFT_SYSGENSETUP','General setup');
define('_MENULEFT_SYSSTATROUTES','Static routes');
define('_MENULEFT_SYSFIRMWARE','Firmware');
define('_MENULEFT_SYSADVANCED','Advanced');
define('_MENULEFT_INTERFACES','Interfaces');
define('_MENULEFT_INTASSIGN','Assign');
define('_MENULEFT_INTLAN','LAN');
define('_MENULEFT_DISKS',_DISKS);
define('_MENULEFT_DISKMANAG','Management');
define('_MENULEFT_DISKMNGSOFTRAID',_SOFTRAID);
define('_MENULEFT_DISKMNGMOUNTPOINT','Mount Point');
define('_MENULEFT_SERVICES','Services');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','Access');
define('_MENULEFT_ACUSERGROUPS','Users and Groups');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS',_STATUS);
define('_MENULEFT_STSYSTEM','System');
define('_MENULEFT_STPROCESS','Process');
define('_MENULEFT_STINTERFACES','Interfaces');
define('_MENULEFT_STDISKS',_DISKS);
define('_MENULEFT_STWIRELESS','Wireless');
define('_MENULEFT_EXTENSIONS','Extensions');
define('_MENULEFT_DIAGNOSTICS','Diagnostics');
define('_MENULEFT_DIAGLOGS','Logs');
define('_MENULEFT_DIAGINF','Information');
define('_MENULEFT_DIAGPINGTRACE','Ping/Traceroute');
define('_MENULEFT_DIAGARPTAB','ARP table');
define('_MENULEFT_DIAGEXEC','Command');
define('_MENULEFT_CMDBACKUPRESTORE','Backup/Restore');
define('_MENULEFT_CMDFACTORYDEF','Factory defaults');
define('_MENULEFT_CMDREBOOTSYS','Reboot system');
define('_MENULEFT_CMDSHUTDOWNSYS','Shutdown system');


// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION',_MENULEFT_SYSGENSETUP);
define('_SYSTEMPHP_HOSTNAME','Hostname');
define('_SYSTEMPHP_HOSTNAMETEXT','name of the NAS host, without domain part<br>e.g. <em>nas');
define('_SYSTEMPHP_DOMAIN','Domain');
define('_SYSTEMPHP_DOMAINTEXT','e.g. <em>mycorp.com</em>');
define('_SYSTEMPHP_DNS','DNS servers');
define('_SYSTEMPHP_DNSTEXT','IP addresses<br>');
define('_SYSTEMPHP_USERNAME','Username');
define('_SYSTEMPHP_USERNAMETEXT','If you want to change the username for accessing the webGUI, enter it here.');
define('_SYSTEMPHP_PASSWORD','Password');
define('_SYSTEMPHP_PASSWORDTEXT','If you want to change the password for accessing the webGUI, enter it here twice.<br>Don\'t use the character :');
define('_SYSTEMPHP_WEBGUIPROTOCOL','WebGUI protocol');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','');
define('_SYSTEMPHP_WEBGUIPORT','WebGUI port');
define('_SYSTEMPHP_WEBGUIPORTTEXT','Enter a custom port number for the webGUI above if you want to override the default (80 for HTTP, 443 for HTTPS).');
define('_SYSTEMPHP_LANGUAGE','Language');
define('_SYSTEMPHP_LANGUAGETEXT','Select the system language');
define('_SYSTEMPHP_TIMEZONE','Time zone');
define('_SYSTEMPHP_TIMEZONETEXT','Select the location closest to you');
define('_SYSTEMPHP_TIMEUPDATEINT','Time update interval');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','Minutes between network time sync.; 300	recommended, or 0 to disable');
define('_SYSTEMPHP_NTPSERVER','NTP time server');
define('_SYSTEMPHP_NTPSERVERTEXT','Use a space to separate multiple hosts (only one required). Remember to set 				  up at least one DNS server if you enter a host name here!');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','System information');
define('_INDEXPHP_NAME','Name');
define('_INDEXPHP_VERSION','Version');
define('_INDEXPHP_OSVERSION','OS Version');
define('_INDEXPHP_PLATFORM','Platform');
define('_INDEXPHP_DATE','Date');
define('_INDEXPHP_UPTIME','Uptime');
define('_INDEXPHP_LASTCHANGE','Last config change');
define('_INDEXPHP_MEMUSE','Memory usage');
define('_INDEXPHP_LOADAVERAGE','Load averages');
define('_INDEXPHP_SHOWPROCESSING','show process information');

// Status Processes Module
// file: status_processes.php
define('_STATSPROCPHP_NAME',_STATUS);
define('_STATSPROCPHP_NAME_DESCRIPTION','Processes');
define('_STATSPROCPHP_PROC_INF','Processes information');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION',_MENULEFT_SYSSTATROUTES);
define('_SYSTEMROUTESPHP_MSG_CHANGED','The static route configuration has been changed.<br>You must apply the changes in order for them to take effect.');
define('_SYSTEMROUTESPHP_INT','Interface');
define('_SYSTEMROUTESPHP_NET','Network');
define('_SYSTEMROUTESPHP_GW','Gateway');
define('_SYSTEMROUTESPHP_DESC',_DESC);
define('_SYSTEMROUTESPHP_EDITROUTE','Edit Route');
define('_SYSTEMROUTESPHP_MSGCONFIRM','Do you really want to delete this route?');
define('_SYSTEMROUTESPHP_DELROUTE','Delete Route');
define('_SYSTEMROUTESPHP_ADDROUTE','Add Route');

// Static Routes Edit Module
// file: system_routes_edit.php
define('_SYSTEMROUTESPHP_MSGVALID','A valid destination network must be specified.');
define('_SYSTEMROUTESPHP_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SYSTEMROUTESPHP_MSGVALIDGW','A valid gateway IP address must be specified.');
define('_SYSTEMROUTESPHP_MSGROUTEEXIST','A route to this destination network already exists.');
define('_SYSTEMROUTESPHP_CHOOSEINT','Choose which interface this route applies to.');
define('_SYSTEMROUTESPHP_DESTNET','Destination network');
define('_SYSTEMROUTESPHP_DESTNETROUTE','Destination network for this static route');
define('_SYSTEMROUTESPHP_GWTONET','Gateway to be used to reach the destination network');
define('_SYSTEMROUTESPHP_DESCREF','You may enter a description here for your reference (not parsed).');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMFIRMWAREPHP_NAMEDESC',_MENULEFT_SYSFIRMWARE);
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','The uploaded image file is not for this platform');
define('_SYSTEMFIRMWAREPHP_OUTMEM','Image upload failed (out of memory?)');
define('_SYSTEMFIRMWAREPHP_INVSIG','The digital signature on this image is invalid.');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','This image is not digitally signed.');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','There has been an error verifying the signature on this image.');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','The image file is corrupt');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','The firmware is now being installed. FreeNAS will reboot automatically.');
define('_SYSTEMFIRMWAREPHP_NOTSUP','Firmware uploading is not supported on this platform.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','This means that the image you uploaded ' .
		 	'is not an official/supported image and may lead to unexpected behavior or security ' .
			'compromises. Only install images that come from sources that you trust, and make sure '.
			'that the image has not been tampered with.<br><br>'.
			'Do you want to install this image anyway (on your own risk)?');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','Click &quot;Enable firmware 
          upload&quot; below, then choose the image file '.$g['fullplatform'].'-*.img)
			 to be uploaded.<br>Click &quot;Upgrade firmware&quot; 
          to start the upgrade process.');			
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','Enable firmware upload');	
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','Disable firmware upload');		
define('_SYSTEMFIRMWAREPHP_FIRMFILE','Firmware image file');	
define('_SYSTEMFIRMWAREPHP_UPFIRM','Upgrade firmware');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','You must reboot the system before you can upgrade the firmware.');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','DO NOT abort the firmware upgrade once it 
                    has started. FreeNAS will reboot automatically after 
                    storing the new firmware. The configuration will be maintained.<br>
                    You need a minium of 80 Mb RAM for firmware update.');


// System Advanced Module
// file: system_advanced.php
define('_SYSTEMADVANCEDPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMADVANCEDPHP_NAMEDESC',_MENULEFT_SYSADVANCED);
define('_SYSTEMADVANCEDPHP_MSGVALIDTCP','The TCP idle timeout must be an integer.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERTKEY','Certificate and key must always be specified together.');
define('_SYSTEMADVANCEDPHP_MSGVALIDCERT','This certificate does not appear to be valid.');
define('_SYSTEMADVANCEDPHP_MSGVALIDKEY','This key does not appear to be valid.');
define('_SYSTEMADVANCEDPHP_NOTE','<strong>Note: </strong></span>the 
              options on this page are intended for use by advanced users only, 
              and there\'s <strong>NO</strong> support for them.');
define('_SYSTEMADVANCEDPHP_WEBGUISSLKEY','webGUI SSL certificate\/key');
define('_SYSTEMADVANCEDPHP_WEBCERT','Certificate');
define('_SYSTEMADVANCEDPHP_WEBCERTTEXT','Paste a signed certificate in X.509 PEM format here.');
define('_SYSTEMADVANCEDPHP_WEBKEY','Key');
define('_SYSTEMADVANCEDPHP_WEBKEYTEXT','Paste an RSA private key in PEM format here.');
define('_SYSTEMADVANCEDPHP_MISC','Miscellaneous');
define('_SYSTEMADVANCEDPHP_CON','Console menu');
define('_SYSTEMADVANCEDPHP_DISCON','Disable console menu');
define('_SYSTEMADVANCEDPHP_CONTEXT','Changes to this option will take effect after a reboot.');
define('_SYSTEMADVANCEDPHP_FIRM','Firmware version check');
define('_SYSTEMADVANCEDPHP_DISFIRM','Disable firmware version check');
define('_SYSTEMADVANCEDPHP_FIRMTEXT','This will cause FreeNAS not to check for newer firmware versions when the <a href=\"system_firmware.php\">System: Firmware</a> page is viewed.');
define('_SYSTEMADVANCEDPHP_TCPIDLE','TCP idle timeout');
define('_SYSTEMADVANCEDPHP_TCPIDLETEXT','Idle TCP connections will be removed from the state table after no packets have been received for the specified number of seconds. Don\'t set this too high or your state table could become full of connections that have been improperly shut down. The default is 2.5 hours.');
define('_SYSTEMADVANCEDPHP_NAV','Navigation');
define('_SYSTEMADVANCEDPHP_NAVTEXT','Keep diagnostics in navigation expanded');
define('_SYSTEMADVANCEDPHP_BEEP','System Beep');
define('_SYSTEMADVANCEDPHP_DISBEEP','Disable speaker beep on startup and shutdown');
define('_SYSTEMADVANCEDPHP_SMART','S.M.A.R.T Daemon');
define('_SYSTEMADVANCEDPHP_ENSMART','Enable the S.M.A.R.T daemon');
define('_SYSTEMADVANCEDPHP_SMARTTEXT','Monitor the S.M.A.R.T device by logging their status in the log file.');
define('_SYSTEMADVANCEDPHP_TUNING','Tuning');
define('_SYSTEMADVANCEDPHP_ENTUNING','Enable tuning of some kernel variable');
define('_SYSTEMADVANCEDPHP_ZEROCONF','ZeroConf');
define('_SYSTEMADVANCEDPHP_DISZEROCONF','Disable ZeroConf service announce');

// Interface assing:
// file: interfaces_assign.php
define('_INTASSIGNPHP_NAME','Interfaces');
define('_INTASSIGNPHP_NAMEDESC','Assign network ports');
define('_INTASSIGNPHP_MSGVALIDPORT','Port ');
define('_INTASSIGNPHP_MSGVALIDPORTWASASSIG',' was assigned to ');
define('_INTASSIGNPHP_MSGVALIDINT',' interfaces:');
define('_INTASSIGNPHP_INT','Interface');
define('_INTASSIGNPHP_PORT','Network port');
define('_INTASSIGNPHP_DELINT','delete interface');
define('_INTASSIGNPHP_ADDINT','add interface');
define('_INTASSIGNPHP_INTTEXT','After you click &quot;Save&quot;, you must reboot FreeNAS to make the changes take effect. You may also have to do one or more of the following steps before you can access your NAS again: </span></p>
<ul>
  <li><span class=\"vexpl\">change the IP address of your computer</span></li>
  <li><span class=\"vexpl\">access the webGUI with the new IP address</span></li>
</ul>');

// Interface LAN/OPT:
// file: interfaces_lan.php
// file: interfaces_opt.php
define('_INTLANPHP_NAME','Interfaces');
define('_INTLANPHP_NAMEDESC','LAN');
define('_INTPHP_MSGVALIDIP',_MSGVALIDIP);
define('_INTPHP_MSGVALIDMASK',_MSGVALIDNETBIT);
define('_INTPHP_MSGVALIDGW','A valid gateway must be specified.');
define('_INTPHP_MSGVALIDMTU','A valid mtu size must be specified.');
define('_INTPHP_IP','IP address');
define('_INTPHP_GW','Gateway');
define('_INTPHP_MTU','MTU');
define('_INTPHP_MTUTEXT','Standard MTU is 1500, use 9000 for jumbo frame.');
define('_INTPHP_POLLING','Device polling');
define('_INTPHP_ENPOLLING','Enable device polling');
define('_INTPHP_POLLINGTEXT','Device polling is a technique that lets the system periodically poll network devices for new
					data instead of relying on interrupts. This can reduce CPU load and therefore increase
					throughput, at the expense of a slightly higher forwarding delay (the devices are polled 1000 times
					per second). Not all NICs support polling; see the m0n0wall homepage for a list of supported cards.');
define('_INTPHP_SPEED','Speed');
define('_INTPHP_DUPLEX','Duplex');
define('_INTPHP_TEXT','after you click &quot;Save&quot;, you may also 
                    have to do one or more of the following steps before you can 
                    access FreeNAS again: 
                    <ul>
                      <li>change the IP address of your computer</li>
                      <li>access the webGUI with the new IP address</li>
                    </ul>');
define('_INTPHP_ENOPT','Enable Optional ');
define('_INTPHP_ENOPTINT',' interface');
define('_INTPHP_DESC',_DESC);
define('_INTPHP_DESCTEXT','Enter a description (name) for the interface here.');
define('_INTPHP_STATIC','Static IP configuration');
define('_INTPHP_DHCP','DHCP client configuration');
define('_INTPHP_GENERAL','General configuration');
define('_INTPHP_DHCPHOSTNAME','Hostname');
define('_INTPHP_DHCPHOSTNAMETEXT','The value in this field is sent as the DHCP hostname when requesting a DHCP lease.');

// Disks: Manage
// Common text
define('_DISKSPHP_NAME',_DISKS);
define('_DISKSPHP_MANAGE','Manage');
define('_DISKSPHP_FORMAT','Format');
define('_DISKSPHP_ISCSIINIT','iSCSI initiator');
define('_DISKSPHP_DISK',_DISK);
define('_DISKSPHP_FILESYSTEM',_FILESYSTEM);

// file: disks_manage.php

define('_DISKSMANAGEPHP_NAMEDESC','Management');
define('_DISKSMANAGEPHP_MSGCHANGED','The disk list has been changed.<br>You must apply the changes in order for them to take effect.');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','Standby time');
define('_DISKSMANAGEPHP_FSUNKNOWN','unknown or unformated');
define('_DISKSMANAGEPHP_EDIT','edit disk');
define('_DISKSMANAGEPHP_DEL','delete disk');
define('_DISKSMANAGEPHP_DELCONF','Do you really want to delete this disk? All elements that still use it will become invalid (e.g. share)!');
define('_DISKSMANAGEPHP_ADD','add disk');
define('_DISKSMANAGEPHP_NOTE','<strong>Note:<br>
                </strong>First configuration step: Add your hardrive to the disk list.');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','This disk already exists in the disk list.');
define('_DISKSMANAGEEDITPHP_UDMA','UDMA mode');
define('_DISKSMANAGEEDITPHP_UDMATEXT','You can force UDMA mode if you have \"UDMA_ERROR.... LBA\" message with your hard drive.');
define('_DISKSMANAGEEDITPHP_STANDBY','Hard disk standby time');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','Puts the hard disk into standby mode when the selected amount of time after the last
                    access has elapsed. <em>Do not set this for CF cards.</em>');
define('_DISKSMANAGEEDITPHP_APM','Advanced Power Management');
define('_DISKSMANAGEEDITPHP_APMTEXT','This allows  you  to lower the power consumption of the drive, at the expense of performance.<em>Do not set this for CF cards.</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','acoustic level');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','This allows you to set how loud the drive is while it\'s  operating.<em>Do not set this for CF cards.</em>');
define('_DISKSMANAGEEDITPHP_PREFS','preformated FS');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','This allows you to set FS type for preformated disk with data.<br>
                     <em>Leave "unformated" for unformated disk and then use Format menu.</em>');

//file: disks_manage_init.php

define('_DISKSMANAGEINITPHP_NAMEDESC','Initialize');
define('_DISKSMANAGEINITPHP_INITTEXT','<strong>Disk initialization details:</strong>');
define('_DISKSMANAGEINITPHP_INITINFO','\"fdisk: Geom not found\"is not an error message!\n');
define('_DISKSMANAGEINITPHP_TEXT','This step will erase all your partition, create partition number 1 and format the hard drive with the file system specified.');

//file: disks_manage_iscsi.php
define('_DISKSMANAGEPHP_NAMEDESC','Management');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','iSCSI Initiator');
define('_DISKSMANAGEISCSIPHP_TARGETIP','Target IP address');
define('_DISKSMANAGEISCSIPHP_TARGETIPTEXT','IP address of the iSCSI target.');
define('_DISKSMANAGEISCSIPHP_TARGETNAME','targetname');
define('_DISKSMANAGEISCSIPHP_TARGETNAMETEXT','targetname.');

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
define('_DISKSRAIDPHP_UNSTABLE','(unstable)');


//file: disks_raid.php

define('_DISKSRAIDPHP_NAMEDESC','RAID');
define('_DISKSRAIDPHP_MSGCHANGED','The Raid configuration has been changed.<br>You must apply the changes in order for them to take effect.');
define('_DISKSRAIDPHP_TYPE','Type');
define('_DISKSRAIDPHP_EDITRAID','edit raid');
define('_DISKSRAIDPHP_DELRAID','delete raid');
define('_DISKSRAIDPHP_DELCONF','Do you really want to delete this raid volume? All elements that still use it will become invalid (e.g. share)!');
define('_DISKSRAIDPHP_NOTE','Note:<br>
                </strong></span>Optional configuration step: Configuring a virtual RAID disk using your<br>
                <a href=\"disks_manage.php\">previsously configured disk</a>.<br>Wait for the \"up\" status before format it and mount it!.');

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

// disks_mount.php

define('_DISKSMOUNTPHP_MSGCHANGED','The mount point list has been changed.<br>You must apply the changes in order for them to take effect.');
define('_DISKSMOUNTPHP_NOTE','Note:<br>
                </strong></span>Second configuration step: Declaring the filesystem used by your<br>
                <a href="disks_manage.php">previously configured disk');
define('_DISKSMOUNTPHP_DELCONF','Do you really want to delete this mount point? All elements that still use it will become invalid (e.g. share)!');
define('_DISKSMOUNTPHP_DEL','delete mount');
define('_DISKSMOUNTPHP_ADD','add mount');

// disks_mount_edit.php

define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','The share name may only consist of the characters a-z, A-Z, 0-9, _ , -.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','The description name contain invalid characters.');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','Can\'t mount the system partition 1, the DATA partition is the 2.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','Duplicate Share Name.');
define('_DISKSMOUNTEDITPHP_TEXT','1. You can\'t mount the partition \'<?php echo htmlspecialchars($cfdevice);?>\' where the config file is stored<br>2. FreeBSD NTFS has lots of bugs.');

// Service: CIFS/SAMBA
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
define('_SRVCIFS_HIDE','Share to hide.');
define('_SRVCIFS_HIDETEXT','Select the CIFS share to hide from browsing.');
define('_SRVCIFS_SNBBUFF','Send Buffer Size');
define('_SRVCIFS_SNBBUFFTEXT','Size of send buffer (16384 by default).');
define('_SRVCIFS_RCVBUFF','Receive Buffer Size');
define('_SRVCIFS_RCVBUFFTEXT','Size of receive buffer (16384 by default).');

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
define('_SRVFTP_MAXIP','Max conn per ip');
define('_SRVFTP_MAXIPTEXT','Maximum connection per IP address.');
define('_SRVFTP_TIMEOUT','Timeout');
define('_SRVFTP_TIMEOUTTEXT','Maximum idle time in seconds.');
define('_SRVFTP_ANONYMOUS','Anonymous login');
define('_SRVFTP_ENANONYMOUS','Enable Anonymous login');
define('_SRVFTP_AUTH','Local User');
define('_SRVFTP_ENAUTH','Enable local User login');
define('_SRVFTP_FXP','FXP');
define('_SRVFTP_ENFXP','Enable FXP protocol.');
define('_SRVFTP_FXPTEXT','FXP allows transfers between two remote servers without any file data going to the client asking for the transfer (insecure!).');
define('_SRVFTP_NATMODE','NAT mode');
define('_SRVFTP_ENNATMODE','Force NAT mode');
define('_SRVFTP_NATMODETEXT','Enable it if your FTP server is behind a NAT box that doesn\'t support applicative FTP proxying');
define('_SRVFTP_PASSIVEIP','Passive IP address');
define('_SRVFTP_PASSIVEIPTEXT','Use this option to override the IP address that FTP daemon will advertise in response to the PASV command.');
define('_SRVFTP_PASVMIN','pasv_min_port');
define('_SRVFTP_PASVMINTEXT','The minimum port to allocate for PASV style data connections.');
define('_SRVFTP_PASVMAX','pasv_max_port');
define('_SRVFTP_PASVMAXTEXT','The maximum port to allocate for PASV style data connections.');
define('_SRVFTP_BANNER','Banner');
define('_SRVFTP_BANNERTEXT','Greeting banner displayed by FTP when a connection first comes in.');

// Service: NFS
// file: services_nfs.php

define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','A valid network must be specified.');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','NFS Server');
define('_SRVNFS_MAPALLROOT','map all user to root');
define('_SRVNFS_MAPALLROOTTEXT','All users will have the root privilege.');
define('_SRVNFS_AUTHNET','Authorised network');
define('_SRVNFS_AUTHNETTEXT','Network that is authorised to access to NFS share');
define('_SRVNFS_TEXT','The name of the exported directories are : /mnt/sharename');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','Server');
define('_SRVRYNC_CLIENT','Client');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','Rsync Daemon');
define('_SRVRYNCD_READONLY','Read only');
define('_SRVRYNCD_MAPUSER','Map to user');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_MAXCON','Maximum connections');
define('_SRVRYNCD_MAXCONTEXT','Maximum number of simultaneous connections. Default is 0 (unlimited)');
define('_SRVRYNCD_MOTD','MOTD');
define('_SRVFTP_MOTDTEXT','message of the day');
define('_SRVRYNCD_TCPORTTEXT','Alternate TCP port.');

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

// Service: SSHD
// file: services_sshd.php

define('_SRVSSHD_NAMEDESC','SSHD');
define('_SRVSSHD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVSSHD_MSGVALIDKEY','This key does not appear to be valid.');
define('_SRVSSHD_SSHD','SSH Daemon');
define('_SRVSSHD_READONLY','Read only');
define('_SRVSSHD_TCPORT',_TCPPORT);
define('_SRVSSHD_KEY','SSH key');
define('_SRVSSHD_PRIVATEKEY','Private Key');
define('_SRVSSHD_PRIVATEKEYTEXT','Paste a DSA PRIVATE KEY in PEM format here.');

// Service: AFP
// file: services_afp.php

define('_SRVAFP_NAMEDESC','AFP');
define('_SRVAFP_MSGVALIDAUTH','You must select at least one authentication method.');
define('_SRVAFP_AFPSRV','AFP Server');
define('_SRVAFP_AFPNAME','Server Name');
define('_SRVAFP_AUTH','Authentication');
define('_SRVAFP_ENABLEGUEST','Enable guest access.');
define('_SRVAFP_ENABLELOCAL','Enable local user authentication.');

// Access
// common words

define('_ACCESS_NAME','Access');
define('_ACCESS_USER','User');
define('_ACCESS_USERS','Users');
define('_ACCESS_GROUP','Group');
define('_ACCESS_GROUPS','Groups');
define('_ACCESS_FULLNAME','Full Name');

// file: access_users.php

define('_ACCESSUSERS_MSGCHANGED','The User list has been changed.<br>You must apply the changes in order for them to take effect.');
define('_ACCESSUSERS_EDITUSER','edit user');
define('_ACCESSUSERS_DELUSER','delete user');
define('_ACCESSUSERS_CONFDEL','Do you really want to delete this user?!');
define('_ACCESSUSERS_ADDUSER','add user');

// file: access_users_edit.php

define('_ACCESSUSERSEDIT_MSGGROUPFIRST','you must create a group first.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGIN','The login name contains invalid characters.');
define('_ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED','The login name is a reserved login name.');
define('_ACCESSUSERSEDIT_MSGVALIDFULLNAME','The full name contains invalid characters.');
define('_ACCESSUSERSEDIT_MSGVALIDDUPLICATE','This user already exists in the user list.');
define('_ACCESSUSERSEDIT_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSUSERSEDIT_LOGIN',_LOGIN);
define('_ACCESSUSERSEDIT_LOGINTEXT','Unique login name of user.');
define('_ACCESSUSERSEDIT_FULLNAME',_ACCESS_FULLNAME);
define('_ACCESSUSERSEDIT_FULLNAMETEXT','User full name.');
define('_ACCESSUSERSEDIT_PASSWORD','Password');
define('_ACCESSUSERSEDIT_PASSWORDTEXT','User password.');
define('_ACCESSUSERSEDIT_PASSWORDCONF','Confirm Password');
define('_ACCESSUSERSEDIT_PASSWORDCONFTEXT','Confirm the password.');
define('_ACCESSUSERSEDIT_GROUPMEMBER','Group Member');
define('_ACCESSUSERSEDIT_FULLSHELL','Full Shell');
define('_ACCESSUSERSEDIT_FULLSHELLTEXT','Give full shell to user');

// file: access_users_groups.php

define('_ACCESSGROUPS_MSGCHANGED','The group list has been changed.<br>You must apply the changes in order for them to take effect.');
define('_ACCESSGROUPS_DESC',_DESC);
define('_ACCESSGROUPS_GROUPEDIT','edit group');
define('_ACCESSGROUPS_GROUPDEL','delete group');
define('_ACCESSGROUPS_GROUPDELCONF','Do you really want to delete this group?!');
define('_ACCESSGROUPS_GROUPADD','add group');

// file: access_users_groups_edit.php

define('_ACCESSGROUPSEDIT_MSGVALIDNAME','The Group name contains invalid characters.');
define('_ACCESSGROUPSEDIT_MSGVALIDDESC','The Group desc contains invalid characters.');
define('_ACCESSGROUPSEDIT_MSGVALIDDUPLICATE','This group already exists in the group list.');
define('_ACCESSGROUPSEDIT_NAME','Name');
define('_ACCESSGROUPSEDIT_NAMETEXT','Group name.');
define('_ACCESSGROUPSEDIT_DESC',_DESC);
define('_ACCESSGROUPSEDIT_DESCTEXT','Group description.');

// file: access_ad.php

define('_ACCESSAD_NAME','Active Directory');
define('_ACCESSAD_MSGVALIDIP',_MSGVALIDIP);
define('_ACCESSAD_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSAD_ADNAME','AD server name');
define('_ACCESSAD_ADNAMETEXT','AD or PDC name.');
define('_ACCESSAD_IP','AD server IP');
define('_ACCESSAD_IPTEXT','IP address of MS Active Directory server.');
define('_ACCESSAD_DOMAIN','Domain name');
define('_ACCESSAD_DOMAINTEXT','Domain name in old format.');
define('_ACCESSAD_ADMINAME','Administrator name');
define('_ACCESSAD_ADMINNAMETEXT','Username of a domain administrator account.');
define('_ACCESSAD_ADMINPASS','Administration password');
define('_ACCESSAD_ADMINPASSTEXT','Password of domain administrator account, enter it here twice.');

// Diagnostics

define('_DIAG_NAME','Diagnostics');

// file: shutdown.php

define('_SHUT_NAME','Shutdown');
define('_SHUT_MSGSHUT','The system is halting now. This may take one minute.');
define('_SHUT_CONF','Are you sure you want to shutdown the system?');

// file: shutdown_sched.php

define('_SHUTSCHED_NAME','Scheduled shutdown');
define('_SHUTSCHED_TIME','Shutdown Time');
define('_SHUTSCHED_TEXT',_MSGSCHED_TEXT);

?>
