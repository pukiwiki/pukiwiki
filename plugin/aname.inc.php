<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: aname.inc.php,v 1.9 2003/03/21 22:49:48 panda Exp $
//

function plugin_aname_inline()
{
	$args = func_get_args();
	return call_user_func_array('plugin_aname_convert',$args);
}
function plugin_aname_convert()
{
	if (func_num_args() < 1)
	{
		return FALSE;
	}
	
	$args = func_get_args();
	$id = $args[0];
	$body = (func_num_args() > 1) ? $args[1] : '';
	
	if (!preg_match('/^[A-Za-z][\w\-]*$/',$id))
	{
		return FALSE;
	}
	
	$s_body = htmlspecialchars($body);
	
	return "<a id=\"$id\">$s_body</a>";
}
?>
