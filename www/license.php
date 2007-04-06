#!/usr/local/bin/php
<?php 
require("guiconfig.inc");
$pgtitle = array(gettext("License")); 
?>
<?php include("fbegin.inc"); ?>
            <p><strong>FreeNAS is Copyright &copy; 2005-2007 by Olivier Cochard-Labbe 
              (<a href="mailto:olivier@freenas.org">olivier@freenas.org</a>).<br>
              All rights reserved.</strong></p>
			  <p>FreeNAS&reg is a registered trademark of Olivier Cochard-Labbe.</p>
		<p>FreeNAS is based on m0n0wall which is Copyright &copy; 2002-2007 by Manuel Kasper (mk@neon1.net).</p>
            <p> Redistribution and use in source and binary forms, with or without<br>
              modification, are permitted provided that the following conditions 
              are met:<br>
              <br>
              1. Redistributions of source code must retain the above copyright 
              notice,<br>
              this list of conditions and the following disclaimer.<br>
              <br>
              2. Redistributions in binary form must reproduce the above copyright<br>
              notice, this list of conditions and the following disclaimer in 
              the<br>
              documentation and/or other materials provided with the distribution.<br>
              <br>
              <strong>THIS SOFTWARE IS PROVIDED &quot;AS IS'' AND ANY EXPRESS 
              OR IMPLIED WARRANTIES,<br>
              INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY<br>
              AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT 
              SHALL THE<br>
              AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
              EXEMPLARY,<br>
              OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT 
              OF<br>
              SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR 
              BUSINESS<br>
              INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER 
              IN<br>
              CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)<br>
              ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
              OF THE<br>
              POSSIBILITY OF SUCH DAMAGE</strong>.</p>
            <hr size="1">
            <p>The following persons have contributed to FreeNAS code:</p>
             <p>Volker Theile (<a href="mailto:votdev@gmx.de">votdev@gmx.de</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">FreeNAS developer team member</font></em><br>
             <p>Stefan Hendricks (<a href="mailto:info@henmedia.de">info@henmedia.de</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Added some system information and processes page</font></em><br>
             <p>Mat Murdock (<a href="mailto:mmurdock@kimballequipment.com">mmurdock@kimballequipment.com</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Improved the Rsync client page and code</font></em><br> 
             <p>Vyatcheslav Tyulyukov<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Found and fixed some bugs: Software RAID display and beep</font></em><br>
              <p>Scott Zahn (<a href="mailto:scott@zahna.com">scott@zahna.com</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Create the buildingscript setupfreenas.sh</font></em><br> 
              <p>Aliet Santiesteban Sifontes (<a href="mailto:aliet@tesla.cujae.edu.cu">aliet@tesla.cujae.edu.cu</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Add Multilanguage support on the WebGUI</font></em><br>
              <p>Michael Mee (<a href="mailto:mm2001@pobox.com">mm2001@pobox.com</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Add Unison support</font></em><br>  
              <p>Niels Endres (<a href="mailto:niels@weaklogic.com">niels@weaklogic.com</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Added FAT support for config file and auto detection of NIC name</font></em><br>
            <br><br>
            <p>The following persons have contributed to FreeNAS documentation project:</p>
            <p>Bob Jaggard (<a href="mailto:rjaggard@bigpond.com">rjaggard@bigpond.com</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Beta tester and user manual contributor</font></em><br>
            <p>Regis Caubet (<a href="mailto:caubet.r@gmail.com">caubet.r@gmail.com</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">French translator of the user manual and WebGUI</font></em><br>
            <p>Yang Vfor (<a href="mailto:vforyang@gmail.com">vforyang@gmail.com</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Chinese translator of the user manual and Webmaster of <a href="http://www.freenas.cn">http://www.freenas.cn</a></font></em><br>
            <p>Pietro De Faccio (<a href="mailto:defaccio.pietro@tiscali.it">defaccio.pietro@tiscali.it</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Italian translator of the WebGUI</font></em><br>
            <p>Dominik Plaszewski (<a href="mailto:domme555@gmx.net">domme555@gmx.net</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">German translator of the WebGUI</font></em><br>
            <p>Falk Menzel (<a href="mailto:fmenzel@htwm.de">fmenzel@htwm.de</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">German translator of the WebGUI</font></em><br>
	   <p>Kris Verhoeven (<a href="mailto:kris@esiv.be">kris@esiv.be</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Dutch translator of the WebGUI</font></em><br>
	   <p>Baikuan Hsu (<a href="mailto:chicworks@gmail.com">chicworks@gmail.com</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Chinese translator of the WebGUI</font></em><br>
 <p>Laurentiu Florin Bubuianu (<a href="mailto:laurb@mail.dntis.ro">laurb@mail.dntis.ro</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Romana translator of the WebGUI</font></em><br>
 <p>Hiroyuki Seino (<a href="mailto:seichan-ml@wakhok.ne.jp">seichan-ml@wakhok.ne.jp</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Japanese translator of the WebGUI</font></em><br>
<p>Alexander Samoilov (<a href="mailto:root@lifeslice.ru">root@lifeslice.ru</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Russian translator of the WebGUI</font></em><br>  
			  
            <br><br>      
            <p>The following persons have contributed to FreeNAS Website:</p>
            <p>Youri Trioreau (<a href="mailto:youri.trioreau@no-log.org">youri.trioreau@no-log.org</a>)<br>
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Webmaster</font></em><br>
             	  <hr size="1">
      <p>FreeNAS is based upon/includes various free software packages, listed 
        below.<br>
        The author of FreeNAS would like to thank the authors of these software 
        packages for their efforts.</p>
      <p>FreeBSD (<a href="http://www.freebsd.org" target="_blank">http://www.freebsd.org</a>)<br>
        Copyright &copy; 1995-2007 The FreeBSD Project. All rights reserved.</p>
	<p>Unofficial <a href="http://home.tiscali.de/cmdr_faako/geom_raid5.tbz">FreeBSD geom raid 5 module</a><br>
        Copyright &copy; 2006-2007 Arne Woerner (<a href="mailto:arne_woerner@yahoo.com">arne_woerner@yahoo.com</a>).</p>

      <p> PHP (<a href="http://www.php.net" target="_blank">http://www.php.net</a>).<br>
        Copyright &copy; 2001-2007 The PHP Group. All rights reserved.</p>
      <p> Lighttpd (<a href="http://www.lighttpd.net" target="_blank">http://www.lighttpd.net</a>)<br>
        Copyright &copy; 2004 by Jan Kneschke &lt;jan@kneschke.de&gt;. All rights reserved.</p>
      
      <p> OpenSSH (<a href="http://www.openssh.com" target="_blank">http://www.openssh.com</a>)<br>
        Copyright &copy; 1999-2007 OpenBSD</p>
        
      <p> Samba (<a href="http://www.samba.org" target="_blank">http://www.samba.org</a>)<br>
       </p>
       
      <p> Rsync (<a href="http://www.samba.org/rsync" target="_blank">http://www.samba.org/rsync</a>)<br>
       </p>
      
      <p> Pure-FTPd (<a href="http://www.pureftpd.org" target="_blank">http://www.pureftpd.org</a>)<br>
      </p>
        
      <p> Netatalk (<a href="http://netatalk.sourceforge.net" target="_blank">http://netatalk.sourceforge.net</a>)<br>
        Copyright &copy; 1990,1996 Regents of The University of Michigan</p>
        
       <p> Apple Bonjour (<a href="http://developer.apple.com/networking/bonjour" target="_blank">http://developer.apple.com/networking/bonjour</a>)<br>
        Apple Public Source License.</p>
     
      <p> Circular log support for FreeBSD syslogd (<a href="http://software.wwwi.com/syslogd" target="_blank">http://software.wwwi.com/syslogd</a>)<br>
        Copyright &copy; 2001 Jeff Wheelhouse (jdw@wwwi.com)</p>
     
      <p>msntp (<a href="http://www.hpcf.cam.ac.uk/export" target="_blank">http://www.hpcf.cam.ac.uk/export</a>)<br>
        Copyright &copy; 1996, 1997, 2000 N.M. Maclaren, University of Cambridge. 
        All rights reserved.</p>
      
	  <p>ataidle (<a href="http://www.cran.org.uk/bruce/software/ataidle.php" target="_blank">http://www.cran.org.uk/bruce/software/ataidle.php</a>)<br>
Copyright  &copy; 2004-2005 Bruce Cran &lt;bruce@cran.org.uk&gt;. All rights reserved.</p>

	  <p>smartmontools (<a href="http://smartmontools.sourceforge.net" target="_blank">http://smartmontools.sourceforge.net</a>)<br>
Copyright  &copy; 2002-2005 Bruce Allen.</p>

	  <p>iSCSI initiator (<a href="ftp://ftp.cs.huji.ac.il/users/danny/freebsd" target="_blank">ftp://ftp.cs.huji.ac.il/users/danny/freebsd</a>)<br>
Copyright &copy; 2005 Daniel Braniss (danny@cs.huji.ac.il).</p>

 <p>NetBSD iSCSI-target (<a href="http://www.netbsd.org/" target="_blank">http://www.netbsd.org/</a>)<br>
	  Copyright &copy; 1994-2005 The NetBSD Foundation, Inc. All rights reserved.
      </p>

    <p>dp.SyntaxHighlighter (<a href="http://www.dreamprojections.com/SyntaxHighlighter" target="_blank">http://www.dreamprojections.com/SyntaxHighlighter</a>)<br>
      Copyright &copy; 2004-2006 Alex Gorbatchev. All rights reserved.</p>    

    <p>uShare UPnP A/V Media Server (<a href="http://ushare.geexbox.org" target="_blank">http://ushare.geexbox.org</a>)<br>
      Copyright &copy; 2005-2006 Benjamin Zores. All rights reserved.</p>
	  
	 <p>NTFS-3G driver (<a href="http://www.ntfs-3g.org/" target="_blank">http://www.ntfs-3g.org/</a>)<br>
      from Szabolcs Szakacsits.</p>
	  
	   <p>Fuse - Filesystem in Userspace (<a href="http://fuse.sourceforge.net/" target="_blank">http://fuse.sourceforge.net/</a>)<br>
      </p>
		<p>e2fsprogs (<a href="http://e2fsprogs.sourceforge.net" target="_blank">http://e2fsprogs.sourceforge.net</a>)<br>
      Copyright &copy; 1994-2006 Theodore Ts'o. All rights reserved.
		</p>

		<p>INADYN - Simple Dynamic DNS client (<a href="http://inadyn.ina-tech.net" target="_blank">http://inadyn.ina-tech.net</a>)<br>
      Copyright &copy; 2003-2004 Narcis Ilisei. All rights reserved.</p>

	  <p>pfSense: FreeNAS use some pfSense code too (<a href="http://www.pfsense.com/" target="_blank">http://www.pfsense.com/</a>)<br>
	  Copyright &copy; 2004, 2005, 2006 Scott Ullrich. All rights reserved.
      </p>

<?php include("fend.inc"); ?>
