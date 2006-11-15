#!/usr/local/bin/php
<?php 
/*
	services_samba_share_edit.php
	Copyright © 2006 Volker Theile (votdev@gmx.de)
  All rights reserved.

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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(_SERVICES,_SRVCIFS_NAMEDESC,_SRVCIFS_SHARE,_EDIT);

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

mount_sort();

if($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if(!$input_errors) {
    if(!$_POST['browsable']) {
      $config['samba']['hidemount'] = array_merge($config['samba']['hidemount'],array($config['mounts']['mount'][$id]['sharename']));
    } else {
      if(is_array($config['samba']['hidemount']) && in_array($config['mounts']['mount'][$id]['sharename'],$config['samba']['hidemount'])) {
        $config['samba']['hidemount'] = array_diff($config['samba']['hidemount'],array($config['mounts']['mount'][$id]['sharename']));
      }
    }

		touch($d_smbshareconfdirty_path);
		write_config();
    header("Location: services_samba_share.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<form action="services_samba_share_edit.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr> 
      <td width="22%" valign="top" class="vncell"><?=_SRVCIFSSHAREEDIT_SHARENAME;?></td>
      <td width="78%" class="vtable"> 
        <input type="text" class="formfld" size="30" value="<?=htmlspecialchars($config['mounts']['mount'][$id]['sharename']);?>" disabled>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncell"><?=_SRVCIFSSHAREEDIT_DESC;?></td>
      <td width="78%" class="vtable"> 
        <input type="text" class="formfld" size="30" value="<?=htmlspecialchars($config['mounts']['mount'][$id]['desc']);?>" disabled>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=_SRVCIFSSHAREEDIT_BROWSABLE;?></td>
      <td width="78%" class="vtable">
        <input name="browsable" type="checkbox" value="<?=$config['mounts']['mount'][$id]['sharename'];?>" <?php echo ((is_array($config['samba']['hidemount']) && in_array($config['mounts']['mount'][$id]['sharename'],$config['samba']['hidemount']))?"":" checked");?>>
        <?=_SRVCIFSSHAREEDIT_BROWSABLETEXT;?>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=_SAVE;?>"> 
        <?php if(isset($id)): ?>
        <input name="id" type="hidden" value="<?=$id;?>"> 
        <?php endif; ?>
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
