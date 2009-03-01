#!/usr/local/bin/php
<?php
/*
	status.php
	Copyright © 2007-2009 Volker Theile (votdev@gmx.de)
  All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require("guiconfig.inc");
require("report.inc");

$statusreport = new StatusReport();
$statusreport->IsHTML(true);
$statusreport->AddArticle(new StatusReportArticleCmd("Version","cat /etc/prd.version"));
$statusreport->AddArticle(new StatusReportArticleCmd("Revision","cat /etc/prd.revision"));
$statusreport->AddArticle(new StatusReportArticleCmd("Platform","cat /etc/platform"));
$statusreport->AddArticle(new StatusReportArticleCmd("System uptime","uptime"));
$statusreport->AddArticle(new StatusReportArticleCmd("dmesg","/sbin/dmesg"));
$statusreport->AddArticle(new StatusReportArticleCmd("Interfaces","/sbin/ifconfig -a"));
$statusreport->AddArticle(new StatusReportArticleCmd("Routing tables","netstat -nr"));
$statusreport->AddArticle(new StatusReportArticleCmd("Firewall","ipfw -at list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Processes","ps xauww"));
$statusreport->AddArticle(new StatusReportArticleCmd("Network performances","netstat -m"));
$statusreport->AddArticle(new StatusReportArticleCmd("Memory","top -b 0 | grep Mem"));
$statusreport->AddArticle(new StatusReportArticleCmd("Swap usage","/usr/sbin/swapinfo"));
$statusreport->AddArticle(new StatusReportArticleCmd("ATA disk","/sbin/atacontrol list"));
$statusreport->AddArticle(new StatusReportArticleCmd("SCSI disk","/sbin/camcontrol devlist"));
$disklist = get_physical_disks_list();
foreach ($disklist as $disknamek => $disknamev) {
	$statusreport->AddArticle(new StatusReportArticleCmd("S.M.A.R.T. [/dev/{$disknamek}]","/usr/local/sbin/smartctl -a /dev/{$disknamek}"));
}
$statusreport->AddArticle(new StatusReportArticleCmd("Geom Concat","/sbin/gconcat list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Geom Stripe","/sbin/gstripe list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Geom Mirror","/sbin/gmirror list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Geom RAID5","/sbin/graid5 list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Geom Vinum","/sbin/gvinum list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Mount point","/sbin/mount"));
$statusreport->AddArticle(new StatusReportArticleCmd("Free disk space","/bin/df -h"));
$statusreport->AddArticle(new StatusReportArticleCmd("Encrypted disks","/sbin/geli list"));
$statusreport->AddArticle(new StatusReportArticleCmd("rc.conf","cat /etc/rc.conf"));
$statusreport->AddArticle(new StatusReportArticleCmd("resolv.conf","cat /var/etc/resolv.conf"));
$statusreport->AddArticle(new StatusReportArticleCmd("hosts","cat /etc/hosts"));
$statusreport->AddArticle(new StatusReportArticleCmd("hosts.allow","cat /etc/hosts.allow"));
$statusreport->AddArticle(new StatusReportArticleCmd("crontab","cat /var/etc/crontab"));
$statusreport->AddArticle(new StatusReportArticleCmd("dhclient.conf","cat /etc/dhclient.conf"));
$statusreport->AddArticle(new StatusReportArticleCmd("smb.conf","cat /var/etc/smb.conf"));
$statusreport->AddArticle(new StatusReportArticleCmd("sshd.conf","cat /var/etc/ssh/sshd_config"));
$statusreport->AddArticle(new StatusReportArticleCmd("mdnsresponder.conf","cat /var/etc/mdnsresponder.conf"));
$statusreport->AddArticle(new StatusReportArticleCmd("Last 200 system log entries","/usr/sbin/clog /var/log/system.log 2>&1 | tail -n 200"));
$statusreport->AddArticle(new StatusReportArticleCmd("/conf","ls /conf"));
$statusreport->AddArticle(new StatusReportArticleCmd("/var/etc","ls /var/etc"));
$statusreport->AddArticle(new StatusReportArticleCmd("/var/run","ls /var/run"));
$statusreport->AddArticle(new StatusReportArticleCmd("config.xml","/usr/local/bin/xml ed -P -u \"//*/password\" -v \"xxxxx\" -u \"//system/email/from\" -v \"xxxxx\" -u \"//statusreport/to\" -v \"xxxxx\" /conf/config.xml"));

// Generate status report.
echo $statusreport->Generate();
?>
