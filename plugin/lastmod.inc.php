<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: lastmod.inc.php,v 1.3 2005/01/31 13:03:41 henoheno Exp $
//
// Lastmod plugin - Show lastmodifled date of the page
// Originally written by Reimy, 2003

function plugin_lastmod_inline()
{
	global $vars, $WikiName, $BracketName;

	$args = func_get_args();
	$page = $args[0];

	if ($page == ''){
		$page = $vars['page']; // Default: page itself
	} else {
		if (preg_match("/^($WikiName|$BracketName)$/", strip_bracket($page))) {
			$page = get_fullname(strip_bracket($page), $vars['page']);
		} else {
			return FALSE;
		}
	}
	if (! is_page($page)) return FALSE;

	return format_date(get_filetime($page));
}
?>
