<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// rightbar.inc.php
// Copyright 2020 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// RightBar plugin

// Use Submenu if true
define('RIGHTBAR_ENABLE_SUBMENU', FALSE);

// Name of Submenu
define('RIGHTBAR_SUBMENUBAR', 'RightBar');

function plugin_rightbar_convert()
{
	global $vars, $rightbar_name;
	static $menu = NULL;

	$num = func_num_args();
	if ($num > 0) {
		// Try to change default 'RightBar' page name (only)
		if ($num > 1) {
			return '#rightbar(): Zero or One argument needed';
		}
		if ($menu !== NULL) {
			return '#rightbar(): Already set: ' . htmlsc($menu);
		}
		$args = func_get_args();
		if (! is_page($args[0])) {
			return '#rightbar(): No such page: ' . htmlsc($args[0]);
		} else {
			$menu = $args[0]; // Set
			return '';
		}
	}
	// Output rightbar page data
	$page = ($menu === NULL) ? $rightbar_name : $menu;
	if (RIGHTBAR_ENABLE_SUBMENU) {
		$path = explode('/', strip_bracket($vars['page']));
		while(! empty($path)) {
			$_page = join('/', $path) . '/' . RIGHTBAR_SUBMENUBAR;
			if (is_page($_page)) {
				$page = $_page;
				break;
			}
			array_pop($path);
		}
	}
	if (! is_page($page)) {
		return '';
	} else if ($vars['page'] === $page) {
		return '<!-- #rightbar(): You already view ' . htmlsc($page) . ' -->';
	} else if (!is_page_readable($page)) {
		return '#rightbar(): ' . htmlsc($page) . ' is not readable';
	} else {
		// Cut fixed anchors
		$menutext = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', get_source($page));
		return convert_html($menutext);
	}
}
