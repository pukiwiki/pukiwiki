<?php
// PukiWiki - Yet another WikiWikiWeb clone
// topicpath.inc.php
// Copyright
//   2004-2017 PukiWiki Development Team
//   2003      reimy       (Some bug fix)
//   2003      t.m         (Migrate to 1.3)
//   2003      Nibun-no-ni (Originally written for PukiWiki 1.4.x)
// License: GPL v2 or (at your option) any later version
//
// 'topicpath' plugin for PukiWiki

// Show a link to $defaultpage or not
define('PLUGIN_TOPICPATH_TOP_DISPLAY', 1);

// Label for $defaultpage
define('PLUGIN_TOPICPATH_TOP_LABEL', 'Top');

// Separetor / of / topic / path
define('PLUGIN_TOPICPATH_TOP_SEPARATOR', '<span class="topicpath-slash">/</span>');

// Show the page itself or not
define('PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY', 1);

// If PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY, add a link to itself
define('PLUGIN_TOPICPATH_THIS_PAGE_LINK', 0);

function plugin_topicpath_convert()
{
	return '<div>' . plugin_topicpath_inline() . '</div>';
}

function plugin_topicpath_inline()
{
	global $vars, $defaultpage;

	$page = isset($vars['page']) ? $vars['page'] : '';
	if ($page == '' || $page == $defaultpage) return '';

	$parts = explode('/', $page);

	$b_link = TRUE;
	if (PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY) {
		$b_link = PLUGIN_TOPICPATH_THIS_PAGE_LINK;
	} else {
		array_pop($parts); // Remove the page itself
	}

	$topic_path = array();
	while (! empty($parts)) {
		$_landing = join('/', $parts);
		$element  = htmlsc(array_pop($parts));
		if (! $b_link)  {
			// This page ($_landing == $page)
			$b_link = TRUE;
			$topic_path[] = $element;
		} else if (PKWK_READONLY && ! is_page($_landing)) {
			// Page not exists
			$topic_path[] = $element;
		} else {
			// Page exists or not exists
			$topic_path[] = '<a href="' . get_page_uri($_landing) . '">' .
				$element . '</a>';
		}
	}
	$s = join(PLUGIN_TOPICPATH_TOP_SEPARATOR, array_reverse($topic_path));
	if (PLUGIN_TOPICPATH_TOP_DISPLAY) {
		$s = '<span class="topicpath-top">' .
			make_pagelink($defaultpage, PLUGIN_TOPICPATH_TOP_LABEL) .
			PLUGIN_TOPICPATH_TOP_SEPARATOR . '</span>' . $s;
	}
	return $s;
}
