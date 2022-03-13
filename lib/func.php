<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// func.php
// Copyright
//   2002-2022 PukiWiki Development Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// General functions

// URI type enum
/** Relative path. */
define('PKWK_URI_RELATIVE', 0);
/** Root relative URI. */
define('PKWK_URI_ROOT', 1);
/** Absolute URI. */
define('PKWK_URI_ABSOLUTE', 2);

/** New page name - its length is need to be within the soft limit. */
define('PKWK_PAGENAME_BYTES_SOFT_LIMIT', 115);
/** Page name - its length is need to be within the hard limit. */
define('PKWK_PAGENAME_BYTES_HARD_LIMIT', 125);

function pkwk_log($message)
{
	$log_filepath = 'log/error.log.php';
	static $dateTimeExists;
	if (!isset($dateTimeExists)) {
		$dateTimeExists = class_exists('DateTime');
		error_log("<?php\n", 3, $log_filepath);
	}
	if ($dateTimeExists) {
		// for PHP5.2+
		$d = \DateTime::createFromFormat('U.u', sprintf('%6F', microtime(true)));
		$timestamp = substr($d->format('Y-m-d H:i:s.u'), 0, 23);
	} else {
		$timestamp = date('Y-m-d H:i:s');
	}
	error_log($timestamp . ' ' . $message . "\n", 3, $log_filepath);
}

/*
 * Get LTSV safe string - Remove tab and newline chars.
 *
 * @param $s target string
 */
function get_ltsv_value($s) {
	if (!$s) {
		return '';
	}
	return preg_replace('#[\t\r\n]#', '', $s);
}

/**
 * Write update_log on updating contents.
 *
 * @param $page page name
 * @param $diff_content diff expression
 */
function pkwk_log_updates($page, $diff_content) {
	global $auth_user, $logging_updates, $logging_updates_log_dir;
	$log_dir = $logging_updates_log_dir;
	$timestamp = time();
	$ymd = gmdate('Ymd', $timestamp);
	$difflog_file = $log_dir . '/diff.' . $ymd . '.log';
	$ltsv_file = $log_dir . '/update.' . $ymd . '.log';
	$d = array(
		'time' => gmdate('Y-m-d H:i:s', $timestamp),
		'uri' => $_SERVER['REQUEST_URI'],
		'method' => $_SERVER['REQUEST_METHOD'],
		'remote_addr' => $_SERVER['REMOTE_ADDR'],
		'user_agent' => $_SERVER['HTTP_USER_AGENT'],
		'page' => $page,
		'user' => $auth_user,
		'diff' => $diff_content
	);
	if (file_exists($log_dir) && defined('JSON_UNESCAPED_UNICODE')) {
		// require: PHP5.4+
		$line = json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
		file_put_contents($difflog_file, $line, FILE_APPEND | LOCK_EX);
		$keys = array('time', 'uri', 'method', 'remote_addr', 'user_agent',
			'page', 'user');
		$ar2 = array();
		foreach ($keys as $k) {
			$ar2[] = $k . ':' . get_ltsv_value($d[$k]);
		}
		$ltsv = join($ar2, "\t") . "\n";
		file_put_contents($ltsv_file, $ltsv, FILE_APPEND | LOCK_EX);
	}
}

/**
 * ctype_digit that supports PHP4+.
 *
 * PHP official document says PHP4 has ctype_digit() function.
 * But sometimes it doen't exists on PHP 4.1.
 */
function pkwk_ctype_digit($s) {
	static $ctype_digit_exists;
	if (!isset($ctype_digit_exists)) {
		$ctype_digit_exists = function_exists('ctype_digit');
	}
	if ($ctype_digit_exists) {
		return ctype_digit($s);
	}
	return preg_match('/^[0-9]+$/', $s) ? true : false;
}

function is_interwiki($str)
{
	global $InterWikiName;
	return preg_match('/^' . $InterWikiName . '$/', $str);
}

function is_pagename($str)
{
	global $BracketName;

	$is_pagename = (! is_interwiki($str) &&
		  preg_match('/^(?!\/)' . $BracketName . '$(?<!\/$)/', $str) &&
		! preg_match('#(^|/)\.{1,2}(/|$)#', $str));

	if (defined('SOURCE_ENCODING')) {
		switch(SOURCE_ENCODING){
		case 'UTF-8': $pattern =
			'/^(?:[\x00-\x7F]|(?:[\xC0-\xDF][\x80-\xBF])|(?:[\xE0-\xEF][\x80-\xBF][\x80-\xBF]))+$/';
			break;
		case 'EUC-JP': $pattern =
			'/^(?:[\x00-\x7F]|(?:[\x8E\xA1-\xFE][\xA1-\xFE])|(?:\x8F[\xA1-\xFE][\xA1-\xFE]))+$/';
			break;
		}
		if (isset($pattern) && $pattern != '')
			$is_pagename = ($is_pagename && preg_match($pattern, $str));
	}

	return $is_pagename;
}

function is_url($str, $only_http = FALSE)
{
	$scheme = $only_http ? 'https?' : 'https?|ftp|news';
	return preg_match('/^(' . $scheme . ')(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]*)$/', $str);
}

