<?php
/*
	wui.inc
	Copyright (c) 2006-2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetDescription($description);
		$this->SetValue($value);
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

		echo "		<input name='{$ctrlname}' type='text' class='formfld' id='{$ctrlname}' size='{$size}' value='{$value}' {$param}>\n";
	}
}

class HTMLPasswordBox extends HTMLEditBox {
	var $_ctrlnameconf = "";
	var $_valueconf = "";

	function HTMLPasswordBox($ctrlname, $ctrlnameconf, $title, $value, $valueconf, $description, $size) {
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

	function RenderCtrl() {
		$ctrlname = $this->GetCtrlName();
		$value = htmlspecialchars($this->GetValue());
		$columns = $this->GetColumns();
		$rows = $this->GetRows();
		$param = $this->GetParam();

		echo "		<textarea name='{$ctrlname}' cols='{$columns}' rows='{$rows}' id='{$ctrlname}' class='formpre' {$param}>{$value}</textarea><br/>\n";
	}
}

class HTMLCheckBox extends HTMLBaseControlJS {
	var $_caption = "";

	function HTMLCheckBox($ctrlname, $title, $value, $caption, $description) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetValue($value);
		$this->SetCaption($caption);
		$this->SetDescription($description);
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

class HTMLComboBox extends HTMLBaseControlJS {
	var $_options = array();

	function HTMLComboBox($ctrlname, $title, $value, $options, $description) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetDescription($description);
		$this->SetValue($value);
		$this->SetOptions($options);
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
		$ctrlname = $this->GetCtrlName();
		$value = htmlspecialchars($this->GetValue());
		$param = $this->GetParam();
		$options = $this->GetOptions();

		echo "    <select name='{$ctrlname}' class='formfld' id='{$ctrlname}' {$param}>\n";
		foreach ($options as $optionk => $optionv) {
			$optparam = "";
			if ($value === $optionk)
				$optparam .= "selected ";
			echo "      <option value='{$optionk}' {$optparam}>{$optionv}</option>\n";
		}
		echo "    </select>\n";
	}
}

class HTMLMountComboBox extends HTMLComboBox {
	function HTMLMountComboBox($ctrlname, $title, $value, $description) {
		global $config;

		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetDescription($description);
		$this->SetValue($value);

		// Generate options.
		if (!is_array($config['mounts']['mount']))
			$config['mounts']['mount'] = array();
		array_sort_key($config['mounts']['mount'], "devicespecialfile");

		$options = array();
		foreach ($config['mounts']['mount'] as $mountv) {
			$options[$mountv['sharename']] = $mountv['sharename'];
		}

		$this->SetOptions($options);
	}
}

class HTMLTimeZoneComboBox extends HTMLComboBox {
	function HTMLTimeZoneComboBox($ctrlname, $title, $value, $description) {
		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetDescription($description);
		$this->SetValue($value);

		// Get time zone data.
		function is_timezone($elt) {
			return !preg_match("/\/$/", $elt);
		}

		exec('/usr/bin/tar -tf /usr/share/zoneinfo.tgz -W strip-components=1', $timezonelist);
		$timezonelist = array_filter($timezonelist, 'is_timezone');
		sort($timezonelist);

		// Generate options.
		$options = array();
		foreach ($timezonelist as $tzv) {
			if (!empty($tzv)) {
				$options[$tzv] = $tzv;
			}
		}

		$this->SetOptions($options);
	}
}

class HTMLLanguageComboBox extends HTMLComboBox {
	function HTMLLanguageComboBox($ctrlname, $title, $value, $description) {
		global $g_languages;

		$this->SetCtrlName($ctrlname);
		$this->SetTitle($title);
		$this->SetDescription($description);
		$this->SetValue($value);

		// Generate options.
		$options = array();
		foreach ($g_languages as $languagek => $languagev) {
			$options[$languagek] = gettext($languagev['desc']);
		}

		$this->SetOptions($options);
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
?>
