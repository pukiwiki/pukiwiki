<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: add.inc.php,v 1.2 2003/05/14 10:08:48 arino Exp $
//
// ÄÉ²Ã
// cmd=add
function plugin_add_action()
{
	global $post,$vars,$_title_add,$_msg_add;
	
	check_editable($vars['page']);
	
	$vars['add'] = $post['add'] = TRUE;
	
	return array(
		'msg' => $_title_add,
		'body' => "<ul>\n <li>$_msg_add</li>\n</ul>" .
			edit_form($vars['page'],'')
	);
}
?>