// If the page exists
function is_page($page, $clearcache = FALSE)
{
	if ($clearcache) clearstatcache();
	return file_exists(get_filename($page));
}

function is_pagename_bytes_within_soft_limit($page)
{
	return strlen($page) <= PKWK_PAGENAME_BYTES_SOFT_LIMIT;
}

function is_pagename_bytes_within_hard_limit($page)
{
	return strlen($page) <= PKWK_PAGENAME_BYTES_SOFT_LIMIT;
}

function page_exists_in_history($page)
{
	if (is_page($page)) {
		return true;
	}
	$diff_file = DIFF_DIR . encode($page) . '.txt';
	if (file_exists($diff_file)) {
		return true;
	}
	$backup_file = BACKUP_DIR . encode($page) . BACKUP_EXT;
	if (file_exists($backup_file)) {
		return true;
	}
	return false;
}

function is_editable($page)
{
	global $cantedit;
	static $is_editable = array();

	if (! isset($is_editable[$page])) {
		$is_editable[$page] = (
			is_pagename($page) &&
			! is_freeze($page) &&
			! in_array($page, $cantedit)
		);
	}

	return $is_editable[$page];
}

function is_freeze($page, $clearcache = FALSE)
{
	global $function_freeze;
	static $is_freeze = array();

	if ($clearcache === TRUE) $is_freeze = array();
	if (isset($is_freeze[$page])) return $is_freeze[$page];

	if (! $function_freeze || ! is_page($page)) {
		$is_freeze[$page] = FALSE;
		return FALSE;
	} else {
		$fp = fopen(get_filename($page), 'rb') or
			die('is_freeze(): fopen() failed: ' . htmlsc($page));
		flock($fp, LOCK_SH) or die('is_freeze(): flock() failed');
		rewind($fp);
		$buffer = fread($fp, 1000);
		flock($fp, LOCK_UN) or die('is_freeze(): flock() failed');
		fclose($fp) or die('is_freeze(): fclose() failed: ' . htmlsc($page));
		$is_freeze[$page] = (bool) preg_match('/^#freeze$/m', $buffer);
		return $is_freeze[$page];
	}
}

// Handling $non_list
// $non_list will be preg_quote($str, '/') later.
function check_non_list($page = '')
{
	global $non_list;
	static $regex;

	if (! isset($regex)) $regex = '/' . $non_list . '/';

	return preg_match($regex, $page);
}

// Auto template
function auto_template($page)
{
	global $auto_template_func, $auto_template_rules;

	if (! $auto_template_func) return '';

	$body = '';
	$matches = array();
	foreach ($auto_template_rules as $rule => $template) {
		$rule_pattrn = '/' . $rule . '/';

		if (! preg_match($rule_pattrn, $page, $matches)) continue;

		$template_page = preg_replace($rule_pattrn, $template, $page);
		if (! is_page($template_page)) continue;

		$body = join('', get_source($template_page));

		// Remove fixed-heading anchors
		$body = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $body);

		// Remove '#freeze'
		$body = preg_replace('/^#freeze\s*$/m', '', $body);

		$count = count($matches);
		for ($i = 0; $i < $count; $i++)
			$body = str_replace('$' . $i, $matches[$i], $body);

		break;
	}
	return $body;
}

function _mb_convert_kana__enable($str, $option) {
	return mb_convert_kana($str, $option, SOURCE_ENCODING);
}
function _mb_convert_kana__none($str, $option) {
	return $str;
}

// Expand all search-words to regexes and push them into an array
function get_search_words($words = array(), $do_escape = FALSE)
{
	static $init, $mb_convert_kana, $pre, $post, $quote = '/';

	if (! isset($init)) {
		// function: mb_convert_kana() is for Japanese code only
		if (LANG == 'ja' && function_exists('mb_convert_kana')) {
			$mb_convert_kana = '_mb_convert_kana__enable';
		} else {
			$mb_convert_kana = '_mb_convert_kana__none';
		}
		if (SOURCE_ENCODING == 'EUC-JP') {
			// Perl memo - Correct pattern-matching with EUC-JP
			// http://www.din.or.jp/~ohzaki/perl.htm#JP_Match (Japanese)
			$pre  = '(?<!\x8F)';
			$post =	'(?=(?:[\xA1-\xFE][\xA1-\xFE])*' . // JIS X 0208
				'(?:[\x00-\x7F\x8E\x8F]|\z))';     // ASCII, SS2, SS3, or the last
		} else {
			$pre = $post = '';
		}
		$init = TRUE;
	}

	if (! is_array($words)) $words = array($words);

	// Generate regex for the words
	$regex = array();
	foreach ($words as $word) {
		$word = trim($word);
		if ($word == '') continue;

		// Normalize: ASCII letters = to single-byte. Others = to Zenkaku and Katakana
		$word_nm = $mb_convert_kana($word, 'aKCV');
		$nmlen   = mb_strlen($word_nm, SOURCE_ENCODING);

		// Each chars may be served ...
		$chars = array();
		for ($pos = 0; $pos < $nmlen; $pos++) {
			$char = mb_substr($word_nm, $pos, 1, SOURCE_ENCODING);

			// Just normalized one? (ASCII char or Zenkaku-Katakana?)
			$or = array(preg_quote($do_escape ? htmlsc($char) : $char, $quote));
			if (strlen($char) == 1) {
				// An ASCII (single-byte) character
				foreach (array(strtoupper($char), strtolower($char)) as $_char) {
					if ($char != '&') $or[] = preg_quote($_char, $quote); // As-is?
					$ascii = ord($_char);
					$or[] = sprintf('&#(?:%d|x%x);', $ascii, $ascii); // As an entity reference?
					$or[] = preg_quote($mb_convert_kana($_char, 'A'), $quote); // As Zenkaku?
				}
			} else {
				// NEVER COME HERE with mb_substr(string, start, length, 'ASCII')
				// A multi-byte character
				$or[] = preg_quote($mb_convert_kana($char, 'c'), $quote); // As Hiragana?
				$or[] = preg_quote($mb_convert_kana($char, 'k'), $quote); // As Hankaku-Katakana?
			}
			$chars[] = '(?:' . join('|', array_unique($or)) . ')'; // Regex for the character
		}

		$regex[$word] = $pre . join('', $chars) . $post; // For the word
	}

	return $regex; // For all words
}

