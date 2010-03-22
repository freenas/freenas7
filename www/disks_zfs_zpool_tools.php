#!/usr/local/bin/php
<?php
/*
	disks_zfs_zpool_tools.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	Copyright (c) 2008-2010 Volker Theile (votdev@gmx.de)
	Copyright (c) 2008 Nelson Silva
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2010 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");
require("zfs.inc");

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Pools"), gettext("Tools"));

if (!isset($config['zfs']['pools']) || !is_array($config['zfs']['pools']['pool']))
	$config['zfs']['pools']['pool'] = array();

if (!isset($config['zfs']['vdevices']) || !is_array($config['zfs']['vdevices']['vdevice']))
	$config['zfs']['vdevices']['vdevice'] = array();

array_sort_key($config['zfs']['pools']['pool'], "name");
array_sort_key($config['zfs']['vdevices']['vdevice'], "name");

$a_pool = $config['zfs']['pools']['pool'];
$a_vdevice = $config['zfs']['vdevices']['vdevice'];

$pconfig['action'] = $_GET['action'];
if (isset($_POST['action']))
	$pconfig['action'] = $_POST['action'];

$pconfig['option'] = $_GET['option'];
if (isset($_POST['option']))
	$pconfig['option'] = $_POST['option'];

$pconfig['pool'] = $_GET['pool'];
if (isset($_POST['pool']))
	$pconfig['pool'] = $_POST['pool'];

$pconfig['device'] = $_GET['device'];
if (isset($_POST['device']))
	$pconfig['device'] = $_POST['device'];

$pconfig['device_new'] = $_GET['device_new'];
if (isset($_POST['device_new']))
	$pconfig['device_new'] = $_POST['device_new'];

if ($_POST || $_GET) {
	unset($input_errors);
	unset($do_action);

	if (!$input_errors) {
		$do_action = true;
	}
}

if (!isset($do_action)) {
	$do_action = false;
	$pconfig['action'] = "history";
	$pconfig['option'] = "";
	$pconfig['pool'] = "";
	$pconfig['device'] = "";
	$pconfig['device_new'] = "";
}
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">//<![CDATA[
function command_change() {
	showElementById('device_new_tr','hide');
	document.iform.option.length = 0;
	var action = document.iform.action.value;
	switch (action) {
		case "upgrade":
			document.iform.option[0] = new Option('Display','v', <?=$pconfig['option'] === 'v' ? "true" : "false"?>);
			document.iform.option[1] = new Option('All','a', <?=$pconfig['option'] === 'a' ? "true" : "false"?>);
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[2] = new Option('Pool','p', <?=$pconfig['option'] === 'p' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "history":
			document.iform.option[0] = new Option('All','a', <?=$pconfig['option'] === 'a' ? "true" : "false"?>);
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[1] = new Option('Pool','p', <?=$pconfig['option'] === 'p' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "scrub":
			document.iform.option[0] = new Option('Start','s', <?=$pconfig['option'] === 's' ? "true" : "false"?>);
			document.iform.option[1] = new Option('Stop','st', <?=$pconfig['option'] === 'st' ? "true" : "false"?>);
			break;
		case "clear":
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('Pool','p', <?=$pconfig['option'] === 'p' ? "true" : "false"?>);
			document.iform.option[1] = new Option('Device','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "offline":
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('Device','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			document.iform.option[1] = new Option('Temporary Device','t', <?=$pconfig['option'] === 't' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "online":
			<?php if(is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('Device','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "remove":
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('Device','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "replace":
			showElementById('device_new_tr','show');
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('Device','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			<?php endif;?>
			break;
		default:
			break;
	}
	option_change();
}

function option_change() {
	var div = document.getElementById("devices");
	div.innerHTML = "<?=gettext("No device selected.");?>";

	document.iform.pool.disabled = 1;
	document.iform.pool.length = 0;
	var option = document.iform.option.value;
	if (option == "s" || option == "st" || option == "p" || option == "d" || option == "t") {
		<?php if (is_array($a_pool) && !empty($a_pool)):?>
		document.iform.pool.disabled = 0;
		<?php $i = 0; foreach($a_pool  as $pool):?>
		document.iform.pool[<?=$i?>] = new Option('<?=$pool['name']?>','<?=$pool['name']?>', <?=$pconfig['pool'] === $pool['name'] ? "true" : "false"?>);
		<?php $i++; endforeach;?>
		<?php endif;?>
	}
	if (option == "d" || option == "t") {
		pool_change();
	}
}

function pool_change() {
	var div = document.getElementById("devices");
	div.innerHTML ="";
	var pool = document.iform.pool.value;
	switch (pool) {
		<?php foreach ($a_pool as $pool):?>
		case "<?=$pool['name'];?>": {
			<?php
			$result = array();
			foreach ($pool['vdevice'] as $vdevicev) {
				$index = array_search_ex($vdevicev, $a_vdevice, "name");
				$vdevice = $a_vdevice[$index];
				foreach ($vdevice['device'] as $devicev) {
					$a_disk = get_conf_disks_filtered_ex("fstype", "zfs");
					$index = array_search_ex($devicev, $a_disk, "devicespecialfile");
					$result[] = $a_disk[$index];
				}
			}
			$i = 0;
			array_sort_key($result, "name");
			foreach ($result as $disk) {
				$checked = "";
				if (is_array($pconfig['device'])) {
					foreach ($pconfig['device'] as $devicev) {
						if ($devicev === $disk['name']) {
							$checked = " checked=\"checked\"";
							break;
						}
					}
				} else {
					if ($pconfig['device'] === $disk['name']) {
						$checked = " checked=\"checked\"";
					}
				}
				?>
				div.innerHTML += "<input name='device[]' id='<?=$i?>' type='checkbox' value='<?=$disk['name'];?>'<?=$checked?> />";
				div.innerHTML += "<?=$disk['name'];?> (<?=$disk['size']?>, <?=$disk['desc']?>)";
				div.innerHTML += "<br />";
				document.iform.device_new[<?=$i;?>] = new Option('<?="{$disk['name']} ({$disk['size']}, {$disk['desc']})";?>','<?=$disk['name'];?>','false');
				<?php
				$i++;
			}
			?>
		}
		break;
		<?php endforeach;?>
	}
}
//]]>
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext("Datasets");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gettext("Configuration");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabinact"><a href="disks_zfs_zpool_vdevice.php"><span><?=gettext("Virtual device");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Management");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_zpool_tools.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Tools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_info.php"><span><?=gettext("Information");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_io.php"><span><?=gettext("I/O statistics");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_zfs_zpool_tools.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Command");?></td>
						<td width="78%" class="vtable">
							<select name="action" class="formfld" id="action" onchange="command_change()">
							<?
								$cmd = "upgrade history";
								if (is_array($a_pool) && !empty($a_pool)) {
									$cmd .= " remove clear scrub offline online replace";
								}
								$a_cmd = explode(" ", $cmd);
								asort($a_cmd);
								foreach ($a_cmd as $cmdv) {
									echo "<option value=\"${cmdv}\"";
									if ($cmdv === $pconfig['action'])
										echo " selected=\"selected\"";
									echo ">${cmdv}</option>";
								}
							?>
							</select>
						</td>
					</tr>
					<?php html_combobox("option", gettext("Option"), NULL, NULL, "", true, false, "option_change()");?>
					<?php html_combobox("pool", gettext("Pool"), NULL, NULL, "", true, true, "pool_change()");?>
					<tr>
						<td valign="top" class="vncellreq"><?=gettext("Devices");?></td>
						<td class="vtable">
							<div id="devices">
								<?=gettext("No device selected.");?>
							</div>
						</td>
					</tr>
					<?php html_combobox("device_new", gettext("New Device"), NULL, NULL, "", true);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Send Command!");?>" />
				</div>
				<?php if ($do_action) {
				echo(sprintf("<div id='cmdoutput'>%s</div>", gettext("Command output:")));
				echo('<pre class="cmdoutput">');
				ob_end_flush();

				$action = $pconfig['action'];
				$option = $pconfig['option'];
				$pool = $pconfig['pool'];
				$device = $pconfig['device'];
				switch ($action) {
					case "upgrade": {
							switch ($option) {
								case "v": {
										zfs_zpool_cmd($action, "-v", false, false, true, &$output);
										foreach ($output as $line) {
											if (preg_match("/(\s+)(\d+)(\s+)(.*)/",$line, $match)) {
												$href = "<a href=\"http://www.opensolaris.org/os/community/zfs/version/{$match[2]}\" target=\"_blank\">{$match[2]}</a>";
												echo "{$match[1]}{$href}{$match[3]}{$match[4]}";
											} else {
												echo htmlspecialchars($line);
											}
											echo "<br />";
										}
									}
									break;

								case "a":
									zfs_zpool_cmd($action, "-a", true);
									break;

								case "p":
									zfs_zpool_cmd($action, $pool, true);
									break;
							}
						}
						break;

					case "history": {
							switch ($option) {
								case "a":
									zfs_zpool_cmd($action, "", true);
									break;

								case "p":
									zfs_zpool_cmd($action, $pool, true);
									break;
							}
						}
						break;

					case "scrub": {
							switch ($option) {
								case "s":
									zfs_zpool_cmd($action, $pool, true);
					 				break;

					 			case "st":
									zfs_zpool_cmd($action,"-s {$pool}", true);
									break;
							}
						}
						break;

					case "clear": {
							switch ($option) {
								case "p":
									zfs_zpool_cmd($action, $pool, true);
									break;

								case "d":
									if (is_array($device) ) {
										foreach ($device as $dev) {
											zfs_zpool_cmd($action, "{$pool} {$dev}", true);
										}
									} else {
										zfs_zpool_cmd($action, "{$pool} {$device}", true);
									}
									break;
							}
						}
						break;

					case "offline": {
							switch ($option) {
								case "t":
									zfs_zpool_cmd($action, "-t {$pool} {$device}", true);
								break;

								case "d":
									if (is_array($device) ) {
										foreach ($device as $dev) {
											zfs_zpool_cmd($action, "{$pool} {$dev}", true);
										}
									} else {
										zfs_zpool_cmd($action, "{$pool} {$device}", true);
									}
								break;
							}
						}
						break;

					case "online": {
							switch ($option) {
								case "d":
									if (is_array($device) ) {
										foreach ($device as $dev) {
											zfs_zpool_cmd($action, "{$pool} {$dev}", true);
										}
									} else {
										zfs_zpool_cmd($action, "{$pool} {$device}", true);
									}
									break;
							}
						}
						break;

					case "remove": {
							switch ($option) {
								case "d":
									if (is_array($device) ) {
										foreach ($device as $dev) {
											zfs_zpool_cmd($action, "{$pool} {$dev}", true);
										}
									} else {
										zfs_zpool_cmd($action, "{$pool} {$device}", true);
									}
								break;
							}
						}
						break;

					case "replace": {
							switch ($option) {
								case "d":
									if (is_array($device) ) {
										foreach ($device as $dev) {
											zfs_zpool_cmd($action, "{$pool} {$dev} {$pconfig['device_new']}", true);
										}
									} else {
										zfs_zpool_cmd($action, "{$pool} {$device} {$pconfig['device_new']}", true);
									}
								break;
							}
						}
						break;
				}
				echo('</pre>');
				};?>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
command_change();
</script>
<?php include("fend.inc");?>
