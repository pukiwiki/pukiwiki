<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: amazon.inc.php,v 1.16 2011/01/25 15:01:01 henoheno Exp $
// Id: amazon.inc.php,v 1.1 2003/07/24 13:00:00 閑舎
//
// Amazon plugin: Book-review maker via amazon.com/amazon.jp
//
// Copyright:
//	2004-2005 PukiWiki Developers Team
//	2003 閑舎 <raku@rakunet.org> (Original author)
//
// License: GNU/GPL
//
// ChangeLog:
// * 2004/04/03 PukiWiki Developer Team (arino <arino@users.sourceforge.jp>)
//        - replace plugin_amazon_get_page().
//        - PLUGIN_AMAZON_XML 'xml.amazon.com' -> 'xml.amazon.co.jp'
// * 0.6  URL が存在しない場合、No image を表示、画像配置など修正。
//        インラインプラグインの呼び出し方を修正。
//	  ASIN 番号部分をチェックする。
//	  画像、タイトルのキャッシュによる速度の大幅アップ。
// * 0.7  ブックレビュー生成のデバッグ、認証問題の一応のクリア。
// * 0.8  amazon 全商品の画像を表示。
//	  アソシエイト ID に対応。
// * 0.9  RedHat9+php4.3.2+apache2.0.46 で画像が途中までしか読み込まれない問題に対処。
//        日本語ページの下にブックレビューを作ろうとすると文字化けして作れない問題の解決。
//        書籍でなく CD など、ASIN 部分が長くてもタイトルをうまく拾うようにする。
//        写影のみ取り込むのでなければ、B000002G6J.01 と書かず B000002G6J と書いても写影が出るようにする。
//	  ASIN に対応するキャッシュ画像/キャッシュタイトルをそれぞれ削除する機能追加。
//	  proxy 対応(試験的)。
//	  proxy 実装の過程で一般ユーザのための AID はなくとも自動生成されることがわかり、削除した。
// * 1.0  ブックレビューでなく、レビューとする。
//        画像のキャッシュを削除する期限を設ける。
//        タイトル、写影を Web Services の XML アクセスの方法によって get することで時間を短縮する。
//        レビューページ生成のタイミングについて注を入れる。
// * 1.1  編集制限をかけている場合、部外者がレビューを作ろうとして、ページはできないが ASIN4774110655.tit などのキャッシュができるのを解決。
//        画像の最後が 01 の場合、image を削除すると noimage.jpg となってしまうバグを修正。
//        1.0 で導入した XML アクセスは高速だが、返す画像情報がウソなので、09 がだめなら 01 をトライする、で暫定的に解決。
//
// Caution!:
// * 著作権が関連する為、www.amazon.co.jp のアソシエイトプログラムを確認の上ご利用下さい。
// * レビューは、amazon プラグインが呼び出す編集画面はもう出来て PukiWiki に登録されているので、
//   中止するなら全文を削除してページの更新ボタンを押すこと。
// * 下の PLUGIN_AMAZON_AID、PROXY サーバの部分、expire の部分を適当に編集して使用してください(他はそのままでも Ok)。
//
// Thanks to: Reimy and PukiWiki Developers Team
//

/////////////////////////////////////////////////
// Settings

// Amazon associate ID
//define('PLUGIN_AMAZON_AID',''); // None
define('PLUGIN_AMAZON_AID','');

// Expire caches per ? days
define('PLUGIN_AMAZON_EXPIRE_IMAGECACHE',   1);
define('PLUGIN_AMAZON_EXPIRE_TITLECACHE', 356);

// Alternative image for 'Image not found'
define('PLUGIN_AMAZON_NO_IMAGE', IMAGE_DIR . 'noimage.png');

// URI prefixes
switch(LANG){
case 'ja':
	// Amazon shop
	define('PLUGIN_AMAZON_SHOP_URI', 'http://www.amazon.co.jp/exec/obidos/ASIN/');

	// Amazon information inquiry (dev-t = default value in the manual)
	define('PLUGIN_AMAZON_XML', 'http://xml.amazon.co.jp/onca/xml3?t=webservices-20&' .
		'dev-t=GTYDRES564THU&type=lite&page=1&f=xml&locale=jp&AsinSearch=');
	break;
default:
	// Amazon shop
	define('PLUGIN_AMAZON_SHOP_URI', 'http://www.amazon.com/exec/obidos/ASIN/');

	// Amazon information inquiry (dev-t = default value in the manual)
	define('PLUGIN_AMAZON_XML', 'http://xml.amazon.com/onca/xml3?t=webservices-20&' .
		'dev-t=GTYDRES564THU&type=lite&page=1&f=xml&locale=us&AsinSearch=');
	break;
}

