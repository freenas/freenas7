#!/usr/local/bin/php
<?php
/*
	services_inetd_services_edit.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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
	
	TODO: Change select box to ajax
*/
require("guiconfig.inc");

$id = $_GET['id'];
if(isset($_POST['id']))
	$id = $_POST['id'];
	
$pgtitle = array(gettext("Services"), gettext("Inetd"), gettext("Services"),isset($id)?gettext("Edit"):gettext("Add"));

if(!isset($config['inetd']['services']) || !is_array($config['inetd']['services']['service']))
	$config['inetd']['services']['service'] = array();
	
array_sort_key($config['inetd']['services']['service'], "name");

$a_services = &$config['inetd']['services']['service'];

$options_type = array('tcpmux','standard','rpc', 'unix');
exec("/usr/bin/grep -v '^#' /etc/services | /usr/bin/awk -F \" \" '{print $1 | \"sort -u\" }'", $options_name_s);
exec("/usr/bin/grep -v '^#' /etc/rpc | /usr/bin/awk -F \" \" '{print $1 | \"sort -u\" }'", $options_name_r);
$options_socket_t = array('stream');
$options_socket = array('stream','dgram','raw','rdm','seqpacket');
$options_socket_u = array('stream','dgram');
$options_protocol_t = array('tcp','tcp4', 'tcp6', 'tcp46');
$options_protocol_s = array('tcp','tcp4', 'tcp6', 'tcp46', 'udp', 'udp4', 'udp6', 'udp46');
$options_protocol_r = array('rpc/tcp','rpc/tcp4','rpc/tcp6','rpc/tcp46','rpc/udp','rpc/udp4','rpc/udp6','rpc/udp46');
$options_protocol_u = array('unix');

$options_server_s = array('custom','tftpd','daytime','time','echo','discard','chargen');
$options_server_r = array('custom','rquotad');

