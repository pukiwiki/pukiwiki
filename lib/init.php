<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// init.php
// Copyright
//   2002-2022 PukiWiki Development Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Init PukiWiki here

// PukiWiki version / Copyright / Licence

define('S_VERSION', '1.5.4');
define('S_COPYRIGHT',
	'<strong>PukiWiki '
	. S_VERSION
	. '</strong>'
	. ' &copy; 2001-2022'
	. ' <a href="https://pukiwiki.osdn.jp/">PukiWiki Development Team</a>'
);

/////////////////////////////////////////////////
// Session security options

ini_set('session.use_strict_mode', 1);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);

/////////////////////////////////////////////////
// Init server variables

// Comapat and suppress notices
if (!isset($HTTP_SERVER_VARS)) $HTTP_SERVER_VARS = array();

foreach (array('SCRIPT_NAME', 'SERVER_ADMIN', 'SERVER_NAME',
	'SERVER_PORT', 'SERVER_SOFTWARE') as $key) {
	define($key, isset($_SERVER[$key]) ? $_SERVER[$key] : '');
	unset(${$key}, $_SERVER[$key], $HTTP_SERVER_VARS[$key]);
}

/////////////////////////////////////////////////
// Init grobal variables

$foot_explain = array();	// Footnotes
$related      = array();	// Related pages
$head_tags    = array();	// XHTML tags in <head></head>

/////////////////////////////////////////////////
// Time settings

define('LOCALZONE', date('Z'));
define('UTIME', time() - LOCALZONE);
define('MUTIME', getmicrotime());

/////////////////////////////////////////////////
// Require INI_FILE

define('INI_FILE',  DATA_HOME . 'pukiwiki.ini.php');
$die = '';
if (! file_exists(INI_FILE) || ! is_readable(INI_FILE)) {
	$die .= 'File is not found. (INI_FILE)' . "\n";
} else {
	require(INI_FILE);
}
if ($die) die_message(nl2br("\n\n" . $die));

/////////////////////////////////////////////////
// Page-URI mapping handler (default)
if (! $page_uri_handler) {
	$page_uri_handler = new PukiWikiStandardPageURIHandler();
}

/////////////////////////////////////////////////
// INI_FILE: LANG に基づくエンコーディング設定

// MB_LANGUAGE: mb_language (for mbstring extension)
//   'uni'(means UTF-8), 'English', or 'Japanese'
// SOURCE_ENCODING: Internal content encoding (for mbstring extension)
//   'UTF-8', 'ASCII', or 'EUC-JP'
// CONTENT_CHARSET: Internal content encoding = Output content charset
//    (for DTD, htmlsc())
//   'UTF-8', 'iso-8859-1', 'EUC-JP' or ...

switch (LANG){
case 'en': define('MB_LANGUAGE', 'English' ); break;
case 'ja': define('MB_LANGUAGE', 'Japanese'); break;
case 'ko': define('MB_LANGUAGE', 'Korean'  ); break; //UTF-8 only
	// See BugTrack2/13 for all hack about Korean support, //UTF-8 only
	// and give us your report!                            //UTF-8 only
default: die_message('No such language "' . LANG . '"'); break;
}

define('PKWK_UTF8_ENABLE', 1); //UTF-8 only
if (defined('PKWK_UTF8_ENABLE')) {
	define('SOURCE_ENCODING', 'UTF-8');
	define('CONTENT_CHARSET', 'UTF-8');
} else {
	switch (LANG){
	case 'en':
		define('SOURCE_ENCODING', 'ASCII');
		define('CONTENT_CHARSET', 'iso-8859-1');
		break;
	case 'ja':
		define('SOURCE_ENCODING', 'EUC-JP');
		define('CONTENT_CHARSET', 'EUC-JP');
		break;
	}
}

mb_language(MB_LANGUAGE);
mb_internal_encoding(SOURCE_ENCODING);
mb_http_output('pass');
mb_detect_order('auto');

/////////////////////////////////////////////////
// INI_FILE: Require LANG_FILE

define('LANG_FILE_HINT', DATA_HOME . LANG . '.lng.php');	// For encoding hint
define('LANG_FILE',      DATA_HOME . UI_LANG . '.lng.php');	// For UI resource
$die = '';
foreach (array('LANG_FILE_HINT', 'LANG_FILE') as $langfile) {
	if (! file_exists(constant($langfile)) || ! is_readable(constant($langfile))) {
		$die .= 'File is not found or not readable. (' . $langfile . ')' . "\n";
	} else {
		require_once(constant($langfile));
	}
}
if ($die) die_message(nl2br("\n\n" . $die));

