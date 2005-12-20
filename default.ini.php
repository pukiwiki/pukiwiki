<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: default.ini.php,v 1.25 2005/12/20 14:04:40 henoheno Exp $
// Copyright (C)
//   2003-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki setting file (user agent:default)

/////////////////////////////////////////////////
// Skin file

if (defined('TDIARY_THEME')) {
	define('SKIN_FILE', DATA_HOME . SKIN_DIR . 'tdiary.skin.php');
} else {
	define('SKIN_FILE', DATA_HOME . SKIN_DIR . 'pukiwiki.skin.php');
}

/////////////////////////////////////////////////
// 雛形とするページの読み込みを可能にする
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
// 添付ファイルの一覧を常に表示する (負担がかかります)
$attach_link = 1;

/////////////////////////////////////////////////
// 関連するページのリンク一覧を常に表示する(負担がかかります)
$related_link = 1;

// リンク一覧の区切り文字
$related_str = "\n ";

// (#relatedプラグインが表示する) リンク一覧の区切り文字
$rule_related_str = "</li>\n<li>";

/////////////////////////////////////////////////
// 水平線のタグ
$hr = '<hr class="full_hr" />';

/////////////////////////////////////////////////
// 脚注機能関連

// 脚注のアンカーに埋め込む本文の最大長
define('PKWK_FOOTNOTE_TITLE_MAX', 16); // Characters

// 脚注のアンカーを相対パスで表示する (0 = 絶対パス)
//  * 相対パスの場合、以前のバージョンのOperaで問題になることがあります
//  * 絶対パスの場合、calendar_viewerなどで問題になることがあります
// (詳しくは: BugTrack/698)
define('PKWK_ALLOW_RELATIVE_FOOTNOTE_ANCHOR', 1);

// 文末の脚注の直前に表示するタグ
$note_hr = '<hr class="note_hr" />';

/////////////////////////////////////////////////
// WikiName,BracketNameに経過時間を付加する
$show_passage = 1;

/////////////////////////////////////////////////
// リンク表示をコンパクトにする
// * ページに対するハイパーリンクからタイトルを外す
// * Dangling linkのCSSを外す
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
	'COLOR\(([^\(\)]*)\){([^}]*)}'	=> '<span style="color:$1">$2</span>',
	'SIZE\(([^\(\)]*)\){([^}]*)}'	=> '<span style="font-size:$1px">$2</span>',
	'COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)'	=> '<span style="color:$1">$2</span>',
	'SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)'	=> '<span class="size$1">$2</span>',
	'%%%(?!%)((?:(?!%%%).)*)%%%'	=> '<ins>$1</ins>',
	'%%(?!%)((?:(?!%%).)*)%%'	=> '<del>$1</del>',
	"'''(?!')((?:(?!''').)*)'''"	=> '<em>$1</em>',
	"''(?!')((?:(?!'').)*)''"	=> '<strong>$1</strong>',
);

/////////////////////////////////////////////////
// フェイスマーク定義ルール(コンバート時に置換)

// $usefacemark = 1ならフェイスマークが置換されます
// 文章内にXDなどが入った場合にfacemarkに置換されてしまうので
// 必要のない方は $usefacemarkを0にしてください。

$facemark_rules = array(
	// Face marks
	'\s(\:\))'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/smile.png" />',
	'\s(\:D)'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/bigsmile.png" />',
	'\s(\:p)'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/huh.png" />',
	'\s(\:d)'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/huh.png" />',
	'\s(XD)'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/oh.png" />',
	'\s(X\()'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/oh.png" />',
	'\s(;\))'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/wink.png" />',
	'\s(;\()'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/sad.png" />',
	'\s(\:\()'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/sad.png" />',
	'&amp;(smile);'	=> ' <img alt="[$1]" src="' . IMAGE_DIR . 'face/smile.png" />',
	'&amp;(bigsmile);'=>' <img alt="[$1]" src="' . IMAGE_DIR . 'face/bigsmile.png" />',
	'&amp;(huh);'	=> ' <img alt="[$1]" src="' . IMAGE_DIR . 'face/huh.png" />',
	'&amp;(oh);'	=> ' <img alt="[$1]" src="' . IMAGE_DIR . 'face/oh.png" />',
	'&amp;(wink);'	=> ' <img alt="[$1]" src="' . IMAGE_DIR . 'face/wink.png" />',
	'&amp;(sad);'	=> ' <img alt="[$1]" src="' . IMAGE_DIR . 'face/sad.png" />',
	'&amp;(heart);'	=> ' <img alt="[$1]" src="' . IMAGE_DIR . 'face/heart.png" />',
	'&amp;(worried);'=>' <img alt="[$1]" src="' . IMAGE_DIR . 'face/worried.png" />',

	// Face marks, Japanese style
	'\s(\(\^\^\))'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/smile.png" />',
	'\s(\(\^-\^)'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/bigsmile.png" />',
	'\s(\(\.\.;)'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/oh.png" />',
	'\s(\(\^_-\))'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/wink.png" />',
	'\s(\(--;)'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/sad.png" />',
	'\s(\(\^\^;\))'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/worried.png" />',
	'\s(\(\^\^;)'	=> ' <img alt="$1" src="' . IMAGE_DIR . 'face/worried.png" />',

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
	'&amp;(phoneto);'=>'[phoneto]',
	'&amp;(faxto);'	=> '[faxto]',
);

?>
