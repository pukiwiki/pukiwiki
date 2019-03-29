<?php
// PukiWiki - Yet another WikiWikiWeb clone
// topicpath.inc.php
// Copyright
//   2004-2018 PukiWiki Development Team
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

function plugin_topicpath_parent_links($page)
{
	$links = plugin_topicpath_parent_all_links($page);
	if (PKWK_READONLY) {
		$active_links = array();
		foreach ($links as $link) {
			if (is_page($link['page'])) {
				$active_links[] = $link;
			} else {
				$active_links[] = array(
					'page' => $link['page'],
					'leaf' => $link['leaf'],
				);
			}
		}
		return $active_links;
	}
	return $links;
}

function plugin_topicpath_parent_all_links($page)
{
	$parts = explode('/', $page);
	$parents = array();
	for ($i = 0, $pos = 0; $pos = strpos($page, '/', $i); $i = $pos + 1) {
		$p = substr($page, 0, $pos);
		$parents[] = array(
			'page' => $p,
			'leaf' => substr($p, $i),
			'uri' => get_page_uri($p),
		);
	}
	return $parents;
}

function plugin_topicpath_inline()
{
	global $vars, $defaultpage;
	$page = isset($vars['page']) ? $vars['page'] : '';
	if ($page == '' || $page == $defaultpage) return '';
	$parents = plugin_topicpath_parent_all_links($page);
	$topic_path = array();
	foreach ($parents as $p) {
		if (PKWK_READONLY && !is_page($p['page'])) {
			// Page not exists
			$topic_path[] = htmlsc($p['leaf']);
		} else {
			// Page exists or not exists
			$topic_path[] = '<a href="' . $p['uri'] . '">' .
				$p['leaf'] . '</a>';
		}
	}
	// This page
	if (PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY) {
		$leaf_name = preg_replace('#^.*/#', '', $page);
		if (PLUGIN_TOPICPATH_THIS_PAGE_LINK) {
			$topic_path[] = '<a href="' . get_page_uri($page) . '">' .
				$leaf_name . '</a>';
		} else {
			$topic_path[] = htmlsc($leaf_name);
		}
	}
	$s = join(PLUGIN_TOPICPATH_TOP_SEPARATOR, $topic_path);
	if (PLUGIN_TOPICPATH_TOP_DISPLAY) {
		$s = '<span class="topicpath-top">' .
			make_pagelink($defaultpage, PLUGIN_TOPICPATH_TOP_LABEL) .
			PLUGIN_TOPICPATH_TOP_SEPARATOR . '</span>' . $s;
	}
	return $s;
}
