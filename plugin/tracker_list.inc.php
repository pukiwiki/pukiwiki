<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: tracker_list.inc.php,v 1.1 2003/07/10 02:49:35 arino Exp $
//

require_once(PLUGIN_DIR.'tracker.inc.php');

function plugin_tracker_list_init()
{
	if (function_exists('plugin_tracker_init'))
	{
		plugin_tracker_init();
	}
}
?>
