<?
// $Id: yetlist.inc.php,v 1.5 2002/07/29 01:47:24 masui Exp $

function plugin_yetlist_action()
{
	global $script,$InterWikiName,$WikiName,$BracketName,$defaultpage;
	
	if ($dir = @opendir(DATA_DIR))
	{
		while($file = readdir($dir))
		{
			if($file == ".." || $file == ".") continue;
			$cnt++;
			$page = decode(trim(preg_replace("/\.txt$/"," ",$file)));
			$data[$page] = file(DATA_DIR.$file);
		}
		closedir($dir);
	}

	$ret["body"] = "<ul>\n";

	foreach($data as $name => $lines)
	{
		$lines = preg_replace("/^\s(.*)$/","",$lines);
		
		$line = join("\n",$lines);
		
		preg_replace("/
		(
			(\[\[([^\]]+)\:(https?|ftp|news)(:\/\/[-_.!~*'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)\]\])
			|
			(\[(https?|ftp|news)(:\/\/[-_.!~*'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)\s([^\]]+)\])
			|
			(https?|ftp|news)(:\/\/[-_.!~*'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)
			|
			([[:alnum:]\-_.]+@[[:alnum:]\-_]+\.[[:alnum:]\-_\.]+)
			|
			(\[\[([^\]]+)\:([[:alnum:]\-_.]+@[[:alnum:]\-_]+\.[[:alnum:]\-_\.]+)\]\])
			|
			($InterWikiName)
			|
			($BracketName)
			|
			($WikiName)
		)/ex","check_link('$1',\$name,\$_gwbn)",$line);
	}
	
	foreach($_gwbn as $wbn => $refs_arr)
	{

		foreach (array_unique($refs_arr) as $name)
		{

		if(preg_match("/^[^>]+>([^\]]+)/",$wbn,$match))
		{
			$wbn = $match[1];
			//閉じブラケットの補充。/^\[\[/でも必要十分だが念のため
			if(preg_match("/^\[\[[^\]]+$/",$wbn))
				$wbn = "$wbn]]";
			if(!preg_match("/($WikiName)|($BracketName)/",$wbn))
				$wbn = "[[$wbn]]";
		}
		
		$keep = $wbn;
		
		if(preg_match("/^\[\[\.\/([^\]]*)\]\]/",$wbn,$match))
		{
			if(!$match[1])
				$wbn = $name;
			else
				$wbn = "[[".strip_bracket($name)."/$match[1]]]";
		}
		else if(preg_match("/^\[\[\..\/([^\]]+)\]\]/",$wbn,$match))
		{
			for($i=0;$i<substr_count($keep,"../");$i++)
				$wbn = preg_replace("/(.+)\/([^\/]+)$/","$1",strip_bracket($name));

			if(!preg_match("/^($BracketName)|($WikiName)$/",$wbn))
				$wbn = "[[$wbn]]";
			
			if($wbn==$name)
				$wbn = "[[$match[1]]]";
			else
				$wbn = "[[".strip_bracket($wbn)."/$match[1]]]";
		}
		else if($wbn == "[[../]]")
		{
			$wbn = preg_replace("/(.+)\/([^\/]+)$/","$1",strip_bracket($name));
			
			if(!preg_match("/^($BracketName)|($WikiName)$/",$wbn))
				$wbn = "[[$wbn]]";
			if($wbn==$name)
				$wbn = $defaultpage;
		}

		if(!is_page($wbn))
		{
			$refer[$wbn][] = $name;
		}

			$wbn = $keep; //ひー ^^;)
		}
	}

	ksort($refer);
	foreach($refer as $wbn => $refs_arr)
	{
		$url = rawurlencode($wbn);
		$name = strip_bracket($wbn);
		
		$link_ref = "";
		foreach(array_unique($refs_arr) as $refs)
		{
			$ref = strip_bracket($refs);
			$refurl = rawurlencode($refs);
			
			$link_ref .= " <a href=\"$script?$refurl\">$ref</a>";
		}
		$link_ref = trim($link_ref);
		
		$ret["body"] .= "<li><a href=\"$script?cmd=edit&amp;page=$url&amp;refer=$refurl\">$name</a> <em>($link_ref)</em></li>\n";
	}


	$ret["body"] .= "</ul>\n";

	$ret["msg"] = "List of pages,are not made yet";
	
	return $ret;
}

function check_link($name,$refer,&$_gwbn)
{
	global $BracketName,$WikiName,$InterWikiName;

	if(preg_match("/^\[\[([^\]]+)\:((https?|ftp|news)([^\]]+))\]\]$/",$name))
	{
		return;
	}
	else if(preg_match("/^\[((https?|ftp|news)([^\]\s]+))\s([^\]]+)\]$/",$name))
	{
		return;
	}
	else if(preg_match("/^(https?|ftp|news).*?(\.gif|\.png|\.jpeg|\.jpg)?$/",$name))
	{
		return;
	}
	else if(preg_match("/^\[\[([^\]]+)\:([[:alnum:]\-_.]+@[[:alnum:]\-_]+\.[[:alnum:]\-_\.]+)\]\]/",$name))
	{
		return;
	}
	else if(preg_match("/^([[:alnum:]\-_]+@[[:alnum:]\-_]+\.[[:alnum:]\-_\.]+)/",$name))
	{
		return;
	}
	else if(preg_match("/^($InterWikiName)$/",$name))
	{
		return;
	}
	else if(preg_match("/^($BracketName)|($WikiName)$/",$name))
	{
		$_gwbn[$name][] = $refer;
		return;
	}
	return;
}
?>
