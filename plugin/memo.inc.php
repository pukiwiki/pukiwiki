<?php
// $Id: memo.inc.php,v 1.4.2.4 2004/07/25 13:59:31 henoheno Exp $

/////////////////////////////////////////////////
// テキストエリアのカラム数
define('MEMO_COLS', 80);

// テキストエリアの行数
define('MEMO_ROWS', 5);

/////////////////////////////////////////////////
function plugin_memo_action()
{
	global $post, $vars, $script, $cols, $rows, $del_backup, $do_backup;
	global $_title_collided, $_msg_collided, $_title_updated;

	if (! isset($post['msg']) || $post['msg'] == '') return;

	$memo_body = $post['msg'];
	$memo_body = preg_replace("/(\x0D\x0A)/", "\n",	$memo_body);
	$memo_body = preg_replace("/(\x0D)/", "\n",	$memo_body);
	$memo_body = preg_replace("/(\x0A)/", "\n",	$memo_body);
	$memo_body = str_replace("\n", "\\n",	$memo_body);
	$memo_body = str_replace('"', '&#x22;',	$memo_body); // Escape double quotes
	$memo_body = str_replace(',', '&#x2c;',	$memo_body); // Escape commas

	$postdata = '';
	$postdata_old  = file(get_filename(encode($post['refer'])));
	$memo_no = 0;

	foreach($postdata_old as $line)
	{
		if(preg_match("/^#memo\(?.*\)?$/", $line))
		{
			if($memo_no == $post['memo_no'] && $post['msg'] != '')
			{
				$postdata .= "#memo($memo_body)\n";
				$line = '';
			}
			++$memo_no;
		}
		$postdata .= $line;
	}
	$postdata_input = "$memo_body\n";

	if(md5(@join('', @file(get_filename(encode($post['refer']))))) != $post['digest'])
	{
		$title = $_title_collided;

		$body = "$_msg_collided\n";

		$body .= "<form action=\"$script?cmd=preview\" method=\"post\">\n"
			. "<div>\n"
			. "<input type=\"hidden\" name=\"refer\"  value=\"" . $post['refer']  . "\" />\n"
			. "<input type=\"hidden\" name=\"digest\" value=\"" . $post['digest'] . "\" />\n"
			. "<textarea name=\"msg\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\" id=\"textarea\">$postdata_input</textarea><br />\n"
			. "</div>\n"
			. "</form>\n";
	}
	else
	{
		$postdata = user_rules_str($postdata);

		// 差分ファイルの作成
		if(is_page($post['refer']))
			$oldpostdata = join('', file(get_filename(encode($post['refer']))));
		else
			$oldpostdata = "\n";
		if($postdata)
			$diffdata = do_diff($oldpostdata, $postdata);
		file_write(DIFF_DIR, $post['refer'], $diffdata);

		// バックアップの作成
		if(is_page($post['refer']))
			$oldposttime = filemtime(get_filename(encode($post['refer'])));
		else
			$oldposttime = time();

		// 編集内容が何も書かれていないとバックアップも削除する?しないですよね。
		if(!$postdata && $del_backup)
			backup_delete(BACKUP_DIR . encode($post['refer']) . '.txt');
		else if($do_backup && is_page($post['refer']))
			make_backup(encode($post['refer']) . '.txt', $oldpostdata, $oldposttime);

		// ファイルの書き込み
		file_write(DATA_DIR, $post['refer'], $postdata);

		// is_pageのキャッシュをクリアする。
		is_page($post['refer'], true);

		$title = $_title_updated;
	}
	$retvars['msg'] = $title;
	$retvars['body'] = $body;

	$post['page'] = $post['refer'];
	$vars['page'] = $post['refer'];

	return $retvars;
}

function plugin_memo_convert()
{
	global $script, $vars, $digest;
	global $_btn_memo_update, $vars;
	static $memo_no = 0;

	$data = func_get_args();
	$data = implode(',', $data);	// Care all arguments
	$data = str_replace('&#x2c;', ',', $data);	// Unescape commas
	$data = str_replace('&#x22;', '"', $data);	// Unescape double quotes
	$data = htmlspecialchars(str_replace("\\n", "\n", $data));

	if((arg_check('read') || $vars['cmd'] == '' || arg_check('unfreeze') ||
	    arg_check('freeze') || $vars['write'] || $vars['memo']))
		$button = "<input type=\"submit\" name=\"memo\" value=\"$_btn_memo_update\" />\n";

	$s_page = htmlspecialchars($vars['page']);

	$string = "<form action=\"$script\" method=\"post\" class=\"memo\">\n"
		 . "<div>\n"
		 . "<input type=\"hidden\" name=\"memo_no\" value=\"$memo_no\" />\n"
		 . "<input type=\"hidden\" name=\"refer\"   value=\"$s_page\" />\n"
		 . "<input type=\"hidden\" name=\"plugin\"  value=\"memo\" />\n"
		 . "<input type=\"hidden\" name=\"digest\"  value=\"$digest\" />\n"
		 . "<textarea name=\"msg\" rows=\"" . MEMO_ROWS . "\" cols=\"" . MEMO_COLS . "\">\n$data</textarea><br />\n"
		 . $button
		 . "</div>\n"
		 . "</form>";

	++$memo_no;

	return $string;
}
?>
