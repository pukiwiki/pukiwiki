<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: rss10.inc.php,v 1.1 2003/01/27 05:38:46 panda Exp $
//
// RecentChanges の RSS を出力
function plugin_rss10_action()
{
	global $script,$BracketName,$rss_max,$page_title,$whatsnew,$BracketName;
	
	$self = 'http://'.SERVER_NAME.PHP_SELF.'?';

	$page_title_utf8 = $page_title;
	if (function_exists('mb_convert_encoding'))
		$page_title_utf8 = mb_convert_encoding($page_title_utf8,'UTF-8','auto');

	$items = $rdf_li = '';
	$lines = array_splice(preg_grep('/^\/\//',get_source($whatsnew)),0,$rss_max);
	
	foreach($lines as $line) {
		if (!preg_match("/^\/\/(\d+)\s($BracketName)$/",$line,$match))
			continue; // fatal error, die?
		
		$page = $match[2];
		
		$r_url = rawurlencode($page);
		
		$title = strip_bracket($match[2]);
		if (function_exists('mb_convert_encoding'))
			$title = mb_convert_encoding($title,'UTF-8','auto');
		
		$dcdate = get_date('Y-m-d\TH:i:sO');
		$desc = get_date('D, d M Y H:i:s T',get_filetime($page));
		$items .= <<<EOD
<item rdf:about="$self$r_url">
 <title>$title</title>
 <link>$self$r_url</link>
 <dc:date>$dcdate</dc:date>
 <description>$desc</description>
</item>

EOD;
		$rdf_li .= "    <rdf:li rdf:resource=\"$self$r_url\" />\n";
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