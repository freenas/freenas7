#!/usr/local/bin/php
<?php 
/*
	system_advanced_swap.php
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

$pgtitle = array(gettext("System"),gettext("Advanced"),gettext("Swap file"));

$pconfig['swap_enable'] = isset($config['system']['swap_enable']);
$pconfig['swap_mountname'] = $config['system']['swap_mountname'];
$pconfig['swap_size'] = $config['system']['swap_size'];

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

mount_sort();

$a_mount = &$config['mounts']['mount'];

if ($_POST) {
	unset($input_errors);

	$pconfig = $_POST;
	$pconfig['swap_enable'] = $_POST['enable'] ? true : false;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();
	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "swap_size swap_mountname"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Swap file size"),gettext("Mount to use for swap")));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (!$input_errors) {
		$config['system']['swap_enable'] = $_POST['enable'] ? true : false;
		$config['system']['swap_mountname'] = $_POST['swap_mountname'];
		$config['system']['swap_size'] = $_POST['swap_size'];

		write_config();

		$retval = rc_update_service("swap");

		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.swap_mountname.disabled = endis;
	document.iform.swap_size.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<p><span class="vexpl"><span class="red"><strong><?=gettext("Note");?>:</strong></span><br><?=gettext("The options on this page are intended for use by advanced users only, and there's <strong>NO</strong> support for them.");?></p>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
				<li class="tabinact"><a href="system_advanced.php"><?=gettext("Advanced");?></a></li>
				<li class="tabact"><a href="system_advanced_swap.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Swap");?></a></li>
				<li class="tabinact"><a href="system_advanced_rcstartup.php"><?=gettext("Startup");?></a></li>
      </ul>
    </td>
  </tr>
  <tr> 
    <td class="tabcont">
			<form action="system_advanced_swap.php" method="post" name="iform" id="iform">
        <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <tr>
            <td colspan="2" valign="top" class="optsect_t">
    				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    				    <tr>
                  <td class="optsect_s"><strong><?=gettext("Swap memory");?></strong></td>
    				      <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['swap_enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable") ;?></strong></td>
                </tr>
    				  </table>
            </td>
          </tr>
		  <tr>
			<td width="22%" valign="top" class="vncellreq"><?=gettext("Mount to use for swap"); ?></td>
			<td width="78%" class="vtable">
				<select name="swap_mountname" class="formfld" id="swap_mountname">
				  <?php foreach ($a_mount as $mount): ?>
				  <option value="<?=$mount['sharename'];?>" <?php if ($mount['sharename'] == $pconfig['swap_mountname']) echo "selected";?>><?php echo htmlspecialchars($mount['sharename']);?></option>
		  		<?php endforeach; ?>
		  	</select>
		  </td>
		</tr>
		  <tr>
          <td width="22%" valign="top" class="vncellreq"><?=gettext("Swap file size") ;?></td>
          <td width="78%" class="vtable">
              <?=$mandfldhtml;?><input name="swap_size" type="text" class="formfld" id="swap_size" size="30" value="<?=htmlspecialchars($pconfig['swap_size']);?>"><br>
							<?=gettext("Size in MB.") ;?>
            </td>
          </tr>
   				<tr>
            <td width="22%" valign="top">&nbsp;</td>
            <td width="78%">
              <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" onClick="enable_change(true)">
            </td>
          </tr>
  			</table>
			</form>
    </td>
  </tr>
</table>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc"); ?>
