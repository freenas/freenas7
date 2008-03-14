{include file="pre.tpl"}
<TABLE width="100%" border="0" cellspacing="0" cellpadding="0">
  <TR align="center" valign="top">
    <TD height="10" colspan="2">&nbsp;</TD>
  </TR>
  <TR align="center" valign="top">
    <TD height="170" colspan="2">
    	{html_image file="logobig.gif"}
		</TD>
  </TR>
  <TR>
    <TD colspan="2" class="listtopic">{gettext text="System information"}</TD>
  </TR>
  <TR>
    <TD width="25%" class="vncellt">{gettext text="Name"}</TD>
    <TD width="75%" class="listr">
			{$hostname}
		</TD>
  </TR>
  <TR>
    <TD width="25%" valign="top" class="vncellt">{gettext text="Version"}</TD>
    <TD width="75%" class="listr">
      <STRONG>{$version}</STRONG><BR>{gettext text="built on"} {$buildtime}
    </TD>
  </TR>
  <TR>
    <TD width="25%" valign="top" class="vncellt">{gettext text="OS Version"}</TD>
    <TD width="75%" class="listr">
      {$osversion}
    </TD>
  </TR>
  <TR>
    <TD width="25%" class="vncellt">{gettext text="Platform"}</TD>
    <TD width="75%" class="listr">
      {$platform}
    </TD>
  </TR>
  <TR>
    <TD width="25%" class="vncellt">{gettext text="Date"}</TD>
    <TD width="75%" class="listr">
      {$smarty.now|date_format}
    </TD>
  </TR>
  <TR>
    <TD width="25%" class="vncellt">{gettext text="Uptime"}</TD>
    <TD width="75%" class="listr">
      {$uptime}
    </TD>
  </TR>
  {if $lastchange}
  <TR>
    <TD width="25%" class="vncellt">{gettext text="Last config change"}</TD>
    <TD width="75%" class="listr">
    	{$lastchange}
    </TD>
  </TR>
  {/if}
  <TR>
    <TD width="25%" class="vncellt">{gettext text="Memory usage"}</TD>
    <TD width="75%" class="listr">
    	{html_progressbar percentage=$memusage.percentage caption=$memusage.caption}
    </TD>
  </TR>
  <TR>
  	<TD width="25%" class="vncellt">{gettext text="Load averages"}</TD>
  	<TD width="75%" class="listr">
			{$loadaverages}
    </TD>
  </TR>
  <TR>
    <TD width="25%" class="vncellt">{gettext text="Disk space usage"}</TD>
    <TD width="75%" class="listr">
	    <TABLE>
	    	{if count($diskusage) > 0}
		    	{foreach item=disk from=$diskusage}
	    		<tr>
						<td>{$disk.name}</td>
						<td>{html_progressbar percentage=$disk.percentage caption=$disk.caption}<br></td>
					</tr>
		    	{/foreach}
		    {else}
		    	{gettext text="No disk configured"}
		    {/if}
			</TABLE>
		</TD>
	</TR>  
</TABLE>
{include file="post.tpl"}
