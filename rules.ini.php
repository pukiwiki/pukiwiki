<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: rules.ini.php,v 1.3 2004/07/13 13:12:15 henoheno Exp $
//
// PukiWiki setting file

/////////////////////////////////////////////////
// ユーザ定義ルール
//
//  正規表現で記述してください。?(){}-*./+\$^|など
//  は \? のようにクォートしてください。
//  前後に必ず / を含めてください。行頭指定は ^ を頭に。
//  行末指定は $ を後ろに。
//
/////////////////////////////////////////////////
// ユーザ定義ルール(直接ソースを置換)
$str_rules = array(
	'now\?' 	=> format_date(UTIME),
	'date\?'	=> get_date($date_format),
	'time\?'	=> get_date($time_format),
	'&now;' 	=> format_date(UTIME),
	'&date;'	=> get_date($date_format),
	'&time;'	=> get_date($time_format),
	'&page;'	=> array_pop(explode('/', $vars['page'])),
	'&fpage;'	=> $vars['page'],
	'&t;'   	=> "\t",
);

?>
