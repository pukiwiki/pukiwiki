<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: aname.inc.php,v 1.13 2003/04/30 08:42:55 arino Exp $
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
	
	// Prior to PHP 4.2.0, array_search() returns NULL on failure instead of FALSE.
	
	$b_super = array_search('super',$args);
	$class = ($b_super !== FALSE and $b_super !== NULL) ? 'anchor_super' : 'anchor';
	
	$b_full = array_search('full',$args);
	$url = ($b_full !== FALSE and $b_full !== NULL) ? "$script?".rawurlencode($vars['page']) : '';
	
	$b_noid = array_search('noid',$args);
	$attr_id = ($b_noid !== FALSE and $b_noid !== NULL) ? '' : " id=\"$id\"";
	
	return "<a class=\"$class\"$attr_id href=\"$url#$id\" title=\"$id\">$body</a>";
}
?>