/////////////////////////////////////////////////
// LANG_FILE: Init encoding hint

define('PKWK_ENCODING_HINT', isset($_LANG['encode_hint'][LANG]) ? $_LANG['encode_hint'][LANG] : '');
unset($_LANG['encode_hint']);

/////////////////////////////////////////////////
// LANG_FILE: Init severn days of the week

$weeklabels = $_msg_week;

/////////////////////////////////////////////////
// INI_FILE: Init $script

if (isset($script)) {
	// Init manually
	pkwk_script_uri_base(PKWK_URI_ABSOLUTE, true, $script);
} else {
	// Init automatically
	$script = pkwk_script_uri_base(PKWK_URI_ABSOLUTE, true);
}

// INI_FILE: Auth settings
if (isset($auth_type) && $auth_type === AUTH_TYPE_SAML) {
	$auth_external_login_url_base = get_base_uri() . '?//cmd.saml//sso';
}


/////////////////////////////////////////////////
// INI_FILE: $agents:  UserAgentの識別

$ua = 'HTTP_USER_AGENT';
$user_agent = $matches = array();

$user_agent['agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

foreach ($agents as $agent) {
	if (preg_match($agent['pattern'], $user_agent['agent'], $matches)) {
		$user_agent['profile'] = isset($agent['profile']) ? $agent['profile'] : '';
		$user_agent['name']    = isset($matches[1]) ? $matches[1] : '';	// device or browser name
		$user_agent['vers']    = isset($matches[2]) ? $matches[2] : ''; // 's version
		break;
	}
}
unset($agents, $matches);

// Profile-related init and setting
define('UA_PROFILE', isset($user_agent['profile']) ? $user_agent['profile'] : '');

define('UA_INI_FILE', DATA_HOME . UA_PROFILE . '.ini.php');
if (! file_exists(UA_INI_FILE) || ! is_readable(UA_INI_FILE)) {
	die_message('UA_INI_FILE for "' . UA_PROFILE . '" not found.');
} else {
	require(UA_INI_FILE); // Also manually
}

define('UA_NAME', isset($user_agent['name']) ? $user_agent['name'] : '');
define('UA_VERS', isset($user_agent['vers']) ? $user_agent['vers'] : '');
unset($user_agent);	// Unset after reading UA_INI_FILE

/////////////////////////////////////////////////
// ディレクトリのチェック

$die = '';
foreach(array('DATA_DIR', 'DIFF_DIR', 'BACKUP_DIR', 'CACHE_DIR') as $dir){
	if (! is_writable(constant($dir)))
		$die .= 'Directory is not found or not writable (' . $dir . ')' . "\n";
}

// 設定ファイルの変数チェック
$temp = '';
foreach(array('rss_max', 'page_title', 'note_hr', 'related_link', 'show_passage',
	'rule_related_str', 'load_template_func') as $var){
	if (! isset(${$var})) $temp .= '$' . $var . "\n";
}
if ($temp) {
	if ($die) $die .= "\n";	// A breath
	$die .= 'Variable(s) not found: (Maybe the old *.ini.php?)' . "\n" . $temp;
}

$temp = '';
foreach(array('LANG', 'PLUGIN_DIR') as $def){
	if (! defined($def)) $temp .= $def . "\n";
}
if ($temp) {
	if ($die) $die .= "\n";	// A breath
	$die .= 'Define(s) not found: (Maybe the old *.ini.php?)' . "\n" . $temp;
}

if($die) die_message(nl2br("\n\n" . $die));
unset($die, $temp);

/////////////////////////////////////////////////
// 必須のページが存在しなければ、空のファイルを作成する

foreach(array($defaultpage, $whatsnew, $interwiki) as $page){
	if (! is_page($page)) pkwk_touch_file(get_filename($page));
}

/////////////////////////////////////////////////
// 外部からくる変数のチェック

// Prohibit $_GET attack
foreach (array('msg', 'pass') as $key) {
	if (isset($_GET[$key])) die_message('Sorry, already reserved: ' . $key . '=');
}

// Expire risk
unset($HTTP_GET_VARS, $HTTP_POST_VARS);	//, 'SERVER', 'ENV', 'SESSION', ...
unset($_REQUEST);	// Considered harmful

// Remove null character etc.
$_GET    = input_filter($_GET);
$_POST   = input_filter($_POST);
$_COOKIE = input_filter($_COOKIE);

// 文字コード変換 ($_POST)
// <form> で送信された文字 (ブラウザがエンコードしたデータ) のコードを変換
// POST method は常に form 経由なので、必ず変換する
//
if (isset($_POST['encode_hint']) && $_POST['encode_hint'] != '') {
	// do_plugin_xxx() の中で、<form> に encode_hint を仕込んでいるので、
	// encode_hint を用いてコード検出する。
	// 全体を見てコード検出すると、機種依存文字や、妙なバイナリ
	// コードが混入した場合に、コード検出に失敗する恐れがある。
	$encode = mb_detect_encoding($_POST['encode_hint']);
	mb_convert_variables(SOURCE_ENCODING, $encode, $_POST);

} else if (isset($_POST['charset']) && $_POST['charset'] != '') {
	// TrackBack Ping で指定されていることがある
	// うまくいかない場合は自動検出に切り替え
	if (mb_convert_variables(SOURCE_ENCODING,
	    $_POST['charset'], $_POST) !== $_POST['charset']) {
		mb_convert_variables(SOURCE_ENCODING, 'auto', $_POST);
	}

} else if (! empty($_POST)) {
	// 全部まとめて、自動検出／変換
	mb_convert_variables(SOURCE_ENCODING, 'auto', $_POST);
}

// 文字コード変換 ($_GET)
// GET method は form からの場合と、<a href="http://script/?key=value> の場合がある
// <a href...> の場合は、サーバーが rawurlencode しているので、コード変換は不要
if (isset($_GET['encode_hint']) && $_GET['encode_hint'] != '')
{
	// form 経由の場合は、ブラウザがエンコードしているので、コード検出・変換が必要。
	// encode_hint が含まれているはずなので、それを見て、コード検出した後、変換する。
	// 理由は、post と同様
	$encode = mb_detect_encoding($_GET['encode_hint']);
	mb_convert_variables(SOURCE_ENCODING, $encode, $_GET);
}


/////////////////////////////////////////////////
// QUERY_STRINGを取得

// cmdもpluginも指定されていない場合は、QUERY_STRINGを
// ページ名かInterWikiNameであるとみなす
$arg = '';
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '') {
	global $g_query_string;
	$g_query_string = $_SERVER['QUERY_STRING'];
	$arg = & $_SERVER['QUERY_STRING'];
} else if (isset($_SERVER['argv']) && ! empty($_SERVER['argv'])) {
	$arg = & $_SERVER['argv'][0];
}
if (PKWK_QUERY_STRING_MAX && strlen($arg) > PKWK_QUERY_STRING_MAX) {
	// Something nasty attack?
	pkwk_common_headers();
	sleep(1);	// Fake processing, and/or process other threads
	echo('Query string too long');
	exit;
}
$arg = input_filter($arg); // \0 除去

