<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: amazon.inc.php,v 1.5 2004/12/25 01:47:20 henoheno Exp $
// Id: amazon.inc.php,v 1.1 2003/07/24 13:00:00 閑舎
//
// Copyright:
//	2004 PukiWiki Developer Team
//	2003 閑舎 <raku@rakunet.org> (Original author)
//
// Thanks: To reimy and PukiWiki Developers Team.
//
// ChangeLog:
// * 2004/04/03 PukiWiki Developer Team (arino <arino@users.sourceforge.jp>)
//        - replace plugin_amazon_get_page().
//        - AMAZON_XML 'xml.amazon.com' -> 'xml.amazon.co.jp'
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
// License: GNU/GPL
//
// Caution!:
// * 著作権が関連する為、www.amazon.co.jp のアソシエイトプログラムを確認の上ご利用下さい。
// * レビューは、amazon プラグインが呼び出す編集画面はもう出来て PukiWiki に登録されているので、
//   中止するなら全文を削除してページの更新ボタンを押すこと。
// * 下の AMAZON_AID、PROXY サーバの部分、expire の部分を適当に編集して使用してください(他はそのままでも Ok)。
//

/////////////////////////////////////////////////
// amazon のアソシエイト ID(ないなら 一般ユーザ)
define('AMAZON_AID','');
/////////////////////////////////////////////////
// expire 画像/タイトルキャッシュを何日で削除するか
define('AMAZON_EXPIRE_IMG',1);
define('AMAZON_EXPIRE_TIT',356);
/////////////////////////////////////////////////
// 画像なしの場合の画像
define('NO_IMAGE','./image/noimage.jpg');
/////////////////////////////////////////////////
// amazon ショップ
define('AMAZON_SHOP','http://www.amazon.co.jp/exec/obidos/ASIN/');
/////////////////////////////////////////////////
// amazon 商品情報問合せ URI(dev-t はマニュアルのディフォルト値)
define('AMAZON_XML','http://xml.amazon.co.jp/onca/xml3?t=webservices-20&dev-t=GTYDRES564THU&type=lite&page=1&f=xml&locale=jp&AsinSearch=');

