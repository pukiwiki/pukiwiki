<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: rss.php,v 1.5.2.3 2004/07/31 03:15:07 henoheno Exp $
/////////////////////////////////////////////////

// RecentChanges の RSS を出力
function catrss($rss)
{
	global $rss_max,$page_title,$WikiName,$BracketName,$script,$whatsnew;

	$lines = get_source($whatsnew);
	header("Content-type: application/xml");

	$self = (preg_match('#^https?://#',$script) ? $script : get_script_uri());
	if ($self === FALSE)
	{
		die_message("please set '\$script' in ".INI_FILE);
	}

	$page_title_utf8 = $page_title;
	if(function_exists("mb_convert_encoding"))
		$page_title_utf8 = mb_convert_encoding($page_title_utf8,"UTF-8","auto");

	$item = "";
	$rdf_li = "";
	$cnt = 0;
	foreach($lines as $line)
	{
		if($cnt > $rss_max - 1) break;

		if(preg_match("/(($WikiName)|($BracketName))/",$line,$match))
		{
			if($match[2])
			{
				$title = $url = $match[1];
			}
			else
			{
				if(function_exists("mb_convert_encoding"))
					$title = mb_convert_encoding(strip_bracket($match[1]),"UTF-8","auto");
				else
					$title = strip_bracket($match[1]);

				$url = $match[1];
			}

			$title = htmlspecialchars($title);

			$dcdate = substr_replace(date("Y-m-d\TH:i:sO"),':',-2,0);
			$desc = date("D, d M Y H:i:s T",filemtime(get_filename(encode($match[1]))));

			if($rss==2)
				$items.= "<item rdf:about=\"$self?".rawurlencode($url)."\">\n";
			else
				$items.= "<item>\n";
			$items.= " <title>$title</title>\n";
			$items.= " <link>$self?".rawurlencode($url)."</link>\n";
			if($rss==2)
			{
				$items.= " <dc:date>$dcdate</dc:date>\n";
			}
			$items.= " <description>$desc</description>\n";
			$items.= "</item>\n\n";
			$rdf_li.= "    <rdf:li rdf:resource=\"$self?".rawurlencode($url)."\" />\n";

		}

		$cnt++;
	}

	if($rss==1)
	{
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>


<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN"
            "http://my.netscape.com/publish/formats/rss-0.91.dtd">

<rss version="0.91">

<channel>
<title><?php echo $page_title_utf8 ?></title>
<link><?php echo "$self?$whatsnew" ?></link>
<description>PukiWiki RecentChanges</description>
<language>ja</language>

<?php echo $items ?>
</channel>
</rss>
<?php
	}
	else if($rss==2)
	{
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>' ?>


<rdf:RDF
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns="http://purl.org/rss/1.0/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xml:lang="ja">

 <channel rdf:about="<?php echo "$self?rss" ?>">
  <title><?php echo $page_title_utf8 ?></title>
  <link><?php echo "$self?$whatsnew" ?></link>
  <description>PukiWiki RecentChanges</description>
  <items>
   <rdf:Seq>
<?php echo $rdf_li ?>
   </rdf:Seq>
  </items>
 </channel>

<?php echo $items ?>
</rdf:RDF>
<?php
	}
}
?>
