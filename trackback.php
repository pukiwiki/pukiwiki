<?php
// $Id: trackback.php,v 1.12 2003/09/03 04:05:38 arino Exp $
/*
 * PukiWiki TrackBack プログラム
 * (C) 2003, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * License: GPL
 *
 * http://localhost/pukiwiki/pukiwiki.php?FrontPage と明確に指定しないと
 * TrackBack ID の取得はできない
 *
 * tb_get_id($page)       TrackBack Ping IDを取得
 * tb_id2page($tb_id)     TrackBack Ping ID からページ名を取得
 * tb_get_filename($page) TrackBack Ping データファイル名を取得
 * tb_count($page)        TrackBack Ping データ個数取得  // pukiwiki.skin.LANG.php
 * tb_send($page,$data)   TrackBack Ping 送信  // file.php
 * tb_delete($page)       TrackBack Ping データ削除  // edit.inc.php
 * tb_get($file,$key=1)   TrackBack Ping データ入力
 * tb_get_rdf($page)      文章中に埋め込むためのrdfをデータを生成 // pukiwiki.php
 * tb_get_url($url)       文書をGETし、埋め込まれたTrackBack Ping URLを取得
 * class TrackBack_XML    XMLからTrackBack Ping IDを取得するクラス
 * == Referer 対応分 ==
 * ref_save($page)        Referer データ保存(更新) // pukiwiki.php
 */

if (!defined('TRACKBACK_DIR'))
{
	define('TRACKBACK_DIR','./trackback/');
}

// TrackBack Ping IDを取得
function tb_get_id($page)
{
	return md5($page);
}

// TrackBack Ping ID からページ名を取得
function tb_id2page($tb_id)
{
	static $pages,$cache = array();
	
	if (array_key_exists($tb_id,$cache))
	{
		return $cache[$tb_id];
	}
	if (!isset($pages))
	{
		$pages = get_existpages();
	}
	foreach ($pages as $page)
	{
		$_tb_id = tb_get_id($page);
		$cache[$_tb_id] = $page;
		unset($pages[$page]);
		if ($_tb_id == $tb_id)
		{
			return $page;
		}
	}
	return FALSE; // 見つからない場合
}

// TrackBack Ping データファイル名を取得
function tb_get_filename($page,$ext='.txt')
{
	return TRACKBACK_DIR.encode($page).$ext;
}

// TrackBack Ping データ個数取得
function tb_count($page,$ext='.txt')
{
	$filename = tb_get_filename($page,$ext);
	return file_exists($filename) ? count(file($filename)) : 0;
}

// TrackBack Ping 送信
function tb_send($page,$data)
{
	global $script,$trackback;
	
	if (!$trackback)
	{
		return;
	}
	
	set_time_limit(0); // 処理実行時間制限(php.ini オプション max_execution_time )
	
	$data = convert_html($data);
	
	// convert_html() 変換結果の <a> タグから URL 抽出
	preg_match_all('#href="(https?://[^"]+)"#',$data,$links,PREG_PATTERN_ORDER);
	
	// 自ホスト($scriptで始まるurl)を除く
	$links = preg_grep("/^(?!".preg_quote($script,'/')."\?)./",$links[1]);
		
	// リンク無しは終了
	if (!is_array($links) or count($links) == 0)
	{
		return;
	}
	
	$r_page = rawurlencode($page);
	$excerpt = strip_htmltag(convert_html(get_source($page)));
	
	// 自文書の情報
	$putdata = array(
		'title'     => $page, // タイトルはページ名
		'url'       => "$script?$r_page", // 送信時に再度、rawurlencode される
		'excerpt'   => mb_strimwidth(preg_replace("/[\r\n]/",' ',$excerpt),0,255,'...'),
		'blog_name' => 'PukiWiki/TrackBack 0.1',
		'charset'   => SOURCE_ENCODING // 送信側文字コード(未既定)
	);
	foreach ($links as $link)
	{
		// URL から TrackBack ID を取得する
		$tb_id = tb_get_url($link);
		if (empty($tb_id)) // TrackBack に対応していない
		{
			continue;
		}
		$result = http_request($tb_id,'POST','',$putdata);
		// FIXME: エラー処理を行っても、じゃ、どうする？だしなぁ...
	}
}

// TrackBack Ping データ削除
function tb_delete($page)
{
	$filename = tb_get_filename($page);
	if (file_exists($filename))
	{
		@unlink($filename);
	}
}

// TrackBack Ping データ入力
function tb_get($file,$key=1)
{
	if (!file_exists($file))
	{
		return array();
	}
	
	$result = array();
	$fp = @fopen($file,'r');
	flock($fp,LOCK_EX);
	while ($data = @fgetcsv($fp,8192,','))
	{
		// $data[$key] = URL
		$result[$data[$key]] = $data;
	}
	flock($fp,LOCK_UN);
	fclose ($fp);
	
	return $result;
}

