<?php
/*
	login.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010-2011 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	Modified by Michael Zoon
	Copyright (C) 2010-2011 Michael Zoon <michael.zoon@freenas.org>.
	All rights reserved.

	Modified by Volker Theile
	Copyright (C) 2009 Volker Theile (votdev@gmx.de)
	All rights reserved.

	Part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2011 Olivier Cochard <olivier@freenas.org>.
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	Session::start();

	if ($_POST['username'] === $config['system']['username'] &&
		$_POST['password'] === $config['system']['password']) {
		Session::initAdmin();
		header('Location: index.php');
		exit;
	} else {
		$users = system_get_user_list();
		foreach ($users as $userk => $userv) {
			$password = crypt($_POST['password'], $userv['password']);
			if (($_POST['username'] === $userv['name']) && ($password === $userv['password'])) {
				// Check if it is a local user
				if (FALSE === ($cnid = array_search_ex($userv['uid'], $config['access']['user'], "id")))
					break;
				// Is user allowed to access the user portal?
				if (!isset($config['access']['user'][$cnid]['userportal']))
					break;
				Session::initUser($userv['uid'], $userv['name']);
				header('Location: index.php');
				exit;
			}
		}
	}

	write_log(gettext("Authentication error for illegal user {$_POST['username']} from {$_SERVER['REMOTE_ADDR']}"));
}
?>
<?php header("Content-Type: text/html; charset=" . system_get_language_codeset());?>
<?php
function gentitle($title) {
	$navlevelsep = "|"; // Navigation level separator string.
	//return join($navlevelsep, $title);
}

function genhtmltitle($title) {
	return system_get_hostname() . " - " . gentitle($title);
}

// Menu items.
// Forum
$menu['forum']['desc'] = gettext("Forum");
$menu['forum']['link'] = "http://apps.sourceforge.net/phpbb/freenas/index.php";
$menu['forum']['visible'] = TRUE;
$menu['forum']['menuitem']['visible'] = FALSE;
// Knogledge Base
$menu['kb']['desc'] = gettext("Knowledge Base");
$menu['kb']['visible'] = TRUE;
$menu['kb']['link'] = "http://www.freenaskb.info/kb";
$menu['kb']['menuitem']['visible'] = FALSE;
// Info and Manual
$menu['info']['desc'] = gettext("Information & Manual");
$menu['info']['visible'] = TRUE;
$menu['info']['link'] = "http://wiki.freenas.org/";
$menu['info']['menuitem']['visible'] = FALSE;
// IRC
$menu['irc']['desc'] = gettext("IRC Live Support");
$menu['irc']['visible'] = TRUE;
$menu['irc']['link'] = "http://webchat.freenode.net/?channels=#freenas";
$menu['irc']['menuitem']['visible'] = FALSE;
// Donate
$menu['donate']['desc'] = gettext("Donate");
$menu['donate']['visible'] = TRUE;
$menu['donate']['link'] = "https://sourceforge.net/donate/index.php?group_id=321862";
$menu['donate']['menuitem']['visible'] = FALSE;

function display_menu($menuid) {
	global $menu;

	// Is menu visible?
	if (!$menu[$menuid]['visible'])
		return;

	$link = $menu[$menuid]['link'];
	if ($link == '') $link = 'index.php';
	echo "<li>\n";
	echo "	<a href=\"{$link}\" onmouseover=\"mopen('{$menuid}')\" onmouseout=\"mclosetime()\">".htmlspecialchars($menu[$menuid]['desc'])."</a>\n";
	echo "	<div id=\"{$menuid}\" onmouseover=\"mcancelclosetime()\" onmouseout=\"mclosetime()\">\n";

	# Display menu items.
	foreach ($menu[$menuid]['menuitem'] as $menuk => $menuv) {
		# Is menu item visible?
		if (!$menuv['visible']) {
			continue;
		}
		if ("separator" !== $menuv['type']) {
			# Display menuitem.
			$link = $menuv['link'];
			if ($link == '') $link = 'index.php';
			echo "<a href=\"{$link}\" target=\"" . (empty($menuv['target']) ? "_self" : $menuv['target']) . "\" title=\"".htmlspecialchars($menuv['desc'])."\">".htmlspecialchars($menuv['desc'])."</a>\n";
		} else {
			# Display separator.
			echo "<span class=\"tabseparator\">&nbsp;</span>";
		}
	}

	echo "	</div>\n";
	echo "</li>\n";
}
?>
<?php header("Content-Type: text/html; charset=" . system_get_language_codeset());?>
<?php
  // XML declarations
/*
  some browser might be broken.
  echo '<?xml version="1.0" encoding="'.system_get_language_codeset().'"?>';
  echo "\n";
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=system_get_language_code();?>" lang="<?=system_get_language_code();?>">
<head>
	<title><?=htmlspecialchars(genhtmltitle($pgtitle));?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=system_get_language_codeset();?>" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<?php if ($pgrefresh):?>
	<meta http-equiv="refresh" content="<?=$pgrefresh;?>" />
	<?php endif;?>
	<link href="gui.css" rel="stylesheet" type="text/css" />
	<link href="navbar.css" rel="stylesheet" type="text/css" />
	<link href="tabs.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="javascript/gui.js"></script>
	<script type="text/javascript" src="javascript/navbar.js"></script>
<?php
	if (isset($pglocalheader) && !empty($pglocalheader)) {
		if (is_array($pglocalheader)) {
			foreach ($pglocalheader as $pglocalheaderv) {
		 		echo $pglocalheaderv;
				echo "\n";
			}
		} else {
			echo $pglocalheader;
			echo "\n";
		}
	}
?>
</head>
<body onload='document.iform.username.focus();'>
<div id="header">
	<div id="headerlogo">
		<a title="www.<?=get_product_url();?>" href="http://<?=get_product_url();?>" target="_blank"><img src="/header_logo.png" alt="logo" /></a>
	</div>
	<div id="headerrlogo">
		<div class="hostname">
			<span><?=system_get_hostname();?>&nbsp;</span>
		</div>
	</div>
</div>
<div id="headernavbar">
	<ul id="navbarmenu">
		<?=display_menu("system");?>
		<?=display_menu("network");?>
		<?=display_menu("disks");?>
		<?=display_menu("services");?>
		<!-- Begin extension section -->
		<?php if (Session::isAdmin() && is_dir("{$g['www_path']}/ext")):?>
		<li>
			<a href="index.php" onmouseover="mopen('extensions')" onmouseout="mclosetime()"><?=gettext("Extensions");?></a>
			<div id="extensions" onmouseover="mcancelclosetime()" onmouseout="mclosetime()">
				<?php
				$dh = @opendir("{$g['www_path']}/ext");
				if ($dh) {
					while (($extd = readdir($dh)) !== false) {
						if (($extd === ".") || ($extd === ".."))
							continue;
						@include("{$g['www_path']}/ext/" . $extd . "/menu.inc");
					}
					closedir($dh);
				}?>
			</div>
		</li>
		<?php endif;?>
		<!-- End extension section -->
		<?=display_menu("forum");?>
		<?=display_menu("kb");?>
		<?=display_menu("info");?>
		<?=display_menu("irc");?>
		<?=display_menu("donate");?>
	</ul>
	<div style="clear:both"></div>
</div>
        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
        <div id="loginpage">
            <table height="100%" width="100%" cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr>
						<td align="center">
							<form name="iform" id="iform" action="login.php" method="post">
								<table>
									<tbody>
										<tr>
											<td align="center">
												<div id="loginbox">
													<fieldset><legend><b><?=gettext("WebGUI Login");?></b></legend>
                                                    <table background="vncell_bg.png">
														<tbody>
															<tr>
																<td><?=gettext("Username");?></td>
																<td><input class="formfld" type="text" name="username" value="" /></td>
															</tr>
															<tr>
																<td><?=gettext("Password");?></td>
																<td><input class="formfld" type="password" name="password" value="" /></td>
															</tr>
															<tr>
																<td></td>
															</tr>
															<tr>
																<td align="center" colspan="2"><input class="formbtn" type="submit" value="<?=gettext("Login");?>" /></td>
															</tr>
														</tbody>
													</table>
                                                    </fieldset>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</form>
						</td>
					</tr>
				</tbody>
			</table>
        </div>
        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
        <hr style="width:90%" />
        <div id="pagefooter">
			<span><a title="www.<?=get_product_url();?>" href="http://<?=get_product_url();?>" target="_blank"><?=get_product_name();?></a> <?=str_replace("Copyright (C)","&copy;",get_product_copyright());?></a></span>
		</div>
    </body>
</html>
