<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: pukiwiki.ini.php,v 1.111.2.4 2005/03/27 13:56:17 henoheno Exp $
//
// PukiWiki メイン設定ファイル

/////////////////////////////////////////////////
// 機能性に関する設定

// PKWK_OPTIMISE - 過剰ではあるが解りやすいチェックや警告を省略する
//   このPukiWikiに関する動作確認を終えているならば '1' に、
//   このPukiWikiが何かトラブルを抱えているならば '0' にして下さい
if (! defined('PKWK_OPTIMISE'))
	define('PKWK_OPTIMISE', 0);

/////////////////////////////////////////////////
// セキュリティ設定

// PKWK_READONLY - Webブラウザ経由の編集やメンテナンスを禁止する
//   補足: カウンター関係の機能は動作します
//         (counterプラグイン、attachプラグインのカウント機能など)
if (! defined('PKWK_READONLY'))
	define('PKWK_READONLY', 0); // 0 or 1

// PKWK_SAFE_MODE - いくつかの安全でない(しかし互換性のある)機能を禁止する
if (! defined('PKWK_SAFE_MODE'))
	define('PKWK_SAFE_MODE', 0);

// PKWK_QUERY_STRING_MAX
//   GETメソッドの最大長を制限することにより、ある種のウイルス(ワーム)
//   からのアクセスを直ちに禁止する
//   注意: ページ名と添付ファイル名を足した長さより大きい必要があります
//        (page-name + attach-file-name) <= PKWK_QUERY_STRING_MAX
define('PKWK_QUERY_STRING_MAX', 640); // Bytes, 0 = OFF

/////////////////////////////////////////////////
// 言語 / エンコーディング方式の設定

// LANG - 内部コンテンツの言語指定 ('en', 'ja', or ...)
define('LANG', 'ja');

// UI_LANG - メニューやボタンなどに使われる言語指定
define('UI_LANG', LANG); // 'en' for Internationalized wikisite

/////////////////////////////////////////////////
// ディレクトリ関係の設定その1
// ('/' で終わっていること。パーミッションは '777')

// index.php の中で定数 DATA_HOME の値を変更することにより
// これらのディレクトリをWebブラウザから隠すことができます

define('DATA_DIR',      DATA_HOME . 'wiki/'     ); // 最新のwikiテキスト
define('DIFF_DIR',      DATA_HOME . 'diff/'     ); // 最新のdiff(直前のデータ)
define('BACKUP_DIR',    DATA_HOME . 'backup/'   ); // バックアップデータ
define('CACHE_DIR',     DATA_HOME . 'cache/'    ); // キャッシュデータ
define('UPLOAD_DIR',    DATA_HOME . 'attach/'   ); // 添付ファイルとログ
define('COUNTER_DIR',   DATA_HOME . 'counter/'  ); // counterプラグインのログ
define('TRACKBACK_DIR', DATA_HOME . 'trackback/'); // TrackBackのログ
define('PLUGIN_DIR',    DATA_HOME . 'plugin/'   ); // プラグインを収める場所

/////////////////////////////////////////////////
// ディレクトリ関係の設定その2 ('/' で終わっていること)

// スキン / スタイルシートを格納する場所
define('SKIN_DIR', 'skin/');
//  このディレクトリ以下のスキンファイル (*.php) はPukiWiki本体側
//  (DATA_HOME/SKIN_DIR) に必要ですが、CSSファイル(*.css) および
//  JavaScriptファイル( *.js) はWebブラウザから見える場所
//  (index.php から見て ./SKIN_DIR にあたる場所)に配置して下さい

// 静的な画像ファイルを格納する場所
define('IMAGE_DIR', 'image/');
//  このディレクトリ以下の全てのファイルはWebブラウザから見える
//  場所(index.php から見て ./IMAGE_DIR にあたる場所)に配置して
//  下さい

/////////////////////////////////////////////////
// ローカル時間の設定

