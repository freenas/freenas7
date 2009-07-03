#!/usr/local/bin/php
<?php
/*
	FreeNAS Report Generator Copyright 2009 Dan Merschi
	Part of code from:
				Exec+ v1.02-000 - Copyright 2001-2003, All rights reserved
				Created by technologEase (http://www.technologEase.com).
				modified for m0n0wall by Manuel Kasper <mk@neon1.net>)
				re-modified for FreeNAS by Olivier Cochard-Labbe <olivier@freenas.org>)
				adapted to FreeNAS GUI by Volker Theile <votdev@gmx.de>)
*/
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("Report generator"));
?>
<?php include("fbegin.inc");?>
<?php
// Function: is Blank. Returns true or false depending on blankness of argument.
	function isBlank( $arg ) { return ereg( "^\s*$", $arg ); }

// Put string, Ruby-style.
	function puts( $arg ) { echo "$arg\n"; }
?>
<script language="javascript">
<!--
// Function: Reset onClick (event handler)  . Resets form on reset button click event.
	function Reset_onClick( form )
	{
		form.txtSubject.value = '';
		form.txtError.value = '';
		form.txtDescription.value = '';
		form.txtDescription.focus();
		return true;
	}
//-->
</script>
<style type="text/css">
<!--
	pre
	{
		border: 2px solid #435370;
		background: #F0F0F0;
		padding: 1em;
		font-family: courier new, courier;
		white-space: pre;
		line-height: 10pt;
		font-size: 10pt;
	}
-->
</style>
<?php // phpBB variables
	$nl				= "\n";	//Set new line
	$hr				= "";	//Set horizontal line
	$bs				= ""; 	//Set bold start
	$be				= "";	//Set bold end
	$cs				= "Error or code:";	//Set code end
	$ce				= "";	//Set code end
?>
<?php
	$fn_version		= file_get_contents('/etc/prd.version');					//Get version
	$fn_revision	= file_get_contents('/etc/prd.revision');					//Get revision
	$fn_platform	= file_get_contents('/etc/platform');						//Get platform
	$sys_cpu 		= exec('/sbin/sysctl -n hw.model');							//Get CPU info
	$sys_mb			= exec('/sbin/sysctl -n dev.acpi.0.%desc');					//Get Motherbord info
	$sys_ram		= floor(exec('/sbin/sysctl -n hw.realmem')/1048576+1);		//Get ram info
	$sys_summary	= str_replace("\n","","FreeNAS $fn_version rev.$fn_revision $fn_platform ; $sys_mb $sys_cpu $sys_ram MB Ram.");
	//Get hardware controllers info
	$hw_info		= exec("/sbin/sysctl -a | /usr/bin/awk -F:\  '/controller|interface/ &&! /AT|VGA|Key|inet|floppy/{!u[$2]++}END{for(i in u) a=a OFS i;print a}'");
?>
<form action="<?=$HTTP_SERVER_VARS['SCRIPT_NAME'];?>" method="POST" enctype="multipart/form-data" name="frmReportGenerator" onSubmit="return frmReportGenerator( this );">
  <table>
	<tr>
		<td class="label" align="right"><?=gettext("Info:");?></td>
		<td class="text" align="left"><?=gettext("$sys_summary");?></td>
	</tr>
	<tr>
		<td class="label" align="right"><?=gettext("Subject:");?></td>
		<td class="text"><input id="txtSubject" name="txtSubject" type="text" size="123"  value="<?php echo $_POST['txtSubject']; ?>"'></input></td>
	</tr>
	<tr>
		<td align="label"><?=gettext("Description");?></td>
		<td class="text"><textarea id="txtDescription" name="txtDescription" type="text" rows="6" cols="77" wrap="on"><?=htmlspecialchars($_POST['txtDescription']);?></textarea></td>
	</tr>
	<tr>
		<td align="label"><?=gettext("Error's");?></td>
		<td class="text"><textarea id="txtError" name="txtError" type="text" rows="2" cols="77" wrap="on"><?=htmlspecialchars($_POST['txtError']);?></textarea></td>
	</tr>
	<tr>
		<td class="label" align="right"><?=gettext("Hardware:");?></td>
		<td class="type" valign="top"><input name="chk_Hardware" type="checkbox" id="chk_Hardware" checked="checked"><?=gettext("Include basic hardware information.");?></td>
	</tr>
	<tr>
		<td class="label" align="right"><?=gettext("phpBB:");?></td>
		<td class="type" valign="top"><input name="chk_phpBB" type="checkbox" id="chk_phpBB" checked="checked"><?=gettext("Format the report for phpBB forum.");?></td>
	</tr>
	<tr>
		<td valign="top">&nbsp;&nbsp;&nbsp;</td>
		<td valign="top" align="center" class="label">
			<input type="submit" class="button" value="<?=gettext("Generate");?>">
			<input type="button" class="button" value="<?=gettext("Clear");?>" onClick="return Reset_onClick( this.form )">
		</td>
	</tr>
  </table>
	<?php
	if (!isBlank($_POST['txtSubject']) && !isBlank($_POST['txtDescription']))
		{
			puts("<pre>");
			require_once("functions.inc");
			if(isset($_POST['chk_phpBB']))	//Format report for phpBB
				{
					$hr	= "[hr]1[/hr]";		//Set horizontal line
					$bs	= "[b]"; 			//Set bold start
					$be	= "[/b]";			//Set bold end
					$cs	= "[code]";			//Set code end
					$ce	= "[/code]";		//Set code end
				}
			print $sys_summary.$nl.$nl;
			if(isset($_POST['chk_Hardware']))
				{
				print $hw_info;
				}
			print $hr.$nl.$nl.$bs."Subject:".$be.$nl.$_POST['txtSubject'].$hr;
			print $nl.$nl.$bs."Description:".$be.$nl.$_POST['txtDescription'];
			if (!isBlank($_POST['txtError']))
				{
				print $nl.$nl.$hr.$cs.$nl.$_POST['txtError'].$nl.$ce;
				}
			puts("</pre>");
		}
	?>
	<?php include("formend.inc");?>
</form>
<script language="JavaScript">
<!--
document.forms[0].txtDescription.focus();
//-->
</script>
<?php include("fend.inc");?>
