#!/usr/local/bin/php
<?php
/*
	license.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2010 Olivier Cochard <olivier@freenas.org>.
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
// Configure page permission
$pgperm['allowuser'] = TRUE;

require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("Help"), gettext("License & Credits"));
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabcont">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<?php html_titleline(gettext("License"));?>
				<tr>
					<td class="listt">
            <p><strong>FreeNAS is Copyright &copy; 2005-2010 by Olivier Cochard-Labbe
              (<a href="mailto:olivier@freenas.org">olivier@freenas.org</a>).<br />
              All rights reserved.</strong></p>
			  <p>FreeNAS&reg; is a registered trademark of Olivier Cochard-Labbe.</p>
		<p>FreeNAS is based on m0n0wall which is Copyright &copy; 2002-2007 by Manuel Kasper (mk@neon1.net).</p>
	<p>FreeNAS code and documentation are released under the BSD license, under terms as follows:</p>
            <p> Redistribution and use in source and binary forms, with or without<br />
              modification, are permitted provided that the following conditions
              are met:<br />
              <br />
              1. Redistributions of source code must retain the above copyright
              notice,<br />
              this list of conditions and the following disclaimer.<br />
              <br />
              2. Redistributions in binary form must reproduce the above copyright<br />
              notice, this list of conditions and the following disclaimer in
              the<br />
              documentation and/or other materials provided with the distribution.<br />
              <br />
              <strong>THIS SOFTWARE IS PROVIDED &quot;AS IS'' AND ANY EXPRESS
              OR IMPLIED WARRANTIES,<br />
              INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY<br />
              AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
              SHALL THE<br />
              AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
              EXEMPLARY,<br />
              OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT
              OF<br />
              SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
              BUSINESS<br />
              INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER
              IN<br />
              CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)<br />
              ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
              OF THE<br />
              POSSIBILITY OF SUCH DAMAGE</strong>.</p>
							</td>
						</tr>
            <?php html_separator();?>
            <?php html_titleline(gettext("Credits"));?>
            <tr>
            	<td class="listt">
            <p>The following persons have contributed to FreeNAS code:</p>
             <div>Volker Theile (<a href="mailto:votdev@gmx.de">votdev@gmx.de</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Developer and project leader</font></em></div><br />
              <div>Daisuke Aoyama (<a href="mailto:aoyama@peach.ne.jp">aoyama@peach.ne.jp</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Developer and adapt iSCSI Target WebGUI to istgt</font></em></div><br />
             <div>Michael Zoon (<a href="mailto:ma.zoon@quicknet.nl">ma.zoon@quicknet.nl</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Developer and Dutch translator of the WebGUI</font></em></div><br />
             <div>Stefan Hendricks (<a href="mailto:info@henmedia.de">info@henmedia.de</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Added some system information and processes page</font></em></div><br />
             <div>Mat Murdock (<a href="mailto:mmurdock@kimballequipment.com">mmurdock@kimballequipment.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Improved the Rsync client page and code</font></em></div><br />
             <div>Vyatcheslav Tyulyukov<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Found and fixed some bugs: Software RAID display and beep</font></em></div><br />
              <div>Scott Zahn (<a href="mailto:scott@zahna.com">scott@zahna.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Create the buildingscript setupfreenas.sh</font></em></div><br />
              <div>Aliet Santiesteban Sifontes (<a href="mailto:aliet@tesla.cujae.edu.cu">aliet@tesla.cujae.edu.cu</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Add Multilanguage support on the WebGUI</font></em></div><br />
              <div>Michael Mee (<a href="mailto:mm2001@pobox.com">mm2001@pobox.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Add Unison support</font></em></div><br />
              <div>Niels Endres (<a href="mailto:niels@weaklogic.com">niels@weaklogic.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Added FAT support for config file and auto detection of NIC name</font></em></div><br />
              <div>Dan Merschi (<a href="mailto:dan@freenaskb.info">dan@freenaskb.info</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Created core implementation of email status report</font></em></div><br />
              <div>Gerard Hickey (<a href="mailto:hickey@kinetic-compute.com">hickey@kinetic-compute.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Patches for userdb script &amp; AFP/Rsync shares</font></em></div><br />
              <div>Nelson Silva (<a href="mailto:nelson.emanuel.silva@gmail.com">nelson.emanuel.silva@gmail.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Add core ZFS implementation and additional code improvements</font></em></div><br />
            <hr size="1" />
            <p>The following persons have contributed to FreeNAS documentation project:</p>
            <div>Bob Jaggard (<a href="mailto:rjaggard@bigpond.com">rjaggard@bigpond.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Beta tester and user manual contributor</font></em></div><br />
            <div>Regis Caubet (<a href="mailto:caubet.r@gmail.com">caubet.r@gmail.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">French translator of the user manual and WebGUI</font></em></div><br />
            <div>Yang Vfor (<a href="mailto:vforyang@gmail.com">vforyang@gmail.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Chinese translator of the user manual and Webmaster of <a href="http://www.freenas.cn">http://www.freenas.cn</a></font></em></div><br />
            <div>Pietro De Faccio (<a href="mailto:defaccio.pietro@tiscali.it">defaccio.pietro@tiscali.it</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Italian translator of the WebGUI</font></em></div><br />
            <div>Dominik Plaszewski (<a href="mailto:domme555@gmx.net">domme555@gmx.net</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">German translator of the WebGUI</font></em></div><br />
            <div>Falk Menzel (<a href="mailto:fmenzel@htwm.de">fmenzel@htwm.de</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">German translator of the WebGUI</font></em></div><br />
            <div>Kris Verhoeven (<a href="mailto:kris@esiv.be">kris@esiv.be</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Dutch translator of the WebGUI</font></em></div><br />
            <div>Baikuan Hsu (<a href="mailto:chicworks@gmail.com">chicworks@gmail.com</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Chinese translator of the WebGUI</font></em></div><br />
            <div>Laurentiu Florin Bubuianu (<a href="mailto:laurb@mail.dntis.ro">laurb@mail.dntis.ro</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Romana translator of the WebGUI</font></em></div><br />
            <div>Hiroyuki Seino (<a href="mailto:seichan-ml@wakhok.ne.jp">seichan-ml@wakhok.ne.jp</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Japanese translator of the WebGUI</font></em></div><br />
            <div>Alexander Samoilov (<a href="mailto:root@lifeslice.ru">root@lifeslice.ru</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Russian translator of the WebGUI</font></em></div><br />
            <div>Ioannis Koniaris (<a href="mailto:ikoniari@csd.auth.gr">ikoniari@csd.auth.gr</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Greek translator of the WebGUI</font></em></div><br />
            <div>Daniel Nylander (<a href="mailto:po@danielnylander.se">po@danielnylander.se</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Swedish translator of the WebGUI</font></em></div><br />
            <hr size="1" />
            <p>The following persons have contributed to FreeNAS Website:</p>
            <div>Youri Trioreau (<a href="mailto:youri.trioreau@no-log.org">youri.trioreau@no-log.org</a>)<br />
              &nbsp;&nbsp;&nbsp;&nbsp;<em><font color="#666666">Webmaster</font></em></div><br />
							</td>
						</tr>
            <?php html_separator();?>
            <?php html_titleline(gettext("Software used"));?>
            <tr>
            	<td class="listt">
      <p>FreeNAS is based upon/includes various free software packages, listed
        below.<br />
        The authors of FreeNAS would like to thank the authors of these software
        packages for their efforts.</p>
      <p>FreeBSD (<a href="http://www.freebsd.org" target="_blank">http://www.freebsd.org</a>)<br />
        Copyright &copy; 1995-2010 The FreeBSD Project. All rights reserved.</p>

      <p>Unofficial <a href="http://wgboome.homepage.t-online.de/geom_raid5.tbz">FreeBSD GEOM RAID5 module</a><br />
        Copyright &copy; 2006-2007 Arne Woerner (<a href="mailto:arne_woerner@yahoo.com">arne_woerner@yahoo.com</a>).</p>

      <p> PHP (<a href="http://www.php.net" target="_blank">http://www.php.net</a>).<br />
        Copyright &copy; 2001-2010 The PHP Group. All rights reserved.</p>

      <p> Lighttpd (<a href="http://www.lighttpd.net" target="_blank">http://www.lighttpd.net</a>)<br />
        Copyright &copy; 2004 by Jan Kneschke &lt;jan@kneschke.de&gt;. All rights reserved.</p>

      <p> OpenSSH (<a href="http://www.openssh.com" target="_blank">http://www.openssh.com</a>)<br />
        Copyright &copy; 1999-2009 OpenBSD</p>

      <p> Samba (<a href="http://www.samba.org" target="_blank">http://www.samba.org</a>)<br />
       </p>

      <p> Rsync (<a href="http://www.samba.org/rsync" target="_blank">http://www.samba.org/rsync</a>)<br />
       </p>

      <p> ProFTPD - Highly configurable FTP server (<a href="http://www.proftpd.org" target="_blank">http://www.proftpd.org</a>)<br />
        Copyright &copy; 1999, 2000-10, The ProFTPD Project</p>

      <p>tftp-hpa (<a href="http://www.kernel.org/pub/software/network/tftp" target="_blank">http://www.kernel.org/pub/software/network/tftp</a>)<br />
       </p>

      <p> Netatalk (<a href="http://netatalk.sourceforge.net" target="_blank">http://netatalk.sourceforge.net</a>)<br />
        Copyright &copy; 1990,1996 Regents of The University of Michigan</p>

       <p> Apple Bonjour (<a href="http://developer.apple.com/networking/bonjour" target="_blank">http://developer.apple.com/networking/bonjour</a>)<br />
        Apple Public Source License.</p>

      <p> Circular log support for FreeBSD syslogd (<a href="http://software.wwwi.com/syslogd" target="_blank">http://software.wwwi.com/syslogd</a>)<br />
        Copyright &copy; 2001 Jeff Wheelhouse (jdw@wheelhouse.org)</p>

	<p>ataidle (<a href="http://www.cran.org.uk/bruce/software/ataidle.php" target="_blank">http://www.cran.org.uk/bruce/software/ataidle.php</a>)<br />
        Copyright &copy; 2004-2005 Bruce Cran &lt;bruce@cran.org.uk&gt;. All rights reserved.</p>

      <p>smartmontools (<a href="http://sourceforge.net/projects/smartmontools" target="_blank">http://sourceforge.net/projects/smartmontools</a>)<br />
        Copyright &copy; 2002-2008 Bruce Allen.</p>

      <p>iSCSI initiator (<a href="ftp://ftp.cs.huji.ac.il/users/danny/freebsd" target="_blank">ftp://ftp.cs.huji.ac.il/users/danny/freebsd</a>)<br />
        Copyright &copy; 2005-2010 Daniel Braniss (danny@cs.huji.ac.il).</p>

      <p>istgt (<a href="http://shell.peach.ne.jp/aoyama" target="_blank">http://shell.peach.ne.jp/aoyama</a>)<br />
        Copyright &copy; 2008-2010 Daisuke Aoyama (aoyama@peach.ne.jp). All rights reserved.</p>

      <p>dp.SyntaxHighlighter (<a href="http://code.google.com/p/syntaxhighlighter" target="_blank">http://code.google.com/p/syntaxhighlighter</a>)<br />
        Copyright &copy; 2004-2007 Alex Gorbatchev. All rights reserved.</p>

      <p>FUPPES - Free UPnP Entertainment Service (<a href="http://fuppes.ulrich-voelkel.de" target="_blank">http://fuppes.ulrich-voelkel.de</a>)<br />
        Copyright &copy; 2005 - 2009 Ulrich V&ouml;lkel (u-voelkel@users.sourceforge.net).</p>

      <p>mt-daapd - Multithread daapd Apple iTunes server (<a href="http://www.fireflymediaserver.org" target="_blank">http://www.fireflymediaserver.org</a>)<br />
        Copyright &copy; 2003 Ron Pedde (ron@pedde.com).</p>

      <p>NTFS-3G driver (<a href="http://www.ntfs-3g.org" target="_blank">http://www.ntfs-3g.org</a>)<br />
        from Szabolcs Szakacsits.</p>

      <p>Fuse - Filesystem in Userspace (<a href="http://fuse.sourceforge.net" target="_blank">http://fuse.sourceforge.net</a>)<br />
       </p>

      <p>e2fsprogs (<a href="http://e2fsprogs.sourceforge.net" target="_blank">http://e2fsprogs.sourceforge.net</a>)<br />
        Copyright &copy; 1994-2006 Theodore Ts'o. All rights reserved.</p>

      <p>inadyn-mt - Simple Dynamic DNS client (<a href="http://sourceforge.net/projects/inadyn-mt" target="_blank">http://sourceforge.net/projects/inadyn-mt</a>)<br />
        Inadyn Copyright &copy; 2003-2004 Narcis Ilisei. All rights reserved.<br />
        Inadyn-mt Copyright &copy; 2007 Bryan Hoover (bhoover@wecs.com).</p>

      <p>XMLStarlet Command Line XML Toolkit (<a href="http://xmlstar.sourceforge.net" target="_blank">http://xmlstar.sourceforge.net</a>)<br />
        Copyright &copy; 2002 Mikhail Grushinskiy. All rights reserved.</p>

      <p>sipcalc (<a href="http://www.routemeister.net/projects/sipcalc" target="_blank">http://www.routemeister.net/projects/sipcalc</a>)<br />
        Copyright &copy; 2003 Simon Ekstrand. All rights reserved.</p>

      <p>msmtp - An SMTP client with a sendmail compatible interface (<a href="http://msmtp.sourceforge.net" target="_blank">http://msmtp.sourceforge.net</a>)<br />
        Copyright &copy; 2008 Martin Lambers and others.</p>

      <p>cdialog - Display simple dialog boxes from shell scripts (<a href="http://invisible-island.net/dialog" target="_blank">http://invisible-island.net/dialog</a>)<br />
        Copyright &copy; 2000-2006, 2007 Thomas E. Dickey.</p>

      <p>host - An utility to query DNS servers<br />
        Rewritten by Eric Wassenaar, Nikhef-H, (e07@nikhef.nl).</p>

      <p>Transmission - Transmission is a fast, easy, and free multi-platform BitTorrent client (<a href="http://www.transmissionbt.com" target="_blank">http://www.transmissionbt.com</a>)<br />
        Copyright &copy; Transmission Project 2008-2010. All rights reserved.</p>

      <p>QuiXplorer - Web-based file-management (<a href="http://quixplorer.sourceforge.net" target="_blank">http://quixplorer.sourceforge.net</a>)<br />
        Copyright &copy; Felix C. Stegerman. All rights reserved.</p>

      <p>pfSense: FreeNAS use some pfSense code too (<a href="http://www.pfsense.com" target="_blank">http://www.pfsense.com</a>)<br />
        Copyright &copy; 2004, 2005, 2006 Scott Ullrich. All rights reserved.</p>
		
      <p>Some of the software used are under the <a href="gpl-license.txt">GNU General Public License (GPL)</a>, <a href="lgpl-license.txt">GNU Lesser General Public License (LGPL)</a>, <a href="apple-license.txt">Apple Public Source License</a> and <a href="php-license.txt">PHP License</a>.</p>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
