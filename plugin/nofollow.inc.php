<?php
// $Id: nofollow.inc.php,v 1.2 2007/07/29 05:22:36 henoheno Exp $
// Copyright (C) 2005, 2007 PukiWiki Developers Team
// License: The same as PukiWiki
//
// NoFollow plugin
// (See BugTrack2/72)

// Output contents with "nofollow,noindex" option
function plugin_nofollow_convert()
{
	global $vars, $nofollow;

	$page = isset($vars['page']) ? $vars['page'] : '';
	if (is_freeze($page)) {
		$nofollow = 1;
		return '';
	} else {
		return '#nofollow: Page not freezed';
	}
}
?>