function plugin_amazon_init()
{
  global $amazon_aid, $amazon_body;

  if (AMAZON_AID == '') {
    $amazon_aid = '';
  } else {
    $amazon_aid = AMAZON_AID . '/';
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

  if (func_num_args() == 0) { // レビュー作成
    $s_page = htmlspecialchars($vars['page']);
    if ($s_page == '') {
      $s_page = $vars['refer'];
    }
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
  } elseif (func_num_args() < 1 || func_num_args() > 3) {
    return false;
  }
  $aryargs = func_get_args();

  $align = strtolower($aryargs[1]);
  if ($align == 'clear') return '<div style="clear:both"></div>'; // 改行挿入
  if ($align != 'left') $align = 'right'; // 配置決定

  $asin_all = htmlspecialchars($aryargs[0]);  // for XSS
  if (is_asin() == false && $align != 'clear') return false;

  if ($aryargs[2] != '') { // タイトル指定か自動取得か
    $title = $alt = htmlspecialchars($aryargs[2]); // for XSS
    if ($alt == 'image') {
      $alt = plugin_amazon_get_asin_title();
      if ($alt == '') return false;
      $title = '';
    } elseif ($alt == 'delimage') {
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
      if ((unlink(CACHE_DIR . 'ASIN' . $asin . '.jpg') && unlink(CACHE_DIR . 'ASIN' . $asin . '.tit'))) {
        return 'Title and Image of ' . $asin . ' deleted...';
      } else {
        return 'Title and Image of ' . $asin . ' NOT DELETED...';
      }
    }
  } else {
    $alt = $title = plugin_amazon_get_asin_title(); // タイトル自動取得
    if ($alt == '') return false;
  }

  return plugin_amazon_print_object($align, $alt, $title);
}

function plugin_amazon_action()
{
  global $vars, $script, $edit_auth, $edit_auth_users;
  global $amazon_body, $asin, $asin_all;

  $asin_all = htmlspecialchars(rawurlencode(strip_bracket($vars['asin'])));

  if (! is_asin()) {
    $retvars['msg']   = 'ブックレビュー編集';
    $retvars['refer'] = $vars['refer'];
    $retvars['body']  = plugin_amazon_convert();
    return $retvars;

  } else {
    $s_page     = $vars['refer'];
    $r_page     = $s_page . '/' . $asin;
    $r_page_url = rawurlencode($r_page);

    pkwk_headers_sent();
    if ($edit_auth && (! isset($_SERVER['PHP_AUTH_USER']) ||
	! array_key_exists($_SERVER['PHP_AUTH_USER'], $edit_auth_users) ||
	$edit_auth_users[$_SERVER['PHP_AUTH_USER']] != $_SERVER['PHP_AUTH_PW'])) {
      header('Location: ' . get_script_uri() . '?cmd=read&page=' . $r_page_url);
    } else {
      $title = plugin_amazon_get_asin_title();
      if ($title == '' || preg_match('/^\//', $s_page)) {
        header('Location: ' . get_script_uri() . '?cmd=read&page=' . encode($s_page));
      }
      $body = '#amazon(' . $asin_all . ',,image)' . "\n" . '*' . $title . "\n" . $amazon_body;
      plugin_amazon_review_save($r_page, $body);
      header('Location: ' . get_script_uri() . '?cmd=edit&page=' . $r_page_url);
    }
    exit;
  }
}

function plugin_amazon_inline()
{
  global $amazon_aid;
  global $asin, $asin_ext, $asin_all;

  list($asin_all) = func_get_args();

  $asin_all = htmlspecialchars($asin_all); // for XSS
  if (! is_asin()) return false;

  $title = plugin_amazon_get_asin_title();
  if ($title == '')
    return false;
  else
    return '<a href="' . AMAZON_SHOP . "$asin/$amazon_aid" . 'ref=nosim">' . "$title</a>\n";
}

function plugin_amazon_print_object($align, $alt, $title)
{
  global $amazon_aid;
  global $asin, $asin_ext, $asin_all;

  $url = plugin_amazon_cache_image_fetch(CACHE_DIR);

  if ($title == '') { // タイトルがなければ、画像のみ表示
    $div = '<div style="float:' . $align . ';margin:16px 16px 16px 16px;text-align:center">' . "\n";
    $div .= ' <a href="' . AMAZON_SHOP . $asin . '/' . $amazon_aid . 'ref=nosim">' .
    	'<img src="' . $url . '" alt="' . $alt . '" /></a>' . "\n";
    $div .= '</div>' . "\n";
  } else {	      // 通常表示
    $div = '<div style="float:' . $align . ';padding:.5em 1.5em .5em 1.5em;text-align:center">' . "\n";
    $div .= ' <table style="width:110px;border:0;text-align:center"><tr><td style="text-align:center">' . "\n";
    $div .= '  <a href="' . AMAZON_SHOP . $asin . '/' . $amazon_aid . 'ref=nosim">' .
    	'<img src="' . $url . '" alt="' . $alt  .'" /></a></td></tr>' . "\n";
    $div .= '  <tr><td style="text-align:center"><a href="' .
    	AMAZON_SHOP . $asin . '/' . $amazon_aid . 'ref=nosim">' . $title . '</a></td>' . "\n";
    $div .= ' </tr></table>' . "\n" . '</div>' . "\n";
  }
  return $div;
}

function plugin_amazon_get_asin_title()
{
  global $asin, $asin_ext, $asin_all;

  if ($asin_all == '') return '';

  $nocache = $nocachable = 0;

  $url = AMAZON_XML . $asin;

  if (file_exists(CACHE_DIR) === false || is_writable(CACHE_DIR) === false) $nocachable = 1; // キャッシュ不可の場合

  if (($title = plugin_amazon_cache_title_fetch(CACHE_DIR)) == false) {
    $nocache = 1; // キャッシュ見つからず
    $body = plugin_amazon_get_page($url); // しかたないので取りにいく
    $tmpary = array();
    $body = mb_convert_encoding($body, SOURCE_ENCODING, 'UTF-8');
    preg_match('/<ProductName>([^<]*)</', $body, $tmpary);
    $title = trim($tmpary[1]);
//    $tmpary[1] = '';
//    preg_match("/<ImageUrlMedium>http:\/\/images-jp.amazon.com\/images\/P\/[^.]+\.(..)\./", $body, $tmpary);
//    if ($tmpary[1] != '') {
//      $asin_ext = $tmpary[1];
//      $asin_all = $asin . $asin_ext;
//    }
  }

  if ($title == '') return '';

  if ($nocache == 1 && $nocachable != 1) plugin_amazon_cache_title_save($title, CACHE_DIR); // タイトルがあればキャッシュに保存
  return $title;
}

// タイトルキャッシュがあるか調べる
function plugin_amazon_cache_title_fetch($dir)
{
  global $asin, $asin_ext, $asin_all;

  $filename = $dir . 'ASIN' . $asin . '.tit';

  $get_tit = 0;
  if (!is_readable($filename)) {
    $get_tit = 1;
  } elseif (AMAZON_EXPIRE_TIT * 3600 * 24 < time() - filemtime($filename)) {
    $get_tit = 1;
  }

  if ($get_tit) return false;

  if (!($fp = @fopen($filename, 'r'))) return false;
  $title = fgets($fp, 4096);
//  $tmp_ext = fgets($fp, 4096);
//  if ($tmp_ext != '') {
//    $asin_ext = $tmp_ext;
//  }
  fclose($fp);

  if (strlen($title) > 0)
    return $title;
  else
    return false;
}

// 画像キャッシュがあるか調べる
function plugin_amazon_cache_image_fetch($dir)
{
  global $asin, $asin_ext, $asin_all;

  $filename = $dir . 'ASIN' . $asin . '.jpg';

  $get_img = 0;
  if (!is_readable($filename)) {
    $get_img = 1;
  } elseif (AMAZON_EXPIRE_IMG * 3600 * 24 < time() - filemtime($filename)) {
    $get_img = 1;
  }

  if ($get_img) {
    $url = 'http://images-jp.amazon.com/images/P/' . $asin . '.' . $asin_ext . '.MZZZZZZZ.jpg';

    if (!is_url($url)) return false; // URL 形式チェック
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
      // キャッシュを NO_IMAGE のコピーとする
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
        $fp = fopen(NO_IMAGE, 'rb');
        if (! $fp) return false;
        $body = '';
        while (!feof($fp)) {
          $body .= fread($fp, 4096);
        }
        fclose ($fp);
      }
    }
    plugin_amazon_cache_image_save($body, CACHE_DIR);
  }
  return $filename;
}