function get_passage_date_html_span($date_atom)
{
	return '<span class="page_passage" data-mtime="' . $date_atom . '"></span>';
}

function get_passage_mtime_html_span($mtime)
{
	$date_atom = get_date_atom($mtime);
	return get_passage_date_html_span($date_atom);
}

/**
 * Get passage span html
 *
 * @param $page
 */
function get_passage_html_span($page)
{
	$date_atom = get_page_date_atom($page);
	return get_passage_date_html_span($date_atom);
}

function get_link_passage_class() {
	return 'link_page_passage';
}

/**
 * Get page link general attributes
 * @param $page
 * @return array('data_mtime' => page mtime or null, 'class' => additinal classes)
 */
function get_page_link_a_attrs($page)
{
	global $show_passage;
	if ($show_passage) {
		$pagemtime = get_page_date_atom($page);
		return array(
			'data_mtime' => $pagemtime,
			'class' => get_link_passage_class(),
		);
	}
	return array(
		'data_mtime' => '',
		'class' => ''
	);
}

/**
 * Get page link general attributes from filetime
 * @param $filetime
 * @return array('data_mtime' => page mtime or null, 'class' => additinal classes)
 */
function get_filetime_a_attrs($filetime)
{
	global $show_passage;
	if ($show_passage) {
		$pagemtime = get_date_atom($filetime + LOCALZONE);
		return array(
			'data_mtime' => $pagemtime,
			'class' => get_link_passage_class(),
		);
	}
	return array(
		'data_mtime' => '',
		'class' => ''
	);
}

// 'Search' main function
function do_search($word, $type = 'AND', $non_format = FALSE, $base = '')
{
	global $whatsnew, $non_list, $search_non_list;
	global $_msg_andresult, $_msg_orresult, $_msg_notfoundresult;
	global $search_auth, $show_passage;

	$retval = array();

	$b_type = ($type == 'AND'); // AND:TRUE OR:FALSE
	$keys = get_search_words(preg_split('/\s+/', $word, -1, PREG_SPLIT_NO_EMPTY));
	foreach ($keys as $key=>$value)
		$keys[$key] = '/' . $value . '/S';

	$pages = get_existpages();

	// Avoid
	if ($base != '') {
		$pages = preg_grep('/^' . preg_quote($base, '/') . '/S', $pages);
	}
	if (! $search_non_list) {
		$pages = array_diff($pages, preg_grep('/' . $non_list . '/S', $pages));
	}
	$pages = array_flip($pages);
	unset($pages[$whatsnew]);

	$count = count($pages);
	foreach (array_keys($pages) as $page) {
		$b_match = FALSE;

		// Search for page name
		if (! $non_format) {
			foreach ($keys as $key) {
				$b_match = preg_match($key, $page);
				if ($b_type xor $b_match) break; // OR
			}
			if ($b_match) continue;
		}

		// Search auth for page contents
		if ($search_auth && ! check_readable($page, false, false)) {
			unset($pages[$page]);
			--$count;
			continue;
		}

		// Search for page contents
		foreach ($keys as $key) {
			$body = get_source($page, TRUE, TRUE, TRUE);
			$b_match = preg_match($key, remove_author_header($body));
			if ($b_type xor $b_match) break; // OR
		}
		if ($b_match) continue;

		unset($pages[$page]); // Miss
	}
	if ($non_format) return array_keys($pages);

	$r_word = rawurlencode($word);
	$s_word = htmlsc($word);
	if (empty($pages))
		return str_replace('$1', $s_word, str_replace('$3', $count, $_msg_notfoundresult));

	ksort($pages, SORT_STRING);

	$retval = '<ul>' . "\n";
	foreach (array_keys($pages) as $page) {
		$r_page  = rawurlencode($page);
		$s_page  = htmlsc($page);
		$passage = $show_passage ? ' ' . get_passage_html_span($page) : '';
		$retval .= ' <li><a href="' . get_base_uri() . '?cmd=read&amp;page=' .
			$r_page . '&amp;word=' . $r_word . '">' . $s_page .
			'</a>' . $passage . '</li>' . "\n";
	}
	$retval .= '</ul>' . "\n";

	$retval .= str_replace('$1', $s_word, str_replace('$2', count($pages),
		str_replace('$3', $count, $b_type ? $_msg_andresult : $_msg_orresult)));

	return $retval;
}

