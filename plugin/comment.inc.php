<?
global $name_cols,$comment_cols;

/////////////////////////////////////////////////
// コメントの名前テキストエリアのカラム数
$name_cols = 15;
/////////////////////////////////////////////////
// コメントのテキストエリアのカラム数
$comment_cols = 70;
/////////////////////////////////////////////////
// コメントの挿入フォーマット(名前)
$name_format = '[[$name]]';
/////////////////////////////////////////////////
// コメントの挿入フォーマット(コメント内容)
$comment_format = '$now $name $msg';
/////////////////////////////////////////////////
// コメントを挿入する位置 1:欄の前 0:欄の後
$comment_ins = 1;


// initialize
$comment_no = 0;

function plugin_comment_action()
{
	global $post,$vars,$script,$cols,$rows,$del_backup,$do_backup,$update_exec,$now;
	global $name_cols,$comment_cols,$name_format,$comment_format,$comment_ins;
	global $_title_collided,$_msg_collided,$_title_updated;

	if($post["msg"])
	{
		$post["msg"] = preg_replace("/\n/","",$post["msg"]);

		$postdata = "";
		$postdata_old  = file(get_filename(encode($post["refer"])));
		$comment_no = 0;

		if($post[name])
		{
			$name = str_replace('$name',$post[name],$name_format);
		}
		if($post[msg])
		{
			if(preg_match("/^(-{1,2})(.*)/",$post[msg],$match))
			{
				$head = $match[1];
				$post[msg] = $match[2];
			}
			
			$comment = str_replace('$msg',$post[msg],$comment_format);
			$comment = str_replace('$name',$name,$comment);
			$comment = str_replace('$now',$now,$comment);
		}
		$comment = $head.$comment;

		foreach($postdata_old as $line)
		{
			if(!$comment_ins) $postdata .= $line;
			if(preg_match("/^#comment/",$line))
			{
				if($comment_no == $post["comment_no"] && $post[msg]!="")
				{
					$postdata .= "-$comment\n";
				}
				$comment_no++;
			}
			if($comment_ins) $postdata .= $line;
		}

		$postdata_input = "-$comment\n";
	}
	
	if(md5(@join("",@file(get_filename(encode($post["refer"]))))) != $post["digest"])
	{
		$title = $_title_collided;
		
		$body = "$_msg_collided\n";

		$body .= "<form action=\"$script?cmd=preview\" method=\"post\">\n"
			."<input type=\"hidden\" name=\"refer\" value=\"".$post["refer"]."\">\n"
			."<input type=\"hidden\" name=\"digest\" value=\"".$post["digest"]."\">\n"
			."<textarea name=\"msg\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\" id=\"textarea\">$postdata_input</textarea><br>\n"
			."</form>\n";
	}
	else
	{
		$postdata = user_rules_str($postdata);

		// 差分ファイルの作成
		if(is_page($post["refer"]))
			$oldpostdata = join("",file(get_filename(encode($post["refer"]))));
		else
			$oldpostdata = "\n";
		if($postdata)
			$diffdata = do_diff($oldpostdata,$postdata);
		file_write(DIFF_DIR,$post["refer"],$diffdata);

		// バックアップの作成
		if(is_page($post["refer"]))
			$oldposttime = filemtime(get_filename(encode($post["refer"])));
		else
			$oldposttime = time();

		// 編集内容が何も書かれていないとバックアップも削除する?しないですよね。
		if(!$postdata && $del_backup)
			backup_delete(BACKUP_DIR.encode($post["refer"]).".txt");
		else if($do_backup && is_page($post["refer"]))
			make_backup(encode($post["refer"]).".txt",$oldpostdata,$oldposttime);

		// ファイルの書き込み
		file_write(DATA_DIR,$post["refer"],$postdata);

		// is_pageのキャッシュをクリアする。
		is_page($post["refer"],true);

		$title = $_title_updated;
	}
	$retvars["msg"] = $title;
	$retvars["body"] = $body;
	
	$post["page"] = $post["refer"];
	$vars["page"] = $post["refer"];
	
	return $retvars;
}
function plugin_comment_convert()
{
	global $script,$comment_no,$vars,$name_cols,$comment_cols,$digest;
	global $_btn_comment,$_btn_name,$vars;

	if((arg_check("read")||$vars["cmd"] == ""||arg_check("unfreeze")||arg_check("freeze")||$vars["write"]||$vars["comment"]))
		$button = "<input type=\"submit\" name=\"comment\" value=\"$_btn_comment\">\n";

	$string = "<form action=\"$script\" method=\"post\">\n"
		 ."<input type=\"hidden\" name=\"comment_no\" value=\"$comment_no\">\n"
		 ."<input type=\"hidden\" name=\"refer\" value=\"$vars[page]\">\n"
		 ."<input type=\"hidden\" name=\"plugin\" value=\"comment\">\n"
		 ."<input type=\"hidden\" name=\"digest\" value=\"$digest\">\n"
		 ."$_btn_name<input type=\"text\" name=\"name\" size=\"$name_cols\">\n"
		 ."<input type=\"text\" name=\"msg\" size=\"$comment_cols\">\n"
		 .$button
		 ."</form>";

	$comment_no++;

	return $string;
}
?>
