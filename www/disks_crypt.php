#!/usr/local/bin/php
<?php
/*
	disks_crypt.php
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

$pgtitle = array(gettext("Disks"),gettext("Encryption"),gettext("Management"));

if (!is_array($config['geli']['vdisk']))
	$config['geli']['vdisk'] = array();

array_sort_key($config['geli']['vdisk'], "devicespecialfile");

$a_geli = &$config['geli']['vdisk'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			// Nothing to do here at the moment.
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_gelidirty_path))
				unlink($d_gelidirty_path);
		}
	}
}

if ($_GET['act'] == "del") {
	if ($a_geli[$_GET['id']]) {
		$device = $a_geli[$_GET['id']]['device'][0];
		$devicespecialfile = $a_geli[$_GET['id']]['devicespecialfile'];

		if (disks_exists($devicespecialfile)) {
			// Kill encrypted volume.
			disks_geli_kill($devicespecialfile);

			// Reset disk file system type attribute ('fstype') in configuration.
			set_conf_disk_fstype($device, "");

			// Delete geli volume in configuration.
			unset($a_geli[$_GET['id']]);

			write_config();

			header("Location: disks_crypt.php");
			exit;
		} else {
			$errormsg[] = gettext("The volume must be detached before it can be deleted.");
		}
	}
}

if ($_GET['act'] == "ret")
{
	if ($a_mount[$_GET['id']]) {
		disks_mount($a_mount[$_GET['id']]);
		header("Location: disks_crypt.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if($errormsg) print_input_errors($errormsg);?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabact"><a href="disks_crypt.php" title="<?=gettext("Reload page");?>" ><?=gettext("Management");?></a></li>
        <li class="tabinact"><a href="disks_crypt_tools.php"><?=gettext("Tools");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
      <form action="disks_crypt.php" method="post">
        <?php if ($savemsg) print_info_box($savemsg); ?>
        <?php if (file_exists($d_gelidirty_path)): ?><p>
        <?php print_info_box_np(gettext("The encrypted volume list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
        <input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
        <?php endif; ?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="25%" class="listhdrr"><?=gettext("Disk"); ?></td>
            <td width="25%" class="listhdrr"><?=gettext("Data integrity"); ?></td>
            <td width="20%" class="listhdrr"><?=gettext("Encryption"); ?></td>
            <td width="20%" class="listhdrr"><?=gettext("Status") ;?></td>
            <td width="10%" class="list"></td>
          </tr>
  			  <?php $i = 0; foreach($a_geli as $geli): ?>
          <tr>
            <td class="listlr"><?=htmlspecialchars($geli['name']);?>&nbsp;</td>
            <td class="listr"><?=htmlspecialchars($geli['aalgo']);?>&nbsp;</td>
            <td class="listr"><?=htmlspecialchars($geli['ealgo']);?>&nbsp;</td>
            <td class="listbg">
              <?php
              if (file_exists($d_gelidirty_path)) {
                echo(gettext("Configuring"));
              } else {
                if(disks_exists($geli['devicespecialfile'])) {
                  echo("<a href=\"disks_crypt_tools.php?disk={$geli['devicespecialfile']}&action=attach\">" . gettext("Not attached") . "</a>");
                } else {
                  echo(gettext("Attached"));
                }
              }
              ?>&nbsp;
            </td>
            <td valign="middle" nowrap class="list">
							<a href="disks_crypt_tools.php?disk=<?=$geli['devicespecialfile'];?>&action=setkey"><img src="e.gif" title="Change password" width="17" height="17" border="0"></a>&nbsp;
              <a href="disks_crypt.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this encrypted volume? All elements that still use it will become invalid (e.g. share)!");?>')"><img src="x.gif" title="<?=gettext("Kill encrypted volume"); ?>" width="17" height="17" border="0"></a>
            </td>
          </tr>
          <?php $i++; endforeach; ?>
          <tr>
            <td class="list" colspan="4"></td>
            <td class="list"><a href="disks_crypt_edit.php"><img src="plus.gif" title="<?=gettext("Create encrypted volume");?>" width="17" height="17" border="0"></a></td>
			    </tr>
        </table>
      </form>
	  </td>
  </tr>
</table>
<?php include("fend.inc");?>
