<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: aname.inc.php,v 1.19 2005/04/24 02:21:07 henoheno Exp $
//
// aname plugin - Set various anchor tag
//   * A simple anchor <a id="key"></a>
//   * A clickable link to the anchor <a href="#key">string</a>
//   * Clickable anchor with the key itself <a id="key" href="#key">string</a>
//
// NOTE:
//   Use 'id="key"' instead of 'name="key"' at XHTML 1.1

define('PLUGIN_ANAME_ID_REGEX', '/^[A-Za-z][\w\-]*$/');
define('PLUGIN_ANAME_ID_MAX',   40); // Max length

// #aname
function plugin_aname_convert()
{
	$args = func_get_args(); // Zero or more
	return plugin_aname_tag($args);
}

// &aname;
function plugin_aname_inline()
{
	$args = func_get_args(); // ONE or more
	return plugin_aname_tag($args, FALSE);
}

// Show usage
function plugin_aname_usage($convert = TRUE, $message = '')
{
	if ($message == '') {
		if ($convert) {
			return '#aname(anchorID[[,super][,full][,noid],Link title])';
		} else {
			return '&amp;aname(anchorID[,super][,full][,noid]){[Link title]}';
		}
	} else {
		if ($convert) {
			return '#aname: ' . $message;
		} else {
			return '&amp;aname: ' . $message . ';';
		}
	}
}

// Aname plugin itself
function plugin_aname_tag($args = array(), $convert = TRUE)
{
	global $vars;
	static $_id = array();

	if (empty($args) || $args[0] == '')
		return plugin_aname_usage($convert);

	$id = array_shift($args);
	if (isset($_id[$id])) {
		return plugin_aname_usage($convert, 'ID already used: '. $id);
	} else {
		if (strlen($id) > PLUGIN_ANAME_ID_MAX)
			return plugin_aname_usage($convert, 'ID too long');
		if (! preg_match(PLUGIN_ANAME_ID_REGEX, $id))
			return plugin_aname_usage($convert, 'Invalid ID string: ' .
				htmlspecialchars($id));
		$_id[$id] = TRUE;
	}
	$id = htmlspecialchars($id); // Insurance

	$body = '';
	if (! empty($args)) {
		if ($convert) {
			$body = htmlspecialchars(array_pop($args));
		} else {
			$body = array_pop($args);
		}
	}
	$f_super = in_array('super', $args); // Option: CSS class
	$f_noid  = in_array('noid',  $args); // Option: Without id attribute
	$f_full  = in_array('full',  $args); // Option: With full(absolute) URI

	$class   = $f_super ? 'anchor_super' : 'anchor';
	$attr_id = $f_noid  ? '' : ' id="' . $id . '"';
	$url     = $f_full  ? get_script_uri() . '?' . rawurlencode($vars['page']) : '';

	// href and title attribute
	if ($body != '') {
		$href  = ' href="' . $url . '#' . $id . '"';
		$title = ' title="' . $id . '"';
	} else {
		if ($f_noid) return plugin_aname_usage($convert, 'Meaningless(No link-title with \'noid\')');
		if ($f_full) return plugin_aname_usage($convert, 'Meaningless(No link-title with \'full\')');
		$href = $title = '';
	}

	return '<a class="' . $class . '"' . $attr_id . $href . $title . '>' .
		$body . '</a>';
}
?>
