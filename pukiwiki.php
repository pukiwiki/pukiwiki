<?php
// pukiwiki.php - Yet another WikiWikiWeb clone.
//
// PukiWiki 1.3.*
//  Copyright (C) 2002 by PukiWiki Developers Team
//  http://pukiwiki.org/
//
// PukiWiki 1.3 (Base)
//  Copyright (C) 2001,2002 by sng.
//  <sng@factage.com>
//  http://factage.com/sng/pukiwiki/
//
// Special thanks
//  YukiWiki by Hiroshi Yuki
//  <hyuki@hyuki.com>
//  http://www.hyuki.com/yukiwiki/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// $Id: pukiwiki.php,v 1.16.2.13 2004/08/08 02:39:05 henoheno Exp $
/////////////////////////////////////////////////

/////////////////////////////////////////////////
// サブルーチンの格納先ディレクトリ (他の *.phpファイル等)

if (! defined('LIB_DIR')) define('LIB_DIR', '');

// プログラムファイル読み込み
require(LIB_DIR . 'func.php');
require(LIB_DIR . 'file.php');
require(LIB_DIR . 'plugin.php');
require(LIB_DIR . 'html.php');
require(LIB_DIR . 'backup.php');

require(LIB_DIR . 'template.php');
require(LIB_DIR . 'rss.php');
require(LIB_DIR . 'make_link.php');
if (! extension_loaded('mbstring')) {
	require(LIB_DIR . 'mbstring.php');
}

/////////////////////////////////////////////////
// プログラムファイル読み込み
require(LIB_DIR . 'init.php');


/////////////////////////////////////////////////
// メイン処理