switch (LANG) { // または指定する
case 'ja':
	define('ZONE', 'JST');
	define('ZONETIME', 9 * 3600); // JST = GMT + 9
	break;
default  :
	define('ZONE', 'GMT');
	define('ZONETIME', 0);
	break;
}

/////////////////////////////////////////////////
// あなたのWikiサイトの名前 (※命名して下さい)
// この値はRSSフィードのチャンネル名などにも使われます
$page_title = 'PukiWiki';

// このPukiWikiのURL (デフォルト:自動判別)
//$script = 'http://example.com/pukiwiki/';

// $scriptを短くする: ファイル名を取り除く (デフォルト:取り除かない)
//$script_directory_index = 'index.php';

// サイト管理者の名前 (※変更して下さい)
$modifier = 'anonymous';

// サイト管理者のWebページ (※変更して下さい)
$modifierlink = 'http://pukiwiki.example.com/';

// ページ名称
$defaultpage  = 'FrontPage';     // トップページ / 既定のページ
$whatsnew     = 'RecentChanges'; // 変更されたページの一覧
$whatsdeleted = 'RecentDeleted'; // 削除されたページの一覧
$interwiki    = 'InterWikiName'; // InterWikiの定義を行うページ
$menubar      = 'MenuBar';       // メニュー

/////////////////////////////////////////////////
// デフォルトの DTD(Document Type Definition) を変更する

// いくつかのWebブラウザが抱えているバグ、または/および Javaアプレットは
// Strict でないDTDを要求するかもしれません。いくつかのプラグイン(paint
// など)はこの値を PKWK_DTD_XHTML_1_0_TRANSITIONAL に変更します

//$pkwk_dtd = PKWK_DTD_XHTML_1_1; // デフォルト
//$pkwk_dtd = PKWK_DTD_XHTML_1_0_STRICT;
//$pkwk_dtd = PKWK_DTD_XHTML_1_0_TRANSITIONAL;
//$pkwk_dtd = PKWK_DTD_HTML_4_01_STRICT;
//$pkwk_dtd = PKWK_DTD_HTML_4_01_TRANSITIONAL;

/////////////////////////////////////////////////

// PKWK_ALLOW_JAVASCRIPT - JavaScriptの利用を許可/禁止する
define('PKWK_ALLOW_JAVASCRIPT', 0);

/////////////////////////////////////////////////
// TrackBack 機能

// トラックバックを有効にする
$trackback = 0;

// トラックバックの一覧を別画面で表示する (JavaScriptを利用する)
$trackback_javascript = 0;

/////////////////////////////////////////////////
// リファラの一覧を表示する
$referer = 0;

/////////////////////////////////////////////////
// WikiNameに対する自動リンク機能を *無効に* する
$nowikiname = 0;

/////////////////////////////////////////////////
// AutoLink 機能

// AutoLink の対象とするページ名の最低バイト長 (0 = 無効)
$autolink = 8;

/////////////////////////////////////////////////
// 凍結 / 凍結解除 機能
$function_freeze = 1;

/////////////////////////////////////////////////
// このWikiサイトの管理者パスワード

// *変更して下さい*
$adminpass = '1a1dc91c907325c69271ddf0c944bc72'; // md5('pass')

