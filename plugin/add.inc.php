<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: add.inc.php,v 1.4 2004/07/31 03:09:19 henoheno Exp $
//
// ÄÉ²Ã
// cmd=add
function plugin_add_action()
{
	global $get,$post,$vars,$_title_add,$_msg_add;

	$page = isset($vars['page']) ? $vars['page'] : '';

	check_editable($page);

	$get['add'] = $post['add'] = $vars['add'] = TRUE;

	return array(
		'msg' => $_title_add,
		'body' => "<ul>\n <li>$_msg_add</li>\n</ul>" .
			edit_form($page, '')
	);
}
?>
