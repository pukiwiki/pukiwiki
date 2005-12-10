<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: keitai.ini.php,v 1.25 2005/12/10 07:57:30 henoheno Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki setting file (Cell phones, PDAs and other thin clients)

/////////////////////////////////////////////////
// 携帯・PDA専用のページを初期ページとして指定する

// $defaultpage = 'm';

/////////////////////////////////////////////////
// スキンファイルの場所
define('SKIN_FILE', DATA_HOME . SKIN_DIR . 'keitai.skin.php');

/////////////////////////////////////////////////
// 雛形とするページの読み込みを可能にする
$load_template_func = 0;

/////////////////////////////////////////////////
// 検索文字列を色分けする
$search_word_color = 0;

/////////////////////////////////////////////////
// 一覧ページに頭文字インデックスをつける
$list_index = 0;

/////////////////////////////////////////////////
// リスト構造の左マージン
$_ul_left_margin =  0;	// リストと画面左端との間隔(px)
$_ul_margin      = 16;	// リストの階層間の間隔(px)
$_ol_left_margin =  0;	// リストと画面左端との間隔(px)
$_ol_margin      = 16;	// リストの階層間の間隔(px)
$_dl_left_margin =  0;	// リストと画面左端との間隔(px)
$_dl_margin      = 16;	// リストの階層間の間隔(px)
$_list_pad_str   = '';

/////////////////////////////////////////////////
// 大・小見出しから目次へ戻るリンクの文字
$top = '';

/////////////////////////////////////////////////
// 添付ファイルの一覧を常に表示する (負担がかかります)
// ※keitaiスキンにはこの一覧を表示する機能がありません
$attach_link = 0;

/////////////////////////////////////////////////
// 関連するページのリンク一覧を常に表示する(負担がかかります)
// ※keitaiスキンにはこの一覧を表示する機能がありません
$related_link = 0;

// リンク一覧の区切り文字
// ※上同
$related_str = "\n ";

// (#relatedプラグインが表示する) リンク一覧の区切り文字
$rule_related_str = "</li>\n<li>";

/////////////////////////////////////////////////
// 水平線のタグ
$hr = '<hr>';

/////////////////////////////////////////////////
// 脚注機能関連

// 脚注のアンカーに埋め込む本文の最大長
define('PKWK_FOOTNOTE_TITLE_MAX', 0); // Characters

// 脚注のアンカーを相対パスで表示する (0 = 絶対パス)
//  * 相対パスの場合、以前のバージョンのOperaで問題になることがあります
//  * 絶対パスの場合、calendar_viewerなどで問題になることがあります
// (詳しくは: BugTrack/698)
define('PKWK_ALLOW_RELATIVE_FOOTNOTE_ANCHOR', 1);

// 文末の注釈の直前に表示するタグ
$note_hr = '<hr>';

/////////////////////////////////////////////////
// WikiName,BracketNameに経過時間を付加する
$show_passage = 0;

/////////////////////////////////////////////////
// リンク表示をコンパクトにする
// * ページに対するハイパーリンクからタイトルを外す
// * Dangling linkのCSSを外す
$link_compact = 1;

/////////////////////////////////////////////////
// フェイスマークを絵文字に変換する (※i-mode, Vodafone, EzWebなど携帯電話限定)
$usefacemark = 1;

/////////////////////////////////////////////////
// accesskey (SKINで使用)
$accesskey = 'accesskey';

/////////////////////////////////////////////////
// $scriptを短縮
if (preg_match('#([^/]+)$#', $script, $matches)) {
	$script = $matches[1];
}

/////////////////////////////////////////////////
// ブラウザ調整前のデフォルト値

// max_size (SKINで使用)
$max_size = 5;	// SKINで使用, KByte

// cols: テキストエリアのカラム数 rows: 行数
$cols = 22; $rows = 5;	// i_mode


/////////////////////////////////////////////////
// ブラウザに合わせた調整