// タイトルキャッシュを保存
function plugin_amazon_cache_title_save($data, $dir)
{
  global $asin, $asin_ext, $asin_all;

  $filename = $dir . 'ASIN' . $asin . '.tit';

  $fp = fopen($filename, 'w');
  fwrite($fp, "$data");
  fclose($fp);

  return $filename;
}

// 画像キャッシュを保存
function plugin_amazon_cache_image_save($data, $dir)
{
  global $asin, $asin_ext, $asin_all;

  $filename = $dir . 'ASIN' . $asin . '.jpg';

  $fp = fopen($filename, 'wb');
  fwrite($fp, $data);
  fclose($fp);

  return $filename;
}

// 書籍データを保存
function plugin_amazon_review_save($page, $data)
{
  global $asin, $asin_ext, $asin_all;

  $filename = DATA_DIR . encode($page) . '.txt';

  if (! is_readable($filename)) {
    $fp = fopen($filename, 'w');
    fwrite($fp, $data);
    fclose($fp);
    return true;
  }
  return false;
}

// ネット上の URL のデータを取ってきて返す(なければ空データ)
function plugin_amazon_get_page($url)
{
	$data = http_request($url);

	return ($data['rc'] == 200) ? $data['data'] : '';
}

// ASINか？
function is_asin()
{
  global $asin, $asin_ext, $asin_all;

  $tmpary = array();
  if (preg_match('/^([A-Z0-9]{10}).?([0-9][0-9])?$/', $asin_all, $tmpary) == false) {
    return false;
  } else {
    $asin = $tmpary[1];
    $asin_ext = $tmpary[2];
    if ($asin_ext == '') {
      $asin_ext = '09';
    }
    $asin_all = $asin . $asin_ext;
    return true;
  }
}

?>
