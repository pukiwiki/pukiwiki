<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: color.inc.php,v 1.3 2003/04/24 14:42:29 arino Exp $
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
	
	return "<span style=\"color:$color\">$body</span>";
}
?>
