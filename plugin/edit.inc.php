<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: edit.inc.php,v 1.12 2004/07/02 12:47:48 henoheno Exp $
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
	global $script,$post;
	global $_title_preview,$_msg_preview,$_msg_preview_delete;

	if (array_key_exists('template_page',$post) and is_page($post['template_page']))
	{
		$post['msg'] = join('',get_source($post['template_page']));
		// 見出しの固有ID部を削除
		$post['msg'] = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m','$1$2',$post['msg']);
	}
	
	// 手書きの#freezeを削除
	$post['msg'] = preg_replace('/^#freeze\s*$/m','',$post['msg']);

	if (!empty($post['add']))
	{
		if ($post['add_top'])
		{
			$postdata  = $post['msg']."\n\n".@join('',get_source($post['page']));
		}
		else
		{
			$postdata  = @join('',get_source($post['page']))."\n\n".$post['msg'];
		}
	}
	else
	{
		$postdata = $post['msg'];
	}

	$body = "$_msg_preview<br />\n";
	if ($postdata == '')
	{
		$body .= "<strong>$_msg_preview_delete</strong>";
	}
	$body .= "<br />\n";

	if ($postdata != '')
	{
		$postdata = make_str_rules($postdata);
		$postdata = explode("\n",$postdata);
		$postdata = drop_submit(convert_html($postdata));
		
		$body .= <<<EOD
<div id="preview">
  $postdata
</div>
EOD;
	}
	$body .= edit_form($post['page'],$post['msg'],$post['digest'],FALSE);
	
	return array('msg'=>$_title_preview,'body'=>$body);
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
