<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: func.php,v 1.17 2003/02/22 06:25:36 panda Exp $
//

// 文字列がInterWikiNameかどうか
function is_interwiki($str)
{
	global $InterWikiName;

	return preg_match("/^$InterWikiName$/",$str);
}
// 文字列がページ名かどうか
function is_pagename($str)
{
	global $BracketName,$WikiName;
	
	$is_pagename = (!is_interwiki($str) and preg_match("/^(?!\.{0,}\/)$BracketName(?<!\/)$/",$str));
	
	if (defined('SOURCE_ENCODING')) {
		if (SOURCE_ENCODING == 'UTF-8') {
			$is_pagename = ($is_pagename and preg_match('/^(?:[\x00-\x7F]|(?:[\xC0-\xDF][\x80-\xBF])|(?:[\xE0-\xEF][\x80-\xBF][\x80-\xBF]))+$/',$str)); // UTF-8
		}
		else if (SOURCE_ENCODING == 'EUC-JP') {
			$is_pagename = ($is_pagename and preg_match('/^(?:[\x00-\x7F]|(?:[\x8E\xA1-\xFE][\xA1-\xFE])|(?:\x8F[\xA1-\xFE][\xA1-\xFE]))+$/',$str)); // EUC-JP
		}
	}
	
	return $is_pagename;
}
// ページが存在するか
function is_page($page,$reload=FALSE)
{
	global $InterWikiName;
	static $is_page;
	
	if (!isset($is_page))
		$is_page = array();
	
	if ($reload or !array_key_exists($page,$is_page))
		$is_page[$page] = file_exists(get_filename($page));
	
	return $is_page[$page];
}
// ページが編集可能か
function is_editable($page)
{
	global $cantedit;
	static $is_editable;
	
	if (!isset($is_editable))
		$is_editable = array();
	
	if (!array_key_exists($page,$is_editable))
		$is_editable[$page] = (is_pagename($page) and !is_freeze($page) and !in_array($page,$cantedit));
	
	return $is_editable[$page];
}

// ページが凍結されているか
function is_freeze($page)
{
	global $function_freeze;

	if (!$function_freeze or !is_page($page)) {
		return FALSE;
	}

	list($lines) = get_source($page);
	return (rtrim($lines) == '#freeze');
}

// 編集不可能なページを編集しようとしたとき
function check_editable()
{
	global $script,$get,$_title_cannotedit,$_msg_unfreeze;
	
	edit_auth();
	
	if (is_editable($get['page'])) {
		return;
	}
	
	$body = $title = str_replace('$1',htmlspecialchars(strip_bracket($get['page'])),$_title_cannotedit);
	$page = str_replace('$1',make_search($get['page']),$_title_cannotedit);

	if(is_freeze($get['page'])) {
		$body .= "(<a href=\"$script?cmd=unfreeze&amp;page=".rawurlencode($get['page'])."\">$_msg_unfreeze</a>)";
	}
	
	catbody($title,$page,$body);
	exit;
}

// 編集時の認証
function edit_auth()
{
	global $get,$edit_auth,$edit_auth_users,$_msg_auth,$_title_cannotedit;

	if ($edit_auth and
		(!isset($_SERVER['PHP_AUTH_USER']) or
		 !array_key_exists($_SERVER['PHP_AUTH_USER'],$edit_auth_users) or
		 $edit_auth_users[$_SERVER['PHP_AUTH_USER']] != $_SERVER['PHP_AUTH_PW']))
	{
		header('WWW-Authenticate: Basic realm="'.$_msg_auth.'"');
		header('HTTP/1.0 401 Unauthorized');
		// press cancel.
		$body = $title = str_replace('$1',htmlspecialchars(strip_bracket($get['page'])),$_title_cannotedit);
		$page = str_replace('$1',make_search($get['page']),$_title_cannotedit);
		
		catbody($title,$page,$body);
		exit;
	}
}

// 自動テンプレート
function auto_template($page)
{
	global $auto_template_func,$auto_template_rules;
	
	if (!$auto_template_func) {
		return '';
	}

	$body = '';
	foreach ($auto_template_rules as $rule => $template) {
		if (preg_match("/$rule/",$page,$matches)) {
			$template_page = preg_replace("/$rule/",$template,$page);
			$body = join('',get_source($template_page));
			for ($i = 0; $i < count($matches); $i++) {
				$body = str_replace("\$$i",$matches[$i],$body);
			}
			break;
		}
	}
	return $body;
}

