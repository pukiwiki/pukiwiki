<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: edit.inc.php,v 1.13 2004/07/02 13:07:31 henoheno Exp $
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

	if (isset($vars['add'])) {
		if (isset($vars['add_top']) && $vars['add_top']) {
			$postdata  = $vars['msg'] . "\n\n" . @join('', get_source($page));
		} else {
			$postdata  = @join('', get_source($page)) . "\n\n" . $vars['msg'];
		}
	} else {
		$postdata = $vars['msg'];
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
	global $script,$post,$vars;
	global $_title_collided,$_msg_collided_auto,$_msg_collided,$_title_deleted;
	
	$retvars = array();
	
	// 手書きの#freezeを削除
	$post['msg'] = preg_replace('/^#freeze\s*$/m','',$post['msg']);
	
	$postdata_input = $post['msg'];
	
	if (!empty($post['add'])) {
		if (!empty($post['add_top'])) {
			$postdata  = $post['msg'];
			$postdata .= "\n\n";
			$postdata .= @join('',get_source($post['page']));
		}
		else {
			$postdata  = @join('',get_source($post['page']));
			$postdata .= "\n\n";
			$postdata .= $post['msg'];
		}
	}
	else {
		$postdata = $post['msg'];
	}
	
	$oldpagesrc = join('',get_source($post['page']));
	$oldpagemd5 = md5($oldpagesrc);
	
	if ($oldpagemd5 != $post['digest']) {
		$retvars['msg'] = $_title_collided;
		
		$post['digest'] = $vars['digest'] = $oldpagemd5;
		list($postdata_input,$auto) = do_update_diff($oldpagesrc,$postdata_input,$post['original']);
		
		$retvars['body'] = ($auto ? $_msg_collided_auto : $_msg_collided)."\n";
		
		if (TRUE) {
			global $do_update_diff_table;
			$retvars['body'] .= $do_update_diff_table;
		}
		
		$retvars['body'] .= edit_form($post['page'],$postdata_input,$oldpagemd5,FALSE);
	}
	else {
		$notimestamp = !empty($post['notimestamp']);
		page_write($post['page'],$postdata,$notimestamp);
		
		if ($postdata != '') {
			header("Location: $script?".rawurlencode($post['page']));
			exit;
		}
		
		$retvars['msg'] = $_title_deleted;
		$retvars['body'] = str_replace('$1',htmlspecialchars($post['page']),$_title_deleted);
		tb_delete($post['page']);
	}
	
	return $retvars;
}

?>
