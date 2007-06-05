#!/usr/local/bin/php
<?php 
/*
	interfaces_lan.php
	part of FreeNAS (http://freenas.org)
	Based on m0n0wall (http://m0n0.ch/wall)
	
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
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
} else {
	$pconfig['type'] = "Static";
	$pconfig['ipaddr'] = $lancfg['ipaddr'];
	$pconfig['subnet'] = $lancfg['subnet'];
}

$pconfig['gateway'] = get_defaultgateway();
$pconfig['dhcphostname'] = $config['system']['hostname'] . "." . $config['system']['domain'];
$pconfig['dhcpclientidentifier'] = get_macaddr($lancfg['if']);
$pconfig['mtu'] = $lancfg['mtu'];
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

	if ($_POST['type'] == "Static")   {
		$reqdfields = explode(" ", "ipaddr subnet");
		$reqdfieldsn = array(gettext("IP address"),gettext("Subnet bit count"));
	
		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

		$reqdfields = explode(" ", "ipaddr gateway");
		$reqdfieldsn = array(gettext("IP address"),gettext("Gateway"));
		$reqdfieldst = explode(" ", "ipaddr ipaddr");
	}

	$reqdfields = array_merge($reqdfields, explode(" ", "mtu"));
	$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("MTU")));
	$reqdfieldst = array_merge($reqdfieldst, explode(" ", "mtu"));

	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

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
			// Reload page to update disabled controls, otherwise they're empty. 
			header("Location: interfaces_lan.php");
		} else {
			touch($d_landirty_path);
		}
	}
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
/* Calculate default netmask bits for network's class. */
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
  switch(document.iform.type.selectedIndex)
  {
		case 0: /* Static */
		  /* use current ip address as default */
      document.iform.ipaddr.value = "<?=htmlspecialchars(get_ipaddr($lancfg['if']))?>";

      document.iform.ipaddr.disabled = 0;
    	document.iform.subnet.disabled = 0;
      document.iform.gateway.disabled = 0;

      showElementById('dhcpclientidentifier_tr','hide');
      showElementById('dhcphostname_tr','hide');

      break;

    case 1: /* DHCP */
      document.iform.ipaddr.disabled = 1;
    	document.iform.subnet.disabled = 1;
      document.iform.gateway.disabled = 1;

      showElementById('dhcpclientidentifier_tr','show');
      showElementById('dhcphostname_tr','show');

      break;
  }
}
// -->
</script>
<form action="interfaces_lan.php" method="post" name="iform" id="iform">
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if (file_exists($d_landirty_path)): ?><p>
<?php print_info_box_np(gettext("The LAN configuration has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
  <input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
<?php endif; ?>
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr> 
      <td colspan="2" valign="top" class="listtopic"><?=gettext("General configuration"); ?></td>
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
        <?=$mandfldhtml;?><input name="ipaddr" type="text" class="formfld" id="ipaddr" size="20" value="<?=htmlspecialchars($pconfig['ipaddr']);?>">
        / 
        <select name="subnet" class="formfld" id="subnet">
          <?php for ($i = 31; $i > 0; $i--): ?>
          <option value="<?=$i;?>" <?php if ($i == $pconfig['subnet']) echo "selected"; ?>>
          <?=$i;?>
          </option>
          <?php endfor; ?>
        </select>
        <img name="calcnetmaskbits" src="calc.gif" title="<?=gettext("Calculate netmask bits");?>" width="16" height="17" align="top" border="0" onclick="change_netmask_bits()" style="cursor:pointer">
      </td>
    </tr>
     <tr> 
      <td valign="top" class="vncell"><?=gettext("Gateway"); ?></td>
      <td class="vtable">
        <input name="gateway" type="text" class="formfld" id="gateway" size="20" value="<?=htmlspecialchars($pconfig['gateway']);?>">
      </td>
    </tr>
    <tr id="dhcpclientidentifier_tr">
      <td width="22%" valign="top" class="vncell"><?=gettext("Client Identifier");?></td>
      <td width="78%" class="vtable">
        <input name="dhcpclientidentifier" type="text" class="formfld" id="dhcpclientidentifier" size="40" value="<?=htmlspecialchars($pconfig['dhcpclientidentifier']);?>" disabled>
        <br><span class="vexpl"><?=gettext("The value in this field is sent as the DHCP client identifier when requesting a DHCP lease.");?></span>
      </td>
    </tr>
    <tr id="dhcphostname_tr">
      <td width="22%" valign="top" class="vncell"><?=gettext("Hostname");?></td>
      <td width="78%" class="vtable">
        <input name="dhcphostname" type="text" class="formfld" id="dhcphostname" size="40" value="<?=htmlspecialchars($pconfig['dhcphostname']);?>" disabled><br>
        <span class="vexpl"><?=gettext("The value in this field is sent as the DHCP hostname when requesting a DHCP lease.");?></span>
      </td>
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
        <select name="media" class="formfld" id="media">
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
		<tr> 
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
</form>
<script language="JavaScript">
<!--
type_change();
//-->
</script>
<?php include("fend.inc"); ?>
