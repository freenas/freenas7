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
$a_vdevice_cache = array();
foreach ($a_vdevice as $vdevicev) {
	if ($vdevicev['type'] == 'cache') {
		$tmp = $vdevicev;
		$a_devs = array();
		foreach ($vdevicev['device'] as $device) {
			$name = preg_replace("/^\/dev\//", "", $device);
			$a_devs[] = $name;
		}
		$tmp['devs'] = implode(" ", $a_devs);
		$a_vdevice_cache[] = $tmp;
	}
}

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

$pconfig['device_cache'] = $_GET['device_cache'];
if (isset($_POST['device_cache']))
	$pconfig['device_cache'] = $_POST['device_cache'];

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
	$pconfig['device_cache'] = "";
}
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">//<![CDATA[
function command_change() {
	showElementById('devices_tr','hide');
	showElementById('device_new_tr','hide');
	showElementById('device_cache_tr','hide');
	document.iform.option.length = 0;
	document.iform.device_new.length = 0;
	document.iform.device_cache.length = 0;
	var action = document.iform.action.value;
	switch (action) {
		case "upgrade":
			document.iform.option[0] = new Option('<?=gettext("Display")?>','v', <?=$pconfig['option'] === 'v' ? "true" : "false"?>);
			document.iform.option[1] = new Option('<?=gettext("All")?>','a', <?=$pconfig['option'] === 'a' ? "true" : "false"?>);
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[2] = new Option('<?=gettext("Pool")?>','p', <?=$pconfig['option'] === 'p' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "history":
			document.iform.option[0] = new Option('<?=gettext("All")?>','a', <?=$pconfig['option'] === 'a' ? "true" : "false"?>);
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[1] = new Option('<?=gettext("Pool")?>','p', <?=$pconfig['option'] === 'p' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "scrub":
			document.iform.option[0] = new Option('<?=gettext("Start")?>','s', <?=$pconfig['option'] === 's' ? "true" : "false"?>);
			document.iform.option[1] = new Option('<?=gettext("Stop")?>','st', <?=$pconfig['option'] === 'st' ? "true" : "false"?>);
			break;
		case "clear":
			showElementById('devices_tr','show');
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('<?=gettext("Pool")?>','p', <?=$pconfig['option'] === 'p' ? "true" : "false"?>);
			document.iform.option[1] = new Option('<?=gettext("Device")?>','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "offline":
			showElementById('devices_tr','show');
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('<?=gettext("Device")?>','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			document.iform.option[1] = new Option('<?=gettext("Temporary Device")?>','t', <?=$pconfig['option'] === 't' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "online":
			showElementById('devices_tr','show');
			<?php if(is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('<?=gettext("Device")?>','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "remove":
			showElementById('devices_tr','show');
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('<?=gettext("Device")?>','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "replace":
			showElementById('devices_tr','show');
			showElementById('device_new_tr','show');
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('<?=gettext("Device")?>','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "cache add":
			showElementById('devices_tr','hide');
			showElementById('device_cache_tr','show');
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('<?=gettext("Device")?>','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
			<?php endif;?>
			break;
		case "cache remove":
			showElementById('devices_tr','hide');
			showElementById('device_cache_tr','show');
			<?php if (is_array($a_pool) && !empty($a_pool)):?>
			document.iform.option[0] = new Option('<?=gettext("Device")?>','d', <?=$pconfig['option'] === 'd' ? "true" : "false"?>);
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
		<?php if ($pconfig['pool'] === $pool['name']) {?>
			document.iform.pool.selectedIndex = <?=$i?>;
		<?php }?>
		<?php $i++; endforeach;?>
		<?php endif;?>
	}
	if (option == "d" || option == "t") {
		pool_change();
	}
}

function pool_change() {
	document.iform.device_new.length = 0;
	document.iform.device_cache.length = 0;
	var div = document.getElementById("devices");
	div.innerHTML ="";
	var pool = document.iform.pool.value;
	var action = document.iform.action.value;
	switch (pool) {
		<?php foreach ($a_pool as $pool):?>
		case "<?=$pool['name'];?>": {
			<?php
			$result = array();
			foreach ($pool['vdevice'] as $vdevicev) {
				$index = array_search_ex($vdevicev, $a_vdevice, "name");
				$vdevice = $a_vdevice[$index];
				$type = $vdevice['type'];
				foreach ($vdevice['device'] as $devicev) {
					$a_disk = get_conf_disks_filtered_ex("fstype", "zfs");
					$index = array_search_ex($devicev, $a_disk, "devicespecialfile");
					$tmp = $a_disk[$index];
					$tmp['type'] = $type;
					$result[] = $tmp;
				}
			}
			$i = 0; $j = 0;
			array_sort_key($result, "name");
			foreach ($result as $disk) {
				$checked = "";
				if (is_array($pconfig['device'])) {
					foreach ($pconfig['device'] as $devicev) {
						if ($devicev === $disk['name']) {
							$checked = " checked='checked'";
							break;
						}
					}
				} else {
					if ($pconfig['device'] === $disk['name']) {
						$checked = " checked='checked'";
					}
				}
				if ($disk['type'] != "cache") {
				?>

				if (action != "cache add" && action != "cache remove") {
					div.innerHTML += "<input name='device[]' id='<?=$i?>' type='checkbox' value='<?=$disk['name'];?>'<?=$checked?> />";
					div.innerHTML += "<?=$disk['name'];?> (<?=$disk['size']?>, <?=htmlspecialchars($disk['desc'])?>)";
					div.innerHTML += "<br />";
					document.iform.device_new[<?=$i;?>] = new Option('<?="{$disk['name']} ({$disk['size']}, {$disk['desc']})";?>','<?=$disk['name'];?>','false');
				}

				<?php
					$i++;
				} else if ($disk['type'] == "cache") {
				?>

				if (action == "cache add" || action == "cache remove") {
					div.innerHTML += "<input name='device[]' id='<?=$i?>' type='checkbox' value='<?=$disk['name'];?>'<?=$checked?> />";
					div.innerHTML += "<?=$disk['name'];?> (<?=$disk['type']?>, <?=$disk['size']?>, <?=htmlspecialchars($disk['desc'])?>)";
					div.innerHTML += "<br />";
					document.iform.device_new[<?=$j;?>] = new Option('<?="{$disk['name']} ({$disk['type']}, {$disk['size']}, {$disk['desc']})";?>','<?=$disk['name'];?>','false');
				}

				<?php
					$j++;
				}
			}

			$result_add = array();
			$result_del = array();
			array_sort_key($a_vdevice_cache, "name");
			foreach ($a_vdevice_cache as $vdevicev) {
				$index = array_search_ex($vdevicev['name'], $a_pool, "vdevice");
				if ($index !== false) {
					if ($a_pool[$index]['name'] == $pool['name']) {
						$result_del[] = $vdevicev;
					}
				} else {
					$result_add[] = $vdevicev;
				}
			}
			?>

			if (action == "cache add") {
			<?php $i = 0; foreach ($result_add as $vdevicev) {?>
				document.iform.device_cache[<?=$i;?>] = new Option('<?="{$vdevicev['name']} ({$vdevicev['devs']})";?>','<?="{$vdevicev['name']}";?>','false');
			<?php $i++; } ?>

			} else if (action == "cache remove") {

			<?php $i = 0; foreach ($result_del as $vdevicev) {?>
				document.iform.device_cache[<?=$i;?>] = new Option('<?="{$vdevicev['name']} ({$vdevicev['devs']})";?>','<?="{$vdevicev['name']}";?>','false');
			<?php $i++; } ?>

			}
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
		$a_cmd[] = "cache add";
		$a_cmd[] = "cache remove";
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
	<tr id='devices_tr'>
	<td valign="top" class="vncellreq"><?=gettext("Devices");?></td>
	<td class="vtable">
	<div id="devices">
	<?=gettext("No device selected.");?>
	</div>
	</td>
	</tr>
	<?php html_combobox("device_new", gettext("New Device"), NULL, NULL, "", true);?>
	<?php html_combobox("device_cache", gettext("Cache Device"), NULL, NULL, "", true);?>
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
		$new_device = $pconfig['device_new'];
		$cache_device = $pconfig['device_cache'];

		if (is_array($device)) {
			$a = array();
			foreach ($device as $dev) {
				$index = array_search_ex("/dev/{$dev}", $a_vdevice, "device");
				if ($index !== false) {
					$aft4k = $a_vdevice[$index]['aft4k'];
					if (isset($aft4k)) {
						$a[] = "{$dev}.nop";
					} else {
						$a[] = "{$dev}";
					}
				}
			}
			$device = $a;
		} else {
			$index = array_search_ex("/dev/{$device}", $a_vdevice, "device");
			if ($index !== false) {
				$aft4k = $a_vdevice[$index]['aft4k'];
				if (isset($aft4k)) {
					$device = "{$device}.nop";
				} else {
					$device = "{$device}";
				}
			}
		}

		$index = array_search_ex("/dev/{$new_device}", $a_vdevice, "device");
		if ($index !== false) {
			$aft4k = $a_vdevice[$index]['aft4k'];
			if (isset($aft4k)) {
				$new_device = "{$new_device}.nop";
			} else {
				$new_device = "{$new_device}";
			}
		}

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
			if (is_array($device)) {
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
			if (is_array($device)) {
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
			if (is_array($device)) {
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
			if (is_array($device)) {
			    foreach ($device as $dev) {
				zfs_zpool_cmd($action, "{$pool} {$dev}", true);
			    }
			} else if (!empty($device)) {
			    zfs_zpool_cmd($action, "{$pool} {$device}", true);
			}
			break;
		    }
		}
		break;

		case "replace": {
		    switch ($option) {
		    case "d":
			if (is_array($device)) {
			    foreach ($device as $dev) {
				zfs_zpool_cmd($action, "{$pool} {$dev} {$new_device}", true);
			    }
			} else {
			    zfs_zpool_cmd($action, "{$pool} {$device} {$new_device}", true);
			}
			break;
		    }
		}
		break;

		case "cache add": {
		    switch ($option) {
		    case "d":
			if ($cache_device == '')
				break;
			$index = array_search_ex($cache_device, $a_vdevice_cache, "name");
			if ($index === false)
				break;
			$vdevice = $a_vdevice_cache[$index];
			$device = $vdevice['device'];
			$result = 0;
			if (isset($vdevice['aft4k'])) {
				$a = array();
				foreach ($device as $dev) {
					$gnop_cmd = "gnop create -S 4096 {$dev}";
					write_log("$gnop_cmd");
					$result = mwexec($gnop_cmd, true);
					if ($result != 0)
						break;
					$a[] = "${dev}.nop";
				}
				$device = $a;
			}
			if ($result != 0)
				break;
			$devs = implode(" ", $device);
			$result = zfs_zpool_cmd("add", "{$pool} cache {$devs}", true);
			// Update config
			if ($result == 0) {
				$index = array_search_ex($pool, $config['zfs']['pools']['pool'], "name");
				if ($index !== false) {
					$config['zfs']['pools']['pool'][$index]['vdevice'][] = $cache_device;
					write_config();
					echo gettext("Done.")."\n";
				}
			}
			break;
		    }
		}
		break;

		case "cache remove": {
		    switch ($option) {
		    case "d":
			if ($cache_device == '')
				break;
			$index = array_search_ex($cache_device, $a_vdevice_cache, "name");
			if ($index === false)
				break;
			$vdevice = $a_vdevice_cache[$index];
			$device = $vdevice['device'];
			$result = 0;
			if (isset($vdevice['aft4k'])) {
				$a = array();
				foreach ($device as $dev) {
					$a[] = "${dev}.nop";
				}
				$device = $a;
			}
			$devs = implode(" ", $device);
			$result = zfs_zpool_cmd("remove", "{$pool} {$devs}", true);

			// Destroy gnop
			if ($result == 0) {
				$device = $vdevice['device'];
				$result = 0;
				if (isset($vdevice['aft4k'])) {
					foreach ($device as $dev) {
						$gnop_cmd = "gnop destroy {$dev}.nop";
						write_log("$gnop_cmd");
						$result = mwexec($gnop_cmd, true);
						if ($result != 0)
							break;
					}
				}
				if ($result != 0)
					break;
			}

			// Update config
			if ($result == 0) {
				$index = array_search_ex($pool, $config['zfs']['pools']['pool'], "name");
				if ($index !== false) {
					$a_vdevice = $config['zfs']['pools']['pool'][$index]['vdevice'];
					$new_vdevice = array();
					foreach ($a_vdevice as $vdevice) {
						if (strcmp($vdevice, $cache_device) != 0) {
							$new_vdevice[] = $vdevice;
						}
					}
					$config['zfs']['pools']['pool'][$index]['vdevice'] = $new_vdevice;
					write_config();
					echo gettext("Done.")."\n";
				}
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
