<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: aname.inc.php,v 1.17 2005/01/23 07:00:16 henoheno Exp $
//
// aname plugin - Set an anchor <a name="key"> to link

function plugin_aname_inline()
{
	$args = func_get_args();
	return call_user_func_array('plugin_aname_convert', $args);
}

function plugin_aname_convert()
{
	global $script, $vars;

	if (func_num_args() < 1) return FALSE;

	$args = func_get_args();
	$id   = array_shift($args);
	if (! preg_match('/^[A-Za-z][\w\-]*$/', $id)) return FALSE;

	$body = ! empty($args) ? preg_replace('/<\/?a[^>]*>/', '', array_pop($args)) : '';

	$class   = in_array('super', $args) ? 'anchor_super' : 'anchor';
	$url     = in_array('full',  $args) ? $script . '?' . rawurlencode($vars['page']) : '';
	$attr_id = in_array('noid',  $args) ? '' : ' id="' . $id . '"';

	return '<a class="' . $class . '"' . $attr_id . ' href="' . $url . '#' . $id .
		'" title="' . $id . '">' . $body . '</a>';
}
?>