// = 注意 =
//
// 管理者パスワードはMD5ハッシュの形で取り扱われます。
// 設定する方法としては、PHPの md5() 関数を用いる方法と、
// MD5ハッシュを別途算出し、その結果を用いる方法があ
// ります。あなたがコンピュータの操作に充分慣れている
// のであれば、後者をお勧めします。
//
// ----
//
// 例えばパスワードを仮に「pass」とする場合、以下の様に記述する
// ことができます。
//
// $adminpass = md5('pass');	// PHPの md5() 関数を使う方法
//
// ただし、この方法では、この設定ファイルを覗き見ることができる
// (できた) 誰かに、パスワードそのものを知られる高い危険性が
// あります。この危険性を下げるために、md5() 関数の結果だけを
// 記述することができます。
//
// MD5関数の結果(ハッシュ)は0から9の数字と、AからFまでの英字
// からなる32文字の文字列で、この情報だけでは元の文字列を
// 推測することは困難です。
//
// // MD5ハッシュのみを使う方法
// $adminpass = '1a1dc91c907325c69271ddf0c944bc72';
//
// 仮に'pass' のMD5ハッシュを算出するには、Linuxやcygwinであれば
//    $ echo -n 'pass' | md5sum
// の様にして算出する事ができます。('-n' オプションを忘れずに!)
// FreeBSDなどではmd5sumの代わりにmd5コマンドを使ってください。
//
// お勧めできませんが、PukiWikiの 'md5プラグイン' でも算出が可能
// です。
// http://<設置した場所>/index.php?plugin=md5
// このURLにアクセスすることで、MD5ハッシュを算出するための画
// 面が表示されます。しかし、この機能を利用する場合、あなたが
// タイプしたパスワードがネットワークをそのまま流れる可能性が
// あるため、(1)あなたが使っているコンピュータ、(2)サーバーま
// でのネットワーク、(3)サーバー のいずれかが信頼できないので
// あれば、この方法は使わないで下さい。

/////////////////////////////////////////////////
// ページ名に読みがなをつける機能 に関する設定
// (ページ一覧の並び順を正しくするために、漢字仮名まじりのページ
//  名について、自動的に読みがなを生成する)

// ChaSen または KAKASHI コマンドを使って読みがなを得る機能を
// 有効にする (1:有効, 0:無効)
$pagereading_enable = 0;

// コンバーターを指定する: ChaSen('chasen'), KAKASI('kakasi'), なし('none')
$pagereading_kanji2kana_converter = 'none';

// 受け渡すデータのエンコーディングを指定する
$pagereading_kanji2kana_encoding = 'EUC'; // Default for Unix
//$pagereading_kanji2kana_encoding = 'SJIS'; // Default for Windows

// コンバーターの絶対パス (ChaSen)
$pagereading_chasen_path = '/usr/local/bin/chasen';
//$pagereading_chasen_path = 'c:\progra~1\chasen21\chasen.exe';

// コンバーターの絶対パス (KAKASI)
$pagereading_kakasi_path = '/usr/local/bin/kakasi';
//$pagereading_kakasi_path = 'c:\kakasi\bin\kakasi.exe';

// 読みがなを格納するページ名
$pagereading_config_page = ':config/PageReading';

// コンバーターが「なし('none')」である時に使われる、既定の読み
// がなを収めたページ名
$pagereading_config_dict = ':config/PageReading/dict';

/////////////////////////////////////////////////
// ユーザー定義
$auth_users = array(
	'foo'	=> 'foo_passwd',
	'bar'	=> 'bar_passwd',
	'hoge'	=> 'hoge_passwd',
);

/////////////////////////////////////////////////
// 認証方法

// 'pagename' : ページ名により認証を行う
// 'contents' : ページの内容により認証を行う
$auth_method_type = 'contents';

/////////////////////////////////////////////////
// 閲覧認証 (0:無効、1:有効)
$read_auth = 0;

// 閲覧認証をかけるための正規表現
$read_auth_pages = array(
	'#ひきこもるほげ#'	=> 'hoge',
	'#(ネタバレ|ねたばれ)#'	=> 'foo,bar,hoge',
);

/////////////////////////////////////////////////
// 編集認証 (0:無効、1:有効)
$edit_auth = 0;

// 編集認証をかけるための正規表現
$edit_auth_pages = array(
	'#Barの公開日記#'	=> 'bar',
	'#ひきこもるほげ#'	=> 'hoge',
	'#(ネタバレ|ねたばれ)#'	=> 'foo',
);

/////////////////////////////////////////////////
// 検索認証
// 0: 無効 (閲覧禁止であるページの内容も検索する)
// 1: 有効 (そのユーザーに許可されているページのみを検索する)
$search_auth = 0;

