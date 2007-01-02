<?php 
/*
	lang-TraditionalChinese.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2006 Baikuan Hsu <chicworks@gmail.com>
	All rights reserved.
	
	This language file matches FreeNAS Ver.0.681B.
	
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
define('_LOCALE','zh_TW.UTF8');
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
define('_TIME', '時間');
define('_TIMEBRIEF','%I:%M %p');
define('_SECOND', '秒');
define('_SECONDS', '秒');
define('_MINUTE', '分');
define('_MINUTES', '分');
define('_HOUR', '小時');
define('_HOURS', '小時');
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
define('_OF','關');
define('_OK','OK');
define('_ON','開');
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
define('_EDIT','編輯');
define('_ENABLE','啟動');
define('_HELP', '說明');
define('_IGNORE','忽略');
define('_LOGIN','登入');
define('_LOGOUT','登出');
define('_MODIFY','修改');
define('_NEW','新增');
define('_SEARCH', '搜尋');
define('_SAVE', '儲存');
define('_SUBMIT','傳送');
define('_UPDATE', '更新');
define('_VIEW', '檢視');
define('_APPLY', '套用');
define('_WARNING', '警告');
define('_LOAD', '載入');
define('_RETRY', '重試');

//FreeNAS common words
define('_SIZE', '大小');
define('_STATUS', '狀態');
define('_TYPE', '類別');
define('_CONFIGURING', '設定中');
define('_DISK', '磁碟');
define('_DISKS', '磁碟');
define('_PARTITION', '磁區');
define('_FILESYSTEM', '檔案系統');
define('_DESC', '描述');
define('_SOFTRAID', '軟體RAID');
define('_SERVICE','服務');
define('_SERVICES','服務');
define('_TCPPORT','TCP port');
define('_CONFIRMATION','確認');
define('_NOTE','提示');
define('_SUCCESSFUL','成功');
define('_FAILED','失敗');
define('_ERROR','錯誤');
define('_HINT','提示');

// common messages
define('_MESSAGES_SUCCESS', '變更套用成功。');
define('_MESSAGES_SUCCESSWITHREBOOT', '變更已儲存，必須<a href=reboot.php>重新開機</a>之後才會生效');
define('_MESSAGES_ERROR', '錯誤：變更無法套用 (error code '.$ok.').');
define('_MSGVALIDIP','請輸入一個有效的IP位置。');
define('_MSGVALIDNETBIT','請輸入一個有效的network bit count。');
define('_MSGVALIDTCPPORT','請輸入一個有效的port number。');
define('_MSGVALIDPASSWORD','密碼不相同');

define('_MSGSCHED_TEXT','提示：Ctrl+滑鼠左鍵(Mac: command+滑鼠主鍵) 可以複選項目。');

// Special Filesystem Warning

define('_MSGFILESYSTEM','UFS 的檔案系統格式是 FreeBSD 原生格式 (FreeNAS的OS)。 如果使用FAT, FAT32, EXT2,
EXT3, 或是 NTFS 可能會導致不可預期的錯誤！');

// states
define('_ACTIVE','運作中');
define('_INACTIVE','停止中');
define('_SELECTED','已選擇');

// Webgui layout
define('_MENULEFT_SYSTEM','系統');
define('_MENULEFT_SYSGENSETUP','設定');
define('_MENULEFT_SYSSTATROUTES','靜態路由');
define('_MENULEFT_SYSFIRMWARE','韌體');
define('_MENULEFT_SYSADVANCED','進階');
define('_MENULEFT_INTERFACES','網路介面');
define('_MENULEFT_INTASSIGN','指派');
define('_MENULEFT_INTLAN','區域網路');
define('_MENULEFT_DISKS',_DISKS);
define('_MENULEFT_DISKMANAG','管理');
define('_MENULEFT_DISKMNGSOFTRAID',_SOFTRAID);
define('_MENULEFT_DISKMNGMOUNTPOINT','掛載點');
define('_MENULEFT_SERVICES','服務');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVUNSN','Unison');
define('_MENULEFT_SRVUPNP','UPnP');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','存取權限');
define('_MENULEFT_ACUSERGROUPS','使用者/群組');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS',_STATUS);
define('_MENULEFT_STSYSTEM','系統');
define('_MENULEFT_STPROCESS','處理程序');
define('_MENULEFT_STINTERFACES','網路介面');
define('_MENULEFT_STDISKS',_DISKS);
define('_MENULEFT_STWIRELESS','無線網路');
define('_MENULEFT_EXTENSIONS','擴充');
define('_MENULEFT_DIAGNOSTICS','診斷');
define('_MENULEFT_DIAGLOGS','記錄檔');
define('_MENULEFT_DIAGINF','資訊');
define('_MENULEFT_DIAGPINGTRACE','Ping/Traceroute');
define('_MENULEFT_DIAGARPTAB','ARP table');
define('_MENULEFT_DIAGEXEC','命令列');
define('_MENULEFT_DIAGEDITFILE','編輯檔案');
define('_MENULEFT_CMDBACKUPRESTORE','備份/還原');
define('_MENULEFT_CMDFACTORYDEF','回到原始預設值');
define('_MENULEFT_CMDREBOOTSYS','重新開機');
define('_MENULEFT_CMDSHUTDOWNSYS','關機');
define('_MENULEFT_GRAPH','圖表');

// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION',_MENULEFT_SYSGENSETUP);
define('_SYSTEMPHP_HOSTNAME','主機名稱');
define('_SYSTEMPHP_HOSTNAMETEXT','NAS的主機名稱，不包含網域<br>例如: <em>nas');
define('_SYSTEMPHP_DOMAIN','網域');
define('_SYSTEMPHP_DOMAINTEXT','例： <em>mycomany.com</em>');
define('_SYSTEMPHP_DNS','DNS伺服器');
define('_SYSTEMPHP_DNSTEXT','IP位置');
define('_SYSTEMPHP_USERNAME','帳號');
define('_SYSTEMPHP_USERNAMETEXT','變更網頁介面的帳號。');
define('_SYSTEMPHP_PASSWORD','密碼');
define('_SYSTEMPHP_PASSWORDTEXT','變更網頁介面的密碼（兩次）<br>不要使用以下的字元 \':\'.');
define('_SYSTEMPHP_WEBGUIPROTOCOL','網頁介面協定');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','HTTPS是加密過的連線');
define('_SYSTEMPHP_WEBGUIPORT','網頁介面連接埠');
define('_SYSTEMPHP_WEBGUIPORTTEXT','輸入自行設定的網頁管理介面連接埠(80: HTTP, 443: HTTPS)。');
define('_SYSTEMPHP_LANGUAGE','Language/語言設定');
define('_SYSTEMPHP_LANGUAGETEXT','選擇系統語言');
define('_SYSTEMPHP_TIMEZONE','時區');
define('_SYSTEMPHP_TIMEZONETEXT','選擇最鄰近的區域。');
define('_SYSTEMPHP_TIMEUPDATEINT','時間同步間隔');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','多少分鐘與時間伺服器同步一次。; 建議300，輸入0可以關閉');
define('_SYSTEMPHP_NTPSERVER','NTP時間伺服器');
define('_SYSTEMPHP_NTPSERVERTEXT','使用空白字元來分隔多個主機位置(一個空白即可) 這個服務需要先設定DNS伺服器');
define('_SYSTEMPHP_MSGHOSTNAME','Hostname只能包含以下字元 a-z, 0-9, \'-\'.');
define('_SYSTEMPHP_MSGDOMAIN','網域只能包含以下字元 a-z, 0-9, \'-\', \'.\'.');
define('_SYSTEMPHP_MSGDNS','主要/次要DNS伺服器必須指定一個有效的IP。');
define('_SYSTEMPHP_MSGUSERNAME','帳號只能包含以下字元 a-z, A-Z, 0-9.');
define('_SYSTEMPHP_MSGWEBGUIPORT','網頁介面必須指定一個有效的TCP/IP連接埠。');
define('_SYSTEMPHP_MSGPASSWORD','您輸入的密碼不相符。');
define('_SYSTEMPHP_MSGLANGUAGE','必須選擇一個有效的語言。');
define('_SYSTEMPHP_MSGTIMEUPDATEINTERVAL','將時間同步間隔設定只接受6-1440的範園，或者輸入0將其關閉。');
define('_SYSTEMPHP_MSGTIMESERVERS','NTP時間伺服器只能包含以下字元a-z, 0-9, \'-\', \'.\'.');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','系統資訊');
define('_INDEXPHP_NAME','名稱');
define('_INDEXPHP_VERSION','版本');
define('_INDEXPHP_OSVERSION','作業系統版本');
define('_INDEXPHP_PLATFORM','硬體平台');
define('_INDEXPHP_DATE','日期');
define('_INDEXPHP_UPTIME','啟動時間');
define('_INDEXPHP_LASTCHANGE','上次設定時間');
define('_INDEXPHP_MEMUSE','記憶體使用率');
define('_INDEXPHP_LOADAVERAGE','系統負載');
define('_INDEXPHP_SHOWPROCESSING','顯示處理程序');
define('_INDEXPHP_DISKS_USE','磁碟空間');
define('_INDEXPHP_NODISK','尚未加入磁碟');

// Status Processes Module
// file: status_process.php
define('_STATSPROCPHP_NAME',_STATUS);
define('_STATSPROCPHP_NAME_DESCRIPTION','程序列表');
define('_STATSPROCPHP_PROC_INF','程序資訊');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION',_MENULEFT_SYSSTATROUTES);
define('_SYSTEMROUTESPHP_MSG_CHANGED','靜態路由已經變更。<br>套用之後才會生效。');
define('_SYSTEMROUTESPHP_INT','網路介面卡');
define('_SYSTEMROUTESPHP_NET','Network');
define('_SYSTEMROUTESPHP_GW','閘道');
define('_SYSTEMROUTESPHP_DESC',_DESC);
define('_SYSTEMROUTESPHP_EDITROUTE','編輯路由');
define('_SYSTEMROUTESPHP_MSGCONFIRM','確定要刪除這個路由資訊嗎？');
define('_SYSTEMROUTESPHP_DELROUTE','刪除路由');
define('_SYSTEMROUTESPHP_ADDROUTE','新增路由');

// Static Routes Edit Module
// file: system_routes_edit.php
define('_SYSTEMROUTESPHP_MSGVALID','請輸入一個有效的目標網段。');
define('_SYSTEMROUTESPHP_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SYSTEMROUTESPHP_MSGVALIDGW','請輸入一個有效的閘道IP位置。');
define('_SYSTEMROUTESPHP_MSGROUTEEXIST','已經有一個目標網段的路由設定存在了。');
define('_SYSTEMROUTESPHP_CHOOSEINT','選擇本路由設定要套用到哪一個網路介面。');
define('_SYSTEMROUTESPHP_DESTNET','目標網段');
define('_SYSTEMROUTESPHP_DESTNETROUTE','本路由設定要套用的目標網段');
define('_SYSTEMROUTESPHP_GWTONET','連線到目標網段的閘道IP位置');
define('_SYSTEMROUTESPHP_DESCREF','可以加註必要的描述文字。(not parsed)');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME',_MENULEFT_SYSTEM);
define('_SYSTEMFIRMWAREPHP_NAMEDESC',_MENULEFT_SYSFIRMWARE);
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','上傳之映像檔並不適用於這個系統');
define('_SYSTEMFIRMWAREPHP_OUTMEM','映像檔上傳失敗(記憶體不足？)');
define('_SYSTEMFIRMWAREPHP_INVSIG','映像檔之數位簽章已經失效。');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','映像檔並沒有通過數位簽章認證。');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','無法驗証映像檔內的數位簽章。');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','映像檔已損毀');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','新的韌體正在安裝中，完成之後FreeNAS將會自動重新開機。');
define('_SYSTEMFIRMWAREPHP_NOTSUP','此系統並不支援韌體上傳功能。.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','這表示你所上傳的映像檔 ' .
		 	'並非由FreeNAS官方所發佈的，繼續安裝可能會導致安全漏洞或是不可預期的錯誤。' .
			'請安裝來自官方，或是你所信任的來源所提供之映像檔，並確定所下載之映像檔並沒有被植入惡意程式。<br><br>'.
			'你要繼續安裝這個映像檔嗎？(自行負責)');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','於下方點選 &quot;開啟韌體升級功能&quot; ，然後選擇將要上傳的映像檔。<br>點選 &quot;韌體升級&quot; 來開啟升級動作');			
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','開啟韌體升級功能');	
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','關閉韌體升級功能');
define('_SYSTEMFIRMWAREPHP_FIRMFILE','韌體映像檔');	
define('_SYSTEMFIRMWAREPHP_UPFIRM','韌體升級');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','系統必須重新開機才能開啟韌體升級功能。');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','一但軔體升級開始，千萬不要中止其動作。FreeNAS在新的韌體上傳完之後便會自動重新開機。所有之前所設定的組態都會被保留。<br>系統最少需要128MB的記憶體以執行此動作。');

// System Advanced Module // file: system_advanced.php 
define('_SYSTEMADVANCEDPHP_NAME',_MENULEFT_SYSTEM); 
define('_SYSTEMADVANCEDPHP_NAMEDESC',_MENULEFT_SYSADVANCED); 
define('_SYSTEMADVANCEDPHP_MSGVALIDTCP','TCP idle timeout必須是一個整數。'); 
define('_SYSTEMADVANCEDPHP_MSGVALIDCERTKEY','驗證與金鑰必須同時提供才能發揮作用。'); 
define('_SYSTEMADVANCEDPHP_MSGVALIDCERT','這個驗證似乎是無效的。'); 
define('_SYSTEMADVANCEDPHP_MSGVALIDKEY','這個金鑰似乎是無效的。'); 
define('_SYSTEMADVANCEDPHP_NOTE','本頁的選項是設計給進階的使用者使用的，同時也<strong>不會</strong>
有本頁相關的技術支援'); define('_SYSTEMADVANCEDPHP_WEBGUISSLKEY','網頁管理介面SSL驗證/金鑰'); 
define('_SYSTEMADVANCEDPHP_WEBCERT','驗證碼'); 
define('_SYSTEMADVANCEDPHP_WEBCERTTEXT','請在此貼上X.509 PEM格式的驗證碼。'); 
define('_SYSTEMADVANCEDPHP_WEBKEY','金鑰'); 
define('_SYSTEMADVANCEDPHP_WEBKEYTEXT','請在此貼上一個PEM格式的RSA私鑰。'); 
define('_SYSTEMADVANCEDPHP_MISC','雜項設定'); define('_SYSTEMADVANCEDPHP_CON',
'Console選單'); define('_SYSTEMADVANCEDPHP_DISCON','關閉console選單'); 
define('_SYSTEMADVANCEDPHP_CONTEXT','這個變更必須等到重新開機之後才會生效。'); 
define('_SYSTEMADVANCEDPHP_FIRM','韌體版本檢查'); define('_SYSTEMADVANCEDPHP_DISFIRM',
'關閉韌體版本檢查功能'); define('_SYSTEMADVANCEDPHP_FIRMTEXT','這會關閉FreeNAS中，<a 
href=system_firmware.php>系統: 韌體</a>頁面的韌體版本檢查功能。'); 
define('_SYSTEMADVANCEDPHP_TCPIDLE','TCP閒置切斷時間'); 
define('_SYSTEMADVANCEDPHP_TCPIDLETEXT',
'閒置太久的TCP連線將會系統切斷。不要把這個數值設定的太高，以免系統內同時存在太多的連線(例:不正常斷線的程式)。預設的數值是2.5小時。'); 
define('_SYSTEMADVANCEDPHP_NAV','導覽'); define('_SYSTEMADVANCEDPHP_NAVTEXT',
'將診斷的頁面連結預設為展開'); define('_SYSTEMADVANCEDPHP_BEEP','提示聲響'); 
define('_SYSTEMADVANCEDPHP_DISBEEP','關閉系統於開/關機時的提示聲響'); 
define('_SYSTEMADVANCEDPHP_SMART','S.M.A.R.T監控程式'); 
define('_SYSTEMADVANCEDPHP_ENSMART','開啟 S.M.A.R.T 監控程式'); 
define('_SYSTEMADVANCEDPHP_SMARTTEXT','透過記錄檔監控S.M.A.R.T裝置。'); 
define('_SYSTEMADVANCEDPHP_TUNING','微調'); define('_SYSTEMADVANCEDPHP_ENTUNING',
'開啟系統核心的微調參數'); define('_SYSTEMADVANCEDPHP_ZEROCONF','ZeroConf'); 
define('_SYSTEMADVANCEDPHP_DISZEROCONF','關閉ZeroConf的服務宣告');


// Interface assing:
// file: interfaces_assign.php
define('_INTASSIGNPHP_NAME','Interfaces');
define('_INTASSIGNPHP_NAMEDESC','指派通訊埠');
define('_INTASSIGNPHP_MSGVALIDPORT','通訊埠 ');
define('_INTASSIGNPHP_MSGVALIDPORTWASASSIG',' 被指派為 ');
define('_INTASSIGNPHP_MSGVALIDINT',' 介面:');
define('_INTASSIGNPHP_INT','介面');
define('_INTASSIGNPHP_PORT','網路卡');
define('_INTASSIGNPHP_DELINT','刪除介面');
define('_INTASSIGNPHP_ADDINT','新增介面');
define('_INTASSIGNPHP_INTTEXT','當按下&quot;儲存&quot;之後，請繼續執行下列的步驟，才能再次連線至FreeNAS：</span></p>
<ul>
  <li><span class=\"vexpl\">變更電腦上的IP位置</span></li>
  <li><span class=\"vexpl\">使用新的IP位置來連接網頁管理介面</span></li>
</ul>');

// Interface LAN/OPT:
// file: interfaces_lan.php
// file: interfaces_opt.php
define('_INTLANPHP_NAME','介面');
define('_INTLANPHP_NAMEDESC','區域網路');
define('_INTPHP_MSGVALIDIP',_MSGVALIDIP);
define('_INTPHP_MSGVALIDMASK',_MSGVALIDNETBIT);
define('_INTPHP_MSGVALIDGW','請輸入一個有效的閘道。');
define('_INTPHP_MSGVALIDMTU','請輸入一個有效的MTU。');
define('_INTPHP_IP','IP位置');
define('_INTPHP_NETMASK','網段遮罩位元數');
define('_INTPHP_GW','閘道');
define('_INTPHP_MTU','MTU');
define('_INTPHP_MTUTEXT','標準MTU是1500，輸入9000則適用於大筆資料的傳輸。');
define('_INTPHP_POLLING','Device polling');
define('_INTPHP_ENPOLLING','開啟device polling');
define('_INTPHP_POLLINGTEXT','Device polling可以讓系統定期的從網路介面擷取新的資料而不必依賴CPU中斷，
					這樣可以減低網路流量對CPU造成的負擔，同時增加系統的整體工作量。但是會稍微增加網路的延遲時間
					(系統每一秒鐘要詢問網路介面卡1000次)。請參訪m0n0wall的網站以確定你的網路卡是否支援Device polling。');
define('_INTPHP_SPEED','速度');
define('_INTPHP_DUPLEX','雙工模式');
define('_INTPHP_TEXT','當按下&quot;儲存&quot;之後，請繼續執行下列的步驟，才能再次連線至FreeNAS：
                    <ul>
                      <li>變更電腦上的IP位置</li>
                      <li>使用新的IP位置來連接網頁管理介面</li>
                    </ul>');
define('_INTPHP_ENOPT','開啟額外的選項');
define('_INTPHP_ENOPTINT',' 網路介面');
define('_INTPHP_DESC',_DESC);
define('_INTPHP_DESCTEXT','在此輸入你對這個網路介面的描述文字。');
define('_INTPHP_STATIC','靜態IP設定');
define('_INTPHP_DHCP','動態DHCP設定');
define('_INTPHP_GENERAL','一般設定');
define('_INTPHP_DHCPHOSTNAME','主機名稱');
define('_INTPHP_DHCPHOSTNAMETEXT','這個數值將會成為DHCP伺服器發送IP時用來識別機器的主機名稱。');
define('_INTPHP_DHCPCLIENTIDENTIFIER','DHCP識別值');
define('_INTPHP_DHCPCLIENTIDENTIFIERTEXT','這個數值將會成為DHCP伺服器發送IP時用來識別機器的參數。');
define('_INTPHP_CALCNETMASKBITS','Calculate netmask bits');
define('_INTPHP_MSGCHANGED','區域網路設定已經變更。<br>套用之後才會生效。');

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
define('_DISKSMANAGEPHP_MSGCHANGED','磁碟組態已經變更。<br>套用之後便會生效。');
define('_DISKSMANAGEPHP_DESC',_DESC);
define('_DISKSMANAGEPHP_STANDBY','進入待命時間');
define('_DISKSMANAGEPHP_FSUNKNOWN','未知/未格式化');
define('_DISKSMANAGEPHP_EDIT','編輯磁碟');
define('_DISKSMANAGEPHP_DEL','刪除磁碟');
define('_DISKSMANAGEPHP_DELCONF','確定要刪除這個磁碟嗎？所有使用此磁碟的元件都會失效！(例：分享)');
define('_DISKSMANAGEPHP_ADD','增加磁碟');
define('_DISKSMANAGEPHP_NOTE','規劃的第一步：新增你的硬碟到磁碟表中。');

// file: disks_manage_edit.php
define('_DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE','這個磁碟已經加入過了。');
define('_DISKSMANAGEEDITPHP_UDMA','UDMA模式');
define('_DISKSMANAGEEDITPHP_UDMATEXT','如果顯示"UDMA_ERROR.... LBA"的訊息，你可以手動選取UDMA的模式。');
define('_DISKSMANAGEEDITPHP_STANDBY','硬碟待命時間');
define('_DISKSMANAGEEDITPHP_STANDBYTEXT','設定在多久時間之後，如果沒有存取動作便將硬碟切換至待命模式。<em>CF卡請勿選用。</em>');
define('_DISKSMANAGEEDITPHP_APM','進階電源管理');
define('_DISKSMANAGEEDITPHP_APMTEXT','用來控制磁碟的電源消耗，相反的會失去一些效能。<em>CF卡請勿選用。</em>');
define('_DISKSMANAGEEDITPHP_ACLEVEL','噪音控制');
define('_DISKSMANAGEEDITPHP_ACLEVELTEXT','用來控制磁碟的運轉噪音程度。<em>CF卡請勿選用。</em>');
define('_DISKSMANAGEEDITPHP_PREFS','已存在之檔案系統');
define('_DISKSMANAGEEDITPHP_PREFSTEXT','如果新增已經包含資料的硬碟，請選擇其原本的檔案系統格式就可以獲取其中的資料。<br>
                     <em>還沒有規劃檔案格式的磁碟機請選擇"尚未格式化"，之後再進入格式化工具選單。</em>');

//file: disks_manage_init.php
define('_DISKSMANAGEINITPHP_NAMEDESC','初始化');
define('_DISKSMANAGEINITPHP_DISKMOUNTERROR','磁碟已經掛載！請先<a href=%s>卸載</a>之後再繼續其他動作。');
define('_DISKSMANAGEINITPHP_INITTEXT','<strong>磁碟初始化系統資訊:</strong>');
define('_DISKSMANAGEINITPHP_INITINFO','\"fdisk: Geom not found\"並不是一個錯誤訊息！\n');
define('_DISKSMANAGEINITPHP_TEXT','這個動作會刪除所有磁區，建立並且格式化第一號磁區成為你指定的檔案系統。');
define('_DISKSMANAGEINITPHP_FORMATDISC','格式化磁碟');
define('_DISKSMANAGEINITPHP_FORMATDISCCONF','確定真的要格式化這個磁碟？所有內部的資料都會消失！');
define('_DISKSPHP_NOMBR','不要刪除MBR');
define('_DISKSPHP_NOMBRTEXT','保留原本的MBR (某些RAID控制卡需要)');

//file: disks_manage_tools.php
define('_DISKSMANAGETOOLS_NAMEDESC','工具');
define('_DISKSMANAGETOOLS_COMMAND','指令');
define('_DISKSMANAGETOOLS_SENDCMD','傳送指令！');
define('_DISKSMANAGETOOLS_RAIDDISKNOTE','在這個磁碟使用<a href="%s">RAID工具</a>！');
define('_DISKSMANAGETOOLS_CMDINFO','指令輸出：');
define('_DISKSMANAGETOOLS_MOUNTNOTE','磁碟目前正在掛載中！系統會將磁碟暫時卸載以進行指令。');
define('_DISKSMANAGETOOLS_UMOUNT','卸載 磁碟/磁區');
define('_DISKSMANAGETOOLS_UMOUNTTEXT','如果選擇的磁碟/磁區正在掛載中，系統會將磁碟暫時卸載以進行指令，否則指令只能在唯讀模式下運作。');

//file: disks_manage_iscsi.php
define('_DISKSMANAGEPHP_NAMEDESC','管理');
define('_DISKSMANAGEISCSIPHP_NAMEDESC','iSCSI');
define('_DISKSMANAGEISCSIPHP_MSGVALIDIP',_MSGVALIDIP);
define('_DISKSMANAGEISCSIPHP_INITIATOR','iSCSI初始');
define('_DISKSMANAGEISCSIPHP_TARGETIP','目標IP位置');
define('_DISKSMANAGEISCSIPHP_TARGETIPTEXT','iSCSI裝置的IP位置');
define('_DISKSMANAGEISCSIPHP_TARGETNAME','目標名稱');
define('_DISKSMANAGEISCSIPHP_TARGETNAMETEXT','iSCSI目標的名稱');

//Software RAID
// Comon value
define('_DISKSRAIDPHP_MANAGE','管理RAID');
define('_DISKSRAIDPHP_FORMAT','格式化RAID');
define('_DISKSRAIDPHP_TOOLS','工具');
define('_DISKSRAIDPHP_INFO','資訊');
define('_DISKSRAIDPHP_VOLUME','Volume名稱');

// Software RAID common word
define('_DISKSRAIDPHP_GVINUM','Geom Vinum');
define('_DISKSRAIDPHP_GMIRROR','Geom Mirror');
define('_DISKSRAIDPHP_GSTRIPE','Geom Stripe');
define('_DISKSRAIDPHP_GCONCAT','Geom Concat');
define('_DISKSRAIDPHP_GRAID5','Geom Raid5');
define('_DISKSRAIDPHP_UNSTABLE','(測試中)');

//file: disks_raid.php
define('_DISKSRAIDPHP_NAMEDESC','RAID');
define('_DISKSRAIDPHP_MSGCHANGED','Raid組態已經變更。<br>套用變更之後才會生效。');
define('_DISKSRAIDPHP_TYPE','類型');
define('_DISKSRAIDPHP_EDITRAID','編輯RAID');
define('_DISKSRAIDPHP_DELRAID','刪除RAID');
define('_DISKSRAIDPHP_ADDRAID','新增RAID');
define('_DISKSRAIDPHP_DELCONF','確定要刪除這個RAID嗎？所有使用此RAID的元件都會失效！(例：分享)');
define('_DISKSRAIDPHP_NOTE','選擇性的規劃步驟：利用你<a href="disks_manage.php">剛才設定的磁碟</a>來建立一個新的RAID磁碟。<br>請等待"up"的狀態出現之後再進行格式化與掛載的動作！');

//file: disks_raid_edit.php
define('_DISKSRAIDEDITPHP_NAMEDESC',_EDIT);
define('_DISKSRAIDEDITPHP_MSGADDDISKFIRST','請先加入磁碟。');
define('_DISKSRAIDEDITPHP_MSGVALIDNAME','裝置名稱只可以由 a-z, A-Z, 0-9 組成。');
define('_DISKSRAIDEDITPHP_MSGEXIST','這個裝置已經是RAID成員。');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID0','架構RAID 0最少要有兩個磁碟。');
define('_DISKSRAIDEDITPHP_MSGVALIDJBOD','架構JBOD最少要有兩個磁碟。');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID1','架構RAID 1最少要有兩個磁碟。');
define('_DISKSRAIDEDITPHP_MSGVALIDRAID5','架構RAID 5最少要有三個磁碟。');
define('_DISKSRAIDEDITPHP_MSGERROR','錯誤！不能儲存RAID設定值。\n');
define('_DISKSRAIDEDITPHP_RAIDNAME','Raid名稱');
define('_DISKSRAIDEDITPHP_GVBALANCEALGO','效能均衡演算法');
define('_DISKSRAIDEDITPHP_GVBALANCEALGOTEXT','選擇讀取檔案之效能均衡演算法.');
define('_DISKSRAIDEDITPHP_RAID0','striping');
define('_DISKSRAIDEDITPHP_JBOD','JBOD');
define('_DISKSRAIDEDITPHP_RAID1','mirroring');
define('_DISKSRAIDEDITPHP_RAID5','rotated block-interleaved parity');
define('_DISKSRAIDEDITPHP_MEMBERS','RAID volume的成員');

//file: disks_raid_info.php
define('_DISKSRAIDINFOPHP_TEXT','軟體RAID資訊與狀態');

//file: disks_raid_init.php
define('_DISKSRAIDINITPHP_INFO','UFS格式化結果輸出：');
define('_DISKSRAIDINITPHP_TEXT','這會將此RAID磁碟格式化為Unix檔案系統(UFS)。');
define('_DISKSRAIDINITPHP_FORMAT','格式化UFS磁碟！');

//file: disks_raid_tools.php
define('_DISKSRAIDTOOLSPHP_OBJNAME','物件名稱');
define('_DISKSRAIDTOOLSPHP_COMMAND','指令');
define('_DISKSRAIDTOOLSPHP_INFO','GVINUM指令結果:');
define('_DISKSRAIDTOOLSPHP_SENDCMD','傳送指令！');
define('_DISKSRAIDTOOLSPHP_TEXT','1. 這些特殊的命令只用來除錯而已！<br>2. RAID磁碟在建立之後會自動的啟動，不需要利用activate工具再啟動一次。');

//Disk Mount Point
// Comon value
define('_DISKSMOUNTPHP_NAME','掛載點');
define('_DISKSMOUNTPHP_SHARENAME','分享名稱');
define('_DISKSMOUNTPHP_MANAGE','管理');
define('_DISKSMOUNTPHP_TOOLS','工具');

// disks_mount.php
define('_DISKSMOUNTPHP_MSGCHANGED','掛載點已經變更。<br>套用之後才會生效。');
define('_DISKSMOUNTPHP_NOTE','規劃的第二步：選擇<a href="disks_manage.php">新增磁碟</a>所使用的檔案系統<p>');
define('_DISKSMOUNTPHP_DELCONF','確定要刪除這個掛載點嗎？所有使用此掛載點的元件都會失效！(例：分享)');
define('_DISKSMOUNTPHP_DEL','刪除掛載點');
define('_DISKSMOUNTPHP_ADD','新增掛載點');

// disks_mount_edit.php
define('_DISKSMOUNTEDITPHP_MSGVALIDNAME','分享名稱只可以包含以下字元：a-z, A-Z, 0-9, _ , -.');
define('_DISKSMOUNTEDITPHP_MSGVALIDDESC','描述文字包含了無效的字元。');
define('_DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS','不能掛載系統區(第1區)，資料分割區為第2區。');
define('_DISKSMOUNTEDITPHP_MSGVALIDDUP','輸入了存在的分享名稱。');
define('_DISKSMOUNTEDITPHP_MSGVALIDDISK','這個磁碟/磁區之前已經設定過了。');
define('_DISKSMOUNTEDITPHP_TEXT','1. You can\'t mount the partition \'%s\' where the config file is stored.<br>2. FreeBSD NTFS has lots of bugs.');

// disks_mount_tools.php
define('_DISKSMOUNTTOOLS_COMMAND','指令');
define('_DISKSMOUNTTOOLS_SENDCMD','傳送指令！');
define('_DISKSMOUNTTOOLS_CMDINFO','指令結果：');
define('_DISKSMOUNTTOOLS_MOUNTTEXT','掛載中...');
define('_DISKSMOUNTTOOLS_UMOUNTTEXT','卸載中...');

// Service: CIFS/SAMBA
// Common text
define('_SRVCIFS_SETTINGS','設定');
define('_SRVCIFS_SHARE','分享');
define('_SRVCIFS_SHARES','分享');

// file: services_samba.php
define('_SRVCIFS_NAMEDESC','CIFS');
define('_SRVCIFS_MSGVALIDNETBIOSNAME','Netbios名稱輸入了無效的字元。');
define('_SRVCIFS_MSGVALIDWORKGOUP','工作群組內輸入了無效的字元。');
define('_SRVCIFS_MSGVALIDWINS','WINS伺服器必須是IP位置。');
define('_SRVCIFS_MSGVALIDSNDBUFF','傳送緩衝區必須是一個數字。');
define('_SRVCIFS_MSGVALIDRCVBUFF','接收緩衝區必須是一個數字。');
define('_SRVCIFS_SHARE','CIFS檔案分享');
define('_SRVCIFS_AUTH','認證方式');
define('_SRVCIFS_NETBIOSNAME','NetBios名稱');
define('_SRVCIFS_WORKGROUP','工作群組');
define('_SRVCIFS_WORKGROUPTEXT','選擇將要加入的工作群組');
define('_SRVCIFS_DESCTEXT','伺服器描述文字。通常可以留白。');
define('_SRVCIFS_DOSCHARSET','Dos編碼方式');
define('_SRVCIFS_UNIXCHARSET','Unix編碼方式');
define('_SRVCIFS_LOGLEVEL','紀錄檔層級');
define('_SRVCIFS_LOCALMASTER','Local Master Browser');
define('_SRVCIFS_LOCALMASTERTEXT','讓FreeNAS嘗試成為local master browser。');
define('_SRVCIFS_TIMESRV','時間伺服器');
define('_SRVCIFS_TIMESRVTEXT','設定FreeNAS成為Windows客戶端的時間伺服器。');
define('_SRVCIFS_WINS','WINS伺服器');
define('_SRVCIFS_WINSTEXT','WINS伺服器的IP位置。');         
define('_SRVCIFS_RECYCLE','資源回收桶');
define('_SRVCIFS_ENRECYCLE','開啟資源回收桶');
define('_SRVCIFS_ENRECYCLETEXT','開啟資源回收筒的功能，可救回刪除之檔案。');
define('_SRVCIFS_SNBBUFF','傳送緩衝區');
define('_SRVCIFS_SNBBUFFTEXT','傳送緩衝區的大小(預設16384).');
define('_SRVCIFS_RCVBUFF','接收緩衝區');
define('_SRVCIFS_RCVBUFFTEXT','接收緩衝區的大小(預設16384).');

// file: services_samba_share.php
define('_SRVCIFSSHARE_MSGCHANGED','分享設定已經變更。<br>套用之後才會生效。');
define('_SRVCIFSSHARE_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHARE_BROWSEABLE','提供瀏覽');
define('_SRVCIFSSHARE_DESC',_DESC);
define('_SRVCIFSSHARE_EDIT','編輯');

// file: services_samba_share_edit.php
define('_SRVCIFSSHAREEDIT_SHARENAME',_DISKSMOUNTPHP_SHARENAME);
define('_SRVCIFSSHAREEDIT_DESC',_DESC);
define('_SRVCIFSSHAREEDIT_BROWSEABLE','提供瀏覽');
define('_SRVCIFSSHAREEDIT_BROWSEABLETEXT','控制這個分享資料夾能不能直接的被瀏覽器看到。');

// Service: FTP
// file: services_ftp.php
define('_SRVFTP_NAMEDESC','FTP');
define('_SRVFTP_MSGVALIDPORT',_MSGVALIDTCPPORT);
define('_SRVFTP_MSGVALIDMAXCLIENT','連線數目上線必須是一個數字。');
define('_SRVFTP_MSGVALIDMAXIP','相同IP的連線數目必須是一個數字。');
define('_SRVFTP_MSGVALIDIDLE','失效時間必須是一個數字。');
define('_SRVFTP_MSGVALIDPASVIP','被動式IP位置必須是一個對外的IP。');
define('_SRVFTP_MSGVALIDPASVMAX','被動式連接埠起點必須是一個有效的port number.');
define('_SRVFTP_MSGVALIDPASVMIN','被動式連接埠終點必須是一個有效的port number.');
define('_SRVFTP_MSGVALIDAUTH','請至少選擇 匿名/本地使用者 認證。');
define('_SRVFTP_FTPSERVER','FTP伺服器');
define('_SRVFTP_TCP',_TCPPORT);
define('_SRVFTP_MAXCLIENT','連線數目');
define('_SRVFTP_MAXCLIENTTEXT','連線數目上限。');
define('_SRVFTP_MAXIP','相同IP的連線數目');
define('_SRVFTP_MAXIPTEXT','來自相同IP的連線數目上限(0 = 不限制).');
define('_SRVFTP_TIMEOUT','失效時間');
define('_SRVFTP_TIMEOUTTEXT','自動切斷連線時間。');
define('_SRVFTP_ANONYMOUS','匿名登入');
define('_SRVFTP_ENANONYMOUS','是否開啟匿名登入');
define('_SRVFTP_AUTH','本機使用者');
define('_SRVFTP_ENAUTH','是否開啟本機使用者登入');
define('_SRVFTP_FXP','FXP');
define('_SRVFTP_ENFXP','開啟FXP協定支援。');
define('_SRVFTP_FXPTEXT','FXP是支援兩個遠端伺服器互傳檔案的通訊協定。(有安全疑慮！).');
define('_SRVFTP_NATMODE','NAT模式');
define('_SRVFTP_ENNATMODE','強迫NAT模式。');
define('_SRVFTP_NATMODETEXT','如果你的FTP伺服器是架設在NAT的環境之下，請開啟這個功能。');
define('_SRVFTP_PASSIVEIP','被動式IP位置');
define('_SRVFTP_PASSIVEIPTEXT','FTP伺服器的被動式連線IP位置。');
define('_SRVFTP_PASVMIN','被動式連接埠起點');
define('_SRVFTP_PASVMINTEXT','被動式連線埠的區段起點(0 = 不限制)');
define('_SRVFTP_PASVMAX','被動式連接埠終點');
define('_SRVFTP_PASVMAXTEXT','被動式連線埠的區段終點(0 = 不限制)');
define('_SRVFTP_BANNER','歡迎文字');
define('_SRVFTP_BANNERTEXT','當使用者剛連接上FTP所顯示的訊息。');

// Service: NFS
// file: services_nfs.php
define('_SRVNFS_NAMEDESC','NFS');
define('_SRVNFS_MSGVALIDNET','必須指定一個有效的網域。');
define('_SRVNFS_MSGVALIDNETBIT',_MSGVALIDNETBIT);
define('_SRVNFS_NFSERVER','NFS伺服器');
define('_SRVNFS_MAPALLROOT','所有人視為root');
define('_SRVNFS_MAPALLROOTTEXT','讓所有的使用者都有root權限。');
define('_SRVNFS_AUTHNET','認可網路');
define('_SRVNFS_AUTHNETTEXT','設定可以使用NFS分享的網路區段');
define('_SRVNFS_AUTHNETMASK','網段遮罩位元數');
define('_SRVNFS_TEXT','輸出的目錄名稱是：/mnt/sharename');

// Service: RSYNC
// Common
define('_SRVRYNC_SERVER','伺服器');
define('_SRVRYNC_CLIENT','客戶端');
define('_SRVRYNC_LOCAL','本地端');

// file: services_rsyncd.php
define('_SRVRYNCD_NAMEDESC','RSYNCD');
define('_SRVRYNCD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVRYNCD_RSYNCD','Rsync伺服器');
define('_SRVRYNCD_READONLY','唯讀');
define('_SRVRYNCD_MAPUSER','對應到本地使用者');
define('_SRVRYNCD_TCPORT',_TCPPORT);
define('_SRVRYNCD_TCPORTTEXT','替代的TCP連接埠。');
define('_SRVRYNCD_MAXCON','連線數目上線');
define('_SRVRYNCD_MAXCONTEXT','同時最高連線數目。預設為0(不限制)。');
define('_SRVRYNCD_MOTD','MOTD');
define('_SRVRYNCD_MOTDTEXT','每日新訊(每次登入時顯示)。');

// file: services_rsyncd_client.php
define('_SRVRYNCC_MSGMPFIRST','請先設定掛載點。');
define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
define('_SRVRYNCC_RSYNCC','Rsync本地端檔案同步');
define('_SRVRYNCC_REMOTESERVER','遠端的RSYNC伺服器');
define('_SRVRYNCC_REMOTEID','遠端的RSYNC伺服器IP位置');
define('_SRVRYNCC_OPTIONS','RSYNC選項');
define('_SRVRYNCC_OPTDEL','刪除本地端已經不存在的檔案。');
define('_SRVRYNCC_SHARES','指定要同步的目錄');
define('_SRVRYNCC_SHARESTEXT','遠端與本地端的分享目錄必須相同。');
define('_SRVRYNCC_TIME','同步啟動時間');
define('_SRVRYNCC_TEXT',_MSGSCHED_TEXT);

// file: services_rsyncd_local.php
define('_SRVRYNCL_MSGVALID','來源與目的不能相同！');
define('_SRVRYNCC_RSYNCL','Rsync本地備援');
define('_SRVRYNCL_SOURCE','來源位置');
define('_SRVRYNCL_SOURCETEXT','選擇來源檔案的掛載點。');
define('_SRVRYNCL_DESTINATION','目地位置');
define('_SRVRYNCL_DESTINATIONTEXT','選擇目的位置的掛載點(備援磁碟機)。');


// Service: SSHD
// file: services_sshd.php
define('_SRVSSHD_NAMEDESC','SSHD');
define('_SRVSSHD_MSGVALIDTCPPORT',_MSGVALIDTCPPORT);
define('_SRVSSHD_MSGVALIDKEY','這個鑰匙似乎是無效的。');
define('_SRVSSHD_SSHD','SSH伺服器');
define('_SRVSSHD_READONLY','唯讀');
define('_SRVSSHD_TCPORT',_TCPPORT);
define('_SRVSSHD_TCPORTTEXT',_SRVRYNCD_TCPORTTEXT);
define('_SRVSSHD_PERMITROOTLOGIN','root准許登入');
define('_SRVSSHD_PERMITROOTLOGINTEXT','指定超級使用者 (root)是否可以透過SSH直接登入。');
define('_SRVSSHD_KEY','SSH key');
define('_SRVSSHD_PRIVATEKEY','私鑰');
define('_SRVSSHD_PRIVATEKEYTEXT','在此貼上PEM格式的私鑰。');

// Service: Unison
// file: services_unison.php
define('_SRVUNSN_NAMEDESC','Unison');
define('_SRVUNSN_MSGVALIDWRKDIR','設定值內的分享/工作目錄不存在。');
define('_SRVUNSN_UNISON','Unison 檔案同步機制');
define('_SRVUNSN_SHARE','分享');
define('_SRVUNSN_SHAREDESC','確定你有足夠的空間來複製所有將要同步的檔案');
define('_SRVUNSN_WORKDIR','工作目錄');
define('_SRVUNSN_WORKDIRDESC','選擇工作檔案要儲存的位置');
define('_SRVUNSN_MAKEDIR','建立');
define('_SRVUNSN_MAKEDIRLABEL','如果你還沒有工作目錄，請建立一個');
define('_SRVUNSN_NOTESSH','Unison服務必須配合<a href="/services_sshd.php">SSHD</a>，而存取的<a href="/access_users.php">使用者</a>必須要有Shell權限。');

// Service: UPnP
// file: services_upnp.php
define('_SRVUPNP_NAMEDESC','UPnP');
define('_SRVUPNP_UPNP','UPnP影音多媒體伺服器');
define('_SRVUPNP_NAME','名稱');
define('_SRVUPNP_NAMETEXT','UPnP friendly name.');
define('_SRVUPNP_INTERFACE','網路介面');
define('_SRVUPNP_INTERFACETEXT','要使用的網路介面');
define('_SRVUPNP_CONTENT','內容');
define('_SRVUPNP_CONTENTTEXT','分享的資料夾(space or CSV list).');

// Service: AFP
// file: services_afp.php
define('_SRVAFP_NAMEDESC','AFP');
define('_SRVAFP_MSGVALIDAUTH','請至少選擇一種認證方式。');
define('_SRVAFP_AFPSRV','AFP伺服器');
define('_SRVAFP_AFPNAME','伺服器名稱');
define('_SRVAFP_AUTH','認證');
define('_SRVAFP_ENABLEGUEST','Guest可以登入。');
define('_SRVAFP_ENABLELOCAL','啟用本地端使用者可以登入。');

// Access
// common words
define('_ACCESS_NAME','權限控制');
define('_ACCESS_USER','使用者');
define('_ACCESS_USERS','使用者');
define('_ACCESS_GROUP','群組');
define('_ACCESS_GROUPS','群組');
define('_ACCESS_FULLNAME','全名');

// file: access_users.php
define('_ACCESSUSERS_MSGCHANGED','使用者名單已經變更。<br>套用變更之後才會生效。');
define('_ACCESSUSERS_EDITUSER','編輯使用者');
define('_ACCESSUSERS_DELUSER','刪除使用者');
define('_ACCESSUSERS_CONFDEL','確定要刪除此使用者帳號？');
define('_ACCESSUSERS_ADDUSER','新增使用者');

// file: access_users_edit.php
define('_ACCESSUSERSEDIT_MSGGROUPFIRST','你必須先建立一個群組。');
define('_ACCESSUSERSEDIT_MSGVALIDLOGIN','登入帳號含有無效的字元。');
define('_ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED','預定的登入帳號。');
define('_ACCESSUSERSEDIT_MSGVALIDFULLNAME','全名包含無效的字元。');
define('_ACCESSUSERSEDIT_MSGVALIDDUPLICATE','使用者帳號已經存在。');
define('_ACCESSUSERSEDIT_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSUSERSEDIT_MSGVALIDPASSWDBADCHAR','密碼包含無效的 : 字元');
define('_ACCESSUSERSEDIT_LOGIN',_LOGIN);
define('_ACCESSUSERSEDIT_LOGINTEXT','用來登入的使用者帳號');
define('_ACCESSUSERSEDIT_FULLNAME',_ACCESS_FULLNAME);
define('_ACCESSUSERSEDIT_FULLNAMETEXT','使用者的全名');
define('_ACCESSUSERSEDIT_PASSWORD','密碼');
define('_ACCESSUSERSEDIT_PASSWORDTEXT','使用者自行設定的密碼');
define('_ACCESSUSERSEDIT_PASSWORDCONF','確認密碼');
define('_ACCESSUSERSEDIT_PASSWORDCONFTEXT','請輸入二次密碼');
define('_ACCESSUSERSEDIT_GROUPMEMBER','群組成員');
define('_ACCESSUSERSEDIT_FULLSHELL','Shell權限');
define('_ACCESSUSERSEDIT_FULLSHELLTEXT','讓這個使用者可以使用shell。');

// file: access_users_groups.php
define('_ACCESSGROUPS_MSGCHANGED','群組列表已經變更。<br>套用變更之後才會生效。');
define('_ACCESSGROUPS_DESC',_DESC);
define('_ACCESSGROUPS_GROUPEDIT','編輯群組');
define('_ACCESSGROUPS_GROUPDEL','刪除群組');
define('_ACCESSGROUPS_GROUPDELCONF','確定要刪除此群組？');
define('_ACCESSGROUPS_GROUPADD','新增群組');

// file: access_users_groups_edit.php
define('_ACCESSGROUPSEDIT_MSGVALIDNAME','群組名稱內有無效的字元。');
define('_ACCESSGROUPSEDIT_MSGVALIDDESC','群組描述內有無效的字元。');
define('_ACCESSGROUPSEDIT_MSGVALIDDUPLICATE','這個群組已經建立過了。');
define('_ACCESSGROUPSEDIT_NAME','名稱');
define('_ACCESSGROUPSEDIT_NAMETEXT','群組名稱。');
define('_ACCESSGROUPSEDIT_DESC',_DESC);
define('_ACCESSGROUPSEDIT_DESCTEXT','群組描述文字。');

// file: access_ad.php
define('_ACCESSAD_NAME','Active Directory');
define('_ACCESSAD_MSGVALIDIP',_MSGVALIDIP);
define('_ACCESSAD_MSGVALIDPASSERROR',_MSGVALIDPASSWORD);
define('_ACCESSAD_ADNAME','AD伺服器名稱');
define('_ACCESSAD_ADNAMETEXT','AD or PDC 的名稱。');
define('_ACCESSAD_IP','AD伺服器IP');
define('_ACCESSAD_IPTEXT','MS Active Directory伺服器的IP位置。');
define('_ACCESSAD_DOMAIN','網域名稱');
define('_ACCESSAD_DOMAINTEXT','網域名稱(in old format)');
define('_ACCESSAD_ADMINAME','管理者帳號');
define('_ACCESSAD_ADMINNAMETEXT','請在此設定管理者的帳號。');
define('_ACCESSAD_ADMINPASS','管理者密碼');
define('_ACCESSAD_ADMINPASSTEXT','請在此設定管理者的密碼，並且輸入兩次以確認無誤。');

// Diagnostics
define('_DIAG_NAME','檢測');

// file: shutdown.php
define('_SHUT_NAME',_MENULEFT_CMDSHUTDOWNSYS);
define('_SHUT_MSGSHUT','系統關閉中，可能需要一分鐘的時間...');
define('_SHUT_CONF','確定要關閉系統？');

// file: shutdown_sched.php
define('_SHUTSCHED_NAME','排程關機');
define('_SHUTSCHED_TIME','關機時間');
define('_SHUTSCHED_TEXT',_MSGSCHED_TEXT);

// file: diag_edit.php
define('_DIAGEDITPHP_NAMEDESC','檔案編輯');
define('_DIAGEDITPHP_FILEPATH','檔案路徑');
define('_DIAGEDITPHP_SAVEMSG','將檔案儲存至');
define('_DIAGEDITPHP_FILENOTFOUND','沒有這個檔案');
define('_DIAGEDITPHP_ROWS','列');
define('_DIAGEDITPHP_COLS','欄');
define('_DIAGEDITPHP_HIGHLIGHTING','語法高亮度顯示');
define('_DIAGEDITPHP_HIGHLIGHTINGENABLED','開啟');
define('_DIAGEDITPHP_HIGHLIGHTINGDISABLED','關閉');

// file: exec.php
define('_DIAGEXECPHP_NAMEDESC','執行指令');

// file: reboot.php
define('_DIAGREBOOT_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGREBOOT_NAMEDESC',_MENULEFT_CMDREBOOTSYS);
define('_DIAGREBOOT_REBOOTMSG','系統重新啟動中，可能需要一分鐘的時間...');
define('_DIAGREBOOT_REBOOTCONF','確定要重新啟動系統？');

// file: license.php
define('_LICENSE_NAME','License');

// file: status_wireless.php
define('_STATSWIRELESS_NAME',_MENULEFT_STATUS);
define('_STATSWIRELESS_NAMEDESC','無線網路');
define('_STATSWIRELESS_INTERFACE','%s 介面 (SSID \'%s\')');
define('_STATSWIRELESS_SIGNALSTRENGTHCACHE','訊號強度快取');
define('_STATSWIRELESS_MACADDRESS','MAC位置');
define('_STATSWIRELESS_IPADDRESS','IP位置');
define('_STATSWIRELESS_SIGNAL','信號');
define('_STATSWIRELESS_NOISE','雜訊');
define('_STATSWIRELESS_QUALITY','品質');
define('_STATSWIRELESS_ASSOCIATEDSTATIONS','連接的無線網路');
define('_STATSWIRELESS_TXRATES','TX速率');
define('_STATSWIRELESS_MSGNOWINTFOUND','並沒有找到支援的無線網路卡(使用wi[n]驅動的網路卡才有支援).');

// file: status_interfaces.php
define('_STATSINTERFACES_NAME',_MENULEFT_STATUS);
define('_STATSINTERFACES_NAMEDESC',_MENULEFT_INTERFACES);
define('_STATSINTERFACES_INTERFACE','%s 介面');
define('_STATSINTERFACES_STATUS','狀態');
define('_STATSINTERFACES_DHCP','DHCP');
define('_STATSINTERFACES_PPPOE','PPPoE');
define('_STATSINTERFACES_PPTP','PPTP');
define('_STATSINTERFACES_MACADDRESS','MAC位置');
define('_STATSINTERFACES_IPADDRESS','IP位置');
define('_STATSINTERFACES_SUBNETMASK','網段遮罩');
define('_STATSINTERFACES_GATEWAY','閘道');
define('_STATSINTERFACES_ISPDNSSERVERS','ISP DNS伺服器');
define('_STATSINTERFACES_MEDIA','Media');
define('_STATSINTERFACES_CHANNEL','頻道');
define('_STATSINTERFACES_SSID','SSID');
define('_STATSINTERFACES_IOPACKETS','I/O封包');
define('_STATSINTERFACES_IOERRORS','I/O錯誤');
define('_STATSINTERFACES_COLLISIONS','封包衝撞');

// file: status_graph_cpu.php
define('_STATSGRAPHCPU_NAME',_MENULEFT_STATUS);
define('_STATSGRAPHCPU_NAMEDESC','CPU負載');

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
define('_INTVLAN_INTERFACE','網路介面');
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
define('_DIAGTRACEROUTE_NOTE','Traceroute需要一段時間才能完成。你可以隨時停止並觀看結果。');

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
define('_DIAGINFOS_PARTITIONS','磁區');
define('_DIAGINFOS_SMART','S.M.A.R.T.');
define('_DIAGINFOS_ATAIDLE','ATAidle');
define('_DIAGINFOS_SPACEUSED','使用空間');
define('_DIAGINFOS_MOUNTS','掛載點');
define('_DIAGINFOS_SOFTWARERAID',_SOFTRAID);
define('_DIAGINFOS_ISCSI','iSCSI');
define('_DIAGINFOS_MSDOMAIN','MS網域');

// file: diag_infos.php
define('_DIAGINFOSDISKS_ATA_INFO','已偵測到的ATA磁碟');
define('_DIAGINFOSDISKS_SCSI_INFO','已偵測到的SCSI磁碟');

// file: diag_infos_mount.php
define('_DIAGINFOSMOUNT_INFO','掛載點一覽');

// file: diag_infos_space.php
define('_DIAGINFOSSPACE_INFO','可用空間');

// file: diag_defaults.php
define('_DIAGDEFAULTS_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGDEFAULTS_NAMEDESC',_MENULEFT_CMDFACTORYDEF);

// file: diag_backup.php
define('_DIAGBACKUP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGBACKUP_NAMEDESC',_MENULEFT_CMDBACKUPRESTORE);
define('_DIAGBACKUP_RESTORENOTE','FreeNAS復原設定組態之後將會自動重新開機。');

// file: diag_arp.php
define('_DIAGARP_NAME',_MENULEFT_DIAGNOSTICS);
define('_DIAGARP_NAMEDESC',_MENULEFT_DIAGARPTAB);
define('_DIAGARP_IPADDRESS','IP位置');
define('_DIAGARP_MACADDRESS','MAC位置');
define('_DIAGARP_HOSTNAME','主機名稱');
define('_DIAGARP_INTERFACE','網路介面');
define('_DIAGARP_DEL','刪除ARP項目');
define('_DIAGARP_DELALL','移除所有ARP table內的項目');
define('_DIAGARP_NOTE','如果你在<a href="diag_logs_settings.php">診斷: 記錄檔</a>頁面，開啟了&quot;轉換成網域名稱&quot;的功能。IP位置會被轉換成網域名稱。');

// file: status_graph.php
define('_STATSGRAPH_NAME',_MENULEFT_STATUS);
define('_STATSGRAPH_NAMEDESC','流量圖表');

// file: graph.php
// Note for translator: Do not use international html coding here
//from TradChinese problem: UTF8 encoded character are not accpetible in this section...
//define('_GRAPH_ERRORGETIFDATA','無法獲取網路流量資料...');
define('_GRAPH_ERRORGETIFDATA','Cannot get data about interface');
//define('_GRAPH_IN','接收');
define('_GRAPH_IN','In');
//define('_GRAPH_OUT','傳送');
define('_GRAPH_OUT','Out');
define('_GRAPH_SWITCHTO','Switch to');
//define('_GRAPH_AUTOSCALE','自動符合檢視大小');
define('_GRAPH_AUTOSCALE','AutoScale');
define('_GRAPH_AUTOSCALE_FOLLOW','Follow');
define('_GRAPH_AUTOSCALE_UP','Up');
define('_GRAPH_SHOWLAST','Graph shows last');
//define('_GRAPH_COLLECTDATA','正在初始化，請稍候...');
define('_GRAPH_COLLECTDATA','Collecting initial data, please wait...');
?>
