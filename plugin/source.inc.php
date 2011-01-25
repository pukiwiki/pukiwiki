<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: source.inc.php,v 1.16 2011/01/25 15:01:01 henoheno Exp $
//
// Source plugin

// Output source text of the page
function plugin_source_action()
{
	global $vars, $_source_messages;

	if (PKWK_SAFE_MODE) die_message('PKWK_SAFE_MODE prohibits this');

	$page = isset($vars['page']) ? $vars['page'] : '';
	$vars['refer'] = $page;

	if (! is_page($page) || ! check_readable($page, false, false))
		return array('msg' => $_source_messages['msg_notfound'],
			'body' => $_source_messages['err_notfound']);

	return array('msg' => $_source_messages['msg_title'],
		'body' => '<pre id="source">' .
		htmlsc(join('', get_source($page))) . '</pre>');
}
?>
