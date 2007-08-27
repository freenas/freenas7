<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty lower modifier plugin
 *
 * Type:     modifier<br>
 * Name:     gettext<br>
 * Purpose:  translate string<br>
 * @author   VolkerTheile <votdev@gmx.de>
 * @param string
 * @return string
 */
function smarty_modifier_gettext($string)
{
    return htmlentities(gettext($string));
}
?>
