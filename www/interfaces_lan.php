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

if ($config['interfaces']['lan']['ipaddr'] == "dhcp") {
	$pconfig['type'] = "DHCP";
} else {
	$pconfig['type'] = "Static";
	$pconfig['ipaddr'] = $config['interfaces']['lan']['ipaddr'];
	$pconfig['subnet'] = $config['interfaces']['lan']['subnet'];
  $pconfig['gateway'] = $config['interfaces']['lan']['gateway'];
}

$pconfig['dhcphostname'] = $config['system']['hostname'] . "." . $config['system']['domain'];
$pconfig['dhcpclientidentifier'] = get_macaddr($lancfg['if']);
$pconfig['mtu'] = $config['interfaces']['lan']['mtu'];
$pconfig['media'] = $config['interfaces']['lan']['media'];
$pconfig['mediaopt'] = $config['interfaces']['lan']['mediaopt'];
$pconfig['polling'] = isset($config['interfaces']['lan']['polling']);

/* Wireless interface? */
if (isset($lancfg['wireless'])) {
	require("interfaces_wlan.inc");
	wireless_config_init();
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

  /* input validation */
	if ($_POST['type'] == "Static")
  {
    $reqdfields = explode(" ", "ipaddr subnet");
    $reqdfieldsn = array(_INTPHP_IP,_INTPHP_NETMASK);
	
    do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	}
	
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
	if (isset($lancfg['wireless'])) {
		$wi_input_errors = wireless_config_post();
		if ($wi_input_errors) {
			$input_errors = array_merge($input_errors, $wi_input_errors);
		}
	}

	if (!$input_errors) {
    if($_POST['type'] == "Static") {
      $config['interfaces']['lan']['ipaddr'] = $_POST['ipaddr'];
      $config['interfaces']['lan']['subnet'] = $_POST['subnet'];
      $config['interfaces']['lan']['gateway'] = $_POST['gateway'];
    } else if ($_POST['type'] == "DHCP") {
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
        $retval = interfaces_lan_configure();
        config_unlock();
      }
      $savemsg = get_std_save_message($retval);
  		if ($retval == 0) {
  			if (file_exists($d_landirty_path))
  				unlink($d_landirty_path);
  		}
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
      break;
    case 1: /* DHCP */
      document.iform.ipaddr.disabled = 1;
    	document.iform.subnet.disabled = 1;
      document.iform.gateway.disabled = 1;
      break;
  }
}
// -->
</script>
<form action="interfaces_lan.php" method="post" name="iform" id="iform">
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if (file_exists($d_landirty_path)): ?><p>
<?php print_info_box_np(_INTPHP_MSGCHANGED);?><br>
  <input name="apply" type="submit" class="formbtn" id="apply" value="<?=_APPLY;?>"></p>
<?php endif; ?>
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr> 
      <td valign="middle"><strong><?=_TYPE; ?></strong></td>
      <td><select name="type" class="formfld" id="type" onchange="type_change()">
          <?php $opts = split(" ", "Static DHCP"); foreach ($opts as $opt): ?>
          <option <?php if ($opt == $pconfig['type']) echo "selected";?>> 
            <?=htmlspecialchars($opt);?>
          </option>
          <?php endforeach; ?>
        </select>
      </td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="listtopic"><?=_INTPHP_STATIC; ?></td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncellreq"><?=_INTPHP_IP; ?></td>
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
        <img name="calcnetmaskbits" src="calc.gif" title="<?=_INTPHP_CALCNETMASKBITS;?>" width="16" height="17" align="top" border="0" onclick="change_netmask_bits()" style="cursor:pointer">
      </td>
    </tr>
     <tr> 
      <td valign="top" class="vncellreq"><?=_INTPHP_GW; ?></td>
      <td class="vtable">
        <?=$mandfldhtml;?><input name="gateway" type="text" class="formfld" id="gateway" size="20" value="<?=htmlspecialchars($pconfig['gateway']);?>">
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
        <?=$mandfldhtml;?><input name="dhcpclientidentifier" type="text" class="formfld" id="dhcpclientidentifier" size="40" value="<?=htmlspecialchars($pconfig['dhcpclientidentifier']);?>" disabled>
        <br><span class="vexpl"><?=_INTPHP_DHCPCLIENTIDENTIFIERTEXT;?></span>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncellreq"><?=_INTPHP_DHCPHOSTNAME;?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?><input name="dhcphostname" type="text" class="formfld" id="dhcphostname" size="40" value="<?=htmlspecialchars($pconfig['dhcphostname']);?>" disabled><br>
        <span class="vexpl"><?=_INTPHP_DHCPHOSTNAMETEXT;?></span>
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
      <td class="vtable">
        <?=$mandfldhtml;?><input name="mtu" type="text" class="formfld" id="mtu" size="20" value="<?=htmlspecialchars($pconfig['mtu']);?>">&nbsp;<br>
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
        </select>
      </td>
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
        <input name="Submit" type="submit" class="formbtn" value="<?=_SAVE;?>"> 
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
<script language="JavaScript">
<!--
type_change();
//-->
</script>
<?php include("fend.inc"); ?>
