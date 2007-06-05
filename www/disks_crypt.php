#!/usr/local/bin/php
<?php
/*
	disks_crypt.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(gettext("Disks"),gettext("Encryption"));

if (!is_array($config['geli']['vdisk']))
	$config['geli']['vdisk'] = array();

geli_sort();

$a_geli = &$config['geli']['vdisk'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			/*config_lock();
			config_unlock();
			*/
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_gelidirty_path))
				unlink($d_gelidirty_path);
		}
	}
}
if ($_GET['act'] == "del")
{
	if ($a_geli[$_GET['id']]) {
		if(disks_geli_check($a_geli[$_GET['id']]['fullname'])) {
			// Killl encrypted volume
			disks_geli_kill($a_geli[$_GET['id']]['fullname']);
			/*Remove the 'geli' fstype of the original disk
			BEGIN OF A COPY/PAST of the disks_crypt_edit.php file
			The should be a more clean way */
			
			$disk = $a_geli[$_GET['id']]['name'];
			
			if (!is_array($config['disks']['disk']))
				$config['disks']['disk'] = array();

			if (!is_array($config['gconcat']['vdisk']))
				$config['gconcat']['vdisk'] = array();

			if (!is_array($config['gmirror']['vdisk']))
				$config['gmirror']['vdisk'] = array();

			if (!is_array($config['graid5']['vdisk']))
				$config['graid5']['vdisk'] = array();

			if (!is_array($config['gstripe']['vdisk']))
				$config['gstripe']['vdisk'] = array();

			if (!is_array($config['gvinum']['vdisk']))
				$config['gvinum']['vdisk'] = array();

			/* Get disk configurations. */
			$a_disk = &$config['disks']['disk'];
			$a_gconcat = &$config['gconcat']['vdisk'];
			$a_gmirror = &$config['gmirror']['vdisk'];
			$a_gstripe = &$config['gstripe']['vdisk'];
			$a_graid5 = &$config['graid5']['vdisk'];
			$a_gvinum = &$config['gvinum']['vdisk'];
			
			/* Get the id of the disk array entry. */
			$NotFound = 1;
			$id = array_search_ex($disk, $a_disk, "name");

			/* disk */
			if ($id !== false) {
				/* Delete the filesystem type. */
 				$a_disk[$id]['fstype']="";
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gmirror, "name");
			}

			/* gmirror */
			if (($id !== false) && $NotFound) {
				/* Delete the filesystem type. */
 				$a_gmirror[$id]['fstype']="";
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gstripe, "name");
			}

			/* gstripe */
			if (($id !== false) && $NotFound) {
				/* Delete the filesystem type. */
 				$a_gstripe[$id]['fstype']="";
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gconcat, "name");
			}

			/* gconcat */
			if (($id !== false) && $NotFound) {
				/* Delete the filesystem type. */
 				$a_gconcat[$id]['fstype']="";

				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_graid5, "name");
			}

			/* graid5 */
			if (($id !== false) && $NotFound) {
				/* Delete the filesystem type. */
 				$a_graid5[$id]['fstype']="";
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gvinum, "name");
			}

			/* gvinum */
			if (($id !== false) && $NotFound) {
				/* Delete the filesystem type. */
 				$a_gvinum[$id]['fstype']="";
				$NotFound = 0;
			}
			
			/* End of COPY/PAST */
			// Del the geli volume on the config file
			unset($a_geli[$_GET['id']]);
			write_config();
			touch($d_gelidirty_path);
			header("Location: disks_crypt.php");
			exit;
		} else {
			$errormsg[] = gettext("This volume must be detached before to be killed");
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
        <li class="tabact"><a href="disks_crypt.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("Manage");?></a></li>
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
                $stat = disks_geli_check($geli['fullname']);
                if(1 == $stat) {
                  echo("<a href=\"disks_crypt_tools.php?disk={$geli['fullname']}&action=attach\">" . gettext("Not attached") . "</a>");
                } else {
                  echo(gettext("Attached"));
                }
              }
              ?>&nbsp;
            </td>
            <td valign="middle" nowrap class="list">
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
<?php include("fend.inc"); ?>
