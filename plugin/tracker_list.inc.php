<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: tracker_list.inc.php,v 1.2 2005/01/23 08:30:14 henoheno Exp $
//
// Issue tracker list plugin (a part of tracker plugin)

require_once(PLUGIN_DIR . 'tracker.inc.php');

function plugin_tracker_list_init()
{
	if (function_exists('plugin_tracker_init'))
		plugin_tracker_init();
}
?>
