<?php 
/*
	checkversion.php
	Modified by Michael Zoon
	Copyright (C) 2010-2011 Michael Zoon <michael.zoon@freenas.org>.
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
// Check for a newer online firmware version and use locale from WEBGUI
$locale = "";
if (isSet($_GET["locale"])) $locale = $_GET["locale"];
putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("messages", "./locale");
textdomain("messages");

$lateststable = trim(file_get_contents("lateststable.txt"));
$latestnightly = trim(file_get_contents("latestnightly.txt"));
$latestbeta = trim(file_get_contents("latestbeta.txt"));
$userversion .= trim(file_get_contents("/etc/prd.version"));
$userversion .= ".";
$userversion .= trim(file_get_contents("/etc/prd.revision"));

/*
$updates = array(

	pb26r606 => array(0, "A minor update (pb26r614) is available (webGUI improvements)."),
	pb26r610 => array(0, "A minor update (pb26r614) is available (webGUI improvements, IPsec race condition fix with dyn. WAN IP).")

);*/

function lateststable($message) {
	return <<<EOD
<p><table border="0" cellspacing="0" cellpadding="4" width="100%">
<tr><td bgcolor="#687BA4" align="center" valign="top" width="36"><img src="exclam.gif" width="28" height="32"></td>
<td bgcolor="#D9DEE8" style="padding-left: 8px">{$message}<br>
<a href="http://sourceforge.net/projects/freenas/files/FreeNAS-7-stable/" target="_blank">Download</a>
</td></tr></table></p>

EOD;
}

function minorbetaupdate($message) {
	return <<<EOD
<p><table border="0" cellspacing="0" cellpadding="4" width="100%">
<tr><td bgcolor="#687BA4" align="center" valign="top" width="36"><img src="exclam.gif" width="28" height="32"></td>
<td bgcolor="#D9DEE8" style="padding-left: 8px">{$message}<br>
<a href="http://sourceforge.net/projects/freenas/files/FreeNAS-7-stable" target="_blank">Download</a>
</td></tr></table></p>

EOD;
}

function latestnightly($message) {
	return <<<EOD
<p><table border="0" cellspacing="0" cellpadding="4" width="100%">
<tr><td bgcolor="#687BA4" align="center" valign="top" width="36"><img src="exclam.gif" width="28" height="32"></td>
<td bgcolor="#D9DEE8" style="padding-left: 8px">{$message}<br>
<a href="http://sourceforge.net/projects/freenas/files/FreeNAS-7-nightly/" target="_blank">Download</a>
</td></tr></table></p>

EOD;
}

function noupdate($message) {
	return <<<EOD
<p><table border="0" cellspacing="0" cellpadding="4" width="100%">
<tr><td bgcolor="#687BA4" align="center" valign="top" width="36"><img src="check.gif" width="28" height="32"></td>
<td bgcolor="#D9DEE8" style="padding-left: 8px">
{$message}
</td></tr></table></p>

EOD;
}

if (isset($updates[$userversion])) {
	if ($updates[$userversion][0])
		$retmsg = $latestnightly($updates[$userversion][1]);
	else
		$retmsg = lateststable($updates[$userversion][1]);

} else if ($userversion == $latestbeta) {
	$retmsg = noupdate(gettext("<b>You are using the most recent Beta build of FreeNAS.</b>"));

} else if (strstr($userversion, "b")) {
	if ($userversion < $latestbeta) {
		$retmsg = minorbetaupdate(gettext("<b>A newer Beta version of FreeNAS than the one you're currently using is available!</b>"));
	}

} else if ($userversion < $lateststable) {
	$retmsg = lateststable(gettext("<b>A newer Stable build release is available for your server!</b>"));
} else if ($userversion < $latestnightly) {
	$retmsg = latestnightly(gettext("A newer <b>Nightly</b> build release is available for your server!"));
}

if ($userversion == $lateststable) {
	$noupdate_stable = noupdate(gettext("You're currently running the latest <b>Stable</b> build of FreeNAS."));
}
if ($userversion == $latestnightly) {
	$noupdate_nightly = noupdate(gettext("You're currently running the latest <b>Nightly</b> build of FreeNAS."));
}
echo $retmsg;
echo $noupdate_stable;
echo $noupdate_nightly;
echo $noupdate_beta;
echo "<br />";
echo gettext("<HR color='#999999'>Your FreeNAS version: "); echo "&nbsp; <strong><font color='#FF0000'><size ='12'>$userversion </font></size></strong>";
echo "<br />";
echo "";
echo "<br />";
echo "";
echo "<br />";
echo gettext("Online version check:");
echo "<br />";
echo "";
echo "<br />";
echo gettext("Latest stable build available: "); echo "&nbsp; <strong><font color='#FF0000'>$lateststable</font></strong>";
echo "<br />";
echo gettext("Latest nightly build available: "); echo "&nbsp; <strong><font color='#FF0000'>$latestnightly</font></strong>";
echo "<br />";
echo "<strong>$latestbeta</strong>";
echo "<HR color='#999999'>";
echo "<br />";
?>