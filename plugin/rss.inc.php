<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: rss.inc.php,v 1.21 2007/02/11 05:53:31 henoheno Exp $
//
// RSS plugin: Publishing RSS of RecentChanges
//
// Usage: plugin=rss[&ver=[0.91|1.0|2.0]] (Default: 0.91)
//
// NOTE for acronyms
//   RSS 0.9,  1.0  : RSS means 'RDF Site Summary'
//   RSS 0.91, 0.92 : RSS means 'Rich Site Summary'
//   RSS 2.0        : RSS means 'Really Simple Syndication' (born from RSS 0.92)

function plugin_rss_action()
{
	global $vars, $rss_max, $page_title, $whatsnew;

	$version = isset($vars['ver']) ? $vars['ver'] : '';
	switch($version){
	case '':  $version = '0.91'; break; // Default
	case '1': $version = '1.0';  break; // Sugar
	case '2': $version = '2.0';  break; // Sugar
	case '0.91': /* FALLTHROUGH */
	case '1.0' : /* FALLTHROUGH */
	case '2.0' : break;
	default: die('Invalid RSS version!!');
	}

	$recent = CACHE_DIR . PKWK_MAXSHOW_CACHE;
	if (! file_exists($recent)) die('PKWK_MAXSHOW_CACHE is not found');

	$lang = LANG;
	$page_title_utf8 = mb_convert_encoding($page_title, 'UTF-8', SOURCE_ENCODING);
	$self = get_script_uri();

	// Creating <item>
	$items = $rdf_li = '';

	foreach (file_head($recent, $rss_max) as $line) {
		list($time, $page) = explode("\t", rtrim($line));
		$r_page = rawurlencode($page);
		$title  = mb_convert_encoding($page, 'UTF-8', SOURCE_ENCODING);

		switch ($version) {
		case '0.91': /* FALLTHROUGH */
		case '2.0':
			$date = get_date('D, d M Y H:i:s T', $time);
			$date = ($version == '0.91') ?
				' <description>' . $date . '</description>' :
				' <pubDate>'     . $date . '</pubDate>';
			$items .= <<<EOD
<item>
 <title>$title</title>
 <link>$self?$r_page</link>
$date
</item>

EOD;
			break;

		case '1.0':
			// Add <item> into <items>
			$rdf_li .= '    <rdf:li rdf:resource="' . $self .
				'?' . $r_page . '" />' . "\n";

			$date = substr_replace(get_date('Y-m-d\TH:i:sO', $time), ':', -2, 0);
			$items .= <<<EOD
<item rdf:about="$self?$r_page">
 <title>$title</title>
 <link>$self?$r_page</link>
 <dc:date>$date</dc:date>
 <dc:identifier>$self?$r_page</dc:identifier>
</item>

EOD;
			break;
		}
	}

	// Feeding start
	pkwk_common_headers();
	header('Content-type: application/xml');
	print '<?xml version="1.0" encoding="UTF-8"?>' . "\n\n";

	$r_whatsnew = rawurlencode($whatsnew);
	switch ($version) {
	case '0.91':
		print '<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN"' .
		' "http://my.netscape.com/publish/formats/rss-0.91.dtd">' . "\n";
		 /* FALLTHROUGH */

	case '2.0':
		print <<<EOD
<rss version="$version">
 <channel>
  <title>$page_title_utf8</title>
  <link>$self?$r_whatsnew</link>
  <description>PukiWiki RecentChanges</description>
  <language>$lang</language>

$items
 </channel>
</rss>
EOD;
		break;

	case '1.0':
		print <<<EOD
<rdf:RDF
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xml:lang="$lang">
 <channel rdf:about="$self?$r_whatsnew">
  <title>$page_title_utf8</title>
  <link>$self?$r_whatsnew</link>
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
		break;
	}
	exit;
}
?>
