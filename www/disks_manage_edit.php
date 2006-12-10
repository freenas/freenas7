#!/usr/local/bin/php
<?php 
/*
	disks_manage_edit.php
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(_DISKSPHP_NAME,_DISK,isset($id)?_EDIT:_ADD);

/* get disk list (without CDROM) */
$disklist = get_physical_disks_list();

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();

disks_sort();

$a_disk = &$config['disks']['disk'];

if (isset($id) && $a_disk[$id])
{
	$pconfig['name'] = $a_disk[$id]['name'];
	$pconfig['harddiskstandby'] = $a_disk[$id]['harddiskstandby'];
	$pconfig['acoustic'] = $a_disk[$id]['acoustic'];
	$pconfig['fstype'] = $a_disk[$id]['fstype'];
	$pconfig['apm'] = $a_disk[$id]['apm'];
	$pconfig['udma'] = $a_disk[$id]['udma'];
	$pconfig['fullname'] = $a_disk[$id]['fullname'];
}

if ($_POST)
{
	unset($input_errors);
	$pconfig = $_POST;
		
	/* check for name conflicts */
	foreach ($a_disk as $disk)
	{
		if (isset($id) && ($a_disk[$id]) && ($a_disk[$id] === $disk))
			continue;

		if ($disk['name'] == $_POST['name'])
		{
			$input_errors[] = _DISKSMANAGEEDITPHP_MSGVALIDDUPLICATE;
			break;
		}
	}

	if (!$input_errors)
	{
		$disks = array();
		
		$devname = $_POST['name'];
		$devharddiskstandby = $_POST['harddiskstandby'];
		$harddiskacoustic = $_POST['acoustic'];
		$harddiskapm  = $_POST['apm'];
		$harddiskudma  = $_POST['udma'];
		$harddiskfstype = $_POST['fstype'];
		
		$disks['name'] = $devname;
		$disks['fullname'] = "/dev/$devname";
		$disks['harddiskstandby'] = $devharddiskstandby ;
		$disks['acoustic'] = $harddiskacoustic ;
		if ($harddiskfstype) $disks['fstype'] = $harddiskfstype ;
		$disks['apm'] = $harddiskapm ;
		$disks['udma'] = $harddiskudma ;
		$disks['type'] = $disklist[$devname]['type'];
		$disks['desc'] = $disklist[$devname]['desc'];
		$disks['size'] = $disklist[$devname]['size'];
		
		if (isset($id) && $a_disk[$id])
			$a_disk[$id] = $disks;
		else
			$a_disk[] = $disks;
		
		touch($d_diskdirty_path);
		
		disks_set_ataidle();
		write_config();
		
		header("Location: disks_manage.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
            <form action="disks_manage_edit.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr>
                  <td width="22%" valign="top" class="vncellreq"><?=_DISKSPHP_DISK; ?></td>
                  <td width="78%" class="vtable">
		<select name="name" class="formfld" id="name">
		  <?php foreach ($disklist as $diski => $diskv): ?>
		  <option value="<?=$diski;?>" <?php if ($diski == $pconfig['name']) echo "selected";?>> 
		  <?php echo htmlspecialchars($diski . ": " .$diskv['size'] . " (" . $diskv['desc'] . ")");				  
		  ?>
		  </option>
		  <?php endforeach; ?>
			</tr>
	<tr> 
                  <td width="22%" valign="top" class="vncell"><?=_DISKSMANAGEEDITPHP_UDMA; ?></td>
                  <td width="78%" class="vtable">
					<select name="udma" class="formfld" id="udma">
                      <?php $types = explode(",", "Auto,UDMA-33,UDMA-66,UDMA-100,UDMA-133");
					        $vals = explode(" ", "auto UDMA2 UDMA4 UDMA5 UDMA6");
					  $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                      <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['udma']) echo "selected";?>> 
                      <?=htmlspecialchars($types[$j]);?>
                      </option>
                      <?php endfor; ?>
                    </select>
                    <br>
                    <?=_DISKSMANAGEEDITPHP_UDMATEXT; ?></td>
				</tr>
			<tr> 
                  <td width="22%" valign="top" class="vncell"><?=_DISKSMANAGEEDITPHP_STANDBY; ?></td>
                  <td width="78%" class="vtable"> 
                    <select name="harddiskstandby" class="formfld">
					<?php $sbvals = array(0=>"Always on",5=>"5 minutes",10=>"10 minutes",20=>"20 minutes",30=>"30 minutes",60=>"60 minutes"); ?>
					<?php foreach ($sbvals as $sbval => $sbname): ?>
                      <option value="<?=$sbval;?>" <?php if($pconfig['harddiskstandby'] == $sbval) echo 'selected';?>><?=htmlspecialchars($sbname);?></option>
					<?php endforeach; ?>
                    </select>
                    <br>
                    <?=_DISKSMANAGEEDITPHP_STANDBYTEXT ;?></td>
				</tr>
		<tr> 
                  <td width="22%" valign="top" class="vncell"><?=_DISKSMANAGEEDITPHP_APM; ?></td>
                  <td width="78%" class="vtable"> 
                    <select name="apm" class="formfld">
					<?php $apmvals = array(0=>"Disabled",1=>"Minimum power usage with Standby",64=>"Medium power usage with Standby",128=>"Minimum power usage without Standby",192=>"Medium power usage without Standby",254=>"Maximum performance, maximum power usage"); ?>
					<?php foreach ($apmvals as $apmval => $apmname): ?>
                      <option value="<?=$apmval;?>" <?php if($pconfig['apm'] == $apmval) echo 'selected';?>><?=htmlspecialchars($apmname);?></option>
					<?php endforeach; ?>
                    </select>
                    <br>
                      <?=_DISKSMANAGEEDITPHP_APMTEXT; ?></td>
				</tr>
		<tr> 
                  <td width="22%" valign="top" class="vncell"><?=_DISKSMANAGEEDITPHP_ACLEVEL; ?></td>
                  <td width="78%" class="vtable"> 
                    <select name="acoustic" class="formfld">
					<?php $acvals = array(0=>"Disabled",1=>"Minimum performance, Minimum acoustic output",64=>"Medium acoustic output",127=>"Maximum performance, maximum acoustic output"); ?>
					<?php foreach ($acvals as $acval => $acname): ?>
                      <option value="<?=$acval;?>" <?php if($pconfig['acoustic'] == $acval) echo 'selected';?>><?=htmlspecialchars($acname);?></option>
					<?php endforeach; ?>
                    </select>
                    <br>
                    <?=_DISKSMANAGEEDITPHP_ACLEVELTEXT; ?></td>
				</tr>
		<tr> 
                  <td width="22%" valign="top" class="vncell"><?=_DISKSMANAGEEDITPHP_PREFS; ?></td>
                  <td width="78%" class="vtable"> 
                    <select name="fstype" class="formfld">
                      <?php $fstlist = get_fstype_list(); ?>
                      <?php foreach ($fstlist as $fstval => $fstname): ?>
                      <option value="<?=$fstval;?>" <?php if($pconfig['fstype'] == $fstval) echo 'selected';?>><?=htmlspecialchars($fstname);?></option>
                      <?php endforeach; ?>
                    </select>
                    <br>
                    <?=_DISKSMANAGEEDITPHP_PREFSTEXT; ?></td>
				</tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_disk[$id]))?_SAVE:_ADD?>"> 
                    <?php if (isset($id) && $a_disk[$id]): ?>
                    <input name="id" type="hidden" value="<?=$id;?>">
                    <?php endif; ?>
                  </td>
                </tr>
              </table>
</form>
<?php include("fend.inc"); ?>
