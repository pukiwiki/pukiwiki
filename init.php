<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: init.php,v 1.20.2.5 2003/03/16 15:22:21 panda Exp $
/////////////////////////////////////////////////

// 設定ファイルの場所
define("INI_FILE","./pukiwiki.ini.php");

//** 初期設定 **

ini_set('error_reporting', 5);
define("S_VERSION","1.3.4");
define("S_COPYRIGHT","<strong>\"PukiWiki\" ".S_VERSION."</strong> Copyright &copy; 2001,2002,2003 <a href=\"http://pukiwiki.org\">PukiWiki Developers Team</a>. License is <a href=\"http://www.gnu.org/\">GNU/GPL</a>.<BR>Based on \"PukiWiki\" 1.3 by <a href=\"http://factage.com/sng/\">sng</a>");
define("UTIME",time());
define("HTTP_USER_AGENT",$HTTP_SERVER_VARS["HTTP_USER_AGENT"]);
define("PHP_SELF",$HTTP_SERVER_VARS["PHP_SELF"]);
define("SERVER_NAME",$HTTP_SERVER_VARS["SERVER_NAME"]);

define("MUTIME",getmicrotime());

if($script == "") {
	$script = (getenv('SERVER_PORT')==443?'https://':('http://')).getenv('SERVER_NAME').(getenv('SERVER_PORT')==80?'':(':'.getenv('SERVER_PORT'))).getenv('SCRIPT_NAME');
}

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

if($post["msg"])
{
	$post["msg"] = preg_replace("/((\x0D\x0A)|(\x0D)|(\x0A))/","\n",$post["msg"]);
}
if($get["page"]) $get["page"] = rawurldecode($get["page"]);
if($post["word"]) $post["word"] = rawurldecode($post["word"]);
if($get["word"]) $get["word"] = rawurldecode($get["word"]);

$vars = array_merge($post,$get);

$arg = rawurldecode((getenv('QUERY_STRING') != '')?
		    getenv('QUERY_STRING') :
		    $HTTP_SERVER_VARS["argv"][0]);

$arg = sanitize_null_character($arg);

//** 初期処理 **
$update_exec = "";
$content_id = 0;

// 設定ファイルの読込
if(!file_exists(INI_FILE)||!is_readable(INI_FILE))
	die_message(INI_FILE." is not found.");
require(INI_FILE);

if(!file_exists(LANG.".lng")||!is_readable(LANG.".lng"))
	die_message(LANG.".lng(language file) is not found.");


if($usefacemark) {
  $line_rules = array_merge($line_rules,$facemark_rules);
}
$user_rules = array_merge($str_rules,$line_rules);

$note_id = 1;
$foot_explain = array();

// 変数のチェック
if(php_sapi_name()=='cgi' && !preg_match("/^http:\/\/[-a-zA-Z0-9\@:;_.]+\//",$script))
	die_message("please set '\$script' in ".INI_FILE);


// 設定ファイルの変数チェック
$wrong_ini_file = "";
if(!isset($rss_max)) $wrong_ini_file .= '$rss_max ';
if(!isset($page_title)) $wrong_ini_file .= '$page_title ';
if(!isset($note_hr)) $wrong_ini_file .= '$note_hr ';
if(!isset($related_link)) $wrong_ini_file .= '$related_link ';
if(!isset($show_passage)) $wrong_ini_file .= '$show_passage ';
if(!isset($rule_related_str)) $wrong_ini_file .= '$rule_related_str ';
if(!isset($load_template_func)) $wrong_ini_file .= '$load_template_func ';
if(!defined("LANG")) $wrong_ini_file .= 'LANG ';
if(!defined("PLUGIN_DIR")) $wrong_ini_file .= 'PLUGIN_DIR ';

if(!is_writable(DATA_DIR))
	die_message("DATA_DIR is not found or not writable.");
if(!is_writable(DIFF_DIR))
	die_message("DIFF_DIR is not found or not writable.");
if($do_backup && !is_writable(BACKUP_DIR))
	die_message("BACKUP_DIR is not found or not writable.");
if($wrong_ini_file)
	die_message("The setting file runs short of information.<br>The version of a setting file may be old.<br><br>These option are not found : $wrong_ini_file");

if(!file_exists(get_filename(encode($defaultpage))))
	touch(get_filename(encode($defaultpage)));
if(!file_exists(get_filename(encode($whatsnew))))
	touch(get_filename(encode($whatsnew)));
if(!file_exists(get_filename(encode($interwiki))))
	touch(get_filename(encode($interwiki)));

$ins_date = date($date_format,UTIME);
$ins_time = date($time_format,UTIME);
$ins_week = "(".$weeklabels[date("w",UTIME)].")";

$now = "$ins_date $ins_week $ins_time";

?>
