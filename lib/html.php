<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: html.php,v 1.13 2004/11/07 13:09:37 henoheno Exp $
//

// 本文を出力
function catbody($title,$page,$body)
{
	global $script, $vars, $arg, $defaultpage, $whatsnew, $help_page, $hr;
	global $related_link, $cantedit, $function_freeze, $search_word_color, $_msg_word;
	global $foot_explain, $note_hr, $head_tags;
	global $trackback, $trackback_javascript, $referer, $javascript;
	global $_LANG, $_LINK, $_IMAGE;

	global $html_transitional; // FALSE:XHTML1.1 TRUE:XHTML1.0 Transitional
	global $page_title;        // ホームページのタイトル
	global $do_backup;         // バックアップを行うかどうか
	global $modifier;          // 編集者のホームページ
	global $modifierlink;      // 編集者の名前

	if (! file_exists(SKIN_FILE) || ! is_readable(SKIN_FILE))
		die_message('SKIN_FILE is not found');

	$_LINK = $_IMAGE = array();

	// Add JavaScript header when ...
	if ($trackback && $trackback_javascript) $javascript = 1; // Set something If you want
	if (! PKWK_ALLOW_JAVASCRIPT) unset($javascript);

	$_page  = isset($vars['page']) ? $vars['page'] : '';
	$r_page = rawurlencode($_page);

	// Set $_LINK for skin
	$_LINK['add']      = "$script?cmd=add&amp;page=$r_page";
	$_LINK['backup']   = "$script?cmd=backup&amp;page=$r_page";
	$_LINK['copy']     = "$script?plugin=template&amp;refer=$r_page";
	$_LINK['diff']     = "$script?cmd=diff&amp;page=$r_page";
	$_LINK['edit']     = "$script?cmd=edit&amp;page=$r_page";
	$_LINK['filelist'] = "$script?cmd=filelist";
	$_LINK['freeze']   = "$script?cmd=freeze&amp;page=$r_page";
	$_LINK['help']     = "$script?" . rawurlencode($help_page);
	$_LINK['list']     = "$script?cmd=list";
	$_LINK['new']      = "$script?plugin=newpage&amp;refer=$r_page";
	$_LINK['rdf']      = "$script?cmd=rss&amp;ver=1.0";
	$_LINK['recent']   = "$script?" . rawurlencode($whatsnew);
	$_LINK['refer']    = "$script?plugin=referer&amp;page=$r_page";
	$_LINK['reload']   = "$script?$r_page";
	$_LINK['rename']   = "$script?plugin=rename&amp;refer=$r_page";
	$_LINK['rss']      = "$script?cmd=rss";
	$_LINK['rss10']    = "$script?cmd=rss&amp;ver=1.0"; // Same as 'rdf'
	$_LINK['rss20']    = "$script?cmd=rss&amp;ver=2.0";
	$_LINK['search']   = "$script?cmd=search";
	$_LINK['top']      = "$script?" . rawurlencode($defaultpage);
	if ($trackback) {
		$tb_id = tb_get_id($_page);
		$_LINK['trackback'] = "$script?plugin=tb&amp;__mode=view&amp;tb_id=$tb_id";
	}
	$_LINK['unfreeze'] = "$script?cmd=unfreeze&amp;page=$r_page";
	$_LINK['upload']   = "$script?plugin=attach&amp;pcmd=upload&amp;page=$r_page";

	// Compat: Skins for 1.4.4 and before
	$link_add       = & $_LINK['add'];
	$link_new       = & $_LINK['new'];	// New!
	$link_edit      = & $_LINK['edit'];
	$link_diff      = & $_LINK['diff'];
	$link_top       = & $_LINK['top'];
	$link_list      = & $_LINK['list'];
	$link_filelist  = & $_LINK['filelist'];
	$link_search    = & $_LINK['search'];
	$link_whatsnew  = & $_LINK['recent'];
	$link_backup    = & $_LINK['backup'];
	$link_help      = & $_LINK['help'];
	$link_trackback = & $_LINK['trackback'];	// New!
	$link_rdf       = & $_LINK['rdf'];		// New!
	$link_rss       = & $_LINK['rss'];
	$link_rss10     = & $_LINK['rss10'];		// New!
	$link_rss20     = & $_LINK['rss20'];		// New!
	$link_freeze    = & $_LINK['freeze'];
	$link_unfreeze  = & $_LINK['unfreeze'];
	$link_upload    = & $_LINK['upload'];
	$link_template  = & $_LINK['copy'];
	$link_refer     = & $_LINK['refer'];	// New!
	$link_rename    = & $_LINK['rename'];

	// ページの表示時TRUE(バックアップの表示、RecentChangesの表示を除く)
	$is_page = (is_pagename($_page) && ! arg_check('backup') && $_page != $whatsnew);

	// ページの読み出し時TRUE
	$is_read = (arg_check('read') && is_page($_page));

	// ページが凍結されているときTRUE
	$is_freeze = is_freeze($_page);

	// ページの最終更新時刻(文字列)
	$lastmodified = $is_read ?  get_date('D, d M Y H:i:s T', get_filetime($_page)) .
		' ' . get_pg_passage($_page, FALSE) : '';

	// 関連するページのリスト
	$related = ($is_read && $related_link) ? make_related($_page) : '';

	// 添付ファイルのリスト
	$attaches = ($is_read && exist_plugin_action('attach')) ? attach_filelist() : '';

	// 注釈のリスト
	ksort($foot_explain, SORT_NUMERIC);
	$notes = ! empty($foot_explain) ? $note_hr . join("\n", $foot_explain) : '';

	// <head>内に追加するタグ
	$head_tag = ! empty($head_tags) ? join("\n", $head_tags) ."\n" : '';

	// 1.3.x compat
	// ページの最終更新時刻(UNIX timestamp)
	$fmt = $is_read ? get_filetime($_page) + LOCALZONE : 0;

	//単語検索
	if ($search_word_color && isset($vars['word'])) {
		$body = '<div class="small">' . $_msg_word . htmlspecialchars($vars['word']) .
			"</div>$hr\n$body";
		$words = array_flip(array_splice(
			preg_split('/\s+/', $vars['word'], -1, PREG_SPLIT_NO_EMPTY),
			0, 10));
		$keys = array();
		foreach ($words as $word=>$id) {
			$keys[$word] = strlen($word);
		}
		arsort($keys, SORT_NUMERIC);
		$keys = get_search_words(array_keys($keys), TRUE);
		$id = 0;
		foreach ($keys as $key=>$pattern)
		{
			$s_key    = htmlspecialchars($key);
			$pattern  = "/<[^>]*>|($pattern)|&[^;]+;/";
			$callback = create_function(
				'$arr',
				'return (count($arr) > 1) ? "<strong class=\"word' . $id++ . '\">{$arr[1]}</strong>" : $arr[0];'
			);
			$body  = preg_replace_callback($pattern, $callback, $body);
			$notes = preg_replace_callback($pattern, $callback, $notes);
		}
	}

	$longtaketime = getmicrotime() - MUTIME;
	$taketime     = sprintf('%01.03f', $longtaketime);

	require(SKIN_FILE);
}

