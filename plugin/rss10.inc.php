<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: rss10.inc.php,v 1.13 2004/11/08 12:01:37 henoheno Exp $
//
// This plugin had been merged into rss plugin.
// Please use it instead of.

// Compat
function plugin_rss10_action()
{
	global $vars;
	exist_plugin('rss') or die('rss plugin not found');

	$vars['ver'] = '1.0';
	plugin_rss_action();
}
?>