/////////////////////////////////////////////////

function plugin_amazon_init()
{
	global $amazon_aid, $amazon_body;

	if (PLUGIN_AMAZON_AID == '') {
		$amazon_aid = '';
	} else {
		$amazon_aid = PLUGIN_AMAZON_AID . '/';
	}
	$amazon_body = <<<EOD
-作者: [[ここ編集のこと]]
-評者: お名前
-日付: &date;
**お薦め対象
[[ここ編集のこと]]

#amazon(,clear)
**感想
[[ここ編集のこと]]

// まず、このレビューを止める場合、全文を削除し、ページの[更新ボタン]を押してください！(PukiWiki にはもう登録されています)
// 続けるなら、上の、[[ここ編集のこと]]部分を括弧を含めて削除し、書き直してください。
// お名前、部分はご自分の名前に変更してください。私だと、閑舎、です。
// **お薦め対象、より上は、新しい行を追加しないでください。目次作成に使用するので。
// //で始まるコメント行は、最終的に全部カットしてください。目次が正常に作成できない可能性があります。
#comment
EOD;
}

function plugin_amazon_convert()
{
	global $script, $vars, $asin, $asin_all;

	if (func_num_args() > 3) {
		if (PKWK_READONLY) return ''; // Show nothing

		return '#amazon([ASIN-number][,left|,right]' .
			'[,book-title|,image|,delimage|,deltitle|,delete])';

	} else if (func_num_args() == 0) {
		// レビュー作成
		if (PKWK_READONLY) return ''; // Show nothing

		$s_page = htmlsc($vars['page']);
		if ($s_page == '') $s_page = isset($vars['refer']) ? $vars['refer'] : '';
		$ret = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="amazon" />
  <input type="hidden" name="refer" value="$s_page" />
  ASIN:
  <input type="text" name="asin" size="30" value="" />
  <input type="submit" value="レビュー編集" /> (ISBN 10 桁 or ASIN 12 桁)
 </div>
</form>
EOD;
		return $ret;
	}

	$aryargs = func_get_args();

	$align = strtolower($aryargs[1]);
	if ($align == 'clear') return '<div style="clear:both"></div>'; // 改行挿入
	if ($align != 'left') $align = 'right'; // 配置決定

	$asin_all = htmlsc($aryargs[0]);  // for XSS
	if (is_asin() == FALSE && $align != 'clear') return FALSE;

	if ($aryargs[2] != '') {
		// タイトル指定
		$title = $alt = htmlsc($aryargs[2]); // for XSS
		if ($alt == 'image') {
			$alt = plugin_amazon_get_asin_title();
			if ($alt == '') return FALSE;
			$title = '';
		} else if ($alt == 'delimage') {
			if (unlink(CACHE_DIR . 'ASIN' . $asin . '.jpg')) {
				return 'Image of ' . $asin . ' deleted...';
			} else {
				return 'Image of ' . $asin . ' NOT DELETED...';
			}
		} elseif ($alt == 'deltitle') {
			if (unlink(CACHE_DIR . 'ASIN' . $asin . '.tit')) {
				return 'Title of ' . $asin . ' deleted...';
			} else {
				return 'Title of ' . $asin . ' NOT DELETED...';
			}
		} elseif ($alt == 'delete') {
			if ((unlink(CACHE_DIR . 'ASIN' . $asin . '.jpg') &&
			     unlink(CACHE_DIR . 'ASIN' . $asin . '.tit'))) {
				return 'Title and Image of ' . $asin . ' deleted...';
			} else {
				return 'Title and Image of ' . $asin . ' NOT DELETED...';
			}
		}
	} else {
		// タイトル自動取得
		$alt = $title = plugin_amazon_get_asin_title();
		if ($alt == '') return FALSE;
	}

	return plugin_amazon_print_object($align, $alt, $title);
}

