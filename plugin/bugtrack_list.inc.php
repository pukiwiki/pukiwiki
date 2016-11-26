<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// bugtrack_list.inc.php
// Copyright
//   2002-2016 PukiWiki Development Team
//   2002 Y.MASUI GPL2  http://masui.net/pukiwiki/ masui@masui.net
//
// BugTrack List plugin

require_once(PLUGIN_DIR . 'bugtrack.inc.php');

function plugin_bugtrack_list_init()
{
	plugin_bugtrack_init();
}