// Argument check for program
function arg_check($str)
{
	global $vars;
	return isset($vars['cmd']) && (strpos($vars['cmd'], $str) === 0);
}

function _pagename_urlencode_callback($matches)
{
	return urlencode($matches[0]);
}

function pagename_urlencode($page)
{
	return preg_replace_callback('|[^/:]+|', '_pagename_urlencode_callback', $page);
}

// Encode page-name
function encode($str)
{
	$str = strval($str);
	return ($str == '') ? '' : strtoupper(bin2hex($str));
	// Equal to strtoupper(join('', unpack('H*0', $key)));
	// But PHP 4.3.10 says 'Warning: unpack(): Type H: outside of string in ...'
}

// Decode page name
function decode($str)
{
	return pkwk_hex2bin($str);
}

// Inversion of bin2hex()
function pkwk_hex2bin($hex_string)
{
	// preg_match : Avoid warning : pack(): Type H: illegal hex digit ...
	// (string)   : Always treat as string (not int etc). See BugTrack2/31
	return preg_match('/^[0-9a-f]+$/i', $hex_string) ?
		pack('H*', (string)$hex_string) : $hex_string;
}

// Remove [[ ]] (brackets)
function strip_bracket($str)
{
	$match = array();
	if (preg_match('/^\[\[(.*)\]\]$/', $str, $match)) {
		return $match[1];
	} else {
		return $str;
	}
}

// Create list of pages
function page_list($pages, $cmd = 'read', $withfilename = FALSE)
{
	global $list_index;
	global $_msg_symbol, $_msg_other;
	global $pagereading_enable;

	$script = get_base_uri();

	// ソートキーを決定する。 ' ' < '[a-zA-Z]' < 'zz'という前提。
	$symbol = ' ';
	$other = 'zz';

	$retval = '';

	if($pagereading_enable) {
		mb_regex_encoding(SOURCE_ENCODING);
		$readings = get_readings($pages);
	}
	$list = $matches = array();
	uasort($pages, 'strnatcmp');
	foreach($pages as $file=>$page) {
		$s_page  = htmlsc($page, ENT_QUOTES);
		// Shrink URI for read
		if ($cmd == 'read') {
			$href = get_page_uri($page);
		} else {
			$href = $script . '?cmd=' . $cmd . '&amp;page=' . rawurlencode($page);
		}
		$str = '   <li><a href="' . $href . '">' .
			$s_page . '</a> ' . get_pg_passage($page);
		if ($withfilename) {
			$s_file = htmlsc($file);
			$str .= "\n" . '    <ul><li>' . $s_file . '</li></ul>' .
				"\n" . '   ';
		}
		$str .= '</li>';

		// WARNING: Japanese code hard-wired
		if($pagereading_enable) {
			if(mb_ereg('^([A-Za-z])', mb_convert_kana($page, 'a'), $matches)) {
				$head = strtoupper($matches[1]);
			} elseif (isset($readings[$page]) && mb_ereg('^([ァ-ヶ])', $readings[$page], $matches)) { // here
				$head = $matches[1];
			} elseif (mb_ereg('^[ -~]|[^ぁ-ん亜-熙]', $page)) { // and here
				$head = $symbol;
			} else {
				$head = $other;
			}
		} else {
			$head = (preg_match('/^([A-Za-z])/', $page, $matches)) ? strtoupper($matches[1]) :
				(preg_match('/^([ -~])/', $page) ? $symbol : $other);
		}
		$list[$head][$page] = $str;
	}
	uksort($list, 'strnatcmp');

	$cnt = 0;
	$arr_index = array();
	$retval .= '<ul>' . "\n";
	foreach ($list as $head=>$sub_pages) {
		if ($head === $symbol) {
			$head = $_msg_symbol;
		} else if ($head === $other) {
			$head = $_msg_other;
		}

		if ($list_index) {
			++$cnt;
			$arr_index[] = '<a id="top_' . $cnt .
				'" href="#head_' . $cnt . '"><strong>' .
				$head . '</strong></a>';
			$retval .= ' <li><a id="head_' . $cnt . '" href="#top_' . $cnt .
				'"><strong>' . $head . '</strong></a>' . "\n" .
				'  <ul>' . "\n";
		}
		$retval .= join("\n", $sub_pages);
		if ($list_index)
			$retval .= "\n  </ul>\n </li>\n";
	}
	$retval .= '</ul>' . "\n";
	if ($list_index && $cnt > 0) {
		$top = array();
		while (! empty($arr_index))
			$top[] = join(' | ' . "\n", array_splice($arr_index, 0, 16)) . "\n";

		$retval = '<div id="top" style="text-align:center">' . "\n" .
			join('<br />', $top) . '</div>' . "\n" . $retval;
	}
	return $retval;
}

