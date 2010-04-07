<?php
/*
	globals.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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
	"media_path" => "/mnt",
	"conf_default_path" => "/conf.default",
	"cf_path" => "/cf",
	"cf_conf_path" => "/cf/conf",
	"www_path" => "/usr/local/www",
	"xml_rootobj" => "freenas",
	"debug" => false,
	"latest_config" => "8.3",
	"nopccard_platforms" => array("wrap", "net48xx"),
	"wireless_regex" => "/^(ndis|wi|ath|an|ral|ural|wai|iwi|awi|wlan|ipw|wpi|rum|arl|zyd)/",
	"default_passwd" => "freenas",
	"default_ip" => "192.168.1.250"
);

$g_install = array(
	"part1size_embedded" => "64",
	"part1size_full" => "128"
);

/* Available languages/locales */
$g_languages = array (
	"bg"    => array("codeset" => "UTF-8", "desc" => gettext("Bulgarian")),
	"zh_CN" => array("codeset" => "UTF-8", "desc" => gettext("Chinese (Simplified)")),
	"zh_TW" => array("codeset" => "UTF-8", "desc" => gettext("Chinese (Traditional)")),
	"nl"    => array("codeset" => "UTF-8", "desc" => gettext("Dutch")),
	"en_US" => array("codeset" => "ISO-8859-1", "desc" => gettext("English")),
	"fr"    => array("codeset" => "UTF-8", "desc" => gettext("French")),
	"da"    => array("codeset" => "UTF-8", "desc" => gettext("Danish")),
	"de"    => array("codeset" => "UTF-8", "desc" => gettext("German")),
	"el"    => array("codeset" => "UTF-8", "desc" => gettext("Greek")),
	"hu"    => array("codeset" => "UTF-8", "desc" => gettext("Hungarian")),
	"it"    => array("codeset" => "UTF-8", "desc" => gettext("Italian")),
	"ja"    => array("codeset" => "UTF-8", "desc" => gettext("Japanese")),
	"no_NB" => array("codeset" => "UTF-8", "desc" => gettext("Norwegian (Bokmal)")),
	"pl"    => array("codeset" => "UTF-8", "desc" => gettext("Polish")),
	"pt"    => array("codeset" => "UTF-8", "desc" => gettext("Portuguese")),
	"ro"    => array("codeset" => "UTF-8", "desc" => gettext("Romanian")),
	"ru"    => array("codeset" => "UTF-8", "desc" => gettext("Russian")),
	"sl"    => array("codeset" => "UTF-8", "desc" => gettext("Slovenian")),
	"sq"    => array("codeset" => "UTF-8", "desc" => gettext("Albanian")),
	"es"    => array("codeset" => "UTF-8", "desc" => gettext("Spanish")),
	"sv"    => array("codeset" => "UTF-8", "desc" => gettext("Swedish")),
	"lv"    => array("codeset" => "UTF-8", "desc" => gettext("Latvian"))
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
	"geli" => "Encrypted",
	"zfs" => "ZFS storage pool device"
);

$g_file_upload_error = array(
	UPLOAD_ERR_OK => gettext("There is no error, the file uploaded with success."),
	UPLOAD_ERR_INI_SIZE => gettext("The uploaded file exceeds the upload_max_filesize directive in php.ini."),
	UPLOAD_ERR_FORM_SIZE => gettext("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form."),
	UPLOAD_ERR_PARTIAL => gettext("The uploaded file was only partially uploaded."),
	UPLOAD_ERR_NO_FILE => gettext("No file was uploaded."),
	UPLOAD_ERR_NO_TMP_DIR => gettext("Missing a temporary folder."),
	UPLOAD_ERR_CANT_WRITE => gettext("Failed to write file to disk."),
	UPLOAD_ERR_EXTENSION => gettext("File upload stopped by extension.")
);
?>
