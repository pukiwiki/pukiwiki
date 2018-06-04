<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// menu.inc.php
// Copyright 2003-2018 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Menu plugin

// Use Submenu if true
define('MENU_ENABLE_SUBMENU', FALSE);

// Name of Submenu
define('MENU_SUBMENUBAR', 'MenuBar');

function plugin_menu_convert()
{
	global $vars, $menubar;
	static $menu = NULL;

	$num = func_num_args();
	if ($num > 0) {
		// Try to change default 'MenuBar' page name (only)
		if ($num > 1)       return '#menu(): Zero or One argument needed';
		if ($menu !== NULL) return '#menu(): Already set: ' . htmlsc($menu);
		$args = func_get_args();
		if (! is_page($args[0])) {
			return '#menu(): No such page: ' . htmlsc($args[0]);
		} else {
			$menu = $args[0]; // Set
			return '';
		}

	} else {
		// Output menubar page data
		$page = ($menu === NULL) ? $menubar : $menu;

		if (MENU_ENABLE_SUBMENU) {
			$path = explode('/', strip_bracket($vars['page']));
			while(! empty($path)) {
				$_page = join('/', $path) . '/' . MENU_SUBMENUBAR;
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
			return '<!-- #menu(): You already view ' . htmlsc($page) . ' -->';
		} else if (!is_page_readable($page)) {
			return '#menu(): ' . htmlsc($page) . ' is not readable';
		} else {
			// Cut fixed anchors
			$menutext = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', get_source($page));
			return convert_html($menutext);
		}
	}
}