// Show text formatting rules
function catrule()
{
	global $rule_page;

	if (! is_page($rule_page)) {
		return '<p>Sorry, page \'' . htmlsc($rule_page) .
			'\' unavailable.</p>';
	} else {
		return convert_html(get_source($rule_page));
	}
}

// Show (critical) error message
function die_message($msg)
{
	$title = $page = 'Runtime error';
	$body = <<<EOD
<h3>Runtime error</h3>
<strong>Error message : $msg</strong>
EOD;

	pkwk_common_headers();
	if(defined('SKIN_FILE') && file_exists(SKIN_FILE) && is_readable(SKIN_FILE)) {
		catbody($title, $page, $body);
	} else {
		$charset = 'utf-8';
		if(defined('CONTENT_CHARSET')) {
			$charset = CONTENT_CHARSET;
		}
		header("Content-Type: text/html; charset=$charset");
		print <<<EOD
<!DOCTYPE html>
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=$charset">
  <title>$title</title>
 </head>
 <body>
 $body
 </body>
</html>
EOD;
	}
	exit;
}

function die_invalid_pagename() {
	$title = 'Error';
	$page = 'Error: Invlid page name';
	$body = <<<EOD
<h3>Error</h3>
<strong>Error message: Invalid page name</strong>
EOD;

	pkwk_common_headers();
	header('HTTP/1.0 400 Bad request');
	catbody($title, $page, $body);
	exit;
}


// Have the time (as microtime)
function getmicrotime()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$sec + (float)$usec);
}

// Elapsed time by second
//define('MUTIME', getmicrotime());
function elapsedtime()
{
	$at_the_microtime = MUTIME;
	return sprintf('%01.03f', getmicrotime() - $at_the_microtime);
}

// Get the date
function get_date($format, $timestamp = NULL)
{
	$format = preg_replace('/(?<!\\\)T/',
		preg_replace('/(.)/', '\\\$1', ZONE), $format);

	$time = ZONETIME + (($timestamp !== NULL) ? $timestamp : UTIME);

	return date($format, $time);
}

// Format date string
function format_date($val, $paren = FALSE)
{
	global $date_format, $time_format, $weeklabels;

	$val += ZONETIME;

	$date = date($date_format, $val) .
		' (' . $weeklabels[date('w', $val)] . ') ' .
		date($time_format, $val);

	return $paren ? '(' . $date . ')' : $date;
}

/**
 * Format date in DATE_ATOM format.
 */
function get_date_atom($timestamp)
{
	// Compatible with DATE_ATOM format
	// return date(DATE_ATOM, $timestamp);
	$zmin = abs(LOCALZONE / 60);
	return date('Y-m-d\TH:i:s', $timestamp) . sprintf('%s%02d:%02d',
		(LOCALZONE < 0 ? '-' : '+') , $zmin / 60, $zmin % 60);
}

// Get short string of the passage, 'N seconds/minutes/hours/days/years ago'
function get_passage($time, $paren = TRUE)
{
	static $units = array('m'=>60, 'h'=>24, 'd'=>1);

	$time = max(0, (UTIME - $time) / 60); // minutes

	foreach ($units as $unit=>$card) {
		if ($time < $card) break;
		$time /= $card;
	}
	$time = floor($time) . $unit;

	return $paren ? '(' . $time . ')' : $time;
}

// Hide <input type="(submit|button|image)"...>
function drop_submit($str)
{
	return preg_replace('/<input([^>]+)type="(submit|button|image)"/i',
		'<input$1type="hidden"', $str);
}

// Generate AutoLink patterns (thx to hirofummy)
function get_autolink_pattern($pages, $min_length)
{
	global $WikiName, $nowikiname;

	$config = new Config('AutoLink');
	$config->read();
	$ignorepages      = $config->get('IgnoreList');
	$forceignorepages = $config->get('ForceIgnoreList');
	unset($config);
	$auto_pages = array_merge($ignorepages, $forceignorepages);

	foreach ($pages as $page) {
		if (preg_match('/^' . $WikiName . '$/', $page) ?
		    $nowikiname : strlen($page) >= $min_length) {
			$auto_pages[] = $page;
		}
	}
	if (empty($auto_pages)) {
		$result = $result_a = '(?!)';
	} else {
		$auto_pages = array_unique($auto_pages);
		sort($auto_pages, SORT_STRING);

		$auto_pages_a = array_values(preg_grep('/^[A-Z]+$/i', $auto_pages));
		$auto_pages   = array_values(array_diff($auto_pages,  $auto_pages_a));

		$result   = get_autolink_pattern_sub($auto_pages,   0, count($auto_pages),   0);
		$result_a = get_autolink_pattern_sub($auto_pages_a, 0, count($auto_pages_a), 0);
	}
	return array($result, $result_a, $forceignorepages);
}

