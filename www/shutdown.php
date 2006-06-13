#!/usr/local/bin/php
<?php 
/*
	shutdown.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(_DIAG_NAME,_SHUT_NAME);

if ($_POST) {
	if ($_POST['Submit'] != " " . _NO . " ") {
		system_halt();
		$rebootmsg = _SHUT_MSGSHUT;
	} else {
		header("Location: index.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabact"><a href="shutdown.php" style="color:black" title="reload page"><?=_SHUT_NAME ;?></a></li>
	<li class="tabinact"><a href="shutdown_sched.php"><?=_SHUTSCHED_NAME ;?></a></li></a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
<?php if ($rebootmsg): echo print_info_box($rebootmsg); else: ?>
      <form action="shutdown.php" method="post">
        <p><strong><?=_SHUT_CONF;?></strong></p>
        <p> 
          <input name="Submit" type="submit" class="formbtn" value=" <?=_YES;?> ">
          <input name="Submit" type="submit" class="formbtn" value=" <?=_NO;?> ">
        </p>

<?php endif; ?>
	</tr>
   </table>
      </form>
	</td>
  </tr>
</table>
<?php include("fend.inc"); ?>
