#!/usr/local/bin/php
<?php
/*
	services_inetd_services.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"), gettext("Inetd"), gettext("Services"));

if(!isset($config['inetd']['services']) || !is_array($config['inetd']['services']['service']))
	$config['inetd']['services']['service'] = array();

array_sort_key($config['inetd']['services']['service'], "name");

$a_services = &$config['inetd']['services']['service'];

if($_POST) {
	$pconfig = $_POST;

	if($_POST['apply']) {
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("inetd");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);

		if(0 == $retval) {
			unlink_if_exists($d_inetdserviceconfdirty_path);
			unlink_if_exists($d_inetdconfdirty_path);
		}
	}
}

if ($_GET['act'] == "del") {
	if ($a_services[$_GET['id']]) {
		unset($a_services[$_GET['id']]);

		write_config();
		touch($d_inetdserviceconfdirty_path);

		header("Location: services_inetd_services.php");
		exit;
	}
}

?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
			<li class="tabinact"><a href="services_inetd.php"><?=gettext("Settings");?></a></li>
			<li class="tabact"><a href="services_inetd_services.php" title="<?=gettext("Reload page");?>"><?=gettext("Services");?></a></li>
		</ul>
	</td>
</tr>
<tr>
	<td class="tabcont">
		<form action="services_inetd_services.php" method="post">
		<?php if ($savemsg) print_info_box($savemsg); ?>
        <?php if (file_exists($d_inetdserviceconfdirty_path) || file_exists($d_inetdconfdirty_path)) print_config_change_box();?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="20%" class="listhdrr"><?=gettext("Service");?></td>
			<td width="15%" class="listhdrr"><?=gettext("Socket");?></td>
			<td width="15%" class="listhdrr"><?=gettext("Protocol");?></td>
			<td width="20%" class="listhdrr"><?=gettext("Wait");?></td>
			<td width="10%" class="list"></td>
		</tr>
		<?php $i = 0; foreach($a_services as $service):?>
		<tr>
			<td class="listlr"><?
			if($service['type'] == "rpc") {
				echo htmlspecialchars($service['name']) ."/".htmlspecialchars($service['version']);
			} else if($service['type'] == "tcpmux") {
				echo "tcpmux/".  htmlspecialchars($service['name']);
			} else {
				echo htmlspecialchars($service['name']);
			}
			?>
			&nbsp;
			</td>
			<td class="listr"><?=htmlspecialchars($service['socket']);?>&nbsp;</td>
			<td class="listr"><?=htmlspecialchars($service['protocol']);?>&nbsp;</td>
			<td class="listr"><?
			echo isset($service['wait']) ? gettext("wait") : gettext("nowait");
			if(isset($service['maxchild'])) {
				echo "/".$service['maxchild'];
				if(isset($service['maxconnections'])) {
					echo "/".$service['maxconnections'];
					if(isset($service['maxchildperip'])) {
						echo "/" . $service['maxchildperip'];
					}
				} else {
					if(isset($service['maxchildperip'])) {
						echo "/0/" . $service['maxchildperip'];
					}
				}
			} else {
				if(isset($service['maxconnections'])) {
					echo "/0/".$service['maxconnections'];
					if(isset($service['maxchildperip'])) {
						echo "/" . $service['maxchildperip'];
					}
				} else {
					if(isset($service['maxchildperip'])) {
						echo "/0/0/" . $service['maxchildperip'];
					}
				}
			}
			?>
			&nbsp;
			</td>
			<td valign="middle" nowrap class="list">
			  <a href="services_inetd_services_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit service");?>" width="17" height="17" border="0"></a>
			  <a href="services_inetd_services.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this service?");?>')"><img src="x.gif" title="<?=gettext("Delete service");?>" width="17" height="17" border="0"></a>
			</td>
		</tr>
		<?php $i++; endforeach;?>
		<tr>
			<td class="list" colspan="4"></td>
			<td class="list"><a href="services_inetd_services_edit.php"><img src="plus.gif" title="<?=gettext("Add service");?>" width="17" height="17" border="0"></a></td>
		</tr>
		</table>
		</form>
	</td>
</tr>
</table>
<?php include("fend.inc");?>
