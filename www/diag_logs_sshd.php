#!/usr/local/bin/php
<?php
/*
	diag_logs_sshd.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard <olivier@freenas.org>.
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
require("diag_logs.inc");

$pgtitle = array(gettext("Diagnostics"), gettext("Logs"), gettext("SSHD"));

$nentries = $config['syslogd']['nentries'];
if (!$nentries)
	$nentries = 50;

if ($_POST['clear']) {
	exec("/usr/sbin/clog -i -s 32768 /var/log/sshd.log");
	/* redirect to avoid reposting form data on refresh */
	header("Location: diag_logs_sshd.php");
	exit;
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
    <li class="tabinact"><a href="diag_logs.php"><?=gettext("System");?></a></li>
    <li class="tabinact"><a href="diag_logs_ftp.php"><?=gettext("FTP");?></a></li>
    <li class="tabinact"><a href="diag_logs_rsyncd.php"><?=gettext("RSYNC");?></a></li>
    <li class="tabact"><a href="diag_logs_sshd.php" title="<?=gettext("Reload page");?>"><?=gettext("SSHD");?></a></li>
    <li class="tabinact"><a href="diag_logs_smartd.php"><?=gettext("SMARTD");?></a></li>
    <li class="tabinact"><a href="diag_logs_daemon.php"><?=gettext("Daemon");?></a></li>
    <li class="tabinact"><a href="diag_logs_upnp.php"><?=gettext("UPnP");?></a></li>
    <li class="tabinact"><a href="diag_logs_settings.php"><?=gettext("Settings");?></a></li>
  </ul>
  </td></tr>
  <tr>
    <td class="tabcont">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
				<td colspan="2" class="listtopic">
				<?php echo sprintf(gettext("Last %d %s log entries"), $nentries, gettext("SSHD"));?>
				</td>
		  </tr>
		  <?php logs_dump("/var/log/sshd.log", $nentries); ?>
		</table>
		<br><form action="diag_logs_sshd.php" method="post">
<input name="clear" type="submit" class="formbtn" value="<?=gettext("Clear log");?>">
</form>
	</td>
  </tr>
</table>
<?php include("fend.inc");?>
