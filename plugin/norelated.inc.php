<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: norelated.inc.php,v 1.3 2005/01/16 13:05:22 henoheno Exp $
//
// norelated plugin
// - Stop showing related link automatically if $related_link = 1

function plugin_norelated_convert()
{
	global $related_link;
	$related_link = 0;
	return '';
}
?>
