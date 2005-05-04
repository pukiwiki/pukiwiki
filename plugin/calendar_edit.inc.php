<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: calendar_edit.inc.php,v 1.8 2005/05/04 05:00:31 henoheno Exp $
//

function plugin_calendar_edit_convert()
{
	global $command;

	if (! file_exists(PLUGIN_DIR . 'calendar.inc.php')) return FALSE;

	$command = 'edit';
	require_once PLUGIN_DIR . 'calendar.inc.php';
	if (! function_exists('plugin_calendar_convert')) return FALSE;

	$args = func_num_args() ? func_get_args() : array();
	return call_user_func_array('plugin_calendar_convert', $args);
}
?>
