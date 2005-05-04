<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: calendar_read.inc.php,v 1.8 2005/05/04 05:11:38 henoheno Exp $
//

function plugin_calendar_read_convert()
{
	global $command;

	if (! file_exists(PLUGIN_DIR . 'calendar.inc.php')) return FALSE;

	require_once PLUGIN_DIR.'calendar.inc.php';
	if (! function_exists('plugin_calendar_convert')) return FALSE;

	$command = 'read';
	$args = func_num_args() ? func_get_args() : array();
	return call_user_func_array('plugin_calendar_convert', $args);
}
?>