// Convert QueryString by PageURIHandler
$arg_replaced = $page_uri_handler->filter_raw_query_string($arg);
if ($arg_replaced !== $arg) {
	$_GET = array();
	$m = array();
	foreach (explode('&', $arg_replaced) as $kv) {
		if (preg_match('/^([^=]+)(=(.+))?/', $kv, $m)) {
			$_GET[$m[1]] = is_null($m[3]) ? '' : $m[3];
		}
	}
	unset($m);
	$arg = $arg_replaced;
}
unset($arg_replaced);

// unset QUERY_STRINGs
foreach (array('QUERY_STRING', 'argv', 'argc') as $key) {
	unset(${$key}, $_SERVER[$key], $HTTP_SERVER_VARS[$key]);
}
// $_SERVER['REQUEST_URI'] is used at func.php NOW
unset($REQUEST_URI, $HTTP_SERVER_VARS['REQUEST_URI']);

// mb_convert_variablesのバグ(?)対策: 配列で渡さないと落ちる
$arg = array($arg);
mb_convert_variables(SOURCE_ENCODING, 'auto', $arg);
$arg = $arg[0];

/////////////////////////////////////////////////
// QUERY_STRINGを分解してコード変換し、$_GET に上書き

// URI を urlencode せずに入力した場合に対処する
$matches = array();
foreach (explode('&', $arg) as $key_and_value) {
	if (preg_match('/^([^=]+)=(.+)/', $key_and_value, $matches) &&
	    mb_detect_encoding($matches[2]) != 'ASCII') {
		$_GET[$matches[1]] = $matches[2];
	}
}
unset($matches);

