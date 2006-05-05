<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: yetlist.inc.php,v 1.24 2006/05/05 02:32:52 henoheno Exp $
// Copyright (C) 2001-2005 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Yet list plugin - Show a list of dangling links (not yet created)

function plugin_yetlist_action()
{
	global $_title_yetlist, $_err_notexisto, $_symbol_noexists, $non_list;

	$retval = array('msg' => $_title_yetlist, 'body' => '');

	// Diff
	$pages = get_existpages(CACHE_DIR, '.ref');
	$pages = array_diff($pages, preg_grep('/' . $non_list . '/S', $pages), get_existpages());
	asort($pages, SORT_STRING);

	// Load .ref files
	$refer = array();
	foreach ($pages as $file=>$page) {
		foreach (file(CACHE_DIR . $file) as $line) {
			list($_page) = explode("\t", rtrim($line));
			$refer[$page][] = $_page;
		}
		if (isset($refer[$page])) {
			$refer[$page] = array_unique($refer[$page]);
			sort($refer[$page], SORT_STRING);
		}
	}
	if (empty($refer)) {
		$retval['body'] = $_err_notexist;
		return $retval;
	}

	// Output
	$script = get_script_uri();
	foreach ($refer as $page=>$refs) {
		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page);

		$r_refer = '';
		$link_refs = array();
		foreach ($refs as $_refer) {
			$r_refer = rawurlencode($_refer);
			$link_refs[] = '<a href="' . $script . '?' . $r_refer . '">' .
				htmlspecialchars($_refer) . '</a>';
		}
		$link_ref = join(' ', $link_refs);

		if (PKWK_READONLY) {
			$href = $s_page;
		} else {
			// Show edit link
			// $r_refer is the last one if there're multiple refer pages
			$href = '<span class="noexists">' . $s_page . '<a href="' .
				$script . '?cmd=edit&amp;page=' . $r_page .
				'&amp;refer=' . $r_refer . '">' . $_symbol_noexists .
				'</a></span>';
		}
		$retval['body'] .= '<li>' . $href . ' <em>(' . $link_ref . ')</em></li>' . "\n";
		unset($refer[$page]);
	}

	if ($retval['body'] != '')
		$retval['body'] = '<ul>' . "\n" . $retval['body'] . '</ul>' . "\n";

	return $retval;
}
?>
