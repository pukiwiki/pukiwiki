<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: norelated.inc.php,v 1.1 2003/01/27 05:38:46 panda Exp $
//

function plugin_norelated_convert()
{
	global $related_link;
	$related_link = 0;
	return '';
}

?>