#!/usr/local/bin/php
<?php
/*
	services_snmp.php
	part of m0n0wall (http://m0n0.ch/wall) and pfSense (http://www.pfsense.org)

	Copyright (C) 2003-2005 Manuel Kasper <mk@neon1.net>.
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

$pgtitle = array(gettext("Services"), gettext("SNMP"));

if (!is_array($config['snmpd'])) {
	$config['snmpd'] = array();
}

$pconfig['enable'] = isset($config['snmpd']['enable']);
$pconfig['location'] = $config['snmpd']['location'];
$pconfig['contact'] = $config['snmpd']['contact'];
$pconfig['read'] = $config['snmpd']['read'];
$pconfig['trapenable'] = isset($config['snmpd']['trapenable']);
$pconfig['traphost'] = $config['snmpd']['traphost'];
$pconfig['trapport'] = $config['snmpd']['trapport'];
$pconfig['trap'] = $config['snmpd']['trap'];
$pconfig['mibii'] = isset($config['snmpd']['modules']['mibii']);
$pconfig['netgraph'] = isset($config['snmpd']['modules']['netgraph']);
$pconfig['hostres'] = isset($config['snmpd']['modules']['hostres']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation
	if ($_POST['enable']) {
		$reqdfields = explode(" ", "location contact read");
		$reqdfieldsn = array(gettext("Location"),gettext("Contact"),gettext("Community"));

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	}

	if ($_POST['trapenable']) {
		$reqdfields = explode(" ", "traphost trapport trap");
		$reqdfieldsn = array(gettext("Trap host"),gettext("Trap port"),gettext("Trap string"));

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	}

	if (!$input_errors) {
		$config['snmpd']['enable'] = $_POST['enable'] ? true : false;
		$config['snmpd']['location'] = $_POST['location'];
		$config['snmpd']['contact'] = $_POST['contact'];
		$config['snmpd']['read'] = $_POST['read'];
		$config['snmpd']['trapenable'] = $_POST['trapenable'] ? true : false;
		$config['snmpd']['traphost'] = $_POST['traphost'];
		$config['snmpd']['trapport'] = $_POST['trapport'];
		$config['snmpd']['trap'] = $_POST['trap'];
		$config['snmpd']['modules']['mibii'] = $_POST['mibii'] ? true : false;
		$config['snmpd']['modules']['netgraph'] = $_POST['netgraph'] ? true : false;
		$config['snmpd']['modules']['hostres'] = $_POST['hostres'] ? true : false;

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("bsnmpd");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);

	if (enable_change.name == "trapenable") {
		endis = !enable_change.checked;

		document.iform.traphost.disabled = endis;
		document.iform.trapport.disabled = endis;
		document.iform.trap.disabled = endis;
	} else {
		document.iform.location.disabled = endis;
		document.iform.contact.disabled = endis;
		document.iform.read.disabled = endis;
		document.iform.mibii.disabled = endis;
		document.iform.netgraph.disabled = endis;
		document.iform.hostres.disabled = endis;
		document.iform.trapenable.disabled = endis;

		if (document.iform.enable.checked == true) {
			endis = !(document.iform.trapenable.checked || enable_change);
		}

		document.iform.traphost.disabled = endis;
		document.iform.trapport.disabled = endis;
		document.iform.trap.disabled = endis;
	}
}
//-->
</script>
<form action="services_snmp.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("enable", gettext("Simple Network Management Protocol"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<?php html_inputbox("location", gettext("Location"), $pconfig['location'], gettext("Location information, e.g. physical location of this system: 'Floor of building, Room xyz'."), true, 40);?>
					<?php html_inputbox("contact", gettext("Contact"), $pconfig['contact'], gettext("Contact information, e.g. name or email of the person responsible for this system: 'admin@email.address'."), true, 40);?>					
					<?php html_inputbox("read", gettext("Community"), $pconfig['read'], gettext("In most cases, 'public' is used here."), true, 40);?>
					<?php html_separator();?>
					<?php html_titleline_checkbox("trapenable", gettext("Traps"), $pconfig['trapenable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<?php html_inputbox("traphost", gettext("Trap host"), $pconfig['traphost'], gettext("Enter trap host name."), true, 40);?>
					<?php html_inputbox("trapport", gettext("Trap port"), $pconfig['trapport'], gettext("Enter the port to send the traps to (default 162)."), true, 5);?>
					<?php html_inputbox("trap", gettext("Trap string"), $pconfig['trap'], gettext("Trap string."), true, 40);?>
					<?php html_separator();?>
					<?php html_titleline(gettext("Modules"));?>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("SNMP Modules");?></td>
						<td width="78%" class="vtable">
							<input name="mibii" type="checkbox" id="mibii" value="yes" <?php if ($pconfig['mibii']) echo "checked"; ?>><?=gettext("MibII");?><br/>
							<input name="netgraph" type="checkbox" id="netgraph" value="yes" <?php if ($pconfig['netgraph']) echo "checked"; ?>><?=gettext("Netgraph");?><br/>
							<input name="hostres" type="checkbox" id="hostres" value="yes" <?php if ($pconfig['hostres']) echo "checked"; ?>><?=gettext("Host resources");?><br/>
						</td>
					</tr>
			  </table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
				</div>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), sprintf(gettext("The associated MIB files can be found at %s."), "/usr/share/snmp/mibs"));?>
				</div>
			</td>
		</tr>
	</table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc");?>
