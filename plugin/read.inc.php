<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: read.inc.php,v 1.6 2004/08/04 13:35:40 henoheno Exp $
//
// ページの表示とInterWikiNameの解釈

function plugin_read_action()
{
	global $get, $post, $vars;
	global $_title_invalidwn, $_msg_invalidiwn;

	$page = isset($vars['page']) ? $vars['page'] : '';

	if (is_page($page)) {
		// ページを表示
		check_readable($page, true, true);
		header_lastmod($page);
		return array('msg'=>'', 'body'=>'');

	} else if (is_interwiki($page)) {
		return do_plugin_action('interwiki'); // InterWikiNameを処理

	} else if (is_pagename($page)) {
		$get['cmd'] = $post['cmd'] = $vars['cmd'] = 'edit';
		return do_plugin_action('edit'); // 存在しないので、編集フォームを表示

	} else {
		// 無効なページ名
		return array(
			'msg'=>$_title_invalidwn,
			'body'=>str_replace('$1', htmlspecialchars($page),
				str_replace('$2', 'WikiName', $_msg_invalidiwn))
		);
	}
}
?>
