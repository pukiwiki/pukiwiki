<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: read.inc.php,v 1.2 2003/06/12 02:23:53 arino Exp $
//
// ページの表示とInterWikiNameの解釈
function plugin_read_action()
{
	global $get,$post,$vars;
	global $_title_edit,$_title_invalidwn,$_msg_invalidiwn;
	
	// WikiName、BracketNameが示すページを表示
	if (is_page($get['page'])) {
		return array('msg'=>'','body'=>'');
	}

	// InterWikiNameを処理
	if (is_interwiki($get['page']))
		return do_plugin_action('interwiki');

	// ページ名として有効だがページが存在しないので、編集フォームを表示
	if (is_pagename($get['page'])) {
		$get['cmd'] = $post['cmd'] = $vars['cmd'] = 'edit';
		return do_plugin_action('edit');
	}
	// 無効なページ名
	return array(
		'msg'=>$_title_invalidwn,
		'body'=>str_replace('$1',htmlspecialchars($get['page']),
			str_replace('$2','WikiName',$_msg_invalidiwn))
	);
}
?>
