#!/usr/local/bin/php
<?php
/*
	services_afp_share_edit.php
	Copyright (C) 2006-2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"), gettext("AFP"), gettext("Share"), isset($id) ? gettext("Edit") : gettext("Add"));

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

array_sort_key($config['mounts']['mount'], "devicespecialfile");

$a_mount = &$config['mounts']['mount'];

if (!is_array($config['afp']['share']))
	$config['afp']['share'] = array();

array_sort_key($config['afp']['share'], "name");

$a_share = &$config['afp']['share'];

if (isset($id) && $a_share[$id]) {
	$pconfig['uuid'] = $a_share[$id]['uuid'];
	$pconfig['name'] = $a_share[$id]['name'];
	$pconfig['path'] = $a_share[$id]['path'];
	$pconfig['comment'] = $a_share[$id]['comment'];
	$pconfig['volpasswd'] = $a_share[$id]['volpasswd'];
	$pconfig['casefold'] = $a_share[$id]['casefold'];
	$pconfig['volcharset'] = $a_share[$id]['volcharset'];
	$pconfig['allow'] = $a_share[$id]['allow'];
	$pconfig['deny'] = $a_share[$id]['deny'];
	$pconfig['rolist'] = $a_share[$id]['rolist'];
	$pconfig['rwlist'] = $a_share[$id]['rwlist'];
	$pconfig['dbpath'] = $a_share[$id]['dbpath'];
	$pconfig['cachecnid'] = isset($a_share[$id]['options']['cachecnid']);
	$pconfig['crlf'] = isset($a_share[$id]['options']['crlf']);
	$pconfig['mswindows'] = isset($a_share[$id]['options']['mswindows']);
	$pconfig['noadouble'] = isset($a_share[$id]['options']['noadouble']);
	$pconfig['nodev'] = isset($a_share[$id]['options']['nodev']);
	$pconfig['nofileid'] = isset($a_share[$id]['options']['nofileid']);
	$pconfig['nohex'] = isset($a_share[$id]['options']['nohex']);
	$pconfig['prodos'] = isset($a_share[$id]['options']['prodos']);
	$pconfig['nostat'] = isset($a_share[$id]['options']['nostat']);
	$pconfig['upriv'] = isset($a_share[$id]['options']['upriv']);
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "";
	$pconfig['path'] = "";
	$pconfig['comment'] = "";
	$pconfig['volpasswd'] = '';
	$pconfig['casefold'] = 'none';
	$pconfig['volcharset'] = 'UTF8';
	$pconfig['allow'] = '';
	$pconfig['deny'] = '';
	$pconfig['rolist'] = '';
	$pconfig['rwlist'] = '';
	$pconfig['dbpath'] = '';
	$pconfig['cachecnid'] = false;
	$pconfig['crlf'] = false;
	$pconfig['mswindows'] = false;
	$pconfig['noadouble'] = false;
	$pconfig['nodev'] = false;
	$pconfig['nofileid'] = false;
	$pconfig['nohex'] = false;
	$pconfig['prodos'] = false;
	$pconfig['nostat'] = false;
	$pconfig['upriv'] = false;
}

if ($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "name comment");
	$reqdfieldsn = array(gettext("Name"), gettext("Comment"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	$reqdfieldst = explode(" ", "string string");
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	// Verify that the share password is not more than 8 characters.
	if (strlen($_POST['volpasswd']) > 8) {
	    $input_errors[] = gettext("Share passwords can not be more than 8 characters.");
	}

	// Check for duplicates.
	if ((!isset($id) && (false !== array_search_ex($_POST['name'], $a_share, "name"))) ||
			(isset($id) && ($a_share[$id]['name'] !== $_POST['name']) && (false !== array_search_ex($_POST['name'], $a_share, "name")))) {
		$input_errors[] = gettext("The share name is already used.");
	}

	if (!$input_errors) {
		$share = array();
		$share['uuid'] = $_POST['uuid'];
		$share['name'] = $_POST['name'];
		$share['path'] = $_POST['path'];
		$share['comment'] = $_POST['comment'];
		$share['volpasswd'] = $_POST['volpasswd'];
		$share['casefold'] = $_POST['casefold'];
		$share['volcharset'] = $_POST['volcharset'];
		$share['allow'] = $_POST['allow'];
		$share['deny'] = $_POST['deny'];
		$share['rolist'] = $_POST['rolist'];
		$share['rwlist'] = $_POST['rwlist'];
		$share['dbpath'] = $_POST['dbpath'];
		$share['options']['cachecnid'] = $_POST['cachecnid'] ? true : false;
		$share['options']['crlf'] = $_POST['crlf'] ? true : false;
		$share['options']['mswindows'] = $_POST['mswindows'] ? true : false;
		$share['options']['noadouble'] = $_POST['noadouble'] ? true : false;
		$share['options']['nodev'] = $_POST['nodev'] ? true : false;
		$share['options']['nofileid'] = $_POST['nofileid'] ? true : false;
		$share['options']['nohex'] = $_POST['nohex'] ? true : false;
		$share['options']['prodos'] = $_POST['prodos'] ? true : false;
		$share['options']['nostat'] = $_POST['nostat'] ? true : false;
		$share['options']['upriv'] = $_POST['upriv'] ? true : false;

		if (isset($id) && $a_share[$id]) {
			$a_share[$id] = $share;
			$mode = UPDATENOTIFICATION_MODE_MODIFIED;
		} else {
			$a_share[] = $share;
			$mode = UPDATENOTIFICATION_MODE_NEW;
		}

		ui_set_updatenotification("afpshare", $mode, $share['uuid']);
		write_config();

		header("Location: services_afp_share.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabinact"><a href="services_afp.php"><span><?=gettext("Settings");?></span></a></li>
        <li class="tabact"><a href="services_afp_share.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Shares");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="services_afp_share_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Name");?></td>
			      <td width="78%" class="vtable">
			        <input name="name" type="text" class="formfld" id="name" size="30" value="<?=htmlspecialchars($pconfig['name']);?>">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Comment");?></td>
			      <td width="78%" class="vtable">
			        <input name="comment" type="text" class="formfld" id="comment" size="30" value="<?=htmlspecialchars($pconfig['comment']);?>">
			      </td>
			    </tr>
			    <tr>
					  <td width="22%" valign="top" class="vncellreq"><?=gettext("Path");?></td>
					  <td width="78%" class="vtable">
					  	<input name="path" type="text" class="formfld" id="path" size="60" value="<?=htmlspecialchars($pconfig['path']);?>">
					  	<input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.path; filechooser = window.open("filechooser.php?p="+escape(ifield.value)+"&sd=/mnt", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \><br/>
					  	<span class="vexpl"><?=gettext("Path to be shared.");?></span>
					  </td>
					</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Share Password");?></td>
			      <td width="78%" class="vtable">
			        <input name="volpasswd" type="text" class="formfld" id="volpasswd" size="16" value="<?=htmlspecialchars($pconfig['volpasswd']);?>">
			        <?=gettext("Set share password.");?><br/>
			        <span class="vexpl"><?=gettext("This controls the access to this share with an access password.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Share Character Set");?></td>
			      <td width="78%" class="vtable">
			        <input name="volcharset" type="text" class="formfld" id="volcharset" size="16" value="<?=htmlspecialchars($pconfig['volcharset']);?>"><br>
			        <span class="vexpl"><?=gettext("Specifies the share character set. For example UTF8, UTF8-MAC, ISO-8859-15, etc.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Allow");?></td>
			      <td width="78%" class="vtable">
			        <input name="allow" type="text" class="formfld" id="allow" size="60" value="<?=htmlspecialchars($pconfig['allow']);?>"><br/>
			        <?=gettext("This option allows the users and groups that access a share to be specified. Users and groups are specified, delimited by commas. Groups are designated by a @ prefix.");?>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Deny");?></td>
			      <td width="78%" class="vtable">
			        <input name="deny" type="text" class="formfld" id="deny" size="60" value="<?=htmlspecialchars($pconfig['deny']);?>"><br/>
			        <?=gettext("The  deny  option specifies users and groups who are not allowed access to the share. It follows the same  format  as  the  allow option.");?>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Read Only Access");?></td>
			      <td width="78%" class="vtable">
			        <input name="rolist" type="text" class="formfld" id="rolist" size="60" value="<?=htmlspecialchars($pconfig['rolist']);?>"><br/>
			        <?=gettext("Allows certain users and groups to have read-only  access  to  a share. This follows the allow option format.");?>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Read/Write Access");?></td>
			      <td width="78%" class="vtable">
			        <input name="rwlist" type="text" class="formfld" id="rwlist" size="60" value="<?=htmlspecialchars($pconfig['rwlist']);?>"><br/>
			        <?=gettext("Allows  certain  users and groups to have read/write access to a share. This follows the allow option format.");?>
			      </td>
			    </tr>
					<tr>
			      <td colspan="2" class="list" height="12"></td>
			    </tr>
			    <tr>
			      <td colspan="2" valign="top" class="listtopic"><?=gettext("Advanced Options");?></td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Case Folding");?></td>
			      <td width="78%" class="vtable">
							<select name="casefold" size="1" id="casefold">
			          <option value="none" <?php if ($pconfig['casefold'] === "none") echo "selected";?>><?=gettext("No case folding");?></option>
								<option value="tolower" <?php if ($pconfig['casefold'] === "tolower") echo "selected";?>><?=gettext("Lowercases names in both directions");?></option>
								<option value="toupper" <?php if ($pconfig['casefold'] === "toupper") echo "selected";?>><?=gettext("Uppercases names in both directions");?></option>
								<option value="xlatelower" <?php if ($pconfig['casefold'] === "xlatelower") echo "selected";?>><?=gettext("Client sees lowercase, server sees uppercase");?></option>
								<option value="xlateupper" <?php if ($pconfig['casefold'] === "xlateupper") echo "selected";?>><?=gettext("Client sees uppercase, server sees lowercase");?></option>
			        </select><br/>
			        <span class="vexpl"><?=gettext("This controls how the case of filenames are viewed and stored.");?></span>
			      </td>
			    </tr>
			    <tr>
					  <td width="22%" valign="top" class="vncell"><?=gettext("dbpath");?></td>
					  <td width="78%" class="vtable">
					  	<input name="dbpath" type="text" class="formfld" id="dbpath" size="60" value="<?=htmlspecialchars($pconfig['dbpath']);?>">
					  	<input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.dbpath; filechooser = window.open("filechooser.php?p="+escape(ifield.value)+"&sd=/mnt", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \><br/>
					  	<span class="vexpl"><?=gettext("Sets the database information to be stored in path. You have to specifiy a writable location, even if the volume is read only.");?></span>
					  </td>
					</tr>
					<tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("cachecnid");?></td>
			      <td width="78%" class="vtable">
			      	<input name="cachecnid" type="checkbox" id="cachecnid" value="yes" <?php if ($pconfig['cachecnid']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("If set afpd uses the ID information stored in AppleDouble V2 header files to reduce database load. Don't set this option if the volume is modified by non AFP clients (NFS/SMB/local).");?></span>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("crlf");?></td>
			      <td width="78%" class="vtable">
			      	<input name="crlf" type="checkbox" id="crlf" value="yes" <?php if ($pconfig['crlf']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("Enables crlf translation for TEXT files, automatically converting macintosh line breaks into Unix ones. Use of this option might be dangerous since some older programs store binary data files as type 'TEXT' when saving and switch the filetype in a second step. Afpd will potentially destroy such files when 'erroneously' changing bytes in order to do line break translation.");?></span>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("mswindows");?></td>
			      <td width="78%" class="vtable">
			      	<input name="mswindows" type="checkbox" id="mswindows" value="yes" <?php if ($pconfig['mswindows']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("This forces filenames to be restricted to the character set used by Windows. This is not recommended for shares used principally by Mac computers.");?></span>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("noadouble");?></td>
			      <td width="78%" class="vtable">
			      	<input name="noadouble" type="checkbox" id="noadouble" value="yes" <?php if ($pconfig['noadouble']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("This controls whether the .AppleDouble directory gets created unless absolutely needed. This option should not be used if files are access mostly by Mac computers.");?></span>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("nodev");?></td>
			      <td width="78%" class="vtable">
			      	<input name="nodev" type="checkbox" id="nodev" value="yes" <?php if ($pconfig['nodev']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("Always use 0 for device number, helps when the device number is not constant across a reboot, cluster, ...");?></span>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("nofileid");?></td>
			      <td width="78%" class="vtable">
			      	<input name="nofileid" type="checkbox" id="nofileid" value="yes" <?php if ($pconfig['nofileid']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("Don't advertise createfileid, resolveid, deleteid calls.");?></span>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("nohex");?></td>
			      <td width="78%" class="vtable">
			      	<input name="nohex" type="checkbox" id="nohex" value="yes" <?php if ($pconfig['nohex']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("Disables :hex translations for anything except dot files. This option makes the '/' character illegal.");?></span>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("prodos");?></td>
			      <td width="78%" class="vtable">
			      	<input name="prodos" type="checkbox" id="prodos" value="yes" <?php if ($pconfig['prodos']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("Provides compatibility with Apple II clients.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("nostat");?></td>
			      <td width="78%" class="vtable">
			      	<input name="nostat" type="checkbox" id="nostat" value="yes" <?php if ($pconfig['nostat']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("Don't stat volume path when enumerating volumes list, useful for automounting or volumes created by a preexec script.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("upriv");?></td>
			      <td width="78%" class="vtable">
			      	<input name="upriv" type="checkbox" id="upriv" value="yes" <?php if ($pconfig['upriv']) echo "checked";?>>
			        <span class="vexpl"><?=gettext("Use AFP3 unix privileges.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
							<input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_share[$id])) ? gettext("Save") : gettext("Add");?>">
							<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
			        <?php if (isset($id) && $a_share[$id]):?>
			        <input name="id" type="hidden" value="<?=$id;?>">
			        <?php endif;?>
			      </td>
			    </tr>
			  </table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
