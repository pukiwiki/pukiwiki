<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: init.php,v 1.20.2.17 2004/06/27 14:34:35 henoheno Exp $
//

/////////////////////////////////////////////////
// 初期設定 (エラー出力レベル)
// (E_WARNING | E_NOTICE)を除外しています。
//error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ALL);

/////////////////////////////////////////////////
// 初期設定 (文字エンコード、言語)
define('LANG','ja');    // Select 'ja' or 'en'

/////////////////////////////////////////////////
// 初期設定(設定ファイルの場所)
define('LANG_FILE', LANG.'.lng');
define("INI_FILE","./pukiwiki.ini.php");

/////////////////////////////////////////////////
// 初期設定 (バージョン/著作権)
define("S_VERSION","1.3.7");
define("S_COPYRIGHT","<strong>\"PukiWiki\" ".S_VERSION."</strong> Copyright &copy; 2001-2004 <a href=\"http://pukiwiki.org\">PukiWiki Developers Team</a>. License is <a href=\"http://www.gnu.org/\">GNU/GPL</a>.<BR>Based on \"PukiWiki\" 1.3 by <a href=\"http://factage.com/sng/\">sng</a>");
define("UTIME",time());

/////////////////////////////////////////////////
// 初期設定 (サーバ変数)
foreach (array('HTTP_USER_AGENT','PHP_SELF','SERVER_NAME','SERVER_SOFTWARE','SERVER_ADMIN') as $key) {
	define($key, array_key_exists($key,$HTTP_SERVER_VARS) ? $HTTP_SERVER_VARS[$key] : '');
}

define("MUTIME",getmicrotime());

$WikiName = '[A-Z][a-z]+(?:[A-Z][a-z]+)+';

//$BracketName = '\[\[(:?[^\s\]#&<>":]+:?)\]\]';
$BracketName = '\[\[(?!\/|\.\/|\.\.\/)(:?[^\s\]#&<>":]+:?)\]\](?<!\/\]\])';

$InterWikiName = "\[\[(\[*[^\s\]]+?\]*):(\[*[^>\]]+?\]*)\]\]";

