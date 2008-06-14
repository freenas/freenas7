<?php
/*
	interfaces_wlan.inc
	part of m0n0wall (http://m0n0.ch/wall)

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
function wireless_config_init() {
	global $optcfg, $pconfig;

	$pconfig['standard'] = $optcfg['wireless']['standard'];
	$pconfig['mode'] = $optcfg['wireless']['mode'];
	$pconfig['ssid'] = $optcfg['wireless']['ssid'];
	$pconfig['stationname'] = $optcfg['wireless']['stationname'];
	$pconfig['channel'] = $optcfg['wireless']['channel'];
	$pconfig['wep_enable'] = isset($optcfg['wireless']['wep']['enable']);

	if (is_array($optcfg['wireless']['wep'])) {
		$i = 1;
		foreach ($optcfg['wireless']['wep']['key'] as $wepkey) {
			$pconfig['key' . $i] = $wepkey['value'];
			if (isset($wepkey['txkey']))
				$pconfig['txkey'] = $i;
			$i++;
		}
		if (!isset($wepkey['txkey']))
			$pconfig['txkey'] = 1;
	}
}

function wireless_config_post() {
	global $optcfg, $pconfig;

	unset($input_errors);

	/* input validation */
	if ($_POST['enable']) {
		$reqdfields = explode(" ", "mode ssid channel");
		$reqdfieldsn = explode(",", "Mode,SSID,Channel");
		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

		if (!$input_errors) {
			/* bridge check (hostap only!) */
			if ($pconfig['bridge'] && ($pconfig['mode'] != "hostap"))
				$input_errors[] = "Bridging a wireless interface is only possible in hostap mode.";
		}
	}

	if (!$input_errors) {
		$optcfg['wireless']['standard'] = $_POST['standard'];
		$optcfg['wireless']['mode'] = $_POST['mode'];
		$optcfg['wireless']['ssid'] = $_POST['ssid'];
		$optcfg['wireless']['stationname'] = $_POST['stationname'];
		$optcfg['wireless']['channel'] = $_POST['channel'];
		$optcfg['wireless']['wep']['enable'] = $_POST['wep_enable'] ? true : false;

		$optcfg['wireless']['wep']['key'] = array();
		for ($i = 1; $i <= 4; $i++) {
			if ($_POST['key' . $i]) {
				$newkey = array();
				$newkey['value'] = $_POST['key' . $i];
				if ($_POST['txkey'] == $i)
					$newkey['txkey'] = true;
				$optcfg['wireless']['wep']['key'][] = $newkey;
			}
		}
	}

	return $input_errors;
}

function wireless_get_channellist($if) {
	$a_chanlist = array();

	exec("/sbin/ifconfig {$if} list chan", $rawdata);
	foreach ($rawdata as $line) {
		$line = trim($line);

		foreach (explode("Channel", $line) as $channelv) {
			if (preg_match("/(\d+)\s+:\s+(\d+)\s+Mhz\s+(.+)/", $channelv, $matches)) {
				$info = array();
				$info['channel'] = $matches[1];
				$info['freq'] = $matches[2];
				$info['mode'] = trim($matches[3]);

				$a_chanlist[$info['channel']] = $info;
			}
		}
	}

	ksort($a_chanlist, SORT_NUMERIC);

	return $a_chanlist;
}

function wireless_get_standards($if) {
	$a_standards = array();

	exec("/sbin/ifconfig -m {$if}", $rawdata);
	foreach ($rawdata as $line) {
		$line = trim($line);

		if (preg_match("/media \S+ mode (11\S+)/", $line, $matches)) {
			$a_standards[] = $matches[1];
		}
	}

	return array_unique($a_standards);
}

