#!/usr/local/bin/php
<?php
/* Run various commands and collect their output into HTML tables.
 * Jim McBeath <jimmc@macrovision.com> Nov 2003
 *
 * (modified for m0n0wall by Manuel Kasper <mk@neon1.net>)
 * and re-used on FreeNAS by Olivier Cochard-Labb� <olivier@freenas.org>)
 */
require("guiconfig.inc");

$pageTitle = get_product_name() . ": Status";

/* Execute a command, with a title, and generate an HTML table
 * showing the results.
 */

function doCmdT($title, $command, $isstr) {
    echo "<p>\n";
    echo "<a name=\"" . $title . "\">\n";
    echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
    echo "<tr><td class=\"listtopic\">" . $title . "</td></tr>\n";
    echo "<tr><td class=\"listlr\"><pre>";		/* no newline after pre */
	
	if ($isstr) {
		echo htmlspecialchars($command);
	} else {
		if ($command == "dumpconfigxml") {
			$fd = @fopen("/conf/config.xml", "r");
			if ($fd) {
				while (!feof($fd)) {
					$line = fgets($fd);
					/* remove password tag contents */
					$line = preg_replace("/<password>.*?<\\/password>/", "<password>xxxxx</password>", $line);
					$line = preg_replace("/<admin_pass>.*?<\\/admin_pass>/", "<admin_pass>xxxxx</admin_pass>", $line);
					$line = preg_replace("/<pre-shared-key>.*?<\\/pre-shared-key>/", "<pre-shared-key>xxxxx</pre-shared-key>", $line);
					$line = str_replace("\t", "    ", $line);
					echo htmlspecialchars($line,ENT_NOQUOTES);
				}
			}
			fclose($fd);
		} else {
			exec ($command . " 2>&1", $execOutput, $execStatus);
			for ($i = 0; isset($execOutput[$i]); $i++) {
				if ($i > 0) {
					echo "\n";
				}
				echo htmlspecialchars($execOutput[$i],ENT_NOQUOTES);
			}
		}
	}
    echo "</pre></tr>\n";
    echo "</table>\n";
}

/* Execute a command, giving it a title which is the same as the command. */
function doCmd($command) {
    doCmdT($command,$command);
}

/* Define a command, with a title, to be executed later. */
function defCmdT($title, $command) {
    global $commands;
    $title = htmlspecialchars($title,ENT_NOQUOTES);
    $commands[] = array($title, $command, false);
}

/* Define a command, with a title which is the same as the command,
 * to be executed later.
 */
function defCmd($command) {
    defCmdT($command,$command);
}

/* Define a string, with a title, to be shown later. */
function defStrT($title, $str) {
    global $commands;
    $title = htmlspecialchars($title,ENT_NOQUOTES);
    $commands[] = array($title, $str, true);
}

/* List all of the commands as an index. */
function listCmds() {
    global $commands;
    echo "<p>This status page includes the following information:\n";
    echo "<ul>\n";
    for ($i = 0; isset($commands[$i]); $i++ ) {
        echo "<li><strong><a href=\"#" . $commands[$i][0] . "\">" . $commands[$i][0] . "</a></strong>\n";
    }
    echo "</ul>\n";
}

/* Execute all of the commands which were defined by a call to defCmd. */
function execCmds() {
    global $commands;
    for ($i = 0; isset($commands[$i]); $i++ ) {
        doCmdT($commands[$i][0], $commands[$i][1], $commands[$i][2]);
    }
}

/* Set up all of the commands we want to execute. */
defCmdT("Version","cat /etc/prd.version");
defCmdT("Platform","cat /etc/platform");
defCmdT("dmesg","/sbin/dmesg");
defCmdT("System uptime","uptime");
defCmdT("Interfaces","/sbin/ifconfig -a");
defCmdT("Routing tables","netstat -nr");
defCmdT("Processes","ps xauww");
defCmdT("Memory","top -b 0|grep Mem");
defCmdT("swap use","/usr/sbin/swapinfo");
defCmdT("ATA disk","/sbin/atacontrol list");
defCmdT("SCSI disk","/sbin/camcontrol devlist");
defCmdT("Geom Concat","/sbin/gconcat list");
defCmdT("Geom Stripe","/sbin/gstripe list");
defCmdT("Geom Mirror","/sbin/gmirror list");
defCmdT("Geom RAID5","/sbin/graid5 list");
defCmdT("Geom Vinum","/sbin/gvinum list");
defCmdT("Mount point","/sbin/mount");
defCmdT("Free Disk Space","/bin/df -h");
defCmdT("Encrypted disks","/sbin/geli list");
defCmdT("rc.conf","cat /etc/rc.conf");
defCmdT("resolv.conf","cat /var/etc/resolv.conf");
defCmdT("hosts","cat /etc/hosts");
defCmdT("crontab","cat /var/etc/crontab");
defCmdT("dhclient.conf","cat /var/etc/dhclient.conf");
defCmdT("smb.conf","cat /var/etc/smb.conf");
defCmdT("sshd.conf","cat /var/etc/ssh/sshd_config");
defCmdT("mdnsresponder.conf","cat /var/etc/mdnsresponder.conf");
defCmdT("last 200 system log entries","/usr/sbin/clog /var/log/system.log 2>&1 | tail -n 200");
defCmd("ls /conf");
defCmd("ls /var/run");
defCmdT("config.xml","dumpconfigxml");

exec("/bin/date", $dateOutput, $dateStatus);
$currentDate = $dateOutput[0];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?=$pageTitle;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="gui.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
pre {
   margin: 0px;
   font-family: courier new, courier;
   font-weight: normal;
   font-size: 9pt;
}
-->
</style>
</head>

<body link="#0000CC" vlink="#0000CC" alink="#0000CC">
<p><span class="pgtitle"><?=$pageTitle;?></span><br>
<strong><?=$currentDate;?></strong>
<p><span class="red"><strong>Note: make sure to remove any sensitive information 
(passwords, maybe also IP addresses) before posting 
information from this page in public places (like mailing lists)!</strong></span><br>
Passwords in config.xml have been automatically removed.

<?php listCmds(); ?>

<?php execCmds(); ?>

</body>
</html>
