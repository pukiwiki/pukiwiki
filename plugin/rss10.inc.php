<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: rss10.inc.php,v 1.5 2003/06/05 06:20:49 arino Exp $
//
// RecentChanges の RSS を出力
function plugin_rss10_action()
{
	global $script,$rss_max,$page_title,$whatsnew;
	
	$self = $script.'?';
	
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
		$tb_id = md5($r_page);
		$title = mb_convert_encoding($page,'UTF-8',SOURCE_ENCODING);
		// 'O'が出力する時刻を'+09:00'の形に整形
		$dcdate = substr_replace(get_date('Y-m-d\TH:i:sO',$time),':',-2,0);
		
//		$desc = get_date('D, d M Y H:i:s T',$time);
// <description>$desc</description>
		
		$items .= <<<EOD
<item rdf:about="$script?$r_page">
 <title>$title</title>
 <link>$script?$r_page</link>
 <dc:date>$dcdate</dc:date>
 <dc:identifer>$script?$r_page</dc:identifer>
 <trackback:ping>$script?plugin=tb&amp;tb_id=$tb_id</trackback:ping>
</item>

EOD;
		$rdf_li .= "    <rdf:li rdf:resource=\"$script?$r_page\" />\n";
	}
	
	header('Content-type: application/xml');
	
	print <<<EOD
<?xml version="1.0" encoding="utf-8"?>

<rdf:RDF 
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/"
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
  xml:lang="ja">

 <channel rdf:about="$script?rss">
  <title>$page_title_utf8</title>
  <link>$script?$whatsnew</link>
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