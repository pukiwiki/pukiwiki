<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: default.ini.php,v 1.6 2004/07/15 12:44:36 henoheno Exp $
//
// PukiWiki setting file (user agent:default)

/////////////////////////////////////////////////
// スキンファイルの場所
define('SKIN_FILE',SKIN_DIR.'pukiwiki.skin.'.LANG.'.php');

/////////////////////////////////////////////////
// 雛形とするページの読み込みを表示させる
$load_template_func = 1;

/////////////////////////////////////////////////
// 検索文字列を色分けする
$search_word_color = 1;

/////////////////////////////////////////////////
// 一覧ページに頭文字インデックスをつける
$list_index = 1;

/////////////////////////////////////////////////
// リスト構造の左マージン
$_ul_left_margin = 0;   // リストと画面左端との間隔(px)
$_ul_margin = 16;       // リストの階層間の間隔(px)
$_ol_left_margin = 0;   // リストと画面左端との間隔(px)
$_ol_margin = 16;       // リストの階層間の間隔(px)
$_dl_left_margin = 0;   // リストと画面左端との間隔(px)
$_dl_margin = 16;        // リストの階層間の間隔(px)
$_list_pad_str = ' class="list%d" style="padding-left:%dpx;margin-left:%dpx"';

/////////////////////////////////////////////////
// テキストエリアのカラム数
$cols = 80;

/////////////////////////////////////////////////
// テキストエリアの行数
$rows = 20;

/////////////////////////////////////////////////
// 大・小見出しから目次へ戻るリンクの文字
$top = $_msg_content_back_to_top;

/////////////////////////////////////////////////
// 関連ページ表示のページ名の区切り文字
$related_str = "\n ";

/////////////////////////////////////////////////
// 整形ルールでの関連ページ表示のページ名の区切り文字
$rule_related_str = "</li>\n<li>";

/////////////////////////////////////////////////
// 水平線のタグ
$hr = '<hr class="full_hr" />';

/////////////////////////////////////////////////
// 文末の注釈の直前に表示するタグ
$note_hr = '<hr class="note_hr" />';

/////////////////////////////////////////////////
// 関連するリンクを常に表示する(負担がかかります)
$related_link = 1;

/////////////////////////////////////////////////
// WikiName,BracketNameに経過時間を付加する
$show_passage = 1;

/////////////////////////////////////////////////
// リンク表示をコンパクトにする
$link_compact = 0;

/////////////////////////////////////////////////
// フェイスマークを使用する
$usefacemark = 1;

/////////////////////////////////////////////////
// ユーザ定義ルール
//
//  正規表現で記述してください。?(){}-*./+\$^|など
//  は \? のようにクォートしてください。
//  前後に必ず / を含めてください。行頭指定は ^ を頭に。
//  行末指定は $ を後ろに。
//
/////////////////////////////////////////////////
// ユーザ定義ルール(コンバート時に置換)
$line_rules = array(
	"COLOR\(([^\(\)]*)\){([^}]*)}"	=> '<span style="color:$1">$2</span>',
	"SIZE\(([^\(\)]*)\){([^}]*)}"	=> '<span style="font-size:$1px">$2</span>',
	"COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)"	=> '<span style="color:$1">$2</span>',
	"SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)"	=> '<span class="size$1">$2</span>',
	"%%%(?!%)((?:(?!%%%).)*)%%%"	=> '<ins>$1</ins>',
	"%%(?!%)((?:(?!%%).)*)%%"	=> '<del>$1</del>',
	"'''(?!')((?:(?!''').)*)'''"	=> '<em>$1</em>',
	"''(?!')((?:(?!'').)*)''"	=> '<strong>$1</strong>',
	'&amp;br;'	=> '<br />',
);

/////////////////////////////////////////////////
// フェイスマーク定義ルール(コンバート時に置換)

// $usefacemark = 1ならフェイスマークが置換されます
// 文章内にXDなどが入った場合にfacemarkに置換されてしまうので
// 必要のない方は $usefacemarkを0にしてください。

$facemark_rules = array(
	// Face marks
	'\s(\:\))'	=> ' <img alt="$1" src="./face/smile.png" />',
	'\s(\:D)'	=> ' <img alt="$1" src="./face/bigsmile.png" />',
	'\s(\:p)'	=> ' <img alt="$1" src="./face/huh.png" />',
	'\s(\:d)'	=> ' <img alt="$1" src="./face/huh.png" />',
	'\s(XD)'	=> ' <img alt="$1" src="./face/oh.png" />',
	'\s(X\()'	=> ' <img alt="$1" src="./face/oh.png" />',
	'\s(;\))'	=> ' <img alt="$1" src="./face/wink.png" />',
	'\s(;\()'	=> ' <img alt="$1" src="./face/sad.png" />',
	'\s(\:\()'	=> ' <img alt="$1" src="./face/sad.png" />',
	'&amp;(smile);'	=> ' <img alt="$1" src="./face/smile.png" />',
	'&amp;(bigsmile);'=> ' <img alt="$1" src="./face/bigsmile.png" />',
	'&amp;(huh);'	=> ' <img alt="$1" src="./face/huh.png" />',
	'&amp;(oh);'	=> ' <img alt="$1" src="./face/oh.png" />',
	'&amp;(wink);'	=> ' <img alt="$1" src="./face/wink.png" />',
	'&amp;(sad);'	=> ' <img alt="$1" src="./face/sad.png" />',
	'&amp;(heart);'	=> ' <img alt="$1" src="./face/heart.png" />',
	'&amp;(sweat);'	=> ' <img alt="$1" src="./face/sweat.png" />',

	// Face marks, Japanese style
	'(\(\^\^\))'	=> ' <img alt="$1" src="./face/smile.png" />',
	'(\(\^-\^)'	=> ' <img alt="$1" src="./face/bigsmile.png" />',
	'(\(\.\.;)'	=> ' <img alt="$1" src="./face/oh.png" />',
	'(\(\^_-\))'	=> ' <img alt="$1" src="./face/wink.png" />',
	'(\(--;)'	=> ' <img alt="$1" src="./face/sad.png" />',
	'(\(\^\^;)'	=> ' <img alt="$1" src="./face/sweat.png" />',
	'(\(\^\^;\))'	=> ' <img alt="$1" src="./face/sweat.png" />',

	// Push buttons, 0-9 and sharp (Compatibility with cell phones)
	'&amp;(pb1);'	=> '[1]',
	'&amp;(pb2);'	=> '[2]',
	'&amp;(pb3);'	=> '[3]',
	'&amp;(pb4);'	=> '[4]',
	'&amp;(pb5);'	=> '[5]',
	'&amp;(pb6);'	=> '[6]',
	'&amp;(pb7);'	=> '[7]',
	'&amp;(pb8);'	=> '[8]',
	'&amp;(pb9);'	=> '[9]',
	'&amp;(pb0);'	=> '[0]',
	'&amp;(pb#);'	=> '[#]',

	// Other icons (Compatibility with cell phones)
	'&amp;(zzz);'	=> '[zzz]',
	'&amp;(man);'	=> '[man]',
	'&amp;(clock);'	=> '[clock]',
	'&amp;(mail);'	=> '[mail]',
	'&amp;(mailto);'=> '[mailto]',
	'&amp;(phone);'	=> '[phone]',
	'&amp;(phoneto);'=> '[phoneto]',
	'&amp;(faxto);'	=> '[faxto]',

);

?>
