<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: size.inc.php,v 1.3 2003/04/24 14:42:29 arino Exp $
//

function plugin_size_inline()
{
	if (func_num_args() != 2)
	{
		return FALSE;
	}
	
	list($size,$body) = func_get_args();
	
	if ($size == '' or $body == '')
	{
		return FALSE;
	}

	return "<span style=\"font-size:{$size}px;display:inline-block;line-height:130%;text-indent:0px\">$body</span>";
}
?>
