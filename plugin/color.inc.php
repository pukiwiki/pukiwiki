<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: color.inc.php,v 1.2 2003/04/23 08:05:16 arino Exp $
//

function plugin_color_inline()
{
	if (func_num_args() != 2)
	{
		return FALSE;
	}
	
	list($color,$body) = func_get_args();
	
	if ($color == '' or $body == '')
	{
		return FALSE;
	}
	$body = make_link($body);
	
	return "<span style=\"color:$color\">$body</span>";
}
?>
