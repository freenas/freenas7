#!/usr/local/bin/php
<?php 
/*
	services_upnp_edit.php
	Copyright © 2006-2009 Volker Theile (votdev@gmx.de)
  All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Services"),gettext("UPnP"),gettext("Content"),isset($id)?gettext("Edit"):gettext("Add"));

if(!is_array($config['upnp']['content']))
	$config['upnp']['content'] = array();

sort($config['upnp']['content']);

if($_POST) {
	unset($input_errors);

	/* input validation */
	$reqdfields = explode(" ", "content");
	$reqdfieldsn = array(gettext("Content"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if(!$input_errors) {
		/* Remove old entry from content list */
		$config['upnp']['content'] = array_diff($config['upnp']['content'],array($config['upnp']['content'][$id]));
		/* Add new entry */
		$config['upnp']['content'] = array_merge($config['upnp']['content'],array($_POST['content']));

		touch($d_upnpconfdirty_path);
		write_config();
    header("Location: services_upnp.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<form action="services_upnp_edit.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
	    	<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			    	<td width="22%" valign="top" class="vncellreq"><?=gettext("Content");?></td>
			      <td width="78%" class="vtable">
							<input name="content" type="text" class="formfld" id="content" size="60" value="<?=htmlspecialchars($config['upnp']['content'][$id]);?>">
							<input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.content; filechooser = window.open("filechooser.php?p="+escape(ifield.value)+"&sd=<?=$g['media_path'];?>", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \>
							<br><?=gettext("Directory to be shared.");?>
			      </td>
			    </tr>
			  </table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=isset($id)?gettext("Save"):gettext("Add")?>"> 
					<?php if(isset($id)): ?>
					<input name="id" type="hidden" value="<?=$id;?>"> 
					<?php endif; ?>
				</div>
			</td>
		</tr>
	</table>
</form>
<?php include("fend.inc"); ?>
