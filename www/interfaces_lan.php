#!/usr/local/bin/php
<?php
/*
	interfaces_lan.php
	part of FreeNAS (http://www.freenas.org)
	Based on m0n0wall (http://m0n0.ch/wall)

	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Interfaces"), gettext("LAN"));

$lancfg = &$config['interfaces']['lan'];

if (strcmp($lancfg['ipaddr'],"dhcp") == 0) {
	$pconfig['type'] = "DHCP";
	$pconfig['ipaddr'] = get_ipaddr($lancfg['if']);
	$pconfig['subnet'] = 24;
} else {
	$pconfig['type'] = "Static";
	$pconfig['ipaddr'] = $lancfg['ipaddr'];
	$pconfig['subnet'] = $lancfg['subnet'];
}

if (strcmp($lancfg['ipv6addr'],"auto") == 0) {
	$pconfig['ipv6type'] = "Auto";
	$pconfig['ipv6addr'] = get_ipv6addr($lancfg['if']);
} else {
	$pconfig['ipv6type'] = "Static";
	$pconfig['ipv6addr'] = $lancfg['ipv6addr'];
	$pconfig['ipv6subnet'] = $lancfg['ipv6subnet'];
}

$pconfig['gateway'] = get_defaultgateway();
$pconfig['ipv6gateway'] = get_ipv6defaultgateway();
$pconfig['dhcphostname'] = $config['system']['hostname'] . "." . $config['system']['domain'];
$pconfig['dhcpclientidentifier'] = get_macaddr($lancfg['if']);

// Get the current configured MTU.
$ifinfo = get_interface_info($lancfg['if']);
$pconfig['mtu'] = $ifinfo['mtu'];

$pconfig['media'] = $lancfg['media'];
$pconfig['mediaopt'] = $lancfg['mediaopt'];
$pconfig['polling'] = isset($lancfg['polling']);

/* Wireless interface? */
if (isset($lancfg['wireless'])) {
	require("interfaces_wlan.inc");
	wireless_config_init();
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	$reqdfields = array();
	$reqdfieldsn = array();
	$reqdfieldst = array();

	if ($_POST['type'] === "Static") {
		$reqdfields = explode(" ", "ipaddr subnet");
		$reqdfieldsn = array(gettext("IP address"),gettext("Subnet bit count"));

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

		if (($_POST['ipaddr'] && !is_ipv4addr($_POST['ipaddr'])))
			$input_errors[] = gettext("A valid IPv4 address must be specified.");
		if ($_POST['subnet'] && !filter_var($_POST['subnet'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 32))))
			$input_errors[] = gettext("A valid network bit count (1-32) must be specified.");
	}

	if ($_POST['ipv6type'] === "Static") {
		$reqdfields = array_merge($reqdfields,explode(" ", "ipv6addr ipv6subnet"));
		$reqdfieldsn = array_merge($reqdfieldsn,array(gettext("IPv6 address"),gettext("Prefix")));

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

		if (($_POST['ipv6addr'] && !is_ipv6addr($_POST['ipv6addr'])))
			$input_errors[] = gettext("A valid IPv6 address must be specified.");
		if ($_POST['ipv6subnet'] && !filter_var($_POST['ipv6subnet'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 128))))
			$input_errors[] = gettext("A valid prefix (1-128) must be specified.");
		if (($_POST['ipv6gatewayr'] && !is_ipv6addr($_POST['ipv6gateway'])))
			$input_errors[] = gettext("A valid IPv6 Gateway address must be specified.");
	}

	/* Wireless interface? */
	if (isset($lancfg['wireless'])) {
		$wi_input_errors = wireless_config_post();
		if ($wi_input_errors) {
			$input_errors = array_merge($input_errors, $wi_input_errors);
		}
	}

	if (!$input_errors) {
		if(strcmp($_POST['type'],"Static") == 0) {
			$config['interfaces']['lan']['ipaddr'] = $_POST['ipaddr'];
			$config['interfaces']['lan']['subnet'] = $_POST['subnet'];
			$config['interfaces']['lan']['gateway'] = $_POST['gateway'];
		} else if (strcmp($_POST['type'],"DHCP") == 0) {
			$config['interfaces']['lan']['ipaddr'] = "dhcp";
		}

		if(strcmp($_POST['ipv6type'],"Static") == 0) {
			$config['interfaces']['lan']['ipv6addr'] = $_POST['ipv6addr'];
			$config['interfaces']['lan']['ipv6subnet'] = $_POST['ipv6subnet'];
			$config['interfaces']['lan']['ipv6gateway'] = $_POST['ipv6gateway'];
		} else if (strcmp($_POST['ipv6type'],"Auto") == 0) {
			$config['interfaces']['lan']['ipv6addr'] = "auto";
		}

		$config['interfaces']['lan']['mtu'] = $_POST['mtu'];
		$config['interfaces']['lan']['media'] = $_POST['media'];
		$config['interfaces']['lan']['mediaopt'] = $_POST['mediaopt'];
		$config['interfaces']['lan']['polling'] = $_POST['polling'] ? true : false;

		write_config();

		if ($_POST['apply']) {
			$retval = 0;
			if (!file_exists($d_sysrebootreqd_path)) {
				config_lock();
				$retval |= interfaces_lan_configure();
				config_unlock();
			}
			$savemsg = get_std_save_message($retval);
			if ($retval == 0) {
				if (file_exists($d_landirty_path)) {
					unlink($d_landirty_path);
				}
			}
		} else {
			touch($d_landirty_path);
		}
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
/* Calculate default IPv4 netmask bits for network's class. */
function calc_netmask_bits(ipaddr) {
    if (ipaddr.search(/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/) != -1) {
        var adr = ipaddr.split(/\./);
        if (adr[0] > 255 || adr[1] > 255 || adr[2] > 255 || adr[3] > 255)
            return "";
        if (adr[0] == 0 && adr[1] == 0 && adr[2] == 0 && adr[3] == 0)
            return "";

		if (adr[0] <= 127)
			return "8";
		else if (adr[0] <= 191)
			return "16";
		else
			return "24";
    }
    else
      return "";
}

function change_netmask_bits() {
	document.iform.subnet.value = calc_netmask_bits(document.iform.ipaddr.value);
}

function type_change() {
  switch(document.iform.type.selectedIndex) {
		case 0: /* Static */
			document.iform.ipaddr.readOnly = 0;
			document.iform.subnet.readOnly = 0;
			document.iform.gateway.readOnly = 0;

			showElementById('dhcpclientidentifier_tr','hide');
			showElementById('dhcphostname_tr','hide');

			break;

    case 1: /* DHCP */
			document.iform.ipaddr.readOnly = 1;
			document.iform.subnet.readOnly = 1;
			document.iform.gateway.readOnly = 1;

			showElementById('dhcpclientidentifier_tr','show');
			showElementById('dhcphostname_tr','show');

			break;
  }
}

function ipv6_type_change() {
  switch(document.iform.ipv6type.selectedIndex) {
		case 0: /* Static */
			document.iform.ipv6addr.readOnly = 0;
			document.iform.ipv6subnet.readOnly = 0;
			document.iform.ipv6gateway.readOnly = 0;
			break;

    case 1: /* Autoconfigure */
			document.iform.ipv6addr.readOnly = 1;
			document.iform.ipv6subnet.readOnly = 1;
			document.iform.ipv6gateway.readOnly = 1;
			break;
  }
}

function media_change() {
  switch(document.iform.media.value) {
		case "autoselect":
			showElementById('mediaopt_tr','hide');
			break;

		default:
			showElementById('mediaopt_tr','show');
			break;
  }
}
// -->
</script>
<form action="interfaces_lan.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				<?php if ($savemsg) print_info_box($savemsg); ?>
				<?php if (file_exists($d_landirty_path)) print_config_change_box();?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td colspan="2" valign="top" class="listtopic"><?=gettext("IPv4 Configuration"); ?></td>
			    </tr>
			    <tr>
			    	<td width="22%" valign="top" class="vncellreq"><?=gettext("Type"); ?></td>
			      <td width="78%" class="vtable">
			  			<select name="type" class="formfld" id="type" onchange="type_change()">
			          <?php $opts = split(" ", "Static DHCP"); foreach ($opts as $opt): ?>
			          <option <?php if ($opt == $pconfig['type']) echo "selected";?>>
			            <?=htmlspecialchars($opt);?>
			          </option>
			          <?php endforeach; ?>
			        </select>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("IP address"); ?></td>
			      <td width="78%" class="vtable">
			        <input name="ipaddr" type="text" class="formfld" id="ipaddr" size="20" value="<?=htmlspecialchars($pconfig['ipaddr']);?>">
			        /
			        <select name="subnet" class="formfld" id="subnet">
			          <?php for ($i = 32; $i > 0; $i--):?>
			          <option value="<?=$i;?>" <?php if ($i == $pconfig['subnet']) echo "selected";?>><?=$i;?></option>
			          <?php endfor;?>
			        </select>
			        <img name="calcnetmaskbits" src="calc.gif" title="<?=gettext("Calculate netmask bits");?>" width="16" height="17" align="top" border="0" onclick="change_netmask_bits()" style="cursor:pointer">
			      </td>
			    </tr>
			     <tr>
			      <td valign="top" class="vncell"><?=gettext("IPv4 Gateway"); ?></td>
			      <td class="vtable">
			        <input name="gateway" type="text" class="formfld" id="gateway" size="20" value="<?=htmlspecialchars($pconfig['gateway']);?>">
			      </td>
			    </tr>
			    <tr id="dhcpclientidentifier_tr">
			      <td width="22%" valign="top" class="vncell"><?=gettext("Client Identifier");?></td>
			      <td width="78%" class="vtable">
			        <input name="dhcpclientidentifier" type="text" class="formfld" id="dhcpclientidentifier" size="40" value="<?=htmlspecialchars($pconfig['dhcpclientidentifier']);?>" readonly>
			        <br><span class="vexpl"><?=gettext("The value in this field is sent as the DHCP client identifier when requesting a DHCP lease.");?></span>
			      </td>
			    </tr>
			    <tr id="dhcphostname_tr">
			      <td width="22%" valign="top" class="vncell"><?=gettext("Hostname");?></td>
			      <td width="78%" class="vtable">
			        <input name="dhcphostname" type="text" class="formfld" id="dhcphostname" size="40" value="<?=htmlspecialchars($pconfig['dhcphostname']);?>" readonly><br>
			        <span class="vexpl"><?=gettext("The value in this field is sent as the DHCP hostname when requesting a DHCP lease.");?></span>
			      </td>
			    </tr>
			    <tr>
						<td colspan="2" class="list" height="12"></td>
					</tr>
					<tr>
			      <td colspan="2" valign="top" class="listtopic"><?=gettext("IPv6 Configuration"); ?></td>
			    </tr>
			    <tr>
			    	<td width="22%" valign="top" class="vncellreq"><?=gettext("Type"); ?></td>
			      <td width="78%" class="vtable">
			  			<select name="ipv6type" class="formfld" id="ipv6type" onchange="ipv6_type_change()">
			          <?php $opts = split(" ", "Static Auto"); foreach ($opts as $opt): ?>
			          <option <?php if ($opt == $pconfig['ipv6type']) echo "selected";?>>
			            <?=htmlspecialchars($opt);?>
			          </option>
			          <?php endforeach; ?>
			        </select>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("IPv6 address"); ?></td>
			      <td width="78%" class="vtable">
			        <input name="ipv6addr" type="text" class="formfld" id="ipv6addr" size="30" value="<?=htmlspecialchars($pconfig['ipv6addr']);?>">
					 /
					 <input name="ipv6subnet" type="text" class="formfld" id="ipv6subnet" size="2" value="<?=htmlspecialchars($pconfig['ipv6subnet']);?>">
			      </td>
			    </tr>
					<tr>
			      <td valign="top" class="vncell"><?=gettext("IPv6 Gateway"); ?></td>
			      <td class="vtable">
			        <input name="ipv6gateway" type="text" class="formfld" id="ipv6gateway" size="20" value="<?=htmlspecialchars($pconfig['ipv6gateway']);?>">
			      </td>
					</tr>
					<tr>
						<td colspan="2" class="list" height="12"></td>
					</tr>
					<tr>
			      <td colspan="2" valign="top" class="listtopic"><?=gettext("Global Configuration"); ?></td>
			    </tr>
			    <tr>
			      <td valign="top" class="vncell"><?=gettext("MTU"); ?></td>
			      <td class="vtable">
			        <input name="mtu" type="text" class="formfld" id="mtu" size="20" value="<?=htmlspecialchars($pconfig['mtu']);?>">&nbsp;<br>
			        <span class="vexpl"><?=gettext("Standard MTU is 1500, use 9000 for jumbo frame."); ?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Device polling"); ?></td>
			      <td width="78%" class="vtable">
			        <input name="polling" type="checkbox" id="polling" value="yes" <?php if ($pconfig['polling']) echo "checked"; ?>>
			        <?=gettext("Enable device polling"); ?><br>
			        <span class="vexpl"><?=gettext("Device polling is a technique that lets the system periodically poll network devices for new data instead of relying on interrupts. This can reduce CPU load and therefore increase throughput, at the expense of a slightly higher forwarding delay (the devices are polled 1000 times per second). Not all NICs support polling."); ?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Speed"); ?></td>
			      <td width="78%" class="vtable">
			        <select name="media" class="formfld" id="media" onchange="media_change()">
			          <?php $types = explode(",", "autoselect,10baseT/UTP,100baseTX,1000baseTX,1000baseSX");
			          $vals = explode(" ", "autoselect 10baseT/UTP 100baseTX 1000baseTX 1000baseSX");
			          $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
			          <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['media']) echo "selected";?>>
			          <?=htmlspecialchars($types[$j]);?>
			          </option>
			          <?php endfor; ?>
			        </select>
			      </td>
					</tr>
					<tr id="mediaopt_tr">
			      <td width="22%" valign="top" class="vncell"><?=gettext("Duplex"); ?></td>
			      <td width="78%" class="vtable">
			        <select name="mediaopt" class="formfld" id="mediaopt">
			          <?php $types = explode(",", "half-duplex,full-duplex");
			          $vals = explode(" ", "half-duplex full-duplex");
			          $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
			          <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['mediaopt']) echo "selected";?>>
			          <?=htmlspecialchars($types[$j]);?>
			          </option>
			          <?php endfor; ?>
			        </select>
			      </td>
			    </tr>
			    </tr>
					<?php /* Wireless interface? */
					if (isset($lancfg['wireless']))
						wireless_config_print();
					?>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%"><span class="vexpl"><span class="red"><strong><?=gettext("Warning"); ?>:<br>
							</strong></span><?php echo sprintf(gettext("After you click &quot;Save&quot;, you may also have to do one or more of the following steps before you can access %s again: <ul><li>change the IP address of your computer</li><li>access the webGUI with the new IP address</li></ul>"), get_product_name());?></span>
						</td>
			    </tr>
			  </table>
			</td>
		</tr>
	</table>
</form>
<script language="JavaScript">
<!--
type_change();
ipv6_type_change();
media_change();
//-->
</script>
<?php include("fend.inc");?>
