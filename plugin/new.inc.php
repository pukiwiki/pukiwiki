<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: new.inc.php,v 1.6 2004/12/25 05:56:50 henoheno Exp $
//
// New! plugin

define('PLUGIN_NEW_FORMAT', '<span class="comment_date">%s</span>');

function plugin_new_init()
{
	// Elapsed time => New! message with CSS
	$messages['_plugin_new_elapses'] = array(
		60 * 60 * 24 * 1 => ' <span class="new1" title="%s">New!</span>',  // 1day
		60 * 60 * 24 * 5 => ' <span class="new5" title="%s">New</span>');  // 5days
	set_plugin_messages($messages);
}

function plugin_new_inline()
{
	global $vars, $_plugin_new_elapses;

	$retval = '';
	$args = func_get_args();
	$date = array_pop($args); // {date} always exists

	if($date !== '') {
		$usage = '&new([nodate]){date};';
		if (func_num_args() > 2) return $usage;
		$timestamp = strtotime($date);
	} else {
		$usage = '&new(pagename[,nolink]);';
		if (func_num_args() > 3) return $usage;
	}

	if (isset($timestamp) && $timestamp !== -1) {
		// &new([nodate]){date};
		$timestamp -= ZONETIME;
		$nodate = in_array('nodate', $args);
		$retval = $nodate ? '' : htmlspecialchars($date);
	} else {
		// &new(pagename[,nolink]);
		$timestamp = 0;
		$name = strip_bracket(! empty($args) ? array_shift($args) : $vars['page']);
		$page = get_fullname($name, $vars['page']);
		$nolink = in_array('nolink', $args);
		if (substr($page, -1) == '/') {
			foreach (preg_grep('/^' . preg_quote($page, '/') . '/',
			    get_existpages()) as $page) {
				$_timestamp = get_filetime($page);
				if ($timestamp < $_timestamp) {
					// Show the latest page
					$retval    = $nolink ? '' : make_pagelink($page);
					$timestamp = $_timestamp;
				}
			}
		} else if (is_page($page)) {
			$retval    = $nolink ? '' : make_pagelink($page, $name);
			$timestamp = get_filetime($page);
		}
		if ($timestamp == 0) return '';
	}

	$erapse = UTIME - $timestamp;
	foreach ($_plugin_new_elapses as $limit=>$tag) {
		if ($erapse <= $limit) {
			$retval .= sprintf($tag, get_passage($timestamp));
			break;
		}
	}
	return sprintf(PLUGIN_NEW_FORMAT, $retval);
}
?>
