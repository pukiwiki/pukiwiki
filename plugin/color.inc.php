<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: color.inc.php,v 1.6 2003/09/03 01:31:14 arino Exp $
//

function plugin_color_inline()
{
	if (func_num_args() == 3)
	{
		list($color,$bgcolor,$body) = func_get_args();
		if ($body == '')
		{
			$body = $bgcolor;
			$bgcolor = '';
		}
	}
	else if (func_num_args() == 2)
	{
		$bgcolor = '';
		list($color,$body) = func_get_args();
	}
	else
	{
		return FALSE;
	}
	
	if ($color == '' or $body == '')
	{
		return FALSE;
	}
	if (!plugin_color_is_valid($color) or !plugin_color_is_valid($bgcolor))
	{
		return $body;
	}
	
	if ($bgcolor != '')
	{
		$color .= ';background-color:'.$bgcolor;
	}
	return "<span style=\"color:$color\">$body</span>";
}
function plugin_color_is_valid($color)
{
	return preg_match('/^(#[0-9a-f]+|[\w-]+)/i',$color);
}
?>
