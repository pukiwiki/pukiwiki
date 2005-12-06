<?php
// $Id: nofollow.inc.php,v 1.1 2005/05/23 14:22:30 henoheno Exp $
// Copyright (C) 2005 PukiWiki Developers Team
// License: The same as PukiWiki
//
// NoFollow plugin

// Output contents with "nofollow,noindex" option
function plugin_nofollow_convert()
{
	global $vars, $nofollow;

	$page = isset($vars['page']) ? $vars['page'] : '';

	if(is_freeze($page)) $nofollow = 1;

	return '';
}
?>
