<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: filelist.inc.php,v 1.3 2005/01/09 08:16:28 henoheno Exp $
//
// Filelist plugin: redirect to list plugin
// cmd=filelist

function plugin_filelist_action()
{
	return do_plugin_action('list');
}
?>
