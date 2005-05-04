<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: calendar_edit.inc.php,v 1.10 2005/05/04 05:07:51 henoheno Exp $
// Copyright (C)
//   2003,2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Calendar_ edit plugin (needs calendar plugin)

function plugin_calendar_edit_convert()
{
	global $command;

	if (! file_exists(PLUGIN_DIR . 'calendar.inc.php')) return FALSE;

	require_once PLUGIN_DIR . 'calendar.inc.php';
	if (! function_exists('plugin_calendar_convert')) return FALSE;

	$command = 'edit';
	$args = func_num_args() ? func_get_args() : array();
	return call_user_func_array('plugin_calendar_convert', $args);
}
?>
