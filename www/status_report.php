#!/usr/local/bin/php
<?php
/*
	status_report.php
	Copyright (C) 2007-2009 Volker Theile (votdev@gmx.de)
	Copyright (C) 2007 Dan Merschi (freenas@bcapro.com)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");
require("email.inc");
require("report.inc");

$pgtitle = array(gettext("Status"), gettext("Email Report"));

if (!is_array($config['statusreport']))
	$config['statusreport'] = array();

$pconfig['enable'] = isset($config['statusreport']['enable']);
$pconfig['to'] = $config['statusreport']['to'];
$pconfig['subject'] = $config['statusreport']['subject'];
$pconfig['report'] = $config['statusreport']['report'];
$pconfig['minute'] = $config['statusreport']['minute'];
$pconfig['hour'] = $config['statusreport']['hour'];
$pconfig['day'] = $config['statusreport']['day'];
$pconfig['month'] = $config['statusreport']['month'];
$pconfig['weekday'] = $config['statusreport']['weekday'];
$pconfig['all_mins'] = $config['statusreport']['all_mins'];
$pconfig['all_hours'] = $config['statusreport']['all_hours'];
$pconfig['all_days'] = $config['statusreport']['all_days'];
$pconfig['all_months'] = $config['statusreport']['all_months'];
$pconfig['all_weekdays'] = $config['statusreport']['all_weekdays'];

$a_months = explode(" ",gettext("January February March April May June July August September October November December"));
$a_weekdays = explode(" ",gettext("Sunday Monday Tuesday Wednesday Thursday Friday Saturday"));

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if($_POST['enable']) {
		$reqdfields = explode(" ", "to");
		$reqdfieldsn = array(gettext("To e-mail"));
		$reqdfieldst = explode(" ", "string");

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

		if (gettext("Send now") !== $_POST['Submit']) {
			// Validate synchronization time
			do_input_validate_synctime($_POST, &$input_errors);
		}
	}

	if (!$input_errors) {
		$config['statusreport']['enable'] = $_POST['enable'] ? true : false;
		$config['statusreport']['to'] = $_POST['to'];
		$config['statusreport']['subject'] = $_POST['subject'];
		$config['statusreport']['report'] = $_POST['report'];
		$config['statusreport']['minute'] = $_POST['minute'];
		$config['statusreport']['hour'] = $_POST['hour'];
		$config['statusreport']['day'] = $_POST['day'];
		$config['statusreport']['month'] = $_POST['month'];
		$config['statusreport']['weekday'] = $_POST['weekday'];
		$config['statusreport']['all_mins'] = $_POST['all_mins'];
		$config['statusreport']['all_hours'] = $_POST['all_hours'];
		$config['statusreport']['all_days'] = $_POST['all_days'];
		$config['statusreport']['all_months'] = $_POST['all_months'];
		$config['statusreport']['all_weekdays'] = $_POST['all_weekdays'];

		write_config();

		if (stristr($_POST['Submit'], gettext("Send now"))) {
			// Send an email status report now.
			$retval = @report_send_mail();
			if (0 == $retval)
				$savemsg = gettext("Status report successfully sent.");
			else
				$failmsg = sprintf(gettext("Failed to send status report. Please check the <a href='%s'>log</a> files."), "diag_log.php");
		} else {
			// Configure cron job.
			if (!file_exists($d_sysrebootreqd_path)) {
				config_lock();
				$retval = rc_update_service("cron");
				config_unlock();
			}

			$savemsg = get_std_save_message($retval);
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
	document.iform.to.disabled = endis;
	document.iform.subject.disabled = endis;
	document.iform.report_systeminfo.disabled = endis;
	document.iform.report_dmesg.disabled = endis;
	document.iform.report_systemlog.disabled = endis;
	document.iform.report_ftplog.disabled = endis;
	document.iform.report_rsynclog.disabled = endis;
	document.iform.report_sshdlog.disabled = endis;
	document.iform.report_smartdlog.disabled = endis;
	document.iform.report_daemonlog.disabled = endis;
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
	document.iform.sendnow.disabled = endis;
}
//-->
</script>
<form action="status_report.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
	    	<?php if (0 !== email_validate_settings()) print_error_box(sprintf(gettext("Make sure you have already configured your <a href='%s'>Email</a> settings."), "system_email.php"));?>
    		<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
				<?php if ($failmsg) print_error_box($failmsg);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("enable", gettext("Email Report"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("To email");?></td>
						<td width="78%" class="vtable">
							<input name="to" type="text" class="formfld" id="to" size="40" value="<?=htmlspecialchars($pconfig['to']);?>"><br>
							<span class="vexpl"><?=gettext("Destination email address.");?> <?=gettext("Separate email addresses by semi-colon.");?></span>
						</td>
					</tr>
					<?php html_inputbox("subject", gettext("Subject"), $pconfig['subject'], gettext("The subject of the email.") . " " . gettext("You can use the following parameters for substitution:<div id='enumeration'><ul><li>%d - Date</li><li>%h - Hostname</li></ul></div>"), false, 60);?>
					<tr>
				    <td width="22%" valign="top" class="vncell"><?=gettext("Reports");?></td>
			      <td width="78%" class="vtable">
			      	<table>
								<tr><td><input name="report[]" type="checkbox" class="formfld" id="report_systeminfo" value="systeminfo" <?php if (is_array($pconfig['report']) && in_array("systeminfo", $pconfig['report'])):?>checked<?php endif;?>><?=gettext("System info");?></td></tr>
								<tr><td><input name="report[]" type="checkbox" class="formfld" id="report_dmesg" value="dmesg" <?php if (is_array($pconfig['report']) && in_array("dmesg", $pconfig['report'])):?>checked<?php endif;?>><?=gettext("System message buffer");?></td></tr>
								<tr><td><input name="report[]" type="checkbox" class="formfld" id="report_systemlog" value="systemlog" <?php if (is_array($pconfig['report']) && in_array("systemlog", $pconfig['report'])):?>checked<?php endif;?>><?=gettext("System log");?></td></tr>
								<tr><td><input name="report[]" type="checkbox" class="formfld" id="report_ftplog" value="ftplog" <?php if (is_array($pconfig['report']) && in_array("ftplog", $pconfig['report'])):?>checked<?php endif;?>><?=gettext("FTP log");?></td></tr>
								<tr><td><input name="report[]" type="checkbox" class="formfld" id="report_rsynclog" value="rsynclog" <?php if (is_array($pconfig['report']) && in_array("rsynclog", $pconfig['report'])):?>checked<?php endif;?>><?=gettext("RSYNC log");?></td></tr>
								<tr><td><input name="report[]" type="checkbox" class="formfld" id="report_sshdlog" value="sshdlog" <?php if (is_array($pconfig['report']) && in_array("sshdlog", $pconfig['report'])):?>checked<?php endif;?>><?=gettext("SSHD log");?></td></tr>
								<tr><td><input name="report[]" type="checkbox" class="formfld" id="report_smartdlog" value="smartdlog" <?php if (is_array($pconfig['report']) && in_array("smartdlog", $pconfig['report'])):?>checked<?php endif;?>><?=gettext("S.M.A.R.T. log");?></td></tr>
								<tr><td><input name="report[]" type="checkbox" class="formfld" id="report_daemonlog" value="daemonlog" <?php if (is_array($pconfig['report']) && in_array("daemonlog", $pconfig['report'])):?>checked<?php endif;?>><?=gettext("Daemon log");?></td></tr>
			        </table>
			      </td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Polling time");?></td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="5" cellspacing="0">
								<tr>
									<td class="listhdrlr"><?=gettext("minutes");?></td>
									<td class="listhdrr"><?=gettext("hours");?></td>
									<td class="listhdrr"><?=gettext("days");?></td>
									<td class="listhdrr"><?=gettext("months");?></td>
									<td class="listhdrr"><?=gettext("week days");?></td>
								</tr>
								<tr>
									<td class="listlr">
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
									<td class="listr" valign=top>
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
									<td class="listr" valign=top>
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
									<td class="listr" valign=top>
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
									<td class="listr" valign=top>
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
							</table>
							<span class="vexpl"><?=gettext("Note: Ctrl-click (or command-click on the Mac) to select and de-select minutes, hours, days and months.");?></span>
						</td>
					</tr>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
					<input name="Submit" id="sendnow" type="submit" class="formbtn" value="<?=gettext("Send now");?>">
				</div>
			</td>
		</tr>
	</table>
	<?php include("formend.inc");?>
</form>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc");?>
