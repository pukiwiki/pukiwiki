<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: rss10.inc.php,v 1.9 2003/09/12 00:38:06 arino Exp $
//
// RecentChanges の RSS を出力
function plugin_rss10_action()
{
	global $script,$rss_max,$page_title,$whatsnew;
	global $trackback;
	
	$self = (preg_match('#^https?://#',$script) ? $script : get_script_uri());
	if ($self === FALSE)
	{
		die_message("please set '\$script' in ".INI_FILE);
	}
	
	$page_title_utf8 = mb_convert_encoding($page_title,'UTF-8',SOURCE_ENCODING);
	
	$items = $rdf_li = '';
	
	if (!file_exists(CACHE_DIR.'recent.dat'))
	{
		return '';
	}
	$recent = file(CACHE_DIR.'recent.dat');
	$lines = array_splice($recent,0,$rss_max);
	foreach ($lines as $line)
	{
		list($time,$page) = explode("\t",rtrim($line));
		$r_page = rawurlencode($page);
		$title = mb_convert_encoding($page,'UTF-8',SOURCE_ENCODING);
		// 'O'が出力する時刻を'+09:00'の形に整形
		$dc_date = substr_replace(get_date('Y-m-d\TH:i:sO',$time),':',-2,0);
		
//		$desc = get_date('D, d M Y H:i:s T',$time);
// <description>$desc</description>
		
		$dc_identifier = $trackback_ping = '';
		if ($trackback)
		{
			$tb_id = md5($r_page);
			$dc_identifier = " <dc:identifer>$self?$r_page</dc:identifer>";
			$trackback_ping = " <trackback:ping>$self?plugin=tb&amp;tb_id=$tb_id</trackback:ping>";
		}
		$items .= <<<EOD
<item rdf:about="$self?$r_page">
 <title>$title</title>
 <link>$self?$r_page</link>
 <dc:date>$dc_date</dc:date>
$dc_identifier
$trackback_ping
</item>

EOD;
		$rdf_li .= "    <rdf:li rdf:resource=\"$self?$r_page\" />\n";
	}
	
	$xmlns_trackback = $trackback ?  
		'  xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/"' : '';
	
	header('Content-type: application/xml');
	
	print <<<EOD
<?xml version="1.0" encoding="utf-8"?>

<rdf:RDF 
  xmlns:dc="http://purl.org/dc/elements/1.1/"
$xmlns_trackback
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
  xml:lang="ja">

 <channel rdf:about="$self?$whatsnew">
  <title>$page_title_utf8</title>
  <link>$self?$whatsnew</link>
  <description>PukiWiki RecentChanges</description>
  <items>
   <rdf:Seq>
$rdf_li
   </rdf:Seq>
  </items>
 </channel>

$items
</rdf:RDF>
EOD;
	exit;
}
?>