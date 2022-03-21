<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// pageaction.inc.php
// Copyright 2022 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// pageaction plugin

function plugin_pageaction_inline()
{
	global $_LANG;
	$args = func_get_args();
	$page = strip_bracket(array_shift($args));
	$action = array_shift($args);
	$base_uri = get_base_uri();
	switch ($action) {
		case 'diff':
			$diff_uri = $base_uri . '?cmd=diff&page=' . pagename_urlencode($page);
			return '<a href="' . htmlsc($diff_uri) . '">' . $_LANG['skin']['diff'] . '</a>';
			break;
		case 'backup':
			$backup_uri = $base_uri . '?cmd=backup&page=' . pagename_urlencode($page);
			return '<a href="' . htmlsc($backup_uri) . '">' . $_LANG['skin']['backup'] . '</a>';
			break;
		default:
			return make_pagelink($page);
	}
}
