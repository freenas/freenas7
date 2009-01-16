#!/usr/local/bin/php
<?php
/*
	services_nfs_share_edit.php
	Copyright (C) 2006-2009 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"), gettext("NFS"), isset($id) ? gettext("Edit") : gettext("Add"));

if (!is_array($config['nfsd']['share']))
	$config['nfsd']['share'] = array();

array_sort_key($config['nfsd']['share'], "path");

$a_share = &$config['nfsd']['share'];

if (isset($id) && $a_share[$id]) {
	$pconfig['uuid'] = $a_share[$id]['uuid'];
	$pconfig['path'] = $a_share[$id]['path'];
	$pconfig['mapall'] = $a_share[$id]['mapall'];
	list($pconfig['network'], $pconfig['mask']) = explode('/', $a_share[$id]['network']);
	$pconfig['comment'] = $a_share[$id]['comment'];
	$pconfig['alldirs'] = isset($a_share[$id]['options']['alldirs']);
	$pconfig['readonly'] = isset($a_share[$id]['options']['ro']);
	$pconfig['quiet'] = isset($a_share[$id]['options']['quiet']);
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['path'] = "";
	$pconfig['mapall'] = "yes";
	$pconfig['network'] = "";
	$pconfig['mask'] = "24";
	$pconfig['comment'] = "";
	$pconfig['alldirs'] = true;
	$pconfig['readonly'] = false;
	$pconfig['quiet'] = false;
}

if ($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "path network mask");
	$reqdfieldsn = array(gettext("Path"), gettext("Authorised network"), gettext("Network mask"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (!$input_errors) {
		$share = array();
		$share['uuid'] = $_POST['uuid'];
		$share['path'] = $_POST['path'];
		$share['mapall'] = $_POST['mapall'];
		$share['network'] = gen_subnet($_POST['network'], $_POST['mask']) . "/" . $_POST['mask'];
		$share['comment'] = $_POST['comment'];
		$share['options']['alldirs'] = $_POST['alldirs'] ? true : false;
		$share['options']['ro'] = $_POST['readonly'] ? true : false;
		$share['options']['quiet'] = $_POST['quiet'] ? true : false;

		if (isset($id) && $a_share[$id]) {
			$a_share[$id] = $share;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_share[] = $share;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("nfsshare", $mode, $share['uuid']);
		write_config();

		header("Location: services_nfs_share.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="services_nfs.php"><span><?=gettext("Settings");?></span></a></li>
				<li class="tabact"><a href="services_nfs_share.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Shares");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="services_nfs_share_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
  				  <td width="22%" valign="top" class="vncellreq"><?=gettext("Path");?></td>
  				  <td width="78%" class="vtable">
  				  	<input name="path" type="text" class="formfld" id="path" size="60" value="<?=htmlspecialchars($pconfig['path']);?>">
  				  	<input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.path; filechooser = window.open("filechooser.php?p="+escape(ifield.value)+"&sd={$g['media_path']}", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \><br/>
  				  	<span class="vexpl"><?=gettext("Path to be shared.");?> <?=gettext("Please note that blanks in path names are not allowed.");?></span>
  				  </td>
  				</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Map all users to root"); ?></td>
			      <td width="78%" class="vtable">
			        <select name="mapall" class="formfld" id="mapall">
			        <?php $types = array(gettext("Yes"),gettext("No"));?>
			        <?php $vals = explode(" ", "yes no");?>
			        <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
			          <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['mapall']) echo "selected";?>>
			          <?=htmlspecialchars($types[$j]);?>
			          </option>
			        <?php endfor; ?>
			        </select></br>
			        <span class="vexpl"><?=gettext("All users will have the root privilege.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Authorised network");?></td>
			      <td width="78%" class="vtable">
			        <input name="network" type="text" class="formfld" id="network" size="20" value="<?=htmlspecialchars($pconfig['network']);?>"> /
			        <select name="mask" class="formfld" id="mask">
			          <?php for ($i = 32; $i >= 1; $i--):?>
			          <option value="<?=$i;?>" <?php if ($i == $pconfig['mask']) echo "selected";?>><?=$i;?></option>
			          <?php endfor;?>
			        </select><br>
			        <span class="vexpl"><?=gettext("Network that is authorised to access the NFS share.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Comment");?></td>
			      <td width="78%" class="vtable">
			        <input name="comment" type="text" class="formfld" id="comment" size="30" value="<?=htmlspecialchars($pconfig['comment']);?>">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("All dirs");?></td>
			      <td width="78%" class="vtable">
			      	<input name="alldirs" type="checkbox" id="alldirs" value="yes" <?php if ($pconfig['alldirs']) echo "checked";?>>
			      	<span class="vexpl"><?=gettext("Share all sub directories.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Read only");?></td>
			      <td width="78%" class="vtable">
			      	<input name="readonly" type="checkbox" id="readonly" value="yes" <?php if ($pconfig['readonly']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("Specifies that the file system should be exported read-only.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Quiet");?></td>
			      <td width="78%" class="vtable">
			      	<input name="quiet" type="checkbox" id="quiet" value="yes" <?php if ($pconfig['quiet']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("Inhibit some of the syslog diagnostics for bad lines in /etc/exports.");?></span>
			      </td>
			    </tr>
			  </table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_share[$id])) ? gettext("Save") : gettext("Add");?>">
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
					<?php if (isset($id) && $a_share[$id]):?>
					<input name="id" type="hidden" value="<?=$id;?>">
					<?php endif;?>
				</div>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
