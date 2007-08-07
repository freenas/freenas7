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
class StatusReportArticle
{
	var $_title = "";
	var $_text = "";

	function StatusReportArticle($title,$text) {
		$this->_title = $title;
		$this->_text = $text;
	}

	function GetTitle() {
		return $this->_title;
	}

	function GetText() {
		return $this->_text;
	}
}

class StatusReportArticleCmd extends StatusReportArticle
{
	function StatusReportArticleCmd($title,$command) {
		$this->_title = $title;
		$this->_text = "";

		if (!empty($command)) {
			exec($command . " 2>&1", $execOutput, $execStatus);
			for ($i = 0; isset($execOutput[$i]); $i++) {
				if ($i > 0) $this->_text .= "\n";
				$this->_text .= $execOutput[$i]; // Print command output.
			}
			$this->_text .= "\n";
		}
	}
}

class StatusReportRenderer
{
	function Generate($articles) {
		$report = "";
		$this->_RenderReport($report,$articles);
		return $report;
	}

	function _RenderReport(&$report,$articles) {
		for ($i = 0; is_object($articles[$i]); $i++) {
			$this->_RenderArticle($report,$articles[$i]);
		}
	}

	function _RenderArticle(&$report,$article) {
		// To be implemented by derived class.
	}
}

class StatusReportRendererText extends StatusReportRenderer
{
	function _RenderReport(&$report,$articles) {
		// Render article title.
		$title = $article->GetTitle();
		if (!empty($title)) {
			$nWidth = strlen($title) + 1;
			$report .= sprintf("%s:\n%'-{$nWidth}s\n",$title,""); // Print title and underline.
		}
		// Render article text.
		$report .= $article->GetText();
		$report .= "\n";
	}
}

class StatusReportRendererHTML extends StatusReportRenderer
{
	function _RenderReport(&$report,$articles) {
		exec("/bin/date", $dateOutput, $dateStatus);
		$currentDate = $dateOutput[0];

		$report = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Status Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="gui.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
pre {
   margin: 0px;
   font-family: courier new, courier;
   font-weight: normal;
   font-size: 9pt;
}
-->
</style>
</head>
<body link="#0000CC" vlink="#0000CC" alink="#0000CC">
<p><span class="pgtitle">Status Report</span><br>
<strong><?=$currentDate;?></strong>
<p><span class="red"><strong>Note: make sure to remove any sensitive information
(passwords, maybe also IP addresses) before posting
information from this page in public places (like mailing lists)!</strong></span><br>
Passwords in config.xml have been automatically removed.

EOD;

		// Render table of contents.
		$this->_RenderTableOfContents($report, $articles);

		// Render articles.
		for ($i = 0; is_object($articles[$i]); $i++) {
			$this->_RenderArticle($report,$articles[$i]);
		}

		$report .= <<<EOD
</body>
</html>

EOD;
	}

	function _RenderTableOfContents(&$report,$articles) {
		$report .= "<p>This status page includes the following information:\n";
		$report .= "<ul>\n";
		for ($i = 0; is_object($articles[$i]); $i++) {
			$report .= "<li><strong><a href=\"#" . $articles[$i]->GetTitle() . "\">" . $articles[$i]->GetTitle() . "</a></strong>\n";
		}
    $report .= "</ul>\n";
	}

	function _RenderArticle(&$report,$article) {
		$report .= "<p>\n";
    $report .= "<a name=\"" . $article->GetTitle() . "\">\n";
    $report .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
    $report .= "<tr><td class=\"listtopic\">" . $article->GetTitle() . "</td></tr>\n";
    $report .= "<tr><td class=\"listlr\"><pre>";
		$report .= htmlspecialchars($article->GetText(),ENT_NOQUOTES);
		$report .= "</pre></tr>\n";
    $report .= "</table>\n";
	}
}

class StatusReport
{
	/**
	* Contains commands using to generate report.
	* @var array
	*/
	var $articles = array();

	/**
	* Sets the Content-type of the message.
	* @var bollean
	*/
  var $isHTML = false;

	/**
	* Sets message type to HTML.
	* @param bool $bool
	* @return void
	*/
	function IsHTML($bool) {
		$this->isHTML = $bool;
	}

	/**
	* Add article.
	* @return void
	*/
	function AddArticle($articles) {
		$this->articles[] = $articles;
	}

	/**
	* Generate report.
	* @return string
	*/
	function Generate() {
		$report = "";
		if (true === $this->isHTML) {
			$renderer = new StatusReportRendererHTML;
			$report = $renderer->Generate($this->articles);
		} else {
			$renderer = new StatusReportRendererText;
			$report = $renderer->Generate($this->articles);
		}

		return $report;
	}
}
?>