// 文章中に trackback:ping を埋め込むためのデータを生成
function tb_get_rdf($page)
{
	global $script,$trackback;
	
	if (!$trackback)
	{
		return '';
	}
	
	$r_page = rawurlencode($page);
	$tb_id = tb_get_id($page);
	// $dcdate = substr_replace(get_date('Y-m-d\TH:i:sO',$time),':',-2,0);
	// dc:date="$dcdate"
	
	return <<<EOD
<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
 <rdf:Description
   rdf:about="$script?$r_page"
   dc:identifier="$script?$r_page"
   dc:title="$page"
   trackback:ping="$script?plugin=tb&amp;tb_id=$tb_id" />
</rdf:RDF>
-->
EOD;
}

// 文書をGETし、埋め込まれたTrackBack Ping urlを取得
function tb_get_url($url)
{
	// プロキシを経由する必要があるホストにはpingを送信しない
	$parse_url = parse_url($url);
	if (empty($parse_url['host']) or via_proxy($parse_url['host']))
	{
		return '';
	}
	
	$data = http_request($url);
	
	if ($data['rc'] !== 200)
	{
		return '';
	}
	
	if (!preg_match_all('#<rdf:RDF[^>]*>(.*?)</rdf:RDF>#si',$data['data'],$matches,PREG_PATTERN_ORDER))
	{
		return '';
	}
	
	$obj = new TrackBack_XML();
	foreach ($matches[1] as $body)
	{
		$tb_url = $obj->parse($body,$url);
		if ($tb_url !== FALSE)
		{
			return $tb_url;
		}
	}
	return '';
}

// 埋め込まれたデータから TrackBack Ping urlを取得するクラス
class TrackBack_XML
{
	var $url;
	var $tb_url;
	
	function parse($buf,$url)
	{
		// 初期化
		$this->url = $url;
		$this->tb_url = FALSE;
		
		$xml_parser = xml_parser_create();
		if ($xml_parser === FALSE)
		{
			return FALSE;
		}
		xml_set_element_handler($xml_parser,array(&$this,'start_element'),array(&$this,'end_element'));
		
		if (!xml_parse($xml_parser,$buf,TRUE))
		{
/*			die(sprintf('XML error: %s at line %d in %s',
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser),
				$buf
			));
*/
			return FALSE;
		}
		
		return $this->tb_url;
	}
	function start_element($parser,$name,$attrs)
	{
		if ($name !== 'RDF:DESCRIPTION')
		{
			return;
		}
		
		$about = $url = $tb_url = '';
		foreach ($attrs as $key=>$value)
		{
			switch ($key)
			{
				case 'RDF:ABOUT':
					$about = $value;
					break;
				case 'DC:IDENTIFER':
				case 'DC:IDENTIFIER':
					$url = $value;
					break;
				case 'TRACKBACK:PING':
					$tb_url = $value;
					break;
			}
		}
		if ($about == $this->url or $url == $this->url)
		{
			$this->tb_url = $tb_url;
		}
	}
	function end_element($parser,$name)
	{
		// do nothing
	}
}

// Referer データ保存(更新)
function ref_save($page)
{
	global $referer;
	
	if (!$referer or empty($_SERVER['HTTP_REFERER']))
	{
		return;
	}
	
	$url = $_SERVER['HTTP_REFERER'];
	
	// URI の妥当性評価
	// 自サイト内の場合は処理しない
	$parse_url = parse_url($url);
	if (empty($parse_url['host']) or $parse_url['host'] == $_SERVER['HTTP_HOST'])
	{
		return;
	}
	
	// TRACKBACK_DIR の存在と書き込み可能かの確認
	if (!is_dir(TRACKBACK_DIR))
	{
		die(TRACKBACK_DIR.': No such directory');
	}
	if (!is_writable(TRACKBACK_DIR))
	{
		die(TRACKBACK_DIR.': Permission denied');
	}
	
	// Referer のデータを更新
	if (ereg("[,\"\n\r]",$url))
	{
		$url = '"'.str_replace('"', '""', $url).'"';
	}
	$filename = tb_get_filename($page,'.ref');
	$data = tb_get($filename,3);
	if (!array_key_exists($url,$data))
	{
		// 0:最終更新日時, 1:初回登録日時, 2:参照カウンタ, 3:Referer ヘッダ, 4:利用可否フラグ(1は有効)
		$data[$url] = array(UTIME,UTIME,0,$url,1);
	}
	$data[$url][0] = UTIME;
	$data[$url][2]++;
	
	if (!($fp = fopen($filename,'w')))
	{
		return 1;
	}
	flock($fp, LOCK_EX);
	foreach ($data as $line)
	{
		fwrite($fp,join(',',$line)."\n");
	}
	flock($fp, LOCK_UN);
	fclose($fp);
	
	return 0;
}
?>
