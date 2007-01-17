<?php 
/*
	lang-SimplifiedChinese.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2006-2007 Baikuan Hsu <chicworks@gmail.com>
	All rights reserved.
	
	This language file works perfectly on FreeNAS Ver.0.683B
	
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
define('_CHARSET','UTF8');
define('_LOCALE','zh_CN.UTF8');
define('_LOCALEWIN', 'UTF8');

// date and time defines
define('_DATE','日期');
define('_DATEBRIEF','%b %d, %Y');
define('_DATELONG','%A, %B %d, %Y');
define('_DATESTRING','%A, %B %d @ %H:%M:%S');
define('_DATETIMEBRIEF','%b %d, %Y - %I:%M %p');
define('_DATETIMELONG','%A, %B %d, %Y - %I:%M %p');
define('_DAY_OF_WEEK_LONG','星期日 星期一 星期二 星期三 星期四 星期五 星期六');
define('_DAY_OF_WEEK_SHORT','周日 周一 周二 周三 周四 周五 周六');
define('_MONTH_LONG','一月 二月 三月 四月 五月 六月 七月 八月 九月 十月 十一月 十二月');
define('_MONTH_SHORT','一月 二月 三月 四月 五月 六月 七月 八月 九月 十月 十一月 十二月');
define('_TIME', '时间');
define('_TIMEBRIEF','%I:%M %p');
define('_SECOND', '秒');
define('_SECONDS', '秒');
define('_MINUTE', '分');
define('_MINUTES', '分');
define('_HOUR', '小时');
define('_HOURS', '小时');
define('_DAY', '天');
define('_DAYS', '天');
define('_WEEK', '周');
define('_WEEKS', '周');
define('_WEEKDAYS', '平常日');
define('_MONTH', '月');
define('_MONTHS', '月');
define('_YEAR', '年');
define('_YEARS', '年');

// common words
define('_ALL','全部');
define('_AND','以及');
define('_BY','by');
define('_DOWN','下');
define('_NO','否');
define('_OF','关');
define('_OK','OK');
define('_ON','开');
define('_TO','To');
define('_THE', 'The');
define('_UP','上');
define('_URL', 'URL');
define('_YES','是');

// common actions
define('_ACTIVATE','啟始');
define('_ADD','增加');
define('_CANCEL', '取消');
define('_COMMIT', '更新');
define('_CREATE', '建立');
define('_DEACTIVATE','停止');
define('_DELETE','刪除');
define('_EDIT','编辑');
define('_ENABLE','啟动');
define('_HELP', '說明');
define('_IGNORE','忽略');
define('_LOGIN','登入');
define('_LOGOUT','登出');
define('_MODIFY','修改');
define('_NEW','新增');
define('_SEARCH', '搜寻');
define('_SAVE', '储存');
define('_SUBMIT','传送');
define('_UPDATE', '更新');
define('_VIEW', '检视');
define('_APPLY', '套用');
define('_WARNING', '警告');
define('_LOAD', '载入');
define('_RETRY', '重试');

//FreeNAS common words
define('_SIZE', '大小');
define('_STATUS', '状态');
define('_TYPE', '类別');
define('_CONFIGURING', '设定中');
define('_DISK', '磁碟');
define('_DISKS', '磁碟');
define('_PARTITION', '磁区');
define('_FILESYSTEM', '档案系统');
define('_DESC', '描述');
define('_SOFTRAID', '软体RAID');
define('_SERVICE','服务');
define('_SERVICES','服务');
define('_TCPPORT','TCP port');
define('_CONFIRMATION','确认');
define('_NOTE','提示');
define('_SUCCESSFUL','成功');
define('_FAILED','失败');
define('_ERROR','错误');
define('_HINT','提示');
define('_FORMAT','格式化');

// common messages
define('_MESSAGES_SUCCESS', '变更套用成功。');
define('_MESSAGES_SUCCESSWITHREBOOT', '变更已储存，必须<a href=reboot.php>重新开机</a>之后才会生效');
define('_MESSAGES_ERROR', '错误：变更无法套用 (error code '.$ok.').');
define('_MSGVALIDIP','请输入一个有效的IP位置。');
define('_MSGVALIDNETBIT','请输入一个有效的network bit count。');
define('_MSGVALIDTCPPORT','请输入一个有效的连接埠。');
define('_MSGVALIDPASSWORD','密码不相同');

define('_MSGSCHED_TEXT','提示：使用Ctrl+滑鼠左键(Mac：command+滑鼠主键)就可以复选项目。');

// Special Filesystem Warning

define('_MSGFILESYSTEM','UFS档案系统格式是 FreeBSD 原生档案格式 (FreeNAS的OS)。 如果使用FAT, FAT32, EXT2,
EXT3, 或是 NTFS 可能会导致不可预期的错误！');

// states
define('_ACTIVE','运作中');
define('_INACTIVE','停止中');
define('_SELECTED','已选择');

// Webgui layout
define('_MENULEFT_SYSTEM','系统');
define('_MENULEFT_SYSGENSETUP','设定');
define('_MENULEFT_SYSSTATROUTES','靜态路由');
define('_MENULEFT_SYSHOSTS','主机');
define('_MENULEFT_SYSFIRMWARE','韧体');
define('_MENULEFT_SYSADVANCED','进阶');
define('_MENULEFT_INTERFACES','网路介面');
define('_MENULEFT_INTASSIGN','指派');
define('_MENULEFT_INTLAN','区域网路');
define('_MENULEFT_DISKS',_DISKS);
define('_MENULEFT_DISKMANAG','管理');
define('_MENULEFT_DISKMNGSOFTRAID',_SOFTRAID);
define('_MENULEFT_DISKMNGFORMAT',_FORMAT);
define('_MENULEFT_DISKMNGMOUNTPOINT','掛载点');
define('_MENULEFT_SERVICES','服务');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVUNSN','Unison');
define('_MENULEFT_SRVUPNP','UPnP');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','存取权限');
define('_MENULEFT_ACUSERGROUPS','使用者/群组');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS',_STATUS);
define('_MENULEFT_STSYSTEM','系统');
define('_MENULEFT_STPROCESS','处理程序');
define('_MENULEFT_STINTERFACES','网路介面');
define('_MENULEFT_STDISKS',_DISKS);
define('_MENULEFT_STWIRELESS','无線网路');
define('_MENULEFT_EXTENSIONS','扩充');
define('_MENULEFT_DIAGNOSTICS','诊断');
define('_MENULEFT_DIAGLOGS','记錄档');
define('_MENULEFT_DIAGINF','资讯');
define('_MENULEFT_DIAGPINGTRACE','Ping/Traceroute');
define('_MENULEFT_DIAGARPTAB','ARP table');
define('_MENULEFT_DIAGEXEC','命令列');
define('_MENULEFT_DIAGEDITFILE','编辑档案');
define('_MENULEFT_CMDBACKUPRESTORE','备份/还原');
define('_MENULEFT_CMDFACTORYDEF','回到原始预设值');
define('_MENULEFT_CMDREBOOTSYS','重新开机');
define('_MENULEFT_CMDSHUTDOWNSYS','关机');
define('_MENULEFT_GRAPH','图表');

// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION',_MENULEFT_SYSGENSETUP);
define('_SYSTEMPHP_HOSTNAME','主机名称');
define('_SYSTEMPHP_HOSTNAMETEXT','NAS的主机名称，不包含网域<br>例如: <em>nas');
define('_SYSTEMPHP_DOMAIN','网域');
define('_SYSTEMPHP_DOMAINTEXT','例： <em>mycompany.com</em>');
define('_SYSTEMPHP_DNS','DNS伺服器');
define('_SYSTEMPHP_DNSTEXT','请输入IP位置');
define('_SYSTEMPHP_USERNAME','帐号');
define('_SYSTEMPHP_USERNAMETEXT','变更网页介面的帐号。');
define('_SYSTEMPHP_PASSWORD','密码');
define('_SYSTEMPHP_PASSWORDTEXT','变更网页介面的密码（两次）<br>不要使用以下的字元 \':\'。');
define('_SYSTEMPHP_WEBGUIPROTOCOL','网页介面协定');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','HTTPS是加密过的连線');
define('_SYSTEMPHP_WEBGUIPORT','网页介面连接埠');
define('_SYSTEMPHP_WEBGUIPORTTEXT','输入自行设定的网页管理介面连接埠(80: HTTP, 443: HTTPS)。');
define('_SYSTEMPHP_LANGUAGE','Language/语言设定');
define('_SYSTEMPHP_LANGUAGETEXT','选择网页管理介面显示语言');
define('_SYSTEMPHP_TIMEZONE','时区');
define('_SYSTEMPHP_TIMEZONETEXT','选择最邻近的区域。');
define('_SYSTEMPHP_TIMEUPDATEINT','时间同步间隔');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','多少分钟与时间伺服器同步一次。建议设为300分钟，输入0则可以关闭同步功能');
define('_SYSTEMPHP_NTPSERVER','NTP时间伺服器');
define('_SYSTEMPHP_NTPSERVERTEXT','使用空白字元来分隔多个主机位置(一个空白即可)，这个服务需要先设定DNS伺服器');
define('_SYSTEMPHP_MSGHOSTNAME','Hostname只能包含以下字元 a-z, 0-9, \'-\'。');
define('_SYSTEMPHP_MSGDOMAIN','网域只能包含以下字元 a-z, 0-9, \'-\', \'.\'。');
define('_SYSTEMPHP_MSGDNS','主要/次要DNS伺服器必须指定一个有效的IP。');
define('_SYSTEMPHP_MSGUSERNAME','帐号只能包含以下字元 a-z, A-Z, 0-9。');
define('_SYSTEMPHP_MSGWEBGUIPORT','网页介面必须指定一个有效的TCP/IP连接埠。');
define('_SYSTEMPHP_MSGPASSWORD','您输入的密码不相符。');
define('_SYSTEMPHP_MSGLANGUAGE','必须选择一个有效的语言。');
define('_SYSTEMPHP_MSGTIMEUPDATEINTERVAL','将时间同步间隔设定只接受6-1440的范园，或者输入0将其关闭。');
define('_SYSTEMPHP_MSGTIMESERVERS','NTP时间伺服器只能包含以下字元a-z, 0-9, \'-\', \'.\'。');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','系统资讯');
define('_INDEXPHP_NAME','名称');
define('_INDEXPHP_VERSION','版本');
define('_INDEXPHP_OSVERSION','作业系统版本');
define('_INDEXPHP_PLATFORM','硬体平台');
define('_INDEXPHP_DATE','日期');
define('_INDEXPHP_UPTIME','系统运作时间');
define('_INDEXPHP_LASTCHANGE','上次设定时间');
define('_INDEXPHP_MEMUSE','记忆体使用率');
define('_INDEXPHP_LOADAVERAGE','系统负载');
define('_INDEXPHP_SHOWPROCESSING','显示处理程序');
define('_INDEXPHP_DISKS_USE','磁碟空间');
define('_INDEXPHP_NODISK','尚未加入磁碟');

// Status Processes Module
// file: status_process.php
define('_STATSPROCPHP_NAME',_STATUS);
define('_STATSPROCPHP_NAME_DESCRIPTION','程序列表');
define('_STATSPROCPHP_PROC_INF','程序资讯');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION',_MENULEFT_SYSSTATROUTES);
define('_SYSTEMROUTESPHP_MSG_CHANGED','靜态路由已经变更。<br>套用之后才会生效。');
define('_SYSTEMROUTESPHP_INT','网路介面卡');
define('_SYSTEMROUTESPHP_NET','目标网段');
define('_SYSTEMROUTESPHP_GW','闸道');
define('_SYSTEMROUTESPHP_DESC',_DESC);
define('_SYSTEMROUTESPHP_EDITROUTE','编辑路由');
define('_SYSTEMROUTESPHP_MSGCONFIRM','确定要刪除这个路由资讯吗？');
define('_SYSTEMROUTESPHP_DELROUTE','刪除路由');
define('_SYSTEMROUTESPHP_ADDROUTE','新增路由');
define('_SYSTEMROUTESPHP_TEXT','设定一个主机名称对NFS服务将有所帮助。');

// Static Routes Edit Module
// file: system_routes_edit.php
define('_SYSTEMROUTESPHP_MSGVALID','请输入一个有效的目标网段。');
define('_SYSTEMROUTESPHP_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SYSTEMROUTESPHP_MSGVALIDGW','请输入一个有效的闸道IP位置。');
define('_SYSTEMROUTESPHP_MSGROUTEEXIST','已经有一个目标网段的路由设定存在了。');
define('_SYSTEMROUTESPHP_CHOOSEINT','选择本路由设定要套用到哪一个网路介面。');
define('_SYSTEMROUTESPHP_DESTNET','目标网段');
define('_SYSTEMROUTESPHP_DESTNETROUTE','本路由设定要套用的目标网段');
define('_SYSTEMROUTESPHP_GWTONET','连線到目标网段的闸道IP位置');
define('_SYSTEMROUTESPHP_DESCREF','可以为这笔资料加註必要的描述文字。(not parsed)');

// Hosts
// file: sytem_hosts.php
define('_SYSTEMHOSTSPHP_MSG_CHANGED','主机列表已经变更。<br>套用之后才会生效。');
define('_SYSTEMHOSTSPHP_EDITHOST','编辑主机');
define('_SYSTEMHOSTSPHP_MSGCONFIRM','确定要刪除这个主机资料？');
define('_SYSTEMHOSTSPHP_DELHOST','刪除主机');
define('_SYSTEMHOSTSPHP_ADDHOST','新增主机');

// file: system_hosts_edit.php
define('_SYSTEMHOSTSPHP_MSGVALIDNAME','主机名称包含了无效的字元。');
define('_SYSTEMHOSTSPHP_MSGVALIDIP','请输入一个有效的IP位置。');
define('_SYSTEMHOSTSPHP_MSGHOSTEXIST','已经存在一个相同名称的主机设定。');
define('_SYSTEMHOSTSPHP_HOSTTEXT','主机名称只能包含以下字元：a-z, A-Z, 0-9, - , _ , .');
define('_SYSTEMHOSTSPHP_ADDRESSTEXT','这个主机所使用的IP位置。');
define('_SYSTEMHOSTSPHP_DESCTEXT','可以为这笔资料加註必要的描述文字。');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMFIRMWAREPHP_NAMEDESC',_MENULEFT_SYSFIRMWARE);
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','上传之映像档並不适用於这个系统');
define('_SYSTEMFIRMWAREPHP_OUTMEM','映像档上传失败(记忆体不足？)');
define('_SYSTEMFIRMWAREPHP_INVSIG','映像档之数位签章已经失效。');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','映像档並沒有通过数位签章认证。');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','无法验証映像档內的数位签章。');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','映像档已损毀');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','新的韧体正在安装中，完成之后FreeNAS将会自动重新开机。');
define('_SYSTEMFIRMWAREPHP_NOTSUP','此系统並不支援韧体上传功能。.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','这表示你所上传的映像档 ' .
		 	'並非由FreeNAS官方所发佈的，继续安装可能会导致安全漏洞或是不可预期的错误。' .
			'请安装来自官方，或是你所信任的来源所提供之映像档，並确定所下载之映像档並沒有被植入恶意程式。<br><br>'.
			'你要继续安装这个映像档吗？(自行负责)');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','於下方点选 &quot;开啟韧体升级功能&quot; ，然后选择将要上传的映像档。<br>点选 &quot;韧体升级&quot; 来开啟升级动作');			
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','开啟韧体升级功能');	
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','关闭韧体升级功能');
define('_SYSTEMFIRMWAREPHP_FIRMFILE','韧体映像档');	
define('_SYSTEMFIRMWAREPHP_UPFIRM','韧体升级');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','系统必须重新开机才能开啟韧体升级功能。');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','一但轫体升级动作已经开始，千万不要中止其动作！FreeNAS在新的韧体上传完成之后便会自动重新开机。所有之前所设定的组态都会被保留。<br>系统最少需要128MB的记忆体才能顺利执行此动作。<br>強烈的建议使用者，进行韧体升级之前请先<a href="diag_backup.php">备份</a>系统设定组态。');

// System Advanced Module // file: system_advanced.php 
define('_SYSTEMADVANCEDPHP_NAME',_MENULEFT_SYSTEM); 
define('_SYSTEMADVANCEDPHP_NAMEDESC',_MENULEFT_SYSADVANCED); 
define('_SYSTEMADVANCEDPHP_MSGVALIDTCP','TCP閒置切断时间必须是一个整数。'); 
define('_SYSTEMADVANCEDPHP_MSGVALIDCERTKEY','验证与金钥必须同时提供才能发挥作用。'); 
define('_SYSTEMADVANCEDPHP_MSGVALIDCERT','这个验证似乎是无效的。'); 
define('_SYSTEMADVANCEDPHP_MSGVALIDKEY','这个金钥似乎是无效的。'); 
define('_SYSTEMADVANCEDPHP_NOTE','本页的选项是设计给进阶的使用者使用的，同时也<strong>不提供</strong>有本页选项的相关技术支援'); 
define('_SYSTEMADVANCEDPHP_WEBGUISSLKEY','网页管理介面SSL验证/金钥'); 
define('_SYSTEMADVANCEDPHP_WEBCERT','验证码'); 
define('_SYSTEMADVANCEDPHP_WEBCERTTEXT','请在此贴上X.509 PEM格式的验证码。'); 
define('_SYSTEMADVANCEDPHP_WEBKEY','金钥'); 
define('_SYSTEMADVANCEDPHP_WEBKEYTEXT','请在此贴上一个PEM格式的RSA私钥。'); 
define('_SYSTEMADVANCEDPHP_MISC','杂项设定'); 
define('_SYSTEMADVANCEDPHP_CON','Console选单'); 
define('_SYSTEMADVANCEDPHP_DISCON','关闭console选单'); 
define('_SYSTEMADVANCEDPHP_CONTEXT','这个变更必须等到重新开机之后才会生效。'); 
define('_SYSTEMADVANCEDPHP_FIRM','韧体版本检查');
define('_SYSTEMADVANCEDPHP_DISFIRM','关闭韧体版本检查功能');
define('_SYSTEMADVANCEDPHP_FIRMTEXT','这会关闭FreeNAS中，<a href=system_firmware.php>系统: 韧体</a>页面的韧体版本检查功能。'); 
define('_SYSTEMADVANCEDPHP_TCPIDLE','TCP閒置切断时间'); 
define('_SYSTEMADVANCEDPHP_TCPIDLETEXT','閒置太久的TCP连線将会系统切断。不要把这个数值设定的太高，以免系统內同时存在太多的连線(例:不正常断線的程式)。预设的数值是9000秒钟（2.5小时）。'); 
define('_SYSTEMADVANCEDPHP_NAV','导览');
define('_SYSTEMADVANCEDPHP_NAVTEXT','将诊断的页面连结预设为展开');
define('_SYSTEMADVANCEDPHP_BEEP','提示声响'); 
define('_SYSTEMADVANCEDPHP_DISBEEP','关闭系统於开/关机时的提示声响'); 
define('_SYSTEMADVANCEDPHP_SMART','S.M.A.R.T监控程式'); 
define('_SYSTEMADVANCEDPHP_ENSMART','开啟 S.M.A.R.T 监控程式'); 
define('_SYSTEMADVANCEDPHP_SMARTTEXT','透过记錄档监控S.M.A.R.T装置。'); 
define('_SYSTEMADVANCEDPHP_TUNING','微调');
define('_SYSTEMADVANCEDPHP_ENTUNING','开啟系统核心的微调参数');
define('_SYSTEMADVANCEDPHP_ZEROCONF','ZeroConf'); 
define('_SYSTEMADVANCEDPHP_DISZEROCONF','关闭ZeroConf的服务宣告');


// Interface assing:
// file: interfaces_assign.php
define('_INTASSIGNPHP_NAME','网路介面');
define('_INTASSIGNPHP_NAMEDESC','指派通讯埠');
define('_INTASSIGNPHP_MSGVALIDPORT','通讯埠 ');
define('_INTASSIGNPHP_MSGVALIDPORTWASASSIG',' 被指派为 ');
define('_INTASSIGNPHP_MSGVALIDINT',' 介面:');
define('_INTASSIGNPHP_INT','介面');
define('_INTASSIGNPHP_PORT','网路卡');
define('_INTASSIGNPHP_DELINT','刪除介面');
define('_INTASSIGNPHP_ADDINT','新增介面');
define('_INTASSIGNPHP_INTTEXT','当按下&quot;储存&quot;之后，请继续执行下列的步骤，才能再次连線至FreeNAS：</span></p>
<ul>
  <li><span class=\"vexpl\">变更电脑上的IP位置</span></li>
  <li><span class=\"vexpl\">使用新的IP位置来连接网页管理介面</span></li>
</ul>');

// Interface LAN/OPT:
// file: interfaces_lan.php
// file: interfaces_opt.php
define('_INTLANPHP_NAME','介面');
define('_INTLANPHP_NAMEDESC','区域网路');
define('_INTPHP_MSGVALIDIP',_MSGVALIDIP);
define('_INTPHP_MSGVALIDMASK',_MSGVALIDNETBIT);
define('_INTPHP_MSGVALIDGW','请输入一个有效的闸道。');
define('_INTPHP_MSGVALIDMTU','请输入一个有效的MTU。');
define('_INTPHP_IP','IP位置');
define('_INTPHP_NETMASK','网段遮罩位元数');
define('_INTPHP_GW','闸道');
define('_INTPHP_MTU','MTU');
define('_INTPHP_MTUTEXT','标準MTU是1500，输入9000则适用於大笔资料的传输。');
define('_INTPHP_POLLING','Device polling');
define('_INTPHP_ENPOLLING','开啟device polling');
define('_INTPHP_POLLINGTEXT','Device polling可以让系统定期的从网路介面撷取新的资料而不必依赖CPU中断，
					这樣可以減低网路流量对CPU造成的负担，同时增加系统的整体工作量。但是会稍微增加网路的延迟时间
					(系统每一秒钟要询问网路介面卡1000次)。请参访m0n0wall的网站以确定你的网路卡是否支援Device polling。');
define('_INTPHP_SPEED','速度');
define('_INTPHP_DUPLEX','双工模式');
define('_INTPHP_TEXT','当按下&quot;储存&quot;之后，请继续执行下列的步骤，才能再次连線至FreeNAS：
                    <ul>
                      <li>变更电脑上的IP位置</li>
                      <li>使用新的IP位置来连接网页管理介面</li>
                    </ul>');
define('_INTPHP_ENOPT','开啟额外的选项');
define('_INTPHP_ENOPTINT',' 网路介面');
define('_INTPHP_DESC',_DESC);
define('_INTPHP_DESCTEXT','在此输入你对这个网路介面的描述文字。');
define('_INTPHP_STATIC','靜态IP设定');
define('_INTPHP_DHCP','动态DHCP设定');
define('_INTPHP_GENERAL','一般设定');
define('_INTPHP_DHCPHOSTNAME','主机名称');
define('_INTPHP_DHCPHOSTNAMETEXT','这个数值将会成为DHCP伺服器发送IP时用来识別机器的主机名称。');
define('_INTPHP_DHCPCLIENTIDENTIFIER','DHCP识別值');
define('_INTPHP_DHCPCLIENTIDENTIFIERTEXT','这个数值将会成为DHCP伺服器发送IP时用来识別机器的参数。');
define('_INTPHP_CALCNETMASKBITS','Calculate netmask bits');
define('_INTPHP_MSGCHANGED','区域网路设定已经变更。<br>套用之后才会生效。');

// Disks: Manage
// Common text
define('_DISKSPHP_NAME',_DISKS);
define('_DISKSPHP_MANAGE','管理');
define('_DISKSPHP_FORMAT','格式化');
define('_DISKSPHP_TOOLS','工具');
define('_DISKSPHP_ISCSIINIT','iSCSI初始');
define('_DISKSPHP_DISK',_DISK);
define('_DISKSPHP_FILESYSTEM',_FILESYSTEM);

// file: disks_manage.php
define('_DISKSMANAGEPHP_NAMEDESC','管理');
define('_DISKSMANAGEPHP_MSGCHANGED','磁碟组态已经变更。<br>套用之后便会生效。');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','进入待命时间');
define('_DISKSMANAGEPHP_FSUNKNOWN','未知/未格式化');
define('_DISKSMANAGEPHP_EDIT','编辑磁碟');
define('_DISKSMANAGEPHP_DEL','刪除磁碟');
define('_DISKSMANAGEPHP_DELCONF','确定要刪除这个磁碟吗？所有使用此磁碟的元件都会失效！(例：分享)');
define('_DISKSMANAGEPHP_ADD','增加磁碟');
define('_DISKSMANAGEPHP_NOTE','规划的第一步：新增你的硬碟到磁碟表中。');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','这个磁碟已经加入过了。');
define('_DISKSMANAGEEDITPHP_UDMA','UDMA模式');
define('_DISKSMANAGEEDITPHP_UDMATEXT','如果显示「UDMA_ERROR.... LBA」的讯息，你可以手动选取UDMA的模式。');
define('_DISKSMANAGEEDITPHP_STANDBY','硬碟待命时间');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','设定在多久时间之后，如果沒有存取动作便将硬碟切換至待命模式。<em>CF卡请勿选用。</em>');
define('_DISKSMANAGEEDITPHP_APM','进阶电源管理');
define('_DISKSMANAGEEDITPHP_APMTEXT','用来控制磁碟的电源消耗，相反的会失去一些效能。<em>CF卡请勿选用。</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','噪音控制');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','用来控制磁碟的运转噪音程度。<em>CF卡请勿选用。</em>');
define('_DISKSMANAGEEDITPHP_PREFS','已存在之档案系统');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','如果新增已经包含资料的硬碟，请选择其原本的档案系统格式就可以获取其中的资料。<br>
                     <em>还沒有规划档案格式的磁碟机请选择「尚未格式化」，之后再进入格式化工具选单。</em>');

//file: disks_manage_init.php
define('_DISKSMANAGEINITPHP_NAMEDESC','初始化');
define('_DISKSMANAGEINITPHP_DISKMOUNTERROR','磁碟已经掛载！请先<a href=%s>卸载</a>之后再继续其他动作。');
define('_DISKSMANAGEINITPHP_INITTEXT','<strong>磁碟初始化系统资讯:</strong>');
define('_DISKSMANAGEINITPHP_INITINFO','「fdisk: Geom not found」並不是一个错误讯息！\n');
define('_DISKSMANAGEINITPHP_TEXT','这个动作会刪除所有磁区，建立並且格式化第一号磁区成为你指定的档案系统。');
define('_DISKSMANAGEINITPHP_FORMATDISC','格式化磁碟');
define('_DISKSMANAGEINITPHP_FORMATDISCCONF','确定真的要格式化这个磁碟？所有內部的资料都会消失！');
define('_DISKSPHP_NOMBR','不要刪除MBR');
define('_DISKSPHP_NOMBRTEXT','保留原本的MBR (某些RAID控制卡需要)');

//file: disks_manage_iscsi.php
define('_DISKSMANAGEPHP_NAMEDESC','管理');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','iSCSI初始');
define('_DISKSMANAGEISCSIPHP_TARGETIP','目标IP位置');
define('_DISKSMANAGEISCSIPHP_TARGETIPTEXT','iSCSI装置的IP位置');
define('_DISKSMANAGEISCSIPHP_TARGETNAME','目标名称');
define('_DISKSMANAGEISCSIPHP_TARGETNAMETEXT','iSCSI目标的名称');

//Software RAID
// Comon value
define('_DISKSRAIDPHP_MANAGE','管理RAID');
define('_DISKSRAIDPHP_FORMAT','格式化RAID');
define('_DISKSRAIDPHP_TOOLS','工具');
define('_DISKSRAIDPHP_INFO','资讯');
define('_DISKSRAIDPHP_VOLUME','Volume名称');
define('_DISKSRAIDPHP_RAIDVOLUMEMOUNTERROR','这个RAID volume目前正被掛载中！进行任何操作之前请先将其<a href=%s>卸载</a>。');

// Software RAID common word
define('_DISKSRAIDPHP_GVINUM','Geom Vinum');
define('_DISKSRAIDPHP_GMIRROR','Geom Mirror');
define('_DISKSRAIDPHP_GSTRIPE','Geom Stripe');
define('_DISKSRAIDPHP_GCONCAT','Geom Concat');
define('_DISKSRAIDPHP_GRAID5','Geom Raid5');
define('_DISKSRAIDPHP_UNSTABLE','(测试中)');

//file: disks_raid.php
define('_DISKSRAIDPHP_NAMEDESC','RAID');
define('_DISKSRAIDPHP_MSGCHANGED','Raid组态已经变更。<br>套用变更之后才会生效。');
define('_DISKSRAIDPHP_TYPE','类型');
define('_DISKSRAIDPHP_EDITRAID','编辑RAID');
define('_DISKSRAIDPHP_DELRAID','刪除RAID');
define('_DISKSRAIDPHP_ADDRAID','新增RAID');
define('_DISKSRAIDPHP_DELCONF','确定要刪除这个RAID吗？所有使用此RAID的元件都会失效！(例：分享)');
define('_DISKSRAIDPHP_NOTE','选择性的规划步骤：利用你<a href="disks_manage.php">刚才设定的磁碟</a>来建立一个新的RAID磁碟。<br>请等待「up」的状态出现之后再进行格式化与掛载的动作！');

//file: disks_raid_edit.php
define('_DISKSRAIDEDITPHP_NAMEDESC',_EDIT);
define('_DISKSRAIDEDITPHP_MSGADDDISKFIRST','请先加入磁碟。');
define('_DISKSRAIDEDITPHP_MSGVALIDNAME','装置名称只可以由 a-z, A-Z, 0-9 组成。');
define('_DISKSRAIDEDITPHP_MSGEXIST','这个装置已经是RAID成员。');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID0','架构RAID 0最少要有两个磁碟。');
define('_DISKSRAIDEDITPHP_MSGVALIDJBOD','架构JBOD最少要有两个磁碟。');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID1','架构RAID 1最少要有两个磁碟。');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID5','架构RAID 5最少要有三个磁碟。');
define('_DISKSRAIDEDITPHP_MSGERROR','错误！不能储存RAID设定值。\n');
define('_DISKSRAIDEDITPHP_RAIDNAME','Raid名称');
define('_DISKSRAIDEDITPHP_GVBALANCEALGO','效能均衡演算法');
define('_DISKSRAIDEDITPHP_GVBALANCEALGOTEXT','选择读取档案时採用的效能均衡演算法。');
define('_DISKSRAIDEDITPHP_RAID0','striping');
define('_DISKSRAIDEDITPHP_JBOD','JBOD');
define('_DISKSRAIDEDITPHP_RAID1','mirroring');
define('_DISKSRAIDEDITPHP_RAID5','rotated block-interleaved parity');
define('_DISKSRAIDEDITPHP_MEMBERS','RAID volume的成员');

//file: disks_raid_info.php
define('_DISKSRAIDINFOPHP_TEXT','软体RAID资讯与状态');

//file: disks_raid_init.php
define('_DISKSRAIDINITPHP_INFO','UFS格式化结果输出：');
define('_DISKSRAIDINITPHP_TEXT','这会将此RAID磁碟格式化为Unix档案系统(UFS)。');
define('_DISKSRAIDINITPHP_FORMAT','格式化UFS磁碟！');

//file: disks_raid_tools.php
define('_DISKSRAIDTOOLSPHP_OBJNAME','物件名称');
define('_DISKSRAIDTOOLSPHP_COMMAND','指令');
define('_DISKSRAIDTOOLSPHP_INFO','GVINUM指令结果:');
define('_DISKSRAIDTOOLSPHP_SENDCMD','传送指令！');
define('_DISKSRAIDTOOLSPHP_TEXT','1. 这些特殊的指令大部分只用来除错而已！<br>2. RAID磁碟在建立之后会自动啟动，不需要再次下达activate指令。');

//Disk Mount Point
// Comon value
define('_DISKSMOUNTPHP_NAME','掛载点');
define('_DISKSMOUNTPHP_SHARENAME','分享名称');
define('_DISKSMOUNTPHP_MANAGE','管理');
define('_DISKSMOUNTPHP_TOOLS','工具');
define('_DISKSMOUNTPHP_FSCK','Fsck');

// disks_mount.php
define('_DISKSMOUNTPHP_MSGCHANGED','掛载点已经变更。<br>套用之后才会生效。');
define('_DISKSMOUNTPHP_NOTE','规划的第二步：选择<a href="disks_manage.php">新增磁碟</a>所使用的档案系统<p>');
define('_DISKSMOUNTPHP_DELCONF','确定要刪除这个掛载点吗？所有使用此掛载点的元件都会失效！(例：分享)');
define('_DISKSMOUNTPHP_DEL','刪除掛载点');
define('_DISKSMOUNTPHP_ADD','新增掛载点');

// disks_mount_edit.php
define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','分享名称只可以包含以下字元：a-z, A-Z, 0-9, _ , -。');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','描述文字包含了无效的字元。');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','不能掛载系统区(第1区)，资料分割区为第2区。');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','输入了存在的分享名称。');
define('_DISKSMOUNTEDITPHP_MSGVALIDDISK','这个磁碟/磁区之前已经设定过了。');
define('_DISKSMOUNTEDITPHP_TEXT','1. 你不可以掛载含有设定档的磁区\'%s\'。<br>2. FreeBSD对NTFS格式的支援尚不完全（有BUG!）。');

// disks_mount_tools.php
define('_DISKSMOUNTTOOLS_COMMAND','指令');
define('_DISKSMOUNTTOOLS_SENDCMD','传送指令！');
define('_DISKSMOUNTTOOLS_CMDINFO','指令结果：');
define('_DISKSMOUNTTOOLS_MOUNTTEXT','掛载中...');
define('_DISKSMOUNTTOOLS_UMOUNTTEXT','卸载中...');

// file: disk_mount_fsck.php
define('_DISKSMOUNTFSCK_NAMEDESC','工具');
define('_DISKSMOUNTFSCK_CMDINFO','指令输入结果：');
define('_DISKSMOUNTFSCK_MOUNTNOTE','磁碟目前正在掛载中！系统会将磁碟暂时卸载以进行指令。');
define('_DISKSMOUNTFSCK_UMOUNT','卸载 磁碟/磁区');
define('_DISKSMOUNTFSCK_UMOUNTTEXT','如果选择的磁碟/磁区正在掛载中，系统会将磁碟暂时卸载以进行指令，否则指令只能在唯读模式下运作。<br>命令执行中如果有使用者存取这个掛载点可能会使服务中断。');

//file: disks_manage_tools.php
define('_DISKSMANAGETOOLS_COMMAND','指令');
define('_DISKSMANAGETOOLS_SENDCMD','传送指令！');
define('_DISKSMANAGETOOLS_RAIDDISKNOTE','在这个磁碟使用<a href="%s">RAID工具</a>！');
define('_DISKSMANAGETOOLS_CMDINFO','指令输出：');
// Service: CIFS/SAMBA
// Common text
define('_SRVCIFS_SETTINGS','设定');
define('_SRVCIFS_SHARE','分享');
define('_SRVCIFS_SHARES','分享');

// file: services_samba.php
define('_SRVCIFS_NAMEDESC','CIFS');
define('_SRVCIFS_MSGVALIDNETBIOSNAME','Netbios名称输入了无效的字元。');
define('_SRVCIFS_MSGVALIDWORKGOUP','工作群组內输入了无效的字元。');
define('_SRVCIFS_MSGVALIDWINS','WINS伺服器必须是IP位置。');
define('_SRVCIFS_MSGVALIDSNDBUFF','传送缓冲区必须是一个数字。');
define('_SRVCIFS_MSGVALIDRCVBUFF','接收缓冲区必须是一个数字。');
define('_SRVCIFS_SHARE','CIFS档案分享');
define('_SRVCIFS_AUTH','认证方式');
define('_SRVCIFS_NETBIOSNAME','NetBios名称');
define('_SRVCIFS_WORKGROUP','工作群组');
define('_SRVCIFS_WORKGROUPTEXT','选择将要加入的工作群组');
define('_SRVCIFS_DESCTEXT','伺服器描述文字。通常可以留白。');
define('_SRVCIFS_DOSCHARSET','Dos编码方式');
define('_SRVCIFS_UNIXCHARSET','Unix编码方式');
define('_SRVCIFS_LOGLEVEL','纪錄档层级');
define('_SRVCIFS_LOCALMASTER','Local Master Browser');
define('_SRVCIFS_LOCALMASTERTEXT','让FreeNAS尝试成为local master browser。');
define('_SRVCIFS_TIMESRV','时间伺服器');
define('_SRVCIFS_TIMESRVTEXT','设定FreeNAS成为Windows客戶端的时间伺服器。');
define('_SRVCIFS_WINS','WINS伺服器');
define('_SRVCIFS_WINSTEXT','WINS伺服器的IP位置。');         
define('_SRVCIFS_RECYCLE','资源回收桶');
define('_SRVCIFS_ENRECYCLE','开啟资源回收桶');
define('_SRVCIFS_ENRECYCLETEXT','开啟资源回收筒的功能，可救回刪除之档案。');
define('_SRVCIFS_SNBBUFF','传送缓冲区');
define('_SRVCIFS_SNBBUFFTEXT','传送缓冲区的大小(预设为16384).');
define('_SRVCIFS_RCVBUFF','接收缓冲区');
define('_SRVCIFS_RCVBUFFTEXT','接收缓冲区的大小(预设为16384).');

// file: services_samba_share.php
define('_SRVCIFSSHARE_MSGCHANGED','分享设定已经变更。<br>套用之后才会生效。');
define('_SRVCIFSSHARE_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHARE_BROWSEABLE','提供浏览');
define('_SRVCIFSSHARE_DESC',_DESC);
define('_SRVCIFSSHARE_EDIT','编辑');

// file: services_samba_share_edit.php
define('_SRVCIFSSHAREEDIT_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHAREEDIT_DESC',_DESC);
define('_SRVCIFSSHAREEDIT_BROWSEABLE','提供浏览');
define('_SRVCIFSSHAREEDIT_BROWSEABLETEXT','控制这个分享资料夹能不能直接的被浏览器看到。');

// Service: FTP
// file: services_ftp.php
define('_SRVFTP_NAMEDESC','FTP');
define('_SRVFTP_MSGVALIDPORT',_MSGVALIDTCPPORT);
define('_SRVFTP_MSGVALIDMAXCLIENT','连線数目上線必须是一个数字。');
define('_SRVFTP_MSGVALIDMAXIP','相同IP的连線数目必须是一个数字。');
define('_SRVFTP_MSGVALIDIDLE','失效时间必须是一个数字。');
define('_SRVFTP_MSGVALIDPASVIP','被动式IP位置必须是一个对外的IP。');
define('_SRVFTP_MSGVALIDPASVMAX','被动式连接埠起点必须是一个有效的port number.');
define('_SRVFTP_MSGVALIDPASVMIN','被动式连接埠终点必须是一个有效的port number.');
define('_SRVFTP_MSGVALIDAUTH','请至少选择 匿名/本地使用者 认证。');
define('_SRVFTP_FTPSERVER','FTP伺服器');
define('_SRVFTP_TCP',_TCPPORT);
define('_SRVFTP_TCPTEXT','FTP的预设连接埠是21');
define('_SRVFTP_MAXCLIENT','连線数目');
define('_SRVFTP_MAXCLIENTTEXT','连線数目上限。');
define('_SRVFTP_MAXIP','相同IP的连線数目');
define('_SRVFTP_MAXIPTEXT','来自相同IP的连線数目上限(0 = 不限制).');
define('_SRVFTP_TIMEOUT','失效时间');
define('_SRVFTP_TIMEOUTTEXT','自动切断连線时间（分钟）。');
define('_SRVFTP_ANONYMOUS','匿名登入');
define('_SRVFTP_ENANONYMOUS','是否开啟匿名登入');
define('_SRVFTP_AUTH','本机使用者');
define('_SRVFTP_ENAUTH','是否开啟本机使用者登入');
define('_SRVFTP_FXP','FXP');
define('_SRVFTP_ENFXP','开啟FXP协定支援。');
define('_SRVFTP_FXPTEXT','FXP是支援两个远端伺服器互传档案的通讯协定。(有安全疑虑！).');
define('_SRVFTP_NATMODE','NAT模式');
define('_SRVFTP_ENNATMODE','強迫NAT模式。');
define('_SRVFTP_NATMODETEXT','如果你的FTP伺服器是架设在NAT的环境之下同时也不支援Applicative FTP Proxying，请开啟这个功能。');
define('_SRVFTP_PASSIVEIP','被动式IP位置');
define('_SRVFTP_PASSIVEIPTEXT','FTP伺服器的被动式连線IP位置。');
define('_SRVFTP_PASVMIN','被动式连接埠起点');
define('_SRVFTP_PASVMINTEXT','被动式连線埠的区段起点(0 = 不限制)');
define('_SRVFTP_PASVMAX','被动式连接埠终点');
define('_SRVFTP_PASVMAXTEXT','被动式连線埠的区段终点(0 = 不限制)');
define('_SRVFTP_BANNER','欢迎文字');
define('_SRVFTP_BANNERTEXT','当使用者刚连接上FTP所显示的讯息。');

// Service: NFS
// file: services_nfs.php
define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','必须指定一个有效的网域。');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','NFS伺服器');
define('_SRVNFS_MAPALLROOT','所有人视为root');
define('_SRVNFS_MAPALLROOTTEXT','让所有的使用者都有root权限。');
define('_SRVNFS_AUTHNET','认可网路');
define('_SRVNFS_AUTHNETTEXT','设定可以使用NFS分享的网路区段');
define('_SRVNFS_AUTHNETMASK','网段遮罩位元数');
define('_SRVNFS_TEXT','输出的目錄名称是：/mnt/sharename');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','伺服器');
define('_SRVRYNC_CLIENT','客戶端');
define('_SRVRYNC_LOCAL','本地端');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','Rsync伺服器');
define('_SRVRYNCD_READONLY','唯读');
define('_SRVRYNCD_MAPUSER','对应到本地使用者');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_TCPORTTEXT','替代的TCP连接埠，预设值为873。');
define('_SRVRYNCD_MAXCON','连線数目上線');
define('_SRVRYNCD_MAXCONTEXT','同时最高连線数目。预设为0(不限制)。');
define('_SRVRYNCD_MOTD','MOTD');
define('_SRVRYNCD_MOTDTEXT','每日新讯(每次登入时显示)。');

// file: services_rsyncd_client.php
define('_SRVRYNCC_MSGMPFIRST','请先设定掛载点。');
define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
define('_SRVRYNCC_RSYNCC','Rsync本地端档案同步');
define('_SRVRYNCC_REMOTESERVER','远端的RSYNC伺服器');
define('_SRVRYNCC_REMOTEID','远端的RSYNC伺服器IP位置');
define('_SRVRYNCC_OPTIONS','RSYNC选项');
define('_SRVRYNCC_OPTDEL','刪除本地端已经不存在的档案。');
define('_SRVRYNCC_SHARES','指定要同步的目錄');
define('_SRVRYNCC_SHARESTEXT','远端与本地端的分享目錄必须相同。');
define('_SRVRYNCC_TIME','同步啟动时间');
define('_SRVRYNCC_TEXT',_MSGSCHED_TEXT);

// file: services_rsyncd_local.php
define('_SRVRYNCL_MSGVALID','来源与目的不能相同！');
define('_SRVRYNCC_RSYNCL','Rsync本地备援');
define('_SRVRYNCL_SOURCE','来源位置');
define('_SRVRYNCL_SOURCETEXT','选择来源档案的掛载点。');
define('_SRVRYNCL_DESTINATION','目地位置');
define('_SRVRYNCL_DESTINATIONTEXT','选择目的位置的掛载点(备援磁碟机)。');


// Service: SSHD
// file: services_sshd.php
define('_SRVSSHD_NAMEDESC','SSHD');
define('_SRVSSHD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVSSHD_MSGVALIDKEY','这个钥匙似乎是无效的。');
define('_SRVSSHD_SSHD','SSH伺服器');
define('_SRVSSHD_READONLY','唯读');
define('_SRVSSHD_TCPORT',_TCPPORT);
define('_SRVSSHD_TCPORTTEXT','变更连接埠。预设值是22');
define('_SRVSSHD_PERMITROOTLOGIN','root准许登入');
define('_SRVSSHD_PERMITROOTLOGINTEXT','指定超级使用者 (root)是否可以透过SSH直接登入。');
define('_SRVSSHD_KEY','SSH key');
define('_SRVSSHD_PRIVATEKEY','私钥');
define('_SRVSSHD_PRIVATEKEYTEXT','在此贴上PEM格式的私钥。');

// Service: Unison
// file: services_unison.php
define('_SRVUNSN_NAMEDESC','Unison');
define('_SRVUNSN_MSGVALIDWRKDIR','设定值內的分享/工作目錄不存在。');
define('_SRVUNSN_UNISON','Unison 档案同步机制');
define('_SRVUNSN_SHARE','分享');
define('_SRVUNSN_SHAREDESC','确定你有足夠的空间来复制所有将要同步的档案');
define('_SRVUNSN_WORKDIR','工作目錄');
define('_SRVUNSN_WORKDIRDESC','选择工作档案要储存的位置');
define('_SRVUNSN_MAKEDIR','建立');
define('_SRVUNSN_MAKEDIRLABEL','如果你还沒有工作目錄，请建立一个');
define('_SRVUNSN_NOTESSH','Unison服务必须配合<a href="/services_sshd.php">SSHD</a>，而存取的<a href="/access_users.php">使用者</a>必须要有Shell权限。');

// Service: UPnP
// file: services_upnp.php
define('_SRVUPNP_NAMEDESC','UPnP');
define('_SRVUPNP_UPNP','UPnP影音多媒体伺服器');
define('_SRVUPNP_NAME','名称');
define('_SRVUPNP_NAMETEXT','UPnP friendly name.');
define('_SRVUPNP_INTERFACE','网路介面');
define('_SRVUPNP_INTERFACETEXT','要使用的网路介面');
define('_SRVUPNP_CONTENT','內容');
define('_SRVUPNP_CONTENTTEXT','分享的资料夹(space or CSV list).');

// Service: AFP
// file: services_afp.php
define('_SRVAFP_NAMEDESC','AFP');
define('_SRVAFP_MSGVALIDAUTH','请至少选择一种认证方式。');
define('_SRVAFP_AFPSRV','AFP伺服器');
define('_SRVAFP_AFPNAME','伺服器名称');
define('_SRVAFP_AUTH','认证');
define('_SRVAFP_ENABLEGUEST','Guest可以登入。');
define('_SRVAFP_ENABLELOCAL','啟用本地端使用者可以登入。');

// Access
// common words
define('_ACCESS_NAME','权限控制');
define('_ACCESS_USER','使用者');
define('_ACCESS_USERS','使用者');
define('_ACCESS_GROUP','群组');
define('_ACCESS_GROUPS','群组');
define('_ACCESS_FULLNAME','全名');

// file: access_users.php
define('_ACCESSUSERS_MSGCHANGED','使用者名单已经变更。<br>套用变更之后才会生效。');
define('_ACCESSUSERS_EDITUSER','编辑使用者');
define('_ACCESSUSERS_DELUSER','刪除使用者');
define('_ACCESSUSERS_CONFDEL','确定要刪除此使用者帐号？');
define('_ACCESSUSERS_ADDUSER','新增使用者');

// file: access_users_edit.php
define('_ACCESSUSERSEDIT_MSGGROUPFIRST','你必须先建立一个群组。');
define('_ACCESSUSERSEDIT_MSGVALIDLOGIN','登入帐号含有无效的字元。');
define('_ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED','预定的登入帐号。');
define('_ACCESSUSERSEDIT_MSGVALIDFULLNAME','全名包含无效的字元。');
define('_ACCESSUSERSEDIT_MSGVALIDDUPLICATE','使用者帐号已经存在。');
define('_ACCESSUSERSEDIT_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSUSERSEDIT_MSGVALIDPASSWDBADCHAR','密码包含无效的 : 字元');
define('_ACCESSUSERSEDIT_LOGIN',_LOGIN);
define('_ACCESSUSERSEDIT_LOGINTEXT','用来登入的使用者帐号');
define('_ACCESSUSERSEDIT_FULLNAME',_ACCESS_FULLNAME);
define('_ACCESSUSERSEDIT_FULLNAMETEXT','使用者的全名');
define('_ACCESSUSERSEDIT_PASSWORD','密码');
define('_ACCESSUSERSEDIT_PASSWORDTEXT','使用者自行设定的密码');
define('_ACCESSUSERSEDIT_PASSWORDCONF','确认密码');
define('_ACCESSUSERSEDIT_PASSWORDCONFTEXT','请输入二次密码');
define('_ACCESSUSERSEDIT_GROUPMEMBER','群组成员');
define('_ACCESSUSERSEDIT_FULLSHELL','Shell权限');
define('_ACCESSUSERSEDIT_FULLSHELLTEXT','让这个使用者可以使用shell。');

// file: access_users_groups.php
define('_ACCESSGROUPS_MSGCHANGED','群组列表已经变更。<br>套用变更之后才会生效。');
define('_ACCESSGROUPS_DESC',_DESC);
define('_ACCESSGROUPS_GROUPEDIT','编辑群组');
define('_ACCESSGROUPS_GROUPDEL','刪除群组');
define('_ACCESSGROUPS_GROUPDELCONF','确定要刪除此群组？');
define('_ACCESSGROUPS_GROUPADD','新增群组');

// file: access_users_groups_edit.php
define('_ACCESSGROUPSEDIT_MSGVALIDNAME','群组名称內有无效的字元。');
define('_ACCESSGROUPSEDIT_MSGVALIDDESC','群组描述內有无效的字元。');
define('_ACCESSGROUPSEDIT_MSGVALIDDUPLICATE','这个群组已经建立过了。');
define('_ACCESSGROUPSEDIT_NAME','名称');
define('_ACCESSGROUPSEDIT_NAMETEXT','群组名称。');
define('_ACCESSGROUPSEDIT_DESC',_DESC);
define('_ACCESSGROUPSEDIT_DESCTEXT','群组描述文字。');

// file: access_ad.php
define('_ACCESSAD_NAME','Active Directory');
define('_ACCESSAD_MSGVALIDIP',_MSGVALIDIP);
define('_ACCESSAD_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSAD_ADNAME','AD伺服器名称');
define('_ACCESSAD_ADNAMETEXT','AD or PDC 的名称。');
define('_ACCESSAD_IP','AD伺服器IP');
define('_ACCESSAD_IPTEXT','MS Active Directory伺服器的IP位置。');
define('_ACCESSAD_DOMAIN','网域名称');
define('_ACCESSAD_DOMAINTEXT','网域名称(in old format)');
define('_ACCESSAD_ADMINAME','管理者帐号');
define('_ACCESSAD_ADMINNAMETEXT','请在此设定管理者的帐号。');
define('_ACCESSAD_ADMINPASS','管理者密码');
define('_ACCESSAD_ADMINPASSTEXT','请在此设定管理者的密码，並且输入两次以确认无误。');

// Diagnostics
define('_DIAG_NAME','检测');

// file: shutdown.php
define('_SHUT_NAME',_MENULEFT_CMDSHUTDOWNSYS);
define('_SHUT_MSGSHUT','系统关闭中，可能需要一分钟的时间...');
define('_SHUT_CONF','确定要关闭系统？');

// file: shutdown_sched.php
define('_SHUTSCHED_NAME','排程关机');
define('_SHUTSCHED_TIME','关机时间');
define('_SHUTSCHED_TEXT',_MSGSCHED_TEXT);

// file: diag_edit.php
define('_DIAGEDITPHP_NAMEDESC','档案编辑');
define('_DIAGEDITPHP_FILEPATH','档案路径');
define('_DIAGEDITPHP_SAVEMSG','将档案储存至');
define('_DIAGEDITPHP_FILENOTFOUND','沒有这个档案');
define('_DIAGEDITPHP_ROWS','列');
define('_DIAGEDITPHP_COLS','栏');
define('_DIAGEDITPHP_HIGHLIGHTING','语法高亮度显示');
define('_DIAGEDITPHP_HIGHLIGHTINGENABLED','开啟');
define('_DIAGEDITPHP_HIGHLIGHTINGDISABLED','关闭');

// file: exec.php
define('_DIAGEXECPHP_NAMEDESC','执行指令');

// file: reboot.php
define('_DIAGREBOOT_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGREBOOT_NAMEDESC',_MENULEFT_CMDREBOOTSYS);
define('_DIAGREBOOT_REBOOTMSG','系统重新啟动中，可能需要一分钟的时间...');
define('_DIAGREBOOT_REBOOTCONF','确定要重新啟动系统？');

// file: license.php
define('_LICENSE_NAME','License');

// file: status_wireless.php
define('_STATSWIRELESS_NAME',_MENULEFT_STATUS);
define('_STATSWIRELESS_NAMEDESC','无線网路');
define('_STATSWIRELESS_INTERFACE','%s 介面 (SSID \'%s\')');
define('_STATSWIRELESS_SIGNALSTRENGTHCACHE','讯号強度快取');
define('_STATSWIRELESS_MACADDRESS','MAC位置');
define('_STATSWIRELESS_IPADDRESS','IP位置');
define('_STATSWIRELESS_SIGNAL','信号');
define('_STATSWIRELESS_NOISE','杂讯');
define('_STATSWIRELESS_QUALITY','品质');
define('_STATSWIRELESS_ASSOCIATEDSTATIONS','连接的无線网路');
define('_STATSWIRELESS_TXRATES','TX速率');
define('_STATSWIRELESS_MSGNOWINTFOUND','並沒有找到支援的无線网路卡(使用wi[n]驱动的网路卡才有支援).');

// file: status_interfaces.php
define('_STATSINTERFACES_NAME',_MENULEFT_STATUS);
define('_STATSINTERFACES_NAMEDESC',_MENULEFT_INTERFACES);
define('_STATSINTERFACES_INTERFACE','%s 介面');
define('_STATSINTERFACES_STATUS','状态');
define('_STATSINTERFACES_DHCP','DHCP');
define('_STATSINTERFACES_PPPOE','PPPoE');
define('_STATSINTERFACES_PPTP','PPTP');
define('_STATSINTERFACES_MACADDRESS','MAC位置');
define('_STATSINTERFACES_IPADDRESS','IP位置');
define('_STATSINTERFACES_SUBNETMASK','网段遮罩');
define('_STATSINTERFACES_GATEWAY','闸道');
define('_STATSINTERFACES_ISPDNSSERVERS','ISP DNS伺服器');
define('_STATSINTERFACES_MEDIA','Media');
define('_STATSINTERFACES_CHANNEL','频道');
define('_STATSINTERFACES_SSID','SSID');
define('_STATSINTERFACES_IOPACKETS','I/O封包');
define('_STATSINTERFACES_IOERRORS','I/O错误');
define('_STATSINTERFACES_COLLISIONS','封包冲撞');

// file: status_graph_cpu.php
define('_STATSGRAPHCPU_NAME',_MENULEFT_STATUS);
define('_STATSGRAPHCPU_NAMEDESC','CPU负载');

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
define('_INTVLAN_INTERFACE','网路介面');
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
define('_DIAGTRACEROUTE_NOTE','Traceroute需要一段时间才能完成。你可以随时停止並观看结果。');

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
define('_DIAGINFOS_PARTITIONS','磁区');
define('_DIAGINFOS_SMART','S.M.A.R.T.');
define('_DIAGINFOS_ATAIDLE','ATAidle');
define('_DIAGINFOS_SPACEUSED','使用空间');
define('_DIAGINFOS_MOUNTS','掛载点');
define('_DIAGINFOS_SOFTWARERAID',_SOFTRAID);
define('_DIAGINFOS_ISCSI','iSCSI');
define('_DIAGINFOS_MSDOMAIN','MS网域');

// file: diag_infos.php
define('_DIAGINFOSDISKS_ATA_INFO','已侦测到的ATA磁碟');
define('_DIAGINFOSDISKS_SCSI_INFO','已侦测到的SCSI磁碟');

// file: diag_infos_mount.php
define('_DIAGINFOSMOUNT_INFO','掛载点一览');

// file: diag_infos_space.php
define('_DIAGINFOSSPACE_INFO','可用空间');

// file: diag_defaults.php
define('_DIAGDEFAULTS_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGDEFAULTS_NAMEDESC',_MENULEFT_CMDFACTORYDEF);

// file: diag_backup.php
define('_DIAGBACKUP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGBACKUP_NAMEDESC',_MENULEFT_CMDBACKUPRESTORE);
define('_DIAGBACKUP_RESTORENOTE','FreeNAS复原设定组态之后将会自动重新开机。');

// file: diag_arp.php
define('_DIAGARP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGARP_NAMEDESC',_MENULEFT_DIAGARPTAB);
define('_DIAGARP_IPADDRESS','IP位置');
define('_DIAGARP_MACADDRESS','MAC位置');
define('_DIAGARP_HOSTNAME','主机名称');
define('_DIAGARP_INTERFACE','网路介面');
define('_DIAGARP_DEL','刪除ARP项目');
define('_DIAGARP_DELALL','移除所有ARP table內的项目');
define('_DIAGARP_NOTE','如果你在<a href="diag_logs_settings.php">诊断: 记錄档</a>页面，开啟了&quot;转換成网域名称&quot;的功能。IP位置会被转換成网域名称。');

// file: status_graph.php
define('_STATSGRAPH_NAME',_MENULEFT_STATUS);
define('_STATSGRAPH_NAMEDESC','流量图表');

// file: graph.php
// Note for translator: Do not use international html coding here
//from TradChinese problem: UTF8 encoded character are not accpetible in this section...
//define('_GRAPH_ERRORGETIFDATA','无法获取网路流量资料...');
define('_GRAPH_ERRORGETIFDATA','Cannot get data about interface');
//define('_GRAPH_IN','接收');
define('_GRAPH_IN','In');
//define('_GRAPH_OUT','传送');
define('_GRAPH_OUT','Out');
define('_GRAPH_SWITCHTO','Switch to');
//define('_GRAPH_AUTOSCALE','自动符合检视大小');
define('_GRAPH_AUTOSCALE','AutoScale');
define('_GRAPH_AUTOSCALE_FOLLOW','Follow');
define('_GRAPH_AUTOSCALE_UP','Up');
define('_GRAPH_SHOWLAST','Graph shows last');
//define('_GRAPH_COLLECTDATA','正在初始化，请稍候...');
define('_GRAPH_COLLECTDATA','Collecting initial data, please wait...');
?>
