<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: version.inc.php,v 1.7 2005/01/22 03:35:54 henoheno Exp $
//
// Show PukiWiki version

function plugin_version_convert()
{
	return '<p>' . S_VERSION . '</p>';
}

function plugin_version_inline()
{
	return S_VERSION;
}
?>
