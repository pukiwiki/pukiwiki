<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: pukiwiki.ini.php,v 1.79 2004/09/04 00:02:03 henoheno Exp $
//
// PukiWiki setting file

/////////////////////////////////////////////////
// ディレクトリ指定 最後に / が必要 属性は 777

// データ格納ディレクトリ
define('DATA_DIR',      DATA_HOME . 'wiki/');	// 最新のデータ
define('DIFF_DIR',      DATA_HOME . 'diff/');	// 差分ファイル
define('BACKUP_DIR',    DATA_HOME . 'backup/');	// バックアップ
define('CACHE_DIR',     DATA_HOME . 'cache/');	// キャッシュ
define('UPLOAD_DIR',    DATA_HOME . 'attach/');	// 添付ファイル
define('COUNTER_DIR',   DATA_HOME . 'counter/');	// カウンタ
define('TRACKBACK_DIR', DATA_HOME . 'trackback/');	// TrackBack
define('PLUGIN_DIR',    DATA_HOME . 'plugin/'); // プラグインファイル

/////////////////////////////////////////////////
// ディレクトリ指定 最後に / が必要
//
//  PukiWiki本体をWebブラウザからアクセスできない
//  場所に設置するときは、以下のディレクトリにある
//  ファイルの一部を Webブラウザからアクセスできる
//  場所に設置する必要があります。
//  (無くとも動作はしますが、少々味気なくなるでしょう)

// スキン/スタイルシートファイル格納ディレクトリ
define('SKIN_DIR', 'skin/');
//  このディレクトリ以下のスキンファイル (*.php) は
//  PukiWiki本体側(DATA_HOME/SKIN_DIR) に必要ですが、
//  CSSファイル(*.css) およびJavaScriptファイル( *.js)
//  はWebブラウザから見える場所(./SKIN_DIR)に配置
//  して下さい

// 画像ファイル格納ディレクトリ
define('IMAGE_DIR', 'image/');
//  このディレクトリ以下の全てのファイルは
//  Webブラウザから見える場所(./IMAGE_DIR)に配置
//  して下さい


/////////////////////////////////////////////////
// ローカル時間
define('ZONE','JST');
define('ZONETIME',9 * 3600); // JST = GMT+9

/////////////////////////////////////////////////
// ホームページのタイトル(修正してください)
// * RSS に出力するチャンネル名を兼ねる
$page_title = 'PukiWiki';

// index.php などに変更した場合のスクリプト名の設定
// とくに設定しなくても問題なし
//$script = 'http://example.com/pukiwiki/';

// 編集者の名前(修正してください)
$modifier = 'anonymous';

// 編集者のホームページ(修正してください)
$modifierlink = 'http://pukiwiki.example.com/';

// デフォルトのページ名
$defaultpage  = 'FrontPage';	// トップページ (ページを指定しないとき)
$whatsnew     = 'RecentChanges';	// 更新履歴
$whatsdeleted = 'RecentDeleted';	// 削除履歴
$interwiki    = 'InterWikiName';	// InterWikiName の一覧を書くページ
$menubar      = 'MenuBar';	// メニューとして表示させる内容を書くページ

/////////////////////////////////////////////////
// TrackBack機能を使用する
$trackback = 0;

/////////////////////////////////////////////////
// Referer機能を使用する
$referer = 0;

/////////////////////////////////////////////////
// WikiNameを *無効に* する
$nowikiname = 0;

/////////////////////////////////////////////////
// AutoLinkを有効にする場合は、AutoLink対象となる
// ページ名の最短バイト数を指定
// AutoLinkを無効にする場合は0
$autolink = 0;

/////////////////////////////////////////////////
// 凍結機能を有効にする
$function_freeze = 1;

/////////////////////////////////////////////////
// 管理者パスワード

// 以下は md5('pass') の出力結果です
$adminpass = '1a1dc91c907325c69271ddf0c944bc72';

