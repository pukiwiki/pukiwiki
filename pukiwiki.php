<?
// pukiwiki.php - Yet another WikiWikiWeb clone.
//
// Copyright (C) 2001,2002 by sng.
// <sng@factage.com>
// http://factage.com/sng/pukiwiki/
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
//# $Id: pukiwiki.php,v 1.1 2002/06/20 13:59:31 masui Exp $
/////////////////////////////////////////////////

/////////////////////////////////////////////////
// 設定ファイルの場所
define("INI_FILE","./pukiwiki.ini.php");

//** 初期設定 **

define("S_VERSION","1.3");
define("UTIME",time());
define("HTTP_USER_AGENT",$HTTP_SERVER_VARS["HTTP_USER_AGENT"]);
define("PHP_SELF",$HTTP_SERVER_VARS["PHP_SELF"]);
define("SERVER_NAME",$HTTP_SERVER_VARS["SERVER_NAME"]);

define("MUTIME",getmicrotime());

$script = basename($PHP_SELF);

$WikiName = '([A-Z][a-z]+([A-Z][a-z]+)+)';
$BracketName = '\[\[(\[*[^\s\]]+?\]*)\]\]';
$InterWikiName = '\[\[(\[*[^\s\]]+?\]*):(\[*[^>\]]+?\]*)\]\]';

//** 入力値の整形 **

$post = $HTTP_POST_VARS;
$get = $HTTP_GET_VARS;

if($get["page"]) $get["page"] = rawurldecode($get["page"]);
if($post["word"]) $post["word"] = rawurldecode($post["word"]);
if($get["word"]) $get["word"] = rawurldecode($get["word"]);
if(get_magic_quotes_gpc())
{
	if($get["page"]) $get["page"] = stripslashes($get["page"]);
	if($post["page"]) $post["page"] = stripslashes($post["page"]);
	if($get["word"]) $get["word"] = stripslashes($get["word"]);
	if($post["word"]) $post["word"] = stripslashes($post["word"]);
	if($post["msg"]) $post["msg"] = stripslashes($post["msg"]);
}
if($post["msg"])
{
	$post["msg"] = preg_replace("/<\/(textarea[^>]*)>/i", "&lt;/$1&gt;", $post["msg"]);
	$post["msg"] = preg_replace("/(\x0D\x0A)/","\n",$post["msg"]);
	$post["msg"] = preg_replace("/(\x0D)/","\n",$post["msg"]);
	$post["msg"] = preg_replace("/(\x0A)/","\n",$post["msg"]);
}

$vars = array_merge($post,$get);
$arg = rawurldecode($HTTP_SERVER_VARS["argv"][0]);

//** 初期処理 **

$update_exec = "";

// 設定ファイルの読込
@require(INI_FILE);
@require(LANG.".lng");

// 設定ファイルの変数チェック
$wrong_ini_file = "";
if(!isset($rss_max)) $wrong_ini_file .= '$rss_max ';
if(!isset($page_title)) $wrong_ini_file .= '$page_title ';
if(!isset($note_hr)) $wrong_ini_file .= '$note_hr ';
if(!isset($related_link)) $wrong_ini_file .= '$related_link ';
if(!isset($show_passage)) $wrong_ini_file .= '$show_passage ';
if(!isset($rule_related_str)) $wrong_ini_file .= '$rule_related_str ';
if(!isset($load_template_func)) $wrong_ini_file .= '$load_template_func ';
if(!defined("LANG")) $wrong_ini_file .= 'LANG ';
if(!defined("PLUGIN_DIR")) $wrong_ini_file .= 'PLUGIN_DIR ';

if(!is_writable(DATA_DIR))
	die_message("DATA_DIR is not found or not writable.");
if(!is_writable(DIFF_DIR))
	die_message("DIFF_DIR is not found or not writable.");
if($do_backup && !is_writable(BACKUP_DIR))
	die_message("BACKUP_DIR is not found or not writable.");
if(!file_exists(INI_FILE))
	die_message("INI_FILE is not found.");
if($wrong_ini_file)
	die_message("The setting file runs short of information.<br>The version of a setting file may be old.<br><br>These option are not found : $wrong_ini_file");
//if(ini_get("register_globals") !== "0")
//	die_message("Wrong PHP4 setting in 'register_globals',set value 'Off' to httpd.conf or .htaccess.");
if(!file_exists(SKIN_FILE))
	die_message("SKIN_FILE is not found.");
if(!file_exists(LANG.".lng"))
	die_message(LANG.".lng(language file) is not found.");

if(!file_exists(get_filename(encode($defaultpage))))
	touch(get_filename(encode($defaultpage)));
if(!file_exists(get_filename(encode($whatsnew))))
	touch(get_filename(encode($whatsnew)));
if(!file_exists(get_filename(encode($interwiki))))
	touch(get_filename(encode($interwiki)));

$ins_date = date($date_format,UTIME);
$ins_time = date($time_format,UTIME);
$ins_week = "(".$weeklabels[date("w",UTIME)].")";

$now = "$ins_date $ins_week $ins_time";

// ** メイン処理 **

// Plug-in hook
if(isset($vars["plugin"]))
{
	if(!file_exists(PLUGIN_DIR.$vars["plugin"].".inc.php"))
	{
		$vars["plugin"] = "";
	}
	else
	{
		require_once(PLUGIN_DIR.$vars["plugin"].".inc.php");
		if(!function_exists("plugin_".$vars["plugin"]."_action"))
		{
			$vars["plugin"] = "";
		}
	}
}


