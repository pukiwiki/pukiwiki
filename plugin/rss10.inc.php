<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: rss10.inc.php,v 1.2 2003/02/20 12:21:13 panda Exp $
//
// RecentChanges の RSS を出力
function plugin_rss10_action()
{
	global $script,$rss_max,$page_title,$whatsnew;
	
	$self = 'http://'.SERVER_NAME.PHP_SELF.'?';

	$page_title_utf8 = $page_title;
	if (function_exists('mb_convert_encoding')) {
		$page_title_utf8 = mb_convert_encoding($page_title_utf8,'UTF-8',SOURCE_ENCODING);
	}

	$items = $rdf_li = '';

	if (!file_exists(CACHE_DIR.'recent.dat')) {
		return '';
	}
	$recent = file(CACHE_DIR.'recent.dat');
	$lines = array_splice($recent,0,$rss_max);
	foreach ($lines as $line) {
		list($time,$page) = explode("\t",rtrim($line));
		$r_page = rawurlencode($page);
		$title = $page;
		if (function_exists('mb_convert_encoding')) {
			$title = mb_convert_encoding($title,'UTF-8',SOURCE_ENCODING);
		}
		
		$dcdate = get_date('Y-m-d\TH:i:sO');
		$desc = get_date('D, d M Y H:i:s T',$time);
		$items .= <<<EOD
<item rdf:about="$self$r_page">
 <title>$title</title>
 <link>$self$r_page</link>
 <dc:date>$dcdate</dc:date>
 <description>$desc</description>
</item>

EOD;
		$rdf_li .= "    <rdf:li rdf:resource=\"$self$r_page\" />\n";
	}
	
	header('Content-type: application/xml');
	
	print <<<EOD
<?xml version="1.0" encoding="utf-8"?>

<rdf:RDF 
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
  xml:lang="ja">

 <channel rdf:about="{$self}rss">
  <title>$page_title_utf8</title>
  <link>$self$whatsnew</link>
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