// インライン要素のパース (obsolete)
function inline($line, $remove = FALSE)
{
	global $NotePattern;

	$line = htmlspecialchars($line);
	if ($remove) 
		$line = preg_replace($NotePattern, '', $line);

	return $line;
}

// インライン要素のパース (リンク、見出し一覧) (obsolete)
function inline2($str)
{
	return make_link($str);
}

// 編集フォームの表示
function edit_form($page, $postdata, $digest = 0, $b_template = TRUE)
{
	global $script, $vars, $rows, $cols, $hr, $function_freeze;
	global $_btn_addtop, $_btn_preview, $_btn_repreview, $_btn_update, $_btn_cancel,
		$_btn_freeze, $_msg_help, $_btn_notchangetimestamp;
	global $whatsnew, $_btn_template, $_btn_load, $non_list, $load_template_func;

	$refer = $template = $addtag = $add_top = '';

	if ($digest == 0) $digest = md5(join('', get_source($page)));

	$checked_top  = isset($vars['add_top'])     ? ' checked="checked"' : '';
	$checked_time = isset($vars['notimestamp']) ? ' checked="checked"' : '';

	if(isset($vars['add'])) {
		$addtag  = '<input type="hidden" name="add" value="true" />';
		$add_top = "<input type=\"checkbox\" name=\"add_top\" value=\"true\"$checked_top /><span class=\"small\">$_btn_addtop</span>";
	}

	if($load_template_func && $b_template) {
		$_pages = get_existpages();
		$pages  = array();
		foreach($_pages as $_page) {
			if ($_page == $whatsnew || preg_match("/$non_list/", $_page))
				continue;
			$s_page = htmlspecialchars($_page);
			$pages[$_page] = "   <option value=\"$s_page\">$s_page</option>";
		}
		ksort($pages);
		$s_pages  = join("\n", $pages);
		$template = <<<EOD
  <select name="template_page">
   <option value="">-- $_btn_template --</option>
$s_pages
  </select>
  <input type="submit" name="template" value="$_btn_load" accesskey="r" />
  <br />
EOD;

		if (isset($vars['refer']) && $vars['refer'] != '')
			$refer = '[[' . strip_bracket($vars['refer']) ."]]\n\n";
	}

	$r_page      = rawurlencode($page);
	$s_page      = htmlspecialchars($page);
	$s_digest    = htmlspecialchars($digest);
	$s_postdata  = htmlspecialchars($refer . $postdata);
	$s_original  = isset($vars['original']) ? htmlspecialchars($vars['original']) : $s_postdata;
	$b_preview   = isset($vars['preview']); // プレビュー中TRUE
	$btn_preview = $b_preview ? $_btn_repreview : $_btn_preview;

	$body = <<<EOD
<form action="$script" method="post">
 <div class="edit_form">
$template
  $addtag
  <input type="hidden" name="cmd"    value="edit" />
  <input type="hidden" name="page"   value="$s_page" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$rows" cols="$cols">$s_postdata</textarea>
  <br />
  <input type="submit" name="preview" value="$btn_preview" accesskey="p" />
  <input type="submit" name="write"   value="$_btn_update" accesskey="s" />
  $add_top
  <input type="checkbox" name="notimestamp" value="true"$checked_time />
  <span style="small">$_btn_notchangetimestamp</span> &nbsp;
  <input type="submit" name="cancel"  value="$_btn_cancel" accesskey="c" />
  <textarea name="original" rows="1" cols="1" style="display:none">$s_original</textarea>
 </div>
</form>
EOD;

	if (isset($vars['help'])) {
		$body .= $hr . catrule();
	} else {
		$body .=
		"<ul><li><a href=\"$script?cmd=edit&amp;help=true&amp;page=$r_page\">$_msg_help</a></li></ul>";
	}

	return $body;
}

