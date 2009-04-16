#!/usr/local/bin/php
<?php
/*
	services_iscsitarget_pg.php
	Copyright (C) 2007-2009 Volker Theile (votdev@gmx.de)
	Copyright (C) 2009 Daisuke Aoyama (aoyama@peach.ne.jp)
	All rights reserved.

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

$pgtitle = array(gettext("Services"), gettext("iSCSI Target"), gettext("Portal Group"));

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("iscsitarget_pg", "iscsitargetpg_process_updatenotification");
			config_lock();
			$retval |= rc_update_service("iscsi_target");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete("iscsitarget_pg");
		}
	}
}

if (!is_array($config['iscsitarget']['portalgroup']))
	$config['iscsitarget']['portalgroup'] = array();

array_sort_key($config['iscsitarget']['portalgroup'], "tag");
$a_iscsitarget_pg = &$config['iscsitarget']['portalgroup'];

if (!is_array($config['iscsitarget']['target']))
	$config['iscsitarget']['target'] = array();

if ($_GET['act'] === "del") {
	$index = array_search_ex($_GET['uuid'], $config['iscsitarget']['portalgroup'], "uuid");
	if ($index !== false) {
		$pg = $config['iscsitarget']['portalgroup'][$index];
		foreach ($config['iscsitarget']['target'] as $target) {
			if (isset($target['pgigmap'])) {
				foreach ($target['pgigmap'] as $pgigmap) {
					if ($pgigmap['pgtag'] == $pg['tag']) {
						$input_errors[] = gettext("This tag is used.");
					}
				}
			}
		}
	}

	if (!$input_errors) {
		updatenotify_set("iscsitarget_pg", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
		header("Location: services_iscsitarget_pg.php");
		exit;
	}
}

function iscsitargetpg_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_DIRTY:
			if (is_array($config['iscsitarget']['portalgroup'])) {
				$index = array_search_ex($data, $config['iscsitarget']['portalgroup'], "uuid");
				if (false !== $index) {
					unset($config['iscsitarget']['portalgroup'][$index]);
					write_config();
				}
			}
			break;
	}

	return $retval;
}
?>
<?php include("fbegin.inc");?>
<form action="services_iscsitarget_pg.php" method="post" name="iform" id="iform">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabinact"><a href="services_iscsitarget.php"><span><?=gettext("Settings");?></span></a></li>
        <li class="tabinact"><a href="services_iscsitarget_target.php"><span><?=gettext("Targets");?></span></a></li>
        <li class="tabact"><a href="services_iscsitarget_pg.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Portals");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_ig.php"><span><?=gettext("Initiators");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_ag.php"><span><?=gettext("Auths");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
      <?php if ($input_errors) print_input_errors($input_errors);?>
      <?php if ($savemsg) print_info_box($savemsg);?>
      <?php if (updatenotify_exists("iscsitarget_pg")) print_config_change_box();?>
      <table width="100%" border="0" cellpadding="6" cellspacing="0">
      <tr>
        <td colspan="2" valign="top" class="listtopic"><?=gettext("Portal Groups");?></td>
      </tr>
      <tr>
        <td width="22%" valign="top" class="vncell"><?=gettext("Portal Group");?></td>
        <td width="78%" class="vtable">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10%" class="listhdrr"><?=gettext("Tag");?></td>
          <td width="80%" class="listhdrr"><?=gettext("Portals");?></td>
          <td width="10%" class="list"></td>
        </tr>
        <?php $i = 0; foreach($config['iscsitarget']['portalgroup'] as $pg):?>
        <?php $notificationmode = updatenotify_get_mode("iscsitarget_pg", $pg['uuid']);?>
        <tr>
          <td class="listlr"><?=htmlspecialchars($pg['tag']);?>&nbsp;</td>
          <td class="listr">
          <?php foreach ($pg['portal'] as $portal): ?>
          <?php echo htmlspecialchars($portal)."<br/>\n"; ?>
          <?php endforeach; ?>
          </td>
          <?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
          <td valign="middle" nowrap class="list">
            <a href="services_iscsitarget_pg_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit portal group");?>" border="0"></a>
            <a href="services_iscsitarget_pg.php?act=del&type=pg&uuid=<?=$pg['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this portal group?");?>')"><img src="x.gif" title="<?=gettext("Delete portal group");?>" border="0"></a>
          </td>
          <?php else:?>
          <td valign="middle" nowrap class="list">
            <img src="del.gif" border="0">
          </td>
          <?php endif;?>
        </tr>
        <?php $i++; endforeach;?>
        <tr>
          <td class="list" colspan="2"></td>
          <td class="list"><a href="services_iscsitarget_pg_edit.php"><img src="plus.gif" title="<?=gettext("Add portal group");?>" border="0"></a></td>
        </tr>
        </table>
        <?=gettext("A Portal Group contains IP addresses and listening TCP ports to connect the target from the initiator.");?>
        </td>
      </tr>
      </table>
    </td>
  </tr>
</table>
</form>
<?php include("fend.inc");?>
