<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: color.inc.php,v 1.1 2003/01/27 05:38:44 panda Exp $
//
function plugin_color_inline()
{
	if (func_num_args() < 2) { return FALSE; }

	list($color,$body) = func_get_args();
	if ($color == '' or $body == '') { return FALSE; }

	return "<span style=\"color:$color\">$body</span>";
}
?>