// Plug-in action
if(!empty($vars["plugin"]))
{
	$retvars = @call_user_func("plugin_".$vars["plugin"]."_action");
	
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
		$body = @join("",@file(get_filename(encode($vars["refer"]))));
		$body = convert_html($body);
	}
}
// 一覧の表示
else if(arg_check("list"))
{
	header_lastmod($whatsnew);
	
	$page = $title = $_title_list;
	$body = "<ul>\n" . get_list(false) . "</ul>\n";
}
// ファイル名一覧の表示
else if(arg_check("filelist"))
{
	header_lastmod($whatsnew);

	$page = $title = $_title_filelist;
	$body = "<ul>\n" . get_list(true) . "</ul>\n";
}
// 編集不可能なページを編集しようとしたとき
else if(((arg_check("add") || arg_check("edit") || arg_check("preview")) && (is_freeze($vars["page"]) || !is_editable($vars["page"]) || $vars["page"] == "")))
{
	$body = $title = str_replace('$1',strip_bracket($vars["page"]),$_title_cannotedit);
	$page = str_replace('$1',make_search($vars["page"]),$_title_cannotedit);

	if(is_freeze($vars["page"]))
		$body .= "(<a href=\"$script?cmd=unfreeze&page=".rawurlencode($vars["page"])."\">$_msg_unfreeze</a>)";
}
// 追加
else if(arg_check("add"))
{
	$title = str_replace('$1',strip_bracket($get["page"]),$_title_add);
	$page = str_replace('$1',make_search($get["page"]),$_title_add);
	$body = "<ul>\n";
	$body .= "<li>$_msg_add</li>\n";
	$body .= "</ul>\n";
	$body .= edit_form("",$get["page"],true);
}
// 編集
else if(arg_check("edit"))
{
	$postdata = @join("",@file(get_filename(encode($get["page"]))));

	$title = str_replace('$1',strip_bracket($get["page"]),$_title_edit);
	$page = str_replace('$1',make_search($get["page"]),$_title_edit);
	$body = edit_form($postdata,$get["page"]);
}
// プレビュー
else if(arg_check("preview") || $post["preview"] || $post["template"])
{
	if($post["template"] && file_exists(get_filename(encode($post["template_page"]))))
	{
		$post["msg"] = @join("",@file(get_filename(encode($post["template_page"]))));
	}
	
	$post["msg"] = preg_replace("/^#freeze\n/","",$post["msg"]);
	$postdata_input = $post["msg"];

	if($post["add"])
	{
		if($post["add_top"])
		{
			$postdata  = $post["msg"];
			$postdata .= "\n\n";
			$postdata .= @join("",@file(get_filename(encode($post["page"]))));
		}
		else
		{
			$postdata  = @join("",@file(get_filename(encode($post["page"]))));
			$postdata .= "\n\n";
			$postdata .= $post["msg"];
		}
	}
	else
	{
		$postdata = $post["msg"];
	}

	$title = str_replace('$1',strip_bracket($post["page"]),$_title_preview);
	$page = str_replace('$1',make_search($post["page"]),$_title_preview);

	$body = "$_msg_preview<br>\n";
	if($postdata == "") $body .= "<b>$_msg_preview_delete</b><br>\n";
	else                $body .= "<br>\n";

	if($postdata != "")
	{
		$postdata = convert_html($postdata);
		
		$body .= "<table width=\"100%\" bgcolor=\"$preview_color\">\n"
			."<tr><td>\n"
			.$postdata
			."\n</td></tr>\n"
			."</table>\n";
	}

	if($post["add"])
	{
		if($post["add_top"]) $checked_top = " checked";
		$addtag = '<input type="hidden" name="add" value="true">';
		$add_top = '<input type="checkbox" name="add_top" value="true"'.$checked_top.'><small>ページの上に追加</small>';
	}
	if($post["notimestamp"]) $checked_time = "checked";

	$body .= "<form action=\"$script\" method=\"post\">\n"
		."<input type=\"hidden\" name=\"help\" value=\"$post[add]\">\n"
		."<input type=\"hidden\" name=\"page\" value=\"".$post["page"]."\">\n"
		."<input type=\"hidden\" name=\"digest\" value=\"".$post["digest"]."\">\n"
		."$addtag\n"
		."<textarea name=\"msg\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\">\n$postdata_input</textarea><br>\n"
		."<input type=\"submit\" name=\"preview\" value=\"$_btn_repreview\" accesskey=\"p\">\n"
		."<input type=\"submit\" name=\"write\" value=\"$_btn_update\" accesskey=\"s\">\n"
		."$add_top\n"
		."<input type=\"checkbox\" name=\"notimestamp\" value=\"true\" $checked_time><small>$_btn_notchangetimestamp</small>\n"
		."</form>\n";
}
// 書き込みもしくは追加もしくはコメントの挿入
else if($post["write"])
{
	$post["msg"] = preg_replace("/^#freeze\n/","",$post["msg"]);
	$postdata_input = $post["msg"];

	if($post["add"])
	{
		if($post["add_top"])
		{
			$postdata  = $post["msg"];
			$postdata .= "\n\n";
			$postdata .= @join("",@file(get_filename(encode($post["page"]))));
		}
		else
		{
			$postdata  = @join("",@file(get_filename(encode($post["page"]))));
			$postdata .= "\n\n";
			$postdata .= $post["msg"];
		}
	}
	else
	{
		$postdata = $post["msg"];
	}

	if(md5(@join("",@file(get_filename(encode($post["page"]))))) != $post["digest"])
	{
		$title = str_replace('$1',strip_bracket($post["page"]),$_title_collided);
		$page = str_replace('$1',make_search($post["page"]),$_title_collided);
		
		$body .= "$_msg_collided\n";

		$body .= "<form action=\"$script?cmd=preview\" method=\"post\">\n"
			."<input type=\"hidden\" name=\"page\" value=\"".$post["page"]."\">\n"
			."<input type=\"hidden\" name=\"digest\" value=\"".$post["digest"]."\">\n"
			."<textarea name=\"msg\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\" id=\"textarea\">$postdata_input</textarea><br>\n"
			."</form>\n";
	}
	else
	{
		$postdata = user_rules_str($postdata);

		// 差分ファイルの作成
		if(is_page($post["page"]))
			$oldpostdata = join("",file(get_filename(encode($post["page"]))));
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
			$title = str_replace('$1',strip_bracket($post["page"]),$_title_updated);
			$page = str_replace('$1',make_search($post["page"]),$_title_updated);
			$body = convert_html($postdata);
		}
		else
		{
			$title = str_replace('$1',strip_bracket($post["page"]),$_title_deleted);
			$page = str_replace('$1',make_search($post["page"]),$_title_deleted);
			$body = str_replace('$1',strip_bracket($post["page"]),$_title_deleted);
		}
	}
}
// 凍結
else if(arg_check("freeze") && $vars["page"] && $function_freeze)
{
	if(is_freeze($vars["page"]))
	{
		$title = str_replace('$1',strip_bracket($vars["page"]),$_title_isfreezed);
		$page = str_replace('$1',make_search($vars["page"]),$_title_isfreezed);
		$body = str_replace('$1',strip_bracket($vars["page"]),$_title_isfreezed);
	}
	else if(md5($post["pass"]) == $adminpass)
	{
		$postdata = file(get_filename(encode($post["page"])));
		$postdata = join("",$postdata);
		$postdata = "#freeze\n".$postdata;

		file_write(DATA_DIR,$vars["page"],$postdata);

		$title = str_replace('$1',strip_bracket($vars["page"]),$_title_freezed);
		$page = str_replace('$1',make_search($vars["page"]),$_title_freezed);
		$postdata = join("",file(get_filename(encode($vars["page"]))));
		$postdata = convert_html($postdata);

		$body = $postdata;
	}
	else
	{
		$title = str_replace('$1',strip_bracket($vars["page"]),$_title_freeze);
		$page = str_replace('$1',make_search($vars["page"]),$_title_freeze);

		$body.= "<br>\n";
		
		if($post["pass"])
			$body .= "<b>$_msg_invalidpass</b><br>\n";
		else
			$body.= "$_msg_freezing<br>\n";
		
		$body.= "<form action=\"$script?cmd=freeze\" method=\"post\">\n";
		$body.= "<input type=\"hidden\" name=\"page\" value=\"$vars[page]\">\n";
		$body.= "<input type=\"password\" name=\"pass\" size=\"12\">\n";
		$body.= "<input type=\"submit\" name=\"ok\" value=\"$_btn_freeze\">\n";
		$body.= "</form>\n";
	}
}
//凍結の解除
else if(arg_check("unfreeze") && $vars["page"] && $function_freeze)
{
	if(!is_freeze($vars["page"]))
	{
		$title = str_replace('$1',strip_bracket($vars["page"]),$_title_isunfreezed);
		$page = str_replace('$1',make_search($vars["page"]),$_title_isunfreezed);
		$body = str_replace('$1',strip_bracket($vars["page"]),$_title_isunfreezed);
	}
	else if(md5($post["pass"]) == $adminpass)
	{
		$postdata = file(get_filename(encode($post["page"])));
		array_shift($postdata);
		$postdata = join("",$postdata);

		file_write(DATA_DIR,$vars["page"],$postdata);

		$title = str_replace('$1',strip_bracket($vars["page"]),$_title_unfreezed);
		$page = str_replace('$1',make_search($vars["page"]),$_title_unfreezed);
		
		$postdata = join("",file(get_filename(encode($vars["page"]))));
		$postdata = convert_html($postdata);
		
		$body = $postdata;
	}
	else
	{
		$title = str_replace('$1',strip_bracket($vars["page"]),$_title_unfreeze);
		$page = str_replace('$1',make_search($vars["page"]),$_title_unfreeze);

		$body.= "<br>\n";

		if($post["pass"])
			$body .= "<b>$_msg_invalidpass</b><br>\n";
		else
			$body.= "$_msg_unfreezing<br>\n";

		$body.= "<form action=\"$script?cmd=unfreeze\" method=\"post\">\n";
		$body.= "<input type=\"hidden\" name=\"page\" value=\"$vars[page]\">\n";
		$body.= "<input type=\"password\" name=\"pass\" size=\"12\">\n";
		$body.= "<input type=\"submit\" name=\"ok\" value=\"$_btn_unfreeze\">\n";
		$body.= "</form>\n";
	}
}
// 差分の表示
else if(arg_check("diff"))
{
	$pagename = strip_bracket($get["page"]);
	if(!is_page($get["page"]))
	{
		$title = $pagename;
		$page = make_search($vars["page"]);
		$body = "指定されたページは見つかりませんでした。";
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
		$title = str_replace('$1',strip_bracket($get["page"]),$_title_diff);
		$page = str_replace('$1',make_search($get["page"]),$_title_diff);

		$diffdata = file(get_filename(encode($get["page"])));
		$body .= "<font color=\"blue\">\n"
			."<pre>\n"
			.join("",$diffdata)
			."\n"
			."</pre>\n"
			."</font>\n";
	}
	else if(file_exists(DIFF_DIR.encode($get["page"]).".txt"))
	{
		$title = str_replace('$1',strip_bracket($get["page"]),$_title_diff);
		$page = str_replace('$1',make_search($get["page"]),$_title_diff);

		$diffdata = file(DIFF_DIR.encode($get["page"]).".txt");
		$diffdata = preg_replace("/</","&lt;",$diffdata);
		$diffdata = preg_replace("/>/","&gt;",$diffdata);
		$diffdata = preg_replace("/^(\-)(.*)/","<font color=\"red\"> $2</font>",$diffdata);
		$diffdata = preg_replace("/^(\+)(.*)/","<font color=\"blue\"> $2</font>",$diffdata);
		
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
		$title = $page = str_replace('$1',$vars["word"],$_title_result);
	}
	else
	{
		$page = $title = $_title_search;
	}

	if($vars["word"])
		$body = do_search($vars["word"],$vars["type"]);
	else
		$body = "<br>\n$_msg_searching";

	if($vars["type"]=="AND" || !$vars["type"]) $and_check = "checked";
	else if($vars["type"]=="OR")               $or_check = "checked";

	$body .= "<form action=\"$script?cmd=search\" method=\"post\">\n"
		."<input type=\"text\" name=\"word\" size=\"20\" value=\"".$vars["word"]."\">\n"
		."<input type=\"radio\" name=\"type\" value=\"AND\" $and_check>$_btn_and\n"
		."<input type=\"radio\" name=\"type\" value=\"OR\" $or_check>$_btn_or\n"
		."&nbsp;<input type=\"submit\" value=\"$_btn_search\">\n"
		."</form>\n";
}
// バックアップ
else if($do_backup && arg_check("backup"))
{
	if($get["page"] && $get["age"] && (file_exists(BACKUP_DIR.encode($get["page"]).".txt") || file_exists(BACKUP_DIR.encode($get["page"]).".gz")))
	{
		$pagename = strip_bracket($get["page"]);
		$body =  "<ul>\n";

		$body .= "<li><a href=\"$script?cmd=backup\">$_msg_backuplist</a></li>\n";

		if(!arg_check("backup_diff") && is_page($get["page"]))
		{
			$link = str_replace('$1',"<a href=\"$script?cmd=backup_diff&page=".rawurlencode($get["page"])."&age=$get[age]\">$_msg_diff</a>",$_msg_view);
			$body .= "<li>$link</li>\n";
		}
		if(!arg_check("backup_nowdiff") && is_page($get["page"]))
		{
			$link = str_replace('$1',"<a href=\"$script?cmd=backup_nowdiff&page=".rawurlencode($get["page"])."&age=$get[age]\">$_msg_nowdiff</a>",$_msg_view);
			$body .= "<li>$link</li>\n";
		}
		if(!arg_check("backup_source"))
		{
			$link = str_replace('$1',"<a href=\"$script?cmd=backup_source&page=".rawurlencode($get["page"])."&age=$get[age]\">$_msg_source</a>",$_msg_view);
			$body .= "<li>$link</li>\n";
		}
		if(arg_check("backup_diff") || arg_check("backup_source") || arg_check("backup_nowdiff"))
		{
			$link = str_replace('$1',"<a href=\"$script?cmd=backup&page=".rawurlencode($get["page"])."&age=$get[age]\">$_msg_backup</a>",$_msg_view);
			$body .= "<li>$link</li>\n";
		}
		
		if(is_page($get["page"]))
		{
			$link = str_replace('$1',"<a href=\"$script?".rawurlencode($get["page"])."\">$pagename</a>",$_msg_goto);
			$body .=  "<li>$link</li>\n";
		}
		else
		{
			$link = str_replace('$1',$pagename,$_msg_deleleted);
			$body .=  "<li>$link</li>\n";
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
				$body .= "<li><a href=\"$script?cmd=$get[cmd]&page=".rawurlencode($get["page"])."&age=$key\">$key $backupdate</a></li>\n";
			else
				$body .= "<li><i>$key $backupdate</i></li>\n";
		}
		if(count($backups)) $body .= "</ul>\n";
		
		if(arg_check("backup_diff"))
		{
			$title = str_replace('$1',$pagename,$_title_backupdiff)."(No.$get[age])";
			$page = str_replace('$1',make_search($get["page"]),$_title_backupdiff)."(No.$get[age])";
			
			$backupdata = @join("",get_backup($get[age]-1,encode($get["page"]).".txt"));
			$postdata = @join("",get_backup($get[age],encode($get["page"]).".txt"));
			$diffdata = split("\n",do_diff($backupdata,$postdata));
		}
		else if(arg_check("backup_nowdiff"))
		{
			$title = str_replace('$1',$pagename,$_title_backupnowdiff)."(No.$get[age])";
			$page = str_replace('$1',make_search($get["page"]),$_title_backupnowdiff)."(No.$get[age])";
			
			$backupdata = @join("",get_backup($get[age],encode($get["page"]).".txt"));
			$postdata = @join("",@file(get_filename(encode($get["page"]))));
			$diffdata = split("\n",do_diff($backupdata,$postdata));
		}
		else if(arg_check("backup_source"))
		{
			$title = str_replace('$1',$pagename,$_title_backupsource)."(No.$get[age])";
			$page = str_replace('$1',make_search($get["page"]),$_title_backupsource)."(No.$get[age])";
			$backupdata = join("",get_backup($get[age],encode($get["page"]).".txt"));
			
			$body.="</ul>\n<pre>\n$backupdata</pre>\n";
		}
		else
		{
			$pagename = strip_bracket($get["page"]);
			$title = str_replace('$1',$pagename,$_title_backup)."(No.$get[age])";
			$page = str_replace('$1',make_search($get["page"]),$_title_backup)."(No.$get[age])";
			$backupdata = join("",get_backup($get[age],encode($get["page"]).".txt"));
			$backupdata = convert_html($backupdata);
			$body .= "</ul>\n"
				."$hr\n";
			$body .= $backupdata;
		}
		
		if(arg_check("backup_diff") || arg_check("backup_nowdiff"))
		{
			$diffdata = preg_replace("/</","&lt;",$diffdata);
			$diffdata = preg_replace("/>/","&gt;",$diffdata);
			$diffdata = preg_replace("/^(\-)(.*)/","<font color=\"red\"> $2</font>",$diffdata);
			$diffdata = preg_replace("/^(\+)(.*)/","<font color=\"blue\"> $2</font>",$diffdata);

			$body .= "<br>\n"
				."<li>$_msg_addline</li>\n"
				."<li>$_msg_delline</li>\n"
				."</ul>\n"
				."$hr\n"
				."<pre>\n".join("\n",$diffdata)."</pre>\n";
		}
	}
	else if($get["page"] && (file_exists(BACKUP_DIR.encode($get["page"]).".txt") || file_exists(BACKUP_DIR.encode($get["page"]).".gz")))
	{
		$title = str_replace('$1',strip_bracket($get["page"]),$_title_pagebackuplist);
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
else if($vars["md5"])
{
	$title = $page = "Make password of MD5";
	$body = "$vars[md5] : ".md5($vars["md5"]);
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
	if($arg != "" && $vars["page"] == "" && $vars["cmd"] == "")
	{
		$post["page"] = $arg;
		$get["page"] = $arg;
		$vars["page"] = $arg;
	}
	
	// ページ名がWikiNameでなく、BracketNameでなければBracketNameとして解釈
	if(!preg_match("/^(($WikiName)|($BracketName)|($InterWikiName))$/",$get["page"]))
	{
		$vars["page"] = "[[$vars[page]]]";
		$get["page"] = $vars["page"];
	}

	// WikiName、BracketNameが示すページを表示
	if(is_page($get["page"]))
	{
		$postdata = join("",file(get_filename(encode($get["page"]))));
		$postdata = convert_html($postdata);

		$title = strip_bracket($get["page"]);
		$page = make_search($get["page"]);
		$body = $postdata;

		header_lastmod($vars["page"]);
	}
	else if(preg_match("/($InterWikiName)/",$get["page"],$match))
	{
	// InterWikiNameの判別とページの表示
		$interwikis = open_interwikiname_list();
		
		if(!$interwikis[$match[2]]["url"])
		{
			$title = $page = $_title_invalidiwn;
			$body = str_replace('$1',strip_bracket($get[page]),str_replace('$2',"<a href=\"$script?InterWikiName\">InterWikiName</a>",$_msg_invalidiwn));
		}
		else
		{
			// 文字エンコーディング
			if($interwikis[$match[2]]["opt"] == "yw")
			{
				// YukiWiki系
				if(!preg_match("/$WikiName/",$match[3]))
					$match[3] = "[[".mb_convert_encoding($match[3],"SJIS","auto")."]]";
			}
			else if($interwikis[$match[2]]["opt"] == "moin")
			{
				// moin系
				if(function_exists("mb_convert_encoding"))
				{
					$match[3] = rawurlencode(mb_convert_encoding($match[3],"EUC-JP","auto"));
					$match[3] = str_replace("%","_",$match[3]);
				}
				else
					$not_mb = 1;
			}
			else if($interwikis[$match[2]]["opt"] == "" || $interwikis[$match[2]]["opt"] == "std")
			{
				// 内部文字エンコーディングのままURLエンコード
				$match[3] = rawurlencode($match[3]);
			}
			else if($interwikis[$match[2]]["opt"] == "asis" || $interwikis[$match[2]]["opt"] == "raw")
			{
				// URLエンコードしない
				$match[3] = $match[3];
			}
			else if($interwikis[$match[2]]["opt"] != "")
			{
				// エイリアスの変換
				if($interwikis[$match[2]]["opt"] == "sjis")
					$interwikis[$match[2]]["opt"] = "SJIS";
				else if($interwikis[$match[2]]["opt"] == "euc")
					$interwikis[$match[2]]["opt"] = "EUC-JP";
				else if($interwikis[$match[2]]["opt"] == "utf8")
					$interwikis[$match[2]]["opt"] = "UTF-8";

				// その他、指定された文字コードへエンコードしてURLエンコード
				if(function_exists("mb_convert_encoding"))
					$match[3] = rawurlencode(mb_convert_encoding($match[3],$interwikis[$match[2]]["opt"],"auto"));
				else
					$not_mb = 1;
			}

			if(strpos($interwikis[$match[2]]["url"],'$1') !== FALSE)
				$url = str_replace('$1',$match[3],$interwikis[$match[2]]["url"]);
			else
				$url = $interwikis[$match[2]]["url"] . $match[3];

			if($not_mb)
			{
				$title = $page = "Not support mb_jstring.";
				$body = "This server's PHP does not have \"mb_jstring\" module.Cannot convert encoding.";
			}
			else
			{
				header("Location: $url");
				die();
			}
		}
	}
	// WikiName、BracketNameが見つからず、InterWikiNameでもない場合
	else
	{
		//$title = strip_bracket($get["page"]);
		//$page = make_search($get["page"]);
		//$body = "指定されたページは見つかりませんでした。";

		$title = str_replace('$1',strip_bracket($get["page"]),$_title_edit);
		$page = str_replace('$1',make_search($get["page"]),$_title_edit);
		$body = edit_form("",$get["page"]);
	}
}
// 何も指定されない場合、トップページを表示
else
{
	$postdata = join("",file(get_filename(encode($defaultpage))));

	$vars["page"] = $defaultpage;
	$title = strip_bracket($defaultpage);
	$page = make_search($vars["page"]);
	$body = convert_html($postdata);

	header_lastmod($vars["page"]);
}

// ** 出力処理 **

catbody($title,$page,$body);

// ** 各種関数 **

// 本文をページ名から出力
function catbodyall($page,$title="",$pg="")
{
	if($title === "") $title = strip_bracket($page);
	if($pg === "") $pg = make_search($page);

	$body = join("",file(get_filename(encode($page))));
	$body = convert_html($body);

	header_lastmod($vars["page"]);
	catbody($title,$pg,$body);
	die();
}

// 本文を出力
function catbody($title,$page,$body)
{
	global $script,$vars,$arg,$do_backup,$modifier,$modifierlink,$defaultpage,$whatsnew,$hr;
	global $date_format,$weeklabels,$time_format,$longtaketime,$related_link;
	global $HTTP_SERVER_VARS,$cantedit;

	if($vars["page"] && !arg_check("backup") && $vars["page"] != $whatsnew)
	{
		$is_page = 1;
	}

	$link_add = "$script?cmd=add&page=".rawurlencode($vars["page"]);
	$link_edit = "$script?cmd=edit&page=".rawurlencode($vars["page"]);
	$link_diff = "$script?cmd=diff&page=".rawurlencode($vars["page"]);
	$link_top = "$script?$defaultpage";
	$link_list = "$script?cmd=list";
	$link_filelist = "$script?cmd=filelist";
	$link_search = "$script?cmd=search";
	$link_whatsnew = "$script?$whatsnew";
	$link_backup = "$script?cmd=backup&page=".rawurlencode($vars["page"]);
	$link_help = "$script?cmd=help";

	if(is_page($vars["page"]) && $is_page)
	{
		$fmt = @filemtime(get_filename(encode($vars["page"])));
	}

	if(is_page($vars["page"]) && $related_link && $is_page && !arg_check("edit") && !arg_check("freeze") && !arg_check("unfreeze"))
	{
		$related = make_related($vars["page"],false);
	}

	if(is_page($vars["page"]) && !in_array($vars["page"],$cantedit) && !arg_check("backup") && !arg_check("edit") && !$vars["preview"])
	{
		$is_read = TRUE;
	}

	//if(!$longtaketime)
		$longtaketime = getmicrotime() - MUTIME;
	$taketime = sprintf("%01.03f",$longtaketime);

	require(SKIN_FILE);
}

// ファイルへの出力
function file_write($dir,$page,$str)
{
	global $post,$update_exec;

	if($str == "")
	{
		@unlink($dir.encode($page).".txt");
	}
	else
	{
		if($post["notimestamp"] && is_page($page))
		{
			$timestamp = @filemtime($dir.encode($page).".txt");
		}
		$fp = fopen($dir.encode($page).".txt","w");
		while(!flock($fp,LOCK_EX));
		fputs($fp,$str);
		flock($fp,LOCK_UN);
		fclose($fp);
		if($timestamp)
			touch($dir.encode($page).".txt",$timestamp);
	}
	
	if(!$timestamp)
		put_lastmodified();

	if($update_exec)
	{
		system($update_exec." > /dev/null &");
	}
}

// バックアップ一覧の取得
function get_backup_list($_page="")
{
	global $script,$date_format,$time_format,$weeklabels,$cantedit;
	global $_msg_backuplist,$_msg_diff,$_msg_nowdiff,$_msg_source;

	$ins_date = date($date_format,$val);
	$ins_time = date($time_format,$val);
	$ins_week = "(".$weeklabels[date("w",$val)].")";
	$ins = "$ins_date $ins_week $ins_time";

	if (($dir = @opendir(BACKUP_DIR)) && !$_page)
	{
		while($file = readdir($dir))
		{
			if(function_exists(gzopen))
				$file = str_replace(".txt",".gz",$file);

			if($file == ".." || $file == ".") continue;
			$page = decode(trim(preg_replace("/(\.txt)|(\.gz)$/"," ",$file)));
			if(in_array($page,$cantedit)) continue;
			$page_url = rawurlencode($page);
			$name = $page;
			$name = strip_bracket($name);
			if(is_page($page))
				$vals[$name]["link"] = "<li><a href=\"$script?$page_url\">$name</a></li>";
			else
				$vals[$name]["link"] = "<li>$name</li>";
			$vals[$name]["name"] = $page;
		}
		closedir($dir);
		$vals = list_sort($vals);
		$retvars[] = "<ul>";
	}
	else
	{
		$page_url = rawurlencode($_page);
		$name = strip_bracket($_page);
		$vals[$name]["link"] = "";
		$vals[$name]["name"] = $_page;
		$retvars[] = "<ul>";
		$retvars[] .= "<li><a href=\"$script?cmd=backup\">$_msg_backuplist</a></li>\n";
	}
	
	
	foreach($vals as $page => $line)
	{
		$arybackups = get_backup_info(encode($line["name"]).".txt");
		$page_url = rawurlencode($line["name"]);
		if(count($arybackups)) $line["link"] .= "\n<ul>\n";
		foreach($arybackups as $key => $val)
		{
			$ins_date = date($date_format,$val);
			$ins_time = date($time_format,$val);
			$ins_week = "(".$weeklabels[date("w",$val)].")";
			$backupdate = "($ins_date $ins_week $ins_time)";
			if(!$_page)
			{
				$line["link"] .= "<li><a href=\"$script?cmd=backup&page=$page_url&age=$key\">$key $backupdate</a></li>\n";
			}
			else
			{
				$line["link"] .= "<li><a href=\"$script?cmd=backup&page=$page_url&age=$key\">$key $backupdate</a> [ <a href=\"$script?cmd=backup_diff&page=$page_url&age=$key\">$_msg_diff</a> | <a href=\"$script?cmd=backup_nowdiff&page=$page_url&age=$key\">$_msg_nowdiff</a> | <a href=\"$script?cmd=backup_source&page=$page_url&age=$key\">$_msg_source</a> ]</li>\n";
			}
		}
		if(count($arybackups)) $line["link"] .= "</ul>";
		$retvars[] = $line["link"];
	}
	$retvars[] = "</ul>";
	
	return join("\n",$retvars);
}

// 最終更新ページの更新
function put_lastmodified()
{
	global $script,$maxshow,$whatsnew,$date_format,$time_format,$weeklabels,$post,$non_list;

	if($post["notimestamp"]) return;

	if ($dir = @opendir(DATA_DIR))
	{
		while($file = readdir($dir))
		{
			$page = decode(trim(preg_replace("/\.txt$/"," ",$file)));

			if($page == $whatsnew || $file == "." || $file == "..") continue;
			if(preg_match("/$non_list/",$page)) continue;

			if(file_exists(get_filename(encode($page))))
			{
				$page_url = rawurlencode($page);
				$lastmodtime = filemtime(get_filename(encode($page)));
				$lastmod = date($date_format,$lastmodtime)
					 . " (" . $weeklabels[date("w",$lastmodtime)] . ") "
					 . date($time_format,$lastmodtime);
				$putval[$lastmodtime][] = "-$lastmod - $page";
			}
		}
		closedir($dir);
	}
	
	$cnt = 1;
	krsort($putval);
	$fp = fopen(get_filename(encode($whatsnew)),"w");
	flock($fp,LOCK_EX);
	foreach($putval as $pages)
	{
		foreach($pages as $page)
		{
			fputs($fp,$page."\n");
			$cnt++;
			if($cnt > $maxshow) break;
		}
		if($cnt > $maxshow) break;
	}
	flock($fp,LOCK_EX);
	fclose($fp);
}

// 検索
function do_search($word,$type="AND",$non_format=0)
{
	global $script,$whatsnew,$vars;
	global $_msg_andresult,$_msg_orresult,$_msg_notfoundresult;
	
	$database = array();
	$retval = array();
	$cnt = 0;

	if ($dir = @opendir(DATA_DIR))
	{
		while($file = readdir($dir))
		{
			if($file == ".." || $file == ".") continue;
			$cnt++;
			$page = decode(trim(preg_replace("/\.txt$/"," ",$file)));
			if($page == $whatsnew) continue;
			if($page == $vars["page"] && $non_format) continue;
			$data[$page] = file(DATA_DIR.$file);
		}
		closedir($dir);
	}
	
	$arywords = explode(" ",$word);
	$result_word = $word;
	
	foreach($data as $name => $lines)
	{
		$line = join("\n",$lines);
		
		$hit = 0;
		if(strpos($result_word," ") !== FALSE)
		{
			foreach($arywords as $word)
			{
				if($type=="AND")
				{
					if(strpos($line,$word) === FALSE)
					{
						$hit = 0;
						break;
					}
					else
					{
						$hit = 1;
					}
				}
				else if($type=="OR")
				{
					if(strpos($line,$word) !== FALSE)
						$hit = 1;
				}
			}
			if($hit==1 || strpos($name,$word)!==FALSE)
			{
				$page_url = rawurlencode($name);
				$word_url = rawurlencode($word);
				$name2 = strip_bracket($name);
				$str = get_pg_passage($name);
				$retval[$name2] = "<li><a href=\"$script?$page_url\">$name2</a>$str</li>";
			}
		}
		else
		{
			if(stristr($line,$word) || stristr($name,$word))
			{
				$page_url = rawurlencode($name);
				$word_url = rawurlencode($word);
				$name2 = strip_bracket($name);
				$link_tag = "<a href=\"$script?$page_url\">$name2</a>";
				$link_tag .= get_pg_passage($name,false);
				if($non_format)
				{
					$tm = @filemtime(get_filename(encode($name)));
					$retval[$tm] = $link_tag;
				}
				else
				{
					$retval[$name2] = "<li>$link_tag</li>";
				}
			}
		}
	}

	if($non_format)
		return $retval;

	$retval = list_sort($retval);

	if(count($retval) && !$non_format)
	{
		$retvals = "<ul>\n" . join("\n",$retval) . "</ul>\n<br>\n";
		
		if($type=="AND")
			$retvals.= str_replace('$1',$result_word,str_replace('$2',count($retval),str_replace('$3',$cnt,$_msg_andresult)));
		else
			$retvals.= str_replace('$1',$result_word,str_replace('$2',count($retval),str_replace('$3',$cnt,$_msg_orresult)));

	}
	else
		$retvals .= str_replace('$1',$result_word,$_msg_notfoundresult);
	return $retvals;
}

// 差分の作成
function do_diff($strlines1,$strlines2)
{
	$lines1 = split("\n",$strlines1);
	$lines2 = split("\n",$strlines2);
	
	$same_lines = $diff_lines = $del_lines = $add_lines = $retdiff = array();
	
	if(count($lines1) > count($lines2)) { $max_line = count($lines1)+2; }
	else                                { $max_line = count($lines2)+2; }

	//$same_lines = array_intersect($lines1,$lines2);

	$diff_lines = array_diff($lines1,$lines2);
	$diff_lines = array_merge($diff_lines,array_diff($lines2,$lines1));

	foreach($diff_lines as $line)
	{
		$index = array_search($line,$lines1);
		if($index > -1)
		{
			$del_lines[$index] = $line;
		}
		
		//$index = array_search($line,$lines2);
		//if($index > -1)
		//{
		//	$add_lines[$index] = $line;
		//}
	}

	$cnt=0;
	foreach($lines2 as $line)
	{
		$line = rtrim($line);
		
		while($del_lines[$cnt])
		{
			$retdiff[] = "- ".$del_lines[$cnt];
			$del_lines[$cnt] = "";
			$cnt++;
		}
		
		if(in_array($line,$diff_lines))
		{
			$retdiff[] = "+ $line";
		}
		else
		{
			$retdiff[] = "  $line";
		}

		$cnt++;
	}
	
	foreach($del_lines as $line)
	{
		if(trim($line))
			$retdiff[] = "- $line";
	}

	return join("\n",$retdiff);
}

// 一覧の取得
function get_list($withfilename)
{
	global $script,$list_index,$top,$non_list,$whatsnew;
	global $_msg_symbol,$_msg_other;
	
	$retval = array();
	if ($dir = @opendir(DATA_DIR))
	{
		while($file = readdir($dir))
		{
			$page = decode(trim(preg_replace("/\.txt$/"," ",$file)));
			if($file == ".." || $file == ".") continue;
			if(preg_match("/$non_list/",$page) && !$withfilename) continue;
			if($page == $whatsnew) continue;
			$page_url = rawurlencode($page);
			$page2 = strip_bracket($page);
			$pg_passage = get_pg_passage($page);
			$retval[$page2] .= "<li><a href=\"$script?$page_url\">$page2</a>$pg_passage</li>\n";
			if($withfilename)
			{
				$retval[$page2] .= "<ul><li>$file</li></ul>\n";
			}
		}
		closedir($dir);
	}
	
	$retval = list_sort($retval);
	
	if($list_index)
	{
		$head_str = "";
		$etc_sw = 0;
		$symbol_sw = 0;
		$top_link = "";
		foreach($retval as $page => $link)
		{
			$head = substr($page,0,1);
			if($head_str != $head && !$etc_sw)
			{
				$retval2[$page] = "";
				
				if(preg_match("/([A-Z])|([a-z])/",$head,$match))
				{
					if($match[1])
						$head_nm = "High:$head";
					else
						$head_nm = "Low:$head";
					
					if($head_str) $retval2[$page] = "</ul>\n";
					$retval2[$page] .= "<li><a href=\"#top:$head_nm\" name=\"$head_nm\"><b>$head</b></a></li>\n<ul>\n";
					$head_str = $head;
					if($top_link) $top_link .= "|";
					$top_link .= "<a href=\"#$head_nm\" name=\"top:$head_nm\"><b>&nbsp;".$head."&nbsp;</b></a>";
				}
				else if(preg_match("/[ -~]/",$head))
				{
					if(!$symbol_sw)
					{
						if($head_str) $retval2[$page] = "</ul>\n";
						$retval2[$page] .= "<li><a href=\"#top:symbol\" name=\"symbol\"><b>$_msg_symbol</b></a></li>\n<ul>\n";
						$head_str = $head;
						if($top_link) $top_link .= "|";
						$top_link .= "<a href=\"#symbol\" name=\"top:symbol\"><b>$_msg_symbol</b></a>";
						$symbol_sw = 1;
					}
				}
				else
				{
					if($head_str) $retval2[$page] = "</ul>\n";
					$retval2[$page] .= "<li><a href=\"#top:etc\" name=\"etc\"><b>$_msg_other</b></a></li>\n<ul>\n";
					$etc_sw = 1;
					if($top_link) $top_link .= "|";
					$top_link .= "<a href=\"#etc\" name=\"top:etc\"><b>$_msg_other</b></a>";
				}
			}
			$retval2[$page] .= $link;
		}
		$retval2[] = "</ul>\n";
		
		$top_link = "<div align=\"center\"><a name=\"top\">$top_link</a></div><br>\n";
		
		array_unshift($retval2,$top_link);
	}
	else
	{
		$retval2 = $retval;
	}
	
	return join("",$retval2);
}

// 編集フォームの表示
function edit_form($postdata,$page,$add=0)
{
	global $script,$rows,$cols,$hr,$vars,$function_freeze;
	global $_btn_addtop,$_btn_preview,$_btn_update,$_btn_freeze,$_msg_help,$_btn_notchangetimestamp;
	global $whatsnew,$_btn_template,$_btn_load,$non_list,$load_template_func;

	$digest = md5(@join("",@file(get_filename(encode($page)))));

	if($add)
	{
		$addtag = '<input type="hidden" name="add" value="true">';
		$add_top = '<input type="checkbox" name="add_top" value="true"><small>'.$_btn_addtop.'</small>';
	}

	if($vars["help"] == "true")
		$help = $hr.catrule();
	else
		$help = "<br>\n<ul><li><a href=\"$script?cmd=edit&help=true&page=".rawurlencode($page)."\">$_msg_help</a></ul></li>\n";

	if($function_freeze)
		$str_freeze = '<input type="submit" name="freeze" value="'.$_btn_freeze.'" accesskey="f">';

	if($load_template_func)
	{
		$vals = array();
		if ($dir = @opendir(DATA_DIR))
		{
			while($file = readdir($dir))
			{
				$pg_org = decode(trim(preg_replace("/\.txt$/"," ",$file)));
				if($file == ".." || $file == "." || $pg_org == $whatsnew) continue;
				if(preg_match("/$non_list/",$pg_org)) continue;
				$name = strip_bracket($pg_org);
				$vals[$name] = "    <option value=\"$pg_org\">$name</option>";
			}
			closedir($dir);
		}
		@ksort($vals);
		
		$template = "   <select name=\"template_page\">\n"
			   ."    <option value=\"\">-- $_btn_template --</option>\n"
			   .join("\n",$vals)
			   ."   </select>\n"
			   ."   <input type=\"submit\" name=\"template\" value=\"$_btn_load\" accesskey=\"r\"><br>\n";

		if($vars["refer"]) $refer = $vars["refer"]."\n\n";
	}

return '
<form action="'.$script.'" method="post">
<input type="hidden" name="page" value="'.$page.'">
<input type="hidden" name="digest" value="'.$digest.'">
'.$addtag.'
<table cellspacing="3" cellpadding="0" border="0">
 <tr>
  <td colspan="2" align="right">
'.$template.'
  </td>
 </tr>
 <tr>
  <td colspan="2" align="right">
   <textarea name="msg" rows="'.$rows.'" cols="'.$cols.'" wrap="virtual">
'.$refer.$postdata.'</textarea>
  </td>
 </tr>
 <tr>
  <td>
   <input type="submit" name="preview" value="'.$_btn_preview.'" accesskey="p">
   <input type="submit" name="write" value="'.$_btn_update.'" accesskey="s">
   '.$add_top.'
   <input type="checkbox" name="notimestamp" value="true"><small>'.$_btn_notchangetimestamp.'</small>
  </td>
  </form>
  <form action="'.$script.'?cmd=freeze" method="post">
   <input type="hidden" name="page" value="'.$vars["page"].'">
  <td align="right">
   '.$str_freeze.'
  </td>
  </form>
 </tr>
</table>
' . $help;
}

// ファイル名を得る(エンコードされている必要有り)
function get_filename($pagename)
{
	return DATA_DIR.$pagename.".txt";
}

// ページが存在するかしないか
function is_page($page,$reload=false)
{
	global $InterWikiName,$_ispage;

	if(($_ispage[$page] === true || $_ispage[$page] === false) && !$reload) return $_ispage[$page];

	if(preg_match("/($InterWikiName)/",$page))
		$_ispage[$page] = false;
	else if(!file_exists(get_filename(encode($page))))
		$_ispage[$page] = false;
	else
		$_ispage[$page] = true;
	
	return $_ispage[$page];
}

// ページが編集可能か
function is_editable($page)
{
	global $BracketName,$WikiName,$InterWikiName,$cantedit,$_editable;

	if($_editable === true || $_editable === false) return $_editable;

	if(preg_match("/^$InterWikiName$/",$page))
		$_editable = false;
	elseif(!preg_match("/^$BracketName$/",$page) && !preg_match("/^$WikiName$/",$page))
		$_editable = false;
	else if(in_array($page,$cantedit))
		$_editable = false;
	else
		$_editable = true;
	
	return $_editable;
}

// ページが凍結されているか
function is_freeze($page)
{
	global $_freeze;

	if(!is_page($page)) return false;
	if($_freeze === true || $_freeze === false) return $_freeze;

	$lines = file(get_filename(encode($page)));
	
	if($lines[0] == "#freeze\n")
		$_freeze = true;
	else
		$_freeze = false;
	
	return $_freeze;
}

// プログラムへの引数のチェック
function arg_check($str)
{
	global $arg,$vars;

	return preg_match("/^".$str."/",$vars["cmd"]);
}

// ページリストのソート
function list_sort($values)
{
	if(!is_array($values)) return array();
	
	// ksortのみだと、[[日本語]]、[[英文字]]、英文字のみ、に順に並べ替えられる
	ksort($values);

	$vals1 = array();
	$vals2 = array();
	$vals3 = array();

	// 英文字のみ、[[英文字]]、[[日本語]]、の順に並べ替える
	foreach($values as $key => $val)
	{
		if(preg_match("/\[\[[^\w]+\]\]/",$key))
			$vals3[$key] = $val;
		else if(preg_match("/\[\[[\W]+\]\]/",$key))
			$vals2[$key] = $val;
		else
			$vals1[$key] = $val;
	}
	return array_merge($vals1,$vals2,$vals3);
}

// ページ名のエンコード
function encode($key)
{
	$enkey = '';
	$arych = preg_split("//", $key, -1, PREG_SPLIT_NO_EMPTY);
	
	foreach($arych as $ch)
	{
		$enkey .= sprintf("%02X", ord($ch));
	}

	return $enkey;
}

// ファイル名のデコード
function decode($key)
{
	$dekey = '';
	
	for($i=0;$i<strlen($key);$i+=2)
	{
		$ch = substr($key,$i,2);
		$dekey .= chr(intval("0x".$ch,16));
	}
	return urldecode($dekey);
}

// テキスト本体をHTMLに変換する
function convert_html($string)
{
	global $result,$saved,$hr,$script,$page,$vars,$top;
	global $note_id,$foot_explain,$digest,$note_hr;
	global $user_rules,$str_rules,$line_rules,$strip_link_wall;

	global $longtaketime;

	$string = rtrim($string);
	$string = preg_replace("/(\x0D\x0A)/","\n",$string);
	$string = preg_replace("/(\x0D)/","\n",$string);
	$string = preg_replace("/(\x0A)/","\n",$string);

	$start_mtime = getmicrotime();

	$digest = md5(@join("",@file(get_filename(encode($vars["page"])))));

	$content_id = 0;
	$user_rules = array_merge($str_rules,$line_rules);

	$result = array();
	$saved = array();
	$arycontents = array();

	$string = preg_replace("/^#freeze\n/","",$string);

	$lines = split("\n", $string);
	$note_id = 1;
	$foot_explain = array();

	$table = 0;

	if(preg_match("/#contents/",$string))
		$top_link = "<a href=\"#contents\">$top</a>";

	foreach ($lines as $line)
	{
		if(!preg_match("/^\/\/(.*)/",$line,$comment_out) && $table != 0)
		{
			if(!preg_match("/^\|(.+)\|$/",$line,$out))
				array_push($result, "</table>");
			if(!$out[1] || $table != count(explode("|",$out[1])))
				$table = 0;
		}

		$comment_out = $comment_out[1];

		if(preg_match("/^(\*{1,3})(.*)/",$line,$out))
		{
			$result = array_merge($result,$saved); $saved = array();
			$str = inline($out[2]);
			
			$level = strlen($out[1]) + 1;
			
			array_push($result, "<h$level><a name=\"content:$content_id\">$str</a> $top_link</h$level>");
			$arycontents[] = str_repeat("-",$level-1)."<a href=\"#content:$content_id\">".strip_htmltag($str)."</a>\n";
			$content_id++;
		}
		else if(preg_match("/^(-{1,4})(.*)/",$line,$out))
		{
			if(strlen($out[1]) == 4)
			{
				$result = array_merge($result,$saved); $saved = array();
				array_push($result, $hr);
			}
			else
			{
				back_push('ul', strlen($out[1]));
				array_push($result, '<li>' . inline($out[2]) . '</li>');
			}
		}
		else if (preg_match("/^:([^:]+):(.*)/",$line,$out))
		{
			back_push('dl', 1);
			array_push($result, '<dt>' . inline($out[1]) . '</dt>', '<dd>' . inline($out[2]) . '</dd>');
		}
		else if(preg_match("/^(>{1,3})(.*)/",$line,$out))
		{
			back_push('blockquote', strlen($out[1]));
			array_push($result, ltrim(inline($out[2])));
		}
		else if (preg_match("/^\s*$/",$line,$out))
		{
			$result = array_merge($result,$saved); $saved = array();
			//array_unshift($saved, "</p>");
			array_push($result, "<p>");
		}
		else if(preg_match("/^(\s+.*)/",$line,$out))
		{
			back_push('pre', 1);
			array_push($result, htmlspecialchars($out[1],ENT_NOQUOTES));
		}
		else if(preg_match("/^\|(.+)\|$/",$line,$out))
		{
			$arytable = explode("|",$out[1]);

			if(!$table)
			{
				$result = array_merge($result,$saved); $saved = array();
				array_push($result,"<table class=\"style_table\" cellspacing=\"1\" border=\"0\">");
				$table = count($arytable);
			}

			array_push($result,"<tr>");
			foreach($arytable as $td)
			{
				array_push($result,"<td class=\"style_td\">");
				array_push($result,ltrim(inline($td)));
				array_push($result,"</td>");
			}
			array_push($result,"</tr>");

		}
		else if(strlen($comment_out) != 0)
		{
			array_push($result," <!-- ".htmlspecialchars($comment_out)." -->");
		}
		else
		{
			array_push($result, inline($line));
		}
	}
	if($table) array_push($result, "</table>");

	$result_last = $result = array_merge($result,$saved); $saved = array();

	if($content_id != 0)
	{
		$result = array();
		$saved = array();

		foreach($arycontents as $line)
		{
			if(preg_match("/^(-{1,3})(.*)/",$line,$out))
			{
				back_push('ul', strlen($out[1]));
				array_push($result, '<li>'.$out[2].'</li>');
			}
		}
		$result = array_merge($result,$saved); $saved = array();
		
		$contents = "<a name=\"contents\"></a>\n";
		$contents .= join("\n",$result);
		if($strip_link_wall)
		{
			$contents = preg_replace("/\[\[([^\]]+)\]\]/","$1",$contents);
		}
	}

	$result_last = inline2($result_last);
	
	$result_last = preg_replace("/^#contents/",$contents,$result_last);

	$str = join("\n", $result_last);

	if($foot_explain)
	{
		$str .= "\n";
		$str .= "$note_hr\n";
		//$str .= "<p>\n";
		$str .= join("\n",inline2($foot_explain));
		//$str .= "</p>\n";
	}

	$longtaketime = getmicrotime() - $start_mtime;

	$str = preg_replace("/&amp;((lt;)|(gt;))/","&$1",$str);

	return $str;
}

// $tagのタグを$levelレベルまで詰める。
function back_push($tag, $level)
{
	global $result,$saved;
	
	while (count($saved) > $level) {
		array_push($result, array_shift($saved));
	}
	if ($saved[0] != "</$tag>") {
		$result = array_merge($result,$saved); $saved = array();
	}
	while (count($saved) < $level) {
		array_unshift($saved, "</$tag>");
		array_push($result, "<$tag>");
	}
}

// リンクの付加その他
function inline($line)
{
	$line = htmlspecialchars($line);

	$line = preg_replace("/(

					(\(\(([^\(\)]+)\)\))
					|
					(\(\((.+)\)\))

				)/ex","make_note(\"$1\")",$line);

	return $line;
}

// リンクの付加その他2
function inline2($str)
{
	global $WikiName,$BracketName,$InterWikiName,$vars,$related,$related_link,$script;
	$cnts_plain = array();
	$cnts_plugin = array();
	$arykeep = array();

	for($cnt=0;$cnt<count($str);$cnt++)
	{
		if(preg_match("/^(\s)/",$str[$cnt]))
		{
			$arykeep[$cnt] = $str[$cnt];
			$str[$cnt] = "";
			$cnts_plain[] = $cnt;
		}
		else if(preg_match("/^\#([^\(]+)\(?(.*)\)?$/",$str[$cnt],$match))
		{
			if(file_exists(PLUGIN_DIR.$match[1].".inc.php"))
			{
				require_once(PLUGIN_DIR.$match[1].".inc.php");
				if(function_exists("plugin_".$match[1]."_convert"))
				{
					$aryplugins[$cnt] = $str[$cnt];
					$str[$cnt] = "";
					$cnts_plugin[] = $cnt;
				}
			}
		}
	}

	$str = preg_replace("/'''([^']+?)'''/s","<i>$1</i>",$str);	// Italic

	$str = preg_replace("/''([^']+?)''/s","<b>$1</b>",$str);	// Bold

	$str = preg_replace("/
		(
			(\[\[([^\]]+)\:(https?|ftp|news)(:\/\/[-_.!~*'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)\]\])
			|
			(\[(https?|ftp|news)(:\/\/[-_.!~*'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)\s([^\]]+)\])
			|
			(https?|ftp|news)(:\/\/[-_.!~*'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)
			|
			([[:alnum:]\-_.]+@[[:alnum:]\-_]+\.[[:alnum:]\-_\.]+)
			|
			(\[\[([^\]]+)\:([[:alnum:]\-_.]+@[[:alnum:]\-_]+\.[[:alnum:]\-_\.]+)\]\])
			|
			($InterWikiName)
			|
			($BracketName)
			|
			($WikiName)
		)/ex","make_link('$1')",$str);

	$str = preg_replace("/#related/",make_related($vars["page"],true),$str);

	$str = make_user_rules($str);

	$aryplugins = preg_replace("/^\#([^\(]+)$/ex","plugin_convert('$1','$2')",$aryplugins);
	$aryplugins = preg_replace("/^\#([^\(]+)\((.*)\)$/ex","plugin_convert('$1','$2')",$aryplugins);

	$tmp = $str;
	$str = preg_replace("/^#norelated$/","",$str);
	if($tmp != $str)
		$related_link = 0;

	foreach($cnts_plain as $cnt)
		$str[$cnt] = $arykeep[$cnt];

	foreach($cnts_plugin as $cnt)
		$str[$cnt] = $aryplugins[$cnt];

	return $str;
}

// Plug-in
function plugin_convert($plugin_name,$plugin_args)
{
	$invalid_return = "#${plugin_name}(${plugin_args})";
	
	if($plugin_args !== "")
		$aryargs = explode(",",$plugin_args);
	else
		$aryargs = array();
	$retvar = call_user_func_array("plugin_${plugin_name}_convert",$aryargs);

	if($retvar === FALSE) return $invalid_return;
	else                  return $retvar;
}

// 関連するページ
function make_related($page,$_isrule)
{
	global $related_str,$rule_related_str,$related,$_make_related,$vars;

	$page_name = strip_bracket($vars["page"]);

	if(!is_array($_make_related))
	{
		$aryrelated = do_search($page,"OR",1);

		if(is_array($aryrelated))
		{
			foreach($aryrelated as $key => $val)
			{
				$new_arylerated[$key.md5($val)] = $val;
			}
		}

		if(is_array($related))
		{
			foreach($related as $key => $val)
			{
				$new_arylerated[$key.md5($val)] = $val;
			}
		}

		@krsort($new_arylerated);
		$_make_related = @array_unique($new_arylerated);
	}

	if($_isrule)
	{
		if(is_array($_make_related))
		{
			foreach($_make_related as $str)
			{
				preg_match("/<a\shref=\"([^\"]+)\">([^<]+)<\/a>(.*)/",$str,$out);
				
				if($out[3]) $title = " title=\"$out[2] $out[3]\"";
				
				$aryret[$out[2]] = "<a href=\"$out[1]\"$title>$out[2]</a>";
			}
			@ksort($aryret);
		}
	}
	else
	{
		$aryret = $_make_related;
	}

	if($_isrule) $str = $rule_related_str;
	else         $str = $related_str;

	return @join($str,$aryret);
}

// 注釈処理
function make_note($str)
{
	global $note_id,$foot_explain;

	$str = preg_replace("/^\(\(/","",$str);
	$str = preg_replace("/\)\)$/","",$str);

	$str= str_replace("\\'","'",$str);

	$str = make_user_rules($str);

	$foot_explain[] = "<a name=\"notefoot:$note_id\" href=\"#notetext:$note_id\"><sup><small>*$note_id</small></sup></a> <small>$str</small><br />\n";
	$note =  "<a name=\"notetext:$note_id\" href=\"#notefoot:$note_id\"><sup><small>*$note_id</small></sup></a>";
	$note_id++;

	return $note;
}

// リンクを付加する
function make_link($name)
{
	global $BracketName,$WikiName,$InterWikiName,$script,$link_target,$interwiki_target;
	global $related,$show_passage,$vars,$defaultpage;

	$aryconv_htmlspecial = array("&amp;","&lt;","&gt;");
	$aryconv_html = array("&","<",">");

	$page = $name;

	if(preg_match("/^\[\[([^\]]+)\:((https?|ftp|news)([^\]]+))\]\]$/",$name,$match))
	{
		$match[2] = str_replace($aryconv_htmlspecial,$aryconv_html,$match[2]);
		return "<a href=\"$match[2]\" target=\"$link_target\">$match[1]</a>";
	}
	else if(preg_match("/^\[((https?|ftp|news)([^\]\s]+))\s([^\]]+)\]$/",$name,$match))
	{
		$match[1] = str_replace($aryconv_htmlspecial,$aryconv_html,$match[1]);
		return "<a href=\"$match[1]\" target=\"$link_target\">$match[4]</a>";
	}
	else if(preg_match("/^(https?|ftp|news).*?(\.gif|\.png|\.jpeg|\.jpg)?$/",$name,$match))
	{
		$name = str_replace($aryconv_htmlspecial,$aryconv_html,$name);
		if($match[2])
			return "<a href=\"$name\" target=\"$link_target\"><img src=\"$name\" border=\"0\"></a>";
		else
			return "<a href=\"$name\" target=\"$link_target\">$page</a>";
	}
	else if(preg_match("/^\[\[([^\]]+)\:([[:alnum:]\-_.]+@[[:alnum:]\-_]+\.[[:alnum:]\-_\.]+)\]\]/",$name,$match))
	{
		$match[1] = str_replace($aryconv_htmlspecial,$aryconv_html,$match[1]);
		$match[2] = str_replace($aryconv_htmlspecial,$aryconv_html,$match[2]);

		return "<a href=\"mailto:$match[2]\">$match[1]</a>";
	}
	else if(preg_match("/^([[:alnum:]\-_]+@[[:alnum:]\-_]+\.[[:alnum:]\-_\.]+)/",$name))
	{
		$name = str_replace($aryconv_htmlspecial,$aryconv_html,$name);
		return "<a href=\"mailto:$name\">$page</a>";
	}
	else if(preg_match("/^($InterWikiName)$/",str_replace($aryconv_htmlspecial,$aryconv_html,$name)))
	{
		$page = strip_bracket($page);
		$percent_name = str_replace($aryconv_htmlspecial,$aryconv_html,$name);
		$percent_name = rawurlencode($percent_name);

		return "<a href=\"$script?$percent_name\" target=\"$interwiki_target\">$page</a>";
	}
	else if(preg_match("/^($BracketName)|($WikiName)$/",str_replace($aryconv_htmlspecial,$aryconv_html,$name)))
	{
		if(preg_match("/^([^>]+)>([^>]+)$/",strip_bracket(str_replace($aryconv_htmlspecial,$aryconv_html,$name)),$match))
		{
			$page = $match[1];
			$name = $match[2];
			if(!preg_match("/^($BracketName)|($WikiName)$/",$page))
				$page = "[[$page]]";
			if(!preg_match("/^($BracketName)|($WikiName)$/",$name))
				$name = "[[$name]]";
		}
		
		if(preg_match("/^\[\[\.\/([^\]]*)\]\]/",str_replace($aryconv_htmlspecial,$aryconv_html,$name),$match))
		{
			if(!$match[1])
				$name = $vars["page"];
			else
				$name = "[[".strip_bracket($vars[page])."/$match[1]]]";
		}
		else if(preg_match("/^\[\[\..\/([^\]]+)\]\]/",str_replace($aryconv_htmlspecial,$aryconv_html,$name),$match))
		{
			for($i=0;$i<substr_count($name,"../");$i++)
				$name = preg_replace("/(.+)\/([^\/]+)$/","$1",strip_bracket($vars["page"]));

			if(!preg_match("/^($BracketName)|($WikiName)$/",$name))
				$name = "[[$name]]";
			
			if($vars["page"]==$name)
				$name = "[[$match[1]]]";
			else
				$name = "[[".strip_bracket($name)."/$match[1]]]";
		}
		else if($name == "[[../]]")
		{
			$name = preg_replace("/(.+)\/([^\/]+)$/","$1",strip_bracket($vars["page"]));
			
			if(!preg_match("/^($BracketName)|($WikiName)$/",$name))
				$name = "[[$name]]";
			if($vars["page"]==$name)
				$name = $defaultpage;
		}
		
		$page = strip_bracket($page);
		$pagename = strip_bracket($name);
		$percent_name = str_replace($aryconv_htmlspecial,$aryconv_html,$name);
		$percent_name = rawurlencode($percent_name);

		$refer = rawurlencode($vars["page"]);
		if(is_page($name))
		{
			$str = get_pg_passage($name,false);
			$tm = @filemtime(get_filename(encode($name)));
			if($vars["page"] != $name)
				$related[$tm] = "<a href=\"$script?$percent_name\">$pagename</a>$str";
			if($show_passage)
			{
				$str_title = "title=\"$pagename $str\"";
			}
			return "<a href=\"$script?$percent_name\" $str_title>$page</a>";
		}
		else
			return "<span class=\"noexists\">$page<a href=\"$script?cmd=edit&page=$percent_name&refer=$refer\">?</a></span>";
	}
	else
	{
		return $page;
	}
}

// ユーザ定義ルール(ソースを置換する)
function user_rules_str($str)
{
	global $str_rules;

	$arystr = split("\n",$str);

	// 日付・時刻置換処理
	foreach($arystr as $str)
	{
		if(substr($str,0,1) != " ")
		{
			foreach($str_rules as $rule => $replace)
			{
				$str = preg_replace("/$rule/",$replace,$str);
			}
		}
		$retvars[] = $str;
	}

	return join("\n",$retvars);
}

// ユーザ定義ルール(ソースは置換せずコンバート)
function make_user_rules($str)
{
	global $user_rules;

	foreach($user_rules as $rule => $replace)
	{
		$str = preg_replace("/$rule/",$replace,$str);
	}

	return $str;
}

// InterWikiName List の解釈(返値:２次元配列)
function open_interwikiname_list()
{
	global $interwiki;
	
	$retval = array();
	$aryinterwikiname = file(get_filename(encode($interwiki)));

	$cnt = 0;
	foreach($aryinterwikiname as $line)
	{
		if(preg_match("/\[((https?|ftp|news)(\:\/\/[[:alnum:]\+\$\;\?\.%,!#~\*\/\:@&=_\-]+))\s([^\]]+)\]\s?([^\s]*)/",$line,$match))
		{
			$retval[$match[4]]["url"] = $match[1];
			$retval[$match[4]]["opt"] = $match[5];
		}
	}

	return $retval;
}

// zlib関数が使用できれば、圧縮して使用するためのファイルシステム関数
function backup_fopen($filename,$mode)
{
	if(function_exists(gzopen))
		return gzopen(str_replace(".txt",".gz",$filename),$mode);
	else
		return fopen($filename,$mode);
}
function backup_fputs($zp,$str)
{
	if(function_exists(gzputs))
		return gzputs($zp,$str);
	else
		return fputs($zp,$str);
}
function backup_fclose($zp)
{
	if(function_exists(gzclose))
		return gzclose($zp);
	else
		return fclose($zp);
}
function backup_file($filename)
{
	if(function_exists(gzfile))
		return @gzfile(str_replace(".txt",".gz",$filename));
	else
		return @file($filename);
}
function backup_delete($filename)
{
	if(function_exists(gzopen))
		return @unlink(str_replace(".txt",".gz",$filename));
	else
		return @unlink($filename);
}

// バックアップデータを作成する
function make_backup($filename,$body,$oldtime)
{
	global $splitter,$cycle,$maxage;
	$aryages = array();
	$arystrout = array();

	if(function_exists(gzfile))
		$filename = str_replace(".txt",".gz",$filename);

	$realfilename = BACKUP_DIR.$filename;

	if(time() - @filemtime($realfilename) > (60 * 60 * $cycle))
	{
		$aryages = read_backup($filename);
		if(count($aryages) >= $maxage)
		{
			array_shift($aryages);
		}
		
		foreach($aryages as $lines)
		{
			foreach($lines as $key => $line)
			{
				if($key && $key == "timestamp")
				{
					$arystrout[] = "$splitter " . rtrim($line);
				}
				else
				{
					$arystrout[] = rtrim($line);
				}
			}
		}

		$strout = join("\n",$arystrout);
		if(!preg_match("/\n$/",$strout) && trim($strout)) $strout .= "\n";

		$body = "$splitter " . $oldtime . "\n" . $body;
		if(!preg_match("/\n$/",$body)) $body .= "\n";

		$fp = backup_fopen($realfilename,"w");
		backup_fputs($fp,$strout);
		backup_fputs($fp,$body);
		backup_fclose($fp);
	}
	
	return true;
}

// 特定の世代のバックアップデータを取得
function get_backup($age,$filename)
{
	$aryages = read_backup($filename);
	
	foreach($aryages as $key => $lines)
	{
		if($key != $age) continue;
		foreach($lines as $key => $line)
		{
			if($key && $key == "timestamp") continue;
			$retvars[] = $line;
		}
	}

	return $retvars;
}

// バックアップ情報を返す
function get_backup_info($filename)
{
	global $splitter;
	$lines = array();
	$retvars = array();
	$lines = backup_file(BACKUP_DIR.$filename);

	if(!is_array($lines)) return array();

	$age = 0;
	foreach($lines as $line)
	{
		preg_match("/^$splitter\s(\d+)$/",trim($line),$match);
		if($match[1])
		{
			$age++;
			$retvars[$age] = $match[1];
		}
	}
	
	return $retvars;
}

// バックアップデータ全体を取得
function read_backup($filename)
{
	global $splitter;
	$lines = array();
	$lines = backup_file(BACKUP_DIR.$filename);

	if(!is_array($lines)) return array();

	$age = 0;
	foreach($lines as $line)
	{
		preg_match("/^$splitter\s(\d+)$/",trim($line),$match);
		if($match[1])
		{
			$age++;
			$retvars[$age]["timestamp"] = $match[1] . "\n";
		}
		else
		{
			$retvars[$age][] = $line;
		}
	}

	return $retvars;
}

// [[ ]] を取り除く
function strip_bracket($str)
{
	global $strip_link_wall;
	
	if($strip_link_wall)
	{
		preg_match("/^\[\[(.*)\]\]$/",$str,$match);
		if($match[1])
			$str = $match[1];
	}
	return $str;
}

// HTMLタグを取り除く
function strip_htmltag($str)
{
	//$str = preg_replace("/<a[^>]+>\?<\/a>/","",$str);
	return preg_replace("/<[^>]+>/","",$str);
}

// テキスト整形ルールを表示する
function catrule()
{
	global $rule_body;
	return $rule_body;
}

// エラーメッセージを表示する
function die_message($msg)
{
	$title = $page = "Runtime error";

	$body = "<h3>Runtime error</h3>\n";
	$body .= "<b>Error message : $msg</b>\n";

	catbody($title,$page,$body);

	die();
}

// 指定されたページの経過時刻
function get_pg_passage($page,$sw=true)
{
	global $_pg_passage,$show_passage;

	if(!$show_passage) return "";

	if(isset($_pg_passage[$page]))
	{
		if($sw)
			return $_pg_passage[$page]["str"];
		else
			return $_pg_passage[$page]["label"];
	}
	if($pgdt = @filemtime(get_filename(encode($page))))
	{
		$pgdt = UTIME - $pgdt;
		if(ceil($pgdt / 60) < 60)
			$_pg_passage[$page]["label"] = "(".ceil($pgdt / 60)."m)";
		else if(ceil($pgdt / 60 / 60) < 24)
			$_pg_passage[$page]["label"] = "(".ceil($pgdt / 60 / 60)."h)";
		else
			$_pg_passage[$page]["label"] = "(".ceil($pgdt / 60 / 60 / 24)."d)";
		
		$_pg_passage[$page]["str"] = "<small>".$_pg_passage[$page]["label"]."</small>";
	}
	else
	{
		$_pg_passage[$page]["label"] = "";
		$_pg_passage[$page]["str"] = "";
	}

	if($sw)
		return $_pg_passage[$page]["str"];
	else
		return $_pg_passage[$page]["label"];
}

// 現在時刻をマイクロ秒で取得
function getmicrotime()
{
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$sec + (float)$usec);
}

// ページ名からページ名を検索するリンクを作成
function make_search($page)
{
	global $script,$WikiName;

	$page = htmlspecialchars($page);
	$name = strip_bracket($page);
	$url = rawurlencode($page);

	//WikiWikiWeb like...
	//if(preg_match("/^$WikiName$/",$page))
	//	$name = preg_replace("/([A-Z][a-z]+)/","$1 ",$name);

	return "<a href=\"$script?cmd=search&word=$url\">$name</a> ";
}

// Last-Modified ヘッダ
function header_lastmod($page)
{
	global $lastmod;
	
	if($lastmod && is_page($page))
	{
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(get_filename(encode($page))))." GMT");
	}
}

// RecentChanges の RSS を出力
function catrss($rss)
{
	global $rss_max,$page_title,$WikiName,$BracketName,$script,$whatsnew;

	$lines = file(get_filename(encode($whatsnew)));
	header("Content-type: application/xml");

	$item = "";
	$rdf_li = "";
	$cnt = 0;
	foreach($lines as $line)
	{
		if($cnt > $rss_max - 1) break;

		if(preg_match("/(($WikiName)|($BracketName))/",$line,$match))
		{
			if($match[2])
			{
				$title = $url = $match[1];
			}
			else
			{
				if(function_exists("mb_convert_encoding"))
					$title = mb_convert_encoding(strip_bracket($match[1]),"UTF-8","auto");
				else
					$title = strip_bracket($match[1]);

				$url = $match[1];
			}
			
			$desc = date("D, d M Y H:i:s T",filemtime(get_filename(encode($match[1]))));
			
			if($rss==2)
				$items.= "<item rdf:about=\"http://".SERVER_NAME.PHP_SELF."?".rawurlencode($url)."\">\n";
			else
				$items.= "<item>\n";
			$items.= " <title>$title</title>\n";
			$items.= " <link>http://".SERVER_NAME.PHP_SELF."?".rawurlencode($url)."</link>\n";
			$items.= " <description>$desc</description>\n";
			$items.= "</item>\n\n";
			$rdf_li.= "    <rdf:li rdf:resource=\"http://".SERVER_NAME.PHP_SELF."?".rawurlencode($url)."\"/>\n";

		}

		$cnt++;
	}

	if($rss==1)
	{
?>
<?='<?xml version="1.0" encoding="UTF-8"?>'?>


<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN"
            "http://my.netscape.com/publish/formats/rss-0.91.dtd">

<rss version="0.91">

<channel>
<title><?=$page_title?></title>
<link><?="http://".SERVER_NAME.PHP_SELF."?$whatsnew"?></link>
<description>PukiWiki RecentChanges</description>
<language>ja</language>

<?=$items?>
</channel>
</rss>
<?
	}
	else if($rss==2)
	{
?>
<?='<?xml version="1.0" encoding="utf-8"?>'?>


<rdf:RDF 
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
  xml:lang="ja">

 <channel rdf:about="<?="http://".SERVER_NAME.PHP_SELF."?rss"?>">
  <title><?=$page_title?></title>
  <link><?="http://".SERVER_NAME.PHP_SELF."?$whatsnew"?></link>
  <description>PukiWiki RecentChanges</description>
  <items>
   <rdf:Seq>
<?=$rdf_li?>
   </rdf:Seq>
  </items>
 </channel>

<?=$items?>
</rdf:RDF>
<?
	}
}
?>