/////////////////////////////////////////////////
// $whatsnew: RecentChangesの最大項目数
$maxshow = 60;

// $whatsdeleted: RecentDeletedの最大項目数 (0 = 無効)
$maxshow_deleted = 60;

/////////////////////////////////////////////////
// 編集を禁止するページ名
$cantedit = array( $whatsnew, $whatsdeleted );

/////////////////////////////////////////////////
// HTTP: Last-Modified ヘッダを出力する
$lastmod = 0;

/////////////////////////////////////////////////
// 日付のフォーマット
$date_format = 'Y-m-d';

// 時間のフォーマット
$time_format = 'H:i:s';

/////////////////////////////////////////////////
// RSSフィードの最大項目数
$rss_max = 15;

/////////////////////////////////////////////////
// バックアップ関係の設定

// バックアップ機能を有効にする
$do_backup = 1;

// ページが削除された時に、そのバックアップも削除するか?
$del_backup = 0;

// バックアップの間隔と世代
$cycle  =   3; // 何時間ごとにバックアップするか (0 = 常に行う)
$maxage = 120; // 何世代までのバックアップを保存するか

// 参考: $cycle x $maxage / 24 = データを失うまでの最短日数
//          3   x   120   / 24 = 15

// バックアップデータの中身を区切る文字列 (注意: 変更するのは危険すぎる!)
define('PKWK_SPLITTER', '>>>>>>>>>>');

/////////////////////////////////////////////////
// 更新される度に実行するコマンド
$update_exec = '';
//$update_exec = '/usr/bin/mknmz --media-type=text/pukiwiki -O /var/lib/namazu/index/ -L ja -c -K /var/www/wiki/';

/////////////////////////////////////////////////
// プロキシの設定 (TrackBackなどが用いる)

// 他のサイトからデータを得るためにHTTPプロキシサーバーを経由する
$use_proxy = 0;

$proxy_host = 'proxy.example.com';
$proxy_port = 8080;

// ベーシック認証を行う
$need_proxy_auth = 0;
$proxy_auth_user = 'username';
$proxy_auth_pass = 'password';

// プロキシサーバーを必要としないホスト
$no_proxy = array(
	'localhost',	// localhost
	'127.0.0.0/8',	// loopback
//	'10.0.0.0/8'	// private class A
//	'172.16.0.0/12'	// private class B
//	'192.168.0.0/16'	// private class C
//	'no-proxy.com',
);

////////////////////////////////////////////////
// 電子メール関連の設定

// ページが更新される度にメールを送る
$notify = 0;

// 差分データのみを送る
$notify_diff_only = 1;

// SMTP サーバー (Windows環境のみ。通常は php.ini で定義されている)
$smtp_server = 'localhost';

// 宛先(To:)と送信者(From:)
$notify_to   = 'to@example.com';	// To:
$notify_from = 'from@example.com';	// From:

// Subject: ($page = 更新されたページの名前に置換される)
$notify_subject = '[PukiWiki] $page';

// メールヘッダ
$notify_header = "From: $notify_from\r\n" .
	'X-Mailer: PukiWiki/' .  S_VERSION . ' PHP/' . phpversion();

/////////////////////////////////////////////////
// 電子メール: POP / APOP Before SMTP

// メールを送る前に POP/APOP 認証を行う
$smtp_auth = 0;

$pop_server = 'localhost';
$pop_port   = 110;
$pop_userid = '';
$pop_passwd = '';

// POPの変わりにAPOPを用いる (もしサーバーが対応していれば)
//   Default = 自動 (可能ならAPOPを用いる)
//   1       = 常に APOP を用いる
//   0       = 常に  POP を用いる
// $pop_auth_use_apop = 1;

/////////////////////////////////////////////////
// 無視するページのリスト

// 無視するページの正規表現
$non_list = '^\:';

