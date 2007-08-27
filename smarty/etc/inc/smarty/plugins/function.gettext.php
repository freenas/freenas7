<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {gettext} function plugin
 *
 * Type:     function<br>
 * Name:     gettext<br>
 * Purpose:  translate string<br>
 * @author   VolkerTheile <votdev@gmx.de>
 * @param array
 * @param Smarty
 */
function smarty_function_gettext($params, &$smarty)
{
	if (!isset($params['text'])) {
		$smarty->trigger_error("gettext: missing 'text' parameter");
		return;
	}

	return htmlentities(gettext($params['text']));
}
?>
