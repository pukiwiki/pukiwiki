<?php
// $Id: trackback.php,v 1.1 2003/06/05 06:20:49 arino Exp $
/*
 * PukiWiki TrackBack プログラム
 * (C) 2003, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * License: GPL
 *
 * http://localhost/pukiwiki/pukiwiki.php?FrontPage と明確に指定しないと
 * TrackBack ID の取得はできない
 *
 * tb_count($page)		TrackBack Ping データ個数取得 // pukiwiki.skin.LANG.php
 * tb_send($page,$data)		TrackBack Ping 送信 // file.php
 * tb_ScanLink($data)		convert_html() 変換結果の <a> タグから URL 抽出
 * tb_PageInfo($page)		ページ情報取得
 * tb_xml_msg($rc,$msg)		XML 結果出力
 * tb_save()			TrackBack Ping データ保存(更新)
 * tb_delete($page)		TrackBack Ping データ削除 // edit.inc.php
 * tb_get($file)		TrackBack Ping データ入力
 * tb_put($file,$data)		TrackBack Ping データ出力
 * tb_mode_rss($file)		?__mode=rss 処理
 * tb_mode_view($id)		?__mode=view 処理
 * tb_body($file)		TrackBack Ping 明細行編集
 * tb_sort_by_date_d($p1, $p2)	データを日付順（降順）で整列
 * tb_id2page($id)		TrackBack ID からページ名取得
 * tb_http($url, $method="GET", $headers="", $post=array(""))
 *				GET, POST, HEAD などの指定処理および レスポンスコード取得
 * tb_PutID($page)		文章中に trackback:ping を埋め込むためのデータを生成 // pukiwiki.php
 * tb_GetID($url)		文書を GET し、埋め込まれた TrackBack ID を取得
 * tb_xml_GetId($data)		埋め込まれたデータから TrackBack ID を取得
 * tb_startElementHandler_GetId($parser, $name, $attribs)
 *				xml_set_element_handler関数でセットした startElementHandler
 * tb_xg_dummy($parser, $name)	xml_set_element_handler関数でセットした EndElementHandler
 *
 */
error_reporting(E_ALL);
if (!defined('TRACKBACK_DIR')) {
  define('TRACKBACK_DIR','./trackback/');
}

// TrackBack Ping データ個数取得
function tb_count($page) {

  $page_enc = md5($page);
  $file = TRACKBACK_DIR.$page_enc.".txt";

  // TRACKBACK_DIR の存在と書き込み可能かの確認
  if (file_exists($file) === false) {
    return 0;
  }
  return count( file($file) );
}

// TrackBack Ping 送信
function tb_send($page,$data) {
  global $script, $trackback;

  if (!$trackback) return;

  $link = tb_ScanLink($data);
  if (!is_array($link)) return; // リンク無しは終了
  $r_page = rawurlencode($page);

  // 自文書の情報
  $putdata = array();
  $putdata["title"] = $page; // タイトルはページ名
  $putdata["url"] = $script."?".$r_page; // 送信時に再度、rawurlencode される
  $putdata["excerpt"] = tb_PageInfo($page);
  $putdata["blog_name"] = "PukiWiki/TrackBack 0.1";
  $putdata["charset"] = SOURCE_ENCODING; // 送信側文字コード(未既定)

  foreach ($link as $x) {
  // URL から TrackBack ID を取得する
    $tbid = tb_GetID($x);
    if (empty($tbid)) continue; // TrackBack に対応していない
    list($resp,$header,$data,$query) = tb_http($tbid,"POST","",$putdata);
    // FIXME: エラー処理を行っても、じゃ、どうする？だしなぁ...
  }

}

// convert_html() 変換結果の <a> タグから URL 抽出
function tb_ScanLink($data) {
  $link = array();
  $string = convert_html($data);

  // ループ
  while (preg_match("'(href=)(\"|\')(http:?[^\"^\'^\>]+)'si", $string, $regs)) {
    $link[] = $regs[3];
    $string = str_replace($regs[3], "", $string);
  }
  return $link;
}

