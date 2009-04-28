#!/usr/local/bin/php
<?php
/*
	services_iscsitarget.php
	Copyright (C) 2007-2009 Volker Theile (votdev@gmx.de)
	Copyright (C) 2009 Daisuke Aoyama (aoyama@peach.ne.jp)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"), gettext("iSCSI Target"));

if (!is_array($config['iscsitarget']['portalgroup']))
	$config['iscsitarget']['portalgroup'] = array();

if (!is_array($config['iscsitarget']['initiatorgroup']))
	$config['iscsitarget']['initiatorgroup'] = array();

if (!is_array($config['iscsitarget']['authgroup']))
	$config['iscsitarget']['authgroup'] = array();

function cmp_tag($a, $b) {
	if ($a['tag'] == $b['tag'])
		return 0;
	return ($a['tag'] > $b['tag']) ? 1 : -1;
}
usort($config['iscsitarget']['portalgroup'], "cmp_tag");
usort($config['iscsitarget']['initiatorgroup'], "cmp_tag");
usort($config['iscsitarget']['authgroup'], "cmp_tag");

$pconfig['enable'] = isset($config['iscsitarget']['enable']);
$pconfig['nodebase'] = $config['iscsitarget']['nodebase'];
$pconfig['discoveryauthmethod'] = $config['iscsitarget']['discoveryauthmethod'];
$pconfig['discoveryauthgroup'] = $config['iscsitarget']['discoveryauthgroup'];
$pconfig['timeout'] = $config['iscsitarget']['timeout'];
$pconfig['nopininterval'] = $config['iscsitarget']['nopininterval'];
$pconfig['maxsessions'] = $config['iscsitarget']['maxsessions'];
$pconfig['maxconnections'] = $config['iscsitarget']['maxconnections'];
$pconfig['firstburstlength'] = $config['iscsitarget']['firstburstlength'];
$pconfig['maxburstlength'] = $config['iscsitarget']['maxburstlength'];
$pconfig['maxrecvdatasegmentlength'] = $config['iscsitarget']['maxrecvdatasegmentlength'];

if ($_POST) {
	unset($input_errors);
	unset($errormsg);

	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "nodebase discoveryauthmethod discoveryauthgroup");
	$reqdfieldsn = array(gettext("Node Base"),
		gettext("Discovery Auth Method"),
		gettext("Discovery Auth Group"));
	$reqdfieldst = explode(" ", "string string numericint");
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	$reqdfields = explode(" ", "timeout nopininterval maxsessions maxconnections firstburstlength maxburstlength maxrecvdatasegmentlength");
	$reqdfieldsn = array(gettext("I/O Timeout"),
		gettext("NOPIN Interval"),
		gettext("Max. sessions"),
		gettext("Max. connections"),
		gettext("FirstBurstLength"),
		gettext("MaxBurstLength"),
		gettext("MaxRecvDataSegmentLength"));
	$reqdfieldst = explode(" ", "numericint numericint numericint numericint numericint numericint numericint");
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	$nodebase = $_POST['nodebase'];
	$nodebase = preg_replace('/\s/', '', $nodebase);
	$pconfig['nodebase'] = $nodebase;

	if (!$input_errors) {
		$config['iscsitarget']['enable'] = $_POST['enable'] ? true : false;

		$config['iscsitarget']['nodebase'] = $nodebase;
		$config['iscsitarget']['discoveryauthmethod'] = $_POST['discoveryauthmethod'];
		$config['iscsitarget']['discoveryauthgroup'] = $_POST['discoveryauthgroup'];
		$config['iscsitarget']['timeout'] = $_POST['timeout'];
		$config['iscsitarget']['nopininterval'] = $_POST['nopininterval'];
		$config['iscsitarget']['maxsessions'] = $_POST['maxsessions'];
		$config['iscsitarget']['maxconnections'] = $_POST['maxconnections'];
		$config['iscsitarget']['firstburstlength'] = $_POST['firstburstlength'];
		$config['iscsitarget']['maxburstlength'] = $_POST['maxburstlength'];
		$config['iscsitarget']['maxrecvdatasegmentlength'] = $_POST['maxrecvdatasegmentlength'];

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("iscsi_target");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);
	}
}

if (!is_array($config['iscsitarget']['portalgroup']))
	$config['iscsitarget']['portalgroup'] = array();

if (!is_array($config['iscsitarget']['initiatorgroup']))
	$config['iscsitarget']['initiatorgroup'] = array();

if (!is_array($config['iscsitarget']['authgroup']))
	$config['iscsitarget']['authgroup'] = array();
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.nodebase.disabled = endis;
	document.iform.discoveryauthmethod.disabled = endis;
	document.iform.discoveryauthgroup.disabled = endis;
	document.iform.timeout.disabled = endis;
	document.iform.nopininterval.disabled = endis;
	document.iform.maxsessions.disabled = endis;
	document.iform.maxconnections.disabled = endis;
	document.iform.firstburstlength.disabled = endis;
	document.iform.maxburstlength.disabled = endis;
	document.iform.maxrecvdatasegmentlength.disabled = endis;
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
				<li class="tabact"><a href="services_iscsitarget.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Settings");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_target.php"><span><?=gettext("Targets");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_pg.php"><span><?=gettext("Portals");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_ig.php"><span><?=gettext("Initiators");?></span></a></li>
				<li class="tabinact"><a href="services_iscsitarget_ag.php"><span><?=gettext("Auths");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
    	<form action="services_iscsitarget.php" method="post" name="iform" id="iform">
	      <?php if ($input_errors) print_input_errors($input_errors);?>
	      <?php if ($savemsg) print_info_box($savemsg);?>
	      <table width="100%" border="0" cellpadding="6" cellspacing="0">
		      <?php html_titleline_checkbox("enable", gettext("iSCSI Target"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
		      <?php html_inputbox("nodebase", gettext("Base Name"), $pconfig['nodebase'], gettext("The base name (e.g. iqn.2007-09.jp.ne.peach.istgt) will append the target name that is not starting with 'iqn.'."), true, 60, false);?>
		      <?php html_combobox("discoveryauthmethod", gettext("Discovery Auth Method"), $pconfig['discoveryauthmethod'], array("Auto" => gettext("Auto"), "CHAP" => gettext("CHAP"), "CHAP mutual" => gettext("Mutual CHAP")), gettext("The method can be accepted in discovery session. Auto means both none and authentication."), true);?>
		      <?php
					$ag_list = array();
					$ag_list['0'] = gettext("None");
					foreach($config['iscsitarget']['authgroup'] as $ag) {
						if ($ag['comment']) {
							$l = sprintf(gettext("Tag%d (%s)"), $ag['tag'], $ag['comment']);
						} else {
							$l = sprintf(gettext("Tag%d"), $ag['tag']);
						}
						$ag_list[$ag['tag']] = htmlspecialchars($l);
					};?>
					<?php html_combobox("discoveryauthgroup", gettext("Discovery Auth Group"), $pconfig['discoveryauthgroup'], $ag_list, gettext("The initiator can discover the targets with correct user and secret in specific Auth Group."), true);?>
		      <?php html_separator();?>
		      <?php html_titleline(gettext("Advanced settings"));?>
		      <?php html_inputbox("timeout", gettext("I/O Timeout"), $pconfig['timeout'], gettext("I/O timeout in seconds (30 by default)."), true, 30, false);?>
		      <?php html_inputbox("nopininterval", gettext("NOPIN Interval"), $pconfig['nopininterval'], gettext("NOPIN sending interval in seconds (20 by default)."), true, 30, false);?>
		      <?php html_inputbox("maxsessions", gettext("Max. sessions"), $pconfig['maxsessions'], gettext("Maximum number of sessions holding at same time (32 by default)."), true, 30, false);?>
		      <?php html_inputbox("maxconnections", gettext("Max. connections"), $pconfig['maxconnections'], gettext("Maximum number of connections in each session (8 by default)."), true, 30, false);?>
		      <?php html_inputbox("firstburstlength", gettext("FirstBurstLength"), $pconfig['firstburstlength'], gettext("iSCSI initial parameter (65536 by default)."), true, 30, false);?>
		      <?php html_inputbox("maxburstlength", gettext("MaxBurstLength"), $pconfig['maxburstlength'], gettext("iSCSI initial parameter (262144 by default)."), true, 30, false);?>
		      <?php html_inputbox("maxrecvdatasegmentlength", gettext("MaxRecvDataSegmentLength"), $pconfig['maxrecvdatasegmentlength'], gettext("iSCSI initial parameter (262144 by default)."), true, 30, false);?>
	      </table>
	      <div id="submit">
	        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
	      </div>
	      <div id="remarks">
	        <?php html_remark("note", gettext("Note"), gettext("You must have a minimum of 256MB of RAM for using iSCSI target."));?>
	      </div>
      </form>
    </td>
  </tr>
</table>
<script language="JavaScript">
<!--
enable_change();
//-->
</script>
<?php include("fend.inc");?>