// Plug-in action
if(!empty($vars["plugin"]) && exist_plugin_action($vars["plugin"]))
{
	$retvars = do_plugin_action($vars["plugin"]);

	$title = strip_bracket($vars["refer"]);
	$page = make_search($vars["refer"]);

	if($retvars["msg"])
	{
		$title =  str_replace("$1",$title,$retvars["msg"]);
		$page =  str_replace("$1",$page,$retvars["msg"]);
	}

	if(!empty($retvars["body"]))
	{
		$body = $retvars["body"];
	}
	else
	{
		$cmd = "read";
		$vars["page"] = $vars["refer"];
		$body = @join("",get_source($vars["refer"]));
		$body = convert_html($body);
	}
}
// 一覧の表示
else if(arg_check("list"))
{
	header_lastmod($whatsnew);

	$page = $title = $_title_list;
	$body = get_list(false);
}
// ファイル名一覧の表示
else if(arg_check("filelist"))
{
	header_lastmod($whatsnew);

	$page = $title = $_title_filelist;
	$body = get_list(true);
}
// 編集不可能なページを編集しようとしたとき
else if(((arg_check("add") || arg_check("edit") || arg_check("preview")) && (is_freeze($vars["page"]) || !is_editable($vars["page"]) || $vars["page"] == "")))
{
	$body = $title = str_replace('$1',htmlspecialchars(strip_bracket($vars["page"])),$_title_cannotedit);
	$page = str_replace('$1',make_search($vars["page"]),$_title_cannotedit);

	if(is_freeze($vars["page"]))
 		$body .= "(<a href=\"$script?cmd=unfreeze&amp;page=".rawurlencode($vars["page"])."\">$_msg_unfreeze</a>)";
}
// 追加
else if(arg_check("add"))
{
	$title = str_replace('$1',htmlspecialchars(strip_bracket($get["page"])),$_title_add);
	$page = str_replace('$1',make_search($get["page"]),$_title_add);
	$body = "<ul>\n";
	$body .= "<li>$_msg_add</li>\n";
	$body .= "</ul>\n";
	$body .= edit_form("",$get["page"],true);
}
// 編集
else if(arg_check("edit"))
{
        $postdata = @join("",get_source($get["page"]));
	if($postdata == '') {
		$postdata = auto_template($get["page"]);
	}
	$title = str_replace('$1',htmlspecialchars(strip_bracket($get["page"])),$_title_edit);
	$page = str_replace('$1',make_search($get["page"]),$_title_edit);
	$body = edit_form($postdata,$get["page"]);
}
// プレビュー
else if(arg_check("preview") || isset($post["preview"]) || isset($post["template"]))
{
        if($post["template"] && is_page($post["template_page"]))
	{
		$post["msg"] = @join("",get_source($post["template_page"]));
	}

	$post["msg"] = preg_replace("/^#freeze\s*$/m","",$post["msg"]);
	$postdata_input = $post["msg"];

	if($post["add"])
	{
		if($post["add_top"])
		{
			$postdata  = $post["msg"];
			$postdata .= "\n\n";
			$postdata .= @join("",get_source($post["page"]));
		}
		else
		{
			$postdata  = @join("",get_source($post["page"]));
			$postdata .= "\n\n";
			$postdata .= $post["msg"];
		}
	}
	else
	{
		$postdata = $post["msg"];
	}

	$title = str_replace('$1',htmlspecialchars(strip_bracket($post["page"])),$_title_preview);
	$page = str_replace('$1',make_search($post["page"]),$_title_preview);

	$body = "$_msg_preview<br />\n";
	if($postdata == "") $body .= "<strong>$_msg_preview_delete</strong><br />\n";
	else                $body .= "<br />\n";

	if($postdata != "")
	{
		$postdata = convert_html($postdata);

		$body .= "<table width=\"100%\" style=\"background-color:$preview_color\">\n"
			."<tr><td>\n"
			.$postdata
			."\n</td></tr>\n"
			."</table>\n";
	}

	if($post["add"])
	{
		if($post["add_top"]) $checked_top = " checked=\"checked\"";
		$addtag = '<input type="hidden" name="add" value="true" />';
		$add_top = '<input type="checkbox" name="add_top" value="true"'.$checked_top.' /><span class="small">$_btn_addtop</span>';
	}
	if($post["notimestamp"]) $checked_time = "checked=\"checked\"";

	$body .= "<form action=\"$script\" method=\"post\">\n"
		."<div>\n"
		."<input type=\"hidden\" name=\"help\" value=\"".htmlspecialchars($post["add"])."\" />\n"
		."<input type=\"hidden\" name=\"page\" value=\"".htmlspecialchars($post["page"])."\" />\n"
		."<input type=\"hidden\" name=\"digest\" value=\"".htmlspecialchars($post["digest"])."\" />\n"
		."$addtag\n"
		."<textarea name=\"msg\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\">\n".htmlspecialchars($postdata_input)."</textarea><br />\n"
		."<input type=\"submit\" name=\"preview\" value=\"$_btn_repreview\" accesskey=\"p\" />\n"
		."<input type=\"submit\" name=\"write\" value=\"$_btn_update\" accesskey=\"s\" />\n"
		."$add_top\n"
		."<input type=\"checkbox\" name=\"notimestamp\" value=\"true\" $checked_time /><span class=\"small\">$_btn_notchangetimestamp</span>\n"
		."</div>\n"
		."</form>\n";
}
// 書き込みもしくは追加もしくはコメントの挿入
else if(isset($post["write"]))
{
	$post["msg"] = preg_replace("/^#freeze\s*\n/","",$post["msg"]);
	$postdata_input = $post["msg"];

	if($post["add"])
	{
		if($post["add_top"])
		{
			$postdata  = $post["msg"];
			$postdata .= "\n\n";
			$postdata .= @join("",get_source($post["page"]));
		}
		else
		{
			$postdata  = @join("",get_source($post["page"]));
			$postdata .= "\n\n";
			$postdata .= $post["msg"];
		}
	}
	else
	{
		$postdata = $post["msg"];
	}

        $oldpagesrc = get_source($post["page"]);
	if(md5(join("",$oldpagesrc)) != $post["digest"])
	{
		$title = str_replace('$1',htmlspecialchars(strip_bracket($post["page"])),$_title_collided);
		$page = str_replace('$1',make_search($post["page"]),$_title_collided);
		$post["digest"] = md5(join("",($oldpagesrc)));
		list($postdata_input,$auto) = do_update_diff(join("",$oldpagesrc),$postdata_input);

		if($auto) {
		  $body = $_msg_collided_auto."\n";
		}
		else {
		  $body = $_msg_collided."\n";
		}
		$body .= "<form action=\"$script\" method=\"post\">\n"
			."<div>\n"
			."<input type=\"hidden\" name=\"page\" value=\"".htmlspecialchars($post["page"])."\" />\n"
			."<input type=\"hidden\" name=\"digest\" value=\"".htmlspecialchars($post["digest"])."\" />\n"
			."<textarea name=\"msg\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\" id=\"textarea\">".htmlspecialchars($postdata_input)."</textarea><br />\n"
			."<input type=\"submit\" name=\"preview\" value=\"$_btn_repreview\" accesskey=\"p\" />\n"
			."<input type=\"submit\" name=\"write\" value=\"$_btn_update\" accesskey=\"s\" />\n"
			."$add_top\n"
			."<input type=\"checkbox\" name=\"notimestamp\" value=\"true\" $checked_time /><span class=\"small\">$_btn_notchangetimestamp</span>\n"
			."</div>\n"
			."</form>\n";
	}
	else
	{
		$postdata = user_rules_str($postdata);

		// 差分ファイルの作成
		if(is_page($post["page"]))
			$oldpostdata = join("",get_source($post["page"]));
		else
			$oldpostdata = "\n";
		if($postdata)
			$diffdata = do_diff($oldpostdata,$postdata);
		file_write(DIFF_DIR,$post["page"],$diffdata);

		// バックアップの作成
		if(is_page($post["page"]))
			$oldposttime = filemtime(get_filename(encode($post["page"])));
		else
			$oldposttime = time();

		// 編集内容が何も書かれていないとバックアップも削除する?しないですよね。
		if(!$postdata && $del_backup)
			backup_delete(BACKUP_DIR.encode($post["page"]).".txt");
		else if($do_backup && is_page($post["page"]))
			make_backup(encode($post["page"]).".txt",$oldpostdata,$oldposttime);

		// ファイルの書き込み
		file_write(DATA_DIR,$post["page"],$postdata);

		// is_pageのキャッシュをクリアする。
		is_page($post["page"],true);

		if($postdata)
		{
			$title = str_replace('$1',htmlspecialchars(strip_bracket($post["page"])),$_title_updated);
			$page = str_replace('$1',make_search($post["page"]),$_title_updated);
			$body = convert_html($postdata);
			header("Location: $script?".rawurlencode($post["page"]));
		}
		else
		{
			$title = str_replace('$1',htmlspecialchars(strip_bracket($post["page"])),$_title_deleted);
			$page = str_replace('$1',make_search($post["page"]),$_title_deleted);
			$body = str_replace('$1',htmlspecialchars(strip_bracket($post["page"])),$_title_deleted);
		}
	}
}
// 凍結
else if(arg_check("freeze") && $vars["page"] && $function_freeze)
{
	if(is_freeze($vars["page"]))
	{
		$title = str_replace('$1',htmlspecialchars(strip_bracket($vars["page"])),$_title_isfreezed);
		$page = str_replace('$1',make_search($vars["page"]),$_title_isfreezed);
		$body = str_replace('$1',htmlspecialchars(strip_bracket($vars["page"])),$_title_isfreezed);
	}
	else if(md5($post["pass"]) == $adminpass)
	{
		$postdata = get_source($post["page"]);
		$postdata = join("",$postdata);
		$postdata = "#freeze\n".$postdata;

		file_write(DATA_DIR,$vars["page"],$postdata);

		$title = str_replace('$1',htmlspecialchars(strip_bracket($vars["page"])),$_title_freezed);
		$page = str_replace('$1',make_search($vars["page"]),$_title_freezed);
		$postdata = join("",get_source($vars["page"]));
		$postdata = convert_html($postdata);

		$body = $postdata;
	}
	else
	{
		$title = str_replace('$1',htmlspecialchars(strip_bracket($vars["page"])),$_title_freeze);
		$page = str_replace('$1',make_search($vars["page"]),$_title_freeze);

		$body.= "<br />\n";

		if($post["pass"])
			$body .= "<strong>$_msg_invalidpass</strong><br />\n";
		else
			$body.= "$_msg_freezing<br />\n";

		$body.= "<form action=\"$script?cmd=freeze\" method=\"post\">\n";
		$body.= "<div>\n";
		$body.= "<input type=\"hidden\" name=\"page\" value=\"".htmlspecialchars($vars["page"])."\" />\n";
		$body.= "<input type=\"password\" name=\"pass\" size=\"12\" />\n";
		$body.= "<input type=\"submit\" name=\"ok\" value=\"$_btn_freeze\" />\n";
		$body.= "</div>\n";
		$body.= "</form>\n";
	}
}
//凍結の解除
else if(arg_check("unfreeze") && $vars["page"] && $function_freeze)
{
	if(!is_freeze($vars["page"]))
	{
		$title = str_replace('$1',htmlspecialchars(strip_bracket($vars["page"])),$_title_isunfreezed);
		$page = str_replace('$1',make_search($vars["page"]),$_title_isunfreezed);
		$body = str_replace('$1',htmlspecialchars(strip_bracket($vars["page"])),$_title_isunfreezed);
	}
	else if(md5($post["pass"]) == $adminpass)
	{
		$postdata = get_source($post["page"]);
		array_shift($postdata);
		$postdata = join("",$postdata);

		file_write(DATA_DIR,$vars["page"],$postdata);

		$title = str_replace('$1',htmlspecialchars(strip_bracket($vars["page"])),$_title_unfreezed);
		$page = str_replace('$1',make_search($vars["page"]),$_title_unfreezed);

		$postdata = join("",get_source($vars["page"]));
		$postdata = convert_html($postdata);

		$body = $postdata;
	}
	else
	{
		$title = str_replace('$1',htmlspecialchars(strip_bracket($vars["page"])),$_title_unfreeze);
		$page = str_replace('$1',make_search($vars["page"]),$_title_unfreeze);

		$body.= "<br />\n";

		if($post["pass"])
			$body .= "<strong>$_msg_invalidpass</strong><br />\n";
		else
			$body.= "$_msg_unfreezing<br />\n";

		$body.= "<form action=\"$script?cmd=unfreeze\" method=\"post\">\n";
		$body.= "<div>\n";
		$body.= "<input type=\"hidden\" name=\"page\" value=\"".htmlspecialchars($vars["page"])."\" />\n";
		$body.= "<input type=\"password\" name=\"pass\" size=\"12\" />\n";
		$body.= "<input type=\"submit\" name=\"ok\" value=\"$_btn_unfreeze\" />\n";
		$body.= "</div>\n";
		$body.= "</form>\n";
	}
}
// 差分の表示
else if(arg_check("diff"))
{
	$pagename = htmlspecialchars(strip_bracket($get["page"]));
	if(!is_page($get["page"]))
	{
		$title = htmlspecialchars($pagename);
		$page = make_search($vars["page"]);
		$body = $_msg_notfound;
	}
	else
	{
		$link = str_replace('$1',"<a href=\"$script?".rawurlencode($get["page"])."\">$pagename</a>",$_msg_goto);

		$body =  "<ul>\n"
			."<li>$_msg_addline</li>\n"
			."<li>$_msg_delline</li>\n"
			."<li>$link</li>\n"
			."</ul>\n"
			."$hr\n";
	}

	if(!file_exists(DIFF_DIR.encode($get["page"]).".txt") && is_page($get["page"]))
	{
		$title = str_replace('$1',htmlspecialchars(strip_bracket($get["page"])),$_title_diff);
		$page = str_replace('$1',make_search($get["page"]),$_title_diff);

		$diffdata = htmlspecialchars(join("",get_source($get["page"])));
		$body .= "<pre style=\"color=:blue\">\n"
			.$diffdata
			."\n"
			."</pre>\n";
	}
	else if(file_exists(DIFF_DIR.encode($get["page"]).".txt"))
	{
		$title = str_replace('$1',htmlspecialchars(strip_bracket($get["page"])),$_title_diff);
		$page = str_replace('$1',make_search($get["page"]),$_title_diff);

		$diffdata = file(DIFF_DIR.encode($get["page"]).".txt");
                for ($i = 0; $i < count($diffdata); $i++) { $diffdata[$i] = htmlspecialchars($diffdata[$i]); }
		$diffdata = preg_replace("/^(\-)(.*)/","<span class=\"diff_removed\"> $2</span>",$diffdata);
		$diffdata = preg_replace("/^(\+)(.*)/","<span class=\"diff_added\"> $2</span>",$diffdata);

		$body .= "<pre>\n"
                        .join("",$diffdata)
			."\n"
			."</pre>\n";
	}
}
// 検索
else if(arg_check("search"))
{
	if($vars["word"])
	{
		$title = $page = str_replace('$1',htmlspecialchars($vars["word"]),$_title_result);
	}
	else
	{
		$page = $title = $_title_search;
	}

	if($vars["word"])
		$body = do_search($vars["word"],$vars["type"]);
	else
		$body = "<br />\n$_msg_searching";

	if($vars["type"]=="AND" || !$vars["type"]) $and_check = "checked=\"checked\"";
	else if($vars["type"]=="OR")               $or_check = "checked=\"checked\"";

	$body .= "<form action=\"$script?cmd=search\" method=\"post\">\n"
		."<div>\n"
		."<input type=\"text\" name=\"word\" size=\"20\" value=\"".htmlspecialchars($vars["word"])."\" />\n"
		."<input type=\"radio\" name=\"type\" value=\"AND\" $and_check />$_btn_and\n"
		."<input type=\"radio\" name=\"type\" value=\"OR\" $or_check />$_btn_or\n"
		."&nbsp;<input type=\"submit\" value=\"$_btn_search\" />\n"
		."</div>\n"
		."</form>\n";
}
// バックアップ
else if($do_backup && arg_check("backup"))
{
	if(!is_numeric($get["age"])) {
		unset($get["age"]);
	}

	if($get["page"] && $get["age"] && (file_exists(BACKUP_DIR.encode($get["page"]).".txt") || file_exists(BACKUP_DIR.encode($get["page"]).".gz")))
	{
		$pagename = htmlspecialchars(strip_bracket($get["page"]));
		$body =  "<ul>\n";

		$body .= "<li><a href=\"$script?cmd=backup\">$_msg_backuplist</a></li>\n";

		if(!arg_check("backup_diff") && is_page($get["page"]))
		{
 			$link = str_replace('$1',"<a href=\"$script?cmd=backup_diff&amp;page=".rawurlencode($get["page"])."&amp;age=".htmlspecialchars($get["age"])."\">$_msg_diff</a>",$_msg_view);
			$body .= "<li>$link</li>\n";
		}
		if(!arg_check("backup_nowdiff") && is_page($get["page"]))
		{
 			$link = str_replace('$1',"<a href=\"$script?cmd=backup_nowdiff&amp;page=".rawurlencode($get["page"])."&amp;age=".htmlspecialchars($get["age"])."\">$_msg_nowdiff</a>",$_msg_view);
			$body .= "<li>$link</li>\n";
		}
		if(!arg_check("backup_source"))
		{
 			$link = str_replace('$1',"<a href=\"$script?cmd=backup_source&amp;page=".rawurlencode($get["page"])."&amp;age=".htmlspecialchars($get["age"])."\">$_msg_source</a>",$_msg_view);
			$body .= "<li>$link</li>\n";
		}
		if(arg_check("backup_diff") || arg_check("backup_source") || arg_check("backup_nowdiff"))
		{
 			$link = str_replace('$1',"<a href=\"$script?cmd=backup&amp;page=".rawurlencode($get["page"])."&amp;age=".htmlspecialchars($get["age"])."\">$_msg_backup</a>",$_msg_view);
			$body .= "<li>$link</li>\n";
		}

		if(is_page($get["page"]))
		{
			$link = str_replace('$1',"<a href=\"$script?".rawurlencode($get["page"])."\">".htmlspecialchars($pagename)."</a>",$_msg_goto);
			$body .=  "<li>$link\n";
		}
		else
		{
			$link = str_replace('$1',htmlspecialchars($pagename),$_msg_deleleted);
			$body .=  "<li>$link\n";
		}

		$backups = array();
		$backups = get_backup_info(encode($get["page"]).".txt");
		if(count($backups)) $body .= "<ul>\n";
		foreach($backups as $key => $val)
		{
			$ins_date = date($date_format,$val);
			$ins_time = date($time_format,$val);
			$ins_week = "(".$weeklabels[date("w",$val)].")";
			$backupdate = "($ins_date $ins_week $ins_time)";
			if($key != $get["age"])
 				$body .= "<li><a href=\"$script?cmd=$get[cmd]&amp;page=".rawurlencode($get["page"])."&amp;age=$key\">$key $backupdate</a></li>\n";
			else
				$body .= "<li><em>$key $backupdate</em></li>\n";
		}
		if(count($backups)) $body .= "</ul>\n";
		$body .= "</li>\n";

		if(arg_check("backup_diff"))
		{
			$title = str_replace('$1',htmlspecialchars($pagename),$_title_backupdiff)."(No.".htmlspecialchars($get["age"]).")";
			$page = str_replace('$1',make_search($get["page"]),$_title_backupdiff)."(No.".htmlspecialchars($get["age"]).")";

			$backupdata = @join("",get_backup($get["age"]-1,encode($get["page"]).".txt"));
			$postdata = @join("",get_backup($get["age"],encode($get["page"]).".txt"));
			$diffdata = split("\n",do_diff($backupdata,$postdata));
			$backupdata = htmlspecialchars($backupdata);
		}
		else if(arg_check("backup_nowdiff"))
		{
			$title = str_replace('$1',$pagename,$_title_backupnowdiff)."(No.".htmlspecialchars($get["age"]).")";
			$page = str_replace('$1',make_search($get["page"]),$_title_backupnowdiff)."(No.".htmlspecialchars($get["age"]).")";

			$backupdata = @join("",get_backup($get["age"],encode($get["page"]).".txt"));
			$postdata = @join("",get_source($get["page"]));
			$diffdata = split("\n",do_diff($backupdata,$postdata));
			$backupdata = htmlspecialchars($backupdata);

		}
		else if(arg_check("backup_source"))
		{
			$title = str_replace('$1',$pagename,$_title_backupsource)."(No.".htmlspecialchars($get["age"]).")";
			$page = str_replace('$1',make_search($get["page"]),$_title_backupsource)."(No.".htmlspecialchars($get["age"]).")";
			$backupdata = htmlspecialchars(join("",get_backup($get["age"],encode($get["page"]).".txt")));

			$body.="</ul>\n<pre>\n$backupdata</pre>\n";
		}
		else
		{
			$title = str_replace('$1',$pagename,$_title_backup)."(No.".htmlspecialchars($get["age"]).")";
			$page = str_replace('$1',make_search($get["page"]),$_title_backup)."(No.".htmlspecialchars($get["age"]).")";
			$backupdata = join("",get_backup($get["age"],encode($get["page"]).".txt"));
			$backupdata = convert_html($backupdata);
			$body .= "</ul>\n"
				."$hr\n";
			$body .= $backupdata;
		}

		if(arg_check("backup_diff") || arg_check("backup_nowdiff"))
		{
                  for ($i = 0; $i < count($diffdata); $i++) { $diffdata[$i] = htmlspecialchars($diffdata[$i]); }
			$diffdata = preg_replace("/^(\-)(.*)/","<span class=\"diff_removed\"> $2</span>",$diffdata);
			$diffdata = preg_replace("/^(\+)(.*)/","<span class=\"diff_added\"> $2</span>",$diffdata);

			$body .= "</ul><br /><ul>\n"
				."<li>$_msg_addline</li>\n"
				."<li>$_msg_delline</li>\n"
				."</ul>\n"
				."$hr\n"
				."<pre>\n".join("\n",$diffdata)."</pre>\n";
		}
	}
	else if($get["page"] && (file_exists(BACKUP_DIR.encode($get["page"]).".txt") || file_exists(BACKUP_DIR.encode($get["page"]).".gz")))
	{
		$title = str_replace('$1',htmlspecialchars(strip_bracket($get["page"])),$_title_pagebackuplist);
		$page = str_replace('$1',make_search($get["page"]),$_title_pagebackuplist);
		$body = get_backup_list($get["page"]);
	}
	else
	{
		$page = $title = $_title_backuplist;
		$body = get_backup_list();
	}
}
// ヘルプの表示
else if(arg_check("help"))
{
	$title = $page = "ヘルプ";
	$body = catrule();
}
// MD5パスワードへの変換
else if(isset($vars["md5"]))
{
	$title = $page = 'Make password of MD5';
	$body = htmlspecialchars($vars['md5']).' : '.md5($vars['md5']);
}
else if(arg_check("rss"))
{
	if(!arg_check("rss10"))
		catrss(1);
	else
		catrss(2);
	die();
}
// ページの表示とInterWikiNameの解釈
else if((arg_check("read") && $vars["page"] != "") || (!arg_check("read") && $arg != "" && $vars["page"] == ""))
{
	// アクションを明示的に指定していない場合ページ名として解釈
	if($arg != "" && (! isset($vars["page"]) || $vars["page"] == "")  &&
			 (! isset($vars["cmd"])  || $vars["cmd"]  == ""))
	{
		$post["page"] = $arg;
		$get["page"] = $arg;
		$vars["page"] = $arg;
	}

	// ページ名がWikiNameでなく、BracketNameでなければBracketNameとして解釈
	if(!preg_match("/^(($WikiName)|($BracketName)|($InterWikiName))$/",$get["page"]))
	{
		$vars["page"] = "[[".$vars["page"]."]]";
		$get["page"] = $vars["page"];
	}

	// WikiName、BracketNameが示すページを表示
	if(is_page($get["page"]))
	{
		$postdata = join("",get_source($get["page"]));
		$postdata = convert_html($postdata);

		$title = htmlspecialchars(strip_bracket($get["page"]));
		$page = make_search($get["page"]);
		$body = $postdata;

		header_lastmod($vars["page"]);
	}
	else if(preg_match("/($InterWikiName)/",$get["page"],$match))
	{
		// InterWikiName は表示の際にレンダリングされるため、もはやここに到達しない
		$title = $page = $_title_invalidiwn;
		$body = str_replace('$1',htmlspecialchars(strip_bracket($get["page"])),str_replace('$2',"<a href=\"$script?InterWikiName\">InterWikiName</a>",$_msg_invalidiwn));
	}
	// WikiName、BracketNameが見つからず、InterWikiNameでもない場合
	else
	{
		//$title = strip_bracket($get["page"]);
		//$page = make_search($get["page"]);
		//$body = "指定されたページは見つかりませんでした。";

		if(preg_match("/^(($BracketName)|($WikiName))$/",$get["page"])) {
			$title = str_replace('$1',htmlspecialchars(strip_bracket($get["page"])),$_title_edit);
			$page = str_replace('$1',make_search($get["page"]),$_title_edit);
			$template = auto_template($get["page"]);
			$body = edit_form($template,$get["page"]);
			$vars["cmd"] = "edit";
		}
		else {
			$title = str_replace('$1',htmlspecialchars(strip_bracket($get["page"])),$_title_invalidwn);
			$body = $page = str_replace('$1',make_search($get["page"]), str_replace('$2','WikiName',$_msg_invalidiwn));
			$template = '';
		}
	}
}
// 何も指定されない場合、トップページを表示
else
{
	$postdata = join("",get_source($defaultpage));

	$vars["page"] = $defaultpage;
	$title = htmlspecialchars(strip_bracket($defaultpage));
	$page = make_search($vars["page"]);
	$body = convert_html($postdata);

	header_lastmod($vars["page"]);
}

// ** 出力処理 **
catbody($title,$page,$body);

// ** 終了 **
?>
