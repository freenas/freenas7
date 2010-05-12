#!/usr/local/bin/php
<?php
/*
	Exec+ v1.02-000 - Copyright 2001-2003, All rights reserved
	Created by technologEase (http://www.technologEase.com).

	(modified for m0n0wall by Manuel Kasper <mk@neon1.net>)
	(re-modified for FreeNAS by Olivier Cochard-Labbe <olivier@freenas.org>)
	(adapted to FreeNAS GUI by Volker Theile <votdev@gmx.de>)
	(modified for XHTML by Daisuke Aoyama <aoyama@peach.ne.jp>)
*/
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("Advanced"), gettext("Execute command"));

if (($_POST['submit'] == "Download") && file_exists($_POST['dlPath'])) {
	session_cache_limiter('public');
	$fd = fopen($_POST['dlPath'], "rb");
	header("Content-Type: application/octet-stream");
	header("Content-Length: " . get_filesize($_POST['dlPath']));
	header("Content-Disposition: attachment; filename=\"" . trim(htmlentities(basename($_POST['dlPath']))) . "\"");

	fpassthru($fd);
	exit;
} else if (($_POST['submit'] == "Upload") && is_uploaded_file($_FILES['ulfile']['tmp_name'])) {
	move_uploaded_file($_FILES['ulfile']['tmp_name'], "/tmp/" . $_FILES['ulfile']['name']);
	$ulmsg = "Uploaded file to /tmp/{$_FILES['ulfile']['name']}";
	unset($_POST['txtCommand']);
}

