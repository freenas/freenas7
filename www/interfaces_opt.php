#!/usr/local/bin/php
<?php 
/*
	interfaces_opt.php
	part of FreeNAS (http://www.freenas.org)
	Based on m0n0wall (http://m0n0.ch/wall)
	
	Copyright (C) 2005 Olivier Cochard-Labbé <olivier@freenas.org>.
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

unset($index);
if ($_GET['index'])
	$index = $_GET['index'];
else if ($_POST['index'])
	$index = $_POST['index'];
	
if (!$index)
	exit;

$optcfg = &$config['interfaces']['opt' . $index];

if ($config['interfaces']['opt' . $index]['ipaddr'] == "dhcp") {
	$pconfig['type'] = "DHCP";
} else {
	$pconfig['type'] = "Static";
	$pconfig['ipaddr'] = $optcfg['ipaddr'];
  $pconfig['subnet'] = $optcfg['subnet'];
}

$pconfig['descr'] = $optcfg['descr'];
$pconfig['mtu'] = $optcfg['mtu'];
$pconfig['enable'] = isset($optcfg['enable']);
$pconfig['polling'] = isset($optcfg['polling']);
$pconfig['media'] = $optcfg['media'];
$pconfig['mediaopt'] = $optcfg['mediaopt'];

/* Wireless interface? */
if (isset($optcfg['wireless'])) {
	require("interfaces_wlan.inc");
	wireless_config_init();
}

if ($_POST) {

	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	if ($_POST['enable'])
	{
		/* description unique? */
		for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++) {
			if ($i != $index) {
				if ($config['interfaces']['opt' . $i]['descr'] == $_POST['descr']) {
					$input_errors[] = "An interface with the specified description already exists.";
				}
			}
		}
	
    if ($_POST['type'] == "Static") {
		  $reqdfields = explode(" ", "descr ipaddr subnet");
		  $reqdfieldsn = explode(",", "Description,IP address,Subnet bit count");
		
		  do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		}
		
		if (($_POST['ipaddr'] && !is_ipaddr($_POST['ipaddr'])))
			$input_errors[] = _INTPHP_MSGVALIDIP;
		if (($_POST['subnet'] && !is_numeric($_POST['subnet'])))
			$input_errors[] = _INTPHP_MSGVALIDMASK;
		if (($_POST['mtu'] && !is_mtu($_POST['mtu'])))
			$input_errors[] = _INTPHP_MSGVALIDMTU;
	
	}
	
	/* Wireless interface? */
	if (isset($optcfg['wireless'])) {
		$wi_input_errors = wireless_config_post();
		if ($wi_input_errors) {
			$input_errors = array_merge($input_errors, $wi_input_errors);
		}
	}
	
	if (!$input_errors) {
    if($_POST['type'] == "Static") {
      $optcfg['ipaddr'] = $_POST['ipaddr'];
			$optcfg['subnet'] = $_POST['subnet'];
		} else if ($_POST['type'] == "DHCP") {
			$optcfg['ipaddr'] = "dhcp";
		}

		$optcfg['descr'] = $_POST['descr'];
		$optcfg['mtu'] = $_POST['mtu'];
		$optcfg['enable'] = $_POST['enable'] ? true : false;
		$optcfg['polling'] = $_POST['polling'] ? true : false;
		$optcfg['media'] = $_POST['media'];
		$optcfg['mediaopt'] = $_POST['mediaopt'];

		write_config();
		
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval = interfaces_optional_configure();
			
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}

