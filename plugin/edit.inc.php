<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: edit.inc.php,v 1.14 2004/07/02 13:34:36 henoheno Exp $
//

// 編集
// cmd=edit
function plugin_edit_action()
{
	global $vars, $_title_edit;

	$page = isset($vars['page']) ? $vars['page'] : '';

	check_editable($page, true, true);

	if (isset($vars['preview']) or isset($vars['template'])) {
		return plugin_edit_preview();
	} else if (isset($vars['write'])) {
		return plugin_edit_write();
	}

	$postdata = @join('', get_source($page));
	if ($postdata == '') {
		$postdata = auto_template($page);
	}

	return array('msg'=>$_title_edit, 'body'=>edit_form($page, $postdata));
}

// プレビュー
function plugin_edit_preview()
{
	global $script, $vars;
	global $_title_preview, $_msg_preview, $_msg_preview_delete;

	$page = isset($vars['page']) ? $vars['page'] : '';

	if (isset($vars['template_page']) && is_page($vars['template_page'])) {

		$vars['msg'] = join('', get_source($vars['template_page']));

		// 見出しの固有ID部を削除
		$vars['msg'] = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $vars['msg']);
	}
	
	// 手書きの#freezeを削除
	$vars['msg'] = preg_replace('/^#freeze\s*$/m', '' ,$vars['msg']);
	$postdata = $vars['msg'];

	if (isset($vars['add']) && $vars['add']) {
		if (isset($vars['add_top']) && $vars['add_top']) {
			$postdata  = $postdata . "\n\n" . @join('', get_source($page));
		} else {
			$postdata  = @join('', get_source($page)) . "\n\n" . $postdata;
		}
	}

	$body = "$_msg_preview<br />\n";
	if ($postdata == '')
		$body .= "<strong>$_msg_preview_delete</strong>";
	$body .= "<br />\n";

	if ($postdata) {
		$postdata = make_str_rules($postdata);
		$postdata = explode("\n", $postdata);
		$postdata = drop_submit(convert_html($postdata));
		$body .= '<div id="preview">' . $postdata . '</div>' . "\n";
	}
	$body .= edit_form($page, $vars['msg'], $vars['digest'], FALSE);
	
	return array('msg'=>$_title_preview, 'body'=>$body);
}

// 書き込みもしくは追加もしくはコメントの挿入
function plugin_edit_write()
{
	global $script, $vars;
	global $_title_collided, $_msg_collided_auto, $_msg_collided, $_title_deleted;

	$page = isset($vars['page']) ? $vars['page'] : '';
	$retvars = array();

	// 手書きの#freezeを削除
	$vars['msg'] = preg_replace('/^#freeze\s*$/m','',$vars['msg']);
	$postdata = $postdata_input = $vars['msg'];

	if (isset($vars['add']) && $vars['add']) {
		if (isset($vars['add_top']) && $vars['add_top']) {
			$postdata  = $postdata . "\n\n" . @join('', get_source($page));
		} else {
			$postdata  = @join('', get_source($page)) . "\n\n" . $postdata;
		}
	}
	
	$oldpagesrc = join('', get_source($page));
	$oldpagemd5 = md5($oldpagesrc);
	
	if (! isset($vars['digest']) || $vars['digest'] != $oldpagemd5) {
		$vars['digest'] = $oldpagemd5;

		$retvars['msg'] = $_title_collided;
		list($postdata_input, $auto) = do_update_diff($oldpagesrc, $postdata_input, $vars['original']);
		
		$retvars['body'] = ($auto ? $_msg_collided_auto : $_msg_collided)."\n";
		
		if (TRUE) {
			global $do_update_diff_table;
			$retvars['body'] .= $do_update_diff_table;
		}
		
		$retvars['body'] .= edit_form($page, $postdata_input, $oldpagemd5, FALSE);
	}
	else {
		$notimestamp = (isset($vars['notimestamp']) && $vars['notimestamp'] != '');
		page_write($page, $postdata, $notimestamp);
		
		if ($postdata) {
			header("Location: $script?" . rawurlencode($page));
			exit;
		}
		
		$retvars['msg'] = $_title_deleted;
		$retvars['body'] = str_replace('$1', htmlspecialchars($page), $_title_deleted);
		tb_delete($page);
	}
	
	return $retvars;
}

?>