function plugin_amazon_action()
{
	global $vars, $script, $edit_auth, $edit_auth_users;
	global $amazon_body, $asin, $asin_all;

	if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');

	$s_page   = isset($vars['refer']) ? $vars['refer'] : '';
	$asin_all = isset($vars['asin']) ?
		htmlsc(rawurlencode(strip_bracket($vars['asin']))) : '';

	if (! is_asin()) {
		$retvars['msg']   = 'ブックレビュー編集';
		$retvars['refer'] = & $s_page;
		$retvars['body']  = plugin_amazon_convert();
		return $retvars;

	} else {
		$r_page     = $s_page . '/' . $asin;
		$r_page_url = rawurlencode($r_page);
		$auth_user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';

		pkwk_headers_sent();
		if ($edit_auth && ($auth_user == '' || ! isset($edit_auth_users[$auth_user]) ||
		    $edit_auth_users[$auth_user] != $_SERVER['PHP_AUTH_PW'])) {
		    	// Edit-auth failed. Just look the page
			header('Location: ' . get_script_uri() . '?' . $r_page_url);
		} else {
			$title = plugin_amazon_get_asin_title();
			if ($title == '' || preg_match('#^/#', $s_page)) {
				// Invalid page name
				header('Location: ' . get_script_uri() . '?' . rawurlencode($s_page));
			} else {
				$body = '#amazon(' . $asin_all . ',,image)' . "\n" .
					'*' . $title . "\n" . $amazon_body;
				plugin_amazon_review_save($r_page, $body);
				header('Location: ' . get_script_uri() .
					'?cmd=edit&page=' . $r_page_url);
			}
		}
		exit;
	}
}

function plugin_amazon_inline()
{
	global $amazon_aid, $asin, $asin_all;

	list($asin_all) = func_get_args();

	$asin_all = htmlsc($asin_all); // for XSS
	if (! is_asin()) return FALSE;

	$title = plugin_amazon_get_asin_title();
	if ($title == '') {
		return FALSE;
	} else {
		return '<a href="' . PLUGIN_AMAZON_SHOP_URI .
			$asin . '/' . $amazon_aid . 'ref=nosim">' . $title . '</a>' . "\n";
	}
}

function plugin_amazon_print_object($align, $alt, $title)
{
	global $amazon_aid;
	global $asin, $asin_ext, $asin_all;

	$url      = plugin_amazon_cache_image_fetch(CACHE_DIR);
	$url_shop = PLUGIN_AMAZON_SHOP_URI . $asin . '/' . $amazon_aid . 'ref=nosim';
	$center   = 'text-align:center';

	if ($title == '') {
		// Show image only
		$div  = '<div style="float:' . $align . ';margin:16px 16px 16px 16px;' . $center . '">' . "\n";
		$div .= ' <a href="' . $url_shop . '"><img src="' . $url . '" alt="' . $alt . '" /></a>' . "\n";
		$div .= '</div>' . "\n";

	} else {
		// Show image and title
		$div  = '<div style="float:' . $align . ';padding:.5em 1.5em .5em 1.5em;' . $center . '">' . "\n";
		$div .= ' <table style="width:110px;border:0;' . $center . '">' . "\n";
		$div .= '  <tr><td style="' . $center . '">' . "\n";
		$div .= '   <a href="' . $url_shop . '"><img src="' . $url . '" alt="' . $alt  .'" /></a></td></tr>' . "\n";
		$div .= '  <tr><td style="' . $center . '"><a href="' . $url_shop . '">' . $title . '</a></td></tr>' . "\n";
		$div .= ' </table>' . "\n";
		$div .= '</div>' . "\n";
	}
	return $div;
}

function plugin_amazon_get_asin_title()
{
	global $asin, $asin_ext, $asin_all;

	if ($asin_all == '') return '';

	$nocache = $nocachable = 0;

	$url = PLUGIN_AMAZON_XML . $asin;

	if (file_exists(CACHE_DIR) === FALSE || is_writable(CACHE_DIR) === FALSE) $nocachable = 1; // キャッシュ不可の場合

	if (($title = plugin_amazon_cache_title_fetch(CACHE_DIR)) == FALSE) {
		$nocache = 1; // キャッシュ見つからず
		$body    = plugin_amazon_get_page($url); // しかたないので取りにいく
		$tmpary  = array();
		$body    = mb_convert_encoding($body, SOURCE_ENCODING, 'UTF-8');
		preg_match('/<ProductName>([^<]*)</', $body, $tmpary);
		$title     = trim($tmpary[1]);
//		$tmpary[1] = '';
//		preg_match('#<ImageUrlMedium>http://images-jp.amazon.com/images/P/[^.]+\.(..)\.#',
//			$body, $tmpary);
//		if ($tmpary[1] != '') {
//			$asin_ext = $tmpary[1];
//			$asin_all = $asin . $asin_ext;
//		}
	}

	if ($title == '') {
		return '';
	} else {
		if ($nocache == 1 && $nocachable != 1)
			plugin_amazon_cache_title_save($title, CACHE_DIR);
		return $title;
	}
}

