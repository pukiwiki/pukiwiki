<?php
// $Id: tb.inc.php,v 1.2 2003/07/27 13:54:58 arino Exp $
/*
 * PukiWiki TrackBack プログラム
 * (C) 2003, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * License: GPL
 *
 * plugin_tb_action()   action
 * tb_save()            TrackBack Ping データ保存(更新)
 * tb_xml_msg($rc,$msg) XML 結果出力
 * tb_mode_rss($tb_id)  ?__mode=rss 処理
 * tb_mode_view($tb_id) ?__mode=view 処理
 */

function plugin_tb_action()
{
	global $script,$vars,$post,$trackback;
	
	// POST: TrackBack Ping を保存する
	if (!empty($post['url']))
	{
		tb_save();
	}
	
	if ($trackback and !empty($vars['__mode']) and !empty($vars['tb_id']))
	{
		switch ($vars['__mode'])
		{
			case 'rss':
				tb_mode_rss($vars['tb_id']);
				break;
			case 'view':
				tb_mode_view($vars['tb_id']);
				break;
		}
	}
	return array('msg'=>'','body'=>'');
}

// TrackBack Ping データ保存(更新)
function tb_save()
{
	global $script,$post,$vars,$trackback;
	static $fields = array(/* UTIME, */'url','title','excerpt','blog_name');
	
	// 許可していないのに呼ばれた場合の対応
	if (!$trackback)
	{
		tb_xml_msg(1,'Feature inactive.');
	}
	// TrackBack Ping における URL パラメータは必須である。
	if (empty($post['url']))
	{
		tb_xml_msg(1,'It is an indispensable parameter. URL is not set up.');
	}
	// Query String を得る
	if (empty($vars['tb_id']))
	{
		tb_xml_msg(1,'TrackBack Ping URL is inaccurate.');
	}
	
	$url = $post['url'];
	$tb_id = $vars['tb_id'];
	
	// ページ存在チェック
	$page = tb_id2page($tb_id);
	if ($page === FALSE)
	{
		tb_xml_msg(1,'TrackBack ID is invalid.');
	}
	
	// URL 妥当性チェック (これを入れると処理時間に問題がでる)
	$result = http_request($url,'HEAD');
	if ($result['rc'] !== 200)
	{
		tb_xml_msg(1,'URL is fictitious.');
	}
	
	// TRACKBACK_DIR の存在と書き込み可能かの確認
	if (!file_exists(TRACKBACK_DIR))
	{
		tb_xml_msg(1,'No such directory');
	}
	if (!is_writable(TRACKBACK_DIR))
	{
		tb_xml_msg(1,'Permission denied');
	}
	
	// TrackBack Ping のデータを更新
	$filename = TRACKBACK_DIR.$tb_id.'.txt';
	$data = tb_get($filename);
	
	$charset = empty($post['charset']) ? 'auto' : $post['charset'];
	
	$items = array(UTIME);
	foreach ($fields as $field)
	{
		$value = array_key_exists($field,$post) ? $post[$field] : '';
		$value = mb_convert_encoding($value,SOURCE_ENCODING,$charset);
		if (ereg("[,\"\n\r]",$value))
		{
			$value = '"'.str_replace('"', '""', $value).'"';
		}
		$items[$field] = $value;
	}
	$data[$items['url']] = $items;
	
	$fp = fopen($filename,'w');
	flock($fp,LOCK_EX);
	foreach ($data as $line)
	{
		fwrite($fp,join(',',$line)."\n");
	}
	flock($fp,LOCK_UN);
	fclose($fp);
	
	tb_xml_msg(0,'');
}

// XML 結果出力
function tb_xml_msg($rc,$msg)
{
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="iso-8859-1"?>';
	echo <<<EOD

<response>
 <error>$rc</error>
 <message>$msg</message>
</response>
EOD;
	die;
}

// ?__mode=rss 処理
function tb_mode_rss($tb_id)
{
	global $script,$vars,$entity_pattern;
	
	$page = tb_id2page($tb_id);
	if ($page === FALSE)
	{
		return FALSE;
	}
	
	$items = '';
	foreach (tb_get(TRACKBACK_DIR.$tb_id.'.txt') as $arr)
	{
		$utime = array_shift($arr);
		list ($url,$title,$excerpt,$blog_name) = array_map(
			create_function('$a','return htmlspecialchars($a);'),$arr);
		$items .= <<<EOD

   <item>
    <title>$title</title>
    <link>$url</link>
    <description>$excerpt</description>
   </item>
EOD;
	}
	
	$title = htmlspecialchars($page);
	$link = "$script?".rawurlencode($page);
	$vars['page'] = $page;
	$excerpt = strip_htmltag(convert_html(join('',get_source($page))));
	$excerpt = preg_replace("/&$entity_pattern;/",'',$excerpt);
	$excerpt = mb_strimwidth(preg_replace("/[\r\n]/",' ',$excerpt),0,255,'...');

	$rc = <<<EOD

<response>
 <error>0</error>
 <rss version="0.91">
  <channel>
   <title>$title</title>
   <link>$link</link>
   <description>$excerpt</description>
   <language>ja-Jp</language>$items
  </channel>
 </rss>
</response>
EOD;
	$rc = mb_convert_encoding($rc,'UTF-8',SOURCE_ENCODING);
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="utf-8" ?>';
	echo $rc;
	die;
}
// ?__mode=view 処理
function tb_mode_view($tb_id)
{
	global $script,$page_title;
	global $_tb_title,$_tb_header,$_tb_entry,$_tb_refer,$_tb_date;
	global $_tb_header_Excerpt,$_tb_header_Weblog,$_tb_header_Tracked;
	
	// TrackBack ID からページ名を取得
	$page = tb_id2page($tb_id);
	if ($page === FALSE)
	{
		return FALSE;
	}
	$r_page = rawurlencode($page);
	
	$tb_title = sprintf($_tb_title,$page);
	$tb_refer = sprintf($_tb_refer,"<a href=\"$script?$r_page\">'$page'</a>","<a href=\"$script\">$page_title</a>");

	
	$data = tb_get(TRACKBACK_DIR.$tb_id.'.txt');
	
	// 最新版から整列
	usort($data,create_function('$a,$b','return $b[0] - $a[0];'));
	
	$tb_body = '';
	foreach ($data as $x)
	{
		list ($time,$url,$title,$excerpt,$blog_name) = $x;
		$time = date($_tb_date, $time + LOCALZONE); // May 2, 2003 11:25 AM
		$tb_body .= <<<EOD
<div class="trackback-body">
 <span class="trackback-post"><a href="$url" target="new">$title</a><br />
  <strong>$_tb_header_Excerpt</strong> $excerpt<br />
  <strong>$_tb_header_Weblog</strong> $blog_name<br />
  <strong>$_tb_header_Tracked</strong> $time
 </span>
</div>
EOD;
	}
	$msg = <<<EOD
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
   $script?plugin=tb&amp;tb_id=$tb_id<br /><br />
   $tb_refer
  </div>
  $tb_body
 </div>
</body>
</html>
EOD;
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo mb_convert_encoding($msg,'UTF-8',SOURCE_ENCODING);
	die;
}
?>
