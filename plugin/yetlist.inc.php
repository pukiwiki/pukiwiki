<?
function plugin_yetlist_action()
{
	global $script,$InterWikiName,$WikiName,$BracketName,$defaultpage,$_gwbn;
	
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
		)/ex","check_link('$1','$name')",$line);
	}
	
	foreach($_gwbn as $tmp)
	{
		$wbn = $tmp["name"];
		$name = $tmp["refer"];
	
		if(preg_match("/^[^>]+>([^\]]+)/",$wbn,$match))
		{
			$wbn = $match[1];
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
			$lists[strip_bracket($wbn)] = strip_bracket($wbn);
			$refer[strip_bracket($wbn)][$name] = $name;
		}
	}

	ksort($lists);
	foreach($lists as $wbn)
	{
		$url = $wbn;
		if(!preg_match("/($WikiName)|($BracketName)/",$url))
			$url = "[[$url]]";
		$url = rawurlencode($url);
		
		$link_ref = "";
		foreach($refer[$wbn] as $refs)
		{
			$ref = strip_bracket($refs);
			$refurl = rawurlencode($refs);
			
			$link_ref .= " <a href=\"$script?$refurl\">$ref</a>";
		}
		$link_ref = trim($link_ref);
		
		$ret["body"] .= "<li><a href=\"$script?cmd=edit&page=$url&refer=$refurl\">$wbn</a> <i>($link_ref)</i></li>\n";
	}


	$ret["body"] .= "</ul>\n";

	$ret["msg"] = "List of pages,are not made yet";
	
	return $ret;
}

function check_link($name,$refer)
{
	global $BracketName,$WikiName,$InterWikiName,$_gwbn;

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
		$_gwbn[$name]["name"] = $name;
		$_gwbn[$name]["refer"] = $refer;
		return;
	}
	else
	{
		return;
	}
}
?>