$ua_name  = $user_agent['name'];
$ua_vers  = $user_agent['vers'];
$ua_agent = $user_agent['agent'];
$matches  = array();

// Browser-name only
switch ($ua_name) {

	// NetFront / Compact NetFront
	//   DoCoMo Net For MOBILE: ｉモード対応HTMLの考え方: ユーザエージェント
	//   http://www.nttdocomo.co.jp/mc-user/i/tag/imodetag.html
	//   DDI POCKET: 機種ラインナップ: AirH"PHONE用ホームページの作成方法
	//   http://www.ddipocket.co.jp/p_s/products/airh_phone/homepage.html
	case 'NetFront':
	case 'CNF':
	case 'DoCoMo':
	case 'Opera': // Performing CNF compatible
		if (preg_match('#\b[cC]([0-9]+)\b#', $ua_agent, $matches)) {
			$max_size = $matches[1];	// Cache max size
		}
		$cols = 22; $rows = 5;	// i_mode
		break;

	// Vodafone (ex. J-PHONE)
	// ボーダフォンライブ！向けウェブコンテンツ開発ガイド [概要編] (Version 1.2.0 P13)
	// http://www.dp.j-phone.com/dp/tool_dl/download.php?docid=110
	// 技術資料: ユーザーエージェントについて
	// http://www.dp.j-phone.com/dp/tool_dl/web/useragent.php
	case 'J-PHONE':
		$matches = array("");
		preg_match('/^([0-9]+)\./', $user_agent['vers'], $matches);
		switch($matches[1]){
		case '3': $max_size =   6; break; // C type: lt   6000bytes
		case '4': $max_size =  12; break; // P type: lt  12Kbytes
		case '5': $max_size = 200; break; // W type: lt 200Kbytes
		}
		$cols = 24; $rows = 20;
		break;

	// UP.Browser
	case 'UP.Browser':
		// UP.Browser for KDDI cell phones
		// http://www.au.kddi.com/ezfactory/tec/spec/xhtml.html ('About 9KB max')
		// http://www.au.kddi.com/ezfactory/tec/spec/4_4.html (User-agent strings)
		if (preg_match('#^KDDI#', $ua_agent)) $max_size =  9;
		break;
}

// Browser-name + version
switch ("$ua_name/$ua_vers") {
	// Restriction For imode:
	//  http://www.nttdocomo.co.jp/mc-user/i/tag/s2.html
	case 'DoCoMo/2.0':	$max_size = min($max_size, 30); break;
}


/////////////////////////////////////////////////
// ユーザ定義ルール
//
//  正規表現で記述してください。?(){}-*./+\$^|など
//  は \? のようにクォートしてください。
//  前後に必ず / を含めてください。行頭指定は ^ を頭に。
//  行末指定は $ を後ろに。

// ユーザ定義ルール(コンバート時に置換)
$line_rules = array(
	'COLOR\(([^\(\)]*)\){([^}]*)}'	=> '<font color="$1">$2</font>',
	'SIZE\(([^\(\)]*)\){([^}]*)}'	=> '$2',	// Disabled
	'COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)'	=> '<font color="$1">$2</font>',
	'SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)'	=> '$2', // Disabled
	'%%%(?!%)((?:(?!%%%).)*)%%%'	=> '<ins>$1</ins>',
	'%%(?!%)((?:(?!%%).)*)%%'	=> '<del>$1</del>',
	"'''(?!')((?:(?!''').)*)'''"	=> '<em>$1</em>',
	"''(?!')((?:(?!'').)*)''"	=> '<strong>$1</strong>',
);


/////////////////////////////////////////////////
// 携帯電話にあわせたフェイスマーク

// $usefacemark = 1ならフェイスマークが置換されます
// 文章内に' XD'などがあった場合にfacemarkに置換されてしまうため、
// 必要のない方は $usefacemarkを0にしてください。

