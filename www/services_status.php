#!/usr/local/bin/php
<?php
/*
	services_status.php
	Copyright (C) 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"), gettext("Status"));

$a_service[] = array("desc" => gettext("CIFS/SMB"), "link" => "services_samba.php", "config" => "samba");
$a_service[] = array("desc" => gettext("FTP"), "link" => "services_ftp.php", "config" => "ftpd");
$a_service[] = array("desc" => gettext("SSH"), "link" => "services_sshd.php", "config" => "sshd");
$a_service[] = array("desc" => gettext("NFS"), "link" => "services_nfs.php", "config" => "nfsd");
$a_service[] = array("desc" => gettext("AFP"), "link" => "services_afp.php", "config" => "afp");
$a_service[] = array("desc" => gettext("RSYNC"), "link" => "services_rsyncd.php", "config" => "rsyncd");
$a_service[] = array("desc" => gettext("Unison"), "link" => "services_unison.php", "config" => "unison");
$a_service[] = array("desc" => gettext("iSCSI Target"), "link" => "services_iscsitarget.php", "config" => "iscsitarget");
$a_service[] = array("desc" => gettext("UPnP"), "link" => "services_upnp.php", "config" => "upnp");
$a_service[] = array("desc" => gettext("iTunes/DAAP"), "link" => "services_daap.php", "config" => "daap");
$a_service[] = array("desc" => gettext("Dynamic DNS"), "link" => "services_dynamicdns.php", "config" => "dynamicdns");
$a_service[] = array("desc" => gettext("SNMP"), "link" => "services_snmp.php", "config" => "snmpd");
$a_service[] = array("desc" => gettext("UPS"), "link" => "services_ups.php", "config" => "ups");
$a_service[] = array("desc" => gettext("Webserver"), "link" => "services_websrv.php", "config" => "websrv");
$a_service[] = array("desc" => gettext("BitTorrent"), "link" => "services_bittorrent.php", "config" => "bittorrent");
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabcont">
      <form action="services_info.php" method="post">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
						<td width="95%" class="listhdrr"><?=gettext("Service");?></td>
						<td width="5%" class="listhdrc"><?=gettext("Status");?></td>
          </tr>
  			  <?php foreach ($a_service as $servicev):?>
          <tr>
          	<?php $enable = isset($config[$servicev['config']]['enable']);?>
						<td class="<?=$enable?"listlr":"listlrd";?>"><?=htmlspecialchars($servicev['desc']);?>&nbsp;</td>
						<td class="<?=$enable?"listrc":"listrcd";?>">
							<a href="<?=$servicev['link'];?>">
								<?php if ($enable):?>
								<img src="status_enabled.png" border="0">
								<?php else:?>
								<img src="status_disabled.png" border="0">
								<?php endif;?>
							</a>
						</td>
          </tr>
          <?php endforeach;?>
        </table>
      </form>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
