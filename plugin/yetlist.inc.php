<?php
// PukiWiki - Yet another WikiWikiWeb clone
// yetlist.inc.php
// Copyright 2001-2020 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// Yet list plugin - Show a list of dangling links (not yet created)

function plugin_yetlist_action()
{
	global $_title_yetlist, $_err_notexist, $_symbol_noexists, $non_list;
	global $whatsdeleted;

	$retval = array('msg' => $_title_yetlist, 'body' => '');

	// Diff
	$pages = array_diff(get_existpages(CACHE_DIR, '.ref'), get_existpages());
	if (empty($pages)) {
		$retval['body'] = $_err_notexist;
		return $retval;
	}

	$empty = TRUE;

	// Load .ref files and Output
	$script      = get_base_uri();
	$refer_regex = '/' . $non_list . '|^' . preg_quote($whatsdeleted, '/') . '$/S';
	asort($pages, SORT_STRING);
	foreach ($pages as $file=>$page) {
		$refer = array();
		foreach (file(CACHE_DIR . $file) as $line) {
			list($_page) = explode("\t", rtrim($line));
			$refer[] = $_page;
		}
		// Diff
		$refer = array_diff($refer, preg_grep($refer_regex, $refer));
		if (! empty($refer)) {
			$empty = FALSE;
			$refer = array_unique($refer);
			sort($refer, SORT_STRING);

			$r_refer = '';
			$link_refs = array();
			foreach ($refer as $_refer) {
				$r_refer = pagename_urlencode($_refer);
				$link_refs[] = '<a href="' . get_page_uri($_refer) . '">' .
					htmlsc($_refer) . '</a>';
			}
			$link_ref = join(' ', $link_refs);
			unset($link_refs);

			$s_page = htmlsc($page);
			if (PKWK_READONLY) {
				$href = $s_page;
			} else {
				// Dangling link
				$symbol_html = '';
				if ($_symbol_noexists !== '') {
					$symbol_html = '<span style="user-select:none;">' .
						htmlsc($_symbol_noexists) . '</span>';
				}
				$href = '<span class="noexists"><a href="' .
					$script . '?cmd=edit&amp;page=' . rawurlencode($page) .
					'&amp;refer=' . $r_refer . '">' . $s_page .
					'</a>' . $symbol_html . '</span>';
			}
			$retval['body'] .= '<li>' . $href . ' <em>(' . $link_ref . ')</em></li>' . "\n";
		}
	}

	if ($empty) {
		$retval['body'] = $_err_notexist;
		return $retval;
	}

	if ($retval['body'] != '')
		$retval['body'] = '<ul>' . "\n" . $retval['body'] . '</ul>' . "\n";

	return $retval;
}
