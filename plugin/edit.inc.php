<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: edit.inc.php,v 1.1 2003/01/27 05:38:46 panda Exp $
//
// 編集
// cmd=edit
function plugin_edit_action()
{
	global $vars,$_title_edit;
	
	if (array_key_exists('preview',$vars) or array_key_exists('template',$vars)) {
		return plugin_edit_preview();
	}
	else if (array_key_exists('write',$vars)) {
		return plugin_edit_write();
	}
	
	check_editable();
	
	$postdata = @join('',get_source($vars['page']));
	if ($postdata == '') {
		$postdata = auto_template($vars['page']);
	}
	
	return array('msg'=>$_title_edit,'body'=>edit_form($vars['page'],$postdata));
}
// プレビュー
function plugin_edit_preview()
{
	global $script,$post;
	global $_title_preview,$_msg_preview,$_msg_preview_delete;

	if (array_key_exists('template_page',$post) and is_page($post['template_page'])) {
		$post['msg'] = join('',get_source($post['template_page']));
	}
	
	$post['msg'] = preg_replace("/^#freeze\n/",'',$post['msg']);
	$postdata_input = $post['msg'];

	if (!empty($post['add'])) {
		if ($post['add_top']) {
			$postdata  = $post['msg']."\n\n".@join('',get_source($post['page']));
		}
		else {
			$postdata  = @join('',get_source($post['page']))."\n\n".$post['msg'];
		}
	}
	else {
		$postdata = $post['msg'];
	}

	$body = "$_msg_preview<br />\n";
	if ($postdata == '') {
		$body .= "<strong>$_msg_preview_delete</strong>";
	}
	$body .= "<br />\n";

	if ($postdata != '') {
		$postdata = drop_submit(convert_html($postdata));
		
		if (!empty($post['viewtag'])) {
			$postdata = preg_replace("/(<[^\/][^>]*>)/e",'"$1".htmlspecialchars("$1")', $postdata);
			$postdata = preg_replace("/(<\/[^>]+>)/e",'htmlspecialchars("$1")."$1"', $postdata);
		}
		
		$body .= <<<EOD
<div id="preview">
  $postdata
</div>
EOD;
	}
	$body .= edit_form($post['page'],$postdata_input,$post['digest'],FALSE);
	
	return array('msg'=>$_title_preview,'body'=>$body);
}

// 書き込みもしくは追加もしくはコメントの挿入
function plugin_edit_write()
{
	global $script,$post,$vars;
	global $_title_collided,$_msg_collided_auto,$_msg_collided,$_title_deleted;
	
	$retvars = array();
	
	// 手書きの#freezeを削除
	$post['msg'] = preg_replace('/^#freeze\n/','',$post['msg']);
	
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
		page_write($post['page'],$postdata);
		
		if ($postdata != '') {
			header("Location: $script?".rawurlencode($post['page']));
			exit;
		}
		
		$retvars['msg'] = $_title_deleted;
		$retvars['body'] = str_replace('$1',htmlspecialchars($post['page']),$_title_deleted);
	}
	
	return $retvars;
}

?>
