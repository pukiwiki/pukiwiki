<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: topicpath.inc.php,v 1.5 2005/01/29 14:08:46 henoheno Exp $
//
// 'topicpath' plugin for PukiWiki, available under GPL

// Show a link to $defaultpage or not
define('PLUGIN_TOPICPATH_TOP_DISPLAY', TRUE);

// Label for $defaultpage
define('PLUGIN_TOPICPATH_TOP_LABEL', 'Top');

// Separetor / of / topic / path
define('PLUGIN_TOPICPATH_TOP_SEPARATOR', ' / ');

// Show the page itself or not
define('PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY', TRUE);

// If PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY, add a link to itself
define('PLUGIN_TOPICPATH_THIS_PAGE_LINK', FALSE);

function plugin_topicpath_convert()
{
	return '<div>' . plugin_topicpath_inline() . '</div>';
}

function plugin_topicpath_inline()
{
	global $script, $vars, $defaultpage;

	$page = isset($vars['page']) ? $vars['page'] : '';
	if ($page == '' || $page == $defaultpage) return '';

	$parts = explode('/', $page);

	if (PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY) {
		$b_link = PLUGIN_TOPICPATH_THIS_PAGE_LINK;
	} else {
		array_pop($parts); // Remove itself
		$b_link = TRUE;    // Link to the parent
	}

	$topic_path = array();
	while (! empty($parts)) {
		$landing = rawurlencode(join('/', $parts));
		$element = htmlspecialchars(array_pop($parts));
		if ($b_link)  {
			$topic_path[] = '<a href="' . $script . '?' . $landing . '">' .
				$element . '</a>';
		} else {
			$b_link = TRUE; // Maybe reacheable once at a time
			$topic_path[] = $element;
		}
	}

	if (PLUGIN_TOPICPATH_TOP_DISPLAY)
		$topic_path[] = make_pagelink($defaultpage, PLUGIN_TOPICPATH_TOP_LABEL);

	return join(PLUGIN_TOPICPATH_TOP_SEPARATOR, array_reverse($topic_path));
}
?>
