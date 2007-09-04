#!/usr/local/bin/php
<?php
/*
	reboot.php
	part of m0n0wall (http://m0n0.ch/wall)

	Copyright (C) 2003-2005 Manuel Kasper <mk@neon1.net>.
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

$pgtitle = array(gettext("System"), gettext("Reboot"));

if ($_POST) {
	if ($_POST['Submit'] != " " . gettext("No") . " ") {
		$rebootmsg = gettext("The system is rebooting now. This may take one minute.");
	} else {
		header("Location: index.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabact"><a href="reboot.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Now");?></a></li>
        <li class="tabinact"><a href="reboot_sched.php"><?=gettext("Scheduled");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<?php if ($rebootmsg): echo print_info_box($rebootmsg); system_reboot(); else:?>
			<form action="reboot.php" method="post">
			  <p><strong><?=gettext("Are you sure you want to reboot the system?");?></strong></p>
			  <p>
			    <input name="Submit" type="submit" class="formbtn" value=" <?=gettext("Yes");?> ">
			    <input name="Submit" type="submit" class="formbtn" value=" <?=gettext("No");?> ">
			  </p>
			</form>
			<?php endif;?>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
