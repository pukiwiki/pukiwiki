<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: aname.inc.php,v 1.10 2003/04/23 08:05:16 arino Exp $
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
	$id = array_shift($args);
	
//	$s_body = count($args) ? make_linke_rules(htmlspecialchars(join(',',$args))) : '';
	$s_body = count($args) ? htmlspecialchars(join(',',$args)) : '';
	
	if (!preg_match('/^[A-Za-z][\w\-]*$/',$id))
	{
		return FALSE;
	}
	
	return "<a id=\"$id\" href=\"#$id\" title=\"$id\">$s_body</a>";
}
?>
