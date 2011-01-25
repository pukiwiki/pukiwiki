<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: aname.inc.php,v 1.28 2011/01/25 15:01:01 henoheno Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// aname plugin - Set various anchor tags
//   * With just an anchor id: <a id="key"></a>
//   * With a hyperlink to the anchor id: <a href="#key">string</a>
//   * With an anchor id and a link to the id itself: <a id="key" href="#key">string</a>
//
// NOTE: Use 'id="key"' instead of 'name="key"' at XHTML 1.1

// Check ID is unique or not (compatible: no-check)
define('PLUGIN_ANAME_ID_MUST_UNIQUE', 0);

// Max length of ID
define('PLUGIN_ANAME_ID_MAX',   40);

// Pattern of ID
define('PLUGIN_ANAME_ID_REGEX', '/^[A-Za-z][\w\-]*$/');

// Show usage
function plugin_aname_usage($convert = TRUE, $message = '')
{
	if ($convert) {
		if ($message == '') {
			return '#aname(anchorID[[,super][,full][,noid],Link title])' . '<br />';
		} else {
			return '#aname: ' . $message . '<br />';
		}
	} else {
		if ($message == '') {
			return '&amp;aname(anchorID[,super][,full][,noid]){[Link title]};';
		} else {
			return '&amp;aname: ' . $message . ';';
		}
	}
}

// #aname
function plugin_aname_convert()
{
	$convert = TRUE;

	if (func_num_args() < 1)
		return plugin_aname_usage($convert);

	return plugin_aname_tag(func_get_args(), $convert);
}

// &aname;
function plugin_aname_inline()
{
	$convert = FALSE;

	if (func_num_args() < 2)
		return plugin_aname_usage($convert);

	$args = func_get_args(); // ONE or more
	$body = strip_htmltag(array_pop($args), FALSE); // Strip anchor tags only
	array_push($args, $body);

	return plugin_aname_tag($args, $convert);
}

// Aname plugin itself
function plugin_aname_tag($args = array(), $convert = TRUE)
{
	global $vars;
	static $_id = array();

	if (empty($args) || $args[0] == '') return plugin_aname_usage($convert);

	$id = array_shift($args);
	$body = '';
	if (! empty($args)) $body = array_pop($args);
	$f_noid  = in_array('noid',  $args); // Option: Without id attribute
	$f_super = in_array('super', $args); // Option: CSS class
	$f_full  = in_array('full',  $args); // Option: With full(absolute) URI

	if ($body == '') {
		if ($f_noid)  return plugin_aname_usage($convert, 'Meaningless(No link-title with \'noid\')');
		if ($f_super) return plugin_aname_usage($convert, 'Meaningless(No link-title with \'super\')');
		if ($f_full)  return plugin_aname_usage($convert, 'Meaningless(No link-title with \'full\')');
	}

	if (PLUGIN_ANAME_ID_MUST_UNIQUE && isset($_id[$id]) && ! $f_noid) {
		return plugin_aname_usage($convert, 'ID already used: '. $id);
	} else {
		if (strlen($id) > PLUGIN_ANAME_ID_MAX)
			return plugin_aname_usage($convert, 'ID too long');
		if (! preg_match(PLUGIN_ANAME_ID_REGEX, $id))
			return plugin_aname_usage($convert, 'Invalid ID string: ' .
				htmlsc($id));
		$_id[$id] = TRUE; // Set
	}

	if ($convert) $body = htmlsc($body);
	$id = htmlsc($id); // Insurance
	$class   = $f_super ? 'anchor_super' : 'anchor';
	$attr_id = $f_noid  ? '' : ' id="' . $id . '"';
	$url     = $f_full  ? get_script_uri() . '?' . rawurlencode($vars['page']) : '';
	if ($body != '') {
		$href  = ' href="' . $url . '#' . $id . '"';
		$title = ' title="' . $id . '"';
	} else {
		$href = $title = '';
	}

	return '<a class="' . $class . '"' . $attr_id . $href . $title . '>' .
		$body . '</a>';
}
?>