// 無視するページを検索するかどうか
$search_non_list = 1;

/////////////////////////////////////////////////
// テンプレートの設定

$auto_template_func = 1;
$auto_template_rules = array(
	'((.+)\/([^\/]+))' => '\2/template'
);

/////////////////////////////////////////////////
// 見出しに既定の形式でアンカー(タグ)を自動挿入する
$fixed_heading_anchor = 1;

/////////////////////////////////////////////////
// 「整形済みテキスト」から先頭のスペースを取り除く
$preformat_ltrim = 1;

/////////////////////////////////////////////////
// 改行を <br /> タグに置換する
$line_break = 0;

/////////////////////////////////////////////////
// ユーザーエージェント設定
//
// もしもリッチコンテンツを含んだWikiサイトとして組み込みブラウザを
// サポートしたく無いのであれば、'keitai' に関する設定を全て削除
// (ないしコメントアウト)して下さい。
//
// もしkeitaiスキンを使用した、簡素なWikiサイトとして利用したいので
// あれば、 keitai.ini.php を default.ini.php にコピーし、中身を
// デスクトップPC向けにカスタマイズして下さい。

$agents = array(
// pattern: A regular-expression that matches device(browser)'s name and version
// profile: A group of browsers

    // Embedded browsers (Rich-clients for PukiWiki)

	// Windows CE (Microsoft(R) Internet Explorer 5.5 for Windows(R) CE)
	// Sample: "Mozilla/4.0 (compatible; MSIE 5.5; Windows CE; sigmarion3)" (sigmarion, Hand-held PC)
	array('pattern'=>'#\b(?:MSIE [5-9]).*\b(Windows CE)\b#', 'profile'=>'default'),

	// ACCESS "NetFront" / "Compact NetFront" and thier OEM, expects to be "Mozilla/4.0"
	// Sample: "Mozilla/4.0 (PS2; PlayStation BB Navigator 1.0) NetFront/3.0" (PlayStation BB Navigator, for SONY PlayStation 2)
	// Sample: "Mozilla/4.0 (PDA; PalmOS/sony/model crdb/Revision:1.1.19) NetFront/3.0" (SONY Clie series)
	// Sample: "Mozilla/4.0 (PDA; SL-A300/1.0,Embedix/Qtopia/1.1.0) NetFront/3.0" (SHARP Zaurus)
	array('pattern'=>'#^(?:Mozilla/4).*\b(NetFront)/([0-9\.]+)#',	'profile'=>'default'),

    // Embedded browsers (Non-rich)

	// Windows CE (the others)
	// Sample: "Mozilla/2.0 (compatible; MSIE 3.02; Windows CE; 240x320 )" (GFORT, NTT DoCoMo)
	array('pattern'=>'#\b(Windows CE)\b#', 'profile'=>'keitai'),

	// ACCESS "NetFront" / "Compact NetFront" and thier OEM
	// Sample: "Mozilla/3.0 (AveFront/2.6)" ("SUNTAC OnlineStation", USB-Modem for PlayStation 2)
	// Sample: "Mozilla/3.0(DDIPOCKET;JRC/AH-J3001V,AH-J3002V/1.0/0100/c50)CNF/2.0" (DDI Pocket: AirH" Phone by JRC)
	array('pattern'=>'#\b(NetFront)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(CNF)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(AveFront)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(AVE-Front)/([0-9\.]+)#',	'profile'=>'keitai'), // The same?

	// NTT-DoCoMo, i-mode (embeded Compact NetFront) and FOMA (embedded NetFront) phones
	// Sample: "DoCoMo/1.0/F501i", "DoCoMo/1.0/N504i/c10/TB/serXXXX" // c以降は可変
	// Sample: "DoCoMo/2.0 MST_v_SH2101V(c100;TB;W22H12;serXXXX;iccxxxx)" // ()の中は可変
	array('pattern'=>'#^(DoCoMo)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Vodafone's embedded browser
	// Sample: "J-PHONE/2.0/J-T03"	// 2.0は"ブラウザの"バージョン
	// Sample: "J-PHONE/4.0/J-SH51/SNxxxx SH/0001a Profile/MIDP-1.0 Configuration/CLDC-1.0 Ext-Profile/JSCL-1.1.0"
	array('pattern'=>'#^(J-PHONE)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Openwave(R) Mobile Browser (EZweb, WAP phone, etc)
	// Sample: "OPWV-SDK/62K UP.Browser/6.2.0.5.136 (GUI) MMP/2.0"
	array('pattern'=>'#\b(UP\.Browser)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Opera, dressing up as other embedded browsers
	// Sample: "Mozilla/3.0(DDIPOCKET;KYOCERA/AH-K3001V/1.4.1.67.000000/0.1/C100) Opera 7.0" (Like CNF at 'keitai'-mode)
	array('pattern'=>'#\bDDIPOCKET\b.+\b(Opera) ([0-9\.]+)\b#',	'profile'=>'keitai'),

	// Planetweb http://www.planetweb.com/
	// Sample: "Mozilla/3.0 (Planetweb/v1.07 Build 141; SPS JP)" ("EGBROWSER", Web browser for PlayStation 2)
	array('pattern'=>'#\b(Planetweb)/v([0-9\.]+)#', 'profile'=>'keitai'),

	// DreamPassport, Web browser for SEGA DreamCast
	// Sample: "Mozilla/3.0 (DreamPassport/3.0)"
	array('pattern'=>'#\b(DreamPassport)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Palm "Web Pro" http://www.palmone.com/us/support/accessories/webpro/
	// Sample: "Mozilla/4.76 [en] (PalmOS; U; WebPro)"
	array('pattern'=>'#\b(WebPro)\b#',	'profile'=>'keitai'),

	// ilinx "Palmscape" / "Xiino" http://www.ilinx.co.jp/
	// Sample: "Xiino/2.1SJ [ja] (v. 4.1; 153x130; c16/d)"
	array('pattern'=>'#^(Palmscape)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#^(Xiino)/([0-9\.]+)#',	'profile'=>'keitai'),

	// SHARP PDA Browser (SHARP Zaurus)
	// Sample: "sharp pda browser/6.1[ja](MI-E1/1.0) "
	array('pattern'=>'#^(sharp [a-z]+ browser)/([0-9\.]+)#',	'profile'=>'keitai'),

	// WebTV
	array('pattern'=>'#^(WebTV)/([0-9\.]+)#',	'profile'=>'keitai'),

    // Desktop-PC browsers

	// Opera (for desktop PC, not embedded) -- See BugTrack/743 for detail
	// NOTE: Keep this pattern above MSIE and Mozilla
	// Sample: "Opera/7.0 (OS; U)" (not disguise)
	// Sample: "Mozilla/4.0 (compatible; MSIE 5.0; OS) Opera 6.0" (disguise)
	array('pattern'=>'#\b(Opera)[/ ]([0-9\.]+)\b#',	'profile'=>'default'),

	// MSIE: Microsoft Internet Explorer (or something disguised as MSIE)
	// Sample: "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)"
	array('pattern'=>'#\b(MSIE) ([0-9\.]+)\b#',	'profile'=>'default'),

	// Mozilla Firefox
	// NOTE: Keep this pattern above Mozilla
	// Sample: "Mozilla/5.0 (Windows; U; Windows NT 5.0; ja-JP; rv:1.7) Gecko/20040803 Firefox/0.9.3"
	array('pattern'=>'#\b(Firefox)/([0-9\.]+)\b#',	'profile'=>'default'),

    	// Loose default: Including something Mozilla
	array('pattern'=>'#^([a-zA-z0-9 ]+)/([0-9\.]+)\b#',	'profile'=>'default'),

	array('pattern'=>'#^#',	'profile'=>'default'),	// Sentinel
);
?>
