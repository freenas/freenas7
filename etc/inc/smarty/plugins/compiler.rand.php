<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {rand} function plugin
 *
 * Type:     function<br>
 * Name:     rand<br>
 * Purpose:  generate a random integer and assign the value to a template variable<br>
 * @author Volker Theile <votdev@gmx.de>
 * @param array
 * @param Smarty
 */
function smarty_compiler_rand($tag_attrs, &$compiler)
{
	$_params = $compiler->_parse_attrs($tag_attrs);

  if (!isset($_params['var'])) {
      $compiler->_syntax_error("rand: missing 'var' parameter", E_USER_WARNING);
      return;
  }

	$_value = rand();

  return "\$this->assign({$_params['var']}, {$_value});";
}
?>
