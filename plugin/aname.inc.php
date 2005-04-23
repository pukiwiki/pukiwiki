<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: aname.inc.php,v 1.18 2005/04/23 14:43:19 henoheno Exp $
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
			return '#aname(anchorID[,super][,full][,noid][,Link title])';
		} else {
			return '&amp;aname(anchorID[,super][,full][,noid])[{Link title}]';
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

	// Option: CSS class
	$class   = in_array('super', $args) ? 'anchor_super' : 'anchor';

	// Option: Without id
	$attr_id = in_array('noid',  $args) ? '' : ' id="' . $id . '"';

	// Option: With full(absolute) URI
	$url     = in_array('full',  $args) ?
		get_script_uri() . '?' . rawurlencode($vars['page']) : '';

	// href and title attribute
	if ($body != '') {
		$href  = ' href="' . $url . '#' . $id . '"';
		$title = ' title="' . $id . '"';
	} else {
		if ($attr_id == '')
			return plugin_aname_usage($convert, 'Meanless(No body and No id)');
		$href = $title = '';
	}

	return '<a class="' . $class . '"' . $attr_id . $href . $title . '>' .
		$body . '</a>';
}
?>
