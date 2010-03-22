<?php
/*
	login.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	Copyright (C) 2009-2010 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2010 Olivier Cochard <olivier@freenas.org>.
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=system_get_language_code();?>" lang="<?=system_get_language_code();?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?=system_get_language_codeset();?>" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<link href="gui.css" rel="stylesheet" type="text/css" />
		<title><?=get_product_name();?></title>
		<style type="text/css">
		html,body {
			height: 100%;
			width: 100%;
		}
		</style>

	</head>
	<body onload='document.iform.username.focus();'>
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
													<table>
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
	</body>
</html>