// 検索
function do_search($word,$type='AND',$non_format=FALSE)
{
	global $script,$vars,$whatsnew;
	global $_msg_andresult,$_msg_orresult,$_msg_notfoundresult;
	
	$database = array();
	$retval = array();

	$b_type = ($type == 'AND'); // AND:TRUE OR:FALSE
	$keys = preg_split('/\s+/',preg_quote($word,'/'),-1,PREG_SPLIT_NO_EMPTY);
	
	$_pages = get_existpages();
	$pages = array();
	
	foreach ($_pages as $page) {
		if ($page == $whatsnew or ($non_format and $page == $vars['page'])) {
			continue;
		}
		
		$source = get_source($page);
		array_unshift($source,$page); // ページ名も検索対象に
		
		$b_match = FALSE;
		foreach ($keys as $key) {
			$tmp = preg_grep("/$key/",$source);
			$b_match = (count($tmp) > 0);
			if ($b_match xor $b_type) {
				break;
			}
		}
		if ($b_match) {
			$pages[$page] = get_filetime($page);
		}
	}
	if ($non_format) {
		return $pages;
	}
	$r_word = rawurlencode($word);
	$s_word = htmlspecialchars($word);
	if (count($pages) == 0) {
		return str_replace('$1',$s_word,$_msg_notfoundresult);
	}
	ksort($pages);
	$retval = "<ul>\n";
	foreach ($pages as $page=>$time) {
		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page);
		$passage = get_passage($time);
		$retval .= " <li><a href=\"$script?cmd=read&amp;page=$r_page&amp;word=$r_word\">$s_page</a>$passage</li>\n";
	}
	$retval .= "</ul>\n";
	
	$retval .= str_replace('$1',$s_word,str_replace('$2',count($pages),
		str_replace('$3',count($_pages),$b_type ? $_msg_andresult : $_msg_orresult)));
	
	return $retval;
}

// プログラムへの引数のチェック
function arg_check($str)
{
	global $vars;
	
	return array_key_exists('cmd',$vars) and (strpos($vars['cmd'],$str) === 0);
}

// ページ名のエンコード
function encode($key)
{
	return strtoupper(join('',unpack('H*0',$key)));
}

// ページ名のデコード
function decode($key)
{
	return $key == '' ? '' : pack('H*',$key);
}

// [[ ]] を取り除く
function strip_bracket($str)
{
	if (preg_match('/^\[\[(.*)\]\]$/',$str,$match)) {
		$str = $match[1];
	}
	return $str;
}

// ページ一覧の作成
function page_list($pages, $cmd = 'read', $withfilename = FALSE)
{
	global $script,$list_index,$top;
	global $_msg_symbol,$_msg_other;
	
	// ソートキーを決定する。 ' ' < '[a-zA-Z]' < 'zz'という前提。
	$symbol = ' ';
	$other = 'zz';
	
	$retval = '';
	
	$list = array();
	foreach($pages as $page) {
		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page,ENT_QUOTES);
		$e_page = encode($page);
		$passage = get_pg_passage($page);
		
		$str = "   <li><a href=\"$script?cmd=$cmd&amp;page=$r_page\">$s_page</a>$passage";
		
		if ($withfilename) {
			$str .= "\n    <ul><li>$e_page</li></ul>\n   ";
		}
		$str .= "</li>";
		
		$head = (preg_match('/^([A-Za-z])/',$page,$matches)) ? $matches[1] :
			(preg_match('/^([ -~0-9])/',$page,$matches) ? $symbol : $other);
		
		$list[$head][$page] = $str;
	}
	ksort($list);
	
	$cnt = 0;
	$arr_index = array();
	$retval .= "<ul>\n";
	foreach ($list as $head=>$pages) {
		if ($head === $symbol) {
			$head = $_msg_symbol;
		}
		else if ($head === $other) {
			$head = $_msg_other;
		}
		
		if ($list_index) {
			$cnt++;
			$arr_index[] = "<a id=\"top_$cnt\" href=\"#head_$cnt\"><strong>$head</strong></a>";
			$retval .= " <li><a id=\"head_$cnt\" href=\"#top_$cnt\"><strong>$head</strong></a>\n  <ul>\n";
		}
		ksort($pages);
		$retval .= join("\n",$pages);
		if ($list_index) {
			$retval .= "\n  </ul>\n </li>\n";
		}
	}
	$retval .= "</ul>\n";
	if ($list_index and $cnt > 0) {
		$top = array();
		while (count($arr_index) > 0) {
			$top[] = join(" | \n",array_splice($arr_index,0,16))."\n";
		}
		$retval = "<div id=\"top\" style=\"text-align:center\">\n".
			join("<br />",$top)."</div>\n".$retval;
	}
	return $retval;
}

// テキスト整形ルールを表示する
function catrule()
{
	global $rule_page;
	
	if (!is_page($rule_page)) {
		return "<p>sorry, $rule_page unavailable.</p>";
	}
	return convert_html(get_source($rule_page));
}

