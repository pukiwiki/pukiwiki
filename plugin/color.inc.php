<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: color.inc.php,v 1.5 2003/05/16 06:13:06 arino Exp $
//

function plugin_color_inline()
{
	$bgcolor = '';

	if (func_num_args() == 3)
	{
		list($color,$bgcolor,$body) = func_get_args();
		if ($body == '')
		{
			$body = $bgcolor;
			$bgcolor = '';
		}
		else if ($bgcolor != '')
		{
			$bgcolor = ';background-color:'.htmlspecialchars($bgcolor);
		}
	}
	else if (func_num_args() == 2)
	{
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
	
	$s_color = htmlspecialchars($color);
	$s_bgcolor = htmlspecialchars($bgcolor);
	return "<span style=\"color:$s_color$s_bgcolor\">$body</span>";
}
?>
