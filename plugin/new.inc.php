<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: new.inc.php,v 1.5 2004/12/18 06:52:57 henoheno Exp $
//
// New! plugin

// 全体の表示フォーマット
define('PLUGIN_NEW_FORMAT', '<span class="comment_date">%s</span>');

function plugin_new_init()
{
	// 経過秒数 => 新着表示タグ
	$messages = array(
		'_plugin_new_elapses' => array(
			1*60*60*24 => ' <span class="new1" title="%s">New!</span>',
			5*60*60*24 => ' <span class="new5" title="%s">New</span>',
		),
	);
	set_plugin_messages($messages);
}

function plugin_new_inline()
{
	global $vars, $_plugin_new_elapses;

	if (func_num_args() < 1) return FALSE;

	$retval = '';
	$args = func_get_args();
	$date = strip_htmltag(array_pop($args)); // {}部分の引数
	if ($date != '' && ($timestamp = strtotime($date)) !== -1) {
		$timestamp -= ZONETIME;
		$nodate = in_array('nodate', $args);
		$retval = $nodate ? '' : htmlspecialchars($date);
	} else {
		$timestamp = 0;
		$name = strip_bracket(! empty($args) ? array_shift($args) : $vars['page']);
		$page = get_fullname($name, $vars['page']);
		$nolink = in_array('nolink', $args);
		if (substr($page, -1) == '/') {
			foreach (preg_grep('/^' . preg_quote($page, '/') . '/',
			    get_existpages()) as $page) {
				$_timestamp = get_filetime($page);
				if ($timestamp < $_timestamp) {
					// 最も新しいページを表示
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
