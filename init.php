<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: init.php,v 1.69 2003/11/22 04:51:28 arino Exp $
//

/////////////////////////////////////////////////
// 初期設定 (エラー出力レベル)
// (E_WARNING | E_NOTICE)を除外しています。
error_reporting(E_ERROR | E_PARSE);

/////////////////////////////////////////////////
// 初期設定 (文字エンコード、言語)
define('SOURCE_ENCODING','EUC-JP');
define('LANG','ja');
mb_internal_encoding(SOURCE_ENCODING);
mb_http_output(SOURCE_ENCODING);

/////////////////////////////////////////////////
// 初期設定(設定ファイルの場所)
define('INI_FILE','./pukiwiki.ini.php');

/////////////////////////////////////////////////
// 初期設定 (バージョン/著作権)
define('S_VERSION','1.4.2');
define('S_COPYRIGHT','
<strong>"PukiWiki" '.S_VERSION.'</strong> Copyright &copy; 2001,2002,2003
<a href="http://pukiwiki.org">PukiWiki Developers Team</a>.
License is <a href="http://www.gnu.org/">GNU/GPL</a>.<br />
Based on "PukiWiki" 1.3 by <a href="http://factage.com/sng/">sng</a>
');

/////////////////////////////////////////////////
// 初期設定 (サーバ変数)
foreach (array('HTTP_USER_AGENT','PHP_SELF','SERVER_NAME','SERVER_SOFTWARE','SERVER_ADMIN') as $key) {
	define($key,array_key_exists($key,$_SERVER) ? $_SERVER[$key] : '');
}

/////////////////////////////////////////////////
// 初期設定 (グローバル変数)
// サーバから来る変数
$vars = array();
// 脚注
$foot_explain = array();
// 関連するページ
$related = array();
// <head>内に追加するタグ
$head_tags = array();

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
// 設定ファイル読み込み
if (!file_exists(INI_FILE)||!is_readable(INI_FILE)) {
	die_message(INI_FILE.' is not found.');
}
require(INI_FILE);

/////////////////////////////////////////////////
// 初期設定($script)
if (!isset($script) or $script == '') {
	$script = get_script_uri();
}
if ($script === FALSE or (php_sapi_name() == 'cgi' and !is_url($script,TRUE)))
{
	die_message('please set "$script" in pukiwiki.ini.php.');
}

/////////////////////////////////////////////////
// 設定ファイル読み込み(UserAgent)
foreach ($agents as $agent) {
	if (preg_match($agent['pattern'],HTTP_USER_AGENT,$matches)) {
		$agent['matches'] = $matches;
		$user_agent = $agent;
		break;
	}
}
define('UA_INI_FILE',$user_agent['name'].'.ini.php');

if (!file_exists(UA_INI_FILE)||!is_readable(UA_INI_FILE)) {
	die_message(UA_INI_FILE.' is not found.');
}
require(UA_INI_FILE);

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
if(!is_writable(CACHE_DIR)) {
	die_message('CACHE_DIR is not found or not writable.');
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
	die_message('The setting file runs short of information.<br />The version of a setting file may be old.<br /><br />These option are not found : '.$wrong_ini_file);
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
// 外部からくる変数をサニタイズ
$get    = sanitize($_GET);
$post   = sanitize($_POST);
$cookie = sanitize($_COOKIE);

/////////////////////////////////////////////////
// 文字コードを変換

// <form> で送信された文字 (ブラウザがエンコードしたデータ) のコードを変換
// post は常に <form> なので、必ず変換
if (array_key_exists('encode_hint',$post))
{
	// html.php の中で、<form> に encode_hint を仕込んでいるので、必ず encode_hint があるはず。
	// encode_hint のみを用いてコード検出する。
	// 全体を見てコード検出すると、機種依存文字や、妙なバイナリコードが混入した場合に、
	// コード検出に失敗する恐れがあるため。
	$encode = mb_detect_encoding($post['encode_hint']);
	mb_convert_variables(SOURCE_ENCODING,$encode,$post);
}
else if (array_key_exists('charset',$post))
{
	// TrackBack Pingに含まれていることがある
	// 指定された場合は、その内容で変換を試みる
	if (mb_convert_variables(SOURCE_ENCODING,$post['charset'],$post) !== $post['charset'])
	{
		// うまくいかなかった場合はコード検出の設定で変換しなおし
		mb_convert_variables(SOURCE_ENCODING,'auto',$post);
	}
}
else if (count($post) > 0)
{
	// encode_hint が無いということは、無いはず。
	// デバッグ用に、取りあえず、警告メッセージを出しておきます。
// 	echo "<p>Warning: 'encode_hint' field is not found in the posted data.</p>\n";
	// 全部まとめて、コード検出、変換
	mb_convert_variables(SOURCE_ENCODING,'auto',$post);
}

// get は <form> からの場合と、<a href="http;//script/?query> の場合がある
if (array_key_exists('encode_hint',$get))
{
	// <form> の場合は、ブラウザがエンコードしているので、コード検出・変換が必要。
	// encode_hint が含まれているはずなので、それを見て、コード検出した後、変換する。
	// 理由は、post と同様
	$encode = mb_detect_encoding($get['encode_hint']);
	mb_convert_variables(SOURCE_ENCODING,$encode,$get);
}	
// <a href...> の場合は、サーバーが rawurlencode しているので、コード変換は不要

// QUERY_STRINGを取得
// cmdもpluginも指定されていない場合は、QUERY_STRINGをページ名かInterWikiNameであるとみなす為
// また、URI を urlencode せずに手打ちで入力した場合に対処する為
$arg = '';
if ($_SERVER['QUERY_STRING'] != '')
{
	$arg = $_SERVER['QUERY_STRING'];
}
else if (array_key_exists(0,$_SERVER['argv']))
{
	$arg = $_SERVER['argv'][0];
}

// サニタイズ (\0 除去)
$arg = sanitize($arg);

// URI 手打の場合、コード変換し、get[] に上書き
// mb_convert_variablesのバグ(?)対策 配列で渡さないと落ちる
$arg = array($arg);
mb_convert_variables(SOURCE_ENCODING,'auto',$arg);
$arg = $arg[0];

foreach (explode('&',$arg) as $tmp_string)
{
	if (preg_match('/^([^=]+)=(.+)/',$tmp_string,$matches)
		and mb_detect_encoding($matches[2]) != 'ASCII')
	{
		$get[$matches[1]] = $matches[2];
	}
}

if (!empty($get['page']))
{
	$get['page']  = strip_bracket($get['page']);
}
if (!empty($post['page']))
{
	$post['page'] = strip_bracket($post['page']);
}
if (!empty($post['msg']))
{
	$post['msg']  = str_replace("\r",'',$post['msg']);
}

$vars = array_merge($post,$get);
if (!array_key_exists('page',$vars))
{
	$get['page'] = $post['page'] = $vars['page'] = '';
}

// 後方互換性 (?md5=...)
if (array_key_exists('md5',$vars) and $vars['md5'] != '')
{
	$vars['cmd'] = 'md5';
}

// cmdもpluginも指定されていない場合は、QUERY_STRINGをページ名かInterWikiNameであるとみなす
if (!array_key_exists('cmd',$vars)  and !array_key_exists('plugin',$vars))
{
	if ($arg == '')
	{
		//なにも指定されていなかった場合は$defaultpageを表示
		$arg = $defaultpage;
	}		
	$arg = rawurldecode($arg);
	$arg = strip_bracket($arg);
	$arg = sanitize($arg);

	$get['cmd'] = $post['cmd'] = $vars['cmd'] = 'read';
	$get['page'] = $post['page'] = $vars['page'] = $arg;
}

/////////////////////////////////////////////////
// 初期設定($WikiName,$BracketNameなど)
// $WikiName = '[A-Z][a-z]+(?:[A-Z][a-z]+)+';
// $WikiName = '\b[A-Z][a-z]+(?:[A-Z][a-z]+)+\b';
// $WikiName = '(?<![[:alnum:]])(?:[[:upper:]][[:lower:]]+){2,}(?![[:alnum:]])';
// $WikiName = '(?<!\w)(?:[A-Z][a-z]+){2,}(?!\w)';
// BugTrack/304暫定対処
$WikiName = '(?:[A-Z][a-z]+){2,}(?!\w)';
// $BracketName = ':?[^\s\]#&<>":]+:?';
$BracketName = '(?!\s):?[^\r\n\t\f\[\]<>#&":]+:?(?<!\s)';
// InterWiki
$InterWikiName = "(\[\[)?((?:(?!\s|:|\]\]).)+):(.+)(?(1)\]\])";
// 注釈
$NotePattern = '/\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)/ex';

/////////////////////////////////////////////////
// 初期設定(ユーザ定義ルール読み込み)
require('rules.ini.php');

/////////////////////////////////////////////////
// 初期設定(その他のグローバル変数)
// 現在時刻
$now = format_date(UTIME);
// skin内でDTD宣言を切り替えるのに使用。paint.inc.php対策
// FALSE:XHTML 1.1
// TRUE :XHTML 1.0 Transitional
$html_transitional = FALSE;
// フェイスマークを$line_rulesに加える
if ($usefacemark)
{
	$line_rules += $facemark_rules;
}
unset($facemark_rules);
// 実体参照パターンおよびシステムで使用するパターンを$line_rulesに加える
//$entity_pattern = '[a-zA-Z0-9]{2,8}';
$entity_pattern = trim(join('',file(CACHE_DIR.'entities.dat')));
$line_rules = array_merge(array(
	'&amp;(#[0-9]+|#x[0-9a-f]+|'.$entity_pattern.');'=>'&$1;',
	"\r"=>"<br />\n", /* 行末にチルダは改行 */
	'#related$'=>'<del>#related</del>',
	'^#contents$'=>'<del>#contents</del>'
),$line_rules);
?>
