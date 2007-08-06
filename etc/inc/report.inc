<?php
/*
	report.inc
	Copyright (c) 2007 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
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
class StatusReport
{
	/**
	* Contains commands using to generate report.
	* @var array
	*/
	var $commands;

	/**
	* Add command with title.
	* @return void
	*/
	function AddCommand($title, $command) {
		$this->$commands[] = array($title, $command);
	}

	/**
	* Add command without title.
	* @return void
	*/
	function AddCommand($command) {
		$this->AddCommand("", $command);
	}

	/**
	* Add title.
	* @return void
	*/
	function AddTitle($title) {
		$this->AddCommand($title, "");
	}

	/**
	* Generate report.
	* @return string
	*/
	function Generate() {
		$report = "";
		for ($i = 0; isset($this->$commands[$i]); $i++) {
			$this->_Render($report, $this->$commands[$i][0], $this->$commands[$i][1]);
		}
		return $report;
	}

	function _Render(&$report, $title, $command) {
		// Set title.
		$this->_SetTitle($report, $title);

		// Execute command and render output.
		$this->_ExecCmd($report, $command);
	}

	function _SetTitle(&$report,$title) {
		if (!empty($title)) {
			$nWidth = strlen($title) + 1; // Print title.
			$report .= sprintf("%s:\n%'-{$nWidth}s\n",$title,""); // Print underline.
		}
	}

	function _ExecCmd(&$report, $command) {
		if (!empty($command)) {
			exec($command . " 2>&1", $execOutput, $execStatus);
			for ($i = 0; isset($execOutput[$i]); $i++) {
				if ($i > 0) {
					$report .= "\n";
				}
				$report .= $execOutput[$i]; // Print command output.
			}
			$report .= "\n\n";
		}
	}
}
?>
