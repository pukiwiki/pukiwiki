<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: aname.inc.php,v 1.12 2003/04/26 05:10:13 arino Exp $
//

function plugin_aname_inline()
{
	$args = func_get_args();
	return call_user_func_array('plugin_aname_convert',$args);
}
function plugin_aname_convert()
{
	global $script,$vars;
	
	if (func_num_args() < 1)
	{
		return FALSE;
	}
	
	$args = func_get_args();
	$id = array_shift($args);
	
	if (!preg_match('/^[A-Za-z][\w\-]*$/',$id))
	{
		return FALSE;
	}
	
	$body = count($args) ? preg_replace('/<\/?a[^>]*>/','',array_pop($args)) : '';
	$class = (array_search('super',$args) !== FALSE) ? 'anchor_super' : 'anchor';
	$url = (array_search('full',$args) !== FALSE) ? "$script?".rawurlencode($vars['page']) : '';
	
	return "<a class=\"$class\" id=\"$id\" href=\"$url#$id\" title=\"$id\">$body</a>";
}
?>