// = 注意 =
//
// パスワードを設定する方法として、md5()関数を使う方法と、
// md5()関数の結果を別途算出して使う方法があります。
// あなたがコンピュータの操作に充分慣れているのであれば、
// 後者をお勧めします。
//
// 例えばパスワードを「pass」としたい場合、以下の様に記述する
// ことができます。
//
// $adminpass = md5('pass');	// md5() 関数を使う方法
//
// ただし、この方法では、このファイルを覗き見ることができる
// (できた) 誰かに、パスワードそのものを知られる高い危険性が
// あります。この危険性を下げるために、md5()関数の結果だけを
// 記述することができます。
//
// md5()関数の結果(MD5ハッシュ)は0から9の数字と、AからFまで
// の英字からなる32文字の文字列で、この情報だけでは元の文字列を
// 推測することは困難です。
//
// MD5ハッシュは、Linuxやcygwinであれば
//
//    $ echo -n 'pass' | md5sum
//
// の様にして計算させる事ができます。('-n' オプションを忘れずに!)
// FreeBSDなどでは md5sum の代わりに md5 コマンドを使ってください。
//
// お勧めできませんが、PukiWikiのmd5コマンドでも算出が可能です。
//
// http://<設置した場所>/pukiwiki.php?md5=pass
//
// このURLにアクセスすることで、算出結果が表示されます。その
// かわり、あなたがタイプしたパスワードはネットワークを流れ、
// 誰にでも覗き見ができ、Webサーバーのログにも残ってしまう、
// といった様々なリスクを負う可能性があります。あなたが使って
// いるコンピュータ、サーバーまでのネットワーク、サーバーの
// どこかが信頼できないのであれば、この方法は使わないで下さい。

/////////////////////////////////////////////////
// ChaSen, KAKASI による、ページ名の読みの取得 (0:無効,1:有効)
$pagereading_enable = 0;

// ChaSen('chasen') or KAKASI('kakasi') or None('none')
$pagereading_kanji2kana_converter = 'none';

// ChaSen/KAKASI との受け渡しに使う漢字コード (UNIX系は EUC、Win系は SJIS が基本)
$pagereading_kanji2kana_encoding = 'EUC';
//$pagereading_kanji2kana_encoding = 'SJIS';

// ChaSen/KAKASI の実行ファイル (各自の環境に合わせて設定)
$pagereading_chasen_path = '/usr/local/bin/chasen';
//$pagereading_chasen_path = 'c:\progra~1\chasen21\chasen.exe';

$pagereading_kakasi_path = '/usr/local/bin/kakasi';
//$pagereading_kakasi_path = 'c:\kakasi\bin\kakasi.exe';

// ページ名読みを格納したページの名前
$pagereading_config_page = ':config/PageReading';

// converter ='none' の場合の読み仮名辞書
$pagereading_config_dict = ':config/PageReading/dict';

/////////////////////////////////////////////////
// ユーザ定義
$auth_users = array(
	'foo'	=> 'foo_passwd',
	'bar'	=> 'bar_passwd',
	'hoge'	=> 'hoge_passwd',
);

/////////////////////////////////////////////////
// 認証方式種別
// 'pagename' : ページ名
// 'contents' : ページ内容
$auth_method_type = 'contents';

/////////////////////////////////////////////////
// 閲覧認証フラグ (0:不要 1:必要)
$read_auth = 0;

// 閲覧認証対象パターン定義
$read_auth_pages = array(
	'/ひきこもるほげ/'	=> 'hoge',
	'/(ネタバレ|ねたばれ)/'	=> 'foo,bar,hoge',
);

/////////////////////////////////////////////////
// 編集認証フラグ (0:不要 1:必要)
$edit_auth = 0;

// 編集認証対象パターン定義
$edit_auth_pages = array(
	'/Barの公開日記/'	=> 'bar',
	'/ひきこもるほげ/'	=> 'hoge',
	'/(ネタバレ|ねたばれ)/'	=> 'foo',
);

/////////////////////////////////////////////////
// 検索認証フラグ
// 0: 閲覧が許可されていないページ内容も検索対象とする
// 1: 検索時のログインユーザに許可されたページのみ検索対象とする
$search_auth = 0;

/////////////////////////////////////////////////
// $whatsnew: 更新履歴を表示するときの最大件数
$maxshow = 60;

