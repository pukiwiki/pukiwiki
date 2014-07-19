<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: read.inc.php,v 1.9 2011/01/25 15:01:01 henoheno Exp $
//
// Read plugin: Show a page and InterWiki

function plugin_read_action()
{
	global $vars, $_title_invalidwn, $_msg_invalidiwn;

	$page = isset($vars['page']) ? $vars['page'] : '';

	if (is_page($page)) {
		// ページを表示
		check_readable($page, true, true);
		header_lastmod($page);
		return array('msg'=>'', 'body'=>'');

	} else if (! PKWK_SAFE_MODE && is_interwiki($page)) {
		return do_plugin_action('interwiki'); // InterWikiNameを処理

	} else if (is_pagename($page)) {
		$vars['cmd'] = 'edit';
		return do_plugin_action('edit'); // 存在しないので、編集フォームを表示

	} else {
		// 無効なページ名
		return array(
			'msg'=>$_title_invalidwn,
			'body'=>str_replace('$1', htmlsc($page),
				str_replace('$2', 'WikiName', $_msg_invalidiwn))
		);
	}
}
?>
