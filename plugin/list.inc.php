<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// list.inc.php
// Copyright 2003-2017 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// IndexPages plugin: Show a list of page names

function plugin_list_action()
{
	global $vars, $_title_list, $_title_filelist, $whatsnew;

	// Redirected from filelist plugin?
	$filelist = (isset($vars['cmd']) && $vars['cmd'] === 'filelist');

	return array(
		'msg'=>$filelist ? $_title_filelist : $_title_list,
		'body'=>plugin_list_getlist($filelist));
}

// Get a list
function plugin_list_getlist($withfilename = FALSE)
{
	global $non_list, $whatsnew;

	$pages = array_diff(get_existpages(), array($whatsnew));
	if (! $withfilename)
		$pages = array_diff($pages, preg_grep('/' . $non_list . '/S', $pages));
	if (empty($pages)) return '';

	return page_list($pages, 'read', $withfilename);
}
