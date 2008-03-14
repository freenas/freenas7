<HTML>
	<HEAD>
		<TITLE>{$head_title}</TITLE>
		<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<LINK href="gui.css" rel="stylesheet" type="text/css">
		<SCRIPT type="text/javascript" src="gui.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="niftycube/niftycube.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="niftycube/niftylayout.js"></SCRIPT>
	</HEAD>
	<BODY>
		<TABLE width="750" border="0" cellspacing="0" cellpadding="2">
		  <TR valign="bottom">
		    <TD width="150" height="65" align="center" valign="middle">
		    	<STRONG><A href="{$header_link}" target="_blank"><IMG src="/logo.gif" width="150" height="47" border="0"></A></STRONG>
				</TD>
		    <TD height="65" class="pgheader">
					<TABLE border="0" cellspacing="0" cellpadding="0" width="100%">
						<TR>
							<TD align="left" valign="bottom">
								<SPAN class="pgheadertext">&nbsp;{$header_title|gettext}</SPAN>
							</TD>
					  	<TD align="right" valign="bottom">
								<SPAN class="hostname">{$header_hostname}&nbsp;</SPAN>
							</TD>
						</TR>
					</TABLE>
				</TD>
			</TR>
		  <TR valign="top">
		  	{include file="menu.tpl"}
		    <TD width="600">
					<TABLE width="100%" border="0" cellpadding="10" cellspacing="0">
		        <TR>
							<TD>
								{if !empty($title)}
								<P class="pgtitle">{$title}</P>
								{/if}