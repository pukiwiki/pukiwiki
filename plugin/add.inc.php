<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: add.inc.php,v 1.7 2005/05/02 02:46:23 henoheno Exp $
//
// Add plugin - Append new text below/above existing page
// Usage: cmd=add&page=pagename

function plugin_add_action()
{
	global $get, $post, $vars, $_title_add, $_msg_add;

	if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');

	$page = isset($vars['page']) ? $vars['page'] : '';
	check_editable($page);

	$get['add'] = $post['add'] = $vars['add'] = TRUE;
	return array(
		'msg'  => $_title_add,
		'body' =>
			'<ul>' . "\n" .
			' <li>' . $_msg_add . '</li>' . "\n" .
			'</ul>' . "\n" .
			edit_form($page, '')
		);
}
?>
