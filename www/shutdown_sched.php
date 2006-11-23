#!/usr/local/bin/php
<?php
/*
	shutdown_sched.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.
	Cron configuration from services_rsynclient.php improved by Mat Murdock <mmurdock@kimballequipment.com>

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

$pgtitle = array(_DIAG_NAME,_SHUTSCHED_NAME);

if (!is_array($config['shutdown'])){
	$config['shutdown'] = array();
}

if ($_POST){
	unset($input_errors);

	$pconfig = $_POST;

	/* input validation */
	// Nothing to check!!!

	if (!$input_errors) {
		$config['shutdown']['minute'] = $_POST['minutes'];
		$config['shutdown']['hour'] = $_POST['hours'];
		$config['shutdown']['day'] = $_POST['days'];
		$config['shutdown']['month'] = $_POST['months'];
		$config['shutdown']['weekday'] = $_POST['weekdays'];
		$config['shutdown']['enable'] = $_POST['enable'] ? true : false;
		$config['shutdown']['all_mins'] = $_POST['all_mins'];
		$config['shutdown']['all_hours'] = $_POST['all_hours'];
		$config['shutdown']['all_days'] = $_POST['all_days'];
		$config['shutdown']['all_months'] = $_POST['all_months'];
		$config['shutdown']['all_weekdays'] = $_POST['all_weekdays'];

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			/* nuke the cache file */
			config_lock();
			services_cron_configure();
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);
	}
}

$pconfig['enable'] = isset($config['shutdown']['enable']);
$pconfig['minute'] = $config['shutdown']['minute'];
$pconfig['hour'] = $config['shutdown']['hour'];
$pconfig['day'] = $config['shutdown']['day'];
$pconfig['month'] = $config['shutdown']['month'];
$pconfig['weekday'] = $config['shutdown']['weekday'];
$pconfig['all_mins'] = $config['shutdown']['all_mins'];
$pconfig['all_hours'] = $config['shutdown']['all_hours'];
$pconfig['all_days'] = $config['shutdown']['all_days'];
$pconfig['all_months'] = $config['shutdown']['all_months'];
$pconfig['all_weekdays'] = $config['shutdown']['all_weekdays'];

$a_months = explode(" ",_MONTH_LONG);
$a_weekdays = explode(" ",_DAY_OF_WEEK_LONG);

if ($pconfig['all_mins'] == 1){
   $all_mins_all = " checked";
} else {
   $all_mins_selected = " checked";
}

if ($pconfig['all_hours'] == 1){
  $all_hours_all = " checked";
} else {
  $all_hours_selected = " checked";
}

if ($pconfig['all_days'] == 1){
  $all_days_all = " checked";
} else {
   $all_days_selected = " checked";
}

if ($pconfig['all_months'] == 1){
   $all_months_all = " checked";
} else {
   $all_months_selected = " checked";
}

