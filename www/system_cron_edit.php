#!/usr/local/bin/php
<?php
/*
	system_cron_edit.php
	Copyright (C) 2007-2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("System"),gettext("Advanced"),gettext("Cron"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['cron']['job']))
	$config['cron']['job'] = array();

$a_cronjob = &$config['cron']['job'];

if (isset($id) && $a_cronjob[$id]) {
	$pconfig['enable'] = isset($a_cronjob[$id]['enable']);
	$pconfig['desc'] = $a_cronjob[$id]['desc'];
	$pconfig['minute'] = $a_cronjob[$id]['minute'];
	$pconfig['hour'] = $a_cronjob[$id]['hour'];
	$pconfig['day'] = $a_cronjob[$id]['day'];
	$pconfig['month'] = $a_cronjob[$id]['month'];
	$pconfig['weekday'] = $a_cronjob[$id]['weekday'];
	$pconfig['all_mins'] = $a_cronjob[$id]['all_mins'];
	$pconfig['all_hours'] = $a_cronjob[$id]['all_hours'];
	$pconfig['all_days'] = $a_cronjob[$id]['all_days'];
	$pconfig['all_months'] = $a_cronjob[$id]['all_months'];
	$pconfig['all_weekdays'] = $a_cronjob[$id]['all_weekdays'];
	$pconfig['who'] = $a_cronjob[$id]['who'];
	$pconfig['command'] = $a_cronjob[$id]['command'];
} else {
	$pconfig['enable'] = true;
	$pconfig['desc'] = "";
	$pconfig['all_mins'] = 1;
	$pconfig['all_hours'] = 1;
	$pconfig['all_days'] = 1;
	$pconfig['all_months'] = 1;
	$pconfig['all_weekdays'] = 1;
	$pconfig['who'] = "root";
	$pconfig['command'] = "";
}

$a_months = explode(" ",gettext("January February March April May June July August September October November December"));
$a_weekdays = explode(" ",gettext("Sunday Monday Tuesday Wednesday Thursday Friday Saturday"));

if ($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	/* Input validation */
	$reqdfields = explode(" ", "desc who command");
	$reqdfieldsn = array(gettext("Description"),gettext("Who"),gettext("Command"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (gettext("Run now") !== $_POST['Submit']) {
		// Validate synchronization time
		do_input_validate_synctime($_POST, &$input_errors);
	}

	if (!$input_errors) {
		$cronjob = array();
		$cronjob['enable'] = $_POST['enable'] ? true : false;
		$cronjob['desc'] = $_POST['desc'];
		$cronjob['minute'] = $_POST['minute'];
		$cronjob['hour'] = $_POST['hour'];
		$cronjob['day'] = $_POST['day'];
		$cronjob['month'] = $_POST['month'];
		$cronjob['weekday'] = $_POST['weekday'];
		$cronjob['all_mins'] = $_POST['all_mins'];
		$cronjob['all_hours'] = $_POST['all_hours'];
		$cronjob['all_days'] = $_POST['all_days'];
		$cronjob['all_months'] = $_POST['all_months'];
		$cronjob['all_weekdays'] = $_POST['all_weekdays'];
		$cronjob['who'] = $_POST['who'];
		$cronjob['command'] = $_POST['command'];

		if (stristr($_POST['Submit'], gettext("Run now"))) {
			mwexec2(escapeshellcmd($_POST['command']), $output, $retval);
			if (0 == $retval) {
				$execmsg = gettext("The cron job has been executed successfully.");
				write_log("The cron job '{$_POST['command']}' has been executed successfully.");
			} else {
				$execfailmsg = gettext("Failed to execute cron job.");
				write_log("Failed to execute cron job '{$_POST['command']}'.");
			}
		} else {
			if (isset($id) && $a_cronjob[$id])
				$a_cronjob[$id] = $cronjob;
			else
				$a_cronjob[] = $cronjob;

			write_config();
			touch($d_cronconfdirty_path);

			header("Location: system_cron.php");
			exit;
		}
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function set_selected(name) {
	document.getElementsByName(name)[1].checked = true;
}

function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.desc.disabled = endis;
	document.iform.minutes1.disabled = endis;
	document.iform.minutes2.disabled = endis;
	document.iform.minutes3.disabled = endis;
	document.iform.minutes4.disabled = endis;
	document.iform.minutes5.disabled = endis;
	document.iform.hours1.disabled = endis;
	document.iform.hours2.disabled = endis;
	document.iform.days1.disabled = endis;
	document.iform.days2.disabled = endis;
	document.iform.days3.disabled = endis;
	document.iform.months.disabled = endis;
	document.iform.weekdays.disabled = endis;
	document.iform.all_mins1.disabled = endis;
	document.iform.all_mins2.disabled = endis;
	document.iform.all_hours1.disabled = endis;
	document.iform.all_hours2.disabled = endis;
	document.iform.all_days1.disabled = endis;
	document.iform.all_days2.disabled = endis;
	document.iform.all_months1.disabled = endis;
	document.iform.all_months2.disabled = endis;
	document.iform.all_weekdays1.disabled = endis;
	document.iform.all_weekdays2.disabled = endis;
	document.iform.who.disabled = endis;
	document.iform.command.disabled = endis;
	document.iform.runnow.disabled = endis;
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="system_advanced.php"><span><?=gettext("Advanced");?></span></a></li>
      	<li class="tabinact"><a href="system_email.php"><span><?=gettext("Email");?></span></a></li>
      	<li class="tabinact"><a href="system_proxy.php"><span><?=gettext("Proxy");?></span></a></li>
      	<li class="tabinact"><a href="system_swap.php"><span><?=gettext("Swap");?></span></a></li>
      	<li class="tabinact"><a href="system_rc.php"><span><?=gettext("Command scripts");?></span></a></li>
        <li class="tabact"><a href="system_cron.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Cron");?></span></a></li>
        <li class="tabinact"><a href="system_rcconf.php"><span><?=gettext("rc.conf");?></span></a></li>
        <li class="tabinact"><a href="system_sysctl.php"><span><?=gettext("sysctl.conf");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="system_cron_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($execmsg) print_info_box($execmsg);?>
				<?php if ($execfailmsg) print_error_box($execfailmsg);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <?php html_titleline_checkbox("enable", gettext("Cron job"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<?php html_inputbox("command", gettext("Command"), $pconfig['command'], gettext("Specifies the command to be run."), true, 60);?>
					<?php $a_user = array(); foreach (system_get_user_list() as $userk => $userv) { $a_user[$userk] = htmlspecialchars($userk); }?>
					<?php html_combobox("who", gettext("Who"), $pconfig['who'], $a_user, "", true);?>
					<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), true, 40);?>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Schedule time");?></td>
						<td width="78%" class="vtable">
							<table width=100% border cellpadding="6" cellspacing="0">
								<tr>
									<td class="optsect_t"><b class="optsect_s"><?=gettext("minutes");?></b></td>
									<td class="optsect_t"><b class="optsect_s"><?=gettext("hours");?></b></td>
									<td class="optsect_t"><b class="optsect_s"><?=gettext("days");?></b></td>
									<td class="optsect_t"><b class="optsect_s"><?=gettext("months");?></b></td>
									<td class="optsect_t"><b class="optsect_s"><?=gettext("week days");?></b></td>
								</tr>
								<tr bgcolor=#cccccc>
									<td valign=top>
										<input type="radio" name="all_mins" id="all_mins1" value="1" <?php if (1 == $pconfig['all_mins']) echo "checked";?>>
										<?=gettext("All");?><br>
										<input type="radio" name="all_mins" id="all_mins2" value="0" <?php if (1 != $pconfig['all_mins']) echo "checked";?>>
										<?=gettext("Selected");?> ..<br>
										<table>
											<tr>
												<td valign=top>
													<select multiple size="12" name="minute[]" id="minutes1" onchange="set_selected('all_mins')">
														<?php for ($i = 0; $i <= 11; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['minute']) && in_array("$i", $pconfig['minute'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
												<td valign=top>
													<select multiple size="12" name="minute[]" id="minutes2" onchange="set_selected('all_mins')">
														<?php for ($i = 12; $i <= 23; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['minute']) && in_array("$i", $pconfig['minute'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
												<td valign=top>
													<select multiple size="12" name="minute[]" id="minutes3" onchange="set_selected('all_mins')">
														<?php for ($i = 24; $i <= 35; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['minute']) && in_array("$i", $pconfig['minute'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
												<td valign=top>
													<select multiple size="12" name="minute[]" id="minutes4" onchange="set_selected('all_mins')">
														<?php for ($i = 36; $i <= 47; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['minute']) && in_array("$i", $pconfig['minute'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
												<td valign=top>
													<select multiple size="12" name="minute[]" id="minutes5" onchange="set_selected('all_mins')">
														<?php for ($i = 48; $i <= 59; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['minute']) && in_array("$i", $pconfig['minute'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
											</tr>
										</table>
										<br>
									</td>
									<td valign=top>
										<input type="radio" name="all_hours" id="all_hours1" value="1" <?php if (1 == $pconfig['all_hours']) echo "checked";?>>
										<?=gettext("All");?><br>
										<input type="radio" name="all_hours" id="all_hours2" value="0" <?php if (1 != $pconfig['all_hours']) echo "checked";?>>
										<?=gettext("Selected");?> ..<br>
										<table>
											<tr>
												<td valign=top>
													<select multiple size="12" name="hour[]" id="hours1" onchange="set_selected('all_hours')">
														<?php for ($i = 0; $i <= 11; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['hour']) && in_array("$i", $pconfig['hour'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
												<td valign=top>
													<select multiple size="12" name="hour[]" id="hours2" onchange="set_selected('all_hours')">
														<?php for ($i = 12; $i <= 23; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['hour']) && in_array("$i", $pconfig['hour'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
											</tr>
										</table>
									</td>
									<td valign=top>
										<input type="radio" name="all_days" id="all_days1" value="1" <?php if (1 == $pconfig['all_days']) echo "checked";?>>
										<?=gettext("All");?><br>
										<input type="radio" name="all_days" id="all_days2" value="0" <?php if (1 != $pconfig['all_days']) echo "checked";?>>
										<?=gettext("Selected");?> ..<br>
										<table>
											<tr>
												<td valign=top>
													<select multiple size="12" name="day[]" id="days1" onchange="set_selected('all_days')">
														<?php for ($i = 1; $i <= 12; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['day']) && in_array("$i", $pconfig['day'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
												<td valign=top>
													<select multiple size="12" name="day[]" id="days2" onchange="set_selected('all_days')">
														<?php for ($i = 13; $i <= 24; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['day']) && in_array("$i", $pconfig['day'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
												<td valign=top>
													<select multiple size="7" name="day[]" id="days3" onchange="set_selected('all_days')">
														<?php for ($i = 25; $i <= 31; $i++):?>
														<option value="<?=$i;?>" <?php if (is_array($pconfig['day']) && in_array("$i", $pconfig['day'])) echo "selected";?>><?=htmlspecialchars($i);?></option>
														<?php endfor;?>
													</select>
												</td>
											</tr>
										</table>
									</td>
									<td valign=top>
										<input type="radio" name="all_months" id="all_months1" value="1" <?php if (1 == $pconfig['all_months']) echo "checked";?>>
										<?=gettext("All");?><br>
										<input type="radio" name="all_months" id="all_months2" value="0" <?php if (1 != $pconfig['all_months']) echo "checked";?>>
										<?=gettext("Selected");?> ..<br>
										<table>
											<tr>
												<td valign=top>
													<select multiple size="12" name="month[]" id="months" onchange="set_selected('all_months')">
														<?php $i = 1; foreach ($a_months as $month):?>
														<option value="<?=$i;?>" <?php if (isset($pconfig['month']) && in_array("$i", $pconfig['month'])) echo "selected";?>><?=htmlspecialchars($month);?></option>
														<?php $i++; endforeach;?>
													</select>
												</td>
											</tr>
										</table>
									</td>
									<td valign=top>
										<input type="radio" name="all_weekdays" id="all_weekdays1" value="1" <?php if (1 == $pconfig['all_weekdays']) echo "checked";?>>
										<?=gettext("All");?><br>
										<input type="radio" name="all_weekdays" id="all_weekdays2" value="0" <?php if (1 != $pconfig['all_weekdays']) echo "checked";?>>
										<?=gettext("Selected");?> ..<br>
										<table>
											<tr>
												<td valign=top>
													<select multiple size="7" name="weekday[]" id="weekdays" onchange="set_selected('all_weekdays')">
														<?php $i = 0; foreach ($a_weekdays as $day):?>
														<option value="<?=$i;?>" <?php if (isset($pconfig['weekday']) && in_array("$i", $pconfig['weekday'])) echo "selected";?>><?=$day;?></option>
														<?php $i++; endforeach;?>
													</select>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr bgcolor=#cccccc>
									<td colspan=5>
										<?=gettext("Note: Ctrl-click (or command-click on the Mac) to select and de-select minutes, hours, days and months.");?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%">
							<input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_cronjob[$id]))?gettext("Save"):gettext("Add")?>" onClick="enable_change(true)">
							<input name="Submit" id="runnow" type="submit" class="formbtn" value="<?=gettext("Run now");?>">
							<?php if (isset($id) && $a_cronjob[$id]): ?>
							<input name="id" type="hidden" value="<?=$id;?>">
							<?php endif; ?>
						</td>
					</tr>
			  </table>
			</form>
    </td>
  </tr>
</table>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc");?>