// ページ情報取得
function tb_PageInfo($page) {
  // 利用ページの引数なし
  if (empty($page)) return "";

  // 概要の生成
  $excerpt = '';
  $ctr_len = 0;
  $body = get_source($page);
  foreach ($body as $x) {
    if ($x[0] == '/' && $x[1] == '/') continue; // PukiWiki としては、コメント行
    $excerpt .= trim($x);
    $ctr_len += strlen(trim($x));
    if ($ctr_len > 255) break; // 255 を超過した時点で終了
  }
  return $excerpt;
}

// XML 結果出力
function tb_xml_msg($rc,$msg) {
  $x = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n".
    "<response>\n".
    "<error>".$rc."</error>\n";
  if (!empty($msg)) $x .= "<message>".$msg."</message>\n";
  $x .= "</response>\n";
  header('Content-Type: text/xml');
  print $x;
  die();
}

// TrackBack Ping データ保存(更新)
function tb_save() {
  global $script,$vars,$post;

  // TrackBack Ping における URL パラメータは必須である。
  if (empty($post["url"])) {
    tb_xml_msg(1,"It is an indispensable parameter. URL is not set up.");
  }

  // URL 妥当性チェック (これを入れると処理時間に問題がでる)
  list($res,$hed,$dat,$query) = tb_http($post["url"],"HEAD");
  if ($res !== 200) {
    tb_xml_msg(1,"URL is fictitious.");
  }

  // TRACKBACK_DIR の存在と書き込み可能かの確認
  if (file_exists(TRACKBACK_DIR) === false) {
    die(TRACKBACK_DIR.": No such directory");
  }
  if (is_writable(TRACKBACK_DIR) === false) {
    die(TRACKBACK_DIR.": Permission denied");
  }

  // Query String を得る
  if (empty($vars["tb_id"])) {
    tb_xml_msg(1,"TrackBack Ping URL is inaccurate.");
  }

  // ページ存在チェック
  $page = tb_id2page($vars["tb_id"]);
  if ($page == $vars["tb_id"]) {
    tb_xml_msg(1,"TrackBack ID is invalid.");
  }

  // TrackBack Ping のデータを読み込む
  $rc = tb_put(TRACKBACK_DIR.$vars["tb_id"].".txt",tb_get(TRACKBACK_DIR.$vars["tb_id"].".txt"));

  tb_xml_msg($rc,"");
}

// TrackBack Ping データ削除
function tb_delete($page) { @unlink(TRACKBACK_DIR.md5($page).".txt"); }

// TrackBack Ping データ入力
function tb_get($file) {
  if (!file_exists($file)) {
    return false;
  }

  $rc    = array();
  $ctr   = 0;

  $fp = @fopen ($file,"r");
  while ($data = @fgetcsv($fp, 8192, ",")) {
    $rc[$ctr++] = $data;
  }
  @fclose ($fp);
  return $rc;
}

