<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {html_progressbar} function plugin
 *
 * Type:     function<br>
 * Name:     html_progressbar<br>
 * Purpose:  display progress bar<br>  
 * @author   Volker Theile <votdev@gmx.de>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_html_progressbar($params, &$smarty)
{
	$percentage = 0;
	$caption = '';

	foreach($params as $_key => $_val) {
		switch($_key) {
			case 'percentage':
			case 'caption':
				$$_key = $_val;
				break;
		}
	}

	if (empty($percentage)) {
		$smarty->trigger_error("html_progressbar: missing 'percentage' parameter", E_USER_NOTICE);
		return;
	}

	if (empty($caption)) {
		$smarty->trigger_error("html_progressbar: missing 'caption' parameter", E_USER_NOTICE);
		return;
	}

	$_html_result  = "<IMG src='bar_left.gif' height='15' width='4' border='0' align='absmiddle'>";
	$_html_result .= "<IMG src='bar_blue.gif' height='15' width='{$percentage}' border='0' align='absmiddle'>";
	$_html_result .= "<IMG src='bar_gray.gif' height='15' width='" . (100 - $percentage) . "' border='0' align='absmiddle'>";
	$_html_result .= "<IMG src='bar_right.gif' height='15' width='5' border='0' align='absmiddle'> ";
	$_html_result .= "{$caption}";

	return $_html_result;
}
?>
