<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: norelated.inc.php,v 1.2 2004/07/31 03:09:20 henoheno Exp $
//

function plugin_norelated_convert()
{
	global $related_link;
	$related_link = 0;
	return '';
}

?>