// TrackBack Ping データ出力
function tb_put($file,$data) {
  global $script,$vars,$post;

  // 文字コード変換
  if (empty($post["charset"])) $post["charset"] = "auto";
  $post["title"]     = mb_convert_encoding($post["title"],SOURCE_ENCODING,$post["charset"]);
  $post["excerpt"]   = mb_convert_encoding($post["excerpt"],SOURCE_ENCODING,$post["charset"]);
  $post["blog_name"] = mb_convert_encoding($post["blog_name"],SOURCE_ENCODING,$post["charset"]);

  if (!($fp = fopen($file,"w"))) return 1;
  @flock($fp, LOCK_EX);

  // カンマが入っても良いように。(なんか違うと思うなぁ)
  $post["url"]       = rawurlencode($post["url"]);
  $post["title"]     = rawurlencode($post["title"]);
  $post["excerpt"]   = rawurlencode($post["excerpt"]);
  $post["blog_name"] = rawurlencode($post["blog_name"]);

  $sw_put = 0; // 更新用
  if ($data !== false) {
    foreach ($data as $x) {
      if ($x[1] == $post["url"]) {
        $sw_put = 1;
        $x[0] = UTIME;
        $x[2] = $post["title"];
        $x[3] = $post["excerpt"];
        $x[4] = $post["blog_name"];
      }
      // UTIME, url, title, excerpt, blog_name
      fwrite($fp, $x[0].",".$x[1].",".$x[2].",".$x[3].",".$x[4]."\n");
    }
  }

  // 更新していない場合は、１件追加する
  if (!$sw_put) {
    fwrite($fp, UTIME.",".$post["url"].",".$post["title"].",".$post["excerpt"].",".$post["blog_name"]."\n");
  }

  @flock($fp, LOCK_UN);
  @fclose($fp);

  return 0;
}

// ?__mode=rss 処理
function tb_mode_rss($file) {

  $data = tb_get($file);
  // 表示データなし
  if ($data === false) {
    tb_xml_msg(1,"TrackBack Ping data does not exist.");
  }

  $rc = <<<EOD
<?xml version="1.0" encoding="utf-8" ?>
<response>
<error>0</error>
<rss version="0.91">
<channel>

EOD;

  $sw_item = 0;
  foreach ($data as $x) {
    if ($sw_item) $rc .= "<item>\n";
    $x[1] = rawurldecode($x[1]);
    $x[2] = rawurldecode($x[2]);
    $x[3] = rawurldecode($x[3]);
    // UTIME, url, title, excerpt, blog_name
    $rc .= <<<EOD
<title>$x[2]</title>
<link>$x[1]</link>
<description>$x[3]</description>

EOD;
    if ($sw_item) {
      $rc .= "</item>\n";
    } else {
      $rc .= "<language>ja-Jp</language>\n";
      $sw_item = 1;
    }
  }

  $rc .= <<<EOD
</channel>
</rss>
</response>

EOD;

  $rc = mb_convert_encoding($rc,"utf-8",SOURCE_ENCODING);
  header('Content-Type: text/xml');
  echo $rc;
}

// ?__mode=view 処理
function tb_mode_view($tbid) {
  global $script, $page_title;
  global $_tb_title, $_tb_header, $_tb_entry, $_tb_refer;

  // TrackBack ID からページ名を取得
  $page   = tb_id2page($tbid);
  $r_page = rawurlencode($page);
  $file   = TRACKBACK_DIR.$tbid.".txt";

  $tb_title = sprintf($_tb_title,$page);
  $tb_refer = sprintf($_tb_refer,"<a href=\"$script?$r_page\">'$page'</a>","<a href=\"$script\">$page_title</a>");

  $msg = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
 <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<title>$tb_title</title>
<link rel="stylesheet" href="skin/trackback.css" type="text/css" />
</head>
<body>
<div id="banner-commentspop">$_tb_header</div>
<div class="blog">

<div class="trackback-url">
$_tb_entry<br />
$script?plugin=tb&tb_id=$tbid <br /><br />
$tb_refer
</div>
EOD;

  $msg .= tb_body($file);
  $msg .= <<<EOD
</div>
</body>
</html>
EOD;

  $msg = mb_convert_encoding($msg,"UTF-8",SOURCE_ENCODING);
  echo $msg;
  die();
}