if (isset($id) && $a_services[$id]) {
	$pconfig['type'] = array_search($a_services[$id]['type'], $options_type);
	switch($pconfig['type']) {
		case 0:
			$pconfig['name'] = $a_services[$id]['name'];
			$pconfig['socket'] = array_search($a_services[$id]['socket'],  $options_socket_t);
			$pconfig['protocol'] = array_search($a_services[$id]['protocol'], $options_protocol_t);
		break;
		case 1:
			$pconfig['name'] = array_search($a_services[$id]['name'], $options_name_s);
			$pconfig['socket'] = array_search($a_services[$id]['socket'],  $options_socket);
			$pconfig['protocol'] = array_search($a_services[$id]['protocol'], $options_protocol_s);
			$pconfig['server'] = array_search($a_services[$id]['server'], $options_server_s);
		break;
		case 2:
			$pconfig['name'] = array_search($a_services[$id]['name'], $options_name_r);
			$pconfig['socket'] = array_search($a_services[$id]['socket'],  $options_socket);
			$pconfig['protocol'] = array_search($a_services[$id]['protocol'], $options_protocol_r);
			$pconfig['version'] = $a_services[$id]['version'];
			$pconfig['server'] = array_search($a_services[$id]['server'], $options_server_r);
		break;
		case 3:
			$pconfig['name'] = $a_services[$id]['name'];
			$pconfig['socket'] = array_search($a_services[$id]['socket'],  $options_socket_u);
			$pconfig['protocol'] = array_search($a_services[$id]['protocol'], $options_protocol_u);
		break;
	}
	
	$pconfig['wait'] = isset($a_services[$id]['wait']) ? true : false;
	$pconfig['maxchild'] = $a_services[$id]['maxchild'];
	$pconfig['maxconnections'] = $a_services[$id]['maxconnections'];
	$pconfig['maxchildperip'] = $a_services[$id]['maxchildperip'];
	
	if((isset($pconfig['server']) && $pconfig['server'] == 0) || $pconfig['type'] == 0) {
		$pconfig['serverprogram'] = $a_services[$id]['serverprogram'];
		$pconfig['serverargs'] = $a_services[$id]['serverargs'];
	}
	
} else {
	$pconfig['maxchild'] = "";
	$pconfig['maxconnections'] = "";
	$pconfig['maxchildperip'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;
	
	if($_POST['type'] == 0 || $_POST['type'] == 3) {
		$reqdfields = explode(" ", "name_in");
		$reqdfieldsn = array(gettext("Name"));
	} else if($_POST['type'] == 2) {
		$reqdfields = explode(" ", "version");
		$reqdfieldsn = array(gettext("Version"));
	}
	
	if(!empty($_POST['server_s']) && $_POST['server_s'] == 0 || !empty($_POST['server_r']) && $_POST['server_r'] == 0) {
		$reqdfields[] = "serverprogram";
		$reqdfields[] = "serverargs";
		$reqdfieldsn[] = gettext("Server-Program");
		$reqdfieldsn[] = gettext("Server-Program-Arguments");
	}
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if (!empty($_POST['maxchild'])) {
		$reqdfields = array("maxchild");
		$reqdfieldsn = array(gettext("max-child"));
		$reqdfieldst = explode(" ", "numericint");
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}
	
	if (!empty($_POST['maxconnections'])) {
		$reqdfields = array("maxconnections");
		$reqdfieldsn = array(gettext("max-connections-per-ip-per-minute"));
		$reqdfieldst = explode(" ", "numericint");
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}
	
	if (!empty($_POST['maxchildperip'])) {
		$reqdfields = array("maxchildperip");
		$reqdfieldsn = array(gettext("max-child-per-ip"));
		$reqdfieldst = explode(" ", "numericint");
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}
	
	if(!empty($_POST['version']) && !preg_match("/^(\d+)$|^(\d+)\-(\d+)$/", $_POST['version'])) {
		$input_errors[] = gettext("This can only be a single numeric argument or a range of versions.");
	}
	
	if(!empty($_POST['version']) && preg_match("/^(\d+)-(\d+)$/", $_POST['version'], $matches)) {
		if($matches[1] == $matches[2]) {
			$input_errors[] = gettext("The RPC low version is equal to the high version. Please don't use a range version.");
		}
		if($matches[1] > $matches[2]) {
			$input_errors[] = gettext("A RPC range is bounded by the low version to the high version.");
		}
	}
	
	if(!$input_errors) {
		$service = array();
		
		$service['type'] = $options_type[$_POST['type']];
		switch($_POST['type']) {
			case 0:
				$service['name'] = $_POST['name_in'];
				$service['socket'] = $options_socket_t[$_POST['socket_t']];
				$service['protocol'] = $options_protocol_t[$_POST['protocol_t']];
			break;
			case 1:
				$service['name'] = $options_name_s[$_POST['name_cb_s']];
				$service['socket'] = $options_socket[$_POST['socket']];
				$service['protocol'] = $options_protocol_s[$_POST['protocol_s']];
				$service['server'] = $options_server_s[$_POST['server_s']];
			break;
			case 2:
				$service['name'] = $options_name_r[$_POST['name_cb_r']];
				$service['version'] = $_POST['version'];
				$service['socket'] = $options_socket[$_POST['socket']];
				$service['protocol'] = $options_protocol_r[$_POST['protocol_r']];
				$service['server'] = $options_server_r[$_POST['server_r']];
			break;
			case 3:
				$service['name'] = $_POST['name_in'];
				$service['socket'] = $options_socket_u[$_POST['socket_u']];
				$service['protocol'] = $options_protocol_u[$_POST['protocol_u']];
			break;
		}
		
		$service['wait'] = isset($_POST['wait']) ? true : false;
		
		if(!empty($_POST['maxchild'])) {
			$service['maxchild'] = $_POST['maxchild'];
		}
		if(!empty($_POST['maxconnections'])){
			$service['maxconnections'] = $_POST['maxconnections'];
		}
		if(!empty($_POST['maxchildperip'])){
			$service['maxchildperip'] = $_POST['maxchildperip'];
		}
		if((isset($_POST['server_s']) && $_POST['server_s'] == 0 || isset($_POST['server_r']) && $_POST['server_r'] == 0) || $$_POST['type'] == 0) {
			$service['serverprogram'] = $_POST['serverprogram'];
			$service['serverargs'] = $_POST['serverargs'];
		}
				
		if (isset($id) && $a_services[$id])
			$a_services[$id] = $service;
		else
			$a_services[] = $service;

		touch($d_inetdserviceconfdirty_path);
		
		write_config();

		header("Location: services_inetd_services.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function type_change()
{
	var type = document.iform.type.value;
	switch(type) {
		case "0":
			showElementById('name_cb_r_tr','hide');
			showElementById('name_cb_s_tr','hide');
			showElementById('name_in_tr','show');
			
			showElementById('version_tr','hide');
			
			showElementById('socket_t_tr','show');
			showElementById('socket_tr','hide');
			showElementById('socket_u_tr','hide');
			
			showElementById('protocol_t_tr','show');
			showElementById('protocol_s_tr','hide');
			showElementById('protocol_r_tr','hide');
			showElementById('protocol_u_tr','hide');
			
			document.iform.wait.checked = false;
			document.iform.wait.disabled = true;
			
			document.iform.server_s.value = 0;
			server_change_s();
			showElementById('server_s_tr','hide');
			document.iform.server_r.value = 0;
			server_change_r();
			showElementById('server_r_tr','hide');
			
			break;
		case "1":
			showElementById('name_cb_r_tr','hide');
			showElementById('name_cb_s_tr','show');
			showElementById('name_in_tr','hide');
			
			showElementById('version_tr','hide');
			
			showElementById('socket_t_tr','hide');
			showElementById('socket_tr','show');
			showElementById('socket_u_tr','hide');
			
			showElementById('protocol_t_tr','hide');
			showElementById('protocol_s_tr','show');
			showElementById('protocol_r_tr','hide');
			showElementById('protocol_u_tr','hide');
			
			document.iform.wait.disabled = false;
			server_change_s();
			showElementById('server_s_tr','show');
			showElementById('server_r_tr','hide');
			break;
		case "2":
			showElementById('name_cb_r_tr','show');
			showElementById('name_cb_s_tr','hide');
			showElementById('name_in_tr','hide');
			
			showElementById('version_tr','show');
			
			showElementById('socket_t_tr','hide');
			showElementById('socket_tr','show');
			showElementById('socket_u_tr','hide');
			
			showElementById('protocol_t_tr','hide');
			showElementById('protocol_s_tr','hide');
			showElementById('protocol_r_tr','show');
			showElementById('protocol_u_tr','hide');
			
			document.iform.wait.disabled = false;
			server_change_r();
			showElementById('server_r_tr','show');
			showElementById('server_s_tr','hide');
			break;
		case "3":
			showElementById('name_cb_r_tr','hide');
			showElementById('name_cb_s_tr','hide');
			showElementById('name_in_tr','show');
			
			showElementById('version_tr','hide');
				
			showElementById('socket_t_tr','hide');
			showElementById('socket_tr','hide');
			showElementById('socket_u_tr','show');
			
			showElementById('protocol_t_tr','hide');
			showElementById('protocol_s_tr','hide');
			showElementById('protocol_r_tr','hide');
			showElementById('protocol_u_tr','show');
			
			document.iform.wait.disabled = false;
			showElementById('server_s_tr','hide');
			showElementById('server_r_tr','hide');
			showElementById('serverprogram_tr','hide');
			showElementById('serverargs_tr','hide');
			break;
	}
}

function server_change_s()
{
	switch(document.iform.server_s.value)  {
		case "0":
			showElementById('serverprogram_tr','show');
			showElementById('serverargs_tr','show');
			break;
		default:
			showElementById('serverprogram_tr','hide');
			showElementById('serverargs_tr','hide');
			break;
	}
}

function server_change_r()
{
	switch(document.iform.server_r.value)  {
		case "0":
			showElementById('serverprogram_tr','show');
			showElementById('serverargs_tr','show');
			break;
		default:
			showElementById('serverprogram_tr','hide');
			showElementById('serverargs_tr','hide');
			break;
	}
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
			<li class="tabinact"><a href="services_inetd.php" title="<?=gettext("Reload page");?>"><?=gettext("Settings");?></a></li>
			<li class="tabact"><a href="services_inetd_services.php"><?=gettext("Services");?></a></li>
		</ul>
	</td>
</tr>
<tr>
	<td class="tabcont">
		<form action="services_inetd_services_edit.php" method="post" name="iform" id="iform">
		<?php if ($input_errors) print_input_errors($input_errors);?>
		<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<?=html_combobox("type",gettext("Type"), $pconfig['type'], $options_type, gettext("Choose the type of service you want inetd to handle."), true, false, "type_change()");?>
		<?=html_combobox("name_cb_s",gettext("Name"), $pconfig['name'], $options_name_s, gettext("This is the service name of the particular daemon."), true);?>
		<?=html_combobox("name_cb_r",gettext("Name"), $pconfig['name'], $options_name_r, gettext("This is the service name of the particular daemon."), true);?>
		<?=html_inputbox("name_in",gettext("Name"),htmlspecialchars($pconfig['name']), gettext("This is the service name of the particular daemon."), true, 20);?>
		<?=html_inputbox("version",gettext("Version"),htmlspecialchars($pconfig['version']), gettext("The RPC version number."), true, 20);?>
		<?=html_combobox("socket",gettext("Socket type"), $pconfig['socket'], $options_socket, gettext("Socket used for the service."), true);?>
		<?=html_combobox("socket_t",gettext("Socket type"), $pconfig['socket'], $options_socket_t, gettext("Socket used for the service."), true);?>
		<?=html_combobox("socket_u",gettext("Socket type"), $pconfig['socket'], $options_socket_u, gettext("Socket used for the service."), true);?>
		<?=html_combobox("protocol_t",gettext("Protocol"), $pconfig['protocol'], $options_protocol_t, gettext("Protocol used for the service."), true);?>
		<?=html_combobox("protocol_s",gettext("Protocol"), $pconfig['protocol'], $options_protocol_s, gettext("Protocol used for the service."), true);?>
		<?=html_combobox("protocol_r",gettext("Protocol"), $pconfig['protocol'], $options_protocol_r, gettext("Protocol used for the service."), true);?>
		<?=html_combobox("protocol_u",gettext("Protocol"), $pconfig['protocol'], $options_protocol_u, gettext("Protocol used for the service."), true);?>
		<?=html_checkbox("wait",gettext("Wait"), $pconfig['wait'], "", gettext("Specifies whether the server that is invoked by inetd will take over the socket associated with the service access point."), true);?>
		<?=html_inputbox("maxchild",gettext("max-child"),htmlspecialchars($pconfig['maxchild']), gettext("The maximum number of child daemons inetd may spawn. 0 for unlimited"), false, 20);?>
		<?=html_inputbox("maxconnections",gettext("max-connections-per-ip-per-minute"),htmlspecialchars($pconfig['maxconnections']), gettext("Limits the number of connections from any particular IP address per minutes. 0 for unlimited."), false, 20);?>
		<?=html_inputbox("maxchildperip",gettext("max-child-per-ip"),htmlspecialchars($pconfig['maxchildperip']), gettext("Limits the number of children that can be started on behalf on any single IP address at any moment. 0 for unlimited."), false, 20);?>
		<?=html_combobox("server_s", gettext("Server"), $pconfig['server'], $options_server_s, gettext("Service to be executed when a connection is received."), true, false, "server_change_s()");?>
		<?=html_combobox("server_r", gettext("Server"), $pconfig['server'], $options_server_r, gettext("Service to be executed when a connection is received."), true, false, "server_change_r()");?>
		<?=html_inputbox("serverprogram",gettext("Server-Program"),htmlspecialchars($pconfig['serverprogram']), gettext("The Path for the program to run."), true, 20);?>
		<?=html_inputbox("serverargs",gettext("Server-Program-Arguments"),htmlspecialchars($pconfig['serverargs']), gettext(""), true, 20);?>
		<tr>
			<td width="22%" valign="top">&nbsp;</td>
			<td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_services[$id]))?gettext("Save"):gettext("Add")?>">
			<?php if (isset($id) && $a_services[$id]): ?>
			<input name="id" type="hidden" value="<?=$id;?>">
			<?php endif; ?>
			</td>
		</tr>
		</table>
		</form>
	</td>
</tr>
</table>
<script language="JavaScript">
<!--
type_change();
//-->
</script>
<?php include("fend.inc");?>
