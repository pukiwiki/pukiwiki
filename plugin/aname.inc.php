<?php
// $Id: aname.inc.php,v 1.7 2003/01/27 05:38:44 panda Exp $

function plugin_aname_inline()
{
	if (func_num_args() < 1) {
		return FALSE;
	}
	list($id,$body) = func_get_args();
	
	if (!preg_match('/[A-Za-z][\w\-]*/',$id)) {
		return FALSE;
	}
	
	return "<a id=\"$id\">$body</a>";
}
?>
