#!/usr/local/bin/php
<?php
/*
	disks_raid_gvinum.php
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

require("guiconfig.inc");

$pgtitle = array(_DISKSPHP_NAME,"Geom vinum",_DISKSRAIDPHP_NAMEDESC);

if (!is_array($config['raid']['vdisk']))
	$config['raid']['vdisk'] = array();

gvinum_sort();

$raidstatus=get_sraid_disks_list();

$a_raid = &$config['raid']['vdisk'];

if ($_POST) {

	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path))
		{
			config_lock();
			/* reload all components that create raid device */
			disks_raid_configure();					
			config_unlock();
			write_config();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_raidconfdirty_path))
				unlink($d_raidconfdirty_path);
		}
	}
}

if ($_GET['act'] == "del") {
	if ($a_raid[$_GET['id']]) {
		$raidname=$a_raid[$_GET['id']]['name'];
		disks_raid_delete($raidname);
		unset($a_raid[$_GET['id']]);
		write_config();
		header("Location: disks_raid_gvinum.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
 <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="disks_raid_gmirror.php">Geom Mirror</a></li>
	<li class="tabact">Geom Vinum (unstable)</li>
  </ul>
  </td></tr>
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabact"><?=_DISKSRAIDPHP_MANAGE; ?></li>
	<li class="tabinact"><a href="disks_raid_gvinum_init.php"><?=_DISKSRAIDPHP_FORMAT; ?></a></li>
	<li class="tabinact"><a href="disks_raid_gvinum_tools.php"><?=_DISKSRAIDPHP_TOOLS; ?></a></li>
	<li class="tabinact"><a href="disks_raid_gvinum_info.php"><?=_DISKSRAIDPHP_INFO; ?></a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
<form action="disks_raid_gvinum.php" method="post">
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if (file_exists($d_raidconfdirty_path)): ?><p>
<?php print_info_box_np("_DISKSRAIDPHP_MSGCHANGED");?><br>
<input name="apply" type="submit" class="formbtn" id="apply" value="<?=_APPLY; ?>"></p>
<?php endif; ?>
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="25%" class="listhdrr"><?=_DISKSRAIDPHP_VOLUME; ?></td>
                  <td width="25%" class="listhdrr"><?=_TYPE; ?></td>
                  <td width="20%" class="listhdrr"><?=_SIZE; ?></td>
                  <td width="20%" class="listhdrr"><?=_STATUS; ?></td>
                  <td width="10%" class="list"></td>
				</tr>
			  <?php $i = 0; foreach ($a_raid as $raid): ?>
                <tr>
                  <td class="listlr">
                    <?=htmlspecialchars($raid['name']);?>
                  </td>
                  <td class="listr">
                    <?=htmlspecialchars($raid['type']);?>
                  </td>
                  <td class="listbg">
                  <?php
		    $raidconfiguring=file_exists($d_raidconfdirty_path) && in_array($raid['name']."\n",file($d_raidconfdirty_path));
                    if ($raidconfiguring)
						echo "_CONFIGURING";
					else
						{
						$tempo=$raid['name'];						
						echo "{$raidstatus[$tempo]['size']}";
						}?>&nbsp;
                  </td>
                 </td>
                   <td class="listbg">
                   <?php
                    if ($raidconfiguring)
						echo "_CONFIGURING";
					else
						{
						echo "{$raidstatus[$tempo]['desc']}";
						}
						?>&nbsp;
                  </td>
                  <td valign="middle" nowrap class="list"> <a href="disks_raid_gvinum_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=_DISKSRAIDPHP_EDITRAID; ?>" width="17" height="17" border="0"></a>
                     &nbsp;<a href="disks_raid_gvinum.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=_DISKSRAIDPHP_DELCONF ;?>')"><img src="x.gif" title="<?=_DISKSRAIDPHP_DELRAID ;?>" width="17" height="17" border="0"></a></td>
				</tr>
			  <?php $i++; endforeach; ?>
                <tr> 
                  <td class="list" colspan="4"></td>
                  <td class="list"> <a href="disks_raid_gvinum_edit.php"><img src="plus.gif" title="<?=_DISKSRAIDPHP_EDITRAID ;?>" width="17" height="17" border="0"></a></td>
				</tr>
              </table>
            </form>
<p><span class="vexpl"><span class="red"><strong><?=_DISKSRAIDPHP_NOTE; ?>
           </span></p>
</td></tr></table>
<?php include("fend.inc"); ?>