function wireless_config_print() {
	global $optcfg, $pconfig;
?>
								<?php html_separator();?>
								<?php html_titleline(gettext("Wireless configuration"));?>
								<?php $a_standard = array(); foreach (wireless_get_standards($optcfg['if']) as $standardv) { $a_standard[$standardv] = "802.{$standardv}"; }?>
								<?php html_combobox("standard", gettext("Standard"), $pconfig['standard'], $a_standard, gettext(""), true);?>
								<tr>
                  <td valign="top" class="vncellreq"><?=gettext("Mode");?></td>
                  <td class="vtable"><select name="mode" class="formfld" id="mode">
                      <?php
											$opts = array();
											if (strstr($optcfg['if'], "wi") || strstr($optcfg['if'], "ath"))
												$opts[] = "hostap";
											$opts[] = "BSS";
											$opts[] = "IBSS";
											foreach ($opts as $opt):?>
                      <option <?php if ($opt == $pconfig['mode']) echo "selected";?>>
                      <?=htmlspecialchars($opt);?>
                      </option>
                      <?php endforeach; ?>
                    </select> <br>
                    Note: IBSS mode is sometimes also called &quot;ad-hoc&quot;
                    mode;<br>
                    BSS mode is also known as &quot;infrastructure&quot; mode</td>
								</tr>
								<?php html_inputbox("ssid", gettext("SSID"), $pconfig['ssid'], gettext("Set the desired Service Set Identifier (aka network name)."), true, 20);?>
								<tr>
									<td valign="top" class="vncellreq"><?=gettext("Channel");?></td>
									<td class="vtable">
										<select name="channel" class="formfld" id="channel">
											<option <?php if ("any" === $pconfig['channel']) echo "selected";?> value="any"><?=gettext("Auto");?></option>
											<?php foreach (wireless_get_channellist($optcfg['if']) as $channelk => $channelv):?>
											<?php if ($channelv['mode'] === "11g") $mode = "11b/g"; else $mode = $channelv['mode'];?>
											<option <?php if ($channelk == $pconfig['channel']) echo "selected";?> value="<?=$channelk;?>"><?="{$channelv['channel']} ({$channelv['freq']} MHz, {$mode})";?></option>
											<?php endforeach;?>
										</select>
									</td>
								</tr>
								<?php html_inputbox("stationname", gettext("Station name"), $pconfig['stationname'], gettext("Set the name of this station (usually empty)."), false, 20);?>
								<?php html_separator();?>
								<?php html_titleline_checkbox("wep_enable", gettext("WEP"), $pconfig['wep_enable'] ? true : false, gettext("Enable"));?>
                <tr>
                  <td valign="top" class="vncell"><?=gettext("WEP");?></td>
                  <td class="vtable">
                    <table border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;TX key&nbsp;</td>
                      </tr>
                      <tr>
                        <td>Key 1:&nbsp;&nbsp;</td>
                        <td> <input name="key1" type="text" class="formfld" id="key1" size="30" value="<?=htmlspecialchars($pconfig['key1']);?>"></td>
                        <td align="center"> <input name="txkey" type="radio" value="1" <?php if ($pconfig['txkey'] == 1) echo "checked";?>>
                        </td>
                      </tr>
                      <tr>
                        <td>Key 2:&nbsp;&nbsp;</td>
                        <td> <input name="key2" type="text" class="formfld" id="key2" size="30" value="<?=htmlspecialchars($pconfig['key2']);?>"></td>
                        <td align="center"> <input name="txkey" type="radio" value="2" <?php if ($pconfig['txkey'] == 2) echo "checked";?>></td>
                      </tr>
                      <tr>
                        <td>Key 3:&nbsp;&nbsp;</td>
                        <td> <input name="key3" type="text" class="formfld" id="key3" size="30" value="<?=htmlspecialchars($pconfig['key3']);?>"></td>
                        <td align="center"> <input name="txkey" type="radio" value="3" <?php if ($pconfig['txkey'] == 3) echo "checked";?>></td>
                      </tr>
                      <tr>
                        <td>Key 4:&nbsp;&nbsp;</td>
                        <td> <input name="key4" type="text" class="formfld" id="key4" size="30" value="<?=htmlspecialchars($pconfig['key4']);?>"></td>
                        <td align="center"> <input name="txkey" type="radio" value="4" <?php if ($pconfig['txkey'] == 4) echo "checked";?>></td>
                      </tr>
                    </table><br/>
                    <?=gettext("40 (64) bit keys may be entered as 5 ASCII characters or 10 hex digits preceded by '0x'.")?><br/>
                    <?=gettext("104 (128) bit keys may be entered as 13 ASCII characters or 26 hex digits preceded by '0x'.")?>
									</td>
                </tr>
<?php } ?>
