<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: touchgraph.inc.php,v 1.1 2003/04/13 07:48:04 arino Exp $
//
// TouchGraph http://www.touchgraph.com/index.html


function plugin_touchgraph_action()
{
	header('Content-type: text/plain');
	plugin_touchgraph_ref();
//	plugin_touchgraph_rel();
	
	die;	
}
function plugin_touchgraph_rel()
{
	foreach (get_existpages() as $page)
	{
		if (file_exists(CACHE_DIR.encode($page).'.rel'))
		{
			$data = file(CACHE_DIR.encode($page).'.rel');
			echo mb_convert_encoding($page.' '.str_replace("\t",' ',trim($data[0]))."\n",'Shift_JIS',SOURCE_ENCODING);
		}
	}
}
function plugin_touchgraph_ref()
{
	foreach (get_existpages() as $page)
	{
		if (file_exists(CACHE_DIR.encode($page).'.ref'))
		{
			$data = file(CACHE_DIR.encode($page).'.ref');
			$node = $page;
			foreach ($data as $line)
			{
				list($name) = explode("\t",$line);
				$node .= " $name";
			}
			echo mb_convert_encoding("$node\n",'Shift_JIS',SOURCE_ENCODING);
		}
	}
}
?>
