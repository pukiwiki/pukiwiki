<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: keitai.skin.ja.php,v 1.6 2004/08/06 12:29:31 henoheno Exp $
//

global $script, $vars, $page_title, $max_size, $accesskey;

if (! defined('DATA_DIR')) exit;

// Shift JIS encode
header('Content-Type: text/html; charset=Shift_JIS');
$title = mb_convert_encoding($title, 'SJIS', SOURCE_ENCODING);
$body  = mb_convert_encoding($body,  'SJIS', SOURCE_ENCODING);

//1KByte余裕を見る(ヘッダなど)
$max_size = (--$max_size * 1024);

// ALT option を持つ IMG タグ(画像)を文字列に置換
$body = preg_replace('#(<div[^>]+>)?(<a[^>]+>)?<img[^>]*alt="([^"]+)"[^>]*>(?(2)</a>)(?(1)</div>)#i', '$3', $body);

// ALT option の無い IMG タグ(画像)を文字列に置換
$body = preg_replace('#(<div[^>]+>)?(<a[^>]+>)?<img[^>]+>(?(2)</a>)(?(1)</div>)#i', '[img]', $body);

// ページ番号
$r_page = isset($vars['page'] ? $vars['page'] : '';
$r_page = rawurlencode($r_page);
$pageno = (isset($vars['p']) and is_numeric($vars['p'])) ? $vars['p'] : 0;
$pagecount = ceil(strlen($body) / $max_size);
$lastpage = $pagecount - 1;

// ナビゲーション
$navi = array();
$navi[] = "<a href=\"$link_top\" $accesskey=\"0\">0.Top</a>";
$navi[] = "<a href=\"$script?plugin=newpage&refer=$r_page\" $accesskey=\"1\">1.New</a>";
$navi[] = "<a href=\"$link_edit\" $accesskey=\"2\">2.Edit</a>";
if ($is_read and $function_freeze) {
	if ($is_freeze) {
		$navi[] = "<a href=\"$link_unfreeze\" $accesskey=\"3\">3.Unfreeze</a>";
	}
	else {
		$navi[] = "<a href=\"$link_freeze\" $accesskey=\"3\">3.Freeze</a>";
	}
}
$navi[] = "<a href=\"$script?MenuBar\" $accesskey=\"4\">4.Menu</a>";
$navi[] = "<a href=\"$link_whatsnew\" $accesskey=\"5\">5.Recent</a>";

// 前/次のブロック
if ($pagecount > 1) {
	$prev = $pageno - 1;
	$next = $pageno + 1;
	if ($pageno > 0) {
		$navi[] = "<a href=\"$script?cmd=read&page=$r_page&p=$prev\" $accesskey=\"7\">7.Prev</a>";
	}
	$navi[] = "$next/$pagecount ";
	if ($pageno < $lastpage) {
		$navi[] = "<a href=\"$script?cmd=read&page=$r_page&p=$next\" $accesskey=\"8\">8.Next</a>";
	}
}

$navi = join(' | ', $navi);
$body = substr($body, $pageno * $max_size, $max_size);

// Output
?><html><head><title><?php
	echo $title
?></title></head><body><?php
	echo $navi
?><hr><?php
	echo $body
?></body></html>
