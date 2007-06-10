<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: rules.ini.php,v 1.10 2007/06/10 02:08:40 henoheno Exp $
// Copyright (C)
//   2003-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki setting file

/////////////////////////////////////////////////
// 日時置換ルール (閲覧時に置換)
// $usedatetime = 1なら日時置換ルールが適用されます
// 必要のない方は $usedatetimeを0にしてください。
$datetime_rules = array(
	'&amp;_now;'	=> format_date(UTIME),
	'&amp;_date;'	=> get_date($date_format),
	'&amp;_time;'	=> get_date($time_format),
);

/////////////////////////////////////////////////
// ユーザ定義ルール(保存時に置換)
//  正規表現で記述してください。?(){}-*./+\$^|など
//  は \? のようにクォートしてください。
//  前後に必ず / を含めてください。行頭指定は ^ を頭に。
//  行末指定は $ を後ろに。
//

// BugTrack2/106: Only variables can be passed by reference from PHP 5.0.5
$page_array = explode('/', $vars['page']); // with array_pop()

$str_rules = array(

	// Compat 1.3.x
	//'now\?' 	=> format_date(UTIME),
	//'date\?'	=> get_date($date_format),
	//'time\?'	=> get_date($time_format),

	'&now;' 	=> format_date(UTIME),
	'&date;'	=> get_date($date_format),
	'&time;'	=> get_date($time_format),
	'&page;'	=> array_pop($page_array),
	'&fpage;'	=> $vars['page'],
	'&t;'   	=> "\t",
);

unset($page_array);

?>
