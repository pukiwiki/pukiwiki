<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: interwiki.inc.php,v 1.9 2004/12/04 14:45:36 henoheno Exp $
//
// InterWiki redirection plugin (OBSOLETE)

function plugin_interwiki_action()
{
	global $vars, $InterWikiName;

	$match = array();
	if (! preg_match("/^$InterWikiName$/", $vars['page'], $match))
		return plugin_interwiki_invalid();

	$url = get_interwiki_url($match[2], $match[3]);
	if ($url === FALSE) return plugin_interwiki_invalid();

	pkwk_headers_sent();
	header('Location: ' . $url);
	exit;
}

function plugin_interwiki_invalid()
{
	global $_title_invalidiwn, $_msg_invalidiwn;
	return array(
		'msg'  => $_title_invalidiwn,
		'body' => str_replace(array('$1', '$2'),
			array(htmlspecialchars(''),
			make_pagelink('InterWikiName')),
			$_msg_invalidiwn));
}
?>