// $whatsdeleted: 削除履歴の最大件数(0で記録しない)
$maxshow_deleted = 60;

/////////////////////////////////////////////////
// 編集することのできないページの名前 , で区切る
$cantedit = array( $whatsnew, $whatsdeleted );

/////////////////////////////////////////////////
// Last-Modified ヘッダを出力する
$lastmod = 0;

/////////////////////////////////////////////////
// 日付フォーマット
$date_format = 'Y-m-d';

// 時刻フォーマット
$time_format = 'H:i:s';

// 曜日配列
$weeklabels = $_msg_week;

/////////////////////////////////////////////////
// RSS に出力するページ数
$rss_max = 15;

/////////////////////////////////////////////////
// バックアップを行う
$do_backup = 1;

// ページを削除した際にバックアップもすべて削除する
$del_backup = 0;

// バックアップ間隔と世代数
$cycle  = 3;	// 直前の修正から何時間経過していたらバックアップするか (0で更新毎)
$maxage = 120;	// 世代数

// NOTE: $cycle x $maxage / 24 = データを失うために最低限必要な日数
//          3   x   120   / 24 = 15

// バックアップの世代を区切る文字列
$splitter = ">>>>>>>>>>";

/////////////////////////////////////////////////
// ページの更新時にバックグランドで実行するコマンド(mknmzなど)
$update_exec = '';
//$update_exec = '/usr/bin/mknmz --media-type=text/pukiwiki -O /var/lib/namazu/index/ -L ja -c -K /var/www/wiki/';

/////////////////////////////////////////////////
// HTTPリクエストにプロキシサーバを使用する
$use_proxy = 0;

$proxy_host = 'proxy.example.com'; // proxyサーバ名
$proxy_port = 8080; // ポート番号

// Basic認証を行う
$need_proxy_auth = 0;
$proxy_auth_user = 'username';	// ユーザー名
$proxy_auth_pass = 'password';	// パスワード

// プロキシサーバを使用しないホストのリスト
$no_proxy = array(
	'localhost',	// localhost
	'127.0.0.0/8',	// loopback
//	'10.0.0.0/8'	// private class A
//	'172.16.0.0/12'	// private class B
//	'192.168.0.0/16'	// private class C
//	'no-proxy.com',
);

////////////////////////////////////////////////
// メール送信

$notify = 0;	// (1:ページの更新時にメールを送信する)
$notify_diff_only = 0;	// (1:差分だけを送信する)

// SMTPサーバ (Windows のみ, 通常は php.ini で指定)
$smtp_server = 'localhost';

$notify_to   = 'to@example.com';	// To:（宛先）
$notify_from = 'from@example.com';	// From:（送り主）

// Subject:（件名） $pageにページ名が入ります
$notify_subject = '[pukiwiki] $page';

// メールヘッダ
$notify_header = "From: $notify_from\r\n" .
	'X-Mailer: PukiWiki/' .  S_VERSION . ' PHP/' . phpversion();

/////////////////////////////////////////////////
// メール送信: POP / APOP Before SMTP

// メール送信前にPOPまたはAPOPによる認証を行う
$smtp_auth = 0;

$pop_server = 'localhost';	// POPサーバ
$pop_port   = 110;	// ポート番号
$pop_userid = '';	// POPユーザ名
$pop_passwd = '';	// POPパスワード

// 認証に APOP を利用するかどうか (※サーバ側の対応が必要)
//   未設定 = 自動 (可能であればAPOPを使用する)
//   1 = APOP固定  (必ずAPOPを使用する)
//   0 = POP固定   (必ずPOPを使用する)
// $pop_auth_use_apop = 1;

/////////////////////////////////////////////////
// 一覧・更新一覧に含めないページ名(正規表現で)
$non_list = '^\:';

// $non_listを文字列検索の対象ページとするか
// 0にすると、上記ページ名が単語検索からも除外されます。
$search_non_list = 1;

/////////////////////////////////////////////////
// ページ名に従って自動で、雛形とするページの読み込み
$auto_template_func = 1;
$auto_template_rules = array(
	'((.+)\/([^\/]+))' => '\2/template'
);