// Browser-name only
$facemark_rules = array();
switch ($ua_name) {

    // Graphic icons for imode HTML 4.0, with Shift-JIS text output
    // http://www.nttdocomo.co.jp/mc-user/i/tag/emoji/e1.html
    // http://www.nttdocomo.co.jp/mc-user/i/tag/emoji/list.html
    case 'DoCoMo':

	$facemark_rules = array(
	// Face marks
	'\s(\:\))'	=>	'&#63893;',	// smile
	'\s(\:D)'	=>	'&#63893;',	// bigsmile
	'\s(\:p)'	=>	'&#xE728;',	// huh
	'\s(\:d)'	=>	'&#xE728;',	// huh
	'\s(XD)'	=>	'&#63895;',	// oh
	'\s(X\()'	=>	'&#63895;',	// oh
	'\s(;\))'	=>	'&#xE729;',	// wink
	'\s(;\()'	=>	'&#63894;',	// sad
	'\s(\:\()'	=>	'&#63894;',	// sad
	'&amp;(smile);'	=>	'&#63893;',
	'&amp;(bigsmile);'=>	'&#63893;',
	'&amp;(huh);'	=>	'&#xE728;',
	'&amp;(oh);'	=>	'&#63895;',
	'&amp;(wink);'	=>	'&#xE729;',
	'&amp;(sad);'	=>	'&#63894;',
	'&amp;(heart);'	=>	'&#63889;',
	'&amp;(worried);'=>	'&#xE722;',

	// Face marks, Japanese style
	'\s(\(\^\^\))'	=>	'&#63893;',	// smile
	'\s(\(\^-\^)'	=>	'&#63893;',	// smile
	'\s(\(\.\.;)'	=>	'&#63895;',	// oh
	'\s(\(\^_-\))'	=>	'&#xE729;',	// wink
	'\s(\(--;)'	=>	'&#63894;',	// sad
	'\s(\(\^\^;\))'	=>	'&#xE722;',	// worried
	'\s(\(\^\^;)'	=>	'&#xE722;',	// worried

	// Push buttons, 0-9 and sharp
	'&amp;(pb1);'	=>	'&#63879;',
	'&amp;(pb2);'	=>	'&#63880;',
	'&amp;(pb3);'	=>	'&#63881;',
	'&amp;(pb4);'	=>	'&#63882;',
	'&amp;(pb5);'	=>	'&#63883;',
	'&amp;(pb6);'	=>	'&#63884;',
	'&amp;(pb7);'	=>	'&#63885;',
	'&amp;(pb8);'	=>	'&#63886;',
	'&amp;(pb9);'	=>	'&#63887;',
	'&amp;(pb0);'	=>	'&#63888;',
	'&amp;(pb#);'	=>	'&#63877;',

	// Others
	'&amp;(zzz);'	=>	'&#63910;',
	'&amp;(man);'	=>	'&#63829;',
	'&amp;(clock);'	=>	'&#63838;',
	'&amp;(mail);'	=>	'&#63863;',
	'&amp;(mailto);'=>	'&#63859;',
	'&amp;(phone);'	=>	'&#63720;',
	'&amp;(phoneto);'=>	'&#63858;',
	'&amp;(faxto);'	=>	'&#63860;',
	);
	break;

    // Graphic icons for Vodafone (ex. J-PHONE) cell phones
    // http://www.dp.j-phone.com/dp/tool_dl/web/picword_top.php
    case 'J-PHONE':

	$facemark_rules = array(
	// Face marks
	'\s(\:\))'	=>	chr(27).'$Gv'.chr(15),	// '&#57430;',	// smile
	'\s(\:D)'	=>	chr(27).'$Gv'.chr(15),	// '&#57430;',	// bigsmile => smile
	'\s(\:p)'	=>	chr(27).'$E%'.chr(15),	// '&#57605;',	// huh
	'\s(\:d)'	=>	chr(27).'$E%'.chr(15),	// '&#57605;',	// huh
	'\s(XD)'	=>	chr(27).'$Gx'.chr(15),	// '&#57432;',	// oh
	'\s(X\()'	=>	chr(27).'$Gx'.chr(15),	// '&#57432;',	// oh
	'\s(;\))'	=>	chr(27).'$E&'.chr(15),	// '&#57606;',	// winkじゃないけどね(^^; (※目がハート)
	'\s(;\()'	=>	chr(27).'$E&'.chr(15),	// '&#57606;',	// sad
	'\s(\:\()'	=>	chr(27).'$Gy'.chr(15),	// '&#57433;',	// sad
	'&amp;(smile);'	=>	chr(27).'$Gv'.chr(15),	// '&#57430;',
	'&amp;(bigsmile);'=>	chr(27).'$Gw'.chr(15),	// '&#57431;',
	'&amp;(huh);'	=>	chr(27).'$E%'.chr(15),	// '&#57605;',
	'&amp;(oh);'	=>	chr(27).'$Gx'.chr(15),	// '&#57432;',
	'&amp;(wink);'	=>	chr(27).'$E&'.chr(15),	// '&#57606;',	// winkじゃないけどね(^^; (※目がハート)
	'&amp;(sad);'	=>	chr(27).'$Gy'.chr(15),	// '&#57433;',
	'&amp;(heart);'	=>	chr(27).'$GB'.chr(15),	// '&#57378;',
	'&amp;(worried);'=>	chr(27).'$E('.chr(15),	// '&#57608;',

	// Face marks, Japanese style
	'\s(\(\^\^\))'	=>	chr(27).'$Gv'.chr(15),	// smile
	'\s(\(\^-\^)'	=>	chr(27).'$Gv'.chr(15),	// smile
	'\s(\(\.\.;)'	=>	chr(27).'$Gx'.chr(15),	// oh
	'\s(\(\^_-\))'	=>	chr(27).'$E&'.chr(15),	// winkじゃないけどね(^^; (※目がハート)
	'\s(\(--;)'	=>	chr(27).'$E&'.chr(15),	// sad
	'\s(\(\^\^;\))'	=>	chr(27).'$E('.chr(15),	// worried
	'\s(\(\^\^;)'	=>	chr(27).'$E('.chr(15),	// worried

	// Push buttons, 0-9 and sharp
	'&amp;(pb1);'	=>	chr(27).'$F<'.chr(15),	// '&#57884;',
	'&amp;(pb2);'	=>	chr(27).'$F='.chr(15),	// '&#57885;',
	'&amp;(pb3);'	=>	chr(27).'$F>'.chr(15),	// '&#57886;',
	'&amp;(pb4);'	=>	chr(27).'$F?'.chr(15),	// '&#57887;',
	'&amp;(pb5);'	=>	chr(27).'$F@'.chr(15),	// '&#57888;',
	'&amp;(pb6);'	=>	chr(27).'$FA'.chr(15),	// '&#57889;',
	'&amp;(pb7);'	=>	chr(27).'$FB'.chr(15),	// '&#57890;',
	'&amp;(pb8);'	=>	chr(27).'$FC'.chr(15),	// '&#57891;',
	'&amp;(pb9);'	=>	chr(27).'$FD'.chr(15),	// '&#57892;',
	'&amp;(pb0);'	=>	chr(27).'$FE'.chr(15),	// '&#57893;',

	// Others
	'&amp;(zzz);'	=>	chr(27).'$E\\'.chr(15),
	'&amp;(man);'	=>	chr(27).'$G!'.chr(15),
	'&amp;(clock);'	=>	chr(27).'$GF'.chr(15),	// '&#xE026;',
	'&amp;(mail);'	=>	chr(27).'$Fv'.chr(15),
	'&amp;(mailto);'=>	chr(27).'$E#'.chr(15),
	'&amp;(phone);'	=>	chr(27).'$G)'.chr(15),
	'&amp;(phoneto);'=>	chr(27).'$E$'.chr(15),
	'&amp;(faxto);'	=>	chr(27).'$G+'.chr(15),
	);
	break;

    case 'UP.Browser':

	// UP.Browser for KDDI cell phones' built-in icons
	// http://www.au.kddi.com/ezfactory/tec/spec/3.html
	if (preg_match('#^KDDI#', $ua_agent)) {
	$facemark_rules = array(
	// Face marks
	'\s(\:\))'	=>	'<img localsrc="68">',	// smile
	'\s(\:D)'	=>	'<img localsrc="257">',	// bigsmile
	'\s(\:p)'	=>	'<img localsrc="264">',	// huh
	'\s(\:d)'	=>	'<img localsrc="264">',	// huh
	'\s(XD)'	=>	'<img localsrc="260">',	// oh
	'\s(X\()'	=>	'<img localsrc="260">',	// oh
	'\s(;\))'	=>	'<img localsrc="348">',	// wink
	'\s(;\()'	=>	'<img localsrc="259">',	// sad
	'\s(\:\()'	=>	'<img localsrc="259">',	// sad
	'&amp;(smile);'	=>	'<img localsrc="68">',
	'&amp;(bigsmile);'=>	'<img localsrc="257">',
	'&amp;(huh);'	=>	'<img localsrc="264">',
	'&amp;(oh);'	=>	'<img localsrc="260">',
	'&amp;(wink);'	=>	'<img localsrc="348">',
	'&amp;(sad);'	=>	'<img localsrc="259">',
	'&amp;(heart);'	=>	'<img localsrc="415">',
	'&amp;(worried);'=>	'<img localsrc="351">',

	// Face marks, Japanese style
	'\s(\(\^\^\))'	=>	'<img localsrc="68">',	// smile
	'\s(\(\^-\^)'	=>	'<img localsrc="68">',	// smile
	'\s(\(\.\.;)'	=>	'<img localsrc="260">',	// oh
	'\s(\(\^_-\))'	=>	'<img localsrc="348">',	// wink
	'\s(\(--;)'	=>	'<img localsrc="259">',	// sad
	'\s(\(\^\^;\))'	=>	'<img localsrc="351">',	// worried
	'\s(\(\^\^;)'	=>	'<img localsrc="351">',	// worried

	// Push buttons, 0-9 and sharp
	'&amp;(pb1);'	=>	'<img localsrc="180">',
	'&amp;(pb2);'	=>	'<img localsrc="181">',
	'&amp;(pb3);'	=>	'<img localsrc="182">',
	'&amp;(pb4);'	=>	'<img localsrc="183">',
	'&amp;(pb5);'	=>	'<img localsrc="184">',
	'&amp;(pb6);'	=>	'<img localsrc="185">',
	'&amp;(pb7);'	=>	'<img localsrc="186">',
	'&amp;(pb8);'	=>	'<img localsrc="187">',
	'&amp;(pb9);'	=>	'<img localsrc="188">',
	'&amp;(pb0);'	=>	'<img localsrc="325">',
	'&amp;(pb#);'	=>	'<img localsrc="818">',

	// Others
	'&amp;(zzz);'	=>	'<img localsrc="261">',
	'&amp;(man);'	=>	'<img localsrc="80">',	// Face of male
	'&amp;(clock);'	=>	'<img localsrc="46">',
	'&amp;(mail);'	=>	'<img localsrc="108">',
	'&amp;(mailto);'=>	'<img localsrc="784">',
	'&amp;(phone);'	=>	'<img localsrc="85">',
	'&amp;(phoneto);'=>	'<img localsrc="155">',	// An ear receiver
	'&amp;(faxto);'	=>	'<img localsrc="166">',	// A FAX
	);
	}
	break;

}

unset($matches, $ua_name, $ua_vers, $ua_agent, $special_rules);

?>
