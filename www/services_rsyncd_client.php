#!/usr/local/bin/php
<?php
/*
	services_rsyncd_client.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
	Improved by Mat Murdock <mmurdock@kimballequipment.com>.
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

$pgtitle = array(_SERVICES,_SRVRYNCD_NAMEDESC);

/* Global arrays. */
$a_months = explode(" ",_MONTH_LONG);
$a_weekdays = explode(" ",_DAY_OF_WEEK_LONG);
$a_mount = array();

if (!is_array($config['rsyncclient'])){
	$config['rsyncclient'] = array();
}

if (!is_array($config['mounts']['mount'])) {
  $nodisk_errors[] = _SRVRYNCC_MSGMPFIRST;
} else {
  if ($_POST) {
  	unset($input_errors);

  	$pconfig = $_POST;

  	/* input validation */
  	$reqdfields = array();
  	$reqdfieldsn = array();

  	if ($_POST['enable']){
  		$reqdfields = array_merge($reqdfields, explode(" ", "rsyncserverip sharetosync"));
  		$reqdfieldsn = array_merge($reqdfieldsn, explode(",", _SRVRYNCC_REMOTESERVER . "," . _SRVRYNCC_SHARES));
  	}

  	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

  	if ($_POST['enable'] && !is_ipaddr($_POST['rsyncserverip'])){
  		$input_errors[] = _SRVRYNCC_MSGVALIDIP;
  	}

  	if (!$input_errors)
  	{
      $config['rsyncclient']['opt_delete'] = $_POST['opt_delete'] ? true : false;;
  		$config['rsyncclient']['rsyncserverip'] = $_POST['rsyncserverip'];
  		$config['rsyncclient']['minute'] = $_POST['minutes'];
  		$config['rsyncclient']['hour'] = $_POST['hours'];
  		$config['rsyncclient']['day'] = $_POST['days'];
  		$config['rsyncclient']['month'] = $_POST['months'];
  		$config['rsyncclient']['weekday'] = $_POST['weekdays'];
  		$config['rsyncclient']['rsyncshare'] = $_POST['rsyncshare'];
  		$config['rsyncclient']['enable'] = $_POST['enable'] ? true : false;
  		$config['rsyncclient']['sharetosync'] = $_POST['sharetosync'];
  		$config['rsyncclient']['all_mins'] = $_POST['all_mins'];
  		$config['rsyncclient']['all_hours'] = $_POST['all_hours'];
  		$config['rsyncclient']['all_days'] = $_POST['all_days'];
  		$config['rsyncclient']['all_months'] = $_POST['all_months'];
  		$config['rsyncclient']['all_weekdays'] = $_POST['all_weekdays'];

			write_config();

			$retval = 0;

  		if (!file_exists($d_sysrebootreqd_path)){
  			/* nuke the cache file */
  			config_lock();
  			services_rsyncclient_configure();
  			services_cron_configure();
  			config_unlock();
  		}

  		$savemsg = get_std_save_message($retval);
  	}
  }

 	mount_sort();
  $a_mount = &$config['mounts']['mount'];

	$pconfig['opt_delete'] = isset($config['rsyncclient']['opt_delete']);
	$pconfig['enable'] = isset($config['rsyncclient']['enable']);
	$pconfig['rsyncserverip'] = $config['rsyncclient']['rsyncserverip'];
	$pconfig['rsyncshare'] = $config['rsyncclient']['rsyncshare'];
	$pconfig['minute'] = $config['rsyncclient']['minute'];
	$pconfig['hour'] = $config['rsyncclient']['hour'];
	$pconfig['day'] = $config['rsyncclient']['day'];
	$pconfig['month'] = $config['rsyncclient']['month'];
	$pconfig['weekday'] = $config['rsyncclient']['weekday'];
	$pconfig['sharetosync'] = $config['rsyncclient']['sharetosync'];
	$pconfig['all_mins'] = $config['rsyncclient']['all_mins'];
	$pconfig['all_hours'] = $config['rsyncclient']['all_hours'];
	$pconfig['all_days'] = $config['rsyncclient']['all_days'];
	$pconfig['all_months'] = $config['rsyncclient']['all_months'];
	$pconfig['all_weekdays'] = $config['rsyncclient']['all_weekdays'];

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
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
<?php $i=0; foreach ($a_mount as $mount):?>
  document.iform.share_<?=$i++;?>.disabled = endis;
<?php endforeach;?>
	document.iform.rsyncserverip.disabled = endis;
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
	document.iform.opt_delete.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="services_rsyncd.php"><?=_SRVRYNC_SERVER ;?></a></li>
    <li class="tabact"><a href="services_rsyncd_client.php" style="color:black" title="reload page"><?=_SRVRYNC_CLIENT ;?></a></li>
    <li class="tabinact"><a href="services_rsyncd_local.php"><?=_SRVRYNC_LOCAL ;?></a></li>
  </ul>
  </td></tr>
  <tr>
    <td class="tabcont">
            <form action="services_rsyncd_client.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr>
                  <td colspan="2" valign="top" class="optsect_t">
				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr><td class="optsect_s"><strong><?=_SRVRYNCC_RSYNCC; ?></strong></td>
				  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=_ENABLE; ?></strong></td></tr>
				  </table></td>
                </tr>
                <tr>
                		<td width="22%" valign="top" class="vncell"><strong><?=_SRVRYNCC_REMOTESERVER;?><strong></td>
                		<td width="78%" class="vtable"> <input name="rsyncserverip" id="rsyncserverip" type="text" class="formfld" size="20" value="<?=htmlspecialchars($pconfig['rsyncserverip']);?>">
                      <br><?=_SRVRYNCC_REMOTEID;?><br>
										</td>
								</tr>
								<tr>
                <td width="22%" valign="top" class="vncell"><strong><?=_SRVRYNCC_OPTIONS; ?><strong></td>
                		<td width="78%" class="vtable"><input name="opt_delete" id="opt_delete" type="checkbox" value="yes" <?php if ($pconfig['opt_delete']) echo "checked"; ?>> <?=_SRVRYNCC_OPTDEL; ?><br>
										</td>
								</tr>
					 			<tr>
                     <td width="22%" valign="top" class="vncellreq"><?=_SRVRYNCC_SHARES;?></td>
                     <td width="78%" class="vtable">
<?php
if (is_array($config['mounts']['mount'])) {
  $i=0;
	foreach ($a_mount as $mountv) {
		echo "<input name=\"sharetosync[]\" id=\"share_" . $i++ . "\" type=\"checkbox\" value=\"" . $mountv['sharename'] . "\"";
		if (in_array($mountv['sharename'], $pconfig['sharetosync'])) {
	 		echo " checked";
	 	}
		echo">";
		echo $mountv['sharename'] . " (" . $mountv['desc'] . ")<br>\n";
	}
}
else
	echo _SRVRYNCC_MSGMPFIRST;
?>
		               <br><?=_SRVRYNCC_SHARESTEXT;?></td>
                      </tr>


                 <tr>
                  <td width="22%" valign="top" class="vncellreq"><?_SRVRYNCC_TIME;?></td>
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
                        <?=_ALL;?><br>
                        	<input type="radio" name="all_mins" id="all_mins2" value="0"<?php echo $all_mins_selected;?>>
                        <?=_SELECTED;?> ..<br>
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
                        <?=_ALL;?><br>
                        <input type="radio" name="all_hours" id="all_hours2" value="0"<?php echo $all_hours_selected;?>>
                        <?=_SELECTED;?> ..<br>
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
                        <?=_ALL;?><br>
                        <input type="radio" name="all_days" id="all_days2" value="0"<?php echo $all_days_selected;?>>
                        <?=_SELECTED;?> ..<br>
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
                        <?=_ALL;?><br>
                        <input type="radio" name="all_months" id="all_months2" value="0"<?php echo $all_months_selected;?>>
                        <?=_SELECTED;?> ..<br>
                        <table>
                          <tr>
                            <td valign=top>
    														<select multiple size="12" name="months[]" id="months">
																<?php $i=1; foreach ($a_months as $month):?>
                                <option value="<?=$i;?>" <?php if (isset($pconfig['month']) && in_array($i, $pconfig['month'])) echo "selected";?>><?=$month;?></option>
                                <?php $i++;?>
                                <?php endforeach;?>
                              </select>
													  </td>
                          </tr>
                        </table></td>
                      <td valign=top><input type="radio" name="all_weekdays" id="all_weekdays1" value="1"<?php echo $all_weekdays_all;?>>
                        <?=_ALL;?><br>
                        <input type="radio" name="all_weekdays" id="all_weekdays2" value="0"<?php echo $all_weekdays_selected;?>>
                        <?=_SELECTED;?> ..<br>
                        <table>
                          <tr>
                            <td valign=top>
    														<select multiple size="7" name="weekdays[]" id="weekdays">
    														<?php $i=0; foreach ($a_weekdays as $day):?>
                                <option value="<?=$i;?>" <?php if (isset($pconfig['weekday']) && in_array($i, $pconfig['weekday'])) echo "selected";?>><?=$day;?></option>
                                <?php $i++;?>
                                <?php endforeach;?>
                              </select>
													  </td>
                          </tr>
                        </table></td>
                    </tr>
                    <tr bgcolor=#cccccc>
                      <td colspan=5><?=_SRVRYNCC_TEXT;?></td>
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
