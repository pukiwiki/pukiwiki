<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: tracker_list.inc.php,v 1.3 2007/01/21 14:29:12 henoheno Exp $
// Copyright (C) 2003, 2005 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Issue tracker list plugin (a part of tracker plugin)

require_once(PLUGIN_DIR . 'tracker.inc.php');

function plugin_tracker_list_init()
{
	if (function_exists('plugin_tracker_init'))
		plugin_tracker_init();
}
?>
