<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: func.php,v 1.44 2003/06/22 06:37:19 arino Exp $
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
	
	$is_pagename = (!is_interwiki($str) and preg_match("/^(?!\/)$BracketName$(?<!\/$)/",$str)
		and !preg_match('/(^|\/)\.{1,2}(\/|$)/',$str));
	
	if (defined('SOURCE_ENCODING'))
	{
		if (SOURCE_ENCODING == 'UTF-8')
		{
			$is_pagename = ($is_pagename and preg_match('/^(?:[\x00-\x7F]|(?:[\xC0-\xDF][\x80-\xBF])|(?:[\xE0-\xEF][\x80-\xBF][\x80-\xBF]))+$/',$str)); // UTF-8
		}
		else if (SOURCE_ENCODING == 'EUC-JP')
		{
			$is_pagename = ($is_pagename and preg_match('/^(?:[\x00-\x7F]|(?:[\x8E\xA1-\xFE][\xA1-\xFE])|(?:\x8F[\xA1-\xFE][\xA1-\xFE]))+$/',$str)); // EUC-JP
		}
	}
	
	return $is_pagename;
}
// 文字列がURLかどうか
function is_url($str)
{
	return preg_match('/^(https?|ftp|news)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]*)$/', $str);
}
// ページが存在するか
function is_page($page,$reload=FALSE)
{
	global $InterWikiName;
	static $is_page = array();
	
	if ($reload or !array_key_exists($page,$is_page))
	{
		$is_page[$page] = file_exists(get_filename($page));
	}
	
	return $is_page[$page];
}
// ページが編集可能か
function is_editable($page)
{
	global $cantedit;
	static $is_editable = array();
	
	if (!array_key_exists($page,$is_editable))
	{
		$is_editable[$page] = (
			is_pagename($page) and
			!is_freeze($page) and
			!in_array($page,$cantedit)
		);
	}
	
	return $is_editable[$page];
}

// ページが凍結されているか
function is_freeze($page)
{
	global $function_freeze;

	if (!$function_freeze or !is_page($page))
	{
		return FALSE;
	}

	list($lines) = get_source($page);
	return (rtrim($lines) == '#freeze');
}

// 編集不可能なページを編集しようとしたとき
// デフォルト値で互換性を保つ
function check_editable($page, $auth_flag=true, $exit_flag=true)
{
	global $script,$_title_cannotedit,$_msg_unfreeze;
	
	if (edit_auth($page, $auth_flag, $exit_flag) and is_editable($page))
	{
		return true;
	}

	if ($exit_flag) {
		$body = $title = str_replace('$1',htmlspecialchars(strip_bracket($page)),$_title_cannotedit);
		if(is_freeze($page))
		{
			$body .= "(<a href=\"$script?cmd=unfreeze&amp;page=".
				rawurlencode($page)."\">$_msg_unfreeze</a>)";
		}

		$page = str_replace('$1',make_search($page),$_title_cannotedit);

		catbody($title,$page,$body);
		exit;
	}
	return false;
}

