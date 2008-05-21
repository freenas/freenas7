#!/usr/local/bin/php
<?php
/*
	disks_raid_gmirror_tools.php
	
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
	JavaScript code are from Volker Theile
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("ZPool"), gettext("Tools"));

$a_pool = get_all_conf_pools();

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
			
if ($_POST || $_GET) {
	unset($input_errors);
	unset($do_action);

	/* input validation */
// 	$reqdfields = explode(" ", "action raid disk");
// 	$reqdfieldsn = array(gettext("Command"),gettext("Volume Name"),gettext("Disk"));
// 	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (!$input_errors) {
		$do_action = true;
	}
}

if (!isset($do_action)) {
	$do_action = false;
	$pconfig['action'] = '';
	$pconfig['option'] = '';
	$pconfig['pool'] = '';
	$pconfig['device'] = '';
}

?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
function command_change() {
	document.iform.option.length = 0;
	var action = document.iform.action.value;
	switch(action) {
		case "upgrade":
			document.iform.option[0] = new Option('Display','v', <?=$pconfig['option'] == 'v' ? "true": "false"?>);
			document.iform.option[1] = new Option('All','a', <?=$pconfig['option'] == 'a' ? "true": "false"?>);
			<?if(is_array($a_pool) && !empty($a_pool)) {?>
			document.iform.option[2] = new Option('Pool','p', <?=$pconfig['option'] == 'p' ? "true": "false"?>);
			<?}?>
		break;
		case "history":
			document.iform.option[0] = new Option('All','a', <?=$pconfig['option'] == 'a' ? "true": "false"?>);
		<?if(is_array($a_pool) && !empty($a_pool)) {?>
			document.iform.option[1] = new Option('Pool','p', <?=$pconfig['option'] == 'p' ? "true": "false"?>);
		<?}?>
		break;
		case "scrub":
			document.iform.option[0] = new Option('Start','s', <?=$pconfig['option'] == 's' ? "true": "false"?>);
			document.iform.option[1] = new Option('Stop','st', <?=$pconfig['option'] == 'st' ? "true": "false"?>);
		break;
		case "clear":
		<?if(is_array($a_pool) && !empty($a_pool)) {?>
			document.iform.option[0] = new Option('Pool','p', <?=$pconfig['option'] == 'p' ? "true": "false"?>);
			document.iform.option[1] = new Option('Device','d', <?=$pconfig['option'] == 'd' ? "true": "false"?>);
		<?}?>
		break;
		case "offline":
		<?if(is_array($a_pool) && !empty($a_pool)) {?>
			document.iform.option[0] = new Option('Device','d', <?=$pconfig['option'] == 'd' ? "true": "false"?>);
			document.iform.option[1] = new Option('Temporary Device','t', <?=$pconfig['option'] == 't' ? "true": "false"?>);
		<?}?>
		break;
		case "online":
		<?if(is_array($a_pool) && !empty($a_pool)) {?>
			document.iform.option[0] = new Option('Device','d', <?=$pconfig['option'] == 'd' ? "true": "false"?>);
		<?}?>
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
	if(option == "s" || option == "st" || option == "p" || option == "d" || option == "t") {
<?if(is_array($a_pool) && !empty($a_pool)) { ?>
	document.iform.pool.disabled = 0;
	<?$i = 0; foreach($a_pool  as $pool) {?>	
	document.iform.pool[<?=$i?>] = new Option('<?=$pool['name']?>','<?=$pool['name']?>', <?=$pconfig['pool'] == $pool['name'] ? "true": "false"?>);
	<? 
	$i++;
	}
}?>
	}
	if(option == "d" || option == "t") {
		pool_change();
	}
}
function pool_change() {
	var div = document.getElementById("devices");
	div.innerHTML ="";
	var pool = document.iform.pool.value;
	switch(pool)
	{
<?
foreach($a_pool  as $pool)	 {
?>
		case "<?=$pool['name']?>":
		{
<?
	$result = array();
	foreach($pool['groups']['group'] as $gn) {
		$group = get_conf_group($gn);
		foreach($group['devices']['device'] as $dev) {
			$result[] = get_conf_disk($dev);
		}
	}
	$i=0;
	array_sort_key($result, "name");
	foreach($result as $disk) {
		$checked = "";
		if(is_array($pconfig['device'])) {
			foreach($pconfig['device'] as $dev) {
				if($dev == $disk['name']) {
					$checked = " checked";
					break;
				}
			}
		} else {
			if($pconfig['device'] == $disk['name']) {
				$checked = " checked";
			}
		}
	?>
		div.innerHTML += "<input name='device[]' id='<?=$i?>' type='checkbox' value='<?=$disk['name'];?>'<?=$checked?>>";
		div.innerHTML += "<?=$disk['name'];?> (<?=$disk['size']?>, <?=$disk['desc']?>)";
		div.innerHTML += "</br>";
	<?
		$i++;
	}
?>
		}
		break;
<?
}
?>
	}
}
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
			<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>" ><?=gettext("ZPool");?></a></li>
			<li class="tabinact"><a href="disks_zfs.php"><?=gettext("ZFS"); ?></a></li>
		</ul>
	</td>
</tr>
<tr>
	<td class="tabcont">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="tabnavtbl">
				<ul id="tabnav">
					<li class="tabinact"><a href="disks_zfs_zpool_groups.php"><?=gettext("Groups");?></a></li>
					<li class="tabinact"><a href="disks_zfs_zpool.php"><?=gettext("Manage Pool");?></a></li>
					<li class="tabact"><a href="disks_zfs_zpool_tools.php" title="<?=gettext("Reload page");?>"><?=gettext("Tools"); ?></a></li>
					<li class="tabinact"><a href="disks_zfs_zpool_info.php"><?=gettext("Information"); ?></a></li>
					<li class="tabinact"><a href="ddisks_zfs_zpool_io.php"><?=gettext("IO Status"); ?></a></li>
				</ul>
			</td>
		</tr>
		<tr>
			<td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				<form action="disks_zfs_zpool_tools.php" method="post" name="iform" id="iform">
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<tr> 
					<td valign="top" class="vncellreq"><?=gettext("Command");?></td>
					<td class="vtable"> 
						<select name="action" class="formfld" id="action" onchange="command_change()">
						<?
							$arr = "upgrade history";
							if(is_array($a_pool) && !empty($a_pool)) {
								$arr .= " clear scrub offline online";
							}
							$actions = explode(" ", $arr);
							asort($actions);
							foreach($actions as $action) {
								echo "<option value=\"${action}\"";
								if($action == $pconfig['action'])
									echo " selected";
								echo ">${action}</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top" class="vncellreq"><?=gettext("Option");?></td>
					<td class="vtable">
					<select name="option" class="formfld" id="option"  onchange="option_change()"></select>
					</td>
				</tr>
				<tr>
					<td valign="top" class="vncellreq"><?=gettext("Pool");?></td>
					<td class="vtable">
					<select name="pool" class="formfld" id="pool" disabled onchange="pool_change()"></select>
					</td>
				</tr>
				<tr>
					<td valign="top" class="vncellreq"><?=gettext("Devices");?></td>
					<td class="vtable">
						<div id="devices">
							<?=gettext("No device selected.");?>
						</div>
					</td>
				</tr>
				<tr>
					<td width="22%" valign="top">&nbsp;</td>
					<td width="78%"> 
						<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Send Command!");?>">
					</td>
				</tr>
				<tr>
					<td valign="top" colspan="2">
<?php if ($do_action) {
echo("<strong>" . gettext("Command output:") . "</strong><br>");
echo('<pre>');
ob_end_flush();
$action = $pconfig['action'];
$option = $pconfig['option'];
$pool = $pconfig['pool'];
$device = $pconfig['device'];
switch($action) {
	case "upgrade":
	{
		switch($option) {
			case "v":
			{
				disks_zpool_cmd($action, "-v", false, false, true, &$output);
				//print_r($output);
				foreach($output as $line) {
					if(preg_match("/(\s+)(\d+)(\s+)(.*)/",$line, $match )) {
						//print_r($match);
						$href = "<a href=\"http://www.opensolaris.org/os/community/zfs/version/${match[2]}\" target=\"_blank\">${match[2]}</a>";
						echo $match[1].$href.$match[3].$match[4];
					} else {
						echo htmlspecialchars($line);
					}
					echo "<br/>";
				}
			}
			break;
			case "a":
				disks_zpool_cmd($action, "-a",true);
			break;
			case "p":
			{
				disks_zpool_cmd($action, $pool,true);
			}
			break;
		}
	}
	break;
	case "history":
	{
		switch($option) {
			case "a":
				disks_zpool_cmd($action, "",true);
				break;
			case "p":
				disks_zpool_cmd($action, $pool,true);
				break;
		}
	}
	break;
	case "scrub":
	{
		switch($option) {
			case "s":
				disks_zpool_cmd($action, $pool,true);
 			break;
 			case "st":
				disks_zpool_cmd($action,"-s ${pool}",true);
			break;
		}
	}
	break;
	case "clear":
	{
		switch($option) {
			case "p":
				disks_zpool_cmd($action, $pool,true);
			break;
			case "d":
				if(is_array($device) ) {
					foreach($device as $dev) {
						disks_zpool_cmd($action, "${pool} ${dev}",true);
					}
				} else {
					disks_zpool_cmd($action, "${pool} ${device}",true);
				}
			break;
		}
	}
	break;
	case "offline":
	{
		switch($option) {
			case "t":
				disks_zpool_cmd($action, "-t ${pool} ${device}",true);
			break;
			case "d":
				if(is_array($device) ) {
					foreach($device as $dev) {
						disks_zpool_cmd($action, "${pool} ${dev}",true);
					}
				} else {
					disks_zpool_cmd($action, "${pool} ${device}",true);
				}
			break;
		}
	}
	break;
	case "online":
	{
		switch($option) {
			case "d":
				if(is_array($device) ) {
					foreach($device as $dev) {
						disks_zpool_cmd($action, "${pool} ${dev}",true);
					}
				} else {
					disks_zpool_cmd($action, "${pool} ${device}",true);
				}
			break;
		}
	}
	break;
}
echo('</pre>');
};?>
					</td>
				</tr>
				</table>
				</form>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<script language="JavaScript">
command_change();
</script>
<?php include("fend.inc"); ?>
