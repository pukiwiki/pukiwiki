<?php
// PukiWiki - Yet another WikiWikiWeb clone
// tracker_list.inc.php
// Copyright 2003-2017 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Issue tracker list plugin (a part of tracker plugin)

require_once(PLUGIN_DIR . 'tracker.inc.php');

function plugin_tracker_list_init()
{
	if (function_exists('plugin_tracker_init'))
		plugin_tracker_init();
}
