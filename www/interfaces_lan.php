#!/usr/local/bin/php
<?php 
/*
	interfaces_lan.php
	part of FreeNAS (http://freenas.org)
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

$pgtitle = array(_INTLANPHP_NAME, _INTLANPHP_NAMEDESC);

$lancfg = &$config['interfaces']['lan'];
$optcfg = &$config['interfaces']['lan'];
$pconfig['ipaddr'] = $config['interfaces']['lan']['ipaddr'];
$pconfig['subnet'] = $config['interfaces']['lan']['subnet'];
$pconfig['gateway'] = $config['interfaces']['lan']['gateway'];
$pconfig['mtu'] = $config['interfaces']['lan']['mtu'];
$pconfig['media'] = $config['interfaces']['lan']['media'];
$pconfig['mediaopt'] = $config['interfaces']['lan']['mediaopt'];
$pconfig['polling'] = isset($config['interfaces']['lan']['polling']);

/* Wireless interface? */
if (isset($optcfg['wireless'])) {
	require("interfaces_wlan.inc");
	wireless_config_init();
}

if ($_POST) {

	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = explode(" ", "ipaddr subnet");
	$reqdfieldsn = explode(",", "IP address,Subnet bit count");
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if (($_POST['ipaddr'] && !is_ipaddr($_POST['ipaddr']))) {
		$input_errors[] = _INTPHP_MSGVALIDIP;
	}
	if (($_POST['subnet'] && !is_numeric($_POST['subnet']))) {
		$input_errors[] = _INTPHP_MSGVALIDMASK;
	}
	if (($_POST['gateway'] && !is_ipaddr($_POST['gateway']))) {
		$input_errors[] = _INTPHP_MSGVALIDGW;
	}
	
	if (($_POST['mtu'] && !is_mtu($_POST['mtu']))) {
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
		$config['interfaces']['lan']['ipaddr'] = $_POST['ipaddr'];
		$config['interfaces']['lan']['subnet'] = $_POST['subnet'];
		$config['interfaces']['lan']['gateway'] = $_POST['gateway'];
		$config['interfaces']['lan']['mtu'] = $_POST['mtu'];
		$config['interfaces']['lan']['media'] = $_POST['media'];
		$config['interfaces']['lan']['mediaopt'] = $_POST['mediaopt'];
		$config['interfaces']['lan']['polling'] = $_POST['polling'] ? true : false;
					
		write_config();
		interfaces_lan_configure();
		
		/*
		touch($d_sysrebootreqd_path);

		$savemsg = get_std_save_message(0);
		
		if ($dhcpd_was_enabled)
			$savemsg .= "<br>Note that the DHCP server has been disabled.<br>Please review its configuration " .
				"and enable it again prior to rebooting.";
		*/
	}
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function gen_bits(ipaddr) {
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
function ipaddr_change() {
	document.iform.subnet.value = gen_bits(document.iform.ipaddr.value);
}
// -->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
            <form action="interfaces_lan.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_INTPHP_IP; ?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="ipaddr" type="text" class="formfld" id="ipaddr" size="20" value="<?=htmlspecialchars($pconfig['ipaddr']);?>" onchange="ipaddr_change()">
                    / 
                    <select name="subnet" class="formfld" id="subnet">
                      <?php for ($i = 31; $i > 0; $i--): ?>
                      <option value="<?=$i;?>" <?php if ($i == $pconfig['subnet']) echo "selected"; ?>>
                      <?=$i;?>
                      </option>
                      <?php endfor; ?>
                    </select></td>
                </tr>
                 <tr> 
                  <td valign="top" class="vncellreq"><?=_INTPHP_GW; ?></td>
                  <td class="vtable"><?=$mandfldhtml;?><input name="gateway" type="text" class="formfld" id="gateway" size="20" value="<?=htmlspecialchars($pconfig['gateway']);?>">
                  </td>
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
                      <?php $types = explode(",", "autoselect,half-duplex,full-duplex");
					        $vals = explode(" ", "autoselect half-duplex full-duplex");
					  $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                      <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['mediaopt']) echo "selected";?>> 
                      <?=htmlspecialchars($types[$j]);?>
                      </option>
                      <?php endfor; ?>
                    </select></td>
				</tr>
                </tr>
				<?php /* Wireless interface? */
				if (isset($optcfg['wireless']))
					wireless_config_print();
				?>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save"> 
                  </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"><span class="vexpl"><span class="red"><strong><?=_WARNING; ?>:<br>
                    </strong></span><?=_INTPHP_TEXT; ?>
                    </span></td>
                </tr>
              </table>
</form>
<?php include("fend.inc"); ?>
