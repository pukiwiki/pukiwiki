<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: init.php,v 1.22 2003/01/27 05:38:44 panda Exp $
//

/////////////////////////////////////////////////
// エラーハンドラを設定
ini_set('error_reporting', E_ALL);
$old_error_handler = set_error_handler('myErrorHandler');

/////////////////////////////////////////////////
// 初期設定 (for EUC-JP)
define('ENCODING','EUC-JP');
define('LANG','ja');
mb_http_output(ENCODING);
mb_internal_encoding(ENCODING);

/////////////////////////////////////////////////
// 初期設定 (for UTF-8)
/*
define('ENCODING','UTF-8');
// HTTP_ACCEPT_LANGUAGEからスキンを決定する(experimental)
$lang = 'en'; // デフォルト
$support = array('ja','en');
if (preg_match_all('/([a-z]{2})(?:;q=([0-9.]+))?/',$_SERVER['HTTP_ACCEPT_LANGUAGE'],$matches,PREG_SET_ORDER)) {
	$matches[0][2] = 1;
	$q = 0;
	foreach ($matches as $arr) {
		if ((array_search($arr[1],$support) !== FALSE) and ($arr[2] > $q)) { //supported and qualify is greater
			$lang = $arr[1];
			$q = $arr[2];
		}
	}
}
define('LANG',$lang);
mb_http_output(ENCODING);
mb_internal_encoding(ENCODING);
*/
/////////////////////////////////////////////////
// 初期設定 (バージョン/著作権)
define('S_VERSION','1.4pre2');
define('S_COPYRIGHT','
<strong>"PukiWiki" '.S_VERSION.'</strong> Copyright &copy; 2001,2002
<a href="http://pukiwiki.org">PukiWiki Developers Team</a>.
License is <a href="http://www.gnu.org/">GNU/GPL</a>.<br />
Based on "PukiWiki" 1.3 by <a href="http://factage.com/sng/">sng</a>
');

/////////////////////////////////////////////////
// 初期設定 (サーバ変数)
foreach (array('HTTP_USER_AGENT','PHP_SELF','SERVER_NAME','SERVER_SOFTWARE','SERVER_ADMIN') as $key) {
	define($key,array_key_exists($key,$HTTP_SERVER_VARS) ? $HTTP_SERVER_VARS[$key] : '');
}

/////////////////////////////////////////////////
// 初期設定(時間)
define('LOCALZONE',date('Z'));
define('UTIME',time() - LOCALZONE);
define('MUTIME',getmicrotime());

/////////////////////////////////////////////////
// 言語ファイル読み込み
if (!file_exists(LANG.'.lng')||!is_readable(LANG.'.lng')) {
	die_message(LANG.'.lng(language file) is not found.');
}
require(LANG.'.lng');

/////////////////////////////////////////////////
// 初期設定(設定ファイルの場所)
define('INI_FILE','./pukiwiki.ini.php');

/////////////////////////////////////////////////
// 設定ファイル読み込み
if (!file_exists(INI_FILE)||!is_readable(INI_FILE)) {
	die_message(INI_FILE.' is not found.');
}
require(INI_FILE);

/////////////////////////////////////////////////
// 設定ファイルの変数チェック
if(!is_writable(DATA_DIR)) {
	die_message('DATA_DIR is not found or not writable.');
}
if(!is_writable(DIFF_DIR)) {
	die_message('DIFF_DIR is not found or not writable.');
}
if($do_backup && !is_writable(BACKUP_DIR)) {
	die_message('BACKUP_DIR is not found or not writable.');
}
$wrong_ini_file = '';
if (!isset($rss_max)) $wrong_ini_file .= '$rss_max ';
if (!isset($page_title)) $wrong_ini_file .= '$page_title ';
if (!isset($note_hr)) $wrong_ini_file .= '$note_hr ';
if (!isset($related_link)) $wrong_ini_file .= '$related_link ';
if (!isset($show_passage)) $wrong_ini_file .= '$show_passage ';
if (!isset($rule_related_str)) $wrong_ini_file .= '$rule_related_str ';
if (!isset($load_template_func)) $wrong_ini_file .= '$load_template_func ';
if (!defined('LANG')) $wrong_ini_file .= 'LANG ';
if (!defined('PLUGIN_DIR')) $wrong_ini_file .= 'PLUGIN_DIR ';
if ($wrong_ini_file) {
	die_message('The setting file runs short of information.<br>The version of a setting file may be old.<br><br>These option are not found : '.$wrong_ini_file);
}
if (!is_page($defaultpage)) {
	touch(get_filename($defaultpage));
}
if (!is_page($whatsnew)) {
	touch(get_filename($whatsnew));
}
if (!is_page($interwiki)) {
	touch(get_filename($interwiki));
}

/////////////////////////////////////////////////
// 初期設定($script)
if (!isset($script) or $script == '') {
	$script =
		 ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://')
		. $_SERVER['SERVER_NAME']
		.($_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT'])
		. $_SERVER['SCRIPT_NAME'];
}
if (php_sapi_name() == 'cgi' && !preg_match("/^http:\/\/[-a-zA-Z0-9\@:;_.]+\//",$script)) {
	die_message("please set '\$script' in ".INI_FILE);
}

/////////////////////////////////////////////////
// 入力値の整形
$cookie = $HTTP_COOKIE_VARS;
if (get_magic_quotes_gpc()) {
	$get = $post = $cookie = array();
	foreach($HTTP_GET_VARS as $key => $value) {
		if (!is_array($value)) {
			$get[$key] = stripslashes($value);
		}
	}
	foreach($HTTP_POST_VARS as $key => $value) {
		$post[$key] = stripslashes($value);
	}
	foreach($HTTP_COOKIE_VARS as $key => $value) {
		$cookie[$key] = stripslashes($value);
	}
}
else {
	$post = is_array($HTTP_POST_VARS) ? $HTTP_POST_VARS : array();
	$get = is_array($HTTP_GET_VARS) ? $HTTP_GET_VARS : array();
}
if (!empty($get['page'])) {
	$get['page']  = preg_replace('/^(\[\[)?(.*)(?(1)\]\])$/','$2',$get['page']);
}
if (!empty($post['page'])) {
	$post['page'] = preg_replace('/^(\[\[)?(.*)(?(1)\]\])$/','$2',$post['page']);
}
if (!empty($post['msg'])) {
	$post['msg']  = preg_replace("/\r/",'',$post['msg']);
}
/*
if (!empty($post['word'])) {
	$post['word'] = $post['word'];
}
if (!empty($get['word'])) {
	$get['word']  = $get['word'];
}
*/

@$vars = array_merge($post,$get);
if (!array_key_exists('page',$vars)) {
	$get['page'] = $post['page'] = $vars['page'] = '';
}

$arg = rawurldecode((getenv('QUERY_STRING') != '') ? getenv('QUERY_STRING') :
	(array_key_exists(0,$HTTP_SERVER_VARS['argv']) ? $HTTP_SERVER_VARS['argv'][0] : ''));

/////////////////////////////////////////////////
// 初期設定($WikiName,$BracketNameなど)
// $WikiName = '[A-Z][a-z]+(?:[A-Z][a-z]+)+';
// $WikiName = '\b[A-Z][a-z]+(?:[A-Z][a-z]+)+\b';
$WikiName = '(?<![[:alnum:]])(?:[[:upper:]][[:lower:]]+){2,}(?![[:alnum:]])';
// $BracketName = ':?[^\s\]#&<>":]+:?';
$BracketName = '(?!\s):?[^\r\n\t\f\[\]<>#&":]+:?(?<!\s)';
// InterWiki
$InterWikiName = "(\[\[)?(\[*[^\s\]]+?\]*):(\[*[^>\]]+?\]*)(?(1)\]\])";
// 注釈
$NotePattern = '/\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)/ex';
// AutoLink
$AutoLink = get_autolink_pattern();

// make_link()関数と密接に関係するので、編集時は注意
$LinkPattern = "/
(                              (?#<1>:all)
	(?# url )
	(?:\[\[([^\]]+)              (?#<2>:alias)
	(?:&gt;|>|:))?               (?# '&gt;' or '>' or ':')
		(\[)?                      (?#<3>:open bracket)
			(                        (?#<4>:url)
				(?:https?|ftp|news)
				(?::\/\/[!~*'();\/?:\@&=+\$,%#\w.-]+)
			)
		(?(3)\s([^\]]+)\])         (?#<5>:alias, close bracket if <3>)
	(?(2)\]\])                   (?# close bracket if <2>)
	|
	(?# mailto)
	(?:\[\[([^\]]+)(?:&gt;|>|:))?(?#<6>:alias)
		([\w.-]+@[\w-]+\.[\w.-]+)  (?#<7>:mailto>)
	(?(6)\]\])                   (?# close bracket if <6>)
	|
	(?# BracketName or InterWikiName)
	(\[\[                        (?#<8>:all)
		(?:
			(\[\[)?                  (?#<9>:open bracket)
			([^\[\]]+)               (?#<10>:alias)
			(?:&gt;|>)               (?# '&gt;' or '>')
		)?
		(?:
			(\[\[)?                  (?#<11>:open bracket)
			(                        (?#<12>PageName)
				($WikiName)            (?#<13>WikiName)
				|
				($BracketName)         (?#<14>BracketName)
			)?
			((?(9)\]\]|(?(11)\]\])))?(?#<15>:close bracket if <9> or <11>) 
			(\#(?:[a-zA-Z][\w-]*)?)? (?#<16>anchor)
			(?(15)|(?(9)\]\]|(?(11)\]\]))) (?#close bracket if <9> or <11> but !<15>)
			|
			(\[\[)?                  (?#<17>:open bracket)
			(\[*?[^\s\]]+?\]*?)      (?#<18>InterWiki)
			((?(9)\]\]|(?(17)\]\])))?(?#<19>:close bracket if <9> or <17>)
			(\:.*?)                  (?#<20>param)
			(?(19)|(?(9)\]\]|(?(17)\]\]))) (?#close bracket if <9> or <17> but !<19>)
		)?
	\]\])
	|
	(?# WikiNmae, AutoLink)
	($AutoLink)                  (?#<21>:all)
)/x";

/////////////////////////////////////////////////
// 初期設定(その他のグローバル変数)
// 現在時刻
$now = format_date(UTIME);
// 脚注
$foot_explain = array();
// 関連するページ
$related = array();
// skin内でDTD宣言を切り替えるのに使用。paint.inc.php対策
// FALSE:XHTML 1.1
// TRUE :XHTML 1.0 Transitional
$html_transitional = FALSE;
// フェイスマークを$line_rulesに加える
if ($usefacemark) {
	$line_rules = array_merge($line_rules,$facemark_rules);
}
// ユーザ定義ルール
$user_rules = array_merge($str_rules,$line_rules);

/////////////////////////////////////////////////
// 初期設定(URL変数の処理)
// 後方互換性 (?md5=...)
if (array_key_exists('md5',$vars) and $vars['md5'] != '') {
	$vars['cmd'] = 'md5';
}

// cmdもpluginも指定されていない場合は、$argをページ名かInterWikiNameであるとみなす
if (!array_key_exists('cmd',$vars)  and !array_key_exists('plugin',$vars)) {
	//$argも指定されていなかった場合は$defaultpageを表示
	if ($arg == '') {
		$arg = $defaultpage;
	}
	$get['cmd'] = $post['cmd'] = $vars['cmd'] = 'read';
	$get['page'] = $post['page'] = $vars['page'] = preg_replace('/^(\[\[)?(.*)(?(1)\]\])$/','$2',$arg);
}
?>
