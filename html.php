<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: html.php,v 1.66 2003/03/06 06:09:35 panda Exp $
//

// 本文を出力
function catbody($title,$page,$body)
{
	global $script,$vars,$arg,$defaultpage,$whatsnew,$help_page,$hr;
	global $related_link,$cantedit,$function_freeze,$search_word_color,$_msg_word;
	global $foot_explain,$note_hr;
	
	global $html_transitional; // FALSE:XHTML1.1 TRUE:XHTML1.0 Transitional
	global $page_title;        // ホームページのタイトル
	global $do_backup;         // バックアップを行うかどうか
	global $modifier;          // 編集者のホームページ
	global $modifierlink;      // 編集者の名前

	$_page = $vars['page'];
	$r_page = rawurlencode($_page);
	
	$link_add      = "$script?cmd=add&amp;page=$r_page";
	$link_edit     = "$script?cmd=edit&amp;page=$r_page";
	$link_diff     = "$script?cmd=diff&amp;page=$r_page";
	$link_top      = "$script?$defaultpage";
	$link_list     = "$script?cmd=list";
	$link_filelist = "$script?cmd=filelist";
	$link_search   = "$script?cmd=search";
	$link_whatsnew = "$script?$whatsnew";
	$link_backup   = "$script?cmd=backup&amp;page=$r_page";
	$link_help     = "$script?".rawurlencode($help_page);
	$link_rss      = "$script?cmd=rss";
	$link_freeze   = "$script?cmd=freeze&amp;page=$r_page";
	$link_unfreeze = "$script?cmd=unfreeze&amp;page=$r_page";
	$link_upload   = "$script?plugin=attach&amp;pcmd=upload&amp;page=$r_page";
	
	// ページの表示時TRUE(バックアップの表示、RecentChangesの表示を除く)
	$is_page = (is_pagename($_page) and !arg_check('backup') and $_page != $whatsnew);
	
	// ページの読み出し時TRUE
	$is_read = (arg_check('read') and is_page($_page));
	
	// ページが凍結されているときTRUE
	$is_freeze = is_freeze($_page);
	
	// ページの最終更新時刻(文字列)
	$lastmodified = $is_read ?
		get_date('D, d M Y H:i:s T',get_filetime($_page)).' '.get_pg_passage($_page,FALSE) : '';
	
	// 関連するページのリスト
	$related = ($is_read and $related_link) ? make_related($_page) : '';
	
	// 添付ファイルのリスト
	$attaches = ($is_read and exist_plugin_action('attach')) ? attach_filelist() : '';
	
	// 注釈のリスト
	sort($foot_explain);
	$notes = count($foot_explain) ? $note_hr.join("\n",$foot_explain) : '';
	
	// 1.3.x compat
	// ページの最終更新時刻(UNIX timestamp)
	$fmt = $is_read ? get_filetime($_page) + LOCALZONE : 0;

	//単語検索
	if ($search_word_color and array_key_exists('word',$vars))
	{
		$search_word = '';
		$words = array_flip(array_splice(preg_split('/\s+/',$vars['word'],-1,PREG_SPLIT_NO_EMPTY),0,10));
		$keys = array();
		foreach ($words as $word=>$id)
		{
			$keys[$word] = strlen($word);
		}
		arsort($keys,SORT_NUMERIC);
		$keys = array_keys($keys);
		foreach ($keys as $key)
		{
			$s_key = htmlspecialchars($key);
			$q_key = preg_quote($s_key,'/');
			$search_word .= " <strong class=\"word{$words[$key]}\">$s_key</strong>";
			$to = "<strong class=\"word{$words[$key]}\">\$1</strong>";
			$body = preg_replace("/(?:^|(?<=>))([^<]*)/e",'preg_replace("/($q_key)/i",$to,\'$1\')',$body);
		}
		$body = "<div class=\"small\">$_msg_word$search_word</div>$hr\n$body";
	}
	
	$longtaketime = getmicrotime() - MUTIME;
	$taketime = sprintf('%01.03f',$longtaketime);
	
	if (!file_exists(SKIN_FILE)||!is_readable(SKIN_FILE))
	{
		die_message(SKIN_FILE.'(skin file) is not found.');
	}
	require(SKIN_FILE);
}

// インライン要素のパース
function inline($line,$remove=FALSE)
{
	global $NotePattern;
	
	$line = htmlspecialchars($line);
	if ($remove)
	{
		$line = preg_replace($NotePattern,'',$line);
	}
	return $line;
}

// インライン要素のパース (リンク、見出し一覧)
function inline2($str)
{
	return make_line_rules(make_link($str));
}

