<?php
/*
  globals.inc
 	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.

	Based on m0n0wall (http://m0n0.ch/wall)
	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
	All rights reserved.

	Based on pfSense (http://www.pfsense.org)
	Copyright (C) 2006-2007 Daniel S. Haischt <me@daniel.stefan.haischt.name>.
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

$g = array(
    "varrun_path" => "/var/run",
    "varetc_path" => "/var/etc",
    "vardb_path" => "/var/db",
    "varlog_path" => "/var/log",
    "etc_path" => "/etc",
    "tmp_path" => "/tmp",
    "conf_path" => "/conf",
    "ftmp_path" => "/ftmp",
    "conf_default_path" => "/conf.default",
    "cf_path" => "/cf",
    "cf_conf_path" => "/cf/conf",
    "www_path" => "/usr/local/www",
    "xml_rootobj" => "freenas",
    "debug" => false,
    "latest_config" => "2.6",
    "nopccard_platforms" => array("wrap", "net48xx"),
    "wireless_regex" => "/^(wi|ath|an|awi|ral|iwi|ipw|ural)/",
    "default_passwd" => "freenas",
    "default_ip" => "192.168.1.250"
);

/* Available languages/locales */
$g_languages = array (
	"Bulgarian" => "bg_BG.UTF8",
	"Dutch" => "nl_BE.ISO8859-1",
	"English" => "en_US.ISO8859-1",
	"French" => "fr_FR.UTF8",
	"German" => "de_DE.ISO8859-1",
	"Greek" => "el_GR.UTF-8",
	"Italian" => "it_IT.ISO8859-1",
	"Japanese" => "ja_JP.UTF8",
	"Romanian" => "ro_RO.ISO8859-1",
	"Russian" => "ru_RU.UTF8",
	"SimplifiedChinese" => "zh_CN.UTF8",
	"Spanish" => "es_ES.ISO8859-1",
	"TraditionalChinese" => "zh_TW.UTF8",
	"Slovenian" => "sl_SI.UTF-8",
	"Portuguese" => "pt_PT.UTF-8",
	"Polish" => "pl_PL.UTF-8",
	"Hungarian" => "hu_HU.UTF-8",
	"Norwegian Bokmal" => "no_NB.UTF-8"
);

/* Supported filesystems */
$g_filesystems = array(
	"" => "Unformated",
	"ufs" => "UFS with Soft Updates",
	"ufs_no_su" => "UFS",
	"ufsgpt" => "UFS (GPT and Soft Updates)",
	"ufsgpt_no_su" => "UFS (EFI/GPT)",
	"msdos" => "FAT32",
	"cd9660" => "CD/DVD",
	"ntfs" => "NTFS",
	"ext2" => "EXT2",
	"softraid" => "Software RAID",
	"geli" => "Encrypted"
);
?>
