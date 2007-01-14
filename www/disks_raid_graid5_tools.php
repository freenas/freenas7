#!/usr/local/bin/php
<?php
/*
	disks_raid_graid5_tools.php
	
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(_DISKSPHP_NAME, _DISKSRAIDPHP_GRAID5, _DISKSRAIDEDITPHP_NAMEDESC);

if (!is_array($config['graid5']['vdisk']))
	$config['graid5']['vdisk'] = array();

graid5_sort();
$a_raid = &$config['graid5']['vdisk'];

if ($_POST) {
	unset($do_action);


	$do_action = true;
	$action = $_POST['action'];
	$raid = $_POST['raid'];
	$disk = $_POST['disk'];

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
	<li class="tabinact"><a href="disks_raid_gmirror.php"><?=_DISKSRAIDPHP_GMIRROR; ?></a></li>
	<li class="tabinact"><a href="disks_raid_gconcat.php"><?=_DISKSRAIDPHP_GCONCAT; ?></a></li> 
	<li class="tabinact"><a href="disks_raid_gstripe.php"><?=_DISKSRAIDPHP_GSTRIPE; ?></a></li>
	<li class="tabact"><?=_DISKSRAIDPHP_GRAID5; ?></li>
	<li class="tabinact"><a href="disks_raid_gvinum.php"><?=_DISKSRAIDPHP_GVINUM; ?><?=_DISKSRAIDPHP_UNSTABLE ;?></a></li> 
  </ul>
  </td></tr>
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="disks_raid_graid5.php"><?=_DISKSRAIDPHP_MANAGE; ?></a></li>
	<li class="tabact"><?=_DISKSRAIDPHP_TOOLS; ?></li>
	<li class="tabinact"><a href="disks_raid_graid5_info.php"><?=_DISKSRAIDPHP_INFO; ?></a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
<?php if ($input_errors) print_input_errors($input_errors); ?>
			<form action="disks_raid_graid5_tools.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
      <td valign="top" class="vncellreq"><?=_DISKSRAIDPHP_VOLUME; ?></td>
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
            <td valign="top" class="vncellreq"><?=_DISK;?></td>
             <td class="vtable">
             <select name="disk" class="formfld" id="disk"></select>
             </td>
          </tr>
				<tr> 
                  <td valign="top" class="vncellreq"><?=_DISKSRAIDTOOLSPHP_COMMAND;?></td>
                  <td class="vtable"> 
                    <select name="action" class="formfld" id="action">
                      <option value="list" <?php if ($action == "list") echo "selected"; ?>>list</option>
                      <option value="status" <?php if ($action == "status") echo "selected"; ?>>status</option>
                      <option value="insert" <?php if ($action == "insert") echo "selected"; ?>>insert</option>
                      <option value="remove" <?php if ($action == "remove") echo "selected"; ?>>remove</option>
                      <option value="clear" <?php if ($action == "clear") echo "selected"; ?>>clear</option>
                      <option value="stop" <?php if ($action == "stop") echo "selected"; ?>>stop</option>
			<option value="destroy" <?php if ($action == "destroy") echo "selected"; ?>>destroy</option>
			<option value="configure" <?php if ($action == "configure") echo "selected"; ?>>configure</option>
			<option value="dump" <?php if ($action == "dump") echo "selected"; ?>>dump</option>
                     </select>
                  </td>
                </tr>
				<tr>
				  <td width="22%" valign="top">&nbsp;</td>
				  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="<?=_DISKSRAIDTOOLSPHP_SENDCMD;?>">
				</td>
				</tr>
				<tr>
				<td valign="top" colspan="2">
				<? if ($do_action)
				{
					echo("<strong>" . _DISKSRAIDTOOLSPHP_INFO . "</strong><br>");
					echo('<pre>');
					ob_end_flush();
					
					//Remove the first 5 character of the diskname: /dev/
					$smalldisk = substr($disk, 5);
					
					
					if (strcmp($action,"insert") == 0 || strcmp($action,"remove")==0) {
						
						
						$cmd = "/sbin/graid5 $action " . escapeshellarg($raid) . " " . escapeshellarg($smalldisk);
					} else if (strcmp($action,"dump") == 0 || strcmp($action,"clear") == 0) {
						$cmd = "/sbin/graid5 $action " . escapeshellarg($smalldisk);
					} else {
						$cmd = "/sbin/graid5 $action " . escapeshellarg($raid);
					}

					
					system($cmd);
					echo('</pre>');
				}
				?>
				</td>
				</tr>
			</table>
</form>
<p><span class="vexpl"><span class="red"><strong><?=_WARNING;?>:</strong></span><br><?=_DISKSRAIDTOOLSPHP_TEXT;?></span></p>
</td></tr></table>
<script language="JavaScript">
<!--
raid_change();
//-->
</script>
<?php include("fend.inc"); ?>
