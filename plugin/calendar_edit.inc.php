<?php
// $Id: calendar_edit.inc.php,v 1.5 2003/01/27 05:38:44 panda Exp $

function plugin_calendar_read_convert()
{
	global $command;
	
	$command = 'edit';
	
	if (!file_exists(PLUGIN_DIR.'calendar.inc.php')) {
		return FALSE;
	}
	require_once PLUGIN_DIR.'calendar.inc.php';
	
	return call_user_func_array('plugin_calendar_convert',
		func_num_args() ? func_get_args() : array()
	);
}
?>
