<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: ls.inc.php,v 1.11 2007/10/27 14:46:45 henoheno Exp $
// Copyright (C)
//   2002-2004, 2007 PukiWiki Developers Team
//   2002      Y.MASUI GPL2 http://masui.net/pukiwiki/ masui@masui.net
// License: GPL version 2
//
// List plugin

function plugin_ls_convert()
{
	global $vars;

	$args = func_get_args();

	$with_title = FALSE;
	if (! empty($args)) {
		$with_title = in_array('title', $args);
	}

	$page  = isset($vars['page']) ? $vars['page'] : '';
	$pages = preg_grep('#^' .  preg_quote($page . '/' , '#') . '#', get_existpages());

	natcasesort($pages);

	$ls = array();
	foreach ($pages as $page) {
		$comment = '';

		if ($with_title) {
			$array = file_head(get_filename($page), 1);
			if ($array) {
				$comment = ' - ' .
					preg_replace(
						array(
							'/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/S',	// Remove fixed-heading anchors
							'/^(?:-+|\*+)/',	// Remove syntax garbages at this situation
						),
						array(
							'$1$2',
							'',
						),
						current($array)
					);
			}
		}

		$ls[] = '- [[' . $page . ']]' . $comment;
	}

	return convert_html($ls);
}
?>