// 編集フォームの表示
function edit_form($page,$postdata,$digest = 0,$b_template = TRUE)
{
	global $script,$vars,$rows,$cols,$hr,$function_freeze;
	global $_btn_addtop,$_btn_preview,$_btn_repreview,$_btn_update,$_btn_freeze,$_msg_help,$_btn_notchangetimestamp;
	global $whatsnew,$_btn_template,$_btn_load,$non_list,$load_template_func;
	
	$refer = $template = $addtag = $add_top = '';
	
	if ($digest == 0) {
		$digest = md5(join('',get_source($page)));
	}
	
	$checked_top = array_key_exists('add_top',$vars) ? ' checked="checked"' : '';
	$checked_time = array_key_exists('notimestamp',$vars) ? ' checked="checked"' : '';
	
	if(array_key_exists('add',$vars)) {
		$addtag = '<input type="hidden" name="add" value="true" />';
		$add_top = "<input type=\"checkbox\" name=\"add_top\" value=\"true\"$checked_top /><span class=\"small\">$_btn_addtop</span>";
	}

	if($load_template_func and $b_template) {
		$_pages = get_existpages();
		$pages = array();
		foreach($_pages as $_page) {
			if ($_page == $whatsnew or preg_match("/$non_list/",$_page)) {
				continue;
			}
			$s_page = htmlspecialchars($_page);
			$pages[$_page] = "   <option value=\"$s_page\">$s_page</option>";
		}
		ksort($pages);
		$s_pages = join("\n",$pages);
		$template = <<<EOD
  <select name="template_page">
   <option value="">-- $_btn_template --</option>
$s_pages
  </select>
  <input type="submit" name="template" value="$_btn_load" accesskey="r" />
  <br />
EOD;
		
		if (array_key_exists('refer',$vars) and $vars['refer'] != '') {
			$refer = '[['.strip_bracket($vars['refer'])."]]\n\n";
		}
	}
	
	$r_page = rawurlencode($page);
	$s_page = htmlspecialchars($page);
	$s_digest = htmlspecialchars($digest);
	$s_postdata = htmlspecialchars($refer.$postdata);
	$s_original = array_key_exists('original',$vars) ? htmlspecialchars($vars['original']) : $s_postdata;
	$b_preview = array_key_exists('preview',$vars); // プレビュー中TRUE
	$btn_preview = $b_preview ? $_btn_repreview : $_btn_preview;
	
	$body = <<<EOD
<form action="$script" method="post">
 <div>
$template
  $addtag
  <input type="hidden" name="cmd" value="edit" />
  <input type="hidden" name="page" value="$s_page" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$rows" cols="$cols">$s_postdata</textarea>
  <br />
  <input type="submit" name="preview" value="$btn_preview" accesskey="p" />
  <input type="submit" name="write" value="$_btn_update" accesskey="s" />
  $add_top
  <input type="checkbox" name="notimestamp" value="true"$checked_time />
  <span style="small">$_btn_notchangetimestamp</span>
  <textarea name="original" rows="1" cols="1" style="display:none">$s_original</textarea>
 </div>
</form>
EOD;
	
	if (array_key_exists('help',$vars)) {
		$body .= $hr.catrule();
	}
	else {
		$body .= <<<EOD
<ul>
 <li><a href="$script?cmd=edit&amp;help=true&amp;page=$r_page">$_msg_help</a></li>
</ul>
EOD;
	}
	return $body;
}

// 関連するページ
function make_related($page,$tag='')
{
	global $script,$vars,$related,$rule_related_str,$related_str,$non_list;
	global $_ul_left_margin, $_ul_margin, $_list_pad_str;
	
	$links = links_get_related($page);
	
	if ($tag) {
		ksort($links);
	}
	else {
		arsort($links);
	}
	$_links = array();
	foreach ($links as $page=>$lastmod)
	{
		if (preg_match("/$non_list/",$page))
		{
			continue;
		}
		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page);
		$passage = get_passage($lastmod);
		$_links[] = $tag ?
			"<a href=\"$script?$r_page\" title=\"$s_page $passage\">$s_page</a>" :
			"<a href=\"$script?$r_page\">$s_page</a>$passage";
	}
	
	if ($tag) {
		$retval = join($rule_related_str,$_links);
		if ($tag == 'p') {
			$margin = $_ul_left_margin + $_ul_margin;
			$style = sprintf($_list_pad_str,1,$margin,$margin);
			$retval =  "\n<ul class=\"list1\" style=\"$style\">\n<li>$retval</li>\n</ul>\n";
		}
	}
	else {
		$retval = join($related_str,$_links);
	}
	return $retval;
}

// ユーザ定義ルール(ソースは置換せずコンバート)
function make_line_rules($str)
{
	global $line_rules;
	
	foreach($line_rules as $rule => $replace)
	{
		$str = preg_replace("/$rule/",$replace,$str);
	}
	
	return $str;
}

// HTMLタグを取り除く
function strip_htmltag($str)
{
	//$str = preg_replace('/<a[^>]+>\?<\/a>/','',$str);
	return preg_replace('/<[^>]+>/','',$str);
}

// ページ名からページ名を検索するリンクを作成
function make_search($page)
{
	global $script,$WikiName;
	
	$s_page = htmlspecialchars($page);
	$r_page = rawurlencode($page);
	
	//WikiWikiWeb like...
	//if(preg_match("/^$WikiName$/",$page))
	//	$name = preg_replace("/([A-Z][a-z]+)/","$1 ",$name);
	
 	return "<a href=\"$script?cmd=search&amp;word=$r_page\">$s_page</a> ";
}
?>
