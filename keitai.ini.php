<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: keitai.ini.php,v 1.2 2004/07/08 12:20:13 henoheno Exp $
//
// PukiWiki setting file (Cell phones, PDAs and other thin clients)

/////////////////////////////////////////////////
// max_size (SKINで使用)

$max_size = 5;	//KByte (default)

$matches = array();

// Browser-name only
switch ($user_agent['name']) {

	// NetFront / Compact NetFront
	//   DoCoMo Net For MOBILE: ｉモード対応HTMLの考え方: ユーザエージェント
	//   http://www.nttdocomo.co.jp/mc-user/i/tag/imodetag.html
	//   DDI POCKET: 機種ラインナップ: AirH"PHONE用ホームページの作成方法
	//   http://www.ddipocket.co.jp/p_s/products/airh_phone/homepage.html
	case 'NetFront':
	case 'CNF':
	case 'DoCoMo':
		if (preg_match('#\bc([0-9]+)\b#', $user_agent['agent'], $matches))
			$max_size = $matches[1];	// Cache size
		break;

	// Vodafone 技術資料: ユーザーエージェントについて
	// http://www.dp.j-phone.com/dp/tool_dl/web/useragent.php
	case 'J-PHONE':
		if (preg_match('#\bProfile/#', $user_agent['agent'])) {
			// パケット対応機
			$max_size = 12; // SKINで使用, KByte
		} else {
			// パケット非対応機
			$max_size =  6;
		}
		break;

}

// Browser-name + version
switch ($user_agent['name'] . '/' . $user_agent['vers']) {
	case 'DoCoMo/2.0':	$max_size = min($max_size, 30); break;
}
unset($matches);


/////////////////////////////////////////////////
// スキンファイルの場所
define('SKIN_FILE',SKIN_DIR.'keitai.skin.'.LANG.'.php');

/////////////////////////////////////////////////
// 雛形とするページの読み込みを表示させる
$load_template_func = 0;

/////////////////////////////////////////////////
// 検索文字列を色分けする
$search_word_color = 0;

/////////////////////////////////////////////////
// 一覧ページに頭文字インデックスをつける
$list_index = 0;

/////////////////////////////////////////////////
// リスト構造の左マージン
$_ul_left_margin = 0;   // リストと画面左端との間隔(px)
$_ul_margin = 16;       // リストの階層間の間隔(px)
$_ol_left_margin = 0;   // リストと画面左端との間隔(px)
$_ol_margin = 16;       // リストの階層間の間隔(px)
$_dl_left_margin = 0;   // リストと画面左端との間隔(px)
$_dl_margin = 16;        // リストの階層間の間隔(px)
$_list_pad_str = '';

/////////////////////////////////////////////////
// cols: テキストエリアのカラム数 rows: 行数

$cols = 22; $rows = 5;	// i_mode
$cols = 24; $rows = 20; // jphone

/////////////////////////////////////////////////
// 大・小見出しから目次へ戻るリンクの文字
$top = '';

/////////////////////////////////////////////////
// 関連ページ表示のページ名の区切り文字
$related_str = "\n ";

/////////////////////////////////////////////////
// 整形ルールでの関連ページ表示のページ名の区切り文字
$rule_related_str = "</li>\n<li>";

/////////////////////////////////////////////////
// 水平線のタグ
$hr = '<hr>';

/////////////////////////////////////////////////
// 文末の注釈の直前に表示するタグ
$note_hr = '<hr>';

/////////////////////////////////////////////////
// 関連するリンクを常に表示する(負担がかかります)
$related_link = 0;

/////////////////////////////////////////////////
// WikiName,BracketNameに経過時間を付加する
$show_passage = 0;

/////////////////////////////////////////////////
// リンク表示をコンパクトにする
$link_compact = 1;

/////////////////////////////////////////////////
// フェイスマークを使用する
$usefacemark = 0;

/////////////////////////////////////////////////
// accesskey (SKINで使用)
$accesskey = 'accesskey';

/////////////////////////////////////////////////
// ユーザ定義ルール
//
//  正規表現で記述してください。?(){}-*./+\$^|など
//  は \? のようにクォートしてください。
//  前後に必ず / を含めてください。行頭指定は ^ を頭に。
//  行末指定は $ を後ろに。
///////////////////////////////////////////////////
// ユーザ定義ルール(コンバート時に置換)
$line_rules = array(
"COLOR\(([^\(\)]*)\){([^}]*)}" => '<font color="$1">$2</font>',
"SIZE\(([^\(\)]*)\){([^}]*)}" => '$2',
"COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)" => '<font color="$1">$2</font>',
"SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)" => '$2',
"%%%(?!%)((?:(?!%%%).)*)%%%" => '<ins>$1</ins>',
"%%(?!%)((?:(?!%%).)*)%%" => '<del>$1</del>',
"'''(?!')((?:(?!''').)*)'''" => '<em>$1</em>',
"''(?!')((?:(?!'').)*)''" => '<strong>$1</strong>',
'&amp;br;' => '<br>',
);

/////////////////////////////////////////////////
// $scriptを短縮
if (preg_match('#([^/]+)$#',$script,$matches)) {
	$script = $matches[1];
}
?>