function get_autolink_pattern_sub($pages, $start, $end, $pos)
{
	if ($end == 0) return '(?!)';

	$result = '';
	$count = $i = $j = 0;
	$x = (mb_strlen($pages[$start]) <= $pos);
	if ($x) ++$start;

	for ($i = $start; $i < $end; $i = $j) {
		$char = mb_substr($pages[$i], $pos, 1);
		for ($j = $i; $j < $end; $j++)
			if (mb_substr($pages[$j], $pos, 1) != $char) break;

		if ($i != $start) $result .= '|';
		if ($i >= ($j - 1)) {
			$result .= str_replace(' ', '\\ ', preg_quote(mb_substr($pages[$i], $pos), '/'));
		} else {
			$result .= str_replace(' ', '\\ ', preg_quote($char, '/')) .
				get_autolink_pattern_sub($pages, $i, $j, $pos + 1);
		}
		++$count;
	}
	if ($x || $count > 1) $result = '(?:' . $result . ')';
	if ($x)               $result .= '?';

	return $result;
}

// Get AutoAlias value
function get_autoalias_right_link($alias_name)
{
	$pairs = get_autoaliases();
	// A string: Seek the pair
	if (isset($pairs[$alias_name])) {
		return $pairs[$alias_name];
	}
	return '';
}

// Load setting pairs from AutoAliasName
function get_autoaliases()
{
	global $aliaspage, $autoalias_max_words;
	static $pairs;
	$preg_u = get_preg_u();

	if (! isset($pairs)) {
		$pairs = array();
		$pattern = <<<EOD
\[\[                # open bracket
((?:(?!\]\]).)+)>   # (1) alias name
((?:(?!\]\]).)+)    # (2) alias link
\]\]                # close bracket
EOD;
		$postdata = join('', get_source($aliaspage));
		$matches  = array();
		$count = 0;
		$max   = max($autoalias_max_words, 0);
		if (preg_match_all('/' . $pattern . '/x' . get_preg_u(), $postdata,
			$matches, PREG_SET_ORDER)) {
			foreach($matches as $key => $value) {
				if ($count ==  $max) break;
				$name = trim($value[1]);
				if (! isset($pairs[$name])) {
					++$count;
					 $pairs[$name] = trim($value[2]);
				}
				unset($matches[$key]);
			}
		}
	}
	return $pairs;
}

/**
 * Get propery URI of this script
 *
 * @param $uri_type relative or absolute option
 *        PKWK_URI_RELATIVE, PKWK_URI_ROOT or PKWK_URI_ABSOLUTE
 */
function get_base_uri($uri_type = PKWK_URI_RELATIVE)
{
	$base_type = pkwk_base_uri_type_stack_peek();
	$type = max($base_type, $uri_type);
	switch ($type) {
	case PKWK_URI_RELATIVE:
		return pkwk_script_uri_base(PKWK_URI_RELATIVE);
	case PKWK_URI_ROOT:
		return pkwk_script_uri_base(PKWK_URI_ROOT);
	case PKWK_URI_ABSOLUTE:
		return pkwk_script_uri_base(PKWK_URI_ABSOLUTE);
	default:
		die_message('Invalid uri_type in get_base_uri()');
	}
}

/**
 * Get URI of the page
 *
 * @param page page name
 * @param $uri_type relative or absolute option
 *        PKWK_URI_RELATIVE, PKWK_URI_ROOT or PKWK_URI_ABSOLUTE
 */
function get_page_uri($page, $uri_type = PKWK_URI_RELATIVE)
{
	global $page_uri_handler, $defaultpage;
	if ($page === $defaultpage) {
		return get_base_uri($uri_type);
	}
	return get_base_uri($uri_type) . $page_uri_handler->get_page_uri_virtual_query($page);
}

// Get absolute-URI of this script
function get_script_uri()
{
	return get_base_uri(PKWK_URI_ABSOLUTE);
}

/**
 * Get or initialize Script URI
 *
 * @param $uri_type relative or absolute potion
 *        PKWK_URI_RELATIVE, PKWK_URI_ROOT or PKWK_URI_ABSOLUTE
 * @param $initialize true if you initialize URI
 * @param $uri_set URI set manually
 */
function pkwk_script_uri_base($uri_type, $initialize = null, $uri_set = null)
{
	global $script_directory_index;
	static $initialized = false;
	static $uri_absolute, $uri_root, $uri_relative;
	if (! $initialized) {
		if (isset($initialize) && $initialize) {
			if (isset($uri_set)) {
				$uri_absolute = $uri_set;
			} else {
				$uri_absolute = guess_script_absolute_uri();
			}
			// Support $script_directory_index (cut 'index.php')
			if (isset($script_directory_index)) {
				$slash_index = '/' . $script_directory_index;
				$len = strlen($slash_index);
				if (substr($uri_absolute,  -1 * $len) === $slash_index) {
					$uri_absolute = substr($uri_absolute, 0, strlen($uri_absolute) - $len + 1);
				}
			}
			$elements = parse_url($uri_absolute);
			$uri_root = $elements['path'];
			if (substr($uri_root, -1) === '/') {
				$uri_relative = './';
			} else {
				$pos = mb_strrpos($uri_root, '/');
				if ($pos >= 0) {
					$uri_relative = substr($uri_root, $pos + 1);
				} else {
					$uri_relative = $uri_root;
				}
			}
			$initialized = true;
		} else {
			die_message('Script URI must be initialized in pkwk_script_uri_base()');
		}
	}
	switch ($uri_type) {
	case PKWK_URI_RELATIVE:
		return $uri_relative;
	case PKWK_URI_ROOT:
		return $uri_root;
	case PKWK_URI_ABSOLUTE:
		return $uri_absolute;
	default:
		die_message('Invalid uri_type in pkwk_script_uri_base()');
	}
}

