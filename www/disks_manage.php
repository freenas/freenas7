#!/usr/local/bin/php
<?php
/*
	disks_manage.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(_DISKSPHP_NAME,_DISKSMANAGEPHP_NAMEDESC);

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();
	
disks_sort();

$a_disk_conf = &$config['disks']['disk'];

if ($_POST) {

	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			/* reload all components that mount disk */
			// disks_mount_all();
			/* Is formated?: If not create FS */
			/* $retval = disk_disks_create_ufs(); */
			
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_diskdirty_path))
				unlink($d_diskdirty_path);
		}
	}
}

if ($_GET['act'] == "del") {
	if ($a_disk_conf[$_GET['id']]) {
		unset($a_disk_conf[$_GET['id']]);
		write_config();
		touch($d_diskdirty_path);
		header("Location: disks_manage.php");
		exit;
	}
}


?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabact"><?=_DISKSPHP_MANAGE; ?></li>
	<li class="tabinact"><a href="disks_manage_init.php"><?=_DISKSPHP_FORMAT; ?></a></li>
	<li class="tabinact"><a href="disks_manage_iscsi.php"><?=_DISKSPHP_ISCSIINIT; ?></a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
<form action="disks_manage.php" method="post">
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if (file_exists($d_diskdirty_path)): ?><p>
<?php print_info_box_np(_DISKSMANAGEPHP_MSGCHANGED);?><br>
<input name="apply" type="submit" class="formbtn" id="apply" value="<?=_APPLY;?>"></p>
<?php endif; ?>

              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="5%" class="listhdrr"><?=_DISKSPHP_DISK; ?></td>
                  <td width="5%" class="listhdrr"><?=_SIZE; ?></td>
                  <td width="50%" class="listhdrr"><?=_DISKSMANAGEPHP_DESC; ?></td>
                  <td width="10%" class="listhdrr"><?=_DISKSMANAGEPHP_STANDBY; ?></td>
                  <td width="10%" class="listhdrr"><?=_DISKSPHP_FILESYSTEM; ?></td>
                  <td width="10%" class="listhdr"><?=_STATUS; ?></td>
                  <td width="10%" class="list"></td>
				</tr>
			  <?php $i = 0; foreach ($a_disk_conf as $disk): ?>
                <tr>
                  <td class="listbg">
                    <?=htmlspecialchars($disk['name']);?>
                  </td>
                  <td class="listbg">
                    <?=htmlspecialchars($disk['size']);?>
                  </td>
                  <td class="listbg">
                    <?=htmlspecialchars($disk['desc']);?>&nbsp;
                  </td>
                   <td class="listbg">
  
                   <?php
                    if ($disk['harddiskstandby'])
                    {
						 $value=$disk['harddiskstandby'];
						 //htmlspecialchars($value);
						 echo $value;
					}
					else
						{
						echo "Always on";
						}
						?>&nbsp;
                   
                  </td>
                  
                   <td class="listbg">
                    <?=($disk['fstype'])?$disk['fstype']:_DISKSMANAGEPHP_FSUNKNOWN ?>&nbsp;
                  </td>           
                   <td class="listbg">
                    <?php
                    $stat=disks_status($disk);
                    echo $stat;?>&nbsp;
                  </td>           
                   <td valign="middle" nowrap class="list"> <a href="disks_manage_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=_DISKSMANAGEPHP_EDIT; ?>" width="17" height="17" border="0"></a>
                     &nbsp;<a href="disks_manage.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=_DISKSMANAGEPHP_DELCONF; ?>')"><img src="x.gif" title="<?=_DISKSMANAGEPHP_DEL; ?>" width="17" height="17" border="0"></a></td>
				</tr>

			  <?php $i++; endforeach; ?>
                <tr> 
                  <td class="list" colspan="6"></td>
                  <td class="list"> <a href="disks_manage_edit.php"><img src="plus.gif" title="<?=_DISKSMANAGEPHP_ADD; ?>" width="17" height="17" border="0"></a></td>
				</tr>
              </table>
            </form>
<p><span class="vexpl"><span class="red"><?=_DISKSMANAGEPHP_NOTE; ?></span></p>
</td></tr></table>
<?php include("fend.inc"); ?>
