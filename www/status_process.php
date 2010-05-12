#!/usr/local/bin/php
<?php
/*
	status_process.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2010 Olivier Cochard-Labbe <olivier@freenas.org>.
	All rights reserved.

	Based on m0n0wall (http://m0n0.ch/wall)
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
require("auth.inc");
require("guiconfig.inc");
require("sajax/sajax.php");

$pgtitle = array(gettext("Status"), gettext("Processes"));

function get_process_info() {
	exec("top -b", $result);
	return implode("\n", $result);
}

sajax_init();
sajax_export("get_process_info");
sajax_handle_client_request();
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">//<![CDATA[
<?php sajax_show_javascript();?>
//]]>
</script>
<script type="text/javascript" src="javascript/status_process.js"></script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabcont">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<?php html_titleline(gettext("Processes information"));?>
			  <tr>
			    <td class="listt">
			    	<pre><textarea id="procinfo" name="procinfo" class="listcontent" cols="95" rows="30" readonly="readonly"><?=htmlspecialchars(get_process_info());?></textarea></pre>
			    </td>
			  </tr>
			</table>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
