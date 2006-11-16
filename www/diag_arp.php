#!/usr/local/bin/php
<?php
/*
	diag_arp.php
	part of m0n0wall (http://m0n0.ch/wall)

	Copyright (C) 2005 Paul Taylor (paultaylor@winndixie.com) and Manuel Kasper <mk@neon1.net>.
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

$pgtitle = array(_DIAGARP_NAME, _DIAGARP_NAMEDESC);

$id = $_GET['id'];
if (isset($_POST['id']))
  $id = $_POST['id'];

if ($_GET['act'] == "del") {
	if (isset($id)) {
		/* remove arp entry from arp table */
		mwexec("/usr/sbin/arp -d " . escapeshellarg($id));

		/* redirect to avoid reposting form data on refresh */
		header("Location: diag_arp.php");
		exit;
	} else {
		/* remove all entries from arp table */
		mwexec("/usr/sbin/arp -d -a");

		/* redirect to avoid reposting form data on refresh */
		header("Location: diag_arp.php");
		exit;
	}
}
$resolve = isset($config['syslog']['resolve']);
?>
<?php include("fbegin.inc"); ?>
<?php
$fp = @fopen("{$g['vardb_path']}/dhcpd.leases","r");
if ($fp) {
	$return = array();
	
	while ($line = fgets($fp)) {
		$matches = "";

		// Sort out comments
		// C-style comments not supported!
		if (preg_match("/^\s*[\r|\n]/", $line, $matches[0]) ||
					preg_match("/^([^\"#]*)#.*$/", $line, $matches[1]) ||
					preg_match("/^([^\"]*)\/\/.*$/", $line, $matches[2]) ||
					preg_match("/\s*#(.*)/", $line, $matches[3]) ||
					preg_match("/\\\"\176/", $line, $matches[4])
			) {
			$line = "";
			continue;
		}
	
		if (preg_match("/(.*)#(.*)/", $line, $matches))
			$line = $matches[0];
	
		// Tokenize lines
		do {
			if (preg_match("/^\s*\"([^\"]*)\"(.*)$/", $line, $matches)) {
				$line = $matches[2];
				$return[] = array($matches[1], 0);
			} else if (preg_match("/^\s*([{};])(.*)$/", $line, $matches)) {
				$line = $matches[2];
				$return[] = array($matches[0], 1);
			} else if (preg_match("/^\s*([^{}; \t]+)(.*)$/", $line, $matches)) {
				$line = $matches[2];
				$return[] = array($matches[1], 0);
			} else
				break;
	
		} while($line);
	
		$lines++;
	}
	
	fclose($fp);
	
	$leases = array();
	$i = 0;
	
	// Put everything together again
	while ($data = array_shift($return)) {
		if ($data[0] == "next") {
			$d = array_shift($return);
		}
		if ($data[0] == "lease") {
			$d = array_shift($return);
			$leases[$i]['ip'] = $d[0];
		}
		if ($data[0] == "client-hostname") {
			$d = array_shift($return);
			$leases[$i]['hostname'] = $d[0];
		}
		if ($data[0] == "hardware") {
			$d = array_shift($return);
			if ($d[0] == "ethernet") {
				$d = array_shift($return);
				$leases[$i]['mac'] = $d[0];
			}
		} else if ($data[0] == "starts") {
			$d = array_shift($return);
			$d = array_shift($return);
			$leases[$i]['start'] = $d[0];
			$d = array_shift($return);
			$leases[$i]['start'] .= " " . $d[0];
		} else if ($data[0] == "ends") {
			$d = array_shift($return);
			$d = array_shift($return);
			$leases[$i]['end'] = $d[0];
			$d = array_shift($return);
			$leases[$i]['end'] .= " " . $d[0];
		} else if ($data[0] == "binding") {
			$d = array_shift($return);
			if ($d[0] == "state") {
				$d = array_shift($return);
				$leases[$i]['act'] = $d[0];
			}
		} else if (($data[0] == "}") && ($data[1] == 1))		// End of group
			$i++;
	}
	
	// Put this in an easy to use form
	$dhcpmac = array();
	$dhcpip = array();
	
	foreach ($leases as $value) {
		$dhcpmac[$value['mac']] = $value['hostname'];	
		$dhcpip[$value['ip']] = $value['hostname'];	
	}
	
	unset($data);
}

exec("/usr/sbin/arp -an",$rawdata);

$i = 0; 
$ifdescrs = array('lan' => 'LAN');
						
for ($j = 1; isset($config['interfaces']['opt' . $j]); $j++) {
	$ifdescrs['opt' . $j] = $config['interfaces']['opt' . $j]['descr'];
}

foreach ($ifdescrs as $key =>$interface) {
	$hwif[$config['interfaces'][$key]['if']] = $interface;
}

$data = array();
foreach ($rawdata as $line) {
	$elements = explode(' ',$line);
	
	if ($elements[3] != "(incomplete)") {
		$arpent = array();
		$arpent['ip'] = trim(str_replace(array('(',')'),'',$elements[1]));
		$arpent['mac'] = trim($elements[3]);
		$arpent['interface'] = trim($elements[5]);
		$data[] = $arpent;
	}
}

function getHostName($mac,$ip)
{
	global $dhcpmac, $dhcpip, $resolve;
	
	if ($dhcpmac[$mac])
		return $dhcpmac[$mac];
	else if ($dhcpip[$ip])
		return $dhcpip[$ip];
	else if ($resolve) 
		return gethostbyaddr($ip);
	else
		return "&nbsp;";
}

?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="listhdrr"><?=_DIAGARP_IPADDRESS;?></td>
    <td class="listhdrr"><?=_DIAGARP_MACADDRESS;?></td>
    <td class="listhdrr"><?=_DIAGARP_HOSTNAME;?></td>
    <td class="listhdr"><?=_DIAGARP_INTERFACE;?></td>
    <td class="list"></td>
  </tr>
  <?php $i = 0; foreach ($data as $entry): ?>
  <tr>
    <td class="listlr"><?=$entry['ip'];?></td>
    <td class="listr"><?=$entry['mac'];?></td>
    <td class="listr"><?=getHostName($entry['mac'], $entry['ip']);?>&nbsp;</td>
    <td class="listr"><?=$hwif[$entry['interface']];?>&nbsp;</td>
    <td valign="middle" nowrap class="list"><a href="diag_arp.php?act=del&id=<?=$entry['ip'];?>"><img src="x.gif" title="<?=_DIAGARP_DEL;?>" width="17" height="17" border="0"></a></td>
  </tr>
  <?php $i++; endforeach; ?>
  <tr> 
    <td></td>
  </tr> 
  <tr> 
    <td class="list" colspan="4"></td>
    <td class="list"><a href="diag_arp.php?act=del"><img src="x.gif" title="<?=_DIAGARP_DELALL;?>" width="17" height="17" border="0"></a></td>
  </tr>
  <tr>
    <td colspan="4">
      <span class="vexpl"><span class="red"><strong><?=_HINT;?>:<br></strong></span><?=_DIAGARP_NOTE;?></span>
    </td>
  </tr>
</table>

<?php include("fend.inc"); ?>