$pglocalheader = <<< EOD
<style type="text/css">
<!--
pre {
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
EOD;
?>
<?php include("fbegin.inc");?>
<?php
// Function: is Blank
// Returns true or false depending on blankness of argument.
function isBlank( $arg ) { return ereg( "^\s*$", $arg ); }

// Function: Puts
// Put string, Ruby-style.
function puts( $arg ) { echo "$arg\n"; }
?>
<script type="text/javascript">
<!--
   // Create recall buffer array (of encoded strings).
<?php
if (isBlank( $_POST['txtRecallBuffer'] )) {
   puts( "   var arrRecallBuffer = new Array;" );
} else {
   puts( "   var arrRecallBuffer = new Array(" );
   $arrBuffer = explode( "&", $_POST['txtRecallBuffer'] );
   for ($i=0; $i < (count( $arrBuffer ) - 1); $i++) puts( "      '" . $arrBuffer[$i] . "'," );
   puts( "      '" . $arrBuffer[count( $arrBuffer ) - 1] . "'" );
   puts( "   );" );
}
?>
   // Set pointer to end of recall buffer.
   var intRecallPtr = arrRecallBuffer.length;

   // Functions to extend String class.
   function str_encode() { return escape( this ) }
   function str_decode() { return unescape( this ) }

   // Extend string class to include encode() and decode() functions.
   String.prototype.encode = str_encode
   String.prototype.decode = str_decode

   // Function: is Blank
   // Returns boolean true or false if argument is blank.
   function isBlank( strArg ) { return strArg.match( /^\s*$/ ) }

   // Function: frmExecPlus onSubmit (event handler)
   // Builds the recall buffer from the command string on submit.
   function frmExecPlus_onSubmit( form ) {
      if (!isBlank(form.txtCommand.value)) {
		  // If this command is repeat of last command, then do not store command.
		  if (form.txtCommand.value.encode() == arrRecallBuffer[arrRecallBuffer.length-1]) { return true }

		  // Stuff encoded command string into the recall buffer.
		  if (isBlank(form.txtRecallBuffer.value))
			 form.txtRecallBuffer.value = form.txtCommand.value.encode();
		  else
			 form.txtRecallBuffer.value += '&' + form.txtCommand.value.encode();
	  }

    return true;
   }

   // Function: btnRecall onClick (event handler)
   // Recalls command buffer going either up or down.
   function btnRecall_onClick( form, n ) {
      // If nothing in recall buffer, then error.
      if (!arrRecallBuffer.length) {
         alert( 'Nothing to recall!' );
         form.txtCommand.focus();
         return;
      }

      // Increment recall buffer pointer in positive or negative direction
      // according to <n>.
      intRecallPtr += n;

      // Make sure the buffer stays circular.
      if (intRecallPtr < 0) { intRecallPtr = arrRecallBuffer.length - 1 }
      if (intRecallPtr > (arrRecallBuffer.length - 1)) { intRecallPtr = 0 }

      // Recall the command.
      form.txtCommand.value = arrRecallBuffer[intRecallPtr].decode();
   }

   // Function: Reset onClick (event handler)
   // Resets form on reset button click event.
   function Reset_onClick( form ) {
      // Reset recall buffer pointer.
      intRecallPtr = arrRecallBuffer.length;

      // Clear form (could have spaces in it) and return focus ready for cmd.
      form.txtCommand.value = '';
      form.txtCommand.focus();

      return true;
   }

   // hansmi, 2005-01-13
   function txtCommand_onKey(e) {
       if(!e) var e = window.event; // IE-Fix
       var code = (e.keyCode?e.keyCode:(e.which?e.which:0));
       if(!code) return;
       var f = document.getElementsByName('frmExecPlus')[0];
       if(!f) return;
       switch(code) {
       case 38: // up
           btnRecall_onClick(f, -1);
           break;
       case 40: // down
           btnRecall_onClick(f, 1);
           break;
       }
   }
//-->
</script>
<?php if (isBlank($_POST['txtCommand'])): ?>
<p class="red"><strong><?=gettext("Note");?>: <?=gettext("This function is unsupported. Use it on your own risk!");?></strong></p>
<?php endif; ?>
<?php if ($ulmsg) echo "<p><strong>" . $ulmsg . "</strong></p>\n"; ?>
<?php
if (!isBlank($_POST['txtCommand'])) {
	puts("<pre>");
	puts("\$ " . htmlspecialchars($_POST['txtCommand']));
	putenv("PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin");
	putenv("SCRIPT_FILENAME=" . strtok($_POST['txtCommand'], " "));	/* PHP scripts */
	$ph = popen($_POST['txtCommand'], "r" );
	while ($line = fgets($ph)) echo htmlspecialchars($line);
	pclose($ph);
	puts("</pre>");
}

if (!isBlank($_POST['txtPHPCommand'])) {
	puts("<pre>");
	require_once("config.inc");
	require_once("functions.inc");
	require_once("util.inc");
	require_once("rc.inc");
	require_once("email.inc");
	require_once("tui.inc");
	require_once("array.inc");
	require_once("services.inc");
	require_once("zfs.inc");
	echo eval($_POST['txtPHPCommand']);
	puts("</pre>");
}
?>
<form action="<?=$HTTP_SERVER_VARS['SCRIPT_NAME'];?>" method="post" enctype="multipart/form-data" name="frmExecPlus" id="frmExecPlus" onsubmit="return frmExecPlus_onSubmit( this );">
  <table>
    <tr>
      <td class="label" align="right"><?=gettext("Command");?></td>
      <td class="type"><input name="txtCommand" type="text" size="80" value="" onkeypress="txtCommand_onKey(event);" /></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td valign="top" class="label">
         <input type="hidden" name="txtRecallBuffer" value="<?=$_POST['txtRecallBuffer'] ?>" />
         <input type="button" class="formbtn" name="btnRecallPrev" value="&lt;" onclick="btnRecall_onClick( this.form, -1 );" />
         <input type="submit" class="formbtn" value="<?=gettext("Execute");?>" />
         <input type="button" class="formbtn" name="btnRecallNext" value="&gt;" onclick="btnRecall_onClick( this.form,  1 );" />
         <input type="button"  class="formbtn" value="<?=gettext("Clear");?>" onclick="return Reset_onClick( this.form );" />
      </td>
    </tr>
    <tr>
      <td height="8"></td>
      <td></td>
    </tr>
    <tr>
      <td align="right"><?=gettext("Download");?></td>
      <td>
        <input name="dlPath" type="text" id="dlPath" size="50" value="" />
        <input name="browse" type="button" class="formbtn" id="Browse" onclick='ifield = form.dlPath; filechooser = window.open("filechooser.php?p="+escape(ifield.value), "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." />
        <input name="submit" type="submit" class="formbtn" id="download" value="<?=gettext("Download");?>" />
        </td>
    </tr>
    <tr>
      <td align="right"><?=gettext("Upload");?></td>
      <td valign="top" class="label">
        <input name="ulfile" type="file" class="formbtn" id="ulfile" />
        <input name="submit" type="submit"  class="formbtn" id="upload" value="<?=gettext("Upload");?>" /></td>
    </tr>
		<tr>
			<td colspan="2" valign="top" height="16"></td>
		</tr>
		<tr>
			<td align="right"><?=gettext("PHP Command");?></td>
			<td class="type"><textarea id="txtPHPCommand" name="txtPHPCommand" rows="3" cols="50" wrap="off"><?=htmlspecialchars($_POST['txtPHPCommand']);?></textarea></td>
		</tr>
		<tr>
			<td valign="top">&nbsp;&nbsp;&nbsp;</td>
			<td valign="top" class="label">
				<input type="submit" class="button" value="<?=gettext("Execute");?>" />
			</td>
		</tr>
  </table>
  <?php include("formend.inc");?>
</form>
<script type="text/javascript">
<!--
  document.forms[0].txtCommand.focus();
//-->
</script>
<?php include("fend.inc"); ?>