// 自動テンプレート
function auto_template($page)
{
	global $auto_template_func,$auto_template_rules;
	
	if (!$auto_template_func)
	{
		return '';
	}

	$body = '';
	foreach ($auto_template_rules as $rule => $template)
	{
		if (preg_match("/$rule/",$page,$matches))
		{
			$template_page = preg_replace("/$rule/",$template,$page);
			if (is_page($template_page))
			{
				$body = join('',get_source($template_page));
				// 見出しの固有ID部を削除
				$body = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m','$1$2',$body);
				// #freezeを削除
				$body = preg_replace('/^#freeze\s*$/m','',$body);
				
				for ($i = 0; $i < count($matches); $i++)
				{
					$body = str_replace("\$$i",$matches[$i],$body);
				}
				break;
			}
		}
	}
	return $body;
}
// 検索語を展開する
function get_search_words($words,$special=FALSE)
{
	$retval = array();
	// Perlメモ - 正しくパターンマッチさせる
	// http://www.din.or.jp/~ohzaki/perl.htm#JP_Match
	$eucpre = $eucpost = '';
	if (SOURCE_ENCODING == 'EUC-JP')
	{
		$eucpre = '(?<!\x8F)';
		// # JIS X 0208 が 0文字以上続いて # ASCII, SS2, SS3 または終端
		$eucpost = '(?=(?:[\xA1-\xFE][\xA1-\xFE])*(?:[\x00-\x7F\x8E\x8F]|\z))';
	}
	// $special : htmlspecialchars()を通すか
	$quote_func = create_function('$str',$special ?
		'return preg_quote($str,"/");' :
		'return preg_quote(htmlspecialchars($str),"/");'
	);
	// LANG=='ja'で、mb_convert_kanaが使える場合はmb_convert_kanaを使用
	$convert_kana = create_function('$str,$option',
		(LANG == 'ja' and function_exists('mb_convert_kana')) ?
			'return mb_convert_kana($str,$option);' : 'return $str;'
	);
	
	foreach ($words as $word)
	{
		// 英数字は半角,カタカナは全角,ひらがなはカタカナに
		$word_zk = $convert_kana($word,'aKCV');
		$chars = array();
		for ($pos = 0; $pos < mb_strlen($word_zk);$pos++)
		{
			$char = mb_substr($word_zk,$pos,1);
			$arr = array($quote_func($char));
			if (strlen($char) == 1) // 英数字
			{
				$_char = strtoupper($char); // 大文字
				$arr[] = $quote_func($_char);
				$arr[] = $quote_func($convert_kana($_char,"A")); // 全角
				$_char = strtolower($char); // 小文字
				$arr[] = $quote_func($_char);
				$arr[] = $quote_func($convert_kana($_char,"A")); // 全角
			}
			else // マルチバイト文字
			{
				$arr[] = $quote_func($convert_kana($char,"c")); // ひらがな
				$arr[] = $quote_func($convert_kana($char,"k")); // 半角カタカナ
			}
			$chars[] = '(?:'.join('|',array_unique($arr)).')';
		}
		$retval[$word] = $eucpre.join('',$chars).$eucpost;
	}
	return $retval;
}
// 検索
function do_search($word,$type='AND',$non_format=FALSE)
{
	global $script,$vars,$whatsnew,$non_list,$search_non_list;
	global $_msg_andresult,$_msg_orresult,$_msg_notfoundresult;
	
	$database = array();
	$retval = array();

	$b_type = ($type == 'AND'); // AND:TRUE OR:FALSE
	$keys = get_search_words(preg_split('/\s+/',$word,-1,PREG_SPLIT_NO_EMPTY));
	
	$_pages = get_existpages();
	$pages = array();
	
	foreach ($_pages as $page)
	{
		if ($page == $whatsnew
			or (!$search_non_list and preg_match("/$non_list/",$page)))
		{
			continue;
		}
		
		// 検索対象ページの制限をかけるかどうか (ページ名は制限外)
		if ($search_auth and !check_readable($page,false,false)) {
			$source = get_source(); // 検索対象ページ内容を空に。
		} else {
			$source = get_source($page);
		}
		if (!$non_format)
		{
			array_unshift($source,$page); // ページ名も検索対象に
		}
		
		$b_match = FALSE;
		foreach ($keys as $key)
		{
			$tmp = preg_grep("/$key/",$source);
			$b_match = (count($tmp) > 0);
			if ($b_match xor $b_type)
			{
				break;
			}
		}
		if ($b_match)
		{
			$pages[$page] = get_filetime($page);
		}
	}
	if ($non_format)
	{
		return array_keys($pages);
	}
	$r_word = rawurlencode($word);
	$s_word = htmlspecialchars($word);
	if (count($pages) == 0)
	{
		return str_replace('$1',$s_word,$_msg_notfoundresult);
	}
	ksort($pages);
	$retval = "<ul>\n";
	foreach ($pages as $page=>$time)
	{
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
	return ($key == '') ? '' : strtoupper(join('',unpack('H*0',$key)));
}

// ページ名のデコード
function decode($key)
{
	return ($key == '') ? '' : substr(pack('H*','20202020'.$key),4);
}

// [[ ]] を取り除く
function strip_bracket($str)
{
	if (preg_match('/^\[\[(.*)\]\]$/',$str,$match))
	{
		$str = $match[1];
	}
	return $str;
}

// ページ一覧の作成
function page_list($pages, $cmd = 'read', $withfilename=FALSE)
{
	global $script,$list_index,$top;
	global $_msg_symbol,$_msg_other;
	
	// ソートキーを決定する。 ' ' < '[a-zA-Z]' < 'zz'という前提。
	$symbol = ' ';
	$other = 'zz';
	
	$retval = '';
	
	$list = array();
	foreach($pages as $file=>$page)
	{
		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page,ENT_QUOTES);
		$passage = get_pg_passage($page);
		
		$str = "   <li><a href=\"$script?cmd=$cmd&amp;page=$r_page\">$s_page</a>$passage";
		
		if ($withfilename)
		{
			$s_file = htmlspecialchars($file);
			$str .= "\n    <ul><li>$s_file</li></ul>\n   ";
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
	foreach ($list as $head=>$pages)
	{
		if ($head === $symbol)
		{
			$head = $_msg_symbol;
		}
		else if ($head === $other)
		{
			$head = $_msg_other;
		}
		
		if ($list_index)
		{
			$cnt++;
			$arr_index[] = "<a id=\"top_$cnt\" href=\"#head_$cnt\"><strong>$head</strong></a>";
			$retval .= " <li><a id=\"head_$cnt\" href=\"#top_$cnt\"><strong>$head</strong></a>\n  <ul>\n";
		}
		ksort($pages);
		$retval .= join("\n",$pages);
		if ($list_index)
		{
			$retval .= "\n  </ul>\n </li>\n";
		}
	}
	$retval .= "</ul>\n";
	if ($list_index and $cnt > 0)
	{
		$top = array();
		while (count($arr_index) > 0)
		{
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
	
	if (!is_page($rule_page))
	{
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
	
	if(defined('SKIN_FILE') && file_exists(SKIN_FILE) && is_readable(SKIN_FILE))
	{
	  catbody($title,$page,$body);
	}
	else
	{
		header('Content-Type: text/html; charset=euc-jp');
		print <<<EOD
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
EOD;
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
function format_date($val, $paren = FALSE)
{
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
	static $units = array('s'=>60,'m'=>60,'h'=>24,'d'=>1);
	
	$time = UTIME - $time;
	
	foreach ($units as $unit=>$card)
	{
		if ($time < $card)
		{
			break;
		}
		$time /= $card;
	}
	$time = floor($time);
	
	return "($time$unit)";
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
	
	$config = &new Config('AutoLink');
	$config->read();
	$ignorepages = $config->get('IgnoreList');
	$forceignorepages = $config->get('ForceIgnoreList');
	unset($config);
	$auto_pages = array_merge($ignorepages,$forceignorepages);
	
	foreach ($pages as $page)
	{
		if (preg_match("/^$WikiName$/",$page) ?
			$nowikiname : strlen($page) >= $autolink)
		{
			$auto_pages[] = $page;
		}
	}

	if (count($auto_pages) == 0)
	{
		return $nowikiname ? '(?!)' : $WikiName;
	}
	
	$auto_pages = array_unique($auto_pages);
	sort($auto_pages,SORT_STRING);
	
	$result = get_autolink_pattern_sub($auto_pages,0,count($auto_pages),0);
	
	return array($result,$forceignorepages);
}
function get_autolink_pattern_sub(&$pages,$start,$end,$pos)
{
	$result = '';
	$count = 0;
	$x = (mb_strlen($pages[$start]) <= $pos);
	
	if ($x)
	{
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

/*
変数内のnull(\0)バイトを削除する
PHPはfopen("hoge.php\0.txt")で"hoge.php"を開いてしまうなどの問題あり

http://ns1.php.gr.jp/pipermail/php-users/2003-January/012742.html
[PHP-users 12736] null byte attack

2003-05-16: magic quotes gpcの復元処理を統合
2003-05-21: 連想配列のキーはbinary safe
*/ 
function sanitize($param)
{
	if (is_array($param))
	{
		$result = array_map('sanitize',$param);
	}
	else
	{
		$result = str_replace("\0",'',$param);
		if (get_magic_quotes_gpc())
		{
			$result = stripslashes($result);
		}
	}
	return $result;
}

//is_a
//(PHP 4 >= 4.2.0)
//
//is_a --  Returns TRUE if the object is of this class or has this class as one of its parents 

if (!function_exists('is_a'))
{
	function is_a($class, $match)
	{
		if (empty($class))
		{
			return false;
		}
		$class = is_object($class) ? get_class($class) : $class;
		if (strtolower($class) == strtolower($match))
		{
			return true;
		}
		return is_a(get_parent_class($class), $match);
	}
}

//array_fill
//(PHP 4 >= 4.2.0)
//
//array_fill -- Fill an array with values

if (!function_exists('array_fill'))
{
	function array_fill($start_index,$num,$value)
	{
		$ret = array();
		
		while ($num-- > 0)
		{
			$ret[$start_index++] = $value;
		}
		return $ret;
	}
}
//md5_file
//(PHP 4 >= 4.2.0)
//
//md5_file -- Calculates the md5 hash of a given filename

if (!function_exists('md5_file'))
{
	function md5_file($filename)
	{
		
		if (!file_exists($filename))
		{
			return FALSE;
		}
		$fd = fopen($filename, 'rb');
		$data = fread($fd, filesize($filename));
		fclose($fd);
		return md5($data);
	}
}

/////////////////////////////////////////////////////////////////
// Basic認証による権限チェック
// 編集認証
// 閲覧との対称性を持たせた
function edit_auth($page, $auth_flag=true, $exit_flag=true) {
	global $_msg_auth,$edit_auth,$auth_users,$edit_auth_pages;
	global $_title_cannotedit,$auth_method_type;

	// 編集認証フラグをチェック (システム全体として編集認証するかどうか)
	if (!$edit_auth) { return true; }

	// 認証要否判断対象文字列を取得する
	// ページ名でチェックする場合
	if ($auth_method_type == "pagename") {
		$target_str = $page;
	}
	// ページ内の文字列でチェックする場合
	else if ($auth_method_type == "contents") {
		$target_str = join('',get_source($page));
	}
	else {
		$target_str = "";
	}

	// 合致したパターンで定義されたユーザリストをマージする
	reset($edit_auth_pages);
	$user_list = "";
	while (list($key, $val) = each($edit_auth_pages)) {
		if (preg_match($key, $target_str)) {
			$user_list .= ",".$val;
		}
	}
	if ($user_list == "") { return true; }

	// ユーザリストに含まれるいずれかのユーザと認証されればOK
	if (!isset($_SERVER['PHP_AUTH_USER'])
		or !preg_match("/".$_SERVER['PHP_AUTH_USER']."/", $user_list)
		or !array_key_exists($_SERVER['PHP_AUTH_USER'], $auth_users)
		or $auth_users[$_SERVER['PHP_AUTH_USER']] != $_SERVER['PHP_AUTH_PW'])
	{
		if ($auth_flag) {
			header('WWW-Authenticate: Basic realm="'.$_msg_auth.'"');
			header('HTTP/1.0 401 Unauthorized');
		}
		if ($exit_flag) {
			$body = $title = str_replace('$1',htmlspecialchars(strip_bracket($page)), $_title_cannotedit);
			$page = str_replace('$1',make_search($page),$_title_cannotedit);
			catbody($title, $page, $body);
			exit;
		}
		return false;
	}
	return true;
}

// 閲覧不可能なページを閲覧しようとしたとき (？)
//  ※あまり必要性を感じないが、editの場合と対称性を持たせるために導入。
function check_readable($page, $auth_flag=true, $exit_flag=true) {
	if (read_auth($page, $auth_flag, $exit_flag)) {
		return true;
	}
	return false;
}

// 閲覧認証
function read_auth($page, $auth_flag=true, $exit_flag=true) {
	global $_msg_auth,$read_auth,$auth_users,$read_auth_pages;
	global $_title_cannotread, $auth_method_type;

	// 閲覧認証フラグをチェック
	if (!$read_auth) { return true; }

	// 認証要否判断対象文字列を取得する
	// ページ名でチェックする場合
	if ($auth_method_type == "pagename") {
		$target_str = $page;
	}
	// ページ内の文字列でチェックする場合
	else if ($auth_method_type == "contents") {
		$target_str = join('',get_source($page));
	}
	else {
		$target_str = "";
	}

	// 合致したパターンで定義されたユーザリストをマージする
	reset($read_auth_pages);
	$user_list = "";
	while (list($key, $val) = each($read_auth_pages)) {
		if (preg_match($key, $target_str)) {
			$user_list .= ",".$val;
		}
	}
	if ($user_list == "") { return true; }

	// ユーザリストに含まれるいずれかのユーザと認証されればOK
	if (!isset($_SERVER['PHP_AUTH_USER'])
		or !preg_match("/".$_SERVER['PHP_AUTH_USER']."/", $user_list)
		or !array_key_exists($_SERVER['PHP_AUTH_USER'], $auth_users)
		or $auth_users[$_SERVER['PHP_AUTH_USER']] != $_SERVER['PHP_AUTH_PW'])
	{
		if ($auth_flag) {
			header('WWW-Authenticate: Basic realm="'.$_msg_auth.'"');
			header('HTTP/1.0 401 Unauthorized');
		}
		if ($exit_flag) {
			$body = $title = str_replace('$1',htmlspecialchars(strip_bracket($page)), $_title_cannotread);
			$page = str_replace('$1',make_search($page),$_title_cannotread);
			catbody($title, $page, $body);
			exit;
		}
		return false;
	}
	return true;
}
?>