/**
 * Create uri_type context
 *
 * @param $uri_type relative or absolute option
 *        PKWK_URI_RELATIVE, PKWK_URI_ROOT or PKWK_URI_ABSOLUTE
 */
function pkwk_base_uri_type_stack_push($uri_type)
{
	_pkwk_base_uri_type_stack(false, true, $uri_type);
}

/**
 * Stop current active uri_type context
 */
function pkwk_base_uri_type_stack_pop()
{
	_pkwk_base_uri_type_stack(false, false);
}

/**
 * Get current active uri_type status
 */
function pkwk_base_uri_type_stack_peek()
{
	$type = _pkwk_base_uri_type_stack(true, false);
	if (is_null($type)) {
		return PKWK_URI_RELATIVE;
	} elseif ($type === PKWK_URI_ABSOLUTE) {
		return PKWK_URI_ABSOLUTE;
	} elseif ($type === PKWK_URI_ROOT) {
		return PKWK_URI_ROOT;
	} else {
		return PKWK_URI_RELATIVE;
	}
}

/**
 * uri_type context internal function
 *
 * @param $peek is peek action or not
 * @param $push push(true) or pop(false) on not peeking
 * @param $uri_type uri_type on push and non-peeking
 * @return $uri_type uri_type for peeking
 */
function _pkwk_base_uri_type_stack($peek, $push, $uri_type = null)
{
	static $uri_types = array();
	if ($peek) {
		// Peek: get latest value
		if (count($uri_types) === 0) {
			return null;
		} else {
			return $uri_types[0];
		}
	} else {
		if ($push) {
			// Push $uri_type
			if (count($uri_types) === 0) {
				array_unshift($uri_types, $uri_type);
			} else {
				$prev_type = $uri_types[0];
				if ($uri_type >= $prev_type) {
					array_unshift($uri_types, $uri_type);
				} else {
					array_unshift($uri_types, $prev_type);
				}
			}
		} else {
			// Pop $uri_type
			return array_shift($uri_types);
		}
	}
}

/**
 * Guess Script Absolute URI.
 *
 * SERVER_PORT: $_SERVER['SERVER_PORT'] converted in init.php
 * SERVER_NAME: $_SERVER['SERVER_NAME'] converted in init.php
 */