if ($pconfig['all_weekdays'] == 1){
   $all_weekdays_all = " checked";
} else {
   $all_weekdays_selected = " checked";
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
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
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabinact"><a href="shutdown.php"><?=_SHUT_NAME ;?></a></li>
        <li class="tabact"><a href="shutdown_sched.php" style="color:black" title="reload page"><?=_SHUTSCHED_NAME ;?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
      <form action="shutdown_sched.php" method="post" name="iform" id="iform">
        <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <tr>
            <td colspan="2" valign="top" class="optsect_t">
              <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                  <td class="optsect_s"><strong><?=_SHUTSCHED_NAME; ?></strong></td>
                  <td align="right" class="optsect_s">
                    <input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=_ENABLE; ?></strong>
                  </td>
                </tr>
				      </table>
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncellreq"><?=_SHUTSCHED_TIME;?></td>
            <td width="78%" class="vtable">
              <table width=100% border cellpadding="6" cellspacing="0">
                <tr>
                  <td class="optsect_t"><b class="optsect_s"><?=_MINUTES;?></b></td>
                  <td class="optsect_t"><b class="optsect_s"><?=_HOURS;?></b></td>
                  <td class="optsect_t"><b class="optsect_s"><?=_DAYS;?></b></td>
                  <td class="optsect_t"><b class="optsect_s"><?=_MONTHS;?></b></td>
                  <td class="optsect_t"><b class="optsect_s"><?=_WEEKDAYS;?></b></td>
                </tr>
                <tr bgcolor=#cccccc>
                  <td valign=top>
        						<input type="radio" name="all_mins" id="all_mins1" value="1"<?php echo $all_mins_all;?>>
                        All<br>
                        	<input type="radio" name="all_mins" id="all_mins2" value="0"<?php echo $all_mins_selected;?>>
                        Selected ..<br>
                        <table>
                          <tr>
                            <td valign=top>
							<select multiple size="12" name="minutes[]" id="minutes1">
							<?php
																$i = 0;
																	 while ($i <= 11){

																	 	if (isset($pconfig['minute'])){
    																	  if (in_array($i, $pconfig['minute'])){
                                    	 		$is_selected = " selected";
    																		} else {
    																			$is_selected = "";
    																		}
																		}

																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
																				 $i++;
																		}
																?>
                            		 </select>
														</td>
                            <td valign=top>
																<select multiple size="12" name="minutes[]" id="minutes2">
                            <?php
																$i = 12;
																	 while ($i <= 23){

																	 	if (isset($pconfig['minute'])){
  																	  if (in_array($i, $pconfig['minute'])){
                                  	 		$is_selected = " selected";
  																		} else {
  																			$is_selected = "";
  																		}
																		}

																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
																				 $i++;
																		}
																?>
                                </select>
														</td>
                            <td valign=top>
																<select multiple size="12" name="minutes[]" id="minutes3">
                               <<?php
																$i = 24;
																	 while ($i <= 35){

																		if (isset($pconfig['minute'])){
  																	  if (in_array($i, $pconfig['minute'])){
                                  	 		$is_selected = " selected";
  																		} else {
  																			$is_selected = "";
  																		}
																		}

																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
																				 $i++;
																		}
																?>
                                  </select></td>
                            <td valign=top>
																<select multiple size="12" name="minutes[]" id="minutes4">
                               <?php
																$i = 36;
																	 while ($i <= 47){

																	  if (isset($pconfig['minute'])){
  																		if (in_array($i, $pconfig['minute'])){
                                  	 		$is_selected = " selected";
  																		} else {
  																			$is_selected = "";
  																		}
																		}
																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
																				 $i++;
																		}
																?>
                                </select>
														</td>
                            <td valign=top>
																<select multiple size="12" name="minutes[]" id="minutes5">
                               <?php
																$i = 48;
																	 while ($i <= 59){

																	 	if (isset($pconfig['minute'])){
  																		if (in_array($i, $pconfig['minute'])){
                                  	 		$is_selected = " selected";
  																		} else {
  																			$is_selected = "";
  																		}
																		}

																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
																				 $i++;
																		}
																?>
                                </select>
													</td>
                          </tr>
                        </table>
                        <br></td>
                      <td valign=top>
											<input type="radio" name="all_hours" id="all_hours1" value="1"<?php echo $all_hours_all;?>>
                        All<br>
                        <input type="radio" name="all_hours" id="all_hours2" value="0"<?php echo $all_hours_selected;?>>
                        Selected ..<br>
                        <table>
                          <tr>
                            <td valign=top>
  														<select multiple size="12" name="hours[]" id="hours1">
                               <?php
																$i = 0;
																	 while ($i <= 11){

																	  if (isset($pconfig['hour'])){
  																	  if (in_array($i, $pconfig['hour'])){
                                  	 		$is_selected = " selected";
  																		} else {
  																			$is_selected = "";
  																		}
																		}
																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
																				 $i++;
																		}
																?>
                              </select>
														</td>
                            <td valign=top>
    														<select multiple size="12" name="hours[]" id="hours2">
                               <?php
																$i = 12;
																	 while ($i <= 23){

																	  if (isset($pconfig['hour'])){
  																	  if (in_array($i, $pconfig['hour'])){
                                  	 		$is_selected = " selected";
  																		} else {
  																			$is_selected = "";
  																		}
																		}
																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
																				 $i++;
																		}
																?>
                              </select></td>
                          </tr>
                        </table></td>
                      <td valign=top><input type="radio" name="all_days" id="all_days1" value="1" <?php echo $all_days_all;?>>
                        All<br>
                        <input type="radio" name="all_days" id="all_days2" value="0"<?php echo $all_days_selected;?>>
                        Selected ..<br>
                        <table>
                          <tr>
                            <td valign=top>
    														<select multiple size="12" name="days[]" id="days1">
                                 <?php
  																$i = 1;
  																	 while ($i <= 12){

																		  if (isset($pconfig['day'])){
    																	  if (in_array($i, $pconfig['day'])){
                                    	 		$is_selected = " selected";
    																		} else {
    																			$is_selected = "";
    																		}
  																		}
  																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
  																				 $i++;
  																		}
  																?>
                                </select></td>
                            <td valign=top>
    														<select multiple size="12" name="days[]" id="days2">
                                  <?php
  																$i = 13;
  																	 while ($i <= 24){

																		  if (isset($pconfig['day'])){
    																	  if (in_array($i, $pconfig['day'])){
                                    	 		$is_selected = " selected";
    																		} else {
    																			$is_selected = "";
    																		}
  																		}
  																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
  																				 $i++;
  																		}
  																?>
                                </select>
														</td>
                            <td valign=top>
  														<select multiple size="7" name="days[]" id="days3">
                                  <?php
  																$i = 25;
  																	 while ($i <= 31){

																		  if (isset($pconfig['day'])){
    																	  if (in_array($i, $pconfig['day'])){
                                    	 		$is_selected = " selected";
    																		} else {
    																			$is_selected = "";
    																		}
  																		}
  																	 			 echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $i . "\n";
  																				 $i++;
  																		}
  																?>
                           		</select></td>
                          </tr>
                        </table></td>
                      <td valign=top><input type="radio" name="all_months" id="all_months1" value="1"<?php echo $all_months_all;?>>
                        All<br>
                        <input type="radio" name="all_months" id="all_months2" value="0"<?php echo $all_months_selected;?>>
                        Selected ..<br>
                        <table>
                          <tr>
                            <td valign=top>
    														<select multiple size="12" name="months[]" id="months">

																<?php
																		 $i=1;

																		 foreach ($a_months as $monthv){

																				if (isset($pconfig['month'])){
  																		 		if (in_array($i, $pconfig['month'])){
                                    	 			 $is_selected = " selected";
      																		} else {
      																			$is_selected = "";
      																		}
																				}
																		 		echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $monthv . "\n";
                                  			$i++;
																			}
																?>
                              </select>
													  </td>
                          </tr>
                        </table></td>
                      <td valign=top><input type="radio" name="all_weekdays" id="all_weekdays1" value="1"<?php echo $all_weekdays_all;?>>
                        All<br>
                        <input type="radio" name="all_weekdays" id="all_weekdays2" value="0"<?php echo $all_weekdays_selected;?>>
                        Selected ..<br>
                        <table>
                          <tr>
                            <td valign=top>
    														<select multiple size="7" name="weekdays[]" id="weekdays">
                                  <?php
																		 $i=0;

																		 foreach ($a_weekdays as $weekdayv){

																		 if (isset($pconfig['weekday'])){
																		 		if (in_array($i, $pconfig['weekday'])){
                                  	 			 $is_selected = " selected";
    																		} else {
    																			$is_selected = "";
    																		}
																			}
																		 		echo "<option value=\"" . $i . "\"" . $is_selected . ">" . $weekdayv . "\n";
                                  			$i++;
																			}
																?>
                              </select>
													  </td>
                          </tr>
                        </table></td>
                    </tr>
                    <tr bgcolor=#cccccc>
                      <td colspan=5><?=_SHUTSCHED_TEXT;?></td>
                    </tr>
                  </table>

										 </td>
                  </td>
				</tr>
				<tr>
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%">
                    <input name="Submit" type="submit" class="formbtn" value="<?=_SAVE;?>" onClick="enable_change(true)">
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

<?php include("fend.inc"); ?>