$LinkPattern = "/( (?# <1>:all)
	(?# url )
	(?:\[\[([^\]]+):)?           (?#<2>:alias)
		(\[)?                      (?#<3>:open bracket)
			((?:https?|ftp|news)(?::\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+)) (?#<4>:url)
		(?(3)\s([^\]]+)\])         (?#<5>:alias, close bracket if <3>)
	(?(2)\]\])                   (?# close bracket if <2>)
	|
	(?# mailto)
	(?:\[\[([^\]]+):)?           (?#<6>alias)
		([\w.-]+@[\w-]+\.[\w.-]+)  (?#<7>:mailto>)
	(?(6)\]\])                   (?# close bracket if <6>)
	|
	(?# BracketName or InterWikiName)
	(\[\[                        (?#<8>:all)
		(?:
			(\[\[)?                  (?#<9>:open bracket)
			([^\[\]]+)               (?#<10>:alias)
			(?:(?:&gt;)|>)           (?# '&gt;' or '>')
		)?
		(?:
			(\[\[)?                  (?#<11>:open bracket)
			(:?[^\s\[\]#&<>\":]*?:?) (?#<12>BracketName)
			((?(9)\]\]|(?(11)\]\])))?(?#<13>:close bracket if <9> or <11>)
			(\#(?:[a-zA-Z][\w-]*)?)? (?#<14>anchor)
			(?(13)|(?(9)\]\]|(?(11)\]\]))) (?#close bracket if <9> or <11> but !<13>)
			|
			(\[\[)?                  (?#<15>:open bracket)
			(\[*?[^\s\]]+?\]*?)      (?#<16>InterWiki)
			((?(9)\]\]|(?(15)\]\])))?(?#<17>:close bracket if <9> or <15>)
			(\:.*?)                  (?#<18>param)
			(?(17)|(?(9)\]\]|(?(15)\]\]))) (?#close bracket if <9> or <15> but !<17>)
		)?
	\]\])
	|
	(?# WikiNmae)
	($WikiName)                  (?#<19>:all)
	)/x";

//** 入力値の整形 **

$cookie = $HTTP_COOKIE_VARS;

$get = $post = array();
if(get_magic_quotes_gpc())
{
	foreach($HTTP_GET_VARS as $key => $value) {
		$get[$key] = stripslashes($HTTP_GET_VARS[$key]);
	}
	foreach($HTTP_POST_VARS as $key => $value) {
		$post[$key] = stripslashes($HTTP_POST_VARS[$key]);
	}
	foreach($HTTP_COOKIE_VARS as $key => $value) {
		$cookie[$key] = stripslashes($HTTP_COOKIE_VARS[$key]);
	}
}
else {
	$post = $HTTP_POST_VARS;
	$get = $HTTP_GET_VARS;
}

// 外部からくる変数をサニタイズ
$get    = sanitize_null_character($get);
$post   = sanitize_null_character($post);
$cookie = sanitize_null_character($cookie);

/////////////////////////////////////////////////
// GET + POST = $vars

$vars = array_merge($post,$get);

// 入力チェック: cmd, plugin の文字列は英数字以外ありえない
foreach(array('cmd', 'plugin') as $var){
	if (array_key_exists($var, $vars) &&
	    ! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $vars[$var])) {
		unset($get[$var], $post[$var], $vars[$var]);
	}
}

// 整形: page, rawurldecode()
if (array_key_exists('page', $vars)) {
	$get['page'] = $post['page'] = $vars['page']  = rawurldecode($vars['page']);
} else {
	$get['page'] = $post['page'] = $vars['page'] = '';
}

// 整形: word, rawurldecode()
if (array_key_exists('word', $vars)) {
	$get['word'] = $post['word'] = $vars['word']  = rawurldecode($vars["word"]);
}

// 整形: msg
if (!empty($vars['msg']))  {
	$get['msg'] = $post['msg'] = $vars['msg'] = preg_replace("/((\x0D\x0A)|(\x0D)|(\x0A))/", "\n", $vars["msg"]);
}

$arg = rawurldecode((getenv('QUERY_STRING') != '') ? getenv('QUERY_STRING') :
		    isset($HTTP_SERVER_VARS['argv'][0]) ? $HTTP_SERVER_VARS['argv'][0] : '');

$arg = sanitize_null_character($arg);

//** 初期処理 **
$update_exec = "";
$content_id = 0;

/////////////////////////////////////////////////
// ファイル読み込み
$die = FALSE; $message = '';
foreach(array('LANG_FILE', 'INI_FILE') as $file){
	if (!file_exists(constant($file)) || !is_readable(constant($file))) {
		$die = TRUE;
		$message = "${message}File is not found. ($file)\n";
	} else {
		require(constant($file));
	}
}
if ($die)
	die_message(nl2br("\n\n" . $message . "\n"));


/////////////////////////////////////////////////
// フェイスマークを$line_rulesに加える
if ($usefacemark)
	$line_rules += $facemark_rules;

$user_rules = array_merge($str_rules,$line_rules);

$note_id = 1;
$foot_explain = array();

// INI_FILE: $script: 初期設定
if (!isset($script) or $script == '') {
	$script = get_script_uri();
	if ($script === FALSE or (php_sapi_name() == 'cgi' and !is_url($script,TRUE))) {
		die_message("get_script_uri() failed: Please set \$script at INI_FILE manually.");
	}
}

/////////////////////////////////////////////////
// ディレクトリのチェック
$die = FALSE; $message = $temp = '';

foreach(array('DATA_DIR', 'DIFF_DIR', 'BACKUP_DIR', 'CACHE_DIR') as $dir){
        if(!is_writable(constant($dir))) {
                $die = TRUE;
                $temp = "${temp}Directory is not found or not writable ($dir)\n";
        }
}
if ($temp)
	$message = "$temp\n";

// 設定ファイルの変数チェック
$temp = '';
foreach(array('rss_max', 'page_title', 'note_hr', 'related_link',
	'show_passage', 'rule_related_str', 'load_template_func') as $var){
        if (!isset(${$var}))
		$temp .= "\$$var\n";
}
if ($temp) {
        $die = TRUE;
        $message = "${message}Variable(s) not found: (Maybe the old *.ini.php?)\n" . $temp . "\n";
}

$temp = '';
foreach(array('LANG', 'PLUGIN_DIR') as $def){
        if (!defined($def))
		$temp .= "$def\n";
}
if ($temp) {
        $die = TRUE;
        $message = "${message}Define(s) not found: (Maybe the old *.ini.php?)\n" . $temp . "\n";
}

if($die)
	die_message(nl2br("\n\n" . $message));


/////////////////////////////////////////////////
// 必須のページが存在しなければ、空のファイルを作成する
foreach(array($defaultpage, $whatsnew, $interwiki) as $page){
	$page = get_filename(encode($page));
	if (!file_exists($page)) {
		touch($page);
	}
}

/////////////////////////////////////////////////
$ins_date = date($date_format,UTIME);
$ins_time = date($time_format,UTIME);
$ins_week = "(".$weeklabels[date("w",UTIME)].")";

$now = "$ins_date $ins_week $ins_time";

?>