// TrackBack Ping 明細行編集
function tb_body($file) {
  global $_tb_header_Excerpt, $_tb_header_Weblog, $_tb_header_Tracked, $_tb_date;

  $data = tb_get($file);
  if ($data === false) return;

  // 最新版から整列
  usort($data, 'tb_sort_by_date_d');

  $rc = "";
  foreach ($data as $x) {
    // UTIME, url, title, excerpt, blog_name
    $x[0] = date($_tb_date, $x[0]+LOCALZONE); // May  2, 2003 11:25 AM
    $x[1] = rawurldecode($x[1]); // URL
    $x[2] = rawurldecode($x[2]); // title
    $x[3] = rawurldecode($x[3]); // excerpt
    $x[4] = rawurldecode($x[4]); // blog_name
    $rc .= <<<EOD
<div class="trackback-body">
<span class="trackback-post"><a href="$x[1]" target="new">$x[2]</a><br />
<strong>$_tb_header_Excerpt</strong> $x[3]<br />
<strong>$_tb_header_Weblog</strong> $x[4]<br />
<strong>$_tb_header_Tracked</strong> $x[0]</span>
</div>
EOD;
  }

  return $rc;
}

// データを日付順（降順）で整列
function tb_sort_by_date_d($p1, $p2) {
  return ($p2['0'] - $p1['0']);
}

// TrackBack ID からページ名取得
function tb_id2page($id) {
  global $tb_pages;

  if (!is_array($tb_pages)) {
    $tb_pages = get_existpages();
    natcasesort($tb_pages);
  }

  foreach ($tb_pages as $x) {
    if ($id == md5(rawurlencode($x))) return rawurldecode($x);
  }
  return $id; // 見つからない場合

}

/*
 * $url     : http://から始まるURL( http://user:pass@host:port/path?query )
 * $method  : GET, POST, HEADのいずれか(デフォルトはGET)
 * $headers : 任意の追加ヘッダ
 * $post    : POSTの時に送信するデータを格納した配列("変数名"=>"値")
 */
function tb_http($url, $method="GET", $headers="", $post=array(""))
{
  $rc = array();
  $url_arry = parse_url($url);

  // query
  if (isset($url_arry['query'])) {
    $url_arry['query'] = "?".$url_arry['query'];
  } else {
    $url_arry['query'] = "";
  }
  // fragment
  if (isset($url_arry['fragment'])) {
    $url_arry['fragment'] = "#".$url_arry['fragment'];
  } else {
    $url_arry['fragment'] = "";
  }

  if (!isset($url_arry['port'])) $url_arry['port'] = 80;

  $query = $method." ".
    $url_arry['path'].$url_arry['query'].$url_arry['fragment'].
    " HTTP/1.0\r\n";
  $query .= "Host: ".$url_arry['host']."\r\n";
  $query .= "User-Agent: PukiWiki/TrackBack 0.1\r\n";

  // Basic 認証用
  if (isset($url_arry['user']) && isset($url_arry['pass'])) {
    $query .= "Authorization: Basic ".
      base64_encode($url_arry['user'].":".$url_arry['pass'])."\r\n";
  }

  $query .= $headers;

  // POST 時は、urlencode したデータとする
  if (strtoupper($method) == "POST") {
    while (list($name, $val) = each($post)) {
      $POST[] = $name."=".urlencode($val);
    }
    $data = implode("&", $POST);
    $query .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $query .= "Content-Length: ".strlen($data)."\r\n";
    $query .= "\r\n";
    $query .= $data;
  } else {
    $query .= "\r\n";
  }

  $fp = fsockopen($url_arry['host'], $url_arry['port'], $errno, $errstr, 30);
  if (!$fp) {
    if ($errno == 0) {
      $rc[0] = 406; // Not Acceptable
      $rc[1] = ""; // Header
      $rc[2] = ""; // Data
      $rc[3] = $query; // Query String
      return $rc;
    }
    // Proxy 経由の場合は、失敗し、errno は 0 となる。
    // Warning: fsockopen() [http://www.php.net/function.fsockopen]:
    // php_network_getaddresses: gethostbyname failed in C:\var\www\html\pukiwiki\trackback.php on line 457
    // Warning: fsockopen() [http://www.php.net/function.fsockopen]:
    // unable to connect to xxxx.xx.xx:80 in C:\var\www\html\pukiwiki\trackback.php on line 457
    // この操作を正しく終了しました。
    // (0)
    $rc[0] = $errno; // エラー番号
    $rc[1] = ""; // Header
    $rc[2] = $errstr; // エラーメッセージ
    $rc[3] = $query; // Query String
    return $rc;
    // die("trackback.php: $errstr ($errno)\n");
  }

  fputs($fp, $query);

  $response = "";
  while (!feof($fp)) {
    $response .= fgets($fp, 4096);
  }

  fclose($fp);
  $resp  = split("\r\n\r\n", $response, 2);
  $rccd  = strtok($resp[0]," ");
  $rc[0] = strtok(" "); // Response Code
  $rc[0] = $rc[0] * 1; // 数字型に変換
  $rc[1] = $resp[0]; // Header
  $rc[2] = $resp[1]; // Data
  $rc[3] = $query; // Query String
  return $rc;
}

