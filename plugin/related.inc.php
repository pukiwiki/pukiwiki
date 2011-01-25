<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: related.inc.php,v 1.11 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C) 2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Related plugin: Show Backlinks for the page

function plugin_related_convert()
{
	global $vars;

	return make_related($vars['page'], 'p');
}

// Show Backlinks: via related caches for the page
function plugin_related_action()
{
	global $vars, $script, $defaultpage, $whatsnew;

	$_page = isset($vars['page']) ? $vars['page'] : '';
	if ($_page == '') $_page = $defaultpage;

	// Get related from cache
	$data = links_get_related_db($_page);
	if (! empty($data)) {
		// Hide by array keys (not values)
		foreach(array_keys($data) as $page)
			if ($page == $whatsnew ||
			    check_non_list($page))
				unset($data[$page]);
	}

	// Result
	$r_word = rawurlencode($_page);
	$s_word = htmlsc($_page);
	$msg = 'Backlinks for: ' . $s_word;
	$retval  = '<a href="' . $script . '?' . $r_word . '">' .
		'Return to ' . $s_word .'</a><br />'. "\n";

	if (empty($data)) {
		$retval .= '<ul><li>No related pages found.</li></ul>' . "\n";	
	} else {
		// Show count($data)?
		ksort($data);
		$retval .= '<ul>' . "\n";
		foreach ($data as $page=>$time) {
			$r_page  = rawurlencode($page);
			$s_page  = htmlsc($page);
			$passage = get_passage($time);
			$retval .= ' <li><a href="' . $script . '?' . $r_page . '">' . $s_page .
				'</a> ' . $passage . '</li>' . "\n";
		}
		$retval .= '</ul>' . "\n";
	}
	return array('msg'=>$msg, 'body'=>$retval);
}
?>
