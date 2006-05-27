<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: counter.inc.php,v 1.18 2006/05/27 13:31:11 henoheno Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2002 Y.MASUI GPL2 http://masui.net/pukiwiki/ masui@masui.net
// License: GPL2
//
// Counter plugin

// Counter file's suffix
define('PLUGIN_COUNTER_SUFFIX', '.count');

// Report one
function plugin_counter_inline()
{
	global $vars;

	// BugTrack2/106: Only variables can be passed by reference from PHP 5.0.5
	$args = func_get_args(); // with array_shift()

	$arg = strtolower(array_shift($args));
	switch ($arg) {
	case ''     : $arg = 'total'; /*FALLTHROUGH*/
	case 'total': /*FALLTHROUGH*/
	case 'today': /*FALLTHROUGH*/
	case 'yesterday':
		$counter = plugin_counter_get_count($vars['page']);
		return $counter[$arg];
	default:
		return '&counter([total|today|yesterday]);';
	}
}

// Report all
function plugin_counter_convert()
{
	global $vars;

	$counter = plugin_counter_get_count($vars['page']);
	return <<<EOD
<div class="counter">
Counter:   {$counter['total']},
today:     {$counter['today']},
yesterday: {$counter['yesterday']}
</div>
EOD;
}

// Return a summary
function plugin_counter_get_count($page)
{
	global $vars;
	static $counters = array();
	static $default;

	if (! isset($default))
		$default = array(
			'total'     => 0,
			'date'      => get_date('Y/m/d'),
			'today'     => 0,
			'yesterday' => 0,
			'ip'        => '');

	if (! is_page($page)) return $default;
	if (isset($counters[$page])) return $counters[$page];

	// Set default
	$counters[$page] = $default;
	$modify = FALSE;

	$file = COUNTER_DIR . encode($page) . PLUGIN_COUNTER_SUFFIX;
	$fp = fopen($file, file_exists($file) ? 'r+' : 'w+')
		or die('counter.inc.php: Cannot open COUTER_DIR/' . basename($file));
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	rewind($fp);
	foreach ($default as $key=>$val) {
		// Update
		$counters[$page][$key] = rtrim(fgets($fp, 256));
		if (feof($fp)) break;
	}
	if ($counters[$page]['date'] != $default['date']) {
		// New day
		$modify = TRUE;
		$is_yesterday = ($counters[$page]['date'] == get_date('Y/m/d', strtotime('yesterday', UTIME)));
		$counters[$page]['ip']        = $_SERVER['REMOTE_ADDR'];
		$counters[$page]['date']      = $default['date'];
		$counters[$page]['yesterday'] = $is_yesterday ? $counters[$page]['today'] : 0;
		$counters[$page]['today']     = 1;
		$counters[$page]['total']++;

	} else if ($counters[$page]['ip'] != $_SERVER['REMOTE_ADDR']) {
		// Not the same host
		$modify = TRUE;
		$counters[$page]['ip']        = $_SERVER['REMOTE_ADDR'];
		$counters[$page]['today']++;
		$counters[$page]['total']++;
	}
	// Modify
	if ($modify && $vars['cmd'] == 'read') {
		rewind($fp);
		ftruncate($fp, 0);
		foreach (array_keys($default) as $key)
			fputs($fp, $counters[$page][$key] . "\n");
	}
	flock($fp, LOCK_UN);
	fclose($fp);

	return $counters[$page];
}
?>
