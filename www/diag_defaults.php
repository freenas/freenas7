#!/usr/local/bin/php
<?php 
/*
	diag_defaults.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array("Diagnostics", "Factory defaults");

require("guiconfig.inc");

if ($_POST) {
	if ($_POST['Submit'] != " No ") {
		reset_factory_defaults();
		system_reboot();
		$rebootmsg = "The system has been reset to factory defaults and is now rebooting. This may take one minute.";
	} else {
		header("Location: index.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($rebootmsg): echo print_info_box($rebootmsg); else: ?>
<form action="diag_defaults.php" method="post">
              <p><strong>If you click &quot;Yes&quot;, FreeNAS will be reset 
                to factory defaults and will reboot immediately. The entire system 
                configuration will be overwritten. The LAN IP address will be 
                reset to 192.168.1.250 and the password will be set to 'freenas'.<br>
                <br>
                Are you sure you want to proceed?</strong></p>
        <p> 
          <input name="Submit" type="submit" class="formbtn" value=" Yes ">
          <input name="Submit" type="submit" class="formbtn" value=" No ">
        </p>
      </form>
<?php endif; ?>
<?php include("fend.inc"); ?>