// タイトルキャッシュがあるか調べる
function plugin_amazon_cache_title_fetch($dir)
{
	global $asin, $asin_ext, $asin_all;

	$filename = $dir . 'ASIN' . $asin . '.tit';

	$get_tit = 0;
	if (! is_readable($filename)) {
		$get_tit = 1;
	} elseif (PLUGIN_AMAZON_EXPIRE_TITLECACHE * 3600 * 24 < time() - filemtime($filename)) {
		$get_tit = 1;
	}

	if ($get_tit) return FALSE;

	if (($fp = @fopen($filename, 'r')) === FALSE) return FALSE;
	$title = fgets($fp, 4096);
//	$tmp_ext = fgets($fp, 4096);
//	if ($tmp_ext != '') $asin_ext = $tmp_ext;
	fclose($fp);

	if (strlen($title) > 0) {
		return $title;
	} else {
		return FALSE;
	}
}

// 画像キャッシュがあるか調べる
function plugin_amazon_cache_image_fetch($dir)
{
	global $asin, $asin_ext, $asin_all;

	$filename = $dir . 'ASIN' . $asin . '.jpg';

	$get_img = 0;
	if (! is_readable($filename)) {
		$get_img = 1;
	} elseif (PLUGIN_AMAZON_EXPIRE_IMAGECACHE * 3600 * 24 < time() - filemtime($filename)) {
		$get_img = 1;
	}

	if ($get_img) {
		$url = 'http://images-jp.amazon.com/images/P/' . $asin . '.' . $asin_ext . '.MZZZZZZZ.jpg';
		if (! is_url($url)) return FALSE;

		$body = plugin_amazon_get_page($url);
		if ($body != '') {
			$tmpfile = $dir . 'ASIN' . $asin . '.jpg.0';
			$fp = fopen($tmpfile, 'wb');
			fwrite($fp, $body);
			fclose($fp);
			$size = getimagesize($tmpfile);
			unlink($tmpfile);
		}
		if ($body == '' || $size[1] <= 1) { // 通常は1が返るが念のため0の場合も(reimy)
			// キャッシュを PLUGIN_AMAZON_NO_IMAGE のコピーとする
			if ($asin_ext == '09') {
				$url = 'http://images-jp.amazon.com/images/P/' . $asin . '.01.MZZZZZZZ.jpg';
				$body = plugin_amazon_get_page($url);
				if ($body != '') {
					$tmpfile = $dir . 'ASIN' . $asin . '.jpg.0';
					$fp = fopen($tmpfile, 'wb');
					fwrite($fp, $body);
					fclose($fp);
					$size = getimagesize($tmpfile);
					unlink($tmpfile);
				}
			}
			if ($body == '' || $size[1] <= 1) {
				$fp = fopen(PLUGIN_AMAZON_NO_IMAGE, 'rb');
				if (! $fp) return FALSE;
				
				$body = '';
				while (! feof($fp)) $body .= fread($fp, 4096);
				fclose ($fp);
			}
		}
		plugin_amazon_cache_image_save($body, CACHE_DIR);
	}
	return $filename;
}

// Save title cache
function plugin_amazon_cache_title_save($data, $dir)
{
	global $asin, $asin_ext, $asin_all;

	$filename = $dir . 'ASIN' . $asin . '.tit';
	$fp = fopen($filename, 'w');
	fwrite($fp, $data);
	fclose($fp);

	return $filename;
}

// Save image cache
function plugin_amazon_cache_image_save($data, $dir)
{
	global $asin, $asin_ext, $asin_all;

	$filename = $dir . 'ASIN' . $asin . '.jpg';
	$fp = fopen($filename, 'wb');
	fwrite($fp, $data);
	fclose($fp);

	return $filename;
}

// Save book data
function plugin_amazon_review_save($page, $data)
{
	global $asin, $asin_ext, $asin_all;

	$filename = DATA_DIR . encode($page) . '.txt';
	if (! is_readable($filename)) {
		$fp = fopen($filename, 'w');
		fwrite($fp, $data);
		fclose($fp);
		return TRUE;
	} else {
		return FALSE;
	}
}

function plugin_amazon_get_page($url)
{
	$data = http_request($url);
	return ($data['rc'] == 200) ? $data['data'] : '';
}

// is ASIN?
function is_asin()
{
	global $asin, $asin_ext, $asin_all;

	$tmpary = array();
	if (preg_match('/^([A-Z0-9]{10}).?([0-9][0-9])?$/', $asin_all, $tmpary) == FALSE) {
		return FALSE;
	} else {
		$asin     = $tmpary[1];
		$asin_ext = isset($tmpary[2]) ? $tmpary[2] : '';
		if ($asin_ext == '') $asin_ext = '09';
		$asin_all = $asin . $asin_ext;
		return TRUE;
	}
}
?>
