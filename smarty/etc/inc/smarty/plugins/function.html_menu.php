<?php
/**
 * Smarty {html_options} function plugin
 *
 * Type:     function<br>
 * Name:     html_options<br>
 * Input:<br>
 *           - name       (optional) - string default "select"
 *           - values     (required if no options supplied) - array
 *           - options    (required if no values supplied) - associative array
 *           - selected   (optional) - string default not set
 *           - output     (required if not options supplied) - array
 * Purpose:  Prints the list of <option> tags generated from
 *           the passed parameters
 * @link http://smarty.php.net/manual/en/language.function.html.options.php {html_image}
 *      (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_menu($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

    $values = null;

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'values':
                $$_key = array_values((array)$_val);
                break;
        }
    }

    if (!isset($values))
        return ''; /* raise error here? */

    $_html_result = '';

    foreach ($values as $_i=>$_key) {
        $_html_result .= smarty_function_html_menu_display($_key);
    }

    return $_html_result;

}

function smarty_function_html_menu_display($menu) {
	# Is menu expandable?
	if ($menu['expandable']) {
		$span_id = "span".rand(); # Create unique id.
		$icon_id = "icon".rand(); # Create unique id.
		$_html_result = "<li><h1><a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\"><img src='/tri_c.gif' id='{$icon_id}' width='14' height='10' border='0'></a><a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\">".gettext($menu['desc'])."</a></h1>\n";
		$_html_result .= "<span id='{$span_id}' style='display: none'>\n";
	}
	else {
		# Display menu section description only.
		$_html_result = "<li><h1>".gettext($menu['desc'])."</h1>\n";
	}

	# Open new navigation layer.
	$_html_result .= "<ul>\n";

	# Display menu items.
	foreach( $menu['menuitem'] as $menuk => $menuv) {
		# Is menu item visible?
		if (!$menuv['visible']) {
			continue;
		}
		# Display menuitem.
		$_html_result .= "<li><a href='".$menuv['link']."' title='".gettext($menuv['desc'])."'>".gettext($menuv['desc'])."</a></li>\n";
	}

	# Close navigation layer.
	$_html_result .= "</ul></li>\n";

	# Is menu expandable?
	if ($menu['expandable']) {
		$_html_result .= "</span>\n";
	}

	return $_html_result;
}
?>
