<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: source.inc.php,v 1.13 2004/11/28 08:52:29 henoheno Exp $
//
// Source plugin

// Output source text of the page
function plugin_source_action()
{
	global $vars, $_source_messages;

	$page = isset($vars['page']) ? $vars['page'] : '';
	$vars['refer'] = $page;

	if (! is_page($page) || ! check_readable($page, false, false))
		return array('msg' => $_source_messages['msg_notfound'],
			'body' => $_source_messages['err_notfound']);

	return array('msg' => $_source_messages['msg_title'],
		'body' => '<pre id="source">' .
		htmlspecialchars(join('', get_source($page))) . '</pre>');
}
?>