// 関連するページ
function make_related($page, $tag = '')
{
	global $script, $vars, $related, $rule_related_str, $related_str, $non_list;
	global $_ul_left_margin, $_ul_margin, $_list_pad_str;

	$links = links_get_related($page);

	if ($tag) {
		ksort($links);
	} else {
		arsort($links);
	}

	$_links = array();
	foreach ($links as $page=>$lastmod) {
		if (preg_match("/$non_list/", $page)) continue;

		$r_page   = rawurlencode($page);
		$s_page   = htmlspecialchars($page);
		$passage  = get_passage($lastmod);
		$_links[] = $tag ?
			"<a href=\"$script?$r_page\" title=\"$s_page $passage\">$s_page</a>" :
			"<a href=\"$script?$r_page\">$s_page</a>$passage";
	}

	if (empty($_links)) return '';

	if ($tag == 'p') { // 行頭から
		$margin = $_ul_left_margin + $_ul_margin;
		$style  = sprintf($_list_pad_str, 1, $margin, $margin);
		$retval =  "\n<ul$style>\n<li>" . join($rule_related_str, $_links) . "</li>\n</ul>\n";
	} else if ($tag) {
		$retval = join($rule_related_str, $_links);
	} else {
		$retval = join($related_str, $_links);
	}

	return $retval;
}

// ユーザ定義ルール(ソースは置換せずコンバート)
function make_line_rules($str)
{
	global $line_rules;
	static $pattern, $replace;

	if (! isset($pattern)) {
		$pattern = array_map(create_function('$a', 'return "/$a/";'), array_keys($line_rules));
		$replace = array_values($line_rules);
		unset($line_rules);
	}

	return preg_replace($pattern, $replace, $str);
}

// HTMLタグを取り除く
function strip_htmltag($str)
{
	global $_symbol_noexists;

	$noexists_pattern = '#<span class="noexists">([^<]*)<a[^>]+>' .
		preg_quote($_symbol_noexists, '#') . '</a></span>#';

	$str = preg_replace($noexists_pattern, '$1', $str);
	//$str = preg_replace('/<a[^>]+>\?<\/a>/', '', $str);
	return preg_replace('/<[^>]+>/', '', $str);
}

// ページ名からページ名を検索するリンクを作成
function make_search($page)
{
	global $script, $WikiName;

	$s_page = htmlspecialchars($page);
	$r_page = rawurlencode($page);

	//WikiWikiWeb like...
	//if(preg_match("/^$WikiName$/", $page))
	//	$name = preg_replace("/([A-Z][a-z]+)/", "$1 ", $name);

	return "<a href=\"$script?cmd=search&amp;word=$r_page\">$s_page</a> ";
}

// 見出しを生成 (注釈やHTMLタグを除去)
function make_heading(& $str, $strip = TRUE)
{
	global $NotePattern;

	// 見出しの固有ID部を削除
	$id = '';
	if (preg_match('/^(\*{0,3})(.*?)\[#([A-Za-z][\w-]+)\](.*?)$/m', $str, $matches)) {
		$str = $matches[2] . $matches[4];
		$id  = $matches[3];
	} else {
		$str = preg_replace('/^\*{0,3}/', '', $str);
	}

	if ($strip === TRUE)
		$str = strip_htmltag(make_link(preg_replace($NotePattern, '', $str)));

	return $id;
}
?>
