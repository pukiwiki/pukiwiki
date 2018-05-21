<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// new.inc.php
// Copyright  2003-2018 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// New! plugin
//
// Usage:
//	&new([nodate]){date};     // Check the date string
//	&new(pagename[,nolink]);  // Check the pages's timestamp
//	&new(pagename/[,nolink]);
//		// Check multiple pages started with 'pagename/',
//		// and show the latest one

define('PLUGIN_NEW_DATE_FORMAT', '<span class="comment_date">%s</span>');

function plugin_new_init()
{
	// Backword compatibility: Keep plugin_new_init() and the messages
	// Elapsed time => New! message with CSS
	$messages['_plugin_new_elapses'] = array(
		60 * 60 * 24 * 1 => ' <span class="new1" title="%s">New!</span>',  // 1day
		60 * 60 * 24 * 5 => ' <span class="new5" title="%s">New</span>');  // 5days
	set_plugin_messages($messages);
}

function plugin_new_inline()
{
	global $vars;

	$retval = '';
	$args = func_get_args();
	$date = strip_autolink(array_pop($args)); // {date} always exists
	if($date !== '') {
		// Show 'New!' message by the time of the $date string
		if (func_num_args() > 2) return '&new([nodate]){date};';
		$timestamp = -1;
		$dm = null;
		if (preg_match('/^\D*(\d{4})\D+(\d{1,2})\D+(\d{1,2})\D+(\d{1,2}:\d{2}:\d{2})\D*$/', $date, $dm)) {
			$iso8601_date = $dm[1]
				. '-' . substr('0' . $dm[2], -2)
				. '-' . substr('0' . $dm[3], -2)
				. ' ' . $dm[4];
			$timestamp = strtotime($iso8601_date);
		} else {
			$timestamp = strtotime($date);
		}
		if ($timestamp === -1 || $timestamp === FALSE) {
			return '&new([nodate]){date}: Invalid date string;';
		}
		$timestamp -= ZONETIME;

		$retval = in_array('nodate', $args) ? '' : htmlsc($date);
	} else {
		// Show 'New!' message by the timestamp of the page
		if (func_num_args() > 3) return '&new(pagename[,nolink]);';

		$name = strip_bracket(! empty($args) ? array_shift($args) : $vars['page']);
		$page = get_fullname($name, $vars['page']);
		$nolink = in_array('nolink', $args);

		if (substr($page, -1) == '/') {
			// Check multiple pages started with "$page"
			$timestamp = 0;
			$regex = '/^' . preg_quote($page, '/') . '/';
			foreach (preg_grep($regex, get_existpages()) as $page) {
				// Get the latest pagename and its timestamp
				$_timestamp = get_filetime($page);
				if ($timestamp < $_timestamp) {
					$timestamp = $_timestamp;
					$retval    = $nolink ? '' : make_pagelink($page);
				}
			}
			if ($timestamp == 0)
				return '&new(pagename/[,nolink]): No such pages;';
		} else {
			// Check a page
			if (is_page($page)) {
				$timestamp = get_filetime($page);
				$retval    = $nolink ? '' : make_pagelink($page, $name);
			} else {
				return '&new(pagename[,nolink]): No such page;';
			}
		}
	}
	if($date !== '') {
		// 1 day hot: <span class="new1">New!</span>
		// 5 days hot: <span class="new5">New</span>
		$retval .= '<span class="__plugin_new" data-mtime="' . get_date_atom($timestamp + LOCALZONE) . '"></span>';
		// Show a date string
		return sprintf(PLUGIN_NEW_DATE_FORMAT, $retval);
	} else {
		// Show a page name
		return $retval;
	}
}
