<?
/*
 * PukiWiki lsプラグイン
 *
 * CopyRight 2002 Y.MASUI GPL2
 * http://masui.net/pukiwiki/ masui@masui.net
 *
 * $Id: ls.inc.php,v 1.3 2002/06/26 06:23:57 masui Exp $
 */

function plugin_ls_convert()
{
	global $vars, $script;
	
	if(func_num_args())
		$aryargs = func_get_args();
	else
		$aryargs = array();

      	$with_title = FALSE;
	if(array_search('title',$aryargs)!==FALSE) {
	  $with_title = TRUE;
	}
	$ls = $comment = '';
	$filepattern = encode('[['.strip_bracket($vars["page"]).'/');
	$filepattern_len = strlen($filepattern);
	if ($dir = @opendir(DATA_DIR))
	{
		while($file = readdir($dir))
		{
			if($file == ".." || $file == ".") continue;
			if(substr($file,0,$filepattern_len)!=$filepattern) continue; 
			$page = decode(trim(preg_replace("/\.txt$/"," ",$file)));
			if($with_title) {
			  $comment = '';
			  $fd = fopen(DATA_DIR . $file,'r');
			  if(!feof ($fd)) {
			    $comment = ereg_replace("^[-*]+",'',fgets($fd,1024));
			    $comment = ereg_replace("[~\r\n]+$",'',$comment);
			    $comment = trim($comment);
			  }
			  if($comment != '' && substr($comment,0,1) != '#') {
			    $comment = " - " . convert_html($comment);
			  }
			  else {
			    $comment = '';
			  }
			  fclose($fd);
			}
      			$url = rawurlencode($page);
			$name = strip_bracket($page);
			$title = $name ." " .get_pg_passage($page,false);
			$ls .= "<li><a href=\"$script?cmd=read&amp;page=$url\" title=\"$title\">$name</a>$comment\n";
		}
		closedir($dir);
	}
	
	if($ls=='') {
	  return '';
	}
	
	return "<ul>$ls</ul>";
}
?>
