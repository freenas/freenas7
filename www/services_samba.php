#!/usr/local/bin/php
<?php
/*
	services_samba.php
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

$pgtitle = array(gettext("Services"),gettext("CIFS"));

if (!is_array($config['samba'])) {
	$config['samba'] = array();
}

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

mount_sort();

$a_mount = &$config['mounts']['mount'];

$pconfig['netbiosname'] = $config['samba']['netbiosname'];
$pconfig['workgroup'] = $config['samba']['workgroup'];
$pconfig['serverdesc'] = $config['samba']['serverdesc'];
$pconfig['security'] = $config['samba']['security'];
$pconfig['localmaster'] = $config['samba']['localmaster'];
$pconfig['winssrv'] = $config['samba']['winssrv'];
$pconfig['timesrv'] = $config['samba']['timesrv'];
$pconfig['unixcharset'] = $config['samba']['unixcharset'];
$pconfig['doscharset'] = $config['samba']['doscharset'];
$pconfig['loglevel'] = $config['samba']['loglevel'];
$pconfig['sndbuf'] = $config['samba']['sndbuf'];
$pconfig['rcvbuf'] = $config['samba']['rcvbuf'];
$pconfig['enable'] = isset($config['samba']['enable']);
$pconfig['recyclebin'] = isset($config['samba']['recyclebin']);
$pconfig['largereadwrite'] = isset($config['samba']['largereadwrite']);

if ($_POST)
{
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();
	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "security netbiosname workgroup localmaster"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Authentication"),gettext("NetBiosName"),gettext("Workgroup"),gettext("Local Master Browser")));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (($_POST['netbiosname'] && !is_domain($_POST['netbiosname']))) {
		$input_errors[] = gettext("The Netbios name contains invalid characters.");
	}
	if (($_POST['workgroup'] && !is_workgroup($_POST['workgroup']))) {
		$input_errors[] = gettext("The Workgroup name contains invalid characters.");
	}
	if (($_POST['winssrv'] && !is_ipaddr($_POST['winssrv']))) {
		$input_errors[] = gettext("The WINS server must be an IP address.");
	}
	if (!is_numericint($_POST['sndbuf'])) {
		$input_errors[] = gettext("The SND Buffer value must be a number.");
	}
	if (!is_numericint($_POST['rcvbuf'])) {
		$input_errors[] = gettext("The RCV Buffer value must be a number.");
	}

	if (!$input_errors) {
		$config['samba']['netbiosname'] = $_POST['netbiosname'];
		$config['samba']['workgroup'] = $_POST['workgroup'];
		$config['samba']['serverdesc'] = $_POST['serverdesc'];
		$config['samba']['security'] = $_POST['security'];
		$config['samba']['localmaster'] = $_POST['localmaster'];
		$config['samba']['winssrv'] = $_POST['winssrv'];
		$config['samba']['timesrv'] = $_POST['timesrv'];
		$config['samba']['doscharset'] = $_POST['doscharset'];
		$config['samba']['unixcharset'] = $_POST['unixcharset'];
		$config['samba']['loglevel'] = $_POST['loglevel'];
		$config['samba']['sndbuf'] = $_POST['sndbuf'];
		$config['samba']['rcvbuf'] = $_POST['rcvbuf'];
		$config['samba']['recyclebin'] = $_POST['recyclebin'] ? true : false;
		$config['samba']['largereadwrite'] = $_POST['largereadwrite'] ? true : false;
		$config['samba']['enable'] = $_POST['enable'] ? true : false;

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			/* nuke the cache file */
			config_lock();
			services_samba_configure();
			services_mdnsresponder_configure(); // Update and announce service via zeroconf.
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis;

	endis = !(document.iform.enable.checked || enable_change);
	document.iform.netbiosname.disabled = endis;
	document.iform.workgroup.disabled = endis;
	document.iform.localmaster.disabled = endis;
	document.iform.winssrv.disabled = endis;
	document.iform.timesrv.disabled = endis;
	document.iform.serverdesc.disabled = endis;
	document.iform.doscharset.disabled = endis;
	document.iform.unixcharset.disabled = endis;
	document.iform.loglevel.disabled = endis;
	document.iform.sndbuf.disabled = endis;
	document.iform.rcvbuf.disabled = endis;
	document.iform.recyclebin.disabled = endis;
	document.iform.security.disabled = endis;
	document.iform.largereadwrite.disabled = endis;
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabact"><a href="services_samba.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Settings");?></a></li>
				<li class="tabinact"><a href="services_samba_share.php"><?=gettext("Shares");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
      <?php if ($input_errors) print_input_errors($input_errors); ?>
      <?php if ($savemsg) print_info_box($savemsg); ?>
      <form action="services_samba.php" method="post" name="iform" id="iform">
        <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <tr>
            <td colspan="2" valign="top" class="optsect_t">
    				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    				    <tr>
                  <td class="optsect_s"><strong>Common Internet File System</strong></td>
    				      <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable") ;?></strong></td>
                </tr>
    				  </table>
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncellreq"><?=gettext("Authentication"); ?></td>
            <td width="78%" class="vtable">
              <?=$mandfldhtml;?><select name="security" class="formfld" id="security">
              <?php $types = explode(",", "Anonymous,Local User,Domain"); $vals = explode(" ", "share user domain");?>
              <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['security']) echo "selected";?>>
                <?=htmlspecialchars($types[$j]);?>
                </option>
              <?php endfor; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncellreq"><?=gettext("NetBiosName") ;?></td>
            <td width="78%" class="vtable">
              <?=$mandfldhtml;?><input name="netbiosname" type="text" class="formfld" id="netbiosname" size="30" value="<?=htmlspecialchars($pconfig['netbiosname']);?>">
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncellreq"><?=gettext("Workgroup") ; ?></td>
            <td width="78%" class="vtable">
              <?=$mandfldhtml;?><input name="workgroup" type="text" class="formfld" id="workgroup" size="30" value="<?=htmlspecialchars($pconfig['workgroup']);?>">
              <br><?=gettext("Workgroup to be member of.") ;?>
            </td>
          </tr>
          <tr>
          <tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Description") ;?></td>
            <td width="78%" class="vtable">
              <input name="serverdesc" type="text" class="formfld" id="serverdesc" size="30" value="<?=htmlspecialchars($pconfig['serverdesc']);?>">
              <br><?=gettext("Server description. This can usually be left blank.") ;?>
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Dos charset") ; ?></td>
            <td width="78%" class="vtable">
              <select name="doscharset" class="formfld" id="doscharset">
              <?php $types = explode(",", "CP850,CP852,CP437,CP932,CP866,ASCII"); $vals = explode(" ", "CP850 CP852 CP437 CP932 CP866 ASCII");?>
              <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['doscharset']) echo "selected";?>>
                <?=htmlspecialchars($types[$j]);?>
                </option>
              <?php endfor; ?>
              </select>
            </td>
          </tr>
	        <tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Unix charset") ; ?></td>
            <td width="78%" class="vtable">
              <select name="unixcharset" class="formfld" id="unixcharset">
              <?php $types = explode(",", "UTF-8,iso-8859-1,iso-8859-15,gb2312,EUC-JP,ASCII"); $vals = explode(" ", "UTF-8 iso-8859-1 iso-8859-15 gb2312 EUC-JP ASCII");?>
              <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['unixcharset']) echo "selected";?>>
                <?=htmlspecialchars($types[$j]);?>
                </option>
              <?php endfor; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Log Level") ; ?></td>
            <td width="78%" class="vtable">
              <select name="loglevel" class="formfld" id="loglevel">
              <?php $types = explode(",", "Minimum,Normal,Full,Debug"); $vals = explode(" ", "1 2 3 10");?>
              <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['loglevel']) echo "selected";?>>
                <?=htmlspecialchars($types[$j]);?>
                </option>
              <?php endfor; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Local Master Browser"); ?></td>
            <td width="78%" class="vtable">
              <select name="localmaster" class="formfld" id="localmaster">
              <?php $types = array(gettext("Yes"),gettext("No")); $vals = explode(" ", "yes no");?>
              <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['localmaster']) echo "selected";?>>
                <?=htmlspecialchars($types[$j]);?>
                </option>
              <?php endfor; ?>
              </select>
              <br><?php echo sprintf(gettext("Allows %s to try and become a local master browser."), get_product_name());?>
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Time server"); ?></td>
            <td width="78%" class="vtable">
              <select name="timesrv" class="formfld" id="timesrv">
              <?php $types = array(gettext("Yes"),gettext("No")); $vals = explode(" ", "yes no");?>
              <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['timesrv']) echo "selected";?>>
                <?=htmlspecialchars($types[$j]);?>
                </option>
              <?php endfor; ?>
              </select>
              <br><?php echo sprintf(gettext("%s advertises itself as a time server to Windows clients."), get_product_name());?>
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("WINS server"); ?></td>
            <td width="78%" class="vtable">
              <input name="winssrv" type="text" class="formfld" id="winssrv" size="30" value="<?=htmlspecialchars($pconfig['winssrv']);?>">
              <br><?=gettext("WINS Server IP address."); ?>
            </td>
  				</tr>
  				<tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Recycle Bin");?></td>
            <td width="78%" class="vtable">
              <input name="recyclebin" type="checkbox" id="recyclebin" value="yes" <?php if ($pconfig['recyclebin']) echo "checked"; ?>>
              <?=gettext("Enable Recycle bin");?><span class="vexpl"><br>
              <?=gettext("This will create a recycle bin on the CIFS shares.");?></span>
            </td>
          </tr>
	        <tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Send Buffer Size"); ?></td>
            <td width="78%" class="vtable">
              <input name="sndbuf" type="text" class="formfld" id="sndbuf" size="30" value="<?=htmlspecialchars($pconfig['sndbuf']);?>">
              <br><?=gettext("Size of send buffer (16384 by default)."); ?>
            </td>
  				</tr>
  				<tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Receive Buffer Size") ; ?></td>
            <td width="78%" class="vtable">
              <input name="rcvbuf" type="text" class="formfld" id="rcvbuf" size="30" value="<?=htmlspecialchars($pconfig['rcvbuf']);?>">
              <br><?=gettext("Size of receive buffer (16384 by default).") ; ?>
            </td>
  				</tr>
  				<tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Large read/write");?></td>
            <td width="78%" class="vtable">
              <input name="largereadwrite" type="checkbox" id="largereadwrite" value="yes" <?php if ($pconfig['largereadwrite']) echo "checked"; ?>>
              <?=gettext("Enable large read/write");?><span class="vexpl"><br>
              <?=gettext("Use the new 64k streaming read and write variant SMB requests.");?></span>
            </td>
          </tr>
  				<tr>
            <td width="22%" valign="top">&nbsp;</td>
            <td width="78%">
              <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart CIFS");?>" onClick="enable_change(true)">
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
