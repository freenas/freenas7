{include file="header.tpl"}
<script language="JavaScript" src="datetimepicker.js"></script>
<FORM action="system.php" method="post" name="iform" id="iform">
  <TABLE width="100%" border="0" cellpadding="6" cellspacing="0">
    <TR>
      <TD width="22%" valign="top" class="vncellreq">{gettext text="Hostname"}</TD>
      <TD width="78%" class="vtable">
        <INPUT name="hostname" type="text" class="formfld" id="hostname" size="40" value="{$hostname}"><BR>
        <SPAN class="vexpl">{gettext text="Name of the NAS host, without domain part<br>e.g. <em>nas</em>"}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncellreq">{gettext text="Domain"}</TD>
      <TD width="78%" class="vtable">
        <INPUT name="domain" type="text" class="formfld" id="domain" size="40" value="{$domain}"><BR>
        <SPAN class="vexpl">{gettext text="e.g. <em>mycorp.com</em>"}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="IPv4 DNS servers"}</TD>
      <TD width="78%" class="vtable">
				<?php $dns_ctrl_disabled = ("dhcp" == $config['interfaces']['lan']['ipaddr']) ? "disabled" : "";?>
				<INPUT name="dns1" type="text" class="formfld" id="dns1" size="20" value="<?=htmlspecialchars($pconfig['dns1']);?>" <?=$dns_ctrl_disabled;?>><BR>
				<INPUT name="dns2" type="text" class="formfld" id="dns2" size="20" value="<?=htmlspecialchars($pconfig['dns2']);?>" <?=$dns_ctrl_disabled;?>><BR>
				<SPAN class="vexpl">{gettext text="IPv4 addresses"}<BR>
      </TD>
    </TR>
	  <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="IPv6 DNS servers"}</TD>
      <TD width="78%" class="vtable">
				<?php $dns_ctrl_disabled = ("auto" == $config['interfaces']['lan']['ipv6addr']) ? "disabled" : "";?>
				<INPUT name="ipv6dns1" type="text" class="formfld" id="ipv6dns1" size="20" value="<?=htmlspecialchars($pconfig['ipv6dns1']);?>" <?=$dns_ctrl_disabled;?>><BR>
				<INPUT name="ipv6dns2" type="text" class="formfld" id="ipv6dns2" size="20" value="<?=htmlspecialchars($pconfig['ipv6dns2']);?>" <?=$dns_ctrl_disabled;?>><BR>
				<SPAN class="vexpl">{gettext text="IPv6 addresses"}<BR>
      </TD>
    </TR>
    <TR>
      <TD valign="top" class="vncell">{gettext text="Username"}</TD>
      <TD class="vtable">
        <INPUT name="username" type="text" class="formfld" id="username" size="20" value="<?=$pconfig['username'];?>"><BR>
        <SPAN class="vexpl">{gettext text="If you want to change the username for accessing the webGUI, enter it here."}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="Password"}</TD>
      <TD width="78%" class="vtable">
        <INPUT name="password" type="password" class="formfld" id="password" size="20"><BR>
        <INPUT name="password2" type="password" class="formfld" id="password2" size="20">&nbsp;({gettext text="Confirmation"})<BR>
        <SPAN class="vexpl">{gettext text="If you want to change the password for accessing the webGUI, enter it here twice.<br>Don't use the character ':'."}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="WebGUI protocol"}</TD>
      <TD width="78%" class="vtable">
        <INPUT name="webguiproto" type="radio" value="http" <?php IF ($pconfig['webguiproto'] == "http") ECHO "checked"; ?>>HTTP &nbsp;&nbsp;&nbsp;
        <INPUT type="radio" name="webguiproto" value="https" <?php IF ($pconfig['webguiproto'] == "https") ECHO "checked"; ?>>HTTPS
      </TD>
    </TR>
    <TR>
      <TD valign="top" class="vncell">{gettext text="WebGUI port"}</TD>
      <TD width="78%" class="vtable">
        <INPUT name="webguiport" type="text" class="formfld" id="webguiport" size="20" value="<?=htmlspecialchars($pconfig['webguiport']);?>"><BR>
        <SPAN class="vexpl">{gettext text="Enter a custom port number for the webGUI above if you want to override the default (80 for HTTP, 443 for HTTPS)."}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="Language"}</TD>
      <TD width="78%" class="vtable">
        <SELECT name="language" id="language">
    			<?php FOREACH ($g_languages AS $langk => $langv): ?>
    			<OPTION value="<?=$langk;?>" <?php IF ($langk === $pconfig['language']) ECHO "selected";?>><?=gettext($langv['desc']);?></OPTION>
	    		<?php ENDFOREACH; ?>
    		</SELECT>
      </TD>
    </TR>
		<TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="System Time"}</TD>
      <TD width="78%" class="vtable">
			  <INPUT name="systime" id="systime" type="text" size="20">
			  <A href="javascript:NewCal('systime','mmddyyyy',true,24)"><IMG src="cal.gif" width="16" height="16" border="0" align="top" alt="{gettext text="Pick a date"}"></A><BR>
        <SPAN class="vexpl">{gettext text="Enter desired system time directly (format mm/dd/yyyy hh:mm) or use icon to select one, then use Save button to update system time. (Mind seconds part will be ignored)"}</SPAN>
			</TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="Time zone"}</TD>
      <TD width="78%" class="vtable">
        <SELECT name="timezone" id="timezone">
          <?php FOREACH ($timezonelist AS $value): ?>
            <OPTION value="<?=htmlspecialchars($value);?>" <?php IF ($value == $pconfig['timezone']) ECHO "selected"; ?>>
            <?=htmlspecialchars($value);?>
            </OPTION>
          <?php ENDFOREACH; ?>
        </SELECT><BR>
        <SPAN class="vexpl">{gettext text="Select the location closest to you."}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="Time update interval"}</TD>
      <TD width="78%" class="vtable">
        <INPUT name="timeupdateinterval" type="text" class="formfld" id="timeupdateinterval" size="20" value="<?=htmlspecialchars($pconfig['timeupdateinterval']);?>"><BR>
        <SPAN class="vexpl">{gettext text="Minutes between network time sync.; 300	recommended, or 0 to disable."}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="NTP time server"}</TD>
      <TD width="78%" class="vtable">
        <INPUT name="timeservers" type="text" class="formfld" id="timeservers" size="40" value="<?=htmlspecialchars($pconfig['timeservers']);?>"><BR>
        <SPAN class="vexpl">{gettext text="Use a space to separate multiple hosts (only one required). Remember to set up at least one DNS server if you enter a host name here!"}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top">&nbsp;</TD>
      <TD width="78%">
        <INPUT name="Submit" type="submit" class="formbtn" value="{gettext text="Save"}">
      </TD>
    </TR>
  </TABLE>
</FORM>
{include file="footer.tpl"}