function guess_script_absolute_uri()
{
	$port = SERVER_PORT;
	$is_ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
		(isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https');
	if ($is_ssl) {
		$host = 'https://' . SERVER_NAME .
			($port == 443 ? '' : ':' . $port);
	} else {
		$host = 'http://' . SERVER_NAME .
			($port == 80 ? '' : ':' . $port);
	}
	$uri_elements = parse_url($host . $_SERVER['REQUEST_URI']);
	return $host . $uri_elements['path'];
}

// Remove null(\0) bytes from variables
//
// NOTE: PHP had vulnerabilities that opens "hoge.php" via fopen("hoge.php\0.txt") etc.
// [PHP-users 12736] null byte attack
// http://ns1.php.gr.jp/pipermail/php-users/2003-January/012742.html
//
// 2003-05-16: magic quotes gpcの復元処理を統合
// 2003-05-21: 連想配列のキーはbinary safe
//
function input_filter($param)
{
	static $magic_quotes_gpc = NULL;
	if ($magic_quotes_gpc === NULL) {
		if (function_exists('get_magic_quotes_gpc')) {
			// No 'get_magic_quotes_gpc' function in PHP8
			$magic_quotes_gpc = get_magic_quotes_gpc();
		} else {
			$magic_quotes_gpc = 0;
		}
	}
	if (is_array($param)) {
		return array_map('input_filter', $param);
	} else {
		$result = str_replace("\0", '', $param);
		if ($magic_quotes_gpc) $result = stripslashes($result);
		return $result;
	}
}

// Compat for 3rd party plugins. Remove this later
function sanitize($param) {
	return input_filter($param);
}

// Explode Comma-Separated Values to an array
function csv_explode($separator, $string)
{
	$retval = $matches = array();

	$_separator = preg_quote($separator, '/');
	if (! preg_match_all('/("[^"]*(?:""[^"]*)*"|[^' . $_separator . ']*)' .
	    $_separator . '/', $string . $separator, $matches))
		return array();

	foreach ($matches[1] as $str) {
		$len = strlen($str);
		if ($len > 1 && $str[0] == '"' && $str[$len - 1] == '"')
			$str = str_replace('""', '"', substr($str, 1, -1));
		$retval[] = $str;
	}
	return $retval;
}

// Implode an array with CSV data format (escape double quotes)
function csv_implode($glue, $pieces)
{
	$_glue = ($glue != '') ? '\\' . $glue[0] : '';
	$arr = array();
	foreach ($pieces as $str) {
		if (preg_match('/[' . '"' . "\n\r" . $_glue . ']/', $str))
			$str = '"' . str_replace('"', '""', $str) . '"';
		$arr[] = $str;
	}
	return join($glue, $arr);
}

// Sugar with default settings
function htmlsc($string = '', $flags = ENT_COMPAT, $charset = CONTENT_CHARSET)
{
	return htmlspecialchars($string, $flags, $charset);	// htmlsc()
}

/**
 * Get JSON string with htmlspecialchars().
 */
function htmlsc_json($obj)
{
	// json_encode: PHP 5.2+
	// JSON_UNESCAPED_UNICODE: PHP 5.4+
	// JSON_UNESCAPED_SLASHES: PHP 5.4+
	if (defined('JSON_UNESCAPED_UNICODE')) {
		return htmlsc(json_encode($obj,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}
	return '';
}

/**
 * Get redirect page name on Page Redirect Rules
 *
 * This function returns exactly false if it doesn't need redirection.
 * So callers need check return value is false or not.
 *
 * @param $page page name
 * @return new page name or false
 */
function get_pagename_on_redirect($page) {
	global $page_redirect_rules;
	foreach ($page_redirect_rules as $rule=>$replace) {
		if (preg_match($rule, $page)) {
			if (is_string($replace)) {
				$new_page = preg_replace($rule, $replace, $page);
			} elseif (is_object($replace) && is_callable($replace)) {
				$new_page = preg_replace_callback($rule, $replace, $page);
			} else {
				die_message('Invalid redirect rule: ' . $rule . '=>' . $replace);
			}
			if ($page !== $new_page) {
				return $new_page;
			}
		}
	}
	return false;
}

/**
 * Redirect from an old page to new page
 *
 * This function returns true when a redirection occurs.
 * So callers need check return value is false or true.
 * And if it is true, then you have to exit PHP script.
 *
 * @return bool Inticates a redirection occurred or not
 */
function manage_page_redirect() {
	global $vars;
	if (isset($vars['page'])) {
		$page = $vars['page'];
	}
	$new_page = get_pagename_on_redirect($page);
	if ($new_page != false) {
		header('Location: ' . get_page_uri($new_page, PKWK_URI_ROOT));
		return TRUE;
	}
	return FALSE;
}

/**
 * Return 'u' (PCRE_UTF8) if PHP7+ and UTF-8.
 */
function get_preg_u() {
	static $utf8u; // 'u'(PCRE_UTF8) or ''
	if (! isset($utf8u)) {
		if (version_compare('7.0.0', PHP_VERSION, '<=')
			&& defined('PKWK_UTF8_ENABLE')) {
			$utf8u = 'u';
		} else {
			$utf8u = '';
		}
	}
	return $utf8u;
}

// Default Page name - URI mapping handler
class PukiWikiStandardPageURIHandler {
	function filter_raw_query_string($query_string) {
		return $query_string;
	}

	function get_page_uri_virtual_query($page) {
		return '?' . pagename_urlencode($page);
	}

	function get_page_from_query_string($query_string) {
		$param1st = preg_replace("#^([^&]*)&.*$#", "$1", $query_string);
		if ($param1st == '') {
			return null; // default page
		}
		if (strpos($param1st, '=') !== FALSE) {
			// Found '/?key=value' (Top page with additional query params)
			return null; // default page
		}
		$page = urldecode($param1st);
		$page2 = input_filter($page);
		if ($page !== $page2) {
			return FALSE; // Error page
		}
		return $page2;
	}
}

//// Compat ////

// is_a --  Returns TRUE if the object is of this class or has this class as one of its parents
// (PHP 4 >= 4.2.0)
if (! function_exists('is_a')) {

	function is_a($class, $match)
	{
		if (empty($class)) return FALSE; 

		$class = is_object($class) ? get_class($class) : $class;
		if (strtolower($class) == strtolower($match)) {
			return TRUE;
		} else {
			return is_a(get_parent_class($class), $match);	// Recurse
		}
	}
}

// array_fill -- Fill an array with values
// (PHP 4 >= 4.2.0)
if (! function_exists('array_fill')) {

	function array_fill($start_index, $num, $value)
	{
		$ret = array();
		while ($num-- > 0) $ret[$start_index++] = $value;
		return $ret;
	}
}

// md5_file -- Calculates the md5 hash of a given filename
// (PHP 4 >= 4.2.0)
if (! function_exists('md5_file')) {

	function md5_file($filename)
	{
		if (! file_exists($filename)) return FALSE;

		$fd = fopen($filename, 'rb');
		if ($fd === FALSE ) return FALSE;
		$data = fread($fd, filesize($filename));
		fclose($fd);
		return md5($data);
	}
}

// sha1 -- Compute SHA-1 hash
// (PHP 4 >= 4.3.0, PHP5)
if (! function_exists('sha1')) {
	if (extension_loaded('mhash')) {
		function sha1($str)
		{
			return bin2hex(mhash(MHASH_SHA1, $str));
		}
	}
}
