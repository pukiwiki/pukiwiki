<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: size.inc.php,v 1.2 2003/04/23 08:05:16 arino Exp $
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
	$body = make_link($body);

	return "<span style=\"font-size:{$size}px;display:inline-block;line-height:130%;text-indent:0px\">$body</span>";
}
?>