// エラーメッセージを表示する
function die_message($msg)
{
	$title = $page = 'Runtime error';
	
	$body = <<<EOD
<h3>Runtime error</h3>
<strong>Error message : $msg</strong>
EOD;
	
	if(defined('SKIN_FILE') && file_exists(SKIN_FILE) && is_readable(SKIN_FILE)) {
	  catbody($title,$page,$body);
	}
	else {
		header('Content-Type: text/html; charset=euc-jp');
		print <<<__TEXT__
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
  <title>$title</title>
  <meta http-equiv="content-type" content="text/html; charset=euc-jp">
 </head>
 <body>
 $body
 </body>
</html>
__TEXT__;
	}
	die();
}

// 現在時刻をマイクロ秒で取得
function getmicrotime()
{
	list($usec, $sec) = explode(' ',microtime());
	return ((float)$sec + (float)$usec);
}

// 日時を得る
function get_date($format,$timestamp = NULL)
{
	$time = ($timestamp === NULL) ? UTIME : $timestamp;
	$time += ZONETIME;
	
	$format = preg_replace('/(?<!\\\)T/',preg_replace('/(.)/','\\\$1',ZONE),$format);
	
	return date($format,$time);
}

// 日時文字列を作る
function format_date($val, $paren = FALSE) {
	global $date_format,$time_format,$weeklabels;
	
	$val += ZONETIME;
	
	$ins_date = date($date_format,$val);
	$ins_time = date($time_format,$val);
	$ins_week = '('.$weeklabels[date('w',$val)].')';
	
	$ins = "$ins_date $ins_week $ins_time";
	return $paren ? "($ins)" : $ins;
}

// 経過時刻文字列を作る
function get_passage($time)
{
	$time = UTIME - $time;
	
	if (ceil($time / 60) < 60) {
		$str = '('.ceil($time / 60).'m)';
	}
	else if (ceil($time / 60 / 60) < 24) {
		$str = '('.ceil($time / 60 / 60).'h)';
	}
	else {
		$str = '('.ceil($time / 60 / 60 / 24).'d)';
	}
	
	return $str;
}

//<input type="(submit|button|image)"...>を隠す
function drop_submit($str)
{
	return preg_replace(
		'/<input([^>]+)type="(submit|button|image)"/i',
		'<input$1type="hidden"',
		$str
	);
}

// AutoLinkのパターンを生成する
// thx for hirofummy
function get_autolink_pattern(&$pages)
{
	global $WikiName,$autolink,$nowikiname;
	
	$auto_pages = array();
	foreach ($pages as $page)
	{
		$match = preg_match("/^$WikiName$/",$page);
		if($match ? $nowikiname : strlen($page) >= $autolink)
		{
			$auto_pages[] = $page;
		}
	}
	if (count($auto_pages) == 0)
	{
		return $nowikiname ? '(?!)' : $WikiName;
	}
	
	sort($auto_pages,SORT_STRING);
	
	$result = get_autolink_pattern_sub($auto_pages,0,count($auto_pages),0);

	if(!$nowikiname)
	{
		$result .= '|(?:'.$WikiName.')';
	}
	
	return $result;
}
function get_autolink_pattern_sub(&$pages,$start,$end,$pos)
{
	$result = '';
	$count = 0;
	$x = (strlen($pages[$start]) <= $pos);
	
	if ($x) {
		$start++;
	}
	for ($i = $start; $i < $end; $i = $j)
	{
		$char = mb_substr($pages[$i],$pos,1);
		for ($j = $i; $j < $end; $j++)
		{
			if (mb_substr($pages[$j],$pos,1) != $char)
			{
				break;
			}
		}
		if ($i != $start)
		{
			$result .= '|';
		}
		if ($i >= ($j - 1))
		{
			$result .= preg_quote(mb_substr($pages[$i],$pos),'/'); 
		}
		else
		{
			$result .=
				preg_quote($char,'/').
				get_autolink_pattern_sub($pages,$i,$j,$pos + 1);
		}
		$count++;
	}
	if ($x or $count > 1)
	{
		$result = '(?:'.$result.')';
	}
	if ($x)
	{
		$result .= '?';
	}
	return $result;
}

//is_a
//(PHP 4 >= 4.2.0)
//
//is_a --  Returns TRUE if the object is of this class or has this class as one of its parents 

if (!function_exists('is_a')) {
	function is_a($class, $match)
	{
		if (empty($class)) {
			return false;
		}
		$class = is_object($class) ? get_class($class) : $class;
		if (strtolower($class) == strtolower($match)) {
			return true;
		}
		return is_a(get_parent_class($class), $match);
	}
}

//array_fill
//(PHP 4 >= 4.2.0)
//
//array_fill -- Fill an array with values

if (!function_exists('array_fill')) {
	function array_fill($start_index,$num,$value) {
		$ret = array();
		
		while ($num-- > 0)
			$ret[$start_index++] = $value;
		
		return $ret;
	}
}
?>
