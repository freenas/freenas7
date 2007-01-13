#!/usr/local/bin/php
<?php 
/*
	system_hosts_edit.php
	part of FreeNAS (http://www.freenas.org)
	
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array("System", "Hosts", "Edit host");
require("guiconfig.inc");

if (!is_array($config['system']['hosts']))
	$config['system']['hosts'] = array();

hosts_sort();
$a_hosts = &$config['system']['hosts'];

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

if (isset($id) && $a_hosts[$id]) {
	$pconfig['name'] = $a_hosts[$id]['name'];
	$pconfig['address'] = $a_hosts[$id]['address'];
	$pconfig['descr'] = $a_hosts[$id]['descr'];
}

if ($_POST) {

	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = explode(" ", "name address");
	$reqdfieldsn = explode(",", "Name,Address");
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if (($_POST['name'] && !is_validdesc($_POST['name']))) {
		$input_errors[] = "The host name contain invalid characters.";
	}
	if (($_POST['address'] && !is_ipaddr($_POST['address']))) {
		$input_errors[] = "A valid address must be specified.";
	}
	
	/* check for name conflicts */
	foreach ($a_hosts as $host) {
		if (isset($id) && ($a_hosts[$id]) && ($a_hosts[$id] === $host))
			continue;

		if ($host['name'] == $_POST['name']) {
			$input_errors[] = "An host with this name already exists.";
			break;
		}
	}

	if (!$input_errors) {
		$host = array();
		$host['name'] = $_POST['name'];
		$host['address'] = $_POST['address'];
		$host['descr'] = $_POST['descr'];

		if (isset($id) && $a_hosts[$id])
			$a_hosts[$id] = $host;
		else
			$a_hosts[] = $host;
		
		touch($d_hostsdirty_path);
		
		write_config();
		
		header("Location: system_hosts.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>

<?php if ($input_errors) print_input_errors($input_errors); ?>
            <form action="system_hosts_edit.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td valign="top" class="vncellreq">Name</td>
                  <td class="vtable"><?=$mandfldhtml;?><input name="name" type="text" class="formfld" id="name" size="40" value="<?=htmlspecialchars($pconfig['name']);?>"> 
                    <br> <span class="vexpl">The host name may only consist 
                    of the characters a-z, A-Z and 0-9, '-' , '_' and dot.</span></td>
                  </td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Address</td>
                  <td width="78%" class="vtable"><?=$mandfldhtml;?><input name="address" type="text" class="formfld" id="address" size="20" value="<?=htmlspecialchars($pconfig['address']);?>">
   
                    <br> <span class="vexpl">The address that this hostname 
                    represents.</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell">Description</td>
                  <td width="78%" class="vtable"> <input name="descr" type="text" class="formfld" id="descr" size="40" value="<?=htmlspecialchars($pconfig['descr']);?>"> 
                    <br> <span class="vexpl">You may enter a description here 
                    for your reference (not parsed).</span></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="Save"> 
                    <?php if (isset($id) && $a_hosts[$id]): ?>
                    <input name="id" type="hidden" value="<?=$id;?>"> 
                    <?php endif; ?>
                  </td>
                </tr>
              </table>
</form>
<?php include("fend.inc"); ?>
