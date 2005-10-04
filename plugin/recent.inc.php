<?php
// $Id: recent.inc.php,v 1.18 2005/10/04 13:41:03 henoheno Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2002      Y.MASUI http://masui.net/pukiwiki/ masui@masui.net
// License: GPL version 2
//
// Recent plugin -- Show RecentChanges list
//   * Usually used at 'MenuBar' page
//   * Also used at special-page, without no #recnet at 'MenuBar'

// Default number of 'Show latest N changes'
define('PLUGIN_RECENT_DEFAULT_LINES', 10);

// Limit number of executions
define('PLUGIN_RECENT_EXEC_LIMIT', 2); // N times per one output

// ----

define('PLUGIN_RECENT_USAGE', '#recent(number-to-show)');

// Place of the cache of 'RecentChanges'
define('PLUGIN_RECENT_CACHE', CACHE_DIR . 'recent.dat');

function plugin_recent_convert()
{
	global $vars, $date_format, $_recent_plugin_frame;
	static $exec_count = 1;

	$recent_lines = PLUGIN_RECENT_DEFAULT_LINES;
	if (func_num_args()) {
		$args = func_get_args();
		if (! is_numeric($args[0]) || isset($args[1])) {
			return PLUGIN_RECENT_USAGE . '<br />';
		} else {
			$recent_lines = $args[0];
		}
	}

	// Show only N times
	if ($exec_count > PLUGIN_RECENT_EXEC_LIMIT) {
		return '#recent(): You called me too much' . '<br />' . "\n";
	} else {
		++$exec_count;
	}

	// Get latest N changes
	if (file_exists(PLUGIN_RECENT_CACHE)) {
		// BugTrack2/106: Only variables can be passed by reference from PHP 5.0.5
		$file_array = file(PLUGIN_RECENT_CACHE); // with array_splice()
		$lines      = array_splice($file_array, 0, $recent_lines);
	} else {
		return '#recent(): Cache file of RecentChanges not found' . '<br />';
	}

	$script = get_script_uri();
	$date = $items = '';
	foreach ($lines as $line) {
		list($time, $page) = explode("\t", rtrim($line));

		$_date = get_date($date_format, $time);
		if ($date != $_date) {
			// End of the day
			if ($date != '') $items .= '</ul>' . "\n";

			// New day
			$date = $_date;
			$items .= '<strong>' . $date . '</strong>' . "\n" .
				'<ul class="recent_list">' . "\n";
		}

		$s_page = htmlspecialchars($page);
		if($page == $vars['page']) {
			// No need to link to the page now you read, notifies where you just read
			$items .= ' <li>' . $s_page . '</li>' . "\n";
		} else {
			$r_page = rawurlencode($page);
			$pg_passage = get_pg_passage($page, FALSE);
			$items .= ' <li><a href="' . $script . '?' . $r_page . '" title="' .
				$s_page . ' ' . $pg_passage . '">' . $s_page . '</a></li>' . "\n";
		}
	}
	// End of the day
	if ($date != '') $items .= '</ul>' . "\n";

	return sprintf($_recent_plugin_frame, count($lines), $items);
}
?>
