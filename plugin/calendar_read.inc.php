<?php
// $Id: calendar_read.inc.php,v 1.4.2.1 2004/07/31 03:15:07 henoheno Exp $

function plugin_calendar_read_convert()
{
	global $command;

	$command = read;

	if(func_num_args())
		$aryargs = func_get_args();
	else
		$aryargs = array();

	if(file_exists(PLUGIN_DIR."calendar.inc.php"))
	{
		require_once PLUGIN_DIR."calendar.inc.php";
		return call_user_func_array("plugin_calendar_convert",$aryargs);
	}
	else
	{
		return FALSE;
	}
}
?>