/////////////////////////////////////////////////
// 見出し行に固有のアンカーを自動挿入する
$fixed_heading_anchor = 1;

/////////////////////////////////////////////////
// <pre>の行頭スペースをひとつ取り除く
$preformat_ltrim = 1;

/////////////////////////////////////////////////
// 改行を反映する(改行を<br />に置換する)
$line_break = 0;

/////////////////////////////////////////////////
// ユーザーエージェント対応設定
//
// リッチクライアントを前提としたサイトを構築する
// ために、携帯電話などに意図的に非対応としたい場合、
// 最後のデフォルト設定以外の行を全て削除あるいは
// コメントアウトして下さい。
//
// デザインやスタイルを簡素なkeitaiプロファイルに
// 統一したい時は、デフォルト設定以外の行を全て削除
// あるいはコメントアウトした後に、デフォルト設定を
// 'profile'=>'keitai' と修正して下さい。

$agents = array(
// pattern: デバイス[ブラウザ]名およびバージョンの検出パターン  profile: 所属するグループ

    // 組み込みブラウザ (リッチクライアント:PukiWikiがそのまま使えるという意味の)

	// "PlayStation BB Navigator" (ACCESS NetFront, for SONY PlayStation 2)
	// Sample: "Mozilla/4.0 (PS2; PlayStation BB Navigator 1.0) NetFront/3.0"
	array('pattern'=>'#\bPlayStation\b.*\b(NetFront)/([0-9\.]+)#',	'profile'=>'default'),

    // 組み込みブラウザ (リッチクライアントではないもの)

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

	// ACCESS "NetFront" / "Compact NetFront" and thier OEM
	// Sample: "Mozilla/4.0 (PDA; PalmOS/sony/model crdb/Revision:1.1.19) NetFront/3.0" (SONY Clie series)
	// Sample: "Mozilla/4.0 (PDA; SL-A300/1.0,Embedix/Qtopia/1.1.0) NetFront/3.0" (Sharp Zaurus)
	// Sample: "Mozilla/3.0 (AveFront/2.6)" ("SUNTAC OnlineStation", USB-Modem for PlayStation 2)
	// Sample: "Mozilla/3.0(DDIPOCKET;JRC/AH-J3001V,AH-J3002V/1.0/0100/c50)CNF/2.0" (DDI Pocket: AirH" Phone by JRC)
	array('pattern'=>'#\b(NetFront)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(CNF)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(AveFront)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(AVE-Front)/([0-9\.]+)#',	'profile'=>'keitai'), // The same?

	// Opera, dressing up as other embedded browsers
	// Sample: "Mozilla/3.0(DDIPOCKET;KYOCERA/AH-K3001V/1.4.1.67.000000/0.1/C100) Opera 7.0" (Like CNF at 'keitai'-mode)
	array('pattern'=>'#\bDDIPOCKET\b.+\b(Opera) ([0-9\.]+)\b#',	'profile'=>'keitai'),

	// Planetweb http://www.planetweb.com/
	// Sample: "Mozilla/3.0 (Planetweb/v1.07 Build 141; SPS JP)" ("EGBROWSER", Web browser for PlayStation 2)
	array('pattern'=>'#\b(Planet[Ww]eb)/[a-z]?([0-9\.]+)#',	'profile'=>'keitai'),

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

	// Sharp PDA Browser (Sharp Zaurus)
	// Sample: "sharp pda browser/6.1[ja](MI-E1/1.0) "
	array('pattern'=>'#^(sharp [a-z]+ browser)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Windows CE
	// Sample: "Mozilla/4.0 (compatible; MSIE 5.5; Windows CE; sigmarion3)" (sigmarion, Hand-held PC)
	array('pattern'=>'#\b(Windows CE)\b#',	'profile'=>'keitai'),

	// WebTV
	array('pattern'=>'#^(WebTV)/([0-9\.]+)#',	'profile'=>'keitai'),

    // デスクトップあるいはリッチクライアント (デバイスを識別する必要がないもの)

    	// デフォルト設定
	array('pattern'=>'#^#',	'profile'=>'default'),	// default

);
?>
