<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: rules.ini.php,v 1.2 2004/03/20 13:32:29 arino Exp $
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
	'now\?' => format_date(UTIME),
	'date\?' => get_date($date_format),
	'time\?' => get_date($time_format),
	'&now;' => format_date(UTIME),
	'&date;' => get_date($date_format),
	'&time;' => get_date($time_format),
	'&page;' => array_pop(explode('/',$vars['page'])),
	'&fpage;' => $vars['page'],
	'&t;' => "\t",
);

/////////////////////////////////////////////////
// フェイスマーク定義ルール(コンバート時に置換)
// $usefacemark = 1ならフェイスマークが置換されます
// 文章内にXDなどが入った場合にfacemarkに置換されてしまうので
// 必要のない方は $usefacemarkを0にしてください。
$facemark_rules = array(
'\s(\:\))' => ' <img src="./face/smile.png" alt="$1" />',
'\s(\:D)' => ' <img src="./face/bigsmile.png" alt="$1" />',
'\s(\:p)' => ' <img src="./face/huh.png" alt="$1" />',
'\s(\:d)' => ' <img src="./face/huh.png" alt="$1" />',
'\s(XD)' => ' <img src="./face/oh.png" alt="$1" />',
'\s(X\()' => ' <img src="./face/oh.png" alt="$1" />',
'\s(;\))' => ' <img src="./face/wink.png" alt="$1" />',
'\s(;\()' => ' <img src="./face/sad.png" alt="$1" />',
'\s(\:\()' => ' <img src="./face/sad.png" alt="$1" />',
'&amp;(smile);' => ' <img src="./face/smile.png" alt="$1" />',
'&amp;(bigsmile);' => ' <img src="./face/bigsmile.png" alt="$1" />',
'&amp;(huh);' => ' <img src="./face/huh.png" alt="$1" />',
'&amp;(oh);' => ' <img src="./face/oh.png" alt="$1" />',
'&amp;(wink);' => ' <img src="./face/wink.png" alt="$1" />',
'&amp;(sad);' => ' <img src="./face/sad.png" alt="$1" />',
'&amp;(heart);' => '<img src="./face/heart.png" alt="$1" />',
);
?>
