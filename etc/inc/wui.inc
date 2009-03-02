<?php
/*
	wui.inc
	Copyright (c) 2006-2009 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require_once("config.inc");
require_once("array.inc");

class HTMLBaseControl {
	var $_ctrlname = "";
	var $_title = "";
	var $_description = "";
	var $_value;
	var $_required = false;
	var $_readonly = false;

	public function HTMLBaseControl($ctrlname, $title, $value, $description = "") {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetDescription($description);
		$this->SetValue($value);
	}

	function IsRequired() {
		return $this->_required;
	}

	function SetRequired($bool) {
		$this->_required = $bool;
	}

	function IsReadOnly() {
		return $this->_readonly;
	}

	function SetReadOnly($bool) {
		$this->_readonly = $bool;
	}

	function GetValue() {
		return $this->_value;
	}

	function SetValue($value) {
		$this->_value = $value;
	}

	function GetDescription() {
		return $this->_description;
	}

	function SetDescription($description) {
		$this->_description = $description;
	}

	function GetTitle() {
		return $this->_title;
	}

	function SetTitle($title) {
		$this->_title = $title;
	}

	function GetCtrlName() {
		return $this->_ctrlname;
	}

	function SetCtrlName($name) {
		$this->_ctrlname = $name;
	}

	function GetClass() {
		$class = "vncell";
		if (true === $this->IsRequired())
			$class = "vncellreq";
		return $class;
	}

	function Render() {
		$ctrlname = $this->GetCtrlName();
		$title = $this->GetTitle();
		$class = $this->GetClass();
		$description = $this->GetDescription();

		echo "<tr id='{$ctrlname}_tr'>\n";
		echo "	<td width='22%' valign='top' class='{$class}'>{$title}</td>\n";
		echo "	<td width='78%' class='vtable'>\n";
		$this->RenderCtrl();
		if (!empty($description))
			echo "		<br/><span class='vexpl'>{$description}</span>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	}

	function RenderCtrl() {
	}
}

class HTMLBaseControlJS extends HTMLBaseControl {
	var $_onclick = "";

	function SetJSonClick($code) {
		$this->_onclick = $code;
	}

	function GetJSonClick() {
		return $this->_onclick;
	}
}

class HTMLEditBox extends HTMLBaseControl {
	var $_size = 40;

	function HTMLEditBox($ctrlname, $title, $value, $description, $size) {
		$this->HTMLBaseControl($ctrlname, $title, $value, $description);
		$this->SetSize($size);
	}

	function GetSize() {
		return $this->_size;
	}

	function SetSize($size) {
		$this->_size = $size;
	}

	function GetParam() {
		$param = "";
		if (true === $this->IsReadOnly())
			$param .= "readonly ";
		return $param;
	}

	function RenderCtrl() {
		$ctrlname = $this->GetCtrlName();
		$value = htmlspecialchars($this->GetValue());
		$size = $this->GetSize();
		$param = $this->GetParam();

		echo "		<input name='{$ctrlname}' type='text' class='formfld' id='{$ctrlname}' size='{$size}' value=\"{$value}\" {$param}>\n";
	}
}

class HTMLPasswordBox extends HTMLEditBox {
	function HTMLPasswordBox($ctrlname, $title, $value, $description, $size) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetDescription($description);
		$this->SetSize($size);
	}

	function RenderCtrl() {
		$ctrlname = $this->GetCtrlName();
		$value = htmlspecialchars($this->GetValue());
		$size = $this->GetSize();
		$param = $this->GetParam();

		echo "		<input name='{$ctrlname}' type='password' class='formfld' id='{$ctrlname}' size='{$size}' value='{$value}' {$param}>\n";
	}
}

class HTMLPasswordConfBox extends HTMLEditBox {
	var $_ctrlnameconf = "";
	var $_valueconf = "";

	function HTMLPasswordConfBox($ctrlname, $ctrlnameconf, $title, $value, $valueconf, $description, $size) {
		$this->SetCtrlName($ctrlname);
		$this->SetCtrlNameConf($ctrlnameconf);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetValueConf($value);
		$this->SetDescription($description);
		$this->SetSize($size);
	}

	function GetCtrlNameConf() {
		return $this->_ctrlnameconf;
	}

	function SetCtrlNameConf($name) {
		$this->_ctrlnameconf = $name;
	}

	function GetValueConf() {
		return $this->_valueconf;
	}

	function SetValueConf($value) {
		$this->_valueconf = $value;
	}

	function RenderCtrl() {
		$ctrlname = $this->GetCtrlName();
		$ctrlnameconf = $this->GetCtrlNameConf();
		$value = htmlspecialchars($this->GetValue());
		$valueconf = htmlspecialchars($this->GetValueConf());
		$size = $this->GetSize();
		$param = $this->GetParam();
		$caption = gettext("Confirmation");

		echo "		<input name='{$ctrlname}' type='password' class='formfld' id='{$ctrlname}' size='{$size}' value='{$value}' {$param}><br/>\n";
		echo "    <input name='{$ctrlnameconf}' type='password' class='formfld' id='{$ctrlnameconf}' size='{$size}' value='{$valueconf}' {$param}>&nbsp;({$caption})\n";
	}
}

class HTMLTextArea extends HTMLEditBox {
	var $_columns = 40;
	var $_rows = 5;
	var $_wrap = true;

	function HTMLTextArea($ctrlname, $title, $value, $description, $columns, $rows) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetDescription($description);
		$this->SetColumns($columns);
		$this->SetRows($rows);
	}

	function GetColumns() {
		return $this->_columns;
	}

	function SetColumns($columns) {
		$this->_columns = $columns;
	}

	function GetRows() {
		return $this->_rows;
	}

	function SetRows($rows) {
		$this->_rows = $rows;
	}

	function IsWrap() {
		return $this->_wrap;
	}

	function SetWrap($bool) {
		$this->_wrap = $bool;
	}

	function GetParam() {
		$param = parent::GetParam();
		if (false === $this->IsWrap())
			$param .= " wrap='off'";
		return $param;
	}

	function RenderCtrl() {
		$ctrlname = $this->GetCtrlName();
		$value = htmlspecialchars($this->GetValue());
		$columns = $this->GetColumns();
		$rows = $this->GetRows();
		$param = $this->GetParam();

		echo "		<textarea name='{$ctrlname}' cols='{$columns}' rows='{$rows}' id='{$ctrlname}' class='formpre' {$param}>{$value}</textarea>\n";
	}
}

class HTMLFileChooser extends HTMLEditBox {
	var $_path = "";

	function HTMLFileChooser($ctrlname, $title, $value, $description, $size = 60) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetDescription($description);
		$this->SetSize($size);
	}

	function GetPath() {
		return $this->_path;
	}

	function SetPath($path) {
		$this->_path = $path;
	}

	function RenderCtrl() {
		$ctrlname = $this->GetCtrlName();
		$value = htmlspecialchars($this->GetValue());
		$size = $this->GetSize();
		$param = $this->GetParam();
		$path = $this->GetPath();

		echo "		<input name='{$ctrlname}' type='text' class='formfld' id='{$ctrlname}' size='{$size}' value='{$value}' {$param}>\n";
		echo "		<input name='fcbrowse' type='button' class='formbtn' id='fcbrowse' onClick='ifield = form.{$ctrlname}; filechooser = window.open(\"filechooser.php?p=\"+escape(ifield.value)+\"&sd={$path}\", \"filechooser\", \"scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300\"); filechooser.ifield = ifield; window.ifield = ifield;' value='...'\>\n";
	}
}

class HTMLIPAddressBox extends HTMLEditBox {
	var $_ctrlnamenetmask = "";
	var $_valuenetmask = "";

	function HTMLIPAddressBox($ctrlname, $ctrlnamenetmask, $title, $value, $valuenetmask, $description) {
		$this->SetCtrlName($ctrlname);
		$this->SetCtrlNameNetmask($ctrlnamenetmask);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetValueNetmask($valuenetmask);
		$this->SetDescription($description);
	}

	function GetCtrlNameNetmask() {
		return $this->_ctrlnamenetmask;
	}

	function SetCtrlNameNetmask($name) {
		$this->_ctrlnamenetmask = $name;
	}

	function GetValueNetmask() {
		return $this->_valuenetmask;
	}

	function SetValueNetmask($value) {
		$this->_valuenetmask = $value;
	}
}

class HTMLIPv4AddressBox extends HTMLIPAddressBox {
	function HTMLIPv4AddressBox($ctrlname, $ctrlnamenetmask, $title, $value, $valuenetmask, $description) {
		$this->HTMLIPAddressBox($ctrlname, $ctrlnamenetmask, $title, $value, $valuenetmask, $description);
		$this->SetSize(20);
	}

	function RenderCtrl() {
		$ctrlname = $this->GetCtrlName();
		$ctrlnamenetmask = $this->GetCtrlNameNetmask();
		$value = htmlspecialchars($this->GetValue());
		$valuenetmask = htmlspecialchars($this->GetValueNetmask());
		$size = $this->GetSize();

		echo "    <input name='{$ctrlname}' type='text' class='formfld' id='{$ctrlname}' size='{$size}' value='{$value}'>\n";
		echo "    /\n";
		echo "    <select name='{$ctrlnamenetmask}' class='formfld' id='{$ctrlnamenetmask}'>\n";
		foreach (range(1, 32) as $netmask) {
			$optparam = "";
			if ($netmask == $valuenetmask)
				$optparam .= "selected ";
			echo "      <option value='{$netmask}' {$optparam}>{$netmask}</option>\n";
		}
		echo "    </select>\n";
	}
}

class HTMLIPv6AddressBox extends HTMLIPAddressBox {
	function HTMLIPv6AddressBox($ctrlname, $ctrlnamenetmask, $title, $value, $valuenetmask, $description) {
		$this->HTMLIPAddressBox($ctrlname, $ctrlnamenetmask, $title, $value, $valuenetmask, $description);
		$this->SetSize(30);
	}

	function RenderCtrl() {
		$ctrlname = $this->GetCtrlName();
		$ctrlnamenetmask = $this->GetCtrlNameNetmask();
		$value = htmlspecialchars($this->GetValue());
		$valuenetmask = htmlspecialchars($this->GetValueNetmask());
		$size = $this->GetSize();

		echo "    <input name='{$ctrlname}' type='text' class='formfld' id='{$ctrlname}' size='{$size}' value='{$value}'>\n";
		echo "    /\n";
		echo "    <input name='{$ctrlnamenetmask}' type='text' class='formfld' id='{$ctrlnamenetmask}' size='2' value='{$valuenetmask}'>\n";
	}
}

class HTMLCheckBox extends HTMLBaseControlJS {
	var $_caption = "";

	function HTMLCheckBox($ctrlname, $title, $value, $caption, $description = "") {
		$this->HTMLBaseControl($ctrlname, $title, $value, $description);
		$this->SetCaption($caption);
	}

	function GetCaption() {
		return $this->_caption;
	}

	function SetCaption($caption) {
		$this->_caption = $caption;
	}

	function IsChecked() {
		return $this->GetValue();
	}

	function SetChecked($bool) {
		$this->SetValue($bool);
	}

	function GetParam() {
		$param = "";
		if (true === $this->IsChecked())
			$param .= "checked ";
		$onclick = $this->GetJSonClick();
		if (!empty($onclick))
			$param .= "onClick='{$onclick}' ";
		return $param;
	}

	function RenderCtrl() {
		$ctrlname = $this->GetCtrlName();
		$caption = $this->GetCaption();
		$description = $this->GetDescription();
		$param = $this->GetParam();

		echo "		<input name='{$ctrlname}' type='checkbox' class='formfld' id='{$ctrlname}' value='yes' {$param}>&nbsp;{$caption}\n";
	}
}

class HTMLSelectControl extends HTMLBaseControlJS {
	var $_ctrlclass = "";
	var $_options = array();

	function HTMLSelectControl($ctrlclass, $ctrlname, $title, $value, $options, $description) {
		$this->HTMLBaseControl($ctrlname, $title, $value, $description);
		$this->SetCtrlClass($ctrlclass);
		$this->SetOptions($options);
	}

	function GetCtrlClass() {
		return $this->_ctrlclass;
	}

	function SetCtrlClass($ctrlclass) {
		$this->_ctrlclass = $ctrlclass;
	}

	function GetOptions() {
		return $this->_options;
	}

	function SetOptions($options) {
		$this->_options = $options;
	}

	function GetParam() {
		$param = "";
		if (true === $this->IsReadOnly())
			$param .= "disabled ";
		$onclick = $this->GetJSonClick();
		if (!empty($onclick))
			$param .= "onClick='{$onclick}' ";
		return $param;
	}

	function RenderCtrl() {
		$ctrlclass = $this->GetCtrlClass();
		$ctrlname = $this->GetCtrlName();
		$value = htmlspecialchars($this->GetValue());
		$param = $this->GetParam();
		$options = $this->GetOptions();

		echo "    <select name='{$ctrlname}' class='{$ctrlclass}' id='{$ctrlname}' {$param}>\n";
		foreach ($options as $optionk => $optionv) {
			$optparam = "";
			if ($value == $optionk)
				$optparam .= "selected ";
			echo "      <option value='{$optionk}' {$optparam}>{$optionv}</option>\n";
		}
		echo "    </select>\n";
	}
}

class HTMLMultiSelectControl extends HTMLSelectControl {
	function HTMLMultiSelectControl($ctrlclass, $ctrlname, $title, $value, $options, $description) {
		$this->HTMLSelectControl($ctrlclass, $ctrlname, $title, $value, $options, $description);
	}

	function RenderCtrl() {
		$ctrlclass = $this->GetCtrlClass();
		$ctrlname = $this->GetCtrlName();
		$value = $this->GetValue();
		$param = $this->GetParam();
		$options = $this->GetOptions();

		echo "    <select name='{$ctrlname}[]' class='{$ctrlclass}' multiple id='{$ctrlname}' {$param}>\n";
		foreach ($options as $optionk => $optionv) {
			$optparam = "";
			if (is_array($value) && in_array($optionk, $value))
				$optparam .= "selected ";
			echo "      <option value='{$optionk}' {$optparam}>{$optionv}</option>\n";
		}
		echo "    </select>\n";
	}
}

class HTMLComboBox extends HTMLSelectControl {
	function HTMLComboBox($ctrlname, $title, $value, $options, $description) {
		$this->HTMLSelectControl("formfld", $ctrlname, $title, $value, $options, $description);
	}
}

class HTMLMountComboBox extends HTMLComboBox {
	function HTMLMountComboBox($ctrlname, $title, $value, $description) {
		global $config;

		// Generate options.
		if (!is_array($config['mounts']['mount']))
			$config['mounts']['mount'] = array();
		array_sort_key($config['mounts']['mount'], "devicespecialfile");

		$options = array();
		$options[""] = gettext("Must choose one");
		foreach ($config['mounts']['mount'] as $mountv) {
			$options[$mountv['uuid']] = $mountv['sharename'];
		}

		$this->HTMLComboBox($ctrlname, $title, $value, $options, $description);
	}
}

class HTMLTimeZoneComboBox extends HTMLComboBox {
	function HTMLTimeZoneComboBox($ctrlname, $title, $value, $description) {
		// Get time zone data.
		function is_timezone($elt) {
			return !preg_match("/\/$/", $elt);
		}

		exec('/usr/bin/tar -tf /usr/share/zoneinfo.tgz', $timezonelist);
		$timezonelist = array_filter($timezonelist, 'is_timezone');
		sort($timezonelist);

		// Generate options.
		$options = array();
		foreach ($timezonelist as $tzv) {
			if (!empty($tzv)) {
				$tzv = substr($tzv, 2); // Remove leading './'
				$options[$tzv] = $tzv;
			}
		}

		$this->HTMLComboBox($ctrlname, $title, $value, $options, $description);
	}
}

class HTMLLanguageComboBox extends HTMLComboBox {
	function HTMLLanguageComboBox($ctrlname, $title, $value, $description) {
		global $g_languages;

		// Generate options.
		$options = array();
		foreach ($g_languages as $languagek => $languagev) {
			$options[$languagek] = gettext($languagev['desc']);
		}

		$this->HTMLComboBox($ctrlname, $title, $value, $options, $description);
	}
}

class HTMLInterfaceComboBox extends HTMLComboBox {
	function HTMLInterfaceComboBox($ctrlname, $title, $value, $description) {
		global $config;

		// Generate options.
		$options = array('lan' => 'LAN');
		for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++) {
			if (isset($config['interfaces']['opt' . $i]['enable']) && !$config['interfaces']['opt' . $i]['bridge']) {
				$options['opt' . $i] = $config['interfaces']['opt' . $i]['descr'];
			}
		}

		$this->HTMLComboBox($ctrlname, $title, $value, $options, $description);
	}
}

class HTMLListBox extends HTMLMultiSelectControl {
	function HTMLListBox($ctrlname, $title, $value, $options, $description) {
		$this->HTMLMultiSelectControl("formselect", $ctrlname, $title, $value, $options, $description);
	}
}

class HTMLSeparator extends HTMLBaseControl {
	function HTMLSeparator() {
	}

	function Render() {
		echo "<tr>\n";
		echo "	<td colspan='2' class='list' height='12'></td>\n";
		echo "</tr>\n";
	}
}

class HTMLTitleLine extends HTMLBaseControl {
	function HTMLTitleLine($title) {
		$this->SetTitle($title);
	}

	function Render() {
		$title = $this->GetTitle();

		echo "<tr>\n";
		echo "	<td colspan='2' valign='top' class='listtopic'>{$title}</td>\n";
		echo "</tr>\n";
	}
}

class HTMLTitleLineCheckBox extends HTMLCheckBox {
	function HTMLTitleLineCheckBox($ctrlname, $title, $value, $caption) {
		$this->HTMLCheckBox($ctrlname, $title, $value, $caption);
	}

	function Render() {
		$ctrlname = $this->GetCtrlName();
		$caption = $this->GetCaption();
		$title = $this->GetTitle();
		$param = $this->GetParam();

		echo "<tr>\n";
		echo "	<td colspan='2' valign='top' class='optsect_t'>\n";
		echo "    <table border='0' cellspacing='0' cellpadding='0' width='100%'>\n";
		echo "      <tr>\n";
		echo "        <td class='optsect_s'><strong>{$title}</strong></td>\n";
		echo "        <td align='right' class='optsect_s'>\n";
		echo "          <input name='{$ctrlname}' type='checkbox' class='formfld' id='{$ctrlname}' value='yes' {$param}><strong>{$caption}</strong>\n";
		echo "        </td>\n";
		echo "      </tr>\n";
		echo "    </table>\n";
		echo "  </td>\n";
		echo "</tr>\n";
	}
}

class HTMLText extends HTMLBaseControl {
	function HTMLText($ctrlname, $title, $text) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($text);
	}

	function RenderCtrl() {
		$text = $this->GetValue();

		echo "{$text}\n";
	}
}

class HTMLRemark extends HTMLBaseControl {
	function HTMLRemark($ctrlname, $title, $text) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($text);
	}

	function Render() {
		$ctrlname = $this->GetCtrlName();
		$title = $this->GetTitle();
		$text = $this->GetValue();

		echo "<div id='remark'>\n";
		echo "  <span class='red'>\n";
		echo "    <strong>{$title}:</strong>\n";
		echo "  </span><br/>\n";
		echo "  {$text}\n";
		echo "</div>\n";
	}
}
?>