$pgtitle = array(_INTLANPHP_NAME, "Optional $index (" . htmlspecialchars($optcfg['descr']) . ")");
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_over) {
	var endis;
	endis = !(document.iform.enable.checked || enable_over);
	document.iform.type.disabled = endis;
	document.iform.descr.disabled = endis;
	document.iform.ipaddr.disabled = endis;
	document.iform.subnet.disabled = endis;
	
	if (document.iform.mode) {
		 document.iform.mode.disabled = endis;
		 document.iform.ssid.disabled = endis;
		 document.iform.channel.disabled = endis;
		 document.iform.stationname.disabled = endis;
		 document.iform.wep_enable.disabled = endis;
		 document.iform.key1.disabled = endis;
		 document.iform.key2.disabled = endis;
		 document.iform.key3.disabled = endis;
		 document.iform.key4.disabled = endis;
	}
}
function bridge_change(enable_over) {
	var endis;

	if (document.iform.enable.checked || enable_over) {
		endis = !((document.iform.bridge.selectedIndex == 0) || enable_over);
	} else {
		endis = true;
	}

	document.iform.ipaddr.disabled = endis;
	document.iform.subnet.disabled = endis;
}
function calc_netmask_bits(ipaddr) {
    if (ipaddr.search(/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/) != -1) {
        var adr = ipaddr.split(/\./);
        if (adr[0] > 255 || adr[1] > 255 || adr[2] > 255 || adr[3] > 255)
            return 0;
        if (adr[0] == 0 && adr[1] == 0 && adr[2] == 0 && adr[3] == 0)
            return 0;
		
		if (adr[0] <= 127)
			return 23;
		else if (adr[0] <= 191)
			return 15;
		else
			return 7;
    }
    else
        return 0;
}
function change_netmask_bits() {
	document.iform.subnet.selectedIndex = calc_netmask_bits(document.iform.ipaddr.value);
}
function type_change() {
  if (document.iform.enable.checked) {
    switch(document.iform.type.selectedIndex)
    {
  		case 0: /* Static */
        document.iform.ipaddr.disabled = 0;
      	document.iform.subnet.disabled = 0;
        break;
      case 1: /* DHCP */
        document.iform.ipaddr.disabled = 1;
      	document.iform.subnet.disabled = 1;
        break;
    }
  }
}
// -->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if ($optcfg['if']): ?>
            <form action="interfaces_opt.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td colspan="2" valign="top" class="optsect_t">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tr>
                        <td class="optsect_s"></td>
                        <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false);bridge_change(false)"><strong><?=_ACTIVATE;?></strong></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell"><?=_TYPE;?></td>
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
                  <td width="22%" valign="top" class="vncell"><?=_INTPHP_DESC;?></td>
                  <td width="78%" class="vtable"> 
                    <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?=htmlspecialchars($pconfig['descr']);?>">
                    <br><span class="vexpl"><?=_INTPHP_DESCTEXT;?></span>
                  </td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" height="16"></td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" class="listtopic"><?=_INTPHP_STATIC;?></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_INTPHP_IP; ?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="ipaddr" type="text" class="formfld" id="ipaddr" size="20" value="<?=htmlspecialchars($pconfig['ipaddr']);?>">
                    /
                	<select name="subnet" class="formfld" id="subnet">
					<?php for ($i = 31; $i > 0; $i--): ?>
					<option value="<?=$i;?>" <?php if ($i == $pconfig['subnet']) echo "selected"; ?>><?=$i;?></option>
					<?php endfor; ?>
                    </select>
                    <img name="calcnetmaskbits" src="calc.gif" title="<?=_INTPHP_CALCNETMASKBITS;?>" width="16" height="17" align="top" border="0" onclick="change_netmask_bits()" style="cursor:pointer">
                  </td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" height="16"></td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" class="listtopic"><?=_INTPHP_DHCP; ?></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_INTPHP_DHCPCLIENTIDENTIFIER;?></td>
                  <td width="78%" class="vtable">
                    <input name="dhcpclientidentifier" type="text" class="formfld" id="dhcpclientidentifier" size="40" value="<?=htmlspecialchars(get_macaddr($optcfg['if']));?>" disabled>
                    <br><span class="vexpl"><?=_INTPHP_DHCPCLIENTIDENTIFIERTEXT;?></span>
                  </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_INTPHP_DHCPHOSTNAME;?></td>
                  <td width="78%" class="vtable">
                    <input name="dhcphostname" type="text" class="formfld" id="dhcphostname" size="40" value="<?=htmlspecialchars($config['system']['hostname'] . "." . $config['system']['domain']);?>" disabled>
                    <br><span class="vexpl"><?=_INTPHP_DHCPHOSTNAMETEXT;?></span>
                  </td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" height="4"></td>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" class="listtopic"><?=_INTPHP_GENERAL; ?></td>
                </tr>
                <tr>
                  <td valign="top" class="vncell"><?=_INTPHP_MTU; ?></td>
                  <td class="vtable"><?=$mandfldhtml;?><input name="mtu" type="text" class="formfld" id="mtu" size="20" value="<?=htmlspecialchars($pconfig['mtu']);?>"> 
                  <br>
					<?=_INTPHP_MTUTEXT; ?>
                  </td>
                </tr>
				<tr> 
                  <td width="22%" valign="top" class="vncell"><?=_INTPHP_POLLING; ?></td>
                  <td width="78%" class="vtable"> 
                    <input name="polling" type="checkbox" id="polling" value="yes" <?php if ($pconfig['polling']) echo "checked"; ?>>
                    <strong><?=_INTPHP_ENPOLLING; ?></strong><br>
					<?=_INTPHP_POLLINGTEXT; ?>
					</td>
                </tr>
                 <tr> 
                  <td width="22%" valign="top" class="vncell"><?=_INTPHP_SPEED; ?></td>
                  <td width="78%" class="vtable">
					<select name="media" class="formfld" id="media">
                      <?php $types = explode(",", "autoselect,10baseT/UTP,100baseTX,1000baseTX,1000baseSX");
					        $vals = explode(" ", "autoselect 10baseT/UTP 100baseTX 1000baseTX 1000baseSX");
					  $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                      <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['media']) echo "selected";?>> 
                      <?=htmlspecialchars($types[$j]);?>
                      </option>
                      <?php endfor; ?>
                    </select></td>
				</tr>
				<tr> 
                  <td width="22%" valign="top" class="vncell"><?=_INTPHP_DUPLEX; ?></td>
                  <td width="78%" class="vtable">
					<select name="mediaopt" class="formfld" id="mediaopt">
                      <?php $types = explode(",", "half-duplex,full-duplex");
					        $vals = explode(" ", "half-duplex full-duplex");
					  $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                      <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['mediaopt']) echo "selected";?>> 
                      <?=htmlspecialchars($types[$j]);?>
                      </option>
                      <?php endfor; ?>
                    </select></td>
				</tr>
				<?php /* Wireless interface? */
				if (isset($optcfg['wireless']))
					wireless_config_print();
				?>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="index" type="hidden" value="<?=$index;?>"> 
				  <input name="Submit" type="submit" class="formbtn" value="<?=_SAVE;?>" onclick="enable_change(true);bridge_change(true)"> 
                  </td>
                </tr>      
              </table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
type_change();
//-->
</script>
<?php else: ?>
<strong>Optional <?=$index;?> has been disabled because there is no OPT<?=$index;?> interface.</strong>
<?php endif; ?>
<?php include("fend.inc"); ?>