// 文章中に trackback:ping を埋め込むためのデータを生成
function tb_PutId($page) {
  global $script;

  $r_page = rawurlencode($page);
  $page_enc = md5($r_page);
  // $dcdate = substr_replace(get_date('Y-m-d\TH:i:sO',$time),':',-2,0);
  // dc:date="$dcdate"

  $rc = <<<EOD
<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
<rdf:Description
    rdf:about="$script?$r_page"
    dc:identifier="$script?$r_page"
    dc:title="$page"
    trackback:ping="$script?plugin=tb&amp;tb_id=$page_enc" />
</rdf:RDF>
-->
EOD;
  return $rc;
}

// 文書を GET し、埋め込まれた TrackBack ID を取得
function tb_GetID($url) {
  global $tb_get_url, $tb_get_id;

  $tb_get_url = $url;
  $tb_get_id  = "";

  // 0: Response Code, 1:Header, 2:Data, 3:Query String
  $data = tb_http($url);
  if ($data[0] !== 200) return "";

  // ループ
  while (preg_match("'(<rdf:RDF .*?>)(.*?)(</rdf:RDF>)'si",$data[2],$regs)) {
    tb_xml_GetId($regs[1].$regs[2].$regs[3]);
    if (!empty($tb_get_id)) return $tb_get_id;
    $data[2] = str_replace($regs[1].$regs[2].$regs[3], "", $data[2]);
  }
  return "";
}

// 埋め込まれたデータから TrackBack ID を取得
function tb_xml_GetId($data) {
  // XML パーサを作成する
  $xml_parser = xml_parser_create();
  if (!$xml_parser) return;

  // start および end 要素のハンドラを設定する
  xml_set_element_handler($xml_parser, "tb_startElementHandler_GetId", "tb_xg_dummy");
  xml_parse($xml_parser, $data, 0);
  xml_parser_free($xml_parser);
  return;
}

// xml_set_element_handler関数でセットした startElementHandler
function tb_startElementHandler_GetId($parser, $name, $attribs) {
  global $tb_get_url, $tb_get_id;

  if ($name !== "RDF:DESCRIPTION") return;

  $tbid = $tburl = $tbabout = "";
  foreach ($attribs as $key=>$value) {
    // print "KEY=".$key." VAL=".$value."\n";
    if ($key == "RDF:ABOUT") {
      $tbabout = $value;
      continue;
    }
    if ($key == "DC:IDENTIFER" || $key == "DC:IDENTIFIER") {
      $tburl = $value;
      continue;
    }
    if ($key == "TRACKBACK:PING") {
      $tbid = $value;
      continue;
    }
  }

  // print "URL:".$tb_get_url."=".$tburl."\n";
  // print "TBID:".$tbid."\n";
  if ($tb_get_url == $tburl || $tb_get_url == $tbabout) $tb_get_id = $tbid;
}

// xml_set_element_handler関数でセットした EndElementHandler
function tb_xg_dummy($parser, $name) { return; }

?>
