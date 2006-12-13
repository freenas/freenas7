#!/usr/local/bin/php
<?php
/*
	services_upnp.php
	Copyright © 2006 Volker Theile (votdev@gmx.de)
  All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(_SERVICES,_SRVUPNP_NAMEDESC);

if (!is_array($config['upnp'])) {
  $config['upnp'] = array();
}

$pconfig['enable'] = isset($config['upnp']['enable']);
$pconfig['name'] = $config['upnp']['name'];
$pconfig['if'] = $config['upnp']['if'];
$pconfig['content'] = $config['upnp']['content'];

/* Set name to configured hostname if it is not set */
if(!$pconfig['name'])
  $pconfig['name'] = $config['system']['hostname'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();

	if($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "name interface content"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(_SRVUPNP_NAME,_SRVUPNP_INTERFACE,_SRVUPNP_CONTENT));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if(!$input_errors) {
    $config['upnp']['enable'] = $_POST['enable'] ? true : false;
		$config['upnp']['name'] = $_POST['name'];
		$config['upnp']['if'] = $_POST['interface'];
		$config['upnp']['content'] = $_POST['content'];

		write_config();

		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			services_upnp_configure();
			config_unlock();
		}
	
		$savemsg = get_std_save_message($retval);
	}
}

$a_interface = get_interface_list();
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.name.disabled = endis;
	document.iform.interface.disabled = endis;
	document.iform.content.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="services_upnp.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td colspan="2" valign="top" class="optsect_t">
  		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
  		  <tr>
          <td class="optsect_s"><strong><?=_SRVUPNP_UPNP;?></strong></td>
  			  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?_ENABLE;?></strong></td>
        </tr>
  		  </table>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=_SRVUPNP_NAME;?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="name" type="text" class="formfld" id="name" size="20" value="<?=htmlspecialchars($pconfig['name']);?>">
        <br><?=_SRVUPNP_NAMETEXT;?>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=_SRVUPNP_INTERFACE;?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <select name="interface" class="formfld" id="interface">
          <?php foreach($a_interface as $if => $ifinfo): ?>
          <option value="<?=$if;?>"
          <?php if ($if == $pconfig['if']) echo "selected";?>><?=$if?></option>
          <?php endforeach; ?>
        </select>
        <br><?=_SRVUPNP_INTERFACETEXT;?>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=_SRVUPNP_CONTENT;?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="content" type="text" class="formfld" id="content" size="80" value="<?=htmlspecialchars($pconfig['content']);?>">
        <br><?=_SRVUPNP_CONTENTTEXT;?>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=_SAVE;?>" onClick="enable_change(true)">
      </td>
    </tr>
  </table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc"); ?>
