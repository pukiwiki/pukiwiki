<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: menu.inc.php,v 1.7 2004/11/19 14:54:43 henoheno Exp $
//

// サブメニューを使用する
define('MENU_ENABLE_SUBMENU', FALSE);

// サブメニューの名称
define('MENU_SUBMENUBAR', 'MenuBar');

function plugin_menu_convert()
{
	global $vars, $menubar;
	static $menu = NULL;

	if (func_num_args()) {
		$args = func_get_args();
		if (is_page($args[0])) $menu = $args[0];
		return '';
	}

	$page = ($menu === NULL) ? $menubar : $menu;

	if (MENU_ENABLE_SUBMENU) {
		$path = explode('/', strip_bracket($vars['page']));
		while(count($path)) {
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
	} else if ($vars['page'] == $page) {
		return '<!-- #menu(): You already view ' . htmlspecialchars($page) . ' -->';
	} else {
		// Cut fixed anchors
		$menutext = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', get_source($page));

		return preg_replace('/<ul[^>]*>/', '<ul>', convert_html($menutext));  
	}
}
?>