/////////////////////////////////////////////////
// GET, POST, COOKIE

$get    = & $_GET;
$post   = & $_POST;
$cookie = & $_COOKIE;

// GET + POST = $vars
if (empty($_POST)) {
	$vars = & $_GET;  // Major pattern: Read-only access via GET
} else if (empty($_GET)) {
	$vars = & $_POST; // Minor pattern: Write access via POST etc.
} else {
	$vars = array_merge($_GET, $_POST); // Considered reliable than $_REQUEST
}

/**
 * Parse specified format query_string as params.
 *
 * For example: ?//key1.value2//key2.value2
 */
function parse_query_string_ext($query_string) {
	$vars = array();
	$m = null;
	if (preg_match('#^//[^&]*#', $query_string, $m)) {
		foreach (explode('//', $m[0]) as $item) {
			$sp = explode('.', $item, 2);
			if (isset($sp[0])) {
				if (isset($sp[1])) {
					$vars[$sp[0]] = $sp[1];
				} else {
					$vars[$sp[0]] = '';
				}
			}
		}
	}
	return $vars;
}
if (isset($g_query_string) && $g_query_string) {
	if (substr($g_query_string, 0, 2) === '//') {
		// Parse ?//key.value//key.value format query string
		$vars = array_merge($vars, parse_query_string_ext($g_query_string));
	}
}


// 入力チェック: 'cmd=' and 'plugin=' can't live together
if (isset($vars['cmd']) && isset($vars['plugin']))
	die('Using both cmd= and plugin= is not allowed');

// 入力チェック: cmd, plugin の文字列は英数字以外ありえない
foreach(array('cmd', 'plugin') as $var) {
	if (isset($vars[$var]) && ! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $vars[$var]))
		unset($get[$var], $post[$var], $vars[$var]);
}

// 整形: page, strip_bracket()
if (isset($vars['page'])) {
	$get['page'] = $post['page'] = $vars['page']  = strip_bracket($vars['page']);
} else {
	$get['page'] = $post['page'] = $vars['page'] = '';
}

// 整形: msg, 改行を取り除く
if (isset($vars['msg'])) {
	$get['msg'] = $post['msg'] = $vars['msg'] = str_replace("\r", '', $vars['msg']);
}

// 後方互換性 (?md5=...)
if (isset($get['md5']) && $get['md5'] != '' &&
    ! isset($vars['cmd']) && ! isset($vars['plugin'])) {
	$get['cmd'] = $post['cmd'] = $vars['cmd'] = 'md5';
}

// cmdもpluginも指定されていない場合は、QUERY_STRINGをページ名かInterWikiNameであるとみなす
if (! isset($vars['cmd']) && ! isset($vars['plugin'])) {

	$get['cmd']  = $post['cmd']  = $vars['cmd']  = 'read';
	$arg = $page_uri_handler->get_page_from_query_string($arg);
	if ($arg === FALSE) {
		// page is FALSE if page name is not valid
		// Keep $arg is FALSE
	} else if (!$arg) {
		// if $arg is null or '' ($arg is NOT FALSE)
		$arg = $defaultpage;
	}
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
$InterWikiName = '(\[\[)?((?:(?!\s|:|\]\]).)+):(.+)(?(1)\]\])';

// 注釈
$NotePattern = '/\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)/x'
	. get_preg_u();

/////////////////////////////////////////////////
// 初期設定(ユーザ定義ルール読み込み)
require(DATA_HOME . 'rules.ini.php');

/////////////////////////////////////////////////
// Load HTML Entity pattern
// This pattern is created by 'plugin/update_entities.inc.php'
require(LIB_DIR . 'html_entities.php');

/////////////////////////////////////////////////
// 初期設定(その他のグローバル変数)

// 現在時刻
$now = format_date(UTIME);

// 日時置換ルールを$line_rulesに加える
if ($usedatetime) $line_rules += $datetime_rules;
unset($datetime_rules);

// フェイスマークを$line_rulesに加える
if ($usefacemark) $line_rules += $facemark_rules;
unset($facemark_rules);

// 実体参照パターンおよびシステムで使用するパターンを$line_rulesに加える
$line_rules = array_merge(array(
	'&amp;(#[0-9]+|#x[0-9a-f]+|' . get_html_entity_pattern() . ');' => '&$1;',
	"\r"          => '<br />' . "\n",	/* 行末にチルダは改行 */
), $line_rules);
