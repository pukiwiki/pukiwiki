<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: html.php,v 1.52 2003/02/04 09:46:16 panda Exp $
//

// 本文を出力
function catbody($title,$page,$body)
{
	global $script,$vars,$arg,$defaultpage,$whatsnew,$help_page,$hr;
	global $related_link,$cantedit,$function_freeze,$search_word_color,$_msg_word;
	global $foot_explain,$note_hr;
	
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
	
	$is_page = (is_pagename($_page) and !arg_check('backup') and $_page != $whatsnew);
	
	$is_read = (arg_check('read') and is_page($_page));
	
	$is_freeze = is_freeze($_page);
	
	$lastmodified = $is_read ?
		get_date('D, d M Y H:i:s T',get_filetime($_page)).' '.get_pg_passage($_page,FALSE) : '';
	
	$related = ($is_read and $related_link) ? make_related($_page) : '';
	
	$attaches = ($is_read and exist_plugin_action('attach')) ? attach_filelist() : '';
	
	sort($foot_explain);
	$notes = count($foot_explain) ? $note_hr.join("\n",$foot_explain) : '';
	
	//単語検索
	if ($search_word_color and array_key_exists('word',$vars)) {
		$search_word = $_msg_word;
		$words = array_flip(array_splice(preg_split('/\s+/',$vars['word'],-1,PREG_SPLIT_NO_EMPTY),0,10));
		$keys = array();
		foreach ($words as $word=>$id) {
			$keys[$word] = strlen($word);
		}
		arsort($keys,SORT_NUMERIC);
		$keys = array_keys($keys);
		foreach ($keys as $key) {
			$to = "<strong class=\"word{$words[$key]}\">$key</strong>";
			$body = preg_replace("/(?:^|(?<=>))([^<]*)/e",'str_replace($key,$to,\'$1\')',$body);
			$search_word .= ' '.$to;
		}
		$body = "<div class=\"small\">$search_word</div>$hr\n$body";
	}
	
	$longtaketime = getmicrotime() - MUTIME;
	$taketime = sprintf('%01.03f',$longtaketime);
	
	$_server = ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://')
		. $_SERVER['SERVER_NAME']
		.($_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT']);
	$htmllint = '/cgi-bin/htmllint/htmllint.cgi?Stat=on&amp;Method=URL&amp;ViewSource=on&amp;URL='.$_server.rawurlencode($_SERVER['REQUEST_URI']);
	
	if (!file_exists(SKIN_FILE)||!is_readable(SKIN_FILE)) {
		die_message(SKIN_FILE.'(skin file) is not found.');
	}
	require(SKIN_FILE);
}

// インライン要素のパース (注釈)
function inline($line,$remove=FALSE)
{
	global $NotePattern;
	
	return preg_replace(
		$NotePattern,
		$remove ? '' : 'make_note(\'$1\')',
		htmlspecialchars($line));
}

// インライン要素のパース (リンク、見出し一覧)
function inline2($str)
{
	return make_user_rules(make_link($str));
}

// 編集フォームの表示
function edit_form($page,$postdata,$digest = 0,$b_template = TRUE)
{
	global $script,$vars,$rows,$cols,$hr,$function_freeze;
	global $_btn_addtop,$_btn_preview,$_btn_repreview,$_btn_update,$_btn_freeze,$_msg_help,$_btn_notchangetimestamp;
	global $whatsnew,$_btn_template,$_btn_load,$non_list,$load_template_func;
	global $_btn_viewtag;
	
	$refer = $template = $addtag = $add_top = '';
	
	if ($digest == 0) {
		$digest = md5(join('',get_source($page)));
	}
	
	$checked_top = array_key_exists('add_top',$vars) ? ' checked="checked"' : '';
	$checked_time = array_key_exists('notimestamp',$vars) ? ' checked="checked"' : '';
	$checked_viewtag = array_key_exists('viewtag',$vars) ? ' checked="checked"' : '';
	
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
  <input type="hidden" name="cmd" value="edit">
  <input type="hidden" name="page" value="$s_page" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$rows" cols="$cols">$s_postdata</textarea>
  <textarea name="original" rows="$rows" cols="$cols" style="display:none">$s_original</textarea>
  <br />
  <input type="submit" name="preview" value="$btn_preview" accesskey="p" />
  <input type="submit" name="write" value="$_btn_update" accesskey="s" />
  $add_top
  <input type="checkbox" name="notimestamp" value="true"$checked_time /><span style="small">$_btn_notchangetimestamp</span>
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
	global $script,$vars,$related,$rule_related_str,$related_str;
	global $_list_left_margin, $_list_margin, $_list_pad_str;
	
	$links = links_get_related($page);
	
	if ($tag) {
		ksort($links);
	}
	else {
		arsort($links);
	}
	$_links = array();
	foreach ($links as $page=>$lastmod) {
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
			$margin = $_list_left_margin + $_list_margin;
			$style = sprintf($_list_pad_str,1,$margin,$margin);
			$retval =  "\n<ul class=\"list1\" style=\"$style\">\n<li>$retval</li>\n</ul>\n";
		}
	}
	else {
		$retval = join($related_str,$_links);
	}
	return $retval;
}

// 注釈処理
function make_note($str)
{
	global $NotePattern,$foot_explain;
	static $note_id = 0;
	
	$note = ++$note_id;
	if (preg_match($NotePattern,$str)) {
		$str = preg_replace($NotePattern,'make_note(\'$1\')',$str);
	}
	
	$str= str_replace("\\'","'",$str);
	$str = inline2($str);
	
	$foot_explain[] = "<a id=\"notefoot_$note\" href=\"#notetext_$note\" class=\"note_super\">*$note</a> <span class=\"small\">$str</span><br />\n";
	
	return "<a id=\"notetext_$note\" href=\"#notefoot_$note\" class=\"note_super\">*$note</a>";
}
// ユーザ定義ルール(ソースを置換する)
function user_rules_str($str)
{
	global $str_rules;
	
	$arystr = explode("\n",$str);
	
	// 日付・時刻置換処理
	foreach ($arystr as $str) {
		if (substr($str,0,1) != " ") {
			foreach ($str_rules as $rule => $replace) {
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
	
	foreach($user_rules as $rule => $replace) {
		$str = preg_replace("/$rule/","$replace",$str);
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
