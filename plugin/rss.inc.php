<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: rss.inc.php,v 1.1 2003/01/27 05:38:46 panda Exp $
//
// RecentChanges の RSS を出力
function plugin_rss_action()
{
	global $script,$rss_max,$page_title,$whatsnew,$BracketName;

	$self = 'http://'.SERVER_NAME.PHP_SELF.'?';

	$page_title_utf8 = $page_title;
	if (function_exists('mb_convert_encoding'))
		$page_title_utf8 = mb_convert_encoding($page_title_utf8,'UTF-8','auto');

	$items = '';
	$lines = array_splice(preg_grep('/^\/\//',get_source($whatsnew)),0,$rss_max);
	
	foreach($lines as $line) {
		if (!preg_match("/^\/\/(\d+)\s($BracketName)$/",$line,$match))
			continue; // fatal error, die?
		
		$page = $match[2];
		
		$r_url = rawurlencode($page);
		
		$title = strip_bracket($page);
		if (function_exists('mb_convert_encoding'))
			$title = mb_convert_encoding($title,'UTF-8','auto');
		
		$desc = get_date('D, d M Y H:i:s T',get_filetime($page));
		$items .= <<<EOD
<item>
 <title>$title</title>
 <link>$self$r_url</link>
 <description>$desc</description>
</item>

EOD;
	}
	
	header('Content-type: application/xml');
	
	print <<<EOD
<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN"
            "http://my.netscape.com/publish/formats/rss-0.91.dtd">

<rss version="0.91">

<channel>
<title>$page_title_utf8</title>
<link>$self$whatsnew</link>
<description>PukiWiki RecentChanges</description>
<language>ja</language>

$items
</channel>
</rss>
EOD;
	exit;
}
?>