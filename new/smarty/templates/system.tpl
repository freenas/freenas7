{include file="header.tpl"}
<FORM action="system.php" method="post" name="iform" id="iform">
  <TABLE width="100%" border="0" cellpadding="6" cellspacing="0">
    <TR>
      <TD width="22%" valign="top" class="vncellreq">{gettext text="Hostname"}</TD>
      <TD width="78%" class="vtable">
        <INPUT name="hostname" type="text" class="formfld" id="hostname" size="40" value="{$pagedata_hostname}"><BR>
        <SPAN class="vexpl">{gettext text="Name of the NAS host, without domain part<br>e.g. <em>nas</em>"}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncellreq">{gettext text="Domain"}</TD>
      <TD width="78%" class="vtable">
        <INPUT name="domain" type="text" class="formfld" id="domain" size="40" value="{$pagedata_domain}"><BR>
        <SPAN class="vexpl">{gettext text="e.g. <em>mycorp.com</em>"}</SPAN>
      </TD>
    </TR>
    <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="IPv4 DNS servers"}</TD>
      <TD width="78%" class="vtable">
				<INPUT name="dns1" type="text" class="formfld" id="dns1" size="20" value="{$pagedata_dns1}" {if !$dns_enabled}disabled{/if}><BR>
				<INPUT name="dns2" type="text" class="formfld" id="dns2" size="20" value="{$pagedata_dns2}" {if !$dns_enabled}disabled{/if}><BR>
				<SPAN class="vexpl">{gettext text="IPv4 addresses"}<BR>
      </TD>
    </TR>
	  <TR>
      <TD width="22%" valign="top" class="vncell">{gettext text="IPv6 DNS servers"}</TD>
      <TD width="78%" class="vtable">
				<INPUT name="ipv6dns1" type="text" class="formfld" id="ipv6dns1" size="20" value="{$pagedata_ipv6dns1}" {if !$ipv6dns_enabled}disabled{/if}><BR>
				<INPUT name="ipv6dns2" type="text" class="formfld" id="ipv6dns2" size="20" value="{$pagedata_ipv6dns2}" {if !$ipv6dns_enabled}disabled{/if}><BR>
				<SPAN class="vexpl">{gettext text="IPv6 addresses"}<BR>
      </TD>
    </TR>
    <TR>
      <TD valign="top" class="vncell">{gettext text="Username"}</TD>
      <TD class="vtable">
        <INPUT name="username" type="text" class="formfld" id="username" size="20" value="{$pagedata_username}"><BR>
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
        <INPUT name="webguiproto" type="radio" value="http" {if "http" === $pagedata_webguiproto}checked{/if}>HTTP &nbsp;&nbsp;&nbsp;
        <INPUT type="radio" name="webguiproto" value="https" {if "https" === $pagedata_webguiproto}checked{/if}>HTTPS
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
