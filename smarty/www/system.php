<?php
$pgtitle = array("System", "General setup");

require_once("guicore.inc");
require_once("util.inc");

$smarty->assign("hostname", $pconfig['hostname']);
$smarty->assign("domain", $pconfig['domain']);






?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<script language="JavaScript" src="datetimepicker.js"></script>
<form action="system.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Hostname");?></td>
      <td width="78%" class="vtable"><?=$mandfldhtml;?>
        <?=$mandfldhtml;?><input name="hostname" type="text" class="formfld" id="hostname" size="40" value="<?=htmlspecialchars($pconfig['hostname']);?>"><br>
        <span class="vexpl"><?=gettext("Name of the NAS host, without domain part<br>e.g. <em>nas</em>");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Domain");?></td>
      <td width="78%" class="vtable"><?=$mandfldhtml;?>
        <?=$mandfldhtml;?><input name="domain" type="text" class="formfld" id="domain" size="40" value="<?=htmlspecialchars($pconfig['domain']);?>"><br>
        <span class="vexpl"><?=gettext("e.g. <em>mycorp.com</em>");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("IPv4 DNS servers");?></td>
      <td width="78%" class="vtable">
				<?php $dns_ctrl_disabled = ("dhcp" == $config['interfaces']['lan']['ipaddr']) ? "disabled" : "";?>
				<input name="dns1" type="text" class="formfld" id="dns1" size="20" value="<?=htmlspecialchars($pconfig['dns1']);?>" <?=$dns_ctrl_disabled;?>><br>
				<input name="dns2" type="text" class="formfld" id="dns2" size="20" value="<?=htmlspecialchars($pconfig['dns2']);?>" <?=$dns_ctrl_disabled;?>><br>
				<span class="vexpl"><?=gettext("IPv4 addresses");?><br>
      </td>
    </tr>
	  <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("IPv6 DNS servers");?></td>
      <td width="78%" class="vtable">
				<?php $dns_ctrl_disabled = ("auto" == $config['interfaces']['lan']['ipv6addr']) ? "disabled" : "";?>
				<input name="ipv6dns1" type="text" class="formfld" id="ipv6dns1" size="20" value="<?=htmlspecialchars($pconfig['ipv6dns1']);?>" <?=$dns_ctrl_disabled;?>><br>
				<input name="ipv6dns2" type="text" class="formfld" id="ipv6dns2" size="20" value="<?=htmlspecialchars($pconfig['ipv6dns2']);?>" <?=$dns_ctrl_disabled;?>><br>
				<span class="vexpl"><?=gettext("IPv6 addresses");?><br>
      </td>
    </tr>
    <tr>
      <td valign="top" class="vncell"><?=gettext("Username");?></td>
      <td class="vtable">
        <input name="username" type="text" class="formfld" id="username" size="20" value="<?=$pconfig['username'];?>"><br>
        <span class="vexpl"><?=gettext("If you want to change the username for accessing the webGUI, enter it here.");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Password");?></td>
      <td width="78%" class="vtable">
        <input name="password" type="password" class="formfld" id="password" size="20"><br>
        <input name="password2" type="password" class="formfld" id="password2" size="20">&nbsp;(<?=gettext("Confirmation");?>)<br>
        <span class="vexpl"><?=gettext("If you want to change the password for accessing the webGUI, enter it here twice.<br>Don't use the character ':'.");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("WebGUI protocol");?></td>
      <td width="78%" class="vtable">
        <input name="webguiproto" type="radio" value="http" <?php if ($pconfig['webguiproto'] == "http") echo "checked"; ?>>HTTP &nbsp;&nbsp;&nbsp;
        <input type="radio" name="webguiproto" value="https" <?php if ($pconfig['webguiproto'] == "https") echo "checked"; ?>>HTTPS
      </td>
    </tr>
    <tr>
      <td valign="top" class="vncell"><?=gettext("WebGUI port");?></td>
      <td width="78%" class="vtable">
        <input name="webguiport" type="text" class="formfld" id="webguiport" size="20" value="<?=htmlspecialchars($pconfig['webguiport']);?>"><br>
        <span class="vexpl"><?=gettext("Enter a custom port number for the webGUI above if you want to override the default (80 for HTTP, 443 for HTTPS).");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Language");?></td>
      <td width="78%" class="vtable">
        <select name="language" id="language">
    			<?php foreach ($g_languages as $langk => $langv): ?>
    			<option value="<?=$langk;?>" <?php if ($langk === $pconfig['language']) echo "selected";?>><?=gettext($langv['desc']);?></option>
	    		<?php endforeach; ?>
    		</select>
      </td>
    </tr>
		<tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("System Time");?></td>
      <td width="78%" class="vtable">
			  <input name="systime" id="systime" type="text" size="20">
			  <a href="javascript:NewCal('systime','mmddyyyy',true,24)"><img src="cal.gif" width="16" height="16" border="0" align="top" alt="<?=gettext("Pick a date");?>"></a><br>
        <span class="vexpl"><?=gettext("Enter desired system time directly (format mm/dd/yyyy hh:mm) or use icon to select one, then use Save button to update system time. (Mind seconds part will be ignored)");?></span>
			</td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Time zone");?></td>
      <td width="78%" class="vtable">
        <select name="timezone" id="timezone">
          <?php foreach ($timezonelist as $value): ?>
            <option value="<?=htmlspecialchars($value);?>" <?php if ($value == $pconfig['timezone']) echo "selected"; ?>>
            <?=htmlspecialchars($value);?>
            </option>
          <?php endforeach; ?>
        </select><br>
        <span class="vexpl"><?=gettext("Select the location closest to you.");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Time update interval");?></td>
      <td width="78%" class="vtable">
        <input name="timeupdateinterval" type="text" class="formfld" id="timeupdateinterval" size="20" value="<?=htmlspecialchars($pconfig['timeupdateinterval']);?>"><br>
        <span class="vexpl"><?=gettext("Minutes between network time sync.; 300	recommended, or 0 to disable.");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("NTP time server");?></td>
      <td width="78%" class="vtable">
        <input name="timeservers" type="text" class="formfld" id="timeservers" size="40" value="<?=htmlspecialchars($pconfig['timeservers']);?>"><br>
        <span class="vexpl"><?=gettext("Use a space to separate multiple hosts (only one required). Remember to set up at least one DNS server if you enter a host name here!");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>">
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
