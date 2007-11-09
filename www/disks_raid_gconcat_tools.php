#!/usr/local/bin/php
<?php
/*
	disks_raid_gconcat_tools.php
	
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
	JavaScript code are from Volker Theile
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

$pgtitle = array(gettext("Disks"), gettext("Geom Concat"), gettext("Tools"));

if (!is_array($config['gconcat']['vdisk']))
	$config['gconcat']['vdisk'] = array();

array_sort_key($config['gconcat']['vdisk'], "name");

$a_raid = &$config['gconcat']['vdisk'];

if ($_POST) {
	unset($input_errors);
	unset($do_action);

	/* input validation */
	$reqdfields = explode(" ", "action raid disk");
	$reqdfieldsn = array(gettext("Command"),gettext("Volume Name"),gettext("Disk"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (!$input_errors)
	{
		$do_action = true;
		$action = $_POST['action'];
		$raid = $_POST['raid'];
		$disk = $_POST['disk'];
	}
}
if (!isset($do_action))
{
	$do_action = false;
	$action = '';
	$object = '';
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function raid_change() {
 var next = null;
 // Remove all entries from partition combobox.
 document.iform.disk.length = 0;
 // Insert entries for disk combobox.
 switch(document.iform.raid.value)
 {
  <?php foreach ($a_raid as $raidv): ?>
    case "<?=$raidv['name'];?>":
      <?php foreach($raidv['diskr'] as $diskn => $diskrv): ?>
         if(document.all) // MS IE workaround.
            next = document.iform.disk.length;
         document.iform.disk.add(new Option("<?=$diskrv;?>","<?=$diskrv;?>",false,
<?php if("{$diskrv}" == $disk){echo "true";}else{echo "false";};?>), next);
       <?php endforeach; ?>
       break;
     <?php endforeach; ?>
   }
 }
// -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabact"><a href="disks_raid_gconcat.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("JBOD");?></a></li>
	<li class="tabinact"><a href="disks_raid_gstripe.php"><?=gettext("RAID 0"); ?> </a></li>
	<li class="tabinact"><a href="disks_raid_gmirror.php"><?=gettext("RAID 1"); ?></a></li>
	<li class="tabinact"><a href="disks_raid_graid5.php"><?=gettext("RAID 5"); ?></a></li> 
	<li class="tabinact"><a href="disks_raid_gvinum.php"><?=gettext("Geom Vinum"); ?> <?=gettext("(unstable)") ;?></a></li> 
  </ul>
  </td></tr>
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="disks_raid_gconcat.php"><?=gettext("Manage RAID"); ?></a></li>
	<li class="tabact"><a href="disks_raid_gconcat_tools.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("Tools");?></a></li>
	<li class="tabinact"><a href="disks_raid_gconcat_info.php"><?=gettext("Information"); ?></a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
<?php if ($input_errors) print_input_errors($input_errors); ?>
			<form action="disks_raid_gconcat_tools.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
                  <tr> 
      <td valign="top" class="vncellreq"><?=gettext("Volume Name"); ?></td>
      <td class="vtable">           
    	 <select name="raid" class="formfld" id="raid" onchange="raid_change()">
    	  <?php foreach ($a_raid as $raidvol): ?>
    				<option value="<?=$raidvol['name'];?>" <?php if ($pconfig['raid'] == $raid['name']) echo "selected";?>> 
    				<?php echo htmlspecialchars($raidvol['name']);	?>
    				</option>
    		  <?php endforeach; ?>
    		</select>
      </td>
    </tr>
<tr>
            <td valign="top" class="vncellreq"><?=gettext("Disk");?></td>
             <td class="vtable">
             <select name="disk" class="formfld" id="disk"></select>
             </td>
          </tr>
				<tr> 
                  <td valign="top" class="vncellreq"><?=gettext("Command");?></td>
                  <td class="vtable"> 
                    <select name="action" class="formfld" id="action">
                      <option value="list" <?php if ($action == "list") echo "selected"; ?>>list</option>
                      <option value="status" <?php if ($action == "status") echo "selected"; ?>>status</option>
                      <option value="clear" <?php if ($action == "clear") echo "selected"; ?>>clear</option>
                      <option value="stop" <?php if ($action == "stop") echo "selected"; ?>>stop</option>
 			<option value="dump" <?php if ($action == "dump") echo "selected"; ?>>dump</option>
                     </select>
                  </td>
                </tr>
				<tr>
				  <td width="22%" valign="top">&nbsp;</td>
				  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Send Command!");?>">
				</td>
				</tr>
				<tr>
				<td valign="top" colspan="2">
				<? if ($do_action)
				{
					echo("<strong>" . gettext("Command output:") . "</strong><br>");
					echo('<pre>');
					ob_end_flush();
					
					//Remove the first 5 character of the diskname: /dev/
					$smalldisk = substr($disk, 5);
					if (strcmp($action,"insert") == 0 || strcmp($action,"remove")==0) {
						
						
						$cmd = "/sbin/gconcat $action " . escapeshellarg($raid) . " " . escapeshellarg($smalldisk);
					} else if (strcmp($action,"dump") == 0 || strcmp($action,"clear") == 0 || strcmp($action,"clear") == 0) {
						$cmd = "/sbin/gconcat $action " . escapeshellarg($smalldisk);
					} else {
						$cmd = "/sbin/gconcat $action " . escapeshellarg($raid);
					}

					
					system($cmd);
				
					echo('</pre>');
				}
				?>
				</td>
				</tr>
			</table>
</form>
<p><span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:</strong></span><br><?=gettext("1. Use these specials actions for debugging only!<br>2. There is no need of using this menu for starting a RAID volume (start automaticaly).");?></span></p>
</td></tr></table>
<script language="JavaScript">
<!--
raid_change();
//-->
</script>
<?php include("fend.inc"); ?>
